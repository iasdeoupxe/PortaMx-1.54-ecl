<?php
/**
* \file AdminBlocks.german-utf8.php
* Language file AdminBlocks.german-utf8
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
*/

// Block description
$txt['pmx_boardnews_description'] = 'Board News';
$txt['pmx_download_description'] = 'Download';
$txt['pmx_mini_calendar_description'] = 'Mini Kalender';
$txt['pmx_html_description'] = 'HTML';
$txt['pmx_newposts_description'] = 'Neue Beiträge';
$txt['pmx_php_description'] = 'PHP';
$txt['pmx_recent_post_description'] = 'Ungelesene Beiträge';
$txt['pmx_recent_topics_description'] = 'Ungelesene Themen';
$txt['pmx_script_description'] = 'Script';
$txt['pmx_statistics_description'] = 'Statistiken';
$txt['pmx_theme_select_description'] = 'Design Auswahl';
$txt['pmx_user_login_description'] = 'Benutzer und Login';
$txt['pmx_cbt_navigator_description'] = 'CBT Navigator';
$txt['pmx_bbc_script_description'] = 'BBC Script';
$txt['pmx_rss_reader_description'] = 'RSS Reader';
$txt['pmx_shoutbox_description'] = 'Shout box';
$txt['pmx_polls_description'] = 'Umfragen';
$txt['pmx_boardnewsmult_description'] = 'Mehrfach Board News';
$txt['pmx_article_description'] = 'Statische Artikel';
$txt['pmx_category_description'] = 'Statische Kategorien';
$txt['pmx_promotedposts_description'] = 'Publizierte Beiträge';
$txt["pmx_fader_description"] = 'Transparenz Fader';

// Block overview
$txt['pmx_add_sideblock'] = 'Klicken zum hinzufügen eines Blocks (%s)';
$txt['pmx_edit_sideblock'] = 'Klicken zum bearbeiten des Blocks';
$txt['pmx_clone_sideblock'] = 'Klicken zum duplizieren des Blocks';
$txt['pmx_move_sideblock'] = 'Klicken zum verschieben des Blocks';
$txt['pmx_delete_sideblock'] = 'Klicken zum löschen des Blocks';
$txt['pmx_confirm_blockdelete'] = 'Sind Sie sicher, das Sie diesen Block löschen wollen?';
$txt['pmx_chg_blockaccess'] = 'Klicken zum ändern der Anzeige Berechtigung';

$txt['pmx_admBlk_order'] = 'Order';
$txt['pmx_admBlk_adj'] = 'Ausrichtung';
$txt['pmx_admBlk_type'] = 'Blocktyp';
$txt['pmx_admBlk_options'] = 'Einstellung Optionen';

$txt['pmx_moveto'] = 'Verschiebe nach:&nbsp;';
$txt['pmx_cloneto'] = 'Dupliziere nach:&nbsp;';
$txt['pmx_clonechoice'] = 'Panel wählen:';
$txt['pmx_chgAccess'] = 'Block Anzeige Berechtigung';

$txt['pmx_have_settings'] = 'Block hat Einstellungs Optionen';
$txt['pmx_have_groupaccess'] = 'Block hat Anzeige Berechtigung';
$txt['pmx_have_modaccess'] = 'Block hat Moderation Berechtigung';
$txt['pmx_have_dynamics'] = 'Block hat Dynamische Anzeige Optionen';
$txt['pmx_have_cssfile'] = 'Block hat benutzerdefinierte CSS Klassen';
$txt['pmx_have_caching'] = 'Block Cache: ';

$txt['pmx_edit_type'] = 'Blocktyp:';
$txt['pmx_edit_cache'] = 'Cache Freigabe:';
$txt['pmx_edit_cachetime'] = 'Zeit:';
$txt['pmx_edit_cachetimemin'] = 'Min';
$txt['pmx_edit_cachetimesec'] = ' Sek';

