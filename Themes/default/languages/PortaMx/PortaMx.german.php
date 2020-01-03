<?php
/**
* \file PortaMx.german.php
* Language file PortaMx.german
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2015 by PortaMx corp. - http://portamx.com
* \version 1.54
* \date 18.11.2015
*/

$txt['forum'] = 'Community';
$txt['pmx_button'] = 'PortaMx';
$txt['pmx_managers'] = 'Ihr PortaMx';
$txt['pmx_expand'] = 'Ausklappen ';
$txt['pmx_collapse'] = 'Einklappen ';
$txt['pmx_hidepanel'] = 'Verberge ';
$txt['pmx_showpanel'] = 'Zeige ';
$txt['pmx_expand_index'] = 'Ausklappen';
$txt['pmx_show_index'] = 'Zeige';

// do not change the array keys !
$txt['pmx_block_panels'] = array(
	'head' => 'Kopf Panel',
	'top' => 'Oberes Panel',
	'left' => 'Linkes Panel',
	'right' => 'Rechtes Panel',
	'bottom' => 'Unteres Panel',
	'foot' => 'Fu&szlig; Panel',
);

// do not change the array keys !
$txt['pmx_block_sides'] = array(
	'head' => 'Kopf',
	'top' => 'Oben zentriert',
	'left' => 'Links',
	'right' => 'Rechts',
	'bottom' => 'Unteres zentriert',
	'foot' => 'Fu&szlig;',
	'front' => 'Front',
	'pages' => 'Seiten',
);

// Admin and dropdown menue
$txt['pmx_admincenter'] = 'PortaMx Admin Center';
$txt['pmx_settings'] = 'Einstellungs Verwaltung';
$txt['pmx_blocks'] = 'Block Verwaltung';
$txt['pmx_adm_settings'] = 'PortaMx Einstellungs Verwaltung';
$txt['pmx_adm_blocks'] = 'PortaMx Block Verwaltung';
$txt['permissionname_manage_portamx'] = 'Moderation der PortaMx Block Verwaltung';
$txt['permissionhelp_manage_portamx'] = 'Mit diesen Rechten kann jeder Benutzer in der Gruppe die "PortaMx Block Verwaltung" Moderation benutzen.';

$txt['pmx_categories'] = 'Kategorie Verwaltung';
$txt['pmx_articles'] = 'Artikel Verwaltung';
$txt['pmx_sefengine'] = 'SEF Verwaltung';
$txt['pmx_languages'] = 'Sprachen Verwaltung';
$txt['pmx_adm_categories'] = 'PortaMx Kategorie Verwaltung';
$txt['pmx_adm_articles'] = 'PortaMx Artikel Verwaltung';
$txt['pmx_adm_sefengine'] = 'PortaMx SEF Verwaltung';
$txt['pmx_adm_languages'] = 'PortaMx Sprachen Verwaltung';

// teaser
$txt['pmx_readmore'] = '<b>Ganzen Beitrag lesen</b>';
$txt['pmx_readclose'] = '<b>Das Intro anzeigen</b>';
$txt['pmx_teaserinfo'] = array(
	0 => ' title="K&uuml;rzung: %s von %s Worten"',
	1 => ' title="K&uuml;rzung: %s von %s Zeichen"',
);

// HighSlide JS
$txt['pmx_hs_read'] = 'Klicken f&uuml;r den ganzen Beitrag';
$txt['pmx_hs_expand'] = 'Klicken zum erweitern';
$txt['pmx_hs_caption'] = '<a href=\'http://highslide.com/\'>Powered by <i>Highslide JS</i></a>, implementiert von PortaMx corp.';

// special PHP type blocks/articles
$txt['pmx_edit_content_init'] = ' (INITIALISIERUNG)';
$txt['pmx_edit_content_show'] = ' (ANZEIGE)';
$txt['pmx_php_partblock'] = 'Initialisierungs Editor';
$txt['pmx_php_partblock_note'] = '<b>Zweiter Editor f&uuml;r spezielle PHP Bl&ouml;cke</b>';
$txt['pmx_php_partblock_help'] = '
	Sie k&ouml;nnen spezielle PHP Bl&ouml;cke erstellen, mit einem <b>Anzeige</b> Teil (wird im Template ausgef&uuml;hrt) im oberen Editor und einem <b>Initialisierungs</b> Teil (wird zur Ladezeit ausgef&uuml;hrt) im <b>zweiten Editor</b>.
	Jeder PHP Block hat zwei vordefinierte Variable (<b>$this->php_content</b> und <b>$this->php_vars</b>) f&uuml;r gemeinsame Nutzung und zum Datentransfer zwischen den beiden Teilen. Beispiel:<br />
	<i>Programm code im Initialisierungs Teil: <b>$this->php_content = \'Hallo Welt!\';</b><br />
	Programm code im Anzeige Teil: <b>echo $this->php_content;</b></i>';

