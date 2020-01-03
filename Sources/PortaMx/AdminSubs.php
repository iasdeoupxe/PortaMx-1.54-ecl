<?php
/**
* \file AdminSubs.php
* AdminSubs holds all subroutines for the Admin part.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* check if is a legal POST session.
* Skip on admin security logon
*/
function PortaMx_checkPOST()
{
	global $context;

	// id admin security logon ?
	if(isset($_POST['admin_pass']))
	{
		// yes .. remove the posts
		unset($_POST['admin_pass']);
		if(isset($_POST['admin_hash_pass']))
			unset($_POST['admin_hash_pass']);
		if(isset($_POST[$context['session_var']]))
			unset($_POST[$context['session_var']]);
	}

	return !empty($_POST);
}

/**
* get one blocks for Block Manager Editblock.
*/
function PortaMx_getAdmEditBlock($id = null, $block = null, $side = null)
{
	global $smcFunc, $context, $sourcedir, $boardurl, $modSettings, $options, $user_info;

	// new block ?
	if(is_null($id))
		$block = PortaMx_getdefaultBlock($side, $block);

	// no, get the block by id
	elseif(is_null($block))
	{
		$result = null;
		$request = $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}portamx_blocks
			WHERE id = {int:id}',
			array('id' => $id)
		);

		if($smcFunc['db_num_rows']($request) > 0)
		{
			$block = $smcFunc['db_fetch_assoc']($request);
			$smcFunc['db_free_result']($request);
		}
	}

	// handle bbc script and download block
	if(in_array($block['blocktype'], array('bbc_script', 'php', 'script', 'download', 'fader')))
	{
		// create the SMF editor.
		require_once($sourcedir . '/Subs-Editor.php');
		$options['wysiwyg_default'] = false;

		if($block['blocktype'] == 'php')
		{
			$modSettings['disable_wysiwyg'] = true;
			if(preg_match('~\[\?pmx_initphp(.*)pmx_initphp\?\]~is', $block['content'], $match))
				$cont = $match[1];
			else
				$cont = '';

			$editorOptionsInit = array(
				'id' => 'content_init',
				'value' => htmlspecialchars($cont, ENT_NOQUOTES),
				'width' => '100%',
				'height' => '200px',
				'labels' => array(),
				'preview_type' => 0,
				'bbc_level' => 0,
				'disable_smiley_box' => 1,
				'force_rich' => false,
			);

			create_control_richedit($editorOptionsInit);
			$context['pmx']['editorID_init'] = array(
				'id' => $editorOptionsInit['id'],
				'havecont' => !empty($cont),
			);

			if(preg_match('~\[\?pmx_showphp(.*)pmx_showphp\?\]~is', $block['content'], $match))
				$cont = $match[1];
			else
				$cont = $block['content'];

			$editorOptions = array(
				'id' => 'content_show',
				'value' => htmlspecialchars($cont, ENT_NOQUOTES),
				'width' => '100%',
				'height' => '200px',
				'labels' => array(),
				'preview_type' => 0,
				'bbc_level' => 0,
				'disable_smiley_box' => 1,
				'force_rich' => false,
			);
		}
		else
		{
			if(in_array($block['blocktype'], array('script', 'fader')))
				$modSettings['disable_wysiwyg'] = true;

			$editorOptions = array(
				'id' => 'content',
				'value' => htmlspecialchars($block['content'], ENT_NOQUOTES),
				'width' => '100%',
				'height' => (in_array($block['blocktype'], array('download', 'fader')) ? '150px' : '200px'),
				'labels' => array(),
				'preview_type' => 0,
				'bbc_level' => (in_array($block['blocktype'], array('script', 'fader')) ? 0 : 'full'),
				'disable_smiley_box' => (in_array($block['blocktype'], array('script', 'fader')) ? 1 : 0),
				'force_rich' => false,
			);
		}

		if(in_array($block['blocktype'], array('bbc_script', 'download')))
			$_REQUEST['content_mode'] = '';

		create_control_richedit($editorOptions);
		$context['pmx']['editorID'] = $editorOptions['id'];

		if(in_array($block['blocktype'], array('bbc_script', 'download')))
			PortaMx_getSmileys();
	}
	// for html blocks
	elseif($block['blocktype'] == 'html')
	{
		$context['html_headers'] .= '
	<script type="text/javascript" src="'. $boardurl .'/ckeditor/ckeditor.js"></script>';

		$context['pmx']['htmledit'] = array(
			'id' => 'content',
			'content' => htmlspecialchars($block['content'], ENT_NOQUOTES),
		);
	}

	require_once($context['pmx_classdir']. $block['blocktype'] .'_adm.php');
	$block_type = 'pmxc_'. $block['blocktype'] .'_adm';
	$result = new $block_type($block);

	// init the admin block
	$result->pmxc_AdmBlock_loadinit('');
	return $result;
}

