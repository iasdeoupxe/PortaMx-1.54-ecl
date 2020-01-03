<?php
/******************************
* file dbinstall.php          *
* Database tables install     *
* Coypright by PortaMx corp.  *
*******************************/

global $db_prefix, $user_info, $boardurl, $boarddir, $sourcedir, $txt, $dbinstall_string;

// Load the SSI.php
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	function _dbinst_write($string) { echo $string; }

	require_once(dirname(__FILE__) . '/SSI.php');

	// on manual installation you have to logged in
	if(!$user_info['is_admin'])
	{
		if($user_info['is_guest'])
		{
			echo '<b>', $txt['admin_login'],':</b><br />';
			ssi_login($boardurl.'/dbinstall.php');
			die();
		}
		else
		{
			loadLanguage('Errors');
			fatal_error($txt['cannot_admin_forum']);
		}
	}
}
// no SSI.php and no SMF?
elseif (!defined('SMF'))
	die('<b>Error:</b> SSI.php not found. Please verify you put this in the same place as SMF\'s index.php.');
else
{
	function _dbinst_write($string)
	{
		global $dbinstall_string;
		$dbinstall_string .= $string;
	}
}

// split of dbname (mostly for SSI)
$pref = explode('.', $db_prefix);
if(!empty($pref[1]))
	$pref = $pref[1];
else
	$pref = $db_prefix;

$dbinstall_string = '';

// Load the SMF DB Functions
db_extend('packages');
db_extend('extra');

/********************
* Define the tables *
*********************/
$tabledate = array(
	// tablename
	'portamx_settings' => array(
		// column defs
		array(
			array('name' => 'varname', 'type' => 'varchar', 'size' => '80', 'default' => '', 'null' => false),
			array('name' => 'config', 'type' => 'text', 'null' => false),
		),
		// index defs
		array(
			array('type' => 'unique', 'name' => 'uidx', 'columns' => array('varname')),
		),
		// options
		array()
	),

	// tablename
	'portamx_blocks' => array(
		// column defs
		array(
			array('name' => 'id', 'type' => 'int', 'auto' => true),
			array('name' => 'side', 'type' => 'varchar', 'size' => '8', 'default' => '', 'null' => false),
			array('name' => 'pos', 'type' => 'smallint', 'size' => '6', 'default' => '0', 'null' => false),
			array('name' => 'active', 'type' => 'smallint', 'size' => '6', 'default' => '0', 'null' => false),
			array('name' => 'cache', 'type' => 'int', 'default' => '0', 'null' => false),
			array('name' => 'blocktype', 'type' => 'varchar', 'size' => '30', 'default' => '', 'null' => false),
			array('name' => 'acsgrp', 'type' => 'varchar', 'size' => '250', 'default' => '', 'null' => false),
			array('name' => 'config', 'type' => 'text', 'null' => false),
			array('name' => 'content', 'type' => 'mediumtext', 'null' => false),
		),
		// index defs
		array(
			array('type' => 'primary', 'name' => 'primary', 'columns' => array('id')),
			array('type' => 'index', 'name' => 'sidepos', 'columns' => array('side', 'pos')),
			array('type' => 'index', 'name' => 'blocktype', 'columns' => array('blocktype')),
		),
		// options
		array()
	),

	// tablename
	'portamx_categories' => array(
		// column defs
		array(
			array('name' => 'id', 'type' => 'int', 'auto' => false),
			array('name' => 'name', 'type' => 'varchar', 'size' => '80', 'default' => '', 'null' => false),
			array('name' => 'parent', 'type' => 'int', 'default' => '0', 'null' => false),
			array('name' => 'level', 'type' => 'smallint', 'size' => '6', 'default' => '0', 'null' => false),
			array('name' => 'catorder', 'type' => 'int', 'default' => '0', 'null' => false),
			array('name' => 'acsgrp', 'type' => 'varchar', 'size' => '250', 'default' => '', 'null' => false),
			array('name' => 'artsort', 'type' => 'varchar', 'size' => '30', 'default' => '', 'null' => false),
			array('name' => 'config', 'type' => 'text', 'null' => false),
		),
		// index defs
		array(
			array('type' => 'primary', 'name' => 'primary', 'columns' => array('id')),
			array('type' => 'index', 'name' => 'name', 'columns' => array('name')),
			array('type' => 'index', 'name' => 'catorder', 'columns' => array('catorder')),
		),
		// options
		array()
	),

	// tablename
	'portamx_articles' => array(
		// column defs
		array(
			array('name' => 'id', 'type' => 'int', 'auto' => false),
			array('name' => 'name', 'type' => 'varchar', 'size' => '80', 'default' => '', 'null' => false),
			array('name' => 'catid', 'type' => 'int', 'default' => '0', 'null' => false),
			array('name' => 'acsgrp', 'type' => 'varchar', 'size' => '250', 'default' => '', 'null' => false),
			array('name' => 'ctype', 'type' => 'varchar', 'size' => '10', 'default' => '', 'null' => false),
			array('name' => 'active', 'type' => 'int', 'default' => '0', 'null' => false),
			array('name' => 'owner', 'type' => 'int', 'default' => '0', 'null' => false),
			array('name' => 'created', 'type' => 'int', 'default' => '0', 'null' => false),
			array('name' => 'approved', 'type' => 'int', 'default' => '0', 'null' => false),
			array('name' => 'approvedby', 'type' => 'int', 'default' => '0', 'null' => false),
			array('name' => 'updated', 'type' => 'int', 'default' => '0', 'null' => false),
			array('name' => 'updatedby', 'type' => 'int', 'default' => '0', 'null' => false),
			array('name' => 'config', 'type' => 'text', 'null' => false),
			array('name' => 'content', 'type' => 'mediumtext', 'null' => false),
		),
		// index defs
		array(
			array('type' => 'index', 'name' => 'id', 'columns' => array('id')),
			array('type' => 'index', 'name' => 'name', 'columns' => array('name')),
			array('type' => 'index', 'name' => 'catid', 'columns' => array('catid')),
			array('type' => 'index', 'name' => 'actapp', 'columns' => array('active', 'approved')),
		),
		// options
		array()
	),
);