// error messages
$txt['pmx_acces_error'] = 'Sie haben f&uuml;r diesen Bereich keine Berechtigung';
$txt['feed_response_error'] = "fsockopen(%s) fehlgeschlagen.\nFehler: Antwortzeit abgelaufen (%s Sekunden).";
$txt['page_reqerror_title'] = 'Fehler bei der Seiten Anfrage';
$txt['page_reqerror_msg'] = 'Die angeforderte Seite ist nicht verf&uuml;gbar oder Sie haben keine Rechte diese anzuzeigen.';
$txt['article_reqerror_title'] = 'Fehler bei der Artikel Anfrage';
$txt['article_reqerror_msg'] = 'Der angeforderte Artikel ist nicht verf&uuml;gbar oder Sie haben keine Rechte diesen anzuzeigen.';
$txt['category_reqerror_title'] = 'Fehler bei der Kategorie Anfrage';
$txt['category_reqerror_msg'] = 'Die angeforderte Kategorie ist nicht verf&uuml;gbar oder Sie haben keine Rechte diese anzuzeigen.';
$txt['download_error_title'] = 'Download Fehler';
$txt['download_acces_error'] = 'Sie haben nicht die notwendigen Rechte um den Download auszuf&uuml;hren.';
$txt['download_notfound_error'] = 'Die angeforderte Datei ist nicht verf&uuml;gbar. Der Download kann nicht ausgef&uuml;hrt werden.';
$txt['download_unknown_error'] = 'Ung&uuml;ltige Anfrage. Der Download kann nicht ausgef&uuml;hrt werden.';
$txt['front_reqerror_title'] = 'Anfrage Fehler';
$txt['front_reqerror_msg'] = 'Die Anfrage kann nicht ausgef&uuml;hrt werden weil die Frontpage gesperrt ist.';
$txt['unknown_reqerror_title'] = 'Anfrage Fehler';
$txt['unknown_reqerror_msg'] = 'Das angeforderte Element ist nicht verf&uuml;gbar oder kann nicht ausgef&uuml;hrt werden.';
$txt['page_reqerror_button'] = 'Zur&uuml;ck';

// Caching
$txt['cachestats'] = array(
	'mode' => '',
	'hits' => ', Treffer:',
	'fails' => ', Fehler:',
	'loaded' => ', Geladen:',
	'saved' => ', Gespeichert:',
	'time' => ', Zeit:'
);
$txt['cachemode'] = array(
	0 => 'gesperrt',
	1 => 'Memcached',
	3 => 'MMCache',
	4 => 'APC',
	5 => 'xCache',
	6 => 'Datei',
);
$txt['cache_status'] = 'PortaMx-cache[ ';
$txt['cacheseconds'] = ' Sekunden';
$txt['cachemilliseconds'] = ' Millisekunden';
$txt['cachekb'] = ' Kb';

// elc authentication
$txt['pmxecl_noAuth'] = 'Cookie Akzeptanz erforderlich';
$txt['pmxelc_needAccept'] = 'Um diese Webseite anzusehen ist es notwendig, Cookies auf Ihrem Computer zu speichern.<br />
	Die Cookies enthalten keine privaten Informationen, sie sind f&uuml;r den Programm Ablauf notwendig.<br />';
$txt['pmxelc_agree'] = '<br /><b>Bevor der Speicherung nicht zugestimmt wurde, k&ouml;nnen Sie nicht fortsetzen.</b>';
$txt['pmxelc_modal'] = '<b>das beim Ansehen diese Website, beim Einloggen und Registrieren Cookies gespeichert werden.</b>';
$txt['pmxelc_button'] = 'Ich stimme zu';
$txt['pmxelc_button_ttl'] = 'Ich stimme der Cookie Speicherung zu';
$txt['pmxelc_lang'] = 'Sprache:';
$txt['pmxelc_privacy'] = 'Datenschutzregeln';
$txt['pmxelc_privacy_ttl'] = 'Zeige/Verberge Datenschutzregeln';
$txt['pmxelc_privacy_note'] = 'Lesen Sie unsere Datenschutzregeln bitte in der normalen Ansicht.';
$txt['pmxelc_privacy_failed'] = 'Keine Datenschutzregeln vorhanden.';
$txt['pmxelc_failed_login'] = 'Sie k&ouml;nnen sich erst Einloggen, wenn der Cookie Speicherung zugestimmt wurde!';
$txt['pmxelc_failed_register'] = 'Sie k&ouml;nnen sich erst Registrieren, wenn der Cookie Speicherung zugestimmt wurde!';
$txt['pmxelc_failed_request'] = 'Sie k&ouml;nnen diese Anfrage erst ausf&uuml;hren, wenn der Cookie Speicherung zugestimmt wurde!';
$txt['pmxelc_failed_access'] = 'Sie haben nicht die notwendige Berechtigung um diese Aktion auszuf&uuml;hren!';

