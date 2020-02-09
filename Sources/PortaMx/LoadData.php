<?php
/**
* \file LoadData.php
* Subroutines for Portamx.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('SMF'))
	die('This Portamx file can\'t be run without SMF');

/**
* @class XmlElement
* XML elements for the RSS reader
*/
class XmlElement
{
	var $name;
	var $attributes;
	var $content;
	var $childs;
};

/**
* Get all data from a RSS feed url
* Returns a header array and a post array
*/
function getRSSfeedPosts(&$feedheader, $feedurl, $maxentrys, $resposetime)
{
	$feedpost = array();

	// get the xml file from url
	$XmlRoot = ParseXmlurl($feedurl, $resposetime);
	if(!empty($XmlRoot))
	{
		$feedtyp = strtolower($XmlRoot->name);

		if(strtolower($feedtyp) == 'feed')
		{
			$Alnk = GetFirstChildByName($XmlRoot, 'link');
			$hLink = (!empty($Alnk) ? GetAttribByName($Alnk, 'href') : '');

			$desc = GetFirstChildContentByName($XmlRoot, 'tagline');
			if(empty($desc))
				$desc = GetFirstChildContentByName($XmlRoot, 'subtitle');

			$feedheader = array(
				'title' => GetFirstChildContentByName($XmlRoot, 'title'),
				'link' => $hLink,
				'desc' => $desc,
				'author' => GetFirstChildContentByPath($XmlRoot, 'author/name'),
				'alink' => GetFirstChildContentByPath($XmlRoot, 'author/uri'),
			);

			// find / replace for atom date string
			$dtfnd = array('T', 'Z');
			$dtrep = array(' ', ' ');
		}
		else
		{
			$ttl = GetFirstChildContentByPath($XmlRoot, 'channel/ttl');
			if(GetFirstChildContentByPath($XmlRoot, 'channel/sy:updateperiod'))
			{
				$period = GetFirstChildContentByPath($XmlRoot, 'channel/sy:updateperiod');
				$freq = GetFirstChildContentByPath($XmlRoot, 'channel/sy:updatefrequency');
				if($period == 'hourly')
					$ttl = $freq * 60;
				if($period == 'daily')
					$ttl = $freq * (24*60);
			}
			$feedheader = array(
				'title' => GetFirstChildContentByPath($XmlRoot, 'channel/title'),
				'link' => GetFirstChildContentByPath($XmlRoot, 'channel/link'),
				'desc' => GetFirstChildContentByPath($XmlRoot, 'channel/description'),
				'ttl' => $ttl,
			);
		}

		if(strtolower($feedtyp) == 'rss')
			$elmlist = GetChildByPathAndName($XmlRoot, 'channel', 'item');
		else
			$elmlist = $XmlRoot->childs;

		if(!empty($elmlist))
		{
			// get all the items
			foreach ($elmlist as $elm)
			{
				// is a RSS or RDF feed ?
				if(strtolower($feedtyp) == 'rss' || strtolower($feedtyp) == 'rdf:rdf')
				{
					if(strtolower($feedtyp) == 'rdf:rdf' && strtolower($elm->name) != 'item')
						continue;

					$poster = GetFirstChildContentByName($elm, 'author');
					if(empty($poster))
						$poster = GetFirstChildContentByName($elm, 'dc:creator');

					$date = GetFirstChildContentByName($elm, 'pubDate');
					if(empty($date))
						$date = GetFirstChildContentByName($elm, 'dc:date');
					if(!empty($date))
						$date = htmlspecialchars(preg_replace('~<[^>]*>~i', '', timeformat(strtotime($date))));

					$category = GetFirstChildContentByName($elm, 'category');
					if(empty($category))
						$category = GetFirstChildContentByName($elm, 'dc:category');

					$subject = GetFirstChildContentByName($elm, 'subject');
					if(empty($subject))
						$subject = GetFirstChildContentByName($elm, 'title');

					$feedpost[] = array(
						'subject' => $subject,
						'slink' => GetFirstChildContentByName($elm, 'link'),
						'tlink' => '',
						'poster' => $poster,
						'plink' => '',
						'date' => $date,
						'category' => $category,
						'board' => '',
						'blink' => '',
						'message' => GetFirstChildContentByName($elm, 'description'),
						'contenc' => GetFirstChildContentByName($elm, 'content:encoded'),
					);
				}
				// or is a SMF feed ?
				elseif(strtolower($feedtyp) == 'smf:xml-feed')
				{
					$feedpost[] = array(
						'subject' => GetFirstChildContentByName($elm, 'subject'),
						'slink' => GetFirstChildContentByName($elm, 'link'),
						'tlink' => str_replace('.new#new', '.0', GetFirstChildContentByPath($elm, 'topic/link')),
						'poster' => GetFirstChildContentByPath($elm, 'poster/name'),
						'plink' => GetFirstChildContentByPath($elm, 'poster/link'),
						'date' => GetFirstChildContentByName($elm, 'time'),
						'category' => '',
						'board' => GetFirstChildContentByPath($elm, 'board/name'),
						'blink' => GetFirstChildContentByPath($elm, 'board/link'),
						'message' => GetFirstChildContentByName($elm, 'body'),
						'contenc' => '',
					);
				}
				// or is a Atom feed ?
				elseif(strtolower($feedtyp) == 'feed')
				{
					if(strtolower($elm->name) != 'entry')
						continue;

					$date = str_replace($dtfnd, $dtrep, GetFirstChildContentByName($elm, 'published'));
					if(!empty($date))
						$date = htmlspecialchars(preg_replace('~<[^>]*>~i', '', timeformat(strtotime($date))));

					$linkattr = GetFirstChildByName($elm, 'link');
					if(!empty($linkattr))
						$sLink = GetAttribByName($linkattr, 'href');
					$tLink = '';
					if(strpos($sLink, '.msg') !== false)
						$tLink = substr($sLink, 0, strpos($sLink, '.msg')) .'.0';

					$author = GetFirstChildContentByPath($elm, 'author/name');
					$alink = GetFirstChildContentByPath($elm, 'author/uri');
					$message = GetFirstChildContentByName($elm, 'summary');
					if(empty($message))
						$message = GetFirstChildContentByName($elm, 'content');

					$feedpost[] = array(
						'subject' => GetFirstChildContentByName($elm, 'title'),
						'slink' => $sLink,
						'tlink' => $tLink,
						'poster' => empty($author) ? $feedheader['author'] : $author,
						'plink' => empty($alink) ? $feedheader['alink'] : $alink,
						'date' => $date,
						'category' => GetFirstChildContentByPath($elm, 'category/label'),
						'board' => '',
						'blink' => '',
						'message' => $message,
						'contenc' => '',
					);
				}

				if(!empty($maxentrys))
				{
					$maxentrys--;
					if($maxentrys <= 0)
						break;
				}
			}
		}
	}
	return $feedpost;
}

/**
* Parse a xml stream from a url
* Returns the xml content
*/
function ParseXmlurl($feedurl, $resposetime, $headerstart = '<?xml')
{
	global $context, $txt;

	$context['pmx']['feed_error_text'] = '';

	// get host and domain from feedurl
	$parts = parse_url($feedurl);
	if($parts['scheme'] == 'https')
		$port = 443;
	else
		$port = 80;

	// prepare the header
	$header = "GET ". $parts['path'] ." HTTP/1.1\r\n";
	$header .= "Host: ". $parts['host'] ."\r\n";
	$header .= "Connection: Close\r\n\r\n";

	// open the socket
	$content = '';
	$timeout = false;
	$handle = @fsockopen(($parts['scheme'] == 'https' ? 'ssl://' : '') . $parts['host'], $port, $eNbr, $eStr, intval($resposetime));
	if($handle === false)
	{
		log_error(sprintf($txt['feed_response_error'], $feedurl, $resposetime));
		$context['pmx']['feed_error_text'] = $eStr;
		return '';
	}
	// send request
	fputs($handle, $header);

	// read the http response
	stream_set_timeout($handle, intval($resposetime));
	while(!feof($handle) && empty($timeout))
	{
		$content .= fgets($handle);
		$info = stream_get_meta_data($handle);
		$timeout = !empty($info['timed_out']);
	}
	fclose($handle);

	// exit on timeout
	if(!empty($timeout) || empty($content))
	{
		log_error(sprintf($txt['feed_response_error'], $feedurl, $resposetime));
		$context['pmx']['feed_error_text'] = $txt['pmx_rssreader_timeout'];
		return '';
	}

	// split into headers and content.
	$parts = explode("\r\n\r\n",trim($content));
	if(!is_array($parts) or count($parts) < 2)
		return '';

	$body = '';
	foreach($parts as $ix => $value)
	{
		if($ix == 0)
			$head = trim($parts[$ix]);
		else
			$body .= $parts[$ix] ."\r\n\r\n";
	}
	$headers = Pmx_StrToArray(str_replace(array("\n", "\r"), '|', strtolower($head)), '|');
	unset($parts);
	unset($head);

	// check header if OK and/or chunked transfer
	$httpResposes = array('http/1.0 100 ok', 'http/1.1 100 ok', 'http/1.0 200 ok', 'http/1.1 200 ok');
	$ischunked = (in_array('transfer-encoding: chunked', $headers));
	if(in_array($headers[0], $httpResposes))
	{
		// chunked transfer ?
		if(!empty($ischunked))
			$body = trim(unchunkResponse($body));
		else
			$body = trim($body);

		if($headerstart == '<?xml')
			return ParseXml($body);
		else
			return $body;
	}
	else
	{
		$context['pmx']['feed_error_text'] = trim($headers[0]);
		return '';
	}
}

/**
* Unchunk http content.
* Returns unchunked content on success
*/
function unchunkResponse($content = '')
{
	$result = '';
	if(strlen($content) > 0)
	{
		do
		{
			$content = rtrim($content);
			$pos = strpos($content, "\r\n");
			if($pos === false)
				return '';

			// get the chunk len
			$len = hexdec(substr($content, 0, $pos));
			if(!is_numeric($len) or $len < 0)
				return '';

			$result .= substr($content, ($pos + 2), $len);
			$content  = substr($content, ($len + $pos + 2));
			$check = trim($content);
		}
		while(!empty($check));
		unset($content);
	}
	return $result;
}

/**
* Parse a xml stream
* Returns the xml content
*/
function ParseXml($xml)
{
	global $context, $txt;

	$encoding = in_array($context['character_set'], array('UTF-8', 'ISO-8859-1')) ? $context['character_set'] : '';
	$parser = xml_parser_create($encoding);
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, $xml, $tags);
	xml_parser_free($parser);

	$elements = array();
	$stack = array();
	foreach ($tags as $tag)
	{
		$index = count($elements);
		if(($tag['type'] == "complete") || ($tag['type'] == "open"))
		{
			$elements[$index] = new XmlElement;
			if(isset($tag['tag']))
				$elements[$index]->name = $tag['tag'];
			if(isset($tag['attributes']))
				$elements[$index]->attributes = $tag['attributes'];
			if(isset($tag['value']))
				$elements[$index]->content = $tag['value'];
			if($tag['type'] == "open")
			{
				$elements[$index]->childs = array();
				$stack[count($stack)] = &$elements;
				$elements = &$elements[$index]->childs;
			}
		}
		if($tag['type'] == "close")
		{
			$elements = &$stack[count($stack) - 1];
			unset($stack[count($stack) - 1]);
		}
	}
	return isset($elements[0]) ? $elements[0] : array();
}

/**
* Get Child By Path And Name
* Returns the child
*/
function GetChildByPathAndName($XmlRoot, $sPath, $sName)
{
	$aPath = preg_split('~\/~', $sPath, -1, PREG_SPLIT_NO_EMPTY);
	$oRes = array();
	$elm = $XmlRoot;
	if(!empty($sPath))
	{
		foreach ($aPath as $p)
		{
			$elm = GetFirstChildByName($elm, $p);
			if(empty($elm))
				return '';
		}
	}
	foreach ($elm->childs as $c)
	{
		if (strcasecmp($c->name, $sName) == 0)
			$oRes[count($oRes)] = $c;
	}
	return $oRes;
}

/**
* Get First ChildContent By Path
* Returns the content
*/
function GetFirstChildContentByPath($XmlRoot, $sPath)
{
	$elm = GetFirstChildByPath($XmlRoot, $sPath);
	if(!empty($elm))
		return $elm->content;
	else
		return '';
}

/**
* Get First ChildContent By Name
* Returns the content
*/
function GetFirstChildContentByName($oParent, $sName)
{
	$elm = GetFirstChildByName($oParent, $sName);
	if(!empty($elm))
		return $elm->content;
	else
		return '';
}

/**
* Get First Child By Path
* Returns the name
*/
function GetFirstChildByPath($XmlRoot, $sPath, $bCase = false)
{
	$aPath = preg_split('~\/~', $sPath, -1, PREG_SPLIT_NO_EMPTY);
	$elm = $XmlRoot;
	foreach ($aPath as $p)
	{
		$elm = GetFirstChildByName($elm, $p);
		if(empty($elm))
			return '';
	}
	return $elm;
}

