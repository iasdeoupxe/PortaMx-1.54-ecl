<?php
/**
* \file AdminSettings.german.php
* Language file AdminSettings.german
*
* \author PortaMx - Portal Management Extension
* \author Copyright 2008-2020 by PortaMx corp. - http://portamx.com
* \version 1.54.1
* \date 03.01.2020
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
$txt['pmx_settings_paneloverflow'] = 'Aktion bei Panelbreite &Uuml;berlauf:';
$txt['pmx_settings_download'] = 'Download Schaltfl&auml;che in der Men&uuml;bar:';
$txt['pmx_settings_download_action'] = 'Aktions Name f&uuml;r die Download Schaltfl&auml;che:';
$txt['pmx_settings_download_acs'] = 'Benutzergruppen welche die  Download Schaltfl&auml;che sehen k&ouml;nnen:';
$txt['pmx_settings_other_actions'] = 'Anfragen die wie Forum Anfragen behandelt werden:';
$txt['pmx_settings_blockcachestats'] = 'Zeige den PMX-Cache Status im Fu&szlig;bereich des Forums:';
$txt['pmx_settings_hidecopyright'] = 'Code Key zum entfernen des PortaMx Copyrights:';
$txt['pmx_settings_enable_xbars'] = 'Panele die mit <b>xBars</b> Ein-/Ausgeklappt werden k&ouml;nnen:';
$txt['pmx_settings_all_toggle'] = 'Alle zwischen Ein/Aus wechseln';
$txt['pmx_settings_enable_xbarkeys'] = 'Panel mit <b>xBarKeys</b> Ein-/Ausklappen:';
$txt['pmx_settings_collapse_visibility'] = 'Einklappen der <b>Dynamischen Anzeige Optionen</b>:';
$txt['pmx_settings_disableHS'] = 'HighSlide Anzeige global deaktivieren:';
$txt['pmx_settings_noHS_onfrontpage'] = 'Deaktivieren der HighSlide Anzeige nur f&uuml;r die Frontpage:';
$txt['pmx_settings_noHS_onaction'] = 'Deaktivieren von HighSlide bei Aktionen:';
$txt['pmx_settings_mainoverflow'] = 'Forum Bereich bei &Uuml;berlauf horizontal verschieben:';
$txt['pmx_settings_postcountacs'] = 'Beitrags Basierende Gruppen f&uuml;r Berechtigungen verwenden:';
$txt['pmx_settings_shrinkimages'] = 'Handlung f&uuml;r das Ein-/Ausblenden von Bl&ouml;cken und Panelen:';
$txt['pmx_settings_shrink'] = array(
	0 => 'Klicken auf das Standard Bild',
	1 => 'Klicken auf das Bild des Designs',
	2 => 'Kein Bild, Doppelklick auf die Titelzeile');

// ecl
$txt['pmx_settings_ecl'] = 'Aktivieren der ECL Betriebsart:';
$txt['pmx_settings_eclhelp'] = 'Diese Einstellung macht SMF und PortaMx kompatibel mit dem <b>EU-Cookie-Gesetz</b>.<br >
	Ist diese Einstellung aktiviert, muss jeder Besucher (Spider ausgenommen) der Speicherung von Cookies zustimmen bevor er auf der Webseite navigieren kann.<br />
	Weitere Informationen (englisch) finden Sie auf <a href="http://ec.europa.eu/ipg/standards/cookies/index_en.htm" target="_blank">Europ&auml;ischen Kommission</a>';
$txt['pmx_settings_eclmodal'] = 'Nicht Modalen ECL Modus verwenden:';
$txt['pmx_settings_eclhelpmodal'] = 'Beim Modalen Modus ist Portal und Forum nicht zug&auml;nglich, bis ECL akzeptiert wird.
	Wenn Sie den nicht Modalen Modus aktivieren, ist Portal und Forum zug&auml;nglich und es wird ein kleines ECL Overlay an oberen Rand der Seite angezeigt.
	<b>Beachten Sie, das in diesem Fall andere Modifikationen oder Adsense Inhalte Cookies speichern k&ouml;nnen!</b><br />
	Diese Einstellung hat keine Wirkung bei WAP/WAP2/IMODE oder wenn ein Mobiles Ger&auml;te erkannt wurde.';

// panels
$txt['pmx_panel_settings'] = 'Panel Einstellungen';
$txt['pmx_settings_panelset'] = 'Einstellungen';
$txt['pmx_settings_panelhead'] = 'Kopf Panel';
$txt['pmx_settings_panelleft'] = 'Linkes Panel';
$txt['pmx_settings_panelright'] = 'Rechtes Panel';
$txt['pmx_settings_paneltop'] = 'Oberes zentriertes Panel';
$txt['pmx_settings_panelbottom'] = 'Unteres zentriertes Panel';
$txt['pmx_settings_panelfoot'] = 'Fu&szlig; Panel';
$txt['pmx_settings_panelhidetitle'] = 'Verbergen des Panels bei Option:';
$txt['pmx_settings_panel_customhide'] = 'Verbergen des Panels bei Aktion:';
$txt['pmx_settings_panel_collapse'] = 'Panel Einklappen sperren:';
$txt['pmx_settings_panelwidth'] = 'Breite des Panels:';
$txt['pmx_settings_panelheight'] = 'Max. H&ouml;he des Panels:';
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
$txt['pmx_settings_hidehelp'] = 'Zum Ausblenden des Panels w&auml;hlen Sie eine oder mehrere Optionen durch haltender der <b>Strg-Taste</b> und <b>klicken</b> auf eine Option.<br />
	Zum wechseln zwischen <b>Anzeigen</b> und <b>Verbergen</b>, halten Sie die <b>Strg-Taste</b> und <b>doppelklicken</b> (IE braucht drei Klicks!) Sie auf eine Option.
	Bei <b>Verbergen</b> erscheint das Symbol <b>^</b> vor der Option.<br />
	<b>Beispiel</b>: Bei "<i>option</i>" wird das Panel verborgen, bei "^<i>option</i>" wird das Panel angezeigt, wenn Sie die <i>option</i> Aktion ausf&uuml;hren';

// frontpage
$txt['pmx_frontpage_settings'] = 'Frontpage Einstellungen';
$txt['pmx_settings_frontpage_none'] = 'Keine Frontpage, direkt zum Forum:';
$txt['pmx_settings_frontpage_centered'] = 'Frontpage im Forumbereich anzeigen:';
$txt['pmx_settings_frontpage_fullsize'] = 'Eine Vollbild Frontpage anzeigen:';
$txt['pmx_settings_pages_hidefront'] = 'Frontpage Bl&ouml;cke bei Seiten, Kategorien oder Artikeln verbergen:';
$txt['pmx_settings_frontpage_menubar'] = 'Men&uuml;leiste bei der Vollbild Frontpage anzeigen:';
$txt['pmx_settings_index_front'] = 'Indizieren der Frontpage durch Spider erlauben:';
$txt['pmx_settings_sendfragment'] = 'Block am oberen Rand der Seite Positionieren:';
$txt['pmx_settings_restoretop'] = 'Vertikale Position der Seite wieder herstellen:';
$txt['pmx_settings_fronttheme'] = 'W&auml;hlen Sie ein Design f&uuml;r die Frontpage:';
$txt['pmx_settings_frontthemepages'] = 'Diese Design auch f&uuml;r Seiten, Kategorien und Artikel verwenden:';
$txt['pmx_front_default_theme'] = '[ Forum Standard ]';

// manager control
$txt['pmx_global_program'] = 'Verwaltungs Einstellungen';
$txt['pmx_settings_blockfollow'] = 'Bei &Auml;nderungen folge dem Block durch die Panele:';
$txt['pmx_settings_quickedit'] = 'Verwende die Block Titelzeile f&uuml;r einen <b>Bearbeitungs</b> Link:';
$txt['pmx_settings_adminpages'] = 'Panele auf die ein <b>Block Moderator</b> Zugriff hat:';
$txt['pmx_settings_article_on_page'] = 'Anzahl der Artikel in der Verwaltungs &Uuml;bersicht Seite:';
$txt['pmx_settings_enable_promote'] = 'Publizieren von Beitr&auml;gen erlauben:';
$txt['pmx_settings_promote_messages'] = 'Zur Zeit publizierte Beitr&auml;ge:';

// access settings
$txt['pmx_access_settings'] = 'Berechtigung Einstellungen';
$txt['pmx_access_promote'] = 'Benutzergruppen die Beitr&auml;ge publizieren k&ouml;nnen:';
$txt['pmx_access_articlecreate'] = 'Artikel erstellen und schreiben (Artikel Ersteller):';
$txt['pmx_access_articlemoderator'] = 'Artikel Moderieren und Freigeben (Artikel Moderator):';
$txt['pmx_access_blocksmoderator'] = 'Bl&ouml;cke in freigegebenen Panelen moderieren (Block Moderator):';
$txt['pmx_access_pmxadmin'] = 'Portal administrieren (Portal Administrator):';

// pmxsef settings
$txt['pmx_sef_engine'] = '<b>Das SEF Modul ben&ouml;tigt mod_rewrite oder Url Rewrite/web.config (IIS7) Unterst&uuml;tzung.</b>';
$txt['pmx_sef_settings'] = 'Suchmaschinen Freundliche URL (SEF) Einstellungen';
$txt['pmx_sef_enable'] = 'Freigabe des SEF Moduls:';
$txt['pmx_sef_lowercase'] = 'Kleinschreibung f&uuml;r alle URLs:';
$txt['pmx_sef_autosave'] = 'Neue Aktionen automatisch speichern:';
$txt['pmx_sef_spacechar'] = 'Zeichen das f&uuml;r Leerzeichen benutzt wird:';
$txt['pmx_sef_stripchars'] = 'Zeichen die aus der URL entfernt werden:';
$txt['pmx_sef_wirelesss'] = 'Alle WIRELESS Symbol Namen:';
$txt['pmx_sef_single_token'] = 'Alle Einzelwort Symbol Namen:';
$txt['pmx_sef_actions'] =  'Alle Aktionen des Forums:';
$txt['pmx_sef_aliasurl'] =  'Alias URL\'s f&uuml;r Ihr Forum:';
$txt['pmx_sef_simplesef_space'] = 'Leerzeichen welches Sie f&uuml;r SimpleSEF verwendet haben:';
$txt['pmx_sef_engine_disabled'] = 'Das SEF Modul ist zur Zeit ausgeschaltet!';
$txt['pmx_sef_ignoreactions'] =  'Aktionen die ignoriert werden:';
$txt['pmx_sef_aliasactions'] =  'Alias f&uuml;r Aktionen:';
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
$txt['pmx_sef_enable_help'] = 'Wenn sich das SEF Modul nicht aktivieren l&auml;sst, pr&uuml;fen Sie ob die Datei <b>.htaccess</b> oder <b>web.config</b> an der richtigen Stelle vorhanden ist.';
$txt['pmx_sef_engine_helpAP'] = 'Wenn Sie einen Apache Webserver verwendet der die mod_rewrite Funktionalit&auml;t hat, ben&ouml;tigen Sie eine <b>.htaccess</b> Datei in Ihrem SMF Verzeichnis mit folgendem Inhalt:';
$txt['pmx_sef_engine_helpIS'] = '<br />Wenn Sie einen IIS7 Webserver verwenden, ben&ouml;tigen Sie eine <b>web.config</b> Datei in Ihrem SMF Verzeichnis mit folgenden Inhalt:';
$txt['pmx_sef_lowercase_help'] = 'Wenn gew&auml;hlt, werden alle URL\'s in Kleinbuchstaben umgewandelt.
	F&uuml;r bessere Resultate sollten Sie diese Einstellung aktivieren.';
$txt['pmx_sef_autosave_help'] = 'Wenn gew&auml;hlt, werden neuen Aktionen automatisch gespeichert.
	Es wird empfohlen diese Einstellung nicht zu aktivieren und neue Aktionen manuell einzutragen.
	Existiert eine Aktion nicht, wird die Frontpage angezeigt.';
$txt['pmx_sef_spacechar_help'] = 'Zeichen welches f&uuml;r Leerzeichen verwendet werden soll (Typisch _ oder -).
	Verwenden Sie das Zeichen <b>-</b> f&uuml;r bessere Resultate. Lassen Sie das Feld leer um Leerzeichen zu entfernen.';
$txt['pmx_sef_stripchars_help'] = 'Diese Zeichen werden aus der URL entfernt.
	Jedes Zeichen muss durch ein Komma getrennt werden.
	<b>Wenn Sie &Auml;nderungen durchf&uuml;hren, ist es m&ouml;glich, das Ihr Forum nicht mehr ordnungsgem&auml;&szlig; arbeitet.<b>';
$txt['pmx_sef_wirelesss_help'] = 'Diese Symbolnamen werden im WIRELESS Modus verwendet.
	Jeder Eintrag muss durch ein Komma getrennt werden.
	<b>Wenn Sie &Auml;nderungen durchf&uuml;hren, ist es m&ouml;glich, das Ihr Forum nicht mehr ordnungsgem&auml;&szlig; arbeitet.<b>';
$txt['pmx_sef_single_token_help'] = 'Symbole denen kein Wert zugewiesen wird und die eine spezielle Behandlung ben&ouml;tigen.
	Jeder Eintrag muss durch ein Komma getrennt werden.
	<b>Wenn Sie &Auml;nderungen durchf&uuml;hren, ist es m&ouml;glich, das Ihr Forum nicht mehr ordnungsgem&auml;&szlig; arbeitet.<b>';
$txt['pmx_sef_actions_help'] = 'Diese sind alle Aktionen, die im Forum verwendet werden. Normalerweise m&uuml;ssen Sie hier nichts &auml;ndern, da das SEF Modul neue Aktionen automatisch hinzuf&uuml;gt.
	Jeder Eintrag muss durch ein Komma getrennt werden.
	<b>Wenn Sie &Auml;nderungen durchf&uuml;hren, ist es m&ouml;glich, das Ihr Forum nicht mehr ordnungsgem&auml;&szlig; arbeitet.<b>';
$txt['pmx_sef_simplesef_space_help'] = 'Wenn Sie vorher die Modifikation SimpleSEF verwendet haben, tragen Sie hier das Zeichen ein, welches Sie f&uuml;r das Leerzeichen definiert haben (Typisch _).
	Diese Einstellung wird ben&ouml;tigt, um SimpleSEF "simple urls" (wie <i>topic_##, board_##</i>) nach PortaMxSEF zu konvertieren.
	Lassen Sie die Einstellung leer, wenn Sie SimpleSEF nicht verwendet haben.';
$txt['pmx_sef_ignoreactions_help'] =  'Aktionen die nicht durch das SEF Modul konvertiert werden. Jeder Eintrag muss durch ein Komma getrennt werden.';
$txt['pmx_sef_aliasactions_help'] =  'Sie k&ouml;nnen einen Alias f&uuml;r jede Aktion definieren. Jeder Alias muss im Format <b>aktion=alias</ b> angegeben werden.
	Jedes <b>aktion=alias</b> Paar muss durch ein Komma getrennt werden.';
$txt['pmx_sef_ignorerequests_help'] =  'Teile einer URL die nicht durch das SEF Modul konvertiert wird. Jeder Teil muss im Format <b>name=wert</b> angegeben werden.
	Jedes <b>name=wert</b> Paar muss durch ein Komma getrennt werden.';
$txt['pmx_settings_index_front_help'] = 'Wenn gew&auml;hlt, kann der Inhalt der Frontpage durch Spider (z.B. Google) indiziert werden.';
$txt['pmx_settings_sendfragment_help'] = 'Die Seite wird bei einigen Anfragen (z.B. neue Seitennummer, neue Kategorie oder neuer Artikel) so positioniert, das der Block am oberen Rand dargestellt wird (durch senden eines "#top" Fragments).';
$txt['pmx_access_promote_help'] = 'Benutzer in den gew&auml;hlen Gruppen k&ouml;nnen Beitr&auml;ge im Forum publizieren.<br />
	<b>Erteilte Rechte:</b> <i>Hinzuf&uuml;gen und entfernen der Publizierung in Beitr&auml;gen</i>';

$txt['pmx_settings_restoretop_help'] = 'Die vertikale Position der Seite wird bei einigen Anfragen (z.B. neue Seitennummer, neue Kategorie oder neuer Artikel) auf die vorherige Position gestellt.';
$txt['pmx_access_articlecreate_help'] = 'Benutzer in den gew&auml;hlten Gruppen k&ouml;nnen neue Artikel erstellen und eigene Artikel bearbeiten und l&ouml;schen.
	Neue Artikel m&uuml;ssen durch einen Artikel Moderator oder dem Administrator freigegeben werden.<br />
	<b>Erteilte Rechte:</b> <i>Neue Artikel erstellen, eigene Artikel bearbeiten, duplizieren, l&ouml;schen, aktivieren/deaktivieren</i>';
$txt['pmx_access_articlemoderator_help'] = 'Benutzer in den gew&auml;hlten Gruppen k&ouml;nnen Artikel bearbeiten, sperren und freigeben, wenn diese f&uuml;r die <b>Artikel Moderation</b> freigegeben sind.
	Diese ist immer der Fall, wenn ein Artikel durch einen Benutzer der <i>Artikel Ersteller</i> Gruppe erstellt wurde.<br />
	<b>Erteilte Rechte:</b> <i>Artikel bearbeiten, aktivieren/deaktivieren, freigeben/sperren</i>';
$txt['pmx_access_blocksmoderator_help'] = 'Benutzer in den gew&auml;hlten Gruppen k&ouml;nnen Bl&ouml;cke bearbeiten, die f&uuml;r die <b>Block Moderation</b> freigegeben sind.
	Der Zugriff ist weiterhin auf die freigegebenen Panele beschr&auml;nkt (Siehe auch Verwaltungs Einstellungen).<br />
	<b>Erteilte Rechte:</b> <i>Block Inhalt, Rechte, Titel und CSS Einstellungen bearbeiten, aktivieren/deaktivieren</i>';
$txt['pmx_access_pmxadmin_help'] = 'Benutzer in den gew&auml;hlten Gruppen haben <b>vollen</b> Zugriff auf <b>alle</b> Funktionen und Einstellungen des Portals.
	Diese Benutzer haben die gleichen Rechte wie ein Forum Administrator, begrenzt auf das Portal. <b>Verwende Sie diese Einstellung mit gr&ouml;&szlig;ter Vorsicht!</b>';
$txt['pmx_settings_noHS_onactionhelp'] = 'Hier k&ouml;nnen Sie Aktionen eintragen, bei denen die HighSlide Anzeige deaktiviert werden soll.
	F&uuml;r die <b>SMF Media Gallery</b> z.B. verwenden Sie den Eintrag <b>mgallery</b>.';
$txt['pmx_frontpage_help'] = 'W&auml;hlen Sie die Frontpage, welche Sie verwenden m&ouml;chten.<br />
	Bachten Sie, das die Vollbild Frontpage normalerweise keine Men&uuml;zeile hat, aber Sie k&ouml;nnen eine einfache Men&uuml;zeile aktivieren.<br />
	Seiten, Kategorien und Artikel werden auch dann angezeigt, wenn Sie "Keine Frontpage" gew&auml;hlt haben.<br />
	Wenn Sie zus&auml;tzliche CSS Klassen f&uuml;r die Vollbild Frontpage ben&ouml;tigen, erstellen Sie die Datei (<b>frontpage.css</b>) und speichern Sie diese im Verzeichnis des Designs.';
$txt['pmx_settings_adminpageshelp'] = 'Benutzer in der Gruppe <b>Block Moderator</b> k&ouml;nnen die Einstellungen und den Inhalt der Bl&ouml;cke in den gew&auml;hlten Panelen bearbeiten.<br />
	<b>Verwenden Sie diese Einstellung mit gr&ouml;&szlig;ter Vorsicht!</b>';
$txt['pmx_settings_xbars_help'] = 'W&auml;hlen Sie die Panele, welche mit den xBars ein- und ausgeklappt werden k&ouml;nnen.';
$txt['pmx_settings_collapse_vishelp'] = 'Diese Panel wird f&uuml;r die Dynamischen Block Einstellungen verwendet. Sie k&ouml;nnen dieses eingeklappt lassen, es wird automatisch ausgeklappt, wenn Dynamische Einstellungen vorhanden sind.';
$txt['pmx_settings_xbarkeys_help'] = 'Wenn gew&auml;hlt, k&ouml;nnen Sie das linke, rechte, obere und das untere Panel mit der <b>Strg</b> Taste und eine der Pfeiltasten (<b>Links, Rechts, Hoch, Runter</b>) und das Kopf und Fu&szlig; Panel mit der <b>Alt</b> Taste und den Pfeiltasten (<b>Hoch, Runter</b>) Ein-/Ausklappen. Die <b>xBarKeys</b> werden automatisch gesperrt, der ein Editor geladen wird.';
$txt['pmx_settings_blockcachestatshelp'] = 'Wenn gew&auml;hlt wird der PMX-Cache Status oberhalb der Seiten Ladezeit angezeigt.';
$txt['pmx_settings_hidecopyrighthelp'] =  'Geben Sie den Code Key ein, den Sie empfangen haben. Wenn der Key f&uuml;r Ihre Domain g&uuml;ltig und nicht abgelaufen ist, wird das PortaMx Copyright nicht angezeigt.
	Verwenden Sie Kopieren und Einf&uuml;gen (der Key ist l&auml;nger als das Eingabefeld) um den Code korrekt einzugeben.';
$txt['pmx_settings_panel_custhelp'] = 'Hier k&ouml;nne Sie andere Aktionen eingeben.
	F&uuml;r Seiten, Artikel und Kategorien wird ein Pr&auml;fix verwendet (<b>p:</b> f&uuml;r Seiten, <b>a:</b> f&uuml;r Artikel und <b>c:</b> f&uuml;r Kategorien).
	Stellen Sie das Pr&auml;fix vor den Namen der Seite, des Artikels oder der Kategorie, z.B. <b>p:meine_seite</b>.
	Die Namen k&ouml;nnen die Platzhalter <b>*</b> and <b>?</b> enthalten. Das Panel ist nicht sichtbar, wenn der Name zu der Aktion passt.
	Weiterhin k&ouml;nnen Sie Subaktionen verwenden, diese beginnen immer mit dem Kaufm&auml;nnischem UND (<b>&amp;</b>) z.B. <b>&amp;subaktionname=wert</b>.
	Weitere Informationen zu den Benutzerdefinierten Aktionen finden Sie in unseren Dokumentationen und im Support Forum.';
$txt['pmx_settings_downloadhelp'] = 'Wenn gew&auml;hlt wird eine <b>Download</b> Schaltfl&auml;che neben der <b>Community</b> Schaltfl&auml;che angezeigt.';
$txt['pmx_settings_dl_actionhelp'] = 'Defieren Sie die Aktion, die der Download Schaltfl&auml;che zugeordet werden soll.
	Sie k&ouml;nnen beliebige Namen mit den Zeichen (<b>a-z, A-Z, 0-9, -, _, .</b>) verwenden.
	Wenn Sie eine Seite, einen Artikel oder eine Kategorie verwenden m&ouml;chten, m&uuml;ssen sie dem Namen ein Pr&auml;fix voranstellen (<b>p:</b> f&uuml;r Seiten, <b>a:</b> f&uuml;r Artikel und <b>c:</b> f&uuml;r Kategorien) z.B. <b>p:download</b>';
$txt['pmx_settings_other_actionshelp'] = 'Hier k&ouml;nne Sie einen oder mehrere Namen (getrennt durch ein Komma) angeben, die als Forum Aktion gewertet werden.
	Sie m&uuml;ssen Namen in der Form <b>name=wert</b> verwenden, z.B.  <b>project=1</b> f&uuml;r das Project tool.';
$txt['pmx_settings_blockfollowhelp'] = 'Wenn Sie eine &Auml;nderung an einem Block vornehmen, einen Block verschieben oder duplizieren, wird das Panel in der &Uuml;bersicht angezeigt in dem sich der Block nach der &Auml;nderung befindet.';
$txt['pmx_settings_quickedithelp'] = 'Wenn gew&auml;hlt, ist der Blocktitel mit einen direkten Link zur <b>Bearbeitung des Blocks</b> verkn&uuml;pft.
	Dieser Link ist nur f&uuml;r Administratoren und Portal Administratoren aktiviert.';
$txt['pmx_settings_pages_help'] = 'Geben Sie den Namen von Seiten, Artikeln oder Kategorien an (durch ein Komma getrennt), bei denen die Frontpage Bl&ouml;cke NICHT angezeigt werden sollen.
	Lassen Sie diese Einstellung frei, wenn Sie die Frontpage Bl&ouml;cke individuell mit den Block Einstellungen platzieren wollen.
	F&uuml;r Seiten, Artikel und Kategorien m&uuml;ssen Sie dem Namen ein Pr&auml;fix (<b>p:</b> f&uuml;r Seiten, <b>a:</b> f&uuml;r Artikel und <b>c:</b> f&uuml;r Kategorien) verwenden.
	Die Namen k&ouml;nnen die Platzhalter <b>*</b> und <b>?</b> enthalten.</b>.';
$txt['pmx_settings_article_on_pagehelp'] = 'Geben Sie die Anzahl der Artikel an, die Sie in der Artikel Verwaltungs &Uuml;bersicht sehen wollen.';
$txt['pmx_settings_forumscrollhelp'] = 'Wenn der Bereich zwischen dem linken und rechten Panel zu breit ist, wird das rechte Panel normalerweise aus dem Bildbereich geschoben.
	Ist diese Option aktiviert, wird der mittlere Bereich (Forum Bereich) verkleinert und kann horizontal verschoben werden.<br />
	<b>Diese Option funktioniert NICHT mit IE kleiner der Version 8.</b>';
$txt['pmx_settings_postcountacshelp'] = 'Beitragsbasierende Gruppen zus&auml;tzlich zu den Regul&auml;ren Gruppen f&uuml;r die Berechtigungen verwenden.';
$txt['pmx_settings_teasermode'] = array(
	0 => 'W&auml;hlen Sie die Z&auml;hlmethode f&uuml;r die Beitrags K&uuml;rzung:',
	1 => 'Worte z&auml;hlen',
	2 => 'Zeichen z&auml;hlen'
);
$txt['pmx_settings_pmxteasecnthelp'] = 'In verschiedenen Bl&ouml;cken und in Artikeln kann eine <i>Beitrags K&uuml;rzung</i> aktiviert werden.
	Hier k&ouml;nnen Sie einstellen, wie diese Option arbeiten soll.
	F&uuml;r Sprachen die kein Leerzeichen kennen (z.B. Japanisch) sollten Sie die Z&auml;hlmethode <b>Zeichen z&auml;hlen</b> w&auml;hlen.';
$txt['pmx_settings_promote_messages_help'] = 'Sie sehen alle Publizierten Beitrags Nummern. Sie k&ouml;nnen Nummern entfernen oder neue hinzuf&uuml;gen (getrennt durch ein Komma).';
$txt['pmx_settings_enable_promote_help'] = 'Wenn gew&auml;hlt ist die Beitrags Publizierung aktiv und <b>Administratoren</b> sehen den Link <b>Beitrag Publizieren</b> unter jedem Beitrag im Forum.
	Ist ein Beitrag bereits Publiziert, wird der Link <b>Publizieren entfernen</b> angezeigt.<br />
	Die Publizierten Beitr&auml;ge k&ouml;nnen mit dem Block <i>Publizierte Beitr&auml;ge</i> z.b. auf der Frontpage angezeigt werden.';
?>
