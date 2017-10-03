<?php
# ilchClan Script (c) by Manuel Staechele
defined('main') or die('no direct access');
if (user_has_admin_right($menu, false) == false) die('F&uuml;r diese Installation ben&ouml;tigt man Administratorenrechte !<br /><a href="index.php">Zur Startseite</a>');
$modulby = 'Maretz.eu';
$script_name = 'Dashboard';
$script_vers = '1.0';
$ilch_vers = '1.1';
$ilch_update = 'P';
$erfolg = '';
$fehler = '';
$title = $allgAr['title'] . ' => ' . $script_name;
$hmenu = $script_name . ' Vers.: ' . $script_vers . ' f&uuml;r ilchClan ' . $ilch_vers . ' Vers.: ' . $ilch_update;
$design = new design($title, $hmenu, 1);
$design->header();
$checktabelle = mysql_query("SHOW TABLES LIKE 'prefix_last24h'");

if (!isset($_POST['do'])) {
?>
        <form action="index.php?installation" method="POST">
        <input type="hidden" name="do" value="1">
        <table width="97%" class="border" border="0" cellspacing="1" cellpadding="3" align="center">
        <tr class="Chead" align="center" style="padding:8px">
         <td><h2><?php echo $script_name; ?> <small><a href="https://www.maretz.eu">by Maretz.eu</a></small></h2></td>
        </tr>
        <tr class="Cmite">
         <td align="left">
             <br />
            <div style="padding:10px;text-align:left;">
            <strong><u>Informationen:</u></strong><br /><br />
            <strong>Modulname:</strong> <?php echo $script_name; ?><br />
            <strong>Version:</strong> <?php echo $script_vers; ?><br />
            <strong>Entwickler:</strong> <?php echo $modulby; ?><br />
            Entwickelt f&uuml;r ilchClan Version <strong><?php echo $ilch_vers; ?> <?php echo $ilch_update; ?></strong> .<br /><br />
            Die Dashboard Seite zeigt die letzten Aktivit&auml;ten der Seite an im Bereich Forum, News und Kommentare mit einer Vorschau.<br /><br />
            </div>
            <br />
            <hr />
            <br />
            <div style="padding:10px;text-align:left;">
            <strong><u>Wichtig:</u></strong><br /><br />
            Machen Sie zuerst ein <a href="admin.php?backup" target="_blank"><b>Backup</b></a> Ihrer Datenbank, falls es doch zu unerwarteten Problemen kommt.<br />
            <br />
            </div>
         </td>
        </tr>
        <tr class="Cdark">
         <td align="center">
            <input type="submit" value="<?php echo $script_name; ?> Installieren" />
         </td>
        </tr>
        </table>
        </form>
<?php
} elseif ($_POST['do'] == '1') {
    $checktabelle = db_query("SELECT kat FROM prefix_config WHERE kat = 'Dashboard'");
    if (db_num_rows($checktabelle)) {
?>
        <form action="index.php?installation" method="POST">
        <input type="hidden" name="do" value="1">
        <table width="97%" class="border" border="0" cellspacing="1" cellpadding="3" align="center">
        <tr class="Chead" align="center" style="padding:8px">
         <td><h2><?php echo $script_name; ?> <small><a href="https://www.maretz.eu">by Maretz.eu</a></small></h2></td>
        </tr>
<tr class="Cmite">
         <td align="center"><br />
<b>Installation abgebrochen</b>. Das Dashboard wurde bereits installiert!<br />
Installationsdateien wurden gel&ouml;scht.<br /><br />
         </td>
        </tr>
        <tr class="Cdark">
         <td align="center">
            <a href="index.php">Zur Startseite</a>&nbsp;&nbsp;<a href="admin.php?allg">Dashboard Einstellungen</a>
         </td>
        </tr>
        </table>
        </form>
<?php
@unlink('include/contents/installation.php') && @unlink('include/contents/installation.sql');
}  else {
$error = '';
$sql_file = implode('', file('include/contents/installation.sql'));
$sql_file = preg_replace("/(\015\012|\015|\012)/", "\n", $sql_file);
$sql_statements = explode(";\n", $sql_file);
foreach($sql_statements as $sql_statement) {
if (trim($sql_statement) != '') {
db_query($sql_statement) OR $error.= mysql_errno() . ': ' . mysql_error() . '<br />';
}
}
// Ausgabe

?>
        <table width="97%" class="border" border="0" cellspacing="1" cellpadding="3" align="center">
        <tr class="Chead" align="center" style="padding:8px">
         <td><h2><?php echo $script_name; ?> <small><a href="http://www.maretz.eu">by Maretz.eu</a></small></h2></td>
        </tr>
                <tr class="Cnorm" align="center">
         <td><h2>Installation abgeschlossen</td>
        </tr>
        <tr class="Cmite">
         <td colspan="3" align="center">
            <br />
<?php
if (!empty($error)) {
if (empty($fehler)) {
$fehler = 'Es sind <b>Fehler bei der Installation</b> aufgetreten!<br />Bitte benachrichtigen Sie den Entwickler ( <a href="mailto:info@maretz.eu?subject=Fehler%20bei%20der%20Installation%20der%20Last24h%20Box">info@maretz.eu</a> ).';
}
$fehler.= '<br /><br />Oben sollten Sie eine ausf&uuml;hrlichere Fehlermeldung sehen. Diese, wenn m&ouml;glich, in der Nachricht anh&auml;ngen.<br />';
echo $fehler . '<br /><br /><hr /><br /><strong style="text-decoration:underline;">Fehlermeldungen:</strong><br /><br /><span style="color:#FF0000;font-size:bold;">' . $error . '</span>';
} else {
if (empty($erfolg)) {
$erfolg = '<b>Das Dashboard wurde erfolgreich installiert.</b>';
}
if (@unlink('include/contents/installation.php') && @unlink('include/contents/installation.sql')) {
$erfolg.= '<br /><br />Diese Installationsdateien wurden erfolgreich gel&ouml;scht.<br />Im Adminbereich > Konfiguration (Dashboard) k&ouml;nnen neben der Anzahl der Beitr&auml;ge weitere Einstellungen vorgenommen werden.';
} else {
$erfolg.= '<br /><br /><strong>Die Installationsdateien konnten nicht automatisch gel&ouml;scht werden. L&ouml;schen Sie folgende Dateien:</strong><br /><i>include/contents/installation.php</i><br /><i>include/contents/installation.sql</i><br /><br />Im Adminbereich > Konfiguration (Dashboard) k&ouml;nnen neben der Anzahl der Beitr&auml;ge weitere Einstellungen vorgenommen werden.';
}
echo $erfolg;
}
?>
            <br />
            <br />
         </td>
        </tr>
        <tr class="Chead">
         <td colspan="3" align="center">
            <a href="index.php">Zur Startseite</a>&nbsp;&nbsp;<a href="admin.php?allg">Dashboard Einstellungen</a>
         </td>
        </tr>
        </table>
      </td>
     </tr>
     </table>
<?php
}
}
$design->footer();
?>