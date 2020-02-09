<?php
/**
* \file PortaMx.php
* The main programm for PortaMx.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('SMF'))
	die('This Portamx file can\'t be run without SMF');

/**
* Init all variables and load the settings from the database.
* Check the requests and prepare the templates to load.
*/
function PortaMx($doinit = false)
{
	global $smcFunc, $pmxCacheFunc, $context, $settings, $db_character_set, $modSettings, $sourcedir, $boarddir, $boardurl, $txt, $mbname, $scripturl, $user_info, $maintenance;

	// we can exit on this...
	if(defined('PortaMx') || (isset($_REQUEST['action']) && $_REQUEST['action'] == 'dlattach' && empty($doinit)))
		return;

	define('PortaMx', 1);

	// redirect page requests (use cookie)
	if(isset($_GET['pg']) && is_array($_GET['pg']))
	{
		$key = key($_GET['pg']);
		if(!pmx_checkECL_Cookie())
		{
			$_POST['pg'] = $_GET['pg'];
			unset($_GET['pg']);
		}
		else
		{
			pmx_setcookie('pgidx_'. $key, $_GET['pg'][$key]);
			unset($_GET['pg']);
			redirectexit(pmx_http_build_query($_GET));
		}
	}

	// check if a language change requested
	if(isset($_REQUEST['language']) && isset($_REQUEST['pmxrd']))
	{
		clean_cache();
		// Allow the user to change their language if its valid.
		if (!empty($modSettings['userLanguage']) && !empty($_GET['language']))
		{
			$user_info['language'] = strtr($_GET['language'], './\\:', '____');
			$_SESSION['language'] = $user_info['language'];
		}
		redirectexit(base64_decode($_REQUEST['pmxrd']));
	}

	// check if a pmxscriptdebug change requested
	if(isset($_GET['pmxscriptdebug']) && in_array($_GET['pmxscriptdebug'], array('on', 'off')))
	{
		if(allowPmx('pmx_admin'))
			pmx_setcookie('pmxscriptdebug', ($_GET['pmxscriptdebug'] == 'on' ? 1 : ''));

		unset($_GET['pmxscriptdebug']);
		redirectexit(pmx_http_build_query($_GET));
	}

	// redirect on illegal request
	if(!empty($_REQUEST['pmxportal']) || !empty($_REQUEST['pmxsef']) || (!empty($_REQUEST['pmxerror']) && !empty($_REQUEST['action'])))
		redirectexit('pmxerror=unknown');

	// check if a permanent theme change requested
	if(isset($_REQUEST['theme']) && isset($_REQUEST['pmxrd']))
		PortaMx_ChangeTheme($_REQUEST['theme'], $_REQUEST['pmxrd']);

	// get all settings
	PortaMx_getSettings();

	// exit on follow actions or wireless
	$rqaction = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	if(WIRELESS || $doinit || isset($_REQUEST['xml']) || in_array($rqaction, array('jseditor', 'jsoption', '.xml', 'xmlhttp', 'verificationcode', 'printpage')))
		return;

	// login with redirect .. correct SEF url
	if($rqaction == 'login' && !empty($_SESSION['old_url']) && function_exists('pmxsef_query'))
		$_SESSION['old_url'] = $scripturl . pmx_http_build_query(pmxsef_query(rawurldecode(ltrim(str_replace($boardurl, '', $_SESSION['old_url']), '/'))));

	// get the restoreTop cookie
	$cook = pmx_getcookie('YOfs');
	if(!is_null($cook) && empty($_REQUEST['action']) && empty($_REQUEST['pmx_shout']) || (!empty($_REQUEST['action']) && $_REQUEST['action'] != 'vote'))
		pmx_setcookie('YOfs', '');

	// load common javascript
	$context['html_headers'] .= '
	<script language="JavaScript" type="text/javascript" src="'. PortaMx_loadCompressed('PortaMx.js') .'"></script>
	<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
		var pmx_popup_rtl = '. (!empty($context['right_to_left']) ? 'true' : 'false') .';
		var pmx_restore_top = '. (empty($context['pmx']['settings']['restoretop']) || !is_numeric($cook) ? '\'\'' : $cook) .';
		var pmx_rescale_images = [];
	// ]]></script>';

	// on Admin or Moderate load admin language and javascript
	if(($rqaction == 'admin' || $rqaction == 'portamx') && isset($_REQUEST['area']) && in_array($_REQUEST['area'], explode(',', $context['pmx']['areas'])))
	{
		// load the admin javascrip
		$context['html_headers'] .= '
	<script language="JavaScript" type="text/javascript" src="'. PortaMx_loadCompressed('PortaMxAdmin.js') .'"></script>';

		// admin languages
		loadLanguage($context['pmx_templatedir'] .'Admin');
	}

	// Error request?
	if(!empty($_REQUEST['pmxerror']))
		return PmxError();

	// check Error request, Forum request
	$context['pmx']['forumReq'] = (!empty($_REQUEST['action']) || !empty($context['current_board']) || !empty($context['current_topic']));
	if(empty($context['pmx']['forumReq']) && !empty($context['pmx']['settings']['other_actions']))
	{
		$reqtyp = Pmx_StrToArray($context['pmx']['settings']['other_actions']);
		foreach($reqtyp as $rtyp)
		{
			@list($rtyp, $rval) = Pmx_StrToArray($rtyp, '=');
			$context['pmx']['forumReq'] = ($context['pmx']['forumReq'] || (isset($_REQUEST[$rtyp]) && (is_null($rval) || $_REQUEST[$rtyp] == $rval)));
		}
	}

	// check Page, category, article request
	$pmxRequestTypes = array('spage', 'art', 'cat', 'child');
	$context['pmx']['pageReq'] = array();
	foreach($pmxRequestTypes as $type)
	{
		if(empty($_REQUEST['action']) && !empty($_REQUEST[$type]))
			$context['pmx']['pageReq'][$type] = PortaMx_makeSafe($_REQUEST[$type]);
	}

	// redirect Forum requests on Helpdesk standalone
	if(!empty($modSettings['helpdesk_active']) && !empty($modSettings['shd_helpdesk_only']))
	{
		if(empty($context['pmx']['pageReq']) && (!empty($context['current_board']) || !empty($context['current_topic'])))
			redirectexit('action=helpdesk');
	}

	// no request on forum or pages and no frontpage .. go to forum
	if(empty($context['pmx']['forumReq']) && empty($context['pmx']['pageReq']) && $context['pmx']['settings']['frontpage'] == 'none')
	{
		$_REQUEST['action'] = $_GET['action'] = 'community';
		$context['pmx']['forumReq'] = true;
	}

	// Disable HighSlide on action?
	if(isset($_REQUEST['action']) && isset($context['pmx']['settings']['noHS_onaction']))
	{
		$noHighSlide = isset($context['pmx']['settings']['noHS_onaction']) ? Pmx_StrToArray($context['pmx']['settings']['noHS_onaction']) : array();
		if(in_array($_REQUEST['action'], $noHighSlide))
			$context['pmx']['settings']['disableHS'] = 1;
	}

	// Admin panel/block hidding ?
	$hideRequest = array_intersect($context['pmx']['extracmd'] , array_keys($_REQUEST));
	if(!empty($hideRequest) && allowPmx('pmx_admin'))
	{
		@list($hideRequest) = array_values($hideRequest);
		$mode = substr($hideRequest, 5);
		$hidetyp = substr($hideRequest, 0, 5);
		$offparts = empty($modSettings['pmx_'. $hidetyp .'off']) ? array() : Pmx_StrToArray($modSettings['pmx_'. $hidetyp .'off']);
		if($mode == 'off')
		{
			if($hidetyp == 'panel')
				$offparts = array_intersect(($_REQUEST[$hideRequest] == 'all' ? $context['pmx']['block_sides'] : array_merge($offparts, Pmx_StrToArray($_REQUEST[$hideRequest]))), $context['pmx']['block_sides']);
			else
				$offparts = array_merge($offparts, Pmx_StrToIntArray($_REQUEST[$hideRequest]));
		}
		else
		{
			if($hidetyp == 'panel')
				$offparts = array_intersect(($_REQUEST[$hideRequest] == 'all' ?  array() : array_diff($offparts, Pmx_StrToArray($_REQUEST[$hideRequest]))), $context['pmx']['block_sides']);
			else
				$offparts = $_REQUEST[$hideRequest] == 'all' ?  array() : array_diff($offparts, Pmx_StrToIntArray($_REQUEST[$hideRequest]));
		}
		updateSettings(array('pmx_'. $hidetyp .'off' => implode(',', $offparts)));
		unset($_GET[$hideRequest]);
		redirectexit(pmx_http_build_query($_GET));
	}

	// check all the actions and more...
	if(empty($context['pmx']['forumReq']))
	{
		// if a redirect request, exit
		$requrl = (strpos($_SERVER['REQUEST_URL'], substr($scripturl, 0, strrpos($scripturl, '/'))) === false ? $_SERVER['REQUEST_URL'] : $scripturl);
		if(substr($requrl, 0, strrpos($requrl, '/')) != substr($scripturl, 0, strrpos($scripturl, '/')))
			return;

		// we use the frontpage ?
		$useFront = ($context['pmx']['settings']['frontpage'] == 'none' && empty($context['pmx']['pageReq'])) ? '' : 'frontpage';

		// get all block on active panels they can view
		$context['pmx']['viewblocks'] = getPanelsToShow($useFront);

		// frontpage and/or Page blocks exist ?
		if(empty($useFront) || !empty($context['pmx']['show_pagespanel']) || (!empty($context['pmx']['show_frontpanel']) && $context['pmx']['settings']['frontpage'] != 'none'))
		{
			// disable HighSlide on Frontpage?
			if(!empty($context['pmx']['settings']['disableHSonfront']))
				$context['pmx']['settings']['disableHS'] = true;

			// setup headers
			PortaMx_headers('frontpage');
			$context['robot_no_index'] = (empty($context['pmx']['settings']['indexfront']));

			if($context['pmx']['settings']['frontpage'] == 'fullsize')
			{
				loadTemplate($context['pmx_templatedir'] .'Frontpage'. ($context['pmx_style_isCore'] ? '_core' : ''));
				$context['template_layers'] = array('fronthtml', 'portamx');
			}
			else
			{
				loadTemplate($context['pmx_templatedir'] .'Mainindex');
				$context['template_layers'][] = 'portamx';
			}
			if(!empty($context['pmx']['pageReq']) || (empty($context['pmx']['forumReq']) && $context['pmx']['settings']['frontpage'] != 'none'))
				loadTemplate($context['pmx_templatedir'] .'PortaMx');
		}

		// frontpage empty or locked
		else
		{
			// page req error?
			if(!empty($context['pmx']['pageReq']) && empty($context['pmx']['show_pagespanel']))
				redirectexit('pmxerror=page');

			// else go to forum
			$_REQUEST['action'] = $_GET['action'] = (!empty($maintenance) && empty($user_info['is_admin']) ? '' : 'community');
			$context['pmx']['forumReq'] = true;
			$context['pmx']['viewblocks'] = null;
		}
	}

	if(!empty($context['pmx']['forumReq']))
	{
		// get the action
		$action = (isset($_REQUEST['action']) ? ($_REQUEST['action'] == 'collapse' ? 'community' : $_REQUEST['action']) : (isset($_REQUEST['board']) ? 'boards' : (isset($_REQUEST['topic']) ? 'topics' : '')));

		// get all block on active panels they can view
		$context['pmx']['viewblocks'] = getPanelsToShow($action);

		// setup headers
		PortaMx_headers($action);

		// load the "Main" template on pages, cats or arts
		if(!empty($context['pmx']['pageReq']))
			loadTemplate($context['pmx_templatedir'] .'PortaMx');

		loadTemplate($context['pmx_templatedir'] .'Mainindex');
		$context['template_layers'][] = 'portamx';
	}

	// Load the Frame template
	loadTemplate($context['pmx_templatedir'] .'Frames');

	// supress these links if ECL not accepted
	if(!empty($rqaction) && !pmx_checkECL_Cookie() && in_array($rqaction, array('calendar', 'markasread', 'profile', 'stats', 'mlist', 'who')))
		pmx_ECL_Error('request');

	// Create the linktree
	return pmx_MakeLinktree();
}

