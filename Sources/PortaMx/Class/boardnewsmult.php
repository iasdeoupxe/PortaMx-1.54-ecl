<?php
/**
* \file boardnewsmult.php
* Systemblock multiple boardnews
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_boardnewsmult
* Systemblock multiple boardnews
* @see boardnewsmult.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_boardnewsmult extends PortaMxC_SystemBlock
{
	var $posts;				///< all posts
	var $attaches;		///< all ataches
	var $imgName;			///< rescale image name

	/**
	* InitContent.
	* Checks the cache status and create the content.
	*/
	function pmxc_InitContent()
	{
		global $context, $scripturl, $pmxCacheFunc;

		// if visible init the content
		if($this->visible)
		{
			if($this->cfg['cache'] > 0)
			{
				// check the block cache
				if(($cachedata = $pmxCacheFunc['get']($this->cache_key, $this->cache_mode)) !== null)
					list($dummy, $dummy, $this->posts, $this->attaches, $this->imgName) = $cachedata;
				else
				{
					$this->fetch_data();
					$cachedata = array(array(), array(), $this->posts, $this->attaches, $this->imgName);
					$pmxCacheFunc['put']($this->cache_key, $cachedata, $this->cache_time, $this->cache_mode);
				}
				unset($cachedata);
			}
			else
				$this->fetch_data();

			// no posts .. disable the block
			if(empty($this->posts))
				$this->visible = false;

			// paging...
			elseif(!empty($this->cfg['config']['settings']['onpage']) && count($this->posts) > $this->cfg['config']['settings']['onpage'])
				$this->pmxc_constructPageIndex(count($this->posts), $this->cfg['config']['settings']['onpage']);
		}

		// return the visibility flag (true/false)
		return $this->visible;
	}

	/**
	* fetch_data.
	* Fetch Boards, Topics, Messages and Attaches.
	*/
	function fetch_data()
	{
		global $context, $smcFunc, $user_info, $modSettings, $settings, $scripturl, $txt;

		$boards = !empty($this->cfg['config']['settings']['board']) ? $this->cfg['config']['settings']['board'] : array();
		$this->cfg['config']['settings']['total'] = empty($this->cfg['config']['settings']['total']) ? 1 : $this->cfg['config']['settings']['total'];
		$this->posts = null;
		$this->attaches = null;
		$this->imgName = '';

		if(!empty($boards))
		{
			// Load the message icons
			$stable_icons = array('xx', 'thumbup', 'thumbdown', 'exclamation', 'question', 'lamp', 'smiley', 'angry', 'cheesy', 'grin', 'sad', 'wink', 'moved', 'recycled', 'wireless');
			$icon_sources = array();
			foreach ($stable_icons as $icon)
				$icon_sources[$icon] = 'images_url';

			// find the n post from each board
			$this->cfg['config']['settings']['total'] = empty($this->cfg['config']['settings']['total']) ? 1 : $this->cfg['config']['settings']['total'];
			$msgids = array();
			$curboard = 0;

			$request = $smcFunc['db_query']('', '
				SELECT b.id_board, b.name, t.id_topic, t.num_replies, t.num_views, m.*
				FROM {db_prefix}topics as t
				LEFT JOIN {db_prefix}boards as b ON (t.id_board = b.id_board)
				LEFT JOIN {db_prefix}messages as m ON (t.id_first_msg = m.id_msg)
				WHERE b.id_board IN ({array_int:boards}) AND {query_wanna_see_board}
					'. ($modSettings['postmod_active'] ? ' AND m.approved = {int:approv}' : '') .'
					AND t.id_last_msg >= {int:min_msg}
				ORDER BY b.id_board ASC, t.id_topic DESC',
				array(
					'boards' => $boards,
					'min_msg' => $modSettings['maxMsgID'] - 100 * $this->cfg['config']['settings']['total'],
					'approv' => 1
				)
			);
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				if($row['id_board'] != $curboard)
				{
					$curboard = $row['id_board'];
					$max = $this->cfg['config']['settings']['total'];
				}
				if(!empty($max))
				{
					$msgids[$row['id_topic']] = $row['id_msg'];
					$max--;
					$row['body'] = parse_bbc($row['body'], $row['smileys_enabled'], $row['id_msg']);

					// Check that this message icon is there...
					if (empty($modSettings['messageIconChecks_disable']) && !isset($icon_sources[$row['icon']]))
						$icon_sources[$row['icon']] = file_exists($settings['theme_dir'] . '/images/post/' . $row['icon'] . '.gif') ? 'images_url' : 'default_images_url';

					censorText($row['subject']);
					censorText($row['body']);

					// remove highslide code for highslide body
					$row['hsbody'] = $this->removeHiglideCode($row['body']);

					// on rescale remove highslide code
					$row['body'] = (!empty($this->cfg['config']['settings']['rescale']) ? $row['hsbody'] : $row['body']);

					// teaser enabled ?
					if(!empty($this->cfg['config']['settings']['teaser']))
						$row['body'] = PortaMx_Tease_posts($row['body'], $this->cfg['config']['settings']['teaser'], '', false, false);

					// Rescale inline Images ?
					if(!empty($this->cfg['config']['settings']['rescale']))
					{
						if(preg_match_all('~<img[^>]*>~iS', $row['body'], $matches) > 0)
						{
							foreach($matches[0] as $i => $data)
							{
								if(strpos($data, $modSettings['smileys_url']) !== false)
									unset($matches[0][$i]);
							}
							if(count($matches[0]) > 0)
							{
								$this->imgName = 'pmx_rscimg'. $this->cfg['id'];
								$fnd = array('~ width?=?"\d+"~', '~ height?=?"\d+"~', '~ class?=?"[^"]*"~');

								// modify the images for highslide
								foreach($matches[0] as $i => $data)
								{
									$datlen = strlen($data);
									preg_match('~src?=?"([^\"]*\")~i', $data, $src);
									$tmp = str_replace($src[0], ' name="'. $this->imgName .'" title="'. substr(strrchr($src[1], '/'), 1) .' '. $src[0], preg_replace($fnd, '', $data));

									if(!empty($context['pmx']['settings']['disableHS']))
										$row['body'] = substr_replace($row['body'], $tmp, strpos($row['body'], $data), $datlen);
									else
									{
										if(empty($this->cfg['config']['settings']['disableHS']))
										{
											$row['body'] = substr_replace($row['body'], $tmp, strpos($row['body'], $data), $datlen);

											if(empty($this->cfg['config']['settings']['disableHSimg']))
												$row['hsbody'] = substr_replace($row['hsbody'],
													'<a href="'. $src[1].' class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">' .$tmp .'</a>',
													strpos($row['hsbody'], $data), $datlen);
											else
												$row['hsbody'] = substr_replace($row['hsbody'], $tmp, strpos($row['hsbody'], $data), $datlen);
										}
										elseif(empty($this->cfg['config']['settings']['disableHSimg']))
											$row['body'] = substr_replace($row['body'],
												'<a href="'. $src[1].' class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">' .$tmp .'</a>',
												strpos($row['body'], $data), $datlen);
										else
											$row['body'] = substr_replace($row['body'], $tmp, strpos($row['body'], $data), $datlen);
									}
								}
							}
						}
					}
					elseif(is_numeric($this->cfg['config']['settings']['rescale']))
						$row['body'] = PortaMx_revoveLinks($row['body'], false, true);
					$HSremImg = empty($this->cfg['config']['settings']['rescale']) && is_numeric($this->cfg['config']['settings']['rescale']);
					$row['hsbody'] = PortaMx_revoveLinks($row['hsbody'], true, $HSremImg);

					$this->posts[] = array(
						'id_board' => $row['id_board'],
						'board_name' => $row['name'],
						'id' => $row['id_topic'],
						'message_id' => $row['id_msg'],
						'icon' => '<img src="' . $settings[$icon_sources[$row['icon']]] . '/post/' . $row['icon'] . '.gif" align="middle" alt="' . $row['icon'] . '" border="0" />',
						'subject' => $row['subject'],
						'time' => timeformat($row['poster_time']),
						'timestamp' => forum_time(true, $row['poster_time']),
						'body' => $row['body'],
						'hsbody' => $row['hsbody'],
						'replies' => $row['num_replies'],
						'views' => $row['num_views'],
						'poster' => array(
							'id' => $row['id_member'],
							'name' => $row['poster_name'],
							'href' => !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
							'link' => !empty($row['id_member']) ? '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['poster_name'] . '</a>' : $row['poster_name']
						),
						'locked' => !empty($row['locked']),
					);
				}
			}
			$smcFunc['db_free_result']($request);

			// any post found?
			if(!is_null($this->posts))
			{
				// get attachments if show thumnails set
				$allow_boards = boardsAllowedTo('view_attachments');
				if(!empty($this->cfg['config']['settings']['thumbs']) && !empty($allow_boards))
				{
					$request = $smcFunc['db_query']('', '
						SELECT a.id_msg, a.id_attach, a.id_thumb, a.filename, m.id_topic
						FROM {db_prefix}attachments AS a
						LEFT JOIN {db_prefix}messages AS m ON (a.id_msg = m.id_msg)
						WHERE a.id_msg IN({array_int:messages}) AND a.mime_type LIKE {string:like}'.
							($allow_boards === array(0) ? '' : (!$modSettings['postmod_active'] || allowedTo('approve_posts') ? '' : ' AND m.approved = 1 AND a.approved = 1') .' AND m.id_board IN ({array_int:boards})') .'
						ORDER BY m.id_msg DESC, a.id_attach ASC',
						array(
							'messages' => $msgids,
							'like' => 'IMAGE%',
							'boards' => $allow_boards,
						)
					);

					$thumbs = array();
					$msgcnt = array();
					$saved = !empty($this->cfg['config']['settings']['thumbcnt']) ? $this->cfg['config']['settings']['thumbcnt'] : 0;
					while($row = $smcFunc['db_fetch_assoc']($request))
					{
						if(!in_array($row['id_attach'], $thumbs))
						{
							if(!empty($this->cfg['config']['settings']['thumbcnt']))
							{
								if(!in_array($row['id_msg'], $msgcnt))
									$saved = $this->cfg['config']['settings']['thumbcnt'];
								elseif(in_array($row['id_msg'], $msgcnt) && empty($saved))
									continue;
							}

							$saved--;
							$msgcnt[] = $row['id_msg'];
							$thumbs[] = $row['id_thumb'];
							$this->attaches[$row['id_msg']][] = array(
								'topic' => $row['id_topic'],
								'image' => $row['id_attach'],
								'thumb' => empty($row['id_thumb']) ? $row['id_attach'] : $row['id_thumb'],
								'fname' => str_replace('_thumb', '', $row['filename'])
							);
						}
					}
					$smcFunc['db_free_result']($request);
				}
			}
		}
	}

	/**
	* Remove HighSlide code from message
	*/
	function removeHiglideCode($message)
	{
		preg_match_all('~<a[^>]*>(<img[^>]*>)<\/a>~imS', $message, $matches, PREG_SET_ORDER);
		foreach($matches as $data)
		{
			if(preg_match('/class.?=.?\"highslide\"/is', $data[0]) > 0)
				$message = substr_replace($message, $data[1], strpos($message, $data[0]), strlen($data[0]));
		}
		return $message;
	}

	/**
	* ShowContent.
	* Output the content and add necessary javascript
	*/
	function pmxc_ShowContent()
	{
		global $context, $settings, $modSettings, $scripturl, $txt;

		// ini all vars
		$this->LR = empty($context['right_to_left']) ? 'left' : 'right';
		$this->RL = empty($context['right_to_left']) ? 'right' : 'left';
		$this->is_Split = $this->cfg['config']['settings']['split'];
		$this->is_last = (!empty($this->pageindex) ? ($this->startpage + $this->postspage > count($this->posts) ? count($this->posts) - $this->startpage : $this->postspage) : count($this->posts));
		$this->half = (!empty($this->is_Split) ? ceil($this->is_last / 2) : $this->is_last);
		$this->spanlast = intval(!empty($this->is_Split) && ($this->half * 2) > $this->is_last && count($this->posts) > 1);
		$this->half = $this->half - $this->spanlast;
		$this->halfpad = ceil($context['pmx']['settings']['panelpad'] / 2);
		$this->fullpad = $context['pmx']['settings']['panelpad'];

		// create the classes
		if(!empty($this->cfg['customclass']))
			$this->isCustFrame = !empty($this->cfg['customclass']['postframe']);
		else
			$this->isCustFrame = false;
		$this->spanclass = $this->isCustFrame && !empty($this->cfg['config']['visuals']['postbody']) ? $this->cfg['config']['visuals']['postbody'] .' ' : '';
		$this->postbody = trim($this->cfg['config']['visuals']['postbody'] .' '. $this->cfg['config']['visuals']['postframe']);

		// find the first post
		reset($this->posts);
		for($i = 0; $i < $this->startpage; $i++)
			list($pid, $post) = PMX_Each($this->posts);

		// only one? .. clear split
		if(count($this->posts) - $this->startpage == 1)
			$this->is_Split = false;

		// show the pageindex line
		if(!empty($this->pageindex) && !empty($this->cfg['config']['settings']['pgidxtop']))
			echo '
					<div class="smalltext pmx_pgidx_top">'. $txt['pages']. ': '. $this->pageindex . (!empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#bot'. $this->cfg['uniID'] .'"><strong>' . $txt['go_down'] . '</strong></a>' : '') .'</div>';

		// the maintable
		echo '
					<table width="100%" cellpadding="0" cellspacing="0" border="0" style="table-layout:fixed;">
						<tr>';

		// show posts in two cols?
		if(!empty($this->is_Split))
		{
			$isEQ = !empty($this->cfg['config']['settings']['equal']) && !empty($this->cfg['config']['settings']['split']);
			echo '
							<td width="50%" valign="top">';

			// write out the left part..
			while(!empty($this->half))
			{
				list($pid, $post) = PMX_Each($this->posts);
				$this->pmxc_ShowPost($pid, $post, $isEQ, $this->half == 1);
				next($this->posts);
				$this->half--;
				$this->is_last--;
			}

			echo '
							</td>
							<td width="50%" valign="top">';

			// shift post by 1..
			reset($this->posts);
			for($i = -1; $i < $this->startpage; $i++)
				list($pid, $post) = PMX_Each($this->posts);

			// write out the right part..
			while($this->is_last - $this->spanlast > 0)
			{
				list($pid, $post) = PMX_Each($this->posts);
				$this->pmxc_ShowPost($pid, $post, $isEQ, $this->is_last == 1 && empty($this->spanlast));
				list($pid, $post) = PMX_Each($this->posts);
				$this->is_last--;
			}

			// we have a single post at least?
			if(!empty($this->spanlast))
			{
				echo '
							</td>
						</tr>
						<tr>
							<td colspan="2" valign="top">';

				// clear split and write the last post
				$this->is_Split = false;
				$this->pmxc_ShowPost($pid, $post, false, true);
			}
		}

		// single col
		else
		{
			echo '
							<td valign="top">';

			// each post in a row
			while(!empty($this->is_last))
			{
				list($pid, $post) = PMX_Each($this->posts);
				$this->pmxc_ShowPost($pid, $post, false, $this->is_last == 1);
				$this->half--;
				$this->is_last--;
			}
		}

		echo '
							</td>
						</tr>
					</table>';

		// show pageindex if exists
		if(!empty($this->pageindex))
			echo '
					<div class="smalltext pmx_pgidx_bot">'. $txt['pages']. ': '. $this->pageindex . (!empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#top'. $this->cfg['uniID'] .'"><strong>' . $txt['go_up'] . '</strong></a>' : '') .'</div>';

		// if we have rescale images, setup the javasctipt
		if(!empty($this->imgName))
			echo '
				<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
					var objlenght = pmx_rescale_images.length;
					pmx_rescale_images[objlenght] = new Object;
					pmx_rescale_images[objlenght].scale = '. $this->cfg['config']['settings']['rescale'] .';
					pmx_rescale_images[objlenght].name = \''. $this->imgName .'\';
				// ]]></script>';
	}

	/**
	* Show one Post.
	*/
	function pmxc_ShowPost($pid, $post, $setQE, $lastrow)
	{
		global $context, $settings, $modSettings, $scripturl, $txt;

		// the post main division..
		echo '
						<div'. (!empty($lastrow) ? ' id="bot'. $this->cfg['uniID'] .'"' : '') .' style="margin-'. ((!empty($this->is_Split) ? (!empty($this->half) ? $this->RL : $this->LR) .':'. $this->halfpad .'px; margin-' : '') . (!empty($lastrow) ? 'bottom:0' : 'bottom:'. $this->fullpad)) .'px;'. (!empty($newRow) ? ' margin-top:-'. $this->fullpad .'px;' : '') .'">';

		// post header .. can have none, titlebg/catbg or as body
		if(empty($this->cfg['config']['visuals']['postheader']) || $this->cfg['config']['visuals']['postheader'] == 'none')
		{
			// header none .. we have a postframe?
			if(!empty($this->cfg['config']['visuals']['postframe']))
			{
				if($this->cfg['config']['visuals']['postframe'] == 'roundframe' || $this->isCustFrame)
					echo '
						<span class="notitlebar '. $this->spanclass . ($this->isCustFrame ? $this->cfg['config']['visuals']['postframe'] .'_top' : 'upperframe') .'"><span></span></span>';
				else
					echo '
						<span class="notitlebar '. (!empty($this->cfg['config']['visuals']['postbody']) ? $this->cfg['config']['visuals']['postbody'] .' ' : '') .'toplice"><span></span></span>';
			}

			// no postframe, use bodyclass if set
			echo '
							<div class="roundtitle'. (!empty($this->postbody) ? ' '. $this->postbody : '') .'" style="padding:0 5px;">';

			// cols set to equal height?
			if(!empty($setQE))
				echo '
							<div class="pmxEQH'. $this->cfg['id'] .'">';

			// postheader .. icon and subject
			if(empty($this->cfg['config']['visuals']['postheader']))
			{
				echo '
						<div class="pmx_postheader">'. $post['icon'] .'
							<span class="normaltext cat_msg_title"><a href="'. $scripturl . '?topic=' . $post['id'] . '.0">'. $post['subject'] .'</a></span>
						</div>';

				if(empty($this->cfg['config']['settings']['postinfo']))
					echo '
						<hr style="margin:2px 0;" />';
			}
		}

		// ok, we have postheader .. put icon and subject on it
		else
		{
			echo '
						<div class="'. str_replace('bg', '_bar', $this->cfg['config']['visuals']['postheader']) .' catbg_grid">
							<h4 class="'. $this->cfg['config']['visuals']['postheader'] .' catbg_grid">
								'. $post['icon'] .'<span class="normaltext cat_msg_title"><a href="' . $scripturl . '?topic=' . $post['id'] . '.0">'. $post['subject'] .'</a></span>
							</h4>
						</div>';

			// bodyclass if set
			echo '
						<div'. (!empty($this->postbody) ? ' class="'. $this->postbody .'"' : '') .' style="padding:0 5px;">';

			// cols set to equal height?
			if(!empty($setQE))
				echo '
							<div class="pmxEQH'. $this->cfg['id'] .'">';
		}

		// show the postinfo lines if enabled
		if(!empty($this->cfg['config']['settings']['postinfo']))
		{
			if(!empty($this->cfg['config']['settings']['postviews']))
				echo '
							<div class="smalltext" style="float:'. $this->LR .';">
								'. $txt['pmx_text_postby'] . $post['poster']['link'] .', '. $post['time'] .'
							</div>
							<div class="smalltext" style="float:'. $this->RL .';">
								'. $txt['pmx_text_replies'] . $post['replies'] .'
							</div>
							<br style="clear:both;" />
							<div class="smalltext msg_bot_pad" style="float:'. $this->LR .';">
								'. $txt['pmx_text_board'] .'<a href="' . $scripturl . '?board='. $post['id_board'] .'.0">'. $post['board_name'] .'</a>
							</div>
							<div class="smalltext msg_bot_pad" style="float:'. $this->RL .';">
							'. $txt['pmx_text_views'] . $post['views'] .'
							</div>
							<hr style="clear:both;" />';
			else
			{
				echo '
							<div class="smalltext" style="float:'. $this->LR .';">
								'. $txt['pmx_text_postby'] . $post['poster']['link'] .', '. $post['time'] .'
							</div>';

				if(empty($this->is_Split))
					echo'
							<div class="smalltext msg_bot_pad" style="float:'. $this->RL .';">';
				else
					echo '
							<br style="clear:both;" />
							<div class="smalltext msg_bot_pad" style="float:'. $this->LR .';">';

				echo '
								'. $txt['pmx_text_board'] .'<a href="' . $scripturl . '?board='. $post['id_board'] .'.0">'. $post['board_name'] .'</a>
							</div>
							<hr style="clear:both;" />';
			}
		}

		// if highslide enabled, create the hidden hs frame
		if(empty($context['pmx']['settings']['disableHS']) && empty($this->cfg['config']['settings']['disableHS']))
			echo '
							<div class="pmxhs_imglink" onmouseover="this.className = \'contentover\'" onmouseout="this.className =\'contentout\'" title="'. $txt['pmx_hs_read'] .'" onclick="return hs.htmlExpand(this,
								{ maincontentId: \'mbn_contid'. $post['message_id'] .'\', align: \'center\', wrapperClassName: \'highslide-wrapper-html\' })">';
		else
			echo '
							<div class="pmxhs_imglink">';

		// output the message
		echo $post['body'];

		// post has attach?
		$msgattach = '';
		if(isset($this->attaches[$post['message_id']]))
		{
			$msgattach = '
							<div class="pmxhs_posting" style="text-align:'.$this->LR.';">';

			echo '
							<div id="mbnatt'. $this->cfg['id'] .'.'. $pid .'" class="pmxhs_posting"'. (!empty($this->cfg['config']['settings']['hidethumbs']) ? ' style="text-align:'.$this->LR.'; display:none;"' : '') .'>';

			// draw out the attaches
			foreach($this->attaches[$post['message_id']] as $att)
			{
				// we have a thumbnail?
				if($att['thumb'] != $att['image'])
				{
					// highslide not disabled for images?
					if(empty($context['pmx']['settings']['disableHS']) && empty($this->cfg['config']['settings']['disableHSimg']))
					{
						$msgattach .= '
								<a href="'. $scripturl .'?action=dlattach;topic='. $post['id'] .'.0;attach='. $att['image'] .';image"  class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">
									<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['id'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />
								</a>';

						// highslide disabled for posts?
						if(!empty($this->cfg['config']['settings']['disableHS']))
							echo '
								<a href="'. $scripturl .'?action=dlattach;topic='. $post['id'] .'.0;attach='. $att['image'] .';image"  class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">
									<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['id'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />
								</a>';
						else
							echo '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['id'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';
					}

					// highslide disabled..
					else
					{
						$msgattach .= '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['id'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';
						echo '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['id'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';
					}
				}

				// no thumbnail...
				else
				{
					if(empty($context['pmx']['settings']['disableHS']) && empty($this->cfg['config']['settings']['disableHSimg']))
					{
						$msgattach .= '
								<a href="'. $scripturl .'?action=dlattach;topic='. $post['id'] .'.0;attach='. $att['image'] .';image"  class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">
									<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['id'] .'.0;attach='. $att['image'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />
								</a>';

						echo '
								<a href="'. $scripturl .'?action=dlattach;topic='. $post['id'] .'.0;attach='. $att['image'] .';image"  class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">
									<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['id'] .'.0;attach='. $att['image'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />
								</a>';
					}
					else
					{
						$msgattach .= '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['id'] .'.0;attach='. $att['image'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';

						echo '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['id'] .'.0;attach='. $att['image'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';
					}
				}
			}

			// ataches done..
			$msgattach .= '
								</div>';
			echo '
								</div>';
		}

		echo '
							</div>';

		// close the equal height div is set
		if(!empty($setQE))
			echo '
						</div>';

		// the read more link..
		echo '
						<div class="smalltext pmxp_button">
							<a style="float:'.$this->LR.';" href="'. $scripturl .'?topic='. $post['id'] . '.0">'. $txt['pmx_text_readmore'] .'</a>';

		// we have attaches and collapse set?
		if(!empty($msgattach) && !empty($this->cfg['config']['settings']['hidethumbs']))
			echo '
							<a style="float:'. $this->RL .';" href="" onclick="ShowMsgAtt(this, \'mbnatt'. $this->cfg['id'] .'.'. $pid .'\')">'. $txt['pmx_text_show_attach'] .'</a>
							<a style="float:'. $this->RL .'; display:none;" href="" onclick="ShowMsgAtt(this, \'mbnatt'. $this->cfg['id'] .'.'. $pid .'\')">'. $txt['pmx_text_hide_attach'] .'</a>';

		echo '
						</div>';

		// here starts the highslide code
		if(empty($context['pmx']['settings']['disableHS']) && empty($this->cfg['config']['settings']['disableHS']))
		{
			// show post icon and subject, posster and board
			echo '
							<div class="highslide-maincontent" id="mbn_contid'. $post['message_id'] .'">
								<div class="highslide-posthead">
									'. $post['icon'] .'<span class="normaltext cat_msg_title">'. $post['subject'] .'</span>
								</div>
								<div class="smalltext" style="float:'. $this->LR .';">
									'. $txt['pmx_text_postby'] . preg_replace('~<[^>]*>~i', '', $post['poster']['link']) .', '. $post['time'] .'
								</div>
								<div class="smalltext" style="float:'. $this->RL .';">
									'. $txt['pmx_text_replies'] . $post['replies'] .'
								</div>
								<br style="clear:both;" />
								<div class="smalltext msg_bot_pad" style="float:'. $this->LR .';">
									'. $txt['pmx_text_board'] . $post['board_name'] .'
								</div>
								<div class="smalltext msg_bot_pad" style="float:'. $this->RL .';">
								'. $txt['pmx_text_views'] . $post['views'] .'
								</div>
								<hr style="clear:both;" />
								'. $post['hsbody'] . $msgattach .'
							</div>';
		}

		// show the lower postfraame if we have one
		if(!empty($this->cfg['config']['visuals']['postframe']))
		{
			if($this->cfg['config']['visuals']['postframe'] == 'roundframe' || $this->isCustFrame)
				echo '
						</div>
						<span class="'. $this->spanclass . ($this->isCustFrame ? $this->cfg['config']['visuals']['postframe'] .'_bot' : 'lowerframe') .'"><span></span></span>';
			else
				echo '
						</div>
						<span class="'. (!empty($this->cfg['config']['visuals']['postbody']) ? $this->cfg['config']['visuals']['postbody'] .' ' : '') .'botslice"><span></span></span>';
		}
		else
			echo '
						</div>';

		// done
		echo '
					</div>';
	}
}
?>
