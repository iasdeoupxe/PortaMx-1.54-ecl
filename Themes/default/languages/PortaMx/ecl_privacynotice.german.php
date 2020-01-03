<?php
/**
* \file ecl_privacynotice.php
* Language file ecl_privacynotice.german
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2015 by PortaMx corp. - http://portamx.com
* \version 1.54
* \date 18.11.2015
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
	<p style="text-align:center;"><strong>Datenschutzregeln f&uuml;r "@site@"</strong></p><br />
	Um die Rechtsvorschrift der Europ&auml;ischen Union zu erf&uuml;llen sind wir verpflichtet,
	Benutzern die auf "@host@" von innerhalb der EU zugreifen, &uuml;ber die verwendeten Cookies
	diese Website und die darin enthaltenen Informationen zu unterrichten und ihnen die M&ouml;glichkeit
	zum "Opt-in" ("Zustimmen") zu geben, Cookies zu setzen.
	Cookies sind kleine Dateien, die von Ihrem Browser gespeichert werden und alle Browser haben eine Option,
	wo Sie den Inhalt dieser Dateien &uuml;berpr&uuml;fen und l&ouml;schen k&ouml;nnen, wenn Sie es w&uuml;nschen.<br />
	<br />
	Die folgende Tabelle zeigt die Namen der einzelnen Cookies, wo es herkommt
	und welche Informationen wir &uuml;ber die Inhalte der Cookies haben.<br /><br />';

/*
	All cookie informations
	if you have more cookies, add them at the end with the same format
*/
$txt['pmx_ecl_headrows'] = array(
	array(
		'<div>Cookie</div>',
		'<div>Herkunft</div>',
		'<div>Persistenz</div>',
		'<div>Information und Verwendung</div>',
	),
	array(
		'ecl_auth',
		'@host@',
		'Verf&auml;llt nach 30 Tagen',
		'Dieses Cookie enth&auml;lt den Text "EU-Cookie-Gesetz - LiPF Cookies zugelassen."
			Ohne dieses Cookie wird die Website-Software an dem Erstellen anderer Cookies gehindert.',
	),
	array(
		'@cookie@',
		'@host@',
		'Verf&auml;llt nach der vom Anwender w&auml;hlbare Dauer',
		'Wenn Sie sich als Mitglied unserer Website Einloggen, enth&auml;lt dieses Cookie Ihren Benutzernamen,
			einen verschl&uuml;sselten Hashwerte Ihres Kennworts und die Zeit, die Sie angemeldet bleiben wollen.
			Es wird von der Website-Software verwendet, um sicherzustellen, dass Funktionen wie Angabe der neuen Nachrichten und
			privaten Nachrichten an Sie angezeigt werden k&ouml;nnen.
			Dieses Cookie ist essentiell f&uuml;r die korrekte Funktion der Website-Software.',
 ),
 array(
		'PHPSESSID',
		'@host@',
		'aktuelle Sitzung',
		'Das Cookie enth&auml;lt eine eindeutige Identifikation der Sitzung. Es wird f&uuml;r Mitglieder und G&auml;ste gesetzt
			und es ist wichtig f&uuml;r die korrekte Funktion der Website-Software. Dieses Cookie ist nur f&uuml;r die aktuelle Sitzung und
			sollte automatisch entfernt werden, wenn Sie das Browserfenster schlie&szlig;en.',
 	),
	array(
		'pmx_upshr{NAME}',
		'@host@',
		'aktuelle Sitzung',
		'Diese Cookies werden gesetzt, um Ihre Einstellungen f&uuml;r die Anzeige der Portalseite aufzunehmen,
			wenn ein Panel oder einzelne Bl&ouml;cke reduziert oder erweitert werden.',
	),
	array(
		'pmx_pgidx_blk{ID}',
		'@host@',
		'aktuelle Sitzung',
		'Diese Cookies werden gesetzt, um die Seitenzahl f&uuml;r die Portalseite aufzunehmen, wenn die
			Seitennummer bei einzelnen Bl&ouml;cken ge&auml;ndert wird.',
	),
	array(
		'pmx_cbtstat{ID}',
		'@host@',
		'aktuelle Sitzung',
		'Dieses Cookie wird gesetzt, um Ihre Einstellungen f&uuml;r den Status des CBT Navigator Blocks zu aufzunehmen.',
	),
	array(
		'pmx_poll{ID}',
		'@host@',
		'aktuelle Sitzung',
		'Dieses Cookie wird gesetzt, um aktuelle ID f&uuml;r die gew&auml;hlte Umfage bei einer Mehrfach Umfrage aufzunehmen.',
	),
	array(
		'pmx_{fadername}',
		'@host@',
		'aktuelle Sitzung',
		'Dieses Cookie wird gesetzt, um den aktuellen Status f&uuml;r einen Opac-Fader Block aufzunehmen.',
	),
	array(
		'pmx_LSBsub{ID}',
		'@host@',
		'aktuelle Sitzung',
		'Dieses Cookie wird gesetzt, um die aktuelle Kategorie f&uuml;r einen Statischen Kategorie Block aufzunehmen.',
	),
	array(
		'pmx_php_ckeck',
		'@host@',
		'Ladezeit der Seite',
		'Dieses Cookie werden Sie vermutlich nie sehen. Es wird gesetzt, wenn Sie eine Syntax Pr&uuml;fung in einem
			PHP Block durchf&uuml;hren. Das Cookie nach der Pr&uuml;fung ausgewertet und dann gel&ouml;scht.',
	),
	array(
		'pmx_YOfs',
		'@host@',
		'Ladezeit der Seite',
		'Dieses Cookie werden Sie vermutlich nie sehen. Es wird gesetzt, wenn Sie eine Portal Aktion
			wie z.B eine Seitennummer anklicken. Das Cookie wird beim Laden der gew&uuml;nschen Seite ausgewertet und dann gel&ouml;scht.
			Es wird verwendet, um die vertikale Bildschirm Position wie vor dem Klicken herzustellen.',
	),
);

