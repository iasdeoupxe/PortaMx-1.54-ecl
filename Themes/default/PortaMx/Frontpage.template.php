<?php
/**
* \file Frontpage.template.php
* Generic template for the Frontpage.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

/**
* The sub template above the content.
*/
function template_fronthtml_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />
	<meta name="keywords" content="', $context['meta_keywords'], '" />
	<title>', $context['page_title_html_safe'], '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// The ?rc2 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?rc3" />
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/print.css?rc3" media="print" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'firefox', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?rc3"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?rc3"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body>';
}

/**
* The sub template above the mainframe.
*/
function template_portamx_above()
{
	global $scripturl, $context, $options, $settings, $txt;

	echo !empty($settings['forum_width']) ? '
<div id="wrapper" style="width: ' . $settings['forum_width'] . '">' : '', '
	<span class="upperframe"><span></span></span>
	<div class="roundframe">
		<div style="padding:0px;">';

	// Insert a small menubar, if enabled
	if(!empty($context['pmx']['settings']['frontpagemenu']))
	{
		$notShow = array('help', 'search', 'profile', 'pm', 'calendar', 'mlist');
		foreach($notShow as $key)
			unset($context['menu_buttons'][$key]);

		echo '
			<div class="title_bar"><h3 class="titlebg largetext"><a href="'. $scripturl .'">'. $context['forum_name_html_safe'] .'</a></h3></div>
			<div class="menu_padding">', template_menu(), '</div>';
	}

	echo '
			<table width="100%" cellspacing="0" cellpadding="0" border="0">';

	// head panel
	if($context['pmx']['show_headpanel'])
	{
		$options['collapse_head'] = (($cook = pmx_getcookie('upshrhead')) && !is_null($cook) ? $cook : 0);

		echo '
				<tr>
					<td colspan="3" valign="top">';

		// IE less then IE8 can't handle floting divisons inside a table
		if(empty($context['browser']['is_ie']) || $context['browser']['is_ie8'])
			echo '
						<table class="pmx_fixedtable" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td valign="top">';

		if(empty($context['pmx']['settings']['head_panel']['collapse']) && $context['pmx']['xbar_head'])
			echo '
						<div id="xbarhead" title="'. (empty($options['collapse_head']) ? $txt['pmx_hidepanel'] : $txt['pmx_showpanel']) . $txt['pmx_block_panels']['head'] .'" onclick="headPanel.toggle()"></div>';
		echo '
						<div id="upshrinkHeadBar" style="margin-bottom:'. $context['pmx']['settings']['panelpad'] .'px;';

		// Height / overflow set?
		if(!empty($context['pmx']['settings']['head_panel']['size']))
		{
			echo ' max-height:'. $context['pmx']['settings']['head_panel']['size'] .'px;';
			if(!empty($context['pmx']['settings']['head_panel']['overflow']))
				echo ' overflow:'. $context['pmx']['settings']['head_panel']['overflow'] .';';
		}

		if(empty($context['pmx']['settings']['head_panel']['collapse']) && !empty($options['collapse_head']))
			echo ' display:none;';
		echo '">';

		PortaMx_ShowBlocks('head');

		echo '
						</div>';

		// IE less then IE8 can't handle floting divisons inside a table
		if(empty($context['browser']['is_ie']) || $context['browser']['is_ie8'])
		echo '
						</td></tr></table>';

		echo '
					</td>
				</tr>';
	}

	echo '
				<tr>
					<td valign="top">';

	// Left panel
	if(empty($context['right_to_left']) && $context['pmx']['show_leftpanel'])
			Show_Block('Left');
	elseif(!empty($context['right_to_left']) && $context['pmx']['show_rightpanel'])
			Show_Block('Right');

	echo '
					</td>
					<td valign="top" width="100%">';

	// IE less then IE8 can't handle floting divisons inside a table
	if(empty($context['browser']['is_ie']) || $context['browser']['is_ie8'])
		echo '
						<table class="pmx_maintable" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td valign="top">';

	if($context['pmx']['show_toppanel'])
	{
		$options['collapse_top'] = (($cook = pmx_getcookie('upshrtop')) && !is_null($cook) ? $cook : 0);

		if(empty($context['pmx']['settings']['top_panel']['collapse']) && $context['pmx']['xbar_top'])
			echo '
						<div id="xbartop" title="'. (empty($options['collapse_top']) ? $txt['pmx_hidepanel'] : $txt['pmx_showpanel']) . $txt['pmx_block_panels']['top'] .'" onclick="topPanel.toggle()"></div>';
		echo '
						<div id="upshrinkTopBar" style="margin-bottom:'. $context['pmx']['settings']['panelpad'] .'px;';

		// Height / overflow set?
		if(!empty($context['pmx']['settings']['top_panel']['size']))
		{
			echo ' max-height:'. $context['pmx']['settings']['top_panel']['size'] .'px;';
			if(!empty($context['pmx']['settings']['top_panel']['overflow']))
				echo ' overflow:'. $context['pmx']['settings']['top_panel']['overflow'] .';';
		}

		if(empty($context['pmx']['settings']['top_panel']['collapse']) && !empty($options['collapse_top']))
			echo ' display:none;';
		echo '">';

		PortaMx_ShowBlocks('top');

		echo '
						</div>';
	}
	echo '
						<div>';
}

