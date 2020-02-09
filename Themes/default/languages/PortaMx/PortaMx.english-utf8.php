<?php
/**
* \file PortaMx.english-utf8.php
* Language file PortaMx.english-utf8
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

$txt['forum'] = 'Community';
$txt['pmx_button'] = 'PortaMx';
$txt['pmx_managers'] = 'Your PortaMx';
$txt['pmx_expand'] = 'Expand ';
$txt['pmx_collapse'] = 'Collapse ';
$txt['pmx_hidepanel'] = 'Hide the ';
$txt['pmx_showpanel'] = 'Show the ';
$txt['pmx_expand_index'] = 'Expand';
$txt['pmx_show_index'] = 'Show';

// do not change the array keys !
$txt['pmx_block_panels'] = array(
	'head' => 'head Panel',
	'top' => 'top center Panel',
	'left' => 'left Panel',
	'right' => 'right Panel',
	'bottom' => 'bottom center Panel',
	'foot' => 'foot Panel',
);

// do not change the array keys !
$txt['pmx_block_sides'] = array(
	'head' => 'Head',
	'top' => 'Top center',
	'left' => 'Left',
	'right' => 'Right',
	'bottom' => 'Bottom center',
	'foot' => 'Foot',
	'front' => 'Front',
	'pages' => 'Pages',
);

// Admin and dropdown menue
$txt['pmx_admincenter'] = 'PortaMx Admin Center';
$txt['pmx_settings'] = 'Settings Manager';
$txt['pmx_blocks'] = 'Block Manager';
$txt['pmx_adm_settings'] = 'PortaMx Settings Manager';
$txt['pmx_adm_blocks'] = 'PortaMx Block Manager';
$txt['permissionname_manage_portamx'] = 'Moderate the PortaMx Block Manager';
$txt['permissionhelp_manage_portamx'] = 'With this permission any members of this group can access the PortaMx Block Manager moderation.';

$txt['pmx_categories'] = 'Category Manager';
$txt['pmx_articles'] = 'Article Manager';
$txt['pmx_sefengine'] = 'SEF Manager';
$txt['pmx_languages'] = 'Language Manager';
$txt['pmx_adm_categories'] = 'PortaMx Category Manager';
$txt['pmx_adm_articles'] = 'PortaMx Article Manager';
$txt['pmx_adm_sefengine'] = 'PortaMx SEF Manager';
$txt['pmx_adm_languages'] = 'PortaMx Language Manager';

// teaser
$txt['pmx_readmore'] = '<b>Read the whole article</b>';
$txt['pmx_readclose'] = '<b>Collapse the article</b>';
$txt['pmx_teaserinfo'] = array(
	0 => ' title="Truncation: %s of %s Words"',
	1 => ' title="Truncation: %s of %s Character"',
);

// HighSlide JS
$txt['pmx_hs_read'] = 'Click to read the full message';
$txt['pmx_hs_expand'] = 'Click to enlarge';
$txt['pmx_hs_caption'] = '<a href=\'http://highslide.com/\'>Powered by <i>Highslide JS</i></a>, implemented by PortaMx corp.';
$txt['pmx_hs_noimage'] = 'Image not exists';

// special PHP type blocks/articles
$txt['pmx_edit_content_init'] = ' (INIT PART)';
$txt['pmx_edit_content_show'] = ' (SHOW PART)';
$txt['pmx_php_partblock'] = 'init part editor';
$txt['pmx_php_partblock_note'] = '<b>Second editor for special PHP blocks</b>';
$txt['pmx_php_partblock_help'] = '
	You can create special PHP blocks with a <b>show part</b> (executed from template) in the above editor and a <b>init part</b> (executed on load time) in the <b>second editor</b>.
	The PHP block have two variables (<b>$this->php_content</b> and <b>$this->php_vars</b>) for common use and transfer values between both parts, as example:<br />
	<i>Code in the init part: <b>$this->php_content = \'Hello world!\';</b><br />
	Code in show part like: <b>echo $this->php_content;</b></i>';

// error messages
$txt['pmx_acces_error'] = 'You are not allowed to access this section';
$txt['feed_response_error'] = "fsockopen(%s) failed.\nError: Response timeout (%s seconds).";
$txt['page_reqerror_title'] = 'Page request Error';
$txt['page_reqerror_msg'] = 'The page you have requested does not exist or you have no access rights.';
$txt['article_reqerror_title'] = 'Article request Error';
$txt['article_reqerror_msg'] = 'The article you have requested does not exist or you have no access rights.';
$txt['category_reqerror_title'] = 'Category request Error';
$txt['category_reqerror_msg'] = 'The category you have requested does not exist or you have no access rights.';
$txt['download_error_title'] = 'Download Error';
$txt['download_acces_error'] = 'You have not enough rights to proceed the requested download.';
$txt['download_notfound_error'] = 'The requested download is not available and can not proceed.';
$txt['download_unknown_error'] = 'Illegal request reached, the download can not proceed.';
$txt['front_reqerror_title'] = 'Request Error';
$txt['front_reqerror_msg'] = 'The request can\'t processed because the Frontpage is locked.';
$txt['unknown_reqerror_title'] = 'Request Error';
$txt['unknown_reqerror_msg'] = 'The requested item is not available or can not proceed.';;
$txt['page_reqerror_button'] = 'Back';

// Caching
$txt['cachestats'] = array(
	'mode' => '',
	'hits' => ', Hits:',
	'fails' => ', Fails:',
	'loaded' => ', Loaded:',
	'saved' => ', Saved:',
	'time' => ', Time:'
);
$txt['cachemode'] = array(
	0 => 'disabled',
	1 => 'Memcached',
	3 => 'MMCache',
	4 => 'APC',
	5 => 'xCache',
	6 => 'file',
);
$txt['cache_status'] = 'PortaMx-cache[ ';
$txt['cacheseconds'] = ' seconds';
$txt['cachemilliseconds'] = ' milliseconds';
$txt['cachekb'] = ' kb';

// elc authentication
$txt['pmxecl_noAuth'] = 'Cookie acceptance required';
$txt['pmxelc_needAccept'] = 'To browser these website, it\'s necessary to store cookies on your computer.<br />
	The cookies contain no personal information, they are required for program control.<br />';
$txt['pmxelc_agree'] = '<br /><b>Until you accept the storage of cookies you can\'t continue.</b>';
$txt['pmxelc_modal'] = '<b>the storage of cookies while browsing this website, on Login and Register.</b>';
$txt['pmxelc_button'] = 'I accept';
$txt['pmxelc_button_ttl'] = 'I accept the cookies';
$txt['pmxelc_lang'] = 'Language:';
$txt['pmxelc_privacy'] = 'Privacy Notice';
$txt['pmxelc_privacy_ttl'] = 'Show or Hide the Privacy Notice';
$txt['pmxelc_privacy_note'] = 'Please read our Privacy Notice on the full version.';
$txt['pmxelc_privacy_failed'] = 'No Privacy Notice exists.';
$txt['pmxelc_failed_login'] = 'You can\'t Login without accept the Cookie storage!';
$txt['pmxelc_failed_register'] = 'You can\'t Register a account without accept the Cookie storage!';
$txt['pmxelc_failed_request'] = 'You can\'t perform the current request without accept the Cookie storage!';
$txt['pmxelc_failed_access'] = 'You have not enough rights to perform the current request!';

// who display
$txt['pmx_who_frontpage'] = 'Viewing the front page';
$txt['pmx_who_spage'] = 'Viewing the page %s';
$txt['pmx_who_art'] = 'Viewing the article %s';
$txt['pmx_who_cat'] = 'Viewing the category %s';
$txt['pmx_who_portamx'] = 'Viewing the PortaMx %s';
$txt['pmx_who_admin'] = 'Viewing the Admin area %s';
$txt['pmx_who_unknow'] = 'Viewing %s';
$txt['pmx_who_acts'] = array(
	'pmx_center' => 'Admin Center',
	'pmx_settings' => 'Settings Manager',
	'pmx_blocks' => 'Blocks Manager',
	'pmx_articles' => 'Article Manager',
	'pmx_categories' => 'Category Manager',
	'pmx_languages' => 'Language Manager',
);

// category/article display
$txt['pmx_openSidebar'] = 'Click to see more articles';
$txt['pmx_clickclose'] = 'Click to close';
$txt['pmx_more_articles'] = 'Articles in the category';
$txt['pmx_more_categories'] = 'More Categories in';

/* Blocktype specific text */
// cbt_navigator
$txt['pmx_cbt_colexp'] = 'Collapse/Expand: ';
$txt['pmx_cbt_expandall'] = 'Expand';
$txt['pmx_cbt_collapseall'] = 'Collapse';

