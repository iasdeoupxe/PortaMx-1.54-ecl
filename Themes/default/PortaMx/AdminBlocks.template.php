<?php
/**
* \file AdminBlocks.template.php
* Template for the Blocks Manager.
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
	global $context, $settings, $options, $user_info, $txt, $scripturl, $modSettings;

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

	$sections = ($context['pmx']['subaction'] == 'all' ? array_keys($txt['pmx_admBlk_sides']) : Pmx_StrToArray($context['pmx']['subaction']));
	if(!allowPmx('pmx_admin', true) && allowPmx('pmx_blocks', true))
	{
		if(!isset($context['pmx']['settings']['manager']['admin_pages']))
			$context['pmx']['settings']['manager']['admin_pages'] = array();

		$showBlocks = array_intersect($sections, $context['pmx']['settings']['manager']['admin_pages']);
		$MenuTabs = array_merge(array('all'), $context['pmx']['settings']['manager']['admin_pages']);
	}
	else
	{
		$showBlocks = $sections;
		$MenuTabs = array_keys($txt['pmx_admBlk_panels']);
	}

	if($context['pmx']['function'] == 'edit' || $context['pmx']['function'] == 'editnew')
		$active = array($context['pmx']['editblock']->getConfigData('side'));
	else
		$active = explode(',', $context['pmx']['subaction']);

	echo '
		<div class="cat_bar"><h3 class="catbg">'. $txt['pmx_blocks'] .'</h3></div>
		<span class="upperframe"><span></span></span>
		<div class="roundframe">'. $txt['pmx_admBlk_desc'] .'</div>
		<span class="lowerframe"><span></span></span>';

	if(empty($context['pmx_style_isCore']))
	{
		echo '
		<div id="adm_submenus" style="margin-top:1.1em;margin-bottom:6px;overflow:hidden;">
			<ul class="dropmenu">';

		foreach($MenuTabs as $name)
			echo '
				<li>
					<a class="firstlevel'. (in_array($name, $active) ? ' active' : '') .'" href="'. $scripturl .'?action='. $context['pmx']['AdminMode'] .';area=pmx_blocks;sa='. $name .';'. $context['session_var'] .'=' .$context['session_id'] .'">
						<span class="firstlevel">'. $txt['pmx_admBlk_panels'][$name] .'</span>
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

		$cnt = count($MenuTabs);
		foreach($MenuTabs as $name)
		{
			$cnt--;
			echo '
				<li'. (in_array($name, $active) || $cnt == 0 ? ' class="'. ($cnt == 0 ? 'last"' : 'active"') : '') .'>
					<a href="'. $scripturl .'?action='. $context['pmx']['AdminMode'] .';area=pmx_blocks;sa='. $name .';'. $context['session_var'] .'=' .$context['session_id'] .'">
						<span>'. (in_array($name, $active) ? '<em>'. $txt['pmx_admBlk_panels'][$name] .'</em>' : $txt['pmx_admBlk_panels'][$name]) .'</span>
					</a>
				</li>';
		}
		echo '
				</ul>
			</div>
		</div>';
	}

	echo '
		<form id="pmx_form" accept-charset="'. $context['character_set'] .'" name="PMxAdminBlocks" action="' . $scripturl . '?action='. $context['pmx']['AdminMode'] .';area=pmx_blocks;sa='. $context['pmx']['subaction'] .';'. $context['session_var'] .'=' .$context['session_id'] .'" method="post" style="margin: 0px;">
			<input type="hidden" name="sc" value="'. $context['session_id'] .'" />
			<input type="hidden" name="function" value="'. $context['pmx']['function'] .'" />
			<input type="hidden" name="sa" value="'. $context['pmx']['subaction'] .'" />
			<input id="common_field" type="hidden" name="" value="" />
			<input id="extra_cmd" type="hidden" name="" value="" />';

	// ---------------------
	// all Blocks overview
	// ---------------------
	if($context['pmx']['function'] == 'overview')
	{
		$cfg_titleicons = PortaMx_getAllTitleIcons();
		$cfg_smfgroups = PortaMx_getUserGroups();
		$LtR = empty($context['right_to_left']) ? 'left' : 'right';
		$RtL = empty($context['right_to_left']) ? 'right' : 'left';

		// common Popup input fields
		echo '
			<input id="pWind.icon.url" type="hidden" value="'. $context['pmx_Iconsurl'] .'" />
			<input id="pWind.image.url" type="hidden" value="'. $context['pmx_imageurl'] .'" />
			<input id="pWind.name" type="hidden" value="" />
			<input id="pWind.id" type="hidden" value="" />
			<input id="pWind.side" type="hidden" value="" />';

		// start blocktype selection popup
		echo '
			<div id="pmxBlockType" class="smalltext" style="position:absolute; width:220px; display:none;">
				'. pmx_popupHeader('') .'
					<div style="margin:-4px 0 5px 0;">'. $txt['pmx_blocks_blocktype'] .'</div>
					<input id="pWind.blocktype.title" type="hidden" value="'. $txt['pmx_add_new_blocktype'] .'" />
					<select id="pmx.block.type" style="width:100%;" size="1">';

		$RegBlocks = eval($context['pmx']['registerblocks']);
		ksort($RegBlocks, SORT_STRING);

		foreach($RegBlocks as $blocktype => $blockDesc)
			echo '
						<option value="'. $blocktype .'">'. $blockDesc['description'] .'</option>';

		echo '
					</select>
					<div style="text-align:'. $RtL .'; margin-top:7px;">
						<input class="button_submit" type="button" value="'. $txt['pmx_create'] .'" name="" onclick="pmxSendBlockType()" />
					</div>
				</div>
				<span class="lowerframe"><span></span></span>
			</div>';
		// end blocktype popup

		foreach($showBlocks as $side)
		{
			$blockCnt = (!empty($context['pmx']['blocks'][$side]) ? count($context['pmx']['blocks'][$side]) : 0);
			$paneldesc = htmlentities($txt['pmx_admBlk_sides'][$side], ENT_QUOTES, $context['pmx']['encoding']);

			echo '
			<div id="addnodes.'. $side .'" style="display:none"></div>
			<div style="overflow:hidden;">
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:5px;">
				<tr>
					<td align="center">
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid">
								<span'. (allowPmx('pmx_admin') ? ' class="pmx_clickaddnew" title="'. sprintf($txt['pmx_add_sideblock'], $txt['pmx_admBlk_sides'][$side]) .'" onclick="SetpmxBlockType(\''. $side .'\', \''. $paneldesc .'\')"' : '') .'></span>
								<span class="cat_msg_title pmxcenter">'. $txt['pmx_admBlk_sides'][$side] .'</span>
							</h4>
						</div>
					</td>
				</tr>
				<tr>
					<td id="pWind.ypos.'. $side .'" valign="top">
						<div class="windowbg">
							<table width="100%" class="table_grid" cellspacing="0" cellpadding="0">
								<tr class="windowbg2 normaltext">
									<td id="pWind.xpos'. $side .'.pmxRowMove" style="padding:3px 5px;"><div style="width:45px;"><b>'. $txt['pmx_admBlk_order'] .'</b></div></td>';

			if(!empty($blockCnt))
				echo '
									<td id="pWind.xpos.'. $side .'.pmxSetTitle" width="50%" onclick="pWindToggleLang(\'.'. $side .'\')" title="'. $txt['pmx_toggle_language'] .'" style="cursor:pointer; padding:3px 5px;"><b>'. $txt['pmx_title'] .' [<span id="pWind.def.lang.'. $side .'">'. $context['pmx']['currlang'] .'</span>]</b></td>';
			else
				echo '
									<td width="50%" title="'. $txt['pmx_toggle_language'] .'" style="padding:3px 5px;"><b>'. $txt['pmx_title'] .' [<span id="pWind.def.lang.'. $side .'">'. $context['pmx']['currlang'] .'</span>]</b></td>';

			echo '
									<td style="padding:3px 5px;"><div style="width:160px;"><b>'. $txt['pmx_admBlk_type'] .'</b></div></td>
									<td style="padding:3px 5px;"><div style="width:130px;"><b>'. $txt['pmx_options'] .'</b></div></td>
									<td style="padding:3px 5px;"><div id="pWind.xpos'. $side .'.pmxBlockType" style="width:50px;"><b>'. $txt['pmx_status'] .'</b></div></td>
									<td id="pWind.xpos.'. $side .'.pmxSetAcs" style="padding:3px 5px;"><div id="pWind.xpos'. $side .'.pmxSetCloneMove" style="width:113px;"><b>'. $txt['pmx_functions'] .'</b></div></td>
								</tr>';

			// call PmxBlocksOverview for each side / block
			$blockIDs = array();
			if(!empty($blockCnt))
			{
				foreach($context['pmx']['blocks'][$side] as $block)
				{
					if(PmxBlocksOverview($block, $side, $cfg_titleicons, $cfg_smfgroups) == true)
					{
						$blockIDs[] = $block['id'];
						$blocktypes[$side][$block['id']] = array(
							'type' => $block['blocktype'],
							'pos' => $block['pos'],
						);
					}
				}
			}

			echo '
							</table>';

			if(count($blockIDs) > 0)
			{
				// common Popup input fields
				echo '
							<input id="pWind.language.'. $side .'" type="hidden" value="'. $context['pmx']['currlang'] .'" />
							<input id="pWind.all.ids.'. $side ,'" type="hidden" name="" value="'. implode(' ', $blockIDs) .'" />
							<div style="margin:0 auto; text-align:center;">
								<input style="margin-top:10px; display:none;" class="button_submit" type="button" value="'. $txt['pmx_savechanges'] .'" name="SavePopUpChanges.'. $side .'" onclick="FormFunc(\'save_overview\', \'1\')" />
								<input style="margin:10px 10px 0 10px; display:none;" class="button_submit" type="button" value="'. $txt['pmx_cancel'] .'" name="SavePopUpChanges.'. $side .'" onclick="pmxCancelPopup(\'SavePopUpChanges\', \''. $side .'\')" />
							</div>
							<span class="botslice"><span></span></span>';

				if(count($blockIDs) == 1 && allowPmx('pmx_admin'))
					echo '
							<script type="text/javascript"><!-- // --><![CDATA[
								document.getElementById("Img.RowMove.'. $blockIDs[0] .'").className = "pmx_clickrow";
								document.getElementById("Img.RowMove.'. $blockIDs[0] .'").title = "";
							// ]]></script>';
			}
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
		}

		// start row move popup
		echo '
			<div id="pmxRowMove" class="smalltext" style="position:absolute; width:310px; z-index:9999; display:none;">
				'. pmx_popupHeader($txt['pmx_rowmove_title']) .'
					<input id="pWind.move.error" type="hidden" value="'. $txt['pmx_block_move_error'] .'" />
					<div style="float:'. $LtR .';width:110px;">
						'. $txt['pmx_block_rowmove'] .'<br />
						<div style="margin-top:5px;">'. $txt['pmx_blockmove_place'] .'</div><br />
						<div style="margin-top:10px;">'. $txt['pmx_blockmove_to'] .'</div>
					</div>
					<div style="padding-'. $LtR .':112px;">
						<div style="margin-'. $LtR .':5px; margin-top:2px;" id="pWind.move.blocktyp"></div>
						<div style="margin-top:5px;">
							<input id="pWind.place.0" class="input_check" type="radio" name="_" value="before" /><span style="vertical-align:3px; padding:0 3px;">'. $txt['rowmove_before'] .'</span><br />
							<input id="pWind.place.1" class="input_check" type="radio" name="_" value="after" checked="checked" /><span style="vertical-align:3px; padding:0 3px;">'. $txt['rowmove_after'] .'</span><br />
						</div>';

		foreach($txt['pmx_admBlk_sides'] as $side => $d)
		{
			if(isset($blocktypes[$side]))
			{
				echo '
						<select id="pWind.sel.'. $side .'" style="width:172px; margin-top:8px; margin-'. $LtR .':5px; display:none;" size="1">';

				// output blocktypes
				foreach($blocktypes[$side] as $id => $data)
					echo '
							<option value="'. $id .'">['. $data['pos'] .'] '. $context['pmx']['RegBlocks'][$data['type']]['description'] .'</option>';

				echo '
						</select>';
			}
		}

		echo '
					</div>
					<div style="clear:both; text-align:'. $RtL .'; margin-top:7px;">
						<input class="button_submit" type="button" value="'. $txt['pmx_save'] .'" name="" onclick="pmxSendRowMove()" />
					</div>
				</div>
				<span class="lowerframe"><span></span></span>
			</div>';
		// end Move popup

		// start title edit popup
		echo '
			<div id="pmxSetTitle" class="smalltext" style="position:absolute; width:370px; z-index:9999; display:none;">
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

			// start Access popup
			echo '
			<div id="pmxSetAcs" class="smalltext" style="position:absolute; width:210px; z-index:9999; display:none;">
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

			// start Clone / Move popup
			echo '
			<div id="pmxSetCloneMove" class="smalltext" style="position:absolute; width:220px; z-index:9999; display:none;">
				'. pmx_popupHeader('<span id="title.clone.move"></span>') .'
					<input id="pWind.txt.clone" type="hidden" value="'. $txt['pmx_text_clone'] .'" />
					<input id="pWind.txt.move" type="hidden" value="'. $txt['pmx_text_move'] .'" />
					<input id="pWind.worktype" type="hidden" value="" />
					<input id="pWind.addoption" type="hidden" value="'. $txt['pmx_clone_move_toarticles'] .'" />
					<div style="float:'. $LtR .';width:60px;">'. $txt['pmx_text_block'] .'</div>
					<div id="pWind.clone.move.blocktype" style="float:'. $LtR .'; margin-top:2px;"></div>
					<div style="clear:both; height:4px;"></div>
					<div>'. $txt['pmx_clone_move_side'] .'</div>
					<select id="pWind.sel.sides" style="width:50%;" size="1">';

			$sel = true;
			foreach($txt['pmx_admBlk_sides'] as $side => $desc)
			{
				echo '
						<option value="'. $side .'"'. (!empty($sel) ? ' selected="selected"' : '') .'>'. $desc .'</option>';
				$sel = false;
			}

			echo '
					</select>
					<input style="float:'. $RtL .'" class="button_submit" type="button" value="'. $txt['pmx_save'] .'" name="" onclick="pmxSendCloneMove()" />
				</div>
				<span class="lowerframe"><span></span></span>
			</div>';
			// end Clone / Move popup
	}

	// --------------------
	// singleblock edit
	// --------------------
	elseif($context['pmx']['function'] == 'edit' || $context['pmx']['function'] == 'editnew')
	{
		echo '
			<table width="100%" cellpadding="1" cellspacing="1" style="margin-bottom:5px;table-layout:fixed;">
				<tr>
					<td align="center">
						<div class="title_bar">
							<h3 class="titlebg">
							'. $txt['pmx_editblock'] .' '. $context['pmx']['RegBlocks'][$context['pmx']['editblock']->cfg['blocktype']]['description'] .'
							</h3>
						</div>
					</td>
				</tr>';

		// call the ShowAdmBlockConfig() methode
		$context['pmx']['editblock']->pmxc_ShowAdmBlockConfig();

		echo '
			</table>';

		// add visual upshrink
		$context['pmx']['html_footer'] .= '
	var upshrinkVis = new smc_Toggle({
		bToggleEnabled: true,
		bCurrentlyCollapsed: '. (empty($options['collapse_visual']) ? 'false' : 'true') .',
		aSwappableContainers: [
			\'upshrinkVisual\',
			\'upshrinkVisual1\',
			\'upshrinkVisual2\',
			\'upshrinkVisual3\'
		],
		aSwapImages: [
			{
				sId: \'upshrinkImgVisual\',';
		if($context['pmx']['settings']['shrinkimages'] != 2)
			$context['pmx']['html_footer'] .= '
				srcCollapsed: \''. $context['pmx_img_colapse'] .'\',';

		$context['pmx']['html_footer'] .= '
				altCollapsed: '. (JavaScriptEscape($txt['pmx_expand'] . $txt['pmx_edit_ext_opts'])) .',';

		if($context['pmx']['settings']['shrinkimages'] != 2)
			$context['pmx']['html_footer'] .= '
				srcExpanded: \''. $context['pmx_img_expand'] .'\',';

		$context['pmx']['html_footer'] .= '
				altExpanded: '. (JavaScriptEscape($txt['pmx_collapse'] . $txt['pmx_edit_ext_opts'])) .'
			}
		],
		oThemeOptions: {
			bUseThemeSettings: true,
			sOptionName: \'collapse_visual\',
			sSessionVar: '. (JavaScriptEscape($context['session_var'])) .',
			sSessionId: '. (JavaScriptEscape($context['session_id'])) .'
		}
	});';
	}

	echo '
		</form>';
}

/**
* P
* Called for each block.
*/
function PmxBlocksOverview($block, $side, $cfg_titleicons, $cfg_smfgroups)
{
	global $context, $user_info, $modSettings, $txt;

	if(allowPmx('pmx_blocks', true))
	{
		if(empty($block['config']['can_moderate']))
			return false;
	}

	$LtR = empty($context['right_to_left']) ? 'left' : 'right';
	$RtL = empty($context['right_to_left']) ? 'right' : 'left';
	$sideID = '.'. $side;

	if(empty($block['config']['title_align']))
		$block['config']['title_align'] = 'left';
	if(empty($block['config']['title_icon']))
		$block['config']['title_icon'] = 'none.gif';

	if(!empty($block['acsgrp']))
		list($grpacs, $denyacs) = Pmx_StrToArray($block['acsgrp'], ',', '=');
	else
		$grpacs = $denyacs = array();

	// pos row
	echo '
							<tr style="height:20px;">
								<td style="padding:3px 2px 3px 5px;white-space:nowrap;">
									<div id="Img.RowMove.'. $block['id'] .'" class="pmx_clickrow'. (allowPmx('pmx_admin') ? ' pmx_moveimg" title="'. $txt['row_move_updown'] .'" onclick="pmxRowMove(\''. $block['id'] .'\', \''. $block['side'] .'\')"' : '"') .'></div>
									<div id="pWind.pos'. $sideID .'.'. $block['id'] .'" style="width:23px; text-align:'. $RtL .'; float:'. $LtR .';">'. $block['pos'] .'</div>
								</td>';

	// title row
	echo '
								<td style="padding:3px 5px;" id="pWind.ypos.'. $block['id'] .'">
									<div onclick="pmxSetTitle(\''. $block['id'] .'\', \''. $sideID .'\')"  title="'. $txt['pmx_click_edit_ttl'] .'" style="cursor:pointer;">
										<img id="uTitle.icon.'. $block['id'] .'" align="'.$LtR.'" style="padding-'.$RtL.':4px;" src="'. $context['pmx_Iconsurl'] . $block['config']['title_icon'] .'" alt="*" title="'. substr($txt['pmx_edit_titleicon'], 0, -1) .'" />
										<img id="uTitle.align.'. $block['id'] .'" align="'. $RtL .'" src="'. $context['pmx_imageurl'] .'text_align_'. $block['config']['title_align'] .'.gif" alt="" title="'. $txt['pmx_edit_title_align'] . $txt['pmx_edit_title_align_types'][$block['config']['title_align']] .'" />
										<span id="sTitle.text.'. $block['id'] . $sideID .'">'. (!empty($block['config']['title'][$context['pmx']['currlang']]) ? htmlspecialchars($block['config']['title'][$context['pmx']['currlang']], ENT_QUOTES) : '???') .'</span>';

	foreach($context['pmx']['languages'] as $lang => $sel)
		echo '
										<input id="sTitle.text.'. $lang .'.'. $block['id'] . $sideID .'" type="hidden"  name="" value="'. (!empty($block['config']['title'][$lang]) ? htmlspecialchars($block['config']['title'][$lang], ENT_QUOTES) : '???') .'" />';

	echo '
										<input id="sTitle.icon.'. $block['id'] .'" type="hidden" value="'. $block['config']['title_icon'] .'" />
										<input id="sTitle.align.'. $block['id'] .'" type="hidden" value="'. $block['config']['title_align'] .'" />
									</div>
								</td>';

	// type row
	echo '
								<td style="padding:3px 5px;">
									<div id="pWind.desc'. $sideID .'.'. $block['id'] .'" title="'. $context['pmx']['RegBlocks'][$block['blocktype']]['blocktype'] .' '. $context['pmx']['RegBlocks'][$block['blocktype']]['description'] .' (ID:'. $block['id'] .')'. ($block['side'] == 'pages' ? ', Name: '. $block['config']['pagename'] : '') .'"><img align="'. $LtR .'" style="padding-'.$RtL.':5px;" src="'. $context['pmx_imageurl'] .'type_'. $context['pmx']['RegBlocks'][$block['blocktype']]['icon'] .'.gif" alt="*" />'. $context['pmx']['RegBlocks'][$block['blocktype']]['description'] .'</div>
								</td>';

	// create the gruop acs data
	$groups = array();
	foreach($cfg_smfgroups as $grp)
	{
		if(in_array($grp['id'], $grpacs))
			$groups[] = '+'. $grp['id'] .'='. intval(!in_array($grp['id'], $denyacs));
		else
			$groups[] = ':'. $grp['id'] .'=1';
	}

	// check extent options
	$extOpts = false;
	if(!empty($block['config']['ext_opts']))
	{
		foreach($block['config']['ext_opts'] as $k => $v)
			$extOpts = !empty($v) ? true : $extOpts;
	}

	// options row
	echo '
								<td id="pmxSetAcs.'. $block['id'] .'"  style="padding:3px 5px;">
									<span id="pmxSetCloneMove.'. $block['id'] .'"></span>
									<input id="pWind.acs.grp.'. $block['id'] .'" type="hidden" value="'. implode(' ', $groups) .'" />
									<div id="AccessPos'. $block['id'] .'">
										<div class="pmx_clickrow'. (!empty($block['config']['settings']) ? ' pmx_settings" title="'. $txt['pmx_have_settings'] : '') .'"></div>
										<div id="pWind.grp.on.'. $block['id'] .'" class="pmx_clickrow pmx_access" title="'. $txt['pmx_have_groupaccess'] .'"'. (empty($block['acsgrp']) ? ' style="display:none;"' : '') .'></div>
										<div id="pWind.grp.off.'. $block['id'] .'" class="pmx_clickrow"'. (!empty($block['acsgrp']) ? ' style="display:none;"' : '') .'></div>
										<div class="pmx_clickrow'. (!empty($block['config']['can_moderate']) ? ' pmx_moderate"  title="'. $txt['pmx_have_modaccess'] : '') .'"></div>
										<div class="pmx_clickrow'. (!empty($extOpts) ? ' pmx_dynopts" title="'. $txt['pmx_have_dynamics'] : '') .'"></div>
										<div class="pmx_clickrow'. (!empty($block['config']['cssfile']) ? ' pmx_custcss" title="'. $txt['pmx_have_cssfile'] : '') .'"></div>
										<div class="pmx_clickrow'. (!empty($block['cache']) ? ' pmx_cache" title="'. $txt['pmx_have_caching'] . $block['cache'] . $txt['pmx_edit_cachetimesec'] : '') .'"></div>
									</div>
								</td>';

	// status row
	echo '
								<td align="center" style="padding:3px 5px;">
									<div class="pmx_clickrow'. ($block['active'] ? ' pmx_active" title="'. $txt['pmx_status_activ'] : ' pmx_inactive" title="'. $txt['pmx_status_inactiv']) .' - '. $txt['pmx_status_change'] .'" style="margin: 0 12px;" onclick="FormFunc(\'chg_status\', \''. $block['id'] .'\')"></div>
								</td>';

	// functions row
	echo '
								<td style="padding:3px 5px;" nowrap="nowrap">
									<div class="pmx_clickrow pmx_pgedit" title="'. $txt['pmx_edit_sideblock'].'" onclick="FormFunc(\'edit_block\', \''. $block['id'] .'\')"></div>
									<div class="pmx_clickrow pmx_grpacs" title="'. $txt['pmx_chg_blockaccess'] .'" onclick="pmxSetAcs(\''. $block['id'] .'\', \''. $sideID .'\')"></div>
									<div class="pmx_clickrow'. (allowPmx('pmx_admin') ? ' pmx_pgclone" title="'. $txt['pmx_clone_sideblock'] .'" onclick="pmxSetCloneMove(\''. $block['id'] .'\', \''. $block['side'] .'\', \'clone\', \''. $block['blocktype'] .'\')"' : '"') .'></div>
									<div class="pmx_clickrow'. (allowPmx('pmx_admin') ? ' pmx_pgmove" title="'. $txt['pmx_move_sideblock'] .'" onclick="pmxSetCloneMove(\''. $block['id'] .'\', \''. $block['side'] .'\', \'move\', \''. $block['blocktype'] .'\')"' : '"') .'></div>
									<div class="pmx_clickrow'. (allowPmx('pmx_admin') ? ' pmx_pgdelete" title="'. $txt['pmx_delete_sideblock'] .'" onclick="FormFunc(\'block_delete\', \''. $block['id'] .'\', \''. $txt['pmx_confirm_blockdelete'] .'\')"' : '"') .'></div>
								</td>
							</tr>';
	return true;
}

/**
* Popup Header bar
**/
function pmx_popupHeader($title)
{
	global $context, $txt;

	$LtR = empty($context['right_to_left']) ? 'left' : 'right';
	return '
				<div class="title_bar catbg_grid" style="cursor: pointer;margin-bottom:0;" onclick="'. ($title == $txt['pmx_rowmove_title'] ? 'pmxRowMove_RemovePopup()' : 'pmxRemovePopup()') .'" title="'. $txt['pmx_clickclose'] .'">
					<h4 class="titlebg catbg_grid" style="line-height:25px;">
						<img class="grid_click_image pmx'. (!empty($context['right_to_left']) ? 'left' : 'right') .'" src="'. $context['pmx_imageurl'] .'cross.png" alt="close" style="padding-'.$LtR.':10px;" />
						<span'. (empty($title) ? ' id="pWind.title.bar"' : '') .'>'. $title .'</span>
					</h4>
				</div>
				<div class="roundframe" style="padding-top:8px;">';
}
?>
