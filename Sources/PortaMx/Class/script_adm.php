<?php
/**
* \file script_adm.php
* Admin Systemblock script
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_script_adm
* Admin Systemblock script_adm
* @see script_adm.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_script_adm extends PortaMxC_SystemAdminBlock
{
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
						<input type="hidden" name="config[settings]" value="" />
						<div style="height:169px;">';

		// show the settings screen
		echo '
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid"><span class="cat_left_title">'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span></h4>
						</div>

						<div class="adm_check" style="min-height:20px;">
							<span class="adm_w85">'. $txt['pmx_content_print'] .'</span>
							<input type="hidden" name="config[settings][printing]" value="0" />
							<input class="input_check" type="checkbox" name="config[settings][printing]" value="1"' .(!empty($this->cfg['config']['settings']['printing']) ? ' checked="checked"' : ''). ' />
						</div>';

		if($this->cfg['side'] == 'pages')
			echo '
						<div class="adm_check" style="min-height:20px;">
							<span class="adm_w85">'. $txt['pmx_enable_sitemap'] .'</span>
							<input type="hidden" name="config[show_sitemap]" value="0" />
							<input class="input_check" type="checkbox" name="config[show_sitemap]" value="1"' .(!empty($this->cfg['config']['show_sitemap']) ? ' checked="checked"' : ''). ' />
						</div>';

		echo '
						</div>';

		// return the used classnames
		return $this->block_classdef;
	}

	/**
	* AdmBlock_content().
	* Open a textarea, to create or edit the content.
	* Returns the AdmBlock_settings
	*/
	function pmxc_AdmBlock_content()
	{
		global $context, $txt;

		// show the content area
		echo '
					<td valign="top" colspan="2" style="padding:4px;">
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid"><span class="cat_left_title">'. $txt['pmx_edit_content'] .'</span></h4>
						</div>

						<div>', template_control_richedit($context['pmx']['editorID']), '</div>
					</td>
				</tr>
				<tr>';

		// return the default settings
		return $this->pmxc_AdmBlock_settings();
	}
}
?>
