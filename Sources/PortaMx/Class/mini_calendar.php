<?php
/**
* \file mini_calendar.php
* Systemblock mini_calendar
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_mini_calendar
* Systemblock mini_calendar
* @see mini_calendar.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_mini_calendar extends PortaMxC_SystemBlock
{
	var $today;
	var $cal_startday;
	var $calgrid;
	var $calbirthdays;
	var $calholidays;
	var $calevents;

	/**
	* checkCacheStatus.
	* Article trigger do nothing.
	*/
	function pmxc_checkCacheStatus()
	{
		global $pmxCacheFunc;

		$result = true;
		if(isset($this->cfg['cache']) && $this->cfg['cache'] > 0)
		{
			if(isset($_POST['calendar']))
			{
				$pmxCacheFunc['clear']($this->cache_key .'-0', false);
				$pmxCacheFunc['clear']($this->cache_key .'-1', false);
				$pmxCacheFunc['clear']($this->cache_key .'-6', false);
				$result = false;
			}
		}
		return $result;
	}

	/**
	* InitContent.
	* Checks the cache status and create the content.
	*/
	function pmxc_InitContent()
	{
		global $context, $sourcedir, $pmxCacheFunc, $modSettings, $options;

		// if visible init the content
		if($this->visible)
		{
			$this->today = array(
				'day' => (int) strftime('%d', forum_time()),
				'month' => (int) strftime('%m', forum_time()),
				'year' => (int) strftime('%Y', forum_time()),
				'date' => strftime('%Y-%m-%d', forum_time()),
			);
			$this->cal_startday = isset($options['calendar_start_day']) ? $options['calendar_start_day'] : $this->cfg['config']['settings']['firstday'];
			$this->cache_key .= '-'. $this->cal_startday;

			$cachedata = null;
			if(!empty($this->cfg['cache']))
			{
				if(($cachedata = $pmxCacheFunc['get']($this->cache_key, $this->cache_mode)) !== null)
				{
					list($curday, $this->calgrid, $this->calbirthdays, $this->calholidays, $this->calevents) = $cachedata;
					if($curday != $this->today['date'])
					{
						$pmxCacheFunc['clear']($this->cache_key, $this->cache_mode);
						$cachedata = null;
					}
				}
			}

			if(empty($cachedata))
			{
				include_once($sourcedir .'/Subs-Calendar.php');

				$calendarOptions = array(
					'start_day' => $this->cal_startday,
					'show_birthdays' => false,
					'show_events' => false,
					'show_holidays' => false,
					'show_week_num' => false,
					'short_day_titles' => true,
					'show_next_prev' => false,
					'show_week_links' => false,
					'size' => 'small',
				);
				$this->calgrid = getCalendarGrid($this->today['month'], $this->today['year'], $calendarOptions);

				$this->calbirthdays = array();
				if(!empty($this->cfg['config']['settings']['birthdays']['show']))
				{
					$start_data = (isset($this->cfg['config']['settings']['birthdays']['before']) ? date('Y-m-d', time() - (86400 * intval($this->cfg['config']['settings']['birthdays']['before']))) : date('Y-m-d'));
					$end_data = (isset($this->cfg['config']['settings']['birthdays']['after']) ? date('Y-m-d', time() + (86400 * intval($this->cfg['config']['settings']['birthdays']['after']))) : date('Y-m-d'));
					$temp = getBirthdayRange($start_data, $end_data);
					foreach($temp as $key => $val)
					{
						$mnt = intval(substr($key, 5, 2));
						if(in_array($mnt, array(11, 12)) && in_array($this->today['month'], array(1, 12)))
							$nkey = strval($this->today['year'] -1) . substr($key, 4);
						else
							$nkey = strval($this->today['year']) . substr($key, 4);
						$this->calbirthdays[$nkey] = $val;
					}
					ksort($this->calbirthdays);
				}

				$this->calholidays = array();
				if(!empty($this->cfg['config']['settings']['holidays']['show']))
				{
					$start_data = (isset($this->cfg['config']['settings']['holidays']['before']) ? date('Y-m-d', time() - (86400 * intval($this->cfg['config']['settings']['holidays']['before']))) : date('Y-m-d'));
					$end_data = (isset($this->cfg['config']['settings']['holidays']['after']) ? date('Y-m-d', time() + (86400 * intval($this->cfg['config']['settings']['holidays']['after']))) : date('Y-m-d'));
					$this->calholidays = getHolidayRange($start_data, $end_data);
					ksort($this->calholidays);
				}

				$this->calevents = array();
				if(!empty($this->cfg['config']['settings']['events']['show']))
				{
					$start_data = (isset($this->cfg['config']['settings']['events']['before']) ? date('Y-m-d', time() - (86400 * intval($this->cfg['config']['settings']['events']['before']))) : date('Y-m-d'));
					$end_data = (isset($this->cfg['config']['settings']['events']['after']) ? date('Y-m-d', time() + (86400 * intval($this->cfg['config']['settings']['events']['after']))) : date('Y-m-d'));
					$events = getEventRange($start_data, $end_data);
					ksort($events);

					foreach($events as $event)
					{
						foreach($event as $data)
						{
							if(!array_key_exists($data['id'], $this->calevents))
								$this->calevents[$data['id']] = $data;
						}
					}
				}

				if(!empty($this->cfg['cache']))
				{
					$cachedata = array($this->today['date'], $this->calgrid, $this->calbirthdays, $this->calholidays, $this->calevents);
					$pmxCacheFunc['put']($this->cache_key, $cachedata, $this->cache_time, $this->cache_mode);
					unset($cachedata);
				}
			}
		}
		return $this->visible;
	}

	/**
	* ShowContent
	*/
	function pmxc_ShowContent()
	{
		global $smcFunc, $scripturl, $modSettings, $txt;

		// writeout the head
		$this->cfg['dateform'] = $txt['pmx_minical_dateform'];

		echo '
				<div class="calgrid_head normaltext">';

		if(!empty($modSettings['cal_enabled']))
			echo '
					<a href="'. $scripturl .'?action=calendar;year='. $this->calgrid['current_year'] .';month='. $this->calgrid['current_month'] .'">'. $txt['months'][intval($this->calgrid['current_month'])] .' '. $this->calgrid['current_year'] .'</a>';
		else
			echo $txt['months'][intval($this->calgrid['current_month'])] .' '. $this->calgrid['current_year'];

		echo '
				</div>
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>';

		// writeout the day names
		foreach ($this->calgrid['week_days'] as $day)
		{
			echo '
						<th class="calgrid';

			// is weekend?
			if(in_array($day, array(0, 6)))
				echo ' calgrid_day'. $day;

			echo '"><b>'. $smcFunc['substr']($txt['days'][intval($day)], 0, 2) .'</b></th> ';
		}
		echo '
					</tr>';

		// now the entires calendar
		foreach ($this->calgrid['weeks'] as $week)
		{
			echo '
					<tr>';
			$wd = 0;
			foreach($week['days'] as $days)
			{
				echo '
						<td class="calgrid';

				// is today?
				if(!empty($days['is_today']))
					echo ' calgrid_today plainbox';

				// is weekend?
				if(in_array($this->calgrid['week_days'][$wd], array(0, 6)))
					echo ' calgrid_day'. $this->calgrid['week_days'][$wd];

				echo '">'. (!empty($days['day']) ? $days['day'] : '') .'</td> ';
				$wd++;
			}
			echo '
					</tr>';
		}

		echo '
				</table>';

		// we have birthdays ?
		if(!empty($this->calbirthdays))
		{
			echo '
				<div class="calgrid_head calgrid_pad normaltext">'. $txt['pmx_cal_birthdays'] .'</div>';
			foreach($this->calbirthdays as $cdate => $data)
			{
				foreach($data as $vals)
					echo $this->caldateform($cdate) .': <a href="'. $scripturl .'?action=profile;u='. $vals['id'] .'">'. $vals['name'] .(!empty($vals['age']) ? ' ('. $vals['age'] .')' : '') .'</a><br />';
			}
		}

		// we have holidays ?
		if(!empty($this->calholidays))
		{
			echo '
				<div class="calgrid_head calgrid_pad normaltext">'. $txt['pmx_cal_holidays'] .'</div>';
			foreach($this->calholidays as $cdate => $data)
			{
				foreach($data as $vals)
					echo $this->caldateform($cdate) .': '. $vals .'<br />';
			}
		}

		// we have events ?
		if(!empty($this->calevents))
		{
			echo '
				<div class="calgrid_head calgrid_pad normaltext">'. $txt['pmx_cal_events'] .'</div>';

			foreach($this->calevents as $data)
			{
				// single event
				if($data['end_date'] == $data['start_date'])
					echo $this->caldateform($data['start_date']) .': '. $data['link'] .'<br />';

				// spaned event
				else
					echo $this->caldateform($data['start_date'], $data['end_date']) .': '. $data['link'] .'<br />';
			}
		}
	}

	// Birthday, Holiday, Event date format
	function caldateform($dat1, $dat2 = '')
	{
		global $txt;

		$datearray = array('%M', '%m', '%d', '%j');
		list($d1['year'], $d1['month'], $d1['day']) = explode('-', $dat1);
		if(empty($dat2))
			return str_replace($datearray, array($txt['months_short'][intval($d1['month'])], $d1['month'], $d1['day'], intval($d1['day'])), $txt['pmx_minical_dateform'][0]);
		else
		{
			list($d2['year'], $d2['month'], $d2['day']) = explode('-', $dat2);
			if($d1['month'] == $d2['month'])
			{
				$tmp = str_replace($datearray, array($txt['months_short'][intval($d1['month'])], $d1['month'], $d1['day'], intval($d1['day'])), $txt['pmx_minical_dateform'][1]);
				return $tmp . str_replace($datearray, array($txt['months_short'][intval($d2['month'])], $d2['month'], $d2['day'], intval($d2['day'])), $txt['pmx_minical_dateform'][2]);
			}
			else
			{
				$tmp = str_replace($datearray, array($txt['months_short'][intval($d1['month'])], $d1['month'], $d1['day'], intval($d1['day'])), $txt['pmx_minical_dateform'][3]);
				return $tmp . str_replace($datearray, array($txt['months_short'][intval($d2['month'])], $d2['month'], $d2['day'], intval($d2['day'])), $txt['pmx_minical_dateform'][4]);
			}
		}
	}
}
?>
