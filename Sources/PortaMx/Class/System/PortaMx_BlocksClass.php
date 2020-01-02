<?php
/**
* \file PortaMx_BlocksClass.php
* Global Blocks class
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2015 by PortaMx corp. - http://portamx.com
* \version 1.54
* \date 18.11.2015
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class PortaMxC_Blocks
* The Global Blocks class.
* @see PortaMx_BlocksClass.php
* \author Copyright by PortaMx - http://portamx.com
*/
class PortaMxC_Blocks
{
	var $cfg;							///< common config
	var $visible;					///< visibility flag
	var $cache_key;				///< cache key
	var $cache_mode;			///> cache mode
	var $cache_time;			///< cache time
	var $cache_trigger;		///< cache trigger
	var $startpage;				///< pageindex start page
	var $postspage;				///< items on a page
	var $pageindex;				///< pageindex sting
	var $inBlockCall;			///< inner block call

	/**
	* The Contructor.
	* Saved the config and checks the visiblity access.
	* If access true, the block css file is loaded if exist.
	*/
	function __construct($blockconfig, &$visible)
	{
		global $context, $options, $user_info, $settings, $mbname, $scripturl, $modSettings, $maintenance;

		// load the config
		if(isset($blockconfig['config']))
			$blockconfig['config'] = unserialize($blockconfig['config']);
		$this->cfg = $blockconfig;
		$this->startpage = 0;
		$this->inBlockCall = $this->cfg['side'] == 'bib';
		$this->cfg['uniID'] = 'blk'. $this->cfg['id'] .(!empty($this->inBlockCall) ? '-0' : '');

		// set the cache_key, cache time and trigger
		$this->cache_key = $this->cfg['blocktype'] . $this->cfg['id'];
		if(in_array($this->cfg['blocktype'], array_keys($context['pmx']['cache']['blocks'])))
		{
			$this->cache_mode = $context['pmx']['cache']['blocks'][$this->cfg['blocktype']]['mode'];
			$this->cache_time = $context['pmx']['cache']['blocks'][$this->cfg['blocktype']]['time'];
			if($context['pmx']['cache']['blocks'][$this->cfg['blocktype']]['trigger'] == 'default')
				$this->cache_trigger = $context['pmx']['cache']['default']['trigger'];
			else
				$this->cache_trigger = $context['pmx']['cache']['blocks'][$this->cfg['blocktype']]['trigger'];

			// call cache trigger
			$visible = $this->pmxc_checkCacheStatus();
			if(empty($visible))
				return;
		}
		else
		{
			$this->cache_mode = false;
			$this->cache_time = 3600;
			$this->cache_trigger = '';
		}

		if(!empty($maintenance) && empty($this->cfg['config']['maintenance_mode']) && empty($user_info['is_admin']))
			$visible = $this->visible = false;

		// SD standalone mode and hide the block on it?
		elseif(!empty($modSettings['shd_helpdesk_only']) && !empty($this->cfg['config']['sd_standalone']))
			$visible = $this->visible = false;
		else
		{
			// check the block visible access if not a BIB block ($this->inBlockCall = true)
			if(empty($this->inBlockCall))
			{
				// check group access
				if(isset($this->cfg['inherit_acs_from_cat']))
					$this->visible = !empty($this->cfg['inherit_acs_from_cat']) || allowPmxGroup($this->cfg['acsgrp']);
				else
					$this->visible = allowPmxGroup($this->cfg['acsgrp']);

				// Show "Home - Community" Buttons?
				if($this->visible && $context['pmx']['settings']['frontpage'] != 'none' && $this->cfg['side'] == 'front')
					$context['pmx']['showhome'] += intval(!isset($this->cfg['config']['ext_opts']['pmxact']) || (isset($this->cfg['config']['ext_opts']['pmxact']) && (is_null($this->cfg['config']['ext_opts']['pmxact']) || (is_array($this->cfg['config']['ext_opts']['pmxact']) && in_array('frontpage=1', $this->cfg['config']['ext_opts']['pmxact'])))));

				// disable frontpage blocks before init if the frontpage not shown
				if($this->visible && $this->cfg['side'] == 'front' && empty($context['pmx']['pageReq']) && !empty($_GET))
					$this->visible = false;

				// hide frontblock on pagerequest?
				if($this->visible && $this->cfg['side'] == 'front' && (array_key_exists('spage', $context['pmx']['pageReq']) || array_key_exists('cat', $context['pmx']['pageReq'])))
					$this->visible = (empty($this->cfg['config']['frontplace']) || (!empty($this->cfg['config']['frontplace']) && $this->cfg['config']['frontplace'] != 'hide'));

				// check page request
				if($this->visible && $this->cfg['side'] == 'pages')
				{
					$this->visible = !empty($context['pmx']['pageReq']) || empty($context['pmx']['forumReq']);
					if(!empty($this->cfg['config']['static_block']) || !in_array($this->cfg['blocktype'], array('article', 'category')))
						$this->cfg['config']['ext_opts']['pmxcust'] .= empty($this->cfg['config']['ext_opts']['pmxcust']) ? '@' : '';
				}

				// check dynamic visibility options
				if($this->visible && !empty($this->cfg['config']['ext_opts']))
					$this->visible = pmx_checkExtOpts(true, $this->cfg['config']['ext_opts'], isset($this->cfg['config']['pagename']) ? $this->cfg['config']['pagename'] : '');

				// continue if the block visible
				if($this->visible)
				{
					// if Page block and have a frontpage switch?
					if(empty($context['pmx']['forumReq']) && $this->cfg['side'] == 'pages' && !empty($this->cfg['config']['frontmode']))
						$context['pmx']['settings']['frontpage'] = $this->cfg['config']['frontmode'];

					// check block display on frontpage mode
					if(!empty($this->cfg['config']['frontview']) && !in_array($this->cfg['side'], array('front', 'pages')))
						$this->visible = empty($context['pmx']['forumReq']) && ($this->cfg['config']['frontview'] == $context['pmx']['settings']['frontpage']);
				}
			}
			// on inblock calls only check the dynmic visibilities
			else
				$this->visible = pmx_checkExtOpts(true, $this->cfg['config']['ext_opts'], $this->cfg['config']['pagename']);

			// if visible check for a custom cssfile
			if(!empty($this->visible) && $this->cfg['blocktype'] != 'category')
				$this->getCustomCSS($this->cfg);

			if(!empty($this->visible) && $this->cfg['side'] == 'pages' && array_key_exists('spage', $context['pmx']['pageReq']))
			{
				$context['pmx']['pagenames']['spage'] = $this->getUserTitle();
				if(empty($context['pmx']['pagenames']['spage']))
					$context['pmx']['pagenames']['spage'] = htmlspecialchars($this->cfg['config']['pagename'], ENT_QUOTES);
			}
			$visible = $this->visible;
		}
	}

