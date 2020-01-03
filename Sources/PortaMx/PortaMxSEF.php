<?php
/**
* \file PortaMxSEF.php
* SEF functions for Portamx.
*
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \author Developer of the Original Code is Matt Zuba.
* \version 1.54.1
* \date 03.01.2020
*
* BEGIN LICENSE BLOCK
* Version: MPL 1.1
*
* The contents of this file are subject to the Mozilla Public License Version
* 1.1 (the "License"); you may not use this file except in compliance with
* the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/
*
* Software distributed under the License is distributed on an "AS IS" basis,
* WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
* for the specific language governing rights and limitations under the
* License.
*
* The Initial Developer of the Original Code is Matt Zuba.
* Portions created by the Initial Developer are Copyright (C) 2010-2011
* All Rights Reserved.
*
* Contributor:
* PortaMx corp. Germany - http://portamx.com
* Partial Copyright 2008-2014 by PortaMx corp. corp.
*
* END LICENSE BLOCK
*/

if(!defined('SMF'))
	die('This Portamx file can\'t be run without SMF');

/*************************************
* Initiate the SEF enging, then convert
* called from hook: integrate_pre_load
***************************************/
function pmxsef_convertSEF()
{
	global $boardurl, $modSettings, $scripturl, $PortaMxSEF, $pmxCacheFunc;

	define('PortaMxSEF', 1);

	$PortaMxSEF = array(
		'actions' => array_merge(explode(',', $modSettings['pmxsef_actions']), array('theme')),
		'aliasactions' => unserialize($modSettings['pmxsef_aliasactions']),
		'ignoreactions' => array_merge(array('admin', 'portamx', 'openidreturn', 'verificationcode'), explode(',', $modSettings['pmxsef_ignoreactions'])),
		'ignorerequests' => unserialize($modSettings['pmxsef_ignorerequests']),
		'stripchars' => array_diff(explode(',', $modSettings['pmxsef_stripchars']), array(trim($modSettings['pmxsef_spacechar']))),
		'wireless' => explode(',', $modSettings['pmxsef_wireless']),
		'singletoken' => explode(',', $modSettings['pmxsef_singletoken']),
		'spacechar' => trim($modSettings['pmxsef_spacechar']),
		'lowercase' => $modSettings['pmxsef_lowercase'],
		'codepages' => $modSettings['pmxsef_codepages'],
		'autosave' => $modSettings['pmxsef_autosave'],
		'ssefspace' => trim($modSettings['pmxsef_ssefspace']),
		'pmxextra' => array(
			'all' => array('paneloff','panelon','blockoff','blockon','show'),
			'save' => array('rply', 'new', 'cont'),
			'view' => array('sa', 'cfr')),
	);

	$PortaMxSEF['allactions'] = array_merge($PortaMxSEF['actions'], $PortaMxSEF['ignoreactions'], array_keys($PortaMxSEF['aliasactions']));
	parse_str(preg_replace('~&(\w+)(?=&|$)~', '&$1=', strtr($_SERVER['QUERY_STRING'], array(';?' => '&', '/;' => '/', ';' => '&', '%00' => '', "\0" => ''))), $_GET);

	// Make sure we know the URL of the current request.
	$scripturl = $boardurl . '/index.php';
	if(empty($_SERVER['REQUEST_URI']))
		$_SERVER['REQUEST_URL'] = $scripturl . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
	elseif(preg_match('~^([^/]+//[^/]+)~', $scripturl, $match) == 1)
		$_SERVER['REQUEST_URL'] = $match[1] . $_SERVER['REQUEST_URI'];
	else
		$_SERVER['REQUEST_URL'] = $_SERVER['REQUEST_URI'];

	// security .. illegal querys
	$_SERVER['REQUEST_URL'] = str_replace('../', '', $_SERVER['REQUEST_URL']);
	if(preg_match('~(http|https|ftp|sftp)\:\/\/~i', $_SERVER['QUERY_STRING']) > 0)
		pmxsef_redir_perm($scripturl);

	if(!empty($modSettings['queryless_urls']))
		updateSettings(array('queryless_urls' => '0'));

	if(SMF == 'SSI')
		return;

	// replace a simple domain.tld/? to /index.php?
	if(strpos($_SERVER['REQUEST_URL'], 'index.php') === false && strpos($_SERVER['REQUEST_URL'], $boardurl .'/?') !== false)
		$_SERVER['REQUEST_URL'] = str_replace($boardurl .'/?', $scripturl .'?', $_SERVER['REQUEST_URL']);

	// fix advanced search params
	if(strpos($_SERVER['REQUEST_URL'], '/;search=') !== false)
		pmxsef_redir_perm(str_replace(array('/;', '='), '/', $_SERVER['REQUEST_URL']));

	// exit on defined requests, redirect other
	if(strpos($_SERVER['REQUEST_URL'], $scripturl) !== false)
	{
		if(isset($_GET['xml']) || (!empty($_GET['action']) && ($_GET['action'] == '.xml' || in_array($_GET['action'], $PortaMxSEF['ignoreactions']))))
		{
			// clear the cache if a board modified
			if(strpos($_SERVER['REQUEST_URL'], 'sa=board2') !== false && isset($_POST['boardid']))
				$pmxCacheFunc['clear']('pmxsef_boardlist', false);
			return;
		}
		elseif(!empty($_GET) && ($tmp = array_intersect(array_keys($_GET), array_keys($PortaMxSEF['ignorerequests']))) && count($tmp) == 1 && ($tmp = current($tmp)) && $_GET[$tmp] == $PortaMxSEF['ignorerequests'][$tmp])
			return;

		pmxsef_redir_perm($_SERVER['REQUEST_URL']);
	}

	// old SSEF url's like topic_##.#.html, board_##.# ?
	if(!empty($PortaMxSEF['ssefspace']) && preg_match('~\/(topic'. $PortaMxSEF['ssefspace'] .'|board'. $PortaMxSEF['ssefspace'] .')(.*)$~', $_SERVER['REQUEST_URL'], $match) > 0)
	{
		if($match[1] == 'topic'. $PortaMxSEF['ssefspace'])
		{
			@list($topic, $page, $ext) = explode('.', $match[2]);
			$_SERVER['REQUEST_URL'] = str_replace($match[0], '?topic='. $topic .'.'. $page, $_SERVER['REQUEST_URL']);
		}
		elseif($match[1] == 'board'. $PortaMxSEF['ssefspace'])
		{
			@list($board, $page) = explode('.', $match[2]);
			$_SERVER['REQUEST_URL'] = str_replace($match[0], '?board='. $board .'.'. trim($page, '/'), $_SERVER['REQUEST_URL']);
		}
		pmxsef_redir_perm($_SERVER['REQUEST_URL']);
	}

	// Parse the url
	if(!empty($_GET['q']))
	{
		$_GET = pmxsef_query(rawurldecode(ltrim(str_replace($boardurl, '', $_SERVER['REQUEST_URL']), '/')));
		$_SERVER['QUERY_STRING'] = pmxsef_build_query($_GET, '', ';');

		// check if the topic subject changed
		if(isset($_GET['action']) && $_GET['action'] == 'post2' && isset($_GET['msg']))
			pmxsef_CheckTopic($_GET['msg']);
	}
}

