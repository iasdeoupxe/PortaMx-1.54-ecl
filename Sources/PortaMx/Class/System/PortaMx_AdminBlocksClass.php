<?php
/**
* \file PortaMx_AdminBlocksClass.php
* Global Blocks Admin class
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2015 by PortaMx corp. - http://portamx.com
* \version 1.54
* \date 18.11.2015
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class PortaMxC_AdminBlocks
* The Global Class for Block Administration.
* @see PortaMx_AdminBlocksClass.php
* \author Copyright by PortaMx - http://portamx.com
*/
class PortaMxC_AdminBlocks
{
	var $cfg;						///< common config
	var $cache_time;		///< block cache time

	/**
	* The Contructor.
	* Saved the config, load the block css if exist.
	* Have the block a css file, the class definition is extracted from ccs header
	*/
	function __construct($blockconfig)
	{
		global $context, $settings;

		// get the block config array
		if(isset($blockconfig['config']))
			$blockconfig['config'] = unserialize($blockconfig['config']);
		$this->cfg = $blockconfig;

		// get the cache time if exist
		if(isset($context['pmx']['cache']['blocks'][$this->cfg['blocktype']]['time']))
			$this->cache_time = $context['pmx']['cache']['blocks'][$this->cfg['blocktype']]['time'];
	}

	/**
	* check the extend options.
	* returns true if any found
	**/
	function getExtoptions()
	{
		$extOpts = false;
		if(!empty($this->cfg['config']['ext_opts']))
		{
			foreach($this->cfg['config']['ext_opts'] as $k => $v)
				$extOpts = (isset($v) && !empty($v) ? true : $extOpts);
		}
		if($this->cfg['side'] == 'front')
			$extOpts = isset($this->cfg['config']['frontplace']) && ($this->cfg['config']['frontplace'] == 'before' || $this->cfg['config']['frontplace'] == 'after') ? true : $extOpts;
		elseif($this->cfg['side'] == 'pages')
			$extOpts = !empty($this->cfg['config']['frontmode']) ? true : $extOpts;
		else
			$extOpts = !empty($this->cfg['config']['frontview']) ? true : $extOpts;

		return $extOpts;
	}

	/**
	* Get Config data (name=value format)
	* return result array
	*/
	function getConfigData($itemstr = '')
	{
		$item = Pmx_StrToArray($itemstr);
		$result = array();

		$ptr = &$this->cfg;
		foreach($item as $key)
			$ptr = &$ptr[$key];

		if(isset($ptr))
		{
			if(is_array($ptr))
			{
				foreach($ptr as $val)
				{
					$tmp = Pmx_StrToArray($val, '=');
					if(isset($tmp[0]) && isset($tmp[1]))
						$result[$tmp[0]] = $tmp[1];
				}
			}
			else
				$result = $ptr;
		}
		return $result;
	}
}

/**
* @class PortaMxC_SystemAdminBlock
* This is the Global Admin class to create or edit a single Block.
* This class prepare the settings screen and call the user dependent settings and content classes.
* @see PortaMx_AdminBlocksClass.php
* \author Copyright by PortaMx - http://portamx.com
*/
class PortaMxC_SystemAdminBlock extends PortaMxC_AdminBlocks
{
	var $smf_themes;				///< all Themes
	var $blockcssfiles;			///< all block cssfiles
	var $smf_groups;				///< all usergroups
	var $smf_boards;				///< all smf boards
	var $register_blocks;		///< all registered blocks
	var $block_classdef;		///< all default classes
	var $can_cached;				///< block can cached (1), not cached (0)
	var $title_icons;				///< array with title icons
	var $custom_css;				///< custom css definitions