	/**
	* Handle a block pageindex
	*/
	function pmxc_constructPageIndex($items, $pageitems)
	{
		global $scripturl, $context, $modSettings;

		// hide pageindex if only one page..
		if($items > $pageitems)
		{
			if(isset($_POST['pg']) && is_array($_POST['pg']) && array_key_exists($this->cfg['uniID'], $_POST['pg']))
			{
				$page = $_POST['pg'][$this->cfg['uniID']];
				unset($_POST['pg'][$this->cfg['uniID']]);
				$this->startpage = $page;
			}
			elseif(($cook = pmx_getcookie('pgidx_'. $this->cfg['uniID'])) && !is_null($cook))
				$this->startpage = $cook;
			else
				$this->startpage = 0;

			$topfragment = !empty($context['pmx']['settings']['topfragment']) ? '#top'. $this->cfg['uniID'] : '';
			$cururl = preg_replace('~pg\[[a-zA-Z0-9\-)+\]\=[0-9\;]+~', '', getCurrentUrl(true)) .'pg['. $this->cfg['uniID'] .']=%1$d'. $topfragment;
			$this->postspage = $pageitems;
			$this->pageindex = $this->pmxc_makePageIndex($cururl, $this->startpage, $items, $this->postspage);
			if(!empty($context['pmx']['settings']['restoretop']))
				$this->pageindex = str_replace('<a', '<a onclick="pmxWinGetTop(\''. $this->cfg['uniID'] .'\')"', $this->pageindex);
		}
	}

