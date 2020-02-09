<?php
/**
* \file AdminBlocks.english-utf8.php
* Language file AdminBlocks.english-utf8
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

// Block description
$txt['pmx_boardnews_description'] = 'Board News';
$txt['pmx_download_description'] = 'Download';
$txt['pmx_mini_calendar_description'] = 'Mini Calendar';
$txt['pmx_html_description'] = 'HTML';
$txt['pmx_newposts_description'] = 'New Posts';
$txt['pmx_php_description'] = 'PHP';
$txt['pmx_recent_post_description'] = 'Recent Post';
$txt['pmx_recent_topics_description'] = 'Recent Topics';
$txt['pmx_script_description'] = 'Script';
$txt['pmx_statistics_description'] = 'Statistics';
$txt['pmx_theme_select_description'] = 'Theme Select';
$txt['pmx_user_login_description'] = 'User Login';
$txt['pmx_cbt_navigator_description'] = 'CBT Navigator';
$txt['pmx_bbc_script_description'] = 'BBC Script';
$txt['pmx_rss_reader_description'] = 'RSS Reader';
$txt['pmx_shoutbox_description'] = 'Shout box';
$txt['pmx_polls_description'] = 'Polls';
$txt['pmx_boardnewsmult_description'] = 'Multiple Board News';
$txt['pmx_article_description'] = 'Static Article';
$txt['pmx_category_description'] = 'Static Category';
$txt['pmx_promotedposts_description'] = 'Promoted Posts';
$txt["pmx_fader_description"] = 'Opaque Fader';

// Block overview
$txt['pmx_add_sideblock'] = 'Click to add a block to %s';
$txt['pmx_edit_sideblock'] = 'Click to edit this block';
$txt['pmx_clone_sideblock'] = 'Click to clone this block';
$txt['pmx_move_sideblock'] = 'Click to move this block';
$txt['pmx_delete_sideblock'] = 'Click to delete this block';
$txt['pmx_confirm_blockdelete'] = 'Are you sure you want to delete this Block?';
$txt['pmx_chg_blockaccess'] = 'Click to change visibility access';

$txt['pmx_admBlk_order'] = 'Order';
$txt['pmx_admBlk_adj'] = 'Adjust';
$txt['pmx_admBlk_type'] = 'Block type';
$txt['pmx_admBlk_options'] = 'Setting options';

$txt['pmx_moveto'] = 'Move to:&nbsp;';
$txt['pmx_cloneto'] = 'Clone to:&nbsp;';
$txt['pmx_clonechoice'] = 'Select side:';
$txt['pmx_chgAccess'] = 'Block visibility access';

$txt['pmx_have_settings'] = 'Block has settings options';
$txt['pmx_have_groupaccess'] = 'Block has visibility access';
$txt['pmx_have_modaccess'] = 'Block has moderate access';
$txt['pmx_have_dynamics'] = 'Block has dynamic visibility options';
$txt['pmx_have_cssfile'] = 'Block has own style sheet file';
$txt['pmx_have_caching'] = 'Block caching: ';

$txt['pmx_edit_type'] = 'Block type:';
$txt['pmx_edit_cache'] = 'Enable cache:';
$txt['pmx_edit_cachetime'] = 'Time:';
$txt['pmx_edit_cachetimemin'] = 'Min';
$txt['pmx_edit_cachetimesec'] = ' Sec';

$txt['pmx_edit_cachehelp'] = 'If enabled, the content is saved and refreshed after given time.<br />
	You can use the multiplicator "*" like "24*60" for one day.';
$txt['pmx_edit_pmxcachehelp'] = 'Don\'t change the cache time, until you known what you do. A bad value can slow down your server!<br >
	To restore the default value, disable the cache and enable again.';
$txt['pmx_edit_nocachehelp'] = 'Caching is not possible for this block type.';
$txt['pmx_edit_noSMFcache'] = 'Caching is disabled in SMF server settings.';

$txt['pmx_edit_frontplacing'] = 'Block placement on Single Pages, Categories or Articles';
$txt['pmx_edit_frontplacing_hide'] = 'Hide block:';
$txt['pmx_edit_frontplacing_before'] = 'Place before:';
$txt['pmx_edit_frontplacing_after'] = 'Place after:';
$txt['pmx_edit_frontplacinghelp'] = 'Choice the placement for this block if a Singe page, Category or Article requested.';

$txt['pmx_edit_frontmode'] = 'Frontpage mode switch';
$txt['pmx_edit_frontmode_none'] = 'No change:';
$txt['pmx_edit_frontmode_center'] = 'To centered:';
$txt['pmx_edit_frontmode_full'] = 'To full size:';
$txt['pmx_edit_frontmodehelp'] = 'Choice the Frontpage mode for this block.
	The Frontpage is switched to the selected mode, if this Page, Category or Article requested.';

$txt['pmx_edit_frontview'] = 'Block visibility on Frontpage mode';
$txt['pmx_edit_frontview_any'] = 'Always:';
$txt['pmx_edit_frontview_center'] = 'On centered:';
$txt['pmx_edit_frontview_full'] = 'On full size:';
$txt['pmx_edit_frontviewhelp'] = 'Choice the block visibility for a Frontpage mode.
	Note that the block is shown only, if the Frontpage in the selected mode.';

$txt['pmx_edit_groups'] = 'Block visibility access settings';
$txt['pmx_edit_groups_help'] = 'Choose your membergroups that will able to see this block.<br />
	You can also use <b>deny group</b>. This is useful when a user is in more than one group, but one of the groups should not see the block.<br />
	To toggle between deny groups and access groups, hold down the <b>Ctrl Key</b> and <b>double click</b> on the item.
	If the group a deny group,  you see the deny symbol <b>^</b> before the group name.';

$txt['pmx_edit_ext_opts'] = 'Dynamic visibility options';
$txt['pmx_edit_ext_opts_help'] = 'If you choose any of these dynamic visibility options, the block will show <b>just</b> on these, nowhere else.
		To display the block without any dynamic visibility, leave <b>all unselected</b>.';
$txt['pmx_edit_ext_opts_morehelp'] = '<br style="line-height:1.7em;" />
	To select or unselect one or more options, hold down the <b>Ctrl Key</b> and <b>click</b> on the items.
	To toggle between <b>Show and Hide</b>, hold down the <b>Ctrl Key</b> and take a <b>double click</b> (IE needs three clicks!) on the item.
	If a item set to <b>Hide</b> the symbol <b>^</b> is shown at the front.<br />
	<u><b>How does it work?</b></u><br />
	<b>Show</b>: If you want to indicate the block only with to one or more actions, boards or languages, then select these.<br />
	<b>Hide</b>: If you want to always indicate the block, only with one or more actions, boards or languages not, then select these with a double click (you see a <b>^</b> at the front).<br />
	<b>Examples</b>:<br />Select the actions "Admin" and "Calendar".. The block shows only on Admin and Calendar.<br />
	Select the action "<b>^</b>Admin" .. The block shows always but not on Admin.';

$txt['pmx_edit_ext_opts_action'] = 'Show or Hide the block on action';
$txt['pmx_edit_ext_opts_custaction'] = 'Show or Hide the block on custom action';
$txt['pmx_edit_ext_opts_boards'] = 'Show or Hide the block on board';
$txt['pmx_edit_ext_opts_languages'] = 'Show or Hide the block on language';
$txt['pmx_edit_ext_opts_themes'] = 'Show or Hide the block on theme';
$txt['pmx_edit_ext_SD_standalone'] = 'Hide on SimpleDesk standalone mode:';
$txt['pmx_edit_ext_maintenance'] = 'Show on Maintenance mode:';

$txt['pmx_edit_ext_opts_custhelp'] = 'Here you can enter any other actions.
	For <b>Single pages, Articles and Categories</b> we use a prefix (<b>p:</b> for Single pages, <b>a:</b> for Articles and <b>c:</b> for Categories).
	Enter the prefix before the page, article or category name, as example <b>p:mypage</b>.
	You can use names with the wildcards <b>*</b> and <b>?</b>. To <b>Hide</b> the Block on a entry, enter the symbol <b>^</b> before the name or the Prefix.
	Furthermore you can also use subaction, these starts alway with a ampersand (<b>&amp;</b>) like <b>&amp;subactionname=value</b>.
	More detailed informations about the customer actions you can find in our documentation or in the Support Forum.';
$txt['pmx_edit_ext_opts_selnote'] = 'To show or hide the block, hold down the <b>Ctrl Key</b> and <b>click</b> on the items.
	To toggle between <b>Show and Hide</b>, hold down the <b>Ctrl Key</b> and take a <b>double click</b> (IE needs three clicks!) on the item.
	If a item set to <b>Hide</b> the symbol <b>^</b> is shown at the front.';

$txt['pmx_block_moderate_title'] = 'Block moderation';
$txt['pmx_block_moderate'] = 'Enable Block moderation:';
$txt['pmx_block_moderatehelp'] = 'If checked, all member in the Block Moderator Group can edit this block.';

$txt['pmx_rowmove_title'] = 'Set new Block position';
$txt['pmx_block_rowmove'] = 'Move Block';
$txt['pmx_blockmove_place'] = 'to the position';
$txt['pmx_blockmove_to'] = 'Block';
$txt['rowmove_before'] = 'before';
$txt['rowmove_after'] = 'after';
$txt['row_move_updown'] = 'Click to move the block position';

$txt['pmx_clone_move_side'] = 'Select the destination:';
$txt['pmx_clone_move_title'] = '';
$txt['pmx_text_clone'] = 'Clone block';
$txt['pmx_text_move'] = 'Move block';
$txt['pmx_text_block'] = 'Block:';
$txt['pmx_blocks_settings_title'] = '%s block settings';
$txt['pmx_clone_move_toarticles'] = 'Articles Manager';
$txt['pmx_promote_all'] = '[ all posts ]';

/* Blocktype specific text */
// cbt_navigator
$txt['pmx_cbtnavnum'] = 'Max number of topics in each board:';
$txt['pmx_cbtnavlen'] = 'Max length for each entry (characters):';
$txt['pmx_cbtnavexpand'] = 'Expand all boards initially:';
$txt['pmx_cbtnavexpandnew'] = 'Expand boards with new posts initially:';
$txt['pmx_cbtnavboards'] = 'Choose the boards to show in the Navigator block';
$txt['pmx_cbt_shorten_hint'] = 'The length is dependent on Font size and Font type.
	On a block width of 170 pixel the value of 20 would be recommend.<br />
	To disable the line shorten, enter a value of "0".';

