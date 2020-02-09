<?php
/**
* \file recent_topics.php
* Systemblock recent_topics
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_recent_topics
* Systemblock recent_topics
* @see recent_topics.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_recent_topics extends PortaMxC_SystemBlock
{
	var $posts;				///< all posts
	var $topics;			///< all topics
	var $isRead;			///< unread topics by member

	/**
	* InitContent.
	* Checks the cache status and create the content.
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
					list($this->topics, $isRead, $this->posts) = $cachedata;
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
		}
		// return the visibility flag (true/false)
		return $this->visible;
	}

	/**
	* fetch_data().
	* Prepare the content and save in $this->cfg['content'].
	*/
	function fetch_data($isRead = null)
	{
		global $user_info;

		$this->posts = null;
		$this->topics = null;
		$this->isRead = null;
		$curRead = $this->get_isRead();

		if(empty($this->cfg['config']['settings']['recentboards']))
			$this->cfg['config']['settings']['recentboards'] = null;

		$this->posts = ssi_recentTopics($this->cfg['config']['settings']['numrecent'], null, $this->cfg['config']['settings']['recentboards'], '');
		if(!empty($this->posts))
		{
			foreach($this->posts as $post)
			{
				$this->topics[] = $post['topic'];
				$this->isRead[$post['topic']] = $curRead['topic'] != $post['topic'] && $post['is_new'] ? false : true;
			}

			// if cache enabled cache the data
			$isRead[$user_info['id']] = $this->isRead;
			return array($this->topics, $isRead, $this->posts);
		}
	}

	/**
	* ShowContent.
	* Output the content and add necessary javascript
	*/
	function pmxc_ShowContent()
	{
		global $context, $scripturl, $settings, $txt;

		$numpost = count($this->posts);
		foreach($this->posts as $post)
		{
			$numpost--;

			if(!empty($this->cfg['config']['settings']['showboard']))
				echo '
				'. $txt['pmx_text_board'] . $post['board']['link'] .'<br />'. $txt['pmx_text_topic'];

			echo '
				<a href="'. $post['href'] .'">'. $post['subject'] .'</a><br />
				'.(empty($context['right_to_left']) ? $txt['by'] .' '. $post['poster']['link'] : $post['poster']['link'] .' '. $txt['by']).'<br />
				'. (empty($this->isRead[$post['topic']]) ? '<a href="'. $scripturl . '?topic='. $post['topic'] . '.msg' . $post['new_from'] .';topicseen#new" /><img src="' . $settings['lang_images_url'] . '/new.gif" alt="' . $txt['new'] . '" border="0" /></a> ' : '').
				'['. $post['time'] .']'. ($numpost > 0 ? '
				<hr />' : '');
		}
	}
}
?>