	/**
	* Create the pageindex
	*/
	function pmxc_makePageIndex($url, $start, $items, $pageitems)
	{
		global $scripturl, $context, $modSettings;

		$pageindex = constructPageIndex(urldecode($url), $start, $items, $pageitems, true);
		$pageindex = preg_replace('/\;start\=([\%\$a-z0-9]+)/', '', $pageindex);

		return $pageindex;
	}

	/**
	* Get a config item.
	* The item can be empty, a single value or a array
	*/
	function getBlockConfig($itemstr = '')
	{
		$item = Pmx_StrToArray($itemstr);
		$result = null;

		if(empty($item))								// no Item, get all
			$result = $this->cfg;
		elseif(!is_array($item))				// no array, get item
			$result = $this->cfg[$item];
		else														// array, find the item
		{
			$ptr = &$this->cfg;
			foreach($item as $key)
				$ptr = &$ptr[$key];

			if(isset($ptr))
				$result = $ptr;
		}
		return $result;
	}

	/**
	* Setting a config item.
	* The item can be a single value or a array
	*/
	function setBlockConfig($itemstr = '', $value = '')
	{
		$result = $this->getBlockConfig($itemstr);
		if(!is_null($result))
		{
			$item = Pmx_StrToArray($itemstr);
			$base = &$this->cfg;
				foreach($item as $val)
					$base = &$base[$val];
			$base = $value;
		}
	}

	/**
	* Get custom css definitions
	*/
	function getCustomCSS(&$cfg)
	{
		global $modSettings;

		// load the custom css
		$cfg['customclass'] = '';

		$result = PortaMx_loadCustomCss($cfg['config']['cssfile'], true);
		if(!empty($result))
		{
			foreach($result['class'] as $key => $val)
			{
				if(!empty($val) && isset($cfg['config']['visuals'][$key]) && $cfg['config']['visuals'][$key] != 'none' && !empty($cfg['config']['visuals'][$key]))
				{
					$cfg['config']['visuals'][$key] = $val;
					$cfg['customclass'][$key] = $val;
				}
			}
		}
	}

	/**
	* Get user title with fallback
	*/
	function getUserTitle($cfg = null)
	{
		global $context, $language;

		if(is_null($cfg))
			$titles = $this->cfg['config']['title'];
		else
			$titles = $cfg['config']['title'];

		if(!empty($titles[$context['pmx']['currlang']]))
			return htmlspecialchars($titles[$context['pmx']['currlang']], ENT_QUOTES);
		elseif(!empty($titles[$language]))
			return htmlspecialchars($titles[$language], ENT_QUOTES);
		else
			return '';
	}
}

