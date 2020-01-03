<?php
/**
* \file PortaMxIntegrate.php
* Integration functions for PortaMx.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('SMF'))
	die('This Portamx file can\'t be run without SMF');

/**
* check guest can browse the dorum.
* called from index.php
**/
function PortaMx_allow_guest($board, $topic)
{
	global $sourcedir;

	if((empty($_REQUEST['action']) && empty($board) && empty($topic)) || PortaMx_frontactions())
		return 'PortaMx';
	else
	{
		require_once($sourcedir . '/Subs-Auth.php');
		return 'KickGuest';
	}
}

/**
* check if wirelees active.
* called from index.php
**/
function PortaMx_wireless()
{
	global $sourcedir;

	if(WIRELESS)
	{
		require_once($sourcedir . '/BoardIndex.php');
		return 'BoardIndex';
	}
	else
		return 'PortaMx';
}

/**
* tell the index.php if we have frontactions.
* called from index.php and PortaMx_allow_guest
**/
function PortaMx_frontactions()
{
	global $modSettings;

	$result = false;
	foreach(array('spage', 'cat', 'act', 'pmxerror') as $act)
		$result = empty($_REQUEST['action']) && !empty($_REQUEST[$act]) ? true : $result;

	// if helpdesk not active disable shd standalone
	if(empty($modSettings['helpdesk_active']))
		$modSettings['shd_helpdesk_only'] = false;

	// emulate a frontaction for SimpleDesc stand alone mode
	elseif(!empty($modSettings['shd_helpdesk_only']) && empty($_REQUEST['action']))
		$result = true;

	return $result;
}

/**
* Add actions to the index actions array
* Called from hook integrate_actions
**/
function PortaMx_Actions(&$actiondata)
{
	global $modSettings;

	$actiondata = array_merge(
		array(
			'community' => (empty($modSettings['shd_helpdesk_only']) ? array('BoardIndex.php', 'BoardIndex') : array('sd_source/SimpleDesk.php', 'shd_main')),
			'portamx' => array('PortaMx/PortaMxAllocator.php', 'PortaMxAllocator'),
		),
		$actiondata
	);
}

/**
* Add Admin menu context
* Called from hook integrate_admin_areas
**/
function PortaMx_AdminMenu(&$menudata)
{
	global $txt, $context, $scripturl;

	// insert Portamx after 'config'
	$fnd = array_search('config', array_keys($menudata)) +1;
	$menudata = array_merge(
		array_slice($menudata, 0, $fnd),
		array(
			'portamx' => array(
				'title' => 'PortaMx',
				'areas' => array(
					'pmx_center' => array(
						'label' => $txt['pmx_admincenter'],
						'icon' => 'pmx_adm_center.gif',
						'file' => $context['pmx_templatedir'] .'AdminCenter.php',
						'function' => 'PortaMx_AdminCenter',
						'permission' => array('admin_forum'),
					),
					'pmx_settings' => array(
						'label' => $txt['pmx_settings'],
						'icon' => 'pmx_adm_settings.gif',
						'file' => $context['pmx_templatedir'] .'AdminSettings.php',
						'function' => 'PortaMx_AdminSettings',
						'permission' => array('admin_forum'),
					),
					'pmx_blocks' => array(
						'label' => $txt['pmx_blocks'],
						'icon' => 'pmx_adm_blocks.gif',
						'file' => $context['pmx_templatedir'] .'AdminBlocks.php',
						'function' => 'PortaMx_AdminBlocks',
						'permission' => array('admin_forum'),
					),
					'pmx_categories' => array(
						'label' => $txt['pmx_categories'],
						'icon' => 'pmx_adm_categories.gif',
						'file' => $context['pmx_templatedir'] .'AdminCategories.php',
						'function' => 'PortaMx_AdminCategories',
						'permission' => array('admin_forum'),
					),
					'pmx_articles' => array(
						'label' => $txt['pmx_articles'],
						'icon' => 'pmx_adm_articles.gif',
						'file' => $context['pmx_templatedir'] .'AdminArticles.php',
						'function' => 'PortaMx_AdminArticles',
						'permission' => array('admin_forum'),
					),
					'pmx_sefengine' => array(
						'label' => $txt['pmx_sefengine'],
						'icon' => 'pmx_adm_sef.gif',
						'file' => $context['pmx_templatedir'] .'AdminSettings.php',
						'function' => 'PortaMx_AdminSettings',
						'permission' => array('admin_forum'),
					),
					'pmx_languages' => array(
						'label' => $txt['pmx_languages'],
						'icon' => 'pmx_adm_languages.gif',
						'file' => $context['pmx_templatedir'] .'AdminCenter.php',
						'function' => 'PortaMx_ShowLanguages',
						'permission' => array('admin_forum'),
					),
				),
			),
		),
		array_slice($menudata, $fnd, count($menudata) - $fnd)
	);
}