$txt['pmx_edit_cachehelp'] = 'Wenn gewählt wird der Inhalt gespeichert und nach der angegebenen Zeit erneut gespeichert.<br />
	Sie können den Multiplikator "*" verwenden, z.B. "24*60" für einen Tag.';
$txt['pmx_edit_pmxcachehelp'] = 'Ändern Sie die Zeit nur dann, wenn Sie genau wissen was Sie tun. Eine falsch eingestellte Zeit kann Ihren Server sehr langsam machen!<br >
	Um die Vorgabe wieder herzustellen, deaktivieren Sie diese Option und Aktivieren sie erneut.';
$txt['pmx_edit_nocachehelp'] = 'Caching ist für diesen Blocktyp nicht möglich.';
$txt['pmx_edit_noSMFcache'] = 'Caching ist in den SMF Server Einstellungen nicht aktiviert.';

$txt['pmx_edit_frontplacing'] = 'Platzierung bei Seiten, Kategorien oder Artikeln';
$txt['pmx_edit_frontplacing_hide'] = 'Block verbergen:';
$txt['pmx_edit_frontplacing_before'] = 'Block davor platzieren:';
$txt['pmx_edit_frontplacing_after'] = 'Block danach platzieren:';
$txt['pmx_edit_frontplacinghelp'] = 'Wählen Sie die Platzierung des Blocks wenn eine Seite, eine Kategorie oder ein Artikel angefordert wird.';

$txt['pmx_edit_frontmode'] = 'Frontpage Typ Änderung';
$txt['pmx_edit_frontmode_none'] = 'Nicht ändern:';
$txt['pmx_edit_frontmode_center'] = 'Zentriert:';
$txt['pmx_edit_frontmode_full'] = 'Vollbild:';
$txt['pmx_edit_frontmodehelp'] = 'Wählen Sie den Frontpage Typ für den Block.
	Die Frontpage wird zum gewählten Typ geändert, wenn eine Seite, eine Kategorie oder ein Artikel angefordert wird.';

$txt['pmx_edit_frontview'] = 'Anzeige des Blocks bei Frontpage Typ';
$txt['pmx_edit_frontview_any'] = 'Immer:';
$txt['pmx_edit_frontview_center'] = 'Zentriert:';
$txt['pmx_edit_frontview_full'] = 'Vollbild:';
$txt['pmx_edit_frontviewhelp'] = 'Wählen Sie die Block Anzeige bei einem Frontpage Typ.
	Beachten Sie, das der Block nur bei dem gewählten Frontpage Typ angezeigt wird.';

$txt['pmx_edit_groups'] = 'Anzeige Berechtigung Einstellungen';
$txt['pmx_edit_groups_help'] = 'Wählen Sie die Benutzergruppen die den Block sehen können.<br />
	Sie können auch Gruppen den Zugriff <b>verweigern</b>. Das ist Hilfreich wenn ein Benutzer in mehreren Gruppen eingetragen ist, aber eine dieser Gruppen den Block nicht sehen soll.<br />
	Zum Umschalten zwischen <b>erlauben</b> und <b>verweigern</b> halten Sie die <b>Strg-Taste</b> gedrückt und <b>doppelklicken</b> Sie auf einen Eintrag.
	Bei Verweigerten Gruppen erscheint das Symbol <b>^</b> vor dem Gruppennamen.';

$txt['pmx_edit_ext_opts'] = 'Dynamische Anzeige Optionen';
$txt['pmx_edit_ext_opts_help'] = 'Wenn Sie eine (oder mehrere) Dynamische Anzeige Optionen wählen, wird der Block <b>nur</b> bei dieser Angezeigt, andernfalls nicht.
	Um den Block ohne Dynamische Optionen anzuzeigen, wählen Sie <b>KEINE</b> der Optionen.';
