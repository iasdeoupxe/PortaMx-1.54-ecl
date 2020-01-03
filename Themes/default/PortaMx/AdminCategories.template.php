<?php
/**
* \file AdminCategories.template.php
* Template for the Categories Manager.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

/**
* The main Subtemplate.
*/
function template_main()
{
	global $context, $settings, $options, $user_info, $txt, $scripturl;
	global $cfg_titleicons, $cfg_smfgroups;

	if(allowPmx('pmx_admin', true))
	{
		$AdmTabs = array(
			'pmx_center' => $txt['pmx_admincenter'],
			'pmx_settings' => $txt['pmx_settings'],
			'pmx_blocks' => $txt['pmx_blocks'],
			'pmx_categories' => $txt['pmx_categories'],
			'pmx_articles' => $txt['pmx_articles'],
			'pmx_downloads' => $txt['pmx_downloads'],
		);
		$curarea = isset($_GET['area']) ? $_GET['area'] : 'pmx_center';

		if(empty($context['pmx_style_isCore']))
		{
			echo '
			<div id="admin_menu" style="margin-bottom:0.5em;">
				<ul class="dropmenu">';

			foreach($AdmTabs as $name => $desc)
				echo '
					<li>
						<a class="firstlevel'. ($name == $curarea ? ' active' : '') .'" href="'. $scripturl .'?action=portamx;area='. $name .';'. $context['session_var'] .'=' .$context['session_id'] .'">
							<span class="firstlevel">'. $desc .'</span>
						</a>
					</li>';

			echo '
				</ul>
			</div>';
		}
		else
		{
			echo '
			<div class="generic_tab_strip">
				<div class="buttonlist">
					<ul class="reset clearfix">';

			$cnt = count($AdmTabs);
			foreach($AdmTabs as $name => $desc)
			{
				$cnt--;
				echo '
					<li'. ($name == $curarea || $cnt == 0 ? ' class="'. ($cnt == 0 ? 'last"' : 'active"') : '') .'>
						<a href="'. $scripturl .'?action=portamx;area='. $name .';'. $context['session_var'] .'=' .$context['session_id'] .'">
							<span>'. ($name == $curarea ? '<em>'. $desc .'</em>' : $desc) .'</span>
						</a>
					</li>';
			}
			echo '
					</ul>
				</div>
			</div>';
		}
	}

	echo '
		<div class="cat_bar"><h3 class="catbg">'. $txt['pmx_categories'] .'</h3></div>
		<span class="upperframe"><span></span></span>
		<div class="roundframe">'. $txt['pmx_categories_desc'] .'</div>
		<span class="lowerframe"><span></span></span>
		<div style="height:1em;"></div>

		<form id="pmx_form" accept-charset="', $context['character_set'], '" name="PMxAdminCategories" action="' . $scripturl . '?action='. $context['pmx']['AdminMode'] .';area=pmx_categories;'. $context['session_var'] .'=' .$context['session_id'] .'" method="post" style="margin: 0px;">
			<input type="hidden" name="sc" value="', $context['session_id'], '" />
			<input type="hidden" name="sa" value="', $context['pmx']['subaction'], '" />
			<input id="common_field" type="hidden" name="" value="" />
			<input id="extra_cmd" type="hidden" name="" value="" />';

	// ------------------------
	// all categories overview
	// ------------------------
	if($context['pmx']['subaction'] == 'overview')
	{
		$cfg_titleicons = PortaMx_getAllTitleIcons();
		$cfg_smfgroups = PortaMx_getUserGroups();
		$categoryCnt = 0;
		$catIDs = array();

		$LtR = empty($context['right_to_left']) ? 'left' : 'right';
		$RtL = empty($context['right_to_left']) ? 'right' : 'left';

		// common Popup input fields
		echo '
			<input id="pWind.language" type="hidden" value="'. $context['pmx']['currlang'] .'" />
			<input id="pWind.icon.url" type="hidden" value="'. $context['pmx_Iconsurl'] .'" />
			<input id="pWind.image.url" type="hidden" value="'. $context['pmx_imageurl'] .'" />
			<input id="pWind.name" type="hidden" value="" />
			<input id="pWind.id" type="hidden" value="" />
			<input id="pWind.side" type="hidden" value="" />
			<div id="addnodes" style="display:none"></div>';

		echo '
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:5px;">
				<tr>
					<td align="center">
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid">
								<span class="pmx_clickaddnew" title="'. $txt['pmx_categories_add'] .'" onclick="FormFunc(\'add_new_category\', \'1\')"></span>
								<span class="cat_msg_title pmxcenter">'. $txt['pmx_categories_overview'] .'</span>
							</h4>
						</div>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<div class="windowbg">
						<table width="100%" class="table_grid" cellspacing="0" cellpadding="0">
							<tr class="windowbg2 normaltext">
								<td style="padding:3px 5px;"><div id="pWind.xpos.pmxSetMove" style="width:45px;"><b>'. $txt['pmx_categories_order'] .'</b></div></td>
								<td id="pWind.xpos.pmxSetTitle" width="60%" onclick="pWindToggleLang()" title="'. $txt['pmx_toggle_language'] .'" style="cursor:pointer; padding:3px 5px;"><b>'. $txt['pmx_title'] .' [<span id="pWind.def.lang">'. $context['pmx']['currlang'] .'</span>]</b></td>
								<td style="padding:3px 5px;"><div style="width:200px;"><b id="pWind.xpos.pmxSetCatName">'. $txt['pmx_categories_name'] .'</b></div></td>
								<td id="pWind.xpos.pmxShowArt" style="padding:3px 5px;"><div style="width:68px;"><b>'. $txt['pmx_options'] .'</b></div></td>
								<td id="pWind.xpos.pmxSetAcs" style="padding:3px 5px;"><div style="width:90px;"><b>'. $txt['pmx_functions'] .'</b></div></td>
							</tr>';

		// call PmxCategoryOverview for each category
		foreach($context['pmx']['catorder'] as $catorder)
		{
			$cat = PortaMx_getCatByOrder($context['pmx']['categories'], $catorder);
			PmxCategoryOverview($cat);
			$catIDs[] = $cat['id'];
			$categoryCnt++;
		}

		echo '
						</table>
						<input id="pWind.all.ids" type="hidden" name="" value="'. implode(' ', $catIDs) .'" />';

		if(!empty($categoryCnt))
			echo '
						<div style="margin:0 auto; text-align:center;">
							<input style="margin-top:10px; display:none;" class="button_submit" type="button" value="'. $txt['pmx_savechanges'] .'" name="SavePopUpChanges" onclick="FormFunc(\'save_overview\', \'1\')" />
							<input style="margin:10px 10px 0 10px; display:none;" class="button_submit" type="button" value="'. $txt['pmx_cancel'] .'" name="SavePopUpChanges" onclick="pmxCancelPopup(\'SavePopUpChanges\')" />
						</div>
						<span class="botslice"><span></span></span>';
		else
			echo '
						<div class="windowbg2">
						<span class="botslice"><span></span></span>
						</div>';

		echo '
						</div>
					</td>
				</tr>
			</table>';

		// start title edit popup
		if(!empty($categoryCnt))
		{
			echo '
			<div id="pmxSetTitle" class="smalltext" style="position:absolute; width:370px; z-index:9999; display:none;">
				'. pmx_popupHeader($txt['pmx_edit_titles']) .'
					<div style="float:'. $LtR .'; width:100px;">'. $txt['pmx_categories_title'] .'</div>
					<input id="pWind.text" style="width:240px;" type="text" value="" />
					<div style="clear:both; height:10px;">
						<img style="float:'. $LtR .';" src="'. $context['pmx_imageurl'] .'arrow_down.gif" alt="*" title="" />
					</div>
					<div style="float:'. $LtR .'; width:100px;">'. $txt['pmx_edit_title_lang'] .'</div>
					<select id="pWind.lang.sel" style="float:'. $LtR .'; width:26%;" size="1" name="" onchange="pmxChgTitles_Lang(this)">';

			// languages
			foreach($context['pmx']['languages'] as $lang => $sel)
				echo '
						<option value="'. $lang .'">'. $lang .'</option>';

			echo '
					</select>
					<div style="float:'. $LtR .'; padding-' . $LtR .':15px;"><span style="vertical-align:6px;">'. $txt['pmx_edit_title_align'] .'</span>';

			// Title align
			foreach($txt['pmx_edit_title_align_types'] as $key => $val)
				echo '
						<img id="pWind.align.'. $key .'" src="'. $context['pmx_imageurl'] .'text_align_'. $key .'.gif" alt="" title="'. $txt['pmx_edit_title_helpalign']. $val .'" style="vertical-align:2px; cursor:pointer;" onclick="pmxChgTitles_Align(\''. $key .'\')" />';

			echo '
					</div>
					<br style="clear:both;" />
					<input style="float:'. $RtL .'; margin-top:5px;" class="button_submit" type="button" value="'.$txt['pmx_update_save'].'" name="" onclick="pmxUpdateTitles()" />
					<div style="float:'. $LtR .';width:100px; padding-top:5px;">'. $txt['pmx_edit_titleicon'] .'</div>
					<select id="pWind.icon_sel" style="width:26%; margin-top:5px;" size="1" name="" onchange="pmxChgTitles_Icon(this);">
						<option value="none.gif">'. $txt['pmx_edit_no_icon'] .'</option>';

			// Title icons
			foreach($cfg_titleicons as $name)
				echo '
						<option value="'. $name .'">'. $name .'</option>';

			echo '
					</select>
					<img src="'. $context['pmx_imageurl'] .'empty.gif" width="5" alt="*" />
					<img id="pWind.icon" style="padding-top:7px; vertical-align:top;" src="" alt="" />
					<div style="clear:both;"></div>
				</div>
				<span class="lowerframe"><span></span></span>
			</div>';
			// end title edit popup

			// start Categorie name popup
			echo '
			<div id="pmxSetCatName" class="smalltext" style="position:absolute; width:280px; z-index:9999; display:none;">
				'. pmx_popupHeader($txt['pmx_categories_setname']) .'
					<div style="float:'. $LtR .';width:140px; height:25px;">'. $txt['pmx_categories_name'] .':
						<img class="info_toggle" onclick=\'Show_help("pmxBH11")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
					</div>
					<span id="check.name.error" style="display:none;">'. sprintf($txt['namefielderror'], $txt['pmx_categories_name']) .'</span>
					<div style="height:25px;">
						<input id="check.name" style="width:160px;" onkeyup="check_requestname(this)" onkeypress="check_requestname(this)" type="text" value="" />
					</div>
					<div id="pmxBH11" class="info_frame" style="margin-top:25px;">'.
						$txt['pmx_edit_pagenamehelp'] .'
					</div>
					<div style="text-align:'. $RtL .';">
						<input class="button_submit" type="button" value="'. $txt['pmx_update_save'] .'" name="" onclick="pmxUpdateCatName()" />
					</div>
				</div>
				<span class="lowerframe"><span></span></span>
			</div>';
			// end Categorie name popup

			// start articles in cat popup
			echo '
			<div id="pmxShowArt" class="smalltext" style="position:absolute; width:210px; z-index:9999; display:none;">
				'. pmx_popupHeader($txt['pmx_categories_showarts']) .'
					<div id="artsorttxt" style="margin-top:-5px;"></div>
					<div id="artsort" class="smalltext" style="max-height: 30px; overflow:auto;"></div><hr />
					<div id="showarts" style="max-height: 170px; overflow:auto;"></div>
				</div>
				<span class="lowerframe"><span></span></span>
			</div>';
			// start articles in cat popup

			// start Access popup
			echo '
			<div id="pmxSetAcs" class="smalltext" style="position:absolute; width:210px; z-index:9999; display:none;">
				'. pmx_popupHeader($txt['pmx_categories_groups']) .'
					<div style="height:15px;margin-top:-5px;">
						<input id="pWindAcsModeupd" onclick="pmxSetAcsMode(\'upd\')" class="input_check" type="radio" name="_" value="" /><span style="vertical-align:3px; padding:0 3px;">'. $txt['pmx_acs_repl'] .'</span>
					</div>
					<div style="height:15px;">
						<input id="pWindAcsModeadd" onclick="pmxSetAcsMode(\'add\')" class="input_check" type="radio" name="_" value="" /><span style="vertical-align:3px; padding:0 3px;">'. $txt['pmx_acs_add'] .'</span>&nbsp;&nbsp;
					</div>
					<div style="height:15px;margin-bottom:10px;">
						<input id="pWindAcsModedel" onclick="pmxSetAcsMode(\'del\')" class="input_check" type="radio" name="_" value="" /><span style="vertical-align:3px; padding:0 3px;">'. $txt['pmx_acs_rem'] .'</span>
					</div>
					<select id="pWindAcsGroup" onchange="changed(\'pWindAcsGroup\')" style="width:100%;" multiple="multiple" size="6" name="">';

			foreach($cfg_smfgroups as $grp)
				echo '
						<option value="">'. $grp['name'] .'</option>';

			echo '
					</select><br />
					<script type="text/javascript"><!-- // --><![CDATA[
						var pWindAcsGroup = new MultiSelect("pWindAcsGroup");
					// ]]></script>
					<div style="text-align:'. $RtL .'; margin-top:7px;">
						<input class="button_submit" type="button" value="'. $txt['pmx_update_save'] .'" name="" onclick="pmxUpdateAcs()" />&nbsp;
						<input id="acs_all_button" class="button_submit" type="button" value="'. $txt['pmx_update_all'] .'" name="" onclick="pmxUpdateAcs(\'all\')" />
					</div>
				</div>
				<span class="lowerframe"><span></span></span>
			</div>';
			// end Access popup

			// start Move popup
			echo '
			<div id="pmxSetMove" class="smalltext" style="position:absolute; width:330px; z-index:9999; display:none;">
				'. pmx_popupHeader($txt['pmx_categories_movecat']) .'
					<input id="pWind.move.error" type="hidden" value="'. $txt['pmx_categories_move_error'] .'" />
					<div style="float:'. $LtR .';width:130px;">'. $txt['pmx_categories_move'] .'</div>
					<div style="margin-'. $LtR .':130px;" id="pWind.move.catname">&nbsp;</div>
					<div style="float:'. $LtR .';width:126px;margin-top:4px;">'. $txt['pmx_categories_moveplace'] .'</div>
					<div style="margin-'. $LtR .':126px;margin-top:6px;">';

			$opt = 0;
			foreach($txt['pmx_categories_places'] as $artType => $artDesc)
			{
				echo '
						<input id="pWind.place.'. $opt .'" class="input_check" type="radio" name="_" value="'. $artType .'"'. ($artType == 'after' ? ' checked="checked"' : '') .' /><span style="vertical-align:3px; padding:0 3px;">'. $artDesc .'</span><br />';
				$opt++;
			}

			// all exist categories
			echo '
					</div>
					<div style="float:'. $LtR .'; width:130px;margin-top:4px;">'. $txt['pmx_categories_tomovecat'] .'</div>
					<div style="margin-'. $LtR .':130px;margin-top:4px;">
						<select id="pWind.sel.destcat" style="width:180px;" size="1">';

			// output cats
			foreach($context['pmx']['catorder'] as $catorder)
			{
				$cat = PortaMx_getCatByOrder($context['pmx']['categories'], $catorder);
				echo '
							<option value="'. $cat['id'] .'">['. $catorder .']'. str_repeat('&bull;', $cat['level']) .' '. $cat['name'] .'</option>';
			}

			echo '
						</select>
					</div>
					<div style="text-align:'. $RtL .'; margin-top:8px;">
						<input class="button_submit" type="button" value="'. $txt['pmx_save'] .'" name="" onclick="pmxSaveMove()" />
					</div>
				</div>
				<span class="lowerframe"><span></span></span>
			</div>';
			// end Move popup
		}
	}

	// --------------------
	// singlecategorie edit
	// --------------------
	elseif($context['pmx']['subaction'] == 'edit' || $context['pmx']['subaction'] == 'editnew')
	{
		echo '
			<table width="100%" cellpadding="1" cellspacing="1" style="margin-bottom:5px;table-layout:fixed;">
				<tr>
					<td align="center">
						<div class="title_bar">
							<h3 class="titlebg">
							'. $txt['pmx_categories_edit'] .'
							</h3>
						</div>
					</td>
				</tr>';

		// call the ShowAdmBlockConfig() methode
		$context['pmx']['editcategory']->pmxc_ShowAdmCategoryConfig();

		echo '
			</table>';
	}

	echo '
		</form>';
}

