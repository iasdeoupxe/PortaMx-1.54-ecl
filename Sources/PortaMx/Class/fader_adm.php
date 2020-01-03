<?php
/**
* \file fader_adm.php
* Admin Systemblock FADER
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

global $context, $txt;

/**
* @class pmxc_fader_adm
* Admin Systemblock FADER
* @see fader_adm.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_fader_adm extends PortaMxC_SystemAdminBlock
{
	/**
	* AdmBlock_init().
	* Setup caching and classdef.
	*/
	function pmxc_AdmBlock_init()
	{
		$this->block_classdef = PortaMx_getdefaultClass(false);	// default classdef
		$this->can_cached = 1;		// enable caching
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
						<input type="hidden" name="config[settings]" value="" />
						<div style="min-height:169px;">';

		// show the settings screen
		echo '
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid">
								<span class="cat_left_title">&nbsp;'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span>
								<img class="grid_click_image pmxleft" onclick=\'Show_help("pmxFH1")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</h4>
						</div>
						<div id="pmxFH1" class="info_frame" style="margin-top:4px;">'. $txt['pmx_fader_timehelp'] .'</div>

						<div class="adm_input" style="height:20px; margin-top:4px;">
							<span class="adm_w60">'. $txt['pmx_fader_uptime'] .'</span>
							<div><input onkeyup="check_numeric(this, \'.\');" size="6" type="text" name="config[settings][uptime]" value="' .(!empty($this->cfg['config']['settings']['uptime']) ? $this->cfg['config']['settings']['uptime'] : '2.2'). '" />&nbsp;'. $txt['pmx_fader_units'] .'</div>
						</div>
						<div class="adm_input" style="height:20px;">
							<span class="adm_w60">'. $txt['pmx_fader_downtime'] .'</span>
							<div><input onkeyup="check_numeric(this, \'.\');" size="6" type="text" name="config[settings][downtime]" value="' .(!empty($this->cfg['config']['settings']['downtime']) ? $this->cfg['config']['settings']['downtime'] : '1.8'). '" />&nbsp;'. $txt['pmx_fader_units'] .'</div>
						</div>
						<div class="adm_input" style="height:20px;">
							<span class="adm_w60">'. $txt['pmx_fader_holdtime'] .'</span>
							<div><input onkeyup="check_numeric(this, \'.\');" size="6" type="text" name="config[settings][holdtime]" value="' .(!empty($this->cfg['config']['settings']['holdtime']) ? $this->cfg['config']['settings']['holdtime'] : '3.5'). '" />&nbsp;'. $txt['pmx_fader_units'] .'</div>
						</div>
						<div class="adm_input" style="height:20px;">
							<span class="adm_w60">'. $txt['pmx_fader_changetime'] .'</span>
							<div><input onkeyup="check_numeric(this, \'.\');" size="6" type="text" name="config[settings][changetime]" value="' .(!empty($this->cfg['config']['settings']['changetime']) ? $this->cfg['config']['settings']['changetime'] : '0.001'). '" />&nbsp;'. $txt['pmx_fader_units'] .'</div>
						</div>';

		if($this->cfg['side'] == 'pages')
			echo '
						<div class="adm_check" style="min-height:20px;">
							<span class="adm_w60">'. $txt['pmx_enable_sitemap'] .'</span>
							<input type="hidden" name="config[show_sitemap]" value="0" />
							<input style="margin-left:0;margin-right:0;" class="input_check" type="checkbox" name="config[show_sitemap]" value="1"' .(!empty($this->cfg['config']['show_sitemap']) ? ' checked="checked"' : ''). ' />
						</div>';

		echo '
						</div>';

		// return the used classnames
		return $this->block_classdef;
	}

	/**
	* AdmBlock_content().
	* Open a the richtext editor, to create or edit the content.
	* Returns the AdmBlock_settings
	*/
	function pmxc_AdmBlock_content()
	{
		global $context, $txt;

		// show the content area
		echo '
					<td valign="top" colspan="2" style="padding:4px;">
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid">
								<span class="cat_left_title">&nbsp;'. $txt['pmx_fader_content'] .'</span>
								<img class="grid_click_image pmxleft" onclick=\'Show_help("pmxfadeHelp")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</h4>
						</div>
						<div id="pmxfadeHelp" class="info_frame" style="margin-top:4px;">
							'. $txt['pmx_fader_content_help'] .'
							<pre>'. htmlentities($txt['pmx_fader_content_help1'], ENT_NOQUOTES, $context['pmx']['encoding']) .'</pre>
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
