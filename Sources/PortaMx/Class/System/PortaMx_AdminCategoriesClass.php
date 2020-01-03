<?php
/**
* \file PortaMx_AdminCategoriesClass.php
* Global Categories Admin class
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class PortaMxC_AdminCategories
* The Global Class for Categories Administration.
* @see PortaMx_AdminCategoriesClass.php
* \author Copyright by PortaMx - http://portamx.com
*/
class PortaMxC_AdminCategories
{
	var $cfg;						///< common config

	/**
	* The Contructor.
	* Saved the config, load the category css file if exist.
	* Have the category a css file, the class definition is extracted from ccs header
	*/
	function __construct($config)
	{
		global $context, $settings;

		// get the block config array
		if(isset($config['config']))
			$config['config'] = unserialize($config['config']);
		$this->cfg = $config;
	}
}

/**
* @class PortaMxC_SystemAdminCategories
* This is the Global Admin class to create or edit a Categories.
* This class prepare the settings screen.
* @see PortaMx_AdminCategoriesClass.php
* \author Copyright by PortaMx - http://portamx.com
*/
class PortaMxC_SystemAdminCategories extends PortaMxC_AdminCategories
{
	var $smf_groups;				///< all usergroups
	var $title_icons;				///< array with title icons
	var $custom_css;				///< custom css definitions
	var $usedClass;					///< used class types
	var $categories;				///< all exist categories

