<?php
/**
* \file XmlPmx.template.php
* XML Respose template.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

function template_main()
{
	global $context;

	echo '<', '?xml version="1.0" encoding="', $context['character_set'], '"?', '><pmx><![CDATA['. trim($context['xmlpmx']) .']]></pmx>';
}
?>