/**
* Add menu to MenuContext
* Called from hook integrate_menu_buttons
**/
function PortaMx_MenuContext(&$menudata)
{
	global $txt, $context, $modSettings, $scripturl;

	// Init the Portal if not loaded
	if(!defined('PortaMx'))
		PortaMx(true);

	// put a "Home" button at first on SD stand alone
	if(!empty($context['pmx']['showhome']) && !empty($modSettings['shd_helpdesk_only']))
		$menudata = array_merge(
			array(
				'front' => array(
					'title' => $txt['home'],
					'href' => $scripturl,
					'active_button' => false,
					'sub_buttons' => array(
					),
				),
			),
			$menudata
		);

	// add community button after 'home'
	$fnd = array_search('home', array_keys($menudata)) + 1;
	if(!empty($context['pmx']['showhome']) && empty($modSettings['shd_helpdesk_only']))
	{
		$menudata = array_merge(
			array_slice($menudata, 0, $fnd),
			array(
				'community' => array(
					'title' => $txt['forum'],
					'href' => $scripturl . '?action=community',
					'active_button' => false,
					'sub_buttons' => array(
					),
				),
			),
			array_slice($menudata, $fnd, count($menudata) - $fnd)
		);
		$fnd++;
	}

	// add download button if enabled and accessible
	$dlact = array(0 => '', 1 => '');
	$dlactErr = array(0 => '', 1 => '');
	$dlaccess = isset($context['pmx']['settings']['dl_access']) ? $context['pmx']['settings']['dl_access'] : '';
	if(allowPmxGroup($dlaccess) && !empty($context['pmx']['settings']['download']) && preg_match('/(p:|c:|a:|)(.*)$/i', $context['pmx']['settings']['dl_action'], $match) > 0)
	{
		if($match[1] == 'a:')
			$dlact = array(0 => 'art', 1 => $match[2]);
		elseif($match[1] == 'c:')
			$dlact = array(0 => 'cat', 1 => $match[2]);
		elseif($match[1] == 'p:')
			$dlact = array(0 => 'spage', 1 => $match[2]);
		else
			$dlact = array(0 => 'action', 1 => $match[2]);

		if(!empty($_REQUEST['pmxerror']) && in_array($_REQUEST['pmxerror'], array('acs', 'fail')))
			$dlactErr = array(0 => 'pmxerror', 1 => $_REQUEST['pmxerror']);

		$menudata = array_merge(
			array_slice($menudata, 0, $fnd),
			array(
				'download' => array(
					'title' => $txt['download'],
					'href' => $scripturl .'?'. $dlact[0] .'='. $dlact[1],
					'active_button' => false,
					'sub_buttons' => array(
					),
				),
			),
			array_slice($menudata, $fnd, count($menudata) - $fnd)
		);

		if((isset($_REQUEST[$dlact[0]]) && $_REQUEST[$dlact[0]] == $dlact[1]) || (isset($_REQUEST[$dlactErr[0]]) && $_REQUEST[$dlactErr[0]] == $dlactErr[1]))
			$context['current_action'] = 'download';
	}

	// add admin submenu
	if(!empty($context['allow_admin']))
	{
		// add submenu before 'featuresettings'
		$fnd = array_search('featuresettings', array_keys($menudata['admin']['sub_buttons']));
		$menudata['admin']['sub_buttons'] = array_merge(
			array_slice($menudata['admin']['sub_buttons'], 0, $fnd),
			array(
				'pmx_center' => array(
					'title' => $txt['pmx_admincenter'],
					'href' => $scripturl . '?action=admin;area=pmx_center',
					'sub_buttons' => array(
						'pmxsettings' => array(
							'title' => $txt['pmx_settings'],
							'href' => $scripturl . '?action=admin;area=pmx_settings;'. $context['session_var'] .'='. $context['session_id'],
						),
						'pmxblocks' => array(
							'title' => $txt['pmx_blocks'],
							'href' => $scripturl . '?action=admin;area=pmx_blocks;'. $context['session_var'] .'='. $context['session_id'],
						),
						'pmxcategories' => array(
							'title' => $txt['pmx_categories'],
							'href' => $scripturl . '?action=admin;area=pmx_categories;'. $context['session_var'] .'='. $context['session_id'],
						),
						'pmxarticles' => array(
							'title' => $txt['pmx_articles'],
							'href' => $scripturl . '?action=admin;area=pmx_articles;'. $context['session_var'] .'='. $context['session_id'],
						),
						'pmxsefengine' => array(
							'title' => $txt['pmx_sefengine'],
							'href' => $scripturl . '?action=admin;area=pmx_sefengine;'. $context['session_var'] .'='. $context['session_id'],
						),
						'pmxlanguages' => array(
							'title' => $txt['pmx_languages'],
							'href' => $scripturl . '?action=admin;area=pmx_languages;'. $context['session_var'] .'='. $context['session_id'],
						),
					),
				),
			),
			array_slice($menudata['admin']['sub_buttons'], $fnd, count($menudata['admin']['sub_buttons']) - $fnd)
		);
	}

	// modify profile menu
	if(allowedTo('profile_view_own') && allowPmx('pmx_admin, pmx_blocks, pmx_articles, pmx_create', true))
	{
		$addmenu = array(
			'portamx' => array(
				'title' => $txt['pmx_managers'],
				'href' => '#',
				'show' => true,
				'sub_buttons' => array(
					'pmxcenter' => array(
						'title' => $txt['pmx_admincenter'],
						'href' => $scripturl . '?action=portamx;area=pmx_center',
						'show' => allowPmx('pmx_admin', true),
					),
					'pmxsettings' => array(
						'title' => $txt['pmx_settings'],
						'href' => $scripturl . '?action=portamx;area=pmx_settings',
						'show' => allowPmx('pmx_admin', true),
					),
					'pmxblocks' => array(
						'title' => $txt['pmx_blocks'],
						'href' => $scripturl . '?action=portamx;area=pmx_blocks',
						'show' => allowPmx('pmx_admin, pmx_blocks', true),
					),
					'pmxcategories' => array(
						'title' => $txt['pmx_categories'],
						'href' => $scripturl . '?action=portamx;area=pmx_categories',
						'show' => allowPmx('pmx_admin', true),
					),
					'pmxarticles' => array(
						'title' => $txt['pmx_articles'],
						'href' => $scripturl . '?action=portamx;area=pmx_articles',
						'show' => allowPmx('pmx_admin, pmx_articles, pmx_create', true),
					),
					'pmxsefengine' => array(
						'title' => $txt['pmx_sefengine'],
						'href' => $scripturl . '?action=portamx;area=pmx_sefengine',
						'show' => allowPmx('pmx_admin', true),
					),
				),
			),
		);

		foreach($addmenu['portamx']['sub_buttons'] as $button => $value)
		{
			if(empty($value['show']))
				unset($addmenu['portamx']['sub_buttons'][$button]);
		}

		$menudata['profile']['sub_buttons'] = array_merge(
			$menudata['profile']['sub_buttons'],
			$addmenu
		);
	}

	/**
	* Highlight the active button
	**/
	// SimpleDesk stand alone mode ?
	if(!empty($context['pmx']['showhome']) && empty($modSettings['shd_helpdesk_only']))
	{
		if(isset($_REQUEST['board']) || isset($_REQUEST['topic']))
			$context['current_action'] = 'community';
		elseif(!empty($_REQUEST['action']) && in_array($_REQUEST['action'], array('community', 'recent', 'unreadreplies', 'unread', 'who', 'collapse')))
			$context['current_action'] = 'community';
	}
	elseif(!empty($context['pmx']['showhome']))
	{
		if(empty($_REQUEST['action']) && empty($_REQUEST['board']) && empty($_REQUEST['topic']))
			$context['current_action'] = empty($modSettings['shd_helpdesk_only']) ? 'home' : 'front';
	}

	// Highlight the profile button on this..
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'portamx' && allowPmx('pmx_admin, pmx_blocks, pmx_articles, pmx_create', true))
		$context['current_action'] = 'profile';
}