$txt['pmx_edit_ext_opts_morehelp'] = '<br style="line-height:1.7em;" />
	Wählen Sie eine oder mehrere Optionen durch haltender der <b>Strg-Taste</b> und <b>klicken</b> auf eine Option.<br />
	Zum wechseln zwischen <b>Anzeigen</b> und <b>Verbergen</b>, halten Sie die <b>Strg-Taste</b> und <b>doppelklicken</b> (IE braucht drei Klicks!) Sie auf eine Option.
	Bei <b>Verbergen</b> erscheint das Symbol <b>^</b> vor der Option.<br />
	<u><b>Wie funktioniert das?</b></u><br />
	<b>Anzeigen</b>: Um den Block bei einer oder mehreren Aktionen, Boards oder Sprachen anzuzeigen, wählen Sie diese.<br />
	<b>Verbergen</b>: Um den Block bei einer oder mehreren Aktionen, Boards oder Sprachen zu verbergen, wählen Sie diese mit einem Doppelklick (Sie sehen das Symbol <b>^</b> vor der Option).<br />
	<b>Beispiele</b>:<br />Wählen Sie die Aktionen "Admin" und "Kalender" ... Der Block wird nur bei Admin und Kalender angezeigt.<br />
	Wählen Sie die Aktionen "<b>^</b>Admin" ... Der Block wird immer angezeigt, jedoch nicht bei Admin.';

$txt['pmx_edit_ext_opts_action'] = 'Zeige/Verberge den Block bei Aktion';
$txt['pmx_edit_ext_opts_custaction'] = 'Zeige/Verberge den Block bei Benutzer Aktionen';
$txt['pmx_edit_ext_opts_boards'] = 'Zeige/Verberge den Block bei Board';
$txt['pmx_edit_ext_opts_languages'] = 'Zeige/Verberge den Block bei Sprache';
$txt['pmx_edit_ext_opts_themes'] = 'Zeige/Verberge den Block bei Designs';
$txt['pmx_edit_ext_SD_standalone'] = 'Verbergen bei SimpleDesk autonom Betrieb:';
$txt['pmx_edit_ext_maintenance'] = 'Bei Wartungsmodus anzeigen:';

$txt['pmx_edit_ext_opts_custhelp'] = 'Hier können Sie andere Aktionen eingeben.
	Für Seiten, Artikel und Kategorien wird ein Präfix verwendet (<b>p:</b> für Seiten, <b>a:</b> für Artikel und <b>c:</b> für Kategorien).
	Setzen Sie das Präfix vor dem Namen der Seite, des Artikels oder der Kategorie, z.B. <b>p:meine_seite</b>.
	Die Namen können die Platzhalter <b>*</b> and <b>?</b> enthalten. Um den Block bei einer Aktion zu <b>verbergen</b>, setzen Sie das Symbol <b>^</b> vor den Eintrag bzw. den Präfix.
	Weiterhin können Sie Subaktionen verwenden, diese beginnen immer mit dem Kaufmännischem UND (<b>&amp;</b>) z.B. <b>&amp;subaktionname=wert</b>.
	Weitere Informationen zu den Benutzer Aktionen finden Sie in unseren Dokumentationen und im Support Forum.';
$txt['pmx_edit_ext_opts_selnote'] = 'Zum anzeigen oder verbergen des Blocks wählen/abwählen Sie eine oder mehrere Optionen durch haltender der <b>Strg-Taste</b> und <b>klicken</b> auf eine Option.
	Zum wechseln zwischen <b>Anzeigen</b> und <b>Verbergen</b>, halten Sie die <b>Strg-Taste</b> und <b>doppelklicken</b> (IE braucht drei Klicks!) Sie auf eine Option.
	Bei <b>Verbergen</b> erscheint das Symbol <b>^</b> vor der Option.<br />
	<b>Beispiel</b>: Bei "<i>option</i>" wird der Block Angezeigt, bei "^<i>option</i>" wird der Block Verborgen, wenn Sie die <i>option</i> Aktion ausführen';

