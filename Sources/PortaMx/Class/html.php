<?php
/**
* \file html.php
* Systemblock HTML
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_html
* Systemblock HTML
* @see html.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_html extends PortaMxC_SystemBlock
{
	/**
	* ShowContent
	* Check for PHP inside, prepare the content and save in $this->cfg['content'].
	* Also check for tease the content.
	*/
	function pmxc_ShowContent()
	{
		global $context, $scripturl, $txt;

		$printdir = empty($context['right_to_left']) ? 'ltr' : 'rtl';

		// check for tease
		if(!empty($this->cfg['config']['settings']['teaser']))
		{
			$statID = 'blk'. $this->cfg['id'];
			$tmp = '
				<div id="short_'. $statID .'">'.
				PortaMx_Tease_posts($this->cfg['content'], -1, '<div class="smalltext" style="text-align:'.(empty($context['right_to_left']) ? 'right' : 'left').';"><a id="href_short_'. $statID .'" href="'.$scripturl .'" style="padding: 0 5px;" onclick="ShowHTML(\''. $statID .'\')">'. $txt['pmx_readmore'] .'</a></div>') .'
				</div>';

			if(!empty($context['pmx']['is_teased']))
			{
				$this->cfg['content'] = '
				<div id="full_'. $statID .'" style="display:none;">'. (!empty($this->cfg['config']['settings']['printing']) ? '
					<img class="pmx_printimg" src="'. $context['pmx_imageurl'] .'Print.png" alt="Print" title="'. $txt['pmx_text_printing'] .'" onclick="PmxPrintPage(\''. $printdir .'\', \''. $this->cfg['id'] .'\', \''. htmlspecialchars($this->getUserTitle(), ENT_QUOTES) .'\')" />
					<div id="print'. $this->cfg['id'] .'">'.
						preg_replace('~<div style="page-break-after\:(.*)<\/div>~i', '', $this->cfg['content']) .'
					</div>' : preg_replace('~<div style="page-break-after\:(.*)<\/div>~i', '', $this->cfg['content'])) .'
					<div class="smalltext" style="text-align:'.(empty($context['right_to_left']) ? 'right' : 'left').';">
						<a id="href_full_'. $statID .'" href="'.$scripturl .'" style="padding: 0 5px;;" onclick="ShowHTML(\''. $statID .'\')">'. $txt['pmx_readclose'] .'</a>
					</div>
				</div>'. $tmp;
			}
			unset($tmp);
		}
		else
		{
			if(!empty($this->cfg['config']['settings']['printing']))
			{
				$this->cfg['content'] = '
				<img class="pmx_printimg" src="'. $context['pmx_imageurl'] .'Print.png" alt="Print" title="'. $txt['pmx_text_printing'] .'" onclick="PmxPrintPage(\''. $printdir .'\', \''. $this->cfg['id'] .'\', \''. htmlspecialchars($this->getUserTitle(), ENT_QUOTES) .'\')" />
				<div id="print'. $this->cfg['id'] .'">'.
					preg_replace('~<div style="page-break-after\:(.*)<\/div>~i', '', $this->cfg['content']) .'
				</div>';
			}
		}

		// check for inside php code
		$phpcount = preg_match_all('/(<\?)(php)(.*)\?>/Ums', $this->cfg['content'], $matches, PREG_SET_ORDER);
		if($phpcount != 0)
		{
			// remove duplicate code
			$cnt = $phpcount -1;
			for($i = 0; $i < $cnt; $i++) {
				if($matches[$i][0] == $matches[$i+1][0])
				{
					unset($matches[$i]);
					$cnt--;
				}
			}

			// create find/replace array,
			foreach($matches as $key => $phpevals)
			{
				$phpcode[$key] = '\';' . "\n". trim($phpevals[3]) ."\n". 'echo \'';
				$remove[$key] = $phpevals[0];
				$marker[$key] = '@['. $key .']@';
			}
			$this->cfg['content'] = str_replace($remove, $marker, $this->cfg['content']);

			// remove spaces, cr, lf before and after php code
			$start = 0;
			foreach($marker as $find)
			{
				$end = strpos($this->cfg['content'], $find, $start);
				$this->cfg['content'] = str_replace(substr($this->cfg['content'], $start, $end), trim(substr($this->cfg['content'], $start, $end)), $this->cfg['content']);
				$start = strpos($this->cfg['content'], $find, $start) + strlen($find);
			}

			// escape single quotes for php echo
			$this->cfg['content'] = str_replace("'", "\'", $this->cfg['content']);

			// put a echo arond for php eval and replace the marker with plain php code
			$this->cfg['content'] = "echo '". str_replace($marker, $phpcode, $this->cfg['content']) ."';";

			// cleanup
			unset($matches);
			unset($phpcode);
		}

		if(!empty($this->cfg['config']['settings']['printing']))
			ob_start();

		// Write out the content
		if($phpcount != 0)
			eval($this->cfg['content']);
		else
			echo $this->cfg['content'];

		if(!empty($this->cfg['config']['settings']['printing']))
			echo ob_get_clean();
	}
}
?>
