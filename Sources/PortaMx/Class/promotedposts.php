<?php
/**
* \file Promotedposts.php
* Systemblock Promotedposts
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2015 by PortaMx corp. - http://portamx.com
* \version 1.54
* \date 18.11.2015
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_promotedposts
* Systemblock Promotedposts
* @see promotedposts.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_promotedposts extends PortaMxC_SystemBlock
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
		global $context, $pmxCacheFunc;

		if(empty($context['pmx']['settings']['manager']['promote']) || empty($context['pmx']['promotes']))
			$this->visible = false;

		// if visible init the content
		if($this->visible)
		{
			// posts can select by posts or boards .. defaut posts
			if(empty($this->cfg['config']['settings']['selectby']))
				$this->cfg['config']['settings']['selectby'] = 'posts';

			// force reload for this block?
			if(!empty($_SESSION['pmx_refresh_promote']) && in_array($this->cfg['id'], $_SESSION['pmx_refresh_promote']))
			{
				// clear reload and fetch the data
				$_SESSION['pmx_refresh_promote'] = array_diff($_SESSION['pmx_refresh_promote'], array($this->cfg['id']));
				$posts = $this->fetch_data();

				// store in cache if ebabled
				if($this->cfg['cache'] > 0)
					$pmxCacheFunc['put']($this->cache_key, array($posts, $this->posts, $this->attaches, $this->imgName), $this->cache_time, $this->cache_mode);
			}

			else
			{
				// cache enabled ?
				if($this->cfg['cache'] > 0)
				{
					// cache valid?
					if(($cachedata = $pmxCacheFunc['get']($this->cache_key, $this->cache_mode)) !== null)
					{
						// yes..
						list($posts, $this->posts, $this->attaches, $this->imgName) = $cachedata;
						unset($cachedata);
					}

					// cache invalid.. get all data and store in cache
					else
					{
						$posts = $this->fetch_data();
						$pmxCacheFunc['put']($this->cache_key, array($posts, $this->posts, $this->attaches, $this->imgName), $this->cache_time, $this->cache_mode);
					}
				}

				// fetch if cache disable
				else
					$posts = $this->fetch_data();
			}

			// no posts .. disable the block
			if(empty($posts) || empty($this->posts))
				$this->visible = false;

			// create page index if set ..
			elseif(!empty($this->cfg['config']['settings']['onpage']))
				$this->pmxc_constructPageIndex(count($this->posts), $this->cfg['config']['settings']['onpage']);
		}

		// return the visibility
		return $this->visible;
	}

	/**
	* fetch_data.
	* Fetch Messages and Attaches.
	*/
	function fetch_data()
	{
		global $context, $scripturl, $smcFunc, $settings, $modSettings, $user_info, $txt;

		// init vars
		$this->posts = null;
		$this->attaches = null;
		$this->imgName = '';
		$posts = null;

		// get messages by posts
		if($this->cfg['config']['settings']['selectby'] == 'posts' && isset($this->cfg['config']['settings']['posts']))
		{
			// check ALL posts set
			if(in_array('0', $this->cfg['config']['settings']['posts']))
				$posts = $context['pmx']['promotes'];
			else
				$posts = $this->cfg['config']['settings']['posts'];
		}

		// get messages by board
		elseif(!empty($this->cfg['config']['settings']['boards']))
		{
			$request = $smcFunc['db_query']('', '
				SELECT m.id_msg
				FROM {db_prefix}messages AS m
				INNER JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
				WHERE m.id_msg IN ({array_int:posts}) AND b.id_board IN ({array_int:boards}) AND {query_wanna_see_board}'. (!$modSettings['postmod_active'] || allowedTo('approve_posts') ? '' : ' AND m.approved = 1') .'',
				array(
					'boards' => $this->cfg['config']['settings']['boards'],
					'posts' => $context['pmx']['promotes']
				)
			);
			while ($row = $smcFunc['db_fetch_assoc']($request))
				$posts[] = $row['id_msg'];
			$smcFunc['db_free_result']($request);
		}

		// posts found?
		if(!empty($posts))
		{
			$request = $smcFunc['db_query']('', '
				SELECT m.poster_time, m.subject, m.id_topic, m.id_member, m.id_msg, m.id_board, m.body, m.smileys_enabled, m.icon,
					b.name AS board_name, CASE WHEN mem.real_name = {string:empty} THEN m.poster_name ELSE mem.real_name END AS poster_name, t.num_views, t.num_replies
				FROM {db_prefix}messages AS m
				LEFT JOIN {db_prefix}boards AS b ON (b.id_board = m.id_board)
				LEFT JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic AND t.id_board = b.id_board)
				LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = m.id_member)
				WHERE m.id_msg IN ({array_int:messages}) AND {query_wanna_see_board}'. (!$modSettings['postmod_active'] || allowedTo('approve_posts') ? '' : ' AND m.approved = 1') .'
				ORDER BY m.id_msg DESC',
				array(
					'messages' => $posts,
					'empty' => '',
				)
			);

			// prepare the post..
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				if(empty($modSettings['messageIconChecks_disable']) && !isset($icon_sources[$row['icon']]))
					$icon_sources[$row['icon']] = file_exists($settings['theme_dir'] .'/images/post/'. $row['icon'] .'.gif') ? 'images_url' : 'default_images_url';

				$row['body'] = parse_bbc($row['body'], $row['smileys_enabled'], $row['id_msg']);
				censorText($row['subject']);
				censorText($row['body']);

				// remove highslide code for highslide body
				$row['hsbody'] = $this->removeHiglideCode($row['body']);

				// on rescale remove highslide code
				$row['body'] = (!empty($this->cfg['config']['settings']['rescale']) ? $row['hsbody'] : $row['body']);

				// teaser enabled ?
				if(!empty($this->cfg['config']['settings']['teaser']))
					$row['body'] = PortaMx_Tease_posts($row['body'], $this->cfg['config']['settings']['teaser']);

				// Rescale inline Images ?
				if(!empty($this->cfg['config']['settings']['rescale']))
				{
					// find all images
					if(preg_match_all('~<img[^>]*>~iS', $row['body'], $matches) > 0)
					{
						// remove smileys
						foreach($matches[0] as $i => $data)
							if(strpos($data, $modSettings['smileys_url']) !== false)
								unset($matches[0][$i]);

						// images found?
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

								// highslide globally disabled?
								if(!empty($context['pmx']['settings']['disableHS']))
									$row['body'] = substr_replace($row['body'], $tmp, strpos($row['body'], $data), $datlen);
								else
								{
									if(empty($this->cfg['config']['settings']['disableHS']))
									{
										$row['body'] = substr_replace($row['body'], $tmp, strpos($row['body'], $data), $datlen);

										// enabled for images?
										if(empty($this->cfg['config']['settings']['disableHSimg']))
											$row['hsbody'] = substr_replace($row['hsbody'],
												'<a href="'. $src[1].' class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">' .$tmp .'</a>',
												strpos($row['hsbody'], $data), $datlen);

										// disabled, image caan't zoomed
										else
											$row['hsbody'] = substr_replace($row['hsbody'], $tmp, strpos($row['hsbody'], $data), $datlen);
									}

									// post disabled, enabled for images?
									elseif(empty($this->cfg['config']['settings']['disableHSimg']))
										$row['body'] = substr_replace($row['body'],
											'<a href="'. $src[1].' class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">' .$tmp .'</a>',
											strpos($row['body'], $data), $datlen);

									// no, images can't zoomed
									else
										$row['body'] = substr_replace($row['body'], $tmp, strpos($row['body'], $data), $datlen);
								}
							}
						}
					}
				}

				// if rescale 0, remove images from posts
				elseif(is_numeric($this->cfg['config']['settings']['rescale']))
					$row['body'] = PortaMx_revoveLinks($row['body'], false, true);

				// remove links from highslide posts and rescale 0, remove alse the images
				$HSremImg = empty($this->cfg['config']['settings']['rescale']) && is_numeric($this->cfg['config']['settings']['rescale']);
				$row['hsbody'] = PortaMx_revoveLinks($row['hsbody'], true, $HSremImg);

				// build the posts array
				$this->posts[$row['id_msg']] = array(
					'id' => $row['id_msg'],
					'board' => array(
						'id' => $row['id_board'],
						'name' => $row['board_name'],
						'href' => $scripturl .'?board='. $row['id_board'] .'.0',
						'link' => '<a href="'. $scripturl .'?board='. $row['id_board'] .'.0">'. $row['board_name'] .'</a>'
					),
					'topic' => array(
						'id' => $row['id_topic'],
						'views' => $row['num_views'],
						'replies' => $row['num_replies'],
					),
					'poster' => array(
						'id' => $row['id_member'],
						'name' => $row['poster_name'],
						'href' => empty($row['id_member']) ? '' : $scripturl .'?action=profile;u='. $row['id_member'],
						'link' => empty($row['id_member']) ? $row['poster_name'] : '<a href="'. $scripturl .'?action=profile;u='. $row['id_member'] .'">'. $row['poster_name'] .'</a>'
					),
					'subject' => $row['subject'],
					'icon' => '<img src="'. $settings[$icon_sources[$row['icon']]] .'/post/'. $row['icon'] .'.gif" align="middle" alt="'. $row['icon'] .'" />',
					'body' => $row['body'],
					'hsbody' => $row['hsbody'],
					'time' => timeformat($row['poster_time']),
					'timestamp' => forum_time(true, $row['poster_time']),
					'href' => $scripturl . '?topic='. $row['id_topic'] .'.msg'. $row['id_msg'] .'#msg'. $row['id_msg'],
					'link' => '<a href="'. $scripturl .'?topic='. $row['id_topic'] .'.msg'. $row['id_msg'] .'#msg'. $row['id_msg'] .'" rel="nofollow">'. $row['subject'] .'</a>',
				);
			}
			$smcFunc['db_free_result']($request);

			// get attachments if show thumnails set and user have show access
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
						'messages' => $posts,
						'like' => 'IMAGE%',
						'boards' => $allow_boards,
					)
				);

				$thumbs = array();
				$msgcnt = array();
				$saved = !empty($this->cfg['config']['settings']['thumbcnt']) ? $this->cfg['config']['settings']['thumbcnt'] : 0;

				// check the count and put attaches to the array
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
		else
			$this->visible = false;

		// return the post
		return $posts;
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
						<span class="'. $this->spanclass . ($this->isCustFrame ? $this->cfg['config']['visuals']['postframe'] .'_top' : 'upperframe') .'"><span></span></span>';
				else
					echo '
						<span class="'. (!empty($this->cfg['config']['visuals']['postbody']) ? $this->cfg['config']['visuals']['postbody'] .' ' : '') .'toplice"><span></span></span>';
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
							<span class="normaltext cat_msg_title"><a href="'. $post['href'] .'">'. $post['subject'] .'</a></span>
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
								'. $post['icon'] .'<span class="normaltext cat_msg_title"><a href="'. $post['href'] .'">'. $post['subject'] .'</a></span>
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
								'. $txt['pmx_text_replies'] . $post['topic']['replies'] .'
							</div>
							<br style="clear:both;" />
							<div class="smalltext msg_bot_pad" style="float:'. $this->LR .';">
								'. $txt['pmx_text_board'] . $post['board']['link'] .'
							</div>
							<div class="smalltext msg_bot_pad" style="float:'. $this->RL .';">
								'. $txt['pmx_text_views'] . $post['topic']['views'] .'
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
								'. $txt['pmx_text_board'] . $post['board']['link'] .'
							</div>
							<hr style="clear:both;" />';
			}
		}

		// if highslide enabled, create the hidden hs frame
		if(empty($context['pmx']['settings']['disableHS']) && empty($this->cfg['config']['settings']['disableHS']))
			echo '
							<div class="pmxhs_imglink" onmouseover="this.className = \'contentover\'" onmouseout="this.className =\'contentout\'" title="'. $txt['pmx_hs_read'] .'" onclick="return hs.htmlExpand(this,
								{ maincontentId: \'pb_contid'. $pid .'\', align: \'center\', wrapperClassName: \'highslide-wrapper-html\' })">';
		else
			echo '
							<div class="pmxhs_imglink">';

		// output the message
		echo $post['body'];

		// post has attach?
		$msgattach = '';
		if(isset($this->attaches[$pid]))
		{
			$msgattach = '
							<div class="pmxhs_posting" style="text-align:'.$this->LR.';">';

			echo '
							<div id="ppatt'. $this->cfg['id'] .'.'. $pid .'" class="pmxhs_posting"'. (!empty($this->cfg['config']['settings']['hidethumbs']) ? ' style="text-align:'.$this->LR.'; display:none;"' : '') .'>';

			// draw out the attaches
			foreach($this->attaches[$pid] as $att)
			{
				// we have a thumbnail?
				if($att['thumb'] != $att['image'])
				{
					// highslide not disabled for images?
					if(empty($context['pmx']['settings']['disableHS']) && empty($this->cfg['config']['settings']['disableHSimg']))
					{
						$msgattach .= '
								<a href="'. $scripturl .'?action=dlattach;topic='. $post['topic']['id'] .'.0;attach='. $att['image'] .';image"  class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">
									<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic']['id'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />
								</a>';

						// highslide disabled for posts?
						if(!empty($this->cfg['config']['settings']['disableHS']))
							echo '
								<a href="'. $scripturl .'?action=dlattach;topic='. $post['topic']['id'] .'.0;attach='. $att['image'] .';image"  class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">
									<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic']['id'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />
								</a>';
						else
							echo '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic']['id'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';
					}

					// highslide disabled..
					else
					{
						$msgattach .= '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic']['id'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';
						echo '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic']['id'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';
					}
				}

				// no thumbnail...
				else
				{
					if(empty($context['pmx']['settings']['disableHS']) && empty($this->cfg['config']['settings']['disableHSimg']))
					{
						$msgattach .= '
								<a href="'. $scripturl .'?action=dlattach;topic='. $post['topic']['id'] .'.0;attach='. $att['image'] .';image"  class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">
									<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic']['id'] .'.0;attach='. $att['image'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />
								</a>';

						echo '
								<a href="'. $scripturl .'?action=dlattach;topic='. $post['topic']['id'] .'.0;attach='. $att['image'] .';image"  class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">
									<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic']['id'] .'.0;attach='. $att['image'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />
								</a>';
					}
					else
					{
						$msgattach .= '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic']['id'] .'.0;attach='. $att['image'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';

						echo '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic']['id'] .'.0;attach='. $att['image'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';
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
							<a style="float:'.$this->LR.';" href="'. $post['href'] .'">'. $txt['pmx_text_readmore'] .'</a>';

		// we have attaches and collapse set?
		if(!empty($msgattach) && !empty($this->cfg['config']['settings']['hidethumbs']))
			echo '
							<a style="float:'. $this->RL .';" href="" onclick="ShowMsgAtt(this, \'ppatt'. $this->cfg['id'] .'.'. $pid .'\')">'. $txt['pmx_text_show_attach'] .'</a>
							<a style="float:'. $this->RL .'; display:none;" href="" onclick="ShowMsgAtt(this, \'ppatt'. $this->cfg['id'] .'.'. $pid .'\')">'. $txt['pmx_text_hide_attach'] .'</a>';

		echo '
						</div>';

		// here starts the highslide code
		if(empty($context['pmx']['settings']['disableHS']) && empty($this->cfg['config']['settings']['disableHS']))
		{
			// show post icon and subject, posster and board
			echo '
						<div class="highslide-maincontent" id="pb_contid'. $pid .'">
							<div class="highslide-posthead">
								'. $post['icon'] .'<span class="normaltext cat_msg_title">'. preg_replace('~<[^>]*>~i', '', $post['link']) .'</span>
							</div>
							<div class="smalltext" style="float:'. $this->LR .';">
								'. $txt['pmx_text_postby'] . preg_replace('~<[^>]*>~i', '', $post['poster']['link']) .', '. $post['time'] .'
							</div>
							<div class="smalltext" style="float:'. $this->RL .';">
								'. $txt['pmx_text_replies'] . $post['topic']['replies'] .'
							</div>
							<br style="clear:both;" />
							<div class="smalltext msg_bot_pad" style="float:'. $this->LR .';">
								'. $txt['pmx_text_board'] . preg_replace('~<[^>]*>~i', '', $post['board']['link']) .'
							</div>
							<div class="smalltext msg_bot_pad" style="float:'. $this->RL .';">
								'. $txt['pmx_text_views'] . $post['topic']['views'] .'
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