$txt['pmx_block_moderate_title'] = 'Block Moderation';
$txt['pmx_block_moderate'] = 'Moderation freigeben:';
$txt['pmx_block_moderatehelp'] = 'Wenn freigegeben können alle Benutzer in der <i>Block Moderator Gruppe</i> diesen Block bearbeiten.';

$txt['pmx_rowmove_title'] = 'Neue Position wählen';
$txt['pmx_block_rowmove'] = 'Verschiebe Block';
$txt['pmx_blockmove_place'] = 'auf die Position';
$txt['pmx_blockmove_to'] = 'Block';
$txt['rowmove_before'] = 'vor';
$txt['rowmove_after'] = 'nach';
$txt['row_move_updown'] = 'Klicken um die Block Position zu ändern';

$txt['pmx_clone_move_side'] = 'Wählen Sie das Ziel:';
$txt['pmx_clone_move_title'] = '';
$txt['pmx_text_clone'] = 'Block duplizieren';
$txt['pmx_text_move'] = 'Block verschieben';
$txt['pmx_text_block'] = 'Block:';
$txt['pmx_blocks_settings_title'] = '%s Block Einstellungen';
$txt['pmx_clone_move_toarticles'] = 'Artikel Verwaltung';
$txt['pmx_promote_all'] = '[ alle Beiträge ]';

/* Blocktype specific text */
// cbt_navigator
$txt['pmx_cbtnavnum'] = 'Max. Nummer von Themen in jedem Board:';
$txt['pmx_cbtnavlen'] = 'Max. Länge für jedem Eintrag (Zeichen):';
$txt['pmx_cbtnavexpand'] = 'Zu Anfang alle Boards ausklappen:';
$txt['pmx_cbtnavexpandnew'] = 'Zu Anfang alle Boards mit neuen Beiträgen ausklappen:';
$txt['pmx_cbtnavboards'] = 'Wählen Sie die Boards welche Sie anzeigen wollen';
$txt['pmx_cbt_shorten_hint'] = 'Die Länge ist Abhängig von der Font Größe und dem Font Typ.
	Bei einer Panel-/Block breite von 170 Pixel ist der Wert von 20 angebracht.<br />
	Um die Zeilenkürzung zu deaktivieren, geben Sie den Wert "0" ein.';

// download
$txt['pmx_download_board'] = 'Wählen Sie das Board von dem der Download erfolgen soll:';
$txt['pmx_download_groups'] = 'Benutzergruppen, die Download Berechtigung haben:';

// fader
$txt['pmx_fader_uptime'] = 'Einblendzeit:';
$txt['pmx_fader_downtime'] = 'Ausblendzeit:';
$txt['pmx_fader_holdtime'] = 'Haltezeit:';
$txt['pmx_fader_changetime'] = 'Wechselzeit:';
$txt['pmx_fader_units'] = 'Sekunden';
$txt['pmx_fader_timehelp'] = 'Alle Zeiten müssen als #.#### Sekunden angegeben werden. Die Werte werden intern in Millisekunden umgerechnet (#.#### * 1000)';
$txt['pmx_fader_content'] = 'Geben Sie den Fader Inhalt ein:';

// do not reformat these !
$txt['pmx_fader_content_help'] = 'Sie können beliebigen HTML Code verwenden.
	Jeder Eintrag muss in geschweifte Klammern <b>{ .. }</b> eingeschlossen werden.
	Zeilenumbrüche, Tabulatoren und Leerzeichen werden zur Laufzeit entfernt.
	Sie können die Zeitwerte für jeden Eintrag überschreiben, indem Sie diese unmittelbar nach der schließender Klammer <b>}</b> wie folgt angeben:
	<b>=(Einblendzeit,Ausblendzeit,Haltezeit)</b>. Die Werte sind in Sekunden anzugegeben.
	Sie können auch einzelne Werte überschreiben, mit <b>=(,,5.0)</b> z.B. wird nur die Haltezeit überschrieben.
