<?php
/**
* \file AdminSettings.php
* AdminSettings reached all Posts from Settings Manager.
* Checks the values and saved the parameter to the database.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* Receive all the Posts from Settings Manager, check and save it.
* Finally the Admin settings are prepared and the templare loaded.
*/
function PortaMx_AdminSettings()
{
	global $boarddir, $smcFunc, $context, $settings, $modSettings, $user_info, $txt, $pmxCacheFunc;

	$admMode = PortaMx_makeSafe($_GET['action']);
	$pmx_area = PortaMx_makeSafe($_GET['area']);
	$newBlockSide = '';

	if(($admMode == 'admin' || $admMode == 'portamx') && ($pmx_area == 'pmx_settings' || $pmx_area == 'pmx_sefengine') && allowPmx('pmx_admin'))
	{
		require_once($context['pmx_sourcedir'] .'AdminSubs.php');
		$context['pmx']['subaction'] = isset($_GET['sa']) ? $_GET['sa'] : ($pmx_area == 'pmx_sefengine' ? '' : 'globals');

		// From template ?
		if(PortaMx_checkPOST())
		{
			checkSession('post');

			// check the Post array
			if(isset($_POST['save_settings']) && !empty($_POST['save_settings']))
			{
				// check defined numeric vars (check_num_vars holds the posted array to check like [varname][varname] ...)
				if(isset($_POST['check_num_vars']))
				{
					foreach($_POST['check_num_vars'] as $val)
					{
						$data = explode(',', $val);
						$post = '$_POST'. str_replace(array('[', ']'), array('[\'', '\']'), $data[0]);
						if(eval("return isset($post);") && eval("return !is_numeric($post);"))
								eval("$post = $data[1];");
					}
					unset($_POST['check_num_vars']);
				}

				// access update?
				if(!empty($_POST['update_access']))
				{
					$perms = array('pmx_promote' => array(), 'pmx_create' => array(), 'pmx_articles' => array(), 'pmx_blocks' => array(), 'pmx_admin' => array());
					if(isset($_POST['setaccess']))
						foreach($_POST['setaccess'] as $acsname => $acsdata)
							$perms[$acsname] = $acsdata;

					$smcFunc['db_insert']('replace', '
						{db_prefix}portamx_settings',
						array(
							'varname' => 'string',
							'config' => 'string',
						),
						array(
							'permissions',
							serialize($perms)
						),
						array('varname')
					);
				}

				// SEF engine update?
				elseif(!empty($_POST['update_pmxsef']))
				{
					$arrayToken = array('pmxsef_stripchars', 'pmxsef_wireless', 'pmxsef_actions');
					foreach($_POST as $token => $value)
					{
						if(substr($token, 0, 7) == 'pmxsef_')
						{
							// check...
							if($token == 'pmxsef_spacechar')
								$_POST[$token] = (!in_array(substr($_POST[$token], 0, 1), array('-', '_', '')) ? '-' : (!empty($_POST[$token]) ? substr($_POST[$token], 0, 1) : ''));
							elseif($token == 'pmxsef_ssefspace')
								$_POST[$token] = substr($_POST[$token], 0, 1);
							elseif($token == 'pmxsef_aliasactions')
							{
								$alias = array();
								$tmp = Pmx_StrToArray($_POST[$token], ',');
								foreach($tmp as $d)
								{
									$t = Pmx_StrToArray($d, '=');
									if(!in_array($t[0], array('admin', 'portamx')))
										$alias[$t[1]] = $t[0];
								}
								$_POST[$token] = pmx_serialize($alias);
							}
							elseif($token == 'pmxsef_ignorerequests')
							{
								$alias = array();
								$tmp = Pmx_StrToArray($_POST[$token], ',');
								foreach($tmp as $d)
								{
									$t = Pmx_StrToArray($d, '=');
									$alias[$t[0]] = $t[1];
								}
								$_POST[$token] = pmx_serialize($alias);
							}
							elseif(in_array($token, $arrayToken))
								$_POST[$token] = implode(',', Pmx_StrToArray($_POST[$token], ','));

							if($token != 'pmxsef_enable')
								$smcFunc['db_insert']('replace', '
									{db_prefix}settings',
									array('variable' => 'string', 'value' => 'string'),
									array($token, $_POST[$token]),
									array('variable')
								);
						}
					}

					// alway disable SEF if no .htaccess or web.config found
					if((file_exists($boarddir .'/.htaccess') || file_exists($boarddir .'/web.config')) == false)
						$_POST['pmxsef_enable'] = '0';

					// setup the the SMF hooks
					$hooklist = array(
						'integrate_pre_load' => 'pmxsef_convertSEF',
						'integrate_buffer' => 'ob_pmxsef',
						'integrate_redirect' => 'pmxsef_Redirect',
						'integrate_outgoing_email' => 'pmxsef_EmailOutput',
						'integrate_exit' => 'pmxsef_XMLOutput',
						'integrate_fix_url' => 'pmxsef_fixurl',
					);

					// get the hooks from database
					$smfhooks = array();
					$request = $smcFunc['db_query']('', '
						SELECT variable, value FROM {db_prefix}settings
						WHERE variable IN ({array_string:hooks})',
						array('hooks' => array_keys($hooklist))
					);
					if($smcFunc['db_num_rows']($request) > 0)
					{
						while($row = $smcFunc['db_fetch_assoc']($request))
							$smfhooks[$row['variable']] = $row['value'];
						$smcFunc['db_free_result']($request);
					}

					// update the hooks
					foreach($hooklist as $hookname => $value)
					{
						if(isset($smfhooks[$hookname]))
							$smfhooks[$hookname] = trim((!empty($_POST['pmxsef_enable']) ? $value .',' : '') . trim(str_replace($value, '', $smfhooks[$hookname]), ','), ',');
						else
							$smfhooks[$hookname] = trim(!empty($_POST['pmxsef_enable']) ? $value : '');

						$smcFunc['db_insert']('replace', '
							{db_prefix}settings',
							array('variable' => 'string', 'value' => 'string'),
							array($hookname, $smfhooks[$hookname]),
							array('variable')
						);
					}

					// clear all cached values
					$pmxCacheFunc['clear']('pmxsef_boardlist', false);
					$pmxCacheFunc['clear']('pmxsef_topiclist', false);
					$pmxCacheFunc['clear']('pmxsef_pageslist', false);
					$pmxCacheFunc['clear']('pmxsef_catlist', false);
					$pmxCacheFunc['clear']('pmxsef_artlist', false);
					$pmxCacheFunc['clear']('pmxsef_userlist', false);
				}

				// other settings update
				else
				{
					$config = array();
					$request = $smcFunc['db_query']('', '
							SELECT config
							FROM {db_prefix}portamx_settings
							WHERE varname = {string:settings}',
						array('settings' => 'settings')
					);
					if($smcFunc['db_num_rows']($request) > 0)
					{
						$row = $smcFunc['db_fetch_assoc']($request);
						$smcFunc['db_free_result']($request);
						$config = unserialize($row['config']);
					}

					$setKeys = array_diff(array_keys($_POST), array('pmx_fronttheme', 'pmx_frontthempg', 'pmx_ecl', 'pmx_eclmodal', 'save_settings', 'sa', 'sc'));
					foreach($setKeys as $key)
					{
						if($key == 'promotes')
						{
							$promo = Pmx_StrToIntArray($_POST[$key]);
							$smcFunc['db_query']('', '
								UPDATE {db_prefix}portamx_settings
									SET config = {string:config}
									WHERE varname = {string:settings}',
								array('config' => pmx_serialize($promo), 'settings' => 'promotes')
							);

							// find all promoted block
							$blocks = null;
							$request = $smcFunc['db_query']('', '
								SELECT id
								FROM {db_prefix}portamx_blocks
								WHERE active = 1 AND blocktype = {string:blocktype}',
								array('blocktype' => 'promotedposts')
							);
							while($row = $smcFunc['db_fetch_assoc']($request))
								$blocks[] = $row['id'];
							$smcFunc['db_free_result']($request);

							$_SESSION['pmx_refresh_promote'] = $blocks;
						}
						else
						{
							if($key == 'dl_access')
								$_POST['dl_access'] = implode(',', $_POST['dl_access']);

							$config[$key] = $_POST[$key];
						}
					}

					$smcFunc['db_query']('', '
						UPDATE {db_prefix}portamx_settings
							SET config = {string:config}
							WHERE varname = {string:settings}',
						array('config' => pmx_serialize($config), 'settings' => 'settings')
					);

					// other settings they stored in smf_settings table
					$setKeys = array('pmx_fronttheme', 'pmx_frontthempg', 'pmx_ecl', 'pmx_eclmodal');
					foreach($setKeys as $key)
					{
						if(isset($_POST[$key]))
							$smcFunc['db_insert']('replace', '
								{db_prefix}settings',
								array(
									'variable' => 'string',
									'value' => 'string',
								),
								array(
									$key,
									$_POST[$key]
								),
								array('variable')
							);
					}

					// set frontmode flag
					$smcFunc['db_insert']('replace', '
						{db_prefix}settings',
						array(
							'variable' => 'string',
							'value' => 'string',
						),
						array(
							'pmx_frontmode',
							($config['frontpage'] == 'none' ? '0' : '1'),
						),
						array('variable')
					);

					if(isset($_POST['pmx_ecl']) && empty($_POST['pmx_ecl']) && isset($_SESSION['pmxcookie']))
						unset($_SESSION['pmxcookie']);
				}

				// clear cached values
				cache_put_data('modSettings', null, 90);
				cache_put_data('menu_buttons-' . implode('_', $user_info['groups']) . '-' . $user_info['language'], null, 90);
				$pmxCacheFunc['clear']('settings', false);
			}
			redirectexit('action='. $admMode .';area='. $pmx_area . (!empty($context['pmx']['subaction']) ? ';sa='. $context['pmx']['subaction'] : '') .';'. $context['session_var'] .'='. $context['session_id']);
		}

		// SEF engine settings ?
		if($pmx_area == 'pmx_sefengine')
		{
			// pmxsef default settings
			$context['pmx']['pmxsef_enable'] = '0';
			$context['pmx']['pmxsef_lowercase'] = '1';
			$context['pmx']['pmxsef_autosave'] = '0';
			$context['pmx']['pmxsef_spacechar'] = '-';
			$context['pmx']['pmxsef_ssefspace'] = '';
			$context['pmx']['pmxsef_stripchars'] = '&quot;,&amp;,&lt;,&gt;,~,!,@,#,$,%,^,&,*,(,),-,=,+,<,[,{,],},>,;,:,\',",/,?,\,|';
			$context['pmx']['pmxsef_wireless'] = 'nowap,wap,wap2,imode,moderate';
			$context['pmx']['pmxsef_singletoken'] = 'add,advanced,all,asc,calendar,check,children,conversation,desc,home,kstart,nw,profile,save,sound,togglebar,topicseen,view,viewweek,xml';
			$context['pmx']['pmxsef_actions'] = 'about:mozilla,about:unknown,activate,admin,announce,attachapprove,buddy,calendar,clock,collapse,community,coppa,credits,deletemsg,display,dlattach,editpoll,editpoll2,emailuser,findmember,groups,help,helpadmin,im,jseditor,jsmodify,jsoption,keepalive,lock,lockvoting,login,login2,logout,markasread,mergetopics,mlist,moderate,modifycat,modifykarma,movetopic,movetopic2,notify,notifyboard,openidreturn,pm,portamx,post,post2,printpage,profile,promote,quotefast,quickmod,quickmod2,recent,register,register2,reminder,removepoll,removetopic2,reporttm,requestmembers,restoretopic,search,search2,sendtopic,smstats,suggest,spellcheck,splittopics,stats,sticky,trackip,unread,unreadreplies,verificationcode,viewprofile,vote,viewquery,viewsmfile,who,.xml,xmlhttp';
			$context['pmx']['pmxsef_ignoreactions'] = '';
			$context['pmx']['pmxsef_aliasactions'] = '';
			$context['pmx']['pmxsef_ignorerequests'] = '';
			$context['pmx']['pmxsef_codepages'] = '/PortaMx/sefcodepages/x';
			$nocheck = array('pmxsef_enable', 'pmxsef_lowercase', 'pmxsef_spacechar');

			// read the settings from database
			$request = $smcFunc['db_query']('', '
				SELECT variable, value FROM {db_prefix}settings
				WHERE variable LIKE {string:variable}',
				array('variable' => 'pmxsef_%')
			);
			if($smcFunc['db_num_rows']($request) > 0)
			{
				while($row = $smcFunc['db_fetch_assoc']($request))
				{
					$value = trim($row['value']);
					if($row['variable'] == 'pmxsef_aliasactions')
					{
						$tmp = unserialize($value);
						if(!empty($tmp))
						{
							foreach($tmp as $act => $alias)
								$context['pmx'][$row['variable']][] = $alias .'='. $act;
							$context['pmx'][$row['variable']] = implode(',', $context['pmx'][$row['variable']]);
						}
					}
					elseif($row['variable'] == 'pmxsef_ignorerequests')
					{
						$tmp = unserialize($value);
						if(!empty($tmp))
						{
							foreach($tmp as $act => $alias)
								$context['pmx'][$row['variable']][] = $act .'='. $alias;
							$context['pmx'][$row['variable']] = implode(',', $context['pmx'][$row['variable']]);
						}
					}
					elseif(in_array($row['variable'], $nocheck) || !empty($value))
						$context['pmx'][$row['variable']] = $value;
				}
				$smcFunc['db_free_result']($request);
			}

			// check if enabled
			$request = $smcFunc['db_query']('', '
				SELECT value FROM {db_prefix}settings
				WHERE variable = {string:hook}',
				array('hook' => 'integrate_pre_load')
			);
			if($smcFunc['db_num_rows']($request) > 0)
			{
				$row = $smcFunc['db_fetch_assoc']($request);
				$smcFunc['db_free_result']($request);
				if(strpos($row['value'], 'pmxsef_convertSEF') !== false)
					$context['pmx']['pmxsef_enable'] = '1';
			}
		}

		// Load data for the other settings
		else
		{
			$context['pmx']['admthemes'] = PortaMx_getsmfThemes();
			$context['pmx']['admgroups'] = PortaMx_getUserGroups(true);
			$context['pmx']['limitgroups'] = PortaMx_getUserGroups(true, false);
			$context['pmx']['acsgroups'] = PortaMx_getUserGroups(false, !empty($context['pmx']['settings']['postcountacs']));
			$context['pmx']['sysstat'] = $pmxCacheFunc['stat']();
			$request = $smcFunc['db_query']('', '
				SELECT variable, value FROM {db_prefix}settings
				WHERE variable IN ({array_string:vars})',
				array('vars' => array('pmx_fronttheme', 'pmx_frontthempg', 'pmx_ecl', 'pmx_eclmodal'))
			);
			if($smcFunc['db_num_rows']($request) > 0)
			{
				while($row = $smcFunc['db_fetch_assoc']($request))
					$context['pmx'][$row['variable']] = $row['value'];
				$smcFunc['db_free_result']($request);
			}
		}

		// setup pagetitle
		$context['page_title'] = $txt['pmx_settings'];
		$context['pmx']['AdminMode'] = $admMode;

		// load language and execute template
		loadLanguage($context['pmx_templatedir'] .'AdminSettings');
		loadTemplate($context['pmx_templatedir'] .'AdminSettings');
	}
	else
		fatal_error($txt['pmx_acces_error']);
}
?>
