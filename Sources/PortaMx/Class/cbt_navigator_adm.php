<?php
/**
* \file cbt_navigator_adm.php
* Admin Systemblock cbt_navigator (Categorie-Board-Topic)
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_cbt_navigator_adm
* Admin Systemblock cbt_navigator_adm
* @see cbt_navigator_adm.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_cbt_navigator_adm extends PortaMxC_SystemAdminBlock
{
	var $boards;

	/**
	* AdmBlock_init().
	* Setup caching and get boards.
	*/
	function pmxc_AdmBlock_init()
	{
		// get all boards
		$this->boards = PortaMx_getsmfBoards(true);
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
						<input type="hidden" name="check_num_vars[]" value="[config][settings][numrecent], 5" />
						<input type="hidden" name="check_num_vars[]" value="[config][settings][numlen], 20" />';

		// show the settings screen
		echo '
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid"><span class="cat_left_title">'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span></h4>
						</div>

						<div class="adm_input" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_cbtnavnum'] .'</span>
							<div><input onkeyup="check_numeric(this);" size="2" type="text" name="config[settings][numrecent]" value="' .(isset($this->cfg['config']['settings']['numrecent']) ? $this->cfg['config']['settings']['numrecent'] : '5'). '" /></div>
						</div>
						<div class="adm_check" style="height:20px;">
							<input type="hidden" name="config[settings][initexpand]" value="0" />
							<span class="adm_w80">'. $txt['pmx_cbtnavexpand'] .'</span>
							<div><input class="input_check" type="checkbox" name="config[settings][initexpand]" value="1"' .(isset($this->cfg['config']['settings']['initexpand']) && $this->cfg['config']['settings']['initexpand'] == 1 ? ' checked="checked"' : ''). ' /></div>
						</div>
						<div class="adm_check" style="height:20px;">
							<input type="hidden" name="config[settings][initexpandnew]" value="0" />
							<span class="adm_w80">'. $txt['pmx_cbtnavexpandnew'] .'</span>
							<div><input class="input_check" type="checkbox" name="config[settings][initexpandnew]" value="1"' .(isset($this->cfg['config']['settings']['initexpandnew']) && $this->cfg['config']['settings']['initexpandnew'] == 1 ? ' checked="checked"' : ''). ' /></div>
						</div>';

		if($this->cfg['side'] == 'pages')
			echo '
						<div class="adm_check" style="min-height:20px;">
							<span class="adm_w80">'. $txt['pmx_enable_sitemap'] .'</span>
							<input type="hidden" name="config[show_sitemap]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[show_sitemap]" value="1"' .(!empty($this->cfg['config']['show_sitemap']) ? ' checked="checked"' : ''). ' /></div>
						</div>';

		echo '
						<div class="adm_input">
							<span>'. $txt['pmx_cbtnavboards'] .'</span>
							<select class="adm_w90" name="config[settings][recentboards][]" multiple="multiple" size="5">';

		$boards = isset($this->cfg['config']['settings']['recentboards']) ? $this->cfg['config']['settings']['recentboards'] : array();
		foreach($this->boards as $brd)
			echo '
								<option value="'. $brd['id'] .'"'. (in_array($brd['id'], $boards) ? ' selected="selected"' : '') .'>'. $brd['name'] .'</option>';

		echo '
							</select>
						</div>';

		// return the default classnames
		return $this->block_classdef;
	}
}
?>