<b>Beispiele</b>:';
// do not reformat these !
$txt['pmx_fader_content_help1'] = '{Ein einfacher Text<br />
  in zwei Zeilen.}
{
  <img src="url.tld/pfad/bildname.png" />
}
{ <a href="url.tld" target="_blank">
   Das ist ein Link
  </a> }=(1.5,1.5,4.0)';

// polls
$txt['pmx_select_polls'] = 'Wählen Sie die Umfragen, die Sie anzeigen wollen:';
$txt['pmx_polls_hint'] ='Wenn Sie mehr als eine Umfrage wählen, wird ein "Mehrfach" Umfrage Block mit eine Auswahl erstellt.';
$txt['pmx_no_polls'] = 'Keine Umfragen vorhanden';

// recent_posts/topics
$txt['pmx_recentpostnum'] = 'Anzahl der anzuzeigenden Beiträge:';
$txt['pmx_recenttopicnum'] = 'Anzahl der anzuzeigenden Themen:';
$txt['pmx_recent_boards'] = 'Wählen Sie die Boards die angezeigt werden sollen:';
$txt['pmx_recent_boards_help'] = 'Wälen Sie die Boards oder nichts um alle Boards anzuzeigen.';
$txt['pmx_recent_showboard'] = 'Board Namen anzeigen:';

// statistics
$txt['pmx_admstat_member'] = 'Zeige Mitglieder Statistik:';
$txt['pmx_admstat_stats'] = 'Zeige Beiträge und Online Statistik: ';
$txt['pmx_admstat_users'] = 'Zeige Benutzer Statistik: ';
$txt['pmx_admstat_spider'] = 'Zeige Spider in der Benutzer Statistik: ';
$txt['pmx_admstat_olheight'] = 'Sichtbare Benutzer in der Online Liste: ';
$txt['pmx_admstat_olheight_help'] = 'Sind mehr Benutzer online, wird die Liste mit eine Rollbalken dargestellt. Geben Sie 0 ein, um die Online Liste zu verbergen.';

// theme_select
$txt['pmx_select_themes'] ='Wählen Sie die Designs die Sie anzeigen wollen';
$txt['pmx_themes_hint'] ='Mit [x] markiert Designs sind im SMF nicht freigegeben.';

// user_login
$txt['show_avatar'] = 'Zeige Benutzerbild (wenn vorhanden):';
$txt['show_pm'] = 'Zeige Persönliche Nachrichten:';
$txt['show_posts'] = 'Zeige ungelesene Beiträge/Antworten:';
$txt['show_logtime'] = 'Zeige eingeloggte Zeit:';
$txt['show_unapprove'] = 'Zeige Ungeprüfte Benutzer:';
$txt['show_login'] = 'Zeige Login für Gäste:';
$txt['show_langsel'] = 'Zeige Sprachen Auswahl:';
$txt['show_logout'] = 'Zeige Logout Schaltfläche:';
$txt['show_time'] = 'Zeige aktuelle Zeit:';
$txt['show_realtime'] = 'Zeige aktuelle Zeit als Echtzeit Uhr:';
$txt['pmx_rtcformatstr'] = 'Echtzeit Uhr Format:';
$txt['pmx_rtc_formathelp'] = '
	Lassen Sie diese Option leer wenn Sie das Zeitformat aus den SMF Einstellungen bzw. des Benutzer Profils verwenden wollen.<hr />
	Folgende Zeichen können in der Format Zeichenkette verwendet werden:<br />
	&nbsp; %a - Gekürzter Wochentag (Ttt)<br />
	&nbsp; %A - Vollständiger Wochentag<br />
	&nbsp; %b - Gekürzter Monatsname (Mmm)<br />
	&nbsp; %B - Vollständiger Monatsname<br />
	&nbsp; %D* - Gleich wie %m/%d/%y<br />
	&nbsp; %d - Tag des Monats (01 bis 31)<br />
	&nbsp; %e* - Tag des Monats (1 bis 31)<br />
	&nbsp; %H - Uhrzeit im 24 Stunden Format (Bereich 00 bis 23)<br />
	&nbsp; %I - Uhrzeit im 12 Stunden Format (Bereich 01 bis 12)<br />
	&nbsp; %m - Monat (01 bis 12)<br />
	&nbsp; %M - Minute (00 bis 59)<br />
	&nbsp; %p - Entweder "am" oder "pm" in Abhängigkeit von der Uhrzeit<br />
	&nbsp; %R* - Uhrzeit im 24 Stunden Format<br />
	&nbsp; %S - Sekunden (00 bis 59)<br />
	&nbsp; %T* - Aktuelle Zeit, Identisch mit %H:%M:%S<br />
	&nbsp; %y - 2 stellige Jahreszahl (00 bis 99)<br />
	&nbsp; %% - Das Symbol \'%\' als Zeichen<br /><br />
	&nbsp; * - Nicht Unterstützt.';

