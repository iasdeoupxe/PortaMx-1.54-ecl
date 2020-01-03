<?php
/**
* \file Frames.template.php
* Template for the Block/Category/Article frame.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

/**
* Top frame
**/
function Pmx_Frame_top($cfg, $count)
{
	global $context, $scripturl, $language, $options, $txt;

	if(!empty($cfg['config']['skip_outerframe']))
		return null;

	// get the block title for user language have it or forum default
	$blocktitle = PortaMx_getTitle($cfg['config']);

	// the title align
	$ttladjust = '';
	switch($cfg['config']['title_align'])
	{
		case 'left':
			$imgalign = empty($context['right_to_left']) ? 'right' : 'left';
			$txtalign = empty($context['right_to_left']) ? 'left' : 'right';
			$ttlimg = $txtalign;
			break;

		case 'right':
			$imgalign = empty($context['right_to_left']) ? 'left' : 'right';
			$txtalign = empty($context['right_to_left']) ? 'right' : 'left';
			$ttlimg = $txtalign;
			break;

		case 'center':
			$imgalign = empty($context['right_to_left']) ? 'right' : 'left';
			$txtalign = 'center';
			$ttlimg = empty($context['right_to_left']) ? 'left' : 'right';
	}

	if($cfg['config']['title_icon'] == 'none.gif')
		$cfg['config']['title_icon'] = '';

	if(empty($cfg['config']['title_icon']))
		$ttladjust = ' pmxadj';

	if($cfg['config']['title_align'] == 'center')
	{
		if(!empty($cfg['config']['title_icon']) && !empty($cfg['config']['collapse']) && $context['pmx']['settings']['shrinkimages'] != 2)
			$ttladjust = ' pmxadj_center';
		elseif(empty($cfg['config']['title_icon']) && empty($cfg['config']['collapse']))
			$ttladjust = '';
		elseif(empty($cfg['config']['title_icon']))
			$ttladjust = ' pmxadj_'. $imgalign;
		else
			$ttladjust = ' pmxadj_'. $ttlimg;
	}
	$plainbox = (empty($context['pmx_style_isCore']) ? 'plainbox'. (!empty($cfg['config']['visuals']['body']) ? ' '. $cfg['config']['visuals']['body'] : '') : 'tborder');
	$cfg['config']['innerpad'] = (isset($cfg['config']['innerpad']) ? $cfg['config']['innerpad'] : '4');
	$innerPad = Pmx_getInnerPad($cfg['config']['innerpad']);

	// custom css ?
	if(!empty($cfg['customclass']))
	{
		$isCustHeader = !empty($cfg['customclass']['header']);
		$isCustFrame = !empty($cfg['customclass']['frame']);
	}
	else
		$isCustHeader = $isCustFrame = false;

	$spanclass = $isCustFrame && !empty($cfg['config']['visuals']['body']) ? $cfg['config']['visuals']['body'] .' ' : '';
	$IDtype = $cfg['blocktype'] . $cfg['id'];

	/**
	* curve styles
	**/
	if(empty($context['pmx_style_isCore']))
	{
		echo '
						<div'. (!empty($cfg['uniID']) && !in_array($cfg['side'], array('left', 'right')) ? ' id="top'. $cfg['uniID'] .'"' : '') .' style="margin-bottom:'. (empty($count) ? '0' : $context['pmx']['settings']['panelpad']) .'px; overflow:hidden;">';

		// show the collapse, if set and have a header
		$hashead = false;
		if((!empty($cfg['config']['visuals']['header']) && $cfg['config']['visuals']['header'] != 'none') || (empty($cfg['config']['visuals']['header']) && !empty($cfg['config']['visuals']['body'])))
		{
			$hashead = true;
			$head_bar = !empty($cfg['config']['visuals']['header']) ? str_replace('bg', '_bar', $cfg['config']['visuals']['header']) : '';
			if(!empty($head_bar) && empty($cfg['config']['visuals']['body']))
				$head_bar .= ' head_round';
			echo '
							<div class="'. (!empty($head_bar) ? $head_bar : 'title_no_bar') .'">
							<h3';

			if(!empty($cfg['config']['collapse']) && $context['pmx']['settings']['shrinkimages'] == 2)
				echo ' ondblclick="PmxBlock_Toggle(this, \''. $IDtype .'\', \''. $txt['pmx_collapse'] .'\', \''. $txt['pmx_expand'] .'\')" title="'. (empty($options['collapse'. $cfg['id']]) ? $txt['pmx_collapse'] : $txt['pmx_expand']) . $blocktitle .'"';

			echo ' class="'. (!empty($cfg['config']['visuals']['header']) ? $cfg['config']['visuals']['header'] : $cfg['config']['visuals']['body'] . ' cbodypad') .'">';

			// show the collapse / expand icon
			if(!empty($cfg['config']['collapse']) && $context['pmx']['settings']['shrinkimages'] != 2)
				echo '
								<img id="upshrink_'. $IDtype .'_Img" class="ce_images pmx'. $imgalign .'" src="'. (empty($options['collapse'. $IDtype]) ? $context['pmx_img_expand'] : $context['pmx_img_colapse']) .'" alt="*" title="'. (empty($options['collapse'. $IDtype]) ? $txt['pmx_collapse'] : $txt['pmx_expand']) . $blocktitle .'" />';

			// show the title icon is set
			if(!empty($cfg['config']['title_icon']))
				echo '
								<img class="title_images pmx'. $ttlimg .'" src="'. $context['pmx_Iconsurl'] . $cfg['config']['title_icon'] .'" alt="" title="'. $blocktitle . '" />';

			echo '
								<span class="pmxtitle pmx'. $txtalign . $ttladjust .'">';

			// if quickedit link the title to blockedit?
			if(!empty($context['pmx']['settings']['manager']['qedit']) && allowPmx('pmx_admin'))
			{
				$btyp = str_replace('static_', '', $cfg['blocktype']);
				echo '
									<a href="'. $scripturl .'?action='. (allowPmx('pmx_admin', true) ? 'portamx' : 'admin') .';area=pmx_'. (in_array($btyp, array('category', 'article')) ? ($btyp == 'category' ? 'categories;sa=edit;id='. preg_replace('/_[0-9]+/', '', $cfg['catid']) : 'articles;sa=edit;id='. preg_replace('/_[0-9]+/', '', $cfg['id'])) : 'blocks;sa='. $cfg['side']) .';edit='. preg_replace('/_[0-9]+/', '', $cfg['id']) .';'. $context['session_var'] .'=' .$context['session_id'] .'">'. $blocktitle .'</a>';
			}
			// else show the title normal
			else
				echo '
									<span>'. $blocktitle .'</span>';

			echo '
								</span>
							</h3>
							</div>';
		}

		// show content frame
		$spanclass = $isCustFrame && !empty($cfg['config']['visuals']['body']) ? $cfg['config']['visuals']['body'] .' ' : '';
		$bodyclass = trim($cfg['config']['visuals']['body'] .' '. $cfg['config']['visuals']['frame']);
		$frame = false;

		if(!empty($cfg['config']['visuals']['frame']) && ($cfg['config']['visuals']['frame'] == 'roundframe' || $isCustFrame))
			echo '
							<div'. (!empty($cfg['config']['collapse']) ? ' id="upshrink_'. $IDtype .'"'. (empty($options['collapse'. $IDtype]) ? '' : ' style="display:none;"') : '') .'>
								<span class="'. $spanclass . ($isCustFrame ? $cfg['config']['visuals']['frame'] .'_top' : 'upperframe') .'"><span></span></span>
								<div'. (!empty($bodyclass) ? ' class="'. $bodyclass .'"' : '') .' style="padding:'. $innerPad[0] .'px '. $innerPad[1] .'px;">
									<div';

		elseif(!empty($cfg['config']['visuals']['frame']) && $cfg['config']['visuals']['frame'] == 'round')
			echo '
							<div'. (!empty($cfg['config']['collapse']) ? ' id="upshrink_'. $IDtype .'"'. (empty($options['collapse'. $IDtype]) ? '' : ' style="display:none;"') : '') . (!empty($cfg['config']['visuals']['body']) ? ' class="blockcontent '. $cfg['config']['visuals']['body'] .'"' : '') .'>
								<span class="topslice"><span></span></span>
								<div style="padding:'. $innerPad[0] .'px '. $innerPad[1] .'px;">
									<div';

		elseif(!empty($cfg['config']['visuals']['frame']))
		{
			$frame = true;
			$frameClass = ($cfg['config']['visuals']['frame'] == 'border' ? $plainbox : $cfg['config']['visuals']['frame']);
			echo '
							<div'. (!empty($cfg['config']['collapse']) ? ' id="upshrink_'. $IDtype .'"'. (empty($options['collapse'. $IDtype]) ? '' : ' style="display:none;"') : '') .' class="'. $frameClass .' blockcontent core_toppad">
							<div'. (!empty($cfg['config']['visuals']['body']) ? ' class="'. $cfg['config']['visuals']['body'] .'"' : '') .'>
								<div style="padding:'. $innerPad[0] .'px '. $innerPad[1] .'px;">
									<div';
		}
		else
		{
			echo '
							<div'. (!empty($cfg['config']['collapse']) ? ' id="upshrink_'. $IDtype .'"'. (empty($options['collapse'. $IDtype]) ? '' : ' style="display:none;"') : '') . (!empty($cfg['config']['visuals']['body']) ? ' class="blockcontent '. $cfg['config']['visuals']['body'] .'"' : '') .'>
								<div'. (!empty($hashead) ? ' class="pmx_noframe_'. $cfg['blocktype'] .'"' : '') .' style="padding:'. $innerPad[0] .'px '. $innerPad[1] .'px;">
									<div';
		}

		// have a bodytext class ?
		if(!empty($cfg['config']['visuals']['bodytext']))
			echo ' class="'. $cfg['config']['visuals']['bodytext'] .'"';

		// have overflow, maxheight?
		if(!empty($cfg['config']['overflow']))
			echo ' style="'. (isset($cfg['config']['maxheight']) && !empty($cfg['config']['maxheight']) ? (empty($cfg['config']['height']) ? 'max-height' : $cfg['config']['height']) .':'. $cfg['config']['maxheight'] .'px; ' : '') .'overflow:'. $cfg['config']['overflow'] .';"';

		echo '>';
	}

	/**
	* core theme styles...
	**/
	else
	{
		$ttladjust .= '_core';

		echo '
					<div'. (!empty($cfg['uniID']) && !in_array($cfg['side'], array('left', 'right')) ? ' id="top'. $cfg['uniID'] .'"' : '');

		// have a Frame, show it
		$frame = false;
		if(!empty($cfg['config']['visuals']['frame']) && $cfg['config']['visuals']['frame'] == 'border')
		{
			$frame = true;
			echo ' class="core '. $plainbox .'" style="padding:1px; margin-top:0;';
		}
		else
			echo ' style="padding:0; margin-top:0;';

		echo ' margin-bottom:'. (empty($count) ? '1' : $context['pmx']['settings']['panelpad']) .'px;">';

		// have a header, show it
		if((!empty($cfg['config']['visuals']['header']) && $cfg['config']['visuals']['header'] != 'none') || (empty($cfg['config']['visuals']['header']) && !empty($cfg['config']['visuals']['body'])))
		{
			echo '
							<div';

			if(!empty($cfg['config']['collapse']) && $context['pmx']['settings']['shrinkimages'] == 2)
				echo ' ondblclick="PmxBlock_Toggle(this, \''. $IDtype .'\', \''. $txt['pmx_collapse'] .'\', \''. $txt['pmx_expand'] .'\')" title="'. (empty($options['collapse'. $IDtype]) ? $txt['pmx_collapse'] : $txt['pmx_expand']) . $blocktitle .'"';

			if(empty($cfg['config']['visuals']['header']) && empty($cfg['config']['visuals']['body']))
				echo '>';
			else
				echo ' class="'. (!empty($cfg['config']['visuals']['header']) ? $cfg['config']['visuals']['header'] : $cfg['config']['visuals']['body']) .'" style="height:30px; padding:0;">';

			// show the collapse image
			if(!empty($cfg['config']['collapse']) && $context['pmx']['settings']['shrinkimages'] != 2)
				echo '
								<img id="upshrink_'. $IDtype .'_Img" class="ce_images pmx'. $imgalign .'_cecore" src="'. (empty($options['collapse'. $IDtype]) ? $context['pmx_img_expand'] : $context['pmx_img_colapse']) .'" alt="*" title="'. (empty($options['collapse'. $IDtype]) ? $txt['pmx_collapse'] : $txt['pmx_expand']) . $blocktitle .'" />';

			// show the title icon is set
			if(!empty($cfg['config']['title_icon']))
				echo '
								<img class="title_images pmx'. $ttlimg .'_core" src="'. $context['pmx_Iconsurl'] . $cfg['config']['title_icon'] .'" alt="" title="'. $blocktitle . '" />';

			echo '
								<span class="pmxtitle pmx'. $txtalign . $ttladjust .'">';

			// if quickedit link the title to blockedit?
			if(!empty($context['pmx']['settings']['manager']['qedit']) && allowPmx('pmx_admin'))
			{
				$btyp = str_replace('static.', '', $cfg['blocktype']);
				echo '
									<a href="'. $scripturl .'?action='. (allowPmx('pmx_admin', true) ? 'portamx' : 'admin') .';area=pmx_'. (in_array($btyp, array('category', 'article')) ? ($btyp == 'category' ? 'categories;sa=edit;id='. preg_replace('/_[0-9]+/', '', $cfg['catid']) : 'articles;sa=edit;id='. preg_replace('/_[0-9]+/', '', $cfg['id'])) : 'blocks;sa='. $cfg['side'] .';edit='. preg_replace('/_[0-9]+/', '', $cfg['id'])) .';'. $context['session_var'] .'=' .$context['session_id'] .'">'. $blocktitle .'</a>';
			}
			// else show the title normal
			else
				echo '
									<span>'. $blocktitle .'</span>';

			echo '
								</span>
							</div>';
		}

		/**
		* show content frame
		**/
		echo '
								<div style="padding:0;'. (!empty($cfg['config']['collapse']) ? (empty($options['collapse'. $IDtype]) ? '"' : 'display:none;"') .' id="upshrink_'. $IDtype .'"' : '');

		if(!empty($cfg['config']['visuals']['frame']) && empty($frame))
		{
			$frame = true;
			if($cfg['config']['visuals']['frame'] == 'round' || $cfg['config']['visuals']['frame'] == 'roundframe')
				$frameClass = $plainbox;
			else
				$frameClass = $cfg['config']['visuals']['frame'];

			echo ' class="'. $frameClass .' blockcontent">
									<div'. (!empty($cfg['config']['visuals']['body']) ? ' class="'. $cfg['config']['visuals']['body'] .'"' : '') .'>
									<div style="padding:'. Pmx_getInnerPad($cfg['config']['innerpad'], 1) .'px;">
										<div';
		}
		elseif(!empty($frame) || empty($cfg['config']['visuals']['frame']))
		{
			$frame = true;
			echo ' style="padding:0;' .(empty($options['collapse'. $IDtype]) ? '' : ' display:none;'). '">
									<div'. (!empty($cfg['config']['visuals']['body']) ? ' class="'. $cfg['config']['visuals']['body'] .'"' : '') .'>
									<div style="padding:'. $innerPad[0] .'px '. $innerPad[1] .'px;">
										<div';
		}
		else
		{
			$frame = false;
			echo ' style="padding:'. $innerPad[0] .'px '. $innerPad[1] .'px;' .(empty($options['collapse'. $IDtype]) ? '' : ' display:none;'). '">
									<div'. (!empty($cfg['config']['visuals']['body']) ? ' class="'. $cfg['config']['visuals']['body'] .'"' : '') .'>
										<div';
		}

		// have a bodytext class ?
		if(!empty($cfg['config']['visuals']['bodytext']))
			echo ' class="'. $cfg['config']['visuals']['bodytext'] .'"';

		// have overflow, maxheight?
		if(!empty($cfg['config']['overflow']))
			echo ' style="'. (isset($cfg['config']['maxheight']) && !empty($cfg['config']['maxheight']) ? (empty($cfg['config']['height']) ? 'max-height' : $cfg['config']['height']) .':'. $cfg['config']['maxheight'] .'px; ' : '') .'overflow:'. $cfg['config']['overflow'] .';"';

		echo '>';
	}

	// if header or frame and can collaps?
	if(!empty($cfg['config']['collapse']) && $cfg['config']['visuals']['header'] != 'none')
	{
		$temp = '
		var '. $IDtype .' = new smc_Toggle({
		bToggleEnabled: true,
		bCurrentlyCollapsed: '. (empty($options['collapse'. $IDtype]) ? 'false' : 'true') .',';

		if(in_array($cfg['blocktype'], array('newposts', 'boardnews', 'boardnewsmult', 'promotedposts', 'rss_reader')))
		$temp .= '
		funcOnBeforeExpand: pmxExpandEQH,';

		$temp .= '
		aSwappableContainers: [
			\'upshrink_'. $IDtype .'\'
		],
		aSwapImages: [
			{
				sId: \'upshrink_'. $IDtype .'_Img\',';

		if(!empty($cfg['config']['collapse']) && $context['pmx']['settings']['shrinkimages'] != 2)
			$temp .= '
				srcCollapsed: \''. $context['pmx_img_colapse'] .'\',';

		$temp .= '
				altCollapsed: '. (JavaScriptEscape($txt['pmx_expand'] . $blocktitle)) .',';

		if($cfg['config']['collapse'] == 1 && $context['pmx']['settings']['shrinkimages'] != 2)
			$temp .= '
				srcExpanded: \''. $context['pmx_img_expand']  .'\',';

		$temp .= '
				altExpanded: '. (JavaScriptEscape($txt['pmx_collapse'] . $blocktitle)) .'
			}
		],
		oCookieOptions: {
			bUseCookie: true,
			sCookieName: \''. 'upshr'. $IDtype .'\',
			sCookieValue: \''. (!empty($options['collapse'. $IDtype]) ? $options['collapse'. $IDtype] : 0) .'\'
		}
	});';
	PortaMx_inlineJS($temp, false, false);
	unset($temp);
	}

	return array($spanclass, $isCustFrame, $frame);
}

/**
* Bottom frame
**/
function Pmx_Frame_bottom($cfg, $topdata)
{
	global $context, $options, $txt;

	if(is_null($topdata))
		return;

	list($spanclass, $isCustFrame, $frame) = $topdata;

	echo '
									</div>
								</div>';

	if(empty($context['pmx_style_isCore']))
	{

		if(!empty($cfg['config']['visuals']['frame']) && ($cfg['config']['visuals']['frame'] == 'roundframe' || $isCustFrame))
			echo '
									<span class="'. $spanclass . ($isCustFrame ? $cfg['config']['visuals']['frame'] .'_bot' : 'lowerframe') .'"><span></span></span>';
		elseif(!empty($cfg['config']['visuals']['frame']) && $cfg['config']['visuals']['frame'] == 'round')
			echo '
									<span class="botslice"><span></span></span>';
	}

	echo '
						</div>';

	if(!empty($frame))
		echo '
						</div>';

	echo '
					</div>';
}
?>
