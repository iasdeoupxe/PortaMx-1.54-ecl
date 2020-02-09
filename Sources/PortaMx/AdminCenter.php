<?php
/**
* \file AdminCenter.php
* Admininistration Center.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* Create all the Informationen for the Admin Center.
* Finally load the templare.
*/
function PortaMx_AdminCenter()
{
	global $smcFunc, $context, $settings, $scripturl, $sourcedir, $boarddir, $txt, $pmxCacheFunc;

	$admMode = isset($_GET['action']) ? $_GET['action'] : '';
	if(($admMode == 'admin' || $admMode == 'portamx') && allowPmx('pmx_admin'))
	{
		$context['pmx']['subaction'] = isset($_GET['sa']) ? $_GET['sa'] : 'main';
		if($context['pmx']['subaction'] == 'settings')
			$context['pmx']['subaction'] = 'main';
		$context['pmx']['pmx_area'] = $_GET['area'];
		$context['pmx']['admmode'] = $admMode;

		// Admin center main?
		if($context['pmx']['subaction'] == 'main')
		{
			// show the Admin center
			$liveinfo = getLiveInfo();
			$context['pmx_info'] = $liveinfo;
			$context['pmx_info']['installed'] = getInstalledPackage();
			$context['pmx_info']['versionOK'] = !empty($liveinfo['version']) && $liveinfo['version'] <= $context['pmx_info']['installed'];

			// If update available, get server from package_server table
			if(!empty($context['pmx_info']['update']) && empty($context['pmx_info']['versionOK']))
			{
				$request = $smcFunc['db_query']('', '
						SELECT id_server
						FROM {db_prefix}package_servers
						WHERE url = {string:url}',
					array(
						'url' => substr($context['pmx']['server']['url'], 0, -1)
					)
				);
				if($row = $smcFunc['db_fetch_assoc']($request))
				{
					$context['pmx_info']['updserver'] = $row['id_server'];
					$smcFunc['db_free_result']($request);
				}
				else
				{
					$smcFunc['db_insert']('', '
						{db_prefix}package_servers',
						array(
							'name' => 'string',
							'url' => 'string'
						),
						array(
							'PortaMx File Server',
							substr($context['pmx']['server']['url'], 0, -1),
						),
						array('id_server')
					);
					$context['pmx_info']['updserver'] = $smcFunc['db_insert_id']('{db_prefix}package_servers', 'id_server');
				}
			}
		}

		// detailed filelist ?
		elseif($context['pmx']['subaction'] == 'flist')
		{
			checkSession('get');

			$srcdir = $sourcedir;
			$thmdir = $settings['default_theme_dir'];
			$dirs = array(
				'pmx_source_files' => array(
					$srcdir => array('/PortaMx/', '/PortaMx/Class/', '/PortaMx/Class/System/'),
				),
				'pmx_template_files' => array(
					$thmdir => array('/PortaMx/', '/PortaMx/SysCss/', '/PortaMx/BlockCss/', '/PortaMx/Scripts/'),
				),
				'pmx_language_files' => array(
					$thmdir .'/languages' => array('/PortaMx/'),
				),
			);

			$allfiles = array();
			$fileExt = array();
			$installed = getInstalledLanguages();
			foreach($installed as $data)
				$fileExt[] = $data['langext'] .'.php';

			// read all dirs
			foreach($dirs as $dirname => $basedirs)
			{
				foreach($basedirs as $base => $subdirs)
				{
					foreach($subdirs as $dir)
					{
						if(is_dir($base . $dir))
						{
							$files = array();
							if($dh = opendir($base . $dir))
							{
								while(($file = readdir($dh)) !== false)
								{
									if(is_file($base . $dir . $file))
									{
										if(strpos($file, '.min.') === false)
											$files[] = $file;
									}
								}
								closedir($dh);
							}
						}

						if(!empty($files))
							$allfiles[$dirname][$dir] = array(
								'dir' => $base . $dir,
								'subdir' => $dir,
								'files' => $files,
							);
					}
				}
			}
			// cleanup..
			unset($dirs[$dirname]);

			// get lifeinfo
			$currentversion = getLiveInfo('version');

			// Package ...
			$result['pmx_filepackage']['files'][''] = array();
			$result['pmx_filepackage']['current'] = $currentversion;
			$result['pmx_filepackage']['installed'] = getInstalledPackage();

			// check all files
			foreach($allfiles as $dirtext => $dirname)
			{
				$lowdate = '01.01.1900';
				$lowversion = '';
				foreach($dirname as $data)
				{
					$subdir = $data['subdir'];
					foreach($data['files'] as $file)
					{
						if($file != 'index.php' && substr($file, -1, 1) != '~')
						{
							$handle = fopen($data['dir'] . $file, "r");
							$content = fread($handle, 512);
							fclose($handle);

							$versOK = preg_match("~\*\s.version\s([A-Za-z0-9\.\-\s]+)~i", $content, $version) != 0;
							if($versOK && strcasecmp(trim($version[1]), $lowversion) >= 0)
								$lowversion = $version[1];
							$dateOK = preg_match("~\*\s.date\s([A-Za-z0-9\.\-]+)~i", $content, $date) != 0;
							if($dateOK && $date[1] > $lowdate)
								$lowdate = $date[1];

							$result[$dirtext]['files'][$file] = array(
								'subdir' => $subdir,
								'version' => ($versOK ? $version[1] : '?.???'),
								'date' => ($dateOK ? $date[1] : '??.??.????'),
							);
							unset($content);
						}
					}
				}
				$result[$dirtext]['current'] = $currentversion;
				$result[$dirtext]['installed'] = $lowversion;
				$result[$dirtext]['lowdate'] = $lowdate;
			}
			$context['pmx_info'] = $result;
			$context['pmx_installed_ext'] = $fileExt;
			unset($result);
		}

		// show languages?
		elseif($context['pmx']['subaction'] == 'showlang')
		{
			checkSession('get');

			// get all installed languages
			$context['pmx']['instlang'] = getInstalledLanguages();

			// get all existing languages
			$info = readDocServer($context['pmx']['server']['lang']);

			if(!empty($info))
			{
				if(GetFirstChildContentByName($info, 'copyright') == 'PortaMx')
				{
					$elmlist = GetChildByPathAndName($info, '', 'item');
					foreach($elmlist as $elm)
					{
						$context['pmx']['langsets'][] = array(
							'name' => GetFirstChildContentByName($elm, 'name'),
							'version' => GetFirstChildContentByName($elm, 'version'),
							'charset' => GetFirstChildContentByName($elm, 'charset'),
							'link' => GetFirstChildContentByName($elm, 'link'),
						);
					}
				}
			}

			// check for manually installable languages
			getManuallyLanguages($context['pmx']['instlang']);
		}

		// Administrate languages?
		elseif($context['pmx']['subaction'] == 'admlang')
		{
			checkSession('post');

			// lang delete ?
			if(isset($_POST['lang_delete']) && !empty($_POST['lang_delete']))
			{
				// get the values...
				$langId = PortaMx_makeSafe($_POST['lang_delete']);
				$failed = AdmCenterLangDelete($langId);
				if(empty($failed))
					redirectexit('action='. $admMode .';area='. $context['pmx']['pmx_area'] .';sa=showlang;'. $context['session_var'] .'='. $context['session_id']);
				else
					AdmCenterError($txt['pmx_center_langdelfailed'], $txt['pmx_center_langdelerror'], 'showlang');
			}

			// lang install ?
			elseif(isset($_POST['lang_install']) && !empty($_POST['lang_install']))
			{
				// Get the install values ...
				$InstLink = PortaMx_makeSafe($_POST['lang_install']);
				$failed = true;
				$info = readDocServer($context['pmx']['server']['lang'] . $InstLink);
				if(GetFirstChildContentByName($info, 'copyright') == 'PortaMx')
				{
					// get the language description
					$langSet = array();
					$langSet['name'] = GetFirstChildContentByName($info, 'name');
					$langSet['version'] = GetFirstChildContentByName($info, 'version');
					$langSet['charset'] = GetFirstChildContentByName($info, 'charset');
					$langSet['langext'] = GetFirstChildContentByName($info, 'langext');

					// get installed languages
					$langlist = getInstalledLanguages();
					$instId = '';
					foreach($langlist as $id => $data)
						$instId = compareLang($langSet, $data) ? $id : $instId;

					// if Update, delete old lang first
					$failed = false;
					if(!empty($instId))
						$failed = AdmCenterLangDelete($instId);
					else
						$instId = 'lang'. $langSet['langext'];

					if(empty($failed))
					{
						// get filelist
						$langfiles = array();
						$elmlist = GetChildByPathAndName($info, '', 'item');
						foreach ($elmlist as $elm)
						{
							$fname = GetFirstChildContentByName($elm, 'name');
							$langfiles[$fname] = GetFirstChildContentByName($elm, 'path');
						}

						// now get languagefiles from Portamx server
						foreach($langfiles as $file => $path)
						{
							$content = readDocServer($context['pmx']['server']['lang'] . $InstLink .'PortaMx/'. $file . $langSet['langext'], '<?php');
							$content = trim($content);
							if(!empty($content))
							{
								$fsize = strlen($content);
								$filename = $settings['default_theme_dir'] . $path . $file . $langSet['langext'] .'.php';

								if(file_exists($filename))
								{
									if(!is_writable($filename))
									{
										@chmod($filename, 0644);
										if(!is_writable($filename))
											@chmod($filename, 0777);
									}
								}

								if(empty($failed))
								{
									$written = 0;
									$fhd = fopen($filename, 'w');
									if($fhd)
									{
										$written = fwrite($fhd, $content);
										fclose($fhd);
									}
								}

								if(!empty($failed) || $written != $fsize)
								{
									$failed = true;
									break;
								}
							}
							else
							{
								$failed = true;
								break;
							}
						}

						if(empty($failed))
						{
							sleep(1);
							// add or replace the installed language
							$lset = array(
								'name' => $langSet['name'],
								'version' => $langSet['version'],
								'charset' => $langSet['charset'],
								'langext' => $langSet['langext'],
							);

							// save installed languages
							$smcFunc['db_insert']('replace', '
								{db_prefix}portamx_settings',
								array(
									'varname' => 'string',
									'config' => 'string'
								),
								array(
									$instId,
									serialize($lset),
								),
								array('varname')
							);

							// clear the filecache and redirect exit
							$pmxCacheFunc['clear']('settings', false);
							clean_cache();
							redirectexit('action='. $admMode .';area='. $context['pmx']['pmx_area'] .';sa=showlang;'. $context['session_var'] .'='. $context['session_id']);
						}
					}
				}

				if(!empty($failed))
				{
					if(isset($context['pmx']['feed_error_text']) && !empty($context['pmx']['feed_error_text']))
						AdmCenterError($txt['pmx_center_langfetchfailed'] .'<br />'. $context['pmx']['feed_error_text'], $txt['pmx_center_langinsterror'], 'showlang');
					else
						AdmCenterError($txt['pmx_center_langfetchfailed'], $txt['pmx_center_langinsterror'], 'showlang');
				}
			}

			// manually lang install ?
			elseif(isset($_POST['lang_install_manually']) && !empty($_POST['lang_install_manually']))
			{
				// Get the install values ...
				$manlang = PortaMx_makeSafe($_POST['lang_install_manually']);
				$langlist = getInstalledLanguages();
				getManuallyLanguages($langlist);

				$langSet = $context['pmx']['manualylangsets'][$manlang];
				$instId = '';
				foreach($langlist as $id => $data)
					$instId = compareLang($data, $langSet) ? $id : $instId;

				// new id if lang not exist
				if(empty($instId))
					$instId = $manlang;

				// add or replace the installed language
				$lset = array(
					'name' => $langSet['name'],
					'version' => $langSet['version'],
					'charset' => $langSet['charset'],
					'langext' => $langSet['langext'],
					'manually' => true
				);

				// save installed language
				$smcFunc['db_insert']('replace', '
						{db_prefix}portamx_settings',
					array(
						'varname' => 'string',
						'config' => 'string'
					),
					array(
						$instId,
						serialize($lset),
					),
					array('varname')
				);

				// clear the filecache and redirect exit
				$pmxCacheFunc['clear']('settings', false);
				clean_cache();
				redirectexit('action='. $admMode .';area='. $context['pmx']['pmx_area'] .';sa=showlang;'. $context['session_var'] .'='. $context['session_id']);
			}
			else
				AdmCenterError($txt['pmx_actionfault']);
		}

		// setup pagetitle
		$context['page_title'] = $txt['pmx_admin_center'];

		// load the template
		loadTemplate($context['pmx_templatedir'] .'AdminCenter');
	}
	else
		fatal_error($txt['pmx_acces_error']);
}