/*************************************
* convert the requested SEF url to SMF
* called from: pmxsef_convertSEF
***************************************/
function pmxsef_query($query)
{
	global $boardurl, $modSettings, $PortaMxSEF;

	$querystring = $querystart = $querysingle = array();
	if(!empty($query) && $query != '/')
	{
		// security .. check illegal chars
		if(preg_match('~(http|https|ftp|sftp)\:\/\/~i', $query) > 0)
			pmxsef_redir_perm($boardurl);

		// cleanup the url
		$url_array = explode('/', trim(str_replace(array(';', '..'), '', $query), '/'));
		$act = array_intersect($url_array, $PortaMxSEF['allactions']);

		// check ignore requests
		$tmp = array_intersect(array_values($url_array), array_keys($PortaMxSEF['ignorerequests']));
		if(count($tmp) == 1 && $url_array[key($tmp)+1] == $PortaMxSEF['ignorerequests'][current($tmp)])
			pmxsef_redir_perm($_SERVER['REQUEST_URL']);

		// ignore action ?
		if(count($act) > 0 && in_array(current($act), $PortaMxSEF['ignoreactions']))
			pmxsef_redir_perm($_SERVER['REQUEST_URL']);

		// check start token
		if(getUrlParams('start', true, $url_array, $querystart) == true)
		{
			preg_match('~^(category|article|pages|[0-9]+)$~', current($url_array), $match);
			if(!empty($match[1]) && count($url_array) >= 2)
			{
				// topic
				if(is_numeric($match[1]))
				{
					$page = !empty($url_array[2]) && (is_numeric($url_array[2]) || substr($url_array[2], 0, 3) == 'msg' || substr($url_array[2], 0, 3) == 'new') ? $url_array[2] : '';
					$querystring['topic'] = $match[1] .'.'. (empty($page) ? '0' : $page);
					array_splice($url_array, 0, 2 + intval(!empty($page)));
				}

				// spage?
				elseif($match[1] == 'pages')
				{
					getPagesNameList();
					if(!empty($PortaMxSEF['PagesNameList']['name'][$url_array[1]]))
						$querystring['spage'] = $PortaMxSEF['PagesNameList']['name'][$url_array[1]];
					else
						$querystring['pmxerror'] = 'page';

					array_splice($url_array, 0, 2);
				}

				// category?
				elseif($match[1] == 'category')
				{
					getCategoryNameList();
					if(!empty($PortaMxSEF['CatNameList']['name'][$url_array[1]]))
					{
						$querystring['cat'] = $PortaMxSEF['CatNameList']['name'][$url_array[1]];
						array_splice($url_array, 0, 2);

						// chils category?
						if(count($url_array) == 1 || count($url_array) == 3)
						{
							if(!empty($PortaMxSEF['CatNameList']['name'][$url_array[0]]))
							{
								$querystring['child'] = $PortaMxSEF['CatNameList']['name'][$url_array[0]];
								array_splice($url_array, 0, 1);
							}
							else
							{
								$url_array = array();
								$querystring['pmxerror'] = 'category';
							}
						}
					}
					else
					{
						$url_array = array();
						$querystring['pmxerror'] = 'category';
					}
				}

				// check article
				if(current($url_array) == 'article' && count($url_array) >= 2)
				{
					getArticleNameList();
					if(!empty($PortaMxSEF['ArtNameList']['name'][$url_array[1]]))
						$querystring['art'] = $PortaMxSEF['ArtNameList']['name'][$url_array[1]];
					else
						$querystring['pmxerror'] = 'article';

					array_splice($url_array, 0, 2);
				}
			}

			// check is boardname
			elseif(!empty($url_array))
			{
				getBoardNameList();
				if(in_array(current($url_array), $PortaMxSEF['BoardNameList']))
				{
					$page = isset($url_array[1]) && is_numeric($url_array[1]) ? $url_array[1] : '0';
					$querystring['board'] = current(array_keys($PortaMxSEF['BoardNameList'], current($url_array))) .'.'. $page;
					array_splice($url_array, 0, 1 + intval(!empty($page)));
				}
			}

			// Get the wireless token and check the actions
			if(getWirelessParams($url_array, $querystring) == true && (in_array(current($url_array), $PortaMxSEF['allactions'])))
			{
				$querystring['action'] = $url_array[0];
				$tmp = array_shift($url_array);

				// alias action?
				if(in_array($querystring['action'], array_keys($PortaMxSEF['aliasactions'])))
					$querystring['action'] = $PortaMxSEF['aliasactions'][$querystring['action']];

				// check for subaction
				$fnd = array_search('sa', $url_array);
				if($fnd !== false && isset($url_array[$fnd +1]))
				{
					$querystring['sa'] = $url_array[$fnd +1];
					array_splice($url_array, $fnd, 2);
				}

				// special handling for theme token
				elseif($querystring['action'] == 'theme')
				{
					unset($querystring['action']);
					array_unshift($url_array, $tmp);
				}

				// check for username
				@list($name, $id) = explode('.', current($url_array));
				if(is_numeric($id))
				{
					$querystring['u'] = ($id == '0' ? $name : $id);
					array_shift($url_array);
				}
			}

			// check single key token
			if(getSingleKeyParams($url_array, $querystring) == true)
			{
				// do the rest of url
				while(!empty($url_array))
					$querystring[array_shift($url_array)] = array_shift($url_array);
			}
		}
	}

	if(!empty($querystart))
		$querystring += $querystart;

	return $querystring;
}

