<?php
/**
* \file download.php
* Systemblock Downlaod
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

if(!defined('PortaMx'))
	die('This file can\'t be run without PortaMx');

/**
* @class pmxc_download
* Systemblock Download
* @see download.php
* \author Copyright by PortaMx - http://portamx.com
*/
class pmxc_download extends PortaMxC_SystemBlock
{
	var $download_content;

	/**
	* InitContent.
	* Checks the autocache and create the content if necessary.
	*/
	function pmxc_InitContent()
	{
		global $smcFunc, $context, $user_info, $scripturl, $txt;

		if($this->visible)
		{
			$L_R = empty($context['right_to_left']) ? 'left' : 'right';
			$R_L = empty($context['right_to_left']) ? 'right' : 'left';

			$this->download_content = parse_bbc($this->cfg['content']);

			if(isset($this->cfg['config']['settings']['download_board']) && !empty($this->cfg['config']['settings']['download_board']))
			{
				// get downloads for board
				$request = $smcFunc['db_query']('', '
						SELECT a.id_attach, a.size, a.downloads, t.id_topic, t.locked, m.subject, m.body
						FROM {db_prefix}attachments a
						LEFT JOIN {db_prefix}messages m ON (a.id_msg = m.id_msg)
						LEFT JOIN {db_prefix}topics t ON (m.id_topic = t.id_topic)
						WHERE m.id_board = {int:board} AND a.mime_type NOT LIKE {string:likestr} AND t.locked = 0',
					array(
						'board' => $this->cfg['config']['settings']['download_board'],
						'likestr' => 'IMAGE%'
					)
				);

				$dlacs = implode('=1,', $this->cfg['config']['settings']['download_acs']);
				$entrys = array();
				$idx = 1;
				while($row = $smcFunc['db_fetch_assoc']($request))
				{
					$ofstr = trim(substr($row['subject'], 0, strpos($row['subject'], ' ')));
					$ofs = intval($ofstr);
					$idx = !empty($ofs) && $ofs !== $idx ? $ofs : $idx++;
					if(!empty($entrys[$idx]))
					{
						end($entrys);
						$ixd = key($entrys);
						$idx++;
					}
					$subj = !empty($ofs) ? trim(substr($row['subject'], strlen($ofstr))) : $row['subject'];
					$entrys[$idx] = '
						<div style="text-align:'. $L_R .';">';

					if(allowPmxGroup($dlacs))
						$entrys[$idx] .= '
							<a href="'. $scripturl .'?action=dlattach;id='. $row['id_attach'] .';fld='. $this->cfg['id'] .'">
								<img style="vertical-align:middle;" src="'. $context['pmx_imageurl'] .'download.png" alt="*" title="'. $subj .'" />
							</a>';

					if($user_info['is_admin'])
						$entrys[$idx] .= '
						<a href="'. $scripturl .'?topic='. $row['id_topic'] .'">
							<strong>'. $subj .'</strong>
						</a>';
					else
						$entrys[$idx] .= '
							<strong>'. $subj .'</strong>';

					$entrys[$idx] .= '
						<div class="dlcomment">'. parse_bbc(trim($row['body'])) .'</div>
						<b>['. round($row['size'] / 1000, 3) .'</b> '. $txt['pmx_kb_downloads'] .'<b>'. $row['downloads'] .'</b>]
					</div>' . ($entrys > 1 ? '<hr />' : '');
				}
				$smcFunc['db_free_result']($request);

				if(empty($idx))
					$this->download_content .= '<br />'. $txt['pmx_download_empty'];
				else
				{
					ksort($entrys);
					foreach($entrys as $cont)
						$this->download_content .= $cont;

					unset($entrys);
					unset($cont);
				}
			}
			else
				$this->download_content .= '<br />'. $txt['pmx_download_empty'];
		}
		// return the visibility flag (true/false)
		return $this->visible;
	}

	/**
	* ShowContent
	* Output the content.
	*/
	function pmxc_ShowContent()
	{
		echo '
		'. $this->download_content;
	}
}
?>
