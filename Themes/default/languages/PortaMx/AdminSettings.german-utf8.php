<?php
/**
* \file AdminSettings.german-utf8.php
* Language file AdminSettings.german-utf8
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.2
* \date 09.02.2020
*/

// AdminSettings
$txt['pmx_admSet_globals'] = 'Globale Einstellungen';
$txt['pmx_admSet_panels'] = 'Panel Einstellungen';
$txt['pmx_admSet_front'] = 'Frontpage Einstellungen';
$txt['pmx_admSet_control'] = 'Verwaltungs Einstellungen';
$txt['pmx_admSet_access'] = 'Berechtigung Einstellungen';
$txt['pmx_admSet_pmxsef'] = 'SEF Modul Einstellungen';

$txt['pmx_admSet_desc_global'] = 'Konfigurieren der globalen Einstellungen.';
$txt['pmx_admSet_desc_panel'] = 'Konfigurieren der Panel Einstellungen.';
$txt['pmx_admSet_desc_front'] = 'Konfigurieren der Frontpage Einstellungen.';
$txt['pmx_admSet_desc_control'] = 'Konfigurieren der Verwaltungs Einstellungen.';
$txt['pmx_admSet_desc_access'] = 'Konfigurieren der PortaMx Admin, PortaMx Moderator und Artikel Berechtigungen.';
$txt['pmx_admSet_desc_pmxsef'] = 'Konfigurieren der Suchmaschinen freundlichen URL (SEF).';

// global
$txt['pmx_global_settings'] = 'Global Einstellungen';
$txt['pmx_settings_panelpadding'] = 'Freiraum zwischen den Panelen:';
$txt['pmx_settings_paneloverflow'] = 'Aktion bei Panelbreite Überlauf:';
$txt['pmx_settings_download'] = 'Download Schaltfläche in der Menübar:';
$txt['pmx_settings_download_action'] = 'Aktions Name für die Download Schaltfläche:';
$txt['pmx_settings_download_acs'] = 'Benutzergruppen welche die  Download Schaltfläche sehen können:';
$txt['pmx_settings_other_actions'] = 'Anfragen die wie Forum Anfragen behandelt werden:';
$txt['pmx_settings_blockcachestats'] = 'Zeige den PMX-Cache Status im Fußbereich des Forums:';
$txt['pmx_settings_hidecopyright'] = 'Code Key zum entfernen des PortaMx Copyrights:';
$txt['pmx_settings_enable_xbars'] = 'Panele die mit <b>xBars</b> Ein-/Ausgeklappt werden können:';
$txt['pmx_settings_all_toggle'] = 'Alle zwischen Ein/Aus wechseln';
$txt['pmx_settings_enable_xbarkeys'] = 'Panel mit <b>xBarKeys</b> Ein-/Ausklappen:';
$txt['pmx_settings_collapse_visibility'] = 'Einklappen der <b>Dynamischen Anzeige Optionen</b>:';
$txt['pmx_settings_disableHS'] = 'HighSlide Anzeige global deaktivieren:';
$txt['pmx_settings_noHS_onfrontpage'] = 'Deaktivieren der HighSlide Anzeige nur für die Frontpage:';
$txt['pmx_settings_noHS_onaction'] = 'Deaktivieren von HighSlide bei Aktionen:';
$txt['pmx_settings_mainoverflow'] = 'Forum Bereich bei Überlauf horizontal verschieben:';
$txt['pmx_settings_postcountacs'] = 'Beitrags Basierende Gruppen für Berechtigungen verwenden:';
$txt['pmx_settings_shrinkimages'] = 'Handlung für das Ein-/Ausblenden von Blöcken und Panelen:';
$txt['pmx_settings_shrink'] = array(
	0 => 'Klicken auf das Standard Bild',
	1 => 'Klicken auf das Bild des Designs',
	2 => 'Kein Bild, Doppelklick auf die Titelzeile');

