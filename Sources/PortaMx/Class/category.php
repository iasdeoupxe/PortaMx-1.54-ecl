<?php
/**
* \file category.php
* Systemblock Category
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_category
* Systemblock Category
* @see category.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_category extends PortaMxC_SystemBlock
{
	var $categories;
	var $articles;
	var $curCat;
	var $firstCat;
	var $postarray;
	var $php_content;
	var $php_vars;

	/**
	* InitContent.
	* Checks the cache status and create the content.
	*/
	function pmxc_InitContent()
	{
		global $context, $user_info, $scripturl, $modSettings, $settings, $pmxCacheFunc;

		// if visible init the content
		if($this->visible)
		{
			$this->postarray = array('cat' => '', 'child' => '', 'art' => '', 'pg' => '');

			// called from static category block?
			if(!empty($this->cfg['config']['static_block']))
			{
				$pKey = 'pmx_static'. $this->cfg['id'] . (!empty($this->inBlockCall) ? '_0' : '') .'_data';

				$data = array();
				$this->postarray['cat'] = $this->cfg['config']['settings']['category'];

				if(!empty($_POST[$pKey]))
					$data = explode(';', $_POST[$pKey]);
				elseif(($cook = pmx_getcookie('LSBsub'. $this->cfg['id'])) && !is_null($cook))
					$data = explode('&', str_replace('->', '=', $cook));

				foreach($data as $var)
				{
					if(!empty($var))
					{
						list($key, $val) = explode('=', $var);
						if(preg_match('~pg\[([a-zA-Z0-9\-]+)\]~', $key, $match) > 0)
							$this->postarray['pg'][$match[1]] = $val;
						else
							$this->postarray[$key] = $val;
					}
				}

				// get the category and his childs
				if(!empty($this->cfg['cache']))
					$cachedata = $pmxCacheFunc['get']($this->cache_key, $this->cache_mode);
				else
					$cachedata = null;

				if($cachedata !== null)
				{
					list($this->categories, $this->articles) = $cachedata;
					$cats = $this->categories[$this->cfg['config']['settings']['category']];
				}
				else
				{
					$cats = PortaMx_getCatByID(null, $this->cfg['config']['settings']['category']);
					$cats['config'] = unserialize($cats['config']);

					// get cat data
					$addSub = !empty($cats['config']['settings']['addsubcats']) || !empty($cats['config']['settings']['showsubcats']);
					if(!empty($this->cfg['config']['settings']['inherit_acs']) || allowPmxGroup($cats['acsgrp']))
						$this->getCatsAndChilds($cats, $addSub, $this->cfg['config']['settings']['inherit_acs']);

					if(!empty($this->cfg['cache']))
						$pmxCacheFunc['put']($this->cache_key, array($this->categories, $this->articles), $this->cache_time, $this->cache_mode);
				}

				$this->firstcat = (!empty($this->postarray['child']) ? $this->postarray['child'] : $cats['name']);
				$this->curCat = null;
				if(isset($this->categories[$this->firstcat]))
					$this->curCat = $this->categories[$this->firstcat];

				if(!is_null($this->curCat) && !empty($this->postarray['art']))
				{
					$found = false;
					foreach($this->articles[$this->curCat['name']] as $article)
						$found = $article['name'] == $this->postarray['art'] ? true : $found;
				}
				else
					$found = true;

				if(!is_null($this->curCat) && !empty($found))
				{
					// check framemode
					if($this->cfg['config']['settings']['usedframe'] == 'cat')
					{
						$this->cfg['config']['skip_outerframe'] = true;
						$this->curCat['catid'] = $this->curCat['id'];
					}
					else
					{
						$this->curCat['config']['skip_outerframe'] = true;
						$this->curCat['config']['visuals']['frame'] = $this->cfg['config']['visuals']['frame'];
						$this->cfg['catid'] = $this->cfg['id'];
						$this->cfg['blocktype'] = 'catblock';
					}
					$this->cfg['uniID'] = 'blk'. $this->cfg['id'];
				}
				else
					$this->visible = false;
			}

			// else requested cat
			else
			{
				// caregory limited to admins?
				if(!empty($this->cfg['config']['request']) && !allowPmx('pmx_admin'))
					$this->visible = false;
				else
				{
					foreach($this->postarray as $key => $val)
					{
						if(!empty($_GET[$key]))
						{
							if(is_array($_GET[$key]))
							{
								$this->postarray[$key][key($_GET[$key])] = $_GET[$key][key($_GET[$key])];
								unset($_GET[$key][key($_GET[$key])]);
							}
							else
								$this->postarray[$key] = $_GET[$key];
						}
						elseif($key == 'pg')
						{
							if(($cook = pmx_getcookie('pgidx_cat'. $this->cfg['id'])) && !is_null($cook))
								$this->postarray[$key]['cat'. $this->cfg['id']] = $cook;
						}
					}

					// get cat data and all childs
					$cats = PortaMx_getCatByID(null, $this->postarray['cat']);
					$addSub = !empty($this->cfg['config']['settings']['addsubcats']) || !empty($this->cfg['config']['settings']['showsubcats']);
					if(allowPmxGroup($cats['acsgrp']))
					{
						$this->getCatsAndChilds($cats, $addSub);
						$this->firstcat = (!empty($this->postarray['child']) ? $this->postarray['child'] : $this->postarray['cat']);
						$this->curCat = null;

						if(isset($this->categories[$this->firstcat]))
							$this->curCat = $this->categories[$this->firstcat];

						if(!is_null($this->curCat) && !empty($this->postarray['art']))
						{
							$found = false;
							foreach($this->articles[$this->curCat['name']] as $article)
								$found = $article['name'] == $this->postarray['art'] ? true : $found;
						}
						else
							$found = true;

						if(!is_null($this->curCat) && !empty($found))
						{
							// save titles for linktree
							$context['pmx']['pagenames']['cat'] = $this->categories[$cats['name']]['title'];
							if(empty($context['pmx']['pagenames']['cat']))
								$context['pmx']['pagenames']['cat'] = htmlspecialchars($cats['name'], ENT_QUOTES);

							if(!empty($this->postarray['child']))
							{
								$context['pmx']['pagenames']['child'] = $this->curCat['title'];
								if(empty($context['pmx']['pagenames']['child']))
									$context['pmx']['pagenames']['child'] = htmlspecialchars($this->curCat['name'], ENT_QUOTES);
							}

							$this->cfg['uniID'] = 'cat'. $this->categories[$cats['name']]['id'];
							$this->cfg['config']['skip_outerframe'] = true;
							$this->curCat['catid'] = $this->curCat['id'];
						}
						else
							$this->visible = false;
					}
					else
						$this->visible = false;
				}
			}
		}

		if(!empty($this->visible) && !empty($this->articles))
		{
			// handle special php articles
			foreach($this->articles as $cn => $artlist)
			{
				foreach($artlist as $id => $article)
				{
					if($article['ctype'] == 'php' && preg_match('~\[\?pmx_initphp(.*)pmx_initphp\?\]~is', $article['content'], $match))
						eval($match[1]);
				}
			}
		}

		return $this->visible;
	}

	/**
	* Get category and his childs
	*/
	function getCatsAndChilds($cats, $addSub, $acs_inherit = false)
	{
		global $context, $smcFunc;

		$catIDs = array();
		$catNames = array();
		$this->categories = array();
		$corder = $cats['catorder'];
		$cat = PortaMx_getCatByOrder(array($cats), $corder);

		while(is_array($cat))
		{
			if(!empty($cat['artsum']))
			{
				// inherit acs from block?
				if(!is_array($cat['config']))
					$cat['config'] = unserialize($cat['config']);

				if(!empty($acs_inherit) || allowPmxGroup($cat['acsgrp']))
				{
					$ttl = htmlspecialchars($this->getUserTitle($cat), ENT_QUOTES);
					if(empty($ttl))
						$ttl = htmlspecialchars($cat['name'], ENT_QUOTES);

					$this->categories[$cat['name']] = array(
						'id' => $cat['id'],
						'name' => $cat['name'],
						'artsort' => $cat['artsort'],
						'acsgrp' => $cat['acsgrp'],
						'config' => $cat['config'],
						'side' => $this->cfg['side'],
						'blocktype' => 'category',
						'customclass' => '',
						'title' => $ttl,
					);
					$catIDs[] = $cat['id'];
					$catNames[$cat['id']] = $cat['name'];
				}
			}

			if(!empty($addSub))
			{
				$corder = PortaMx_getNextCat($corder);
				$cat = PortaMx_getCatByOrder(array($cats), $corder);
			}
			else
				break;
		}

		if(!empty($catIDs))
		{
			// get articles for any cat
			$request = $smcFunc['db_query']('', '
				SELECT a.id, a.name, a.acsgrp, a.catid, a.ctype, a.config, a.owner, a.active, a.created, a.updated, a.approved, a.content, CASE WHEN m.real_name = {string:empty} THEN m.member_name ELSE m.real_name END AS mem_name
				FROM {db_prefix}portamx_articles AS a
				LEFT JOIN {db_prefix}members AS m ON (a.owner = m.id_member)
				WHERE a.catid IN ({array_int:cats}) AND a.active > 0 AND a.approved > 0
				ORDER BY a.id',
				array(
					'cats' => $catIDs,
					'empty' => '',
				)
			);

			if($smcFunc['db_num_rows']($request) > 0)
			{
				while($row = $smcFunc['db_fetch_assoc']($request))
				{
					$row['config'] = unserialize($row['config']);
					if(!empty($this->categories[$catNames[$row['catid']]]['config']['settings']['inherit_acs']) || allowPmxGroup($row['acsgrp']))
					{
						$row['side'] = $this->cfg['side'];
						$row['blocktype'] = !empty($this->cfg['config']['static_block']) ? 'static_article' : 'article';
						$row['member_name'] = $row['mem_name'];
						$this->articles[$catNames[$row['catid']]][] = $row;
					}
				}
				$smcFunc['db_free_result']($request);
			}
		}

		// articles found?
		$ccats = $this->categories;
		foreach($ccats as $cname => $cdata)
		{
			if(!empty($this->articles[$cname]))
			{
				$this->articles[$cname] = PortaMx_ArticleSort($this->articles[$cname], $this->categories[$cname]['artsort']);

				// if article reqested, get the tile
				if(empty($this->cfg['config']['static_block']) && !empty($this->postarray['art']) && $cname == (empty($this->postarray['child']) ? $this->postarray['cat'] : $this->postarray['child']))
				{
					foreach($this->articles[$cname] as $art)
					{
						if($art['name'] == $this->postarray['art'])
						{
							$context['pmx']['pagenames']['art'] = htmlspecialchars($this->getUserTitle($art), ENT_QUOTES);
							if(empty($context['pmx']['pagenames']['art']))
								$context['pmx']['pagenames']['art'] = htmlspecialchars($art['name'], ENT_QUOTES);
							break;
						}
					}
				}
			}
			else
				unset($this->categories[$cname]);
		}
	}

	/**
	* create a url for requested or static block
	**/
	function GetUrl($data, $firstchr = '')
	{
		global $context, $modSettings, $scripturl;

		if(!empty($this->cfg['config']['static_block']))
			return $firstchr .'onclick="pmx_StaticBlockSub(\''. $this->cfg['id'] .'\', this, \''. $data .'\', \''. $this->cfg['uniID'] .'\')';
		else
		{
			$topfragment = !empty($context['pmx']['settings']['topfragment']) ? '#top'. $this->cfg['uniID'] : '';
			$data = !empty($this->postarray['cat']) ? 'cat='. $this->postarray['cat'] .(!empty($data) ? ';' : ''). $data : $data;
			return $scripturl .(!empty($data) ? '?'. $data : '') . $topfragment . '" onclick="pmxWinGetTop(\''. $this->cfg['uniID'] .'\')';
		}
	}

	/**
	* ShowContent
	*/
	function pmxc_ShowContent($blockcount)
	{
		global $context, $scripturl, $modSettings, $user_info, $txt;

		$topfragment = !empty($context['pmx']['settings']['topfragment']) ? '#top'. $this->cfg['uniID'] : '';

		if(!empty($this->cfg['config']['static_block']))
		{
			echo '
			<form id="pmx_static'. $this->cfg['id'] .'_form" accept-charset="'. $context['character_set'] .'" action="'. $topfragment .'" method="post" style="margin: 0px;">
				<input type="hidden" id="pmx_static'. $this->cfg['id'] .'_data" name="pmx_static'. $this->cfg['id'] .'_data" value="" />';
		}
		else
		{
			if(!empty($topfragment))
				$context['pmx']['html_footer'] .= '
				window.location.href = window.location.href.replace(/#top'. $this->cfg['uniID'] .'/g, "") + "#top'. $this->cfg['uniID'] .'";';

			foreach($this->postarray as $key => $val)
			{
				if(isset($_GET[$key]))
					unset($_GET[$key]);
			}
		}

		// show all articles on a page
		if($this->curCat['config']['settings']['showmode'] == 'pages')
		{
			// create the pageindex
			$page = 0;
			if(isset($this->postarray['pg']) && is_array($this->postarray['pg']) && array_key_exists($this->cfg['uniID'], $this->postarray['pg']))
				$page = $this->postarray['pg'][$this->cfg['uniID']];

			$artCount = count($this->articles[$this->curCat['name']]);
			if(empty($this->curCat['config']['settings']['pages']))
				$this->curCat['config']['settings']['pages'] = $artCount;

			if($artCount > $this->curCat['config']['settings']['pages'] || !empty($this->curCat['config']['settings']['pageindex']))
			{
				$url = preg_replace('~pg\[[a-zA-Z0-0)+\]\=[0-9\;]+~', '', getCurrentUrl(true)) .'pg['. $this->cfg['uniID'] .']=%1$d'. $topfragment;
				$pageindex = $this->pmxc_makePageIndex($url, $page, $artCount, $this->curCat['config']['settings']['pages']);
				if(empty($this->cfg['config']['static_block']))
					$pageindex = str_replace('href="', 'onclick="pmxWinGetTop(\''. $this->cfg['uniID'] .'\')" href="', $pageindex);
				else
					$pageindex = str_replace('href="', $this->GetUrl('pg') .'" href="', $pageindex);

				$count = $artCount - $page;
				if($count > $this->curCat['config']['settings']['pages'])
					$count = $this->curCat['config']['settings']['pages'];
			}

			echo '
				<a name="top'. $this->cfg['uniID'] .'"></a>';

			// show category frame?
			if(in_array($this->curCat['config']['settings']['framemode'], array('both', 'category')))
			{
				$this->getCustomCSS($this->curCat);
				$this->curCat['id'] = !empty($this->cfg['config']['static_block']) ? $this->cfg['id'] : $this->curCat['id'];
				$catdata = Pmx_Frame_top($this->curCat, $blockcount);
				$catframe = $this->curCat['config']['visuals']['frame'];
			}
			else
				$catframe = '';

			// top pageindex
			if(!empty($pageindex))
				echo '
					<div class="smalltext pmx_pgidx_top">'. $txt['pages']. ': '. $pageindex . $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#bot'. $this->cfg['uniID'] .'"><strong>' . $txt['go_down'] . '</strong></a></div>';

			if(!empty($this->curCat['config']['settings']['showsubcats']))
			{
				$subcats = array();
				$firstcat = false;
				foreach($this->categories as $name => $cat)
				{
					if($this->firstcat == $name && empty($firstcat))
					{
						$firstcat = true;
						$sbCat = $cat;
					}
					if($this->postarray['cat'] != $name)
						$subcats[] = '<a href="'. $this->GetUrl('child='. $name, '#" ') .'">'. $cat['title'] .'</a><br />';
				}

				$this->getCustomCSS($sbCat);
				$sbCat['config']['visuals']['header'] = 'none';
				$sbCat['config']['visuals']['bodytext'] = 'smalltext';
				$sbCat['config']['innerpad'] = '0,5';
				$sbCat['config']['collapse'] = 0;
				$sbCat['config']['visuals']['frame'] = $sbCat['config']['visuals']['frame'];

				echo '
					<table width="100%" cellspacing="0" cellpadding="0" border="0">
						<tr>';

				if(!empty($this->curCat['config']['settings']['sbpalign']) && !empty($subcats))
				{
					echo '
							<td valign="top" align="left">
								<div  class="smalltext" style="width:'. $this->curCat['config']['settings']['catsbarwidth'] .'px; margin-'. (empty($context['right_to_left']) ? 'right' : 'left') .':10px;">';

					$this->WriteSidebar($sbCat, $subcats, '', '');

					echo '
								</div>
							</td>
							<td valign="top" width="100%">';
				}

				elseif(empty($this->curCat['config']['settings']['sbpalign']) && !empty($subcats))
					echo '
							<td valign="top" width="100%">';
			}

			// output the article content
			$sumart = null;
			foreach($this->articles[$this->curCat['name']] as $cnt => $article)
			{
				if($cnt >= $page && $cnt - $page < $this->curCat['config']['settings']['pages'])
				{
					$sumart = is_null($sumart) ? ($artCount - $page > $this->curCat['config']['settings']['pages'] ? $this->curCat['config']['settings']['pages'] -1 : ($artCount < $this->curCat['config']['settings']['pages'] ? $artCount -1 : $artCount - ($page +1))) : $sumart;

					// show article frame?
					if(in_array($this->curCat['config']['settings']['framemode'], array('both', 'article')))
					{
						$this->getCustomCSS($article);
						if($article['config']['visuals']['frame'] == 'round' && $article['config']['visuals']['frame'] == $catframe)
							$article['config']['visuals']['frame'] = '';

						$artdata = Pmx_Frame_top($article, $sumart);
						$this->WriteContent($article);
						Pmx_Frame_bottom($article, $artdata);
					}
					else
						$this->WriteContent($article);

					$sumart--;
					if(empty($sumart))
						echo '
					<a name="bot'. $this->cfg['uniID'] .'"></a>';
				}
			}

			// show childcats in the sidebar?
			if(empty($this->curCat['config']['settings']['sbpalign']) && !empty($subcats))
			{
				echo '
							</td>
							<td valign="top" align="right">
								<div  class="smalltext" style="width:'. $this->curCat['config']['settings']['catsbarwidth'] .'px; margin-'. (empty($context['right_to_left']) ? 'left' : 'right') .':10px;">';

					$this->WriteSidebar($sbCat, $subcats, '', '');

				echo '
								</div>';
			}

			if(!empty($this->curCat['config']['settings']['showsubcats']))
				echo '
							</td>
						</tr>
					</table>';

			// bottom pageindex
			if(!empty($pageindex))
				echo '
					<div class="smalltext pmx_pgidx_bot">'. $txt['pages']. ': '. $pageindex . $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#top'. $this->cfg['uniID'] .'"><strong>' . $txt['go_up'] . '</strong></a></div>';

			// show category frame?
			if(in_array($this->curCat['config']['settings']['framemode'], array('both', 'category')))
				Pmx_Frame_bottom($this->curCat, $catdata);
		}

		// first article and titles in the sidebar
		else
		{
			echo '
				<a name="top'. $this->cfg['uniID'] .'"></a>';

			// show category frame?
			$catframe = '';
			if(in_array($this->curCat['config']['settings']['framemode'], array('both', 'category')))
			{
				$this->getCustomCSS($this->curCat);
				$this->curCat['id'] = !empty($this->cfg['config']['static_block']) ? $this->cfg['id'] : $this->curCat['id'];
				$catdata = Pmx_Frame_top($this->curCat, $blockcount);
				$catframe = $this->curCat['config']['visuals']['frame'];
			}

			$subcats = array();
			foreach($this->categories as $name => $cat)
			{
				if($this->firstcat == $name)
					$sbCat = $cat;
				if($this->curCat['name'] == $name)
					$artcat = '<a href="'. $this->GetUrl($this->postarray['cat'] == $name ? '' : 'child='. $name, '#" ') .'">'. $this->categories[$this->curCat['name']]['title'] .'</a>';
				if(!empty($this->curCat['config']['settings']['addsubcats']))
				{
					if($this->postarray['cat'] != $name)
						$subcats[] = '<a href="'. $this->GetUrl('child='. $name, '#" ') .'">'. $cat['title'] .'</a><br />';
				}
			}

			$curart = $this->postarray['art'];
			if(empty($curart))
				$curart = $this->articles[$this->curCat['name']][0]['name'];

			$this->getCustomCSS($sbCat);
			$sbCat['config']['visuals']['header'] = 'none';
			$sbCat['config']['visuals']['bodytext'] = 'smalltext';
			$sbCat['config']['collapse'] = 0;
			$sbCat['config']['innerpad'] = '0,5';
			$sbCat['config']['visuals']['frame'] = $sbCat['config']['visuals']['frame'];

			echo '
					<table width="100%" cellspacing="0" cellpadding="0" border="0">
						<tr>';

			// subcategory list at left
			if(!empty($this->curCat['config']['settings']['sbmalign']))
			{
				echo '
							<td valign="top" align="left">
								<div class="smalltext" style="width:'. $this->curCat['config']['settings']['sidebarwidth'] .'px; margin-'. (empty($context['right_to_left']) ? 'right' : 'left') .':10px;">';

				$this->WriteSidebar($sbCat, $subcats, $artcat, $curart);

				echo '
								</div>
							</td>';
			}
			echo '
							<td valign="top" width="100%">';

			$count = 0;
			foreach($this->articles[$this->curCat['name']] as $article)
				$count += intval($article['name'] == $curart);

			foreach($this->articles[$this->curCat['name']] as $article)
			{
				if($article['name'] == $curart)
				{
					// show article frame?
					if(in_array($this->curCat['config']['settings']['framemode'], array('both', 'article')))
					{
						$count--;
						$this->getCustomCSS($article);
						if($article['config']['visuals']['frame'] == 'round' && $article['config']['visuals']['frame'] == $catframe)
							$article['config']['visuals']['frame'] = '';

						$artdata = Pmx_Frame_top($article, $count);
						$this->WriteContent($article);
						Pmx_Frame_bottom($article, $artdata);
					}
					else
						$this->WriteContent($article);
					break;
				}
			}

			// subcategory list at right
			if(empty($this->curCat['config']['settings']['sbmalign']))
			{
				echo '
							</td>
							<td valign="top" align="right">
								<div  class="smalltext" style="width:'. $this->curCat['config']['settings']['sidebarwidth'] .'px; margin-'. (empty($context['right_to_left']) ? 'left' : 'right') .':10px;">';

				$this->WriteSidebar($sbCat, $subcats, $artcat, $curart);

				echo '
								</div>';
			}

			echo '
							</td>
						</tr>
					</table>';

			if(in_array($this->curCat['config']['settings']['framemode'], array('both', 'category')))
				Pmx_Frame_bottom($this->curCat, $catdata);
		}

		if(!empty($this->cfg['config']['static_block']))
			echo '
			</form>';

		return 1;
	}

	/**
	* Write out the Sidebar
	*/
	function WriteSidebar($sbCat, $subcats, $artcat, $curart)
	{
		global $context, $scripturl, $txt;

		$sbCat['noofs'] = true;
		$sbdata = Pmx_Frame_top($sbCat, 1);

		if(!empty($curart))
		{
			echo '
							<em>'. $txt['pmx_more_articles'] .'<br />'. $artcat .'</em><hr />';

			foreach($this->articles[$this->curCat['name']] as $article)
			{
				$ttl = $this->getUserTitle($article);
				if(empty($ttl))
					$ttl = htmlspecialchars($article['name'], ENT_QUOTES);

				echo '
							<a href="'. $this->GetUrl((!empty($this->postarray['child']) ? 'child='. $this->postarray['child'] .';' : '') .'art='. $article['name'], '#" ') .'">'. ($curart == $article['name'] ? '<b>'. $ttl .'</b>' : $ttl) .'</a><br />';
			}
		}

		if(!empty($subcats))
		{
			if(!empty($curart))
				echo '
							<br />';
			echo '
							<em>'. $txt['pmx_more_categories'] .'<br />
							<a href="'. $this->GetUrl('', '#" ') .'">'. $this->categories[$this->postarray['cat']]['title'] .'</a></em>
							<hr />';

			foreach($subcats as $cat)
				echo $cat;
		}

		Pmx_Frame_bottom($sbCat, $sbdata);
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
										preg_replace('~<div style="page-break-after: always;">.*</div>~i', '', $article['content']) .'
										</div>' : $article['content']) .'
										<div class="smalltext" style="text-align:'.(empty($context['right_to_left']) ? 'right' : 'left').';">
											<a id="href_full_'. $statID .'" href="'.$scripturl .'" style="padding: 0 5px;" onclick="ShowHTML(\''. $statID .'\')">'. $txt['pmx_readclose'] .'</a>
										</div>
									</div>'. $tmp;
				else
					$tease = 0;

				unset($tmp);
			}

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
									'. $txt['pmx_text_createdby'] .'<a href="'. $scripturl .'?action=profile;u='. $article['owner'] .'">'. $article['member_name'] .'</a>, '. timeformat($article['created']) .'
								</div>';

			if(!empty($article['updated']))
			{
				echo '
								<div class="smalltext" style="float:'. (empty($context['right_to_left']) ? 'right' : 'left') .';">';
				echo '
									'. $txt['pmx_text_updated'] . timeformat($article['updated']) .'
								</div>';
			}
			echo '
							</div>';
		}
	}
}
?>