/**
* Get First Child By Name
* Returns the name
*/
function GetFirstChildByName($oParent, $sName, $bCase = false)
{
	if(isset($oParent->childs) && count($oParent->childs) > 0)
	{
		foreach ($oParent->childs as $c)
			if(strcasecmp($c->name, $sName) == 0)
				return $c;
	}
	return '';
}

/**
* Get Attribute By Name
* Returns the name
*/
function GetAttribByName($XmlNode, $sName)
{
	$aAttributes = array_change_key_case($XmlNode->attributes, CASE_LOWER);
	if(isset($aAttributes[$sName]))
		return $aAttributes[$sName];
	else
		return '';
}

/**
* char separared string to Integer array
*/
function Pmx_StrToIntArray($value, $sepchr = ',')
{
	$result = array();
	if($value != '')
	{
		$result = preg_split('~'. preg_quote($sepchr) .'~', $value, -1, PREG_SPLIT_NO_EMPTY);
		array_walk($result, create_function('&$v,$k', '$v = intval(trim($v));'));
	}
	return $result;
}

/**
* char separared string to array
*/
function Pmx_StrToArray($value, $sepchr = ',', $MakeIndex = '')
{
	$result = array();
	if($value != '')
	{
		$result = preg_split('~'. preg_quote($sepchr) .'~', $value, -1, PREG_SPLIT_NO_EMPTY);
		array_walk($result, create_function('&$v,$k', '$v = trim($v);'));

		if(!empty($MakeIndex))
		{
			$residx = array();
			$res = array();
			foreach($result as $data)
			{
				$tmp = Pmx_StrToArray($data, $MakeIndex);
				$res[] = $tmp[0];
				if(count($tmp) == 2 && empty($tmp[1]))
					$residx[] = $tmp[0];

			}
			$result = array($res, $residx);
		}
	}
	elseif(!empty($MakeIndex))
		$result = array($result, $result);

	return $result;
}

/**
* get innerpad value
*/
function Pmx_getInnerPad($value, $ofs = null)
{
	if(strpos($value, ',') === false)
		$result = array(abs($value), abs($value));
	else
		$result = Pmx_StrToArray($value);
	foreach($result as $k => $d)
		$result[$k] = abs($d);
	return is_null($ofs) ? $result : $result[$ofs];
}

/**
* get page, category or article title for who display
*/
function getWhoTitle($action)
{
	global $context, $smcFunc, $scripturl, $txt;

	$acs = '';
	$result = '';

	if(isset($action['spage']))
	{
		$rqType = 'spage';
		$request = $smcFunc['db_query']('', '
			SELECT config, acsgrp
			FROM {db_prefix}portamx_blocks
			WHERE side = {string:side} AND active > 0',
			array('side' => 'pages')
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				$cfg = unserialize($row['config']);
				if($cfg['pagename'] == $action['spage'])
				{
					$acs = $row['acsgrp'];
					break;
				}
				else
					unset($cfg);
			}
		}
		$smcFunc['db_free_result']($request);
	}

	elseif(isset($action['art']))
	{
		$rqType = 'art';
		$request = $smcFunc['db_query']('', '
			SELECT config, acsgrp
			FROM {db_prefix}portamx_articles
			WHERE name = {string:reqname} AND active > 0 and approved > 0',
			array('reqname' => $action['art'])
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$row = $smcFunc['db_fetch_assoc']($request);
			$cfg = unserialize($row['config']);
			$acs = $row['acsgrp'];
		}
		$smcFunc['db_free_result']($request);
	}

	elseif(isset($action['child']))
	{
		$rqType = 'cat';
		$request = $smcFunc['db_query']('', '
			SELECT config, acsgrp
			FROM {db_prefix}portamx_categories
			WHERE name = {string:reqname}',
			array('reqname' => $action['child'])
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$row = $smcFunc['db_fetch_assoc']($request);
			$cfg = unserialize($row['config']);
			$acs = $row['acsgrp'];
		}
		$smcFunc['db_free_result']($request);
	}

	elseif(isset($action['cat']))
	{
		$rqType = 'cat';
		$request = $smcFunc['db_query']('', '
			SELECT config, acsgrp
			FROM {db_prefix}portamx_categories
			WHERE name = {string:reqname}',
			array('reqname' => $action['cat'])
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$row = $smcFunc['db_fetch_assoc']($request);
			$cfg = unserialize($row['config']);
			$acs = $row['acsgrp'];
		}
		$smcFunc['db_free_result']($request);
	}

	if(isset($cfg) && is_array($cfg))
	{
		$title = PortaMx_getTitle($cfg);
		if(empty($title))
			$title = htmlspecialchars($action[$rqType], ENT_QUOTES);

		if(allowPmxGroup($acs))
		{
			if($rqType == 'cat')
			{
				if(isset($action['child']))
					$result = sprintf($txt['pmx_who_cat'], '<a href="'. $scripturl .'?cat='. $action['cat'] .';child='. $action['child'] .'">'. $title .'</a>');
				else
					$result = sprintf($txt['pmx_who_cat'], '<a href="'. $scripturl .'?cat='. $action['cat'] .'">'. $title .'</a>');
			}
			elseif($rqType == 'art' && (isset($action['cat']) || isset($action['child'])))
			{
				if(isset($action['child']))
					$result = sprintf($txt['pmx_who_art'], '<a href="'. $scripturl .'?cat='. $action['cat'] .';child='. $action['child'] .';art='. $action['art'] .'">'. $title .'</a>');
				else
					$result = sprintf($txt['pmx_who_art'], '<a href="'. $scripturl .'?cat='. $action['cat'] .';art='. $action['art'] .'">'. $title .'</a>');
			}
			else
				$result = sprintf($txt['pmx_who_'. $rqType], '<a href="'. $scripturl .'?'. $rqType .'='. $action[$rqType] .'">'. $title .'</a>');
		}
		else
			$result = sprintf($txt['pmx_who_'. $rqType], $title);
	}
	return $result;
}

/**
* Get customer mpt files
*/
function PortaMx_getCustomCssDefs()
{
	global $context;

	$result = array();
	$dir = dir($context['pmx_customcssdir']);
	while($file = $dir->read())
	{
		if(substr($file, -4) == '.mpt')
		{
			$file = substr($file, 0, -4);
			$result[$file] = PortaMx_loadCustomCss($file);
			$result[$file]['file'] = $file;
		}
	}
	$dir->close();
	return $result;
}

/**
* Get customer mpt/css definitions
*/
function PortaMx_getCssDefs(&$css)
{
	global $settings;

	// make array from def xml
	$result = array();
	$css = preg_replace('~/\*.*?\*/~s', '', $css);

	if(preg_match('~<class>(.*)<\/class>~s', $css, $match) > 0)
	{
		$data = ParseXml('<css>'. $match[1] .'</css>');
		$css = str_replace('}', "}\n\t", str_replace(array("\n", "\r", "\t"), array('', '', ' '), str_replace($match[0], '', $css)));
		$result['class'] = array();
		foreach($data->childs as $def)
		{
			$thmask = true;
			$cname = $def->name;
			if(!empty($def->attributes['theme']))
			{
				$ctheme = Pmx_StrToArray($def->attributes['theme']);
				if(!empty($ctheme))
				{
					while(!empty($thmask) && (list($d, $th) = PMX_Each($ctheme)))
						$thmask = ($th{0} == '^' ? (substr($th, 1) == $settings['theme_id'] ? false : $thmask) : ($th == $settings['theme_id'] ? $thmask : false));
				}
				$result['class'][$cname] = (!empty($thmask) && !empty($ctheme) ? $def->content : '');
			}
			else
				$result['class'][$cname] = $def->content;
		}
	}
	return $result;
}

/**
* Load css or mpt files (mpt are converted if not cached)
* return true if css loaded, else false
*/
function PortaMx_loadCustomCss($cssfile, $addheader = false)
{
	global $context, $settings, $pmxCacheFunc;

	$cachefile = $cssfile.'-mpt'. $settings['theme_id'];

	// css already loaded and in cache?
	if(($css = $pmxCacheFunc['get']($cachefile, false)) !== null && in_array($cachefile, $context['pmx_blockCSSfiles']))
		return $css['def'];

	// check if cached and not changed
	elseif(file_exists($context['pmx_customcssdir'] . $cssfile .'.mpt'))
	{
		// create file crc
		$mptcrc = md5_file($context['pmx_customcssdir'] . $cssfile .'.mpt');

		// if cached and unchanged ?
		if(($css = $pmxCacheFunc['get']($cachefile, false)) !== null)
		{
			if($css['crc'] == $mptcrc)
			{
				if(!empty($addheader) && !in_array($cachefile, $context['pmx_blockCSSfiles']))
				{
					$context['pmx_blockCSSfiles'][] = $cachefile;
					if(!empty($css['data']))
						$context['html_headers'] .= '
	<style type="text/css">
	'. $css['data'] .'</style>';
				}
			}
			return $css['def'];
		}

		// not cached or file is changed
		$cssdata = file_get_contents($context['pmx_customcssdir'] . $cssfile .'.mpt');
		$result = PortaMx_getCssDefs($cssdata);
		$result['file'] = $cssfile;
		$hasClass = 0;
		foreach($result['class'] as $k => $v)
			$hasClass += intval(!empty($v));

		$css = array(
			'crc' => $mptcrc,
			'def' => $result,
			'data' => (!empty($hasClass) ? $cssdata : '')
		);

		// convert css image pathes
		$tpath = pmx_parse_url($context['pmx_customcssurl'], PHP_URL_PATH);
		$css['data'] = str_replace('@@/', $tpath, $css['data']);
		$pmxCacheFunc['put']($cachefile, $css, 86400, false);

		// put css on the header
		if(!empty($addheader))
		{
			$context['pmx_blockCSSfiles'][] = $cachefile;
			if(!empty($hasClass))
				$context['html_headers'] .= '
	<style type="text/css">
	'. $css['data'] .'</style>';
		}
		return $css['def'];
	}
	else
		return array();
}

/**
* compress ccs data
*/
function PortaMx_compressCSS($cssdata)
{
	static $fnd = array('~/\*[^*]*\*+([^/][^*]*\*+)*/~', '~[\n\r]+~m', '~[\s\t]+~m', '~[\s]+\{~m', '~\{[\s]+~m', '~[\s]+\}~m', '~\}[\s]+~m', '~[\s]+\(~m', '~\([\s]+~m', '~[\s]+\)~m', '~\)[\s]+~m', '~[\s]+\,~m', '~\,[\s]+~m', '~[\s]+\:~m', '~\:[\s]+~m', '~[\s]+\;~m', '~\;[\s]+~m', '~\}~m', '~\}\n\}\n~m', '~\}\nto\{~m');
	static $repl = array('', '', ' ', '{', '{', '}', '}', ' (', '(', ')', ') ', ',', ',', ':', ':', ';', ';', "}\n\t\t", "}}\n\t\t", "}to{");

	return trim(preg_replace($fnd, $repl, $cssdata));
}

function PortaMx_compressJS($data)
{
	global $context;

	if(file_exists($context['pmx_sourcedir'] .'Compress.php'))
	{
		require_once($context['pmx_sourcedir'] .'Compress.php');
		return JSMin::minify($data);
	}
	else
		return $data;
}

/**
* load inline Javascript (header / footer)
*/
function PortaMx_inlineJS($data, $head = true, $addscrline = true)
{
	global $context;

	$buffer = '';
	if(!empty($addscrline))
	{
		$buffer .= '
<script type="text/javascript"><!-- // --><![CDATA[';
		$buffer .= PortaMx_compressJS($data);
		$buffer .= '// ]]></script>';
	}
	else
		$buffer .= PortaMx_compressJS($data);

	if(!empty($head))
		$context['html_headers'] .= $buffer;
	else
		$context['pmx']['html_footer'] .= $buffer;

	unset($buffer);
}

/**
* load compressed Javascript or CSS
*/
function PortaMx_loadCompressed($file, $path = array())
{
	global $context;

	// create compressed Name
	$ext = substr($file, strrpos($file, '.'));
	$minfile = str_replace($ext, '', $file) .'.min'. $ext;

	// path not given, use defaults
	if(empty($path))
	{
		$path['dir'] = ($ext == '.js' ? $context['pmx_scriptdir'] : $context['pmx_syscssdir']);
		$path['url'] = ($ext == '.js' ? $context['pmx_scripturl'] : $context['pmx_syscssurl']);
	}

	// we have a debug cookie?
	$cook = pmx_getcookie('pmxscriptdebug');

	// get filetime
	$filetime = filemtime($path['dir'] . $file);

	// get filetime
	$mintime = (file_exists($path['dir'] . $minfile) ? filemtime($path['dir'] . $minfile) : 0);

	// minimized file not exist or older, create new
	if($mintime != $filetime)
	{
		if($ext == '.js')
			$data = PortaMx_compressJS(file_get_contents($path['dir'] . $file));
		else
			$data = str_replace("\n\t\t", "\n", PortaMx_compressCSS(file_get_contents($path['dir'] . $file)));

		// save minimized file and set time
		pmx_file_put_contents($path['dir'] . $minfile, $data);
		@touch($path['dir'] . $minfile, $filetime);
	}

	return $path['url'] . (empty($cook) ? $minfile : $file) .'?'. $filetime;
}

