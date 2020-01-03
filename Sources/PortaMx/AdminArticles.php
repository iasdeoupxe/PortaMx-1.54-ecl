<?php
/**
* \file AdminArticles.php
* AdminArticles reached all Posts from Articles Manager.
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
function PortaMx_AdminArticles()
{
	global $smcFunc, $pmxCacheFunc, $context, $settings, $sourcedir, $user_info, $txt;

	$admMode = isset($_GET['action']) ? $_GET['action'] : '';
	if(($admMode == 'admin' || $admMode == 'portamx') && isset($_GET['area']) && $_GET['area'] == 'pmx_articles')
	{
		if(allowPmx('pmx_admin, pmx_articles, pmx_create'))
		{
			require_once($context['pmx_sourcedir'] .'AdminSubs.php');

			$context['pmx']['subaction'] = isset($_POST['sa']) ? $_POST['sa'] : 'overview';
			$pmx_area = PortaMx_makeSafe($_GET['area']);

			// From template ?
			if(PortaMx_checkPOST())
			{
				// check the Post array
				checkSession('post');

				// get current pageindex
				$context['pmx']['articlestart'] = PortaMx_makeSafe($_POST['articlestart']);

				// actions from overview ?
				if($context['pmx']['subaction'] == 'overview' && empty($_POST['cancel_overview']))
				{
					// filter set ?
					$_SESSION['PortaMx']['filter'] = (isset($_POST['filter']) ? $_POST['filter'] : array('category' => '', 'approved' => 0, 'active' => 0, 'myown' => 0, 'member' => ''));

					// Row pos updates from overview ?
					if(!empty($_POST['upd_rowpos']))
					{
						list($fromID, $place, $idto) = Pmx_StrToArray($_POST['upd_rowpos']);

						$request = $smcFunc['db_query']('', '
							SELECT id
							FROM {db_prefix}portamx_articles
							WHERE id '. ($place == 'before' ? '<' : '>') .' {int:id}
							LIMIT 1',
							array('id' => $idto)
						);
						list($toID) = $smcFunc['db_fetch_row']($request);
						$smcFunc['db_free_result']($request);
						$toID = (is_null($toID) ? ($place == 'before' ? -1 : 0) : $toID);

						$request = $smcFunc['db_query']('', '
							SELECT MAX(id) +1
							FROM {db_prefix}portamx_articles',
							array()
						);
						list($maxID) = $smcFunc['db_fetch_row']($request);
						$smcFunc['db_free_result']($request);

						// create the query...
						if($toID == -1) // move from to first
							$query = array(
								'SET id = 0 WHERE id = '. $fromID,
								'SET id = id + 1 WHERE id >= 1 AND id <= '. $fromID,
								'SET id = 1 WHERE id = 0',
							);

						elseif($toID == 0) // move from to end
							$query = array(
								'SET id = '. $maxID .' WHERE id = '. $fromID,
								'SET id = id - 1 WHERE id >= '. $fromID,
							);

						elseif($toID > $fromID) // to > from - move to after from
							$query = array(
								'SET id = id + 1 WHERE id >= '. $toID,
								'SET id = '. $toID .' WHERE id = '. $fromID,
								'SET id = id - 1 WHERE id >= '. $fromID,
							);

						else // to < from - move to before from
							$query = array(
								'SET id = 0 WHERE id = '. $fromID,
								'SET id = id + 1 WHERE id >= '. $toID .' AND id <= '. $fromID,
								'SET id = '. $toID .' WHERE id = 0',
							);

						// execute
						foreach($query as $qdata)
						{
							$x=1;
							$smcFunc['db_query']('', 'UPDATE {db_prefix}portamx_articles '. $qdata, array());
						}
					}

					// updates from overview popups ?
					if(!empty($_POST['upd_overview']))
					{
						$curcatid = 0;
						$updates = array();
						foreach($_POST['upd_overview'] as $updkey => $updvalues)
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

						// save all updates
						$idList = array();
						$catList = array();
						foreach($updates as $id => $values)
						{
							$idList[] = $id;
							foreach($values as $rowname => $data)
							{
								$request = $smcFunc['db_query']('', '
									SELECT config, catid, acsgrp
									FROM {db_prefix}portamx_articles
									WHERE id = {int:id}',
									array('id' => $id)
								);
								$row = $smcFunc['db_fetch_assoc']($request);
								$smcFunc['db_free_result']($request);
								$catList[] = $row['catid'];

									// update config
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
										UPDATE {db_prefix}portamx_articles
										SET config = {string:config}
										WHERE id = {int:id}',
									  array(
											'id' => $id,
											'config' => pmx_serialize($cfg))
								  );
								}

								// update cat id
								elseif($rowname == 'category')
								{
									$smcFunc['db_query']('', '
										UPDATE {db_prefix}portamx_articles
										SET catid = {int:val}
										WHERE id = {int:id}',
										array(
											'id' => $id,
											'val' => $data)
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
										UPDATE {db_prefix}portamx_articles
										SET acsgrp = {string:val}
										WHERE id = {int:id}',
										array(
											'id' => $id,
											'val' => implode(',', $newacs))
									);
								}
							}
						}

						// clear cached blockd
						clearCachedBlocks($idList, $catList);
					}

					// add new article
					if(!empty($_POST['add_new_article']))
					{
						$article = PortaMx_getDefaultArticle($_POST['add_new_article']);
						$context['pmx']['subaction'] = 'editnew';
					}

					// edit / clone article
					elseif(!empty($_POST['edit_article']) || !empty($_POST['clone_article']))
					{
						$id = PortaMx_makeSafe(!empty($_POST['clone_article']) ? $_POST['clone_article'] : $_POST['edit_article']);

						// load the article for edit/clone
						$request = $smcFunc['db_query']('', '
							SELECT *
							FROM {db_prefix}portamx_articles
							WHERE id = {int:id}',
							array(
								'id' => $id
							)
						);
						$row = $smcFunc['db_fetch_assoc']($request);
						$article = array(
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

						if(!empty($_POST['clone_article']))
						{
							$article['id'] = 0;
							$article['active'] = 0;
							$article['approved'] = 0;
							$article['owner'] = $user_info['id'];
							$article['created'] = 0;
							$article['updated'] = 0;
							$article['updatedby'] = 0;
							$context['pmx']['subaction'] = 'editnew';
						}
						else
							$context['pmx']['subaction'] = 'edit';
					}

					// delete article ?
					elseif(!empty($_POST['delete_article']))
					{
						$delid = PortaMx_makeSafe($_POST['delete_article']);

						// get the current page
						$context['pmx']['articlestart'] = getCurrentPage($delid, $context['pmx']['settings']['manager']['artpage'], true);

						$smcFunc['db_query']('', '
							DELETE FROM {db_prefix}portamx_articles
							WHERE id = {int:id}',
							array('id' => $delid)
						);

						// clear cached blockd
						clearCachedBlocks($delid);
					}

					// toggle approve ?
					elseif(!empty($_POST['chg_approved']))
					{
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}portamx_articles
							SET approved = CASE WHEN approved = 0 THEN {int:apptime} ELSE 0 END, approvedby = {int:appmember}
							WHERE id = {int:id}',
							array(
								'id' => PortaMx_makeSafe($_POST['chg_approved']),
								'apptime' => forum_time(),
								'appmember' => $user_info['id'])
						);

						// clear cached blockd
						clearCachedBlocks(PortaMx_makeSafe($_POST['chg_approved']));
					}

					// toggle active ?
					elseif(!empty($_POST['chg_status']))
					{
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}portamx_articles
							SET active = CASE WHEN active = 0 THEN {int:apptime} ELSE 0 END
							WHERE id = {int:id}',
							array(
								'id' => PortaMx_makeSafe($_POST['chg_status']),
								'apptime' => forum_time())
						);

						// clear cached blockd
						clearCachedBlocks(PortaMx_makeSafe($_POST['chg_status']));
					}
				}

				// edit article canceled ?
				elseif(!empty($_POST['cancel_edit']) || !empty($_POST['cancel_overview']))
				{
					// called fron blocks move/clone ?
					if(!empty($_POST['fromblock']))
					{
						// on cancel after saved remove the article
						if($_POST['sa'] == 'edit' && !empty($_POST['id']))
						{
							$smcFunc['db_query']('', '
								DELETE FROM {db_prefix}portamx_articles
								WHERE id = {int:id}',
								array('id' => $_POST['id'])
							);

							clearCachedBlocks($_POST['id'], $_POST['catid']);
						}

						// redirect back to the blocks manager
						@list($mode, $side, $bid) = explode('.', $_POST['fromblock']);
						redirectexit('action='. $admMode .';area=pmx_blocks;sa='. $side .';'. $context['session_var'] .'=' .$context['session_id']);
					}

					// else overview
					$context['pmx']['subaction'] = 'overview';
				}

				// actions from edit article
				elseif($context['pmx']['subaction'] == 'editnew' || $context['pmx']['subaction'] == 'edit')
				{
					$context['pmx']['fromblock'] = $_POST['fromblock'];

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

					if(isset($_POST['content']) && PortaMx_makeSafeContent($_POST['content']) != '')
					{
						// convert html/script to bbc
						if($_POST['ctype'] == 'bbc' && in_array($_POST['contenttype'], array('html', 'code')))
						{
							require_once($sourcedir . '/Subs-Editor.php');
							$user_info['smiley_set'] = 'PortaMx';
							$_POST['content'] = html_to_bbc($_POST['content']);
							$_POST['content'] = preg_replace_callback('/(\n)(\s)/', create_function('$matches', 'return $matches[1];'), $_POST['content']);
						}

						// convert bbc to html/script
						elseif($_POST['contenttype'] == 'bbc' && in_array($_POST['ctype'], array('html', 'code')))
						{
							require_once($sourcedir . '/Subs-Editor.php');
							$_POST['content'] = PortaMx_BBCsmileys(parse_bbc(PortaMx_makeSafeContent($_POST['content'], $_POST['contenttype']), false));
						}

						// handling special php blocks
						elseif($_POST['ctype'] == 'php' && $_POST['contenttype'] == 'php')
							pmxPHP_convert();
					}

					// get all data
					$article = array(
						'id' => $_POST['id'],
						'name' => PortaMx_makeSafe($_POST['name']),
						'catid' => $_POST['catid'],
						'acsgrp' => (!empty($_POST['acsgrp']) ? implode(',', $_POST['acsgrp']) : ''),
						'ctype' => PortaMx_makeSafe($_POST['ctype']),
						'config' => pmx_serialize($_POST['config']),
						'content' => $_POST['content'],
						'active' => $_POST['active'],
						'owner' => $_POST['owner'],
						'created' => $_POST['created'],
						'approved' => $_POST['approved'],
						'approvedby' => $_POST['approvedby'],
						'updated' => $_POST['updated'],
						'updatedby' => $_POST['updatedby'],
					);

					// save article if have content..
					if(!empty($article['content']) && empty($_POST['edit_change']) && (!empty($_POST['save_edit']) || (!empty($article['content']) && !empty($_POST['save_edit_continue']))))
					{
						// if new article get the last id
						if($context['pmx']['subaction'] == 'editnew')
						{
							$request = $smcFunc['db_query']('', '
								SELECT MAX(id)
								FROM {db_prefix}portamx_articles',
								array()
							);
							list($dbid) = $smcFunc['db_fetch_row']($request);
							$smcFunc['db_free_result']($request);
							$article['id'] = strval(1 + ($dbid === null ? $article['id'] : $dbid));
							$article['created'] = forum_time();

							// auto approve for admins
							if(allowPmx('pmx_admin'))
							{
								$article['approved'] = forum_time();
								$article['approvedby'] = $user_info['id'];
							}

							// insert new article
							$smcFunc['db_insert']('ignore', '
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
									$article['id'],
									$article['name'],
									$article['catid'],
									$article['acsgrp'],
									$article['ctype'],
									$article['config'],
									$article['content'],
									$article['active'],
									$article['owner'],
									$article['created'],
									$article['approved'],
									$article['approvedby'],
									$article['updated'],
									$article['updatedby'],
								),
								array()
							);

						}
						else
						{
							$article['updated'] = forum_time();
							$article['updatedby'] = $user_info['id'];

							// update the article
							$smcFunc['db_query']('', '
								UPDATE {db_prefix}portamx_articles
								SET name = {string:name}, catid = {int:catid}, acsgrp = {string:acsgrp}, ctype = {string:ctype}, config = {string:config},
										content = {string:content}, active = {int:active}, owner = {int:owner}, created = {int:created}, approved = {int:approved},
										approvedby = {int:approvedby}, updated = {int:updated}, updatedby = {int:updatedby}
								WHERE id = {int:id}',
								array(
									'id' => $article['id'],
									'name' => $article['name'],
									'catid' => $article['catid'],
									'acsgrp' => $article['acsgrp'],
									'ctype' => $article['ctype'],
									'config' => $article['config'],
									'content' => $article['content'],
									'active' => $article['active'],
									'owner' => $article['owner'],
									'created' => $article['created'],
									'approved' => $article['approved'],
									'approvedby' => $article['approvedby'],
									'updated' => $article['updated'],
									'updatedby' => $article['updatedby']
								)
							);
						}
						$context['pmx']['subaction'] = 'edit';
					}

					// continue edit ?
					if(!empty($_POST['save_edit']) || !empty($_POST['save_edit_continue']))
					{
						if(empty($_POST['save_edit_continue']))
						{
							// edit done, is a move/clone from blocks?
							if(!empty($context['pmx']['fromblock']))
							{
								@list($mode, $side, $bid) = explode('.', $context['pmx']['fromblock']);

								// is block moved?
								if($mode == 'move')
								{
									$request = $smcFunc['db_query']('', '
										SELECT pos, blocktype
										FROM {db_prefix}portamx_blocks
										WHERE id = {int:bid}',
										array('bid' => $bid)
									);
									$block = $smcFunc['db_fetch_assoc']($request);
									$smcFunc['db_free_result']($request);

									// update all pos >= moved id
									$smcFunc['db_query']('', '
										UPDATE {db_prefix}portamx_blocks
										SET pos = pos - 1
										WHERE side = {string:side} AND pos >= {int:pos}',
										array('side' => $side, 'pos' => $block['pos'])
									);

									// delete the block
									$smcFunc['db_query']('', '
										DELETE FROM {db_prefix}portamx_blocks
										WHERE id = {int:id}',
										array('id' => $bid)
									);

									// clear block and SEF pages list
									$pmxCacheFunc['clear']($block['blocktype'] . $bid, true);
									if($side == 'pages')
										$pmxCacheFunc['clear']('pmxsef_pageslist', false);
								}
							}

							// go to article overview
							$context['pmx']['subaction'] = 'overview';
							$context['pmx']['articlestart'] = getCurrentPage($article['id'], $context['pmx']['settings']['manager']['artpage']);
						}
					}

					// clear cached blockd
					clearCachedBlocks($article['id'], $article['catid']);
				}

				if($context['pmx']['subaction'] == 'overview')
				{
					if(!isset($context['pmx']['articlestart']))
						$context['pmx']['articlestart'] = 0;
					redirectexit('action='. $admMode .';area=pmx_articles;'. $context['session_var'] .'=' .$context['session_id'] .';pg='. $context['pmx']['articlestart']);
				}
			}

			// load template, setup pagetitle
			loadTemplate($context['pmx_templatedir'] .'AdminArticles');
			$context['page_title'] = $txt['pmx_articles'];
			$context['pmx']['AdminMode'] = $admMode;

			// direct edit request?
			if(isset($_GET['sa']) && PortaMx_makeSafe($_GET['sa']) == 'edit' && !empty($_GET['id']))
			{
				// move or clone from blocks?
				if(isset($_GET['from']))
				{
					$context['pmx']['fromblock'] = PortaMx_makeSafe($_GET['from']) .'.'. PortaMx_makeSafe($_GET['id']);

					// load the block
					$request = $smcFunc['db_query']('', '
						SELECT *
						FROM {db_prefix}portamx_blocks
						WHERE id = {int:id}',
						array(
							'id' => PortaMx_makeSafe($_GET['id'])
						)
					);
					$row = $smcFunc['db_fetch_assoc']($request);
					$smcFunc['db_free_result']($request);

					// modify the config array
					$cfg = unserialize($row['config']);
					if(isset($cfg['pagename']))
					{
						$pgname = $cfg['pagename'];
						unset($cfg['pagename']);
					}
					else
						$pgname = '';
					unset($cfg['ext_opts']);
					if(isset($cfg['frontmode']))
						unset($cfg['frontmode']);
					$cfg['can_moderate'] = allowedTo('admin_forum') ? 0 : 1;

					$article = array(
						'id' => 0,
						'name' => $pgname,
						'catid' => 0,
						'acsgrp' => $row['acsgrp'],
						'ctype' => ($row['blocktype'] == 'script' ? 'code' : ($row['blocktype'] == 'bbc_script' ? 'bbc' : $row['blocktype'])),
						'config' => pmx_serialize($cfg),
						'content' => $row['content'],
						'active' => 0,
						'owner' => $user_info['id'],
						'created' => 0,
						'approved' => 0,
						'approvedby' => 0,
						'updated' => 0,
						'updatedby' => 0,
					);

					$context['pmx']['subaction'] = 'editnew';
					$context['pmx']['articlestart'] = 0;
				}

				// load the article for edit
				else
				{
					$context['pmx']['fromblock'] = '';

					$request = $smcFunc['db_query']('', '
						SELECT *
						FROM {db_prefix}portamx_articles
						WHERE id = {int:id}',
						array(
							'id' => PortaMx_makeSafe($_GET['id'])
						)
					);

					if($smcFunc['db_num_rows']($request) > 0)
					{
						$row = $smcFunc['db_fetch_assoc']($request);
						$article = array(
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

						$context['pmx']['subaction'] = 'edit';
						$context['pmx']['articlestart'] = 0;
					}
				}
			}

			// continue edit or overview ?
			if($context['pmx']['subaction'] == 'overview')
			{
				// load articel data for overview
				if(!allowPmx('pmx_articles') && allowPmx('pmx_create', true))
					$where = 'WHERE a.owner = {int:owner}';
				else
					$where = '';

				if(!isset($_SESSION['PortaMx']['filter']))
					$_SESSION['PortaMx']['filter'] = array('category' => '', 'approved' => 0, 'active' => 0, 'myown' => 0, 'member' => '');

				if($_SESSION['PortaMx']['filter']['category'] != '')
					$where .= (empty($where) ? 'WHERE ' : ' AND '). 'a.catid IN ({array_int:catfilter})';

				if($_SESSION['PortaMx']['filter']['approved'] != 0)
				{
					$where .= (empty($where) ? 'WHERE ' : ' AND ');
					if($_SESSION['PortaMx']['filter']['active'] != 0)
						$where .= '(a.approved = 0 OR a.active = 0)';
					else
						$where .= 'a.approved = 0';
				}

				if($_SESSION['PortaMx']['filter']['active'] != 0)
				{
					$where .= (empty($where) ? 'WHERE ' : ' AND ');
					if($_SESSION['PortaMx']['filter']['approved'] != 0)
						$where .= '(a.active = 0 OR a.approved = 0)';
					else
						$where .= 'a.active = 0';
				}

				if($_SESSION['PortaMx']['filter']['myown'] != 0)
					$where .= (empty($where) ? 'WHERE ' : ' AND ') .'a.owner = {int:owner}';

				if($_SESSION['PortaMx']['filter']['member'] != '')
					$where .= (empty($where) ? 'WHERE ' : ' AND ') .'m.member_name LIKE {string:memname}';

				if(isset($_GET['pg']) && !is_array($_GET['pg']))
				{
					$context['pmx']['articlestart'] = PortaMx_makeSafe($_GET['pg']);
					unset($_GET['pg']);
				}
				elseif(!isset($context['pmx']['articlestart']))
					$context['pmx']['articlestart'] = 0;

				$cansee = allowPmx('pmx_articles, pmx_create', true);
				$isadmin = allowPmx('pmx_admin');

				$memerIDs = array();
				$context['pmx']['articles'] = array();
				$context['pmx']['article_rows'] = array();
				$context['pmx']['totalarticles'] = 0;
				$result = null;

				$request = $smcFunc['db_query']('', '
					SELECT a.id, a.name, a.catid, a.acsgrp, a.ctype, a.config, a.active, a.owner, a.created, a.approved, a.approvedby, a.updated, a.updatedby, a.content, c.artsort, c.level, c.name AS catname
					FROM {db_prefix}portamx_articles AS a'. ($_SESSION['PortaMx']['filter']['member'] != '' ? '
					LEFT JOIN {db_prefix}members AS m ON (a.owner = m.id_member)' : '') .'
					LEFT JOIN {db_prefix}portamx_categories AS c ON (a.catid = c.id)
					'. $where .'
					ORDER BY a.id',
					array(
						'catfilter' => Pmx_StrToArray($_SESSION['PortaMx']['filter']['category']),
						'memname' => str_replace('*', '%', $_SESSION['PortaMx']['filter']['member']),
						'owner' => $user_info['id'])
				);
				if($smcFunc['db_num_rows']($request) > 0)
				{
					while($row = $smcFunc['db_fetch_assoc']($request))
					{
						$cfg = unserialize($row['config']);
						if(!empty($isadmin) || ($cansee && !empty($cfg['can_moderate'])))
						{
							$memerIDs[] = $row['owner'];
							$memerIDs[] = $row['approvedby'];
							$memerIDs[] = $row['updatedby'];

							$context['pmx']['article_rows'][$row['id']] = array(
								'name' => $row['name'],
								'cat' => str_repeat('&bull;', $row['level']) . $row['catname'],
							);

							$result[] = array(
								'id' => $row['id'],
								'name' => $row['name'],
								'catid' => $row['catid'],
								'cat' => str_repeat('&bull;', $row['level']) . $row['catname'],
								'acsgrp' => $row['acsgrp'],
								'ctype' => $row['ctype'],
								'config' => $cfg,
								'active' => $row['active'],
								'owner' => $row['owner'],
								'created' => $row['created'],
								'approved' => $row['approved'],
								'approvedby' => $row['approvedby'],
								'updated' => $row['updated'],
								'updatedby' => $row['updatedby'],
								'content' => $row['content'],
							);
						}
					}
					$smcFunc['db_free_result']($request);

					if(!empty($result))
					{
						$context['pmx']['totalarticles'] = count($result);
						if($context['pmx']['totalarticles'] <= $context['pmx']['articlestart'])
							$context['pmx']['articlestart'] = 0;

						$st = $context['pmx']['articlestart'];
						while($st < $context['pmx']['articlestart'] + $context['pmx']['settings']['manager']['artpage'] && isset($result[$st]))
						{
							$context['pmx']['articles'][$st] = $result[$st];
							$st++;
						}
						unset($result);

						// get all membernames
						$request = $smcFunc['db_query']('', '
							SELECT id_member, member_name
							FROM {db_prefix}members
							WHERE id_member IN ({array_int:members})',
							array('members' => array_unique($memerIDs))
						);
						if($smcFunc['db_num_rows']($request) > 0)
						{
							while($row = $smcFunc['db_fetch_assoc']($request))
								$context['pmx']['articles_member'][$row['id_member']] = $row['member_name'];
							$smcFunc['db_free_result']($request);
						}
					}
				}
				$context['html_headers'] .= '
	<script type="text/javascript" src="'. PortaMx_loadCompressed('PortaMxPopup.js') .'"></script>';
			}
			elseif(empty($_POST['save_edit']))
			{
				// prepare the editor
				PortaMx_EditArticle($article['ctype'], 'content', $article['content']);

				// load the class file and create the object
				require_once($context['pmx_sysclassdir']. 'PortaMx_AdminArticlesClass.php');
				$context['pmx']['editarticle'] = new PortaMxC_SystemAdminArticle($article);
				$context['pmx']['editarticle']->pmxc_AdmArticle_loadinit();
			}
		}
		else
			fatal_error($txt['pmx_acces_error']);
	}
}

/**
* Find the currect page
**/
function getCurrentPage($id, $numPage, $delmode = false)
{
	global $smcFunc;

	$start = 0;
	$articlestart = 0;

	$request = $smcFunc['db_query']('', '
		SELECT id
		FROM {db_prefix}portamx_articles
		ORDER BY id ASC',
		array()
	);
	while(($row = $smcFunc['db_fetch_assoc']($request)) && $row['id'] != $id)
	{
		$start++;
		if($start >= $numPage)
		{
			$articlestart += $numPage;
			$start = 0;
		}
	}
	$smcFunc['db_free_result']($request);

	if(!empty($delmode) && !empty($articlestart))
	{
		$request = $smcFunc['db_query']('', '
			SELECT COUNT(id)
			FROM {db_prefix}portamx_articles',
			array()
		);
		list($maxart) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);
		$maxart = $maxart === null ? 0 : $maxart -1;

		if($maxart <= $articlestart)
			$articlestart -= $numPage;
	}

	return $articlestart;
}

