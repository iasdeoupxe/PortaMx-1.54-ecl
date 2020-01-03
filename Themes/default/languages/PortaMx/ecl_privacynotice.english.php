<?php
/**
* \file ecl_privacynotice.php
* Language file ecl_privacynotice.english
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

/*
	Additional informations to this file format.
	We have 3 tokens, they replaced at run time:
	@site@   - replace with the Forum name
	@host@   - replaced with the Domain name
	@cookie@ - replaced the the cookie you have setup in SMF
*/

/* Header text */
$txt['pmx_ecl_header'] = '
	<p style="text-align:center;"><strong>Privacy Notice for "@site@"</strong></p><br />
	To comply with European Union law, we are required to inform users accessing "@host@" from within the
	EU about the cookies that this site uses and the information they contain and also provide them with the means
	to "opt-in" - in other words, permit the site to set cookies.
	Cookies are small files that are stored by your browser and all browsers have an option whereby you can inspect
	the content of these files and delete them if you wish.<br />
	<br />
	The following table details the name of each cookie, where it comes from and what we know about the information
	that cookie stores:<br /><br />';

/*
	All cookie informations
	if you have more cookies, add them at the end with the same format
*/
$txt['pmx_ecl_headrows'] = array(
	array(
		'<div>Cookie</div>',
		'<div>Origin</div>',
		'<div>Persistency</div>',
		'<div>Information and Usage</div>',
	),
	array(
		'ecl_auth',
		'@host@',
		'Expires after 30 days',
		'This cookie contains the text "EU Cookie Law - LiPF cookies authorised".
			Without this cookie, the Forums\' software is prevented from setting other cookies.',
	),
	array(
		'@cookie@',
		'@host@',
		'Expires according to user-chosen session duration',
		'If you log-in as a member of this site, this cookie contains your user name, an encrypted hash of
			your password and the time you logged-in. It is used by the site software to ensure that features such as indicating
			new Forum and Private messages are indicated to you. This cookie is essential for the site software to work correctly.',
	),
	array(
		'PHPSESSID',
		'@host@',
		'Current session only',
		'This cookie contains a unique Session Identification value. It is set for both members and
			non-members (guests) and it is essential for the site software to work correctly. This cookie is not persistent
			and should be automatically removed when you close the browser window.',
	),
	array(
		'pmx_upshr{NAME}',
		'@host@',
		'Current session only',
		'These cookies are set to records your display preferences for the site\'s Portal page if a panel
			or individual block is collapsed or expanded',
	),
	array(
		'pmx_pgidx_blk{ID}',
		'@host@',
		'Current session only',
		'These cookies are set to records the page number for the site\'s Portal page if the page for a
			individual block is changed.',
	),
	array(
		'pmx_cbtstat{ID}',
		'@host@',
		'Current session only',
		'These cookies are set to records the expand/collapse state for a CBT Navigator block content.',
	),
	array(
		'pmx_poll{ID}',
		'@host@',
		'Current session only',
		'These cookies are set to records the id for the current poll in a multiple Poll block.',
	),
	array(
		'pmx_{fadername}',
		'@host@',
		'Current session only',
		'These cookies are set to records the state for a Opac-Fader block.',
	),
	array(
		'pmx_LSBsub{ID}',
		'@host@',
		'Current session only',
		'These cookies are set to records the current category and the state for a static Category block.',
	),
	array(
		'pmx_shout{ID}',
		'@host@',
		'Current session only',
		'These cookies are set to records the current state of a Shout box block.',
	),
	array(
		'pmx_php_ckeck',
		'@host@',
		'Page load time',
		'This cookie will probably never see you. It is set, if a Syntax check on a PHP block is initiated
			and will be deleted if the function executed.',
	),
	array(
		'pmx_YOfs',
		'@host@',
		'Page load time',
		'This cookie will probably never see you. It is set on portal actions like click on a page number.
			The cookie is evaluated on load the desired page and then deleted. It is used to restore the vertical screen position
			as before the click.'
	),
);

/* footer header */
$txt['pmx_ecl_footertop'] = '
	<span><strong>Notes:</strong></span><br />';

/* footer informations */
$txt['pmx_ecl_footrows'] = array(
	array(
		'1',
		'We are aware that Google uses additional cookies it stores on your PC and when you browse our site and all other
			sites. These are used to target advertising and Google currently does this without seeking your permission. Four of
			these cookies we know about are named "rememberme", "NID", "PREF" and "PP_TOS_ACK"
			and are stored in Google\'s cache on your computer.',
	),
	array(
		'2',
		'If you are accessing this site using someone else\'s computer, please ask the owner\'s permission before
			accepting cookies.',
	),
	array(
		'3',
		'Your browser provides you with the ability to inspect all cookies stored on your PC. In addition your browser
			is responsible for removing "current session only" cookies and those that have expired; if your browser is
			not doing this, you should report the matter to your browser\'s authors.',
	),
	array(
		'4',
		'We regret and apologies for any inconvenience this causes to members and guests who are accessing our web site
			from outside the European Union. It is not currently possible for us to interrogate your browser and obtain geographic
			location information in order to decide whether or not to prompt you to accept cookies.',
	),
);

/* last line for ecl privacy */
$txt['pmx_ecl_footer'] = '
	<br />For further and fuller information about cookies and their use, please visit
		<a target="_blank" class="ecl_link" href="http://www.allaboutcookies.org">All About Cookies</a><br />';
?>
