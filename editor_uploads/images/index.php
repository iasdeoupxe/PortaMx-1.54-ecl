<?php
/**
* \file index.php
* Supress direct acceess to the directory.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2015 by PortaMx corp. - http://portamx.com
* \version 1.53
* \date 18.11.2015
*/

if(file_exists(realpath('../../../Settings.php')))
{
	require(realpath('../../../Settings.php'));
	header('Location: ' . $boardurl);
}
else
	exit;
?>