<?php
/**
* \file Admin.english-utf8.php
* Language file Admin.english-utf8
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2015 by PortaMx corp. - http://portamx.com
* \version 1.54
* \date 18.11.2015
*/

// Admin Center
$txt['pmx_admin_center'] = 'PortaMx Administration Center';
$txt['pmx_admin_main_welcome'] = 'This is your "'. $txt['pmx_admin_center'] .'".
	From here, you can edit settings and maintain your blocks.
	You may also find answers to your questions by clicking the Help symbols %s for more information on the related functions.';

$txt['pmx_center_news'] = 'Live information from PortaMx corp.';
$txt['pmx_center_support'] = 'PortaMx Support informations';
$txt['pmx_center_versioninfo'] = '<b>Version information</b>';
$txt['pmx_center_installed'] = 'Installed PortaMx version: ';
$txt['pmx_center_version'] = 'Current PortaMx version: ';
$txt['pmx_center_download'] = '<b>Download the current version</b>';
$txt['pmx_center_update'] = '<b>Install now !</b>';
$txt['pmx_center_detailed'] = '[<b>More detailed information</b>]';
$txt['pmx_center_nolivedata'] = 'Can\'t read information from PortaMx document server';
$txt['pmx_center_mansettings'] = '<b>Settings Manager</b>';
$txt['pmx_center_mansettings_desc'] = 'Change or set options for the Panels, the Frontpage and for the Block Manager.';
$txt['pmx_center_manblocks'] = '<b>Block Manager</b>';
$txt['pmx_center_manblocks_desc'] = 'Change or set options on the Blocks, create new, move between panels and delete.';
$txt['pmx_center_manlangs'] = '<b>Language Manager</b>';
$txt['pmx_center_manlangs_desc'] = 'Search for additional languages, Install new, Update or Remove installed languages.';
$txt['pmx_center_error'] = 'An error has occurred!';
$txt['pmx_center_mancategories'] = '<b>Category Manager</b>';
$txt['pmx_center_mancategories_desc'] = 'Change or set options for Categories, create new, move, edit and delete.';
$txt['pmx_center_manarticles'] = '<b>Article Manager</b>';
$txt['pmx_center_manarticles_desc'] = 'Change or set options for Articles, create new, move, edit and delete.';
$txt['pmx_center_mansefengine'] = '<b>SEF Manager</b>';
$txt['pmx_center_mansefengine_desc'] = 'Change or set options for the Search Engine Friendly URL (SEF) Engine.';

// File check
$txt['pmx_center_vercheck'] = 'Detailed Version Check';
$txt['pmx_center_vercheck_info'] = 'This shows you the versions of your installation\'s files versus those of the latest version.
	If any of these files are out of date, you should download and upgrade to the latest version.';
$txt['pmx_filepackage'] = 'PortaMx package';
$txt['pmx_source_files'] = 'Source files';
$txt['pmx_template_files'] = 'Template files';
$txt['pmx_language_files'] = 'Language files';
$txt['pmx_center_file'] = '<b>PortaMx files</b>';
$txt['pmx_center_filecurrent'] = '<b>Current Version</b>';
$txt['pmx_center_fileinstalled'] = '<b>Installed Version</b>';
$txt['pmx_center_filename'] = '<b>Filename</b>';
$txt['pmx_center_fileversion'] = '<b>File Version</b>';
$txt['pmx_center_filedate'] = '<b>File Date</b>';

// Language manager
$txt['pmx_center_showlang'] = 'Language Manager';
$txt['pmx_center_showlang_info'] = 'This shows you the installed languages.
	You can add new languages, update or remove installed languages.';