/**
* load the article editor by article type.
* field: name of a input element.
* content: the content in the editor or empty.
*/
function PortaMx_EditArticle($type, $field, $content)
{
	global $context, $sourcedir, $boardurl, $modSettings, $options, $user_info;

	// for html blocks
	if($type == 'html')
	{
		$context['html_headers'] .= '
	<script type="text/javascript" src="'. $boardurl .'/ckeditor/ckeditor.js"></script>';

		$context['pmx']['htmledit'] = array(
			'id' => 'content',
			'content' => htmlspecialchars($content, ENT_NOQUOTES),
		);
	}
	else
	{
		// create the SMF editor.
		require_once($sourcedir . '/Subs-Editor.php');
		$options['wysiwyg_default'] = false;

		if($type == 'php')
		{
			$modSettings['disable_wysiwyg'] = true;
			if(preg_match('~\[\?pmx_initphp(.*)pmx_initphp\?\]~is', $content, $match))
				$cont = $match[1];
			else
				$cont = '';

			$editorOptionsInit = array(
				'id' => 'content_init',
				'value' => htmlspecialchars($cont, ENT_NOQUOTES),
				'width' => '100%',
				'height' => '200px',
				'labels' => array(),
				'preview_type' => 0,
				'bbc_level' => 0,
				'disable_smiley_box' => 1,
				'force_rich' => false,
			);

			create_control_richedit($editorOptionsInit);
			$context['pmx']['editorID_init'] = array(
				'id' => $editorOptionsInit['id'],
				'havecont' => !empty($cont),
			);

			if(preg_match('~\[\?pmx_showphp(.*)pmx_showphp\?\]~is', $content, $match))
				$cont = $match[1];
			else
				$cont = $content;

			$editorOptions = array(
				'id' => 'content_show',
				'value' => htmlspecialchars($cont, ENT_NOQUOTES),
				'width' => '100%',
				'height' => '200px',
				'labels' => array(),
				'preview_type' => 0,
				'bbc_level' => 0,
				'disable_smiley_box' => 1,
				'force_rich' => false,
			);
		}
		else
		{
			if($type == 'code')
				$modSettings['disable_wysiwyg'] = true;

			$editorOptions = array(
				'id' => $field,
				'value' => htmlspecialchars($content, ENT_NOQUOTES),
				'width' => '100%',
				'height' => '300px',
				'labels' => array(),
				'preview_type' => 0,
				'bbc_level' => ($type == 'script' ? 0 : 'full'),
				'disable_smiley_box' => ($type == 'code' ? 1 : 0),
				'force_rich' => false,
			);
		}
		if($type == 'bbc')
			$_REQUEST[$field . '_mode'] = '';

		create_control_richedit($editorOptions);
		$context['pmx']['editorID'] = $editorOptions['id'];

		if($type == 'bbc')
			PortaMx_getSmileys();
	}
}

/**
* convert smileys from html to bbc.
*/
function PortaMx_convertSmileys($content)
{
	global $modSettings;

	$smset = PortaMx_getSmileySet();
	$smileyfrom = $smset['files'];
	$smileyto = $smset['symbols'];

	if(preg_match_all('~\[img.*\[\/img\]~U', $content, $match) > 0)
	{
		foreach($match[0] as $smiley)
		{
			if(strpos($smiley, $modSettings['smileys_url']) !== false)
			{
				$smileyName = substr(str_replace('[/img]', '', $smiley), strrpos(str_replace('[/img]', '', $smiley), '/') +1);
				$smileyPos = array_search($smileyName, $smileyfrom);
				if(is_numeric($smileyPos))
					$content = str_replace($smiley, $smileyto[$smileyPos], $content);
			}
		}
	}
	return $content;
}