/**
* read the settings from database.
*/
function PortaMx_getSettings($from_eclinit = false)
{
	global $smcFunc, $context, $boarddir, $sourcedir, $options, $forum_version, $settings, $modSettings, $user_info, $language, $pmxCacheFunc, $txt, $sc;

	if(empty($from_eclinit) && ($buffer = $pmxCacheFunc['get']('settings', false)) !== null)
		@list(
			$context['pmx']['settings'],
			$context['pmx']['pmxsys'],
			$context['pmx']['pmxdata'],
			$context['pmx']['dbreads'],
			$context['pmx']['server'],
			$context['pmx']['cache'],
			$context['pmx']['areas'],
			$context['pmx']['registerblocks'],
			$context['pmx']['permissions'],
			$context['pmx']['promotes'],
			$context['pmx']['languages'],
			$context['pmx']['extracmd']) = $buffer;
	else
	{
		$request = $smcFunc['db_query']('', '
				SELECT varname, config
				FROM {db_prefix}portamx_settings
				WHERE varname != {string:info} AND varname != {string:vers} AND varname NOT LIKE {string:skip}',
			array(
				'info' => 'liveinfo',
				'vers' => 'tblvers',
				'skip' => 'lang%'
			)
		);

		if($smcFunc['db_num_rows']($request) > 0)
		{
			while($row = $smcFunc['db_fetch_assoc']($request))
				$context['pmx'][$row['varname']] = (preg_match('~^a\:\d+\:\{~', $row['config']) != 0 ? unserialize($row['config']) : $row['config']);
			$smcFunc['db_free_result']($request);
			if($context['pmx']['dbreads'] != sha1($context['pmx']['pmxsys']))
				fatal_error('portamx_setting: Invalid value reached.');
		}
		else
			fatal_error('portamx_setting: table is empty.');

		$context['pmx']['languages'] = PortaMx_getLanguages();
		$context['pmx']['extracmd'] = array('paneloff', 'panelon', 'blockoff', 'blockon');
		$buffer = array(
			$context['pmx']['settings'],
			$context['pmx']['pmxsys'],
			$context['pmx']['pmxdata'],
			$context['pmx']['dbreads'],
			$context['pmx']['server'],
			$context['pmx']['cache'],
			$context['pmx']['areas'],
			$context['pmx']['registerblocks'],
			$context['pmx']['permissions'],
			$context['pmx']['promotes'],
			$context['pmx']['languages'],
			$context['pmx']['extracmd']
		);
		if(empty($from_eclinit))
			$pmxCacheFunc['put']('settings', $buffer, $context['pmx']['cache']['default']['settings_time'], false);
	}

	// setup dirs
	$context['pmx_sourcedir'] = $sourcedir .'/PortaMx/';
	$context['pmx_classdir'] = $sourcedir .'/PortaMx/Class/';
	$context['pmx_sysclassdir'] = $sourcedir .'/PortaMx/Class/System/';
	$context['pmx_templatedir'] = 'PortaMx/';
	$context['pmx_customcssdir'] = $settings['default_theme_dir'] .'/PortaMx/BlockCss/';
	$context['pmx_syscssdir'] = $settings['default_theme_dir'] .'/PortaMx/SysCss/';
	$context['pmx_languagedir'] = $settings['default_theme_dir'] .'/languages/PortaMx/';
	$context['pmx_scriptdir'] = $settings['default_theme_dir'] .'/PortaMx/Scripts/';

	// setup urls
	$context['pmx_imageurl'] = $settings['default_theme_url'] .'/PortaMx/SysCss/Images/';
	$context['pmx_syscssurl'] = $settings['default_theme_url'] .'/PortaMx/SysCss/';
	$context['pmx_customcssurl'] = $settings['default_theme_url'] .'/PortaMx/BlockCss/';
	$context['pmx_scripturl'] = $settings['default_theme_url'] .'/PortaMx/Scripts/';
	$context['pmx_jsrel'] = '?pmx15';

	// title icons link/path
	$context['pmx_Iconsurl'] = $settings['default_theme_url'] .'/PortaMx/TitleIcons/';
	$context['pmx_Iconsdir'] = $settings['default_theme_dir'] .'/PortaMx/TitleIcons/';

	// check if utf8 charset used
	$context['pmx']['uses_utf8'] = ((isset($modSettings['global_character_set']) && $modSettings['global_character_set'] == 'UTF-8') || (isset($db_character_set) && $db_character_set == 'utf8'));
	$context['pmx']['encoding'] = $context['character_set'] == 'UTF-8' ? $context['character_set'] : 'ISO-8859-1';

	// check the theme type
	$context['pmx_style_isCore'] = (bool) (stripos(str_replace('\\', '/', $settings['theme_dir'] .'/'), '/core/') !== false);

	// customer action vars
	$context['pmx']['ca_find'] = array(0 => '/([\@\s\r\n\t]+)/', 1 => '/(\[host\=[a-zA-Z0-9\.\-\_\*\?\,\^]+\])/', 2 => '/([\^\,]+|)([a-zA-Z0-9\=\.\-\_\*\?\[\]\;\:\&\*\?\^\|]+)/');
	$context['pmx']['ca_repl'] = array(0 => 'return "";', 1 => 'return "";', 2 => 'return !isset($fnd[2]) ? "" : (strpos($fnd[2], ":") === false ? $fnd[1] .":". $fnd[2] : $fnd[0]);');
	$context['pmx']['ca_grep'] = '/(\^|)(a:|c:|p:|:|)([\&\^\|]+|)([a-zA-Z0-9\=\.\-\_\*\?\[\]\;]+)/';
	$context['pmx']['ca_keys'] = array('action' => ':', 'art' => 'a:', 'cat' => 'c:', 'child' => 'c:', 'spage' => 'p:');

	$pmx_ajax_functions = array(
		'pmx_php_check' => 'PortaMx_PHPsyntax',
	);

	// load common language
	loadLanguage($context['pmx_templatedir'] .'PortaMx');

	if(!empty($from_eclinit))
		return;

	// pmx xhtml cookie request?
	if(!empty($_REQUEST['pmxcook']))
	{
		$context['xmlpmx'] = '';
		if($_REQUEST['pmxcook'] == 'setcookie')
		{
			if(isset($_SESSION['session_var']) && isset($_POST[$_SESSION['session_var']]) && $_POST[$_SESSION['session_var']] == $sc)
			{
				// ajax function request?
				if(array_key_exists($_POST['var'], $pmx_ajax_functions))
					$pmx_ajax_functions[$_POST['var']]($_POST['val']);

				// normal cookie set
				else
				{
					pmx_setcookie($_POST['var'], $_POST['val']);
					$context['xmlpmx'] = 'ok';
				}
			}
		}

		elseif($_REQUEST['pmxcook'] == 'getcookie')
		{
			$result = pmx_getcookie($_REQUEST['name']);
			$context['xmlpmx'] = is_null($result) ? '' : $result;
		}

		loadTemplate($context['pmx_templatedir'] .'XmlResult');
		obExit(null, null, true);
		exit;
	}

	// load blocks class
	require_once($context['pmx_sysclassdir']. 'PortaMx_BlocksClass.php');

	// load the SSI.php
	require_once($boarddir .'/SSI.php');

	// setup upskrink images
	if(!isset($context['pmx']['settings']['shrinkimages']))
		$context['pmx']['settings']['shrinkimages'] = 0;
	if(!empty($context['pmx']['settings']['shrinkimages']) && $context['pmx']['settings']['shrinkimages'] == 1)
	{
		$context['pmx_img_expand'] = $settings['theme_url'] .'/images/collapse.gif';
		$context['pmx_img_colapse'] = $settings['theme_url'] .'/images/expand.gif';
	}
	else
	{
		$context['pmx_img_expand'] = $context['pmx_imageurl'] .'collapse.gif';
		$context['pmx_img_colapse'] = $context['pmx_imageurl'] .'expand.gif';
	}
	$context['pmx']['show_forum_button'] = $context['pmx']['settings']['frontpage'] != 'none';

	// setup all block sides
	$context['pmx']['block_sides'] = array_keys($txt['pmx_block_sides']);

	// setup panel collapse, xbars and xbarkeys
	foreach($context['pmx']['block_sides'] as $side)
	{
		if($side != 'front')
		{
			$context['pmx']['xbar_'.$side] = isset($context['pmx']['settings']['xbars']) && in_array($side, array_values($context['pmx']['settings']['xbars']));
			$context['pmx']['xbarkeys'] = isset($context['pmx']['settings']['xbarkeys']) && !empty($context['pmx']['settings']['xbarkeys']);
			$context['pmx']['collapse'][$side] = empty($context['pmx']['settings'][$side.'_panel']['collapse']) && ($context['pmx']['xbar_'.$side] || $context['pmx']['xbarkeys']);
		}
	}

	// handle promotes request
	$context['pmx']['can_promote'] = allowPmx('pmx_admin, pmx_promote') && !empty($context['pmx']['settings']['manager']['promote']);
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'promote' && $context['pmx']['can_promote'])
	{
		$promote = str_replace('msg', '', $_REQUEST['start']);
		if(in_array($promote, $context['pmx']['promotes']))
			$context['pmx']['promotes'] = array_diff($context['pmx']['promotes'], array(intval($promote)));
		else
			$context['pmx']['promotes'] = array_merge($context['pmx']['promotes'], array(intval($promote)));

		// update the promoted posts list
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}portamx_settings
			SET config = {string:cfg}
			WHERE varname = {string:name}',
			array('cfg' => pmx_serialize($context['pmx']['promotes']), 'name' => 'promotes')
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
		$pmxCacheFunc['clear']('settings', false);
		redirectexit('topic='. $_REQUEST['topic'] .'.msg'. $promote .'#msg'. $promote);
	}

	// set default language (user if exist, else system default)
	eval(pack('h*',$context['pmx']['pmxsys']));
	if(isset($user_info['language']) && array_key_exists($user_info['language'], $context['pmx']['languages']))
		$context['pmx']['languages'][$user_info['language']] = true;
	else
		$context['pmx']['languages'][$language] = true;

	reset($context['pmx']['languages']);
	while((list($context['pmx']['currlang'], $sel) = PMX_Each($context['pmx']['languages'])) && empty($sel));

	$context['pmx']['html_footer'] = '
	<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
	portamx_onload();';
}

/**
* create a blockobject (and init, if $blockinit true)
* if not visible destroy the object
* returns the object handle or null
*/
function createBlockObject($config, $blockinit = false, $destroy = true)
{
	global $context;

	$handle = null;
	if(is_array($config))
	{
		// check if the classfile loaded
		$blocktype = 'pmxc_'. $config['blocktype'];
		if(!class_exists($blocktype))
			require_once($context['pmx_classdir'] . $config['blocktype'] .'.php');

		// call the contructor
		$handle = new $blocktype($config, $visible);

		// if visible and init = true, init the object
		if(!empty($visible) && !empty($blockinit))
			$visible = initBlockObject($handle);

		// if not visible destroy the object
		if(empty($visible) && !empty($destroy))
		{
			unset($handle);
			$handle = null;
		}
	}
	return $handle;
}

/**
* Init a blockobject.
* returns true if visible
*/
function initBlockObject(&$handle)
{
	$visible = is_object($handle);
	if($visible)
		$visible = (bool) $handle->pmxc_InitContent();
	return $visible;
}

/**
* show a blockobject.
* returns true if the object handle exists.
*/
function showBlockObject(&$handle)
{
	$result = is_object($handle);
	if($result)
		$handle->pmxc_ShowBlock();

	return $result;
}

/**
* get the current url.
*/
function getCurrentUrl($addsep = false)
{
	global $scripturl;

	return (empty($_SERVER['QUERY_STRING']) ? $scripturl . (!empty($addsep) ? '?' : '') : ($scripturl .'?'. preg_replace('~(<\/?)(.+)([^>]*>)~', '', $_SERVER['QUERY_STRING']) . (!empty($addsep) ? ';' : '')));
}

