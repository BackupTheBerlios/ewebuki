<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: fileed-list.inc.php,v 1.10 2006/09/14 14:18:21 chaot Exp $";
// "fileed - list funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $cfg["right"] == "" || $rechte[$cfg["right"]] == -1 ) {

        // funktions bereich
        // ***

        $ausgaben["search"] = "";
        $position = $environment["parameter"][1]+0;




        // array images_memo aufbauen
        session_register("images_memo");
        if ($environment["parameter"][2]) {
            if (is_array($HTTP_SESSION_VARS["images_memo"])) {
                if (in_array($environment["parameter"][2],$HTTP_SESSION_VARS["images_memo"])) {
                    unset ($HTTP_SESSION_VARS["images_memo"][$environment["parameter"][2]]);
                } else {
                    $HTTP_SESSION_VARS["images_memo"][$environment["parameter"][2]] = $environment["parameter"][2];
                }
            } else {
                $HTTP_SESSION_VARS["images_memo"][$environment["parameter"][2]] = $environment["parameter"][2];
            }
        }
        if ( $HTTP_GET_VARS["search"] != "" ) {
            $anhang = "?".$getvalues;
        } else {
            $anhang = "";
        }




        session_register("return");
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Session (return): ".$HTTP_SESSION_VARS["return"].$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "UID: ".$HTTP_SESSION_VARS["uid"].$debugging["char"];
        $ausgaben["images_aktion"] = $HTTP_SESSION_VARS["return"];


        if ($HTTP_SESSION_VARS["return"]) {
            $ausgaben["send_image"] = "<a href=".$HTTP_SESSION_VARS["return"]."?referer=".$HTTP_SESSION_VARS["referer"].">#(send_image)</a>";
        } else {
            $ausgaben["send_image"] = "";
        }





        // funktions auswahl wenn markierte files
        $anzahl = count($HTTP_SESSION_VARS["images_memo"]);
        switch ($anzahl) {
            case 0:
                $ausgaben["filemodify"] = "";
                $ausgaben["filedel"]    = "";
                break;

            case 1:
                $ausgaben["filemodify"] = "<a href=\"".$cfg["basis"]."/edit.html\">#(describe)</a>";
                $ausgaben["filedel"]    = "<a href=\"".$cfg["basis"]."/delete.html\">#(delete1)</a>";
                break;

            default:
                $ausgaben["filemodify"] = "<a href=\"".$cfg["basis"]."/edit.html\">#(describe)</a>";
                $ausgaben["filedel"]    = "<a href=\"".$cfg["basis"]."/delete.html\">#(delete2)</a>";
        }




        // filter selektoren erstellen
        foreach( $cfg["filter"] as $key => $value ) {
            unset($filter);
            $ausgaben["filter"] .= " ";
            session_register("filter".$key);
            if ( $HTTP_GET_VARS["filter".$key] != "" ) {
                $filter[$HTTP_GET_VARS["filter".$key]] = " selected";
                $HTTP_SESSION_VARS["filter".$key] = $HTTP_GET_VARS["filter".$key];
            } else {
                $filter[$HTTP_SESSION_VARS["filter".$key]] = " selected";
            }
            $ausgaben["filter"]  .= "<select name=\"filter".$key."\" onChange=\"submit()\">";
            foreach ( $value as $num => $label ) {
                $ausgaben["filter"] .= "<option value=\"".$num."\"".$filter[$num].">".$label."</option>";
            }
            $ausgaben["filter"] .= "</select>";
        }




        // art der anzeige
        session_register("art");
        if ( $HTTP_GET_VARS["art"] != "") {
            $art = $HTTP_GET_VARS["art"];
            $HTTP_SESSION_VARS["art"] = $HTTP_GET_VARS["art"];
        } else {
            $art = $HTTP_SESSION_VARS["art"];
        }

        switch ( $art ) {
            case "pdf": case "pdt": case "ods": case "odp":
                $ausgaben["4"] = " checked";
                $art = "'pdf','pdt','ods','odp'";
                break;
            default:
                $art = "'gif', 'jpg','png'";
                $ausgaben["5"] = " checked";
        }




        // Suche
        if ( $HTTP_GET_VARS["search"] != "" ) {
            $search_value = $HTTP_GET_VARS["search"];
            $ausgaben["search"] = $search_value;
            $ausgaben["result"] = "Ihre Schnellsuche nach \"".$search_value."\" hat ";
            $search_value = explode(" ",$search_value);
            $suche = array("ffname","fdesc","fhit");
            $wherea = "";
            foreach ( $search_value as $value1 ) {
                if ( $value1 != "" ) {
                    if ($getvalues == "") $getvalues = "search=";
                    $getvalues .= $value1." ";
                    foreach ($suche as $value2) {
                        if ($wherea != "") $wherea .= " or ";
                        $wherea .= $value2. " LIKE '%" .$value1."%'";
                    }
                }
            }
        }




        // sql erweitern
        switch ( $HTTP_SESSION_VARS["filter0"] ) {
            case 2:
                $whereb = "";
                $getvalues .= "&what=3";
                break;
            case 1:
                $whereb = " fdid = '".$HTTP_SESSION_VARS["custom"]."' AND";
                $getvalues .= "&what=2";
                break;
            default:
            $whereb = " fuid = '".$HTTP_SESSION_VARS["uid"]."' AND";
        }

        switch ( $HTTP_SESSION_VARS["filter1"] ) {
            case 2:
                $whereb .= " ffart in ('zip','bz2','gz')";
                $getvalues .= "&art=".$HTTP_SESSION_VARS["filter1"];
                $hidedata["other"] = array();
                break;
            case 1:
                $whereb .= " ffart in ('pdf','odt','ods','odp')";
                $getvalues .= "&art=".$HTTP_SESSION_VARS["filter1"];
                $hidedata["other"] = array();
                break;
            default:
                $whereb .= " ffart in ('gif','jpg','png')";
                $hidedata["images"] = array();


        }
        // gibt es beide
        if ($wherea && $whereb) $trenner = " AND ";
        // ist wherea da, klammern setzen
        if ($wherea) $wherea = "(".$wherea.")";
        // where zusammensetzen
        if ($wherea || $whereb) $where = " WHERE ".$wherea.$trenner.$whereb;




        ### put your code here ###

        /* z.B. db query */

        $sql = "SELECT *
                  FROM ".$cfg["db"]["file"]["entries"]."
                  ".$where."
              ORDER BY ".$cfg["db"]["file"]["order"];
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

        // seiten umschalter
        $inhalt_selector = inhalt_selector( $sql, $environment["parameter"][1], $cfg["db"]["file"]["rows"], $parameter, 1, 3, $getvalues );
        $ausgaben["inhalt_selector"] = $inhalt_selector[0];
        $sql = $inhalt_selector[1];
        $ausgaben["anzahl"] = $inhalt_selector[2];

        $result = $db -> query($sql); $i = 0;

        if ( $db->num_rows($result) == 0 ) {
            $ausgaben["result"] .= " keine Eintr�ge gefunden.";
        } else {
            // nur erweitern wenn bereits was drin steht
            if ( $ausgaben["result"] ) {
                $ausgaben["result"] .= " folgende Eintr�ge gefunden.";
            } else {
                $ausgaben["result"]  = "";
            }
        }

        while ( $data = $db -> fetch_array($result,1) ) {

            if (is_array($HTTP_SESSION_VARS["images_memo"])) {
                if (in_array($data["fid"],$HTTP_SESSION_VARS["images_memo"])) {
                    $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$data["fid"].".html".$anhang."><img width=\"13\" height\"13\" border=\"0\" src=\"".$cfg["iconpath"]."cms-cb1.png\"></a>";
                } else {
                    $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$data["fid"].".html".$anhang."><img width=\"13\" height\"13\" border=\"0\" src=\"".$cfg["iconpath"]."cms-cb0.png\"></a>";
                }
            } else {
                $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$data["fid"].".html".$anhang."><img width=\"13\" height\"13\" border=\"0\" src=".$cfg["iconpath"]."cms-cb0.png border=0></a>";
            }


            # -> $cfg["db"]["file"]["key"] "fid"
            #$dataloop["list"][$data["fid"]][0] = $data["field1"];
            #$dataloop["list"][$data["fid"]][1] = $data["field2"];

            // tabellen farben wechseln
            if ( $cfg["color"]["set"] == $cfg["color"]["a"]) {
                $cfg["color"]["set"] = $cfg["color"]["b"];
            } else {
                $cfg["color"]["set"] = $cfg["color"]["a"];
            }
            $dataloop["list"][$data["fid"]]["color"] = $cfg["color"]["set"];

            #$dataloop["list"][$data["fid"]]["href"] = "list/view,o,".$data["fid"].".html";
            $dataloop["list"][$data["fid"]]["href"] = "edit,".$data["fid"].".html";

            $dataloop["list"][$data["fid"]]["src"] = $pathvars["filebase"]["webdir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["tn"]."tn_".$data["fid"].".".$data["ffart"];
            $dataloop["list"][$data["fid"]]["alt"] = $data["ffname"];
            $dataloop["list"][$data["fid"]]["title"] = $data["ffname"];

            $dataloop["list"][$data["fid"]]["cb"] = $cb;

            $dataloop["list"][$data["fid"]]["ohref"] = "list/view,o,".$data["fid"].".html";
            $dataloop["list"][$data["fid"]]["bhref"] = "list/view,b,".$data["fid"].".html";
            $dataloop["list"][$data["fid"]]["mhref"] = "list/view,m,".$data["fid"].".html";
            $dataloop["list"][$data["fid"]]["shref"] = "list/view,s,".$data["fid"].".html";

            $i++;
            $even = $i / $cfg["db"]["file"]["line"];
            if ( is_int($even) ) {
                $dataloop["list"][$data["fid"]]["newline"] = $cfg["db"]["file"]["newline"];
            } else {
                $dataloop["list"][$data["fid"]]["newline"] = "";
            }

            #$dataloop["list"][$data["fid"]]["editlink"] = $cfg["basis"]."/edit,".$data["fid"].".html";
            #$dataloop["list"][$data["fid"]]["edittitel"] = "#(edittitel)";

            #$dataloop["list"][$data["fid"]]["deletelink"] = $cfg["basis"]."/delete,".$data["fid"].".html";
            #$dataloop["list"][$data["fid"]]["deletetitel"] = "#(deletetitel)";
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
        $ausgaben["link_new"] = $cfg["basis"]."/add.html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        $cfg["path"] = str_replace($pathvars["virtual"],"",$cfg["basis"]);
        $mapping["main"] = crc32($cfg["path"]).".list";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
            $ausgaben["inaccessible"] .= "# (edittitel) #(edittitel)<br />";
            $ausgaben["inaccessible"] .= "# (deletetitel) #(deletetitel)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        $ausgaben["form1_aktion"] = $cfg["basis"]."/list.html";
        $ausgaben["form2_aktion"] = $cfg["basis"]."/upload.html";

        // +++
        // page basics

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