/**
*	Show installable languages
*/
function PortaMx_ShowLanguages()
{
	$_GET['sa'] = 'showlang';
	PortaMx_AdminCenter();
}

/**
*	Find manually installable languages
*/
function getManuallyLanguages($installed)
{
	global $context, $settings;

	// check for manually installable languages
	$langPath = $settings['default_theme_dir'] . '/languages/PortaMx';
	$context['pmx']['manualylangsets'] = array();

	$dir = dir($langPath);
	while ($entry = $dir->read())
	{
		preg_match('~^PortaMx\.?([a-zA-Z0-9_-]+)\.php~', $entry, $match);
		if(!empty($match))
		{
			$handle = fopen($langPath .'/PortaMx.'.  $match[1] .'.php', "r");
			$content = fread($handle, 512);
			fclose($handle);

			$isutf = strrchr($match[1], '-');
			if($isutf !== false && $isutf == '-utf8')
				$name = ucfirst(substr($match[1], 0, -5));
			else
				$name = ucfirst($match[1]);

			$context['pmx']['manualylangsets']['lang.'. $match[1]] = array(
				'name' => $name,
				'version' => (preg_match("~\*\s.version\s([A-Za-z0-9\.\-\s]+)~i", $content, $version) != 0 && isset($version[1]) ? $version[1] : '?.???'),
				'charset' => ($isutf !== false && $isutf == '-utf8' ? 'UTF-8' : 'ISO-8859-1'),
				'langext' => '.'. $match[1],
				'manually' => true,
			);
		}
	}
	$dir->close();

	// remove installed languages from manually set
	if(!empty($context['pmx']['manualylangsets']))
	{
		foreach($installed as $ldata)
		{
			$lext = substr($ldata['langext'], 1);
			if(isset($context['pmx']['manualylangsets']['lang'. $ldata['langext']]))
			{
				if(compareLang($ldata, $context['pmx']['manualylangsets']['lang'. $ldata['langext']], 'gt'))
					unset($context['pmx']['manualylangsets']['lang'. $ldata['langext']]);
			}
		}
	}
}