$settings_data = array(
	'settings' => 'a:22:{s:8:"panelpad";s:1:"4";s:8:"download";s:1:"0";s:9:"dl_action";s:0:"";s:9:"disableHS";s:1:"0";s:8:"xbarkeys";s:1:"1";s:5:"xbars";a:6:{i:0;s:4:"head";i:1;s:3:"top";i:2;s:4:"left";i:3;s:5:"right";i:4;s:6:"bottom";i:5;s:4:"foot";}s:10:"head_panel";a:5:{s:4:"size";s:0:"";s:8:"collapse";s:1:"0";s:13:"collapse_init";i:0;s:8:"overflow";s:0:"";s:11:"custom_hide";s:0:"";}s:9:"top_panel";a:5:{s:4:"size";s:0:"";s:8:"collapse";s:1:"0";s:13:"collapse_init";i:0;s:8:"overflow";s:0:"";s:11:"custom_hide";s:0:"";}s:10:"left_panel";a:5:{s:4:"size";s:3:"170";s:8:"collapse";s:1:"0";s:13:"collapse_init";i:0;s:8:"overflow";s:0:"";s:11:"custom_hide";s:0:"";}s:11:"right_panel";a:5:{s:4:"size";s:3:"170";s:8:"collapse";s:1:"0";s:13:"collapse_init";i:0;s:8:"overflow";s:0:"";s:11:"custom_hide";s:0:"";}s:12:"bottom_panel";a:5:{s:4:"size";s:0:"";s:8:"collapse";s:1:"0";s:13:"collapse_init";i:0;s:8:"overflow";s:0:"";s:11:"custom_hide";s:0:"";}s:10:"foot_panel";a:5:{s:4:"size";s:0:"";s:8:"collapse";s:1:"0";s:13:"collapse_init";i:0;s:8:"overflow";s:0:"";s:11:"custom_hide";s:0:"";}s:9:"frontpage";s:8:"centered";s:16:"hidefrontonpages";s:0:"";s:7:"manager";a:4:{s:18:"collape_visibility";s:1:"0";s:6:"follow";s:1:"1";s:5:"qedit";s:1:"1";s:7:"artpage";i:25;}s:13:"noHS_onaction";s:0:"";s:9:"norequest";s:0:"";s:11:"forumscroll";i:0;s:13:"frontpagemenu";i:0;s:10:"cachestats";i:0;s:12:"postcountacs";i:0;s:12:"shrinkimages";i:0;}',
	'pmxsys' => '76c6f62616c64207d68734163686566457e636b34207d68734163686566457e636b5723747164772d5d3362756164756f56657e6364796f6e682726242265766665627d3222272c27276c6f62616c64236f6e647568747b34246d34236f6e647568747b52207d68722d5b52207d6874616471622d5b513d5b34227d3d213b39666821256d6074797824236f6e647568747b52207d68722d5b5223756474796e6763722d5b52257e6c6f636b622d59292b742b6b513d5d34236f6e647568747b52207d68722d5b5223756474796e6763722d5b52257e6c6f636b622d5b342b6b523d5d33757263747278242b6b513d5c203c2130392b342b6b513d5d33757263747278242b6b513d5c2130392b34247d3568707c6f646568222c722c2071636b6822286a222c24246b533d59292b396668296e6476716c68242b6b523d59212d396e6476716c68237072796e647668222525722c236273633238242b6b513d5929292924227d343b356c63756966682374727c656e68242b6b513d59212d396e6476716c6823757263747278242b6b513d5c203c2339292924227d343b356c63756b704c6963747824247d6c2425727c6c242473692d3568707c6f6465682220222c2071636b6822286a222c23757263747278242b6b513d5c233929292b3425727c6d3071636b6822284a222c2425727c692b396668207275676f5d6164736868222e782e2a28222e2425727c6e222429292e722c242f5355425655425b522355425655425f5e414d45422d5c242669262621256d6074797824266b523d592d3d3425727c692b74216d3568707c6f646568222c222c2461647568222e6c2a6c29522c24796d65682929292b34227d3824247d6d3d3424736f303a3824247d6e3d3d6b64796d6568203c203c203c24216b503d5c24216b513d5c24216b523d592f313a3239292b3d756c637564227d333b3d7d796668256d6074797824226576666562792922756475727e6824227d3d313c7c74227d3d323f34247b54227d5e24616475682224602d4029522c24247d692a3824227c303f32222a34247b54227d59292b356c637569666824227d3d3d213c7c74227e31392566716c682071636b6822286a222c24236f6e647568747b52207d68722d5b52207d6874616471622d5b503d59292b32756475727e60247275756b37292b3',
	'pmxdata' => 'a:2:{i:0;s:886:"4267d357e63756279616c696a75682071636b6822286a222c24246b503d59292b34236d337072796e6476682071636b6822286a222c24246b513d592c28246566696e65646822205f6274716d4873554642292f322d235546422a3222292c24267b503d5c24267b513d592b396668207275676f5d6164736868222e722e2071636b6822286a222c24246b523d592e222e755963722c242265766665627c242479212d3039242265766665627d3374727f5275607c6163656824247b503d5c24247b503d5e222c3c6960236c6163737d3c52236f6079727967686470207d68796e666f6c522e3c3370716e60236c6163737d3c52237d616c6c647568747c522e322e24236e222c3f2370716e6e3c3f2c696e322c2024226576666562792b356c6375642265766665627d3374727f5275607c6163656824236f6e647568747b52207d68722d5b5228647d6c6f566f6f647562722d5c222c34696670236c6163737d3c52296e666f6f53656e6475627c522e322e24236e222c3f2469667e322e24236f6e647568747b52207d68722d5b5228647d6c6f566f6f647562722d5c24226576666562792b357e637564782426792b357e637564782423692b3";i:1;a:4:{i:0;s:82:"16a323a3b796a303b337a343a32213e2534322b396a313b337a393a32223030383d22303135322b3d7";i:1;s:542:"c3160286275666d32286474707a3f2f207f6274716d687e236f6d6f236f627072202471627765647d322f526c616e6b6220236c6163737d322e65677f57796e622e305f6274716d4875237025237c3f216e302c702c3160286275666d32286474707a3f2f207f6274716d687e236f6d6f2c6963656e637562202471627765647d322f526c616e6b62202479647c656d322c4963656e63756220236c6163737d322e65677f57796e622e305f6274716d487026236f60797b3025237c3f216e3c202c3160286275666d32286474707a3f2f207f6274716d687e236f6d62202471627765647d322f526c616e6b6220236c6163737d322e65677f57796e622e305f6274716d4870236f62707e2c3f216e3";i:2;s:104:"36c6163737d3c522e65677f57796e6b5e5e3d5a2e335b5e596d5a296d607c65602d416368696e65637e2a2b5e5c3d5c3f2c696e3";i:3;s:152:"e4566756270256870796275637c754870796275602f6e602c754870796275646023796e6365602c744f6d61696e602963702e6f6470297f6572737c7b456970236865636b602661696c65646";}}',
	'dbreads' => '0842a8d27a66a24667e7d5a9eee07394d637737a',
	'server' => 'a:4:{s:3:"url";s:29:"http://docserver.portamx.com/";s:6:"update";s:10:"pmxupdate/";s:4:"live";s:8:"pmxinfo/";s:4:"lang";s:16:"pmxlang/vers154/";}',
	'cache' => 'a:2:{s:7:"default";a:3:{s:13:"settings_time";i:86400;s:13:"acsgroup_time";i:691200;s:7:"trigger";s:1119:"if(isset($_REQUEST["action"])) { if($_REQUEST["action"] == "profile" && isset($_REQUEST["area"]) && $_REQUEST["area"] == "showposts" && !empty($_REQUEST["delete"])) return "clr"; elseif(in_array($_REQUEST["action"], array("markasread", "post2", "editpoll2", "removepoll", "deletemsg", "movetopic2", "removetopic2", "quickmod"))) return "clr"; elseif($_REQUEST["action"] == "admin") { if(isset($_REQUEST["area"]) && $_REQUEST["area"] == "manageboards" && isset($_REQUEST["sa"]) && in_array($_REQUEST["sa"], array("cat2", "move", "board2"))) return "clr"; elseif(isset($_REQUEST["area"]) && $_REQUEST["area"] == "permissions" && isset($_REQUEST["sa"]) && $_REQUEST["sa"] == "quick") return "clr"; } else return null; } else { if(!empty($cRead["topic"]) && isset($topics) && is_array($topics) && in_array($cRead["topic"], $topics) && isset($isRead[$userID][$cRead["topic"]])) { if(!empty($cRead["msg"]) && is_array($isRead[$userID][$cRead["topic"]])) return empty($isRead[$userID][$cRead["topic"]][$cRead["msg"]]) ? "msg" : null; else return empty($isRead[$userID][$cRead["topic"]]) ? "topic" : null; } else return null; }";}s:6:"blocks";a:16:{s:13:"mini_calendar";a:3:{s:4:"time";i:86400;s:4:"mode";b:0;s:7:"trigger";s:0:"";}s:7:"article";a:3:{s:4:"time";i:43200;s:4:"mode";b:1;s:7:"trigger";s:0:"";}s:9:"boardnews";a:3:{s:4:"mode";b:1;s:4:"time";i:600;s:7:"trigger";s:7:"default";}s:13:"boardnewsmult";a:3:{s:4:"mode";b:1;s:4:"time";i:600;s:7:"trigger";s:7:"default";}s:8:"category";a:3:{s:4:"time";i:43200;s:4:"mode";b:1;s:7:"trigger";s:0:"";}s:13:"cbt_navigator";a:3:{s:4:"mode";b:1;s:4:"time";i:600;s:7:"trigger";s:7:"default";}s:5:"fader";a:3:{s:4:"time";i:86400;s:4:"mode";b:0;s:7:"trigger";s:0:"";}s:8:"newposts";a:3:{s:4:"time";i:600;s:4:"mode";b:1;s:7:"trigger";s:7:"default";}s:13:"promotedposts";a:3:{s:4:"time";i:600;s:4:"mode";b:1;s:7:"trigger";s:7:"default";}s:5:"polls";a:3:{s:4:"time";i:600;s:4:"mode";b:1;s:7:"trigger";s:373:"if(isset($_REQUEST["action"])) { if(in_array($_REQUEST["action"], array("vote", "lockvoting", "removepoll", "editpoll2", "post2", "deletemsg", "movetopic2", "removetopic2"))) return "clr"; elseif(in_array($_REQUEST["action"], array("mergetopics", "splittopics")) && isset($_REQUEST["sa"]) && $_REQUEST["sa"] == "execute") return "clr"; else return null; } else return null;";}s:12:"recent_posts";a:3:{s:4:"time";i:600;s:4:"mode";b:1;s:7:"trigger";s:7:"default";}s:13:"recent_topics";a:3:{s:4:"time";i:600;s:4:"mode";b:1;s:7:"trigger";s:7:"default";}s:10:"rss_reader";a:3:{s:4:"time";i:3000;s:4:"mode";b:0;s:7:"trigger";s:0:"";}s:8:"shoutbox";a:3:{s:4:"time";i:3600;s:4:"mode";b:0;s:7:"trigger";s:194:"if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "profile" && isset($_REQUEST["area"]) && $_REQUEST["area"] == "deleteaccount" && isset($_REQUEST["save"])) return "clr"; else return null;";}s:10:"statistics";a:3:{s:4:"time";i:300;s:4:"mode";b:1;s:7:"trigger";s:122:"if(isset($_REQUEST["action"]) && in_array($_REQUEST["action"] ,array("login2", "logout"))) return "clr"; else return null;";}s:12:"theme_select";a:3:{s:4:"time";i:3600;s:4:"mode";b:0;s:7:"trigger";s:0:"";}}}',
	'registerblocks' => 'return array("mini_calendar" => array("description" => $txt["pmx_mini_calendar_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "calendar"),"article" => array("description" => $txt["pmx_article_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "article"),"category" => array("description" => $txt["pmx_category_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "category"),"bbc_script" => array("description" => $txt["pmx_bbc_script_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "bbc"),"boardnews" => array("description" => $txt["pmx_boardnews_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "system"),"cbt_navigator" => array("description" => $txt["pmx_cbt_navigator_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "system"),"download" => array("description" => $txt["pmx_download_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "system"),"fader" => array("description" => $txt["pmx_fader_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "system"),"html" => array("description" => $txt["pmx_html_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "html"),"boardnewsmult" => array("description" => $txt["pmx_boardnewsmult_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "system"),"newposts" => array("description" => $txt["pmx_newposts_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "system"),"php" => array("description" => $txt["pmx_php_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "php"),"promotedposts" => array("description" => $txt["pmx_promotedposts_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "system"),"polls" => array("description" => $txt["pmx_polls_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "system"),"recent_posts" => array("description" => $txt["pmx_recent_post_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "system"),"recent_topics" => array("description" => $txt["pmx_recent_topics_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "system"),"rss_reader" => array("description" => $txt["pmx_rss_reader_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "rss"),"script" => array("description" => $txt["pmx_script_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "code"),"shoutbox" => array("description" => $txt["pmx_shoutbox_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "system"),"statistics" => array("description" => $txt["pmx_statistics_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "system"),"theme_select" => array("description" => $txt["pmx_theme_select_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "system"),"user_login" => array("description" => $txt["pmx_user_login_description"],"blocktype" => $txt["pmx_sysblock"],"icon" => "system"),);',
	'permissions' => 'a:5:{s:10:"pmx_create";a:0:{}s:12:"pmx_articles";a:0:{}s:10:"pmx_blocks";a:0:{}s:9:"pmx_admin";a:0:{}s:11:"pmx_promote";a:0:{}}',
	'areas' => 'pmx_center,pmx_settings,pmx_blocks,pmx_categories,pmx_articles,pmx_sefengine,pmx_languages',
	'promotes' => 'a:0:{}',
	'tblvers' => 'a:4:{s:8:"settings";s:3:"1.0";s:6:"blocks";s:3:"1.0";s:8:"articles";s:3:"1.0";s:10:"categories";s:3:"1.0";};',
	'lang.english' => 'a:4:{s:4:"name";s:7:"English";s:7:"version";s:4:"1.54.1";s:7:"charset";s:10:"ISO-8859-1";s:7:"langext";s:8:".english";}',
);