// download
$txt['pmx_download_board'] = 'Choose the Board to download from:';
$txt['pmx_download_groups'] = 'Choose groups that have download access:';

// fader
$txt['pmx_fader_uptime'] = 'Uptime:';
$txt['pmx_fader_downtime'] = 'Downtime:';
$txt['pmx_fader_holdtime'] = 'Holdtime:';
$txt['pmx_fader_changetime'] = 'Changetime:';
$txt['pmx_fader_units'] = 'seconds';
$txt['pmx_fader_timehelp'] = 'All times must be entered as #.#### seconds. The given value is internal converted to milliseconds (#.#### * 1000)';
$txt['pmx_fader_content'] = 'Enter the Fader content:';

// do not reformat these !
$txt['pmx_fader_content_help'] = 'You can use any html code in the fader.
	Each entry must enclosed in curly brackets <b>{ .. }</b>.
	Line breaks, carriage returns, tabs and spaces will be removed on runtime.
	You can overwrite the time values for each entry by adding a <b>=(uptime,downtime,holdtime)</b> immediate after the closed curly brackets <b>}</b>.
	All time values must be define in seconds.
	You can also overwrite a singe value like <b>=(,,5.0)</b> which will change the holdtime only.
	<b>Examples</b>:';
// do not reformat these !
$txt['pmx_fader_content_help1'] = '{A simple text<br />
	break in two lines.}
{
	<img src="url.tld/path/imagename.png" />
}
{ <a href="url.tld" target="_blank">
	 This is a link
</a> }=(1.5,1.5,4.0)';

