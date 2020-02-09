<?php
/**
* \file article.php
* Systemblock Article
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_article
* Systemblock Article
* @see article.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_article extends PortaMxC_SystemBlock
{
	var $articles;
	var $php_content;
	var $php_vars;

	/**
	* checkCacheStatus.
	* Article trigger do nothing.
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
		global $context, $smcFunc, $pmxCacheFunc;

		// if visible init the content
		if($this->visible)
		{
			// called from static article block?
			if(!empty($this->cfg['config']['static_block']))
			{
				$this->cfg['name'] = $this->cfg['config']['settings']['article'];
				$this->cfg['blocktype'] = 'artblock';

				if($this->cfg['config']['settings']['usedframe'] == 'article')
					$this->cfg['config']['skip_outerframe'] = true;
				$this->cfg['config']['visuals']['bodytext'] = '';

				if(!empty($this->cfg['cache']))
					$this->articles = $pmxCacheFunc['get']($this->cache_key, $this->cache_mode);
				else
					$this->articles = array();
			}

			// requested
			else
			{
				$this->cache_key = 'req'. $this->cache_key;
				$this->articles = array();
				$this->cfg['config']['skip_outerframe'] = true;
				$this->cfg['config']['settings']['usedframe'] = 'article';
			}

			// get the articles
			if(empty($this->articles))
			{
				if(!empty($this->cfg['name']))
				{
					$request = $smcFunc['db_query']('', '
						SELECT a.id, a.name, a.acsgrp, a.ctype, a.config, a.owner, a.active, a.created, a.updated, a.content, CASE WHEN m.real_name = {string:empty} THEN m.member_name ELSE m.real_name END AS mem_name
						FROM {db_prefix}portamx_articles AS a
						LEFT JOIN {db_prefix}members AS m ON (a.owner = m.id_member)
						WHERE a.name = {string:art} AND a.active > 0 AND a.approved > 0
						ORDER BY a.id',
						array(
							'art' => $this->cfg['name'],
							'empty' => '',
						)
					);

					if($smcFunc['db_num_rows']($request) > 0)
					{
						while($row = $smcFunc['db_fetch_assoc']($request))
						{
							$row['config'] = unserialize($row['config']);
							if(!empty($this->cfg['config']['settings']['inherit_acs']) || allowPmxGroup($row['acsgrp']))
							{
								// have a custom cssfile, load
								if(!empty($row['config']['cssfile']))
									$this->getCustomCSS($row);
								else
									$row['customclass'] = '';

								$row['side'] = $this->cfg['side'];
								$row['blocktype'] = (!empty($this->cfg['config']['static_block']) ? 'static_article' : 'article');
								$row['member_name'] = $row['mem_name'];
								$this->articles[] = $row;
							}
						}
						$smcFunc['db_free_result']($request);
					}

					// static block?
					if(!empty($this->cfg['config']['static_block']) && !empty($this->cfg['cache']))
						$pmxCacheFunc['put']($this->cache_key, $this->articles, $this->cache_time, $this->cache_mode);
				}
			}

			// articles found?
			if(count($this->articles) > 0)
			{
				// requested ?
				if(!empty($this->cfg['config']['static_block']))
				{
					$this->cfg['blocktype'] = 'artblock';
					$this->cfg['uniID'] = 'blk'. $this->cfg['id'];
				}
				else
				{
					$this->cfg['uniID'] = 'art'. $this->articles[0]['id'];
					$context['pmx']['pagenames']['art'] = htmlspecialchars($this->getUserTitle($this->articles[0]), ENT_QUOTES);
					if(empty($context['pmx']['pagenames']['art']))
						$context['pmx']['pagenames']['art'] = htmlspecialchars($this->articles[0]['name'], ENT_QUOTES);
				}

				if(!isset($_GET['cat']) && empty($this->cfg['config']['static_block']) && !empty($context['pmx']['settings']['topfragment']))
					$context['pmx']['html_footer'] .= '
						window.location.href = window.location.href.replace(/#topart'. $this->articles[0]['id'] .'/g, "") + "#topart'. $this->articles[0]['id'] .'";';
			}
			else
				$this->visible = false;

			if(!empty($this->visible))
			{
				// check for special php content
				foreach($this->articles as $art)
					if($art['ctype'] == 'php' && preg_match('~\[\?pmx_initphp(.*)pmx_initphp\?\]~is', $art['content'], $match))
						eval($match[1]);
			}
		}
		return $this->visible;
	}

	/**
	* ShowContent
	*/
	function pmxc_ShowContent()
	{
		$count = count($this->articles);
		foreach($this->articles as $cnt => $article)
		{
			if(!empty($this->cfg['uniID']) && $count == count($this->articles))
				$article['uniID'] = $this->cfg['uniID'];

			if($this->cfg['config']['settings']['usedframe'] == 'block')
			{
				if(count($this->articles) > 1)
				{
					$article['config']['collapse'] = 0;
					$count--;

					$artdata = Pmx_Frame_top($article, $count);
					$this->WriteContent($article);
					Pmx_Frame_bottom($article, $artdata);
				}
				else
					$this->WriteContent($article);
			}
			else
			{
				$artdata = Pmx_Frame_top($article, $count);
				$this->WriteContent($article);
				Pmx_Frame_bottom($article, $artdata);
			}
		}
	}

	/**
	* Write out the Content
	*/
	function WriteContent($article)
	{
		global $context, $modSettings, $scripturl, $txt;

		if(!empty($article['config']['settings']['printing']))
			ob_start();

		$printdir = empty($context['right_to_left']) ? 'ltr' : 'rtl';
		$printID = 'art'. $article['id'];
		$statID = 'art'. $article['id'] . $this->cfg['side'];
		$tease = 0;

		if($article['ctype'] == 'php')
		{
			if(!empty($article['config']['settings']['printing']))
				echo '
				<img class="pmx_printimg" src="'. $context['pmx_imageurl'] .'Print.png" alt="Print" title="'. $txt['pmx_text_printing'] .'" onclick="PmxPrintPage(\''. $printdir .'\', \''. $printID .'\', \''. htmlspecialchars($this->getUserTitle($article), ENT_QUOTES) .'\')" />
				<div id="print'. $printID .'">';

			// Check we have a show part
			if(preg_match('~\[\?pmx_showphp(.*)pmx_showphp\?\]~is', $article['content'], $match))
				eval($match[1]);

			// else write out the content
			else
				eval($article['content']);

			if(!empty($article['config']['settings']['printing']))
				echo '
				</div>';
		}

		else
		{
			if($article['ctype'] == 'bbc')
			{
				$article['content'] = PortaMx_BBCsmileys(parse_bbc($article['content'], false));
				$tease = $article['config']['settings']['teaser'];
			}

			elseif($article['ctype'] == 'html')
				$tease = !empty($article['config']['settings']['teaser']) ? -1 : 0;

			else
				$tease = $article['config']['settings']['teaser'];

			// article teaser set?
			if(!empty($tease))
			{
				$tmp = '
									<div id="short_'. $statID .'">
									'. PortaMx_Tease_posts($article['content'], $tease, '<div class="smalltext" style="text-align:'.(empty($context['right_to_left']) ? 'right' : 'left').';"><a id="href_short_'. $statID .'" href="'.$scripturl .'" style="padding: 0 5px;" onclick="ShowHTML(\''. $statID .'\')">'. $txt['pmx_readmore'] .'</a></div>') .'
									</div>';

				// if teased?
				if(!empty($context['pmx']['is_teased']))
					$article['content'] = '
									<div id="full_'. $statID .'" style="display:none;">'. (!empty($article['config']['settings']['printing']) ? '
										<img class="pmx_printimg" src="'. $context['pmx_imageurl'] .'Print.png" alt="Print" title="'. $txt['pmx_text_printing'] .'" onclick="PmxPrintPage(\''. $printdir .'\', \''. $printID .'\', \''. htmlspecialchars($this->getUserTitle($article), ENT_QUOTES) .'\')" />
										<div id="print'. $printID .'">'.
											$article['content'] .'
										</div>' : $article['content']) .'
										<div class="smalltext" style="text-align:'.(empty($context['right_to_left']) ? 'right' : 'left').';">
											<a id="href_full_'. $statID .'" href="'.$scripturl .'" style="padding: 0 5px;" onclick="ShowHTML(\''. $statID .'\')">'. $txt['pmx_readclose'] .'</a>
										</div>
									</div>'. $tmp;
				else
					$tease = 0;

				unset($tmp);
			}

			$article['content'] = preg_replace('~<div style="page-break-after\:(.*)<\/div>~i', '', $article['content']);
			if(empty($tease) && !empty($article['config']['settings']['printing']))
				$article['content'] = '
								<img class="pmx_printimg" src="'. $context['pmx_imageurl'] .'Print.png" alt="Print" title="'. $txt['pmx_text_printing'] .'" onclick="PmxPrintPage(\''. $printdir .'\', \''. $printID .'\', \''. htmlspecialchars($this->getUserTitle($article), ENT_QUOTES) .'\')" />
								<div id="print'. $printID .'">'.
									$article['content'] .'
								</div>';

			echo $article['content'];
		}

		if(!empty($article['config']['settings']['printing']))
			echo ob_get_clean();

		if(!empty($article['config']['settings']['showfooter']))
		{
			echo '
							<div style="clear:both;min-height:20px;"><hr />
								<div class="smalltext" style="float:'. (empty($context['right_to_left']) ? 'left' : 'right') .';">
									'. $txt['pmx_text_createdby'] . (!empty($article['member_name']) ? '<a href="'. $scripturl .'?action=profile;u='. $article['owner'] .'">'. $article['member_name'] .'</a>' : $txt['pmx_user_unknown']) .', '. timeformat($article['created']) .'
								</div>';

			if(!empty($article['updated']))
			{
				echo '
								<div class="smalltext" style="float:'. (empty($context['right_to_left']) ? 'right' : 'left') .';">
									'. $txt['pmx_text_updated'] . timeformat($article['updated']) .'
								</div>';
			}
			echo '
							</div>';
		}
	}
}
?>