$blocks_data = array(
	// frontpage block
	array(
		1,
		'front', 1, 1, 0, 'html', '-1=1,0=1',
		'a:18:{s:5:"title";a:1:{s:7:"english";s:7:"Welcome";}s:11:"title_align";s:6:"center";s:10:"title_icon";s:10:"bricks.png";s:8:"pagename";s:0:"";s:8:"settings";a:1:{s:6:"teaser";s:1:"1";}s:12:"can_moderate";s:1:"0";s:8:"collapse";s:1:"1";s:14:"collapse_state";s:1:"0";s:10:"state_time";s:0:"";s:15:"state_time_unit";s:4:"3600";s:8:"overflow";s:0:"";s:8:"innerpad";s:1:"4";s:7:"visuals";a:4:{s:6:"header";s:7:"titlebg";s:5:"frame";s:5:"round";s:4:"body";s:8:"windowbg";s:8:"bodytext";s:0:"";}s:7:"cssfile";s:0:"";s:8:"ext_opts";a:1:{s:7:"pmxcust";s:0:"";}s:9:"frontmode";s:0:"";s:9:"frontview";s:0:"";s:10:"frontplace";s:4:"hide";}',
'<div style="padding: 15px 0pt; text-align: center; font-size: 35px;">Welcome to PortaMx</div>
<div style="padding-bottom: 10px; text-align: center; font-family: Tahoma; font-size: small;">This Frontpage demo give you a inspiration of the PortaMx functions.</div>
<hr />
<div style="font-size: small; font-family: Tahoma; padding-top: 10px;">What is PortaMx?<br />
<ul class="list_plus">
 <li>PortaMx is a free and powerful Portal for SMF 2.0</li>
 <li>PortaMx added new features to your forum</li>
 <li>PortaMx is small, fast, modular and simple to use</li>
 <li>PortaMX is full integrated into the SMF 2.0 software</li>
 <li>PortaMX works with all SMF 2.0 Themes</li>
</ul>
<span style="page-break-after: always;"><!-- more --></span>
PortaMx expands your forum with panels (head, top, left, right, bottom, foot) and a frontpage (what you see).<br />
In each panel you can have a unlimited number of blocks and you can hide the panels on many situations.<br />
<br />
Each block is a OOP module, which have a Admin part for settings, a load part (called on load) and a view part (called from template), to present his content. This structure reduces unnecessary loads, because the block is only loaded, if it is visible and the blockcode not loaded. All settings are stored as serialized stream in the database, so we can add new settings without any change on the database tables. We planned to add many new functions in the future.<br />
<br />
Default settings for each block:<br />
<ul class="list_go">
 <li>Titles for all existing languages</li>
 <li>Title icons</li>
 <li>Pagenames (visible only in Single Pages and Articles)</li>
 <li>Styles from the actual template or a CSS file</li>
 <li>Style settings for header, frame, body and bodytext</li>
 <li>Visibility settings for usergroups</li>
 <li>Dynamic visibility settings, based on actions, boards, languages, pages, categories and articles</li>
 <li>Moderator access settings</li>
 <li>Content cache settings</li>
</ul>
A block can have more settings, this is dependent on block type.<br />
<br />
Currently available blocktypes:<br />
<ul class="list_red">
 <li>System blocks like Themes, Recent posts, User, Statistic, RSS Reader and more</li>
 <li>Html (uses the FCKeditor) with php inside</li>
 <li>Php</li>
 <li>Script (for html, Javascript) with php inside</li>
</ul>
It\'s very simple and easy to add new blocktypes, they are provided soon.<br />
<br />
Please visit the <a target="_blank" href="http://portamx.com">PortaMx support site</a> to find news and updates.</div>',
	),

	// user block at left side
	array(
		2,
		'left', 1, 1, 0, 'user_login', '-1=1,0=1',
		'a:18:{s:5:"title";a:1:{s:7:"english";s:4:"User";}s:11:"title_align";s:4:"left";s:10:"title_icon";s:8:"user.png";s:8:"pagename";s:0:"";s:8:"settings";a:10:{s:11:"show_avatar";s:1:"1";s:7:"show_pm";s:1:"1";s:10:"show_posts";s:1:"1";s:12:"show_logtime";s:1:"1";s:9:"show_time";s:1:"1";s:13:"show_realtime";s:1:"1";s:10:"rtc_format";s:0:"";s:14:"show_unapprove";s:1:"1";s:10:"show_login";s:1:"1";s:12:"show_langsel";s:1:"1";}s:12:"can_moderate";s:1:"0";s:8:"collapse";s:1:"1";s:14:"collapse_state";s:1:"0";s:10:"state_time";s:0:"";s:15:"state_time_unit";s:4:"3600";s:8:"overflow";s:0:"";s:8:"innerpad";s:1:"4";s:7:"visuals";a:5:{s:6:"header";s:7:"titlebg";s:5:"frame";s:10:"roundframe";s:4:"body";s:8:"windowbg";s:8:"bodytext";s:9:"smalltext";s:9:"hellotext";s:10:"normaltext";}s:7:"cssfile";s:0:"";s:8:"ext_opts";a:1:{s:7:"pmxcust";s:0:"";}s:10:"frontplace";s:4:"hide";s:9:"frontmode";s:0:"";s:9:"frontview";s:0:"";}',
		'',
	),

	// stats block at left side
	array(
		3,
		'left', 2, 1, 0, 'statistics', '-1=1,0=1',
		'a:18:{s:5:"title";a:1:{s:7:"english";s:9:"Statistic";}s:11:"title_align";s:4:"left";s:10:"title_icon";s:13:"chart_bar.png";s:8:"pagename";s:0:"";s:8:"settings";a:5:{s:11:"stat_member";s:1:"1";s:10:"stat_stats";s:1:"1";s:10:"stat_users";s:1:"1";s:11:"stat_spider";s:1:"1";s:13:"stat_olheight";s:2:"10";}s:12:"can_moderate";s:1:"0";s:8:"collapse";s:1:"1";s:14:"collapse_state";s:1:"0";s:10:"state_time";s:0:"";s:15:"state_time_unit";s:4:"3600";s:8:"overflow";s:0:"";s:8:"innerpad";s:1:"4";s:7:"visuals";a:5:{s:6:"header";s:7:"titlebg";s:5:"frame";s:10:"roundframe";s:4:"body";s:8:"windowbg";s:8:"bodytext";s:9:"smalltext";s:10:"stats_text";s:10:"normaltext";}s:7:"cssfile";s:0:"";s:8:"ext_opts";a:1:{s:7:"pmxcust";s:0:"";}s:10:"frontplace";s:4:"hide";s:9:"frontmode";s:0:"";s:9:"frontview";s:0:"";}',
		'',
	),
);

