<?php
/**
* \file promotedposts_adm.php
* Admin Systemblock Promotedposts
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_promotedposts_adm
* Admin Systemblock promotedposts_adm
* @see promotedposts_adm.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_promotedposts_adm extends PortaMxC_SystemAdminBlock
{
	var $posts;				///< all posts

	/**
	* AdmBlock_init().
	* Setup caching and returns the language file name.
	*/
	function pmxc_AdmBlock_init()
	{
		global $context, $modSettings, $smcFunc;

		// get all subject for select
		$padlen = 0;
		$this->posts = array();

		if(!empty($context['pmx']['promotes']))
		{
			$request = $smcFunc['db_query']('', '
				SELECT id_msg, subject
				FROM {db_prefix}messages
				WHERE id_msg IN ({array_int:messages})'. ($modSettings['postmod_active'] ? ' AND approved = {int:is_approved}' : '') .'
				ORDER BY id_msg DESC',
				array(
					'messages' => $context['pmx']['promotes'],
					'is_approved' => 1
				)
			);
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				censorText($row['subject']);
				$padlen = ($padlen == 0 ? strlen($row['id_msg']) : $padlen);
				$this->posts[$row['id_msg']] = '['. str_pad($row['id_msg'], $padlen, ' ', STR_PAD_LEFT) .'] '. $row['subject'];
			}
			$smcFunc['db_free_result']($request);
		}

		$this->block_classdef = PortaMx_getdefaultClass(true);	// extended classdef
		$this->can_cached = 1;		// enable caching
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

		// define numeric vars to check
		echo '
						<input type="hidden" name="check_num_vars[]" value="[config][settings][teaser], 40" />';

		// show the settings screen
		if(empty($this->cfg['config']['settings']['selectby']))
			$selMode = 'posts';
		else
			$selMode = $this->cfg['config']['settings']['selectby'];

		echo '
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid"><span class="cat_left_title">'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span></h4>
						</div>

						<div class="adm_input">
							<span class="adm_w80">'. $txt['pmx_promoted_selposts'] .'</span>
							<input id="selpost" onchange="togglePromote(this)" class="input_check" type="radio" name="config[settings][selectby]" value="posts"'. ($selMode == 'posts' ? ' checked="checked"' : '') .' />
						</div>
						<div class="adm_input">
							<span class="adm_w80">'. $txt['pmx_promoted_selboards'] .'</span>
							<input id="selboard" onchange="togglePromote(this)" class="input_check" type="radio" name="config[settings][selectby]" value="boards"'. ($selMode == 'boards' ? ' checked="checked"' : '') .' />
						</div>

						<div id="selpostdiv" class="adm_input"'. ($selMode != 'posts' ? ' style="display:none;"' : '') .'>
							<span>'. $txt['pmx_promoted_posts'] .'</span>
							<select class="adm_w90" name="config[settings][posts][]" multiple="multiple" size="6">
								<option value="0"'. (in_array('0', $this->cfg['config']['settings']['posts']) ? ' selected="selected"' : '') .'>'. $txt['pmx_promote_all'] .'</option>';

		foreach($this->posts as $msgid => $subject)
			echo '
								<option value="'. $msgid .'"'. (in_array($msgid, $this->cfg['config']['settings']['posts']) ? ' selected="selected"' : '') .'>'. $subject .'</option>';

		echo '
							</select>
						</div>

						<div id="selboarddiv" class="adm_input"'. ($selMode != 'boards' ? ' style="display:none;"' : '') .'>
							<span>'. $txt['pmx_postnews_boards'] .'</span>
							<select class="adm_w90" name="config[settings][boards][]" size="6" multiple="multiple" >';

		$boards = !empty($this->cfg['config']['settings']['boards']) ? (!is_array($this->cfg['config']['settings']['boards']) ? array($this->cfg['config']['settings']['boards']) : $this->cfg['config']['settings']['boards']) : array();
		foreach($this->smf_boards as $brd)
			echo '
								<option value="'. $brd['id'] .'"'. (in_array($brd['id'], $boards) ? ' selected="selected"' : '') .'>'. $brd['name'] .'</option>';

		echo '
							</select>
						</div>
						<script type="text/javascript"><!-- // --><![CDATA[
							function togglePromote(elm)
							{
								if(elm.id == "selpost")
								{
									document.getElementById("selboarddiv").style.display = "none";
									document.getElementById("selpostdiv").style.display = "";
								}
								else
								{
									document.getElementById("selboarddiv").style.display = "";
									document.getElementById("selpostdiv").style.display = "none";
								}
							}
						// ]]></script>

						<div class="adm_check" style="height:20px;margin-top:3px;"">
							<span class="adm_w80">'. $txt['pmx_boponews_postinfo'] .'</span>
							<input type="hidden" name="config[settings][postinfo]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][postinfo]" value="1"' .(!empty($this->cfg['config']['settings']['postinfo']) ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_boponews_postviews'] .'</span>
							<input type="hidden" name="config[settings][postviews]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][postviews]" value="1"' .(!empty($this->cfg['config']['settings']['postviews']) ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_input" style="min-height:20px;">
							<span class="adm_w80">'. $txt['pmx_boponews_page'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxNPH03")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<div><input onkeyup="check_numeric(this);" size="2" type="text" name="config[settings][onpage]" value="' .(isset($this->cfg['config']['settings']['onpage']) ? $this->cfg['config']['settings']['onpage'] : ''). '" /></div>
							<div id="pmxNPH03" class="info_frame" style="margin-top:2px;">'. $txt['pmx_pageindex_help'] .'</div>
						</div>

						<div class="adm_check" style="min-height:20px;">
							<span class="adm_w80">'. $txt['pmx_pageindex_pagetop'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxNPH04")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<input type="hidden" name="config[settings][pgidxtop]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][pgidxtop]" value="1"' .(isset($this->cfg['config']['settings']['pgidxtop']) && !empty($this->cfg['config']['settings']['pgidxtop']) ? ' checked="checked"' : ''). ' /></div>
							<div id="pmxNPH04" class="info_frame" style="margin-top:4px;">'. $txt['pmx_pageindex_tophelp'] .'</div>
						</div>

						<div class="adm_input" style="min-height:20px;">
							<span class="adm_w80">'. sprintf($txt['pmx_adm_teaser'], $txt['pmx_teasemode'][intval(!empty($context['pmx']['settings']['teasermode']))]) .'
								<img class="info_toggle" onclick=\'Show_help("pmxNPH01")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<div><input onkeyup="check_numeric(this);" size="2" type="text" name="config[settings][teaser]" value="' .(isset($this->cfg['config']['settings']['teaser']) ? $this->cfg['config']['settings']['teaser'] : '40'). '" /></div>
							<div id="pmxNPH01" class="info_frame" style="margin-top:2px;">'. $txt['pmx_adm_teasehelp'] .'</div>
						</div>

						<div class="adm_input" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_boponews_rescale'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxNPH02")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<div><input onkeyup="check_numeric(this);" size="3" type="text" name="config[settings][rescale]" value="' .(isset($this->cfg['config']['settings']['rescale']) ? $this->cfg['config']['settings']['rescale'] : '0'). '" /></div>
						</div>
						<div id="pmxNPH02" class="info_frame" style="margin-top:4px;">'. $txt['pmx_boponews_rescalehelp'] .'</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_boponews_showthumbs'] .'</span>
							<input type="hidden" name="config[settings][thumbs]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][thumbs]" value="1"' .(isset($this->cfg['config']['settings']['thumbs']) && !empty($this->cfg['config']['settings']['thumbs']) ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_input" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_boponews_thumbcnt'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxNPH2x")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<div><input onkeyup="check_numeric(this);" size="2" type="text" name="config[settings][thumbcnt]" value="' .(isset($this->cfg['config']['settings']['thumbcnt']) ? $this->cfg['config']['settings']['thumbcnt'] : ''). '" /></div>
						</div>
						<div id="pmxNPH2x" class="info_frame" style="margin-top:4px;">'. $txt['pmx_boponews_thumbcnthelp'] .'</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_boponews_hidethumbs'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxNPH2y")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<input type="hidden" name="config[settings][hidethumbs]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][hidethumbs]" value="1"' .(isset($this->cfg['config']['settings']['hidethumbs']) && !empty($this->cfg['config']['settings']['hidethumbs']) ? ' checked="checked"' : ''). ' /></div>
						</div>
						<div id="pmxNPH2y" class="info_frame" style="margin-top:4px;">'. $txt['pmx_boponews_hidethumbshelp'] .'</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_boponews_split'] .'</span>
							<input type="hidden" name="config[settings][split]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][split]" value="1"' .(isset($this->cfg['config']['settings']['split']) && !empty($this->cfg['config']['settings']['split']) ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_boponews_equal'] .'</span>
							<input type="hidden" name="config[settings][equal]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][equal]" value="1"' .(isset($this->cfg['config']['settings']['equal']) && !empty($this->cfg['config']['settings']['equal']) ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_boponews_disableHS'] .'</span>
							<input type="hidden" name="config[settings][disableHS]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][disableHS]" value="1"' .(isset($this->cfg['config']['settings']['disableHS']) && !empty($this->cfg['config']['settings']['disableHS']) ? ' checked="checked"' : '').(!empty($context['pmx']['settings']['disableHS']) ? ' disabled="disabled"' : '') .' /></div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_boponews_disableHSimage'] .'</span>
							<input type="hidden" name="config[settings][disableHSimg]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][disableHSimg]" value="1"' .(isset($this->cfg['config']['settings']['disableHSimg']) && !empty($this->cfg['config']['settings']['disableHSimg']) ? ' checked="checked"' : '').(!empty($context['pmx']['settings']['disableHS']) ? ' disabled="disabled"' : '') .' /></div>
						</div>';

		if($this->cfg['side'] == 'pages')
			echo '
						<div class="adm_check" style="min-height:20px;">
							<span class="adm_w80">'. $txt['pmx_enable_sitemap'] .'</span>
							<input type="hidden" name="config[show_sitemap]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[show_sitemap]" value="1"' .(!empty($this->cfg['config']['show_sitemap']) ? ' checked="checked"' : ''). ' /></div>
						</div>';

		// return the used classnames
		return $this->block_classdef;
	}
}
?>