/**
* The sub template below the mainframe.
*/
function template_portamx_below()
{
	global $context, $options, $scripturl, $settings, $txt;

	echo '
						</div>';

	if($context['pmx']['show_bottompanel'])
	{
		$options['collapse_bottom'] = (($cook = pmx_getcookie('upshrbottom')) && !is_null($cook) ? $cook : 0);

		if(empty($context['pmx']['settings']['bottom_panel']['collapse']) && $context['pmx']['xbar_bottom'])
			echo '
						<div id="xbarbottom" title="'. (empty($options['collapse_bottom']) ? $txt['pmx_hidepanel'] : $txt['pmx_showpanel']) . $txt['pmx_block_panels']['bottom'] .'" onclick="bottomPanel.toggle()"></div>';
		echo '
						<div id="upshrinkBottomBar" style="margin-top:'. $context['pmx']['settings']['panelpad'] .'px;';

		// Height / overflow set?
		if(!empty($context['pmx']['settings']['bottom_panel']['size']))
		{
			echo ' max-height:'. $context['pmx']['settings']['bottom_panel']['size'] .'px;';
			if(!empty($context['pmx']['settings']['bottom_panel']['overflow']))
				echo ' overflow:'. $context['pmx']['settings']['bottom_panel']['overflow'] .';';
		}

		if(empty($context['pmx']['settings']['bottom_panel']['collapse']) && !empty($options['collapse_bottom']))
			echo ' display:none;';
		echo '">';

		PortaMx_ShowBlocks('bottom');

		echo '
						</div>';
	}

	// IE less then IE8 can't handle floting divisons inside a table
	if(empty($context['browser']['is_ie']) || $context['browser']['is_ie8'])
		echo '
						</td></tr></table>';

	echo '
					</td>
					<td valign="top">';

	// Right panel
	if(empty($context['right_to_left']) && $context['pmx']['show_rightpanel'])
			Show_Block('Right');
	elseif(!empty($context['right_to_left']) && $context['pmx']['show_leftpanel'])
			Show_Block('Left');

	echo '
					</td>
				</tr>';

	// foot panel
	if($context['pmx']['show_footpanel'])
	{
		$options['collapse_foot'] = (($cook = pmx_getcookie('upshrfoot')) && !is_null($cook) ? $cook : 0);

		echo '
				<tr>
					<td colspan="3" valign="top">';

		// IE less then IE8 can't handle floting divisons inside a table
		if(empty($context['browser']['is_ie']) || $context['browser']['is_ie8'])
			echo '
						<table class="pmx_fixedtable" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td valign="top">';

		if(empty($context['pmx']['settings']['foot_panel']['collapse']) && $context['pmx']['xbar_foot'])
			echo '
						<div id="xbarfoot" title="'. (empty($options['collapse_foot']) ? $txt['pmx_hidepanel'] : $txt['pmx_showpanel']) . $txt['pmx_block_panels']['foot'] .'" onclick="footPanel.toggle()"></div>';
		echo '
						<div id="upshrinkFootBar" style="margin-top:'. $context['pmx']['settings']['panelpad'] .'px;';

		// Height / overflow set?
		if(!empty($context['pmx']['settings']['foot_panel']['size']))
		{
			echo ' max-height:'. $context['pmx']['settings']['foot_panel']['size'] .'px;';
			if(!empty($context['pmx']['settings']['foot_panel']['overflow']))
				echo ' overflow:'. $context['pmx']['settings']['foot_panel']['overflow'] .';';
		}

		if(empty($context['pmx']['settings']['foot_panel']['collapse']) && !empty($options['collapse_foot']))
			echo ' display:none;';
		echo '">';

		PortaMx_ShowBlocks('foot');

		echo '
						</div>';

		// IE less then IE8 can't handle floting divisons inside a table
		if(empty($context['browser']['is_ie']) || $context['browser']['is_ie8'])
		echo '
						</td></tr></table>';

		echo '
					</td>
				</tr>';
	}

	echo '
			</table>'. $context['pmx']['html_footer'] .'
	// ]]></script>';
}

/**
* The sub template below the content.
*/
function template_fronthtml_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show the "Powered by" as well as the copyright.
	echo '
			<div id="footerarea">
				<div style="margin:0 auto; text-align:center;">
					<div class="copywrite smalltext">', theme_copyright(), '</div>
				</div>
			</div>
		</div>
	</div>
	<span class="lowerframe"><span></span></span>',
!empty($settings['forum_width']) ? '
</div>' : '', '
</body></html>';
}

/**
* write out a side block (left or right).
*/
function Show_Block($side)
{
	global $context, $options, $txt;

	$options['collapse_'.strtolower($side)] = (($cook = pmx_getcookie('upshr'.strtolower($side))) && !is_null($cook) ? $cook : 0);

	if(empty($context['pmx']['settings'][strtolower($side).'_panel']['collapse']) && $context['pmx']['xbar_'.strtolower($side)])
		echo '
				<div id="xbar'.strtolower($side).'" title="'. (empty($options['collapse_'.strtolower($side)]) ? $txt['pmx_hidepanel'] : $txt['pmx_showpanel']) . $txt['pmx_block_panels'][strtolower($side)] .'" onclick="'.strtolower($side).'Panel.toggle();portamx_EqualHeight();"></div>';
	echo '
				<div id="upshrink'.$side.'Bar" style="width:'. $context['pmx']['settings'][strtolower($side).'_panel']['size'] .'px; margin-'.($side == 'Left' ? 'right' : 'left').':'. $context['pmx']['settings']['panelpad'] .'px; overflow:auto;';

	if(empty($context['pmx']['settings'][strtolower($side).'_panel']['collapse']) && !empty($options['collapse_'.strtolower($side)]))
		echo ' display:none;';
	echo '">';

	PortaMx_ShowBlocks(strtolower($side));

	echo '
				</div>';
}
?>