$txt['pmx_processing'] = '<b>Processing</b>';
$txt['pmx_center_langinstalled'] = 'Installed languages';
$txt['pmx_center_langname'] = 'Language Name';
$txt['pmx_center_langcharset'] = 'Character Set';
$txt['pmx_center_langversion'] = 'Version';
$txt['pmx_center_langaction'] = 'Action to do';
$txt['pmx_center_langavailable'] = 'Available languages on PortaMx.com';
$txt['pmx_center_fetchlang_failed'] = 'Can\'t read language data from PortaMx';
$txt['pmx_center_langinstall'] = 'Install';
$txt['pmx_center_langupdate'] = 'Update';
$txt['pmx_center_langreplace'] = 'Replace';
$txt['pmx_center_langdelete'] = 'Delete';
$txt['pmx_confirm_langdelete'] = 'Are you sure you want to delete this language?';
$txt['pmx_confirm_langreplace'] = 'The language which you want to install is already exist. Replace?';
$txt['pmx_confirm_langupdate'] = 'Do you want to replace the installed language by the newer version?';
$txt['pmx_center_langdelfailed'] = 'Can\'t delete all files, because one or more files are not removable.';
$txt['pmx_center_langinstfailed'] = 'Can\'t install or update all files, because one or more files are not writable.';
$txt['pmx_center_langfetchfailed'] = 'Can\'t retrieve language install informations from source Server.';
$txt['pmx_center_langdelerror'] = 'An error has occurred on language delete!';
$txt['pmx_center_langinsterror'] = 'An error has occurred on language install!';
$txt['pmx_center_manuallylang'] = 'Manually installable languages';

// AdminBlocks
$txt['pmx_admBlk_desc'] = 'Manage your blocks with edit, move, create or delete.';
$txt['pmx_blocks_mod'] = 'PortaMx Block Manager moderation';
$txt['pmx_admBlk_panels'] = array(
	'all' => 'Panel overview',
	'head' => 'Head',
	'top' => 'Top center',
	'left' => 'Left',
	'right' => 'Right',
	'bottom' => 'Bottom center',
	'foot' => 'Foot',
	'front' => 'Frontpage',
	'pages' => 'Single Pages',
	'bib' => 'BiB Blocks',
);
$txt['pmx_admBlk_sides'] = array(
	'head' => 'Head Panel',
	'top' => 'Top center Panel',
	'left' => 'Left Panel',
	'right' => 'Right Panel',
	'bottom' => 'Bottom center Panel',
	'foot' => 'Foot Panel',
	'front' => 'Frontpage',
	'pages' => 'Single Pages',
	'bib' => 'BiB Blocks',
);

// default for access
$txt['pmx_guest'] = 'Guests';
$txt['pmx_ungroupedmembers'] = 'Regular Members';

// panel / block overflow actions
$txt['pmx_overflow_actions'] = array(
	'' => 'none',
	'auto' => 'Let\'s do the Browser',
	'hidden' => 'Clip at frame');

// actions for panels and blocks
$txt['pmx_action_names'] = array(
	'frontpage' => 'Frontpage',
	'pages' => 'Pages',
	'articles' => 'Articles',
	'categories' => 'Categories',
	'community' => 'Community',
	'boards' => 'Boards',
	'topics' => 'Topics',
	'admin' => 'Admin',
	'calendar' => 'Calendar',
	'help' => 'Help',
	'login,login2,reminder' => 'Login',
	'logout' => 'Logout',
	'moderate' => 'Moderate',
	'mlist' => 'Memberlist',
	'pm' => 'Pers. Messages',
	'post' => 'Post Topic',
	'profile' => 'Profile',
	'recent' => 'Recent posts',
	'register,register2' => 'Register',
	'stats' => 'Show stats',
	'search,search2' => 'Search',
	'unread' => 'Unread posts',
	'unreadreplies' => 'Unread replies',
	'who' => 'Who',
	'helpdesk' => 'Simpledesk Modfication',
);

// Device settings
$txt['pmx_blocks_devices'] = 'Visibility on device types';
$txt['pmx_blocks_deviceshelp'] = 'Select the type of device on which the block is to be displayed.';
$txt['pmx_settings_devices'] = 'Panel visibility on device types:';
$txt['pmx_settings_deviceshelp'] = 'Select the type of device on which the panel is to be displayed.';
$txt['pmx_devices']['all'] = 'Show on all devices';
$txt['pmx_devices']['desk'] = 'Show only on Desktop devices';
$txt['pmx_devices']['mobil'] = 'Show only on Mobile devices';

// default settings
$txt['pmx_defaultsettings'] = 'This block type have no settings.';
$txt['pmx_default_header_none'] = 'Not show';
$txt['pmx_default_header_asbody'] = 'as body';
$txt['pmx_default_none'] = 'none';
$txt['pmx_enable_sitemap'] = 'Enable for Sitemap extension:';

