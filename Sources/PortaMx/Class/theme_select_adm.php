<?php
/**
* \file theme_select_adm.php
* Admin Systemblock theme_select
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_theme_select_adm
* Admin Systemblock theme_select_adm
* @see theme_select_adm.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_theme_select_adm extends PortaMxC_SystemAdminBlock
{
	var $smf_themes;

	/**
	* AdmBlock_init().
	* get all available themes
	* Setup caching and and all themes.
	*/
	function pmxc_AdmBlock_init()
	{
		$this->smf_themes = PortaMx_getsmfThemes();		// get all themes
		$this->can_cached = 1;												// enable cache
	}

	/**
	* AdmBlock_settings().
	* Setup the config vars and output the block settings.
	* Returns the css classes they are used.
	*/
	function pmxc_AdmBlock_settings()
	{
		global $context, $txt;

		$RtL = empty($context['right_to_left']) ? 'right' : 'left';

		// define the settings options
		echo '
					<td valign="top" style="padding:4px;">
						<input type="hidden" name="config[settings]" value="" />
						<input type="hidden" name="content" value="" />';

		// show the settings screen
		echo '
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid"><span class="cat_left_title">'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span></h4>
						</div>

						<div class="adm_input">
							<span>'. $txt['pmx_select_themes'] .'</span>
							<img class="info_toggle" align="'. $RtL .'" style="padding:1px 5px;" onclick=\'Show_help("pmxUSH01")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							<select class="adm_w90" name="config[settings][themes][]" size="7" multiple="multiple">';

			foreach($this->smf_themes as $thid => $data)
				echo '
								<option value="'. $thid .'"'. (!empty($this->cfg['config']['settings']['themes']) ? (in_array($thid, $this->cfg['config']['settings']['themes']) && $data['smfenabled'] ? ' selected="selected"' : '') : ($data['smfenabled'] ? ' selected="selected"' : '')) . ($data['smfenabled'] ? '' : ' disabled="disabled"'). '>'. $data['name'] .($data['smfenabled'] ? '' : ' [x]') .'</option>';

		echo '
							</select>
						</div>
						<div id="pmxUSH01" class="info_frame" style="margin-top:5px;">'. $txt['pmx_themes_hint'] .'</div>';

		if($this->cfg['side'] == 'pages')
			echo '
						<div class="adm_check" margin-top:8px;>
							<span class="adm_w85">'. $txt['pmx_enable_sitemap'] .'</span>
							<input type="hidden" name="config[show_sitemap]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[show_sitemap]" value="1"' .(!empty($this->cfg['config']['show_sitemap']) ? ' checked="checked"' : ''). ' /></div>
						</div>';

		// return the default classnames
		return $this->block_classdef;
	}
}
?>