/**
* AdmCategoryOverview
* Called for each category.
*/
function PmxCategoryOverview($category)
{
	global $context, $user_info, $modSettings, $txt;
	global $cfg_titleicons, $cfg_smfgroups;

	$LtR = empty($context['right_to_left']) ? 'left' : 'right';
	$RtL = empty($context['right_to_left']) ? 'right' : 'left';

	$category['config'] = unserialize($category['config']);
	if(empty($category['config']['title_align']))
		$category['config']['title_align'] = 'left';
	if(empty($category['config']['title_icon']))
		$category['config']['title_icon'] = 'none.gif';

	// title row
	echo '
							<tr style="height:20px;">
								<td style="padding:3px 5px;white-space:nowrap;">
									<div class="pmx_clickrow'. (count($context['pmx']['catorder']) > 1 ? ' pmx_moveimg" title="'. $txt['pmx_move_categories'] .'" onclick="pmxSetMove(\''. $category['id'] .'\')"' : '"') .'></div>
									<div style="width:23px; text-align:'. $RtL .'; float:'. $LtR .';">'. $category['catorder'] .'</div>
								</td>
								<td style="padding:3px 5px;" id="pWind.ypos.'. $category['id'] .'">
									<div onclick="pmxSetTitle(\''. $category['id'] .'\')"  title="'. $txt['pmx_click_edit_ttl'] .'" style="cursor:pointer;">
										<img id="uTitle.icon.'. $category['id'] .'" align="'.$LtR.'" style="padding-'.$RtL.':4px;" src="'. $context['pmx_Iconsurl'] . $category['config']['title_icon'] .'" alt="*" title="'. substr($txt['pmx_edit_titleicon'], 0, -1) .'" />
										<img id="uTitle.align.'. $category['id'] .'" align="'. $RtL .'" src="'. $context['pmx_imageurl'] .'text_align_'. $category['config']['title_align'] .'.gif" alt="" title="'. $txt['pmx_edit_title_align'] . $txt['pmx_edit_title_align_types'][$category['config']['title_align']] .'" />
										<span id="sTitle.text.'. $category['id'] .'">'. (!empty($category['config']['title'][$user_info['language']]) ? htmlspecialchars($category['config']['title'][$user_info['language']], ENT_QUOTES) : '???') .'</span>';

	foreach($context['pmx']['languages'] as $lang => $sel)
		echo '
										<input id="sTitle.text.'. $lang .'.'. $category['id'] .'" type="hidden"  name="" value="'. (!empty($category['config']['title'][$lang]) ? htmlspecialchars($category['config']['title'][$lang], ENT_QUOTES) : '???') .'" />';

	echo '
										<input id="sTitle.icon.'. $category['id'] .'" type="hidden" value="'. $category['config']['title_icon'] .'" />
										<input id="sTitle.align.'. $category['id'] .'" type="hidden" value="'. $category['config']['title_align'] .'" />
									</div>
								</td>';

	// name row
	$details = PortaMx_getCatDetails($category, $context['pmx']['categories']);
	echo '
								<td id="pmxSetCatName.'. $category['id'] .'" style="padding:3px 5px; cursor:pointer;" onclick="pmxSetCatName(\''. $category['id'] .'\')">
									<input id="pWind.parent.id.'. $category['id'] .'" type="hidden" value="'. $category['parent'] .'" />
									<input id="pWind.move.cat.'. $category['id'] .'" type="hidden" value="['. $category['catorder'] .']'. ($category['level'] > 0 ? ' ' : '') . str_repeat('&bull;', $category['level']) .' '. $category['name'] .'" />
									<div id="pmxSetMove.'. $category['id'] .'" title="'. $details['parent'] . $txt['pmx_editname_categories'] .'" class="'. $details['class'] .'">'. $details['level'] .'
										<span id="pmxSetAcs.'. $category['id'] .'"><span id="pWind.cat.name.'. $category['id'] .'" class="cat_names">'. $category['name'] .'</span></span>
									</div>
								</td>';

	if(!empty($category['acsgrp']))
		list($grpacs, $denyacs) = Pmx_StrToArray($category['acsgrp'], ',', '=');
	else
		$grpacs = $denyacs = array();

	$groups = array();
	foreach($cfg_smfgroups as $grp)
	{
		if(in_array($grp['id'], $grpacs))
			$groups[] = '+'. $grp['id'] .'='. intval(!in_array($grp['id'], $denyacs));
		else
			$groups[] = ':'. $grp['id'] .'=1';
	}

	$sort = array();
	$catarts = array();
	$sorts = explode(',', $category['artsort']);
	foreach($sorts as $s)
		$sort[] = htmlentities($txt['pmx_categories_artsort'][str_replace(array('=0', '=1'), array('', ''), $s)], ENT_QUOTES, $context['pmx']['encoding']) . $txt['pmx_artsort'][intval(substr($s, -1, 1))];

	if(!empty($category['articles']))
	{
		foreach($category['articles'] as $arts)
			$catarts[] = '['. $arts['id'] .'] '. $arts['name'];
	}

	// options row
	echo '
								<td nowrap="nowrap" style="padding:3px 5px;">
									<input id="pWind.catarts.'. $category['id'] .'" type="hidden" value="'. implode('|', $catarts) .'" />
									<input id="pWind.artsorttxt.'. $category['id'] .'" type="hidden" value="'. $txt['pmx_categorie_articlesort'] .'" />
									<input id="pWind.artsort.'. $category['id'] .'" type="hidden" value="'. implode('|', $sort) .'" />
									<input id="pWind.acs.grp.'. $category['id'] .'" type="hidden" value="'. implode(' ', $groups) .'" />
									<div id="pWind.grp.on.'. $category['id'] .'" class="pmx_clickrow pmx_access" title="'. $txt['pmx_categories_groupaccess'] .'"'. (empty($category['acsgrp']) ? ' style="display:none;"' : '') .'></div>
									<div id="pWind.grp.off.'. $category['id'] .'" class="pmx_clickrow"'. (!empty($category['acsgrp']) ? ' style="display:none;"' : '') .'></div>
									<div class="pmx_clickrow'. (!empty($category['config']['cssfile']) ? ' pmx_custcss" title="'. $txt['pmx_categories_cssfile'] : '') .'"></div>
									<div class="pmx_clickrow'. (!empty($category['artsum']) ? ' pmx_articles" title="'. sprintf($txt['pmx_categories_articles'], $category['artsum']) .'" onclick="pmxShowArt(\''. $category['id'] .'\')"' : '"') .'></div>
								</td>';

	// functions row
	echo '
								<td style="padding:3px 5px;" nowrap="nowrap">
									<div class="pmx_clickrow pmx_pgedit" title="'. $txt['pmx_edit_categories'].'" onclick="FormFunc(\'edit_category\', \''. $category['id'] .'\')"></div>
									<div class="pmx_clickrow pmx_grpacs" title="'. $txt['pmx_chg_categoriesaccess'] .'" onclick="pmxSetAcs(\''. $category['id'] .'\')"></div>
									<div class="pmx_clickrow pmx_pgclone" title="'. $txt['pmx_clone_categories'] .'" onclick="FormFunc(\'clone_category\', \''. $category['id'] .'\', \''. $txt['pmx_confirm_categoriesclone'] .'\')"></div>
									<div class="pmx_clickrow pmx_pgdelete" title="'. $txt['pmx_delete_categories'] .'" onclick="FormFunc(\'delete_category\', \''. $category['id'] .'\', \''. $txt['pmx_confirm_categoriesdelete'] .'\')"></div>
								</td>
							</tr>';
}

/**
* Popup Header bar
**/
function pmx_popupHeader($title)
{
	global $context, $txt;

	$LtR = empty($context['right_to_left']) ? 'left' : 'right';
	return '
				<div class="title_bar catbg_grid" style="cursor:pointer; margin-bottom:0;" onclick="pmxRemovePopup()" title="'. $txt['pmx_clickclose'] .'">
					<h4 class="titlebg catbg_grid" style="line-height:25px;">
						<img class="grid_click_image pmxright"  src="'. $context['pmx_imageurl'] .'cross.png" alt="close" style="padding-'.$LtR.':10px;" />
						'. $title .'
					</h4>
				</div>
				<div class="roundframe" style="padding-top:8px;">';
}
?>
