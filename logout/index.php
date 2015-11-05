<?php
  session_start();
  if(isset($_SESSION['commkey']) && isset($_SESSION['uid'])) {
    $token = randomalphanumeric(10,20);
    include "../utils/stutor_helper.php";
    require "$root/utils/dbopen.php";
    pg_query_params($conn,"INSERT INTO member_security(token) VALUES ($1) WHERE id = $2", array($token,$_SESSION['uid']));
    include "$root/utils/dbclose.php";
  }
  session_unset();
  session_destroy();
  exit;
?>