// ecl
$txt['pmx_settings_ecl'] = 'Aktivieren der ECL Betriebsart:';
$txt['pmx_settings_eclhelp'] = 'Diese Einstellung macht SMF und PortaMx kompatibel mit dem <b>EU-Cookie-Gesetz</b>.<br >
	Ist diese Einstellung aktiviert, muss jeder Besucher (Spider ausgenommen) der Speicherung von Cookies zustimmen bevor er auf der Webseite navigieren kann.<br />
	Weitere Informationen (englisch) finden Sie auf <a href="http://ec.europa.eu/ipg/standards/cookies/index_en.htm" target="_blank">Europäischen Kommission</a>';
$txt['pmx_settings_eclmodal'] = 'Nicht Modalen ECL Modus verwenden:';
$txt['pmx_settings_eclhelpmodal'] = 'Beim Modalen Modus ist Portal und Forum nicht zugänglich, bis ECL akzeptiert wird.
	Wenn Sie den nicht Modalen Modus aktivieren, ist Portal und Forum zugänglich und es wird ein kleines ECL Overlay an oberen Rand der Seite angezeigt.
	<b>Beachten Sie, das in diesem Fall andere Modifikationen oder Adsense Inhalte Cookies speichern können!</b><br />
	Diese Einstellung hat keine Wirkung bei WAP/WAP2/IMODE oder wenn ein Mobiles Geräte erkannt wurde.';

// panels
$txt['pmx_panel_settings'] = 'Panel Einstellungen';
$txt['pmx_settings_panelset'] = 'Einstellungen';
$txt['pmx_settings_panelhead'] = 'Kopf Panel';
$txt['pmx_settings_panelleft'] = 'Linkes Panel';
$txt['pmx_settings_panelright'] = 'Rechtes Panel';
$txt['pmx_settings_paneltop'] = 'Oberes zentriertes Panel';
$txt['pmx_settings_panelbottom'] = 'Unteres zentriertes Panel';
$txt['pmx_settings_panelfoot'] = 'Fuß Panel';
$txt['pmx_settings_panelhidetitle'] = 'Verbergen des Panels bei Option:';
$txt['pmx_settings_panel_customhide'] = 'Verbergen des Panels bei Aktion:';
$txt['pmx_settings_panel_collapse'] = 'Panel Einklappen sperren:';
$txt['pmx_settings_panelwidth'] = 'Breite des Panels:';
$txt['pmx_settings_panelheight'] = 'Max. Höhe des Panels:';
$txt['pmx_pixel'] = 'Pixel';

$txt['pmx_settings_collapse_state'] = 'Panel Anfangs Status:';
$txt['pmx_settings_collapse_mode'] = array(
	0 => 'Vorgabe',
	1 => 'Eingeklappt',
	2 => 'Ausgeklappt'
);
$txt['pmx_hw_pixel'] = array(
	'head' => 'Pixel oder Leer',
	'top' => 'Pixel oder Leer',
	'bottom' => 'Pixel oder Leer',
	'foot' => 'Pixel oder Leer',
	'left' => 'Pixel',
	'right' => 'Pixel'
);
$txt['pmx_settings_hidehelp'] = 'Zum Ausblenden des Panels wählen Sie eine oder mehrere Optionen durch haltender der <b>Strg-Taste</b> und <b>klicken</b> auf eine Option.<br />
	Zum wechseln zwischen <b>Anzeigen</b> und <b>Verbergen</b>, halten Sie die <b>Strg-Taste</b> und <b>doppelklicken</b> (IE braucht drei Klicks!) Sie auf eine Option.
	Bei <b>Verbergen</b> erscheint das Symbol <b>^</b> vor der Option.<br />
	<b>Beispiel</b>: Bei "<i>option</i>" wird das Panel verborgen, bei "^<i>option</i>" wird das Panel angezeigt, wenn Sie die <i>option</i> Aktion ausführen';

