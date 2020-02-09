<?php
/**
* \file Error.template.php
* Error template.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

function template_main()
{
	global $context, $scripturl, $settings, $language, $cookiename, $txt;

	$replaces = array('@host@' => $_SERVER['SERVER_NAME'], '@cookie@' => $cookiename, '@site@' => $context['forum_name']);

	echo '
		<div style="margin:0 auto; width:'. (!empty($context['pmx_eclcheck']) ? '90%' : '70%;') .'">
			<div class="cat_bar">
				<h3 class="catbg"><span style="display:block;text-align:center;">'. $context['pmx_error_title'] .'</span></h3>
			</div>
			<span class="upperframe"><span></span></span>
				<div class="windowbg roundframe middletext"'. (!empty($context['pmx_style_isCore']) ? 'style="margin-top:-1px;"' : '') .'>
					<div style="padding: 5px 10px 10px 10px;line-height:1.5em;">';

	if(!empty($context['pmx_eclcheck']))
	{
		$currlang = !empty($_GET['language']) ? $_GET['language'] : $language;
		if(empty($modSettings['pmxportal_disabled']) && isset($context['pmx']['languages']) && count($context['pmx']['languages']) > 1)
		{
			echo '
						<div style="text-align:right; height:30px;">
						'. $txt['pmxelc_lang'] .'&nbsp;
							<select size="1" name="" onchange="Setlang(this)">';

			foreach($context['pmx']['languages'] as $lang => $sel)
				echo '
							<option value="'. $scripturl .'?language='. $lang .'"'. (!empty($sel) ? ' selected="selected"' : '') .'>'. $lang .'</option>';

			echo '
							</select>
						</div>';
		}
		echo '
						<div style="clear:both;padding:10px;text-align:center;line-height:1.5em;">
							'. $context['pmx_error_text'] .'<br /><br />
							<input class="button_submit" type="button" name="accept" value="'. $txt['pmxelc_button'] .'" title="'. $txt['pmxelc_button_ttl'] .'" onclick="pmx_seteclcook(\'ecl_auth\', 1);window.location.href=smf_scripturl" />&nbsp;
							<input class="button_submit" type="button" name="privacy" value="'. $txt['pmxelc_privacy'] .'" title="'. $txt['pmxelc_privacy_ttl'] .'" onclick="pmx_showprivacy()" />
						</div>
						<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
							function pmx_showprivacy()
							{
								if(document.getElementById("ecl_privacy"))
									document.getElementById("ecl_privacy").style.display = document.getElementById("ecl_privacy").style.display == "" ? "none" : "";
							}
						// ]]></script>
						<div id="ecl_privacy" style="padding:5px;display:none;">
							<hr />';

		$privacyfile = $settings['default_theme_dir'] .'/languages/PortaMx/ecl_privacynotice.'. $currlang .'.php';
		if(file_exists($privacyfile))
		{
			include_once($privacyfile);

			echo '
				<div id="ecl_privacytext">
				'. strtr($txt['pmx_ecl_header'], $replaces) .'
					<table cellspacing="0" cellpadding="0" width="100%" border="0">';

			foreach($txt['pmx_ecl_headrows'] as $row => $ecltextrows)
			{
				echo '
						<tr>';

				foreach($ecltextrows as $nr => $ecltext)
					echo '
							<td valign="top"'. ($nr == 2 ? ' width="20%"' : '') . empty($row).'>'. (empty($row) ? '<u>' : '') . strtr($ecltext, $replaces) .(empty($row) ? '</u>' : '') .'</td>';

				echo '
						</tr>';
			}

			echo '
					</table>
					<br />';

			echo '
				'. $txt['pmx_ecl_footertop'] .'
					<table cellspacing="0" cellpadding="0" width="100%" border="0">';

			foreach($txt['pmx_ecl_footrows'] as $ecltextrows)
			{
				echo '
						<tr>';

				foreach($ecltextrows as $ecltext)
					echo '
							<td valign="top">'. $ecltext .'</td>';

				echo '
						</tr>';
			}

			echo '
					</table>';

			echo '
				'. $txt['pmx_ecl_footer'] .'
				</div>';
		}
		else
			echo '
							<div>'. $txt['pmxelc_privacy_failed'] .'</div>';

		echo '
						</div>';
	}
	else
		echo '
						<div style="padding:10px;text-align:center;line-height:1.5em;">
							'. $context['pmx_error_text'] .'<br /><br />
							<input class="button_submit" type="button" name="back" value="'. $txt['page_reqerror_button'] .'" onclick="window.history.back()" />
						</div>';

	echo '
					</div>
				</div>
			<span class="lowerframe"><span></span></span>
		</div>';
}

/**
* Wap subtemplate eclcookie
*/
function template_wap_eclcookie()
{
	global $context, $scripturl, $txt;

	echo '
		<card id="main" title="'. $context['page_title'] .'">
			<p><strong>', $context['forum_name_html_safe'], '</strong><br /></p>
			<p><strong>'. $context['pmx_error_title'] .'</strong></p>
			<p>'. $context['pmx_error_text'] .'<br />'. $txt['pmxelc_privacy_note'] .'</p>
			<p><a href="'. $scripturl .'?wap;accepteclcoookie=yes" accesskey="0">'. $txt['pmxelc_button'] .'</a></p>
		</card>';
}

/**
* Wap2 subtemplate eclcookie
*/
function template_wap2_eclcookie()
{
	global $context, $scripturl, $txt;

	echo '
		<p class="catbg">'. $context['forum_name_html_safe'] .'</p>
		<p><b>'. $context['pmx_error_title'] .'</b></p>
		<p class="windowbg">'. $context['pmx_error_text'] .'<br />'. $txt['pmxelc_privacy_note'] .'</p>
		<p class="windowbg"><a href="'. $scripturl .'?wap2;accepteclcoookie=yes" accesskey="0">'. $txt['pmxelc_button'] .'</a></p>';
}

/**
* Imode subtemplate eclcookie
*/
function template_imode_eclcookie()
{
	global $context, $scripturl, $txt;

	echo '
		<table border="0" cellspacing="0" cellpadding="0">
			<tr bgcolor="#6d92aa"><td><font color="#ffffff">', $context['forum_name_html_safe'], '</font></td></tr>
			<tr><td><br /><b>'. $context['pmx_error_title'] .'</b></td></tr>
			<tr><td>'. $context['pmx_error_text'] .'<br />'. $txt['pmxelc_privacy_note'] .'</td></tr>
			<tr class="windowbg"><td><a href="'. $scripturl .'?imode;accepteclcoookie=yes" accesskey="0">'. $txt['pmxelc_button'] .'</a></td></tr>
		</table>';
}
?>