/**
* Add menu to Profile Menu
* Called from hook integrate_profile_areas
**/
function PortaMx_ProfileMenu(&$menudata)
{
	global $txt, $context, $scripturl;

	if(allowedTo('profile_view_own') && allowPmx('pmx_admin, pmx_blocks, pmx_articles, pmx_create', true))
		$menudata = array_merge(
			$menudata,
			array(
			'portamx' => array(
				'title' => $txt['pmx_managers'],
				'areas' => array(
					'pmxcenters' => array(
						'label' => $txt['pmx_admincenter'],
						'custom_url' => $scripturl . '?action=portamx;area=pmx_center',
						'enabled' => allowPmx('pmx_admin', true),
						'permission' => array(
							'own' => 'profile_view_own',
						),
					),
					'pmxsettings' => array(
						'label' => $txt['pmx_settings'],
						'custom_url' => $scripturl . '?action=portamx;area=pmx_settings',
						'enabled' => allowPmx('pmx_admin', true),
						'permission' => array(
							'own' => 'profile_view_own',
						),
					),
					'pmxblocks' => array(
						'label' => $txt['pmx_blocks'],
						'custom_url' => $scripturl . '?action=portamx;area=pmx_blocks',
						'enabled' => allowPmx('pmx_admin, pmx_blocks', true),
						'permission' => array(
							'own' => 'profile_view_own',
						),
					),
					'pmxcategories' => array(
						'label' => $txt['pmx_categories'],
						'custom_url' => $scripturl . '?action=portamx;area=pmx_categories',
						'enabled' => allowPmx('pmx_admin', true),
						'permission' => array(
							'own' => 'profile_view_own',
						),
					),
					'pmxarticles' => array(
						'label' => $txt['pmx_articles'],
						'custom_url' => $scripturl . '?action=portamx;area=pmx_articles',
						'enabled' => allowPmx('pmx_admin, pmx_articles, pmx_create', true),
						'permission' => array(
							'own' => 'profile_view_own',
						),
					),
					'pmxsefengine' => array(
						'label' => $txt['pmx_sefengine'],
						'custom_url' => $scripturl . '?action=portamx;area=pmx_sefengine',
						'enabled' => allowPmx('pmx_admin', true),
						'permission' => array(
							'own' => 'profile_view_own',
						),
					),
				),
			),
		)
	);
}