$txt['pmx_information_icon'] = 'Click for more Information';
$txt['pmx_actionfault'] = 'Illegal request reached!';
$txt['pmx_sysblock'] = 'Systemblock';
$txt['pmx_userblock'] = 'Userblock';

// popups
$txt['pmx_category_popup'] = 'Category settings';
$txt['pmx_article_information'] = 'Detailed informations for article id %s';
$txt['pmx_click_edit_ttl'] = 'Click to edit the title';
$txt['pmx_edit_titles'] = 'Edit title settings';

// Buttons
$txt['pmx_save'] = 'Save';
$txt['pmx_create'] = 'Create';
$txt['pmx_save_exit'] = 'Save &amp; Exit';
$txt['pmx_save_cont'] = 'Save &amp; Continue';
$txt['pmx_savechanges'] = 'Save changes';
$txt['pmx_cancel'] = 'Cancel';
$txt['pmx_update_save'] = 'Update';
$txt['pmx_update_all'] =  'Update ALL';
$txt['pmx_update_blocks'] = 'ALL %s Blocks';

// Article Manager
$txt['pmx_articles'] = 'Article Manager';
$txt['pmx_articles_desc'] = 'Manage your articles with create, edit, clone or delete.
	On the overview you have also a lot of quick functions to edit the elements.
	Click on the title, on the category or on the member icon in the functions.
	To show detailed article infos, click on the type field.';

// overview
$txt['pmx_articles_overview'] = 'Article overview';
$txt['pmx_articles_add'] = 'Add new article';
$txt['pmx_articles_title'] = 'Title';
$txt['pmx_articles_type'] = 'Type';
$txt['pmx_articles_catname'] = 'Category';
$txt['pmx_edit_article'] = 'Click to edit this article';
$txt['pmx_articles_info'] = 'Click for detailed informations';
$txt['pmx_chg_articlnocats'] = 'No category';
$txt['pmx_chg_articlcats'] = ' - click to change';
$txt['pmx_status_activ'] = 'Active';
$txt['pmx_status_inactiv'] = 'Inactive';
$txt['pmx_status_change'] = 'Click to change';

$txt['pmx_rowmove_title'] = 'Set new Article position';
$txt['pmx_rowmove'] = 'Move Article';
$txt['pmx_rowmove_to'] = 'Article';
$txt['pmx_rowmove_place'] = 'to the position';
$txt['pmx_rowmove_before'] = 'before';
$txt['pmx_rowmove_after'] = 'after';
$txt['pmx_rowmove_updown'] = 'Click to move Article position';
$txt['pmx_rowmove_error'] = 'You can\'t move the article to itself !';

$txt['pmx_chg_articleaccess'] = 'Click to change the article access';
$txt['pmx_clone_article'] = 'Click to clone this article';
$txt['pmx_delete_article'] = 'Click to delete this article';
$txt['pmx_confirm_articledelete'] = 'Are you sure you want to delete this article?';
$txt['pmx_confirm_articlclone'] = 'You want to clone this article?';
$txt['pmx_article_groupaccess'] = 'Article have access settings';
$txt['pmx_article_modaccess'] = 'Article have moderate access';
$txt['pmx_article_cssfile'] = 'Article have own style sheet file';
$txt['pmx_article_approved'] = 'Article is approved';
$txt['pmx_article_not_approved'] = 'Article is not approved';

$txt['pmx_article_filter'] = 'Click to set a article filter';
$txt['pmx_article_setfilter'] = 'Setup article filter';
$txt['pmx_article_filter_category'] = 'Show category(s):';
$txt['pmx_article_filter_categoryClr'] = 'clear';
$txt['pmx_article_filter_approved'] = 'Show unapproved articles:';
$txt['pmx_article_filter_active'] = 'Show inactive articles:';
$txt['pmx_article_filter_myown'] = 'Show my own articles:';
$txt['pmx_article_filter_member'] = 'Show articles from:';
$txt['pmx_article_filter_membername'] = '(Member name)';

