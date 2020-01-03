<?php
/**
* \file SubsCache.php
* Cache subroutines for Portamx.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('SMF'))
	die('This Portamx file can\'t be run without SMF');

/**
* Init the cache functions array
*/
global $PortaMx_cache, $pmxCacheFunc, $boardurl;
$PortaMx_cache['key'] = md5($boardurl . filemtime(__FILE__)) .'-PMX';
$PortaMx_cache['vals'] = array(
	'mode' => 0,
	'hits' => 0,
	'fails' => 0,
	'loaded' => 0,
	'saved' => 0,
	'time' => 0
);

// setup caching functions array
if(empty($modSettings['cache_enable']) && !empty($modSettings))
{
	// no caching
	$PortaMx_cache['vals']['mode'] = 0;
	$pmxCacheFunc = array(
		'get' =>   'pmxCacheGetNull',
		'put' =>   'pmxCacheNull',
		'clear' => 'pmxCacheNull'
	);
}
else
{
	$pmxCacheFunc = array(
		'get' =>   'pmxCacheGet',
		'put' =>   'pmxCachePut',
		'clear' => 'pmxCacheClr'
	);

	/**
	* memCache
	*/
	if(function_exists('memcache_get') && isset($modSettings['cache_memcached']) && trim($modSettings['cache_memcached']) != '')
	{
		$PortaMx_cache['vals']['mode'] = 1;

		// Get key data from cache
		function pmxCacheGet($key, $useMember = false, $null_array = false)
		{
			global $PortaMx_cache, $user_info, $mcache;

			$st = get_milliseconds();
			$key = $PortaMx_cache['key'] . ($useMember ? '-'. implode('_', $user_info['groups']) : '') .'-'. $key;

			// connected?
			if(empty($mcache))
				connect_mcache();
			if($mcache)
			{
				$value = memcache_get($mcache, $key);
				if(!empty($value))
				{
					$PortaMx_cache['vals']['loaded'] += strlen($value);
					$PortaMx_cache['vals']['hits']++;
					$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;
					return unserialize($value);
				}
			}
			$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;;
			return empty($null_array) ? null : array();
		}

		// Put key data to cache
		function pmxCachePut($key, $value, $ttl, $useMember = false, $cleaner = null)
		{
			global $PortaMx_cache, $user_info, $mcache;

			$st = get_milliseconds();
			$ckey = $PortaMx_cache['key'] . ($useMember ? '-'. implode('_', $user_info['groups']) : '') .'-'. $key;

			if($value !== null)
				$value = pmx_serialize($value);
			else
			{
				if($cleaner !== null && $useMember)
					$ckey = $PortaMx_cache['key'] .'-'. $cleaner .'-'. $key;
			}

			// connected?
			if(empty($mcache))
				connect_mcache();
			if($mcache)
			{
				memcache_set($mcache, $ckey, $value, 0, $ttl);

				if($value !== null)
				{
					$PortaMx_cache['vals']['saved'] += strlen($value);
					$PortaMx_cache['vals']['fails']++;
				}
				$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;
			}

			// handle member groups key?
			if($useMember && $cleaner === null)
				pmxCacheMemGroupAcs();
		}

		// Connect a memcached server
		function connect_mcache($level = 3)
		{
			global $modSettings, $mcache, $db_persist;

			$servers = explode(',', $modSettings['cache_memcached']);
			$server = explode(':', trim($servers[array_rand($servers)]));

			// Don't try more times than we have servers!
			$level = min(count($servers), $level);

			if(empty($db_persist))
				$mcache = memcache_connect($server[0], empty($server[1]) ? 11211 : $server[1]);
			else
				$mcache = memcache_pconnect($server[0], empty($server[1]) ? 11211 : $server[1]);

			if(!$mcache && $level > 0)
				connect_mcache($level - 1);
		}
	}

	/**
	* Turck MMCache
	*/
	elseif(function_exists('mmcache_get'))
	{
		$PortaMx_cache['vals']['mode'] = 3;

		// Get key data from cache
		function pmxCacheGet($key, $useMember = false, $null_array = false)
		{
			global $PortaMx_cache, $user_info;

			$st = get_milliseconds();
			$key = $PortaMx_cache['key'] . ($useMember ? '-'. implode('_', $user_info['groups']) : '') .'-'. $key;

			$value = mmcache_get($key);
			if(!empty($value))
			{
				$PortaMx_cache['vals']['loaded'] += strlen($value);
				$PortaMx_cache['vals']['hits']++;
				$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;
				return unserialize($value);
			}
			$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;
			return empty($null_array) ? null : array();
		}

		// Put key data to cache
		function pmxCachePut($key, $value, $ttl, $useMember = false, $cleaner = null)
		{
			global $PortaMx_cache, $user_info;

			$st = get_milliseconds();

			if($value === null && $cleaner !== null && $useMember)
				$ckey = $PortaMx_cache['key'] .'-'. $cleaner .'-'. $key;
			else
				$ckey = $PortaMx_cache['key'] . ($useMember ? '-'. implode('_', $user_info['groups']) : '') .'-'. $key;

			if(mt_rand(0, 10) == 1)
				mmcache_gc();

			if($value !== null)
			{
				$value = pmx_serialize($value);
				mmcache_put($ckey, $value, $ttl);
			}
			else
				@mmcache_rm($ckey);

			if($value !== null)
			{
				$PortaMx_cache['vals']['saved'] += strlen($value);
				$PortaMx_cache['vals']['fails']++;
			}
			$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;

			// handle member groups key?
			if($useMember && $cleaner === null)
				pmxCacheMemGroupAcs();
		}
	}

	/**
	* Alternative PHP Cache (APC)
	*/
	elseif(function_exists('apc_store'))
	{
		$PortaMx_cache['vals']['mode'] = 4;

		// Get key data from cache
		function pmxCacheGet($key, $useMember = false, $null_array = false)
		{
			global $PortaMx_cache, $user_info;

			$st = get_milliseconds();
			$key = $PortaMx_cache['key'] . ($useMember ? '-'. implode('_', $user_info['groups']) : '') .'-'. $key;

			$value = apc_fetch($key . '-pmx');
			if(!empty($value))
			{
				$PortaMx_cache['vals']['loaded'] += strlen($value);
				$PortaMx_cache['vals']['hits']++;
				$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;
				return unserialize($value);
			}
			$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;
			return empty($null_array) ? null : array();
		}

		// Put key data to cache
		function pmxCachePut($key, $value, $ttl, $useMember = false, $cleaner = null)
		{
			global $PortaMx_cache, $user_info;

			$st = get_milliseconds();

			if($value === null && $cleaner !== null && $useMember)
				$ckey = $PortaMx_cache['key'] .'-'. $cleaner .'-'. $key;
			else
				$ckey = $PortaMx_cache['key'] . ($useMember ? '-'. implode('_', $user_info['groups']) : '') .'-'. $key;

			if($value !== null)
			{
				$value = pmx_serialize($value);
				apc_store($ckey . '-pmx', $value, $ttl);
			}
			else
				apc_delete($ckey . '-pmx');

			if($value !== null)
			{
				$PortaMx_cache['vals']['saved'] += strlen($value);
				$PortaMx_cache['vals']['fails']++;
			}
			$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;

			// handle member groups key?
			if($useMember && $cleaner === null)
				pmxCacheMemGroupAcs();
		}
	}

	/**
	* xCache
	*/
	elseif(function_exists('xcache_get') && ini_get('xcache.var_size') > 0)
	{
		$PortaMx_cache['vals']['mode'] = 5;

		// Get key data from cache
		function pmxCacheGet($key, $useMember = false, $null_array = false)
		{
			global $PortaMx_cache, $user_info;

			$st = get_milliseconds();
			$key = $PortaMx_cache['key'] . ($useMember ? '-'. implode('_', $user_info['groups']) : '') .'-'. $key;

			$value = xcache_get($key);
			if(!empty($value))
			{
				$PortaMx_cache['vals']['loaded'] += strlen($value);
				$PortaMx_cache['vals']['hits']++;
				$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;
				return unserialize($value);
			}
			$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;
			return empty($null_array) ? null : array();
		}

		// Put key data to cache
		function pmxCachePut($key, $value, $ttl, $useMember = false, $cleaner = null)
		{
			global $PortaMx_cache, $user_info;

			$st = get_milliseconds();

			if($value === null && $cleaner !== null && $useMember)
				$ckey = $PortaMx_cache['key'] .'-'. $cleaner .'-'. $key;
			else
				$ckey = $PortaMx_cache['key'] . ($useMember ? '-'. implode('_', $user_info['groups']) : '') .'-'. $key;

			if($value !== null)
			{
				$value = pmx_serialize($value);
				xcache_set($ckey, $value, $ttl);
			}
			else
				xcache_unset($ckey);

			if($value !== null)
			{
				$PortaMx_cache['vals']['saved'] += strlen($value);
				$PortaMx_cache['vals']['fails']++;
			}
			$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;

			// handle member groups key?
			if($useMember && $cleaner === null)
				pmxCacheMemGroupAcs();
		}
	}

	/**
	* file cache
	*/
	else
	{
		$PortaMx_cache['vals']['mode'] = 6;

		// Get key data from cache
		function pmxCacheGet($key, $useMember = false, $null_array = false)
		{
			global $PortaMx_cache, $cachedir, $user_info;

			$st = get_milliseconds();
			$fname = $cachedir .'/pmx_'. $PortaMx_cache['key'] . ($useMember ? '-'. implode('_', $user_info['groups']) : '') .'-'. $key .'.php';

			if(file_exists($fname) && is_readable($fname) && time() <= filemtime($fname) && filesize($fname) > 50)
			{
				eval(file_get_contents($fname));
				$PortaMx_cache['vals']['hits']++;
				$PortaMx_cache['vals']['loaded'] += strlen($value);
				$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;
				return unserialize($value);
			}
			else
			{
				@unlink($fname);
				return empty($null_array) ? null : array();
			}
			$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;
		}

		// Put key data to cache
		function pmxCachePut($key, $value, $ttl, $useMember = false, $cleaner = null)
		{
			global $PortaMx_cache, $cachedir, $user_info;

			$st = get_milliseconds();
			$fname = $cachedir .'/pmx_'. $PortaMx_cache['key'] . ($useMember ? '-'. implode('_', $user_info['groups']) : '') .'-'. $key .'.php';

			if($value !== null)
			{
				$cache_data = 'if(!(defined(\'PortaMxSEF\') || defined(\'PortaMx\'))) die; $value = \''. addcslashes(serialize($value), '\\\'') .'\';';
				$fp = @fopen($fname, 'wb');
				if($fp)
				{
					stream_set_write_buffer($fp, 0);
					flock($fp, LOCK_EX);
					$cache_bytes = fwrite($fp, $cache_data);
					flock($fp, LOCK_UN);
					fclose($fp);
					@touch($fname, (time() + $ttl));

					// Check that the cache write was successfully
					if($cache_bytes != strlen($cache_data))
						@unlink($fname);
					else
					{
						$PortaMx_cache['vals']['fails']++;
						$PortaMx_cache['vals']['saved'] += $cache_bytes;
					}
				}
			}
			else
			{
				if($cleaner !== null && $useMember)
					$fname = $cachedir .'/pmx_'. $PortaMx_cache['key'] .'-'. $cleaner .'-'. $key .'.php';

				@unlink($fname);
			}
			$PortaMx_cache['vals']['time'] += get_milliseconds() - $st;

			// handle member groups key?
			if($useMember && $cleaner === null)
				pmxCacheMemGroupAcs();
		}
	}
}