// boardnews/newposts/promoted posts
$txt['pmx_promoted_selposts'] = 'Anzeige durch Beiträge selektieren:';
$txt['pmx_promoted_selboards'] = 'Anzeige durch Boards selektieren:';
$txt['pmx_promoted_posts'] = 'Wählen Sie die anzuzeigenden Beiträge:';
$txt['pmx_boardnews_boards'] = 'Wählen Sie das Board für die anzuzeigenden Board News:';
$txt['pmx_postnews_boards'] = 'Wählen Sie die Boards für die anzuzeigenden Beiträge:';
$txt['pmx_multbonews'] = 'Max. Anzahl Beiträge in jedem Board:';
$txt['pmx_boponews_total'] = 'Anzahl der anzuzeigenden Beiträge:';
$txt['pmx_boponews_split'] = 'Beiträge in zwei Spalten anzeigen:';
$txt['pmx_boponews_rescale'] = 'Größe von eingefügten Bildern:';

$txt['pmx_boponews_rescalehelp'] = 'Eingefügte Bilder können in der Größe verändert oder entfernt werden. Geben Sie die maximale Größe an (Pixel) oder 0 um die Bilder zu entfernen.
	Wenn Sie die Bilder nicht verändern wollen, lassen Sie dieses Feld leer.';
$txt['pmx_boponews_showthumbs'] = 'Zeige Miniaturbilder unter dem Beitrag:';
$txt['pmx_boponews_hidethumbs'] = 'Miniaturbilder Bereich zusammenklappen:';
$txt['pmx_boponews_hidethumbshelp'] = 'Wenn gewählt, wird der Bereich für die Miniaturbilder zuammengeklappt und kann bei jedem Beitrag manuell aufgeklappt werden.';
$txt['pmx_boponews_thumbcnt'] = 'Anzahl der Miniaturbilder:';
$txt['pmx_boponews_thumbcnthelp'] = 'Geben Sie die Anzahl der Miniaturbilder an, die angezeigt werden sollen. Lassen Sie diese Option leer, wenn alle Miniaturbilder angezeigt werden sollen.';
$txt['pmx_boponews_disableHS'] = 'HighSlide Viewer für Beiträge sperren:';
$txt['pmx_boponews_disableHSimage'] = 'HighSlide Viewer für Bilder sperren:';
$txt['pmx_boponews_page'] = 'Anzahl Beiträge in einer Seite:';
$txt['pmx_boponews_equal'] = 'Spalten auf gleiche Höhe setzen:';
$txt['pmx_boponews_postinfo'] = 'Zeige Kopfzeile (Geschrieben von, Board):';
$txt['pmx_boponews_postviews'] = 'Gelesen/Antworten zur Kopfzeile hinzufügen:';

