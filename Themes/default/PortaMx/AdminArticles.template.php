<?php
/**
* \file AdminArticles.template.php
* Template for the Articles Manager.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

/**
* The main Subtemplate.
*/
function template_main()
{
	global $context, $settings, $options, $user_info, $txt, $scripturl;

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

	if(!isset($context['pmx']['articlestart']))
		$context['pmx']['articlestart'] = 0;

	echo '
		<div class="cat_bar"><h3 class="catbg">'. $txt['pmx_articles'] .'</h3></div>
		<span class="upperframe"><span></span></span>
		<div class="roundframe">'. $txt['pmx_articles_desc'] .'</div>
		<span class="lowerframe"><span></span></span>
		<div style="height:1em;"></div>

		<form id="pmx_form" accept-charset="'. $context['character_set'] .'" name="PMxAdminArticles" action="' . $scripturl . '?action='. $context['pmx']['AdminMode'] .';area=pmx_articles;'. $context['session_var'] .'=' .$context['session_id'] .'" method="post" style="margin: 0px;">
			<input type="hidden" name="sc" value="'. $context['session_id'] .'" />
			<input type="hidden" name="sa" value="'. $context['pmx']['subaction'] .'" />
			<input type="hidden" name="articlestart" value="'. $context['pmx']['articlestart'] .'" />
			<input type="hidden" name="fromblock" value="'. (!empty($context['pmx']['fromblock']) ? $context['pmx']['fromblock'] : '') .'" />
			<input id="common_field" type="hidden" name="" value="" />
			<input id="extra_cmd" type="hidden" name="" value="" />';

	// ---------------------
	// all articles overview
	// ---------------------
	if($context['pmx']['subaction'] == 'overview')
	{
		$cfg_titleicons = PortaMx_getAllTitleIcons();
		$cfg_smfgroups = PortaMx_getUserGroups();
		$categories = PortaMx_getCategories();
		$LtR = empty($context['right_to_left']) ? 'left' : 'right';
		$RtL = empty($context['right_to_left']) ? 'right' : 'left';

		// create the pageindex
		$cururl = (!empty($_GET) ? pmx_http_build_query($_GET, '', ';') .';' : '');
		$pageindex = constructPageIndex($scripturl . '?'. $cururl .'pg=%1$d#top', $context['pmx']['articlestart'], $context['pmx']['totalarticles'], $context['pmx']['settings']['manager']['artpage'], true);
		$pageindex = str_replace(';start=%1$d', '', $pageindex);

		// common Popup input fields
		echo '
			<input id="pWind.language" type="hidden" value="'. $context['pmx']['currlang'] .'" />
			<input id="pWind.icon.url" type="hidden" value="'. $context['pmx_Iconsurl'] .'" />
			<input id="pWind.image.url" type="hidden" value="'. $context['pmx_imageurl'] .'" />
			<input id="pWind.name" type="hidden" value="" />
			<input id="pWind.id" type="hidden" value="" />
			<input id="pWind.side" type="hidden" value="" />
			<input id="set.filter.category" type="hidden" name="filter[category]" value="'. $_SESSION['PortaMx']['filter']['category'] .'" />
			<input id="set.filter.approved" type="hidden" name="filter[approved]" value="'. $_SESSION['PortaMx']['filter']['approved'] .'" />
			<input id="set.filter.active" type="hidden" name="filter[active]" value="'. $_SESSION['PortaMx']['filter']['active'] .'" />
			<input id="set.filter.myown" type="hidden" name="filter[myown]" value="'. $_SESSION['PortaMx']['filter']['myown'] .'" />
			<input id="set.filter.member" type="hidden" name="filter[member]" value="'. $_SESSION['PortaMx']['filter']['member'] .'" />
			<div id="addnodes" style="display:none"></div>';

		// start row move popup
		echo '
			<div id="pmxRowMove" class="smalltext" style="position:absolute; width:310px; z-index:9999; display:none;">
				'. pmx_popupHeader($txt['pmx_rowmove_title']) .'
					<input id="pWind.move.error" type="hidden" value="'. $txt['pmx_rowmove_error'] .'" />
					<div style="float:'. $LtR .';width:110px;">
						'. $txt['pmx_rowmove'] .'<br />
						<div style="margin-top:5px;">'. $txt['pmx_rowmove_place'] .'</div><br />
						<div style="margin-top:10px;">'. $txt['pmx_rowmove_to'] .'</div>
					</div>
					<div style="padding-'. $LtR .':112px;">
						<div style="margin-'. $LtR .':5px; margin-top:2px;" id="pWind.move.pos"></div>
						<div style="margin-top:5px;">
							<input id="pWind.place.0" class="input_check" type="radio" name="_" value="before" /><span style="vertical-align:3px; padding:0 3px;">'. $txt['pmx_rowmove_before'] .'</span><br />
							<input id="pWind.place.1" class="input_check" type="radio" name="_" value="after" checked="checked" /><span style="vertical-align:3px; padding:0 3px;">'. $txt['pmx_rowmove_after'] .'</span><br />
						</div>
						<select id="pWind.sel" style="width:172px; margin-top:8px; margin-'. $LtR .':5px;" size="1">';

		foreach($context['pmx']['article_rows'] as $id => $data)
			echo '
							<option value="'. $id .'">['. $id .'] '. $data['name'] .' ('. $data['cat'] .')</option>';

		echo '
						</select>
					</div>
					<div style="clear:both; text-align:'. $RtL .'; margin-top:7px;">
						<input class="button_submit" type="button" value="'. $txt['pmx_save'] .'" name="" onclick="pmxSendArtMove()" />
					</div>
				</div>
				<span class="lowerframe"><span></span></span>
			</div>';
		// end Move popup

		// start articletype selection popup
		echo '
			<div id="pmxArticleType" class="smalltext" style="position:absolute; width:200px; z-index:9999; display:none;">
				'. pmx_popupHeader($txt['pmx_add_new_articletype']) .'
					<div style="margin:-4px 0 5px 0;">'. $txt['pmx_articles_articletype'] .'</div>
					<select id="pmx.article.type" style="width:100%;" size="1">';

		foreach($txt['pmx_articles_types'] as $articleType => $articleDesc)
			echo '
						<option value="'. $articleType .'">'. $articleDesc .'</option>';

		echo '
					</select>
					<div style="text-align:'. $RtL .'; margin-top:7px;">
						<input class="button_submit" type="button" value="'. $txt['pmx_create'] .'" name="" onclick="pmxSendArticleType()" />
					</div>
				</div>
				<span class="lowerframe"><span></span></span>
			</div>';
		// end popup

		// start filter popup
		echo '
			<div id="pmxSetFilter" class="smalltext" style="position:absolute; width:280px; z-index:9999; display:none;">
				'. pmx_popupHeader($txt['pmx_article_setfilter']) .'
					<div style="padding-bottom:3px; margin-top:-4px;">'. $txt['pmx_article_filter_category'] .'<span style="float:'. $RtL .'; cursor:pointer" onclick="pmxSetFilterCatClr()">[<b>'. $txt['pmx_article_filter_categoryClr'] .'</b>]</span></div>
					<select id="pWind.filter.category" style="width:100%;" size="4" name="" multiple="multiple">';

		$selcats = array_merge(array(PortaMx_getDefaultCategory($txt['pmx_categories_none'])), $categories);
		$ordercats = array_merge(array(0), $context['pmx']['catorder']);
		$catfilter = Pmx_StrToArray($_SESSION['PortaMx']['filter']['category']);
		$isWriter = allowPmx('pmx_create, pmx_articles', true);
		$isAdm = allowPmx('pmx_admin');
		foreach($ordercats as $catorder)
		{
			$cat = PortaMx_getCatByOrder($selcats, $catorder);
			$cfg = unserialize($cat['config']);
			if(!empty($isAdm) || (!empty($isWriter) && empty($cfg['global'])))
				echo '
						<option value="'. $cat['id'] .'"'. (in_array($cat['id'], $catfilter) ? ' selected="selected"' : '') .'>'. str_repeat('&bull;', $cat['level']) .' '. $cat['name'] .'</option>';
		}
		echo '
					</select><br />
					<div style="height:20px;padding-top:4px;">
						'. $txt['pmx_article_filter_approved'] .'
						<input id="pWind.filter.approved" style="float:'. $RtL .'" class="input_check" type="checkbox" value="1"'. (!empty($_SESSION['PortaMx']['filter']['approved']) ? ' checked="checked"' : '') .' />
					</div>
					<div style="height:20px;">
						'. $txt['pmx_article_filter_active'] .'
						<input id="pWind.filter.active" style="float:'. $RtL .'" class="input_check" type="checkbox" value="1"'. (!empty($_SESSION['PortaMx']['filter']['active']) ? ' checked="checked"' : '') .' />
					</div>';

		if(allowPmx('pmx_articles, pmx_admin'))
			echo '
					<div style="height:20px;">
						'. $txt['pmx_article_filter_myown'] .'
						<input id="pWind.filter.myown" style="float:'. $RtL .'" class="input_check" type="checkbox" value="1"'. (!empty($_SESSION['PortaMx']['filter']['myown']) ? ' checked="checked"' : '') .' />
					</div>
					<div style="height:15px;">
						'. $txt['pmx_article_filter_member'] .'
						<input id="pWind.filter.member" style="float:'. $RtL .'; width:130px;" class="input_text" type="text" value="'. $_SESSION['PortaMx']['filter']['member'] .'" />
					</div><sup>'. $txt['pmx_article_filter_membername'] .'</sup><br />';

		echo '
					<div style="text-align:'. $RtL .'">
						<input class="button_submit" type="button" value="'. $txt['pmx_save'] .'" name="" onclick="pmxSendFilter()" />
					</div>
				</div>
				<span class="lowerframe"><span></span></span>
			</div>';
		// end filter popup

		$filterActive = ($_SESSION['PortaMx']['filter']['category'] != '' || $_SESSION['PortaMx']['filter']['approved'] != 0 || $_SESSION['PortaMx']['filter']['active'] != 0 || $_SESSION['PortaMx']['filter']['myown'] != 0 || $_SESSION['PortaMx']['filter']['member'] != '');

		// top pageindex
		echo '
		<a name="top"></a>
		<div class="smalltext pmx_pgidx_top">'. $txt['pages']. ': '. $pageindex . $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#bot"><strong>' . $txt['go_down'] . '</strong></a></div>';

		echo '
		<div style="overflow:hidden;">
		<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:5px;">
			<tr>
				<td align="center">
					<div class="cat_bar catbg_grid">
						<h4 class="catbg catbg_grid">
							<span'. (allowPmx('pmx_create, pmx_admin') ? ' class="pmx_clickaddnew" title="'. $txt['pmx_articles_add'] .'" onclick="SetpmxArticleType()"' : '') .'></span>
							<span class="cat_msg_title pmxcenter">'. $txt['pmx_articles_overview'] .'</span>
						</h4>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<div class="windowbg">
					<table id="main.table" width="100%" class="table_grid" cellspacing="0" cellpadding="0">
						<tr class="windowbg2 normaltext">
							<td id="pWind.xpos.pmxRowMove" style="padding:3px 5px;"><div style="width:24px;text-align:center;"></div></td>
							<td id="pWind.xpos.pmxSetTitle" width="50%" onclick="pWindToggleLang()" title="'. $txt['pmx_toggle_language'] .'" style="cursor:pointer; padding:3px 5px;"><b>'. $txt['pmx_title'] .' [<span id="pWind.def.lang">'. $context['pmx']['currlang'] .'</span>]</b></td>
							<td id="pWind.xpos.pmxArticleInfo" style="padding:3px 5px;"><div style="width:110px;"><b>'. $txt['pmx_articles_type'] .'</b></div></td>
							<td id="pWind.xpos.pmxSetCats" style="padding:3px 5px;">
								<div id="pWind.xpos.pmxSetFilter" class="pmx_filtertxt"><b id="pWind.ypos.0">'. $txt['pmx_articles_catname'] .'</b>
									<span class="pmx_'. (empty($filterActive) ? 'nofilter' : 'filter') .'" title="'. $txt['pmx_article_filter'] .'" onclick="pmxSetFilter()"></span>
								</div>
							</td>
							<td style="padding:3px 5px;"><div style="width:63px;"><b>'. $txt['pmx_options'] .'</b></div></td>
							<td id="pWind.xpos.pmxArticleType" style="padding:3px 5px;" align="center"><b>'. $txt['pmx_status'] .'</b></td>
							<td id="pWind.xpos.pmxSetAcs" style="padding:3px 5px;"><div style="width:91px;"><b>'. $txt['pmx_functions'] .'</b></div></td>
						</tr>';

		// call PmxArticleOverview for each article
		$articleCnt = count($context['pmx']['articles']);
		$artIDs = array();
		foreach($context['pmx']['articles'] as $article)
		{
			PmxArticleOverview($article, $cfg_titleicons, $cfg_smfgroups, $categories);
			$artIDs[] = $article['id'];
		}

		echo '
					</table>
					<a name="bot"></a>
					<input id="pWind.all.ids" type="hidden" name="" value="'. implode(' ', $artIDs) .'" />';

		if(!empty($articleCnt))
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
		</table>
		</div>';

		// bottom pageindex
		echo '
		<div class="smalltext pmx_pgidx_top">'. $txt['pages']. ': '. $pageindex . $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#top"><strong>' . $txt['go_up'] . '</strong></a></div>';

		// start title edit popup
		if(!empty($articleCnt))
		{
			echo '
			<div id="pmxSetTitle" class="smalltext" style="position:absolute; width:370px; display:none;">
				'. pmx_popupHeader($txt['pmx_edit_titles']) .'
					<div style="float:'. $LtR .'; width:100px;">'. $txt['pmx_article_title'] .'</div>
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

			// start info popup
			echo '
			<div id="pmxArticleInfo" class="smalltext" style="position:absolute; width:340px; display:none;">
				<div class="title_bar catbg_grid" style="cursor: pointer;" onclick="pmxRemovePopup()" title="'. $txt['pmx_clickclose'] .'">
					<h4 id="pWind.title" class="titlebg catbg_grid" style="line-height:25px;">
						<img class="grid_click_image pmx'. $RtL .'" src="'. $context['pmx_imageurl'] .'cross.png" alt="close" />
					</h4>
				</div>
				<div class="roundframe smalltext" style="padding-top:3px;">
					<input id="pWind.info.names" type="hidden" value="reqname access created updated approved active" />
					<div id="pWind.info.text">
					</div>
				</div>
				<span class="lowerframe"><span></span></span>
			</div>
			<div style="display:none;">
				<span id="pWind.title.txt">'. $txt['pmx_article_information'] .'<br /></span>
				<span id="pWind.reqname.txt">'. $txt['pmx_article_info_reqname'] .'<br /></span>
				<span id="pWind.access.txt">'. $txt['pmx_article_info_access'] .'<br /></span>
				<span id="pWind.created.txt">'. $txt['pmx_article_info_created'] .'<br /></span>
				<span id="pWind.updated.txt">'. $txt['pmx_article_info_updated'] .'<br /></span>
				<span id="pWind.updated.not.txt">'. $txt['pmx_article_info_not_updated'] .'<br /></span>
				<span id="pWind.approved.txt">'. $txt['pmx_article_info_approved'] .'<br /></span>
				<span id="pWind.approved.not.txt">'. $txt['pmx_article_info_not_approved'] .'<br /></span>
				<span id="pWind.active.txt">'. $txt['pmx_article_info_activated'] .'<br /></span>
				<span id="pWind.active.not.txt">'. $txt['pmx_article_info_not_activated'] .'<br /></span>';

			if(empty($context['pmx']['settings']['disableHS']))
				echo '
				<span id="pWind.preview.cmd"><a href="#" onclick="return hs.htmlExpand(this, { maincontentId: \'article_cont[00]\', align: \'center\', wrapperClassName: \'highslide-wrapper-html\'})">'. $txt['pmx_article_info_preview'] .'</a></span>';

			echo '
			</div>';
			// end info popup

			// categorie popup
			echo '
			<div id="pmxSetCats" class="smalltext" style="position:absolute; width:220px; display:none;">
				'. pmx_popupHeader($txt['pmx_category_popup']) .'
					<select id="pWind.cats.sel" onchange="pmxChgCats(this)" style="width:100%;" size="6" name="">';

			$selcats = array_merge(array(PortaMx_getDefaultCategory($txt['pmx_categories_none'])), $categories);
			$ordercats = array_merge(array(0), $context['pmx']['catorder']);
			$isWriter = allowPmx('pmx_create, pmx_articles', true);
			$isAdm = allowPmx('pmx_admin');

			foreach($ordercats as $catorder)
			{
				$cat = PortaMx_getCatByOrder($selcats, $catorder);
				$cfg = unserialize($cat['config']);
				if(!empty($isAdm) || (!empty($isWriter) && empty($cfg['global'])))
				{
				  $details = PortaMx_getCatDetails($cat, $selcats);
				  $details['parent'] .= $txt['pmx_chg_articlcats'];
				  $catdetais[] = $cat['id'] .'|'. $details['level'] .'|'. $details['parent'] .'|'. $details['class'] .'|'. $cat['name'];

				  echo '
						<option value="'. $cat['id'] .'">'. str_repeat('&bull;', $cat['level']) .' '. $cat['name'] .'</option>';
				}
			}

			echo '
					</select><br />
					<input id="pWind.all.catdata" type="hidden" name="" value="'. implode('{}', str_replace(array('<', '>'), array('&lt;', '&gt;'), $catdetais)) .'" />
					<div style="text-align:'. $RtL .'; margin-top:7px;">
						<input class="button_submit" type="button" value="'. $txt['pmx_update_save'] .'" name="" onclick="pmxUpdateCats()" />&nbsp;
						<input class="button_submit" type="button" value="'. $txt['pmx_update_all'] .'" name="" onclick="pmxUpdateCats(\'all\')" />
					</div>
				</div>
				<span class="lowerframe"><span></span></span>
			</div>';
			// end categorie popup

			// start Access popup
			echo '
			<div id="pmxSetAcs" class="smalltext" style="position:absolute; width:210px; display:none;">
				'. pmx_popupHeader($txt['pmx_article_groups']) .'
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
		}
	}

	// --------------------
	// singleblock edit
	// --------------------
	elseif($context['pmx']['subaction'] == 'edit' || $context['pmx']['subaction'] == 'editnew')
	{
		echo '
			<table width="100%" cellpadding="1" cellspacing="1" style="margin-bottom:5px;table-layout:fixed;">
				<tr>
					<td align="center">
						<div class="title_bar">
							<h3 class="titlebg">
							'. $txt['pmx_article_edit'] .' '. $txt['pmx_articles_types'][$context['pmx']['editarticle']->cfg['ctype']] .'
							</h3>
						</div>
					</td>
				</tr>';

		// call the ShowAdmBlockConfig() methode
		$context['pmx']['editarticle']->pmxc_ShowAdmArticleConfig();

		echo '
			</table>';
	}

	echo '
		</form>';
}

