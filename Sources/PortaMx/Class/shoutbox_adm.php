<?php
/**
* \file shoutbox_adm.php
* Admin Systemblock shoutbox_adm
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_shoutbox_adm
* Admin Systemblock shoutbox_adm
* @see shoutbox_adm.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_shoutbox_adm extends PortaMxC_SystemAdminBlock
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
		global $context, $user_info, $txt;

		// define the settings options
		echo '
					<td valign="top" style="padding:4px;">
						<input type="hidden" name="config[settings]" value="" />
						<input type="hidden" name="content" value="'. base64_encode($this->cfg['content']) .'" />';

		// define numeric vars to check
		echo '
						<input type="hidden" name="check_num_vars[]" value="[config][settings][maxlen], 100" />
						<input type="hidden" name="check_num_vars[]" value="[config][settings][maxshouts], 50" />
						<input type="hidden" name="check_num_vars[]" value="[config][settings][maxheight], 250" />
						<input type="hidden" name="check_num_vars[]" value="[config][settings][scrollspeed], 0" />';

		// show the settings screen
		echo '
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid"><span class="cat_left_title">'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span></h4>
						</div>

						<div class="adm_input">
							<span class="adm_w80">'. $txt['pmx_shoutbox_maxlen'] .'</span>
							<div><input onkeyup="check_numeric(this);" size="4" type="text" name="config[settings][maxlen]" value="' .(isset($this->cfg['config']['settings']['maxlen']) ? $this->cfg['config']['settings']['maxlen'] : '100'). '" /></div>
						</div>
						<div class="adm_input">
							<span class="adm_w80">'. $txt['pmx_shoutbox_maxshouts'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxSBXH01")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<div><input onkeyup="check_numeric(this);" size="4" type="text" name="config[settings][maxshouts]" value="' .(isset($this->cfg['config']['settings']['maxshouts']) ? $this->cfg['config']['settings']['maxshouts'] : '50'). '" /></div>
							<div id="pmxSBXH01" class="info_frame" style="margin-top:2px;">'. $txt['pmx_shoutbox_maxshouthelp'] .'</div>
						</div>
						<div class="adm_input">
							<span class="adm_w80">'. $txt['pmx_shoutbox_maxheight'] .'</span>
							<div><input onkeyup="check_numeric(this);" size="4" type="text" name="config[settings][maxheight]" value="' .(isset($this->cfg['config']['settings']['maxheight']) ? $this->cfg['config']['settings']['maxheight'] : '250'). '" /></div>
						</div>
						<div class="adm_input">
							<span class="adm_w80">'. $txt['pmx_shoutbox_scrollspeed'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxSBXH02")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<div><input onkeyup="check_numeric(this);" size="4" type="text" name="config[settings][scrollspeed]" value="' .(isset($this->cfg['config']['settings']['scrollspeed']) ? $this->cfg['config']['settings']['scrollspeed'] : '0'). '" /></div>
							<div id="pmxSBXH02" class="info_frame" style="margin-top:2px;">'. $txt['pmx_shoutbox_speedhelp'] .'</div>
						</div>
						<div class="adm_check">
							<span class="adm_w80">'. $txt['pmx_shoutbox_collapse'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxSBXH03")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<div><input class="input_check" type="checkbox" name="config[settings][boxcollapse]" value="1"'.(!empty($this->cfg['config']['settings']['boxcollapse']) ? ' checked="checked"' : '') .' /></div>
							<div id="pmxSBXH03" class="info_frame" style="margin-top:2px;">'. $txt['pmx_shoutbox_collapsehelp'] .'</div>
						</div>
						<div class="adm_check">
							<span class="adm_w80">'. $txt['pmx_shoutbox_reverse'] .'</span>
							<div><input onchange="SetReOrder(this)" class="input_check" type="checkbox" name="config[settings][reverse]" value="1"' .(isset($this->cfg['config']['settings']['reverse']) && $this->cfg['config']['settings']['reverse'] == 1 ? ' checked="checked"' : ''). ' /></div>
							<input id="pmx_shout_reorder" type="hidden" name="config[settings][reorder]" value="0" />
							<input id="pmx_shout_reverse_state" type="hidden" name="" value="' .(!empty($this->cfg['config']['settings']['reverse']) ? '1' : '0') .'" />
						</div>
						<div class="adm_check">
							<span class="adm_w80">'. $txt['pmx_shoutbox_allowedit'] .'</span>
							<div><input class="input_check" type="checkbox" name="config[settings][allowedit]" value="1"' .(isset($this->cfg['config']['settings']['allowedit']) && $this->cfg['config']['settings']['allowedit'] == 1 ? ' checked="checked"' : ''). ' /></div>
						</div>';

		if($this->cfg['side'] == 'pages')
			echo '
						<div class="adm_check">
							<span class="adm_w80">'. $txt['pmx_enable_sitemap'] .'</span>
							<input type="hidden" name="config[show_sitemap]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[show_sitemap]" value="1"' .(!empty($this->cfg['config']['show_sitemap']) ? ' checked="checked"' : ''). ' /></div>
						</div>';

		echo '
						<div class="adm_input" style="margin-top:5px;">
							<span>'. $txt['pmx_shoutbox_canshout'] .'</span>
							<input type="hidden" name="config[settings][shout_acs][]" value="" />
							<select class="adm_w90" name="config[settings][shout_acs][]" multiple="multiple" size="5">';

		foreach($this->smf_groups as $grp)
			echo '
								<option value="'. $grp['id'] .'"'. (!empty($this->cfg['config']['settings']['shout_acs']) && in_array($grp['id'], $this->cfg['config']['settings']['shout_acs']) ? ' selected="selected"' : '') .'>'. $grp['name'] .'</option>';
		echo '
							</select>
						</div>
						<script type="text/javascript"><!-- // --><![CDATA[
							function SetReOrder(elm)
							{
								var oldstate = (document.getElementById("pmx_shout_reverse_state").value == "0" ? false : true);
								document.getElementById("pmx_shout_reorder").value = (elm.checked == oldstate ? "0" : "1");
							}
						// ]]></script>';

		// return the default classnames
		return $this->block_classdef;
	}
}
?>