// info
$txt['pmx_article_info_reqname'] = '<b>Article name:</b> %s';
$txt['pmx_article_info_not_defined'] = 'not defined';
$txt['pmx_article_info_access'] = '<b>Accessible for:</b> %s';
$txt['pmx_article_info_created'] = '<b>Created:</b> %s, <b>by</b> %s';
$txt['pmx_article_info_updated'] = '<b>Updated:</b> %s, <b>by</b> %s';
$txt['pmx_article_info_not_updated'] = '<b>Not updated</b>';
$txt['pmx_article_info_approved'] = '<b>Approved:</b> %s, <b>by</b> %s';
$txt['pmx_article_info_not_approved'] = '<b>Not approved</b>';
$txt['pmx_article_info_activated'] = '<b>Activated:</b> %s';
$txt['pmx_article_info_not_activated'] = '<b>Not activated</b>';
$txt['pmx_article_info_preview'] = '<b>Show the article content</b>';

// Article types
// do not change the keys of this array !!
$txt['pmx_articles_types'] = array(
	'html' => 'HTML',
	'code' => 'Script',
	'bbc' => 'BBC Script',
	'php' => 'PHP',
);

// edit
$txt['pmx_article_edit'] = 'Edit article';
$txt['pmx_article_title'] = 'Article title:';
$txt['pmx_article_type'] = 'Article type:';
$txt['pmx_article_cats'] = 'Category:';
$txt['pmx_article_name'] = 'Article name:';
$txt['pmx_article_settings_title'] = 'Article settings';
$txt['pmx_article_groups'] = 'Access settings';
$txt['pmx_article_moderate_title'] = 'Article moderation';
$txt['pmx_article_moderate'] = 'Enable article moderation:';
$txt['pmx_article_teaser'] = 'Number of %s before tease:';
$txt['pmx_article_teasehelp'] = 'Enter 0 for no tease';
$txt['pmx_article_footer'] = 'Show Author, Date, last Update:';
$txt['pmx_article_footerhelp'] = 'If checked, the Article author, date created and last update is show below the article';

// access popup
$txt['pmx_acs_repl'] = 'Replace groups';
$txt['pmx_acs_add'] = 'Add groups';
$txt['pmx_acs_rem'] = 'Remove groups';

// edit help
$txt['pmx_article_moderatehelp'] = 'If checked, all member in the Article Moderator Group can edit, delete and approve this article.';
$txt['pmx_article_groupshelp'] = 'Choose your membergroups that will able to see this article.<br />
	You can also use <b>deny group</b>. This is useful when a user is in more than one group, but one of the groups should not see the block.<br />
	To toggle between deny groups and access groups, hold down the <b>Ctrl Key</b> and <b>double click</b> on the item.
	If the group a deny group,  you see the deny symbol <b>^</b> before the group name.';

// Categories Manager
$txt['pmx_categories'] = 'Category Manager';
$txt['pmx_categories_desc'] = 'Manage your categories with create, edit, move, clone or delete.
	On the overview you have also a lot of quick functions to edit the elements.
	Click on the title or on the member icon in the functions.';

// overview
$txt['pmx_categories_overview'] = 'Category overview';
$txt['pmx_categories_add'] = 'Add new category';
$txt['pmx_categories_name'] = 'Category name';
$txt['pmx_categories_order'] = 'Order';
$txt['pmx_categories_level'] = 'Level';
$txt['pmx_edit_categories'] = 'Click to edit this category';
$txt['pmx_clone_categories'] = 'Click to clone this category';
$txt['pmx_move_categories'] = 'Click to move this category';
$txt['pmx_editname_categories'] = ' - Click to edit the name';
$txt['pmx_categories_showarts'] = 'Articles in the category';
$txt['pmx_delete_categories'] = 'Click to delete this category';
$txt['pmx_chg_categoriesaccess'] = 'Click to change category access';
$txt['pmx_confirm_categoriesdelete'] = 'Are you sure you want to delete this category?';
$txt['pmx_confirm_categoriesclone'] = 'You want to clone this category?';
$txt['pmx_categories_groupaccess'] = 'Category have access settings';
$txt['pmx_categories_cssfile'] = 'Category have own style sheet file';
$txt['pmx_categories_articles'] = 'Category have %s article(s)';
$txt['pmx_categories_none'] = '[none]';
$txt['pmx_categories_setname'] = 'Change category name';
$txt['pmx_update_all'] =  'Update ALL';
$txt['pmx_savechanges'] = 'Save changes';

