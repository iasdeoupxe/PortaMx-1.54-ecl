<?php
/**
* \file php_adm.php
* Admin Systemblock php
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_php_adm
* Admin Systemblock php_adm
* @see php_adm.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_php_adm extends PortaMxC_SystemAdminBlock
{
	/**
	* AdmBlock_init().
	* Setup caching.
	*/
	function pmxc_AdmBlock_init()
	{
		$this->can_cached = 0;			// disable caching
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
						<input type="hidden" name="config[settings]" value="" />
						<div style="height:169px;">
							<div class="cat_bar catbg_grid grid_padd">
								<h4 class="catbg catbg_grid"><span class="cat_left_title">'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span></h4>
							</div>
							<div class="adm_check">
								<span class="adm_w85">'. $txt['pmx_content_print'] .'</span>
								<input type="hidden" name="config[settings][printing]" value="0" />
								<input class="input_check" type="checkbox" name="config[settings][printing]" value="1"' .(!empty($this->cfg['config']['settings']['printing']) ? ' checked="checked"' : ''). ' />
							</div>';

		if($this->cfg['side'] == 'pages')
			echo '
							<div class="adm_check" style="min-height:20px;">
								<span class="adm_w85">'. $txt['pmx_enable_sitemap'] .'</span>
								<input type="hidden" name="config[show_sitemap]" value="0" />
								<input class="input_check" type="checkbox" name="config[show_sitemap]" value="1"' .(!empty($this->cfg['config']['show_sitemap']) ? ' checked="checked"' : ''). ' />
							</div>';

		echo '
						</div>';

		// return the used classnames
		return $this->block_classdef;
	}

	/**
	* AdmBlock_content().
	* Open a textarea, to create or edit the content.
	* Returns the AdmBlock_settings
	*/
	function pmxc_AdmBlock_content()
	{
		global $context, $options, $txt;

		// show the content area
		$options['collapse_phpinit'] = empty($context['pmx']['editorID_init']['havecont']);

		echo '
					<td valign="top" colspan="2" style="padding:4px;">
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid">
								<span style="float:right;display:block;margin-top:-2px;">
									<img onclick="php_syntax(\''. $context['pmx']['editorID'] .'\')" style="padding:3px 5px 3px 10px;cursor:pointer;" alt="Syntax check" src="'. $context['pmx_imageurl'] .'syntaxcheck.png" class="pmxright" />
								</span>
								<span class="cat_left_title">'. $txt['pmx_edit_content'] .'
								<span id="upshrinkPHPinitCont"'. (empty($options['collapse_phpinit']) ? '' : ' style="display:none;"') .'>'. $txt['pmx_edit_content_show'] .'</span></span>
							</h4>
						</div>
						<div id="check_'. $context['pmx']['editorID'] .'" class="info_frame" style="line-height:1.4em;margin-bottom:0;"></div>
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
								<h4 class="catbg catbg_grid">
									<span style="float:right;display:block;margin-top:-2px;">
										<img onclick="php_syntax(\''. $context['pmx']['editorID_init']['id'] .'\')" style="padding:3px 5px 3px 10px;cursor:pointer;" alt="Syntax check" src="'. $context['pmx_imageurl'] .'syntaxcheck.png" class="pmxright" />
									</span>
									<span class="cat_left_title">'. $txt['pmx_edit_content'] . $txt['pmx_edit_content_init'] .'</span>
								</h4>
							</div>
							<div id="check_'. $context['pmx']['editorID_init']['id'] .'" class="info_frame" style="line-height:1.4em;margin-bottom:0;"></div>
							<div>', template_control_richedit($context['pmx']['editorID_init']['id']) ,'</div>
						</div>
						<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
						function php_syntax(elmid)
						{
							document.getElementById("check_" + elmid).className = "info_frame";
							var result = pmx_setCookie("pmx_php_check", document.getElementById(elmid).value);
							result = result.replace(/@elm@/g, elmid);
							document.getElementById("check_" + elmid).innerHTML = result;
							Show_help("check_" + elmid);

							var errLine = /(on\sline\s)(\d+)(.*)/;
							errLine.exec(result);
							errLine = RegExp.$2;
							php_showerrline(elmid, errLine);
						}
						function php_showerrline(elmid, errLine)
						{
							if(errLine != "" && !isNaN(errLine))
							{
								var lines = document.getElementById(elmid).value.split("\n");
								var count = 0;
								for(var i = 0; i < errLine -1; i++)
									count += lines[i].length +1;

								if(document.getElementById(elmid).setSelectionRange)
								{
									document.getElementById(elmid).focus();
									document.getElementById(elmid).setSelectionRange(count, count+lines[i].length);
								}
								else if(document.getElementById(elmid).createTextRange)
								{
									range=document.getElementById(elmid).createTextRange();
									range.collapse(true);
									range.moveStart("character", count);
									range.moveEnd("character", count+lines[i].length);
									range.select();
								}
							}
						}
						function Hide_SyntaxCheck(elm)
						{
							elm.className = "info_frame";
						}';

		if(empty($context['pmx']['editorID_show']['havecont']))
			echo '
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
						});';

		echo '
					// ]]></script>';

		echo '
					</td>
				</tr>
				<tr>';

		// return the default settings
		return $this->pmxc_AdmBlock_settings();
	}
}
?>
