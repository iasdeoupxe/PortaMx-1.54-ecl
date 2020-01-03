<?php
/**
* \file AdminBlocks.php
* AdminBlocks reached all Posts from Blocks Manager.
* Checks the values and saved the parameter to the database.
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* Receive all the Posts from Articles Manager, check and save it.
* Finally the articles are prepared and the template loaded.
*/
function PortaMx_AdminBlocks()
{
	global $smcFunc, $context, $settings, $sourcedir, $user_info, $txt;

	$admMode = PortaMx_makeSafe($_GET['action']);
	$pmx_area = PortaMx_makeSafe($_GET['area']);
	$newBlockSide = '';

	if(($admMode == 'admin' || $admMode == 'portamx') && $pmx_area == 'pmx_blocks')
	{
		if(allowPmx('pmx_admin, pmx_blocks'))
		{
			require_once($context['pmx_sourcedir'] .'AdminSubs.php');
			$context['pmx']['subaction'] = isset($_POST['sa']) ? $_POST['sa'] : 'all';

			// From template ?
			if(PortaMx_checkPOST())
			{
				// check the Post array
				checkSession('post');
				$context['pmx']['function'] = $_POST['function'];

				// actions from overview ?
				if($context['pmx']['function'] == 'overview')
				{
					// update action from overview?
					if(!empty($_POST['upd_overview']))
					{
						$updates = array();
						$chgSides = array();
						foreach($_POST['upd_overview'] as $side => $sidevalues)
						{
							$chgSides[] = $side;
							foreach($sidevalues as $updkey => $updvalues)
							{
								foreach($updvalues as $id => $values)
								{
									$id = PortaMx_makeSafe($id);
									$updkey = PortaMx_makeSafe($updkey);
									if($updkey == 'title')
									{
										foreach($values as $key => $val)
										{
											if($key == 'lang')
											{
												foreach($val as $langname => $langvalue)
													$updates[$id]['config'][$updkey][$langname] = $langvalue;
											}
											else
												$updates[$id]['config'][$updkey .'_'. $key] = $val;
										}
									}
									else
										$updates[$id][$updkey] = $values;
								}
							}
						}

						// save all updates (title, access)
						foreach($updates as $id => $values)
						{
							$request = $smcFunc['db_query']('', '
								SELECT config, acsgrp, blocktype
								FROM {db_prefix}portamx_blocks
								WHERE id = {int:id}',
								array('id' => $id)
							);
							$row = $smcFunc['db_fetch_assoc']($request);
							$smcFunc['db_free_result']($request);
							$blocktype = $row['blocktype'];

							foreach($values as $rowname => $data)
							{
								// update config array
								if($rowname == 'config')
								{
									$cfg = unserialize($row['config']);
									foreach($data as $ckey => $cval)
									{
										if($ckey == 'title')
											foreach($cval as $lang => $val)
												$cfg[$ckey][$lang] = $val;
										else
											$cfg[$ckey] = $cval;
									}
									$smcFunc['db_query']('', '
										UPDATE {db_prefix}portamx_blocks
										SET config = {string:config}
										WHERE id = {int:id}',
										array(
											'id' => $id,
											'config' => pmx_serialize($cfg),
										)
									);
								}

								// access groups
								else
								{
									$mode = substr($rowname, 0, 3);

									// update (replace)
									if($mode == 'upd')
										$newacs = explode(',', $data);

									// add group(s)
									elseif($mode == 'add')
										$newacs = array_unique(array_merge(Pmx_StrToArray($row['acsgrp']), Pmx_StrToArray($data)));

									// delete group(s)
									else
									{
										@list($groups, $grpsdeny) = Pmx_StrToArray($row['acsgrp'], ',', '=');
										@list($delgrps, $deldeny) = Pmx_StrToArray($data, ',', '=');
										$grps = array_diff($groups, $delgrps);
										$newacs = array();
										foreach($grps as $grp)
											$newacs[] = $grp .'='. intval(!in_array($grp, $grpsdeny));
									}

									$smcFunc['db_query']('', '
										UPDATE {db_prefix}portamx_blocks
										SET acsgrp = {string:val}
										WHERE id = {int:id}',
										array(
											'id' => $id,
											'val' => implode(',', $newacs))
									);
								}
							}

							// clear block cache
							clearCachedBlocks(array(), $blocktype, $id);
						}

						// clear SEF pages
						clearCachedBlocks($chgSides, '', 0);

						if(!empty($context['pmx']['settings']['manager']['follow']))
							$context['pmx']['subaction'] = implode(',', $chgSides);
					}

					// add new block
					if(!empty($_POST['add_new_block']))
					{
						$id = null;
						$context['pmx']['function'] = 'editnew';
						list($newBlockSide) = array_keys($_POST['add_new_block']);
						list($block) = array_values($_POST['add_new_block']);
					}

					// move rowpos
					elseif(!empty($_POST['upd_rowpos']))
					{
						list($side) = PMX_Each($_POST['upd_rowpos']);
						list($fromID, $place, $toID) = Pmx_StrToArray($_POST['upd_rowpos'][$side]['rowpos']);

						$request = $smcFunc['db_query']('', '
							SELECT id, pos
							FROM {db_prefix}portamx_blocks
							WHERE id IN({array_int:ids})',
							array('ids' => array($fromID, $toID))
						);
						while($row = $smcFunc['db_fetch_assoc']($request))
							$moveData[$row['id']] = $row['pos'];
						$smcFunc['db_free_result']($request);

						// create the query...
						if($moveData[$fromID] > $moveData[$toID])
							$query = 'SET pos = pos + 1 WHERE side = \''. $side .'\' AND pos >= '. $moveData[$toID] .' AND pos <= '. $moveData[$fromID];
						else
							$query = 'SET pos = pos - 1 WHERE side = \''. $side .'\' AND pos >= '. $moveData[$fromID] .' AND pos <= '. $moveData[$toID];
						// .. and execute
						$smcFunc['db_query']('', 'UPDATE {db_prefix}portamx_blocks '. $query, array());

						// update the fromID pos
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}portamx_blocks
							SET pos = {int:pos}
							WHERE id = {int:id}',
							array('id' => $fromID, 'pos' => $moveData[$toID])
						);

						if(!empty($context['pmx']['settings']['manager']['follow']))
							$context['pmx']['subaction'] = $side;
					}

					// toggle active ?
					elseif(!empty($_POST['chg_status']))
					{
						$id = PortaMx_makeSafe($_POST['chg_status']);
						if(!empty($context['pmx']['settings']['manager']['follow']))
						{
							$request = $smcFunc['db_query']('', '
								SELECT side, blocktype
								FROM {db_prefix}portamx_blocks
								WHERE id = {int:id}',
								array('id' => $id)
							);
							list($context['pmx']['subaction'], $blocktype) = $smcFunc['db_fetch_row']($request);
							$smcFunc['db_free_result']($request);
						}

						$smcFunc['db_query']('', '
							UPDATE {db_prefix}portamx_blocks
							SET active = CASE WHEN active = 0 THEN 1 ELSE 0 END
							WHERE id = {int:id}',
							array('id' => $id)
						);

						// clear all cache data
						clearCachedBlocks($context['pmx']['subaction'], $blocktype, $id);
					}

					elseif(!empty($_POST['edit_block']))
					{
						$id = $_POST['edit_block'];
						$context['pmx']['function'] = 'edit';
						$block = null;
					}

					// move block, clone block
					elseif(!empty($_POST['clone_block']) || !empty($_POST['move_block']))
					{
						if(!empty($_POST['clone_block']))
							list($id, $side) = Pmx_StrToArray($_POST['clone_block']);
						else
							list($id, $side) = Pmx_StrToArray($_POST['move_block']);

						// load the block for move/clone
						$request = $smcFunc['db_query']('', '
							SELECT *
							FROM {db_prefix}portamx_blocks
							WHERE id = {int:id}',
							array(
								'id' => $id
							)
						);
						$row = $smcFunc['db_fetch_assoc']($request);
						$smcFunc['db_free_result']($request);

						// redirect on move/clone to articles..
						if($side == 'articles')
							redirectexit('action='. $admMode .';area=pmx_articles;sa=edit;id='. $id .';from='. (!empty($_POST['clone_block']) ? 'clone.' : 'move.') . $row['side'] .';'. $context['session_var'] .'=' .$context['session_id']);

						// block move
						if(!empty($_POST['move_block']))
						{
							// update all pos >= moved id
							$smcFunc['db_query']('', '
								UPDATE {db_prefix}portamx_blocks
								SET pos = pos - 1
								WHERE side = {string:side} AND pos >= {int:pos}',
								array('side' => $row['side'], 'pos' => $row['pos'])
							);

							// get max pos for destination panel
							$request = $smcFunc['db_query']('', '
								SELECT MAX(pos)
								FROM {db_prefix}portamx_blocks
								WHERE side = {string:side}',
								array('side' => $side)
							);
							list($dbpos) = $smcFunc['db_fetch_row']($request);
							$smcFunc['db_free_result']($request);
							$block['pos'] = strval(1 + ($dbpos === null ? 0 : $dbpos));
							$block['side'] = $side;

							// now update the block
							$smcFunc['db_query']('', '
								UPDATE {db_prefix}portamx_blocks
								SET pos = {int:pos}, side = {string:side}
								WHERE id = {int:id}',
								array('id' => $id, 'pos' => $block['pos'], 'side' => $block['side'])
							);

							// clear all cache data
							clearCachedBlocks($block['side'], $row['blocktype'], $id);

							$context['pmx']['function'] = 'overview';
							if(!empty($context['pmx']['settings']['manager']['follow']) && empty($_POST['move_block']))
								$context['pmx']['subaction'] = $side;
						}

						// clone block
						else
						{
							$block = array(
								'id' => $row['id'],
								'side' => $row['side'],
								'pos' => $row['pos'],
								'active' => $row['active'],
								'cache' => $row['cache'],
								'blocktype' => $row['blocktype'],
								'acsgrp' => $row['acsgrp'],
								'config' => $row['config'],
								'content' => $row['content'],
							);

							$block['side'] = $side;
							$block['active'] = 0;
							$context['pmx']['function'] = 'editnew';
						}
					}
					// delete block ?
					elseif(!empty($_POST['block_delete']))
					{
						$request = $smcFunc['db_query']('', '
							SELECT side, pos, blocktype
							FROM {db_prefix}portamx_blocks
							WHERE id = {int:id}',
							array('id' => $_POST['block_delete'])
						);
						list($side, $pos, $blocktype) = $smcFunc['db_fetch_row']($request);
						$smcFunc['db_free_result']($request);

						// update all pos >= deleted id
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}portamx_blocks
							SET pos = pos - 1
							WHERE side = {string:side} AND pos >= {int:pos}',
							array('side' => $side, 'pos' => $pos)
						);

						// delete the block
						$smcFunc['db_query']('', '
							DELETE FROM {db_prefix}portamx_blocks
							WHERE id = {int:id}',
							array('id' => $_POST['block_delete'])
						);

						// clear all cache data
						clearCachedBlocks($side, $blocktype, $_POST['block_delete']);

						if(!empty($context['pmx']['settings']['manager']['follow']))
							$context['pmx']['subaction'] = $side;
					}

					// redirect ?
					if($context['pmx']['function'] == 'overview')
						redirectexit('action='. $admMode .';area='. $pmx_area .';sa='. $context['pmx']['subaction'] .';'. $context['session_var'] .'=' .$context['session_id']);
				}

				// edit block canceled ?
				if(!empty($_POST['cancel_edit']))
					$context['pmx']['function'] = 'overview';

				// actions for/from edit block
				elseif(empty($_POST['edit_block']) && empty($_POST['add_new_block']) && ($context['pmx']['function'] == 'editnew' || $context['pmx']['function'] == 'edit'))
				{
					// check defined numeric vars (check_num_vars holds the posted array to check like [varname][varname] ...)
					if(isset($_POST['check_num_vars']))
					{
						foreach($_POST['check_num_vars'] as $val)
						{
							$data = explode(',', $val);
							$post = '$_POST'. str_replace(array('[', ']'), array('[\'', '\']'), $data[0]);
							if(eval("return isset($post);") && eval("return !is_numeric($post);"))
									eval("$post = $data[1];");
						}
					}

					// add a change date to config array
					$_POST['config']['created'] = time();

					// blocktype change?
					if(!empty($_POST['chg_blocktype']))
					{
						if(isset($_POST['content']) && PortaMx_makeSafeContent($_POST['content']) != '')
						{
							// convert html/script to bbc
							if($_POST['blocktype'] == 'bbc_script' && in_array($_POST['contenttype'], array('html', 'script')))
							{
								require_once($sourcedir . '/Subs-Editor.php');
								$user_info['smiley_set'] = 'PortaMx';
								$_POST['content'] = html_to_bbc($_POST['content']);
								$_POST['content'] = preg_replace_callback('/(\n)(\s)/', create_function('$matches', 'return $matches[1];'), $_POST['content']);
							}

							// convert bbc to html/script
							elseif($_POST['contenttype'] == 'bbc_script' && in_array($_POST['blocktype'], array('html', 'script')))
							{
								require_once($sourcedir . '/Subs-Editor.php');
								$_POST['content'] = PortaMx_BBCsmileys(parse_bbc(PortaMx_makeSafeContent($_POST['content'], $_POST['contenttype']), false));
							}

							// handling special php blocks
							elseif($_POST['blocktype'] == 'php')
							{
								if($_POST['contenttype'] == 'php')
									pmxPHP_convert();
							}
						}
						elseif($_POST['blocktype'] == 'php' || $_POST['contenttype'] == 'php' && in_array($_POST['blocktype'], array('html', 'script', 'bbc_script')))
							pmxPHP_convert();
						else
							$_POST['content'] = '';

						$id = $_POST['id'];
					}

					// save data from blocktype change
					if(empty($_POST['move_block']) && (!empty($_POST['save_edit']) || !empty($_POST['save_edit_continue']) || !empty($_POST['chg_blocktype'])))
					{
						if($_POST['blocktype'] == 'php' && $_POST['contenttype'] == 'php')
							pmxPHP_convert();
						elseif($_POST['blocktype'] == 'shoutbox')
							$_POST['content'] = base64_decode($_POST['content']);
						else
							$_POST['content'] = isset($_POST['content']) ? PortaMx_makeSafeContent($_POST['content'], 'html') : '';

						$block = array(
							'id' => $_POST['id'],
							'side' => $_POST['side'],
							'pos' => $_POST['pos'],
							'active' => $_POST['active'],
							'cache' => $_POST['cache'],
							'blocktype' => $_POST['blocktype'],
							'acsgrp' => (!empty($_POST['acsgrp']) ? implode(',', $_POST['acsgrp']) : ''),
							'config' => pmx_serialize($_POST['config']),
							'content' => $_POST['content'],
						);

						$id = $_POST['id'];
					}

					// save block..
					if(!empty($_POST['save_edit']) || !empty($_POST['save_edit_continue']))
					{
						// if new article get the last id
						if($context['pmx']['function'] == 'editnew')
						{
							$request = $smcFunc['db_query']('', '
								SELECT MAX(a.id), MAX(b.pos)
								FROM {db_prefix}portamx_blocks as a
								LEFT JOIN {db_prefix}portamx_blocks as b ON(b.side = {string:side})
								GROUP BY b.side',
								array('side' => $block['side'])
							);
							list($dbid, $dbpos) = $smcFunc['db_fetch_row']($request);
							$smcFunc['db_free_result']($request);
							$block['id'] = strval(1 + ($dbid === null ? 0 : $dbid));
							$block['pos'] = strval(1 + ($dbpos === null ? 0 : $dbpos));
						}

						// now save all data
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
								$block['id'],
								$block['side'],
								$block['pos'],
								$block['active'],
								$block['cache'],
								$block['blocktype'],
								$block['acsgrp'],
								$block['config'],
								$block['content'],
							),
							array('id')
						);

						// clear all cache data
						clearCachedBlocks($block['side'], $block['blocktype'], $block['id']);

						$context['pmx']['function'] = 'edit';
					}

					// end edit ?
					if(!empty($_POST['save_edit']))
					{
						$context['pmx']['function'] = 'overview';
						if(!empty($context['pmx']['settings']['manager']['follow']))
							$context['pmx']['subaction'] = $block['side'];

						if(!empty($block['active']))
							redirectexit('action='. $admMode .';area='. $pmx_area .';sa='. $context['pmx']['subaction'] .';'. $context['session_var'] .'=' .$context['session_id']);
					}
					elseif(!empty($_POST['save_edit_continue']))
					{
						if(!empty($block['active']))
						{
							$_SESSION['pmx_save_edit_continue'] = $block['id'];
							redirectexit('action='. $admMode .';area='. $pmx_area .';sa='. $context['pmx']['subaction'] .';'. $context['session_var'] .'=' .$context['session_id']);
						}
					}
				}
			}
			else
			{
				$context['pmx']['subaction'] = (isset($_GET['sa']) && $_GET['sa'] != 'settings' ? PortaMx_makeSafe($_GET['sa']) : 'all');
				$context['pmx']['function'] = 'overview';

				// direct edit request?
				if(isset($_GET['edit']) && intval(PortaMx_makeSafe($_GET['edit'])) != 0)
				{
					$id = PortaMx_makeSafe($_GET['edit']);
					$context['pmx']['function'] = 'edit';
					$block = null;
				}
				elseif(isset($_SESSION['pmx_save_edit_continue']))
				{
					$block = null;
					$id = $_SESSION['pmx_save_edit_continue'];
					unset($_SESSION['pmx_save_edit_continue']);
					$context['pmx']['function'] = 'edit';
				}
			}

			// load template and languages, setup pagetitle
			loadTemplate($context['pmx_templatedir'] .'AdminBlocks');
			loadLanguage($context['pmx_templatedir'] .'AdminBlocks');
			$context['pmx']['RegBlocks'] = eval($context['pmx']['registerblocks']);
			$context['page_title'] = $txt['pmx_blocks'];
			$context['pmx']['AdminMode'] = $admMode;

			// continue edit or overview ?
			if($context['pmx']['function'] == 'overview')
			{
				// load blocks data for overview
				$context['pmx']['blocks'] = array();
				$request = $smcFunc['db_query']('', '
					SELECT id, side, pos, active, cache, blocktype, acsgrp, config
					FROM {db_prefix}portamx_blocks
					WHERE side IN ({array_string:side})
					ORDER BY side, pos',
					array(
						'side' => Pmx_StrToArray(($context['pmx']['subaction'] == 'all' ? implode(',', array_keys($txt['pmx_admBlk_sides'])) : $context['pmx']['subaction'])),
					)
				);
				if($smcFunc['db_num_rows']($request) > 0)
				{
					while($row = $smcFunc['db_fetch_assoc']($request))
						$context['pmx']['blocks'][$row['side']][$row['pos']] = array(
							'id' => $row['id'],
							'side' => $row['side'],
							'pos' => $row['pos'],
							'active' => $row['active'],
							'cache' => $row['cache'],
							'blocktype' => $row['blocktype'],
							'acsgrp' => $row['acsgrp'],
							'config' => unserialize($row['config']),
						);
					$smcFunc['db_free_result']($request);
				}

				$context['html_headers'] .= '
	<script type="text/javascript" src="'. PortaMx_loadCompressed('PortaMxPopup.js') .'"></script>';
			}

			elseif(empty($_POST['save_edit']))
			{
				// load the class file and create the object
				require_once($context['pmx_sysclassdir']. 'PortaMx_AdminBlocksClass.php');
				$context['pmx']['editblock'] = PortaMx_getAdmEditBlock($id, $block, $newBlockSide);
			}
		}
		else
			fatal_error($txt['pmx_acces_error']);
	}
}

/**
* clear cached blocks
**/
function clearCachedBlocks($sides, $blocktype, $id)
{
	global $context, $pmxCacheFunc;

	if(in_array($blocktype, array_merge(array('php'), array_keys($context['pmx']['cache']['blocks']))))
	{
		if($blocktype == 'mini_calendar')
		{
			$pmxCacheFunc['clear']($blocktype . $id .'-0', false);
			$pmxCacheFunc['clear']($blocktype . $id .'-1', false);
			$pmxCacheFunc['clear']($blocktype . $id .'-6', false);
		}
		else
			$pmxCacheFunc['clear']($blocktype . $id, true);
	}

	// clear SEF pages list
	if((is_array($sides) && in_array('pages', $sides)) || (!is_array($sides) && $sides == 'pages'))
		$pmxCacheFunc['clear']('pmxsef_pageslist', false);
}
?>