// download
$txt['download'] = 'Download';
$txt['pmx_download_empty'] = '<strong>No downloads available</strong>';
$txt['pmx_kb_downloads'] = 'Kb, Downloads: ';

// polls
$txt['pmx_poll_novote_opt'] = 'You didn\'t select a vote option.';
$txt['pmx_pollmultiview'] = 'Choose a poll to show:';
$txt['pmx_poll_closed'] = 'Voting closed.';
$txt['pmx_poll_select_locked'] = ' [Locked]';
$txt['pmx_poll_select_expired'] = ' [Expired]';

// rss reader
$txt['pmx_rssreader_postat'] = 'Posted: ';
$txt['pmx_rssreader_error'] = 'Response timeout error, can\'t read the feed.';
$txt['pmx_rssreader_timeout'] = 'Timeout while waiting for data.';

// shoutbox
$txt['pmx_shoutbox_toggle'] = 'Toggle edit mode';
$txt['pmx_shoutbox_shoutdelete'] = 'Delete this shout';
$txt['pmx_shoutbox_shoutconfirm'] = 'Are you sure you want to delete this shout?';
$txt['pmx_shoutbox_shoutedit'] = 'Edit this shout';
$txt['pmx_shoutbox_button_open'] = 'Shout?';
$txt['pmx_shoutbox_button'] = 'Shout!';
$txt['pmx_shoutbox_button_title'] = 'Enter a new Shout!';
$txt['pmx_shoutbox_send_title'] = 'Send your Shout!';
$txt['pmx_shoutbox_bbc_code'] = 'Toggle BBC Display';
$txt['pmx_shoutbbc_b'] = 'Bold';
$txt['pmx_shoutbbc_i'] = 'Italic';
$txt['pmx_shoutbbc_u'] = 'Underline';
$txt['pmx_shoutbbc_s'] = 'Strikethrough';
$txt['pmx_shoutbbc_m'] = 'Marquee';
$txt['pmx_shoutbbc_sub'] = 'Subscript';
$txt['pmx_shoutbbc_sup'] = 'Superscript';
$txt['pmx_shoutbbc_changecolor'] = 'Change color';
$txt['pmx_shoutbbc_colorBlack'] = 'Black';
$txt['pmx_shoutbbc_colorRed'] = 'Red';
$txt['pmx_shoutbbc_colorYellow'] = 'Yellow';
$txt['pmx_shoutbbc_colorPink'] = 'Pink';
$txt['pmx_shoutbbc_colorGreen'] = 'Green';
$txt['pmx_shoutbbc_colorOrange'] = 'Orange';
$txt['pmx_shoutbbc_colorPurple'] = 'Purple';
$txt['pmx_shoutbbc_colorBlue'] = 'Blue';
$txt['pmx_shoutbbc_colorBeige'] = 'Beige';
$txt['pmx_shoutbbc_colorBrown'] = 'Brown';
$txt['pmx_shoutbbc_colorTeal'] = 'Teal';
$txt['pmx_shoutbbc_colorNavy'] = 'Navy';
$txt['pmx_shoutbbc_colorMaroon'] = 'Maroon';
$txt['pmx_shoutbbc_colorLimeGreen'] = 'Lime Green';
$txt['pmx_shoutbbc_colorWhite'] = 'White';