/**
* error requested
*/
function PmxError()
{
	global $context, $txt;

	// get all block on active panels
	$context['pmx']['pageReq'] = array();
	$action = 'frontpage';
	$context['pmx']['viewblocks'] = getPanelsToShow($action);

	// setup headers
	PortaMx_headers($action);

	if($context['pmx']['settings']['frontpage'] == 'fullsize')
	{
		loadTemplate($context['pmx_templatedir'] .'Frontpage'. ($context['pmx_style_isCore'] ? '_core' : ''));
		$context['template_layers'] = array('fronthtml', 'portamx');
	}
	else
	{
		loadTemplate($context['pmx_templatedir'] .'Mainindex');
		$context['template_layers'][] = 'portamx';
	}
	loadTemplate($context['pmx_templatedir'] .'Error');
	loadTemplate($context['pmx_templatedir'] .'Frames');

	if(in_array($_REQUEST['pmxerror'], array('page', 'article', 'category', 'unknown')))
	{
		$context['pmx_error_title'] = $txt[$_REQUEST['pmxerror'] .'_reqerror_title'];
		$context['pmx_error_text'] = $txt[$_REQUEST['pmxerror'] .'_reqerror_msg'];
	}
	elseif($_REQUEST['pmxerror'] == 'front')
	{
		$context['pmx_error_title'] = $txt['front_reqerror_title'];
		$context['pmx_error_text'] = $txt['front_reqerror_msg'];
	}
	else
	{
		$context['pmx_error_title'] = $txt['download_error_title'];
		if($_REQUEST['pmxerror'] == 'acs')
			$context['pmx_error_text'] = $txt['download_acces_error'];
		elseif($_REQUEST['pmxerror'] == 'fail')
			$context['pmx_error_text'] = $txt['download_notfound_error'];
		else
			$context['pmx_error_text'] = $txt['download_unknown_error'];
	}
	return pmx_MakeLinktree();
}