// popups
$txt['pmx_categories_popup'] = 'Category settings';
$txt['pmx_categories_movecat'] = 'Set new Category position';
$txt['pmx_categories_move'] = 'Move Category';
$txt['pmx_categories_moveplace'] = 'to the position';
$txt['pmx_categories_tomovecat'] = 'Category';
$txt['pmx_categories_move_error'] = 'You can\'t move a category to itself !';

// cat infos
$txt['pmx_categories_root'] = 'Root category';
$txt['pmx_categories_rootchild'] = 'Root category with child\'s';
$txt['pmx_categories_childchild'] = 'Child of category &quot;%s&quot; with child\'s';
$txt['pmx_categories_child'] = 'Child of category &quot;%s&quot;';

// Category placement
// do not change the keys of this array !!
$txt['pmx_categories_places'] = array(
	'before' => 'before',
	'child' => 'as child of',
	'after' => 'after',
);

// showmodes
$txt['pmx_categories_modsidebar'] = 'Show the first article and all titles in a sidebar:';
$txt['pmx_categories_modpage'] = 'Show all articles in one page:';
$txt['pmx_categories_modpage_count'] = 'Number of articles in a page:';
$txt['pmx_categories_modpage_pageindex'] = 'Show page index always:';
$txt['pmx_categories_addsubcats'] = 'Add subcategories to the sidebar:';
$txt['pmx_categories_showsubcats'] = 'Show subcategories in a sidebar:';
$txt['pmx_categories_sidebarwith'] = 'Width of sidebar (Pixel):';
$txt['pmx_categorie_inherit'] = 'Inherit Category access to Articles:';
$txt['pmx_categorie_articlesort'] = 'Sort articles by:';
$txt['pmx_categories_sidebaralign'] = 'Sidebar align:';
// do not change the keys of this array !!
$txt['pmx_categories_sbalign'] = array(
	0 => 'Left',
	1 => 'Right',
);

// Articles sort mode
// do not change the keys of this array !!
$txt['pmx_categories_artsort'] = array(
	'id' => 'Article ID',
	'name' => 'Article name',
	'created' => 'Date created',
	'updated' => 'Date updated',
	'active' => 'Date activated',
	'approved' => 'Date approved',
	'owner' => 'Article owner',
);
$txt['pmx_artsort'] = array(
	0 => ' (dec)',
	1 => ' (asc)'
);

// Categories/Articles show mode
// do not change the keys of this array !!
$txt['pmx_categories_showmode'] = array(
	'both' => 'Show Titlebar/Frame for Category and Articles:',
	'article' => 'Hide Titlebar/Frame for Category, show Titlebar/Frame for Articles:',
	'category' => 'Show Titlebar/Frame for Category, hide Titlebar/Frame for Articles:',
	'none' => 'Hide Titlebar/Frame for Category and Articles:',
);

// edit
$txt['pmx_categories_edit'] = 'Edit category';
$txt['pmx_categories_title'] = 'Category title:';
$txt['pmx_categories_type'] = 'Place category:';
$txt['pmx_categories_cats'] = 'Category:';
$txt['pmx_categories_settings_title'] = 'Category settings';
$txt['pmx_categories_groups'] = 'Access settings';
$txt['pmx_categories_globalcat'] = 'Global category access';
$txt['pmx_categorie_global'] = 'Disable global use:';
$txt['pmx_categorie_request'] = 'Disable category request:';

// edit help
$txt['pmx_categories_groupshelp'] = 'Choose your membergroups that will able to see this category.<br />
	You can also use <b>deny group</b>. This is useful when a user is in more than one group, but one of the groups should not see the category.<br />
	To toggle between deny groups and access groups, hold down the <b>Ctrl Key</b> and <b>double click</b> on the item.
	If the group a deny group,  you see the deny symbol <b>^</b> before the group name.';
$txt['pmx_categories_sorthelp'] = 'You can sort the articles in this category with variable values.
	If you choice more then one sort option, these are logically XOR-ed (the result is true, if <b>one</b> option true, else the result is false).</br >
	To select more then one sort option, hold down the <b>Ctrl Key</b> and click on the items.
	To toggle between ascending and descending sort, hold down the <b>Ctrl Key</b> and <b>double click</b> the item.
	For a descending sort the symbol <b>^</b> is shown before the sort option.';