/******************************************
* convert redirected SMF urls to SEF format
* called from hook: integrate_redirect
********************************************/
function pmxsef_Redirect(&$setLocation, &$refresh)
{
	global $scripturl, $PortaMxSEF, $pmxCacheFunc, $modSettings;

	$PortaMxSEF['redirect'] = true;
	$refresh = true;

	// Only do this if it's an URL for this board
	if(strpos($setLocation, $scripturl) !== false)
	{
		$setLocation = create_sefurl($setLocation);

		// Check to see if we need to update the actions lists
		if(!empty($PortaMxSEF['autosave']) && count(explode(',', $modSettings['pmxsef_actions'])) != count($PortaMxSEF['actions']))
		{
			$changeArray['pmxsef_actions'] = implode(',', array_unique($PortaMxSEF['actions']));
			updateSettings($changeArray);
		}
	}
}

/*********************************
* convert XML urls to SEF format
* called from hook: integrate_exit
***********************************/
function pmxsef_XMLOutput($do_footer)
{
	global $PortaMxSEF, $modSettings;

	if(!$do_footer && empty($PortaMxSEF['redirect']))
	{
		$temp = ob_get_contents();

		ob_end_clean();
		ob_start(!empty($modSettings['enableCompressedOutput']) && !in_array('ob_gzhandler', ob_list_handlers()) ? 'ob_gzhandler' : '');
		ob_start('ob_pmxsef');

		echo $temp;
	}
}

/*******************************************
* convert eMail urls to SEF format
* called from hook: integrate_outgoing_email
*********************************************/
function pmxsef_EmailOutput(&$subject, &$message, &$header)
{
	// We're just fixing the subject and message
	$subject = ob_pmxsef($subject);
	$message = ob_pmxsef($message);

	// We must return true, otherwise we fail!
	return true;
}

/*******************************************
* convert urls to SEF format
* called from hook: integrate_fix_url
*********************************************/
function pmxsef_fixurl($url)
{
	$url = create_sefurl($url);
	return $url;
}