/**
* convert smileys from html to bbc.
*/
function PortaMx_getSmileys()
{
	global $context, $settings, $modSettings;

	$smset = PortaMx_getSmileySet();
	foreach($smset['files'] as $i => $file)
		$smileys[$i] = array(
			'code' => $smset['symbols'][$i],
			'filename' => $file,
			'description' => substr($file, 0, -4)
		);
	$smileys[count($smileys) -1]['isLast'] = true;

	$context['smileys'] = array(
		'postform' => array(
			array(
				'smileys' => $smileys,
				'isLast' => true
			),
		),
		'popup' => array(),
	);
	$settings['smileys_url'] = $modSettings['smileys_url'] . '/PortaMx';
}

/**
* convert html to bbc.
*/
function PortaMx_HTMLtoBBC($content)
{
	$content = preg_replace("~\s*[\r\n]+\s*~", '', $content);
	$html = array(
		'~span\sclass="bbc_tt">(.+?)</span>~i' => '[tt]$1[/tt]',
		'~<tt>(.+?)</tt>~i' => '[tt]$1[/tt]',
		'~<div([^>]*)>~i' => '[bbcdiv$1]',
		'~</div>~i' => '[/bbcdiv]',
		'~<span([^>]*)>~i' => '[bbcspan$1]',
		'~</span>~i' => '[/bbcspan]',
		'~<p([^>]*)>~i' => '[bbcp$1]',
		'~</p>~i' => '[/bbcp]',
		'~<img([^>]*)>~i' => '[bbcimg$1]',
		'~<br([^>]*)>~i' => '[bbcbr]',
	);
	$content = preg_replace(array_keys($html), array_values($html), $content);

	return $content;
}

/**
* convert bbc to html.
*/
function PortaMx_BBCtoHTML($content)
{
	$bbc_html = array(
		'~<br[^>]*>~i' => "<br />\n",
		'~\[bbcdiv([^\]]*)\]~i' => '<div$1>',
		'~\[/bbcdiv\]~i' => '</div>',
		'~\[bbcspan([^\]]*)\]~i' => '<span$1>',
		'~\[/bbcspan\]~i' => '</span>',
		'~\[bbcp([^\]]*)\]~i' => '<p$1>',
		'~\[/bbcp\]~i' => '</p>',
		'~\[nncimg([^\]]*)\]~i' => '<img$1>',
		'~\[bbcbr]~i' => "<br />\n",
	);
	$content = preg_replace("~\s*[\r\n]+\s*~", '', $content);
	$content = preg_replace(array_keys($bbc_html), array_values($bbc_html), $content);

	return $content;
}

/**
* convert php content
**/
function pmxPHP_convert()
{
	$find = array('/^<\?php/i', '/^<\?/', '/\?>$/');
	$initcont = !empty($_POST['content_init']) ? trim(preg_replace($find, '', trim($_POST['content_init']))) : '';
	$showcont = !empty($_POST['content_show']) ? trim(preg_replace($find, '', trim($_POST['content_show']))) : '';
	if(!empty($initcont))
		$_POST['content'] = '[?pmx_initphp'."\n". $initcont ."\n".'pmx_initphp?]' ."\n". '[?pmx_showphp'."\n". $showcont ."\n".'pmx_showphp?]';
	else
		$_POST['content'] = $showcont;

	unset($_POST['content_init']);
	unset($_POST['content_show']);
}

/**
* get the setup for default Css classes.
*/
function PortaMx_getdefaultClass($extended = false, $isarticle = false)
{
	global $context, $txt;

	$result = array(
		'header' => array(
			' '. $txt['pmx_default_header_none'] => 'none',
			' '. $txt['pmx_default_header_asbody'] => '',
			'+titlebg' => 'titlebg',
			' catbg' => 'catbg',
		),
		'frame' => array(
			' '.$txt['pmx_default_none'] => '',
			'+roundframe' => 'roundframe',
			' round' => 'round',
			' border' => 'border',
		),
		'body' => array(
			' '.$txt['pmx_default_none'] => '',
			'+windowbg' => 'windowbg',
			' windowbg2' => 'windowbg2',
		),
	);

	if(!empty($isarticle))
		$article = array(
			'bodytext' => array(
				'+'.$txt['pmx_default_none'] => '',
				' smalltext' => 'smalltext',
				' middletext' => 'middletext',
				' normaltext' => 'normaltext',
				' largetext' => 'largetext',
			)
		);
	else
		$article = array(
			'bodytext' => array(
				' '.$txt['pmx_default_none'] => '',
				'+smalltext' => 'smalltext',
				' middletext' => 'middletext',
				' normaltext' => 'normaltext',
				' largetext' => 'largetext',
			)
		);
	$result = array_merge($result, $article);

	if(!empty($extended))
	{
		$extend = array(
			'postheader' => array(
				' '. $txt['pmx_default_header_none'] => 'none',
				' '. $txt['pmx_default_header_asbody'] => '',
				'+catbg' => 'catbg',
				' titlebg' => 'titlebg',
			),
			'postframe' => array(
				' '. $txt['pmx_default_none'] => '',
				'+roundframe' => 'roundframe',
				' round' => 'round',
			),
			'postbody' => array(
				' '. $txt['pmx_default_none'] => '',
				'+windowbg' => 'windowbg',
				' windowbg2' => 'windowbg2',
			),
		);
		$result = array_merge($result, $extend);
	}
	return $result;
}