// polls
$txt['pmx_select_polls'] = 'Select polls to show in the Poll block:';
$txt['pmx_polls_hint'] ='If you select more then one poll, a "multiple" pollblock with a select bar at the bottom is created.';
$txt['pmx_no_polls'] = 'No polls found';

// recent_posts/topics
$txt['pmx_recentpostnum'] = 'Number of posts to show:';
$txt['pmx_recenttopicnum'] = 'Number of topics to show:';
$txt['pmx_recent_boards'] = 'Choose the boards to show in the Recent block';
$txt['pmx_recent_boards_help'] = 'Select the boards to show or select nothing to show all boards.';
$txt['pmx_recent_showboard'] = 'Show boardname:';

// statistics
$txt['pmx_admstat_member'] = 'Show Member statistics:';
$txt['pmx_admstat_stats'] = 'Show Post and Online statistics: ';
$txt['pmx_admstat_users'] = 'Show User statistics: ';
$txt['pmx_admstat_spider'] = 'Show Spider in the User stats: ';
$txt['pmx_admstat_olheight'] = 'Entries in the Userlist before scroll: ';
$txt['pmx_admstat_olheight_help'] = 'Enter 0 to disable the User online list';

// theme_select
$txt['pmx_select_themes'] ='Select themes to show in the Theme block';
$txt['pmx_themes_hint'] ='Themes marked with [x] are not enabled in SMF';