// no caching func
function pmxCacheGetNull($key, $useMember = false, $null_array = false)
{
	return empty($null_array) ? null : array();
}

function pmxCacheNull()
{
	return null;
}

/**
* Handle membergroup access data
*/
function pmxCacheMemGroupAcs()
{
	global $pmxCacheFunc, $context, $user_info;

	$acskey = 'accessgroups';
	$acs = implode('_', $user_info['groups']);
	$tmp = $pmxCacheFunc['get']($acskey, false);

	// new group key..
	if(empty($tmp))
		$pmxCacheFunc['put']($acskey, array($acs), $context['pmx']['cache']['default']['acsgroup_time'], false);

	elseif(!in_array($acs, $tmp))
	{
		// add a group..
		$tmp = array_merge($tmp, array($acs));
		$pmxCacheFunc['put']($acskey, $tmp, $context['pmx']['cache']['default']['acsgroup_time'], false);
	}
}

/**
* clear cached values
*/
function pmxCacheClr($key, $auto_clean = true)
{
	global $pmxCacheFunc, $user_info;

	if($auto_clean)
	{
		$acskey = 'accessgroups';
		$tmp = $pmxCacheFunc['get']($acskey, false);
		if($tmp !== null)
		{
			$acs = array(implode('_', $user_info['groups']));
			$tmp = array_merge(array_diff($tmp, $acs), $acs);

			// clear all group caches
			foreach($tmp as $memgrp)
				$pmxCacheFunc['put']($key, null, -1, true, $memgrp);
		}
		$pmxCacheFunc['put']($key, null, -1, false);
	}
	else
		$pmxCacheFunc['put']($key, null, -1, false);
}

/**
* get microtime
**/
function get_milliseconds()
{
	if(@version_compare(PHP_VERSION, '5.0.0') < 0)
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
	else
		return microtime(true);
}
?>