// who display
$txt['pmx_who_frontpage'] = 'Schaut die Frontpage';
$txt['pmx_who_spage'] = 'Schaut die Seite %s';
$txt['pmx_who_art'] = 'Schaut den Artikel %s';
$txt['pmx_who_cat'] = 'Schaut die Kategorie %s';
$txt['pmx_who_portamx'] = 'Schaut die PortaMx %s';
$txt['pmx_who_admin'] = 'Schaut den Admin Bereich %s';
$txt['pmx_who_unknow'] = 'Schaut %s';
$txt['pmx_who_acts'] = array(
	'pmx_center' => 'Admin Center',
	'pmx_settings' => 'Einstellungs Verwaltung',
	'pmx_blocks' => 'Block Verwaltung',
	'pmx_articles' => 'Artikel Verwaltung',
	'pmx_categories' => 'Kategorie Verwaltung',
	'pmx_languages' => 'Sprachen Verwaltung',
);

// category/article display
$txt['pmx_openSidebar'] = 'Klicken f&uuml;r weitere Artikel';
$txt['pmx_clickclose'] = 'Klicken zum Schlie&szlig;en';
$txt['pmx_more_articles'] = 'Artikel in der Kategorie';
$txt['pmx_more_categories'] = 'Weitere Kategorien in';

/* Blocktype specific text */
// cbt_navigator
$txt['pmx_cbt_colexp'] = 'Ein-/Ausklappen: ';
$txt['pmx_cbt_expandall'] = 'Ausklappen';
$txt['pmx_cbt_collapseall'] = 'Einklappen';

// download
$txt['download'] = 'Download';
$txt['pmx_download_empty'] = '<strong>Keine Downloads vorhanden</strong>';
$txt['pmx_kb_downloads'] = 'Kb, Downloads: ';

// polls
$txt['pmx_poll_novote_opt'] = 'Sie haben keine Abstimmung Option gew&auml;hlt.';
$txt['pmx_pollmultiview'] = 'W&auml;hlen Sie eine Abstimmung:';
$txt['pmx_poll_closed'] = 'Abstimmung geschlossen.';
$txt['pmx_poll_select_locked'] = ' [Geschlossen]';
$txt['pmx_poll_select_expired'] = ' [Abgelaufen]';

// rss reader
$txt['pmx_rssreader_postat'] = 'Geschrieben: ';
$txt['pmx_rssreader_error'] = 'Antwortzeit Fehler, der Feed kann nicht gelesen werden.';
$txt['pmx_rssreader_timeout'] = 'Zeit&uuml;berschreitung beim warten auf Daten.';

// shoutbox
$txt['pmx_shoutbox_toggle'] = 'Bearbeitungsmodus wechseln';
$txt['pmx_shoutbox_shoutdelete'] = 'Shout l&ouml;schen';
$txt['pmx_shoutbox_shoutconfirm'] = 'Wollen Sie diesen Shout wirklich l&ouml;schen?';
$txt['pmx_shoutbox_shoutedit'] = 'Shout bearbeiten';
$txt['pmx_shoutbox_button_open'] = 'Shout?';
$txt['pmx_shoutbox_button'] = 'Shout!';
$txt['pmx_shoutbox_button_title'] = 'Neuer Shout!';
$txt['pmx_shoutbox_send_title'] = 'Ihren Shout senden!';
$txt['pmx_shoutbox_bbc_code'] = 'BBC Display Ein-/Ausblenden';
$txt['pmx_shoutbbc_b'] = 'Fett';
$txt['pmx_shoutbbc_i'] = 'Kursiv';
$txt['pmx_shoutbbc_u'] = 'Unterstrichen';
$txt['pmx_shoutbbc_s'] = 'Durchgestrichen';
$txt['pmx_shoutbbc_m'] = 'Laufschrift';
$txt['pmx_shoutbbc_sub'] = 'Tiefstellen';
$txt['pmx_shoutbbc_sup'] = 'Hochstellen';
$txt['pmx_shoutbbc_changecolor'] = 'Farbe &auml;ndern';
$txt['pmx_shoutbbc_colorBlack'] = 'Schwarz';
$txt['pmx_shoutbbc_colorRed'] = 'Rot';
$txt['pmx_shoutbbc_colorYellow'] = 'Gelb';
$txt['pmx_shoutbbc_colorPink'] = 'Pink';
$txt['pmx_shoutbbc_colorGreen'] = 'Gr&uuml;n';
$txt['pmx_shoutbbc_colorOrange'] = 'Orange';
$txt['pmx_shoutbbc_colorPurple'] = 'Magenta';
$txt['pmx_shoutbbc_colorBlue'] = 'Blau';
$txt['pmx_shoutbbc_colorBeige'] = 'Beige';
$txt['pmx_shoutbbc_colorBrown'] = 'Braun';
$txt['pmx_shoutbbc_colorTeal'] = 'Petrol';
$txt['pmx_shoutbbc_colorNavy'] = 'Marineblau';
$txt['pmx_shoutbbc_colorMaroon'] = 'Rotbraun';
$txt['pmx_shoutbbc_colorLimeGreen'] = 'Hellgr&uuml;n';
$txt['pmx_shoutbbc_colorWhite'] = 'Wei&szlig;';

