<?php
/**
* \file rss_reader.php
* Systemblock RSS Feed Reader
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_rss_reader
* Systemblock RSS Feed Reader
* @see rss_reader.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_rss_reader extends PortaMxC_SystemBlock
{
	var $TimeToLife;		///< Feed TTL if send
	var $feedheader;		///< header info
	var $rsscontent;		///< content info

	/**
	* checkCacheStatus.
	* do nothing
	*/
	function pmxc_checkCacheStatus()
	{
		return true;
	}

	/**
	* InitContent.
	* Checks the cache status and create the content.
	*/
	function pmxc_InitContent()
	{
		global $context, $pmxCacheFunc;

		if($this->visible)
		{
			if($this->cfg['cache'] > 0)
			{
				// check if the block cached
				if(($cachedata = $pmxCacheFunc['get']($this->cache_key, $this->cache_mode)) !== null)
					list($this->feedheader, $this->rsscontent) = $cachedata;
				else
				{
					$this->rssreader_Content();
					$cachedata = array($this->feedheader, $this->rsscontent);
					$pmxCacheFunc['put']($this->cache_key, $cachedata, $this->TimeToLife, $this->cache_mode);
				}

				unset($cachedata);
			}
			$this->rssreader_Content();

			// paging...
			if(!empty($this->cfg['config']['settings']['onpage']) && count($this->rsscontent) > $this->cfg['config']['settings']['onpage'])
				$this->pmxc_constructPageIndex(count($this->rsscontent), $this->cfg['config']['settings']['onpage']);
		}

		// return the visibility flag (true/false)
		return $this->visible;
	}

	/**
	* rssreader_Content.
	* Prepare the content and save in $this->rsscontent.
	*/
	function rssreader_Content()
	{
		global $context;

		$this->TimeToLife = $this->cache_time;
		$this->rsscontent = '';
		$this->cfg['content'] = '';

		// get all articles from feed
		if(!empty($this->cfg['config']['settings']['rssfeedurl']))
		{
			// get All posts from feed
			$this->rsscontent = getRSSfeedPosts($this->feedheader, $this->cfg['config']['settings']['rssfeedurl'], $this->cfg['config']['settings']['rssmaxitems'], $this->cfg['config']['settings']['rsstimeout']);

			if(empty($this->feedheader['title']))
			{
				$this->feedheader['title'] = $this->cfg['config']['settings']['rssfeed_name'];
				$this->feedheader['link'] = $this->cfg['config']['settings']['rssfeed_link'];
				$this->feedheader['desc'] = $this->cfg['config']['settings']['rssfeed_desc'];
			}
			// Time To Life send ?
			if(!empty($this->cfg['config']['settings']['usettl']) && !empty($this->feedheader['ttl']))
				$this->TimeToLife = intval($this->feedheader['ttl']) * 60;
		}
	}

	/**
	* ShowContent.
	* Output the content and add necessary javascript
	*/
	function pmxc_ShowContent()
	{
		global $context, $settings, $modSettings, $scripturl, $txt;

		if(!empty($this->rsscontent))
		{
			// ini all vars
			$this->LR = empty($context['right_to_left']) ? 'left' : 'right';
			$this->RL = empty($context['right_to_left']) ? 'right' : 'left';
			$this->is_Split = $this->cfg['config']['settings']['split'];
			$this->is_last = (!empty($this->pageindex) ? ($this->startpage + $this->postspage > count($this->rsscontent) ? count($this->rsscontent) - $this->startpage : $this->postspage) : count($this->rsscontent));
			$this->half = (!empty($this->is_Split) ? ceil($this->is_last / 2) : $this->is_last);
			$this->spanlast = intval(!empty($this->is_Split) && ($this->half * 2) > $this->is_last && count($this->rsscontent) > 1);
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

			// write out the content
			if(!empty($this->cfg['config']['settings']['showhead']))
			{
				echo '
				<div class="smalltext"'. (empty($this->pageindex) || empty($this->cfg['config']['settings']['pgidxtop']) ? ' style="padding-bottom:3px;"': '') .'>
					'. (!empty($this->feedheader['link']) ? '<a href="'. $this->feedheader['link'] .'" target="_blank"><b>'. $this->feedheader['title'] .'</b></a>' : '<b>'. $this->feedheader['link'] .'</b>') .'
					'. (!empty($this->feedheader['desc']) ? '<br />'. $this->feedheader['desc'] : '');

				if(!empty($this->pageindex) && !empty($this->cfg['config']['settings']['pgidxtop']))
					echo '
					<hr />';

				echo '
				</div>';
			}

			// find the first post
			reset($this->rsscontent);
			for($i = 0; $i < $this->startpage; $i++)
				list($pid, $post) = PMX_Each($this->rsscontent);

			// only one? .. clear split
			if(count($this->rsscontent) - $this->startpage == 1)
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
					list($pid, $post) = PMX_Each($this->rsscontent);
					$this->pmxc_ShowPost($pid, $post, $isEQ, $this->half == 1);
					next($this->rsscontent);
					$this->half--;
					$this->is_last--;
				}

				echo '
								</td>
								<td width="50%" valign="top">';

				// shift post by 1..
				reset($this->rsscontent);
				for($i = -1; $i < $this->startpage; $i++)
					list($pid, $post) = PMX_Each($this->rsscontent);

				// write out the right part..
				while($this->is_last - $this->spanlast > 0)
				{
					list($pid, $post) = PMX_Each($this->rsscontent);
					$this->pmxc_ShowPost($pid, $post, $isEQ, $this->is_last == 1 && empty($this->spanlast));
					list($pid, $post) = PMX_Each($this->rsscontent);
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
					list($pid, $post) = PMX_Each($this->rsscontent);
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
		}
		else
		{
			if(!empty($context['pmx']['feed_error_text']))
				echo $txt['error_occured'] .'<br />'. $context['pmx']['feed_error_text'];
			else
				echo $txt['pmx_rssreader_error'];
		}
	}

	/**
	* Show one Post.
	*/
	function pmxc_ShowPost($pid, $post, $setQE, $lastrow)
	{
		global $context, $settings, $modSettings, $scripturl, $txt;

		// the post main division..
		echo '
						<div'. (!empty($lastrow) ? ' id="bot'. $this->cfg['uniID'] .'"' : '') .' style="margin-'. ((!empty($this->is_Split) ? (!empty($this->half) ? $this->RL : $this->LR) .':'. $this->halfpad .'px; margin-' : '') . (!empty($lastrow) ? 'bottom:0' : 'bottom:'. $this->fullpad)) .'px;">';

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
								<div class="pmx_postheader">
									<img style="float:'. $this->LR .';" src="'. $context['pmx_imageurl'] .'rssfeed.gif" alt="*" title="" align="middle" />
									<span class="normaltext cat_msg_title rss_feed">';

				if(!empty($post['tlink']) || !empty($post['slink']))
					echo '
										<a href="'. (!empty($post['tlink']) ? $post['tlink'] : $post['slink']) .'" target="_blank" title="'. str_replace('"', '\"', $post['subject']) .'">'. $post['subject'] .'</a>';
				else
					echo $post['subject'];

				echo '
									</span>
								</div>';
			}
		}

		// ok, we have postheader .. put icon and subject on it
		else
		{
			echo '
								<div class="'. str_replace('bg', '_bar', $this->cfg['config']['visuals']['postheader']) .' catbg_grid">
									<h4 class="'. $this->cfg['config']['visuals']['postheader'] .' catbg_grid">
									<img style="float:'. $this->LR .';" src="'. $context['pmx_imageurl'] .'rssfeed.gif" alt="*" title="" align="middle" />
									<span class="normaltext cat_msg_title rss_feed">';

			if(!empty($post['tlink']) || !empty($post['slink']))
				echo '
										<a href="'. (!empty($post['tlink']) ? $post['tlink'] : $post['slink']) .'" target="_blank" title="'. str_replace('"', '\"', $post['subject']) .'">'. $post['subject'] .'</a>';
			else
				echo $post['subject'];

			echo '
									</span>
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

		if($this->cfg['config']['visuals']['postheader'] != 'none')
		{
			echo $txt['pmx_text_postby'];

			if(!empty($post['plink']))
				echo '
									<a href="'. $post['plink'] .'" target="_blank">'. $post['poster'] .'</a>';
			else
				echo $post['poster'];

			if(!empty($post['date']))
			{
				if(empty($post['poster']))
					echo $txt['pmx_rssreader_postat'];
				else
					echo ', ';
				echo $post['date'];
			}

			if(!empty($post['board']) || !empty($post['category']))
				echo '<br />'. (!empty($post['board']) ? $txt['pmx_text_board'] . (!empty($post['blink']) ? '<a href="'. $post['blink'] .'" target="_blank">'. $post['board'] .'</a>' : $post['board']) : $txt['pmx_text_category'] .$post['category']);

			echo '
									<hr />';
		}

		echo '
									<div style="overflow:hidden;">';

		if(!empty($this->cfg['config']['settings']['cont_encode']) && !empty($post['contenc']))
		{
			if(!empty($this->cfg['config']['settings']['delimage']))
				$post['contenc'] = PortaMx_revoveLinks($post['contenc'], false, true);

			if(!empty($this->cfg['config']['settings']['teaser']))
				echo PortaMx_Tease_posts($post['contenc'], $this->cfg['config']['settings']['teaser']);

			else
				echo $post['contenc'];
		}
		else
		{
			if(!empty($this->cfg['config']['settings']['delimage']))
				$post['message'] = PortaMx_revoveLinks($post['message'], false, true);

			if(!empty($this->cfg['config']['settings']['teaser']))
				echo PortaMx_Tease_posts($post['message'], $this->cfg['config']['settings']['teaser'], '', false, !empty($this->cfg['config']['settings']['delimages']));

			else
				echo $post['message'];
		}

		echo '
									</div>';

		// close the equal height div is set
		if(!empty($setQE))
			echo '
								</div>';

		// the read more link..
		if(!empty($post['slink']))
			echo '
							<div class="smalltext  pmxp_button">
								<a style="float:'. $this->LR .';" href="'. $post['slink'] .'" target="_blank">'. $txt['pmx_text_readmore'] .'</a>
							</div>';

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
