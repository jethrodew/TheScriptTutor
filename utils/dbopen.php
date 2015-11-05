<?php
  include  dirname(__FILE__)."/../include/config.default.php";
  include  dirname(__FILE__)."/../include/config.php";
  
	$conn = pg_connect("host=".$db_host." port=".$db_port."dbname=".$db_name."user=".$db_user." password=".$db_password);
  if (!$conn) { 
    header($_SERVER['SERVER_PROTOCOL'] . ' 550 DataAccess', true, 550);
    exit;
  }
?>