// rss_reader
$txt['pmx_rssreader_url'] = 'Vollständige URL für den Feed:';
$txt['pmx_rssreader_urlhelp'] = 'Für SMF Foren verwenden Sie folgendes:<br />
	<b>forumurl?action=.xml;<i>optionen</i></b><br />
	<i>Optionen:</i> &nbsp;type=s;sa=s;boards=n;limit=n;<br />
	&nbsp; type: <b>rss</b> | <b>rss2</b> | <b>rdf</b> | <b>atom</b><br />
	&nbsp; sa: <b>recent</b> | <b>news</b> | <b>members</b><br />
	&nbsp; boards: <b>#[,#,#]</b> (# ist die Board Nummer)<br />
	&nbsp; limit: <b>#</b> (# ist eine Zahl 1 bis n)<br />
	<i>Vorgabe: </i>sa=recent';
$txt['pmx_rssreader_timeout'] = 'Feed Antwort Ablaufzeit (Sek):';
$txt['pmx_rssreader_timeouthelp'] = 'Das lesen des Feed wird nach dieser Zeit angehalten, wenn keine Daten empfangen wurden. (Vorgabe: <b>5</b> Sekunden)';
$txt['pmx_rssreader_usettl'] = 'Cache Zeit automatisch von TTL setzen:';
$txt['pmx_rssreader_usettlhelp'] = 'Wenn gewählt, wird das Cache freigegeben und auf die im TTL (Time To Life) übertragene Zeit gesetzt.';
$txt['pmx_rssreader_maxitems'] = 'Max. Anzahl der Beiträge:';
$txt['pmx_rssmaxitems_help'] = 'Geben Sie die maximale Anzahl von Beiträgen an oder lassen Sie diese Option leer um alle Beiträge zu sehen.';
$txt['pmx_rssreader_cont_encode'] = '"content:encoded" verwenden, wenn gesendet:';
$txt['pmx_rssreader_cont_encodehelp'] = 'Wenn gewählt und der Feed diese Option sendet (viele tun das), sehen Sie i.d.R. eine längeren Inhalt der auch Bilder enthalten kann.';
$txt['pmx_rssreader_split'] = 'Beiträge in zwei Spalten anzeigen:';
$txt['pmx_rssreader_showhead'] = 'Den Feed Kopf anzeigen:';
$txt['pmx_rssreader_help'] = 'Die folgende Einstellungen werden nur verwendet, wenn kein Feed Kopf gesendet wurde. Beachten Sie, das SMF Foren keinen Kopf senden!';
$txt['pmx_rssreader_name'] = 'Seiten Name:';
$txt['pmx_rssreader_link'] = 'Seiten Link:';
$txt['pmx_rssreader_desc'] = 'Beschreibung:';
$txt['pmx_rssreader_delimages'] = 'Eingefügte Bilder entfernen:';
$txt['pmx_rssreader_delimagehelp'] = 'Wenn gewählt, werden eingefügte Bilder und Objekte entfernt.';
$txt['pmx_rssreader_page'] = 'Anzahl Beiträge in einer Seite:';
$txt['pmx_rsspageindex_help'] = 'Anzahl von Beiträgen, die in einer Seite dargestellt werden sollen.';

// shoutbox
$txt['pmx_shoutbox_maxlen'] = 'Maximale Zeichen in einem Shout:';
$txt['pmx_shoutbox_maxshouts'] = 'Anzahl anzuzeigende Shouts:';
$txt['pmx_shoutbox_maxshouthelp'] = 'Geben Sie die Anzahl der anzuzeigenden Shouts ein. Beachten Sie, das ältere Shouts automatisch gelöscht werden, wenn dieser Wert überschritten wird.';
$txt['pmx_shoutbox_maxheight'] = 'Maximal Höhe der Shout Box (Pixel):';
$txt['pmx_shoutbox_scrollspeed'] = 'Shout Box Roll-Geschwindigkeit:';
$txt['pmx_shoutbox_speedhelp'] = 'Nach dem bearbeiten oder bei "Shouts Umkehren", wird der bearbeitete bzw. der letzte Shout nach unten gerollt.
	Hier können Sie die Geschwindigkeit in ~0,1 Sekunden Einheiten angeben. Wenn Sie diesen Wert auf 0 setzen, wird die Position unmittelbar gesetzt.';
