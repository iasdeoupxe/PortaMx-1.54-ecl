<?php
/**
* \file AdminSettings.english.php
* Language file AdminSettings.english
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

// AdminSettings
$txt['pmx_admSet_globals'] = 'Global settings';
$txt['pmx_admSet_panels'] = 'Panel settings';
$txt['pmx_admSet_front'] = 'Frontpage settings';
$txt['pmx_admSet_control'] = 'Manager settings';
$txt['pmx_admSet_access'] = 'Access settings';
$txt['pmx_admSet_pmxsef'] = 'SEF Engine settings';

$txt['pmx_admSet_desc_global'] = 'Configure the global settings.';
$txt['pmx_admSet_desc_panel'] = 'Configure the panel setting.';
$txt['pmx_admSet_desc_front'] = 'Configure the Frontpage settings.';
$txt['pmx_admSet_desc_control'] = 'Configure the Manager control settings.';
$txt['pmx_admSet_desc_access'] = 'Configure the PortaMx Admin, PortaMx Moderator and the Article writer access settings.';
$txt['pmx_admSet_desc_pmxsef'] = 'Configure the Search Engine Friendly URL (SEF) Manager';

// global
$txt['pmx_global_settings'] = 'Global settings';
$txt['pmx_settings_panelpadding'] = 'Padding between panels:';
$txt['pmx_settings_paneloverflow'] = 'Action on Panel size overflow:';
$txt['pmx_settings_download'] = 'Show download button on Menubar:';
$txt['pmx_settings_download_action'] = 'Action name for download button:';
$txt['pmx_settings_download_acs'] = 'Membergroups that can see the download button:';
$txt['pmx_settings_other_actions'] = 'Request names they handled as Forum request:';
$txt['pmx_settings_blockcachestats'] = 'Show the pmx-cache status in the footer:';
$txt['pmx_settings_hidecopyright'] = 'PortaMx copyright removal codekey:';
$txt['pmx_settings_enable_xbars'] = 'Panels to collapse/expand with <b>xBars</b>:';
$txt['pmx_settings_all_toggle'] = 'Toggle all on/off';
$txt['pmx_settings_enable_xbarkeys'] = 'Enable panel <b>xBarKeys</b>:';
$txt['pmx_settings_collapse_visibility'] = 'Collapse the <b>Dynamic visibility</b> panel:';
$txt['pmx_settings_disableHS'] = 'Disable HighSlide viewer globally:';
$txt['pmx_settings_noHS_onaction'] = 'Disable HighSlide viewer on actions:';
$txt['pmx_settings_mainoverflow'] = 'Enable Forum area horizontal scroll on overflow:';
$txt['pmx_settings_enable_xbars'] = 'Panels to collapse/expand with <b>xBars</b>:';
$txt['pmx_settings_postcountacs'] = 'Use Post count based groups for access settings:';
$txt['pmx_settings_shrinkimages'] = 'Handling of block and panel expand / collapse:';
$txt['pmx_settings_shrink'] = array(
	0 => 'Show default images',
	1 => 'Show theme dependent images',
	2 => 'No image, use the titlebar');

// ecl
$txt['pmx_settings_ecl'] = 'Enable the ECL mode:';
$txt['pmx_settings_eclhelp'] = 'This make SMF and PortaMx compatible with the <b>EU Cookie Law</b>.<br >
	If enabled, any visitor (except spider) must accept the storage of cookies before he can browse the Webseite.<br />
	More information you find on <a href="http://ec.europa.eu/ipg/standards/cookies/index_en.htm" target="_blank">European Commission</a>';
$txt['pmx_settings_eclmodal'] = 'Use ECL none modal mode:';
$txt['pmx_settings_eclhelpmodal'] = 'On modal mode, Portal and Forum are not accessible until ECL is accepted.
	If you enable the none modal mode, the site is accessible and only a small ECL accept overlay is shown at the top of the site.
	<b>Note, that is this case any additional modification or adsense content can store cookies!</b><br />
	This setting has no effect when a mobile device or WAP/WAP2/IMODE was detected.';

// panels
$txt['pmx_panel_settings'] = 'Panel settings';
$txt['pmx_settings_panelset'] = 'Settings';
$txt['pmx_settings_panelhead'] = 'Head panel';
$txt['pmx_settings_panelleft'] = 'Left panel';
$txt['pmx_settings_panelright'] = 'Right panel';
$txt['pmx_settings_paneltop'] = 'Top center panel';
$txt['pmx_settings_panelbottom'] = 'Bottom center panel';
$txt['pmx_settings_panelfoot'] = 'Foot panel';
$txt['pmx_settings_panelhidetitle'] = 'Hide the panel on section:';
$txt['pmx_settings_panel_customhide'] = 'Hide the panel on action:';
$txt['pmx_settings_panel_collapse'] = 'Disable panel collapse:';
$txt['pmx_settings_panelwidth'] = 'Width of panel:';
$txt['pmx_settings_panelheight'] = 'Max height of panel:';
$txt['pmx_pixel'] = 'Pixel';

$txt['pmx_settings_collapse_state'] = 'Entry panel state:';
$txt['pmx_settings_collapse_mode'] = array(
	0 => 'default',
	1 => 'collapsed',
	2 => 'expanded');
$txt['pmx_hw_pixel'] = array(
	'head' => 'Pixel or leave blank',
	'top' => 'Pixel or leave blank',
	'bottom' => 'Pixel or leave blank',
	'foot' => 'Pixel or leave blank',
	'left' => 'Pixel',
	'right' => 'Pixel'
);
$txt['pmx_settings_hidehelp'] = 'To hide the panel, select or unselect one or more options by hold down the <b>Ctrl Key</b> and <b>click</b> on the items.<br />
	To toggle between <b>Show and Hide</b>, hold down the <b>Ctrl Key</b> and take a <b>double click</b> (IE needs three clicks!) on the item.
	If a item set to <b>Hide</b> the symbol <b>^</b> is shown at the front.<br />
	<b>Select example</b>: On "Admin" the panel is hidden only on <i>Admin</i>, on "^Admin" the panel is always hidden, but not on <i>Admin</i>';

// Frontpage
$txt['pmx_frontpage_settings'] = 'Frontpage settings';
$txt['pmx_settings_frontpage_none'] = 'No Frontpage, go directly to Forum:';
$txt['pmx_settings_frontpage_centered'] = 'Show the Frontpage in the Forum area:';
$txt['pmx_settings_frontpage_fullsize'] = 'Show a full size Frontpage:';
$txt['pmx_settings_pages_hidefront'] = 'Hide Frontpage blocks on Pages, Categories or Articles:';
$txt['pmx_settings_frontpage_menubar'] = 'Enable Menubar on full size Frontpage:';
$txt['pmx_settings_index_front'] = 'Enable the Frontpage indexing for spider:';
$txt['pmx_settings_restoretop'] = 'Restore the browser vertical page position:';
$txt['pmx_settings_sendfragment'] = 'Move the browser page to the block top position:';
$txt['pmx_settings_fronttheme'] = 'Choice a theme for the Frontpage:';
$txt['pmx_settings_frontthemepages'] = 'Use this theme also for Single pages, Categories or Articles:';
$txt['pmx_front_default_theme'] = '[ Forum default ]';

// manager control
$txt['pmx_global_program'] = 'Manager control settings';
$txt['pmx_settings_blockfollow'] = 'Follow the block thru the panels on change:';
$txt['pmx_settings_quickedit'] = 'Show a <b>quick edit link</b> on the block titlebar:';
$txt['pmx_settings_adminpages'] = 'Panels on which a PortaMx Moderator has access:';
$txt['pmx_settings_article_on_page'] = 'Number of Articles on the Manager overview page:';
$txt['pmx_settings_enable_promote'] = 'Enable the Promote messages feature:';
$txt['pmx_settings_promote_messages'] = 'Currently promoted messages:';

// access settings
$txt['pmx_access_settings'] = 'Access settings';
$txt['pmx_access_promote'] = 'Membergroups that can promote posts:';
$txt['pmx_access_articlecreate'] = 'Membergroups that can create and write articles:';
$txt['pmx_access_articlemoderator'] = 'Membergroups that can moderate and approve articles:';
$txt['pmx_access_blocksmoderator'] = 'Membergroups that can moderate blocks in enabled panels:';
$txt['pmx_access_pmxadmin'] = 'Membergroups that can Administrate the entire Portal:';

// pmxsef settings
$txt['pmx_sef_engine'] = '<b>The SEF engine requires mod_rewrite or URL Rewrite/web.config (IIS7) support.</b>';
$txt['pmx_sef_settings'] = 'Search Engine Friendly URL (SEF) Manager';
$txt['pmx_sef_enable'] = 'Enable the SEF engine:';
$txt['pmx_sef_lowercase'] = 'Lowercase alls URLs:';
$txt['pmx_sef_autosave'] = 'Save new actions automatically:';
$txt['pmx_sef_spacechar'] = 'Character to used for spaces:';
$txt['pmx_sef_stripchars'] = 'Character they removed from the url:';
$txt['pmx_sef_wirelesss'] = 'All WIRELESS token:';
$txt['pmx_sef_single_token'] = 'All single token:';
$txt['pmx_sef_actions'] =  'All actions of your forum:';
$txt['pmx_sef_simplesef_space'] = 'Space character you used for SimpleSEF:';
$txt['pmx_sef_engine_disabled'] = 'The SEF engine is currently switched off.';
$txt['pmx_sef_ignoreactions'] =  'Actions they ignored:';
$txt['pmx_sef_aliasactions'] =  'Alias for actions:';
$txt['pmx_sef_ignorerequests'] =  'Parts of a URL they ignored:';

// DO NOT CHANGE ANY OF THIS !!
$txt['pmx_sef_engine_APcode'] = 'RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?q=$1 [L,QSA]';

$txt['pmx_sef_engine_IScode'] = '<?xml version="1.0" encoding="UTF-8"?>
<configuration>
 <system.webServer>
	<rewrite>
	 <rules>
		<rule name="PortaMxSEF">
		 <match url="^(.*)$" ignoreCase="false" />
		 <conditions logicalGrouping="MatchAll">
			<add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" pattern="" ignoreCase="false" />
			<add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" pattern="" ignoreCase="false" />
		 </conditions>
		 <action type="Rewrite" url="index.php?q={R:1}" appendQueryString="true" />
		</rule>
	 </rules>
	</rewrite>
 </system.webServer>
</configuration>';
// END NO CHANGE

// help;
$txt['pmx_sef_engine_APIS_copy'] = 'Click to select the text, then copy the text with Ctrl-Insert.';
$txt['pmx_sef_enable_help'] = 'If you can\'t enable the SEF engine, please check if the <b>.htaccess</b> or the <b>web.config</b> file exist and at the right place.';
$txt['pmx_sef_engine_helpAP'] = 'If you have an Apache webserver, or one that uses .htaccess and has mod_rewrite functionality, you need a <b>.htaccess</b> file in your main SMF directory with the following:';
$txt['pmx_sef_engine_helpIS'] = '<br />If you have a IIS7 webserver, you need a <b>web.config</b> file in your main SMF directory with the following:';
$txt['pmx_sef_lowercase_help'] = 'If checked all url\'s converted to lower case letters.
	You should enable this for better results.';
$txt['pmx_sef_autosave_help'] = 'If checked all new actions are automatically saved.
	It\'s recommeded to disable this option and add new actions manually.
	On not existing actions the Frontpage is shown.';
$txt['pmx_sef_spacechar_help'] = 'Character to be used for spaces in the url (typically _ or -).
	You should use <b>-</b> for better results. Leave this empty to remove any space.';
$txt['pmx_sef_stripchars_help'] = 'These characters will be stripped out of url\'s.
	Each char must be sepatated by a comma.
	<b>If you change this, it may be that your forum not works properly.<b>';
$txt['pmx_sef_wirelesss_help'] = 'These token are used in WIRELESS mode.
	Each entry must be sepatated by a comma.
	<b>If you change this, it may be that your forum not works properly.<b>';
$txt['pmx_sef_single_token_help'] = 'Token they have no value and need a special handling.
	Each entry must be sepatated by a comma.
	<b>If you change this, it may be that your forum not works properly.<b>';
$txt['pmx_sef_actions_help'] = 'These are all of the actions of your forum and normally you do not have to modify this, because the SEF engine add not listed actions automatically.
	Each entry must be sepatated by a comma.
	<b>If you change this, it may be that your forum not works properly.<b>';
$txt['pmx_sef_simplesef_space_help'] = 'If you have used SimpleSEF before, enter here the character you have used for spaces (typically _ or -).
	We need this, to convert SimpleSEF "simple url\'s" (like <i>topic_##, board_##</i>) to PortaMxSEF url\'s.
	Leave this empty, if you not have SimpleSEF url\'s in you forum.';
$txt['pmx_sef_ignoreactions_help'] =  'Actions they not converted by the SEF engine. Each entry must be sepatated by a comma.';
$txt['pmx_sef_aliasactions_help'] =  'You can define a alias for any actions. Each alias must be in the format <b>action=alias</b>. Each action=alias pair must be sepatated by a comma.';
$txt['pmx_sef_ignorerequests_help'] =  'Parts of a URL they not converted by the SEF engine. Each part must be in the format <b>name=value</b>. Each name=value pair must be sepatated by a comma.';
$txt['pmx_settings_index_front_help'] = 'If checked, the Frontpage content can be indexed by spiders like google.';
$txt['pmx_settings_restoretop_help'] = 'The browser vertically page position is restored to the previous position on many request like change the page, category or article.';
$txt['pmx_settings_sendfragment_help'] = 'The browser vertically page position is set to Block top position (by send a "#top" fragment) on many request like change the page, category or article.';
$txt['pmx_access_promote_help'] = 'Members in the selected groups can promote posts in the forum.<br />
	<b>Granted rights:</b> <i>Add and remove promote to posts</i>';
$txt['pmx_access_articlecreate_help'] = 'Members in the selected groups can write articles and edit or delete his own articles.
	Articles the created by this membergroups must be approved by a Article Moderator or Administrator.<br />
	<b>Granted rights:</b> <i>Full article edit, clone, delete, activate/deactivate own articles</i>';
$txt['pmx_access_articlemoderator_help'] = 'Members in the selected groups can edit and approve articles they enabled for <b>Moderate Article</b>.
	This is always given, if a article created by the Article create groups.<br />
	<b>Granted rights:</b> <i>Full article edit, activate/deactivate, approve/unapprove</i>';
$txt['pmx_access_blocksmoderator_help'] = 'Members in the selected groups can edit blocks they enabled for <b>Moderate Blocks</b>.
	The access to the blocks is limited by the enabled panels (see Manager settings).<br />
	<b>Granted rights:</b> <i>Edit the content, access, title, css settings, activate/deactivate</i>';
$txt['pmx_access_pmxadmin_help'] = 'Members in the selected groups have <b>full access</b> to all parts of the entire Portal.
	The Members have the same rights as a Forum Admin, but limited to the Portal. <b>Handle this with care !</b>';
$txt['pmx_settings_noHS_onactionhelp'] = 'Here you can define actions (separated by a comma), on they the HighSlide viewer is disabled.
	For the <b>SMF Media Gallery</b> as example, enter <b>mgallery</b>.';
$txt['pmx_frontpage_help'] = 'Select the Frontpage, which you use.<br />
	Note, that the full size Frontpage normally have <b>no</b> Menubar, but you can enable a small Menubar.<br />
	Single pages are always displayed, even if the Frontpage set to "no Frontpage".<br />
	If you need a additional CSS for the full size Frontpage, create a CSS file (<b>frontpage.css</b>) and save it to the directory of the theme.';
$txt['pmx_settings_adminpageshelp'] = 'Members in the <b>PortaMx Moderator group</b> can change the settings on the overview and edit the content of the block.<br />
	<b>Handle this option with care!</b>';
$txt['pmx_settings_xbars_help'] = 'Select the panels, they you can collapse or expand with the xBars.';
$txt['pmx_settings_collapse_vishelp'] = 'The panel is used in Block settings. You can collapse that initially, but it\'s shown always if the Block have dynamic visibility options.';
$txt['pmx_settings_xbarkeys_help'] = 'If checked, you can collapse or expand the left, right, top, bottom panels with the <b>Ctrl key</b> and a arrow key (<b>left, right, up, down</b>) and the head, foot panel with the <b>Alt key</b> and a arrow key (<b>up, down</b>). Note that the <b>xBarKeys</b> are disabled if the editor loaded.';
$txt['pmx_settings_blockcachestatshelp'] = 'If enabled the pmx-cache status is shown in the footer above the page load.';
$txt['pmx_settings_hidecopyrighthelp'] = 'Enter the codekey as you received. If the key valid for your domain and not expired, the PortaMx copyright is not shown.
	Please use copy and paste (the key is longer as the inputfield) to put in the correct code.';
$txt['pmx_settings_panel_custhelp'] = 'Here you can enter any other actions.
	For <b>Single pages, Articles and Categories</b> we use a prefix (<b>p:</b> for Single pages, <b>a:</b> for Articles and <b>c:</b> for Categories).
	Enter the prefix before the page, article or category name, as example <b>p:mypage</b>.
	You can use names with the wildcards <b>*</b> and <b>?</b>. In this case the panel is invisible, whose name matched.
	Furthermore you can also use subaction, these starts alway with a ampersand (<b>&amp;</b>) like <b>&amp;subactionname=value</b>.
	For more detailed informations about the customer actions read our documentation.';
$txt['pmx_settings_downloadhelp'] = 'If checked, a <b>Download</b> button is shown next to the <b>Forum</b> button.';
$txt['pmx_settings_dl_actionhelp'] = 'Define the action which the download button to be assigned.<br />
	You can use any name with the character (<b>a-z, A-Z, 0-9, -, _, .</b>).<br />For Single pages, Articles and Categories you have to add a prefix before the name
	(<b>p:</b> for Single pages, <b>a:</b> for Articles and <b>c:</b> for Categories) as example <b>p:download</b>';
$txt['pmx_settings_other_actionshelp'] = 'Enter one or more request names (separated by comma) they are handled as Forum requests.
	You can enter <b>name=value</b> pairs like <b>project=1</b> for the Project tool.';
$txt['pmx_settings_blockfollowhelp'] = 'If you make any change on a block in the Overview screen or you move a block, the screen is switched to the panel in which the block is located or moved.';
$txt['pmx_settings_quickedithelp'] = 'You can enable a direct link to the Manager <b>edit function</b>.
	The links is associated to the <b>title</b> and is active only for Admins and Portal Admins.';
$txt['pmx_settings_pages_help'] = 'Enter names for Singe Pages, Categories and Articles (separated by comma), for which you will hide the Frontpage blocks.
	Leave this empty, if you want to place Frontpage block individually with the block settings.
	Use the prefix <b>p:</b> for Single pages, <b>a:</b> for Articles and <b>c:</b> for Categories.
	Also you can use names with the wildcards <b>*</b> and <b>?</b>.';
$txt['pmx_settings_article_on_pagehelp'] = 'Enter the number of Articles you will see in the Article Manager overview page';
$txt['pmx_settings_forumscrollhelp'] = 'If the forum area is wider than the space between the left and right panel, is usually the right panel on the screen moved.
	If this option is chosen, the forum section is not expanded, but may be rolled horizontally.<br />
	<b>Note, that this don\'t work on IE less then version 8.</b>';
$txt['pmx_settings_postcountacshelp'] = 'Use the SMF Post count based groups for the block access, additional to the Regular groups.';
$txt['pmx_settings_teasermode'] = array(
	0 => 'Choose the counting method for the Post teaser:',
	1 => 'Count words',
	2 => 'Count characters'
);
$txt['pmx_settings_pmxteasecnthelp'] = 'In different blocks a <i>Post teaser</i> is used.
	Here you can set, as the teaser is supposed to work.
	For languages that do not use spaces between words, the setting, <b>Count characters</b> is suggest.';
$txt['pmx_settings_noHS_onfrontpage'] = 'Disable HighSlide viewer on Frontpage only:';
$txt['pmx_settings_promote_messages_help'] = 'You see all promoted message id\'s and you can add or remove message id\'s. Note that each id is separated by a comma.';
$txt['pmx_settings_enable_promote_help'] = 'If checked the Promote function is enabled and you see a <b>Promote message</b> link belove each message. If the message already promoted, the link is show as <b>Clear Promote</b>.';
?>