// statistics
$txt['pmx_stat_member'] = 'Benutzer';
$txt['pmx_stat_totalmember'] = 'Benutzer gesamt';
$txt['pmx_stat_lastmember'] = 'Letzter';
$txt['pmx_stat_stats'] = 'Statistik';
$txt['pmx_stat_stats_post'] = 'Beitr&auml;ge gesamt';
$txt['pmx_stat_stats_topic'] = 'Themen gesamt';
$txt['pmx_stat_stats_ol_today'] = 'Heute online';
$txt['pmx_stat_stats_ol_ever'] = 'Am meisten online';
$txt['pmx_stat_users'] = 'Benutzer online';
$txt['pmx_stat_users_reg'] = 'Benutzer';
$txt['pmx_stat_users_guest'] = 'G&auml;ste';
$txt['pmx_stat_users_spider'] = 'Spider';
$txt['pmx_stat_users_total'] = 'Gesamt';
$txt['pmx_memberlist_icon'] = 'Mitgliederliste';
$txt['pmx_statistics_icon'] = 'Statistiken';
$txt['pmx_online_user_icon'] = 'Benutzer online';

// theme select
$txt['pmx_theme_change'] = 'Auf das Bild klicken, um das Design zu wechseln';

// user_login
$txt['pmx_hello'] = 'Hallo ';
$txt['pmx_pm'] = 'Private Mitteilungen';
$txt['pmx_unread'] = 'Ungelesene Beitr&auml;ge';
$txt['pmx_replies'] = 'Ungelesene Antworten';
$txt['pmx_showownposts'] = 'Meine Beitr&auml;ge anzeigen';
$txt['pmx_unapproved_members'] = 'Unbest&auml;tigte Benutzer:';
$txt['pmx_maintenace'] = 'Wartungsmodus';
$txt['pmx_loggedintime'] = 'Angemeldet';
$txt['pmx_Ldays'] = 'T';
$txt['pmx_Lhours'] = 'S';
$txt['pmx_Lminutes'] = 'M';
$txt['pmx_langsel'] = 'Sprache w&auml;hlen:';

// mini_calendar
$txt['pmx_cal_birthdays'] = 'Geburtstage';
$txt['pmx_cal_holidays'] = 'Feiertage';
$txt['pmx_cal_events'] = 'Ereignisse';
/* Birthday, Holiday, Event date format chars:
%M = Month (Jan - Dec)
%m = Month (01 - 12)
%d = Day (01 - 31)
%j = Day (1 - 31) */
$txt['pmx_minical_dateform'] = array(
	'%j. %M',			// single date
	'%j.',				// start-date same month
	' - %j. %M',	// end-date same month
	'%j. %M',			// start-date not same month
	' - %j. %M'		// end-date not same month
);

// common use
$txt['pmx_text_category'] = 'Kategorie: ';
$txt['pmx_text_board'] = 'Board: ';
$txt['pmx_text_topic'] = 'Thema: ';
$txt['pmx_text_post'] = 'Beitrag: ';
$txt['pmx_text_postby'] = 'Geschrieben von: ';
$txt['pmx_text_replies'] = ' Antworten: ';
$txt['pmx_text_views'] = 'Gelesen: ';
$txt['pmx_text_createdby'] = 'Erstellt von: ';
$txt['pmx_text_updated'] = 'Letzte &Auml;nderung: ';
$txt['pmx_text_readmore'] = '<b>Weiter Lesen</b>';
$txt['pmx_text_show_attach'] = '<b>Zeige Bildanh&auml;nge</b>';
$txt['pmx_text_hide_attach'] = '<b>Verberge Bildanh&auml;nge</b>';
$txt['pmx_text_printing'] = 'Inhalt Drucken';
$txt['pmx_user_unknown'] = 'Unbekannt';
$txt['pmx_set_promote'] = 'Beitrag Publizieren';
$txt['pmx_unset_promote'] = 'Publizieren entfernen';
?>