/**
* AdmArticleOverview
* Called for each artile.
*/
function PmxArticleOverview($article, &$cfg_titleicons, &$cfg_smfgroups, $categories)
{
	global $context, $user_info, $modSettings, $txt;

	$LtR = empty($context['right_to_left']) ? 'left' : 'right';
	$RtL = empty($context['right_to_left']) ? 'right' : 'left';

	if(empty($article['config']['title_align']))
		$article['config']['title_align'] = 'left';

	if(empty($article['config']['title_icon']))
		$article['config']['title_icon'] = 'none.gif';

	if(!empty($article['acsgrp']))
		list($grpacs, $denyacs) = Pmx_StrToArray($article['acsgrp'], ',', '=');
	else
		$grpacs = $denyacs = array();

	// ID row
	echo '
							<tr style="height:20px;">
								<td id="pWind.pos'. $article['id'] .'" style="padding:3px 2px 3px 5px;white-space:nowrap;">';

	if(count($context['pmx']['article_rows']) > 1)
		echo '
									<div id="Img.RowMove.'. $article['id'] .'" class="pmx_clickrow'. (allowPmx('pmx_articles, pmx_admin') ? ' pmx_moveimg" title="'. $txt['pmx_rowmove_updown'] .'" onclick="pmxArtMove(\''. $article['id'] .'\', \'<b>'. $article['name'] .'</b> ('. $article['cat'] .')\')"' : '"') .'></div>';

	echo '
								</td>';

	// title row
	echo '
								<td style="padding:3px 5px;" id="pWind.ypos.'. $article['id'] .'">';

	// Start HighSlide code for Article preview
	if(empty($context['pmx']['settings']['disableHS']))
	{
		echo '
									<div class="highslide-maincontent" id="article_cont'. $article['id'] .'" style="display:none;">';

		if($article['ctype'] == 'bbc')
		{
			$tmp = parse_bbc($article['content'], false);
			echo PortaMx_BBCsmileys($tmp);
			unset($tmp);
		}
		elseif($article['ctype'] == 'php')
			echo $article['content'];
		else
			echo $article['content'];

		echo '
									</div>';
	}
	// End HighSlide code for Article preview

	echo '
									<div onclick="pmxSetTitle(\''. $article['id'] .'\')"  title="'. $txt['pmx_click_edit_ttl'] .'" style="cursor:pointer;">
										<img id="uTitle.icon.'. $article['id'] .'" align="'.$LtR.'" style="padding-'.$RtL.':4px;" src="'. $context['pmx_Iconsurl'] . $article['config']['title_icon'] .'" alt="*" title="'. substr($txt['pmx_edit_titleicon'], 0, -1) .'" />
										<img id="uTitle.align.'. $article['id'] .'" align="'. $RtL .'" src="'. $context['pmx_imageurl'] .'text_align_'. $article['config']['title_align'] .'.gif" alt="" title="'. $txt['pmx_edit_title_align'] . $txt['pmx_edit_title_align_types'][$article['config']['title_align']] .'" />
										<span id="sTitle.text.'. $article['id'] .'">'. (!empty($article['config']['title'][$context['pmx']['currlang']]) ? htmlspecialchars($article['config']['title'][$context['pmx']['currlang']], ENT_QUOTES) : '???') .'</span>';

	foreach($context['pmx']['languages'] as $lang => $sel)
		echo '
										<input id="sTitle.text.'. $lang .'.'. $article['id'] .'" type="hidden"  name="" value="'. (!empty($article['config']['title'][$lang]) ? htmlspecialchars($article['config']['title'][$lang], ENT_QUOTES) : '???') .'" />';

	echo '
										<input id="sTitle.icon.'. $article['id'] .'" type="hidden" value="'. $article['config']['title_icon'] .'" />
										<input id="sTitle.align.'. $article['id'] .'" type="hidden" value="'. $article['config']['title_align'] .'" />
									</div>
								</td>';

	// type row
	echo '
								<td style="padding:3px 5px;">
									<input id="pWind.ctype.'. $article['id'] .'" type="hidden" value="'. $article['ctype'] .'" />
									<div onclick="pmxArticleInfo(\''. $article['id'] .'\')" style="cursor:pointer;" title="'. $txt['pmx_articles_info'] .'">
										<img align="'.$LtR.'" style="padding-'.$RtL.':5px; cursor:pointer;" src="'. $context['pmx_imageurl'] .'type_'. $article['ctype'] .'.gif" alt="*" title="'. $article['ctype'] .'" />'. $txt['pmx_articles_types'][$article['ctype']] .'
									</div>
									<div style="display:none">';

	// create the group acs data
	$groups = array();
	$access = array();
	foreach($cfg_smfgroups as $grp)
	{
		if(in_array($grp['id'], $grpacs))
		{
			$groups[] = '+'. $grp['id'] .'='. intval(!in_array($grp['id'], $denyacs));
			$access[] = in_array($grp['id'], $denyacs) ? '^'. $grp['name'] : $grp['name'];
		}
		else
			$groups[] = ':'. $grp['id'] .'=1';
	}

	echo '
										<input id="pWind.acs.grp.'. $article['id'] .'" type="hidden" value="'. implode(' ', $groups) .'" />
										<span id="reqname.'. $article['id'] .'">1[|]'. (empty($article['name']) ? $txt['pmx_article_info_not_defined'] : $article['name']) .'</span>
										<span id="access.'. $article['id'] .'">1[|]'. (empty($access) ? $txt['pmx_article_info_not_defined'] : implode(', ', $access)) .'</span>
										<span id="created.'. $article['id'] .'">'. timeformat($article['created']) .'[|]'. (isset($context['pmx']['articles_member'][$article['owner']]) ? $context['pmx']['articles_member'][$article['owner']] : $txt['pmx_user_unknown']) .'</span>
										<span id="updated.'. $article['id'] .'">'. (empty($article['updated']) ? '0[|]0' : timeformat($article['updated']) .'[|]'. (isset($context['pmx']['articles_member'][$article['updatedby']]) ? $context['pmx']['articles_member'][$article['updatedby']] : $txt['pmx_user_unknown'])) .'</span>
										<span id="approved.'. $article['id'] .'">'. (empty($article['approved']) ? '0[|]0' : timeformat($article['approved']) .'[|]'. (isset($context['pmx']['articles_member'][$article['approvedby']]) ? $context['pmx']['articles_member'][$article['approvedby']] : $txt['pmx_user_unknown'])) .'</span>
										<span id="active.'. $article['id'] .'">'. (empty($article['active']) ? '0[|]0' : '1[|]'. timeformat($article['active'])) .'</span>
									</div>
								</td>';

	// category row
	echo '
								<td id="pmxSetCats.'. $article['id'] .'" valign="middle" style="padding:3px 5px;">';

	$cat = PortaMx_getCatByID($categories, $article['catid']);
	$details = PortaMx_getCatDetails($cat, $categories);
	if(empty($cat))
	{
		$cat['id'] = 0;
		$cat['name'] = $txt['pmx_categories_none'];
	}
	$details['parent'] .= $txt['pmx_chg_articlcats'];

	echo '
									<input id="pWind.catid.'. $article['id'] .'" type="hidden" value="'. $cat['id'] .'" />
									<div onclick="pmxSetCats(\''. $article['id'] .'\')" style="cursor:pointer;" title="'. $txt['pmx_chg_articlcats'] .'">
										<div id="pWind.catclass.'. $article['id'] .'" title="'. $details['parent'] .'" class="'. $details['class'] .'">
											<span id="pWind.catlevel.'. $article['id'] .'">'. $details['level'] .'</span>
											<span class="cat_names" id="pWind.catname.'. $article['id'] .'">'. $cat['name'] .'</span>
										</div>
									</div>
								</td>';

	// options row
	echo '
								<td id="pmxSetAcs.'. $article['id'] .'" nowrap="nowrap" style="padding:3px 5px;">
									<div>
										<div id="pWind.grp.on.'. $article['id'] .'" class="pmx_clickrow pmx_access" title="'. $txt['pmx_article_groupaccess'] .'"'. (empty($article['acsgrp']) ? ' style="display:none;"' : '') .'></div>
										<div id="pWind.grp.off.'. $article['id'] .'" class="pmx_clickrow"'. (!empty($article['acsgrp']) ? ' style="display:none;"' : '') .'></div>
										<div class="pmx_clickrow'. (!empty($article['config']['can_moderate']) ? ' pmx_moderate"  title="'. $txt['pmx_article_modaccess'] : '') .'"></div>
										<div class="pmx_clickrow'. (!empty($article['config']['cssfile']) ? ' pmx_custcss" title="'. $txt['pmx_article_cssfile'] : '') .'"></div>
									</div>
								</td>';

	// status row
	echo '
								<td style="padding:3px 5px;" nowrap="nowrap" align="center">
									<div class="pmx_clickrow'. ($article['approved'] ? ' pmx_approved" title="'. $txt['pmx_article_approved'] : ' pmx_notapproved" title="'. $txt['pmx_article_not_approved']) . (allowPmx('pmx_articles, pmx_admin') ? ' - '. $txt['pmx_status_change'] .'" onclick="FormFunc(\'chg_approved\', \''. $article['id'] .'\')"' : '"') .'></div>
									<div class="pmx_clickrow'. ($article['active'] ? ' pmx_active" title="'. $txt['pmx_status_activ'] : ' pmx_inactive" title="'. $txt['pmx_status_inactiv']) .' - '. $txt['pmx_status_change'] .'" onclick="FormFunc(\'chg_status\', \''. $article['id'] .'\')"></div>
								</td>';

	// functions row
	echo '
								<td style="padding:3px 5px;" nowrap="nowrap">
									<div class="pmx_clickrow pmx_pgedit" title="'. $txt['pmx_edit_article'].'" onclick="FormFunc(\'edit_article\', \''. $article['id'] .'\')"></div>
									<div class="pmx_clickrow pmx_grpacs" title="'. $txt['pmx_chg_articleaccess'] .'" onclick="pmxSetAcs(\''. $article['id'] .'\')"></div>
									<div class="pmx_clickrow'. (allowPmx('pmx_admin') || (allowPmx('pmx_create') && $article['owner'] == $user_info['id']) ? ' pmx_pgclone" title="'. $txt['pmx_clone_article'] .'" onclick="FormFunc(\'clone_article\', \''. $article['id'] .'\', \''. $txt['pmx_confirm_articlclone'] .'\')"' : '"') .'></div>
									<div class="pmx_clickrow'. (allowPmx('pmx_admin') || (allowPmx('pmx_create') && $article['owner'] == $user_info['id']) ? ' pmx_pgdelete" title="'. $txt['pmx_delete_article'] .'" onclick="FormFunc(\'delete_article\', \''. $article['id'] .'\', \''. $txt['pmx_confirm_articledelete'] .'\')"' : '"') .'></div>
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
				<div class="title_bar catbg_grid" style="cursor: pointer; pointer;margin-bottom:0;" onclick="pmxRemovePopup()" title="'. $txt['pmx_clickclose'] .'">
					<h4 class="titlebg catbg_grid" style="line-height:25px;">
						<img class="grid_click_image pmx'. (!empty($context['right_to_left']) ? 'left' : 'right') .'" src="'. $context['pmx_imageurl'] .'cross.png" alt="close" style="padding-'.$LtR.':10px;" />
						'. $title .'
					</h4>
				</div>
				<div class="roundframe" style="padding-top:8px;">';
}
?>
