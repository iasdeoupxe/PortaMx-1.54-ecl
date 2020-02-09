<?php
/**
* \file article_adm.php
* Admin Systemblock article
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_article_adm
* Admin Systemblock article_adm
* @see article_adm.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_article_adm extends PortaMxC_SystemAdminBlock
{
	var $articles;

	/**
	* AdmBlock_init().
	* Setup caching and get the articles.
	*/
	function pmxc_AdmBlock_init()
	{
		global $smcFunc;

		$this->can_cached = 1;		// enable caching
		$this->articles = array();

		// get all active and approved articles
		$request = $smcFunc['db_query']('', '
			SELECT a.id, a.name, a.acsgrp, a.ctype, a.config, a.owner, a.active, a.created, a.updated, a.content, m.member_name
			FROM {db_prefix}portamx_articles AS a
			LEFT JOIN {db_prefix}members AS m ON (a.owner = m.id_member)
			WHERE a.active > 0 AND a.approved > 0
			ORDER BY a.id',
			array()
		);

		if($smcFunc['db_num_rows']($request) > 0)
		{
			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				$row['config'] = unserialize($row['config']);
				if(!empty($this->cfg['config']['settings']['inherit_acs']) || allowPmxGroup($row['acsgrp']))
				{
					$row['side'] = $this->cfg['side'];
					$this->articles[] = $row;
				}
			}
			$smcFunc['db_free_result']($request);
		}
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
						<input type="hidden" name="config[static_block]" value="1" />
						<div style="min-height:169px;">';

		// show the settings screen
		echo '
							<div class="cat_bar catbg_grid grid_padd">
								<h4 class="catbg catbg_grid"><span class="cat_left_title">'. sprintf($txt['pmx_blocks_settings_title'], $this->register_blocks[$this->cfg['blocktype']]['description']) .'</span></h4>
							</div>

							<div>
								<div style="float:'. (empty($context['right_to_left']) ? 'left' : 'right') .';">'. $txt['pmx_artblock_arts'] .'</div>
								<div style="float:'. (empty($context['right_to_left']) ? 'right' : 'left') .';width:40%;padding-'. (empty($context['right_to_left']) ? 'right' : 'left') .':10%;">
									<select style="width:99%;" name="config[settings][article]" size="1">';

		// output articles
		foreach($this->articles as $art)
			echo '
										<option value="'. $art['name'] .'"' .(isset($this->cfg['config']['settings']['article']) && $this->cfg['config']['settings']['article'] == $art['name'] ? ' selected="selected"' : '') .'>'. $art['name'] .'</option>';

		echo '
										</select>
									</div>
								</div>';

		// show mode (titelbar/frame)
		$this->cfg['config']['settings']['usedframe'] = !isset($this->cfg['config']['settings']['usedframe']) ? 'block' : $this->cfg['config']['settings']['usedframe'];
		echo '
							<div class="adm_check" style="padding-top:5px; min-height:20px;">
								<span style="width:86%;">'. $txt['pmx_artblock_blockframe'] .'</span>
								<div><input class="input_check" type="radio" name="config[settings][usedframe]" value="block"' .(isset($this->cfg['config']['settings']['usedframe']) && $this->cfg['config']['settings']['usedframe'] == 'block' ? ' checked="checked"' : '') .' /></div>
							</div>

							<div class="adm_check" style="min-height:20px;">
								<span style="width:86%;">'. $txt['pmx_artblock_artframe'] .'</span>
								<div><input class="input_check" type="radio" name="config[settings][usedframe]" value="article"' .(isset($this->cfg['config']['settings']['usedframe']) && $this->cfg['config']['settings']['usedframe'] == 'article' ? ' checked="checked"' : '') .' /></div>
							</div>

							<div class="adm_check" style="min-height:20px;">
								<span style="width:86%;">'. $txt['pmx_artblock_inherit'] .'
									<img class="info_toggle" onclick=\'Show_help("pmxartH01")\' src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="'. $txt['pmx_information_icon'] .'" />
								</span>
								<input type="hidden" name="config[settings][inherit_acs]" value="0" />
								<div><input class="input_check" type="checkbox" name="config[settings][inherit_acs]" value="1"' .(!empty($this->cfg['config']['settings']['inherit_acs']) ? ' checked="checked"' : '') .' /></div>
							</div>
							<div id="pmxartH01" class="info_frame" style="margin-top:4px;">'. $txt['pmx_artblock_inherithelp'] .'</div>';

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
		return PortaMx_getdefaultClass(false, true);  // default classdef
	}
}
?>
