<?php
/**
* \file user_login_adm.php
* Admin Systemblock user_login
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_user_login_adm
* Admin Systemblock user_login_adm
* @see user_login_adm.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_user_login_adm extends PortaMxC_SystemAdminBlock
{
	/**
	* AdmBlock_init().
	* Setup caching.
	*/
	function pmxc_AdmBlock_init()
	{
		$this->can_cached = 0;		// enable caching
	}

	/**
	* AdmBlock_settings().
	* Setup the config vars and output the block settings.
	* Returns the css classes they are used.
	*/
	function pmxc_AdmBlock_settings()
	{
		global $context, $txt;

		// define additional classnames and styles
		$used_classdef = $this->block_classdef;
		$used_classdef['hellotext'] = array(
			' '. $txt['pmx_default_none'] => '',
			' smalltext' => 'smalltext',
			' middletext' => 'middletext',
			'+normaltext' => 'normaltext',
			' largetext' => 'largetext',
		);

		// define the settings options
		echo '
					<td valign="top" style="padding:4px;">
						<input type="hidden" name="config[settings]" value="" />';

		// show the settings screen
		echo '
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid"><span class="cat_left_title">'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span></h4>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w90">'. $txt['show_avatar'] .'</span>
							<div><input class="input_check" type="checkbox" name="config[settings][show_avatar]" value="1"' .(isset($this->cfg['config']['settings']['show_avatar']) && $this->cfg['config']['settings']['show_avatar'] == 1 ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w90">'. $txt['show_pm'] .'</span>
							<div><input class="input_check" type="checkbox" name="config[settings][show_pm]" value="1"' .(isset($this->cfg['config']['settings']['show_pm']) && $this->cfg['config']['settings']['show_pm'] == 1 ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w90">'. $txt['show_posts'] .'</span>
							<div><input class="input_check" type="checkbox" name="config[settings][show_posts]" value="1"' .(isset($this->cfg['config']['settings']['show_posts']) && $this->cfg['config']['settings']['show_posts'] == 1 ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w90">'. $txt['show_logtime'] .'</span>
							<div><input class="input_check" type="checkbox" name="config[settings][show_logtime]" value="1"' .(isset($this->cfg['config']['settings']['show_logtime']) && $this->cfg['config']['settings']['show_logtime'] == 1 ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w90">'. $txt['show_time'] .'</span>
							<div><input class="input_check" type="checkbox" name="config[settings][show_time]" value="1"' .(isset($this->cfg['config']['settings']['show_time']) && $this->cfg['config']['settings']['show_time'] == 1 ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w90">'. $txt['show_realtime'] .'</span>
							<div><input id="pmx_rtcEnabled" onclick="Toggle_pmxRTC(this)" class="input_check" type="checkbox" name="config[settings][show_realtime]" value="1"' .(isset($this->cfg['config']['settings']['show_realtime']) && $this->cfg['config']['settings']['show_realtime'] == 1 ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div id="pmx_rtcformat" class="adm_input" style="width:95%; display:none;">
							<span style="width:45%;">'. $txt['pmx_rtcformatstr'] .'
								<img class="info_toggle" onclick="Show_help(\'pmxul_H01\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<input style="margin:-1px 0 4px 0; width:47%;" align="right" type="text" name="config[settings][rtc_format]" value="'. (isset($this->cfg['config']['settings']['rtc_format']) ? $this->cfg['config']['settings']['rtc_format'] : '') .'" />
						</div>
						<div id="pmxul_H01" class="info_frame" style="margin-top:2px">'. $txt['pmx_rtc_formathelp'] .'</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w90">'. $txt['show_unapprove'] .'</span>
							<div><input class="input_check" type="checkbox" name="config[settings][show_unapprove]" value="1"' .(isset($this->cfg['config']['settings']['show_unapprove']) && $this->cfg['config']['settings']['show_unapprove'] == 1 ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w90">'. $txt['show_login'] .'</span>
							<div><input class="input_check" type="checkbox" name="config[settings][show_login]" value="1"' .(isset($this->cfg['config']['settings']['show_login']) && $this->cfg['config']['settings']['show_login'] == 1 ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w90">'. $txt['show_langsel'] .'</span>
							<div><input class="input_check" type="checkbox" name="config[settings][show_langsel]" value="1"' .(isset($this->cfg['config']['settings']['show_langsel']) && $this->cfg['config']['settings']['show_langsel'] == 1 ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w90">'. $txt['show_logout'] .'</span>
							<div><input class="input_check" type="checkbox" name="config[settings][show_logout]" value="1"' .(isset($this->cfg['config']['settings']['show_logout']) && $this->cfg['config']['settings']['show_logout'] == 1 ? ' checked="checked"' : ''). ' /></div>
						</div>';

		if($this->cfg['side'] == 'pages')
			echo '
						<div class="adm_check">
							<span class="adm_w90">'. $txt['pmx_enable_sitemap'] .'</span>
							<input type="hidden" name="config[show_sitemap]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[show_sitemap]" value="1"' .(!empty($this->cfg['config']['show_sitemap']) ? ' checked="checked"' : ''). ' /></div>
						</div>';

		echo '
						<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
							function Toggle_pmxRTC(elm)
							{
								if(elm.checked == true)
									document.getElementById("pmx_rtcformat").style.display = "";
								else
									document.getElementById("pmx_rtcformat").style.display = "none";
							}
							Toggle_pmxRTC(document.getElementById("pmx_rtcEnabled"));
						// ]]></script>';

		// return the classnames to use
		return $used_classdef;
	}
}
?>