// statistics
$txt['pmx_stat_member'] = 'Members';
$txt['pmx_stat_totalmember'] = 'Total Members';
$txt['pmx_stat_lastmember'] = 'Latest';
$txt['pmx_stat_stats'] = 'Stats';
$txt['pmx_stat_stats_post'] = 'Total Posts';
$txt['pmx_stat_stats_topic'] = 'Total Topics';
$txt['pmx_stat_stats_ol_today'] = 'Online Today';
$txt['pmx_stat_stats_ol_ever'] = 'Most Online';
$txt['pmx_stat_users'] = 'Users online';
$txt['pmx_stat_users_reg'] = 'Users';
$txt['pmx_stat_users_guest'] = 'Guests';
$txt['pmx_stat_users_spider'] = 'Spiders';
$txt['pmx_stat_users_total'] = 'Total';
$txt['pmx_memberlist_icon'] = 'memberlist';
$txt['pmx_statistics_icon'] = 'statistics';
$txt['pmx_online_user_icon'] = 'online user';

// theme select
$txt['pmx_theme_change'] = 'Click on the image to change the Theme';

// user_login
$txt['pmx_hello'] = 'Hello ';
$txt['pmx_pm'] = 'Personal Messages';
$txt['pmx_unread'] = 'Show unread posts';
$txt['pmx_replies'] = 'Show unread replies';
$txt['pmx_showownposts'] = 'Show my posts';
$txt['pmx_unapproved_members'] = 'Unapproved member:';
$txt['pmx_maintenace'] = 'Maintenance mode';
$txt['pmx_loggedintime'] = 'Logged in';
$txt['pmx_Ldays'] = 'd';
$txt['pmx_Lhours'] = 'h';
$txt['pmx_Lminutes'] = 'm';
$txt['pmx_langsel'] = 'Select language:';