/**
* get the default Block class & config.
*/
function PortaMx_getdefaultBlock($side, $blocktype)
{
	global $context, $txt;

	$lang = array();
	foreach($context['pmx']['languages'] as $lng => $sel)
		$lang[$lng] = '';

	$config = array(
		'title' => $lang,
		'title_align' => 'left',
		'title_icon' => '',
		'collapse' => 0,
		'overflow' => '',
		'innerpad' => 4,
		'visuals' => array(
			'header' => 'titlebg',
			'frame' => 'round',
			'body' => 'windowbg',
			'bodytext' => (in_array($blocktype, array('category', 'article', 'html'))) ? '' : 'smalltext',
		),
		'cssfile' => '',
		'ext_opts' => array(
			'pmxact' => array(),
			'pmxcust' => '',
			'pmxbrd' => array(),
			'pmxlng' => array(),
			'pmxthm' => array(),
		),
		'can_moderate' => allowedTo('admin_forum') ? 0 : 1,
		'settings' => array(),
	);

	$data = array(
		'id' => 0,
		'side' => $side,
		'pos' => 0,
		'active' => 0,
		'cache' => 0,
		'blocktype' => $blocktype,
		'acsgrp' => '',
		'config' => pmx_serialize($config),
		'content' => '',
	);

	return $data;
}

/**
* get the default article Config.
*/
function PortaMx_getdefaultArticle($arttype)
{
	global $context, $user_info, $txt;

	$lang = array();
	foreach($context['pmx']['languages'] as $lng => $sel)
		$lang[$lng] = '';

	$config = array(
		'title' => $lang,
		'title_align' => 'left',
		'title_icon' => '',
		'collapse' => 0,
		'overflow' => '',
		'innerpad' => 4,
		'visuals' => array(
			'header' => 'titlebg',
			'frame' => 'round',
			'body' => 'windowbg',
			'bodytext' => '',
		),
		'cssfile' => '',
		'can_moderate' => allowedTo('admin_forum') ? 0 : 1,
		'settings' => array(),
	);

	$result = array(
		'id' => 0,
		'name' => '',
		'catid' => 0,
		'acsgrp' => '',
		'ctype' => $arttype,
		'config' => pmx_serialize($config),
		'content' => '',
		'active' => 0,
		'owner' => $user_info['id'],
		'created' => 0,
		'approved' => 0,
		'approvedby' => 0,
		'updated' => 0,
		'updatedby' => 0,
	);

	return $result;
}

/**
* get the default catehory Config.
*/
function PortaMx_getDefaultCategory($name = '')
{
	global $context, $user_info, $txt;

	$lang = array();
	foreach($context['pmx']['languages'] as $lng => $sel)
		$lang[$lng] = '';

	$config = array(
		'title' => $lang,
		'title_align' => 'left',
		'title_icon' => '',
		'collapse' => 0,
		'overflow' => '',
		'innerpad' => 4,
		'visuals' => array(
			'header' => 'titlebg',
			'frame' => 'round',
			'body' => 'windowbg',
			'bodytext' => '',
		),
		'cssfile' => '',
		'can_moderate' => allowedTo('admin_forum') ? 0 : 1,
		'settings' => array(
			'framemode' => 'both',
			'global' => 0,
			'request' => 0,
			'showmode' => 'sidebar',
			'sidebarwidth' => 140,
			'addsubcats' => 0,
			'pages' => '20',
			'showsubcats' => 0,
			'catsbarwidth' => 140,
			'inherit_acs' => 0,
		),
	);

	$result = array(
		'id' => 0,
		'name' => $name,
		'parent' => 0,
		'level' => 0,
		'catorder' => 0,
		'acsgrp' => '',
		'artsort' => array(),
		'config' => pmx_serialize($config),
	);

	return $result;
}

