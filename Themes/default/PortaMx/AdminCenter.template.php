<?php
/**
* \file AdminCenter.template.php
* Template for the Admin Center.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

function template_main()
{
	global $context, $settings, $scripturl, $txt;

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

	if($context['pmx']['subaction'] != 'showlang')
		echo '
	<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:1px; overflow:hidden;">
		<tr>
			<td align="center">
				<div class="title_bar">
					<h3 class="titlebg largetext">', $txt['pmx_admin_center'], '</h3>
				</div>
			</td>
		</tr>
		<tr>
			<td class="normaltext" valign="top">
				<span class="upperframe"><span></span></span>
					<div class="roundframe">
						<b>'. $txt['hello_guest'] .' '. $context['user']['name'] .'!</b>
						'. sprintf($txt['pmx_admin_main_welcome'] ,'<img src="'. $context['pmx_imageurl'] .'information.png" alt="*" title="" style="margin-bottom:-3px;" />') .'
					</div>
				<span class="lowerframe"><span></span></span>
			</td>
		</tr>
		<tr>
			<td valign="top">';

	// Admin center main ?
	if($context['pmx']['subaction'] == 'main')
	{
		echo '
					<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:1em;">
						<tr>
							<td width="65%">
								<div class="cat_bar">
									<h3 class="catbg">'. $txt['pmx_center_news'] .'</h3>
								</div>
							</td>
							<td width="5" style="padding:0px 3px;"></td>
							<td width="40%">
								<div class="cat_bar">
									<h3 class="catbg">'. $txt['pmx_center_support'] .'</h3>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top"><div class="windowbg">
								<span class="topslice"><span></span></span>
									<div class="smalltext" style="padding:0 4px 0 6px; height:116px; overflow:auto;">';

			if(!empty($context['pmx_info']['item']))
			{
				foreach($context['pmx_info']['item'] as $data)
					echo '
										'. $data['subject'] .' on '. $data['date'] .'
										<div style="padding:0 0 10px 0;border-top: 1px dashed;">'. $data['msg'] .'</div>';
			}
			else
				echo '
										<div>'. $txt['pmx_center_nolivedata'] .'</div>';

			echo '
									</div>
								<span class="botslice"><span></span></span>
							</div></td>
							<td width="10" style="padding:0px 7px;"></td>
							<td valign="top"><div class="windowbg">
								<span class="topslice"><span></span></span>
									<div style="height:100px;">
										<div class="normaltext" style="padding:2px 4px;">'. $txt['pmx_center_versioninfo'] .'<br />'.
											$txt['pmx_center_installed'] .'<i>'. $context['pmx_info']['installed'] .'</i><br />'.
											$txt['pmx_center_version'] .'<i>';

			if(!empty($context['pmx_info']['versionOK']))
				echo $context['pmx_info']['version'];
			else
				echo '<b>'. $context['pmx_info']['version'] .'</b>';
			echo	'</i>
										</div>
										<div class="normaltext" style="padding:2px 3px;">';

			if(!empty($context['pmx_info']['update']) && empty($context['pmx_info']['versionOK']))
				echo '
											<a href="'. $scripturl .'?action=admin;area=packages;sa=download;get;server='. $context['pmx_info']['updserver'] .';package='. $context['pmx']['server']['update'] . $context['pmx_info']['update'] .';'. $context['session_var'] .'='. $context['session_id'] .'">'. $txt['pmx_center_update'] .'</a>';

			echo '
										</div>
										<div class="normaltext" style="padding:2px 3px;">';

			if(!empty($context['pmx_info']['download']) && empty($context['pmx_info']['versionOK']))
				echo '
										<a href="'. $context['pmx_info']['download'] .'">'. $txt['pmx_center_download'] .'</a>';

			echo '
										</div>
									</div>
									<div class="normaltext" style="position:relative; left:3px; bottom:3px;">
										<a href="'. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_center;sa=flist;'. $context['session_var'] .'=' .$context['session_id'] .'">'. $txt['pmx_center_detailed'] .'</a>
									</div>
								<span class="botslice"><span></span></span>
							</div></td>
						</tr>
						<tr>
							<td colspan="3" style="padding-top:0.8em;">
								<div class="windowbg2" style="min-height:190px;">
									<span class="topslice"><span></span></span>
									<table class="normaltext" width="100%" cellspacing="5" cellpadding="0">
										<tr>
											<td valign="top" width="50%" style="padding:5px 0">
												<a href="'. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_settings;'. $context['session_var'] .'=' .$context['session_id'] .'"><img align="left" hspace="10" style="padding-bottom:20px;" src="'. $context['pmx_imageurl'] .'admc_settings.png" alt="*" title="" /></a>
												<a href="'. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_settings;'. $context['session_var'] .'=' .$context['session_id'] .'">'. $txt['pmx_center_mansettings'] .'</a><br /><span class="smalltext">'. $txt['pmx_center_mansettings_desc'] .'</span>
											</td>
											<td valign="top" width="50%" style="padding:5px 0">
												<a href="'. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_blocks;'. $context['session_var'] .'=' .$context['session_id'] .'"><img align="left" hspace="10" style="padding-bottom:20px;" src="'. $context['pmx_imageurl'] .'admc_blocks.png" alt="*" title="" /></a>
												<a href="'. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_blocks;'. $context['session_var'] .'=' .$context['session_id'] .'">'. $txt['pmx_center_manblocks'] .'</a><br /><span class="smalltext">'. $txt['pmx_center_manblocks_desc'] .'</span>
											</td>
										</tr>
										<tr>
											<td valign="top" width="50%" style="padding:5px 0">
												<a href="'. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_categories;'. $context['session_var'] .'=' .$context['session_id'] .'"><img align="left" hspace="10" style="padding-bottom:20px;" src="'. $context['pmx_imageurl'] .'admc_category.png" alt="*" title="" /></a>
												<a href="'. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_categories;'. $context['session_var'] .'=' .$context['session_id'] .'">'. $txt['pmx_center_mancategories'] .'</a><br /><span class="smalltext">'. $txt['pmx_center_mancategories_desc'] .'</span>
											</td>
											<td valign="top" width="50%" style="padding:5px 0">
												<a href="'. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_articles;'. $context['session_var'] .'=' .$context['session_id'] .'"><img align="left" hspace="10" style="padding-bottom:20px;" src="'. $context['pmx_imageurl'] .'admc_article.png" alt="*" title="" /></a>
												<a href="'. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_articles;'. $context['session_var'] .'=' .$context['session_id'] .'">'. $txt['pmx_center_manarticles'] .'</a><br /><span class="smalltext">'. $txt['pmx_center_manarticles_desc'] .'</span>
											</td>
										</tr>
										<tr>
											<td valign="top" width="50%" style="padding:5px 0">
												<a href="'. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_sefengine;'. $context['session_var'] .'=' .$context['session_id'] .'"><img align="left" hspace="10" src="'. $context['pmx_imageurl'] .'admc_pmxsef.png" alt="*" title="" /></a>
												<a href="'. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_sefengine;'. $context['session_var'] .'=' .$context['session_id'] .'">'. $txt['pmx_center_mansefengine'] .'</a><br /><span class="smalltext">'. $txt['pmx_center_mansefengine_desc'] .'</span>
											</td>
											<td valign="top" width="50%" style="padding:5px 0">
												<a href="'. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_languages;'. $context['session_var'] .'=' .$context['session_id'] .'"><img align="left" hspace="10" src="'. $context['pmx_imageurl'] .'admc_language.png" alt="*" title="" /></a>
												<a href="'. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_languages;'. $context['session_var'] .'=' .$context['session_id'] .'">'. $txt['pmx_center_manlangs'] .'</a><br /><span class="smalltext">'. $txt['pmx_center_manlangs_desc'] .'</span>
											</td>
										</tr>
									</table>
									<span class="botslice"><span></span></span>
								</div>
							</td>
						</tr>
					</table>';
	}

	// Detailed filelist ?
	if($context['pmx']['subaction'] == 'flist')
	{
		echo '
					<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:1em;">
						<tr>
							<td valign="top">
								<div class="cat_bar">
									<h3 class="catbg">'. $txt['pmx_center_vercheck'] .'</h3>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<span class="upperframe"><span></span></span>
									<div class="roundframe">
									'. $txt['pmx_center_vercheck_info'] .'
									</div>
								<span class="lowerframe"><span></span></span>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding-top:5px;"><div class="windowbg">
								<span class="topslice"><span></span></span>

								<table class="normaltext" width="100%" cellspacing="0" cellpadding="1" style="padding:0 5px;">
									<tr>
										<td valign="top" width="50%">'. $txt['pmx_center_file'] .'</td>
										<td valign="top" align="right" width="25%">'. $txt['pmx_center_fileinstalled'] .'</td>
										<td valign="top" align="right" width="25%">'. $txt['pmx_center_filecurrent'] .'</td>
									</tr>';

		foreach($context['pmx_info'] as $dirtext => $data)
		{
			echo '
									<tr>
										<td>';

			if($dirtext == 'pmx_filepackage')
				echo $txt[$dirtext];
			else
				echo '<a href="javascript:void(0);" onclick="return ToggleFileList(\''. $dirtext .'\');">'. $txt[$dirtext] .'</a>';

			echo '</td>
										<td align="right">'. $data['installed'] .'</td>
										<td align="right">'. $data['current'] .'</td>
									</tr>
									<tr>
										<td colspan="3">
											<div id="'. $dirtext .'" style="display:none; padding:3px 0px 5px 10px;">
												<table width="100%" cellspacing="0" cellpadding="0">
													<tr>
														<td width="60%">'. $txt['pmx_center_filename'] .'</td>
														<td width="25%" align="left">'. $txt['pmx_center_fileversion'] .'</td>
														<td width="15%" align="right">'. $txt['pmx_center_filedate'] .'</td>
													</tr>';

			if($dirtext == 'pmx_language_files')
			{
				foreach($context['pmx_installed_ext'] as $ext)
				{
					foreach($data['files'] as $file => $value)
					{
						if(!empty($file) && strstr($file, '.') == $ext)
							echo '
													<tr>
														<td valign="top">'. $value['subdir'] . $file .'</td>
														<td valign="top" align="left">'. $value['version'] .'</td>
														<td valign="top" align="right">'. $value['date'] .'</td>
													</tr>';
					}
					echo '
													<tr>
														<td colspan="3"><div style="height:5px;"></div></td>
													</tr>';
				}
			}

			else
			{
				$cdir = '';
				foreach($data['files'] as $file => $value)
				{
					if(!empty($file))
					{
						if(empty($cdir))
							$cdir = $value['subdir'];
						elseif($cdir != $value['subdir'])
						{
							$cdir = $value['subdir'];
							echo '
													<tr>
														<td colspan="3"><div style="height:5px;"></div></td>
													</tr>';
						}
						echo '
													<tr>
														<td valign="top">'. $value['subdir'] . $file .'</td>
														<td valign="top" align="left">'. $value['version'] .'</td>
														<td valign="top" align="right">'. $value['date'] .'</td>
													</tr>';
					}
				}
			}

			echo '
												</table>
											</div>
										</td>
									</tr>';
		}

		echo '
									<tr>
										<td colspan="3" style="padding:0.2em;text-align:center"><hr />
											<input class="button_submit" type="button" name="back" value="'. $txt['page_reqerror_button'] .'" onclick="window.location.href=\''. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_center\'" />
										</td>
									</tr>
								</table>

								<span class="botslice"><span></span></span>
								</div>
							</td>
						</tr>
					</table>';
	}

	// Show Error ?
	if($context['pmx']['subaction'] == 'error')
	{
		echo '
					<table width="75%" cellspacing="0" cellpadding="0" style="margin:0 auto; margin-top:1em; text-align:center;">
						<tr>
							<td align="center">
								<div class="cat_bar">
									<h3 class="titlebg largetext headerpadding">', $context['pmx']['AdmcError']['title'], '</h3>
								</div>
							</td>
						</tr>
						<tr>
							<td class="normaltext" valign="top">
								<span class="upperframe"><span></span></span>
									<div class="roundframe">
										'. $context['pmx']['AdmcError']['msg'] .'
										<div style="padding-top:10px;text-align:center">
											<input class="button_submit" type="button" name="back" value="'. $txt['page_reqerror_button'] .'" onclick="window.location.href=\''. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_center'. (!empty($context['pmx']['AdmcError']['subact']) ? ';sa='. $context['pmx']['AdmcError']['subact'] .';'. $context['session_var'] .'=' .$context['session_id'] : '') .'\'" />
										</div>
									</div>
								<span class="lowerframe"><span></span></span>
							</td>
						</tr>
					</table>';
	}

	// Show languages ?
	if($context['pmx']['subaction'] == 'showlang')
	{
		echo '
	<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:1px; overflow:hidden;">
		<tr>
			<td valign="top">
				<form id="pmx_form" accept-charset="'. $context['character_set'] .'" name="PMxAdmCenter" action="' . $scripturl . '?action='. $context['pmx']['admmode'] .';area=pmx_center;sa=admlang;'. $context['session_var'] .'=' .$context['session_id'] .'" method="post" style="margin: 0px;">
					<input type="hidden" name="sc" value="'. $context['session_id'] .'" />
					<input id="common_field" type="hidden" name="" value="" />';

		$tmp = explode(' ', $txt['pmx_admin_center']);
		echo '
					<table width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<div class="cat_bar">
									<h3 class="catbg">'. $tmp[0] .' '. $txt['pmx_center_showlang'] .'</h3>
								</div>
							</td>
						</tr>
						<tr>
							<td class="normaltext" valign="top">
								<span class="upperframe"><span></span></span>
									<div class="roundframe">
										<div id="holding" class="smalltext">'. $txt['pmx_center_showlang_info'] .'</div>
										<div id="loading" style="display:none; margin:0 auto; text-align:center;">'. $txt['pmx_processing'] .'<img hspace="10" style="vertical-align:-2px;" src="'. $context['pmx_imageurl'] .'loading.gif" alt="*" title="" /></div>
									</div>
								<span class="lowerframe"><span></span></span>
							</td>
						</tr>
						<tr>
							<td valign="top" style="padding-top:1em;">
								<div class="title_bar">
									<h3 class="titlebg centertext">'. $txt['pmx_center_langinstalled'] .'	</h3>
								</div>
								<table class="table_grid" width="100%" cellspacing="1" cellpadding="0">
									<tr class="catbg">
										<th class="first_th" align="left" width="35%">'. $txt['pmx_center_langname'] .'</th>
										<th align="left" width="20%">'. $txt['pmx_center_langcharset'] .'</th>
										<th align="left" width="10%">'. $txt['pmx_center_langversion'] .'</th>
										<th class="last_th" align="right" width="15%">'. $txt['pmx_center_langaction'] .'</th>
									</tr>';

		// show installed languages
		$installed = array();
		foreach($context['pmx']['instlang'] as $id => $data)
		{
			$installed[$id] = $data;
			$lastbg = 'windowbg' .($id == 'lang.english' ? '' : '2');
			echo '
									<tr class="normaltext '. $lastbg .'">
										<td>'. (isset($data['manually']) ? '* ' : '') . $data['name'] .'</td>
										<td>'. $data['charset'] .'</td>
										<td>'. $data['version'] .'</td>
										<td align="right">';

			if($id != 'lang.english')
				echo '<a href="javascript:void(\'\')" onclick="FormFunc(\'lang_delete\', \''. $id .'\', \''. $txt['pmx_confirm_langdelete'] .'\');">'. $txt['pmx_center_langdelete'] .'</a>';

			echo '
										</td>
									</tr>';
		}

		echo '
								</table>
								<div class="'. $lastbg .'" style="margin-bottom:1em;">
									<span class="botslice"><span></span></span>
								</div>

								<div class="title_bar">
									<h3 class="titlebg centertext">', $txt['pmx_center_langavailable'], '</h3>
								</div>
								<table class="table_grid" width="100%" cellspacing="1" cellpadding="0">
									<tr class="catbg">
										<th class="first_th" align="left" width="35%">'. $txt['pmx_center_langname'] .'</th>
										<th align="left" width="20%">'. $txt['pmx_center_langcharset'] .'</th>
										<th align="left" width="10%">'. $txt['pmx_center_langversion'] .'</th>
										<th class="last_th" align="right" width="15%">'. $txt['pmx_center_langaction'] .'</th>
									</tr>';

		// show available languages
		if(!empty($context['pmx']['langsets']))
		{
			foreach($context['pmx']['langsets'] as $data)
			{
				// check is installed
				$isInst = compareLang($data, $installed['lang.english'], 'eq');
				$isUpd = false;
				foreach($installed as $i => $inst)
				{
					if(compareLang($data, $inst, 'gt'))
					{
						$isInst = true;
						if($data['version'] > $inst['version'])
							$isUpd = true;
						break;
					}
				}

				$lastbg = 'windowbg'. ($isInst || $isUpd ? '2' : '');
				echo '
								<tr class="normaltext '. $lastbg .'">
									<td>'. (isset($data['manually']) ? '* ' : '') . $data['name'] .'</td>
									<td>'. $data['charset'] .'</td>
									<td>'. $data['version'] .'</td>
									<td align="right">
										<a href="javascript:void(\'\')" onclick="FormFunc(\'lang_install\', \''. $data['link'] . (!empty($isInst) || !empty($isUpd) ? '\', \''. (empty($isUpd) ? $txt['pmx_confirm_langreplace'] : $txt['pmx_confirm_langupdate']) : '') .'\')">'. (empty($isInst) ? $txt['pmx_center_langinstall'] : (empty($isUpd) ? $txt['pmx_center_langreplace'] : $txt['pmx_center_langupdate'])) .'</a>
									</td>
								</tr>';
			}
		}
		else
			echo '
									<tr class="normaltext windowbg">
										<td colspan="4">'. $txt['pmx_center_fetchlang_failed'] .'</td>
									</tr>';

		echo '
								</table>
								<div class="'. $lastbg .'" style="margin-bottom:1em;">
									<span class="botslice"><span></span></span>
								</div>';

		// show manually instalable languages
		if(!empty($context['pmx']['manualylangsets']))
		{
			echo '
								<div class="title_bar">
									<h3 class="titlebg centertext">', $txt['pmx_center_manuallylang'], '</h3>
								</div>
								<table class="table_grid" width="100%" cellspacing="1" cellpadding="0">
									<tr class="catbg">
										<th class="first_th" align="left" width="35%">'. $txt['pmx_center_langname'] .'</th>
										<th align="left" width="20%">'. $txt['pmx_center_langcharset'] .'</th>
										<th align="left" width="10%">'. $txt['pmx_center_langversion'] .'</th>
										<th class="last_th" align="right" width="15%">'. $txt['pmx_center_langaction'] .'</th>
									</tr>';

			foreach($context['pmx']['manualylangsets'] as $key => $data)
			{
				$isInst = false;
				$isUpd = false;
				foreach($installed as $i => $inst)
				{
					if(compareLang($data, $inst, 'gt'))
					{
						$isInst = true;
						if($data['version'] > $inst['version'])
							$isUpd = true;
						break;
					}
				}

				$lastbg = 'windowbg'. ($isInst || $isUpd ? '2' : '');
				echo '
									<tr class="normaltext '. $lastbg .'">
										<td>'. $data['name'] .'</td>
										<td>'. $data['charset'] .'</td>
										<td>'. $data['version'] .'</td>
										<td align="right">
											<a href="javascript:void(\'\')" onclick="FormFunc(\'lang_install_manually\', \''. $key .'\')">'. (empty($isInst) ? $txt['pmx_center_langinstall'] : (empty($isUpd) ? $txt['pmx_center_langreplace'] : $txt['pmx_center_langupdate'])) .'</a>
										</td>
									</tr>';
			}
			echo '
								</table>
								<div class="'. $lastbg .'" style="margin-bottom:1em;">
									<span class="botslice"><span></span></span>
								</div>';
		}

		echo '
								<div style="padding:5px 10px;text-align:center">
									<input class="button_submit" type="button" name="back" value="'. $txt['page_reqerror_button'] .'" onclick="window.location.href=\''. $scripturl .'?action='. $context['pmx']['admmode'] .';area=pmx_center\'" />
								</div>
							</td>
						</tr>
					</table>
					<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
						function StartProgress()
						{
							document.getElementById("holding").style.display = "none";
							document.getElementById("loading").style.display = "";
						}
					// ]]></script>
					</form>';
	}
	echo '
				</td>
			</tr>
		</table>';
}
?>