/**
* ReadDir recursive
*/
function getDirFiles(&$files, $path, $langext)
{
	if(is_dir($path))
	{
		if($dh = opendir($path))
		{
			while(($file = readdir($dh)) !== false)
			{
				if(is_file($path .'/'. $file) && strpos($file, $langext) !== false)
					$files[] = $path .'/'. $file;
				elseif(is_dir($path .'/'. $file) && !in_array($file, array('.', '..')))
					getDirFiles($files, $path .'/'. $file, $langext);
			}
		}
	}
}

/**
* Delete a language
* also called on Langupdate
*/
function AdmCenterLangDelete($langId)
{
	global $context, $smcFunc, $settings, $pmxCacheFunc;

	$failed = false;
	$removeSet = getInstalledLanguages($langId);

	// do not remove files an a manually installed language
	if(empty($removeSet['manually']))
	{
		$removeSet = array_merge($removeSet, array('subdir' => '/Blocks'));
		$themePaths = array(
			$settings['default_theme_dir'] . '/languages/PortaMx',
		);

		foreach($themePaths as $themePath)
		{
			if(is_dir($themePath))
			{
				$files = array();
				getDirFiles($files, $themePath, $removeSet['langext'] .'.php');

				// now remove found files
				foreach($files as $file)
				{
					if(file_exists($file))
					{
						if(!is_writable($file))
						{
							@chmod($filename, 0644);
							if(!is_writable($file))
								@chmod($file, 0777);
						}
						if(!unlink($file))
						{
							$failed =  true;
							break;
						}
					}
				}
			}
			else
				$failed =  true;
		}
	}

	if(empty($failed))
	{
		sleep(1);

		// remove the language from installed if not baselang
		if($langId != 'lang.english')
		{
			// remove the language from installed
			$smcFunc['db_query']('', '
					DELETE FROM {db_prefix}portamx_settings
					WHERE varname = {string:id}',
				array('id' => $langId)
			);
		}

		// clear the filecache and redirect exit
		$pmxCacheFunc['clear']('settings', false);
		clean_cache();
	}
	return $failed;
}