// frontpage
$txt['pmx_frontpage_settings'] = 'Frontpage Einstellungen';
$txt['pmx_settings_frontpage_none'] = 'Keine Frontpage, direkt zum Forum:';
$txt['pmx_settings_frontpage_centered'] = 'Frontpage im Forumbereich anzeigen:';
$txt['pmx_settings_frontpage_fullsize'] = 'Eine Vollbild Frontpage anzeigen:';
$txt['pmx_settings_pages_hidefront'] = 'Frontpage Blöcke bei Seiten, Kategorien oder Artikeln verbergen:';
$txt['pmx_settings_frontpage_menubar'] = 'Menüleiste bei der Vollbild Frontpage anzeigen:';
$txt['pmx_settings_index_front'] = 'Indizieren der Frontpage durch Spider erlauben:';
$txt['pmx_settings_sendfragment'] = 'Block am oberen Rand der Seite Positionieren:';
$txt['pmx_settings_restoretop'] = 'Vertikale Position der Seite wieder herstellen:';
$txt['pmx_settings_fronttheme'] = 'Wählen Sie ein Design für die Frontpage:';
$txt['pmx_settings_frontthemepages'] = 'Diese Design auch für Seiten, Kategorien und Artikel verwenden:';
$txt['pmx_front_default_theme'] = '[ Forum Standard ]';

// manager control
$txt['pmx_global_program'] = 'Verwaltungs Einstellungen';
$txt['pmx_settings_blockfollow'] = 'Bei Änderungen folge dem Block durch die Panele:';
$txt['pmx_settings_quickedit'] = 'Verwende die Block Titelzeile für einen <b>Bearbeitungs</b> Link:';
$txt['pmx_settings_adminpages'] = 'Panele auf die ein <b>Block Moderator</b> Zugriff hat:';
$txt['pmx_settings_article_on_page'] = 'Anzahl der Artikel in der Verwaltungs Übersicht Seite:';
$txt['pmx_settings_enable_promote'] = 'Publizieren von Beiträgen erlauben:';
$txt['pmx_settings_promote_messages'] = 'Zur Zeit publizierte Beiträge:';

// access settings
$txt['pmx_access_settings'] = 'Berechtigung Einstellungen';
$txt['pmx_access_promote'] = 'Benutzergruppen die Beiträge publizieren können:';
$txt['pmx_access_articlecreate'] = 'Artikel erstellen und schreiben (Artikel Ersteller):';
$txt['pmx_access_articlemoderator'] = 'Artikel Moderieren und Freigeben (Artikel Moderator):';
$txt['pmx_access_blocksmoderator'] = 'Blöcke in freigegebenen Panelen moderieren (Block Moderator):';
$txt['pmx_access_pmxadmin'] = 'Portal administrieren (Portal Administrator):';

// pmxsef settings
$txt['pmx_sef_engine'] = '<b>Das SEF Modul benötigt mod_rewrite oder Url Rewrite/web.config (IIS7) Unterstützung.</b>';
$txt['pmx_sef_settings'] = 'Suchmaschinen Freundliche URL (SEF) Einstellungen';
$txt['pmx_sef_enable'] = 'Freigabe des SEF Moduls:';
$txt['pmx_sef_lowercase'] = 'Kleinschreibung für alle URLs:';
$txt['pmx_sef_autosave'] = 'Neue Aktionen automatisch speichern:';
$txt['pmx_sef_spacechar'] = 'Zeichen das für Leerzeichen benutzt wird:';
$txt['pmx_sef_stripchars'] = 'Zeichen die aus der URL entfernt werden:';
$txt['pmx_sef_wirelesss'] = 'Alle WIRELESS Symbol Namen:';
$txt['pmx_sef_single_token'] = 'Alle Einzelwort Symbol Namen:';
$txt['pmx_sef_actions'] =  'Alle Aktionen des Forums:';
$txt['pmx_sef_aliasurl'] =  'Alias URL\'s für Ihr Forum:';
$txt['pmx_sef_simplesef_space'] = 'Leerzeichen welches Sie für SimpleSEF verwendet haben:';
$txt['pmx_sef_engine_disabled'] = 'Das SEF Modul ist zur Zeit ausgeschaltet!';
$txt['pmx_sef_ignoreactions'] =  'Aktionen die ignoriert werden:';
$txt['pmx_sef_aliasactions'] =  'Alias für Aktionen:';
$txt['pmx_sef_ignorerequests'] =  'Teile einer URL die ignoriert wird:';