/****************************************************
* Convert all SMF urls in the outbuffer in SEF format
* called from hook: integrate_buffer
******************************************************/
function ob_pmxsef($buffer)
{
	global $scripturl, $boardurl, $PortaMxSEF, $modSettings;

	if(!empty($_REQUEST['pmxcook']))
		return $buffer;

	// lock formular data
	$formdata = '';
	if(isset($_REQUEST['area']) && in_array($_REQUEST['area'], array('pmx_blocks', 'pmx_articles')))
	{
		preg_match_all('~<form\s+id="pmx_form"[^>]+>(.*?)</form>~is', $buffer, $matches);
		if(isset($matches[1][0]))
		{
			$formdata = $matches[1][0];
			$buffer = str_replace($formdata, '[@][PmX-Formular-Data][@]', $buffer);
		}
	}

	// convert SSEF simple url's
	if(!empty($PortaMxSEF['ssefspace']))
	{
		$matches = array();
		preg_match_all('~(\b'. preg_quote($boardurl) .'\/(topic'. $PortaMxSEF['ssefspace'] .'([a-zA-Z0-9\.]+)|board'. $PortaMxSEF['ssefspace'] .'([a-zA-Z0-9\.]+)))~', $buffer, $matches);
		if(!empty($matches[2]))
		{
			$replacements = array();

			// topics..
			$topics = array_diff(array_unique($matches[3]), array(''));
			foreach($topics as $i => $url)
				$replacements[$matches[2][$i]] = rtrim(getTopicName($url), '/');

			// boards
			$boards = array_diff(array_unique($matches[4]), array(''));
			foreach($boards as $i => $url)
				$replacements[$matches[2][$i]] = rtrim(getBoardName($url), '/');

			$buffer = str_replace(array_keys($replacements), array_values($replacements), $buffer);
		}
	}

	// fix expandable pagelinks
	$buffer = str_replace('/index.php\'+\'?', '/index.php?', $buffer);

	// Get all topics..
	$matches = array();
	preg_match_all('~\b' . preg_quote($scripturl) . '.*?topic=([0-9]+)~', $buffer, $matches);
	if(!empty($matches[1]))
		getTopicNameList(array_unique($matches[1]));

	// Get all user..
	$matches = array();
	preg_match_all('~\b'. preg_quote($scripturl) .'.*?u=([0-9]+)~', $buffer, $matches);
	if(!empty($matches[1]))
		getUserNameList(array_unique($matches[1]));

	// Do the rest of the URLs, skip admin urls
	$matches = array();
	preg_match_all('~\b('. preg_quote($scripturl) .'(?!\?action=admin)(?!\?action=portamx)[-a-zA-Z0-9+&@#/%?=\~_|!:,.;\[\]]*[-a-zA-Z0-9+&@#/%=\~_|\[\]]?)([^-a-zA-Z0-9+&@#/%=\~_|])~', $buffer, $matches);
	if(!empty($matches[0]))
	{
		$replacements = array();
		foreach(array_unique($matches[1]) as $i => $url)
		{
			$replace = create_sefurl($url);
			if($url != $replace)
				$replacements[$matches[0][$i]] = $replace . str_replace(';', '', $matches[2][$i]);
		}
		$buffer = str_replace(array_keys($replacements), array_values($replacements), $buffer);
	}

	// unlock formular data
	if(!empty($formdata))
		$buffer = str_replace('[@][PmX-Formular-Data][@]', $formdata, $buffer);

	// Gotta fix up some javascript laying around in the templates
	$extra_replacements = array(
		'%1/$d' => '%1$d/',
		'/$d\',' => '_%1$d/\',',
		'/rand,' => '/rand=',
		'%1.html$d\',' => '%1$d.html\',',
		$boardurl . '/topic/' => $scripturl . '?topic=',
		'%1_%1$d/\',' => '%1$d/\',',
		'var smf_scripturl = "' . $boardurl . '/' => 'var smf_scripturl = "' . $scripturl,
	);
	$buffer = str_replace(array_keys($extra_replacements), array_values($extra_replacements), $buffer);

	// Check to see if we need to update the actions lists
	if(!empty($PortaMxSEF['autosave']) && count(explode(',', $modSettings['pmxsef_actions'])) != count($PortaMxSEF['actions']))
	{
		$changeArray['pmxsef_actions'] = implode(',', array_unique($PortaMxSEF['actions']));
		updateSettings($changeArray);
	}

	// done
	return $buffer;
}

/******************************************
* redirected for unknow SMF urls
* called from hook: pmxsef_convertSEF
********************************************/
function pmxsef_redir_perm($url)
{
	define('WIRELESS', false);
	header('HTTP/1.1 301 Moved Permanently');
	redirectexit($url);
}