// user_login
$txt['show_avatar'] = 'Show Avatar (if exist):';
$txt['show_pm'] = 'Show Personal Messages:';
$txt['show_posts'] = 'Show unread replies/posts:';
$txt['show_logtime'] = 'Show total logged in time:';
$txt['show_unapprove'] = 'Show unapproved member:';
$txt['show_login'] = 'Show login for Guests:';
$txt['show_langsel'] = 'Show language selector:';
$txt['show_logout'] = 'Show logout Button:';
$txt['show_time'] = 'Show current time:';
$txt['show_realtime'] = 'Show the current time as real time:';
$txt['pmx_rtcformatstr'] = 'Real time format:';
$txt['pmx_rtc_formathelp'] = '
	Leave this empty if you want to use the time format as setup in SMF or you Profile.<hr />
	The following characters are recognized in the format string:<br />
	&nbsp; %a - abbreviated weekday name (Ddd)<br />
	&nbsp; %A - full weekday name<br />
	&nbsp; %b - abbreviated month name (Mmm)<br />
	&nbsp; %B - full month name<br />
	&nbsp; %D* - same as %m/%d/%y<br />
	&nbsp; %d - day of the month (01 to 31)<br />
	&nbsp; %e* - day of the month (1 to 31)<br />
	&nbsp; %H - hour using a 24-hour clock (range 00 to 23)<br />
	&nbsp; %I - hour using a 12-hour clock (range 01 to 12)<br />
	&nbsp; %m - month (01 to 12)<br />
	&nbsp; %M - minute (00 to 59)<br />
	&nbsp; %p - either "am" or "pm" according to the given time<br />
	&nbsp; %R* - time in 24 hour notation<br />
	&nbsp; %S - second (00 to 59)<br />
	&nbsp; %T* - current time, equal to %H:%M:%S<br />
	&nbsp; %y - 2 digit year (00 to 99)<br />
	&nbsp; %% - a literal \'%\' character<br /><br />
	&nbsp; * - Not supported.';