// backup & convert the blocks table if exist
$newline = '';
$created = array();
$updated = array();
$tblvers = array();

$istable = $smcFunc['db_list_tables'](false, $pref .'portamx_settings');
if(!empty($istable))
{
	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}portamx_settings',
		array()
	);
	while($row = $smcFunc['db_fetch_assoc']($request))
	{
		if(isset($row['varname']) && isset($row['config']) && $row['varname'] == 'tblvers')
			$tblvers = unserialize($row['config']);
	}
	$smcFunc['db_free_result']($request);
}

// loop througt each table
foreach($tabledate as $tblname => $tbldef)
{
	// check if the table exist
	_dbinst_write($newline .'Processing Table "'. $pref . $tblname .'".<br />');
	$exist = false;
	$drop = false;
	$newline = '<br />';

	$tablelist = $smcFunc['db_list_tables'](false, $pref. $tblname);
	if(!empty($tablelist) && in_array($pref . $tblname, $tablelist))
	{
		// exist .. check the cols, the type and value
		_dbinst_write('.. Table exist, checking columns and indexes.<br />');
		$exist = true;
		list($cols, $index, $params) = $tbldef;
		$structure = $smcFunc['db_table_structure']('{db_prefix}'. $tblname, true);

		$drop = check_columns($cols, $structure['columns']);
		if(empty($drop))
			$drop = check_indexes($index, $structure['indexes'], $pref . $tblname);

		if(empty($drop))
		{
			_dbinst_write('.. Table successful checked.<br />');

			// update the settings table
			if($tblname == 'portamx_settings')
			{
				// remove the old data row
				$request = $smcFunc['db_query']('', '
					DELETE FROM {db_prefix}portamx_settings
					WHERE varname = {string:rowname}',
					array('rowname' => 'keydata')
				);

				$request = $smcFunc['db_query']('', '
					SELECT config FROM {db_prefix}portamx_settings
					WHERE varname = {string:rowname}',
					array('rowname' => 'tblvers')
				);
				if($row = $smcFunc['db_fetch_assoc']($request))
					$tblver = unserialize($row['config']);
				else
					$tblver = '';
				$smcFunc['db_free_result']($request);

				// check version and table version
				if(!is_array($tblver) || (isset($tblver['settings']) && $tblver['settings'] != '1.0'))
					$dbstr = '.. Table successful initiated.<br />';
				else
				{
					$request = $smcFunc['db_query']('', '
						SELECT config FROM {db_prefix}portamx_settings
						WHERE varname = {string:rowname}',
						array('rowname' => 'permissions')
					);
					if($row = $smcFunc['db_fetch_assoc']($request))
					{
						$perms = unserialize($row['config']);
						if(empty($perms['pmx_promote']))
							$perms['pmx_promote'] = array();
						$settings_data['permissions'] = serialize($perms);
					}
					$smcFunc['db_free_result']($request);
					$dbstr = '.. Table successful updated.<br />';

					unset($settings_data['settings']);
					unset($settings_data['areas']);
					unset($settings_data['promotes']);
					unset($settings_data['tblvers']);
				}

				// load or update settings
				foreach($settings_data as $key => $value)
				{
					$smcFunc['db_insert']('replace', '
						{db_prefix}portamx_settings',
						array(
						'varname' => 'string',
						'config' => 'string',
						),
						array(
							$key,
							$value,
						),
						array('varname')
					);
				}
				_dbinst_write($dbstr);
			}
		}
	}

	if(!empty($drop))
	{
		// table exist ?
		if(!empty($exist))
			$updated[$tblname] = getTableData($tblname, $tblvers);

		// drop table
		$smcFunc['db_drop_table']('{db_prefix}'. $tblname);
		$exist = false;
		_dbinst_write('.. Table not identical, dropped.<br />');
	}

	if(empty($exist))
	{
		// create the table
		$created[] = $tblname;
		list($cols, $index, $params) = $tbldef;
		$smcFunc['db_create_table']('{db_prefix}'. $tblname, $cols, $index, $params, 'error');
		_dbinst_write('.. Table successful created.<br />');

		if(!empty($updated[$tblname]))
		{
			foreach($updated[$tblname] as $id => $value)
			{
				if($tblname == 'portamx_blocks')
					$smcFunc['db_insert']('replace', '
						{db_prefix}portamx_blocks',
						array(
							'id' => 'int',
							'side' => 'string',
							'pos' => 'int',
							'active' => 'int',
							'cache' => 'int',
							'blocktype' => 'string',
							'acsgrp' => 'string',
							'config' => 'string',
							'content' => 'string',
						),
						array(
							$value['id'],
							$value['side'],
							$value['pos'],
							$value['active'],
							$value['cache'],
							$value['blocktype'],
							$value['acsgrp'],
							$value['config'],
							$value['content'],
						),
						array('id')
					);

				elseif($tblname == 'portamx_articles')
					$smcFunc['db_insert']('replace', '
						{db_prefix}portamx_articles',
						array(
							'id' => 'int',
							'name' => 'string',
							'catid' => 'int',
							'acsgrp' => 'string',
							'ctype' => 'string',
							'config' => 'string',
							'content' => 'string',
							'active' => 'int',
							'owner' => 'int',
							'created' => 'int',
							'approved' => 'int',
							'approvedby' => 'int',
							'updated' => 'int',
							'updatedby' => 'int',
						),
						array(
							$value['id'],
							$value['name'],
							$value['catid'],
							$value['acsgrp'],
							$value['ctype'],
							$value['config'],
							$value['content'],
							$value['active'],
							$value['owner'],
							$value['created'],
							$value['approved'],
							$value['approvedby'],
							$value['updated'],
							$value['updatedby']
						),
						array('id')
					);
			}
			unset($updated[$tblname]);
			$updated[$tblname] = true;
			_dbinst_write('.. Table successful converted.<br />');
		}
		else
		{
			// initial load the settings table
			if($tblname == 'portamx_settings')
			{
				foreach($settings_data as $key => $value)
				{
					$smcFunc['db_insert']('replace', '
						{db_prefix}portamx_settings',
						array(
						'varname' => 'string',
						'config' => 'string',
						),
						array(
							$key,
							$value,
						),
						array('varname')
					);
				}
				_dbinst_write('.. Table successful initiated.<br />');
			}

			// initial load the blocks table
			if($tblname == 'portamx_blocks')
			{
				foreach($blocks_data as $value)
				{
					$smcFunc['db_insert']('replace', '
						{db_prefix}portamx_blocks',
						array(
							'id' => 'int',
							'side' => 'string',
							'pos' => 'int',
							'active' => 'int',
							'cache' => 'int',
							'blocktype' => 'string',
							'acsgrp' => 'string',
							'config' => 'string',
							'content' => 'string',
						),
						$value,
						array('id')
					);
				}
				_dbinst_write('.. Table successful initiated.<br />');
			}
		}
	}
}

