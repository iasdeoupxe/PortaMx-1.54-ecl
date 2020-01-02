<?php
/**
* \file PortaMx_AdminArticlesClass.php
* Global Articles Admin class
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2015 by PortaMx corp. - http://portamx.com
* \version 1.54
* \date 18.11.2015
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class PortaMxC_AdminArticles
* The Global Class for Articles Administration.
* @see PortaMx_AdminArticlesClass.php
* \author Copyright by PortaMx - http://portamx.com
*/
class PortaMxC_AdminArticles
{
	var $cfg;						///< common config

	/**
	* The Contructor.
	* Saved the config, load the article css file if exist.
	* Have the article a css file, the class definition is extracted from ccs header
	*/
	function __construct($config)
	{
		global $context, $settings;

		// get the article config array
		if(isset($config['config']))
			$config['config'] = unserialize($config['config']);
		$this->cfg = $config;
	}
}

/**
* @class PortaMxC_SystemAdminArticle
* This is the Global Admin class to create or edit a Article.
* This class prepare the settings screen and the and content.
* @see PortaMx_AdminArticlesClass.php
* \author Copyright by PortaMx - http://portamx.com
*/
class PortaMxC_SystemAdminArticle extends PortaMxC_AdminArticles
{
	var $smf_groups;				///< all usergroups
	var $title_icons;				///< array with title icons
	var $custom_css;				///< custom css definitions
	var $usedClass;					///< used class types
	var $categories;				///< all exist categories