// mini_calendar
$txt['pmx_cal_birthdays'] = 'Birthdays';
$txt['pmx_cal_holidays'] = 'Holidays';
$txt['pmx_cal_events'] = 'Events';
/* Birthday, Holiday, Event date format chars:
%M = Month (Jan - Dec)
%m = Month (01 - 12)
%d = Day (01 - 31)
%j = Day (1 - 31) */
$txt['pmx_minical_dateform'] = array(
	'%M %d',		// single date
	'%M %d',		// start-date same month
	' - %d',		// end-date same month
	'%M %d',		// start-date not same month
	' - %M %d'	// end-date not same month
);

// common use
$txt['pmx_text_category'] = 'Category: ';
$txt['pmx_text_board'] = 'Board: ';
$txt['pmx_text_topic'] = 'Topic: ';
$txt['pmx_text_post'] = 'Post: ';
$txt['pmx_text_postby'] = 'Posted by: ';
$txt['pmx_text_replies'] = ' Replies: ';
$txt['pmx_text_views'] = 'Views: ';
$txt['pmx_text_createdby'] = 'Created by: ';
$txt['pmx_text_updated'] = 'Last update: ';
$txt['pmx_text_readmore'] = '<b>Read more</b>';
$txt['pmx_text_show_attach'] = '<b>Show attaches</b>';
$txt['pmx_text_hide_attach'] = '<b>Hide attaches</b>';
$txt['pmx_text_printing'] = 'Print the content';
$txt['pmx_user_unknown'] = 'Unknown';
$txt['pmx_set_promote'] = 'Promote Message';
$txt['pmx_unset_promote'] = 'Clear Promote';
?>