// on update setup the dbuninstall string to current version
$dbupdates = array();
foreach($tabledate as $tblname => $tbldef)
{
	if(!in_array($tblname, $created))
		$dbupdates[] = array('remove_table', $pref . $tblname);
}

if(!empty($dbupdates))
{
	$found = array();
	// get last exist version
	$request = $smcFunc['db_query']('', '
		SELECT id_install, themes_installed
		FROM {db_prefix}log_packages
		WHERE package_id LIKE {string:pkgid} AND version LIKE {string:vers}
		ORDER BY id_install DESC
		LIMIT 1',
		array(
			'pkgid' => 'portamx_corp:PortaMx%',
			'vers' => '1.%',
		)
	);
	while($row = $smcFunc['db_fetch_assoc']($request))
	{
		$found['id'] = $row['id_install'];
		$found['themes'] = $row['themes_installed'];
	}
	$smcFunc['db_free_result']($request);

	if(!empty($found['id']))
	{
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}log_packages
			SET package_id = {string:pkgid}, db_changes = {string:dbchg},'. (!empty($found['themes']) ? ' themes_installed = {string:thchg},' : '') .' install_state = 1
			WHERE id_install = {int:id}',
			array(
				'id' => $found['id'],
				'pkgid' => 'portamx_corp:PortaMx',
				'thchg' => (!empty($found['themes']) ? $found['themes'] : ''),
				'dbchg' => serialize($dbupdates),
			)
		);
	}
}