/************************************************
* convert SMF urls to SEF format
* called from: ssef_fixRedirectUrl, ob_simplesef
**************************************************/
function create_sefurl($url)
{
	global $boardurl, $PortaMxSEF, $pmxCacheFunc;

	// Init..
	$sefstring = $sefstring1 = $sefstring2 = '';
	$query_parts = array();

	// Get the query string
	$params = array();
	$url_parts = parse_url($url);
	parse_str(!empty($url_parts['query']) ? preg_replace('~&(\w+)(?=&|$)~', '&$1=', strtr($url_parts['query'], array('&amp;' => '&', ';' => '&'))) : '', $params);

	if(!empty($params))
	{
		// check ingnore actions
		if(!empty($params['action']) && in_array($params['action'], $PortaMxSEF['ignoreactions']))
			return $url;

		// check ingnore requests
		$tmp = array_intersect(array_keys($params), array_keys($PortaMxSEF['ignorerequests']));
		if(count($tmp) == 1 && ($tmp = current($tmp)) && $params[$tmp] == $PortaMxSEF['ignorerequests'][$tmp])
			return $url;

		// single action token
		$act = isset($params['action']) ? $params['action'] : '';
		foreach($PortaMxSEF['wireless'] as $key)
		{
			if(isset($params[$key]) && $key != $act)
			{
				$sefstring1 .= $key .'/';
				unset($params[$key]);
			}
		}

		// boards or topics
		if(isset($params['board']))
		{
			$sefstring .= getBoardName($params['board']);
			unset($params['board']);
		}
		elseif(isset($params['topic']))
		{
			$sefstring .= getTopicName($params['topic']);
			unset($params['topic']);
		}

		// actions
		if(isset($params['action']))
		{
			if(in_array($params['action'], array_values($PortaMxSEF['aliasactions'])))
			{
				$acts = array_flip($PortaMxSEF['aliasactions']);
				$params['action'] = $acts[$params['action']];
			}
			elseif(!in_array($params['action'], array_merge($PortaMxSEF['actions'], array('theme', 'language'))))
			{
				preg_match('/[a-zA-Z0-9\_\-]+/', $params['action'], $action);
				if(!empty($action[0]))
					$PortaMxSEF['actions'][] = $action[0];
			}
			$sefstring .= $params['action'] .'/';
			unset($params['action']);

			// user
			if(isset($params['u']))
			{
				$sefstring .= ($params['u'] == 'all' ? $params['u'] .'.0/' : getUserName($params['u']));
				unset($params['u']);
			}
		}

		// category & article
		elseif(isset($params['cat']))
		{
			// root cat
			if(isset($params['cat']))
			{
				$sefstring .= getCategoryName($params['cat']);
				unset($params['cat']);
			}

			//child cat?
			if(isset($params['child']))
			{
				$sefstring .= getCategoryName($params['child'], true);
				unset($params['child']);
			}

			// have article?
			if(isset($params['art']))
			{
				$sefstring .= getArticleName($params['art']);
				unset($params['art']);
			}
		}

		// article request?
		elseif(isset($params['art']))
		{
			$sefstring .= getArticleName($params['art']);
			unset($params['art']);
		}

		// pages request?
		elseif(isset($params['spage']))
		{
			$sefstring .= getPageName($params['spage']);
			unset($params['spage']);
		}

		// single request token
		foreach($PortaMxSEF['singletoken'] as $key)
		{
			if(array_key_exists($key, $params) && $params[$key] == '')
			{
				$sefstring2 .= $key .'/';
				unset($params[$key]);
			}
		}

		// do the rest
		foreach($params as $key => $value)
		{
			if($key == 'start')
				$sefstring2 .= ($value != '' ? $key .'/'. $value .'/' : '');

			elseif(is_array($value))
				$sefstring1 .= $key .'['. key($value) .']/'. $value[key($value)] .'/';

			else
				$sefstring1 .= $key .'/'. $value .'/';
		}

		// Build the URL
		if(isset($query_parts['action']))
			$sefstring .= $query_parts['action'] .'/';

		$sefstring .= $sefstring1 . $sefstring2;
	}
	return $boardurl .'/'. $sefstring . (!empty($url_parts['fragment']) ? '#' . $url_parts['fragment'] : '');
}

/**
* convert boad names to SEF
* called from: getBoardName, pmxsef_convertQuery
**/
function getBoardNameList()
{
	global $PortaMxSEF, $smcFunc, $pmxCacheFunc;

	if(empty($PortaMxSEF['BoardNameList']))
		$PortaMxSEF['BoardNameList'] = $pmxCacheFunc['get']('pmxsef_boardlist', false, true);

	if(empty($PortaMxSEF['BoardNameList']))
	{
		$PortaMxSEF['BoardNameList']['dupes'] = 0;
		$request = $smcFunc['db_query']('', '
			SELECT id_board, name
			FROM {db_prefix}boards',
			array()
		);
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$name = pmxsef_encode($row['name']);
			$check = @array_keys($PortaMxSEF['BoardNameList'], $name);
			if(!empty($check))
			{
				if(!empty($PortaMxSEF['BoardNameList']['dupes']))
					$name .= $PortaMxSEF['spacechar'] . $PortaMxSEF['BoardNameList']['dupes'];
				$PortaMxSEF['BoardNameList']['dupes']++;
			}
			$PortaMxSEF['BoardNameList'][$row['id_board']] = $name;
		}
		$smcFunc['db_free_result']($request);
		$pmxCacheFunc['put']('pmxsef_boardlist', $PortaMxSEF['BoardNameList'], 3600);
	}
}

/**
* get the SEF board name for id
* called from: create_sefurl
**/
function getBoardName($id)
{
	global $PortaMxSEF;

	if(!empty($id))
	{
		@list($board, $page) = explode('.', $id);
		if(empty($PortaMxSEF['BoardNameList'][$board]))
			getBoardNameList();

		if(!empty($PortaMxSEF['BoardNameList'][$board]))
			return $PortaMxSEF['BoardNameList'][$board] .(!empty($page) ? '/'. str_replace('\\', '', $page) : '') .'/';
	}
	return '';
}

