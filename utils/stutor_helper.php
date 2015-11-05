<?php
/*
 * 
 *
 *
 *
 *
 */

 #Include Config
include_once dirname(__FILE__)."/../include/config.default.php";
include_once dirname(__FILE__)."/../include/config.php";

#global functions
function randomalphanumeric($length) {
    $chars = "1234567890abcdefghijklmnopqrstuvwxyz";
    $string="";
    $charcount = strlen($chars)-1;

    for($i=0;$i<$length;$i++) {
        $s.= $chars[rand(0,$charcount)] ;
    }
    return $string;
  } 