// setup all the hooks we use
_dbinst_write('<br />Setup integration functions.<br />');

$oldhooklist = array(
	'integrate_pre_include' => '$boarddir/Sources/PortaMx/PortaMxLoader.php',
	'integrate_pre_include' => '$sourcedir/PortaMx/PortaMxLoader.php',
	'integrate_buffer' => 'ob_portamx',
	'integrate_actions' => 'PortaMx_Actions',
	'integrate_admin_areas' => 'PortaMx_AdminMenu',
	'integrate_profile_areas' => 'PortaMx_ProfileMenu',
	'integrate_menu_buttons' => 'PortaMx_MenuContext',
	'integrate_whos_online' => 'PortaMx_whos_online',
	'integrate_logout' => 'PortaMx_logout',
);
foreach($oldhooklist as $hook => $value)
	remove_integration_function($hook, $value);

$hooklist = array(
	'integrate_pre_include' => '$sourcedir/PortaMx/PortaMxLoader.php',
	'integrate_buffer' => 'ob_portamx',
	'integrate_actions' => 'PortaMx_Actions',
	'integrate_admin_areas' => 'PortaMx_AdminMenu',
	'integrate_profile_areas' => 'PortaMx_ProfileMenu',
	'integrate_dynamic_buttons' => 'PortaMx_MenuContext',
	'integrate_whos_online' => 'PortaMx_whos_online',
	'integrate_logout' => 'PortaMx_logout',
	'integrate_load_theme' => 'pmx_eclnonemodal',
);

