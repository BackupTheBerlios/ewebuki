<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id: view.inc.php,v 1.2 2006/07/31 10:15:41 chaot Exp $";
  $Script["desc"] = "picture viewer";
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

        // page basics
        // ***

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

        // +++
        // page basics


        // funktions bereich
        // ***

        $fid = $environment["parameter"][2];

        if ( $environment["parameter"][3] != "" ) {
            $sql = "SELECT *
                    FROM ".$cfg["db"]["entries"]."
                    WHERE fhit like '#p".$environment["parameter"][3]."%'
                ORDER BY ".$cfg["db"]["order"];
        } else {
            $sql = "SELECT *
                    FROM ".$cfg["db"]["entries"]."
                    WHERE fid =".$fid;
        }
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);



        $erste = "";
        while ( $data = $db -> fetch_array($result,1) ) {
            $dataloop["leer"][$data["fid"]][0] = $data["fid"];
            $dataloop["leer"][$data["fid"]][1] = $data["fhit"];

            $gesamt++;

            if ( $erste == "" ) $erste = $data["fid"];

            if ( $gefunden == True ) {
                $nachher = $data["fid"];
                $gefunden = False;
            }

            if ( $data["fid"] == $fid ) {
                $gefunden = True;
                $vorher = $merker;
                $aktuell = $gesamt;
                $filename = $data["ffname"];
                $filetyp = $data["ffart"];
                $ausgaben["beschriftung"] = $data["funder"];
                $ausgaben["beschreibung"] = $data["fdesc"];
            }

            $merker = $data["fid"];

            #$marke = substr($data["fhit"],0,strpos($data["fhit"],"#",1));
            #$merke = str_replace( "#p".$environment["parameter"][3].",", "", $marke )."<br />";
        }
        $letzte = $merker;
        #$hidedata["leer"][0] = "enable";

        // ueberlauf sicherstellen
        if ( $fid == $erste ) {
            $vorher = $letzte;
        }
        if ( $fid == $letzte ) {
            $nachher = $erste;
        }

        // navi links
        $ausgaben["zurueck"] = "view,".$environment["parameter"][1].",".$vorher.",".$environment["parameter"][3].".html";
        $ausgaben["aktuell"] = $aktuell;
        $ausgaben["gesamt"] = $gesamt;
        $ausgaben["vor"] = "view,".$environment["parameter"][1].",".$nachher.",".$environment["parameter"][3].".html";
        $ausgaben["referer"] = dirname($pathvars["requested"]).".html";
        if ( $environment["parameter"][3] != "" ) {
            $hidedata["navi"][0] = "enable";
        } else {
            $ausgaben["vor"] = $ausgaben["referer"];
        }


        // img werte
        if ( $pathvars["filebase"]["realname"] == True ) {
            $img = $filetyp."/".$fid."/".$environment["parameter"][1]."/".$filename;
        } else {
            $img =  $pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"][$environment["parameter"][1]]."img_".$fid.".".$filetyp;
        }

        $imgfile = $pathvars["filebase"]["maindir"].$img;
        $ausgaben["imgurl"] = $pathvars["webroot"].$pathvars["filebase"]["webdir"].$img;
        if ( file_exists($imgfile) ) {
            $imgsize = getimagesize($imgfile);
            $ausgaben["imgsize"] = " ".$imgsize[3];
        }

        // +++
        // funktions bereich


        // page basics
        // ***

        // fehlermeldungen
        if ( $HTTP_GET_VARS["error"] != "" ) {
            if ( $HTTP_GET_VARS["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            }
        } else {
            $ausgaben["form_error"] = "";
        }

        // navigation erstellen
        #$ausgaben["add"] = $cfg["basis"]."/add,".$environment["parameter"][1].",verify.html";
        #$mapping["navi"] = "leer";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        #$mapping["main"] = crc32($environment["ebene"]).".list";
        $mapping["main"] = "viewer";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
