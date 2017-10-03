<?php
#   Dashboard by Maretz.eu Ilch CMS

defined('main') or die('no direct access');
setlocale(LC_TIME, "de_DE");
$ILCH_HEADER_ADDITIONS .= "<link rel=\"stylesheet\" href=\"include/includes/css/dashboard.css\">";
$title  = $allgAr['title'] . ' :: Dashboard';
$hmenu  = 'Dashboard';
$design = new design($title, $hmenu);
$design->header();
$erg = db_query("SELECT a.fid as test, a.id, a.name,a.erst as autor, a.rep,b.name as top, a.last_post_id as vergleicha, b.id as fid, c.erst as last,c.txt, c.erstid, c.id as pid, c.id as vergleich, c.time, a.rep, a.erst, a.hit, a.art, a.stat, d.name as kat
FROM prefix_topics a
  LEFT JOIN prefix_forums b ON b.id = a.fid
  LEFT JOIN prefix_posts c ON c.id = a.last_post_id
    LEFT JOIN prefix_forumcats d ON d.id = b.cid AND b.id = a.fid
  LEFT JOIN prefix_groupusers vg ON vg.uid = " . $_SESSION['authid'] . " AND vg.gid = b.view
  LEFT JOIN prefix_groupusers rg ON rg.uid = " . $_SESSION['authid'] . " AND rg.gid = b.reply
  LEFT JOIN prefix_groupusers sg ON sg.uid = " . $_SESSION['authid'] . " AND sg.gid = b.start
WHERE ((" . $_SESSION['authright'] . " <= b.view AND b.view < 1) 
   OR (" . $_SESSION['authright'] . " <= b.reply AND b.reply < 1)
   OR (" . $_SESSION['authright'] . " <= b.start AND b.start < 1)
     OR vg.fid IS NOT NULL
     OR rg.fid IS NOT NULL
     OR sg.fid IS NOT NULL
     OR -9 >= " . $_SESSION['authright'] . ")
ORDER BY c.time DESC
LIMIT 0," . $allgAr['dbforumin'] . "");
if ($allgAr['dbforum'] == 1) {
    echo '<h3>Letzte Forum Aktivit&auml;ten</h3><hr>';
    if (loggedin()) {
        $admin = '';
        if (user_has_admin_right($menu, false)) {
            $admin = '<br /><a href="admin.php?forum">neues Forum erstellen</a>';
        }
    }
    if (@db_num_rows($erg) == 0) {
        echo '<div class="border dashboardborder"><div class="padding1dashboard"><div class="Cnorm paddingdashboard dbtextcenter">';
        echo 'kein Forumeintrag vorhanden' . $admin . '';
        echo '</div></div></div>';
    }
    while ($row = db_fetch_assoc($erg)) {
        $times     = $row['time'];
        $diff      = time() - $row['time'];
        $fullHours = intval($diff / 60 / 60);
        $Minutes   = intval(($diff / 60) - (60 * $fullHours));
        if ($Minutes == 0) {
            $Minutes = 'gerade eben';
        } elseif ($Minutes == 1) {
            $Minutes = 'vor einer Minute';
        } else {
            $Minutes = 'vor ' . $Minutes . ' Minuten';
        }
        if ($fullHours == 0) {
            $Stunde = $Minutes;
        } elseif ($fullHours == 1) {
            $Stunde = 'vor einer Stunde';
        } else {
            $Stunde = 'vor ' . $fullHours . ' Stunden';
        }
        $wochentag = strftime("%A", $times);
        
        if (date("d.m.Y", $times) == date("d.m.Y")) {
            if ($fullHours < 8) {
                $row['date'] = $Stunde;
            } else {
                $row['date'] = "Heute, " . date("H:i", $times) . " Uhr";
            }
        } elseif (date("d.m.Y", $times) == date("d.m.Y", time() - 60 * 60 * 24)) {
            if ($fullHours < 8) {
                $row['date'] = $Stunde;
            } else {
                $row['date'] = "Gestern, " . date("H:i", $times) . " Uhr";
            }
        } elseif (date("d.m.Y", $times) == date("d.m.Y", time() - 60 * 60 * 48)) {
            $row['date'] = "$wochentag, " . date("H:i", $times) . " Uhr";
        } elseif (date("d.m.Y", $times) == date("d.m.Y", time() - 60 * 60 * 72)) {
            $row['date'] = "$wochentag, " . date("H:i", $times) . " Uhr";
        } elseif (date("d.m.Y", $times) == date("d.m.Y", time() - 60 * 60 * 96)) {
            $row['date'] = "$wochentag, " . date("H:i", $times) . " Uhr";
        } elseif (date("d.m.Y", $times) == date("d.m.Y", time() - 60 * 60 * 120)) {
            $row['date'] = "$wochentag, " . date("H:i", $times) . " Uhr";
        } else {
            $row['date'] = strftime("%d. %B %Y", $times);
        }
        if ($allgAr['dbvorschau'] == 1) {
            $row['dashbbgcolor'] = 'Cdark';
        } else {
            $row['dashbbgcolor'] = 'Cnorm';
        }
        $autorid        = @db_result(db_query('SELECT id FROM prefix_user WHERE name = "' . $row['autor'] . '"'), 0);
        $comavatar      = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "' . $row['last'] . '"'), 0);
        $row['avatar']  = (!empty($comavatar) AND file_exists($comavatar)) ? '<img class="drashboardavatar" src="' . $comavatar . '" alt="Avatar" />' : '<img class="drashboardavatar" src="include/images/avatars/wurstegal.jpg" />';
        $row['texte']   = bbCode($row['txt']);
        $row['page']    = ceil(($row['rep'] + 1) / $allgAr['Fpanz']);
        $row['autore']  = '<a href="?forum-showposts-' . $row['id'] . '"><b>' . $row['autor'] . '</b></a>';
        $row['name']    = 'Hat auf das Thema <a href="?forum-showposts-' . $row['id'] . '-p' . $row['page'] . '#' . $row['pid'] . '"><b>' . $row['name'] . '</b></a> von ' . $row['autore'] . ' zuletzt geantwortet.';
        $row['autores'] = '<a class="box" href="?user-details-' . $row['erstid'] . '"><b>' . $row['last'] . '</b></a>';
        $row['kat']     = $row['kat'];
        $row['right']   = '<a title="zum Beitrag" href="?forum-showposts-' . $row['id'] . '-p' . $row['page'] . '#' . $row['pid'] . '"><b>&raquo;</b></a>';
        echo '<div class="border dashboardborder"><div class="padding1dashboard"><div class="' . $row['dashbbgcolor'] . ' paddingdashboard"><table class="dashboardtable">';
        echo '<tr>';
        echo '<td class="dbverticaltop dashb55">' . $row['avatar'] . '</td>';
        echo '<td class="dbverticaltop">' . $row['autores'] . ' <span class="smalfont">- ' . $row['date'] . '</span><br />' . $row['name'] . '</td>';
        echo '<td class="dbverticaltop dbtextright dashb10">' . $row['right'] . '</td></tr>';
        if ($allgAr['dbvorschau'] == 1) {
            echo '<tr><td colspan="3"><div class="border dbvorschau"><div class="padding1dashboard"><div class="dboardin Cnorm" style="max-height:' . $allgAr['dbvorschauh'] . 'px;"><div class="dboardinpadding">' . $row['texte'] . '</div></div></div></div></td></tr>';
        }
        echo '</table></div></div></div>';
    }
}
$abf = "SELECT
          a.news_kat as kate,
          a.news_time,
          a.news_title as title,
          a.news_id as id, 
          a.news_text as txt,    
          b.name as username,
          b.id as userid        
          FROM prefix_news as a
          LEFT JOIN prefix_user as b ON a.user_id = b.id
          WHERE news_recht >= " . $_SESSION['authright'] . "
          ORDER BY a.news_time DESC
          LIMIT 0," . $allgAr['dbnewsin'] . "";
if ($allgAr['dbnews'] == 1) {
    echo '<h3>Letzte News Eintr&auml;ge</h3><hr>';
    $erg2 = db_query($abf);
    if (loggedin()) {
        $admin = '';
        if (user_has_admin_right($menu, false)) {
            $admin = '<a href="admin.php?news">jetzt eine News erstellen</a>';
        }
    }
    if (@db_num_rows($erg2) == 0) {
        echo '<div class="border dashboardborder"><div class="padding1dashboard"><div class="Cnorm paddingdashboard dbtextcenter">';
        echo 'kein Newseintrag vorhanden<br>' . $admin . '';
        echo '</div></div></div>';
    } else {
        while ($row = db_fetch_object($erg2)) {
            $newstimestramp = new DateTime($row->news_time);
            $timesnews      = $newstimestramp->getTimestamp();
            $diffnews       = time() - $timesnews;
            $fullHoursnews  = intval($diffnews / 60 / 60);
            $Minutesnews    = intval(($diffnews / 60) - (60 * $fullHoursnews));
            if ($Minutesnews == 0) {
                $Minutesnews = 'gerade eben';
            } elseif ($Minutesnews == 1) {
                $Minutesnews = 'vor einer Minute';
            } else {
                $Minutesnews = 'vor ' . $Minutesnews . ' Minuten';
            }
            if ($fullHoursnews == 0) {
                $Stundenews = $Minutesnews;
            } elseif ($fullHoursnews == 1) {
                $Stundenews = 'vor einer Stunde';
            } else {
                $Stundenews = 'vor ' . $fullHoursnews . ' Stunden';
            }
            $wochentagnews = strftime("%A", $timesnews);
            
            if (date("d.m.Y", $timesnews) == date("d.m.Y")) {
                if ($fullHoursnews < 8) {
                    $row->newnewstime = $Stundenews;
                } else {
                    $row->newnewstime = "Heute, " . date("H:i", $timesnews) . " Uhr";
                }
            } elseif (date("d.m.Y", $timesnews) == date("d.m.Y", time() - 60 * 60 * 24)) {
                if ($fullHoursnews < 8) {
                    $row->newnewstime = $Stundenews;
                } else {
                    $row->newnewstime = "Gestern, " . date("H:i", $timesnews) . " Uhr";
                }
            } elseif (date("d.m.Y", $timesnews) == date("d.m.Y", time() - 60 * 60 * 48)) {
                $row->newnewstime = "$wochentagnews, " . date("H:i", $timesnews) . " Uhr";
            } elseif (date("d.m.Y", $timesnews) == date("d.m.Y", time() - 60 * 60 * 72)) {
                $row->newnewstime = "$wochentagnews, " . date("H:i", $timesnews) . " Uhr";
            } elseif (date("d.m.Y", $timesnews) == date("d.m.Y", time() - 60 * 60 * 96)) {
                $row->newnewstime = "$wochentagnews, " . date("H:i", $timesnews) . " Uhr";
            } elseif (date("d.m.Y", $timesnews) == date("d.m.Y", time() - 60 * 60 * 120)) {
                $row->newnewstime = "$wochentagnews, " . date("H:i", $timesnews) . " Uhr";
            } else {
                $row->newnewstime = strftime("%d. %B %Y", $timesnews);
            }
            
            if ($allgAr['dbvorschau'] == 1) {
                $row->dashbbgcolor = 'Cdark';
            } else {
                $row->dashbbgcolor = 'Cnorm';
            }
            $comavatar2   = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "' . $row->username . '"'), 0);
            $row->avatar2 = (!empty($comavatar2) AND file_exists($comavatar2)) ? '<img class="drashboardavatar" src="' . $comavatar2 . '" alt="Avatar" />' : '<img class="drashboardavatar" src="include/images/avatars/wurstegal.jpg" />';
            $row->text    = bbCode($row->txt);
            $row->title   = '<a href="?news-' . $row->id . '"><b>' . $row->title . '</b></a>';
            $row->titl    = 'Hat die News ' . $row->title . ' in der Kategorie <b>' . $row->kate . '</b> eingetragen.';
            $row->right   = '<a title="zur News" href="?news-' . $row->id . '"><b>&raquo;</b></a>';
            echo '<div class="border dashboardborder"><div class="padding1dashboard"><div class="' . $row->dashbbgcolor . ' paddingdashboard"><table class="dashboardtable">';
            echo '<tr>';
            echo '<td class="dbverticaltop dashb55">' . $row->avatar2 . '</td>';
            echo '<td class="dbverticaltop"><a class="box" href="?user-details-' . $row->userid . '"><b>' . $row->username . '</b></a> <span class="smalfont">- ' . $row->newnewstime . '</span><br />' . $row->titl . '</td>';
            echo '<td class="dbverticaltop dbtextright dashb10">' . $row->right . '</td></tr>';
            if ($allgAr['dbvorschau'] == 1) {
                echo '<tr><td colspan="3"><div class="border dbvorschau"><div class="padding1dashboard"><div class="dboardin Cnorm" style="max-height:' . $allgAr['dbvorschauh'] . 'px;"><div class="dboardinpadding">' . $row->text . '</div></div></div></div></td></tr>';
            }
            echo '</table></div></div></div>';
            
        }
    }
}
if ($allgAr['dbkom'] == 1) {
    $comAbf = "SELECT * FROM `prefix_koms` ORDER BY id DESC LIMIT 0," . $allgAr['dbkomin'] . "";
    $comErg = db_query($comAbf);
    
    if (db_num_rows($comErg) > 0) {
        echo '<h3>Letzte Kommentare</h3><hr>';
        while ($comRow = db_fetch_object($comErg)) {
            
            $diffkom3      = time() - $comRow->time;
            $fullHourskom = intval($diffkom3 / 60 / 60);
            $Minuteskom   = intval(($diffkom3 / 60) - (60 * $fullHourskom));
            if ($Minuteskom == 0) {
                $Minuteskom = '- gerade eben';
            } elseif ($Minuteskom == 1) {
                $Minuteskom = '- vor einer Minute';
            } else {
                $Minuteskom = '- vor ' . $Minuteskom . ' Minuten';
            }
            if ($fullHourskom == 0) {
                $Stundenkom = $Minuteskom;
            } elseif ($fullHourskom == 1) {
                $Stundenkom = '- vor einer Stunde';
            } else {
                $Stundenkom = '- vor ' . $fullHourskom . ' Stunden';
            }
            $wochentagkom = strftime("- %A", $comRow->time);
            
            if (date("d.m.Y", $comRow->time) == date("d.m.Y")) {
                if ($fullHourskom < 8) {
                    $komtime = $Stundenkom;
                } else {
                    $komtime = "- Heute, " . date("H:i", $comRow->time) . " Uhr";
                }
            } elseif (date("d.m.Y", $comRow->time) == date("d.m.Y", time() - 60 * 60 * 24)) {
                if ($fullHourskom < 8) {
                    $komtime = $Stundenkom;
                } else {
                    $komtime = "- Gestern, " . date("H:i", $comRow->time) . " Uhr";
                }
            } elseif (date("d.m.Y", $comRow->time) == date("d.m.Y", time() - 60 * 60 * 48)) {
                $komtime = "$wochentagkom, " . date("H:i", $comRow->time) . " Uhr";
            } elseif (date("d.m.Y", $comRow->time) == date("d.m.Y", time() - 60 * 60 * 72)) {
                $komtime = "$wochentagkom, " . date("H:i", $comRow->time) . " Uhr";
            } elseif (date("d.m.Y", $comRow->time) == date("d.m.Y", time() - 60 * 60 * 96)) {
                $komtime = "$wochentagkom, " . date("H:i", $comRow->time) . " Uhr";
            } elseif (date("d.m.Y", $comRow->time) == date("d.m.Y", time() - 60 * 60 * 120)) {
                $komtime = "$wochentagkom, " . date("H:i", $comRow->time) . " Uhr";
            } else {
                $komtime = strftime("- %d. %B %Y", $comRow->time);
            }
            if ($comRow->time == 0) {
                $komtime = '';
            }
            if ($comRow->cat == 'NEWS') {
                $link        = 'index.php?news-' . $comRow->uid;
                $namekate    = 'die News';
                $nameeintrag = @db_result(db_query('SELECT news_title FROM prefix_news WHERE news_id = "' . $comRow->uid . '"'), 0);
                $namekat     = '';
            } elseif ($comRow->cat == 'GBOOK') {
                $link        = 'index.php?gbook-show-' . $comRow->uid;
                $namekate    = 'den G&auml;stebucheintrag von';
                $nameeintrag = @db_result(db_query('SELECT name FROM prefix_gbook WHERE id = "' . $comRow->uid . '"'), 0);
                $namekat     = '';
            } elseif ($comRow->cat == 'WARSLAST') {
                $link        = 'index.php?wars-more-' . $comRow->uid;
                $namekate    = 'den War gegen';
                $nameeintrag = @db_result(db_query('SELECT gegner FROM prefix_wars WHERE id = "' . $comRow->uid . '"'), 0);
                $namekat     = '';
            } elseif ($comRow->cat == 'GALLERYIMG') {                
                $namekate    = 'das Bild ';
                $endung = @db_result(db_query('SELECT endung FROM prefix_gallery_imgs WHERE id = "' . $comRow->uid . '"'), 0);
                $namebild = @db_result(db_query('SELECT datei_name FROM prefix_gallery_imgs WHERE id = "' . $comRow->uid . '"'), 0);
                $nameeintrag = $namebild.'.'.$endung;           
                $bildid      = @db_result(db_query('SELECT cat FROM prefix_gallery_imgs WHERE datei_name = "' . $namebild . '"'), 0);
                $link        = 'index.php?gallery-' . $bildid ;
                $namekat     = @db_result(db_query('SELECT name FROM prefix_gallery_cats WHERE id = "' . $bildid . '"'), 0);
                if ($bildid  == 0) {
                $namekat     = '';
                } else {
                $namekat     = 'in der Kategorie <b>'. $namekat .'</b>';
              }
            }
            if ($allgAr['dbvorschau'] == 1) {
                $dashbbgcolor = 'Cdark';
            } else {
                $dashbbgcolor = 'Cnorm';
            }
            $name        = $comRow->name;
            $comavatar   = @db_result(db_query('SELECT avatar FROM prefix_user WHERE name = "' . $name . '"'), 0);
            $text        = bbcode($comRow->text);
            $avatars     = (!empty($comavatar) AND file_exists($comavatar)) ? '<img class="drashboardavatar" src="' . $comavatar . '" alt="Avatar" />' : '<img class="drashboardavatar" src="include/images/avatars/wurstegal.jpg" />';
            $right       = '<a title="zum Kommentar" href="' . $link . '"><b>&raquo;</b></a>';
            $userid      = @db_result(db_query('SELECT id FROM prefix_user WHERE name = "' . $name . '"'), 0);
            $nameeintarg = @db_result(db_query('SELECT id FROM prefix_user WHERE name = "' . $name . '"'), 0);
            $titel       = 'Hat ' . $namekate . ' <a href="' . $link . '"><b>' . $nameeintrag . '</b></a> ' . $namekat . ' kommentiert.';
            echo '<div class="border dashboardborder"><div class="padding1dashboard"><div class="' . $dashbbgcolor . ' paddingdashboard"><table class="dashboardtable">';
            echo '<tr>';
            echo '<td class="dbverticaltop dashb55">' . $avatars . '</td>';
            echo '<td class="dbverticaltop"><a class="box" href="?user-details-' . $userid . '"><b>' . $name . '</b></a> <small>' . $komtime . '</small><br />' . $titel . '</td>';
            echo '<td class="dbverticaltop dbtextright dashb10">' . $right . '</td></tr>';
            if ($allgAr['dbvorschau'] == 1) {
                echo '<tr><td colspan="3"><div class="border dbvorschau"><div class="padding1dashboard"><div class="dboardin Cnorm" style="max-height:' . $allgAr['dbvorschauh'] . 'px;"><div class="dboardinpadding">' . $text . '</div></div></div></div></td></tr>';
            }
            echo '</table></div></div></div>';
        }
    } else {
        echo '<h3>Letzte Kommentare</h3><hr>';
        echo '<div class="border dashboardborder"><div class="padding1dashboard"><div class="Cnorm paddingdashboard dbtextcenter">';
        echo 'kein Kommentar vorhanden';
        echo '</div></div></div>';
    }
}
$design->footer();

?> 