$txt['pmx_categories_inherithelp'] = 'if checked, the category permissions is inherit to the article.
	This is done even, if the permission on the article are higher as on the category.';
$txt['pmx_categories_gloablcathelp'] = 'If you disable a category for global use, the category is invisible for Members in the Article Writer and the Article Moderator group.';
$txt['pmx_categorie_requesthelp'] = 'If you check this option, only a Forum Admin and a Portal Admin can request these category.';

// common edit for Blocks, Articles, Categories
$txt['pmx_editblock'] = 'Edit Block ';
$txt['pmx_edit_title'] = 'Block title:';
$txt['pmx_edit_title_lang'] = 'Language:';
$txt['pmx_edit_title_align'] = 'Align:';
$txt['pmx_edit_pagename'] = 'Page name:';
$txt['pmx_edit_pagenamehelp'] = 'You can use any name with the chars <b>a-z, A-Z, 0-9</b>, underscore(<b>_</b>), dot(<b>.</b>) and hyphen(<b>-</b>).';
$txt['pmx_edit_titleicon'] = 'Title icon:';
$txt['pmx_edit_no_icon'] = 'no icon';
$txt['pmx_edit_content'] = 'Create or edit the content';

$txt['pmx_block_move_error'] = 'You can\'t move the block to itself !';
$txt['namefielderror'] = 'The input field for "%s" is empty !';
$txt['pmx_edit_title_helpalign'] = 'Click to set Title align ';
$txt['pmx_edit_title_align_types'] = array(
	'left' => 'Left',
	'center' => 'Center',
	'right' => 'Right'
);

$txt['pmx_title'] = 'Title';
$txt['pmx_status'] = 'Status';
$txt['pmx_options'] = 'Options';
$txt['pmx_functions'] = 'Functions';
$txt['pmx_edit_titles'] = 'Edit title settings';
$txt['pmx_edit_titlehelp'] = 'Enter a title for each language you have.';
$txt['pmx_toggle_language'] = 'Click to toggle between languages';

$txt['pmx_edit_visuals'] = 'Visual settings and CSS classes';
$txt['pmx_edit_cancollapse'] = 'Can collapse:';
$txt['pmx_edit_overflow'] = 'Overflow action:';
$txt['pmx_pixel_blank'] = ' Pixel or leave blank';

$txt['pmx_edit_height'] = 'Fixed block height as:';
$txt['pmx_edit_height_mode'] = array(
	'max-height' => 'max height',
	'height' => 'height',
	'min-height' => 'min height');

$txt['pmx_edit_collapse_state'] = 'Entry block state:';
$txt['pmx_collapse_mode'] = array(
	0 => 'default',
	1 => 'collapsed',
	2 => 'expanded');

$txt['pmx_edit_cssfilename'] = 'CSS File:';
$txt['pmx_edit_usedclass_type'] = 'Type name';
$txt['pmx_edit_usedclass_style'] = 'Assigned style class';
$txt['pmx_edit_canhavecssfile'] = 'Select a cssfile or leave blank';
$txt['pmx_edit_nocss_class'] = '[not defined for Theme %s]';

$txt['pmx_edit_innerpad'] = 'Inner padding:';
$txt['pmx_pixel'] = ' Pixel';

$txt['pmx_htmlsettings_title'] = 'Html block settings';
$txt['pmx_html_teaser'] = 'Enable the html teaser:';
$txt['pmx_html_teasehelp'] = 'You can insert a teaser mark in the html content. To do this, set the cursor to a suitable tease position, then click on the "<b>page-break</b>" icon in the editor.';

$txt['pmx_teasemode'] = array(
	0 => 'words',
	1 => 'characters'
);

$txt['pmx_add_new_blocktype'] = 'Add %s block';
$txt['pmx_blocks_blocktype'] = 'Select the Blocktype:';
$txt['pmx_add_new_articletype'] = 'Add new Article';
$txt['pmx_articles_articletype'] = 'Select the Articletype:';
$txt['pmx_content_print'] = 'Enable content printing:';
?>