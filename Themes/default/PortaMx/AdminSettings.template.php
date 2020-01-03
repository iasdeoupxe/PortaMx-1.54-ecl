<?php
/**
* \file AdminSettings.template.php
* Template for the Settings Manager.
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
	global $context, $settings, $modSettings, $options, $user_info, $txt, $scripturl;

	$curarea = isset($_GET['area']) ? $_GET['area'] : 'pmx_center';

	if(allowPmx('pmx_admin', true))
	{
		$AdmTabs = array(
			'pmx_center' => $txt['pmx_admincenter'],
			'pmx_settings' => $txt['pmx_settings'],
			'pmx_blocks' => $txt['pmx_blocks'],
			'pmx_categories' => $txt['pmx_categories'],
			'pmx_articles' => $txt['pmx_articles'],
			'pmx_sefengine' => $txt['pmx_sefengine'],
		);

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

	$MenuTabs = array(
		'globals' => $txt['pmx_admSet_globals'],
		'frontpage' => $txt['pmx_admSet_front'],
		'panels' => $txt['pmx_admSet_panels'],
		'control' => $txt['pmx_admSet_control'],
		'access' => $txt['pmx_admSet_access'],
	);

	$Descriptions = array(
		'globals' => $txt['pmx_admSet_desc_global'],
		'frontpage' => $txt['pmx_admSet_desc_front'],
		'panels' => $txt['pmx_admSet_desc_panel'],
		'control' => $txt['pmx_admSet_desc_control'],
		'access' => $txt['pmx_admSet_desc_access'],
	);

	$LtR = empty($context['right_to_left']) ? 'left' : 'right';
	$RtL = empty($context['right_to_left']) ? 'right' : 'left';

	if($curarea == 'pmx_sefengine')
		$context['pmx']['subaction'] = '';
	else
	{
		echo '
			<div class="cat_bar"><h3 class="catbg">'. $txt['pmx_settings'] .'</h3></div>
			<span class="upperframe"><span></span></span>
			<div class="roundframe">'. $Descriptions[$context['pmx']['subaction']] .'</div>
			<span class="lowerframe"><span></span></span>';

		if(empty($context['pmx_style_isCore']))
		{
			echo '
			<div id="adm_submenus" style="margin-top:1.1em;margin-bottom:6px;overflow:hidden;">
				<ul class="dropmenu">';

			foreach($MenuTabs as $name => $desc)
				echo '
					<li>
						<a class="firstlevel'. ($name == $context['pmx']['subaction'] ? ' active' : '') .'" href="'. $scripturl .'?action='. $context['pmx']['AdminMode'] .';area=pmx_settings;sa='. $name .';'. $context['session_var'] .'=' .$context['session_id'] .'">
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

			$cnt = count($MenuTabs);
			foreach($MenuTabs as $name => $desc)
			{
				$cnt--;
				echo '
					<li'. ($name == $context['pmx']['subaction'] || $cnt == 0 ? ' class="'. ($cnt == 0 ? 'last"' : 'active"') : '') .'>
						<a href="'. $scripturl .'?action='. $context['pmx']['AdminMode'] .';area=pmx_settings;sa='. $name .';'. $context['session_var'] .'=' .$context['session_id'] .'">
							<span>'. ($name == $context['pmx']['subaction'] ? '<em>'. $desc .'</em>' : $desc) .'</span>
						</a>
					</li>';
			}
			echo '
					</ul>
				</div>
			</div>';
		}
	}

	$admset = $context['pmx']['settings'];
	echo '
	<form id="pmx_form" accept-charset="', $context['character_set'], '" name="PMxAdminSettings" action="' . $scripturl . '?action='. $context['pmx']['AdminMode'] .';area='. $curarea . (!empty($context['pmx']['subaction']) ? ';sa='. $context['pmx']['subaction'] : '') .';'. $context['session_var'] .'=' .$context['session_id'] .'" method="post" style="margin: 0px;">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input id="common_field" type="hidden" name="" value="" />';


	if($context['pmx']['subaction'] == 'globals')
	{
		// define numeric vars to check
		echo '
		<input type="hidden" name="check_num_vars[]" value="[left_panel][size], 170" />
		<input type="hidden" name="check_num_vars[]" value="[right_panel][size], 170" />
		<input type="hidden" name="check_num_vars[]" value="[panels][padding], 4" />';

		// Global settings
		echo '
		<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:5px; table-layout:fixed;">
			<tr>
				<td align="center">
					<div class="title_bar">
						<h3 class="titlebg">'. $txt['pmx_global_settings'] .'</h3>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<div class="windowbg">
					<span class="topslice"><span></span></span>
					<table width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" style="padding:10px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:20px;">'. $txt['pmx_settings_ecl'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick="Show_help(\'pmxelc\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxelc" class="info_frame">'. $txt['pmx_settings_eclhelp'] .'</div>
							</td>
							<td valign="top" style="padding:10px 5px 0 5px;width:50%;">
								<div style="min-height:20px;margin-'. $LtR .':-4px;">
									<input type="hidden" name="pmx_ecl" value="0" />
									<input onchange="eclcheckmodal(this)" style="float:'. $LtR .';" class="input_check" type="checkbox" name="pmx_ecl" value="1"'. (!empty($context['pmx']['pmx_ecl']) ? 'checked="checked"' : '') .' />
								</div>
							</td>
						</tr>
						<tr id="eclmodal" style="display:'. (!empty($context['pmx']['pmx_ecl']) ? '' : 'none;') .'">
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:20px;">'. $txt['pmx_settings_eclmodal'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick="Show_help(\'pmxelcmodal\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxelcmodal" class="info_frame">'. $txt['pmx_settings_eclhelpmodal'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="min-height:20px;margin-'. $LtR .':-4px;">
									<input type="hidden" name="pmx_eclmodal" value="0" />
									<input style="float:'. $LtR .';" class="input_check" type="checkbox" name="pmx_eclmodal" value="1"'. (!empty($context['pmx']['pmx_eclmodal']) ? 'checked="checked"' : '') .' />
								</div>
							</td>
						</tr>
						<script type="text/javascript"><!-- // --><![CDATA[
							function eclcheckmodal(elm) {
								document.getElementById("eclmodal").style.display = (elm.checked == true ? "" : "none");
							}
						// ]]></script>
						<tr>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">'. $txt['pmx_settings_disableHS'] .'</div>
								<div style="min-height:25px;">'. $txt['pmx_settings_noHS_onfrontpage'] .'</div>
								<div>'. $txt['pmx_settings_noHS_onaction'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick="Show_help(\'pmxH22\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH22" class="info_frame">'. $txt['pmx_settings_noHS_onactionhelp'] .'</div>
							</td>
							<td valign="top" style="margin-left:1px; padding:5px 5px 0 5px;width:50%;">
								<div style="min-height:25px;margin-'. $LtR .':-4px;">
									<input type="hidden" name="disableHS" value="0" />
									<input style="float:'. $LtR .';" class="input_check" type="checkbox" name="disableHS" value="1"'. (!empty($admset['disableHS']) ? 'checked="checked"' : '') .' />
								</div>
								<div style="min-height:25px;margin-'. $LtR .':-4px;">
									<input type="hidden" name="disableHSonfront" value="0" />
									<input style="float:'. $LtR .';" class="input_check" type="checkbox" name="disableHSonfront" value="1"'. (!empty($admset['disableHSonfront']) ? 'checked="checked"' : '') .' />
								</div>
								<div style="min-height:25px;">
									<input class="adm_w80" type="text" name="noHS_onaction" value="'. (!empty($admset['noHS_onaction']) ? $admset['noHS_onaction'] : '') .'" />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div>'. $txt['pmx_settings_download'] .'
									<img class="info_toggle" onclick="Show_help(\'pmxH01\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH01" class="info_frame">'. $txt['pmx_settings_downloadhelp'] .'</div>
							</td>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;">
								<div style="margin-'. $LtR .':-4px;">
									<input type="hidden" name="download" value="0" />
									<input onchange="chk_dlbutton(this)" style="float:'. $LtR .';" class="input_check" type="checkbox" name="download" value="1"'. (!empty($admset['download']) ? ' checked="checked"' : '') .' />
								</div>
							</td>
						</tr>
						<tr id="dlbutchk1" style="display:'. (!empty($admset['download']) ? '' : 'none;') .'">
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div>'. $txt['pmx_settings_download_action'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick="Show_help(\'pmxH20\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH20" class="info_frame">'. $txt['pmx_settings_dl_actionhelp'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="min-height:25px;">
									<input class="adm_w80" type="text" name="dl_action" value="'. (!empty($admset['dl_action']) ? $admset['dl_action'] : '') .'" />
								</div>
							</td>
						</tr>
						<tr id="dlbutchk2" style="display:'. (!empty($admset['download']) ? '' : 'none;') .'">
							<td valign="top" style="padding:5px;width:50%;text-align:'. $RtL .'">
								<div>'. $txt['pmx_settings_download_acs'] .'</div>
							</td>
							<td valign="top" style="padding:5px;width:50%;">
								<select class="adm_w50" name="dl_access[]" size="5" multiple="multiple">';

		$dlaccess = !empty($admset['dl_access']) ? explode(',', $admset['dl_access']) : array();
		foreach($context['pmx']['acsgroups'] as $group)
			if($group['id'] != 1)
				echo '
									<option value="'. $group['id'] .'=1"'. (in_array($group['id'] .'=1', $dlaccess) ? ' selected="selected"' : '') .'>'. $group['name'] .'</option>';

		echo '
								</select>
								<script type="text/javascript"><!-- // --><![CDATA[
									function chk_dlbutton(elm) {
										document.getElementById("dlbutchk1").style.display = (elm.checked == true ? "" : "none");
										document.getElementById("dlbutchk2").style.display = (elm.checked == true ? "" : "none");
									}
								// ]]></script>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div>'. $txt['pmx_settings_other_actions'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick="Show_help(\'pmxH201\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH201" class="info_frame">'. $txt['pmx_settings_other_actionshelp'] .'</div>
							</td>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;">
								<div style="min-height:25px;">
									<input class="adm_w80" type="text" name="other_actions" value="'. (!empty($admset['other_actions']) ? $admset['other_actions'] : '') .'" />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">'. $txt['pmx_settings_panelpadding'] .'</div>
								<div>'. $txt['pmx_settings_mainoverflow'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick="Show_help(\'pmxH23\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH23" class="info_frame">'. $txt['pmx_settings_forumscrollhelp'] .'</div>
							</td>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;">
								<div style="min-height:25px;">
									<input onkeyup="check_numeric(this);" type="text" size="2" name="panelpad" value="'. (isset($admset['panelpad']) ? $admset['panelpad'] : '4') .'" />
									'. $txt['pmx_pixel'] .'
								</div>
								<div style="margin-'. $LtR .':-4px;">
									<input type="hidden" name="forumscroll" value="0" />
									<input style="float:'. $LtR .';" class="input_check" type="checkbox" name="forumscroll" value="1"'. (!empty($admset['forumscroll']) ? ' checked="checked"' : '') .' />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div>'. $txt['pmx_settings_teasermode'][0] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick="Show_help(\'pmxteasecnt\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxteasecnt" class="info_frame">'. $txt['pmx_settings_pmxteasecnthelp'] .'</div>
							</td>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;">
								<div style="min-height:25px; float:'. $LtR .'; margin-'. $LtR .':-4px;">
									<input type="hidden" name="teasermode" value="0" />
									<div><input class="input_check" type="radio" name="teasermode" value="0"'. (empty($admset['teasermode']) ? ' checked="checked"' : '') .' />&nbsp;'. $txt['pmx_settings_teasermode'][1] .'</div>
									<div><input class="input_check" type="radio" name="teasermode" value="1"'. (!empty($admset['teasermode']) ? ' checked="checked"' : '') .' />&nbsp;'. $txt['pmx_settings_teasermode'][2] .'</div>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">'. $txt['pmx_settings_shrinkimages'] .'</div>
							</td>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;">
								<div style="min-height:25px; float:'. $LtR .'; margin-'. $LtR .':-4px;">
									<input type="hidden" name="shrinkimages" value="0" />
									<div><input class="input_check" type="radio" name="shrinkimages" value="0"'. (empty($admset['shrinkimages']) ? ' checked="checked"' : '') .' />&nbsp;'. $txt['pmx_settings_shrink'][0] .'</div>
									<div><input class="input_check" type="radio" name="shrinkimages" value="1"'. (!empty($admset['shrinkimages']) && $admset['shrinkimages'] == 1 ? ' checked="checked"' : '') .' />&nbsp;'. $txt['pmx_settings_shrink'][1] .'</div>
									<div><input class="input_check" type="radio" name="shrinkimages" value="2"'. (!empty($admset['shrinkimages']) && $admset['shrinkimages'] == 2 ? ' checked="checked"' : '') .' />&nbsp;'. $txt['pmx_settings_shrink'][2] .'</div>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div>'. $txt['pmx_settings_hidecopyright'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick="Show_help(\'pmxH24c\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH24c" class="info_frame">'. $txt['pmx_settings_hidecopyrighthelp'] .'</div>
							</td>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;">
								<div style="min-height:25px;">
									<input class="adm_w80" type="text" name="unlock" value="'. (!empty($admset['unlock']) ? $admset['unlock'] : '') .'" />
									'. (!empty($admset['unlock']) ? '<div class="smalltext"><b><i>' . $context['pmx']['sysstat'] .'</i></b></div>' : '') .'
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div>'. $txt['pmx_settings_blockcachestats'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick="Show_help(\'pmxH24a\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH24a" class="info_frame">'. $txt['pmx_settings_blockcachestatshelp'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="margin-'. $LtR .':-4px;">
									<input type="hidden" name="cachestats" value="0" />
									<input style="float:'. $LtR .';" class="input_check" type="checkbox" name="cachestats" value="1"'. (!empty($admset['cachestats']) ? ' checked="checked"' : '') .' />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div>'. $txt['pmx_settings_postcountacs'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick="Show_help(\'pmxH25\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH25" class="info_frame">'. $txt['pmx_settings_postcountacshelp'] .'</div>
							</td>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;">
								<div style="min-height:25px; margin-'. $LtR .':-4px;">
									<input type="hidden" name="postcountacs" value="0" />
									<input style="float:'. $LtR .';" class="input_check" type="checkbox" name="postcountacs" value="1"'. (!empty($admset['postcountacs']) ? ' checked="checked"' : '') .' />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div>'. $txt['pmx_settings_enable_xbarkeys'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick="Show_help(\'pmxH02\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH02" class="info_frame">'. $txt['pmx_settings_xbarkeys_help'] .'</div>
							</td>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;">
								<div style="margin-'. $LtR .':-4px;">
									<input type="hidden" name="xbarkeys" value="0" />
									<input style="float:'. $LtR .';" class="input_check" type="checkbox" name="xbarkeys" value="1"'. (!empty($admset['xbarkeys']) ? ' checked="checked"' : '') .' />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div>'. $txt['pmx_settings_enable_xbars'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick="Show_help(\'pmxH03\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH03" class="info_frame">'. $txt['pmx_settings_xbars_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="height:25px;">
									<img id="pmxTXB" class="adm_hover" onclick="ToggleCheckbox(this, \'xsel\', 0)" width="13" height="13" style="float:'. $LtR .';margin-top:2px;" src="'. $context['pmx_syscssurl'] .'Images/bullet_plus.gif" alt="*" title="'.$txt['pmx_settings_all_toggle'].'" />
								</div>
								<input type="hidden" name="xbars[]" value="" />';

		foreach($txt['pmx_block_sides'] as $side => $sidename)
		{
			if($side != 'front' && $side != 'pages')
			{
				echo '
								<div class="adm_clear" style="height:25px;margin-'. $LtR .':-4px;">
									<input id="xsel'.$side.'" class="input_check" type="checkbox" name="xbars[]" value="'. $side .'"'. (isset($admset['xbars']) && in_array($side, $admset['xbars']) ? ' checked="checked"' : '') .' />&nbsp;<span style="vertical-align:2px;">'. $sidename .'</span>
								</div>';
			}
		}

		echo '
								<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
									ToggleCheckbox(document.getElementById("pmxTXB"), \'xsel\', 1)
								// ]]></script>
							</td>
						</tr>
					</table>
					<p align="center"><input class="button_submit" type="button" value="'.$txt['pmx_save'].'" name="" onclick="FormFunc(\'save_settings\', \'yes\')" /></p>
					<span class="botslice"><span></span></span>
					</div>
				</td>
			</tr>
		</table>';
	}

	if($context['pmx']['subaction'] == 'control')
	{
		// Blockmanager control settings
		echo '
		<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:5px; table-layout:fixed;">
			<tr>
				<td align="center">
					<div class="title_bar">
						<h3 class="titlebg">'. $txt['pmx_global_program'] .'</h3>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<div class="windowbg">
					<span class="topslice"><span></span></span>
					<table width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" style="padding:10px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">'. $txt['pmx_settings_collapse_visibility'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick=\'Show_help("pmxH05")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
							</td>
							<td valign="top" style="padding:10px 5px 0 5px;width:50%;">
								<div style="margin-'. $LtR .':-4px;">
									<input type="hidden" name="manager[collape_visibility]" value="0" />
									<input style="float:'. $LtR .';" class="input_check" type="checkbox" name="manager[collape_visibility]" value="1"'. (!empty($admset['manager']['collape_visibility']) ? ' checked="checked"' : '') .' />
									<div id="pmxH05" class="info_frame" style="margin-'. $LtR .':30px;">'. $txt['pmx_settings_collapse_vishelp'] .'</div>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:1px 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">'. $txt['pmx_settings_blockfollow'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick=\'Show_help("pmxH06")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
							</td>
							<td valign="top" style="padding:1px 5px;width:50%;">
								<div style="margin-'. $LtR .':-4px;">
									<input type="hidden" name="manager[follow]" value="0" />
									<input style="float:'. $LtR .';" class="input_check" type="checkbox" name="manager[follow]" value="1"'. (!empty($admset['manager']['follow']) ? ' checked="checked"' : '') .' />
									<div id="pmxH06" class="info_frame" style="margin-'. $LtR .':30px;">'. $txt['pmx_settings_blockfollowhelp'] .'</div>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:1px 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">'. str_replace('[##]', '<img style="vertical-align:-3px;" src="'. $context['pmx_imageurl'] .'page_edit.gif" alt="*" title="" />', $txt['pmx_settings_quickedit']) .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick=\'Show_help("pmxH07")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
							</td>
							<td valign="top" style="padding:1px 5px;width:50%;">
								<div style="margin-'. $LtR .':-4px;">
									<input type="hidden" name="manager[qedit]" value="0" />
									<input style="float:'. $LtR .';" class="input_check" type="checkbox" name="manager[qedit]" value="1"'. (!empty($admset['manager']['qedit']) ? ' checked="checked"' : '') .' />
									<div id="pmxH07" class="info_frame" style="margin-'. $LtR .':30px;">'. $txt['pmx_settings_quickedithelp'] .'</div>
								</div>
							</td>
						</tr>

						<tr>
							<td valign="top" style="padding:1px 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">'. $txt['pmx_settings_enable_promote'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick=\'Show_help("pmxH1promo")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
							</td>
							<td valign="top" style="padding:1px 5px;width:50%;">
								<div style="margin-'. $LtR .':-4px;">
									<input type="hidden" name="manager[promote]" value="0" />
									<input style="float:'. $LtR .';" class="input_check" type="checkbox" name="manager[promote]" value="1"'. (!empty($admset['manager']['promote']) ? ' checked="checked"' : '') .' />
									<div id="pmxH1promo" class="info_frame" style="margin-'. $LtR .':30px;">'. $txt['pmx_settings_enable_promote_help'] .'</div>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:2px 5px; width:50%; text-align:'. $RtL .';">
								<div>'. $txt['pmx_settings_promote_messages'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick="Show_help(\'pmxH2promo\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
							</td>
							<td style="padding:2px 5px; width:50%;" valign="top">
								<div style="min-height:25px;">
									<textarea class="adm_textarea adm_w80" rows="2" cols="35" name="promotes">'. implode(',', $context['pmx']['promotes']) .'</textarea>
								</div>
								<div id="pmxH2promo" class="info_frame" style="margin-top:4px;">'. $txt['pmx_settings_promote_messages_help'] .'</div>
							</td>
						</tr>

						<tr>
							<td valign="top" style="padding:1px 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">', $txt['pmx_settings_article_on_page'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick=\'Show_help("pmxH10")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
							</td>
							<td valign="top" style="padding:1px 5px;width:50%;">
								<div>
									<input style="float:'. $LtR .';" type="text" name="manager[artpage]" size="3" value="'. (!empty($admset['manager']['artpage']) ? $admset['manager']['artpage'] : '25') .'" />
									<div id="pmxH10" class="info_frame" style="margin-'. $LtR .':50px;">'. $txt['pmx_settings_article_on_pagehelp'] .'</div>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:1px 5px;width:50%;text-align:'. $RtL .';">
								<div style="height:25px;">'. $txt['pmx_settings_adminpages'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick=\'Show_help("pmxH09")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>';

		foreach($txt['pmx_block_sides'] as $side => $sidename)
			echo '
								<div style="height:25px;">'. $sidename .':</div>';

		echo '
							</td>
							<td valign="top" style="padding:1px 5px;width:50%;">
								<div style="height:25px;">
									<img id="pmxTMP" class="adm_hover" onclick="ToggleCheckbox(this, \'modsel\', 0)" width="13" height="13" style="float:'. $LtR .';margin-top:2px;" src="'. $context['pmx_syscssurl'] .'Images/bullet_plus.gif" alt="*" title="'.$txt['pmx_settings_all_toggle'].'" />
									<div id="pmxH09" class="info_frame" style="margin-'. $LtR .':26px;">'. $txt['pmx_settings_adminpageshelp'] .'</div>
								</div>';

		foreach($txt['pmx_block_sides'] as $side => $sidename)
		{
			echo '
								<div class="adm_clear" style="height:25px;margin-'. $LtR .':-4px;">
									<input id="modsel'.$side.'" class="input_check" type="checkbox" name="manager[admin_pages][]" value="'. $side .'"'. (isset($admset['manager']['admin_pages']) && in_array($side, $admset['manager']['admin_pages']) ? ' checked="checked"' : '') .' />
								</div>';
		}

		echo '
									<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
									ToggleCheckbox(document.getElementById("pmxTMP"), \'modsel\', 1)
								// ]]></script>
							</td>
						</tr>
					</table>
					<p align="center"><input class="button_submit" type="button" value="'.$txt['pmx_save'].'" name="" onclick="FormFunc(\'save_settings\', \'yes\')" /></p>
					<span class="botslice"><span></span></span>
					</div>
				</td>
			</tr>
		</table>';
	}

	if($context['pmx']['subaction'] == 'panels')
	{
		// Global panel settings
		echo '
		<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:5px; table-layout:fixed;">
			<tr>
				<td align="center">
					<div class="title_bar">
						<h3 class="titlebg">'. $txt['pmx_panel_settings'] .'</h3>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<div class="windowbg">
					<span class="topslice"><span></span></span>
					<table width="100%" cellspacing="0" cellpadding="0" style="padding:0 5px;">';

		$idnbr = 10;
		foreach($txt['pmx_block_sides'] as $side => $sidename)
		{
			if($side != 'front' && $side != 'pages')
			{
				echo '
						<tr>
							<td colspan="2" align="center" valign="top">
								<div class="cat_bar catbg_grid">
									<h4 class="catbg catbg_grid">
										<span class="normaltext cat_msg_title">'. $txt['pmx_settings_panel'. $side] .'</span>
									</h4>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px; width:50%; text-align:'. $RtL .';">
								<div style="float:'. $LtR .'; padding-'. $LtR .':2px; padding-top:4px;">
									<img src="'. $context['pmx_imageurl'] . $side .'_panel.gif" alt="*" title="'. $txt['pmx_settings_panel'. $side] .'" />
								</div>
								<div style="min-height:25px;">'. $txt['pmx_settings_panel_collapse'] .'</div>
								<div style="min-height:60px;">'. $txt['pmx_settings_collapse_state'] .'</div>
								<div style="min-height:25px;">'. ($side == 'left' || $side == 'right' ? $txt['pmx_settings_panelwidth'] : $txt['pmx_settings_panelheight']) .'</div>';

				if(in_array($side, array('head', 'top', 'bottom', 'foot')))
					echo '
								<div style="min-height:25px;">'. $txt['pmx_settings_paneloverflow'] .'</div>';

				echo '
								<div>
									'. $txt['pmx_settings_panelhidetitle'] .'&nbsp;<img class="info_toggle" align="'. $RtL .'" style="padding-top:2px;" onclick="Show_help(\'pmxH_'. $side .'\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH_'. $side .'" class="info_frame">'. $txt['pmx_settings_hidehelp'] .'</div>
							</td>
							<td style="padding:5px; width:50%;" valign="top">
								<input type="hidden" name="'. $side .'_panel[size]" value="0" />
								<input type="hidden" name="'. $side .'_panel[collapse]" value="0" />
								<div style="min-height:25px; margin-'. $LtR .':-4px;">
									<input class="input_check" type="checkbox" name="'. $side .'_panel[collapse]" value="1"'. (!empty($admset[$side .'_panel']['collapse']) ? ' checked="checked"' : '') .' />
								</div>
								<div style="min-height:60px; margin-'. $LtR .':-5px;">';

				if(!isset($admset[$side .'_panel']['collapse_init']))
					$admset[$side .'_panel']['collapse_init'] = 0;

				foreach($txt['pmx_settings_collapse_mode'] as $key => $text)
					echo '
									<div><input class="input_radio" type="radio" name="'. $side .'_panel[collapse_init]" value="'. $key .'"'. (isset($admset[$side .'_panel']['collapse_init']) && $admset[$side .'_panel']['collapse_init'] == $key ? ' checked="checked"' : '') .' style="vertical-align:-3px;" /> '. $text .'</div>';

				echo '
								</div>
								<div style="min-height:25px;">
									<input id="pmx_size_'. $side .'" onkeyup="check_numeric(this);" type="text" size="3" name="'. $side .'_panel[size]" value="'. (!empty($admset[$side .'_panel']['size']) ? $admset[$side .'_panel']['size'] : '') .'" /> '. $txt['pmx_hw_pixel'][$side] .'
								</div>';

				if(in_array($side, array('head', 'top', 'bottom', 'foot')))
				{
					echo '
								<div style="min-height:25px;">
									<select id="pmx_chksize'. $side .'" class="adm_w60" size="1" name="'. $side .'_panel[overflow]" onchange="checkSizeInput(this, \''. $side .'\')">';

					foreach($txt['pmx_overflow_actions'] as $key => $text)
						echo '
										<option value="'. $key .'"'. (isset($admset[$side .'_panel']['overflow']) && $admset[$side .'_panel']['overflow'] == $key ? ' selected="selected"' : '') .'>'. $text .'</option>';
					echo '
									</select>
								</div>
								<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
									checkSizeInput(document.getElementById("pmx_chksize'. $side .'"), \''. $side .'\');
								// ]]></script>';
				}

				echo '
								<div style="min-height:25px;">
									<select id="pmxact_'. $side .'" onchange="changed(\'pmxact_'. $side .'\');" class="adm_w60" name="'. $side .'_panel[hide][]" multiple="multiple" size="5">';

				$data = array();
				if(!empty($admset[$side .'_panel']['hide']))
				{
					$hidevals = is_array($admset[$side .'_panel']['hide']) ? $admset[$side .'_panel']['hide'] : array($admset[$side .'_panel']['hide']);
					foreach($hidevals as $val)
					{
						$tmp = Pmx_StrToArray($val, '=');
						if(isset($tmp[0]) && isset($tmp[1]))
							$data[$tmp[0]] = $tmp[1];
					}
				}
				foreach($txt['pmx_action_names'] as $act => $actdesc)
					echo '
										<option value="'. $act .'='. (array_key_exists($act, $data) ? $data[$act] .'" selected="selected' : '1') .'">'. (array_key_exists($act, $data) ? ($data[$act] == 0 ? '^' : '') : '') . $actdesc .'</option>';

				echo '
									</select>
								</div>
								<script type="text/javascript"><!-- // --><![CDATA[
									var pmxact_'. $side .' = new MultiSelect("pmxact_'. $side .'");
								// ]]></script>';

				$cust = isset($admset[$side .'_panel']['custom_hide']) ? $admset[$side .'_panel']['custom_hide'] : '';
				echo '
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:2px 5px; width:50%; text-align:'. $RtL .';">
								<div>'. $txt['pmx_settings_panel_customhide'] .'
									<img class="info_toggle" style="margin-bottom:-3px;" onclick="Show_help(\'pmxH'. $idnbr .'\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH'. $idnbr .'" class="info_frame">'. $txt['pmx_settings_panel_custhelp'] .'</div>
							</td>
							<td style="padding:2px 5px; width:50%;" valign="top">
								<div style="min-height:25px;">
									<textarea class="adm_textarea adm_w60" rows="2" cols="35" name="'. $side .'_panel[custom_hide]">'. $cust .'</textarea>
								</div>
							</td>
						</tr>

						<tr>
							<td style="padding:2px 5px; width:50%; text-align:'. $RtL .';" valign="top">
								<div style="min-height:25px;padding-top:4px;">'. $txt['pmx_settings_devices'] .'
									<img class="info_toggle" style="text-align:right;padding-top:2px;" onclick="Show_help(\'pmxDH_'. $side .'\', \'left\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxDH_'. $side .'" class="info_frame">'. $txt['pmx_settings_deviceshelp'] .'</div>
							</td>
							<td style="padding:5px; width:50%;">
								<input type="hidden" name="'. $side .'_panel[device]" value="0" />
								<div style="min-height:60px; margin-left:-5px;">
									<div style="width:95%;"><input class="input_radio" type="radio" name="'. $side .'_panel[device]" value="0"'. (empty($admset[$side .'_panel']['device']) ? ' checked="checked"' : '') .' style="vertical-align:-3px;" /> '. $txt['pmx_devices']['all'] .'</div>
									<div style="width:95%;"><input class="input_radio" type="radio" name="'. $side .'_panel[device]" value="1"'. (!empty($admset[$side .'_panel']['device']) && $admset[$side .'_panel']['device'] == '1' ? ' checked="checked"' : '') .' style="vertical-align:-3px;" /> '. $txt['pmx_devices']['mobil'] .'</div>
									<div style="width:95%;"><input class="input_radio" type="radio" name="'. $side .'_panel[device]" value="2"'. (!empty($admset[$side .'_panel']['device']) && $admset[$side .'_panel']['device'] == '2' ? ' checked="checked"' : '') .' style="vertical-align:-3px;" /> '. $txt['pmx_devices']['desk'] .'</div>
								</div>
							</td>
						</tr>

						<tr>
							<td valign="top" colspan="2">
								<div style="margin:0 auto; padding:5px 0 '. ($side == 'foot' ? '0' : '10px') .' 0; text-align:center;">
									<input class="button_submit" type="button" value="'.$txt['pmx_save'].'" name="" onclick="FormFunc(\'save_settings\', \'yes\')" />
								</div>
							</td>
						</tr>';
			}
			$idnbr++;
		}
				echo '
					</table>
					<span class="botslice"><span></span></span>
					</div>
				</td>
			</tr>
		</table>';
	}

	if($context['pmx']['subaction'] == 'frontpage')
	{
		// Frontpage settings
		echo '
		<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:5px; table-layout:fixed;">
			<tr>
				<td align="center">
					<div class="title_bar">
						<h3 class="titlebg">'. $txt['pmx_frontpage_settings'] .'</h3>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<div class="windowbg">
					<span class="topslice"><span></span></span>
					<table width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" style="padding:5px;width:50%;text-align:'. $RtL .'">
								<div style="min-height:25px;">'. $txt['pmx_settings_frontpage_fullsize'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH18")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div style="min-height:25px;">'. $txt['pmx_settings_frontpage_centered'] .'</div>
								<div style="min-height:25px;">'. $txt['pmx_settings_frontpage_none'] .'</div>
								<div style="min-height:25px;padding-top:5px;">'. $txt['pmx_settings_frontpage_menubar'] .'</div>
							</td>
							<td valign="top" style="padding:5px;width:50%;">
								<div style="float:'. $LtR .'; width:28px;">
									<div style="min-height:25px;margin-'. $LtR .':-4px;">
										<input class="input_radio" type="radio" name="frontpage" value="fullsize"'. (isset($admset['frontpage']) && $admset['frontpage'] == 'fullsize' ? ' checked="checked"' : '') .' />
									</div>
									<div style="min-height:25px;margin-'. $LtR .':-4px;">
										<input class="input_radio" type="radio" name="frontpage" value="centered"'. (isset($admset['frontpage']) && $admset['frontpage'] == 'centered' ? ' checked="checked"' : '') .' />
									</div>
									<div style="min-height:25px;margin-'. $LtR .':-4px;">
										<input class="input_radio" type="radio" name="frontpage" value="none"'. (!isset($admset['frontpage']) || (isset($admset['frontpage']) && $admset['frontpage'] == 'none') ? ' checked="checked"' : '') .' />
									</div>
									<div style="min-height:25px; padding-top:5px; margin-'. $LtR .':-4px;">
										<input type="hidden" name="frontpagemenu" value="0" />
										<input class="input_check" type="checkbox" name="frontpagemenu" value="1"'. (!empty($admset['frontpagemenu']) ? ' checked="checked"' : '') .' />
									</div>
								</div>
								<div id="pmxH18" class="info_frame" style="position:relative; top:0;'. $LtR .':0px; margin-'. $LtR .':28px;">
									'. $txt['pmx_frontpage_help'] .'
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px;width:50%;text-align:'. $RtL .'">
								<div style="min-height:25px;">'. $txt['pmx_settings_index_front'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH182")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
							</td>
							<td valign="top" style="padding:5px;width:50%;">
								<div style="float:'. $LtR .'; width:28px;">
									<div style="min-height:25px; margin-'. $LtR .':-4px;">
										<input type="hidden" name="indexfront" value="0" />
										<input class="input_check" type="checkbox" name="indexfront" value="1"'. (!empty($admset['indexfront']) ? ' checked="checked"' : '') .' />
									</div>
								</div>
								<div id="pmxH182" class="info_frame" style="position:relative; top:0;'. $LtR .':0px; margin-'. $LtR .':28px;">
									'. $txt['pmx_settings_index_front_help'] .'
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px;width:50%;text-align:'. $RtL .'">
								<div style="min-height:25px;">'. $txt['pmx_settings_restoretop'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH185")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
							</td>
							<td valign="top" style="padding:5px;width:50%;">
								<div style="float:'. $LtR .'; width:28px;">
									<div style="min-height:25px; margin-'. $LtR .':-4px;">
										<input type="hidden" name="restoretop" value="0" />
										<input id="restoretop" onchange=\'document.getElementById("topfragment").disabled = this.checked\'; class="input_check" type="checkbox" name="restoretop" value="1"'. (!empty($admset['topfragment']) ? ' disabled="disabled"' : (!empty($admset['restoretop']) ? ' checked="checked"' : '')) .' />
									</div>
								</div>
								<div id="pmxH185" class="info_frame" style="position:relative; top:0;'. $LtR .':0px; margin-'. $LtR .':28px;">
									'. $txt['pmx_settings_restoretop_help'] .'
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px;width:50%;text-align:'. $RtL .'">
								<div style="min-height:25px;">'. $txt['pmx_settings_sendfragment'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH181")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
							</td>
							<td valign="top" style="padding:5px;width:50%;">
								<div style="float:'. $LtR .'; width:28px;">
									<div style="min-height:25px; margin-'. $LtR .':-4px;">
										<input type="hidden" name="topfragment" value="0" />
										<input id="topfragment" onchange=\'document.getElementById("restoretop").disabled = this.checked\'; class="input_check" type="checkbox" name="topfragment" value="1"'. (!empty($admset['restoretop']) ? ' disabled="disabled"' : (!empty($admset['topfragment']) ? ' checked="checked"' : '')) .' />
									</div>
								</div>
								<div id="pmxH181" class="info_frame" style="position:relative; top:0;'. $LtR .':0px; margin-'. $LtR .':28px;">
									'. $txt['pmx_settings_sendfragment_help'] .'
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px;width:50%;text-align:'. $RtL .'">
								<div>'. $txt['pmx_settings_pages_hidefront'] .'
									<img class="info_toggle" onclick="Show_help(\'pmxH20\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
							</td>
							<td valign="top" style="padding:5px;width:50%;">
								<div>
									<input class="adm_w90" type="text" name="hidefrontonpages" value="'. $admset['hidefrontonpages'] .'" />
								</div>
								<div id="pmxH20" class="info_frame" style="margin-top:5px;">'. $txt['pmx_settings_pages_help'] .'</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">'. $txt['pmx_settings_fronttheme'] .'</div>
								<div style="min-height:25px; padding-top:5px;">'. $txt['pmx_settings_frontthemepages'] .'</div>
							</td>
							<td valign="top" style="padding:15px 5px 0 5px;width:50%;">
								<div style="min-height:25px;">
									<select style="width:61%;" name="pmx_fronttheme" size="1">
										<option value="0"'. (empty($context['pmx']['pmx_fronttheme']) ? ' selected="selected"' : '') .'>'. $txt['pmx_front_default_theme'] .'</option>';

		foreach($context['pmx']['admthemes'] as $thid => $data)
			echo '
										<option value="'. $thid .'"'. (!empty($context['pmx']['pmx_fronttheme']) && $context['pmx']['pmx_fronttheme'] == $thid ? ' selected="selected"' : '') .'>'. $data['name'] .'</option>';

		echo '
									</select>
								</div>
								<div style="min-height:25px; padding-top:5px; margin-'. $LtR .':-4px;">
									<input type="hidden" name="pmx_frontthempg" value="0" />
									<input class="input_check" type="checkbox" name="pmx_frontthempg" value="1"'. (!empty($context['pmx']['pmx_frontthempg']) ? ' checked="checked"' : '') .' />
								</div>
							</td>
						</tr>
					</table>
					<p align="center"><input class="button_submit" type="button" value="'.$txt['pmx_save'].'" name="" onclick="FormFunc(\'save_settings\', \'yes\')" /></p>
					<span class="botslice"><span></span></span>
					</div>
				</td>
			</tr>
		</table>';
	}

	// Access settings
	if($context['pmx']['subaction'] == 'access')
	{
		echo '
		<input type="hidden" name="update_access" value="1" />
		<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:5px; table-layout:fixed;">
			<tr>
				<td align="center">
					<div class="title_bar">
						<h3 class="titlebg">'. $txt['pmx_access_settings'] .'</h3>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<div class="windowbg">
					<span class="topslice"><span></span></span>
					<table width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" style="padding:5px;width:50%;text-align:'. $RtL .'">
								<div>'. $txt['pmx_access_promote'] .'
									<img class="info_toggle" onclick="Show_help(\'pmxH50\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH50" class="info_frame">'. $txt['pmx_access_promote_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px;width:50%;">
								<select style="width:61%;" name="setaccess[pmx_promote][]" size="5" multiple="multiple">';

		// 'pmx_articles' - Moderate articles
		foreach($context['pmx']['limitgroups'] as $group)
		{
			if($group['id'] != 1)
				echo '
									<option value="'. $group['id'] .'"'. (in_array($group['id'], $context['pmx']['permissions']['pmx_promote']) ? ' selected="selected"' : '') .'>'. $group['name'] .'</option>';
		}

		echo '
								</select>
							</td>
						</tr>

						<tr>
							<td valign="top" style="padding:5px;width:50%;text-align:'. $RtL .'">
								<div>'. $txt['pmx_access_articlecreate'] .'
									<img class="info_toggle" onclick="Show_help(\'pmxH30\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH30" class="info_frame">'. $txt['pmx_access_articlecreate_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px;width:50%;">
								<select style="width:61%;" name="setaccess[pmx_create][]" size="5" multiple="multiple">
									<option value="0"'. (in_array('0', $context['pmx']['permissions']['pmx_create']) ? ' selected="selected"' : '') .'>'. $txt['pmx_ungroupedmembers'] .'</option>';

		// 'pmx_create' - Create and Write articles
		foreach($context['pmx']['admgroups'] as $group)
		{
			if($group['id'] != 1)
				echo '
									<option value="'. $group['id'] .'"'. (in_array($group['id'], $context['pmx']['permissions']['pmx_create']) ? ' selected="selected"' : '') .'>'. $group['name'] .'</option>';
		}

		echo '
								</select>
							</td>
						</tr>

						<tr>
							<td valign="top" style="padding:5px;width:50%;text-align:'. $RtL .'">
								<div>'. $txt['pmx_access_articlemoderator'] .'
									<img class="info_toggle" onclick="Show_help(\'pmxH31\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH31" class="info_frame">'. $txt['pmx_access_articlemoderator_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px;width:50%;">
								<select style="width:61%;" name="setaccess[pmx_articles][]" size="5" multiple="multiple">';

		// 'pmx_articles' - Moderate articles
		foreach($context['pmx']['limitgroups'] as $group)
		{
			if($group['id'] != 1)
				echo '
									<option value="'. $group['id'] .'"'. (in_array($group['id'], $context['pmx']['permissions']['pmx_articles']) ? ' selected="selected"' : '') .'>'. $group['name'] .'</option>';
		}

		echo '
								</select>
							</td>
						</tr>

						<tr>
							<td valign="top" style="padding:5px;width:50%;text-align:'. $RtL .'">
								<div>'. $txt['pmx_access_blocksmoderator'] .'
									<img class="info_toggle" onclick="Show_help(\'pmxH32\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH32" class="info_frame">'. $txt['pmx_access_blocksmoderator_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px;width:50%;">
								<select style="width:61%;" name="setaccess[pmx_blocks][]" size="5" multiple="multiple">';

		// 'pmx_blocks' - Moderate blocks
		foreach($context['pmx']['limitgroups'] as $group)
		{
			if($group['id'] != 1)
				echo '
									<option value="'. $group['id'] .'"'. (in_array($group['id'], $context['pmx']['permissions']['pmx_blocks']) ? ' selected="selected"' : '') .'>'. $group['name'] .'</option>';
		}

		echo '
								</select>';

		if(allowedTo('admin_forum'))
		{
			echo '
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px;width:50%;text-align:'. $RtL .'">
								<div>'. $txt['pmx_access_pmxadmin'] .'
									<img class="info_toggle" onclick="Show_help(\'pmxH33\', \''. $LtR .'\')" src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH33" class="info_frame">'. $txt['pmx_access_pmxadmin_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px;width:50%;">
								<select style="width:61%;" name="setaccess[pmx_admin][]" size="5" multiple="multiple">';

			// 'pmx_admin' - PortaMx admin
			foreach($context['pmx']['limitgroups'] as $group)
			{
				if($group['id'] != 1)
					echo '
									<option value="'. $group['id'] .'"'. (in_array($group['id'], $context['pmx']['permissions']['pmx_admin']) ? ' selected="selected"' : '') .'>'. $group['name'] .'</option>';
			}

			echo '
								</select>
							</td>
						</tr>';
		}
		else
		{
			// 'pmx_admin' - PortaMx admin
			foreach($context['pmx']['limitgroups'] as $group)
			{
				if(in_array($group['id'], $context['pmx']['permissions']['pmx_admin']))
					echo '
								<input type="hidden" name="setaccess[pmx_admin][]" value="'. $group['id'] .'" />';
			}
			echo '
							</td>
						</tr>';
		}

		echo '
					</table>
					<p align="center"><input class="button_submit" type="button" value="'.$txt['pmx_save'].'" name="" onclick="FormFunc(\'save_settings\', \'yes\')" /></p>
					<span class="botslice"><span></span></span>
					</div>
				</td>
			</tr>
		</table>';
	}

	// SEF option settings
	if($curarea == 'pmx_sefengine')
	{
		echo '
		<input type="hidden" name="update_pmxsef" value="1" />
		<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:5px; table-layout:fixed;">
			<tr>
				<td align="center">
					<div class="title_bar">
						<h3 class="titlebg">'. $txt['pmx_sef_settings'] .'</h3>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<div class="windowbg">
					<span class="topslice"><span></span></span>
					<div style="text-align:center;padding:5px 0;">
						'. $txt['pmx_sef_engine'] .'
						<img class="info_toggle" onclick=\'Show_help("pmxH40")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
					</div>
					<div id="pmxH40" style="text-align:'. $LtR .';margin:0 10px;" class="info_frame">
						'. $txt['pmx_sef_engine_helpAP'] .'<pre id="pmxsefAP" style="cursor:pointer; font-weight:bold;" title="'. $txt['pmx_sef_engine_APIS_copy'] .'" onclick="return smfSelectText(\'pmxsefAP\', true);">'. htmlentities($txt['pmx_sef_engine_APcode'], ENT_NOQUOTES, $context['pmx']['encoding']) .'</pre>
						'. $txt['pmx_sef_engine_helpIS'] .'<pre id="pmxsefIS" style="cursor:pointer; font-weight:bold;" title="'. $txt['pmx_sef_engine_APIS_copy'] .'" onclick="return smfSelectText(\'pmxsefIS\', true);">'. htmlentities($txt['pmx_sef_engine_IScode'], ENT_NOQUOTES, $context['pmx']['encoding']) .'</pre>
					</div>
					<table width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" style="padding:10px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">
									'. $txt['pmx_sef_enable'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH40a")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH40a" style="text-align:'. $LtR .';" class="info_frame">'. $txt['pmx_sef_enable_help'] .'</div>
							</td>
							<td valign="top" style="padding:10px 5px 0 5px;width:50%;">
								<div style="min-height:25px; margin-'. $LtR .':-4px;">
									<input type="hidden" name="pmxsef_enable" value="0" />
									<input class="input_check" type="checkbox" name="pmxsef_enable" value="1"'. (!empty($context['pmx']['pmxsef_enable']) ? ' checked="checked"' : '') .' />';

		if(!empty($modSettings['pmxsef_disabled']))
			echo '
									<div style="float:'. $RtL .'; margin-'. $RtL .':10px; width:90%;"><i>'. $txt['pmx_sef_engine_disabled'] .'</i></div>';

		echo '
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">
									'. $txt['pmx_sef_lowercase'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH41")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH41" style="text-align:'. $LtR .';" class="info_frame">'. $txt['pmx_sef_lowercase_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="min-height:25px; margin-'. $LtR .':-4px;">
									<input type="hidden" name="pmxsef_lowercase" value="0" />
									<input class="input_check" type="checkbox" name="pmxsef_lowercase" value="1"'. (!empty($context['pmx']['pmxsef_lowercase']) ? ' checked="checked"' : '') .' />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">
									'. $txt['pmx_sef_autosave'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH41a")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH41a" style="text-align:'. $LtR .';" class="info_frame">'. $txt['pmx_sef_autosave_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="min-height:25px; margin-'. $LtR .':-4px;">
									<input type="hidden" name="pmxsef_autosave" value="0" />
									<input class="input_check" type="checkbox" name="pmxsef_autosave" value="1"'. (!empty($context['pmx']['pmxsef_autosave']) ? ' checked="checked"' : '') .' />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">
									'. $txt['pmx_sef_spacechar'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH42")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH42" style="text-align:'. $LtR .';" class="info_frame">'. $txt['pmx_sef_spacechar_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="min-height:25px;">
									<input type="text" size="1" name="pmxsef_spacechar" value="'. $context['pmx']['pmxsef_spacechar'] .'" />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">
									'. $txt['pmx_sef_stripchars'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH43")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH43" style="text-align:'. $LtR .';" class="info_frame">'. $txt['pmx_sef_stripchars_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="min-height:25px;">
									<input style="width:98%;" type="text" name="pmxsef_stripchars" value="'. htmlentities($context['pmx']['pmxsef_stripchars'],ENT_QUOTES, $context['pmx']['encoding']) .'" />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">
									'. $txt['pmx_sef_wirelesss'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH44")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH44" style="text-align:'. $LtR .';" class="info_frame">'. $txt['pmx_sef_wirelesss_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="min-height:25px;">
									<input style="width:98%;" type="text" name="pmxsef_wireless" value="'. $context['pmx']['pmxsef_wireless'] .'" />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">
									'. $txt['pmx_sef_single_token'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH45")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH45" style="text-align:'. $LtR .';" class="info_frame">'. $txt['pmx_sef_single_token_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="min-height:25px;">
									<input style="width:98%;" type="text" name="pmxsef_singletoken" value="'. $context['pmx']['pmxsef_singletoken'] .'" />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">
									'. $txt['pmx_sef_actions'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH46")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH46" style="text-align:'. $LtR .';" class="info_frame">'. $txt['pmx_sef_actions_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="min-height:25px;">
									<textarea style="width:98%;" rows="8" cols="50" name="pmxsef_actions">'. $context['pmx']['pmxsef_actions'] .'</textarea>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">
									'. $txt['pmx_sef_aliasactions'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH46a")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH46a" style="text-align:'. $LtR .';" class="info_frame">'. $txt['pmx_sef_aliasactions_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="min-height:25px;">
									<input style="width:98%;" type="text" name="pmxsef_aliasactions" value="'. $context['pmx']['pmxsef_aliasactions'] .'" />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">
									'. $txt['pmx_sef_ignoreactions'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH46i")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH46i" style="text-align:'. $LtR .';" class="info_frame">'. $txt['pmx_sef_ignoreactions_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="min-height:25px;">
									<input style="width:98%;" type="text" name="pmxsef_ignoreactions" value="'. $context['pmx']['pmxsef_ignoreactions'] .'" />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">
									'. $txt['pmx_sef_ignorerequests'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH46r")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH46r" style="text-align:'. $LtR .';" class="info_frame">'. $txt['pmx_sef_ignorerequests_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="min-height:25px;">
									<input style="width:98%;" type="text" name="pmxsef_ignorerequests" value="'. $context['pmx']['pmxsef_ignorerequests'] .'" />
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;text-align:'. $RtL .';">
								<div style="min-height:25px;">
									'. $txt['pmx_sef_simplesef_space'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxH48")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</div>
								<div id="pmxH48" style="text-align:'. $LtR .';" class="info_frame">'. $txt['pmx_sef_simplesef_space_help'] .'</div>
							</td>
							<td valign="top" style="padding:5px 5px 0 5px;width:50%;">
								<div style="min-height:25px;">
									<input type="text" size="1" name="pmxsef_ssefspace" value="'. $context['pmx']['pmxsef_ssefspace'] .'" />
								</div>
								<input type="hidden" name="pmxsef_codepages" value="'. $context['pmx']['pmxsef_codepages'] .'" />
							</td>
						</tr>';

		echo '
					</table>
					<p align="center"><input class="button_submit" type="button" value="'.$txt['pmx_save'].'" name="" onclick="FormFunc(\'save_settings\', \'yes\')" /></p>
					<span class="botslice"><span></span></span>
					</div>
				</td>
			</tr>
		</table>';
	}

	echo '
	</form>';
}
?>
