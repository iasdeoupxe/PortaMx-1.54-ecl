<?php
/**
* \file cbt_navigator.php
* Systemblock cbt_navigator (Categorie-Board-Topic)
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_cbt_navigator
* Systemblock cbt_navigator
* @see cbt_navigator.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_cbt_navigator extends PortaMxC_SystemBlock
{
	var $boards;					///< all boards
	var $topics;					///< all topics
	var $cat_board;				///< all cats
	var $isRead;					///< unread topics by member

	/**
	* InitContent.
	*/
	function pmxc_InitContent()
	{
		global $context, $user_info, $pmxCacheFunc;

		// if visible init the content
		if($this->visible)
		{
			if($this->cfg['cache'] > 0)
			{
				// check the block cache
				if(($cachedata = $pmxCacheFunc['get']($this->cache_key, $this->cache_mode)) !== null)
				{
					list($this->topics, $isRead, $this->boards, $this->cat_board) = $cachedata;
					$this->isRead = (isset($isRead[$user_info['id']]) ? $isRead[$user_info['id']] : null);
					if($this->isRead === null)
					{
						$cachedata = $this->cbt_fetchdata($isRead);
						$pmxCacheFunc['put']($this->cache_key, $cachedata, $this->cache_time, $this->cache_mode);
					}
				}
				else
				{
					$cachedata = $this->cbt_fetchdata();
					$pmxCacheFunc['put']($this->cache_key, $cachedata, $this->cache_time, $this->cache_mode);
				}
				unset($cachedata);
			}
			else
				$this->cbt_fetchdata();

			// no posts .. disable the block
			if(empty($this->boards))
				$this->visible = false;
		}
		// return the visibility flag (true/false)
		return $this->visible;
	}

	/**
	* cbt__fetchdata.
	* Fetch all Categories, Boards and Topics.
	*/
	function cbt_fetchdata($isRead = null)
	{
		global $context, $smcFunc, $user_info, $modSettings, $settings;

		$this->boards = null;
		$this->topics = null;
		$this->cat_board = null;
		$this->isRead = null;
		$curRead = $this->get_isRead();

		if(isset($this->cfg['config']['settings']['recentboards']) && !empty($this->cfg['config']['settings']['recentboards']))
		{
			// get Categories, Board, Topics and Messages .. what a monstrous query ;-)
			$request = $smcFunc['db_query']('', '
					SELECT c.name AS catname, c.id_cat,
						b.name AS boardname, b.id_board, b.child_level, b.redirect,
						COALESCE(t.id_topic, 0) AS id_topic, COALESCE(t.id_last_msg, 0) AS id_last_msg,
						m.id_msg, m.subject, m.poster_name, m.poster_time, '. ($user_info['is_guest'] ? '1 AS isRead, 0 AS new_from' : '
						COALESCE(lt.id_msg, COALESCE(lmr.id_msg, 0)) >= m.id_msg_modified AS isRead,
						COALESCE(lt.id_msg, COALESCE(lmr.id_msg, -1)) + 1 AS new_from') .'
					FROM {db_prefix}boards AS b
					LEFT JOIN {db_prefix}topics AS t ON (t.id_board = b.id_board)
					LEFT JOIN {db_prefix}categories AS c ON (b.id_cat = c.id_cat)
					LEFT JOIN {db_prefix}messages AS m ON (t.id_last_msg = m.id_msg)'. (!$user_info['is_guest'] ? '
					LEFT JOIN {db_prefix}log_topics AS lt ON (t.id_topic = lt.id_topic AND lt.id_member = {int:idmem})
					LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = m.id_board AND lmr.id_member = {int:idmem})' : '').'
					WHERE b.id_board IN ({array_int:boards}) AND {query_wanna_see_board}
					'. ($modSettings['postmod_active'] ? ' AND m.approved = {int:approv}' : '') .'
					AND (b.id_last_msg = m.id_msg OR t.id_last_msg >= {int:min_msg} OR t.id_last_msg IS NULL)
					ORDER BY c.cat_order ASC, b.board_order ASC, t.id_board ASC, m.poster_time DESC',
				array(
					'idmem' => $user_info['id'],
					'boards' => $this->cfg['config']['settings']['recentboards'],
					'min_msg' => $modSettings['maxMsgID'] - 100 * $this->cfg['config']['settings']['numrecent'],
					'approv' => 1,
				)
			);

			// now sort out all the records
			$bID = 0;
			$cID = 0;
			$topic = null;
			$rowsRead = $smcFunc['db_num_rows']($request);

			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				// new categorie ?
				if($cID != $row['id_cat'])
				{
					// yes, save topics from previous cat / board
					if(!empty($cID) && !empty($bID))
					{
						$this->cat_board[$cID]['boards'][$bID]['topics'] = $topic;
						$topic = array();
					}

					// save the new cat
					$cID = $row['id_cat'];
					$this->cat_board[$cID]['name'] = $row['catname'];
					$bID = 0;
				}

				// same categorie ?
				if($cID == $row['id_cat'])
				{
					// yes, new board?
					if($bID != $row['id_board'])
					{
						// yes, save topics from previous board
						if(!empty($bID))
						{
							$this->cat_board[$cID]['boards'][$bID]['topics'] = $topic;
							$topic = array();
						}

						// save the new board
						$bID = $row['id_board'];
						if(empty($row['redirect']) && !empty($row['id_topic']))
							$this->boards[] = $bID;

						$this->cat_board[$cID]['boards'][$bID] = array(
							'name' => $row['boardname'],
							'level' => $row['child_level'],
							'isredir' => !empty($row['redirect']),
							'hastopics' => !empty($row['id_topic']),
						);

						// setup the topic count
						$count = (!empty($row['id_topic']) ? $this->cfg['config']['settings']['numrecent'] : 0);
					}
				}

				// count the topics
				if($count > 0)
				{
					$this->topics[] = $row['id_topic'];
					$this->isRead[$row['id_topic']] = $row['id_topic'] != $curRead['topic'] && empty($row['isRead']) ? false : true;

					censorText($row['subject']);
					$topic[$row['id_topic']] = array(
						'subject' => $row['subject'],
						'post_name' => $row['poster_name'],
						'post_time' => preg_replace('~<[^>]*>~i', '', timeformat($row['poster_time'])),
						'id_msg' => $row['id_msg'],
						'last_msg' => $row['id_last_msg'],
						'new_from' => $row['new_from'],
					);
					$count --;
				}
			}

			// save last topics
			if(!empty($bID))
				$this->cat_board[$cID]['boards'][$bID]['topics'] = $topic;

			// done
			$smcFunc['db_free_result']($request);

			$isRead[$user_info['id']] = $this->isRead;
			return array($this->topics, $isRead, $this->boards, $this->cat_board);
		}
	}

	/**
	* ShowContent.
	* Output the content from Categories, Boards and Topics.
	*/
	function pmxc_ShowContent()
	{
		global $context, $user_info, $modSettings, $scripturl, $txt;

		echo '
				<script type="text/javascript"><!-- // --><![CDATA[
					var CBTboardIDs = new Array("'. implode('","', $this->boards) .'");
					function NavCatToggle(brdid)
					{
						var cstat = document.getElementById("pmxcbt'. $this->cfg['id'] .'.brd."+ brdid).style.display;
						var img = "'. $context['pmx_imageurl'] .'"+ (cstat == "none" ? "minus.png" : "plus.png");
						document.getElementById("pmxcbt'. $this->cfg['id'] .'.img."+ brdid).src = img;
						document.getElementById("pmxcbt'. $this->cfg['id'] .'.brd."+ brdid).style.display = (cstat == "none" ? "" : "none");
						var cook = "0.";
						for(var i = 0; i < CBTboardIDs.length; i++)
						{
							if(CBTboardIDs[i] == brdid)
								cook = cook + (cstat == "none" ? CBTboardIDs[i] : "" +".");
							else
								cook = cook + (document.getElementById("pmxcbt'. $this->cfg['id'] .'.brd."+ CBTboardIDs[i]).style.display == "none" ? "" : CBTboardIDs[i] +".");
						}
						pmx_setCookie("cbtstat'. $this->cfg['id'] .'", cook);
					}
					function NavCatToggleALL(mode)
					{
						var cook = "0.";
						for(var i = 0; i < CBTboardIDs.length; i++)
						{
							document.getElementById("pmxcbt'. $this->cfg['id'] .'.brd."+ CBTboardIDs[i]).style.display = (mode == 0 ? "none" : "");
							document.getElementById("pmxcbt'. $this->cfg['id'] .'.img."+ CBTboardIDs[i]).src = (mode == 0 ? "'. $context['pmx_imageurl'] .'plus.png" : "'. $context['pmx_imageurl'] .'minus.png");
							cook = cook + (mode == 0 ? "" : CBTboardIDs[i] +".");
						}
						pmx_setCookie("cbtstat'. $this->cfg['id'] .'", cook);
					}
				// ]]></script>

				<div>
					<a href="javascript:void(\'\')" style="float:'. (empty($context['right_to_left']) ? 'left' : 'right') .';" onclick="NavCatToggleALL(1)">'. $txt['pmx_cbt_expandall'] .'</a>
					<a href="javascript:void(\'\')" style="float:'. (empty($context['right_to_left']) ? 'right' : 'left') .';" onclick="NavCatToggleALL(0)">'. $txt['pmx_cbt_collapseall'] .'</a>
				</div>
				<br style="clear:both; line-height:1px;" />
				<hr style="margin-top:3px;" />
				<div style="margin-top: -3px;">';

		// loop through all cats, boards and topics
		$found = pmx_getcookie('cbtstat'. $this->cfg['id']);
		$isInit = is_null($found) && empty($user_info['is_guest']);
		$cook = array();

		if($isInit && !empty($this->cfg['config']['settings']['initexpandnew']))
			$exp = array();
		elseif($isInit && !empty($this->cfg['config']['settings']['initexpand']))
			$exp = $this->boards;
		else
			$exp = !empty($found) ? explode('.', $found) : array();

		foreach($this->cat_board as $cid => $cats)
		{
			if(!empty($cats['boards']))
			{
				echo '
					<div class="cbtshorttxt" style="padding-right:0">
						<a class="cbtshorttxt" style="padding-right:0" href="'. $scripturl . (!empty($modSettings['pmx_frontmode']) ? '?action=community;' : '') .'#c'. $cid .'" title="'. $txt['pmx_text_category'] . $cats['name'] .'"><b>'. $cats['name'].'</b></a>
					</div>';

				foreach($cats['boards'] as $bid => $board)
				{
					$board['unread'] = 0;
					if($board['hastopics'])
					{
						foreach($board['topics'] as $tid => $topic)
							$board['unread'] = empty($this->isRead[$tid]) ? 1 : $board['unread'];
					}

					if($isInit && !empty($board['unread']) && !empty($this->cfg['config']['settings']['initexpandnew']))
						$exp[] = $bid;

					echo '
					<div style="margin-bottom:-2px; padding-'. (empty($context['right_to_left']) ? 'left' : 'right') .':'.($board['level'] * 8).'px;">';

					if($board['isredir'] || empty($board['hastopics']))
						echo '
						<img id="pmxcbt'. $this->cfg['id'] .'.img.'. $bid .'" src="'. $context['pmx_imageurl'] . ($board['isredir'] ? 'redir' : 'notopic') .'.png" alt="*" title="" />';
					else
						echo '
						<img class="cbtshortcat" id="pmxcbt'. $this->cfg['id'] .'.img.'. $bid .'" onclick="NavCatToggle(\''. $bid .'\')" src="'. $context['pmx_imageurl'] . (in_array($bid, $exp) ? 'minus' : 'plus') .'.png" alt="*" title="'. $txt['pmx_cbt_colexp'] . $board['name'] .'" style="cursor:pointer;" />';

					echo '
						<div'. (!$board['unread'] ? ' style="padding-right:0"' : '') .' class="cbtshorttxt">
							<a'. (!$board['unread'] ? ' style="padding-right:0"' : '') .' class="cbtshorttxt" href="'. $scripturl .'?board='. $bid .'.0" title="'. $txt['pmx_text_board'] . $board['name'] .'">'. $board['name'] .'</a>
							'.($board['unread'] ? '<img src="'. $context['pmx_imageurl'] .'unread.gif" alt="*" title="" />' : '').'
						</div>
					</div>';

					if($board['hastopics'])
					{
						echo '
					<div id="pmxcbt'. $this->cfg['id'] .'.brd.'. $bid .'" style="margin-bottom:2px;'. (!in_array($bid, $exp) ? ' display:none' : '') .'">';

						foreach($board['topics'] as $tid => $topic)
						{
							$ttl = $txt['pmx_text_topic'] . $topic['subject'] .' '. $txt['by'] .' '. $topic['post_name'] .', '. $topic['post_time'];
							echo '
						<div class="cbtshorttxt" style="'. (!empty($this->isRead[$tid]) ? 'padding-right:0; ' : '') .'padding-left:'. (17 + ($board['level'] * 5)).'px;">
							<a class="cbtshorttxt"'. (!empty($this->isRead[$tid]) ? ' style="padding-right:0"' : '') .' href="'. $scripturl .'?topic='. $tid .'.msg'. (empty($this->isRead[$tid]) ? $topic['new_from'] .';topicseen#new' : $topic['id_msg'] .';topicseen#msg'. $topic['id_msg']) .'" title="'. $ttl .'">'. $topic['subject'] .'</a>
							'. (empty($this->isRead[$tid]) ? '<img src="'. $context['pmx_imageurl'] .'unread.gif" alt="*" title="" />' : '') .'
						</div>';
						}

						echo '
					</div>';
					}
				}
			}
		}
		// done
		echo '
				</div>';
	}
}
?>