	/**
	* Output the Block config screen
	*/
	function pmxc_ShowAdmBlockConfig()
	{
		global $context, $settings, $modSettings, $options, $txt;

		// directions
		$isIE = $context['browser']['is_ie'];
		$LtR = empty($context['right_to_left']) ? 'left' : 'right';
		$RtL = empty($context['right_to_left']) ? 'right' : 'left';
		$floatL = ' style="float:' .(empty($context['right_to_left']) ? 'left' : 'right'). ';"';
		$noCache = empty($modSettings['cache_enable']) && !empty($modSettings);

		echo '
				<tr>
					<td valign="top">
						<div class="windowbg" style="margin-top:-3px;">
						<span class="topslice"><span></span></span>
						<table width="100%" cellspacing="1" cellpadding="1">
							<tr>
								<td valign="top" width="50%" style="padding:4px;">
									<input type="hidden" name="id" value="'. $this->cfg['id'] .'" />
									<input type="hidden" name="pos" value="'. $this->cfg['pos'] .'" />
									<input type="hidden" name="side" value="'. $this->cfg['side'] .'" />
									<input type="hidden" name="active" value="'. $this->cfg['active'] .'" />
									<input type="hidden" name="cache" value="'. $this->cfg['cache'] .'" />
									<input type="hidden" name="contenttype" value="'. ($this->cfg['blocktype'] == 'download' ? 'bbc_script' : $this->cfg['blocktype']) .'" />
									<input type="hidden" name="check_num_vars[]" value="[config][maxheight], \'\'" />
									<div style="height:50px;">
										<div style="float:'. $LtR .';width:110px; padding-top:1px;">'. $txt['pmx_edit_title'] .'</div>';

		// all titles depend on language
		$curlang = '';
		foreach($context['pmx']['languages'] as $lang => $sel)
		{
			$curlang = !empty($sel) ? $lang : $curlang;
			echo '
										<span id="'. $lang .'" style="white-space:nowrap;'. (!empty($sel) ? '' : ' display:none;') .'">
											<input style="width:60%;" type="text" name="config[title]['. $lang .']" value="'. (isset($this->cfg['config']['title'][$lang]) ? htmlspecialchars($this->cfg['config']['title'][$lang], ENT_QUOTES) : '') .'" />
										</span>';
		}

		echo '
										<input id="curlang" type="hidden" name="" value="'. $curlang .'" />
										<div style="clear:both; height:10px;">
											<img style="float:'. $LtR .';" src="'. $context['pmx_imageurl'] .'arrow_down.gif" alt="*" title="" />
										</div>
										<div style="float:'. $LtR .'; width:110px;">'. $txt['pmx_edit_title_lang'] .'
											<img class="info_toggle" onclick=\'Show_help("pmxBH01")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
										</div>
										<select style="float:'. $LtR .'; width:26%;" size="1" name="" onchange="setTitleLang(this)">';

		foreach($context['pmx']['languages'] as $lang => $sel)
			echo '
											<option value="'. $lang .'"' .(!empty($sel) ? ' selected="selected"' : '') .'>'. $lang .'</option>';

		echo '
										</select>
										<div style="float:'. $LtR .'; padding-' . $LtR .':15px;"><span style="vertical-align:6px;">'. $txt['pmx_edit_title_align'] .'</span>';

		// title align
		if(!isset($this->cfg['config']['title_align']))
			$this->cfg['config']['title_align'] = 'left';

		echo '
											<input type="hidden" id="titlealign" name="config[title_align]" value="'. $this->cfg['config']['title_align'] .'" />';

		foreach($txt['pmx_edit_title_align_types'] as $key => $val)
			echo '
											<img id="img'. $key .'" src="'. $context['pmx_imageurl'] .'text_align_'. $key .'.gif" alt="*" title="'. $txt['pmx_edit_title_helpalign']. $val .'" style="cursor:pointer;'.($this->cfg['config']['title_align'] == $key ? ' background-color:#e02000;' : '').'" onclick="setAlign(\'\', \''. $key .'\')" />';

		echo '
										</div>
										<div class="adm_clear" style="line-height:1px;">&nbsp;</div>
									</div>
									<div id="pmxBH01" style="margin-top:5px;" class="info_frame">'.
										$txt['pmx_edit_titlehelp'] .'
									</div>';

		// Title icon
		echo '
									<div class="adm_clear">
										<div style="float:'. $LtR .';width:110px; padding-top:5px;">'. $txt['pmx_edit_titleicon'] .'</div>
										<select id="init_ttlicon" style="width:55%; margin-top:5px;" size="1" name="config[title_icon]" onchange="setTitleIcon(this, \'\');">
											<option value=""' .(empty($this->cfg['config']['title_icon']) ? ' selected="selected"' : '') .'>'. $txt['pmx_edit_no_icon'] .'</option>';

		foreach($this->title_icons as $name)
			echo '
											<option value="'. $name .'"' .(!empty($this->cfg['config']['title_icon']) && $this->cfg['config']['title_icon'] == $name ? ' selected="selected"' : '') .'>'. $name .'</option>';
		echo '
										</select>
										<img src="'. $context['pmx_imageurl'] .'empty.gif" width="5" alt="*" title="" />
										<img style="padding-top:7px; vertical-align:top;" id="pmxttlicon" src="'. $context['pmx_Iconsurl'] .'none.gif" alt="" title="" />
										<script type="text/javascript"><!-- // --><![CDATA[
											setTitleIcon(document.getElementById("init_ttlicon"), \'\');
										// ]]></script>
									</div>';

		// show registered block types
		echo '
								</td>
								<td valign="top" style="padding:4px;">
									<div style="height:50px;">
										<div style="float:'. $LtR .'; width:130px; padding-top:2px;">'. $txt['pmx_edit_type'] .'</div>';

		if(allowPmx('pmx_admin'))
		{
			echo '
										<select id="pmx.check.type" style="width:60%;" size="1" name="blocktype" onchange="FormFunc(\'chg_blocktype\', 1)">';

			foreach($this->register_blocks as $blocktype => $blockDesc)
				echo '
											<option value="'. $blocktype .'"' .($this->cfg['blocktype'] == $blocktype ? ' selected="selected"' : '') .'>'. $blockDesc['description'] .'</option>';
			echo '
										</select>
										<input id="cache_value" type="hidden" name="cache" value="0" />
										<div style="clear:both; line-height:7px;">&nbsp;</div>';
		}
		else
			echo '
										<input type="hidden" name="blocktype" value="'. $this->cfg['blocktype'] .'" />
										<input style="width:50%;" type="text" value="'. $this->cfg['blocktype'] .'" disabled="disabled" />';

		// cache settings
		if(!empty($this->can_cached) && empty($noCache))
		{
			echo '
										<div style="float:'. $LtR .'; width:127px; padding-top:5px;">'. $txt['pmx_edit_cache'] .'</div>';

			if(in_array($this->cfg['blocktype'], array_keys($context['pmx']['cache']['blocks'])))
				echo '
										<input style="float:'. $LtR .'; margin-top:'.($isIE ? '5' : '7').'px;" id="cacheflag" class="input_check" type="checkbox" name="cacheflag" onclick="checkPmxCache(this, '. $this->cache_time .')" value="1"'. (!empty($this->cfg['cache']) ? ' checked="checked"' : ''). ' />
										<div style="float:'. $LtR .'; margin-'. $LtR .':15px; padding-top:5px;">'. $txt['pmx_edit_cachetime'] .'</div>
										<input style="float:'. $LtR .'; margin-top:3px; margin-'. $LtR .':6px;'. (empty($this->cfg['cache']) ? 'background-color:#8898b0;' : '') .'" onkeyup="check_numeric(this)" id="cacheval" type="text" name="cache" value="'.(empty($this->cfg['cache']) ? '0"' : (empty($this->cfg['cache']) ? $this->cache_time : $this->cfg['cache'])) .'" size="7" />
										<div class="smalltext" style="float:'. $LtR .'; margin-'. $LtR .':3px; padding-top:5px;">'. $txt['pmx_edit_cachetimesec'];

			echo '
											<img class="info_toggle" onclick=\'Show_help("pmxBH02")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
										</div>
										<div style="clear:both; line-height:4px; margin-top:-4px;">&nbsp;</div>
									</div>';

			if(in_array($this->cfg['blocktype'], array_keys($context['pmx']['cache']['blocks'])))
				echo '
									<div id="pmxBH02" class="info_frame" style="margin-top:5px">'. $txt['pmx_edit_pmxcachehelp'] .'</div>';
		}
		else
		{
			echo '
									<div style="float:'. $LtR .'; margin-top:5px; height:20px;">';

			if(empty($this->can_cached))
				echo '
										'. $txt['pmx_edit_nocachehelp'];
			elseif(!empty($noCache))
				echo '
										'. $txt['pmx_edit_noSMFcache'];
			echo '
									</div>';
		}

			// Pagename
		if($this->cfg['side'] == 'pages')
			echo '
									<div class="adm_clear">
										<div class="adm_clear" style="float:'. $LtR .';width:130px; padding-top:7px;">'. $txt['pmx_edit_pagename'] .'
											<img class="info_toggle" onclick=\'Show_help("pmxBH11")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
										</div>
										<input style="width:60%; margin-top:5px;" onkeyup="check_pagename(this)" type="text" name="config[pagename]" value="'. (!empty($this->cfg['config']['pagename']) ? $this->cfg['config']['pagename'] : '') .'" />
									</div>

									<div id="pmxBH11" class="info_frame" style="margin-top:5px;">'.
										$txt['pmx_edit_pagenamehelp'] .'
									</div>';
		else
			echo '
									<input type="hidden" name="config[pagename]" value="'. (!empty($this->cfg['config']['pagename']) ? $this->cfg['config']['pagename'] : '') .'" />';

		echo '
								</td>
							</tr>
							<tr>';

		/**
		* Call the block depended settings.
		* Because each block can have his own settings, we have to call the settings now.
		*/
		$usedClass = $this->pmxc_AdmBlock_content();

		// the group access
		echo '
									<div style="clear:both; height:6px;"></div>
									<div class="cat_bar catbg_grid">
										<h4 class="catbg catbg_grid">
											<span class="cat_msg_title">'. $txt['pmx_edit_groups'] .'</span>
											<img class="grid_click_image pmxleft" align="'. $RtL .'" onclick=\'Show_help("pmxBH03")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
										</h4>
									</div>
									<select id="pmxgroups" onchange="changed(\'pmxgroups\');" style="margin:5px 0px; width:90%;" name="acsgrp[]" multiple="multiple" size="5">';

		if(!empty($this->cfg['acsgrp']))
			list($grpacs, $denyacs) = Pmx_StrToArray($this->cfg['acsgrp'], ',', '=');
		else
			$grpacs = $denyacs = array();

		foreach($this->smf_groups as $grp)
			echo '
										<option value="'. $grp['id'] .'='. intval(!in_array($grp['id'], $denyacs)) .'"'. (in_array($grp['id'], $grpacs) ? ' selected="selected"' : '') .'>'. (in_array($grp['id'], $denyacs) ? '^' : '') . $grp['name'] .'</option>';

		echo '
									</select>
									<div id="pmxBH03" class="info_frame">'. $txt['pmx_edit_groups_help'] .'</div>
									<script type="text/javascript"><!-- // --><![CDATA[
										var pmxgroups = new MultiSelect("pmxgroups");
									// ]]></script>';

		// Block moderate
		if(!isset($this->cfg['config']['can_moderate']))
			$this->cfg['config']['can_moderate'] = (allowPmx('pmx_admin') ? 0 : 1);

		if(allowPmx('pmx_blocks', true))
			echo '
									<input type="hidden" name="config[can_moderate]" value="'. $this->cfg['config']['can_moderate'] .'" />';
		else
			echo '
									<div style="height:4px;"></div>
									<div class="cat_bar catbg_grid grid_padd">
										<h4 class="catbg catbg_grid">
											<span class="cat_msg_title">'. $txt['pmx_block_moderate_title'] .'</span>
											<img class="grid_click_image pmxleft" onclick=\'Show_help("pmxBModHelp")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
											</h4>
									</div>
									<div class="adm_check">
										<span class="adm_w85">'. $txt['pmx_block_moderate'] .'</span>
										<input type="hidden" name="config[can_moderate]" value="0" />
										<div><input class="input_check" type="checkbox" name="config[can_moderate]" value="1"' .(!empty($this->cfg['config']['can_moderate']) ? ' checked="checked"' : ''). ' /></div>
									</div>
									<div id="pmxBModHelp" class="info_frame" style="margin-top:4px; margin-bottom:0px;">'. $txt['pmx_block_moderatehelp'] .'</div>
								</td>';

		// the visual options
		echo '
								<td valign="top" style="padding:4px;">
									<div class="cat_bar catbg_grid grid_padd">
										<h4 class="catbg catbg_grid"><span class="cat_left_title">'. $txt['pmx_edit_visuals'] .'</span></h4>
									</div>
									<div style="float:'. $LtR .'; height:24px; width:177px;">'. $txt['pmx_edit_cancollapse'] .'</div>
									<input style="padding-'. $LtR .':141px;" type="hidden" name="config[collapse]" value="0" />
									<input class="input_check" id="collapse" type="checkbox" name="config[collapse]" value="1"'. ($this->cfg['config']['visuals']['header'] == 'none' ? ' disabled="disabled"' : ($this->cfg['config']['collapse'] == 1 ? ' checked="checked"' : '')) .' />
									<div style="clear:both;" /></div>
									<div style="float:'. $LtR .'; height:24px; width:180px;">'. $txt['pmx_edit_collapse_state'] .'</div>
									<select style="width:46%;" size="1" name="config[collapse_state]">';

		foreach($txt['pmx_collapse_mode'] as $key => $text)
			echo '
										<option value="'. $key .'"'. (isset($this->cfg['config']['collapse_state']) && $this->cfg['config']['collapse_state'] == $key ? ' selected="selected"' : '') .'>'. $text .'</option>';
		echo '
									</select>
									<br style="clear:both;" />
									<div style="float:'. $LtR .'; height:24px; width:180px;">'. $txt['pmx_edit_overflow'] .'</div>
									<select style="width:46%;" size="1" id="mxhgt" name="config[overflow]" onchange="checkMaxHeight(this);">';

		foreach($txt['pmx_overflow_actions'] as $key => $text)
			echo '
										<option value="'. $key .'"'. (isset($this->cfg['config']['overflow']) && $this->cfg['config']['overflow'] == $key ? ' selected="selected"' : '') .'>'. $text .'</option>';
		echo '
									</select>
									<br style="clear:both;" />
									<div style="float:'. $LtR .'; min-height:24px; width:99%;">
										<div style="float:'. $LtR .'; min-height:24px; width:180px;">'. $txt['pmx_edit_height'] .'</div>
										<input onkeyup="check_numeric(this)" id="maxheight" type="text" size="4" name="config[maxheight]" value="'. (isset($this->cfg['config']['maxheight']) ? $this->cfg['config']['maxheight'] : '') .'"'. (!isset($this->cfg['config']['overflow']) || empty($this->cfg['config']['overflow']) ? ' disabled="disabled"' : '') .' /><span class="smalltext">'. $txt['pmx_pixel'] .'</span><span style="display:inline-block; width:10px;"></span>
										<select id="maxheight_sel" style="width:25%;" size="1" name="config[height]">';

		foreach($txt['pmx_edit_height_mode'] as $key => $text)
			echo '
											<option value="'. $key .'"'. (isset($this->cfg['config']['height']) && $this->cfg['config']['height'] == $key ? ' selected="selected"' : '') .'>'. $text .'</option>';
		echo '
										</select>
									</div>
									<br style="clear:both;" />
									<script type="text/javascript"><!-- // --><![CDATA[
										checkMaxHeight(document.getElementById("mxhgt"));
									// ]]></script>

									<div style="float:'. $LtR .'; height:24px; width:180px;">'. $txt['pmx_edit_innerpad'] .'</div>
									<input onkeyup="check_numeric(this, \',\')" type="text" size="4" name="config[innerpad]" value="'. (isset($this->cfg['config']['innerpad']) ? $this->cfg['config']['innerpad'] : '4') .'" /><span class="smalltext">'. $txt['pmx_pixel'] .' (xy/y,x)</span>
									<br style="clear:both;" />';

		// CSS class settings
		echo '
									<div class="cat_bar catbg_grid  grid_padd">
										<h4 class="catbg catbg_grid grid_botpad">
											<div style="float:'. $LtR .'; width:180px;"><span class="cat_left_title">'. $txt['pmx_edit_usedclass_type'] .'</span></div>
											<span class="cat_left_title">'. $txt['pmx_edit_usedclass_style'] .'</span>
										</h4>
									</div>
									<div style="margin:0px 2px;">';

		// write out the classes
		foreach($usedClass as $ucltyp => $ucldata)
		{
			echo '
										<div style="float:'. $LtR .'; width:180px; height:22px; padding-top:2px;">'. $ucltyp .'</div>
										<select style="width:46%;" name="config[visuals]['. $ucltyp .']" onchange="checkCollapse(this)">';

			foreach($ucldata as $cname => $class)
					echo '
											<option value="'. $class .'"'. (isset($this->cfg['config']['visuals'][$ucltyp]) ? ($this->cfg['config']['visuals'][$ucltyp] == $class ? ' selected="selected"' : '') : (substr($cname,0,1) == '+' ? ' selected="selected"' : '')) .'>'. substr($cname, 1) .'</option>';
			echo '
										</select>
										<br style="clear:both;" />';
		}
		echo '
									</div>
									<div class="cat_bar catbg_grid grid_padd">
										<h4 class="catbg catbg_grid"><span class="cat_left_title">'. $txt['pmx_edit_canhavecssfile'] .'</span></h4>
									</div>
									<div style="float:'. $LtR .'; margin:0px 2px; width:176px;">'. $txt['pmx_edit_cssfilename'] .'</div>
									<select id="sel.css.file" style="width:46%;margin-bottom:2px;" name="config[cssfile]" onchange="pmxChangeCSS(this)">
										<option value=""></option>';

		// custon css files exist ?
		if(!empty($this->custom_css))
		{
			// write out custom mpt/css definitions
			foreach($this->custom_css as $custcss)
			{
				if(is_array($custcss))
					echo '
										<option value="'. $custcss['file'] .'"'. ($this->cfg['config']['cssfile'] == $custcss['file'] ? ' selected="selected"' : '') .'>'. $custcss['file'] .'</option>';
			}
			echo '
									</select>
									<div style="clear:both; height:6px;"></div>';

			// write out all class definitions (hidden)
			foreach($this->custom_css as $custcss)
			{
				if(is_array($custcss))
				{
					echo '
									<div id="'. $custcss['file'] .'" style="display:none;">';

					foreach($custcss['class'] as $key => $val)
					{
						if(in_array($key, array_keys($usedClass)))
							echo '
										<div style="float:'. $LtR .'; width:180px; padding:0 2px;">'. $key .'</div>'. (empty($val) ? sprintf($txt['pmx_edit_nocss_class'], $settings['theme_id']) : $val) .'<br />';
					}

					echo '
									</div>';
				}
			}
			echo '
									<script type="text/javascript"><!-- // --><![CDATA[
										var elm = document.getElementById("sel.css.file");
										var fname = elm.options[elm.selectedIndex].value;
										if(document.getElementById(fname))
											document.getElementById(fname).style.display = "";
										function pmxChangeCSS(elm)
										{
											for(i=0; i<elm.length; i++)
											{
												if(document.getElementById(elm.options[i].value))
													document.getElementById(elm.options[i].value).style.display = "none";
											}
											var fname = elm.options[elm.selectedIndex].value;
											if(document.getElementById(fname))
												document.getElementById(fname).style.display = "";
										}
									// ]]></script>';
		}
		else
			echo '
									</select>
									<div style="clear:both; height:6px;"></div>';

		echo '
								</td>
							</tr>
							<tr>
								<td colspan="2" valign="top" align="center" style="padding:4px 4px 0 4px;"><hr />
									<input class="button_submit" type="button" style="margin-right:10px;" value="'. $txt['pmx_save_exit'] .'" name="" onclick="FormFunc(\'save_edit\', \'1\')" />
									<input class="button_submit" type="button" style="margin-right:10px;" value="'. $txt['pmx_save_cont'] .'" name="" onclick="FormFunc(\'save_edit_continue\', \'1\')" />
									<input class="button_submit" type="button" value="'. $txt['pmx_cancel'] .'" name="" onclick="FormFunc(\'cancel_edit\', \'1\')" />
								</td>
							</tr>';

		// the dynamic visibility options
		$extOpts = $this->getExtoptions() || !empty($this->cfg['config']['sd_standalone']);
		if(!empty($extOpts))
			$options['collapse_visual'] = 0;
		elseif(empty($options['collapse_visual']) && !empty($context['pmx']['settings']['manager']['collape_visibility']))
			$options['collapse_visual'] = 1;

		echo '
							<tr>
								<td valign="top" colspan="2" style="padding:8px 4px 0 4px;">
									<div class="cat_bar">
										<h3';

		if($context['pmx']['settings']['shrinkimages'] == 2)
			echo ' ondblclick="PmxBlock_Toggle(this, \'upshrinkVis\', \''. $txt['pmx_collapse'] .'\', \''. $txt['pmx_expand'] .'\')" title="'. (empty($options['collapse_visual']) ? $txt['pmx_collapse'] : $txt['pmx_expand']) . $txt['pmx_edit_ext_opts'] .'"';

		echo ' class="catbg">';

		if($context['pmx']['settings']['shrinkimages'] != 2)
			echo '
											<img id="upshrinkImgVisual" class="ce_images pmxleft" src="'. (empty($options['collapse_visual']) ? $context['pmx_img_expand'] : $context['pmx_img_colapse']) .'" alt="*" title="'. (empty($options['collapse_visual']) ? $txt['pmx_collapse'] : $txt['pmx_expand']) . $txt['pmx_edit_ext_opts'] .'" />';

		echo '
											<span class="pmxtitle pmxcenter pmxadj_right"><span>'. $txt['pmx_edit_ext_opts'] .'</span></span>
										</h3>
									</div>

									<div id="upshrinkVisual1"' .(empty($options['collapse_visual']) ? '' : ' style="display:none;"') .'>
										<div class="info_border" style="margin-top:5px;">
											<span><img onclick=\'Toggle_help("pmxBH05")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" /></span>
											<div>'. $txt['pmx_edit_ext_opts_help'] .'</div>
											<div id="pmxBH05" style="margin-top:-16px; display:none;">'. $txt['pmx_edit_ext_opts_morehelp'] .'</div>
										</div>
									</div>
								</td>
							</tr>
							<tr id="upshrinkVisual"' .(empty($options['collapse_visual']) ? '' : ' style="display:none;"'). '>';

		// on default actions
		echo '
								<td valign="top" style="padding:4px;">
									<div class="cat_bar catbg_grid">
										<h4 class="catbg catbg_grid">
											<span class="cat_msg_title">'. $txt['pmx_edit_ext_opts_action'] .'</span>
											<img class="grid_click_image pmxleft" onclick=\'Show_help("pmxBH06")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
										</h4>
									</div>
									<select id="pmxact" onchange="changed(\'pmxact\');" style="margin:5px 0px;width:90%;" name="config[ext_opts][pmxact][]" multiple="multiple" size="12">';

		// get config data
		$data = $this->getConfigData('config, ext_opts, pmxact');
		foreach($txt['pmx_action_names'] as $act => $actdesc)
			echo '
										<option value="'. $act .'='. (array_key_exists($act, $data) ? $data[$act] .'" selected="selected' : '1') .'">'. (array_key_exists($act, $data) ? ($data[$act] == 0 ? '^' : '') : '') . $actdesc .'</option>';

		echo '
									</select>
									<div id="pmxBH06" class="info_frame">'. $txt['pmx_edit_ext_opts_selnote'] .'</div>
									<script type="text/javascript"><!-- // --><![CDATA[
										var pmxact = new MultiSelect("pmxact");
									// ]]></script>
								</td>';

		// custom action
		echo '
								<td valign="top" style="padding:4px;">
									<div class="cat_bar catbg_grid grid_padd">
										<h4 class="catbg catbg_grid">
											<span class="cat_msg_title">'. $txt['pmx_edit_ext_opts_custaction'] .'</span>
											<img class="grid_click_image pmxleft" onclick=\'Show_help("pmxBH07")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
										</h4>
									</div>
									<textarea class="adm_textarea" style="width:90%;margin-top:5px;" rows="2" name="config[ext_opts][pmxcust]">'. $this->cfg['config']['ext_opts']['pmxcust'] .'</textarea>
									<div id="pmxBH07" class="info_frame" style="margin-top:5px;">'. $txt['pmx_edit_ext_opts_custhelp'] .'</div>';

		// SD standalone and Maintenance
		echo '
									<div class="adm_check" style="margin-top:2px">
										<span class="adm_w85">'. $txt['pmx_edit_ext_SD_standalone'] .'</span>
										<input type="hidden" name="config[sd_standalone]" value="0" />
										<input class="input_check" type="checkbox" name="config[sd_standalone]" value="1"' .(!empty($this->cfg['config']['sd_standalone']) ? ' checked="checked"' : ''). ' />
									</div>';

		if(!in_array($this->cfg['side'], array('front', 'pages')))
			echo '
									<div class="adm_check">
										<span class="adm_w85">'. $txt['pmx_edit_ext_maintenance'] .'</span>
										<input type="hidden" name="config[maintenance_mode]" value="0" />
										<input class="input_check" type="checkbox" name="config[maintenance_mode]" value="1"' .(!empty($this->cfg['config']['maintenance_mode']) ? ' checked="checked"' : ''). ' />
									</div>';

		// Frontpage block placing on Page request
		if($this->cfg['side'] == 'front')
			echo '
									<div class="adm_clear" style="margin-top:7px;">
										<div class="cat_bar catbg_grid grid_padd">
											<h4 class="catbg catbg_grid">
												<span class="cat_msg_title">'. $txt['pmx_edit_frontplacing'] .'</span>
												<img class="grid_click_image pmxleft" onclick=\'Show_help("pmxBHf1")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
											</h4>
										</div>
										<input type="hidden" name="config[frontmode]" value="" />
										<input type="hidden" name="config[frontview]" value="" />

										<div style="float:'. $LtR .'; height:20px; width:160px;">'. $txt['pmx_edit_frontplacing_hide'] .'</div>
										<input class="input_radio" type="radio" name="config[frontplace]" value="hide"'. (isset($this->cfg['config']['frontplace']) && $this->cfg['config']['frontplace'] == 'hide' || empty($this->cfg['config']['frontplace']) ? ' checked="checked"' : '') .' /><br class="adm_clear" />
										<div style="float:'. $LtR .'; height:20px; width:160px;">'. $txt['pmx_edit_frontplacing_before'] .'</div>
										<input class="input_radio" type="radio" name="config[frontplace]" value="before"'. (isset($this->cfg['config']['frontplace']) && $this->cfg['config']['frontplace'] == 'before' ? ' checked="checked"' : '') .' /><br class="adm_clear" />
										<div style="float:'. $LtR .'; height:20px; width:160px;">'. $txt['pmx_edit_frontplacing_after'] .'</div>
										<input class="input_radio" type="radio" name="config[frontplace]" value="after"'. (isset($this->cfg['config']['frontplace']) && $this->cfg['config']['frontplace'] == 'after' ? ' checked="checked"' : '') .' />
									</div>
									<br class="adm_clear" style="line-height:1px;" />
									<div id="pmxBHf1" class="info_frame">'.  $txt['pmx_edit_frontplacinghelp'] .'</div>';

		// Frontpage mode switch for Single Page
		elseif($this->cfg['side'] == 'pages')
			echo '
									<div class="adm_clear" style="margin-top:7px;">
										<div class="cat_bar catbg_grid grid_padd">
											<h4 class="catbg catbg_grid">
												<span class="cat_msg_title">'. $txt['pmx_edit_frontmode'] .'</span>
												<img class="grid_click_image pmxleft" onclick=\'Show_help("pmxBHf2")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
											</h4>
										</div>
										<input type="hidden" name="config[frontplace]" value="hide" />
										<input type="hidden" name="config[frontview]" value="" />

										<div style="float:'. $LtR .'; height:20px; width:136px;">'. $txt['pmx_edit_frontmode_none'] .'</div>
										<input class="input_radio" type="radio" name="config[frontmode]" value=""'. (empty($this->cfg['config']['frontmode']) ? ' checked="checked"' : '') .' /><br class="adm_clear" />
										<div style="float:'. $LtR .'; height:20px; width:136px;">'. $txt['pmx_edit_frontmode_center'] .'</div>
										<input class="input_radio" type="radio" name="config[frontmode]" value="centered"'. (isset($this->cfg['config']['frontmode']) && $this->cfg['config']['frontmode'] == 'centered' ? ' checked="checked"' : '') .' /><br class="adm_clear" />
										<div style="float:'. $LtR .'; height:20px; width:136px;">'. $txt['pmx_edit_frontmode_full'] .'</div>
										<input class="input_radio" type="radio" name="config[frontmode]" value="fullsize"'. (isset($this->cfg['config']['frontmode']) && $this->cfg['config']['frontmode'] == 'fullsize' ? ' checked="checked"' : '') .' />
									</div>
									<br class="adm_clear" style="line-height:1px;" />
									<div id="pmxBHf2" class="info_frame">'. $txt['pmx_edit_frontmodehelp'] .'</div>';

		// Block display on Frontpage mode
		else
			echo '
									<div class="adm_clear" style="margin-top:7px;">
										<div class="cat_bar catbg_grid grid_padd">
											<h4 class="catbg catbg_grid">
												<span class="cat_msg_title">'. $txt['pmx_edit_frontview'] .'</span>
												<img class="grid_click_image pmxleft" onclick=\'Show_help("pmxBHf3")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
											</h4>
										</div>
										<input type="hidden" name="config[frontplace]" value="hide" />
										<input type="hidden" name="config[frontmode]" value="" />

										<div style="float:'. $LtR .'; height:20px; width:136px;">'. $txt['pmx_edit_frontview_any'] .'</div>
										<input class="input_radio" type="radio" name="config[frontview]" value=""'. (empty($this->cfg['config']['frontview']) ? ' checked="checked"' : '') .' /><br class="adm_clear" />
										<div style="float:'. $LtR .'; height:20px; width:136px;">'. $txt['pmx_edit_frontview_center'] .'</div>
										<input class="input_radio" type="radio" name="config[frontview]" value="centered"'. (isset($this->cfg['config']['frontview']) && $this->cfg['config']['frontview'] == 'centered' ? ' checked="checked"' : '') .' /><br class="adm_clear" />
										<div style="float:'. $LtR .'; height:20px; width:136px;">'. $txt['pmx_edit_frontview_full'] .'</div>
										<input class="input_radio" type="radio" name="config[frontview]" value="fullsize"'. (isset($this->cfg['config']['frontview']) && $this->cfg['config']['frontview'] == 'fullsize' ? ' checked="checked"' : '') .' />
									</div>
									<br class="adm_clear" style="line-height:1px;" />
									<div id="pmxBHf3" class="info_frame">'. $txt['pmx_edit_frontviewhelp'] .'</div>';

		echo '
								</td>
							</tr>
							<tr id="upshrinkVisual2"' .(empty($options['collapse_visual']) ? '' : ' style="display:none;"'). '>';

		// on boards
		echo '
								<td valign="top" style="padding:0px 4px;">
									<div class="cat_bar catbg_grid">
										<h4 class="catbg catbg_grid">
											<span class="cat_msg_title">'. $txt['pmx_edit_ext_opts_boards'] .'</span>
											<img class="grid_click_image pmxleft" onclick=\'Show_help("pmxBH08")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
										</h4>
									</div>
									<select id="pmxbrd" onchange="changed(\'pmxbrd\');" style="margin:5px 0px; width:90%;" name="config[ext_opts][pmxbrd][]" multiple="multiple" size="10">';

		// get config data
		$data = $this->getConfigData('config, ext_opts, pmxbrd');
		foreach($this->smf_boards as $brd)
			echo '
										<option value="'. $brd['id'] .'='. (array_key_exists($brd['id'], $data) ? $data[$brd['id']] .'" selected="selected' : '1') .'">'. (array_key_exists($brd['id'], $data) ? ($data[$brd['id']] == '0' ? '^' : '') : '') . $brd['name'] .'</option>';

		echo '
									</select>
									<div id="pmxBH08" class="info_frame">'. $txt['pmx_edit_ext_opts_selnote'] .'</div>
									<script type="text/javascript"><!-- // --><![CDATA[
										var pmxbrd = new MultiSelect("pmxbrd");
									// ]]></script>
								</td>
								<td valign="top" style="padding:0px 4px;">';

		// on theme
		echo '
									<div class="cat_bar catbg_grid">
										<h4 class="catbg catbg_grid">
											<span class="cat_msg_title">'. $txt['pmx_edit_ext_opts_themes'] .'</span>
											<img class="grid_click_image pmxleft" onclick=\'Show_help("pmxBH09a")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
										</h4>
									</div>
									<select id="pmxthm" onchange="changed(\'pmxthm\');" style="margin:5px 0px; width:90%;" name="config[ext_opts][pmxthm][]" multiple="multiple" size="4">';

		// get config data
		$data = $this->getConfigData('config, ext_opts, pmxthm');
		foreach($this->smf_themes as $thid => $thdata)
				echo '
										<option value="'. $thid .'='. (array_key_exists($thid, $data) ? $data[$thid] .'" selected="selected' : '1') .'">'. (array_key_exists($thid, $data) ? ($data[$thid] == 0 ? '^' : '') : '') . $thdata['name'] .' ('. $thid .')</option>';

		echo '
									</select>
									<div id="pmxBH09a" class="info_frame">'. $txt['pmx_edit_ext_opts_selnote'] .'</div>
									<script type="text/javascript"><!-- // --><![CDATA[
										var pmxthm = new MultiSelect("pmxthm");
									// ]]></script>';

		// on language
		echo '
									<div class="cat_bar catbg_grid">
										<h4 class="catbg catbg_grid">
											<span class="cat_msg_title">'. $txt['pmx_edit_ext_opts_languages'] .'</span>
											<img class="grid_click_image pmxleft" onclick=\'Show_help("pmxBH09")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
										</h4>
									</div>
									<select id="pmxlng" onchange="changed(\'pmxlng\');" style="margin:5px 0px; width:90%;" name="config[ext_opts][pmxlng][]" multiple="multiple" size="4">';

		// get config data
		$data = $this->getConfigData('config, ext_opts, pmxlng');
		foreach($context['pmx']['languages'] as $lang => $sel)
			echo '
										<option value="'. $lang .'='. (array_key_exists($lang, $data) ? $data[$lang] .'" selected="selected' : '1') .'">'. (array_key_exists($lang, $data) ? ($data[$lang] == 0 ? '^' : '') : '') . ucfirst($lang) .'</option>';

		echo '
									</select>
									<div id="pmxBH09" class="info_frame">'. $txt['pmx_edit_ext_opts_selnote'] .'</div>
									<script type="text/javascript"><!-- // --><![CDATA[
										var pmxlng = new MultiSelect("pmxlng");
									// ]]></script>
								</td>
							</tr>
							<tr id="upshrinkVisual3"' .(empty($options['collapse_visual']) ? '' : ' style="display:none;"'). '>
								<td colspan="2" valign="top" align="center" style="padding:4px 4px 0 4px;"><hr />
									<input class="button_submit" type="button" style="margin-right:10px;" value="'. $txt['pmx_save_exit'] .'" name="" onclick="FormFunc(\'save_edit\', \'1\')" />
									<input class="button_submit" type="button" style="margin-right:10px;" value="'. $txt['pmx_save_cont'] .'" name="" onclick="FormFunc(\'save_edit_continue\', \'1\')" />
									<input class="button_submit" type="button" value="'. $txt['pmx_cancel'] .'" name="" onclick="FormFunc(\'cancel_edit\', \'1\')" />
								</td>
							</tr>
						</table>
						<span class="botslice"><span></span></span>
						</div>
					</td>
				</tr>';
	}

	/**
	* This Methode is called on loadtime.
	* After all variables initiated, it calls the block dependent init methode.
	* Finaly the css and language is loaded if exist
	*/
	function pmxc_AdmBlock_loadinit()
	{
		global $context, $txt;

		$this->smf_themes = PortaMx_getsmfThemes();											// get all themes
		$this->smf_groups = PortaMx_getUserGroups();										// get all usergroups
		$this->smf_boards = PortaMx_getsmfBoards();											// get all smf boards
		$this->register_blocks = $context['pmx']['RegBlocks'];					// get all registered block
		$this->block_classdef = PortaMx_getdefaultClass();							// get default classes
		$this->title_icons = PortaMx_getAllTitleIcons();								// get all title icons
		$this->custom_css = PortaMx_getCustomCssDefs();									// custom css definitions
		$this->can_cached = 0;																					// default no caching

		// sort the registered blocks
		ksort($this->register_blocks, SORT_STRING);

		// call the blockdepend init methode
		$this->pmxc_AdmBlock_init();
	}

	/**
	* The default init Methode.
	* Note: Most blocks overwrite this methode.
	*/
	function pmxc_AdmBlock_init()
	{
		$this->can_cached = 0;		// disable caching
		return '';
	}

	/**
	* The default content Methode.
	* returns the blocksettings.
	* Note: Most blocks overwrite this methode.
	*/
	function pmxc_AdmBlock_content()
	{
		// default .. no content
		return $this->pmxc_AdmBlock_settings();
	}

	/**
	* The default settings Methode.
	* returns the block css class definition.
	* Note: Most blocks overwrite this methode.
	*/
	function pmxc_AdmBlock_settings()
	{
		global $context, $txt;

		// the default settings
		echo '
					<td valign="top" style="padding:4px;">
						<input type="hidden" name="config[settings]" value="" />
						<div style="height:169px;">
							<div class="cat_bar catbg_grid grid_padd">
								<h4 class="catbg catbg_grid"><span class="cat_left_title">'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span></h4>
							</div>'.
							$txt['pmx_defaultsettings'] .'
						</div>';

		// return the default classnames
		return $this->block_classdef;
	}
}
?>
