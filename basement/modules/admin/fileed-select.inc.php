<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: fileed-select.inc.php,v 1.2 2003/10/12 01:01:27 chaot Exp $";
// "short description";
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

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $ausgaben["output"] .="<form action=\"".$cfg["basis"]."/describe,check.html\" method=\"post\" enctype=\"multipart/form-data\">";
    #$ausgaben["output"] .="<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"200000000\">";

    for ( $i = 1; $i <= $HTTP_GET_VARS["anzahl"]; $i++ ) {
        $ausgaben["output"] .="<input type=\"file\" name=\"upload".$i."\"><br>";
    }
    $ausgaben["output"] .="<input type=\"submit\" value=\"los\">";
    $ausgaben["output"] .="</form>";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