$txt['pmx_shoutbox_collapse'] = 'Das Eingabefeld zu Anfang zuklappen:';
$txt['pmx_shoutbox_collapsehelp'] = 'Wenn gewählt ist das Eingabefeld nicht sichtbar. Es wird aufgeklappt wenn auf die Schaltfläche "<b>Shout?</b>" geklickt wird.';
$txt['pmx_shoutbox_reverse'] = 'Shouts Umkehren (letzter Shout unten):';
$txt['pmx_shoutbox_allowedit'] = 'Benutzer können eigene Shouts bearbeiten und löschen:';
$txt['pmx_shoutbox_canshout'] = 'Wählen Sie die Benutzergruppen die Shouts erstellen können';

// Category
$txt['pmx_catblock_cats'] = 'Wählen Sie die Kategorie:';
$txt['pmx_catblock_blockframe'] = 'Titelzeile und Rahmen dieses Blocks verwenden:';
$txt['pmx_catblock_catframe'] = 'Titelzeile und Rahmen der Kategorie verwenden:';
$txt['pmx_catblock_inherit'] = 'Anzeige Berechtigungen an die Kategorie vererben:';
$txt['pmx_catblock_inherithelp'] = 'Wenn gewählt, werden die Anzeige Berechtigungen des Blocks an die Kategorie übertragen.
	Das erfolgt in jedem Fall, auch wenn die Berechtigungen der Kategorie höher sind als die des Blocks.<br />
	Beachten Sie, das die Anzeige Berechtigungen nicht an die Artikel in der Kategorie vererbt werden. Die Artikel können nur die eingestellten Anzeige Berechtigungen der Kategorie erben.';

// Article
$txt['pmx_artblock_arts'] = 'Wählen Sie den Artikel:';
$txt['pmx_artblock_blockframe'] = 'Titelzeile und Rahmen dieses Blocks verwenden:';
$txt['pmx_artblock_artframe'] = 'Titelzeile und Rahmen des Artikels verwenden:';
$txt['pmx_artblock_inherit'] = 'Anzeige Berechtigungen an den Artikel vererben:';
$txt['pmx_artblock_inherithelp'] = 'Wenn gewählt, werden die Anzeige Berechtigungen des Blocks an den Artikel übertragen.
	Das erfolgt in jedem Fall, auch wenn die Berechtigungen des Artikels höher sind als die des Blocks.';

// mini calendar
$txt['pmx_minical_firstday'] = 'Erster Tag der Woche:';
$txt['pmx_minical_firstdays'] = array(
	0 => 'Sonntag',
	1 => 'Montag',
	6 => 'Samstag');
$txt['pmx_minical_birthdays'] = 'Zeige Geburtstage:';
$txt['pmx_minical_holidays'] = 'Zeige Feiertage:';
$txt['pmx_minical_events'] = 'Zeige Ereignisse:';
$txt['pmx_minical_bdays_before'] = 'Tage vor Heute:';
$txt['pmx_minical_bdays_after'] = 'Tage nach Heute:';

// common for teaser
$txt['pmx_adm_teaser'] = 'Anzahl der %s vor der Beitragskürzung:';
$txt['pmx_adm_teasehelp'] = 'Geben Sie 0 ein, wenn Sie keine Beitragskürzung wünschen.';

// common for pages
$txt['pmx_pageindex_pagetop'] = 'Zeige Seitenindex auch oben:';
$txt['pmx_pageindex_help'] = 'Geben Sie die Zahl der Beiträge ein, die in einer Seite dargestellt werden sollen.
	Wenn die Anzahl der Beiträge größer als dieser Wert ist, wird der Seitenindex angezeigt.
	Lassen Sie diese Option leer (oder geben Sie 0 ein) wenn Sie keine Seiten Begrenzung wollen.';
$txt['pmx_pageindex_tophelp'] = 'Wenn gewählt wird der Seitenindex auch oben angezeigt, andernfalls wird der Seitenindex nur unten angezeigt.';
?>
