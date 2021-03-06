<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: function_content.inc.php,v 1.23 2006/09/25 16:32:20 chaot Exp $";
// "content sprachabhaengig holen";
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

    function content($line, $tname) {

        global $db, $debugging, $pathvars, $specialvars, $environment, $defaults, $ausgaben, $rechte;

        if ( $specialvars["crc32"] == -1 ) {
            if ( $environment["ebene"] != "" && $tname == $environment["kategorie"] ) {
                $tname = crc32($environment["ebene"]).".".$tname;
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "crc32 tname \"".$tname."\" forced!!!".$debugging["char"];
            }
        } else {
            // ist das eine sub kategorie ?
            if ( $environment["subkatid"] != "" && $tname == $environment["katid"] ) {
                $tname = $tname.".".$environment["subkatid"];
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sub tname \"".$tname."\" forced!!!".$debugging["char"];
                #$dbtname = $tname;
            }
        }


        while ( strpos($line, "#(") !== false || strpos($line, "g(") !== false ) {

            // wo beginnt die marke
            $labelbeg = strpos($line,"#(");
            $art = "#(";
            $bez = "# (";
            $dbtname = $tname;
            if ( $labelbeg === false ) {
                $labelbeg = strpos($line,"g(");
                $art = "g(";
                $bez = "g (";
                $dbtname = "global";
            }

            // wo endet die marke (wichtig der offset!)
            $labelend = strpos($line,")",$labelbeg);
            // wie lang ist die marke
            $labellen = $labelend-$labelbeg;
            // token name extrahieren
            $label = substr($line,$labelbeg+2,$labellen-2);

            if ( strpos($label,",") !== false ) break; // javascript fix

            $sql = "SELECT html, content FROM ". SITETEXT ." WHERE tname='".$dbtname."' AND lang='".$environment["language"]."' AND label='$label'";
            #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result  = $db -> query($sql);
            $row = $db -> fetch_row($result);

            if ( !is_array($row) ) {
                // wenn "aktuelle sprache" = "default sprache" ueberfluessige fehlermeldung nicht anzeigen!
                if ( $environment["language"] != $specialvars["default_language"] ) {
                    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Language: \"".$environment["language"]."\" for #(".$label.") in template \"".$dbtname."\" not found using default: \"".$specialvars["default_language"]."\"".$debugging["char"];
                }
                $sql = "SELECT html, content FROM ". SITETEXT ." WHERE tname='$dbtname' AND lang='".$specialvars["default_language"]."' AND label='$label'";
                $result  = $db -> query($sql);
                $row = $db -> fetch_row($result);
            }

            if ( $row[1] == "" ) {
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Language: Uuuuups no default language \"".$specialvars["default_language"]."\" for #(".$label.") in template \"".$dbtname."\" found. Giving up!".$debugging["char"];
            }

            $database = $db->getDb();
            if ( is_array($_SESSION["dbzugriff"]) ) {
                // admin darf alles in seiner db !!
                if ( in_array($database,$_SESSION["dbzugriff"]) && $rechte[$specialvars["security"]["overwrite"]] == -1 ) {
                    $dbzugriff = -1;
                    $katzugriff = -1;
                // sperre fuer bestimmte templates
                } elseif ( in_array($tname,(array)$specialvars["security"]["nochk"]) ) {
                    $katzugriff = FALSE;
                    $dbzugriff = FALSE;
                // hier erfolgt der check wenn man kein admin ist und bei nicht gesperrten templates
                } else {
                    if (right_check("-1",$environment["ebene"],$environment["kategorie"],$database) != "") {
                        $dbzugriff = -1;
                        $katzugriff = -1;
                    } else {
                        $katzugriff = FALSE;
                        $dbzugriff = FALSE;
                    }
                }
            } else {
                $dbzugriff = -1;
                // admin darf alles
                if ( $rechte[$specialvars["security"]["overwrite"]] == -1 ) {
                    $katzugriff = -1;
                // sperre fuer bestimmte templates
                } elseif ( in_array($tname,(array)$specialvars["security"]["nochk"]) ) {
                    $katzugriff = FALSE;
                // hier erfolgt der check wenn man kein admin ist und bei nicht gesperrten templates
                } else {
                    if (right_check("-1",$environment["ebene"],$environment["kategorie"],$database) != "") {
                        $katzugriff = -1;
                    } else {
                        $katzugriff = FALSE;
                    }
                }
            }

            $replace = $row[1];

            // wenn content nicht in html ist und deaktiviert wurde
            if ( $row[0] != -1 && $specialvars["denyhtml"] == -1 ) {
                // html killer :)
                $pattern = "<[\!\/a-zA-Z].{0,}>";
                while ( preg_match("/".$pattern."/", $replace, $tag) ) {
                    $replace = str_replace($tag[0]," - html gel&ouml;scht! -",$replace);
                }
            }

            // cms edit link einblenden
            if ( $specialvars["editlock"] == False ) {
                // erlaubt wenn content_right nicht gesetzt und cms_edit = -1
                if ( $rechte["cms_edit"] == -1 && ( $specialvars["security"]["enable"] != -1 ) ||
                  // erlaubt wenn content_right gesetzt katzugriff und nur im
                  ( $specialvars["security"]["enable"] == -1 && $katzugriff == -1 && $dbzugriff == -1 )){

                    // konvertieren ?
                    if ( $specialvars["wysiwyg"] == "" && $row[0] == -1 ) {
                        $convert = ",,tag";
                        $signal = "c";
                    } elseif ( $specialvars["wysiwyg"] != "" && $row[0] != -1 ) {
                        $convert = ",,html";
                        $signal = "c";
                    } else {
                        $convert = "";
                        $signal = "e";
                    }
                    $editurl = $pathvars["virtual"]."/cms/edit,".$db->getDb().",".$dbtname.",".$label;

                    if ( $defaults["cms-tag"]["signal"] == "" ) {
                        $defaults["cms-tag"]["signal"] = "<img src=\"/images/default/cms-tag-";
                        $defaults["cms-tag"]["/signal"] = ".png\" width=\"4\" height=\"4\" border=\"0\" alt=\"Bearbeiten\" />";
                    }

                    // wenn es kein value, alt, title und status in der zeile gibt
                    $vorher = substr($line,$labelbeg-20,20);
                    if ( !strpos($vorher,"value=\"")
                      && !strpos($vorher,"alt=\"")
                      && !strpos($vorher,"title=\"")
                      && !strpos($vorher,"status='") ) {
                        $replace .= " <a target=\"_top\" href=\"".$editurl.$convert.".html\">".$defaults["cms-tag"]["signal"].$signal.$defaults["cms-tag"]["/signal"]."</a>";
                    } else {
                        #$line = $line."# (".$label.")&nbsp;<a target=\"_top\" href=\"".$editurl.$convert.".html\">".$defaults["cms-tag"]["signal"].$signal.$defaults["cms-tag"]["/signal"]."</a><br />\n";
                        #$ausgaben["inaccessible"] .= "# (".$label.")&nbsp;#(".$label.")<a target=\"_top\" href=\"".$editurl.$convert.".html\">".$defaults["cms-tag"]["signal"].$signal.$defaults["cms-tag"]["/signal"]."</a><br />\n";
                        $ausgaben["inaccessible"] .= $bez.$label.")&nbsp;".$art.$label.")<br />\n";
                    }
                }
            }

            // wenn content nicht in html ist
            if ( $row[0] != -1 ) {
                // intelligenten link tag bearbeiten
                $replace = intelilink($replace);
                // neues generelles tagreplace
                $replace = tagreplace($replace);
                // newlines nach br wandeln (muss zuletzt gemacht werden)
                $replace = nlreplace($replace);
            }

            // marke ersetzen
            if ( strpos($line,$art) !== false ) {
                $line = str_replace($art.$label.")",$replace,$line);
            }
        }
        return($line);
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