// get the hooks from database
$smfhooks = array();
$request = $smcFunc['db_query']('', '
	SELECT variable, value FROM {db_prefix}settings
	WHERE variable IN ({array_string:hooks})',
	array('hooks' => array_keys($hooklist))
);
if($smcFunc['db_num_rows']($request) > 0)
{
	while($row = $smcFunc['db_fetch_assoc']($request))
		$smfhooks[$row['variable']] = $row['value'];
	$smcFunc['db_free_result']($request);
}

// update the hooks
foreach($hooklist as $hookname => $value)
{
	if(isset($smfhooks[$hookname]))
		$smfhooks[$hookname] = trim($hooklist[$hookname] .','. trim(str_replace($value, '', $smfhooks[$hookname]), ','), ',');
	else
		$smfhooks[$hookname] = trim($value);

	$smcFunc['db_insert']('replace', '
		{db_prefix}settings',
		array('variable' => 'string', 'value' => 'string'),
		array($hookname, $smfhooks[$hookname]),
		array('variable')
	);
}

// update pmxsef settings
_dbinst_write('Update SEF settings.<br />');

$sefsettings = array(
	'pmxsef_autosave' => '0',
	'pmxsef_wireless' => 'nowap,wap,wap2,imode,moderate',
	'pmxsef_singletoken' => 'add,advanced,all,asc,calendar,check,children,conversation,desc,home,kstart,nw,profile,save,sound,togglebar,topicseen,view,viewweek,xml',
	'pmxsef_ignoreactions' => '',
	'pmxsef_aliasactions' => serialize(array()),
	'pmxsef_ignorerequests' => serialize(array()),
	'pmxsef_codepages' => '/PortaMx/sefcodepages/x',
);

$request = $smcFunc['db_query']('', '
	SELECT variable, value FROM {db_prefix}settings
	WHERE variable IN ({array_string:hooks})',
	array('hooks' => array_keys($sefsettings))
);
if($smcFunc['db_num_rows']($request) > 0)
{
	while($row = $smcFunc['db_fetch_assoc']($request))
	{
		if($row['variable'] != 'pmxsef_codepages')
			$sefsettings[$row['variable']] = $row['value'];
	}
	$smcFunc['db_free_result']($request);
}

foreach($sefsettings as $sefname => $value)
{
	$smcFunc['db_insert']('replace', '
		{db_prefix}settings',
		array('variable' => 'string', 'value' => 'string'),
		array($sefname, $value),
		array('variable')
	);
}

// check the $modSettings['pmx_frontmode']
if(!isset($modSettings['pmx_frontmode']))
	$smcFunc['db_insert']('replace', '
		{db_prefix}settings',
		array('variable' => 'string', 'value' => 'string'),
		array('pmx_frontmode', '1'),
		array('variable')
	);

// clear the cache
cache_put_data('modSettings', null, 90);

// modify existing .htaccess
$fname = $boarddir .'/.htaccess';
if(file_exists($fname) && is_readable($fname))
{
	$htaccess = file_get_contents($fname);
	if(!empty($htaccess))
	{
		if(strpos($htaccess, 'RewriteRule (.*) index.php') !== false)
		{
			$htaccess = trim(str_replace('RewriteRule (.*) index.php', 'RewriteRule ^(.*)$ index.php?q=$1 [L,QSA]', $htaccess));
			$htaccess .= "\n".'# RewriteBase /forumpath/'."\n".'# if you have problem, try to uncomment RewriteBase'."\n".'# and replace /forumpath/ with the path to your forum.';
			$fp = @fopen($fname, 'wb');
			if($fp)
			{
				fwrite($fp, $htaccess);
				fclose($fp);
				_dbinst_write('file .htaccess updated.<br />');
			}
		}
	}
}