	/**
	* Output the Article config screen
	*/
	function pmxc_ShowAdmArticleConfig()
	{
		global $context, $settings, $boarddir, $modSettings, $options, $user_info, $txt;

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
									<input type="hidden" name="owner" value="'. $this->cfg['owner'] .'" />
									<input type="hidden" name="contenttype" value="'. $this->cfg['ctype'] .'" />
									<input type="hidden" name="config[settings]" value="" />
									<input type="hidden" name="active" value="'. $this->cfg['active'] .'" />
									<input type="hidden" name="approved" value="'. $this->cfg['approved'] .'" />
									<input type="hidden" name="approvedby" value="'. $this->cfg['approvedby'] .'" />
									<input type="hidden" name="created" value="'. $this->cfg['created'] .'" />
									<input type="hidden" name="updated" value="'. $this->cfg['updated'] .'" />
									<input type="hidden" name="updatedby" value="'. $this->cfg['updatedby'] .'" />
									<input type="hidden" name="check_num_vars[]" value="[config][maxheight], \'\'" />
									<div style="height:50px;">
										<div style="float:'. $LtR .';width:110px; padding-top:1px;">'. $txt['pmx_article_title'] .'</div>';

		// all titles depend on language
		foreach($context['pmx']['languages'] as $lang => $sel)
			echo '
										<span id="'. $lang .'" style="white-space:nowrap;'. (!empty($sel) ? '' : ' display:none;') .'">
											<input style="width:60%;" type="text" name="config[title]['. $lang .']" value="'. (isset($this->cfg['config']['title'][$lang]) ? htmlspecialchars($this->cfg['config']['title'][$lang], ENT_QUOTES) : '') .'" />
										</span>';

		echo '
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
										<input id="curlang" type="hidden" name="" value="'. $context['pmx']['currlang'] .'" />
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

		// show article types
		echo '
								</td>
								<td valign="top" style="padding:4px;">
									<div style="height:50px;">
										<div style="float:'. $LtR .'; width:130px;">'. $txt['pmx_article_type'] .'</div>';

		if(allowPmx('pmx_admin, pmx_create'))
		{
			echo '
										<select style="width:60%;" size="1" name="ctype" onchange="FormFunc(\'edit_change\', \'1\')">';

			foreach($txt['pmx_articles_types'] as $artType => $artDesc)
				echo '
											<option value="'. $artType .'"' .($this->cfg['ctype'] == $artType ? ' selected="selected"' : '') .'>'. $artDesc .'</option>';

			echo '
										</select>
										<div style="clear:both; line-height:10px;">&nbsp;</div>';
		}
		else
		{
			echo '
										<input type="hidden" name="ctype" value="'. $this->cfg['ctype'] .'" />';

			foreach($txt['pmx_articles_types'] as $artType => $artDesc)
			{
				if($artType == $this->cfg['ctype'])
					echo '
										<input style="width:60%;" type="text" value="'. $artDesc .'" disabled="disabled" />';
			}
			echo '
										<div style="clear:both; line-height:8px;">&nbsp;</div>';
		}

		// all exist categories
		$selcats = array_merge(array(PortaMx_getDefaultCategory($txt['pmx_categories_none'])), $this->categories);
		$ordercats = array_merge(array(0), $context['pmx']['catorder']);
		$isWriter = allowPmx('pmx_create, pmx_articles', true);
		$isAdm = allowPmx('pmx_admin');
		echo '
										<div style="float:'. $LtR .'; width:130px; padding-top:2px;">'. $txt['pmx_article_cats'] .'</div>
										<select style="width:60%;" size="1" name="catid">';

		foreach($ordercats as $catorder)
		{
			$cat = PortaMx_getCatByOrder($selcats, $catorder);
			$cfg = unserialize($cat['config']);
			if(!empty($isAdm) || (!empty($isWriter) && empty($cfg['global'])))
				echo '
											<option value="'. $cat['id'] .'"'. ($cat['id'] == $this->cfg['catid'] ? ' selected="selected"' : '') .'">'. str_repeat('&bull;', $cat['level']).' '. $cat['name'] .'</option>';
		}

		echo '
										</select>
										<div style="clear:both; line-height:7px;">&nbsp;</div>
									</div>';

		// articlename
		echo '
									<div style="clear:both; line-height:1px;">&nbsp;</div>
									<div class="adm_clear" style="float:'. $LtR .';width:130px; padding-top:7px;">'. $txt['pmx_article_name'] .'
										<img class="info_toggle" onclick=\'Show_help("pmxBH11")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
									</div>
									<input id="check.name" style="width:60%; margin-top:5px;" onkeyup="check_requestname(this)" onkeypress="check_requestname(this)" type="text" name="name" value="'. $this->cfg['name'] .'" />
									<span id="check.name.error" style="display:none;">'. sprintf($txt['namefielderror'], $txt['pmx_article_name']) .'</span>
									<div id="pmxBH11" class="info_frame" style="margin-top:5px;">'.
										$txt['pmx_edit_pagenamehelp'] .'
									</div>
								</td>
							</tr>';

		// the editor area dependent on article type
		echo '
							<tr>
								<td valign="top" colspan="2" style="padding:4px;">';

		if($this->cfg['ctype'] == 'html')
		{
			// show the editor
			$allow = allowPmx('pmx_admin') || allowPmx('pmx_articles') || allowPmx('pmx_create');
			$fnd = explode('/', str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']));
			$smfpath = str_replace('\\', '/', $boarddir);
			foreach($fnd as $key => $val) { $fnd[$key] = $val; $rep[] = ''; }
			$filepath = trim(str_replace($fnd, $rep, $smfpath), '/') .'/editor_uploads/images';
			if(count($fnd) == count(explode('/', $smfpath)))
				$filepath = '/'. $filepath;
			$_SESSION['pmx_ckfm'] = array('ALLOW' => $allow, 'FILEPATH' => $filepath);

			echo '
							<textarea name="'. $context['pmx']['htmledit']['id'] .'">'. $context['pmx']['htmledit']['content'] .'</textarea>
							<script language="JavaScript" type="text/javascript">
								CKEDITOR.replace("'. $context['pmx']['htmledit']['id'] .'", {filebrowserBrowseUrl: "ckeditor/fileman/index.php"});
							</script>';
		}
		else
		{
			if($this->cfg['ctype'] == 'bbc')
			{
				echo '
									<div class="cat_bar catbg_grid">
										<h4 class="catbg catbg_grid"><span class="cat_left_title">'. $txt['pmx_edit_content'] .'</span></h4>
									</div>
									<input type="hidden" id="smileyset" value="PortaMx" />
									<div id="bbcBox_message"></div>
									<div id="smileyBox_message"></div>
									<div>', template_control_richedit($context['pmx']['editorID'], 'smileyBox_message', 'bbcBox_message'), '</div>
									<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
										function PmX_RichEdit_Submit()
										{
											if(oEditorHandle_'. $context['pmx']['editorID'].'.bRichTextPossible && oEditorHandle_'. $context['pmx']['editorID'].'.bRichTextEnabled)
											{
												oEditorHandle_'. $context['pmx']['editorID'].'.doSubmit();
												document.getElementById("extra_cmd").name = "html_to_bbc";
												document.getElementById("extra_cmd").value = "1";
											}
										}
									// ]]></script>';
			}
			elseif($this->cfg['ctype'] == 'php')
			{
				$options['collapse_phpinit'] = empty($context['pmx']['editorID_init']['havecont']);
				echo '
									<div class="cat_bar catbg_grid">
										<h4 class="catbg catbg_grid"><span class="cat_left_title">'. $txt['pmx_edit_content'] .'
											<span id="upshrinkPHPinitCont"'. (empty($options['collapse_phpinit']) ? '' : ' style="display:none;"') .'>'. $txt['pmx_edit_content_show'] .'</span></span>
										</h4>
									</div>
									<div>', template_control_richedit($context['pmx']['editorID']) ,'</div>

									<div class="plainbox" style="margin: 5px 0; padding: 7px 0 5px 0;">
									<div class="normaltext" style="margin:0 10px;">
									'.(empty($context['pmx']['editorID_init']['havecont']) ?
										'<img id="upshrinkPHPshowImg" src="'. (empty($options['collapse_phpinit']) ? $context['pmx_img_expand'] : $context['pmx_img_colapse']) .'" alt="*" title="'. (empty($options['collapse_phpinit']) ? $txt['pmx_collapse'] . $txt['pmx_php_partblock'] : $txt['pmx_expand'] . $txt['pmx_php_partblock']) .'" />&nbsp;' : '') .'
										<span>'. $txt['pmx_php_partblock_note'] .'
											<img class="info_toggle" onclick=\'Toggle_help("pmxPHPH01")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
										</span>
									</div>
									<div id="pmxPHPH01" style="display:none; margin:4px 10px 0;">'. $txt['pmx_php_partblock_help'] .'</div>
								</div>

								<div id="upshrinkPHPshowCont"' .(empty($options['collapse_phpinit']) ? '' : ' style="display:none;"') .'>

								<div class="cat_bar catbg_grid">
									<h4 class="catbg catbg_grid"><span class="cat_left_title">'. $txt['pmx_edit_content'] . $txt['pmx_edit_content_init'] .'</span></h4>
								</div>
								<div>', template_control_richedit($context['pmx']['editorID_init']['id']) ,'</div>
							</div>';

				if(empty($context['pmx']['editorID_show']['havecont']))
					echo '
							<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
							var upshrinkPHPshow = new smc_Toggle({
								bToggleEnabled: true,
								bCurrentlyCollapsed: '. (empty($options['collapse_phpinit']) ? 'false' : 'true') .',
								aSwappableContainers: [
									\'upshrinkPHPshowCont\',
									\'upshrinkPHPinitCont\'
								],
								aSwapImages: [
									{
										sId: \'upshrinkPHPshowImg\',
										srcCollapsed: \''. $context['pmx_img_colapse'] .'\',
										altCollapsed: '. JavaScriptEscape($txt['pmx_expand'] . $txt['pmx_php_partblock']) .',
										srcExpanded: \''. $context['pmx_img_expand'] .'\',
										altExpanded: '. JavaScriptEscape($txt['pmx_collapse'] . $txt['pmx_php_partblock']) .'
									}
								]
							});
							// ]]></script>';
			}

			else
				echo '
									<div class="cat_bar catbg_grid">
										<h4 class="catbg catbg_grid"><span class="cat_left_title">'. $txt['pmx_edit_content'] .'</span></h4>
									</div>
									<div>', template_control_richedit($context['pmx']['editorID']), '</div>';
		}
		echo '
								</td>
							</tr>
							<tr class="windowbg">
								<td valign="top" style="padding:4px;">
									<input type="hidden" name="config[settings]" value="" />';

		// show the settings area
		echo '
									<div class="cat_bar catbg_grid">
										<h4 class="catbg catbg_grid"><span class="cat_left_title">'. $txt['pmx_articles_types'][$this->cfg['ctype']] .' '. $txt['pmx_article_settings_title'] .'</span></h4>
									</div>
									<div style="min-height:101px;">';

		if($this->cfg['ctype'] == 'html')
			echo '
										<div class="adm_check" style="min-height:20px;">
											<span class="adm_w80">'. $txt['pmx_html_teaser'] .'
												<img class="info_toggle" onclick=\'Show_help("pmxHTMLH01")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
											</span>
											<input type="hidden" name="config[settings][teaser]" value="0" />
											<div><input class="input_check" type="checkbox" name="config[settings][teaser]" value="1"' .(isset($this->cfg['config']['settings']['teaser']) && !empty($this->cfg['config']['settings']['teaser']) ? ' checked="checked"' : ''). ' /></div>
										</div>
										<div id="pmxHTMLH01" class="info_frame" style="margin-top:4px;">'. $txt['pmx_html_teasehelp'] .'</div>';

		elseif($this->cfg['ctype'] != 'php')
			echo '
										<div class="adm_check" style="min-height:20px;">
											<span class="adm_w80">'. sprintf($txt['pmx_article_teaser'], $txt['pmx_teasemode'][intval(!empty($context['pmx']['settings']['teasermode']))]) .'
												<img class="info_toggle" onclick=\'Show_help("pmxHTMLH02")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
											</span>
											<div><input class="input_text" type="text" size="5" name="config[settings][teaser]" value="'. (isset($this->cfg['config']['settings']['teaser']) ? $this->cfg['config']['settings']['teaser'] : '') .'" /></div>
										</div>
										<div id="pmxHTMLH02" class="info_frame" style="margin-top:4px;">'. $txt['pmx_article_teasehelp'] .'</div>';

		echo '
										<div class="adm_check" style="min-height:20px;">
											<span class="adm_w80">'. $txt['pmx_content_print'] .'</span>
											<input type="hidden" name="config[settings][printing]" value="0" />
											<div><input class="input_check" type="checkbox" name="config[settings][printing]" value="1"' .(!empty($this->cfg['config']['settings']['printing']) ? ' checked="checked"' : ''). ' /></div>
										</div>

										<div class="adm_check" style="min-height:20px;">
											<span class="adm_w80">'. $txt['pmx_article_footer'] .'
												<img class="info_toggle" onclick=\'Show_help("pmxARTH01")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
											</span>
											<input type="hidden" name="config[settings][showfooter]" value="0" />
											<div><input class="input_check" type="checkbox" name="config[settings][showfooter]" value="1"' .(isset($this->cfg['config']['settings']['showfooter']) && !empty($this->cfg['config']['settings']['showfooter']) ? ' checked="checked"' : ''). ' /></div>
										</div>
										<div id="pmxARTH01" class="info_frame" style="margin-top:4px;">'. $txt['pmx_article_footerhelp'] .'</div>

										<div class="adm_check" style="min-height:20px;">
											<span class="adm_w80">'. $txt['pmx_enable_sitemap'] .'</span>
											<input type="hidden" name="config[show_sitemap]" value="0" />
											<div><input class="input_check" type="checkbox" name="config[show_sitemap]" value="1"' .(!empty($this->cfg['config']['show_sitemap']) ? ' checked="checked"' : ''). ' /></div>
										</div>
									</div>';

		// the group access
		echo '
									<div class="cat_bar catbg_grid">
										<h4 class="catbg catbg_grid">
											<span class="cat_msg_title">'. $txt['pmx_article_groups'] .'</span>
											<img class="grid_click_image pmxleft" onclick=\'Show_help("pmxBH03")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
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
									<div id="pmxBH03" class="info_frame">'. $txt['pmx_article_groupshelp'] .'</div>
									<script type="text/javascript"><!-- // --><![CDATA[
										var pmxgroups = new MultiSelect("pmxgroups");
									// ]]></script>';

		// article moderate
		if(!isset($this->cfg['config']['can_moderate']))
			$this->cfg['config']['can_moderate'] = 1;

		if(allowPmx('pmx_articles, pmx_create', true))
			echo '
									<input type="hidden" name="config[can_moderate]" value="'. $this->cfg['config']['can_moderate'] .'" />';
		else
			echo '
									<div style="height:9px;"></div>
									<div class="cat_bar catbg_grid">
										<h4 class="catbg catbg_grid">
											<span class="cat_msg_title">'. $txt['pmx_article_moderate_title'] .'</span>
											<img class="grid_click_image pmxleft" onclick=\'Show_help("pmxHTMLH03")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
										</h4>
									</div>

									<div class="adm_check">
										<span class="adm_w85">'. $txt['pmx_article_moderate'] .'</span>
										<input type="hidden" name="config[can_moderate]" value="0" />
										<div><input class="input_check" type="checkbox" name="config[can_moderate]" value="1"' .(!empty($this->cfg['config']['can_moderate']) ? ' checked="checked"' : ''). ' /></div>
									</div>
									<div id="pmxHTMLH03" class="info_frame" style="margin-top:4px;">'. $txt['pmx_article_moderatehelp'] .'</div>
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
									<div class="cat_bar catbg_grid  grid_padd">
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
									<div style="clear:both; height:6px;"></div>';

		echo '
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
	function pmxc_AdmArticle_loadinit()
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