// boardnews/newposts/promoted posts
$txt['pmx_promoted_selposts'] = 'Show posts selected by Messages:';
$txt['pmx_promoted_selboards'] = 'Show posts selected by Boards:';
$txt['pmx_promoted_posts'] = 'Choose the posts to show:';
$txt['pmx_boardnews_boards'] = 'Choose the Board from which to show boardnews:';
$txt['pmx_postnews_boards'] = 'Choose the Boards from which to show posts:';
$txt['pmx_multbonews'] = 'Max number of posts in each board:';
$txt['pmx_boponews_total'] = 'Number of posts to show:';
$txt['pmx_boponews_split'] = 'Show the posts in two columns:';
$txt['pmx_boponews_rescale'] = 'Rescale inline images:';
$txt['pmx_boponews_rescalehelp'] = 'Inline images can be rescaled or removed. Enter the max size (pixel) or 0 to remove inline images.
	If you don\'t change the images, leave this empty.';
$txt['pmx_boponews_showthumbs'] = 'Show thumbnails under the posts:';
$txt['pmx_boponews_hidethumbs'] = 'Collapse the thumbnails area:';
$txt['pmx_boponews_hidethumbshelp'] = 'If checked, the thumbnail area is collapsed and can expand manually for each post.';
$txt['pmx_boponews_thumbcnt'] = 'Number of thumbnails to show:';
$txt['pmx_boponews_thumbcnthelp'] = 'Enter the number of max thumbnail to show or leave this empty to show all.';
$txt['pmx_boponews_disableHS'] = 'Disable HighSlide viewer for Posts:';
$txt['pmx_boponews_disableHSimage'] = 'Disable HighSlide viewer for Images:';
$txt['pmx_boponews_page'] = 'Number of posts on page:';
$txt['pmx_boponews_equal'] = 'Set columns in a row to the same height:';
$txt['pmx_boponews_postinfo'] = 'Show Postheader (Posted by, Board):';
$txt['pmx_boponews_postviews'] = 'Add Views/Replies to Postheader:';

// rss_reader
$txt['pmx_rssreader_url'] = 'Enter the full url for the feed:';
$txt['pmx_rssreader_urlhelp'] = 'For SMF Forums you can use follow:<br />'.
	'<b>forumurl?action=.xml;<i>options</i></b><br />'.
	'<i>Options:</i> &nbsp;type=s;sa=s;boards=n;limit=n;<br />'.
	'&nbsp; type: <b>rss</b> | <b>rss2</b> | <b>rdf</b> | <b>atom</b><br />'.
	'&nbsp; sa: <b>recent</b> | <b>news</b> | <b>members</b><br />'.
	'&nbsp; boards: <b>#[,#,#]</b> (# is the board id)<br />'.
	'&nbsp; limit: <b>#</b> (# is a number, 1 to n)<br />'.
	'<i>Defaults: </i>sa=recent';
$txt['pmx_rssreader_timeout'] = 'Feed response timeout (sec):';
$txt['pmx_rssreader_timeouthelp'] = 'The reader stops the reading after response timeout, if no data received. (Default: <b>5</b> seconds)';
$txt['pmx_rssreader_usettl'] = 'Set the cache time automatic from TTL:';
$txt['pmx_rssreader_usettlhelp'] = 'If checked, a received TTL (Time To Life) enabled the cache and set the cache time automatic to the received value.';
$txt['pmx_rssreader_maxitems'] = 'Max items to show:';
$txt['pmx_rssreader_cont_encode'] = 'Use "content:encoded" if send:';
$txt['pmx_rssreader_cont_encodehelp'] = 'If you enable this option and the feed send a "encoded" content (many feeds do that), you see a longer content with images and other elements.';
$txt['pmx_rssreader_split'] = 'Show the posts in two columns:';
$txt['pmx_rssreader_showhead'] = 'Show the feed header:';
$txt['pmx_rssreader_help'] = 'The follow settings used only, if not feed header send.	<b>Note</b> that SMF don\'t send header lines !';
$txt['pmx_rssreader_name'] = 'Site name:';
$txt['pmx_rssreader_link'] = 'Site link:';
$txt['pmx_rssreader_desc'] = 'Description:';
$txt['pmx_rssreader_delimages'] = 'Remove inline images:';
$txt['pmx_rssreader_delimagehelp'] = 'If enabled, inline images and objects are removed.';
$txt['pmx_rssreader_maxitems'] = 'Max items to show:';
$txt['pmx_rssmaxitems_help'] = 'Enter the number of max item you will see or leave empty to see all received articles.';
$txt['pmx_rssreader_page'] = 'Number of article on page:';
$txt['pmx_rsspage index_help'] = 'Enter the number of articles you will see on a page.';

