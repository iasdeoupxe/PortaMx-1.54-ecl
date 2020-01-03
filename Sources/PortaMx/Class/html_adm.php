<?php
/**
* \file html_adm.php
* Admin Systemblock html
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_html_adm
* Admin Systemblock html_adm
* @see html_adm.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_html_adm extends PortaMxC_SystemAdminBlock
{
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
						<div style="min-height:169px;">';

		// show the settings screen
		echo '
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid"><span class="cat_left_title">'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span></h4>
						</div>

						<div class="adm_check" style="min-height:20px;">
							<span class="adm_w85">'. $txt['pmx_html_teaser'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxHTMLH01")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<input type="hidden" name="config[settings][teaser]" value="0" />
							<input class="input_check" type="checkbox" name="config[settings][teaser]" value="1"' .(!empty($this->cfg['config']['settings']['teaser']) ? ' checked="checked"' : ''). ' />
							<div id="pmxHTMLH01" class="info_frame" style="margin-top:4px;margin-bottom:0;">'. $txt['pmx_html_teasehelp'] .'</div>
						</div>
						<div class="adm_check" style="min-height:20px;">
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
	* Load the WYSIWYG Editor, to create or edit the content.
	* Returns the AdmBlock_settings
	*/
	function pmxc_AdmBlock_content()
	{
		global $context, $boarddir, $txt;

		// show the content area
		echo '
					<td valign="top" colspan="2" style="padding:4px;">
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid"><span class="cat_left_title">'. $txt['pmx_edit_content'] .'</span></h4>
						</div>';

		// show the editor
		$allow = allowPmx('pmx_admin') || allowPmx('pmx_blocks');
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

		echo '
					</td>
				</tr>
				<tr>';

		// return the default settings
		return $this->pmxc_AdmBlock_settings();
	}
}
?>
