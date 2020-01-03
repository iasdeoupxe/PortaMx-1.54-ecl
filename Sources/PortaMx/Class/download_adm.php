<?php
/**
* \file download_adm.php
* Admin Systemblock download
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_download_adm
* Admin Systemblock download_adm
* @see download_adm.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_download_adm extends PortaMxC_SystemAdminBlock
{
	/**
	* AdmBlock_init().
	* Setup caching.
	*/
	function pmxc_AdmBlock_init()
	{
		$this->can_cached = 0;		// disable caching
	}

	/**
	* AdmBlock_settings().
	* Setup the config vars and output the block settings.
	* Returns the css classes they are used.
	*/
	function pmxc_AdmBlock_settings()
	{
		global $context, $txt;

		// define the settings options
		echo '
					<td valign="top" style="padding:4px;">
						<input type="hidden" name="config[settings]" value="" />';

		// show the settings screen
		echo '
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid"><span class="cat_left_title">'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span></h4>
						</div>

						<div class="adm_input">
							<span>'. $txt['pmx_download_board'] .'</span>
							<select class="adm_w90" name="config[settings][download_board]">';

		$board = isset($this->cfg['config']['settings']['download_board']) ? $this->cfg['config']['settings']['download_board'] : 0;
		foreach($this->smf_boards as $brd)
			echo '
								<option value="'. $brd['id'] .'"'. ($brd['id'] == $board ? ' selected="selected"' : '') .'>'. $brd['name'] .'</option>';

		echo '
							</select>
						</div>

						<div class="adm_input">
							<span>'. $txt['pmx_download_groups'] .'</span>
							<input type="hidden" name="config[settings][download_acs][]" value="" />
							<select class="adm_w90" name="config[settings][download_acs][]" multiple="multiple" size="5">';

		foreach($this->smf_groups as $grp)
			echo '
								<option value="'. $grp['id'] .'"'. (!empty($this->cfg['config']['settings']['download_acs']) && in_array($grp['id'], $this->cfg['config']['settings']['download_acs']) ? ' selected="selected"' : '') .'>'. $grp['name'] .'</option>';
		echo '
							</select>
						</div>';

		if($this->cfg['side'] == 'pages')
			echo '
						<div class="adm_check" style="min-height:20px;">
							<span class="adm_w85">'. $txt['pmx_enable_sitemap'] .'</span>
							<input type="hidden" name="config[show_sitemap]" value="0" />
							<input class="input_check" type="checkbox" name="config[show_sitemap]" value="1"' .(!empty($this->cfg['config']['show_sitemap']) ? ' checked="checked"' : ''). ' />
						</div>';

		// return the used classnames
		return $this->block_classdef;
	}

	/**
	* AdmBlock_content().
	* Load the BBC Editor, to create or edit the content.
	* Returns the AdmBlock_settings
	*/
	function pmxc_AdmBlock_content()
	{
		global $context, $txt;

		// show the content area
		echo '
					<td valign="top" colspan="2" style="padding:4px;">
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid"><span class="cat_left_title">'. $txt['pmx_edit_content'] .'</span></h4>
						</div>
						<input type="hidden" id="smileyset" value="PortaMx" />
						<div id="bbcBox_message"></div>
						<div id="smileyBox_message"></div>
						<div>', template_control_richedit($context['pmx']['editorID'], 'smileyBox_message', 'bbcBox_message'), '</div>
					</td>
				</tr>
				<tr>';

				// If the BBC WYSIWYG editor available and active on save, we need a special handling.
				// The doSubmit() copied the html back to the textarea and we send a html_to_bbc.
				// On this, the Post handler convert the html back to bbc code.
				echo '
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

		// return the default settings
		return $this->pmxc_AdmBlock_settings();
	}
}
?>