/**
* Error on Admin Center
*/
function AdmCenterError($errMsg, $title = '', $subaction = '')
{
	global $context, $txt;

	if(!empty($context['pmx']['isError']))
		return;

	$context['pmx']['isError'] = true;
	$context['pmx']['subaction'] = 'error';
	$context['pmx']['AdmcError'] = array(
		'title' => (empty($title) ? $txt['pmx_center_error'] : $title),
		'msg' => $errMsg,
		'subact' => $subaction,
	);
}

/**
* Compare two languages
*/
function compareLang($SetA, $SetB, $cmpmode = 'eq')
{
	$result = true;
	foreach($SetA as $key => $val)
	{
		switch ($key)
		{
			case 'name':
				$result = isset($SetB[$key]) && strtolower(trim($val)) == strtolower(trim($SetB[$key])) ? $result : false;
			break;

			case 'version':
				if($cmpmode == 'eq')
					$result = isset($SetB[$key]) && strtolower(trim($val)) == strtolower(trim($SetB[$key])) ? $result : false;
				elseif($cmpmode == 'gt')
					$result = isset($SetB[$key]) && strtolower(trim($val)) >= strtolower(trim($SetB[$key])) ? $result : false;
			break;

			case 'charset':
				$result = isset($SetB[$key]) && strtolower(trim($val)) == strtolower(trim($SetB[$key])) ? $result : false;
			break;
		}
	}
	return $result;
}