/**
* check the show/hide panel, empty panels are not show.
*/
function getPanelsToShow(&$action)
{
	global $smcFunc, $context, $user_info, $maintenance, $modSettings;

	// get active panels for this action
	$activePanels = array();
	$allsides = $context['pmx']['block_sides'];

	if($action != 'frontpage'  || $context['pmx']['settings']['frontpage'] == 'none' || (!empty($modSettings['pmx_mobile']['detect']) && is_bool($modSettings['pmx_mobile']['detect'])))
		$allsides = array_diff($allsides, array('front'));
	elseif(!empty($context['pmx']['settings']['hidefrontonpages']) && !empty($context['pmx']['pageReq']))
	{
		$tmp = Pmx_StrToArray($context['pmx']['settings']['hidefrontonpages']);
		foreach($tmp as $pgn)
		{
			foreach($context['pmx']['pageReq'] as $rqType => $rqVal)
				if(preg_match('~'. str_replace(array(($rqType == 'spage' ? 'p:' : ($rqType == 'cat' ? 'c:' : 'a:')),'*','?'), array('','.*','.?'), trim($pgn)) .'~i', $_GET[$rqType], $match) != 0 && $match[0] == $rqVal)
					$allsides = array_diff($allsides, array('front'));
		}
	}

	foreach($allsides as $side)
	{
		$hidepanels = isset($context['pmx']['settings'][$side .'_panel']['hide']) ? $context['pmx']['settings'][$side .'_panel']['hide'] : array();
		$customhide = isset($context['pmx']['settings'][$side .'_panel']['custom_hide']) ? $context['pmx']['settings'][$side .'_panel']['custom_hide'] : '';

		// hide pages panel?
		if($side == 'pages' && !array_key_exists('spage', $context['pmx']['pageReq']))
			$context['pmx']['show_'. $side .'panel'] = false;
		// any hide action?
		elseif(!empty($hidepanels) || !empty($customhide))
		{
			$itemData = array('pmxact' => $hidepanels, 'pmxcust' => $customhide);
			$show = pmx_checkExtOpts(true, $itemData);

			$context['pmx']['show_'. $side .'panel'] = empty($show);
			$context['pmx']['collapse'][$side] = empty($show);
			if(empty($show))
				$activePanels[] = $side;
		}
		else
			$activePanels[] = $side;

		// hide panel on device types?
		if(!empty($context['pmx']['settings'][$side .'_panel']['device']))
		{
			if(!empty($modSettings['pmx_isMobile']) && $context['pmx']['settings'][$side .'_panel']['device'] != '1')
			{
				$activePanels = array_diff($activePanels,	array($side));
				$context['pmx']['show_'. $side .'panel'] = false;
			}
			if(empty($modSettings['pmx_isMobile']) && $context['pmx']['settings'][$side .'_panel']['device'] != '2')
			{
				$activePanels = array_diff($activePanels,	array($side));
				$context['pmx']['show_'. $side .'panel'] = false;
			}
		}
	}

	if(!empty($modSettings['pmx_paneloff']))
	{
		$offPanels = is_array($modSettings['pmx_paneloff']) ? $modSettings['pmx_paneloff'] : explode(',', $modSettings['pmx_paneloff']);
		$activePanels = array_diff($activePanels,	$offPanels);
		foreach($offPanels as $side)
			$context['pmx']['show_'. $side .'panel'] = false;
	}

	// hide frontpage on Maintenance
	if(!empty($maintenance) && empty($user_info['is_admin']))
	{
		$activePanels = array_diff($activePanels,	array('front'));
		$context['pmx']['show_frontpanel'] = false;
	}

	// read the panels from database
	$context['pmx']['pagenames'] = array();
	$context['pmx_blockCSSfiles'] = array();
	$context['pmx']['showhome'] = 0;
	$cachedblocks = array_keys($context['pmx']['cache']['blocks']);
	$result = array();

	$request = $smcFunc['db_query']('', '
		SELECT id, side, pos, active, cache, blocktype, acsgrp, config, content,
			CASE WHEN side = {string:front} THEN 1 WHEN side = {string:pages} THEN 2 ELSE 0 END AS SortFlag
		FROM {db_prefix}portamx_blocks
		WHERE active = 1'. (!empty($modSettings['pmx_blockoff']) ? ' AND NOT id IN({array_int:offblocks})' : '') .' AND (side IN ({array_string:sides})'. (empty($modSettings['pmx_paneloff']) ? ' OR (blocktype IN ({array_string:cachedblocks}) AND cache > 0)' : '') .')
		ORDER BY SortFlag DESC, side ASC, pos ASC',
		array(
			'sides' => ($context['pmx']['settings']['frontpage'] != 'none' ? array_unique(array_merge($activePanels, array('front'))) : array_merge($activePanels, array('none'))),
			'cachedblocks' => $cachedblocks,
			'offblocks' => !empty($modSettings['pmx_blockoff']) ? explode(',', $modSettings['pmx_blockoff']) : array(),
			'front' => 'front',
			'pages' => 'pages'
		)
	);

	while($row = $smcFunc['db_fetch_assoc']($request))
	{
		unset($row['SortFlag']);

		// on bib's call the contructor only
		if($row['side'] == 'bib')
			$result[$row['side']][$row['id']] = createBlockObject($row);

		// call the contructor and init if visible
		elseif(($result[$row['side']][$row['id']] = createBlockObject($row, true)) === null)
			unset($result[$row['side']][$row['id']]);

		// destroy blocks in not active panels
		if(isset($result[$row['side']][$row['id']]) && !in_array($row['side'], $activePanels))
			unset($result[$row['side']][$row['id']]);
	}
	$smcFunc['db_free_result']($request);

	// check category/article request
	if(count(array_intersect(array('art', 'cat'), array_keys($context['pmx']['pageReq']))) > 0)
	{
		$nullobj = null;
		if(array_key_exists('cat', $context['pmx']['pageReq']))
		{
			$row = PortaMx_getCatByID(null, $context['pmx']['pageReq']['cat']);
			if(!empty($row))
			{
				$row['side'] = 'pages';
				$row['blocktype'] = 'category';
			}
			else
				pmx_fatalerror('category', $nullobj);
		}

		elseif(array_key_exists('art', $context['pmx']['pageReq']))
		{
			$request = $smcFunc['db_query']('', '
				SELECT id, name, acsgrp, config
				FROM {db_prefix}portamx_articles
				WHERE name = {string:reqname} AND active > 0 AND approved > 0',
				array('reqname' => $context['pmx']['pageReq']['art'])
			);
			if($smcFunc['db_num_rows']($request) > 0)
			{
				$row = $smcFunc['db_fetch_assoc']($request);
				$row['side'] = 'pages';
				$row['blocktype'] = 'article';
			}
			$smcFunc['db_free_result']($request);

			if(empty($row))
				pmx_fatalerror('article', $nullobj);
		}

		if(($result[$row['side']][$row['id']] = createBlockObject($row, true)) === null)
		{
			unset($result[$row['side']][$row['id']]);
			pmx_fatalerror($row['blocktype'], $result);
		}
		else
			$context['pmx']['show_pagespanel'] = true;
	}

	// switch off empty panels
	$ecloff = (isset($_REQUEST['pmxerror']) && $_REQUEST['pmxerror'] == 'pmx_eclauth');
	foreach($activePanels as $side)
	{
		$context['pmx']['show_'. $side .'panel'] = ($ecloff ? false : (isset($result[$side]) && !empty($result[$side])));
		if(empty($context['pmx']['show_'. $side .'panel']))
			$context['pmx']['collapse'][$side] =  false;
	}

	return $result;
}

/**
* get a categorie by id or name
* the categorie (with childs) or false is returned
*/
function PortaMx_getCatByID($cats, $id)
{
	if(is_null($cats))
		$cats = PortaMx_getCategories();

	$fnd = null;
	if(is_array($cats))
	{
		reset($cats);
		while((list($ofs, $cat) = PMX_Each($cats)) && empty($fnd))
		{
			if((is_numeric($id) && $cat['id'] == $id) || (is_string($id) && $cat['name'] == $id))
				$fnd = $cat;

			elseif(isset($cat['childs']) && is_array($cat['childs']))
				$fnd = PortaMx_getCatByID($cat['childs'], $id);
		}
	}
	return $fnd;
}

/**
* get a categorie by catorder
* the categorie (with childs) or false is returned
*/
function PortaMx_getCatByOrder($cats, $order, $dept = 0)
{
	reset($cats);
	do
	{
		@list($d, $cat) = PMX_Each($cats);
		if(isset($cat['childs']) && is_array($cat['childs']) && $cat['catorder'] != $order)
		{
			$cat = PortaMx_getCatByOrder($cat['childs'], $order, $dept +1);
			$cat = !is_array($cat) ? array('catorder' => 0) : $cat;
		}
	} while(is_array($cat) && $cat['catorder'] != $order);
	return $cat;
}

/**
* get next categorie by catorder
*/
function PortaMx_getNextCat($order)
{
	global $context;

	$maxorder = $context['pmx']['catorder'][count($context['pmx']['catorder']) -1] +1;
	$key = array_search($order, $context['pmx']['catorder']);
	return ($key === false ? $maxorder : (isset($context['pmx']['catorder'][$key +1]) ? $context['pmx']['catorder'][$key +1] : $maxorder));
}

/**
* get previose categorie by catorder
*/
function PortaMx_getPrevCat($order)
{
	global $context;

	$key = array_search($order, $context['pmx']['catorder']);
	return ($key === false ? -1 : (isset($context['pmx']['catorder'][$key -1]) ? $context['pmx']['catorder'][$key -1] : -1));
}

/**
* get all categories and sort them by catorder
*/
function find_cat_insert_pos(&$cats, $cat, $id)
{
	$fnd = false;
	reset($cats);

	while((list($ofs, $data) = PMX_Each($cats)) && empty($fnd))
	{
		if($data['id'] == $id)
		{
			$cats[$ofs]['childs'][] = &$cat;
			$fnd = true;
		}
		elseif(isset($data['childs']) && is_array($data['childs']))
			$fnd = find_cat_insert_pos($cats[$ofs]['childs'], $cat, $id);
	}
	return $fnd;
}

function PortaMx_getCategories($getart = false)
{
	global $context, $smcFunc;

	$context['pmx']['catorder'] = array();
	$result = array();
	$articles = array();

	if(!empty($getart))
	{
		$request = $smcFunc['db_query']('', '
			SELECT id, name, catid, owner, active, created, updated, approved
			FROM {db_prefix}portamx_articles
			ORDER BY catid',
			array()
		);
		while($row = $smcFunc['db_fetch_assoc']($request))
			$articles[$row['catid']][] = $row;

		$smcFunc['db_free_result']($request);
	}

	$request = $smcFunc['db_query']('', '
			SELECT a.id, a.name, a.parent, a.level, a.catorder, a.acsgrp, a.artsort, a.config, COUNT(c.id) AS artsum
			FROM {db_prefix}portamx_categories AS a
			LEFT JOIN {db_prefix}portamx_articles AS c ON(c.catid = a.id)
			GROUP by a.id
			ORDER BY catorder',
		array(
		)
	);
	while($row = $smcFunc['db_fetch_assoc']($request))
	{
		$cat = array(
			'id' => $row['id'],
			'name' => $row['name'],
			'parent' => $row['parent'],
			'level' => $row['level'],
			'catorder' => $row['catorder'],
			'acsgrp' => $row['acsgrp'],
			'artsort' => $row['artsort'],
			'config' => $row['config'],
			'artsum' => $row['artsum'],
			'childs' => null,
		);

		if(!empty($getart) && array_key_exists($row['id'], $articles))
			$cat['articles'] = PortaMx_ArticleSort($articles[$row['id']], $row['artsort']);

		$context['pmx']['catorder'][] = $row['catorder'];
		if($cat['parent'] != 0)
			find_cat_insert_pos($result, $cat, $cat['parent']);
		elseif(find_cat_insert_pos($result, $cat, 0) == false)
			$result[] = $cat;
	}
	$smcFunc['db_free_result']($request);

	return $result;
}

