<?php
/**
* \file ecl_privacynotice.php
* Language file ecl_privacynotice.german
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
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
	<p style="text-align:center;"><strong>Datenschutzregeln für "@site@"</strong></p><br />
	Um die Rechtsvorschrift der Europäischen Union zu erfüllen sind wir verpflichtet,
	Benutzern die auf "@host@" von innerhalb der EU zugreifen, über die verwendeten Cookies
	diese Website und die darin enthaltenen Informationen zu unterrichten und ihnen die Möglichkeit
	zum "Opt-in" ("Zustimmen") zu geben, Cookies zu setzen.
	Cookies sind kleine Dateien, die von Ihrem Browser gespeichert werden und alle Browser haben eine Option,
	wo Sie den Inhalt dieser Dateien überprüfen und löschen können, wenn Sie es wünschen.<br />
	<br />
	Die folgende Tabelle zeigt die Namen der einzelnen Cookies, wo es herkommt
	und welche Informationen wir über die Inhalte der Cookies haben.<br /><br />';

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
		'Verfällt nach 30 Tagen',
		'Dieses Cookie enthält den Text "EU-Cookie-Gesetz - LiPF Cookies zugelassen."
			Ohne dieses Cookie wird die Website-Software an dem Erstellen anderer Cookies gehindert.',
	),
	array(
		'@cookie@',
		'@host@',
		'Verfällt nach der vom Anwender wählbare Dauer',
		'Wenn Sie sich als Mitglied unserer Website Einloggen, enthält dieses Cookie Ihren Benutzernamen,
			einen verschlüsselten Hashwerte Ihres Kennworts und die Zeit, die Sie angemeldet bleiben wollen.
			Es wird von der Website-Software verwendet, um sicherzustellen, dass Funktionen wie Angabe der neuen Nachrichten und
			privaten Nachrichten an Sie angezeigt werden können.
			Dieses Cookie ist essentiell für die korrekte Funktion der Website-Software.',
 ),
 array(
		'PHPSESSID',
		'@host@',
		'aktuelle Sitzung',
		'Das Cookie enthält eine eindeutige Identifikation der Sitzung. Es wird für Mitglieder und Gäste gesetzt
			und es ist wichtig für die korrekte Funktion der Website-Software. Dieses Cookie ist nur für die aktuelle Sitzung und
			sollte automatisch entfernt werden, wenn Sie das Browserfenster schließen.',
 	),
	array(
		'pmx_upshr{NAME}',
		'@host@',
		'aktuelle Sitzung',
		'Diese Cookies werden gesetzt, um Ihre Einstellungen für die Anzeige der Portalseite aufzunehmen,
			wenn ein Panel oder einzelne Blöcke reduziert oder erweitert werden.',
	),
	array(
		'pmx_pgidx_blk{ID}',
		'@host@',
		'aktuelle Sitzung',
		'Diese Cookies werden gesetzt, um die Seitenzahl für die Portalseite aufzunehmen, wenn die
			Seitennummer bei einzelnen Blöcken geändert wird.',
	),
	array(
		'pmx_cbtstat{ID}',
		'@host@',
		'aktuelle Sitzung',
		'Dieses Cookie wird gesetzt, um Ihre Einstellungen für den Status des CBT Navigator Blocks zu aufzunehmen.',
	),
	array(
		'pmx_poll{ID}',
		'@host@',
		'aktuelle Sitzung',
		'Dieses Cookie wird gesetzt, um aktuelle ID für die gewählte Umfage bei einer Mehrfach Umfrage aufzunehmen.',
	),
	array(
		'pmx_{fadername}',
		'@host@',
		'aktuelle Sitzung',
		'Dieses Cookie wird gesetzt, um den aktuellen Status für einen Opac-Fader Block aufzunehmen.',
	),
	array(
		'pmx_LSBsub{ID}',
		'@host@',
		'aktuelle Sitzung',
		'Dieses Cookie wird gesetzt, um die aktuelle Kategorie für einen Statischen Kategorie Block aufzunehmen.',
	),
	array(
		'pmx_php_ckeck',
		'@host@',
		'Ladezeit der Seite',
		'Dieses Cookie werden Sie vermutlich nie sehen. Es wird gesetzt, wenn Sie eine Syntax Prüfung in einem
			PHP Block durchführen. Das Cookie nach der Prüfung ausgewertet und dann gelöscht.',
	),
	array(
		'pmx_YOfs',
		'@host@',
		'Ladezeit der Seite',
		'Dieses Cookie werden Sie vermutlich nie sehen. Es wird gesetzt, wenn Sie eine Portal Aktion
			wie z.B eine Seitennummer anklicken. Das Cookie wird beim Laden der gewünschen Seite ausgewertet und dann gelöscht.
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
		'Wir sind uns bewusst, dass Google zusätzlichen Cookies verwendet und auf Ihrem PC speichert, wenn Sie sich auf
			unserer Website und anderen Seiten umsehen. Diese dienen dazu, Werbung ohne Ihre Erlaubnis gezielt zu sucht.
				Vier dieser Cookies sind (was wir wir darüber wissen) "rememberme", "NID", "PREF" und "PP_TOS_ACK" genannt
				und werden in dem Google-Cache auf Ihrem Computer gespeichert.',
	),
	array(
		'2',
		'Wenn Sie mit dem Computer eines anderen auf diese Website zugreifen, bitte Sie um die Erlaubnis des
			Inhabers vor der Annahme von Cookies.',
	),
	array(
		'3',
		'Ihr Browser bietet Ihnen die Möglichkeit, alle auf Ihrem PC gespeicherten Cookies zu inspizieren.
			Normalerweise ist Ihr Browser für die Entfernung von "aktueller Sitzung" Cookies und diejenigen, die abgelaufen sind
			zuständig. Wenn Ihr Browser das nicht tun, sollten Sie die Einstellungen überprüfen oder den Hersteller
			Ihres Browsers informieren.',
	),
	array(
		'4',
		'Wir bedauern und entschuldigen uns für die Unannehmlichkeiten, die Mitglieder und Gäste bei dem Zugriff auf
			unsere Website von außerhalb der Europäischen Union haben. Es ist derzeit nicht möglich Ihren
			geographischen Standort zu ermitteln, um zu entscheiden ob Sie aufgefordert werden müssen Cookies zu akzeptieren.',
	),
);

/* last line for ecl privacy */
$txt['pmx_ecl_footer'] = '<br />Weitere und umfassendere Informationen über Cookies und deren Verwendung finden Sie unter
	<a target="_blank" class="ecl_link" href="http://www.allaboutcookies.org">All About Cookies</a><br />';
?>
