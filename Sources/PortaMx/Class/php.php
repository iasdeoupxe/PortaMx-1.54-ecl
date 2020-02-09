<?php
/**
* \file php.php
* Systemblock PHP
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_php
* Systemblock PHP
* @see php.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_php extends PortaMxC_SystemBlock
{
	var $php_content;
	var $php_vars;

	/**
	* InitContent.
	* Check we have a init part
	*/
	function pmxc_InitContent()
	{
		if(preg_match('~\[\?pmx_initphp(.*)pmx_initphp\?\]~is', $this->cfg['content'], $match))
		eval($match[1]);

		return $this->visible;
	}

	/**
	* ShowContent
	* Output the content.
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

		// Check we have a show part
		if(preg_match('~\[\?pmx_showphp(.*)pmx_showphp\?\]~is', $this->cfg['content'], $match))
			eval($match[1]);

		// else write out the content
		else
			eval($this->cfg['content']);

		if(!empty($this->cfg['config']['settings']['printing']))
		{
			echo '
			</div>';

			echo ob_get_clean();
		}
	}
}
?>
