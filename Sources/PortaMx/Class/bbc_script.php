<?php
/**
* \file bbc_script.php
* Systemblock BBC_SCRIPT
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_bbc_script
* Systemblock BBC_SCRIPT
* @see bbc_script.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_bbc_script extends PortaMxC_SystemBlock
{
	/**
	* ShowContent
	*/
	function pmxc_ShowContent()
	{
		global $context, $txt;

		if(!empty($this->cfg['config']['settings']['printing']))
		{
			$printdir = empty($context['right_to_left']) ? 'ltr' : 'rtl';
			ob_start();

			echo '
			<img class="pmx_printimg" src="'. $context['pmx_imageurl'] .'Print.png" alt="Print" title="'. $txt['pmx_text_printing'] .'" onclick="PmxPrintPage(\''. $printdir .'\', \''. $this->cfg['id'] .'\', \''. htmlspecialchars($this->getUserTitle(), ENT_QUOTES) .'\')" />
			<div id="print'. $this->cfg['id'] .'">';
		}

		// Write out bbc parsed content
		echo '
		'. PortaMx_BBCsmileys(parse_bbc($this->cfg['content'], false));

		if(!empty($this->cfg['config']['settings']['printing']))
		{
			echo '
			</div>';

			echo ob_get_clean();
		}
	}
}
?>