/**
* @class PortaMxC_SystemBlock
* The Global Systemblock Class.
* @see PortaMx_BlocksClass.php
* \author Copyright by PortaMx - http://portamx.com
*/
 class PortaMxC_SystemBlock extends PortaMxC_Blocks
{
	/**
	* The display block Methode.
	* ShowBlock prepare the frame, header and the body of each block.
	* Load the a css file if available.
	* After frame, header and body is prepared, the block depended content output is called.
	*/
	function pmxc_ShowBlock($count = 0, $placement = '')
	{
		global $context, $user_info, $settings, $options, $scripturl, $boardurl, $modSettings, $txt;

		if(!empty($this->inBlockCall))
			$this->cfg['id'] .= '_0';

		// set block upshrink
		$cook = 'upshr'. $this->cfg['blocktype'] . $this->cfg['id'];
		$cookval = pmx_getcookie($cook);
		if(!empty($this->cfg['config']['collapse_state']) && is_null($cookval))
		{
			$cookval = $options['collapse'. $this->cfg['blocktype'] . $this->cfg['id']] = ($this->cfg['config']['collapse_state'] == '1' ? 1 : 0);
			pmx_setcookie($cook, $cookval);
		}
		else
			$options['collapse'. $this->cfg['blocktype'] . $this->cfg['id']] = intval(!empty($cookval));

		// Placement for Frontpage blocks?
		if(function_exists('Pmx_Frame_top') && (empty($placement) || (!empty($placement) && ($placement == $this->cfg['config']['frontplace'] || empty($this->cfg['config']['frontplace'])))))
		{
			if($this->cfg['blocktype'] == 'category');
				$this->getCustomCSS($this->cfg);

			$topdata = Pmx_Frame_top($this->cfg, $count);

			// whe have now to call the block depended methode.
			$this->pmxc_ShowContent($count);

			Pmx_Frame_bottom($this->cfg, $topdata);

			return 1;
		}
		else
			return 0;
	}

	/**
	* get_isRead.
	* Get the requested topic and message id
	*/
	function get_isRead()
	{
		$res = array('topic' => 0, 'msg' => 0);
		if(!empty($_REQUEST['topic']))
		{
			$curTopic = explode('.', $_REQUEST['topic']);							// topicID
			$res['topic'] = isset($curTopic[0]) ? $curTopic[0] : 0;

			if(isset($_REQUEST['start']))
			{
				preg_match('~msg(\d+)~', $_REQUEST['start'], $curMsg);	// msgID
				$res['msg'] = isset($curMsg[1]) ? $curMsg[1] : 0;
			}
		}
	  return $res;
	}

	/**
	* checkCacheStatus.
	* If the cache enabled, the cache trigger will be checked.
	* This is often overwrite.
	*/
	function pmxc_checkCacheStatus()
	{
		global $context, $user_info, $pmxCacheFunc;

		$result = true;
		if(isset($this->cfg['cache']) && $this->cfg['cache'] > 0 && !empty($this->cache_trigger))
		{
			if(($data = $pmxCacheFunc['get']($this->cache_key, $this->cache_mode)) !== null)
			{
				$UserInfoID = isset($user_info['id']) ? $user_info['id'] : '';
				$curRead = $this->get_isRead();
				$cachefunc = create_function('$cRead, $topics, $isRead, $userID', $this->cache_trigger);
				$res = $cachefunc($curRead, $data[0], $data[1], $UserInfoID);

				if($res == 'msg')
				{
					$data[1][$user_info['id']][$curRead['topic']][$curRead['msg']] = true;
					$pmxCacheFunc['put']($this->cache_key, $data, $this->cache_time, $this->cache_mode);
				}
				elseif($res == 'topic')
				{
					$data[1][$user_info['id']][$curRead['topic']] = true;
					$pmxCacheFunc['put']($this->cache_key, $data, $this->cache_time, $this->cache_mode);
				}
				elseif($res == 'clr')
					$pmxCacheFunc['clear']($this->cache_key, $this->cache_mode);

				// cleanup
				unset($data);
				unset($topics);
				unset($isRead);

				$result = ($res != 'clr');
			}
		}
		return $result;
	}

	/**
	* InitContent returns the visibility flag.
	* This is mostly overwrite.
	*/
	function pmxc_InitContent()
	{
		// return the visibility flag (true/false)
		return $this->visible;
	}

	/**
	* ShowContent outputs the content of a block.
	* This is often overwrite.
	*/
	function pmxc_ShowContent()
	{
		// Write out the content
		echo $this->cfg['content'];
	}
}
?>