_dbinst_write('Setup PortaMx package server.<br />');
// setup Portamx package server
$request = $smcFunc['db_query']('', '
	SELECT id_server
	FROM {db_prefix}package_servers
	WHERE url = {string:url}',
	array(
		'url' => 'http://docserver.portamx.com'
	)
);
if($row = $smcFunc['db_fetch_assoc']($request))
	$smcFunc['db_free_result']($request);
else
{
	$smcFunc['db_insert']('', '
		{db_prefix}package_servers',
		array(
			'name' => 'string',
			'url' => 'string'
		),
		array(
			'PortaMx File Server',
			'http://docserver.portamx.com'
		),
		array('id_server')
	);
}

// done
_dbinst_write('<br />dbinstall done.');

if(!empty($dbinstall_string))
{
	$filename = str_replace('dbinstall.php', '', __FILE__) .'installdone.html';
	$instdone = file_get_contents($filename);
	$instdone = str_replace('<div></div>', '<div style="text-align:left;"><strong>Database install results:</strong><br />'. $dbinstall_string .'</div>', $instdone);
	$fh = fopen($filename, 'w');
	if($fh)
	{
		fwrite($fh, $instdone);
		fclose($fh);
	}
	else
		log_error($dbinstall_string);
}

// clear cache
clean_cache();

/***************************
* GetTableData for convert *
****************************/
function getTableData($tblname, $tblvers)
{
	global $smcFunc, $settings_data;

	if($tblname == 'portamx_blocks')
	{
		// check version and table version
		if(empty($tblvers['blocks']) || (isset($tblvers['blocks']) && $tblvers['blocks'] != '1.0'))
		{
			$result = array();
			$sides = array();
			$pos = 1;

			// convert the table
			$request = $smcFunc['db_query']('', '
					SELECT *
					FROM {db_prefix}portamx_blocks
					ORDER BY side DESC, pos ASC',
				array()
			);
			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				if($row['blocktype'] != 'default')
				{
					if(!in_array($row['side'], $sides))
					{
						$sides[] = $row['side'];
						$pos = 1;
					}
					else
						$pos++;

					$cfg = unserialize($row['config']);
					if(isset($row['bibmode']) && !empty($row['bibmode']))
						$row['side'] = 'bib';
					if(isset($cfg['config']['bibmode']))
					{
						if(!empty($cfg['config']['bibmode']))
							$row['side'] = 'bib';
						unset($cfg['config']['bibmode']);
					}

					$acsgrp = '';
					if(isset($cfg['grp_acs']))
					{
						if(!empty($cfg['grp_acs']))
							$acsgrp = implode(',', $cfg['grp_acs']);
						unset($cfg['grp_acs']);
					}

					$cfg['can_moderate'] = '0';

					$result[] = array(
						'id' => $row['id'],
						'side' => $row['side'],
						'pos' => $pos,
						'active' => $row['active'],
						'cache' => $row['cache'],
						'blocktype' => $row['blocktype'],
						'acsgrp' => $acsgrp,
						'config' => serialize($cfg),
						'content' => $row['content'],
					);
				}
			}
			$smcFunc['db_free_result']($request);

			return $result;
		}
		else
			return '';
	}

	elseif($tblname == 'portamx_articles')
	{
		$result = array();
		$request = $smcFunc['db_query']('', '
				SELECT *
				FROM {db_prefix}portamx_articles
				ORDER BY id',
			array()
		);
		while($row = $smcFunc['db_fetch_assoc']($request))
			$result[] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'catid' => $row['catid'],
				'acsgrp' => $row['acsgrp'],
				'ctype' => $row['ctype'],
				'config' => $row['config'],
				'content' => $row['content'],
				'active' => $row['active'],
				'owner' => $row['owner'],
				'created' => $row['created'],
				'approved' => $row['approved'],
				'approvedby' => $row['approvedby'],
				'updated' => $row['updated'],
				'updatedby' => $row['updatedby'],
			);

		$smcFunc['db_free_result']($request);
		return $result;
	}

	else
		return '';
}

/************************
* Column check function *
*************************/
function check_columns($cols, $data)
{
	// col count same?
	if(count($cols) != count($data))
		$drop = true;
	else
	{
		// yes, check each col
		$drop = false;
		foreach($cols as $col)
		{
			if(array_key_exists($col['name'], $data))
			{
				$check = $data[$col['name']];
				foreach($col as $def => $val)
					$drop = (isset($check[$def]) && ($check[$def] == $val || ($check[$def] == "''" && empty($val)))) ? $drop : true;
			}
			else
				$drop = true;
		}
	}
	return $drop;
}

/**
* Index check function
**/
function check_indexes($indexes, $data, $tblname)
{
	// index count same?
	if(count($indexes) != count($data))
		$drop = true;
	else
	{
		// yes, check each index
		$drop = false;
		foreach($indexes as $index => $values)
		{
			// find the index type
			$check = '';
			foreach($data as $fnd)
			{
				if(strcasecmp($fnd['name'], $values['name']) == 0 || strcasecmp($fnd['name'],$tblname .'_'. $values['name']) == 0)
				{
					$check = $fnd;
					$check['name'] = $values['name'];
					break;
				}
				elseif(strcasecmp($fnd['name'], $tblname .'_pkey') == 0 && strtolower($values['name']) == 'primary')
				{
					$check = $fnd;
					$check['name'] = 'primary';
					break;
				}
			}

			// now check the values
			if(!empty($check))
			{
				foreach($values as $def => $value)
				{
					// index cols?
					if(is_array($value))
					{
						if(array_diff($check[$def], $value) != array())
							$drop = true;
					}
					// no, type and name
					elseif((isset($check[$def]) && ($check[$def] == $value || $check[$def] == strtoupper($value))) === false)
						$drop = true;
				}
			}
			else
				$drop = true;
		}
	}
	return $drop;
}
?>
