<?php
/**
* \file theme_select.php
* Systemblock theme_select
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_theme_select
* Systemblock theme_select
* @see theme_select.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_theme_select extends PortaMxC_SystemBlock
{
	var $availthemes;			///< exist themes

	/**
	* checkCacheStatus.
	* do nothing
	*/
	function pmxc_checkCacheStatus()
	{
		return true;
	}

	/**
	* InitContent.
	* Checks the cache status and create the content.
	*/
	function pmxc_InitContent()
	{
		global $context, $pmxCacheFunc;

		// if visible init the content
		if($this->visible)
		{
			if($this->cfg['cache'] > 0)
			{
				// check the block cache
				if(($this->cfg['content'] = $pmxCacheFunc['get']($this->cache_key, $this->cache_mode)) === null)
				{
					$this->ThemeContent();
					$pmxCacheFunc['put']($this->cache_key, $this->cfg['content'], $this->cache_time, $this->cache_mode);
				}
			}
			else
				$this->ThemeContent();
		}

		// return the visibility flag (true/false)
		return $this->visible;
	}

	/**
	* ShowContent
	* Output the content and add necessary javascript
	*/
	function pmxc_ShowContent()
	{
		global $settings, $scripturl, $txt;

		// Write out the content
		echo $this->cfg['content'];

		$thid = (!empty($settings['theme_id']) ? $settings['theme_id'] : '1');
		echo '
	<script type="text/javascript"><!-- // --><![CDATA[
		var server_query_string = "'. base64_encode(str_replace(array($scripturl, '?'), '', getCurrentUrl())) .'";
		document.getElementById("thumbnail").title = "'. $txt['pmx_theme_change'] .'";
		document.getElementById("thumbnail").src = themeimages['. $thid .'];
		var elm = document.getElementById("pmx'.$this->cfg['id'].'themeselect");
		for(var idx = 0; idx < this.elm.length; idx++)
			elm.options[idx].selected = elm.options[idx].value == '. $thid .';
	// ]]></script>';
	}

	/**
	* ThemeContent.
	* Prepare the content and save in $this->cfg['content'].
	*/
	function ThemeContent()
	{
		global $context, $settings, $scripturl, $boardurl;

		$this->cfg['content'] = '';
		if(isset($this->cfg['config']['settings']['themes']))
		{
			$this->availthemes = PortaMx_getsmfThemes();	// get all themes

			$this->cfg['content'] .= '
									<select class="themsel" id="pmx'.$this->cfg['id'].'themeselect" name="" size="1" onchange="setThemeImage(this)">';

			foreach($this->availthemes as $thid => $data)
			{
				if(in_array($thid, $this->cfg['config']['settings']['themes']))
					$this->cfg['content'] .= '
										<option value="'. $thid .'"'. ($data['usertheme'] ? ' selected="selected"' : ($settings['theme_id'] == $thid ? ' selected="selected"' : '')) .'>'. $data['name'] .'</option>';
			}

			$this->cfg['content'] .= '
									</select>
									<form id="pmxthemechg'.$this->cfg['id'].'" action="'. $scripturl .'" method="post"></form>
									<div class="themthumb">
										<img class="tborder" id="thumbnail" src="" alt="*" title="" onclick="themeChange()" />
									</div>
	<script type="text/javascript"><!-- // --><![CDATA[
		var themeimages = new Array();';

			foreach($this->availthemes as $thid => $data)
			{
				if(in_array($thid, $this->cfg['config']['settings']['themes']))
					$this->cfg['content'] .= '
		themeimages['. $thid .'] = "'. $data['images_url'] .'/thumbnail.gif";';
			}

			$this->cfg['content'] .= '
		setThemeImage(document.getElementById("pmx'.$this->cfg['id'].'themeselect"));
		function setThemeImage(elm)
		{
			var idx = elm.selectedIndex;
			var thid = elm.options[idx].value;
			document.getElementById("thumbnail").src = themeimages[thid];
		}
		function themeChange()
		{
			pmxWinGetTop(\'themeselect'.$this->cfg['id'] .'\');
			var elm = document.getElementById("pmx'.$this->cfg['id'].'themeselect");
			document.getElementById("pmxthemechg'.$this->cfg['id'].'").action = smf_scripturl + "?theme=" + elm.options[elm.selectedIndex].value + ";pmxrd=" + server_query_string;
			document.getElementById("pmxthemechg'.$this->cfg['id'].'").submit();
		}
// ]]></script>';
		}
	}
}
?>
