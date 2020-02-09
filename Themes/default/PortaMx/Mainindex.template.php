<?php
/**
* \file Mainindex.template.php
* Template for the Maininxdex (Forumpage).
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

/**
* The sub template above the mainframe.
*/
function template_portamx_above()
{
	global $context, $options, $txt;

	echo '
	<table width="100%" cellspacing="0" cellpadding="0" border="0">';

	// head panel
	if(!empty($context['pmx']['show_headpanel']))
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
	if(empty($context['right_to_left']) && !empty($context['pmx']['show_leftpanel']))
			Show_Block('Left');
	elseif(!empty($context['right_to_left']) && !empty($context['pmx']['show_rightpanel']))
			Show_Block('Right');

	echo '
			</td>
			<td width="100%" valign="top">';

	// IE less then IE8 can't handle floting divisons inside a table
	if(empty($context['browser']['is_ie']) || $context['browser']['is_ie8'])
		echo '
				<table id="pmx_maintable" class="pmx_maintable" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td valign="top">';

	if(!empty($context['pmx']['show_toppanel']))
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
				<div id="pmx_toppad">';
}

/**
* The sub template below the mainframe.
*/
function template_portamx_below()
{
	global $context, $options, $scripturl, $settings, $txt;

	echo '
				</div>';

	if(!empty($context['pmx']['show_bottompanel']))
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
	if(empty($context['right_to_left']) && !empty($context['pmx']['show_rightpanel']))
			Show_Block('Right');
	elseif(!empty($context['right_to_left']) && !empty($context['pmx']['show_leftpanel']))
			Show_Block('Left');

	echo '
			</td>
		</tr>';

	// foot panel
	if(!empty($context['pmx']['show_footpanel']))
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
