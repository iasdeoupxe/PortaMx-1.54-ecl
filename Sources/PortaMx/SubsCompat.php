<?php
/**
* \file SubsCompat.php
* Compatibility & ECL Subroutines for Portamx.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('SMF'))
	die('This Portamx file can\'t be run without SMF');

/**
* parse_url values
*/
if(@version_compare(PHP_VERSION, '5.1.2') < 0)
{
	define('PHP_URL_SCHEME', 'scheme');
	define('PHP_URL_HOST', 'host');
	define('PHP_URL_PORT', 'port');
	define('PHP_URL_USER', 'user');
	define('PHP_URL_PASS', 'pass');
	define('PHP_URL_PATH', 'path');
	define('PHP_URL_QUERY', 'query');
	define('PHP_URL_FRAGMENT', 'fragment');
}

/**
* System modal ECL init
*/
function pmx_ECL_Init()
{
	global $user_info, $context, $maintenance, $settings, $modSettings, $txt;

	if(WIRELESS && !empty($_GET['accepteclcoookie']) && $_GET['accepteclcoookie'] = 'yes')
	{
		pmx_setECL_Cookie();
		redirectexit();
	}
	else
	{
		$context['html_headers'] .= '
	<style type="text/css">
	#ecl_privacy
	{
		font-size: 1.0em;
		line-height: 1.3em;
	}
	#ecl_privacy td
	{
		padding: 0 10px 5px 0;
	}
	#ecl_privacy td strong
	{
		text-decoration: underline;
	}
	#ecl_privacy p
	{
		margin-top: 0px;
	}
	</style>
	<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
		function Setlang(elm){window.location.href = elm.options[elm.selectedIndex].value;}
		function pmx_seteclcook(sName, sValue){
		var expired = new Date();
		expired.setTime(expired.getTime() + (30 * 24 * 60 * 60 * 1000));
		document.cookie = sName +"="+ sValue +";expires="+ expired.toGMTString() +";path=/;";}
	// ]]></script>';

		if(!empty($maintenance))
			return;

		if(empty($modSettings['pmxportal_disabled']))
			PortaMx_getSettings(true);
		else
			loadLanguage('PortaMx/PortaMx');

		loadTemplate('PortaMx/Error');
		if(WIRELESS)
			$context['sub_template'] = WIRELESS_PROTOCOL . '_eclcookie';

		$context['pmx_error_title'] = $txt['pmxecl_noAuth'];
		$context['pmx_error_text'] = $txt['pmxelc_needAccept'] . $txt['pmxelc_agree'];
		$context['pmx_eclcheck'] = true;
	}
}

/**
* System none modal ECL init
*/
function pmx_eclnonemodal()
{
	global $context, $settings, $modSettings, $maintenance, $scripturl, $txt;

	if(empty($_REQUEST['pmxcook']) && !WIRELESS && !empty($modSettings['pmx_eclmodal']) && empty($modSettings['pmx_mobile']['detect']) && !pmx_checkECL_Cookie())
	{
		if(file_exists($settings['theme_dir'] .'/css/pmx_eclnomodal.css'))
			$cssfile = $settings['theme_url'] .'/css/pmx_eclnomodal.css';
		else
			$cssfile = $settings['default_theme_url'] .'/PortaMx/SysCss/pmx_eclnomodal.css';

		$context['html_headers'] .= '
		<link rel="stylesheet" type="text/css" href="'. $cssfile .'" />
		<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
			function Setlang(elm){window.location.href = elm.options[elm.selectedIndex].value;}
			function pmx_seteclcook(sName, sValue){
			var expired = new Date();
			expired.setTime(expired.getTime() + (30 * 24 * 60 * 60 * 1000));
			document.cookie = sName +"="+ sValue +";expires="+ expired.toGMTString() +";path=/;";}
		// ]]></script>';

		if(empty($maintenance))
		{
			if(!empty($modSettings['pmxportal_disabled']))
				loadLanguage('PortaMx/PortaMx');

			loadtemplate('PortaMx/EclNoneModal');
			$context['template_layers'][] = 'eclnonemodal';
		}
	}
}