/**
* get short article data in one or more categories
*/
function PortaMx_getArticles($cats, $intcats = false)
{
	global $context, $scripturl, $smcFunc;

	$result = array();
	$request = $smcFunc['db_query']('', '
			SELECT c.id AS catid, c.name AS catname, c.acsgrp AS catacs, c.artsort, c.config as catcfg, CASE WHEN m.real_name = {string:empty} THEN m.member_name ELSE m.real_name END AS owner_name,
				a.id AS artid, a.name AS artname, a.acsgrp AS artacs, a.owner, a.config AS artcfg, a.active, a.created, a.approved, a.updated
			FROM {db_prefix}portamx_categories AS c
			LEFT JOIN {db_prefix}portamx_articles AS a ON (c.id = a.catid)
			LEFT JOIN {db_prefix}members AS m ON (a.owner = m.id_member)
			WHERE '. (empty($intcats) ? 'c.name IN ({array_string:cats})' : 'c.id IN ({array_int:cats})') .' AND a.active > 0 AND a.approved  > 0
			ORDER BY c.catorder',
		array(
			'cats' => empty($intcats) ? Pmx_StrToArray($cats) : Pmx_StrToIntArray($cats),
			'empty' => '',
		)
	);
	while($row = $smcFunc['db_fetch_assoc']($request))
	{
		if(!isset($result[$row['catid']]) && allowPmxGroup($row['catacs']))
		{
			$cfg = unserialize($row['catcfg']);
			if(empty($cfg['request']) || (!empty($cfg['request']) && allowPmx('pmx_admin')))
			{
				$title = PortaMx_getTitle($cfg);
				if(empty($title))
					$title = htmlspecialchars($row['catname'], ENT_QUOTES);

				$result[$row['catid']] = array(
					'name' => $row['catname'],
					'title' => $title,
					'acsgrp' => $row['catacs'],
					'acsinherit' => $cfg['settings']['inherit_acs'],
					'artsort' => $row['artsort'],
					'link' => '<a href="'. $scripturl .'?cat='. $row['catname'] .'">'. $title .'</a>',
					'href' => $scripturl .'?cat='. $row['catname'],
					'articles' => array(),
				);
			}
		}

		if(isset($result[$row['catid']]) && (!empty($result[$row['catid']]['acsinherit']) || allowPmxGroup($row['artacs'])))
		{
			$title = PortaMx_getTitle($row['artcfg']);
			if(empty($title))
				$title = htmlspecialchars($row['artname'], ENT_QUOTES);

			$result[$row['catid']]['articles'][] = array(
				'id' => $row['artid'],
				'name' => $row['artname'],
				'title' => $title,
				'acsgrp' => $row['artacs'],
				'active' => $row['active'],
				'created' => $row['created'],
				'approved' => $row['approved'],
				'updated' => $row['updated'],
				'owner' => $row['owner'],
				'time_created' => timeformat($row['created'], false),
				'link' => '<a href="'. $scripturl .'?art='. $row['artname'] .'">'. $title .'</a>',
				'href' => $scripturl .'?art='. $row['artname'],
				'member' => array(
					'member_id' => $row['owner'],
					'member_name' => $row['owner_name'],
					'link' => '<a href="'. $scripturl .'?action=profile;u='. $row['owner'] .'">'. $row['owner_name'] .'</a>',
					'href' => $scripturl .'?action=profile;u='. $row['owner'],
				),
			);
		}
	}
	$smcFunc['db_free_result']($request);

	// sort the articles
	foreach($result as $cid => $data)
		$result[$cid]['articles'] = PortaMx_ArticleSort($data['articles'], $data['artsort']);

	return $result;
}

/**
* get the language depended titel.
*/
function PortaMx_getTitle($cfg)
{
	global $context, $language;

	$title = '';
	$cfg = !is_array($cfg) ? unserialize($cfg) : $cfg;

	if(!empty($cfg['title'][$context['pmx']['currlang']]))
		$title = htmlspecialchars($cfg['title'][$context['pmx']['currlang']], ENT_QUOTES);
	elseif(!empty($cfg['title'][$language]))
		$title = htmlspecialchars($cfg['title'][$language], ENT_QUOTES);

	return $title;
}

/**
* get all smf languages.
*/
function PortaMx_getLanguages()
{
	global $context, $settings;

	// check SMF languages
	$lang_dir = $settings['default_theme_dir'] . '/languages';
	$dir = dir($lang_dir);
	while ($entry = $dir->read())
	{
		preg_match('~^Admin\.?([a-zA-Z0-9\_\-]+)\.php~', $entry, $match);
		if(!empty($match))
			$result[$match[1]] = false;
	}
	$dir->close();

	// check PortaMx languages
	$lang_dir = $settings['default_theme_dir'] . '/languages/PortaMx';
	$dir = dir($lang_dir);
	while ($entry = $dir->read())
	{
		preg_match('~^PortaMx\.?([a-zA-Z0-9\_\-]+)\.php~', $entry, $match);
		if(!empty($match) && !array_key_exists($match[1], $result))
			$result[$match[1]] = false;
	}
	$dir->close();

	return $result;
}

/**
* Get the installed languages
*/
function getInstalledLanguages($setID = '')
{
global $smcFunc, $context;

	$result = array();
	$request = $smcFunc['db_query']('', '
			SELECT varname, config
			FROM {db_prefix}portamx_settings
			WHERE varname LIKE {string:like}
			ORDER BY varname',
		array(
			'like' => 'lang%',
		)
	);
	while($row = $smcFunc['db_fetch_assoc']($request))
		$result[$row['varname']] = unserialize($row['config']);

	$smcFunc['db_free_result']($request);

	if(!empty($setID))
		return $result[$setID];
	else
		return $result;
}

/**
* get all smf themes.
* idonly = false: Id's and names.
* idonly = true: only Id's.
*/
function PortaMx_getsmfThemes($idonly = false)
{
	global $context, $smcFunc;

	$result = null;

	// get the theme defaults
	$request = $smcFunc['db_query']('', '
			SELECT variable, value
			FROM {db_prefix}settings
			WHERE variable IN({array_string:var})',
		array('var' => array('theme_default', 'knownThemes'))
	);
	while($row = $smcFunc['db_fetch_assoc']($request))
		$themedef[$row['variable']] = $row['value'];
	$smcFunc['db_free_result']($request);

	$smfenabled = Pmx_StrToArray($themedef['knownThemes']);
	if($idonly)
		return $smfenabled;

	// get all themes
	$request = $smcFunc['db_query']('', '
			SELECT m.id_theme as mem_theme, t.id_theme, t.variable, t.value
			FROM {db_prefix}themes as t
			LEFT JOIN {db_prefix}members as m on({int:mem} = m.id_member)
			WHERE variable in({array_string:val})
			ORDER BY value, id_theme',
		array(
			'val' => array('name', 'images_url'),
			'mem' => $context['user']['id']
		)
	);
	while($row = $smcFunc['db_fetch_assoc']($request))
	{
		if(is_null($row['mem_theme']))
			$row['mem_theme'] = $themedef['theme_default'];
		$result[$row['id_theme']][$row['variable']] = $row['value'];
		if($row['variable'] == 'name')
		{
			$result[$row['id_theme']]['isdefault'] = ($row['id_theme'] == $themedef['theme_default']);
			$result[$row['id_theme']]['usertheme'] = ($row['mem_theme'] == $row['id_theme']);
			$result[$row['id_theme']]['smfenabled'] = (in_array($row['id_theme'], $smfenabled));
		}
	}
	$smcFunc['db_free_result']($request);

	return $result;
}

/**
* show the blocks for a side.
*/
function PortaMx_ShowBlocks($side, $spacer = 0, $placement = '')
{
	global $context, $txt, $scripturl;

	$placed = 0;
	$pages = array();
	$count = isset($context['pmx']['viewblocks'][$side]) ? count($context['pmx']['viewblocks'][$side]) : 0;
	if($count > 0)
	{
		$count += $spacer;
		foreach($context['pmx']['viewblocks'][$side] as $ObjHdl)
		{
			$count--;
			$placed += $ObjHdl->pmxc_ShowBlock($count, $placement);
			if($ObjHdl->cfg['side'] == 'pages' && array_key_exists('spage', $context['pmx']['pageReq']))
				$pages[] = $ObjHdl->cfg['config']['pagename'];
		}
		if($side == 'pages' && array_key_exists('spage', $context['pmx']['pageReq']) && !in_array($_GET['spage'], $pages))
			pmx_fatalerror('page', $context['pmx']['viewblocks']);
	}
	else
	{
		if($side == 'pages' && array_key_exists('spage', $context['pmx']['pageReq']))
			pmx_fatalerror('page', $context['pmx']['viewblocks']);
	}
	return $placed;
}

/**
* check the visibility access by extend options,
* (action, custom action, board, topic, themes and language)
*/
function pmx_checkExtOpts($show, $itemData, $blockpagename = '')
{
	global $context, $settings;

	// find items they have values
	$allitems = array('pmxact', 'pmxcust', 'pmxbrd', 'pmxthm', 'pmxlng');
	$checkitems = array();
	foreach($allitems as $item)
	{
		if(isset($itemData[$item]) && !empty($itemData[$item]))
			$checkitems[] = $item;
	}

	// nothing to do?
	if(empty($checkitems))
		return $show;

	// check exist items
	$bits = pmx_setBits(null);
	foreach($checkitems as $item)
	{
		// convert elements for simpler checking
		if($item != 'pmxcust')
			$data = pmx_decodeOptions($itemData[$item]);

		switch($item)
		{
			// actions...
			case 'pmxact':
				// frontpage ?
				if(array_key_exists('frontpage', $data) && empty($context['pmx']['forumReq']) && empty($context['pmx']['pageReq']))
					$bits['front'] = $data['frontpage'];

				// Pages ?
				elseif(array_key_exists('pages', $data) && array_key_exists('spage', $context['pmx']['pageReq']))
					$bits['spage'] = $data['pages'];

				// Articles ?
				elseif(array_key_exists('articles', $data) && array_key_exists('art', $context['pmx']['pageReq']))
					$bits['art'] = $data['articles'];

				// Categories ?
				elseif(array_key_exists('categories', $data) && array_key_exists('cat', $context['pmx']['pageReq']))
					$bits['cat'] = $data['categories'];

				// global on topics?
				elseif(array_key_exists('topics', $data) && !empty($context['current_topic']))
					$bits['topic'] = $data['topics'];

				// global on boards?
				elseif(array_key_exists('boards', $data) && !empty($context['current_board']))
					$bits['board'] = $data['boards'];

				// action ?
				elseif(isset($_GET['action']) && array_key_exists($_GET['action'], $data))
					$bits['action'] = $data[$_GET['action']];

				// action && any option set?
				elseif(isset($_GET['action']) && !array_key_exists($_GET['action'], $data) && array_key_exists('any', $data))
					$bits['action'] = $data['any'];

				// other && any option set?
				elseif((!empty($context['pmx']['pageReq']) || !empty($context['current_topic']) || !empty($context['current_board'])) && array_key_exists('any', $data))
					$bits['action'] = $data['any'];

			break;

			// custom actions...
			case 'pmxcust':
				// page, category, article request?
				if(!empty($context['pmx']['pageReq']))
				{
					foreach(array('spage', 'cat', 'child', 'art') as $tok)
					{
						$bits[$tok] = (is_null($bits[$tok]) ? 0 : $bits[$tok]);

						if($tok == 'spage' && isset($context['pmx']['pageReq']['spage']) && $context['pmx']['pageReq']['spage'] == $blockpagename)
							$bits['spage'] = 1;
						elseif(array_key_exists($tok, $context['pmx']['pageReq']))
							pmx_checkCustActions($bits, $itemData[$item], $tok);
					}
				}

				// any other request?
				elseif(isset($_GET))
					pmx_checkCustActions($bits, $itemData[$item], 'action');

			break;

			// specific board ?
			case 'pmxbrd':
				if(!empty($context['current_board']))
				{
					if(array_key_exists($context['current_board'], $data))
					{
						if(empty($data[$context['current_board']]) || (!empty($context['current_topic']) && $bits['topic'] === '0'))
							$bits = pmx_setBits(0);
						else
							$bits['board'] = $data[$context['current_board']];
					}
					// any action ?
					else
					{
						if(array_key_exists('any', $data))
							$bits['board'] = $data['any'];
						elseif(!empty($bits['topic']))
							$bits = pmx_setBits(0);
					}
				}
			break;

			// theme ?
			case 'pmxthm':
				if(isset($settings['theme_id']))
				{
					$state = pmx_getBits($bits);
					if(is_null($state) || !empty($state))
					{
						if(array_key_exists($settings['theme_id'], $data))
						{
							$bits['theme'] = $data[$settings['theme_id']];
							if(empty($bits['theme']))
								$bits = pmx_setBits(0);
						}
						elseif(array_key_exists('any', $data))
							$bits['theme'] = $data['any'];
						else
							$bits = pmx_setBits(0);
					}
				}
			break;

			// language ?
			case 'pmxlng':
				if(isset($context['user']['language']))
				{
					$state = pmx_getBits($bits);
					if(is_null($state) || !empty($state))
					{
						if(array_key_exists($context['user']['language'], $data))
						{
							$bits['lang'] = $data[$context['user']['language']];
							if(empty($bits['lang']))
								$bits = pmx_setBits(0);
						}
						elseif(array_key_exists('any', $data))
							$bits['lang'] = $data['any'];
						else
							$bits = pmx_setBits(0);
					}
				}
			break;
		}
	}

	// support for our SubForums modification
	if(!empty($itemData['pmxcust']) && function_exists('Subforums_checkhost'))
		Subforums_checkhost($itemData['pmxcust'], $bits);

	return (int) (intval(implode('', $bits)) > 0);
}

