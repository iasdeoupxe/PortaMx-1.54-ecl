<?php
/**
* \file newposts.php
* Systemblock Newposts
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2015 by PortaMx corp. - http://portamx.com
* \version 1.54
* \date 18.11.2015
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_newposts
* Systemblock Newposts
* @see newposts.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_newposts extends PortaMxC_SystemBlock
{
	var $posts;				///< all posts
	var $topics;			///< all topics
	var $attaches;		///< all ataches
	var $isRead;			///< unread topics by member
	var $imgName;			///< rescale image name

	/**
	* InitContent.
	* Checks the cache status and create the content.
	*/
	function pmxc_InitContent()
	{
		global $context, $scripturl, $user_info, $pmxCacheFunc;

		// if visible init the content
		if($this->visible)
		{
			if($this->cfg['cache'] > 0)
			{
				// check the block cache
				if(($cachedata = $pmxCacheFunc['get']($this->cache_key, $this->cache_mode)) !== null)
				{
					list($this->topics, $isRead, $this->posts, $this->attaches, $this->imgName) = $cachedata;
					$this->isRead = (isset($isRead[$user_info['id']]) ? $isRead[$user_info['id']] : null);
					if($this->isRead === null)
					{
						$cachedata = $this->fetch_data($isRead);
						$pmxCacheFunc['put']($this->cache_key, $cachedata, $this->cache_time, $this->cache_mode);
					}
				}
				else
				{
					$cachedata = $this->fetch_data();
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
	* Fetch Messages and Attaches.
	*/
	function fetch_data($isRead = null)
	{
		global $context, $scripturl, $smcFunc, $settings, $modSettings, $user_info, $txt;

		$this->posts = null;
		$this->attaches = null;
		$this->topics = null;
		$this->isRead = null;
		$this->imgName = '';
		$curRead = $this->get_isRead();

		if(isset($this->cfg['config']['settings']['boards']) && !empty($this->cfg['config']['settings']['boards']))
		{
			$ssiposts = ssi_recentTopics($this->cfg['config']['settings']['total'], null, $this->cfg['config']['settings']['boards'], '');

			if(!empty($ssiposts))
			{
				$msgids = array();
				foreach($ssiposts as $post)
				{
					preg_match('~msg(\d+)~', $post['href'], $found);
					$msgids[] = $found[1];
					$post['msgID'] = $found[1];
					$this->posts[$found[1]] = $post;
					$this->topics[] = $post['topic'];
					$this->isRead[$post['topic']] = $curRead['topic'] != $post['topic'] && !empty($post['is_new']) ? false : true;

				}
				unset($ssiposts);

				// get full messagebody
				$msg = array();
				$request = $smcFunc['db_query']('', '
					SELECT id_msg, body, smileys_enabled
						FROM {db_prefix}messages
					WHERE id_msg IN ({array_int:msgids})',
					array(
						'msgids' => $msgids
					)
				);

				while($row = $smcFunc['db_fetch_assoc']($request))
				{
					$msg = parse_bbc($row['body'], $row['smileys_enabled'], $row['id_msg']);
					censorText($msg);

					// remove highslide code for highslide body
					$this->posts[$row['id_msg']]['hsbody'] = $this->removeHiglideCode($msg);

					// on rescale remove highslide code
					$this->posts[$row['id_msg']]['body'] = (!empty($this->cfg['config']['settings']['rescale']) ? $this->posts[$row['id_msg']]['hsbody'] : $msg);

					// teaser enabled ?
					if(!empty($this->cfg['config']['settings']['teaser']))
						$this->posts[$row['id_msg']]['body'] = PortaMx_Tease_posts($this->posts[$row['id_msg']]['body'], $this->cfg['config']['settings']['teaser'], '', false, false);

					// Rescale inline Images ?
					if(!empty($this->cfg['config']['settings']['rescale']))
					{
						if(preg_match_all('~<img[^>]*>~iS', $this->posts[$row['id_msg']]['body'], $matches) > 0)
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

								foreach($matches[0] as $i => $data)
								{
									$datlen = strlen($data);
									preg_match('~src?=?"([^\"]*\")~i', $data, $src);
									$tmp = str_replace($src[0], ' name="'. $this->imgName .'" title="'. substr(strrchr($src[1], '/'), 1) .' '. $src[0], preg_replace($fnd, '', $data));

									if(!empty($context['pmx']['settings']['disableHS']))
										$this->posts[$row['id_msg']]['body'] = substr_replace($this->posts[$row['id_msg']]['body'], $tmp, strpos($this->posts[$row['id_msg']]['body'], $data), $datlen);
									else
									{
										if(empty($this->cfg['config']['settings']['disableHS']))
										{
											$this->posts[$row['id_msg']]['body'] = substr_replace($this->posts[$row['id_msg']]['body'], $tmp, strpos($this->posts[$row['id_msg']]['body'], $data), $datlen);

											if(empty($this->cfg['config']['settings']['disableHSimg']))
												$this->posts[$row['id_msg']]['hsbody'] = substr_replace($this->posts[$row['id_msg']]['hsbody'],
													'<a href="'. $src[1].' class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">' .$tmp .'</a>',
													strpos($this->posts[$row['id_msg']]['hsbody'], $data), $datlen);
											else
												$this->posts[$row['id_msg']]['hsbody'] = substr_replace($this->posts[$row['id_msg']]['hsbody'], $tmp, strpos($this->posts[$row['id_msg']]['hsbody'], $data), $datlen);
										}
										elseif(empty($this->cfg['config']['settings']['disableHSimg']))
											$this->posts[$row['id_msg']]['body'] = substr_replace($this->posts[$row['id_msg']]['body'],
												'<a href="'. $src[1].' class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">' .$tmp .'</a>',
												strpos($this->posts[$row['id_msg']]['body'], $data), $datlen);
										else
											$this->posts[$row['id_msg']]['body'] = substr_replace($this->posts[$row['id_msg']]['body'], $tmp, strpos($this->posts[$row['id_msg']]['body'], $data), $datlen);
									}
								}
							}
						}
					}
					elseif(is_numeric($this->cfg['config']['settings']['rescale']))
						$this->posts[$row['id_msg']]['body'] = PortaMx_revoveLinks($this->posts[$row['id_msg']]['body'], false, true);

					$HSremImg = empty($this->cfg['config']['settings']['rescale']) && is_numeric($this->cfg['config']['settings']['rescale']);
					$this->posts[$row['id_msg']]['hsbody'] = PortaMx_revoveLinks($this->posts[$row['id_msg']]['hsbody'], true, $HSremImg);
				}
				$smcFunc['db_free_result']($request);

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

				$isRead[$user_info['id']] = $this->isRead;
				return array($this->topics, $isRead, $this->posts, $this->attaches, $this->imgName);
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

		if(empty($post['is_new']))
			$post['href'] = str_replace('#new', '#msg'. $post['msgID'], $post['href']);

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

		// we have postheader .. put icon and subject on it
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
							'. $txt['pmx_text_postby'] . $post['poster']['link'] .', '. $post['time'] . (empty($this->isRead[$post['topic']]) ? '
							<span style="padding-'. $this->LR .':5px;">
								<a href="'. $scripturl . '?topic='. $post['topic'] . '.msg' . $post['new_from'] .';topicseen#new" /><img src="' . $settings['lang_images_url'] . '/new.gif" alt="' . $txt['new'] . '" border="0" /></a>
							</span>' : '') .'
						</div>
						<div class="smalltext" style="float:'. $this->RL .';">
							'. $txt['pmx_text_replies'] . $post['replies'] .'
						</div>
						<br style="clear:both;" />
						<div class="smalltext msg_bot_pad" style="float:'. $this->LR .';">
							'. $txt['pmx_text_board'] . $post['board']['link'] .'
						</div>
						<div class="smalltext msg_bot_pad" style="float:'. $this->RL .';">
							'. $txt['pmx_text_views'] . $post['views'] .'
						</div>
						<hr style="clear:both;" />';
			else
			{
				echo '
						<div class="smalltext" style="float:'. $this->LR .';">
							'. $txt['pmx_text_postby'] . $post['poster']['link'] .', '. $post['time'] . (empty($this->isRead[$post['topic']]) ? '
							<span style="padding-'. $this->LR .':5px;">
								<a href="'. $scripturl . '?topic='. $post['topic'] . '.msg' . $post['new_from'] .';topicseen#new" /><img src="' . $settings['lang_images_url'] . '/new.gif" alt="' . $txt['new'] . '" border="0" /></a>
							</span>' : '') .'
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
								{ maincontentId: \'np_contid'. $pid .'\', align: \'center\', wrapperClassName: \'highslide-wrapper-html\' })">';

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
							<div id="npatt'. $this->cfg['id'] .'.'. $pid .'" class="pmxhs_posting"'. (!empty($this->cfg['config']['settings']['hidethumbs']) ? ' style="text-align:'.$this->LR.'; display:none;"' : '') .'>';

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
								<a href="'. $scripturl .'?action=dlattach;topic='. $post['topic'] .'.0;attach='. $att['image'] .';image" class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">
									<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />
								</a>';

						// highslide disabled for posts?
						if(!empty($this->cfg['config']['settings']['disableHS']))
							echo '
								<a href="'. $scripturl .'?action=dlattach;topic='. $post['topic'] .'.0;attach='. $att['image'] .';image"  class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">
									<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />
								</a>';
						else
							echo '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';
					}

					// highslide disabled..
					else
					{
						$msgattach .= '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';
						echo '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic'] .'.0;attach='. $att['thumb'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';
					}
				}

				// no thumbnail...
				else
				{
					if(empty($context['pmx']['settings']['disableHS']) && empty($this->cfg['config']['settings']['disableHSimg']))
					{
						$msgattach .= '
								<a href="'. $scripturl .'?action=dlattach;topic='. $post['topic'] .'.0;attach='. $att['image'] .';image"  class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">
									<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic'] .'.0;attach='. $att['image'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />
								</a>';

						echo '
								<a href="'. $scripturl .'?action=dlattach;topic='. $post['topic'] .'.0;attach='. $att['image'] .';image"  class="highslide" title="'. $txt['pmx_hs_expand'] .'" onclick="return hs.expand(this, {align: \'center\', headingEval: \'this.thumb.title\'})">
									<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic'] .'.0;attach='. $att['image'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />
								</a>';
					}
					else
					{
						$msgattach .= '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic'] .'.0;attach='. $att['image'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';

						echo '
								<img align="top" class="pmxhs_img" src="'. $scripturl .'?action=dlattach;topic='. $post['topic'] .'.0;attach='. $att['image'] .';image" alt="'. $att['fname'] .'" title="'. $att['fname'] .'" />';
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
							<a style="float:'. $this->LR .';" href="'. $post['href'] .'">'. $txt['pmx_text_readmore'] .'</a>';

		// we have attaches and collapse set?
		if(!empty($msgattach) && !empty($this->cfg['config']['settings']['hidethumbs']))
			echo '
							<a style="float:'. $this->RL .';" href="" onclick="ShowMsgAtt(this, \'npatt'. $this->cfg['id'] .'.'. $pid .'\')">'. $txt['pmx_text_show_attach'] .'</a>
							<a style="float:'. $this->RL .'; display:none;" href="" onclick="ShowMsgAtt(this, \'npatt'. $this->cfg['id'] .'.'. $pid .'\')">'. $txt['pmx_text_hide_attach'] .'</a>';

		echo '
						</div>';

		// here starts the highslide code
		if(empty($context['pmx']['settings']['disableHS']) && empty($this->cfg['config']['settings']['disableHS']))
			echo '
						<div class="highslide-maincontent" id="np_contid'. $pid .'">
							<div class="highslide-posthead">
								'. $post['icon'] .'<span class="normaltext cat_msg_title">'. preg_replace('~<[^>]*>~i', '', $post['link']) .'</span>
							</div>
							<div class="smalltext" style="float:'. $this->LR .';">
								'. $txt['pmx_text_postby'] . preg_replace('~<[^>]*>~i', '', $post['poster']['link']) .', '. $post['time'] . (empty($this->isRead[$post['topic']]) ? '<span style="padding-'. $this->LR .':5px;"><img src="' . $settings['lang_images_url'] . '/new.gif" alt="' . $txt['new'] . '" border="0" /></span>' : '') .'
							</div>
							<div class="smalltext" style="float:'. $this->RL .';">
								'. $txt['pmx_text_replies'] . $post['replies'] .'
							</div>
							<br style="clear:both;" />
							<div class="smalltext msg_bot_pad" style="float:'. $this->LR .';">
								'. $txt['pmx_text_board'] . preg_replace('~<[^>]*>~i', '', $post['board']['link']) .'
							</div>
							<div class="smalltext msg_bot_pad" style="float:'. $this->RL .';">
								'. $txt['pmx_text_views'] . $post['views'] .'
							</div>
							<hr style="clear:both;" />
							'. $post['hsbody'] . $msgattach .'
						</div>';

		// show the lower postframe if we have one
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
