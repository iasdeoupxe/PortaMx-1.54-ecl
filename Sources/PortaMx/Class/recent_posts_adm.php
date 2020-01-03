<?php
/**
* \file recent_posts_adm.php
* Admin Systemblock recent_posts
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_recent_posts_adm
* Admin Systemblock recent_posts_adm
* @see recent_posts_adm.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_recent_posts_adm extends PortaMxC_SystemAdminBlock
{
	/**
	* AdmBlock_init().
	* Setup caching.
	*/
	function pmxc_AdmBlock_init()
	{
		$this->can_cached = 1;			// enable caching
	}

	/**
	* AdmBlock_settings().
	* Setup the config vars and output the block settings.
	* Returns the css classes they are used.
	*/
	function pmxc_AdmBlock_settings()
	{
		global $context, $txt;

		// define the settings options
		echo '
					<td valign="top" style="padding:4px;">
						<input type="hidden" name="config[settings]" value="" />';

		// define numeric vars to check
		echo '
						<input type="hidden" name="check_num_vars[]" value="[config][settings][numrecent], 5" />';

		// show the settings screen
		echo '
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid"><span class="cat_left_title">'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span></h4>
						</div>

						<div class="adm_input" style="margin-top:3px;">
							<span class="adm_w80">'. $txt['pmx_recent_boards'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxRPH1")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<select class="adm_w90" name="config[settings][recentboards][]" multiple="multiple" size="5">';

		$boards = isset($this->cfg['config']['settings']['recentboards']) ? $this->cfg['config']['settings']['recentboards'] : array();
		foreach($this->smf_boards as $brd)
			echo '
								<option value="'. $brd['id'] .'"'. (in_array($brd['id'], $boards) ? ' selected="selected"' : '') .'>'. $brd['name'] .'</option>';

		echo '
							</select>
							<div id="pmxRPH1" class="info_frame" style="margin-top:5px;">'. $txt['pmx_recent_boards_help'] .'</div>
						</div>

						<div class="adm_input" style="min-height:20px;margin-top:5px;">
							<span class="adm_w80">'. $txt['pmx_recentpostnum'] .'</span>
							<div><input onkeyup="check_numeric(this);" size="2" type="text" name="config[settings][numrecent]" value="' .(isset($this->cfg['config']['settings']['numrecent']) ? $this->cfg['config']['settings']['numrecent'] : '5'). '" /></div>
						</div>
						<div class="adm_check" style="min-height:20px;">
							<span class="adm_w80">'. $txt['pmx_recent_showboard'] .'</span>
							<input type="hidden" name="config[settings][showboard]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][showboard]" value="1"' .(!empty($this->cfg['config']['settings']['showboard']) ? ' checked="checked"' : '') .' /></div>
						</div>';

		if($this->cfg['side'] == 'pages')
			echo '
						<div class="adm_check" style="min-height:20px;">
							<span class="adm_w80">'. $txt['pmx_enable_sitemap'] .'</span>
							<input type="hidden" name="config[show_sitemap]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[show_sitemap]" value="1"' .(!empty($this->cfg['config']['show_sitemap']) ? ' checked="checked"' : ''). ' /></div>
						</div>';

		// return the default classnames
		return $this->block_classdef;
	}
}
?>
