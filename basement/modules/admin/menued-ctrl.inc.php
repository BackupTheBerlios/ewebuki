<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id: menued-ctrl.inc.php,v 1.2 2006/09/20 17:26:54 chaot Exp $";
  $Script["desc"] = "menued - steuerung";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2006 Werner Ammon ( wa<at>chaos.de )

    This script is a part of eWeBuKi

    eWeBuKi is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    eWeBuKi is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with eWeBuKi; If you did not, you may download a copy at:

    URL:  http://www.gnu.org/licenses/gpl.txt

    You may also request a copy from:

    Free Software Foundation, Inc.
    59 Temple Place, Suite 330
    Boston, MA 02111-1307
    USA

    You may contact the author/development team at:

    Chaos Networks
    c/o Werner Ammon
    Lerchenstr. 11c

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    // warnung ausgeben
    if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warnung: register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

    // path fuer die schaltflaechen anpassen
    if ( $cfg["iconpath"] == "" ) $cfg["iconpath"] = "/images/default/";

    // label bearbeitung aktivieren
    if ( isset($HTTP_GET_VARS["edit"]) ) {
        $specialvars["editlock"] = 0;
    } else {
        $specialvars["editlock"] = -1;
    }

    // lokale db auswaehlen
    if ( $cfg["db"]["change"] == -1 ) {
        if ( $environment["fqdn"][0] == $specialvars["dyndb"] && in_array($specialvars["dyndb"],$_SESSION["dbzugriff"]) ) {
            $db->selectDb($specialvars["dyndb"],FALSE);
        } elseif ( $environment["fqdn"][0] == $cfg["fqdn0"] && $_SESSION["sti"] == -1 ) {
            ### loesung?
        } else {
            $sql = "SELECT adakz FROM db_adrd where adid='".$_SESSION["custom"]."'";
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result,$nop);
            $db->selectDb("intra".$data["adakz"],FALSE);
        }
    }

    // include function loader
    if ( is_array($cfg["function"][$environment["kategorie"]]) ) include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-functions.inc.php";

    // magic include loader
    if ( array_key_exists($environment["kategorie"], $cfg["function"]) ) {
        include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-".$environment["kategorie"].".inc.php";
    } else {
        include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-list.inc.php";
    }

    // globale db auswaehlen
    if ( $cfg["db"]["change"] == -1 ) {
        $db -> selectDb(DATABASE,FALSE);
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
