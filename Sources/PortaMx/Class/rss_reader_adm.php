<?php
/**
* \file rss_reader_adm.php
* Admin Systemblock rss_reader
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_rss_reader_adm
* Admin Systemblock rss_reader_adm
* @see rss_reader_adm.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_rss_reader_adm extends PortaMxC_SystemAdminBlock
{
	/**
	* AdmBlock_init().
	* Setup caching and classdef.
	*/
	function pmxc_AdmBlock_init()
	{
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
						<input type="hidden" name="check_num_vars[]" value="[config][settings][rssmaxitems], \'\'" />
						<input type="hidden" name="check_num_vars[]" value="[config][settings][rsstimeout], \'5\'" />';

		// show the settings screen
		echo '
						<div class="cat_bar catbg_grid">
							<h4 class="catbg catbg_grid"><span class="cat_left_title">'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span></h4>
						</div>

						<div class="adm_input" style="min-height:40px;">
							<span class="adm_w90">'. $txt['pmx_rssreader_url'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxRSSH01")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<input class="adm_w90" style="margin-top:1px;" type="text" name="config[settings][rssfeedurl]" value="' .(!empty($this->cfg['config']['settings']['rssfeedurl']) ? $this->cfg['config']['settings']['rssfeedurl'] : ''). '" />
						</div>
						<div id="pmxRSSH01" class="info_frame" style="margin:2px 0px;">'. $txt['pmx_rssreader_urlhelp'] .'</div>

						<div class="adm_input" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_rssreader_timeout'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxRSSH02")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<div><input onkeyup="check_numeric(this);" size="2" type="text" name="config[settings][rsstimeout]" value="' .(!empty($this->cfg['config']['settings']['rsstimeout']) ? $this->cfg['config']['settings']['rsstimeout'] : '5'). '" /></div>
						</div>
						<div id="pmxRSSH02" class="info_frame" style="margin:2px 0px;">'. $txt['pmx_rssreader_timeouthelp'] .'</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_rssreader_usettl'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxRSSH03")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<input type="hidden" name="config[settings][usettl]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][usettl]" value="1"' .(!empty($this->cfg['config']['settings']['usettl']) ? ' checked="checked"' : ''). ' /></div>
						</div>
						<div id="pmxRSSH03" class="info_frame" style="margin:2px 0px;">'. $txt['pmx_rssreader_usettlhelp'] .'</div>

						<div class="adm_input" style="min-height:20px;">
							<span class="adm_w80">'. $txt['pmx_rssreader_maxitems'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxRSSH06a")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<div><input onkeyup="check_numeric(this);" size="2" type="text" name="config[settings][rssmaxitems]" value="' .(!empty($this->cfg['config']['settings']['rssmaxitems']) ? $this->cfg['config']['settings']['rssmaxitems'] : ''). '" /></div>
							<div id="pmxRSSH06a" class="info_frame" style="margin-top:2px;">'. $txt['pmx_rssmaxitems_help'] .'</div>
						</div>

						<div class="adm_input" style="min-height:20px;">
							<span class="adm_w80">'. $txt['pmx_rssreader_page'] .'</span>
							<div><input onkeyup="check_numeric(this);" size="2" type="text" name="config[settings][onpage]" value="' .(isset($this->cfg['config']['settings']['onpage']) ? $this->cfg['config']['settings']['onpage'] : ''). '" /></div>
						</div>

						<div class="adm_check" style="min-height:20px;">
							<span class="adm_w80">'. $txt['pmx_pageindex_pagetop'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxRSSH07")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<input type="hidden" name="config[settings][pgidxtop]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][pgidxtop]" value="1"' .(isset($this->cfg['config']['settings']['pgidxtop']) && !empty($this->cfg['config']['settings']['pgidxtop']) ? ' checked="checked"' : ''). ' /></div>
							<div id="pmxRSSH07" class="info_frame" style="margin-top:4px;">'. $txt['pmx_pageindex_tophelp'] .'</div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_rssreader_cont_encode'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxRSSH04")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<input type="hidden" name="config[settings][cont_encode]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][cont_encode]" value="1"' .(!empty($this->cfg['config']['settings']['cont_encode']) ? ' checked="checked"' : ''). ' /></div>
						</div>
						<div id="pmxRSSH04" class="info_frame" style="margin:2px 0px;">'. $txt['pmx_rssreader_cont_encodehelp'] .'</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_rssreader_split'] .'</span>
							<input type="hidden" name="config[settings][split]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][split]" value="1"' .(!empty($this->cfg['config']['settings']['split']) ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_boponews_equal'] .'</span>
							<input type="hidden" name="config[settings][equal]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][equal]" value="1"' .(isset($this->cfg['config']['settings']['equal']) && !empty($this->cfg['config']['settings']['equal']) ? ' checked="checked"' : ''). ' /></div>
						</div>

						<div class="adm_check" style="height:20px;">
							<span class="adm_w80">'. $txt['pmx_rssreader_showhead'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxRSSH05")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<input type="hidden" name="config[settings][showhead]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][showhead]" value="1"' .(!empty($this->cfg['config']['settings']['showhead']) ? ' checked="checked"' : ''). ' /></div>
						</div>
						<div id="pmxRSSH05" class="info_frame" style="margin:2px 0px;">'. $txt['pmx_rssreader_help'] .'</div>

						<div class="adm_input" style="height:20px;">
							<span class="adm_w30">'. $txt['pmx_rssreader_name'] .'</span>
							<input class="adm_w60" type="text" name="config[settings][rssfeed_name]" value="' .(!empty($this->cfg['config']['settings']['rssfeed_name']) ? $this->cfg['config']['settings']['rssfeed_name'] : ''). '" />
						</div>

						<div class="adm_input" style="height:20px;">
							<span class="adm_w30">'. $txt['pmx_rssreader_link'] .'</span>
							<input class="adm_w60" type="text" name="config[settings][rssfeed_link]" value="' .(!empty($this->cfg['config']['settings']['rssfeed_link']) ? $this->cfg['config']['settings']['rssfeed_link'] : ''). '" />
						</div>

						<div class="adm_input" style="height:20px;">
							<span class="adm_w30">'. $txt['pmx_rssreader_desc'] .'</span>
							<input class="adm_w60" type="text" name="config[settings][rssfeed_desc]" value="' .(!empty($this->cfg['config']['settings']['rssfeed_desc']) ? $this->cfg['config']['settings']['rssfeed_desc'] : ''). '" />
						</div>

						<div class="adm_input" style="min-height:20px;">
							<span class="adm_w80">'. sprintf($txt['pmx_adm_teaser'], $txt['pmx_teasemode'][intval(!empty($context['pmx']['settings']['teasermode']))]) .'
								<img class="info_toggle" onclick=\'Show_help("pmxRSSH08")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<div><input onkeyup="check_numeric(this);" size="2" type="text" name="config[settings][teaser]" value="' .(isset($this->cfg['config']['settings']['teaser']) ? $this->cfg['config']['settings']['teaser'] : '40'). '" /></div>
							<div id="pmxRSSH08" class="info_frame" style="margin-top:2px;">'. $txt['pmx_adm_teasehelp'] .'</div>
						</div>

						<div class="adm_check" style="min-height:20px;">
							<span class="adm_w80">'. $txt['pmx_rssreader_delimages'] .'
								<img class="info_toggle" onclick=\'Show_help("pmxRSSH09")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
							</span>
							<input type="hidden" name="config[settings][delimage]" value="0" />
							<div><input class="input_check" type="checkbox" name="config[settings][delimage]" value="1"' .(!empty($this->cfg['config']['settings']['delimage']) ? ' checked="checked"' : ''). ' /></div>
						</div>
						<div id="pmxRSSH09" class="info_frame" style="margin:2px 0px;">'. $txt['pmx_rssreader_delimagehelp'] .'</div>';

		if($this->cfg['side'] == 'pages')
			echo '
						<div class="adm_check" style="min-height:20px;">
							<span class="adm_w80">'. $txt['pmx_enable_sitemap'] .'</span>
							<div>
								<input type="hidden" name="config[show_sitemap]" value="0" />
								<div><input class="input_check" type="checkbox" name="config[show_sitemap]" value="1"' .(!empty($this->cfg['config']['show_sitemap']) ? ' checked="checked"' : ''). ' /></div>
							</div>
						</div>';

		// return the used classnames
		return $this->block_classdef;
	}
}
?>
