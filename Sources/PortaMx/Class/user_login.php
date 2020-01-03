<?php
/**
* \file user_login.php
* Systemblock user_login
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_user_login
* Systemblock user_login
* @see user_login.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_user_login extends PortaMxC_SystemBlock
{
	/**
	* ShowContent
	* Output the content.
	*/
	function pmxc_ShowContent()
	{
		global $context, $scripturl, $boardurl, $modSettings, $user_info, $txt;

		$is_adj = $context['browser']['is_ie'] || $context['browser']['is_opera'];

		// avatar
		if(!empty($context['user']['avatar']) && !empty($this->cfg['config']['settings']['show_avatar']))
			echo '
									<div style="padding-bottom:4px;">
										<a href="'. $scripturl .'?action=profile;u='. $context['user']['id'] .'">'. $context['user']['avatar']['image'] .'</a>
									</div>';

		// User logged in?
		if($context['user']['is_logged'])
		{
			echo '
									<span' .(isset($this->cfg['config']['visuals']['hellotext']) ? ' class="'. $this->cfg['config']['visuals']['hellotext'] .'"' : ''). '>'.
										(empty($context['right_to_left'])
										? $txt['pmx_hello'] .' <a href="'. $scripturl .'?action=profile;u='. $context['user']['id'] .'"><b>'. $context['user']['name'] .'</b></a>'
										: '<a href="'. $scripturl .'?action=profile;u='. $context['user']['id'] .'"><b>'. $context['user']['name'] .'</b></a> '. $txt['pmx_hello']).'
									</span>';

			$img = '<img src="'. $context['pmx_syscssurl'].'Images/bullet_blue.gif" alt="*" title="" />';
			$img1 = '<img src="'. $context['pmx_syscssurl'].'Images/bullet_red.gif" alt="*" title="" />';
			echo '
									<ul class="userlogin">';

			// show pm?
			if(!empty($this->cfg['config']['settings']['show_pm']) && $context['allow_pm'])
				echo '
										<li>'.($context['user']['unread_messages'] > 0 ? $img1 : $img).'<span><a href="'. $scripturl .'?action=pm">'. $txt['pmx_pm'] .($context['user']['unread_messages'] > 0 ? ': '.$context['user']['unread_messages'].' <img src="'. $context['pmx_imageurl'].'newpm.gif" alt="*" title="'. $context['user']['unread_messages'] .'" />' : '').'</a></span></li>';

			// Are there any members waiting for approval?
			if(!empty($this->cfg['config']['settings']['show_unapprove']) && !empty($context['unapproved_members']))
				echo '
										<li>'.$img1.'<span><a href="'. $scripturl .'?action=admin;area=viewmembers;sa=browse;type=approve">'. $txt['pmx_unapproved_members'] .' <b>'. $context['unapproved_members']  .'</b></a></span></li>';

			// show post?
			if(empty($modSettings['shd_helpdesk_only']) && !empty($this->cfg['config']['settings']['show_posts']))
			{
				echo '
										<li>'.$img.'<span><a href="'. $scripturl .'?action=unread">'. $txt['pmx_unread'] .'</a></span></li>
										<li>'.$img.'<span><a href="'. $scripturl .'?action=unreadreplies">'. $txt['pmx_replies'] .'</a></span></li>
										<li>'.$img.'<span><a href="'. $scripturl .'?action=profile;area=showposts;u='. $context['user']['id'] .'">'. $txt['pmx_showownposts'] .'</a></span></li>';
			}
			echo '
									</ul>';

			// Is the forum in maintenance mode?
			if($context['in_maintenance'] && $context['user']['is_admin'])
				echo '
									<b>'. $txt['pmx_maintenace'] .'</b><br />';

			// Show the total time logged in?
			if(!empty($context['user']['total_time_logged_in']) && isset($this->cfg['config']['settings']['show_logtime']) && $this->cfg['config']['settings']['show_logtime'] == 1)
			{
				$totm = $context['user']['total_time_logged_in'];
				if(empty($context['right_to_left']))
				{
					$form = '%s: %s%s %s%s %s%s';
					echo sprintf($form, $txt['pmx_loggedintime'], $totm['days'], $txt['pmx_Ldays'], $totm['hours'], $txt['pmx_Lhours'], $totm['minutes'], $txt['pmx_Lminutes']);
				}
				else
				{
					$form ='<br />%s%s %s%s %s%s :%s';
					echo sprintf($form, $txt['pmx_Ldays'], $totm['days'], $txt['pmx_Lhours'], $totm['hours'], $txt['pmx_Lminutes'], $totm['minutes'], $txt['pmx_loggedintime']);
				}
				echo '<br />';
			}
		}

		// Otherwise they're a guest, ask them to register or login.
		else
		{
			$L_R = empty($context['right_to_left']) ? 'left' : 'right';
			$R_L = empty($context['right_to_left']) ? 'right' : 'left';
			echo '
									<span' .(isset($this->cfg['config']['visuals']['hellotext']) ? ' class="'. $this->cfg['config']['visuals']['hellotext'] .'"' : ''). '>'. sprintf($txt['welcome_guest'], $txt['guest_title']) .'</span><br />';

			if(!empty($this->cfg['config']['settings']['show_login']))
			{
				echo '
									<div style="padding-top:4px;">
										<form action="'. $scripturl .'?action=login2" method="post">
											<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
											<input type="text" name="user" value="" style="width:42%;float:'. $L_R .';margin-bottom:3px;" />
											<input type="password" name="passwrd" value="" style="width:42%;float:'. $R_L .';margin-bottom:3px;margin-'. $R_L .':4px;" />';

				if(!empty($modSettings['enableOpenID']))
					echo '
											<input type="text" name="openid_identifier" value="" id="pmx'.$this->cfg['id'].'_openid_url" class="openid_login" style="width:84%;margin-bottom:3px;" />';

				echo '
											<select name="cookielength" style="width:45%;float:'. $L_R .';">
												<option value="60">'. $txt['one_hour'] .'</option>
												<option value="1440">'. $txt['one_day'] .'</option>
												<option value="10080">'. $txt['one_week'] .'</option>
												<option value="302400">'. $txt['one_month'] .'</option>
												<option value="-1" selected="selected">' . $txt['forever'] .'</option>
											</select>
											<input style="float:'.$R_L.';margin-'.$R_L.':4px;" type="submit" value="'. $txt['login'] .'" />
											<br style="clear:both;" />'. $txt['quick_login_dec'] .'
										</form>
									</div>';
			}
		}

		// show current time?
		if(!empty($this->cfg['config']['settings']['show_time']))
		{
			if(!empty($this->cfg['config']['settings']['show_realtime']))
			{
				$cdate = date('Y,n-1,j,G,', Forum_Time()) . intval(date('i', Forum_Time())) .','. intval(date('s', Forum_Time()));
				echo '
								<span id="ulClock"></span>
								<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
								var pmx_rctMonths = new Array("'. implode('","', $txt['months']) .'");
								var pmx_rctShortMonths = new Array("'. implode('","', $txt['months_short']) .'");
								var pmx_rctDays = new Array("'. implode('","', $txt['days']) .'");
								var pmx_rctShortDays = new Array("'. implode('","', $txt['days_short']) .'");
								var pmx_rtcFormatTypes = new Array("%a", "%A", "%d", "%b", "%B", "%m", "%Y", "%y", "%H", "%I", "%M", "%S", "%p", "%%", "%D", "%e", "%R", "%T");
								var pmx_rtcFormat = "'. (empty($user_info['time_format']) ? $modSettings['time_format'] : $user_info['time_format']) .'";
								var pmx_rtcOffset = new Date('. $cdate .') - new Date();

								// show the server time with users offset
								function ulClock()
								{
									var pmx_CTime = pmx_rtcFormat;
									var pmx_rtc = new Date();
									pmx_rtc.setTime(pmx_rtc.getTime() + pmx_rtcOffset);
									var pmx_rtcMt = "0" + pmx_rtc.getMonth();
									var pmx_rtcD = "0" + pmx_rtc.getDate();
									var pmx_rtcH = pmx_rtc.getHours();
									var pmx_rtcM = "0" + pmx_rtc.getMinutes();
									var pmx_rtcS = "0" + pmx_rtc.getSeconds();
									var pmx_rtcAM = "am";
									if(pmx_CTime.search(/%I/) != -1)
									{
										if(pmx_rtcH == 0)
											pmx_rtcH = pmx_rtcH + 12;
										else
										{
											if(pmx_rtcH >= 12)
											{
												pmx_rtcH = pmx_rtcH > 12 ? pmx_rtcH - 12 : pmx_rtcH;
												pmx_rtcAM = "pm";
											}
										}
									}
									pmx_rtcH = "0" + pmx_rtcH;
									var pmx_rtc_values = new Array(
										pmx_rctShortDays[pmx_rtc.getDay()],
										pmx_rctDays[pmx_rtc.getDay()],
										pmx_rtcD.toString().substr(pmx_rtcD.length - 2),
										pmx_rctShortMonths[pmx_rtc.getMonth()],
										pmx_rctMonths[pmx_rtc.getMonth()],
										pmx_rtcMt.substr(pmx_rtcMt.length - 2),
										pmx_rtc.getFullYear(),
										pmx_rtc.getFullYear().toString().substr(2, 2),
										pmx_rtcH.substr(pmx_rtcH.length - 2),
										pmx_rtcH.substr(pmx_rtcH.length - 2),
										pmx_rtcM.substr(pmx_rtcM.length - 2),
										pmx_rtcS.substr(pmx_rtcS.length - 2),
										pmx_rtcAM,
										"%",
										"",
										"",
										"",
										""
									);
									for(var i = 0; i < pmx_rtcFormatTypes.length; i++)
									{
										if(pmx_CTime.search(pmx_rtcFormatTypes[i]) != -1)
											pmx_CTime = pmx_CTime.replace(pmx_rtcFormatTypes[i], pmx_rtc_values[i]);
									}
									document.getElementById("ulClock").innerHTML = pmx_CTime;
									setTimeout("ulClock()",1000);
								}
								ulClock();
								// ]]></script>';
			}
			else
				echo $context['current_time'];
		}

		// show logout button?
		if($context['user']['is_logged'] && !empty($this->cfg['config']['settings']['show_logout']))
			echo '
								<br />
								<div style="text-align:center;margin-top:5px;">
									<input class="button_submit" type="button" value="'. $txt['logout'] .'" onclick="DoLogout()" />
								</div>
								<script type="text/javascript"><!-- // --><![CDATA[
									function DoLogout()
									{
										window.location = "'. $scripturl .'?action=logout;'. $context['session_var'] .'='. $context['session_id'] .'";
									}
								// ]]></script>';

		// show a language dropdown selector
		if(pmx_checkECL_Cookie() && !empty($this->cfg['config']['settings']['show_langsel']) && count($context['pmx']['languages']) > 1)
		{
			echo '
								<hr />'. $txt['pmx_langsel'] .'
								<form id="pmxlangchg'.$this->cfg['id'].'" action="" method="post">
									<div style="padding-top:3px;">
										<select name="" style="width:98%;" onchange="ChangeLang'.$this->cfg['id'].'(this);">';

			foreach($context['pmx']['languages'] as $lang => $sel)
				echo '
											<option value="'. $lang .'"' .(!empty($sel) ? ' selected="selected"' : '') .'>'. $lang .'</option>';

			echo '
										</select>
									</div>
								</form>
								<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
									function ChangeLang'.$this->cfg['id'].'(elm)
									{
										pmxWinGetTop(\'user'. $this->cfg['id'] .'\');
										document.getElementById("pmxlangchg'.$this->cfg['id'].'").action = smf_scripturl + "?language="+ elm.options[elm.selectedIndex].value +";pmxrd='. base64_encode(str_replace(array($scripturl, '?'), '', getCurrentUrl())) .'";
										document.getElementById("pmxlangchg'.$this->cfg['id'].'").submit();
									}
								// ]]></script>';
		}
	}
}
?>