/**
* Get the installed package version
*/
function getInstalledPackage()
{
global $smcFunc, $context, $settings, $sourcedir, $txt;

	$result = '?.???';
	$request = $smcFunc['db_query']('', '
			SELECT version
			FROM {db_prefix}log_packages
			WHERE package_id = {string:pktid} AND install_state > 0
			ORDER BY id_install DESC',
		array(
			'pktid' => 'portamx_corp:PortaMx',
		)
	);
	if($row = $smcFunc['db_fetch_assoc']($request))
		$result = $row['version'];
	$smcFunc['db_free_result']($request);

	return $result;
}

/**
* Get info from docserver
*/
function readDocServer($filepath, $header = '<?xml')
{
	global $context;

	if($header == '<?xml')
		$filepath .= 'pmxinfo.xml';
	return ParseXmlurl($context['pmx']['server']['url'] . $filepath, 5, $header);
}

/**
* Get the Liveinfo from Database or PortaMx server
* Returns the info array
*/
function getLiveInfo($element = '')
{
	global $smcFunc, $context;

	// read live info from database
	$result = '';
	$request = $smcFunc['db_query']('', '
			SELECT config
			FROM {db_prefix}portamx_settings
			WHERE varname = {string:liveinfo}',
		array('liveinfo' => 'liveinfo')
	);

	if($row = $smcFunc['db_fetch_assoc']($request))
	{
		$result = $row['config'];
		$smcFunc['db_free_result']($request);
	}

	// info exist?
	if(!empty($result))
	{
		$liveinfo = unserialize($result);
		// out of TTL .. read from server
		if(time() > $liveinfo['ttl'])
		{
			$result = ReadLiveInfo();
			if(!empty($result))
			{
				unset($liveinfo);
				$liveinfo = $result;
				unset($result);
			}
		}
	}
	else
	// empty .. read from server
		$liveinfo = ReadLiveInfo();

	if(!empty($element))
		return $liveinfo[$element];
	else
		return $liveinfo;
}

/**
* Get the liveinfo from PortaMx
* Returns the info array
*/
function ReadLiveInfo()
{
	global $context, $txt, $smcFunc;

	// get XMLinfo from PortaMx.com
	$liveinfo = readDocServer($context['pmx']['server']['live']);

	$info['version'] = '?.???';
	if(!empty($liveinfo))
	{
		if(GetFirstChildContentByName($liveinfo, 'copyright') == 'PortaMx')
		{
			$info['ttl'] = GetFirstChildContentByName($liveinfo, 'ttl');
			if(!empty($info['ttl']))
			{
				$attr = GetFirstChildByName($liveinfo, 'ttl');
				$ttlbase = GetAttribByName($attr, 'timebase');
				if(!empty($ttlbase))
					$info['ttl'] = intval($info['ttl']) * intval($ttlbase);
				$info['ttl'] += time();
			}
			else
				$info['ttl'] = 0;

			$info['version'] = GetFirstChildContentByName($liveinfo, 'version');
			$info['update'] = GetFirstChildContentByName($liveinfo, 'update');
			$info['download'] = GetFirstChildContentByName($liveinfo, 'download');

			$elmlist = GetChildByPathAndName($liveinfo, '', 'item');
			foreach ($elmlist as $elm)
			{
				$subject = trim(GetFirstChildContentByName($elm, 'subject'));
				$date = trim(GetFirstChildContentByName($elm, 'pubDate'));
				$link = trim(GetFirstChildContentByName($elm, 'link'));
				$msg = trim(GetFirstChildContentByName($elm, 'description'));

				$info['item'][] = array(
					'subject' => (!empty($link) ? '<a href="'. $link .'" target="blank_">'. $subject .'</a>' : $subject),
					'date' => $date,
					'msg' => $msg,
				);
			}

			// save to database ..
			if(!empty($info['ttl']))
				$smcFunc['db_insert']('replace', '
						{db_prefix}portamx_settings',
					array(
						'varname' => 'string',
						'config' => 'string'
					),
					array(
						'liveinfo',
						serialize($info),
					),
					array('varname')
				);
		}
	}
	return $info;
}
?>
