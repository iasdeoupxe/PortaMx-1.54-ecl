<?php
/**
* \file shoutbox.php
* Systemblock shoutbox
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_shoutbox
* Systemblock shoutbox
* @see shoutbox.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_shoutbox extends PortaMxC_SystemBlock
{
	var $smileys;			///< all smileys
	var $bb_code;			///< all bb codes
	var $bb_colors;		///< all bbc colors
	var $memdata;			///< shout memberdata
	var $shouts;			///< shout data
	var $legalcodes;	///< all legal bbc codes
	var $canShout;

	/**
	* checkCacheStatus.
	* If the cache enabled, the cache trigger will be checked.
	*/
	function pmxc_checkCacheStatus()
	{
		global $pmxCacheFunc;

		$result = true;
		if($this->cfg['cache'] > 0 && !empty($this->cache_trigger))
		{
			if(($data = $pmxCacheFunc['get']($this->cache_key, $this->cache_mode)) !== null)
			{
				$cachefunc = create_function('', $this->cache_trigger);
				$res = $cachefunc();
				if($res == 'clr')
					$pmxCacheFunc['clear']($this->cache_key, $this->cache_mode);

				unset($data);
				$result = ($res === null);
			}
		}
		return $result;
	}

	/**
	* InitContent.
	* Checked is a shout received ($_POST).
	*/
	function pmxc_InitContent()
	{
		global $scripturl, $context, $user_info, $user_profile, $smcFunc, $modSettings, $txt, $pmxCacheFunc;

		$this->pmxc_ShoutSetup();

		// shout send?
		if(isset($_POST['pmx_shout']) && !empty($_POST['pmx_shout']))
		{
			if(!empty($this->canShout))
			{
				$shoutcmd = PortaMx_makeSafe($_POST['pmx_shout']);
				$update = false;

				// get the shouts
				$shouts = unserialize($this->cfg['content']);
				$shouts = is_array($shouts) ? $shouts : array();

				// delete a shout?
				if($shoutcmd == 'delete')
				{
					$id = PortaMx_makeSafe($_POST['shoutid']);
					if(isset($shouts[$id]))
					{
						unset($shouts[$id]);
						if(!empty($shouts))
						{
							foreach($shouts as $data)
								$new[] = $data;
							$shouts = $new;
						}
						$this->cfg['content'] = pmx_serialize($shouts);
						unset($new);
						$update = true;
					}
				}

				// update a shout?
				if($shoutcmd == 'update')
				{
					$id = PortaMx_makeSafe($_POST['shoutid']);
					if(isset($shouts[$id]))
					{
						// clean the input stream
						$post = PortaMx_makeSafeContent(str_replace(array("\r", "\n"), array('', ' '), $_POST['post']));
						$post = $this->ShortenBBCpost($post, intval($this->cfg['config']['settings']['maxlen']));
						if($this->BBCtoHTML($post) != '')
						{
							// convert html to char
							$post = $this->HTMLtoChar($post);
							$shouts[$id]['post'] = $this->ChartoHTML($post, true);
							$this->cfg['content'] = pmx_serialize($shouts);
							$update = true;
							$_SESSION['PortaMx']['shout_edit'] = $id;
						}
					}
				}

				// save a new shout ?
				if($shoutcmd == 'save')
				{
					// clean the input stream
					$post = PortaMx_makeSafeContent(str_replace(array("\r", "\n"), array('', ' '), $_POST['post']));
					$post = $this->ShortenBBCpost($post, intval($this->cfg['config']['settings']['maxlen']));
					if($this->BBCtoHTML($post) != '')
					{
						// get the shouts
						$shout = array(
							'uid' => $user_info['id'],
							'ip' => $user_info['ip'],
							'time' => forum_time(false),
							'post' => $this->ChartoHTML($post, true),
						);

						if(!empty($this->cfg['config']['settings']['reverse']))
							array_push($shouts, $shout);
						else
							array_unshift($shouts, $shout);

						// max shouts reached?
						if(isset($this->cfg['config']['settings']['maxshouts']))
						{
							if(!empty($this->cfg['config']['settings']['reverse']))
							{
								while(count($shouts) > $this->cfg['config']['settings']['maxshouts'])
									array_shift($shouts);
							}
							else
								array_splice($shouts, $this->cfg['config']['settings']['maxshouts']);

							// resort
							foreach($shouts as $data)
								$new[] = $data;
							$shouts = $new;
							unset($new);
						}
						$this->cfg['content'] = pmx_serialize($shouts);
						$update = true;
					}
				}

				// need to save?
				if(!empty($update))
				{
					$smcFunc['db_query']('', '
							UPDATE {db_prefix}portamx_blocks
							SET content = {string:content}
							WHERE id = {int:id}',
						array(
							'id' => $this->cfg['id'],
							'content' => $this->cfg['content'],
						)
					);
				}

				// cleanup
				unset($shouts);

				if($this->cfg['cache'] > 0)
					$pmxCacheFunc['clear']($this->cache_key, $this->cache_mode);

				redirectexit(str_replace(array($scripturl, '?'), '', getCurrentUrl()));
			}
		}

		if($this->visible)
		{
			// get the shouts
			$this->shouts = unserialize($this->cfg['content']);
			$this->shouts = is_array($this->shouts) ? $this->shouts : array();

			// just reorder shouts ?
			if(!empty($this->cfg['config']['settings']['reorder']))
			{
				$this->shouts = array_reverse($this->shouts);
				$this->cfg['content'] = pmx_serialize($this->shouts);
				unset($this->cfg['config']['settings']['reorder']);
				$cfg = pmx_serialize($this->cfg['config']);

				// save new order
				$smcFunc['db_query']('', '
						UPDATE {db_prefix}portamx_blocks
						SET content = {string:content},
								config = {string:config}
						WHERE id = {int:id}',
					array(
						'id' => $this->cfg['id'],
						'content' => $this->cfg['content'],
						'config' => $cfg,
					)
				);
			}

			// get member data
			if($this->cfg['cache'] > 0)
			{
				if(($this->memdata = $pmxCacheFunc['get']($this->cache_key, $this->cache_mode)) === null)
				{
					$this->get_memberdata();
					$pmxCacheFunc['put']($this->cache_key, $this->memdata, $this->cache_time, $this->cache_mode);
				}
			}
			else
				$this->get_memberdata();
		}

		// return the visibility flag (true/false)
		return $this->visible;
	}

	/**
	* Get the members name and onlinecolor
	*/
	function get_memberdata()
	{
		global $smcFunc, $modSettings, $txt;

		$this->memdata = array();
		if(!empty($this->shouts))
		{
			// get all member id's
			foreach($this->shouts as $data)
				$members[] = intval($data['uid']);

			// get member name and online color
			$request = $smcFunc['db_query']('', '
				SELECT mem.id_member, CASE WHEN mem.real_name = {string:empty} THEN mem.member_name ELSE mem.real_name END AS name, mg.online_color AS color
				FROM {db_prefix}members AS mem
				LEFT JOIN {db_prefix}membergroups AS mg ON ('. (!empty($modSettings['permission_enable_postgroups']) ? '(mg.id_group = 0 AND mg.id_group = mem.id_post_group OR mg.id_group > 0 AND mg.id_group = mem.id_group)' : 'mg.id_group = mem.id_group') .' OR FIND_IN_SET(mg.id_group, mem.additional_groups) != 0)
				WHERE mem.id_member IN ({array_int:members})
				GROUP BY mem.id_member',
				array(
					'members' => array_unique($members),
					'empty' => '',
				)
			);

			// save member data
			while($row = $smcFunc['db_fetch_assoc']($request))
				$this->memdata[$row['id_member']] = array('name' => $row['name'], 'color' => $row['color']);
			$smcFunc['db_free_result']($request);

			// add Guest shout
			$this->memdata[0] = array('name' => $txt['guest_title'], 'color' => '');
		}
	}

	/**
	* Shout setup variables
	*/
	function pmxc_ShoutSetup()
	{
		global $context, $modSettings, $txt;

		// setup bb codes
		$this->bb_code = array(
			array(
				'code' => 'b',
				'image' => $context['pmx_imageurl'] . 'shoutbbc_b.gif',
				'title' => $txt['pmx_shoutbbc_b'],
				),
			array(
				'code' => 'i',
				'image' => $context['pmx_imageurl'] . 'shoutbbc_i.gif',
				'title' => $txt['pmx_shoutbbc_i'],
				),
			array(
				'code' => 'u',
				'image' => $context['pmx_imageurl'] . 'shoutbbc_u.gif',
				'title' => $txt['pmx_shoutbbc_u'],
				),
			array(
				'code' => 'strike',
				'image' => $context['pmx_imageurl'] . 'shoutbbc_s.gif',
				'title' => $txt['pmx_shoutbbc_s'],
				),
			array(
				'code' => 'marquee',
				'image' => $context['pmx_imageurl'] . 'shoutbbc_m.gif',
				'title' => $txt['pmx_shoutbbc_m'],
				),
			array(
				'code' => 'sub',
				'image' => $context['pmx_imageurl'] . 'shoutbbc_sub.gif',
				'title' => $txt['pmx_shoutbbc_sub'],
				),
			array(
				'code' => 'sup',
				'image' => $context['pmx_imageurl'] . 'shoutbbc_sup.gif',
				'title' => $txt['pmx_shoutbbc_sup'],
				),
		);

		foreach($this->bb_code as $data)
			$this->legalcodes[] = $data['code'];

		// setup bbc colors codes
		$this->bb_colors = array(
			$txt['pmx_shoutbbc_changecolor'] => $txt['pmx_shoutbbc_changecolor'],
			$txt['pmx_shoutbbc_colorBlack'] => '#000000',
			$txt['pmx_shoutbbc_colorRed'] => '#ff0000',
			$txt['pmx_shoutbbc_colorYellow'] => '#ffff00',
			$txt['pmx_shoutbbc_colorPink'] => '#ff00ff',
			$txt['pmx_shoutbbc_colorGreen'] => '#008000',
			$txt['pmx_shoutbbc_colorOrange'] => '#FFA500',
			$txt['pmx_shoutbbc_colorPurple'] => '#800080',
			$txt['pmx_shoutbbc_colorBlue'] => '#0000ff',
			$txt['pmx_shoutbbc_colorBeige'] => '#F5F5DC',
			$txt['pmx_shoutbbc_colorBrown'] => '#A52A2A',
			$txt['pmx_shoutbbc_colorTeal'] => '#008080',
			$txt['pmx_shoutbbc_colorNavy'] => '#000080',
			$txt['pmx_shoutbbc_colorMaroon'] => '#800000',
			$txt['pmx_shoutbbc_colorLimeGreen'] => '#00ff00',
			$txt['pmx_shoutbbc_colorWhite'] => '#ffffff',
		);

		// setup the PortaMx smileys
		$codes = array(':)', ';)', ':D', ';D', '>:(', ':(', ':o', '8)', '???', '::)', ':P', ':-[', ':-X', ':-\\\\', ':-*', ':\\\'(', '>:D', '^-^', ':))', 'O0');
		$files = array('smiley', 'wink', 'cheesy', 'grin', 'angry', 'sad', 'shocked', 'cool', 'huh', 'rolleyes', 'tongue', 'embarrassed', 'lipsrsealed', 'undecided', 'kiss', 'cry', 'evil', 'azn', 'laugh', 'afro');
		$smPath = $modSettings['smileys_url'] . '/PortaMx/';
		foreach($codes as $i => $code)
			$this->smileys[] = array(
				'code' => $code,
				'image' => $smPath. $files[$i] .'.gif',
				'title' => ucfirst($files[$i]),
			);

		// check if member can shout
		if(isset($this->cfg['config']['settings']['shout_acs']))
			$this->canShout = allowPmxGroup(implode(',', $this->cfg['config']['settings']['shout_acs']));
		else
			$this->canShout = false;

		// disable shout if a user banned
		foreach(array('cannot_access', 'cannot_login', 'cannot_post') as $bannmode)
			$this->canShout = (isset($_SESSION['ban'][$bannmode]) ? false : $this->canShout);
	}

	/**
	* ShowContent
	*/
	function pmxc_ShowContent()
	{
		global $context, $scripturl, $user_info, $modSettings, $smcFunc, $txt;

		$context['pmx']['shout_edit'] = -1;

		// smiley && bb codes popup
		if(!empty($this->cfg['customclass']))
			$isCustFrame = !empty($this->cfg['customclass']['frame']);
		else
			$isCustFrame = false;
		$innerPad = Pmx_getInnerPad($this->cfg['config']['innerpad']);
		$bodyclass = ($this->cfg['config']['visuals']['body'] == 'windowbg' ? 'windowbg2 ' : 'windowbg ');
		$spanclass = $isCustFrame ? $bodyclass .' ' : '';

		echo '
			<div id="bbcodes" style="position:absolute; z-index:9999; width:342px; height:110px; display:none">
				<div class="'. $bodyclass .'" style="padding:0;margin-top:10px;">';

		if(empty($context['pmx_style_isCore']))
			echo '
					<div class="'. $bodyclass .' plainbox blockcontent" style="margin:auto; text-align:center; padding:5px 0 !important;">';
		else
			echo '
					<div class="'. $bodyclass .'shoutbox_core '. $this->cfg['config']['visuals']['frame'] .'" style="margin:auto; text-align:center;">';

		echo '
						<div style="height:25px;">';

		$half = 10;
		foreach($this->smileys as $sm)
		{
			echo '
						<img onclick="InsertSmiley(\''. addslashes($sm['code']) .'\')" src="'. $sm['image'] .'" vspace="3" hspace="7" alt="*" title="'. $sm['title'] .'" align="left" style="cursor:pointer;" />';
			$half--;
			if($half == 0)
				echo '
						</div>
						<div style="height:25px;">';
		}
		echo '
						</div>
						<hr />
						<div style="height:28px;">';

		foreach($this->bb_code as $sm)
			echo '
							<img onclick="InsertBBCode(\''. $sm['code'] .'\'); return false;" src="'. $sm['image'] .'" hspace="4" vspace="4" alt="*" title="'. $sm['title'] .'" align="left" style="cursor:pointer; background-color:#a0a8b0; border-width:2px; border-style:solid; border-color:#e0e0e0 #808890 #808890 #e0e0e0;" />';

		echo '
							<select id="shout_color" size="1" name="" onchange="InsertBBColor(this); return false;" style="float:right; margin:6px 8px 0 8px; width:100px;">';

		foreach($this->bb_colors as $coltxt => $colname)
			echo '
								<option value="'. $colname .'"'. ($colname == $coltxt ? ' selected="selected"' : '') .'>'. $coltxt .'</option>';

		echo '
							</select>
						</div>
					</div>
				</div>
			</div>';

		echo '
			<div id="shoutframe" style="'. (isset($this->cfg['config']['settings']['maxheight']) ? 'max-height:'. $this->cfg['config']['settings']['maxheight'] .'px; overflow:auto; ' : '') .'padding:0px '. Pmx_getInnerPad($this->cfg['config']['innerpad'], 1) .'px;">';

		$haveshouts = false;
		$allowAdmin = allowPmx('pmx_admin');

		foreach($this->shouts as $id => $data)
		{
			echo '
				<div id="shoutitem'. $id .'">
					<div class="tborder shoutbox_user">';

			// show the edit/delete images
			if($allowAdmin || ($user_info['id'] == $data['uid'] && !empty($this->cfg['config']['settings']['allowedit']) && $this->canShout))
			{
				$haveshouts = $allowAdmin || $user_info['id'] == $data['uid'] ? true : $haveshouts;
				echo '
						<img name="shoutimg" onclick="DeleteShout('. $id .');" style="cursor:pointer; margin-top:2px; display:none;" src="'. $context['pmx_imageurl'] .'shout_del.gif" align="'. (empty($context['right_to_left']) ? 'right' : 'left') .'" alt="*" title="'. $txt['pmx_shoutbox_shoutdelete'] .'" />
						<img name="shoutimg" onclick="EditShout('. $id .', \''. addslashes($data['post']) .'\');" style="cursor:pointer; margin-top:2px; padding-right:4px; display:none;" src="'. $context['pmx_imageurl'] .'shout_edit.gif" align="'. (empty($context['right_to_left']) ? 'right' : 'left') .'" alt="*" title="'. $txt['pmx_shoutbox_shoutedit'] .'" />';
			}

			// show the ip image
			if($allowAdmin && !empty($data['ip']))
				echo '
						<a href="'. $scripturl .'?action=trackip;searchip='. $data['ip'] .'"><img name="shoutimg" align="'. (empty($context['right_to_left']) ? 'right' : 'left') .'" src="'. $context['pmx_imageurl'] .'ip.gif" style="padding:1px 3px 0 0;" title="'. $data['ip'] .'" alt="*" /></a>';

			// convert smileys and bb codes
			$data['post'] = $this->BBCtoHTML($data['post'], true);

			// Guest shout?
			if($data['uid'] != 0 && isset($this->memdata[$data['uid']]))
				echo '
						<a href="'. $scripturl .'?action=profile;u='. $data['uid'] .'"><span'. (isset($this->memdata[$data['uid']]['color']) && !empty($this->memdata[$data['uid']]['color']) ? ' style="color:'. $this->memdata[$data['uid']]['color'] .';"' : '') .'>'. $this->memdata[$data['uid']]['name'] .'</span></a>';
			else
			{
				$data['uid'] = 0;
				echo '
									'. $this->memdata[$data['uid']]['name'];
			}
			echo '
					</div>
					<div style="padding:0px 1px 10px 1px;">
						'. timeformat($data['time']) .'<br />
						'. $data['post'] .'
					</div>
				</div>';
		}

		echo '
			</div>';

		// have shout access?
		if($this->canShout)
		{
			$cact = str_replace($scripturl, '', getCurrentUrl());
			$canEdit = !$user_info['is_guest'] && (($allowAdmin && $haveshouts) || ($haveshouts && !empty($this->cfg['config']['settings']['allowedit'])));
			$Admimg[0] = $context['pmx_imageurl'] . ($canEdit ? 'shout_admon.gif' : 'empty.gif');
			$Admimg[1] = $context['pmx_imageurl'] . ($canEdit ? 'shout_admoff.gif' : 'empty.gif');
			echo '
			<div style="overflow:hidden;">
				<form id="pmx_shoutform" action="'. $scripturl . $cact .'" method="post" style="padding-top:3px; margin:0 auto; text-align:center;">
					<input type="hidden" name="shoutbox_action" value="shout" />
					<input type="hidden" name="sc" value="'. $context['session_id'] .'" />
					<input type="hidden" id="shout" name="pmx_shout" value="" />
					<input type="hidden" id="shoutid" name="shoutid" value="" />
					<textarea id="shoutcontent"'. (!empty($this->cfg['config']['settings']['boxcollapse']) ? ' style="display:none;"' : '') .' name="post" cols="15" rows="4"></textarea>
					<img id="shoutbbon" style="cursor:pointer; margin-top:4px;'. (!empty($this->cfg['config']['settings']['boxcollapse']) ? 'display:none;' : '') .'" onclick="ShoutPopup();" src="'. $context['pmx_imageurl'] . 'type_bbc.gif" align="left" alt="*" title="'. $txt['pmx_shoutbox_bbc_code'] .'" />';

			if(!empty($this->cfg['config']['settings']['boxcollapse']))
				echo '
					<img id="shoutbboff" style="margin-top:4px;" src="'. $context['pmx_imageurl'] . 'empty.gif" align="left" alt="*" title="" />';

			echo '
					<img id="shout_toggle" style="'. ($canEdit ? 'cursor:pointer;' : '') .'margin-top:4px;"'. ($canEdit ? ' onclick="ShoutAdmin(\'check\');"' : '') .' src="'. $Admimg[0] .'" align="right" alt="*" title="'. $txt['pmx_shoutbox_toggle'] .'" />
					<input id="shout_key" onclick="SendShout()" class="button_submit" type="button" name="button" value="'. (!empty($this->cfg['config']['settings']['boxcollapse']) ? $txt['pmx_shoutbox_button_open'] : $txt['pmx_shoutbox_button']) .'" title="'. (!empty($this->cfg['config']['settings']['boxcollapse']) ? $txt['pmx_shoutbox_button_title'] : $txt['pmx_shoutbox_send_title']) .'" style="margin-top:3px; width:100px;" />
				</form>
			</div>';

			if(isset($_SESSION['PortaMx']['shout_edit']))
			{
				$context['pmx']['shout_edit'] = $_SESSION['PortaMx']['shout_edit'];
				unset($_SESSION['PortaMx']['shout_edit']);
			}
		}

		echo '
			<script type="text/javascript"><!-- // --><![CDATA[
				var MaxLoops = 50;
				var SB_StartPosition = 0;
				var SB_EndPosition = 0;
				var SB_Step = 100;

				// Shoutbox Scolling start checks if all smileys loaded
				function SB_ScollStart()
				{
					var SB_isLoad = 0;
					for(var i = 0; i < document.getElementsByTagName("img").length; i++)
					{
						var SB_smiley = document.getElementsByTagName("img")[i].src;
						if(SB_smiley.search(/'. substr(strrchr($modSettings['smileys_dir'], '/'), 1) .'\/PortaMx/) != -1)
						{
							if(!document.getElementsByTagName("img")[i].complete)
								SB_isLoad++;
						}
					}

					if(SB_isLoad != 0)
					{
						MaxLoops--;
						if(MaxLoops <= 0)
							window.clearInterval(SB_ScrollTimer);
					}
					else
					{
						window.clearInterval(SB_ScrollTimer);';

		if($context['pmx']['shout_edit'] >= 0)
			echo '
						var SB_Id = '. $context['pmx']['shout_edit'] .';
						var SB_y = 0;
						SB_Id++;
						while(document.getElementById("shoutitem"+SB_Id))
						{
							SB_y += document.getElementById("shoutitem"+SB_Id).offsetHeight;
							SB_Id++;
						}
						SB_EndPosition = (document.getElementById("shoutframe").scrollHeight - SB_y) - document.getElementById("shoutframe").offsetHeight;';

		elseif(!empty($this->cfg['config']['settings']['reverse']))
			echo '
						SB_EndPosition = document.getElementById("shoutframe").scrollHeight - document.getElementById("shoutframe").offsetHeight;';

		if(!empty($this->cfg['config']['settings']['scrollspeed']) && $context['pmx']['shout_edit'] < 0)
			echo '
						SB_Step = SB_EndPosition / (7.5 * '. $this->cfg['config']['settings']['scrollspeed'] .');';
		else
			echo '
						SB_Step = SB_EndPosition;';

		if($context['pmx']['shout_edit'] >= 0 || !empty($this->cfg['config']['settings']['reverse']))
			echo '
						SB_ScrollTimer = window.setInterval("SB_Srcoll_To()", 10);';

		echo '
					}
				}

				// Scroll the Shoutbox to endposition
				function SB_Srcoll_To()
				{
					if(SB_StartPosition < SB_EndPosition)
					{
						document.getElementById("shoutframe").scrollTop = SB_StartPosition;
						SB_StartPosition += SB_Step;
					}
					else
					{
						window.clearInterval(SB_ScrollTimer);
						document.getElementById("shoutframe").scrollTop = SB_EndPosition + '. ($context['pmx']['shout_edit'] < 0 ? 'document.getElementById("shoutframe").offsetHeight' : '0') .';
					}
				}';

		if($this->canShout)
		{
			$inistate = pmx_getcookie('shout'. $this->cfg['id']);
			echo '
				var initstate = ("'. (is_null($inistate) ? 'none' : $inistate) .'");
				// insert smiley code
				function InsertSmiley(smcode)
				{
					ShoutInsert(" " + smcode, "");
				}

				// insert BB code
				function InsertBBCode(smcode)
				{
					var aTag = "["+smcode+"]";
					var eTag = "[/"+smcode+"]";
					ShoutInsert(aTag, eTag);
				}

				// insert bb color code
				function InsertBBColor(elm)
				{
					var idx = elm.selectedIndex;
					if(idx != 0)
					{
						var color = elm.options[idx].value;
						var aTag = "[c="+color+"]";
						var eTag = "[/c]";
						ShoutInsert(aTag, eTag);
						elm.selectedIndex = 0;
					}
				}

				// insert a bbc element
				function ShoutInsert(aTag, eTag)
				{
					document.getElementById("shoutcontent").focus();
					var input = document.getElementById("shoutcontent");
					// IE
					if(typeof document.selection != "undefined")
					{
						var range = document.selection.createRange();
						var insText = range.text;
						range.text = aTag + insText + eTag;
						// adjust cursor
						range = document.selection.createRange();
						if (insText.length == 0)
							range.move("character", -eTag.length);
						else
							range.moveStart("character", aTag.length + insText.length + eTag.length);
						range.select();
					}
					// Gecko
					else if(typeof input.selectionStart != "undefined")
					{
						var start = input.selectionStart;
						var end = input.selectionEnd;
						var insText = input.value.substring(start, end);
						input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
						// adjust cursor
						var pos;
						if (insText.length == 0)
							pos = start + aTag.length;
						else
							pos = start + aTag.length + insText.length + eTag.length;
						input.selectionStart = pos;
						input.selectionEnd = pos;
					}
				}

				// popup for bb code and smileys
				function ShoutPopup()
				{
					var element = document.getElementById("bbcodes");
					if(element.style.display == "none")
					{';

			if(!empty($this->cfg['config']['settings']['boxcollapse']))
				echo '
						if(document.getElementById("shoutcontent").style.display == "none")
							return;';

			echo '
						element.style.display = "";
						var pos = getShoutPosition("shoutcontent");
						element.style.top = (pos.y - 10 - element.offsetHeight) + "px";';

			if($this->cfg['side'] == 'right')
				echo '
						var delta = element.offsetWidth - document.getElementById("shoutcontent").offsetWidth;
						element.style.left = (pos.x - 20 - delta) + "px";';
			else
				echo '
						element.style.left = (pos.x + 10) + "px";';

			echo '
					}
					else
						element.style.display = "none";

					document.getElementById("shoutcontent").focus();
				}

				// get a pixel value from styles
				function getPixVal(value)
				{
					var find = /(\d+)/;
					find.exec(value);
					return parseInt(RegExp.$1);
				}

				// get element position (x, y)
				function getShoutPosition(elmID)
				{
					var tagname = "";
					var x = 0, y = 0;
					var elem = document.getElementById(elmID);

					while((typeof(elem) == "object")&&(typeof(elem.tagName) != "undefined"))
					{
						y += elem.offsetTop;
						x += elem.offsetLeft;
						tagname = elem.tagName.toUpperCase();
						if(tagname == "BODY")
							elem = 0;

						if(typeof(elem) == "object")
						{
							if(typeof(elem.offsetParent) == "object")
								elem = elem.offsetParent;
							else
								elem = 0;
						}
					}
					var position = new Object();
					position.x = x;
					position.y = y;
					return position;
				}

				// delete a shout
				function SubmitAnyShout()
				{
					pmxWinGetTop(\'shout'. $this->cfg['id'] .'\');
					document.getElementById("pmx_shoutform").submit();
				}

				// delete a shout
				function DeleteShout(Id)
				{
					if(confirm("'. $txt['pmx_shoutbox_shoutconfirm'] .'") == true)
					{
						document.getElementById("shout").value = "delete";
						document.getElementById("shoutid").value = Id;
						SubmitAnyShout();
					}
				}

				// edit a shout
				function EditShout(Id, post)
				{
					document.getElementById("shoutcontent").style.display = "";
					document.getElementById("shout").value = "update";
					document.getElementById("shoutid").value = Id;
					document.getElementById("shoutcontent").value = post;
					document.getElementById("shoutcontent").focus();
					document.getElementById("shoutbbon").style.display = "";
					document.getElementById("shoutbboff").style.display = "none";
				}

				// send (submit) a shout
				function SendShout()
				{
					pmxWinGetTop(\'shout'. $this->cfg['id'] .'\');';

			if(!empty($this->cfg['config']['settings']['boxcollapse']))
				echo '
					if(document.getElementById("shoutcontent").style.display == "none")
					{
						document.getElementById("shout_key").title = "'. $txt['pmx_shoutbox_send_title'] .'";
						document.getElementById("shout_key").value = "'. $txt['pmx_shoutbox_button'] .'";
						document.getElementById("shoutcontent").style.display = "";
						document.getElementById("shoutbbon").style.display = "";
						document.getElementById("shoutbboff").style.display = "none";
						document.getElementById("shoutcontent").focus();
					}
					else
					{
						document.getElementById("shout_key").title = "'. $txt['pmx_shoutbox_button_title'] .'";
						document.getElementById("shout_key").value = "'. $txt['pmx_shoutbox_button_open'] .'";
						var cont = document.getElementById("shoutcontent").value;
						if(cont.match(/\S/g))
						{
							if(document.getElementById("shout").value == "")
								document.getElementById("shout").value = "save";
							SubmitAnyShout();
						}
						else
						{
							document.getElementById("shoutcontent").value = "";
							document.getElementById("shoutcontent").style.display = "none";
							if(document.getElementById("bbcodes").style.display == "")
								ShoutPopup();
							document.getElementById("shoutbbon").style.display = "none";
							document.getElementById("shoutbboff").style.display = "";
						}
					}';
			else
				echo '
					var cont = document.getElementById("shoutcontent").value;
					if(cont.match(/\S/g))
					{
						if(document.getElementById("shout").value == "")
							document.getElementById("shout").value = "save";
						SubmitAnyShout();
					}
					else
						document.getElementById("shoutcontent").value = "";';

			echo '
				}

				// toggle the edit mode
				function ShoutAdmin(state)
				{
					if(state == "check")
					{
						state = "none";
						if(document.getElementsByName("shoutimg")[0])
							state = document.getElementsByName("shoutimg")[0].style.display == "none" ? "" :"none";
					}

					if(document.getElementsByName("shoutimg")[0])
					{
						for(var i = 0; i < document.getElementsByName("shoutimg").length; i++)
							document.getElementsByName("shoutimg")[i].style.display = state;
					}
					else
						state = "none";

					if(state == "none")
					{
						document.getElementById("shout_toggle").src = "'. $Admimg[0] .'";
						document.getElementById("shoutcontent").value = "";
					}
					else
						document.getElementById("shout_toggle").src = "'. $Admimg[1] .'";

					if(initstate != state)
						pmx_setCookie("shout'. $this->cfg['id'] .'", state);
				}
				ShoutAdmin(initstate);';
		}
		echo '
				var SB_ScrollTimer = window.setInterval("SB_ScollStart()", 50);
			// ]]></script>';
	}

	// decode html chars
	function HTMLtoChar($value)
	{
		$value = str_replace(array('&#039;', '&quot;', '&lt;', '&gt;'), array("'", '\"', '<', '>'), $value);
		return  str_replace('&amp;', '&', $value);
	}

	/**
	* encode html chars
	*/
	function ChartoHTML($value, $strip_spaces = false)
	{
		global $smcFunc;

		if($strip_spaces)
		{
			$value = trim($value);
			$i = 0;
			while($i < $smcFunc['strlen']($value))
			{
				if($value{$i} == ' ')
				{
					$l = 1;
					while($i + $l < $smcFunc['strlen']($value) && $value{$i + $l} == ' ')
						$l++;
					if($l > 1)
						$value = $smcFunc['substr']($value, 0, $i) . $smcFunc['substr']($value, ($i + $l) -1);
				}
				$i++;
			}
		}
		return htmlspecialchars($value);
	}

	/**
	* Get the color and inner content from color tag
	*/
	function getBBC_Color($value, &$col)
	{
		global $smcFunc;

		$col = '';
		$i = $smcFunc['strpos']($value, ']');
		if($i !== false)
		{
			$col = trim($smcFunc['substr']($value, 0, $i));
			$value = $smcFunc['substr']($value, $i +1);
		}
		return $value;
	}

	/**
	* Shorten a shout entry
	*/
	function ShortenBBCpost($value, $maxlen)
	{
		global $smcFunc;

		// Remove illegal bbc codes
		if(preg_match_all('~\[(.*?)(\]|\=)~i', $value, $matches, PREG_PATTERN_ORDER) > 0)
		{
			foreach($matches[1] as $id => $tag)
				$matches[1][$id] = trim(strtolower($tag));

			foreach($matches[1] as $id => $tag)
			{
				if($tag{0} != '/')
				{
					$cid = array_search('/'. $tag, $matches[1]);
					if($cid > $id)
					{
						if(!in_array($tag, $this->legalcodes) && !($tag{0} == 'c' && trim($matches[2][$id]) == '='))
						{
							$value = str_replace($matches[0][$id], '', $value);
							$value = str_replace($matches[0][$cid], '', $value);
						}
					}
				}
			}
		}

		$tmp = $value;
		// remove all bbc tags to get a clean stream
		while(preg_match_all('~\[(.*)(\]|\=)(.*)\[\/\\1\]~U', $tmp, $matches, PREG_PATTERN_ORDER) > 0)
		{
			if(!empty($matches[0]) && count($matches) == 4)
			{
				foreach($matches[0] as $id => $repl)
				{
					if($matches[2][$id] == ']')
						$tmp = str_replace($repl, $matches[3][$id], $tmp);
					else
					{
						$coltxt = $this->getBBC_Color($matches[3][$id], $col);
						$tmp = str_replace($repl, $coltxt, $tmp);
					}
				}
			}
		}

		// now check the length and shorten if longer then max
		if($smcFunc['strlen']($tmp) > $maxlen)
		{
			while($smcFunc['strlen']($tmp) > $maxlen)
			{
				$tmp = trim($tmp);
				while($smcFunc['strlen']($tmp) > $maxlen && $value{$smcFunc['strlen']($value)-1} != ']')
				{
					$tmp = $smcFunc['substr']($tmp, 0, -1);
					$value = $smcFunc['substr']($value, 0, -1);
				}

				if($smcFunc['strlen']($tmp) > $maxlen)
				{
					if(preg_match_all('~\[(.*)(\]|\=)(.*)\[\/\\1\]~U', $value, $matches, PREG_PATTERN_ORDER))
					{
						if(!empty($matches[0]) && count($matches) == 4)
						{
							$id = count($matches[1]) -1;
							if(preg_match_all('~\[(.*)(\]|\=)(.*)\[\/\\1\]~U', $matches[3][$id], $match, PREG_PATTERN_ORDER))
							{
								$id2 = count($match[1]) -1;
								$value = str_replace($match[0][$id2], '', $value);
								if($match[2][$id] == '=')
								{
									$coltxt = $this->getBBC_Color($match[3][$id], $col);
									$tmp = $smcFunc['substr']($tmp, 0, -$smcFunc['strlen']($coltxt));
								}
								else
									$tmp = $smcFunc['substr']($tmp, 0, -$smcFunc['strlen']($match[3][$id2]));
							}
							else
							{
								$value = str_replace($matches[0][$id], '', $value);
								if($matches[2][$id] == '=')
								{
									$coltxt = $this->getBBC_Color($matches[3][$id], $col);
									$tmp = $smcFunc['substr']($tmp, 0, -$smcFunc['strlen']($coltxt));
								}
							}
						}
					}
				}
			}
		}
		return $value;
	}

	/**
	* Convert BB codes to html
	*/
	function BBCtoHTML($value, $addSmiley = false)
	{
		while(preg_match_all('~\[(.*)(\]|\=)(.*)\[\/\\1\]~U', $value, $matches, PREG_PATTERN_ORDER) > 0)
		{
			if(!empty($matches[0]) && count($matches) == 4)
			{
				if(preg_match_all('~\[(.*)(\]|\=)(.*)\[\/\\1\]~U', $matches[3][0], $match, PREG_PATTERN_ORDER) > 0)
				{
					$tmp = $this->BBCtoHTML($matches[3][0]);
					$value = str_replace($matches[3][0], $tmp, $value);
				}
				else
				{
					foreach($matches[0] as $id => $repl)
					{
						if($matches[2][$id] == '=')
						{
							$coltxt = $this->getBBC_Color($matches[3][$id], $col);
							if($col != '')
								$value = str_replace($repl, '<span style="color:'. $col .';">'. $coltxt .'</span>', $value);
							else
								$value = str_replace($repl, '', $value);
						}
						else
						{
							if(trim($matches[3][$id]) != '')
							{
								if(in_array(strtolower($matches[1][$id]), $this->legalcodes))
									$value = str_replace($repl, '<'. $matches[1][$id] .'>'. $matches[3][$id] .'</'. $matches[1][$id] .'>', $value);
								else
									$value = str_replace($repl, $matches[3][$id], $value);
							}
							else
								$value = str_replace($repl, '', $value);
						}
	        }
				}
			}
		}
		if($addSmiley)
			$value = $this->convertSmileys($value);
		return $value;
	}

	/**
	* Convert the Smileys
	*/
	function convertSmileys($value)
	{
		global $smReplace, $smImage;

		foreach($this->smileys as $data)
		{
			$code = $this->ChartoHTML($data['code']);
			$smImage[$code] = '<img src="'. $data['image'] .'" title="'. $data['title'] .'" alt="*" />';
			$smReplace[$code] = preg_quote($code, '>');
		}
		$smPregSearch = '/('. implode('|', $smReplace) . ')(\s|$)+/';
		return preg_replace_callback($smPregSearch, create_function('$matches', 'global $smReplace, $smImage; return isset($smReplace[$matches[1]]) ? $smImage[$matches[1]] . $matches[2] : "";'), $value);
	}
}
?>