/**
* add actions for the Who display
* Called from hook integrate_whos_online
**/
function PortaMx_whos_online($actions)
{
	global $txt, $context, $scripturl;

	$result = '';
	if(!empty($actions['action']) && $actions['action'] == 'community')
		$result = $txt['who_index'];

	elseif(isset($actions['spage']) || isset($actions['art']) || isset($actions['cat']) || isset($actions['child']))
		$result = getWhoTitle($actions);

	elseif(empty($actions['action']) && empty($actions['topic']) && empty($actions['board']))
	{
		$frontpage = true;
		if(!empty($context['pmx']['settings']['other_actions']))
		{
			$reqtyp = Pmx_StrToArray($context['pmx']['settings']['other_actions']);
			foreach($reqtyp as $rtyp)
			{
				@list($rtyp, $rval) = Pmx_StrToArray($rtyp, '=');
				$frontpage = (isset($_REQUEST[$rtyp]) && (is_null($rval) || $_REQUEST[$rtyp] == $rval) ? false : $frontpage);
			}
		}
		if(!empty($frontpage))
			$result = $txt['pmx_who_frontpage'];
	}

	elseif(!empty($actions['action']) && allowPmx('pmx_admin'))
	{
		if($actions['action'] == 'portamx' && isset($txt['pmx_who_acts'][$actions['area']]))
			$result = sprintf($txt['pmx_who_portamx'], $txt['pmx_who_acts'][$actions['area']]);

		elseif(isset($actions['area']) && $actions['action'] == $actions['area'] && isset($txt['pmx_who_acts'][$actions['area']]))
			$result = sprintf($txt['pmx_who_portamx'], $txt['pmx_who_acts'][$actions['area']]);
	}

	return $result;
}

/**
* Logout
* Called from hook integrate_logout
**/
function PortaMx_logout($membername)
{
	if(isset($_SESSION['pmxcookie']))
		unset($_SESSION['pmxcookie']);
}
?>