/**
* convert topic subjects to SEF
* called from: getTopicName, create_sefurl
**/
function getTopicNameList($topics)
{
	global $PortaMxSEF, $smcFunc, $pmxCacheFunc;

	if(empty($PortaMxSEF['TopicNameList']))
		$PortaMxSEF['TopicNameList'] = $pmxCacheFunc['get']('pmxsef_topiclist', false, true);

	// make integers for secure
	array_walk($topics, create_function('&$v,$k', '$v = intval(trim($v));'));

	$notcached = array_diff($topics, array_keys($PortaMxSEF['TopicNameList']));
	if(!empty($notcached))
	{
		$request = $smcFunc['db_query']('', '
			SELECT t.id_topic, m.subject
			FROM {db_prefix}topics AS t
			INNER JOIN {db_prefix}messages AS m ON(m.id_msg = t.id_first_msg)
			WHERE t.id_topic IN ({array_int:topics})',
			array('topics' => $notcached)
		);
		while($row = $smcFunc['db_fetch_assoc']($request))
		{
			$name = pmxsef_encode($row['subject']);
			$PortaMxSEF['TopicNameList'][$row['id_topic']] = $name;
		}
		$smcFunc['db_free_result']($request);
		$pmxCacheFunc['put']('pmxsef_topiclist', $PortaMxSEF['TopicNameList'], 3600);
	}
}

/**
* get the SEF topic name for id
* called from: ob_pmxsef, create_sefurl
**/
function getTopicName($id)
{
	global $PortaMxSEF;

	if(!empty($id))
	{
		@list($topic, $page) = explode('.', $id);
		if(empty($PortaMxSEF['TopicNameList'][$topic]))
			getTopicNameList(array($topic));

		if(!empty($PortaMxSEF['TopicNameList'][$topic]))
			return $topic .'/'. $PortaMxSEF['TopicNameList'][$topic] . (!empty($page) ? '/'. $page : '') .'/';
	}
	return '';
}

/**
* convert all single page titles to SEF
* called from: pmxsef_convertQuery, getPageName
**/
function getPagesNameList()
{
	global $PortaMxSEF, $smcFunc, $pmxCacheFunc;

	if(empty($PortaMxSEF['PagesNameList']))
		$PortaMxSEF['PagesNameList'] = $pmxCacheFunc['get']('pmxsef_pageslist', false, true);

	if(empty($PortaMxSEF['PagesNameList']))
	{
		$PortaMxSEF['PagesNameList']['Pagesdupes'] = 0;
		$PortaMxSEF['PagesNameList']['name'] = array();

		$request = $smcFunc['db_query']('', '
			SELECT id, config
			FROM {db_prefix}portamx_blocks
			WHERE side = {string:pages} AND active > 0',
			array('pages' => 'pages')
		);
		while ($row = $smcFunc['db_fetch_assoc']($request))
			getCustomTitle($row, 'Pages');
		$smcFunc['db_free_result']($request);

		$pmxCacheFunc['put']('pmxsef_pageslist', $PortaMxSEF['PagesNameList'], 3600);
	}
}

/**
* get a SEF Singe Page name
* called from: create_sefurl
**/
function getPageName($pagename)
{
	global $PortaMxSEF;

	getPagesNameList();

	$tmp = array_flip($PortaMxSEF['PagesNameList']['name']);
	if(!empty($tmp[$pagename]))
		return 'pages/'. $tmp[$pagename] .'/';
	else
		return 'pmxerror/page/';
}

/**
* convert all category titles to SEF
* called from: getCategoryName, pmxsef_convertQuery
**/
function getCategoryNameList()
{
	global $PortaMxSEF, $smcFunc, $pmxCacheFunc;

	if(empty($PortaMxSEF['CatNameList']))
		$PortaMxSEF['CatNameList'] = $pmxCacheFunc['get']('pmxsef_catlist', false, true);

	if(empty($PortaMxSEF['CatNameList']))
	{
		$PortaMxSEF['CatNameList']['Catdupes'] = 0;
		$PortaMxSEF['CatNameList']['name'] = array();

		$request = $smcFunc['db_query']('', '
			SELECT id, name, config
			FROM {db_prefix}portamx_categories
			ORDER by catorder',
			array()
		);
		while ($row = $smcFunc['db_fetch_assoc']($request))
			getCustomTitle($row, 'Cat');
		$smcFunc['db_free_result']($request);

		$pmxCacheFunc['put']('pmxsef_catlist', $PortaMxSEF['CatNameList'], 3600);
	}
}

/**
* get the SEF Category name for $catname
* called from: create_sefurl
**/
function getCategoryName($catname, $isChild = false)
{
	global $PortaMxSEF;

	getCategoryNameList();

	$tmp = array_flip($PortaMxSEF['CatNameList']['name']);
	if(!empty($tmp[$catname]))
		return (empty($isChild) ? 'category/' : '') . $tmp[$catname] .'/';
	else
		return 'pmxerror/category/';
}

/**
* convert all article titles to SEF
* called from: getArticleName
**/
function getArticleNameList()
{
	global $PortaMxSEF, $smcFunc, $pmxCacheFunc;

	if(empty($PortaMxSEF['ArtNameList']))
		$PortaMxSEF['ArtNameList'] = $pmxCacheFunc['get']('pmxsef_artlist', false, true);

	if(empty($PortaMxSEF['ArtNameList']))
	{
		$PortaMxSEF['ArtNameList']['Artdupes'] = 0;
		$PortaMxSEF['ArtNameList']['name'] = array();

		$request = $smcFunc['db_query']('', '
			SELECT name, config
			FROM {db_prefix}portamx_articles
			WHERE active > 0 AND approved > 0',
			array()
		);
		while ($row = $smcFunc['db_fetch_assoc']($request))
			getCustomTitle($row, 'Art');
		$smcFunc['db_free_result']($request);

		$pmxCacheFunc['put']('pmxsef_artlist', $PortaMxSEF['ArtNameList'], 3600);
	}
}

