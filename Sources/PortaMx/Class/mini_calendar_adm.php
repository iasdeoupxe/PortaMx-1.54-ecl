<?php
/**
* \file mini_calendar_adm.php
* Admin Systemblock mini_calendar
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_mini_calendar_adm
* Admin Systemblock mini_calendar_adm
* @see mini_calendar_adm.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_mini_calendar_adm extends PortaMxC_SystemAdminBlock
{
	/**
	* AdmBlock_init().
	* Setup caching.
	*/
	function pmxc_AdmBlock_init()
	{
		$this->can_cached = 1;		// enable caching
	}

	/**
	* AdmBlock_settings().
	* Setup the config vars and output the block settings.
	* Returns the css classes they are used.
	*/
	function pmxc_AdmBlock_settings()
	{
		global $context, $modSettings, $txt;

		// define the settings options
		echo '
					<td valign="top" style="padding:4px;">
						<input type="hidden" name="config[settings]" value="" />';

		// show the settings screen
		echo '
						<div class="cat_bar catbg_grid grid_padd">
							<h4 class="catbg catbg_grid"><span class="cat_left_title">'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span></h4>
						</div>

						<div style="width:93%;">
							<span>'. $txt['pmx_minical_firstday'] .'</span>
							<div style="float:'. (empty($context['right_to_left']) ? 'right' : 'left') .';">
								<select name="config[settings][firstday]" size="1">';

		foreach($txt['pmx_minical_firstdays'] as $dayid => $dayname)
			echo '
									<option value="'. $dayid .'"'. (isset($this->cfg['config']['settings']['firstday']) && $this->cfg['config']['settings']['firstday'] == $dayid ? ' selected="selected"' : '') .'>'. $dayname.'</option>';

		echo '
								</select>
							</div>
						</div>

						<div class="adm_check" style="padding-top:10px;">
							<span class="adm_w90">'. $txt['pmx_minical_birthdays'] .'</span>
							<div>
								<input type="hidden" name="config[settings][birthdays][show]" value="0" />
								<input class="input_check" type="checkbox" name="config[settings][birthdays][show]" value="1"' .(!empty($this->cfg['config']['settings']['birthdays']['show']) ? ' checked="checked"' : ''). ' />
							</div>
						</div>
						<div style="width:93%;" class="adm_input">
							'. $txt['pmx_minical_bdays_before'] .'
							<input class="input_text" size="2" type="text" name="config[settings][birthdays][before]" value="'. (isset($this->cfg['config']['settings']['birthdays']['before']) ? $this->cfg['config']['settings']['birthdays']['before'] : '') .'" />
							<div style="float:'. (empty($context['right_to_left']) ? 'right' : 'left') .';">
								'. $txt['pmx_minical_bdays_after'] .'
								<input class="input_text" size="2" type="text" name="config[settings][birthdays][after]" value="'. (isset($this->cfg['config']['settings']['birthdays']['after']) ? $this->cfg['config']['settings']['birthdays']['after'] : '') .'" />
							</div>
						</div>

						<div class="adm_check" style="padding-top:10px;">
							<span class="adm_w90">'. $txt['pmx_minical_holidays'] .'</span>
							<div>
								<input type="hidden" name="config[settings][holidays][show]" value="0" />
								<input class="input_check" type="checkbox" name="config[settings][holidays][show]" value="1"' .(!empty($this->cfg['config']['settings']['holidays']['show']) ? ' checked="checked"' : ''). ' />
							</div>
						</div>
						<div style="width:93%;" class="adm_input">
							'. $txt['pmx_minical_bdays_before'] .'
							<input class="input_text" size="2" type="text" name="config[settings][holidays][before]" value="'. (isset($this->cfg['config']['settings']['holidays']['before']) ? $this->cfg['config']['settings']['holidays']['before'] : '') .'" />
							<div style="float:'. (empty($context['right_to_left']) ? 'right' : 'left') .';">
								'. $txt['pmx_minical_bdays_after'] .'
								<input class="input_text" size="2" type="text" name="config[settings][holidays][after]" value="'. (isset($this->cfg['config']['settings']['holidays']['after']) ? $this->cfg['config']['settings']['holidays']['after'] : '') .'" />
							</div>
						</div>

						<div class="adm_check" style="padding-top:10px;">
							<span class="adm_w90">'. $txt['pmx_minical_events'] .'</span>
							<div>
								<input type="hidden" name="config[settings][events][show]" value="0" />
								<input class="input_check" type="checkbox" name="config[settings][events][show]" value="1"' .(!empty($this->cfg['config']['settings']['events']['show']) ? ' checked="checked"' : ''). ' />
							</div>
						</div>
						<div style="width:93%;" class="adm_input">
							'. $txt['pmx_minical_bdays_before'] .'
							<input class="input_text" size="2" type="text" name="config[settings][events][before]" value="'. (isset($this->cfg['config']['settings']['events']['before']) ? $this->cfg['config']['settings']['events']['before'] : '') .'" />
							<div style="float:'. (empty($context['right_to_left']) ? 'right' : 'left') .';">
								'. $txt['pmx_minical_bdays_after'] .'
								<input class="input_text" size="2" type="text" name="config[settings][events][after]" value="'. (isset($this->cfg['config']['settings']['events']['after']) ? $this->cfg['config']['settings']['events']['after'] : '') .'" />
							</div>
						</div>';

		if($this->cfg['side'] == 'pages')
			echo '
						<div class="adm_check" style="padding-top:10px;">
							<span class="adm_w90">'. $txt['pmx_enable_sitemap'] .'</span>
							<div>
								<input type="hidden" name="config[show_sitemap]" value="0" />
								<input class="input_check" type="checkbox" name="config[show_sitemap]" value="1"' .(!empty($this->cfg['config']['show_sitemap']) ? ' checked="checked"' : ''). ' />
							</div>
						</div>';

		// return the classnames to use
		return $this->block_classdef;
	}
}
?>