/**
* check customer actions & subactions
*/
function pmx_checkCustActions(&$bits, $item, $actname)
{
	global $context;

	foreach($context['pmx']['ca_find'] as $key => $regex)
	{
		preg_match_all($regex, $item, $fndall, PREG_SET_ORDER);
		foreach($fndall as $fnd)
			$item = !empty($fnd) ? str_replace($fnd[0], eval($context['pmx']['ca_repl'][$key]), $item) : $item;
	}

	preg_match_all($context['pmx']['ca_grep'], $item, $actions);
	$key = $context['pmx']['ca_keys'][$actname];
	$keyCtl = 1; $keyPos = 2; $actCtl = 3; $actPos = 4;

	// process the current action
	$indexes = array_keys(array_values($actions[$keyPos]), $key);
	if(count($indexes) > 0)
	{
		$fnd = false;
		$autoAny = true;

		// loop through all entrys until we found one
		while((list($idxPos, $aix) = PMX_Each($indexes)) && empty($fnd))
		{
			$hideAct = strpos($actions[$keyCtl][$indexes[$idxPos]], '^') === false;

			// work on all entries..
			do
			{
				// check action or subaction
				if(strpos($actions[$actCtl][$aix], '&') === false)
				{
					// action..
					if(pmx_checkActions($actions[$actPos][$aix], $actname, false))
					{
						// ..found
						$fnd = true;

						// have a subaction?
						if(isset($actions[$actCtl][$aix+1]) && strpos($actions[$actCtl][$aix+1], '&') !== false)
						{
							// subaction ..
							$aix++;
							$hideSubAct = strpos($actions[$actCtl][$aix], '^') === false;
							$subact = pmx_checkActions($actions[$actPos][$aix], $actname);
							if(!empty($subact))
								$bits[$actname] = intval($hideAct && $hideSubAct);
							else
							{
								$bits[$actname] = intval($hideAct && !$hideSubAct);
								$autoAny = false;
							}
						}

						// no subaction given
						else
						{
							$bits[$actname] = intval($hideAct);
							$autoAny = false;
						}
					}

					// action not found .. check subaction
					else
					{
						if(isset($actions[$actCtl][$aix+1]) && strpos($actions[$actCtl][$aix+1], '&') !== false)
						{
							// get the subaction
							$aix++;
							$hideSubAct = strpos($actions[$actCtl][$aix], '^') === false;
							$autoAny = false;
							$subfnd = pmx_checkActions($actions[$actPos][$aix], $actname);
							if(!empty($subfnd))
								$bits[$actname] = intval(is_null($bits[$actname]) ? (!$hideAct && $hideSubAct) : ($bits[$actname] && intval(!$hideAct && $hideSubAct)));
							elseif(isset($_GET[$actname]))
								$bits[$actname] = intval(is_null($bits[$actname]) ? (!$hideAct && !$hideSubAct) : ($bits[$actname] && intval(!$hideAct && !$hideSubAct)));
						}
						// nothing .. check request
						elseif(isset($_GET[$actname]))
							$bits[$actname] = intval(is_null($bits[$actname]) ? !$hideAct : ($bits[$actname] == 2 ? $bits[$actname] && intval(!$hideAct) : $bits[$actname]));
					}
				}

				// only subaction
				elseif(isset($_GET[$actname]))
				{
					$autoAny = false;
					$hideSubAct = strpos($actions[$actCtl][$aix], '^') === false;
					$fnd = pmx_checkActions($actions[$actPos][$aix], $actname);
					$bits[$actname] = intval($bits[$actname] && intval(!empty($fnd) ? $hideSubAct : ($bits[$actname] != 2 ? intval(!$hideSubAct) : $bits[$actname] && intval(!$hideSubAct))));
				}

				// next action if nothing found..
				$aix++;
			} while(empty($fnd) && isset($actions[$keyPos][$aix]) && empty($actions[$keyPos][$aix]));
		}

		// nothing found, we have a ANY action?
		if(empty($fnd) && !empty($autoAny) && count(array_keys($actions[1], '^')) != 0)
		{
			$any = count(array_intersect($indexes, array_keys($actions[1], '^')) == 0);
			$bits[$actname] = isset($_GET[$actname]) && $any == count($actions[$keyCtl]) ? 2 : $bits[$actname];
		}

		// action/subaction found and set to 0 ?
		elseif(!empty($fnd) && !is_null($bits[$actname]) && empty($bits[$actname]))
			$bits = pmx_setBits(0);
	}
}

/**
* check actions
*/
function pmx_checkActions($actionlist, $actname, $check = true)
{
	$fnd = null;
	$getacts = array_diff(array_keys($_GET), array($actname));
	if(!empty($getacts) || empty($check))
	{
		$actions = explode(';', $actionlist);
		while(empty($fnd) && (@list($d, $action) = PMX_Each($actions)) && !empty($action))
		{
			@list($act, $val) = explode('=', (strpos($action, '=') === false ? $actname .'=' : '') . $action);
			$val = str_replace(array('*','?'), array('.*','.?'), $val);
			if(isset($_GET[$act]) && preg_match('~'. $val .'~i', $_GET[$act], $match) != 0 && $match[0] == $_GET[$act])
				$fnd = is_null($fnd) ? true : $fnd && true;
			else
				$fnd = false;
		}
	}
	return is_null($fnd) ? false : $fnd;
}

/**
* prepare the extend option bits.
*/
function pmx_setBits($val)
{
	return array('front' => $val, 'spage' => $val, 'art' => $val, 'cat' => $val, 'child' => $val, 'action' => $val, 'board' => $val, 'topic' => $val, 'theme' => $val, 'lang' => $val);
}

/**
* check the extend option bits.
*/
function pmx_getBits($bits, $strip = array())
{
	$result = null;
	foreach($bits as $key => $val)
	{
		if(!in_array($key, $strip))
			$result = (!is_null($val) ? $result .= $val : $result);
	}
	return (is_null($result) ? null : intval(implode('', $bits)) > 0);
}

/**
* convert Extend Option for faster check
* for actions, boards, themes, languages
*/
function pmx_decodeOptions($item)
{
	global $__dat;
	array_walk($item, create_function('$val, $key, $__dat', 'global $__dat; $temp = explode("=", $val); $multi = explode(",", $temp[0]); foreach($multi as $ky) $__dat[$ky] = $temp[1];'), $__dat = array());

	// if all data negated add the any action
	if(($tmp = array_count_values(array_diff_assoc($__dat, array('frontpage' => 0, 'frontpage' => 1)))) && count($tmp) == 1 & key($tmp) == 0)
		$__dat['any'] = 2;

	return $__dat;
}

/**
* remove html, the $ and array[element].
*/
function PortaMx_makeSafe($value)
{
	return preg_replace('~(\[\/?)(.+)([^\]]*\])|\$~', '', preg_replace('~(<\/?)(.+)([^>]*>)~', '', $value));
}

/**
* remove all unnecessary <br> and lf/cr from content.
*/
function PortaMx_makeSafeContent($value, $type = '')
{
	global $context;

	// remove <br> and lf/cr from end
	$value = rtrim($value);
	if(in_array($type, array('html','script', 'code')))
	{
		$res = true;
		while($res)
		{
			$last = substr($value, -6);
			$res = preg_match('~<br[^>]*>~i', $last, $found);
			if(!empty($res))
				$value = rtrim(substr($value, 0, -(strlen($found[0])+1)));
			else
			{
				$res = preg_match('~\&nbsp\;~i', $last, $found);
				if(!empty($res))
					$value = rtrim(substr($value, 0, -strlen($found[0])));
			}
		}
	}
	return $value;
}

/**
* get the PortaMx smileys set.
*/
function PortaMx_getSmileySet()
{
	$set['files'] = array('laugh.gif', 'smiley.gif', 'wink.gif', 'cheesy.gif', 'grin.gif', 'angry.gif', 'sad.gif', 'shocked.gif', 'cool.gif', 'huh.gif', 'rolleyes.gif', 'tongue.gif', 'embarrassed.gif', 'lipsrsealed.gif', 'undecided.gif', 'kiss.gif', 'cry.gif', 'evil.gif', 'azn.gif', 'afro.gif');
	$set['symbols'] = array(':))', ':)', ';)', ':D', ';D', '>:(', ':(', ':o', '8)', '???', '::)', ':P', ':-[', ':-X', ':-\\', ':-*', ':\'(', '>:D','^-^', 'O0');
	return $set;
}

/**
* convert smileys (PortaMx set)
**/
function PortaMx_BBCsmileys($content)
{
	global $modSettings, $context, $pmxCacheFunc, $smileyPregReplace;

	if(!empty($content))
	{
		// convert special tags
		$html = array(
			'~\[bbcdiv([^\]]*)\]~i' => '<div$1>',
			'~\[/bbcdiv\]~i' => '</div>',
			'~\[bbcspan([^\]]*)\]~i' => '<span$1>',
			'~\[/bbcspan\]~i' => '</span>',
			'~\[bbcp([^\]]*)\]~i' => '<p$1>',
			'~\[/bbcp\]~i' => '</p>',
			'~\[bbcimg([^\]]*)\]~i' => '<img$1>',
			'~\[bbcbr]~i' => '<br />',
		);
		$content = preg_replace(array_keys($html), array_values($html), $content);

		// smileys cached ?
		if(($data = $pmxCacheFunc['get']('smileys', false)) !== null)
		{
			$smileyPregSearch = $data['search'];
			$smileyPregReplace = $data['replace'];
		}
		else
		{
			$smileyfrom = array(':))', ':)', ';)', ':D', ';D', '>:(', ':(', ':o', '8)', '???', '::)', ':P', ':-[', ':-X', ':-\\', ':-*', ':\'(', '>:D','^-^', 'O0');
			$smileyto = array('laugh.gif', 'smiley.gif', 'wink.gif', 'cheesy.gif', 'grin.gif', 'angry.gif', 'sad.gif', 'shocked.gif', 'cool.gif', 'huh.gif', 'rolleyes.gif', 'tongue.gif', 'embarrassed.gif', 'lipsrsealed.gif', 'undecided.gif', 'kiss.gif', 'cry.gif', 'evil.gif', 'azn.gif', 'afro.gif');

			$smileyPregReplace = array();
			$searchParts = array();
			for ($i = 0, $n = count($smileyfrom); $i < $n; $i++)
			{
				$smileyCode = '<img src="' . $modSettings['smileys_url'] . '/PortaMx/' . $smileyto[$i] . '" alt="*" title="" class="smiley" />';
				$smileyPregReplace[$smileyfrom[$i]] = $smileyCode;
				$searchParts[] = preg_quote($smileyfrom[$i], '>');
			}
			$smileyPregSearch = '/(' . implode('|', $searchParts) . ')(\s|\<|$)+/';

			// put to cache
			$data['search'] = $smileyPregSearch;
			$data['replace'] = $smileyPregReplace;
			$pmxCacheFunc['put']('smileys', $data, 86400, false);
		}

		// convert smileys
		return preg_replace_callback($smileyPregSearch, create_function('$matches', 'global $smileyPregReplace; return isset($smileyPregReplace[$matches[1]]) ? $smileyPregReplace[$matches[1]] . (isset($matches[2]) ? $matches[2] : "") : "";'), $content);
	}
	else
		return $content;
}

/**
* Sort articles by sortmodes
* $sortData: sortstring
* $articles: array(articledata)
**/
function PortaMx_ArticleSort($articles, $sortData)
{
	$cmpStr = '';
	$sorts = Pmx_StrToArray($sortData);
	foreach($sorts as $sort)
	{
		@list($sKey, $sDir) = Pmx_StrToArray($sort, '=');
		$cmpStr .= (empty($cmpStr) ? 'return ' : ' xor ') .'($articles[$s1][\''. $sKey .'\'] '. (empty($sDir) ? '<' : '>') .' $articles[$s2][\''. $sKey .'\'])';
	}

	$cmpStr .= ';';
	for($s1 = 0; $s1 < sizeof($articles); $s1++)
	{
		for($s2 = $s1 + 1; $s2 < sizeof($articles); $s2++)
		{
			if(eval($cmpStr) == true)
			{
				$tmp = $articles[$s1];
				$articles[$s1] = $articles[$s2];
				$articles[$s2] = $tmp;
			}
		}
	}
	return $articles;
}

/**
* remove Links from content.
* unlinkimg = false: images not unlink.
* unlinkimg = true: unlink images.
* unlinkhref = false: a href not unlink.
* unlinkhref = true: unlink a href.
*/
function PortaMx_revoveLinks($content, $unlinkhref = false, $unlinkimg = false)
{
	global $modSettings, $txt;

	// remove links
	if($unlinkhref)
	{
		$found = preg_match_all("!<a[^>]*>(.+?)</a>!iS", $content, $matches, PREG_SET_ORDER);
		foreach($matches as $i => $data)
		{
			if(strpos(strtolower($data[1]), '<img') === false)
				$content = str_replace($data[0], $data[1], $content);
		}
	}

	// remove inside images
	if($unlinkimg)
	{
		$found = preg_match_all('!<img[^>]*>!iS', $content, $matches);
		if(!empty($found))
		{
			foreach($matches[0] as $data)
			{
				if(strpos($data, $modSettings['smileys_url']) === false)
					$content = str_replace($data, '', $content);
			}
		}
		// remove embedded objects
		$found = preg_match_all('!<object[^>]*>.*<\/object[^>]*>!iS', $content, $matches);
		if(!empty($found))
		{
			foreach($matches[0] as $data)
				$content = str_replace($data, '', $content);
		}
	}

	return $content;
}