/**
* get a SEF Article name
* called from: create_sefurl
**/
function getArticleName($pagename)
{
	global $PortaMxSEF;

	getArticleNameList();

	$tmp = array_flip($PortaMxSEF['ArtNameList']['name']);
	if(!empty($tmp[$pagename]))
		return 'article/'. $tmp[$pagename] .'/';
	else
		return 'pmxerror/article/';
}

/**
* convert user real name to SEF
* called from: pmxsef_convertQuery
**/
function getUserNameList($user = array())
{
	global $PortaMxSEF, $smcFunc, $pmxCacheFunc;

	if(empty($PortaMxSEF['UserNameList']))
		$PortaMxSEF['UserNameList'] = $pmxCacheFunc['get']('pmxsef_userlist', false, true);

	array_walk($user, create_function('&$v,$k', '$v = intval(trim($v));'));
	$notcached = array_diff($user, array_keys($PortaMxSEF['UserNameList']), array(''));
	if(!empty($notcached))
	{
		$request = $smcFunc['db_query']('', '
			SELECT id_member, real_name
			FROM {db_prefix}members
			WHERE id_member IN ({array_int:members})',
			array('members' => $notcached)
		);
		while($row = $smcFunc['db_fetch_assoc']($request))
		{
			$name = pmxsef_encode($row['real_name']);
			$PortaMxSEF['UserNameList'][$row['id_member']] = $name;
		}
		$smcFunc['db_free_result']($request);
		$pmxCacheFunc['put']('pmxsef_userlist', $PortaMxSEF['UserNameList'], 3600);
	}
}

/**
* get the SEF user name for id
* called from: create_sefurl
**/
function getUserName($id)
{
	global $PortaMxSEF;

	if(!empty($id))
	{
		getUserNameList(array($id));
		if(isset($PortaMxSEF['UserNameList'][$id]))
			return (empty($PortaMxSEF['UserNameList'][$id]) ? 'u' : $PortaMxSEF['UserNameList'][$id]) .'.'. $id .'/';
	}
	return '';
}

/**
* convert a titles to SEF
* called from: getPagesNameList, getCategoryList, getArticleNameList
**/
function getCustomTitle($row, $type)
{
	global $PortaMxSEF, $language;

	$cfg = unserialize($row['config']);
	if($type == 'Pages')
		$row['name'] = $cfg['pagename'];

	if(!empty($cfg['title'][$language]))
		$title = pmxsef_encode($cfg['title'][$language]);
	else
		$title = pmxsef_encode($row['name']);

	// dupe check..
	if(@array_key_exists($title, $PortaMxSEF[$type .'NameList']['name']) && $PortaMxSEF[$type .'NameList']['name'][$title] != $row['name'])
		$title .= $PortaMxSEF['spacechar'] . ++$PortaMxSEF[$type .'NameList'][$type .'dupes'];

	$PortaMxSEF[$type .'NameList']['name'][$title] = $row['name'];
}

/**
* find and convert a url entry to SMF
* called from: pmxsef_convertQuery
**/
function getUrlParams($key, $hasval, &$url, &$result)
{
	$pos = array_search($key, $url);
	if($pos === false)
		return !empty($url);

	$result[$key] = !empty($hasval) ? $url[$pos +1] : '';
	array_splice($url, $pos, intval($hasval) +1);
	return !empty($url);
}

/**
* find and convert wireless token to SMF
* called from: pmxsef_convertQuery
**/
function getWirelessParams(&$url, &$result)
{
	global $PortaMxSEF;

	if(!empty($url))
	{
		$wap = false;
		foreach($PortaMxSEF['wireless'] as $key)
		{
			if(empty($wap) && $key == 'moderate')
				break;
			else
			{
				$fnd = array_search($key, $url);
				if($fnd !== false)
				{
					$result[$key] = '';
					array_splice($url, $fnd, 1);
					$wap = $key != 'nowap';
				}
			}
		}
	}
	return !empty($url);
}

/**
* find and convert single token to SMF
* called from: pmxsef_convertQuery
**/
function getSingleKeyParams(&$url, &$result)
{
	global $PortaMxSEF;

	if(!empty($url))
	{
		foreach($PortaMxSEF['singletoken'] as $key)
		{
			$pos = array_search($key, $url);
			if($pos !== false && array_key_exists($key, $PortaMxSEF['pmxextra']) && isset($url[$pos -1]) && array_search($url[$pos -1], $PortaMxSEF['pmxextra'][$key]) === false)
			{
				$result[$key] = '';
				array_splice($url, $pos, 1);
			}
		}
	}
	return !empty($url);
}