/**
* Create Linktree and Page Title
**/
function pmx_MakeLinktree()
{
	global $context, $scripturl, $txt, $mbname;

	// Setup page title
	if(empty($context['current_board']) && empty($context['current_topic']) && empty($_REQUEST['action']))
		$context['page_title'] = $context['forum_name'];

	// build the linktree
	$pmxforum = array();
	if(empty($context['linktree']))
		$context['linktree'] = array(array('url' => $scripturl, 'name' => $mbname));

	if(!empty($_GET['pmxerror']))
		$pmxforum[] = array('url' => $scripturl . '?pmxerror='. $_GET['pmxerror'], 'name' => $context['pmx_error_title']);

	$pmxhome[] = array_shift($context['linktree']);
	$inForum = !empty($context['current_board']) || !empty($context['current_topic']) || (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], array('community', 'unread', 'unreadreplies', 'markasread')) || isset($_REQUEST['board']) || isset($_REQUEST['topic']));
	if($context['pmx']['settings']['frontpage'] != 'none' && empty($context['pmx']['pageReq']) && !empty($context['pmx']['showhome']) && !empty($inForum))
		$pmxhome[] = array('url' => $scripturl . '?action=community', 'name' => $txt['forum']);

	if(!empty($context['pmx']['pageReq']))
	{
		if(!isset($context['pmx']['pagenames']))
			$context['pmx']['pagenames'] = $txt['page_reqerror_title'];
		else
		{
			if(array_key_exists('spage', $context['pmx']['pagenames']))
			{
				$pmxforum[] = array('url' => $scripturl . '?spage='. $_GET['spage'], 'name' => $context['pmx']['pagenames']['spage']);
				$context['page_title'] .= ' - '. $context['pmx']['pagenames']['spage'];
			}
			else
			{
				if(array_key_exists('cat', $context['pmx']['pagenames']))
				{
					$pmxforum[] = array('url' => $scripturl . '?cat='. $_GET['cat'], 'name' => $context['pmx']['pagenames']['cat']);
					$context['page_title'] .= ' - '. $context['pmx']['pagenames']['cat'];
				}
				if(array_key_exists('child', $context['pmx']['pagenames']))
				{
					$pmxforum[] = array('url' => $scripturl . '?cat='. $_GET['cat'] .';child='. $_GET['child'], 'name' => $context['pmx']['pagenames']['child']);
					$context['page_title'] = $context['forum_name'] .' - '. $context['pmx']['pagenames']['child'];
				}
				if(array_key_exists('art', $context['pmx']['pagenames']))
				{
					$context['page_title'] .= ' - '. $context['pmx']['pagenames']['art'];
					if(array_key_exists('child', $context['pmx']['pagenames']))
						$pmxforum[] = array('url' => $scripturl . '?cat='. $_GET['cat'] .';child='. $_GET['child'] .';art='. $_GET['art'], 'name' => $context['pmx']['pagenames']['art']);
					elseif(array_key_exists('cat', $context['pmx']['pagenames']))
						$pmxforum[] = array('url' => $scripturl . '?cat='. $_GET['cat'] .';art='. $_GET['art'], 'name' => $context['pmx']['pagenames']['art']);
					else
						$pmxforum[] = array('url' => $scripturl . '?art='. $_GET['art'], 'name' => $context['pmx']['pagenames']['art']);
				}
			}
		}
	}

	if(empty($pmxforum))
		$context['linktree'] = array_merge($pmxhome, $context['linktree']);
	else
		$context['linktree'] = array_merge($pmxhome, $pmxforum, $context['linktree']);
}
?>