// shoutbox
$txt['pmx_shoutbox_maxlen'] = 'Max number of characters in shout:';
$txt['pmx_shoutbox_maxshouts'] = 'Number of shouts to show:';
$txt['pmx_shoutbox_maxshouthelp'] = 'Enter the number of shout to show. Note, that older shouts automatically removed, if this value overflow.';
$txt['pmx_shoutbox_maxheight'] = 'Max height of shout box (pixel):';
$txt['pmx_shoutbox_scrollspeed'] = 'Shout box scroll speed:';
$txt['pmx_shoutbox_speedhelp'] = 'After edit a shout or on "Reverse shouts", the editing or last shout is scrolled to the bottom.
	Here you can setup the scroll speed in ~0,1 sec units. If you set this value to 0, the position is set immediate.';
$txt['pmx_shoutbox_collapse'] = 'Collapse the input box initially:';
$txt['pmx_shoutbox_collapsehelp'] = 'If checked, the Shout input box is normally collapsed and is open with a click on the "Shout!" button.';
$txt['pmx_shoutbox_reverse'] = 'Reverse shouts (show last at bottom):';
$txt['pmx_shoutbox_allowedit'] = 'User can edit and delete own shouts:';
$txt['pmx_shoutbox_canshout'] = 'Select the user groups that can shout';

// Category
$txt['pmx_catblock_cats'] = 'Choose the Category:';
$txt['pmx_catblock_blockframe'] = 'Use Titlebar and Frame from the Block:';
$txt['pmx_catblock_catframe'] = 'Use the Titlebar and Frame from the Category:';
$txt['pmx_catblock_inherit'] = 'Inherit Block access to the Category:';
$txt['pmx_catblock_inherithelp'] = 'if checked, the block permissions is inherit to the category.
	This is done even, if the permission on the category are higher as on the block.<br />
	Note that the access to the articles in the category in NOT inherit from the block,
	the article access is given by the article or is inherit from the category.';

// Article
$txt['pmx_artblock_arts'] = 'Choose the Article:';
$txt['pmx_artblock_blockframe'] = 'Use Titlebar and Frame from the Block:';
$txt['pmx_artblock_artframe'] = 'Use the Titlebar and Frame from the Article';
$txt['pmx_artblock_inherit'] = 'Inherit Block access to the Article:';
$txt['pmx_artblock_inherithelp'] = 'if checked, the block permissions is inherit to the article.
	This is done even, if the permission on the article are higher as on the block.';

// mini calendar
$txt['pmx_minical_firstday'] = 'Default first day of the week:';
$txt['pmx_minical_firstdays'] = array(
	0 => 'Sunday',
	1 => 'Monday',
	6 => 'Saturday');
$txt['pmx_minical_birthdays'] = 'Show birthdays:';
$txt['pmx_minical_holidays'] = 'Show holidays:';
$txt['pmx_minical_events'] = 'Show events:';
$txt['pmx_minical_bdays_before'] = 'Days before today:';
$txt['pmx_minical_bdays_after'] = 'Days after today:';

// common for teaser
$txt['pmx_adm_teaser'] = 'Number of %s before tease:';
$txt['pmx_adm_teasehelp'] = 'Enter 0 for no tease';

// common for pages
$txt['pmx_pageindex_pagetop'] = 'Show page index also on top:';
$txt['pmx_pageindex_help'] = 'Enter the number of posts you will see on a page.
	If the number of posts to show bigger as this value, the page index is show.
	Leave this empty (or set to 0) to disable the pagination.';
$txt['pmx_pageindex_tophelp'] = 'If checked, the page index is show on top and bottom.
	If disabled the page index is show only on bottom.';
?>