	/**
	* Output the Category config screen
	*/
	function pmxc_ShowAdmCategoryConfig()
	{
		global $context, $settings, $modSettings, $options, $txt;

		// directions
		$LtR = empty($context['right_to_left']) ? 'left' : 'right';
		$RtL = empty($context['right_to_left']) ? 'right' : 'left';

		echo '
				<tr>
					<td valign="top">
						<div class="windowbg" style="margin-top:-3px;">
						<span class="topslice"><span></span></span>
						<table width="100%" cellspacing="1" cellpadding="1">
							<tr>
								<td valign="top" width="50%" style="padding:4px;">
									<input type="hidden" name="id" value="'. $this->cfg['id'] .'" />
									<input type="hidden" name="parent" value="'. $this->cfg['parent'] .'" />
									<input type="hidden" name="level" value="'. $this->cfg['level'] .'" />
									<input type="hidden" name="catorder" value="'. $this->cfg['catorder'] .'" />
									<input type="hidden" name="config[settings]" value="" />
									<input type="hidden" name="check_num_vars[]" value="[config][maxheight], \'\'" />
									<div style="height:50px;">
										<div style="float:'. $LtR .';width:110px; padding-top:1px;">'. $txt['pmx_categories_title'] .'</div>';

		// all titles depend on language
		foreach($context['pmx']['languages'] as $lang => $sel)
			echo '
										<span id="'. $lang .'" style="white-space:nowrap;'. (!empty($sel) ? '' : ' display:none;') .'">
											<input style="width:60%;" type="text" name="config[title]['. $lang .']" value="'. (isset($this->cfg['config']['title'][$lang]) ? htmlspecialchars($this->cfg['config']['title'][$lang], ENT_QUOTES) : '') .'" />
										</span>';

		echo '
										<input id="curlang" type="hidden" name="" value="'. $context['pmx']['currlang'] .'" />
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
									</div>
								</td>
								<td valign="top" style="padding:4px;">
									<div style="height:50px;">';

		// show placement for new categories
		if($context['pmx']['subaction'] == 'editnew')
		{
			if(!empty($this->categories))
			{
				echo '
										<div style="float:'. $LtR .'; width:143px; padding-top:2px;">'. $txt['pmx_categories_type'] .'</div>';

				$opt = 0;
				foreach($txt['pmx_categories_places'] as $artType => $artDesc)
				{
					echo '
							<input id="pWind.place.'. $opt .'" class="input_check" type="radio" name="catplace" value="'. $artType .'"'. ($artType == 'after' ? ' checked="checked"' : '') .' /><span style="vertical-align:3px; padding:0 3px;">'. $artDesc .'</span>&nbsp;&nbsp;';
					$opt++;
				}

				echo '
										<div style="clear:both; line-height:7px;">&nbsp;</div>';

				// all exist categories
				echo '
										<div style="float:'. $LtR .'; width:150px; padding-top:2px;">'. $txt['pmx_categories_cats'] .'</div>
										<select style="width:53%;" size="1" name="catid">';

				// output cats
				foreach($context['pmx']['catorder'] as $order)
				{
					$cat = PortaMx_getCatByOrder($this->categories, $order);
					echo '
											<option value="'. $cat['id'] .'"' .($this->cfg['id'] == $cat['id'] ? ' selected="selected"' : '') .'>'. str_repeat('&bull;', $cat['level']) .' '. $cat['name'] .'</option>';
				}

				echo '
										</select>
										<div style="clear:both; line-height:7px;">&nbsp;</div>';
			}
			else
			 echo '
										<input type="hidden" name="catid" value="0" />
										<input type="hidden" name="catplace" value="0" />';
		}
		echo '
									</div>';

		// category name
		echo '
									<div style="clear:both; line-height:1px;">&nbsp;</div>
									<div class="adm_clear" style="float:'. $LtR .';width:150px; padding-top:7px;">'. $txt['pmx_categories_name'] .':
										<img class="info_toggle" onclick=\'Show_help("pmxBH11")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
									</div>
									<span id="check.name.error" style="display:none;">'. sprintf($txt['namefielderror'], $txt['pmx_categories_name']) .'</span>
									<input id="check.name" style="width:52%; margin-top:5px;" onkeyup="check_requestname(this)" onkeypress="check_requestname(this)" type="text" name="name" value="'. $this->cfg['name'] .'" />
									<div id="pmxBH11" class="info_frame" style="margin-top:5px;">'.
										$txt['pmx_edit_pagenamehelp'] .'
									</div>
								</td>
							</tr>

							<tr class="windowbg">
								<td valign="top" style="padding:4px;">
									<input type="hidden" name="config[settings]" value="" />';

		// show the settings area
		echo '
									<div class="cat_bar catbg_grid">
										<h4 class="catbg catbg_grid"><span class="cat_left_title">'. $txt['pmx_categories_settings_title'] .'</span></h4>
									</div>';

		// show mode (titelbar/frame)
		foreach($txt['pmx_categories_showmode'] as $key => $value)
			echo '
									<div class="adm_check" style="height:32px;">
										<img align="'. $LtR .'" src="'. $context['pmx_imageurl'] .'ca_frame_'. $key .'.png" alt="*" />
										<div style="float:'. $LtR .'; width:72%; padding:0 10px;">'. $value .'</div>
										<input style="float:'. $RtL .';margin-'. $RtL .':20px;" name="config[settings][framemode]" class="input_radio" type="radio" value="'. $key .'"'. ($this->cfg['config']['settings']['framemode'] == $key ? ' checked="checked"' : '') .' />
									</div>';

		// show mode (sidebar)
		echo '
									<div class="adm_check" style="height:20px; padding-top:10px;">
										<div style="float:'. $LtR .'; width:82%;">'. $txt['pmx_categories_modsidebar'] .'</div>
										<input id="shm.sidebar" style="float:'. $RtL .';margin-'. $RtL .':20px;" onchange="check_PageMode(this, \'pages\')" name="config[settings][showmode]" class="input_radio" type="radio" value="sidebar"'. ($this->cfg['config']['settings']['showmode'] == 'sidebar' ? ' checked="checked"' : '') .' />
									</div>';

		// show mode (pages)
		echo '
									<div class="adm_check" style="height:20px;">
										<div style="float:'. $LtR .'; width:82%;">'. $txt['pmx_categories_modpage'] .'</div>
										<input id="shm.pages" style="float:'. $RtL .';margin-'. $RtL .':20px;" onchange="check_PageMode(this, \'sidebar\')" name="config[settings][showmode]" class="input_radio" type="radio" value="pages"'. ($this->cfg['config']['settings']['showmode'] == 'pages' ? ' checked="checked"' : '') .' />
									</div>';

		// options for SideBar mode
		echo '
									<div id="opt.sidebar" class="adm_clear" style="padding-top:0px;'. ($this->cfg['config']['settings']['showmode'] == 'pages' ? ' display:none;' : '') .'">
										<div style="height:20px;margin-top:3px;">
											<div style="float:'. $LtR .'; width:80%;">'. $txt['pmx_categories_sidebarwith'] .'</div>
											<input style="float:'. $RtL .';margin-'. $RtL .':20px;" onkeyup="check_numeric(this)" type="text" size="3" name="config[settings][sidebarwidth]" value="'. (!empty($this->cfg['config']['settings']['sidebarwidth']) ? $this->cfg['config']['settings']['sidebarwidth'] : '') .'" />
										</div>
										<div style="height:20px;margin-top:3px;">
											<div style="float:'. $LtR .';">'. $txt['pmx_categories_sidebaralign'] .'</div>
											<div style="float:'. $RtL .'; margin-'. $RtL .':20px;margin-top:0px;">
												<input class="input_radio" type="radio" name="config[settings][sbmalign]" value="1"'. (!empty($this->cfg['config']['settings']['sbmalign']) ? ' checked="checked"' : '') .' /><span style="vertical-align:2px;">'. $txt['pmx_categories_sbalign'][0] .'&nbsp;&nbsp;&nbsp;</span>
												<input class="input_radio" type="radio" name="config[settings][sbmalign]" value="0"'. (empty($this->cfg['config']['settings']['sbmalign']) ? ' checked="checked"' : '') .' /><span style="vertical-align:2px;">'. $txt['pmx_categories_sbalign'][1] .'</span>
											</div>
										</div>
										<div style="height:20px;margin-top:3px;">
											<div style="float:'. $LtR .'; width:82%;">'. $txt['pmx_categories_addsubcats'] .'</div>
											<input type="hidden" name="config[settings][addsubcats]" value="0" />
											<input style="float:'. $RtL .';margin-'. $RtL .':20px;" type="checkbox" class="input_check" name="config[settings][addsubcats]" value="1"'. (!empty($this->cfg['config']['settings']['addsubcats']) ? ' checked="checked"' : '') .' />
										</div>
									</div>';

		// options for Pages mode
		echo '
									<div id="opt.pages" class="adm_clear" style="padding-top:3px;'. ($this->cfg['config']['settings']['showmode'] == 'sidebar' ? 'display:none;' : '') .'">
										<div style="height:20px;">
											<div style="float:'. $LtR .';width:80%;">'. $txt['pmx_categories_modpage_count'] .'</div>
											<input style="float:'. $RtL .';margin-'. $RtL .':20px;" onkeyup="check_numeric(this)" type="text" size="3" name="config[settings][pages]" value="'. (!empty($this->cfg['config']['settings']['pages']) ? $this->cfg['config']['settings']['pages'] : '') .'" />
										</div>
										<div style="height:20px;margin-top:3px;">
											<div style="float:'. $LtR .';width:80%;">'. $txt['pmx_categories_modpage_pageindex'] .'</div>
											<input type="hidden" name="config[settings][pageindex]" value="0" />
											<input style="float:'. $RtL .';margin-'. $RtL .':20px;" type="checkbox" class="input_check" name="config[settings][pageindex]" value="1"'. (!empty($this->cfg['config']['settings']['pageindex']) ? ' checked="checked"' : '') .' />
										</div>
										<div style="height:20px;margin-top:3px;">
											<div style="float:'. $LtR .'; width:82%;">'. $txt['pmx_categories_showsubcats'] .'</div>
											<input type="hidden" name="config[settings][showsubcats]" value="0" />
											<input id="opt.pages.sbar.check" style="float:'. $RtL .';margin-'. $RtL .':20px;" type="checkbox" class="input_check" name="config[settings][showsubcats]" value="1"'. (!empty($this->cfg['config']['settings']['showsubcats']) ? ' checked="checked"' : '') .' onchange="set_PageMode(this)" />
										</div>
									</div>
									<div id="opt.pages.sbar" class="adm_clear" style="padding-top:0px;'. ($this->cfg['config']['settings']['showmode'] == 'sidebar' ? 'display:none;' : '') .'">
										<div style="height:20px;margin-top:3px;">
											<div style="float:'. $LtR .'; width:80%;">'. $txt['pmx_categories_sidebarwith'] .'</div>
											<input style="float:'. $RtL .';margin-'. $RtL .':20px;" onkeyup="check_numeric(this)" type="text" size="3" name="config[settings][catsbarwidth]" value="'. (!empty($this->cfg['config']['settings']['catsbarwidth']) ? $this->cfg['config']['settings']['catsbarwidth'] : '') .'" />
										</div>
										<div style="height:20px;margin-top:3px;">
											<div style="float:'. $LtR .';">'. $txt['pmx_categories_sidebaralign'] .'</div>
											<div style="float:'. $RtL .'; margin-'. $RtL .':20px;margin-top:0px;">
												<input class="input_radio" type="radio" name="config[settings][sbpalign]" value="1"'. (!empty($this->cfg['config']['settings']['sbpalign']) ? ' checked="checked"' : '') .' /><span style="vertical-align:2px;">'. $txt['pmx_categories_sbalign'][0] .'&nbsp;&nbsp;&nbsp;</span>
												<input class="input_radio" type="radio" name="config[settings][sbpalign]" value="0"'. (empty($this->cfg['config']['settings']['sbpalign']) ? ' checked="checked"' : '') .' /><span style="vertical-align:2px;">'. $txt['pmx_categories_sbalign'][1] .'</span>
											</div>
										</div>
									</div>
									<div class="adm_check" style="height:20px; padding-top:15px;">
										<div style="float:'. $LtR .'; width:82%;">
											<img class="info_toggle" align="'. $RtL .' style="padding:2px 5px;" onclick=\'Show_help("pmxCH06")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
											'. $txt['pmx_categorie_inherit'] .'
										</div>
										<input name="config[settings][inherit_acs]" type="hidden" value="0" />
										<input style="float:'. $RtL .';margin-'. $RtL .':20px;" name="config[settings][inherit_acs]" class="input_check" type="checkbox" value="1"'. (!empty($this->cfg['config']['settings']['inherit_acs']) ? ' checked="checked"' : '') .' />
									</div>
									<div id="pmxCH06" class="info_frame">'. $txt['pmx_categories_inherithelp'] .'</div>
									<script type="text/javascript"><!-- // --><![CDATA[
										function check_PageMode(elm, mode)
										{
											var shm_id = elm.id.replace("shm.", "");
											document.getElementById("opt."+ shm_id).style.display = (elm.checked == true ? "" : "none");
											document.getElementById("opt."+ mode).style.display =  (elm.checked == true ? "none" : "");
											if(mode == "pages" && elm.checked == true)
												document.getElementById("opt.pages.sbar").style.display = "none";
											else
											{
												if(mode == "sidebar" && document.getElementById("opt.pages.sbar.check").checked == true)
													document.getElementById("opt.pages.sbar").style.display = "";
											}
										}
										function set_PageMode(elm)
										{
											document.getElementById("opt.pages.sbar").style.display = (elm.checked == true ? "" : "none");
										}
									// ]]></script>';

		// article sort
		echo '
									<div class="adm_clear" style="padding-top:10px; height:55px;">
										<div style="float:'. $LtR .'; width:150px;">
											<img class="info_toggle" align="'. $RtL .' style="padding:2px 5px;" onclick=\'Show_help("pmxCH05")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
											'. $txt['pmx_categorie_articlesort'] .'
										</div>
										<select style="float:'. $RtL .';margin-'. $RtL .':20px; width:45%;margin-top:3px;" name="artsort[]" id="pmxartsort" onchange="changed(\'pmxartsort\');" size="3" multiple="multiple">';

		if(!empty($this->cfg['artsort']))
		{
			$sortdata = array();
			$sortval = Pmx_StrToArray($this->cfg['artsort']);
			foreach($sortval as $sort)
			{
				@list($k, $v) = Pmx_StrToArray($sort, '=');
					$sortdata[$k] = $v;
			}
		}
		else
			$sortdata = array('id' => 1);

		foreach($txt['pmx_categories_artsort'] as $key => $value)
			echo '
											<option value="'. $key .'='. (array_key_exists($key, $sortdata) ? $sortdata[$key] .'" selected="selected' : '1') .'">'. (array_key_exists($key, $sortdata) ? ($sortdata[$key] == '0' ? '^' : '') : '') . $value .'</option>';

		echo '
										</select>
									</div>
									<div class"adm_clear"></div>
									<div id="pmxCH05" class="info_frame">'. $txt['pmx_categories_sorthelp'] .'</div>
									<script type="text/javascript"><!-- // --><![CDATA[
										var pmxartsort = new MultiSelect("pmxartsort");
									// ]]></script>';

		// Categorie for common use
		echo '
									<div style="height:5px;"></div>
									<div class="cat_bar catbg_grid">
										<h4 class="catbg catbg_grid"><span class="cat_left_title">'. $txt['pmx_categories_globalcat'] .'</span></h4>
									</div>
									<div class="adm_check">
										<span class="adm_w80">'. $txt['pmx_categorie_global'] .'
											<img class="info_toggle" onclick=\'Show_help("pmxHCAT02")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
										</span>
										<input type="hidden" name="config[global]" value="0" />
										<input class="input_check" type="checkbox" name="config[global]" value="1"' .(!empty($this->cfg['config']['global']) ? ' checked="checked"' : ''). ' />
										<div id="pmxHCAT02" class="info_frame" style="margin-top:4px;">'. $txt['pmx_categories_gloablcathelp'] .'</div>
									</div>
									<div class="adm_check">
										<span class="adm_w80">'. $txt['pmx_categorie_request'] .'
											<img class="info_toggle" onclick=\'Show_help("pmxHCAT03")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
										</span>
										<input type="hidden" name="config[request] value="0" />
										<input class="input_check" type="checkbox" name="config[request]" value="1"' .(!empty($this->cfg['config']['request']) ? ' checked="checked"' : ''). ' />
										<div id="pmxHCAT03" class="info_frame" style="margin-top:4px;">'. $txt['pmx_categorie_requesthelp'] .'</div>
									</div>
								</td>';

		// the visual options
		echo '
								<td id="set_col" valign="top" style="padding:4px;">
									<div class="cat_bar catbg_grid grid_padd">
										<h4 class="catbg catbg_grid"><span class="cat_left_title">'. $txt['pmx_edit_visuals'] .'</span></h4>
									</div>
									<div style="float:'. $LtR .'; height:24px; width:177px;">'. $txt['pmx_edit_cancollapse'] .'</div>
									<input style="padding-'. $LtR .':141px;" type="hidden" name="config[collapse]" value="0" />
									<input class="input_check" id="collapse" type="checkbox" name="config[collapse]" value="1"'. ($this->cfg['config']['visuals']['header'] == 'none' ? ' disabled="disabled"' : ($this->cfg['config']['collapse'] == 1 ? ' checked="checked"' : '')) .' />
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
									<div class="cat_bar catbg_grid grid_padd">
										<h4 class="catbg catbg_grid grid_botpad">
											<div style="float:'. $LtR .'; width:180px;"><span class="cat_left_title">'. $txt['pmx_edit_usedclass_type'] .'</span></div>
											<span class="cat_left_title">'. $txt['pmx_edit_usedclass_style'] .'</span>
										</h4>
									</div>
									<div style="margin:0px 2px;">';

		// write out the classes
		foreach($this->usedClass as $ucltyp => $ucldata)
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
						$tmp = preg_replace('~\s\[.*\]~', '', $key);
						if(in_array($tmp, array_keys($this->usedClass)))
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
									<div style="clear:both;"></div>';

		// the group access
		echo '
									<div class="adm_clear cat_bar catbg_grid grid_padd" style="margin-top:8px;">
										<h4 class="catbg catbg_grid">
											<span class="cat_msg_title">'. $txt['pmx_categories_groups'] .'</span>
											<img class="grid_click_image pmxleft" onclick=\'Show_help("pmxBH03")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
										</h4>
									</div>
									<select name="acsgrp[]" id="pmxgroups" onchange="changed(\'pmxgroups\');" style="width:88%;" multiple="multiple" size="5">';

		if(!empty($this->cfg['acsgrp']))
			list($grpacs, $denyacs) = Pmx_StrToArray($this->cfg['acsgrp'], ',', '=');
		else
			$grpacs = $denyacs = array();

		foreach($this->smf_groups as $grp)
			echo '
										<option value="'. $grp['id'] .'='. intval(!in_array($grp['id'], $denyacs)) .'"'. (in_array($grp['id'], $grpacs) ? ' selected="selected"' : '') .'>'. (in_array($grp['id'], $denyacs) ? '^' : '') . $grp['name'] .'</option>';

		echo '
									</select>
									<div id="pmxBH03" class="info_frame" style="margin-top:5px;">'. $txt['pmx_categories_groupshelp'] .'</div>
									<script type="text/javascript"><!-- // --><![CDATA[
										var pmxgroups = new MultiSelect("pmxgroups");
									// ]]></script>
								</td>
							</tr>
							<tr>
							<tr>
								<td colspan="2" valign="top" align="center" style="padding:4px 4px 0 4px;"><hr />
									<input class="button_submit" type="button" style="margin-right:10px;" value="'. $txt['pmx_save_exit'] .'" name="" onclick="FormFuncCheck(\'save_edit\', \'1\')" />
									<input class="button_submit" type="button" style="margin-right:10px;" value="'. $txt['pmx_save_cont'] .'" name="" onclick="FormFuncCheck(\'save_edit_continue\', \'1\')" />
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
	* Finaly the css is loaded if exist
	*/
	function pmxc_AdmCategories_loadinit()
	{
		global $context;

		$this->smf_groups = PortaMx_getUserGroups();										// get all usergroups
		$this->title_icons = PortaMx_getAllTitleIcons();								// get all title icons
		$this->custom_css = PortaMx_getCustomCssDefs();									// custom css definitions
		$this->usedClass = PortaMx_getdefaultClass(false, true);				// default class types
		$this->categories = PortaMx_getCategories();										// exist categories
	}
}
?>