/**
* Check if the first msg subject changed
* called from: pmxsef_convertQuery
**/
function pmxsef_CheckTopic($msgid)
{
	global $PortaMxSEF, $smcFunc, $pmxCacheFunc;

	$request = $smcFunc['db_query']('', '
		SELECT id_topic
		FROM {db_prefix}topics
		WHERE id_first_msg = {int:msg}',
		array('msg' => intval($msgid))
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		$row = $smcFunc['db_fetch_assoc']($request);

		if(empty($PortaMxSEF['TopicNameList']))
			$PortaMxSEF['TopicNameList'] = $pmxCacheFunc['get']('pmxsef_topiclist', false, true);

		if(isset($PortaMxSEF['TopicNameList'][$row['id_topic']]))
		{
			unset($PortaMxSEF['TopicNameList'][$row['id_topic']]);
			$pmxCacheFunc['put']('pmxsef_topiclist', $PortaMxSEF['TopicNameList'], 3600);
		}
	}
	$smcFunc['db_free_result']($request);
}

/**
* build the querystring for SMF
* called from: pmxsef_convertQuery
**/
function pmxsef_build_query($data, $prefix = '', $sep = ';')
{
	$ret = array();
	foreach ((array) $data as $k => $v)
	{
		$k = urlencode($k);
		if(is_int($k) && !empty($prefix))
			$k = $prefix . $k;
		if(is_array($v) || is_object($v))
			array_push($ret, pmxsef_build_query($v, '', $sep));
		elseif($v == '')
			array_push($ret, $k);
		else
			array_push($ret, $k .'='. urlencode($v));
	}

	if(empty($sep))
		$sep = ini_get("arg_separator.output");

	return implode($sep, $ret);
}

/**
* convert a string for SEF url's
**/
function pmxsef_encode($string)
{
	global $modSettings, $sourcedir, $PortaMxSEF, $txt;
	static $utf8_db = array();

	$string = trim($string);
	if(empty($string))
		return '';

	// make all strings to ISO-8859-1 or UTF-8 and if not, convert to UTF-8
	$char_set = empty($modSettings['global_character_set']) ? $txt['lang_character_set'] : $modSettings['global_character_set'];
	if($char_set != 'ISO-8859-1' || $char_set != 'UTF-8')
	{
		if(function_exists('iconv'))
			$string = iconv($char_set, 'UTF-8//IGNORE', $string);
		elseif(function_exists('mb_convert_encoding'))
			$string = mb_convert_encoding($string, 'UTF8', $char_set);
		elseif(function_exists('unicode_decode'))
			$string = unicode_decode($string, $char_set);
	}

	$character = 0;
	$result = '';
	$length = strlen($string);
	$i = 0;

	while($i < $length)
	{
		$charInt = ord($string[$i++]);
		// normal Ascii character
		if(($charInt & 0x80) == 0)
			$character = $charInt;

		// Two byte unicode
		elseif(($charInt & 0xE0) == 0xC0)
		{
			$temp1 = ord($string[$i++]);
			if (($temp1 & 0xC0) != 0x80)
				$character = 63;
			else
				$character = ($charInt & 0x1F) << 6 | ($temp1 & 0x3F);
		}

		// Three byte ..
		elseif(($charInt & 0xF0) == 0xE0)
		{
			$temp1 = ord($string[$i++]);
			$temp2 = ord($string[$i++]);
			if (($temp1 & 0xC0) != 0x80 || ($temp2 & 0xC0) != 0x80)
				$character = 63;
			else
				$character = ($charInt & 0x0F) << 12 | ($temp1 & 0x3F) << 6 | ($temp2 & 0x3F);
		}

		// Four byte ..
		elseif(($charInt & 0xF8) == 0xF0)
		{
			$temp1 = ord($string[$i++]);
			$temp2 = ord($string[$i++]);
			$temp3 = ord($string[$i++]);
			if (($temp1 & 0xC0) != 0x80 || ($temp2 & 0xC0) != 0x80 || ($temp3 & 0xC0) != 0x80)
				$character = 63;
			else
				$character = ($charInt & 0x07) << 18 | ($temp1 & 0x3F) << 12 | ($temp2 & 0x3F) << 6 | ($temp3 & 0x3F);
		}

		// Thats wrong... use ?
		else
			$character = 63;

		// get the codepage for this character.
		$charBank = $character >> 8;
		if(!isset($utf8_db[$charBank]))
		{
			// Load up the codepage if it's not already in memory
			$cpFile = $sourcedir . $PortaMxSEF['codepages'] . sprintf('%02x', $charBank) . '.php';
			if(file_exists($cpFile))
				include_once($cpFile);
			else
				$utf8_db[$charBank] = array();
		}

		$finalChar = $character & 255;
		$result .= isset($utf8_db[$charBank][$finalChar]) ? $utf8_db[$charBank][$finalChar] : '?';
	}

	$result = trim(str_replace($PortaMxSEF['stripchars'], '', $result), "\t\r\n\0\x0B .");
	$result = urlencode($result);
	$result = str_replace(array('%2F','%2C','%27','%60'), '', $result);
	$result = str_replace(array($PortaMxSEF['spacechar'], '.'), '+', $result);
	$result = preg_replace('~(\+)+~', $PortaMxSEF['spacechar'], $result);

	return (!empty($PortaMxSEF['lowercase']) ? strtolower($result) : $result);
}
?>