/* footer header */
$txt['pmx_ecl_footertop'] = '
	<span><strong>Anmerkungen:</strong></span><br />';

/* footer informations */
$txt['pmx_ecl_footrows'] = array(
	array(
		'1',
		'Wir sind uns bewusst, dass Google zus&auml;tzlichen Cookies verwendet und auf Ihrem PC speichert, wenn Sie sich auf
			unserer Website und anderen Seiten umsehen. Diese dienen dazu, Werbung ohne Ihre Erlaubnis gezielt zu sucht.
				Vier dieser Cookies sind (was wir wir dar&uuml;ber wissen) "rememberme", "NID", "PREF" und "PP_TOS_ACK" genannt
				und werden in dem Google-Cache auf Ihrem Computer gespeichert.',
	),
	array(
		'2',
		'Wenn Sie mit dem Computer eines anderen auf diese Website zugreifen, bitte Sie um die Erlaubnis des
			Inhabers vor der Annahme von Cookies.',
	),
	array(
		'3',
		'Ihr Browser bietet Ihnen die M&ouml;glichkeit, alle auf Ihrem PC gespeicherten Cookies zu inspizieren.
			Normalerweise ist Ihr Browser f&uuml;r die Entfernung von "aktueller Sitzung" Cookies und diejenigen, die abgelaufen sind
			zust&auml;ndig. Wenn Ihr Browser das nicht tun, sollten Sie die Einstellungen &uuml;berpr&uuml;fen oder den Hersteller
			Ihres Browsers informieren.',
	),
	array(
		'4',
		'Wir bedauern und entschuldigen uns f&uuml;r die Unannehmlichkeiten, die Mitglieder und G&auml;ste bei dem Zugriff auf
			unsere Website von au&szlig;erhalb der Europ&auml;ischen Union haben. Es ist derzeit nicht m&ouml;glich Ihren
			geographischen Standort zu ermitteln, um zu entscheiden ob Sie aufgefordert werden m&uuml;ssen Cookies zu akzeptieren.',
	),
);

/* last line for ecl privacy */
$txt['pmx_ecl_footer'] = '<br />Weitere und umfassendere Informationen &uuml;ber Cookies und deren Verwendung finden Sie unter
	<a target="_blank" class="ecl_link" href="http://www.allaboutcookies.org">All About Cookies</a><br />';
?>