/**
* get category datails.
*/
function PortaMx_getCatDetails($category, $allcats)
{
	global $txt;

	if(empty($category['id']) || !is_array($category))
	{
		$catclass = 'cat_nonelevel';
		$parent = $txt['pmx_chg_articlnocats'];
		$level = '&nbsp;';
	}
	elseif(is_array($category['childs']))
	{
		$catclass = 'cat_child';
		if(empty($category['parent']))
		{
			$catclass = 'cat_rootchild';
			$parent =  $txt['pmx_categories_rootchild'];
			$level = '<sup>&nbsp;&nbsp;</sup>';
		}
		else
		{
			$pcat = PortaMx_getCatByID($allcats, $category['parent']);
			$catclass = 'cat_child';
			$parent = sprintf($txt['pmx_categories_childchild'], $pcat['name']);
			$level = '<sup>'. $category['level'] .'</sup>';
		}
	}
	elseif(!empty($category['parent']))
	{
		$pcat = PortaMx_getCatByID($allcats, $category['parent']);
		$catclass = 'cat_level';
		$parent = sprintf($txt['pmx_categories_child'], $pcat['name']);
		$level = '<sup>'. $category['level'] .'</sup>';
	}
	else
	{
		$catclass = 'cat_root';
		$parent = $txt['pmx_categories_root'];
		$level = '<sup>&nbsp;&nbsp;</sup>';
	}

	return array(
		'class' => $catclass,
		'parent' => $parent,
		'level' => $level);
}

/**
* get all smf user groups.
*/
function PortaMx_getUserGroups($noGuest = false, $showPostcount = true)
{
	global $smcFunc, $context, $txt;

	// guest & normal members
	if(empty($noGuest))
	{
		$result = array(
			0 => array(
				'id' => '-1',
				'name' => $txt['pmx_guest'],
			),
			1 => array(
				'id' => '0',
				'name' => $txt['pmx_ungroupedmembers'],
			),
		);
		$where = (!empty($showPostcount) && !empty($context['pmx']['settings']['postcountacs'])) ? '' : 'WHERE min_posts = -1';
	}
	else
	{
		$result = array();
		$where = 'WHERE min_posts = -1';
	}

	// get SMF membergroups
	$where = (!empty($showPostcount) && !empty($context['pmx']['settings']['postcountacs'])) ? '' : 'WHERE min_posts = -1';
	$request = $smcFunc['db_query']('', '
			SELECT id_group, group_name
			FROM {db_prefix}membergroups
			'. $where .'
			ORDER BY id_group',
		array()
	);
	while($row = $smcFunc['db_fetch_assoc']($request))
	{
		$result[] = array(
			'id' => $row['id_group'],
			'name' => $row['group_name'],
		);
	}
	$smcFunc['db_free_result']($request);
	return $result;
}

/**
* get all smf boards.
*/
function PortaMx_getsmfBoards($redir_boards = false)
{
	global $context, $modSettings, $smcFunc;

	$result = null;
	$request = $smcFunc['db_query']('', '
			SELECT id_board, name, child_level
			FROM {db_prefix}boards
			WHERE id_board != {int:excl}'. (empty($redir_boards) ? ' AND redirect = {string:nullstr}' : '') .'
			ORDER BY board_order',
		array(
			'excl' => isset($modSettings['recycle_board']) ? $modSettings['recycle_board'] : '0',
			'nullstr' => ''
		)
	);
	while($row = $smcFunc['db_fetch_assoc']($request))
		$result[] = array(
			'id' => $row['id_board'],
			'name' => (!empty($row['child_level']) ? str_repeat('&bull;', $row['child_level']).' ' : '') . $row['name']
		);

	$smcFunc['db_free_result']($request);
	return $result;
}

/**
* get all title icons.
*/
function PortaMx_getAllTitleIcons()
{
	global $context;

	$result = array();
	if(is_dir($context['pmx_Iconsdir']))
	{
		if($dh = opendir($context['pmx_Iconsdir']))
		{
			while(($file = readdir($dh)) !== false)
				if($file != 'none.gif' && $file != 'index.php' && $file != '..' && $file != '.')
					$result[] = $file;
		}
		closedir($dh);
		sort($result, SORT_STRING);
	}
	return $result;
}
?>
