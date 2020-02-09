<?php
/**
* \file PortaMxAllocator.php
* The main programm for PortaMx.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* Init all variables and load the settings from the database.
* Check the requests and prepare the templates to load.
*/
function PortaMxAllocator()
{
	global $context, $txt;

	$allocate = array(
		'pmx_center' => array('AdminCenter.php', 'PortaMx_AdminCenter'),
		'pmx_languages' => array('AdminCenter.php', 'PortaMx_ShowLanguages'),
		'pmx_settings' => array('AdminSettings.php', 'PortaMx_AdminSettings'),
		'pmx_blocks' => array('AdminBlocks.php', 'PortaMx_AdminBlocks'),
		'pmx_articles' => array('AdminArticles.php', 'PortaMx_AdminArticles'),
		'pmx_categories' => array('AdminCategories.php', 'PortaMx_AdminCategories'),
		'pmx_sefengine' => array('AdminSettings.php', 'PortaMx_AdminSettings'),
	);

	// load admin language and javascript
	if(isset($_GET['area']) && in_array($_GET['area'], explode(',', $context['pmx']['areas'])))
	{
		if(allowPmx('pmx_admin, pmx_blocks, pmx_articles, pmx_create'))
		{
			$_GET[$context['session_var']] = $context['session_id'];
			require_once($context['pmx_sourcedir'] . $allocate[$_GET['area']][0]);
			$allocate[$_GET['area']][1]();
		}
		else
			fatal_error($txt['pmx_acces_error']);
	}
	else
		fatal_error($txt['pmx_acces_error']);
}
?>