// DO NOT CHANGE ANY OF THIS !!
$txt['pmx_sef_engine_APcode'] = 'RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?q=$1 [L,QSA]';

$txt['pmx_sef_engine_IScode'] = '<?xml version="1.0" encoding="UTF-8"?>
<configuration>
 <system.webServer>
  <rewrite>
   <rules>
    <rule name="PortaMxSEF">
     <match url="^(.*)$" ignoreCase="false" />
     <conditions logicalGrouping="MatchAll">
      <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" pattern="" ignoreCase="false" />
      <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" pattern="" ignoreCase="false" />
     </conditions>
     <action type="Rewrite" url="index.php?q={R:1}" appendQueryString="true" />
    </rule>
   </rules>
  </rewrite>
 </system.webServer>
</configuration>';
// END NO CHANGE

// help;
$txt['pmx_sef_engine_APIS_copy'] = 'Klicken um den Text zu selektieren, dann den Text mit Strg-Einfg kopieren.';
$txt['pmx_sef_enable_help'] = 'Wenn sich das SEF Modul nicht aktivieren lässt, prüfen Sie ob die Datei <b>.htaccess</b> oder <b>web.config</b> an der richtigen Stelle vorhanden ist.';
$txt['pmx_sef_engine_helpAP'] = 'Wenn Sie einen Apache Webserver verwendet der die mod_rewrite Funktionalität hat, benötigen Sie eine <b>.htaccess</b> Datei in Ihrem SMF Verzeichnis mit folgendem Inhalt:';
$txt['pmx_sef_engine_helpIS'] = '<br />Wenn Sie einen IIS7 Webserver verwenden, benötigen Sie eine <b>web.config</b> Datei in Ihrem SMF Verzeichnis mit folgenden Inhalt:';
$txt['pmx_sef_lowercase_help'] = 'Wenn gewählt, werden alle URL\'s in Kleinbuchstaben umgewandelt.
	Für bessere Resultate sollten Sie diese Einstellung aktivieren.';
$txt['pmx_sef_autosave_help'] = 'Wenn gewählt, werden neuen Aktionen automatisch gespeichert.
	Es wird empfohlen diese Einstellung nicht zu aktivieren und neue Aktionen manuell einzutragen.
	Existiert eine Aktion nicht, wird die Frontpage angezeigt.';
$txt['pmx_sef_spacechar_help'] = 'Zeichen welches für Leerzeichen verwendet werden soll (Typisch _ oder -).
	Verwenden Sie das Zeichen <b>-</b> für bessere Resultate. Lassen Sie das Feld leer um Leerzeichen zu entfernen.';
$txt['pmx_sef_stripchars_help'] = 'Diese Zeichen werden aus der URL entfernt.
	Jedes Zeichen muss durch ein Komma getrennt werden.
	<b>Wenn Sie Änderungen durchführen, ist es möglich, das Ihr Forum nicht mehr ordnungsgemäß arbeitet.<b>';
$txt['pmx_sef_wirelesss_help'] = 'Diese Symbolnamen werden im WIRELESS Modus verwendet.
	Jeder Eintrag muss durch ein Komma getrennt werden.
	<b>Wenn Sie Änderungen durchführen, ist es möglich, das Ihr Forum nicht mehr ordnungsgemäß arbeitet.<b>';
$txt['pmx_sef_single_token_help'] = 'Symbole denen kein Wert zugewiesen wird und die eine spezielle Behandlung benötigen.
	Jeder Eintrag muss durch ein Komma getrennt werden.
	<b>Wenn Sie Änderungen durchführen, ist es möglich, das Ihr Forum nicht mehr ordnungsgemäß arbeitet.<b>';
$txt['pmx_sef_actions_help'] = 'Diese sind alle Aktionen, die im Forum verwendet werden. Normalerweise müssen Sie hier nichts ändern, da das SEF Modul neue Aktionen automatisch hinzufügt.
	Jeder Eintrag muss durch ein Komma getrennt werden.
	<b>Wenn Sie Änderungen durchführen, ist es möglich, das Ihr Forum nicht mehr ordnungsgemäß arbeitet.<b>';