/**
* Post teaser (shorten posts by given word/character count).
* $remtags = true: remove Links from content.
* $remimgs = true: remove images.
* $morelink: if give, it's added at the end of the content
*/
function PortaMx_Tease_posts($content, $wordcount, $morelink = '', $remtags = false, $remimgs = false)
{
	global $context, $txt, $smcFunc, $pmx_transchr;

	// remove images/links
	if($remtags || $remimgs)
		$content = PortaMx_revoveLinks($content, $remtags, $remimgs);

	// setup Post teaser mode
	$PmxTeaseCount = (empty($context['pmx']['settings']['teasermode']) ? 'pmx_teasecountwords' : 'pmx_teasecountchars');
	$PmxTeaseShorten = (empty($context['pmx']['settings']['teasermode']) ? 'pmx_teasegetwords' : 'pmx_teasegetchars');
	$pmx_transchr = array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES)) + array('&#039;' => '\'', '&nbsp;' => ' ', '&#160;' => ' ');
	$teaseMode = intval(!empty($context['pmx']['settings']['teasermode']));
	$content = str_replace(array("\n", "\r"), '', $content);
	$contentlen = $PmxTeaseCount($content);
	$teased = false;
	$wordcount = ($wordcount == -1 ? pmx_teasecountchars($content) : $wordcount);

	// we have a html teaser?
	if(preg_match('/(<span|<div)\s+style=\"page-break-after\:/is', $content, $match) > 0)
	{
		$pgbrk = $smcFunc['strpos']($content, $match[0]);
		$content = pmx_teasegetchars($smcFunc['substr']($content, 0, $pgbrk), $pgbrk);
		$tags = pmx_tease_gettags($content);
		$words = preg_split('/ /', $content);
		$words[count($words)-1] = preg_replace('/<(br|p|div|span|ol|ul|li|table|tr|td)[^>]*>/', '', $words[count($words)-1]);
		$content = pmx_tease_settags(implode(' ', $words), $tags);
		$context['pmx']['is_teased'] = $PmxTeaseCount($content);
		$teased = true;
	}
	else
	{
		if(($contentlen = $PmxTeaseCount($content)) > $wordcount)
		{
			$content = $PmxTeaseShorten($content, $wordcount);
			$teased = true;
		}
	}

	if(!empty($teased))
	{
		// close open tags
		$content = pmx_tease_closetags($content);

		// get the teased length
		$context['pmx']['is_teased'] = $PmxTeaseCount($content);

		// insert teaser mark [...]
		$content .= '<span class="pmx_tease"'. sprintf($txt['pmx_teaserinfo'][$teaseMode], $context['pmx']['is_teased'], $contentlen) .'> [...]</span>';

		if(!empty($morelink))
			$content .= $morelink;
	}
	else
		$context['pmx']['is_teased'] = 0;

	return $content;
}

/**
* get word count for post_teaser.
*/
function pmx_teasecountwords($text)
{
	global $pmx_transchr;
	$text = preg_replace_callback('/(\S)(<br[^>]*>)(\S)/', create_function('$matches', 'return $matches[1] . $matches[2] ." ". $matches[3];'), $text);
	$text = preg_replace_callback('/([a-zA-Z0-9])(\/)(\S)/', create_function('$matches', 'return $matches[1] ." ". $matches[2] . $matches[3];'), $text);
	return count(preg_split('/ /', preg_replace('/<[^>]*>/', '', $text)));
}

/**
* get charater cont for post_teaser.
*/
function pmx_teasecountchars($text)
{
	global $smcFunc, $pmx_transchr;

	return $smcFunc['strlen'](strtr(preg_replace('/<[^>]*>/', '', $text), $pmx_transchr));
}

/**
* get a shorten wordcount string for post_teaser.
*/
function pmx_teasegetwords($text, $wordcount)
{
	global $pmx_transchr;
	$text = preg_replace_callback('/(\S)(<br[^>]*>)(\S)/', create_function('$matches', 'return $matches[1] . $matches[2] ." ". $matches[3];'), $text);
	$tags = pmx_tease_gettags($text);
	$text = preg_replace_callback('/([a-zA-Z0-9])(\/)(\S)/', create_function('$matches', 'return $matches[1] ." ". $matches[2] . $matches[3];'), $text);
	$words = preg_split('/ /', preg_replace('/<[^>]*>/', '', $text));
	$addwords = 0;
	foreach($words as $i => $word)
	{
		$addwords += intval(trim(strtr($word, $pmx_transchr)) == '');
		if($i == $wordcount)
			break;
	}
	$wordcount += $addwords;
	$words = preg_split('/ /', $text, $wordcount+1);
	if(isset($words[$wordcount-1]))
		$words[$wordcount-1] = preg_replace('/<[^>]*>/', '', $words[$wordcount-1]);
	unset($words[$wordcount]);

	$text = '';
	foreach($words as $i => $word)
		$text .= isset($words[$i+1]) && !empty($words[$i+1]) && $words[$i+1]{0} == '/' ? $word : $word .' ';
	unset($words);

	$text = pmx_tease_settags(rtrim($text), $tags);
	$text = preg_replace_callback('/(\S)(<br[^>]*>)(" ")/', create_function('$matches', 'return $matches[1] . $matches[2];'), $text);

	return $text;
}

/**
* get a shorten charcount string for post_teaser.
*/
function pmx_teasegetchars($text, $wordcount)
{
	global $smcFunc, $pmx_transchr;

	$tags = pmx_tease_gettags($text);
	if(!empty($tags))
	{
		if(preg_match_all('/<[0-9]+>/', utf8_decode(strtr($text, $pmx_transchr)), $repl, PREG_OFFSET_CAPTURE) > 0)
		{
			foreach($repl[0] as $nt)
				if($nt[1] < $wordcount) $wordcount += strlen($nt[0]); else break;
		}
		$text = pmx_tease_settags($smcFunc['substr']($text, 0, $wordcount), $tags);
	}

	return $text;
}

/**
* get tags in a post_teaser block.
*/
function pmx_tease_gettags(&$text)
{
	preg_match_all('~<[^>]*>~si', $text, $tags);
	foreach($tags[0] as $i => $tag)
		$text = substr_replace($text, '<'. $i .'>', strpos($text, $tag), strlen($tag));

	return $tags[0];
}

/**
* set tags in a post_teaser block.
*/
function pmx_tease_settags($text, $tags)
{
	$text = rtrim($text);
	foreach($tags as $i => $tag)
	{
		$repl = '<'. strval($i) .'>';
		if(strpos($text, $repl) === false)
			break;
		$text = substr_replace($text, $tag, strpos($text, $repl), strlen($repl));
	}

	return $text;
}

/**
* close open tags in a post_teaser block.
*/
function pmx_tease_closetags($text)
{
	preg_match_all('~<(\w+)[^>]*>~s', $text, $open);
	preg_match_all('~<\/(\w+)[^>]*>~s', $text, $closed);

	foreach($open[1] as $i => $tag)
	{
		if(substr($open[0][$i], -2, 2) == '/>')
			unset($open[1][$i]);
		elseif(($fnd = array_search($tag, $closed[1])) !== false)
		{
			unset($closed[1][$fnd]);
			unset($open[1][$i]);
		}
	}

	foreach(array_reverse($open[1]) as $element)
		$text .= "</$element>";

	return $text;
}

/**
* change a theme permanent.
*/
function PortaMx_ChangeTheme($themeid, $redirurl)
{
	global $context, $smcFunc, $scripturl, $pmxCacheFunc;

	$thid = PortaMx_makeSafeContent($themeid);
	if(in_array($thid, PortaMx_getsmfThemes(true)) && $context['user']['is_logged'])
	{
		$smcFunc['db_query']('', '
				UPDATE {db_prefix}members
				SET id_theme = {int:idtheme}
				WHERE id_member = {int:idmem}',
			array(
				'idtheme' => $thid,
				'idmem' => $context['user']['id']
			)
		);

		// clear cached blocks
		$request = $smcFunc['db_query']('', '
			SELECT id, blocktype FROM {db_prefix}portamx_blocks WHERE blocktype IN ({array_string:blocktype})',
			array('blocktype' => array('newposts', 'boardnews', 'boardnewsmult', 'promotedposts'))
		);
		while($row = $smcFunc['db_fetch_assoc']($request))
			$pmxCacheFunc['clear']($row['blocktype'] . $row['id'], true);
		$smcFunc['db_free_result']($request);
	}
	redirectexit(base64_decode($redirurl));
}

/**
* Fatal Error redirect
**/
function pmx_fatalerror($redir, &$blockobjects)
{
	if(!empty($blockobjects))
	{
		if(isset($blockobjects['front']))
			unset($blockobjects['front']);
		if(isset($blockobjects['pages']))
			unset($blockobjects['pages']);
	}
	redirectexit('pmxerror='. $redir);
}

/**
* Ajax function php syntax check
*/
function PortaMx_PHPsyntax($data)
{
	global $context, $boarddir;

	$line = '';
	$context['xmlpmx'] .= '<img onclick="Hide_SyntaxCheck(this.parentNode)" style="padding-left:10px;cursor:pointer;" alt="close" src="'. $context['pmx_imageurl'] .'cross.png" class="pmxright" />';

	// call the syntaxCheck
	$result = php_syntax_error($data);

	// setup result
	if(empty($result))
		$line = '<b>No syntax errors detected.</b>';
	else
	{
		$errline = empty($result[1]) ? '1' : $result[1];
		$line = '<b>'. $result[0] .' on line: '. $errline .'</b>';
		$line .= '&nbsp;&nbsp;<img style="vertical-align:-2px;width:14px;height:14px;cursor:pointer;" onclick="php_showerrline(\'@elm@\', '. $errline .')" src="'. $context['pmx_imageurl'] .'opt_nofilter.png" alt="*" title="'. $errline .'" />';
	}
	$context['xmlpmx'] .= $line .'<br />';

	// remove the cookie
	pmx_setcookie('pmx_php_check', '');
}

/**
* php syntax check
*/
function php_syntax_error($code)
{
	$braces = 0;
	$inString = 0;

	// First of all, we need to know if braces are correctly balanced.
	// This is not trivial due to variable interpolation which
	// occurs in heredoc, backticked and double quoted strings
	foreach(token_get_all('<?php ' . $code) as $token)
	{
		if(is_array($token))
		{
			switch ($token[0])
			{
				case T_CURLY_OPEN:
				case T_DOLLAR_OPEN_CURLY_BRACES:
				case T_START_HEREDOC: ++$inString; break;
				case T_END_HEREDOC:   --$inString; break;
			}
		}
		else if($inString & 1)
		{
			switch($token)
			{
				case '`':
				case '"': --$inString; break;
			}
		}
		else
		{
			switch($token)
			{
			case '`':
			case '"': ++$inString; break;
			case '{': ++$braces; break;
			case '}':
				if($inString) --$inString;
				else
				{
					--$braces;
					if($braces < 0) break 2;
				}
				break;
			}
		}
	}

	// Display parse error messages and use output buffering to catch them
	$inString = @ini_set('log_errors', false);
	$token = @ini_set('display_errors', true);
	ob_start();

	// If $braces is not zero, then we are sure that $code is broken.
	// We run it anyway in order to catch the error message and line number.
	// Else, if $braces are correctly balanced, then we can safely put
	// $code in a dead code sandbox to prevent its execution.
	// Note that without this sandbox, a function or class declaration inside
	// $code could throw a "Cannot redeclare" fatal error.
	$braces || $code = "if(0){{$code}\n}";

	if(false === eval($code))
	{
		if($braces) $braces = PHP_INT_MAX;
			else
		{
			// Get the maximum number of lines in $code to fix a border case
			false !== strpos($code, "\r") && $code = strtr(str_replace("\r\n", "\n", $code), "\r", "\n");
			$braces = substr_count($code, "\n");
		}

		$code = ob_get_clean();
		$code = strip_tags($code);

		// Get the error message and line number
		if(preg_match("'syntax error, (.+) in .+ on line (\d+)$'s", $code, $code))
		{
			$code[2] = (int) $code[2];
			$code = $code[2] <= $braces
				? array($code[1], $code[2])
				: array('unexpected $end' . substr($code[1], 14), $braces);
		}
		else
			$code = array('syntax error', 0);
	}
	else
	{
		ob_end_clean();
		$code = false;
	}

	@ini_set('display_errors', $token);
	@ini_set('log_errors', $inString);
	return $code;
}

/**
* modify the outbuffer
*/
function ob_portamx($buffer)
{
	global $context, $pmxCacheFunc, $PortaMx_cache, $modSettings, $txt;

	if(!empty($_REQUEST['pmxcook']))
		return $buffer;

	// add cache stats to buffer
	if($pmxCacheFunc['stat']($buffer) && !empty($context['pmx']['settings']['cachestats']) && !empty($PortaMx_cache['vals']['mode']))
	{
		if(preg_match('~(<p.*>)('. $txt['page_created'] .'.*</p>)~U', $buffer, $match) == 1 && count($match) == 3)
		{
			$values = $PortaMx_cache['vals'];
			$values['mode'] = $txt['cachemode'][$values['mode']];
			$values['time'] = $values['time'] < 1.0 ? strval(round($values['time'] * 1000, 3)) . $txt['cachemilliseconds'] : strval(round($values['time'], 3)) . $txt['cacheseconds'];
			$tmp = $txt['cache_status'];

			foreach($txt['cachestats'] as $key => $keytxt)
				$tmp .= $keytxt . (in_array($key, array('loaded', 'saved')) ? round($values[$key] / 1024, 3) . $txt['cachekb'] : $values[$key]);

			$tmp .= ' ]<br />'. $match[2];
			$buffer = preg_replace('~'. $match[2] .'~', $tmp, $buffer);
		}
	}
	return $buffer;
}