/**
* adsense content check for customer php blocks
*/
function pmx_checkads($state)
{
	global $modSettings, $user_info;

	if(!empty($modSettings['pmx_ecl']) && !pmx_checkECL_Cookie() && $user_info['is_guest'] && !$user_info['possibly_robot'])
		return false;
	else
		return $state && true;
}

/**
* set the ECL cookie
*/
function pmx_setECL_Cookie()
{
	global $modSettings;

	if(!empty($modSettings['pmx_ecl']))
	{
		setcookie('ecl_auth', 1, time() + (30 * 24 * 60 * 60), '/');
		$_COOKIE['ecl_auth'] = 1;
	}
}

/**
* Check the ECL cookie
*/
function pmx_checkECL_Cookie()
{
	global $modSettings;

	return (!empty($modSettings['pmx_ecl']) ? !empty($_COOKIE['ecl_auth']) : true);
}

/**
* Error on missing ECL cookie
*/
function pmx_ECL_Error($what)
{
	global $modSettings, $txt;

	if(!empty($modSettings['pmx_ecl']))
	{
		if(empty($modSettings['pmxportal_disabled']) && !defined('PortaMx'))
			PortaMx(true);
		else
			loadLanguage('PortaMx/PortaMx');

		fatal_lang_error('pmxelc_failed_'. $what, false);
	}
}

/**
* Replacement for setcookie
*/
function pmx_setcookie($name, $value)
{
	global $modSettings, $boardurl;

	if(pmx_checkECL_Cookie())
	{
		$path = (!empty($modSettings['localCookies']) ? pmx_parse_url($boardurl, PHP_URL_PATH) : '') .'/';
		if($value == '')
			setcookie('pmx_'. $name, '', time() - 60, $path);
		else
			setcookie('pmx_'. $name, $value, 0, $path);
	}
}

/**
* Replacement for $_COOKIE
*/
function pmx_getcookie($name)
{
	return isset($_COOKIE['pmx_'. $name]) ? $_COOKIE['pmx_'. $name] : null;
}

/**
* Replacement for file_put_contents on PHP < 5.1.0
*/
function pmx_file_put_contents($fname, $data)
{
	if(@version_compare(PHP_VERSION, '5.1.0') >= 0)
		return file_put_contents($fname, $data, LOCK_EX);
	else
	{
		$fp = @fopen($fname, 'wb');
		if($fp)
		{
			@flock($fp, LOCK_EX);
			stream_set_write_buffer($fp, 0);
			$cache_bytes = fwrite($fp, $data);
			fclose($fp);
			return $cache_bytes;
		}
		else
			return -1;
	}
}

/**
* Replacement for http_build_query PHP < 5.1.3
*/
function pmx_http_build_query($data, $prefix = '', $sep = ';')
{
	$ret = array();
	foreach ((array) $data as $k => $v)
	{
		$k = urlencode($k);
		if(is_int($k) && !empty($prefix))
			$k = $prefix . $k;
		if(is_array($v) || is_object($v))
			array_push($ret, pmx_http_build_query($v, '', $sep));
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
* Replacement for parse_url PHP < 5.1.2
*/
function pmx_parse_url($data, $component = '')
{
	if(@version_compare(PHP_VERSION, '5.1.2') >= 0)
		return empty($component) ? parse_url($data) : parse_url($data, $component);
	else
	{
		$tmp = parse_url($data);
		return empty($component) ? $tmp : $tmp[$component];
	}
}

/**
* Replacement for pmx_serialize (don't work with miltybyte utf8)
*/
function pmx_serialize($data)
{
	if(@version_compare(PHP_VERSION, '5.2.1') > 0)
		return serialize($data);
	else
		return preg_replace('/s:(\d+):"([^"]*)";/se', "'s:'. strlen('\\2') .':\"\\2\";'", serialize($data));
}

/**
* pmx_IsMobile - called from PortaMx Loader
* returns nothing
**/
function pmx_IsMobile()
{
	global $modSettings;

	$modSettings['pmx_isMobile'] = false;

	// smartphone string variables.
	$mobileStrings = array(
		'android',
		'iemobile',
		'iphone',
		'ipod',
		'ipad',
		'kindle',
		'mobile',
		'mobi',
	);

	$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
	if(preg_match_all('~'. implode('\b|', $mobileStrings) .'\b~i', $useragent, $device))
		$modSettings['pmx_isMobile'] = true;
}
?>