$txt['pmx_sef_simplesef_space_help'] = 'Wenn Sie vorher die Modifikation SimpleSEF verwendet haben, tragen Sie hier das Zeichen ein, welches Sie für das Leerzeichen definiert haben (Typisch _).
	Diese Einstellung wird benötigt, um SimpleSEF "simple urls" (wie <i>topic_##, board_##</i>) nach PortaMxSEF zu konvertieren.
	Lassen Sie die Einstellung leer, wenn Sie SimpleSEF nicht verwendet haben.';
$txt['pmx_sef_ignoreactions_help'] =  'Aktionen die nicht durch das SEF Modul konvertiert werden. Jeder Eintrag muss durch ein Komma getrennt werden.';
$txt['pmx_sef_aliasactions_help'] =  'Sie können einen Alias für jede Aktion definieren. Jeder Alias muss im Format <b>aktion=alias</ b> angegeben werden.
	Jedes <b>aktion=alias</b> Paar muss durch ein Komma getrennt werden.';
$txt['pmx_sef_ignorerequests_help'] =  'Teile einer URL die nicht durch das SEF Modul konvertiert wird. Jeder Teil muss im Format <b>name=wert</b> angegeben werden.
	Jedes <b>name=wert</b> Paar muss durch ein Komma getrennt werden.';
$txt['pmx_settings_index_front_help'] = 'Wenn gewählt, kann der Inhalt der Frontpage durch Spider (z.B. Google) indiziert werden.';
$txt['pmx_settings_sendfragment_help'] = 'Die Seite wird bei einigen Anfragen (z.B. neue Seitennummer, neue Kategorie oder neuer Artikel) so positioniert, das der Block am oberen Rand dargestellt wird (durch senden eines "#top" Fragments).';
$txt['pmx_access_promote_help'] = 'Benutzer in den gewählen Gruppen können Beiträge im Forum publizieren.<br />
	<b>Erteilte Rechte:</b> <i>Hinzufügen und entfernen der Publizierung in Beiträgen</i>';

$txt['pmx_settings_restoretop_help'] = 'Die vertikale Position der Seite wird bei einigen Anfragen (z.B. neue Seitennummer, neue Kategorie oder neuer Artikel) auf die vorherige Position gestellt.';
$txt['pmx_access_articlecreate_help'] = 'Benutzer in den gewählten Gruppen können neue Artikel erstellen und eigene Artikel bearbeiten und löschen.
	Neue Artikel müssen durch einen Artikel Moderator oder dem Administrator freigegeben werden.<br />
	<b>Erteilte Rechte:</b> <i>Neue Artikel erstellen, eigene Artikel bearbeiten, duplizieren, löschen, aktivieren/deaktivieren</i>';
$txt['pmx_access_articlemoderator_help'] = 'Benutzer in den gewählten Gruppen können Artikel bearbeiten, sperren und freigeben, wenn diese für die <b>Artikel Moderation</b> freigegeben sind.
	Diese ist immer der Fall, wenn ein Artikel durch einen Benutzer der <i>Artikel Ersteller</i> Gruppe erstellt wurde.<br />
	<b>Erteilte Rechte:</b> <i>Artikel bearbeiten, aktivieren/deaktivieren, freigeben/sperren</i>';
$txt['pmx_access_blocksmoderator_help'] = 'Benutzer in den gewählten Gruppen können Blöcke bearbeiten, die für die <b>Block Moderation</b> freigegeben sind.
	Der Zugriff ist weiterhin auf die freigegebenen Panele beschränkt (Siehe auch Verwaltungs Einstellungen).<br />
	<b>Erteilte Rechte:</b> <i>Block Inhalt, Rechte, Titel und CSS Einstellungen bearbeiten, aktivieren/deaktivieren</i>';
$txt['pmx_access_pmxadmin_help'] = 'Benutzer in den gewählten Gruppen haben <b>vollen</b> Zugriff auf <b>alle</b> Funktionen und Einstellungen des Portals.
	Diese Benutzer haben die gleichen Rechte wie ein Forum Administrator, begrenzt auf das Portal. <b>Verwende Sie diese Einstellung mit größter Vorsicht!</b>';
$txt['pmx_settings_noHS_onactionhelp'] = 'Hier können Sie Aktionen eintragen, bei denen die HighSlide Anzeige deaktiviert werden soll.
	Für die <b>SMF Media Gallery</b> z.B. verwenden Sie den Eintrag <b>mgallery</b>.';
$txt['pmx_frontpage_help'] = 'Wählen Sie die Frontpage, welche Sie verwenden möchten.<br />
	Bachten Sie, das die Vollbild Frontpage normalerweise keine Menüzeile hat, aber Sie können eine einfache Menüzeile aktivieren.<br />
	Seiten, Kategorien und Artikel werden auch dann angezeigt, wenn Sie "Keine Frontpage" gewählt haben.<br />
	Wenn Sie zusätzliche CSS Klassen für die Vollbild Frontpage benötigen, erstellen Sie die Datei (<b>frontpage.css</b>) und speichern Sie diese im Verzeichnis des Designs.';
$txt['pmx_settings_adminpageshelp'] = 'Benutzer in der Gruppe <b>Block Moderator</b> können die Einstellungen und den Inhalt der Blöcke in den gewählten Panelen bearbeiten.<br />
	<b>Verwenden Sie diese Einstellung mit größter Vorsicht!</b>';
$txt['pmx_settings_xbars_help'] = 'Wählen Sie die Panele, welche mit den xBars ein- und ausgeklappt werden können.';
$txt['pmx_settings_collapse_vishelp'] = 'Diese Panel wird für die Dynamischen Block Einstellungen verwendet. Sie können dieses eingeklappt lassen, es wird automatisch ausgeklappt, wenn Dynamische Einstellungen vorhanden sind.';
$txt['pmx_settings_xbarkeys_help'] = 'Wenn gewählt, können Sie das linke, rechte, obere und das untere Panel mit der <b>Strg</b> Taste und eine der Pfeiltasten (<b>Links, Rechts, Hoch, Runter</b>) und das Kopf und Fuß Panel mit der <b>Alt</b> Taste und den Pfeiltasten (<b>Hoch, Runter</b>) Ein-/Ausklappen. Die <b>xBarKeys</b> werden automatisch gesperrt, der ein Editor geladen wird.';
$txt['pmx_settings_blockcachestatshelp'] = 'Wenn gewählt wird der PMX-Cache Status oberhalb der Seiten Ladezeit angezeigt.';
$txt['pmx_settings_hidecopyrighthelp'] =  'Geben Sie den Code Key ein, den Sie empfangen haben. Wenn der Key für Ihre Domain gültig und nicht abgelaufen ist, wird das PortaMx Copyright nicht angezeigt.
	Verwenden Sie Kopieren und Einfügen (der Key ist länger als das Eingabefeld) um den Code korrekt einzugeben.';
$txt['pmx_settings_panel_custhelp'] = 'Hier könne Sie andere Aktionen eingeben.
	Für Seiten, Artikel und Kategorien wird ein Präfix verwendet (<b>p:</b> für Seiten, <b>a:</b> für Artikel und <b>c:</b> für Kategorien).
	Stellen Sie das Präfix vor den Namen der Seite, des Artikels oder der Kategorie, z.B. <b>p:meine_seite</b>.
	Die Namen können die Platzhalter <b>*</b> and <b>?</b> enthalten. Das Panel ist nicht sichtbar, wenn der Name zu der Aktion passt.
	Weiterhin können Sie Subaktionen verwenden, diese beginnen immer mit dem Kaufmännischem UND (<b>&amp;</b>) z.B. <b>&amp;subaktionname=wert</b>.
	Weitere Informationen zu den Benutzerdefinierten Aktionen finden Sie in unseren Dokumentationen und im Support Forum.';
$txt['pmx_settings_downloadhelp'] = 'Wenn gewählt wird eine <b>Download</b> Schaltfläche neben der <b>Community</b> Schaltfläche angezeigt.';
$txt['pmx_settings_dl_actionhelp'] = 'Defieren Sie die Aktion, die der Download Schaltfläche zugeordet werden soll.
	Sie können beliebige Namen mit den Zeichen (<b>a-z, A-Z, 0-9, -, _, .</b>) verwenden.
	Wenn Sie eine Seite, einen Artikel oder eine Kategorie verwenden möchten, müssen sie dem Namen ein Präfix voranstellen (<b>p:</b> für Seiten, <b>a:</b> für Artikel und <b>c:</b> für Kategorien) z.B. <b>p:download</b>';
$txt['pmx_settings_other_actionshelp'] = 'Hier könne Sie einen oder mehrere Namen (getrennt durch ein Komma) angeben, die als Forum Aktion gewertet werden.
	Sie müssen Namen in der Form <b>name=wert</b> verwenden, z.B.  <b>project=1</b> für das Project tool.';
$txt['pmx_settings_blockfollowhelp'] = 'Wenn Sie eine Änderung an einem Block vornehmen, einen Block verschieben oder duplizieren, wird das Panel in der Übersicht angezeigt in dem sich der Block nach der Änderung befindet.';
$txt['pmx_settings_quickedithelp'] = 'Wenn gewählt, ist der Blocktitel mit einen direkten Link zur <b>Bearbeitung des Blocks</b> verknüpft.
	Dieser Link ist nur für Administratoren und Portal Administratoren aktiviert.';
$txt['pmx_settings_pages_help'] = 'Geben Sie den Namen von Seiten, Artikeln oder Kategorien an (durch ein Komma getrennt), bei denen die Frontpage Blöcke NICHT angezeigt werden sollen.
	Lassen Sie diese Einstellung frei, wenn Sie die Frontpage Blöcke individuell mit den Block Einstellungen platzieren wollen.
	Für Seiten, Artikel und Kategorien müssen Sie dem Namen ein Präfix (<b>p:</b> für Seiten, <b>a:</b> für Artikel und <b>c:</b> für Kategorien) verwenden.
	Die Namen können die Platzhalter <b>*</b> und <b>?</b> enthalten.</b>.';
$txt['pmx_settings_article_on_pagehelp'] = 'Geben Sie die Anzahl der Artikel an, die Sie in der Artikel Verwaltungs Übersicht sehen wollen.';
$txt['pmx_settings_forumscrollhelp'] = 'Wenn der Bereich zwischen dem linken und rechten Panel zu breit ist, wird das rechte Panel normalerweise aus dem Bildbereich geschoben.
	Ist diese Option aktiviert, wird der mittlere Bereich (Forum Bereich) verkleinert und kann horizontal verschoben werden.<br />
	<b>Diese Option funktioniert NICHT mit IE kleiner der Version 8.</b>';
$txt['pmx_settings_postcountacshelp'] = 'Beitragsbasierende Gruppen zusätzlich zu den Regulären Gruppen für die Berechtigungen verwenden.';
$txt['pmx_settings_teasermode'] = array(
	0 => 'Wählen Sie die Zählmethode für die Beitrags Kürzung:',
	1 => 'Worte zählen',
	2 => 'Zeichen zählen'
);
$txt['pmx_settings_pmxteasecnthelp'] = 'In verschiedenen Blöcken und in Artikeln kann eine <i>Beitrags Kürzung</i> aktiviert werden.
	Hier können Sie einstellen, wie diese Option arbeiten soll.
	Für Sprachen die kein Leerzeichen kennen (z.B. Japanisch) sollten Sie die Zählmethode <b>Zeichen zählen</b> wählen.';
$txt['pmx_settings_promote_messages_help'] = 'Sie sehen alle Publizierten Beitrags Nummern. Sie können Nummern entfernen oder neue hinzufügen (getrennt durch ein Komma).';
$txt['pmx_settings_enable_promote_help'] = 'Wenn gewählt ist die Beitrags Publizierung aktiv und <b>Administratoren</b> sehen den Link <b>Beitrag Publizieren</b> unter jedem Beitrag im Forum.
	Ist ein Beitrag bereits Publiziert, wird der Link <b>Publizieren entfernen</b> angezeigt.<br />
	Die Publizierten Beiträge können mit dem Block <i>Publizierte Beiträge</i> z.b. auf der Frontpage angezeigt werden.';
?>