/**
* Check Group access.
*/
function allowPmxGroup($groupstr)
{
	global $user_info;

	$isAdmin = allowedTo('admin_forum');

	// get the groups and (we have) deny groups
	@list($groups, $denygrps) = Pmx_StrToArray($groupstr, ',', '=');
	$result = !empty($isAdmin) ? (true xor count(array_intersect($user_info['groups'], $denygrps)) > 0) : count(array_intersect($user_info['groups'], $denygrps)) == 0;
	return !empty($isAdmin) ? $result : ($result && count(array_intersect($user_info['groups'], array_diff($groups, $denygrps))) > 0);
}

/**
* Check access
*/
function allowPmx($permission, $hideAdmin = false)
{
	global $context, $user_info;

	$isAdmin = allowedTo('admin_forum');

	// Administrators
	if(empty($hideAdmin) && !empty($isAdmin))
		return true;

	if(empty($context['pmx']['permissions']))
		return false;

	// Check if they have access
	$perms = array();
	$permission = Pmx_StrToArray($permission);
	foreach($permission as $perm)
		$perms = (array_key_exists($perm, $context['pmx']['permissions']) ? array_merge($perms, $context['pmx']['permissions'][$perm]) : $perms);

	return count(array_intersect(array_unique($perms), $user_info['groups'])) > 0 && empty($isAdmin);
}

/**
* formatted output stream (html) from any variable.
*/
function PortaMx_Printvar($vardata, $varname = '', $dept = 0)
{
	global $smcFunc;

	$result = '';
	$find_replace = array(
		'find' => array('&nbsp;', '&quot;', '&lt;', '&gt;', '&amp;'),
		'repl' => array(' ', '"', '<', '>', '&')
	);
	$format = array(
		'find' => array("\n", "\t"),
		'repl' => array('<br />', '&nbsp;&nbsp;')
	);

	if(is_array($vardata) || is_object($vardata))
	{
		if(!empty($dept))
			$varname = ($varname != '' ? ($varname{0} == '$' ? $varname : (is_string($varname) ? '\''. $varname .'\'' : strval($varname))) : $varname);

		if($varname != '')
			$result .= str_pad('', $dept*6, '&'.'nbsp;', STR_PAD_LEFT) . (empty($dept) ? '<b>'. $smcFunc['htmlspecialchars']($varname) .'</b>' : $smcFunc['htmlspecialchars']($varname)) .($dept > 0 ? ' => ' : ' = ') . (is_object($vardata) ? 'object(' : 'array(') .'<br />';
		else
			$result .= str_pad('', $dept*6, '&'.'nbsp;', STR_PAD_LEFT) . $smcFunc['htmlspecialchars']($varname) .($dept > 0 ? ' => ' : ' = '). (is_object($vardata) ? 'object(' : 'array(') .'<br />';

		$dept += 3;
		foreach($vardata as $key => $val)
		{
			if(is_array($val) || is_object($val))
				$result .= PortaMx_Printvar($val, $key, $dept);
			else
			{
				$val = (is_string($val) ? '\''. str_replace($format['find'], $format['repl'], $smcFunc['htmlspecialchars'](str_replace($find_replace['find'], $find_replace['repl'], $val), ENT_NOQUOTES) .'\'') : (is_bool($val) ? (!empty($val) ? 'true' : 'false') : strval($val)));
				$result .= str_pad('', $dept*6, '&'.'nbsp;', STR_PAD_LEFT) . (is_string($key) ? '\''. $smcFunc['htmlspecialchars']($key) .'\'' : strval($key)) .' => '. $val .',<br />';
			}
		}
		$result .= str_pad('', ($dept - 3)*6, '&'.'nbsp;', STR_PAD_LEFT) . ')'. ($dept - 3 > 0 ? ',' : '') .'<br />';
	}
	else
	{
		$vardata = (is_string($vardata) ? '\''. str_replace($format['find'], $format['repl'], $smcFunc['htmlspecialchars'](str_replace($find_replace['find'], $find_replace['repl'], $vardata), ENT_NOQUOTES) .'\'') : (is_bool($vardata) ? (!empty($vardata) ? 'true' : 'false') : $vardata));
		$varname = ($varname != '' ? ($varname{0} == '$' ? $varname : (is_string($varname) ? '\''. $varname .'\'' : strval($varname))) : $varname);
		$result .= str_pad('', $dept*6, '&'.'nbsp;', STR_PAD_LEFT) . $varname .' = '. $vardata .'<br />';
	}
	return $result;
}

/**
* create the header for PortaMx.
*/
function PortaMx_headers($action = '')
{
	global $context, $settings, $txt, $options, $scripturl, $boardurl, $user_info, $modSettings;

	$panel_names = array_keys($txt['pmx_block_panels']);
	foreach($panel_names as $pname)
	{
		// set panel upshrink
		$cook = 'upshr'. $pname;
		$cookval = pmx_getcookie($cook);
		if(!empty($context['pmx']['settings'][$pname .'_panel']['collapse_init']) && is_null($cookval))
		{
			$cookval = $options['collapse_'. $pname] = ($context['pmx']['settings'][$pname .'_panel']['collapse_init'] == 1 ? 1 : 0);
			pmx_setcookie($cook, $cookval);
		}
		else
			$options['collapse_'. $pname] = intval(!empty($cookval));
	}

	// switch of xbarkeys on posting
	if($action == 'post')
		$context['pmx']['xbarkeys'] = 0;

	if(file_exists($settings['theme_dir'] . '/PortaMxPadding.php'))
		require_once($settings['theme_dir'] . '/PortaMxPadding.php');
	elseif(empty($modSettings['allow_guestAccess']) && $user_info['is_guest'] && $action == 'community')
		$tbpad = '0';
	else
	{
		if(!empty($_GET['board']) && $action != 'post')
			$tbpad = '0';
		else
			switch ($action)
			{
				case 'admin':
				case 'moderate':
				case 'profile':
				case 'pm':
				case 'stats':
				case 'mlist':
					$tbpad = '0';
				break;

				case 'community':
				case 'collapse':
					$tbpad = '-2';
				break;

				case 'search':
					$tbpad = '-6';
				break;

				case 'search2':
					$tbpad = '0';
				break;

				case 'post':
					$tbpad = '-16';
				break;

				default:
					$tbpad = '0';
			}
	}

	if(empty($context['pmx']['settings']['disableHS']))
	{
		$context['html_headers'] .= '
<link rel="stylesheet" type="text/css" href="'. PortaMx_loadCompressed('highslide.css', array('dir' => $settings['default_theme_dir'] .'/highslide/', 'url' => $settings['default_theme_url'] .'/highslide/')) .'" />
<script type="text/javascript" src="'. $settings['default_theme_url'] .'/highslide/highslide-full.packed.js"></script>';

	PortaMx_inlineJS('
		var pmx_failed_image = \''. $context['pmx_imageurl'] .'missing_image.png\';
		var pmx_failed_image_text = \''. $txt['pmx_hs_noimage'] .'\';
		hs.graphicsDir = "'. $settings['default_theme_url'] .'/highslide/graphics/";
		hs.blockRightClick = true;
		hs.fadeInOut = true;
		hs.outlineType = "rounded-white";
		hs.transitions = ["expand", "crossfade"];
		hs.transitionDuration = 500;
		hs.dimmingOpacity = 0.3;
		hs.showCredits = false;
		hs.restoreDuration = 250;
		hs.enableKeyListener = false;
		hs.zIndexCounter = 10001;
		hs.align = "center";
		hs.allowSizeReduction = true;');

		if(!empty($context['right_to_left']))
			$context['html_headers'] .= '
<link rel="stylesheet" type="text/css" href="'. PortaMx_loadCompressed('highslide_rtl.css', array('dir' => $settings['default_theme_dir'] .'/highslide/', 'url' => $settings['default_theme_url'] .'/highslide/')) .'" />';
	}

	$context['html_headers'] .= '
<link rel="stylesheet" type="text/css" href="'. PortaMx_loadCompressed('portamx.css') .'" />';
	PortaMx_inlineJS('
		function pmx_setCookie(sName, sValue){return pmxXMLrequest("action=xmlhttp;pmxcook=setcookie;xml", "var="+ sName +"&val="+ encodeURIComponent(sValue) +"&'. $context['session_var'] .'=' .$context['session_id'] .'");}
		function pmx_getCookie(sName){return pmxXMLrequest("action=xmlhttp;pmxcook=getcookie;xml;name="+ sName);}');

	if(!empty($context['right_to_left']))
		$context['html_headers'] .= '
<link rel="stylesheet" type="text/css" href="'. PortaMx_loadCompressed('portamx_rtl.css') .'" />';

		// load additional css file on a fullsize frontpage
	if($action == 'frontpage' && $context['pmx']['settings']['frontpage'] == 'fullsize' && file_exists($settings['theme_dir'] .'/css/pmx_frontpage.css'))
		$context['html_headers'] .= '
<link rel="stylesheet" type="text/css" href="'. $settings['theme_url'] .'/css/pmx_frontpage.css'. $context['pmx_jsrel'] .'" />';

	$context['html_headers'] .= '
<!--[if IE]><style type="text/css">#xbarleft:hover, #xbarright:hover, #xbartop:hover, #xbarbottom:hover, #xbarhead:hover, #xbarfoot:hover; {filter: Alpha(Opacity=70);filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=70);}</style><![endif]-->
<style type="text/css">#preview_section{ margin-top:'. abs($tbpad) .'px; } #pmx_toppad{ margin-top:'. $tbpad .'px;'. (!empty($context['pmx']['settings']['forumscroll']) ? ' overflow:auto;' : '') .' } .pmx_maintable{'. (!empty($context['pmx']['settings']['forumscroll']) ? 'table-layout:fixed;' : '') .'}';

	if(empty($context['pmx']['collapse']['head']))
		$context['html_headers'] .= '	#xbartop{ top: 1px; }';

	if(empty($context['pmx']['collapse']['foot']))
		$context['html_headers'] .= '	#xbarbottom{ bottom: 1px; }';

	if($action == 'frontpage')
		$context['html_headers'] .= '.bbc_code{ max-height: 7em; }';

	if(!empty($context['pmx_style_isCore']))
		$context['html_headers'] .= '.pmxinfo{ padding-top: 0.5em !important;}';

	$context['html_headers'] .= '</style>';

	// javascript for the footer part
	PortaMx_inlineJS('
	var pmx_xBarKeys = '. (!empty($context['pmx']['xbarkeys']) ? 'true' : 'false') .';
	var xBarKeys_Status = pmx_xBarKeys;
	var panel_text = new Object();', false, false);

	foreach($txt['pmx_block_panels'] as $key => $val)
		PortaMx_inlineJS('
	panel_text["'. $key .'"] = "'. htmlentities($val, ENT_QUOTES, $context['pmx']['encoding']) .'";', false, false);

	PortaMx_inlineJS('
	function setUpshrinkTitles() {
		if(this.opt.bToggleEnabled)
		{	var panel = this.opt.aSwappableContainers[0].substring(8, this.opt.aSwappableContainers[0].length - 3).toLowerCase();
			document.getElementById("xbar" + panel).setAttribute("title", (this.bCollapsed ? "'. htmlentities($txt['pmx_hidepanel'], ENT_QUOTES, $context['pmx']['encoding']) .'" : "'. htmlentities($txt['pmx_showpanel'], ENT_QUOTES, $context['pmx']['encoding']) .'") + panel_text[panel]); }
	}', false, false);

	foreach($panel_names as $pname)
		PortaMx_inlineJS('
	var '. $pname .'Panel = new smc_Toggle({
		bToggleEnabled: '. (empty($context['pmx']['show_'. $pname .'panel']) ? 'false' : 'true') .',
		bCurrentlyCollapsed: '. (empty($options['collapse_'. $pname]) ? 'false' : 'true') .',
		funcOnBeforeCollapse: setUpshrinkTitles,
		funcOnBeforeExpand: setUpshrinkTitles,
		aSwappableContainers: [
			\'upshrink'. ucfirst($pname) .'Bar\'
		],
		oCookieOptions: {
			bUseCookie: true,
			sCookieName: \'upshr'. $pname .'\',
			sCookieValue: \''. $options['collapse_'. $pname] .'\'
		}
	});', false, false);
}

// Custom each() function for deprecated PHP each() function.
function PMX_Each(&$arr) {
	$key = key($arr);
	$result = ($key === null) ? false : [$key, current($arr), 'key' => $key, 'value' => current($arr)];
	next($arr);
	return $result;
}
?>