/**
* clear cached Category block
**/
function clearCachedBlocks($artid, $catid = 0)
{
	global $smcFunc, $pmxCacheFunc;

	// get article and category blocks
	$blocks = array();
	$request = $smcFunc['db_query']('', '
		SELECT id, blocktype FROM {db_prefix}portamx_blocks
		WHERE blocktype IN ({array_string:types}) AND cache > 0',
		array('types' => array('category', 'article'))
	);
	while($row = $smcFunc['db_fetch_assoc']($request))
		$blocks[] = $row['blocktype'] . $row['id'];
	$smcFunc['db_free_result']($request);

	foreach($blocks as $block)
		$pmxCacheFunc['clear']($block, true);

	if(is_array($artid))
	{
		foreach($artid as $id)
			$pmxCacheFunc['clear']('reqarticle'. $id, true);
	}
	else
		$pmxCacheFunc['clear']('reqarticle'. $artid, true);

	if(is_array($catid))
	{
		foreach($catid as $id)
		{
			$pmxCacheFunc['clear']('category'. $id, true);
			$pmxCacheFunc['clear']('reqcategory'. $id, true);
		}
	}
	else
	{
		$pmxCacheFunc['clear']('category'. $catid, true);
		$pmxCacheFunc['clear']('reqcategory'. $catid, true);
	}

	// clear SEF article & category list
	$pmxCacheFunc['clear']('pmxsef_artlist', false);
	$pmxCacheFunc['clear']('pmxsef_catlist', false);
}
?>
