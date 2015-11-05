<?php
  session_start();
  include "../../utils/stutor_helper.php";
  require "$root/utils/commsecure.php";

  $owner = $_SESSION['uid'];
  if(isset($_GET['sid'])) {
    $sid = preg_replace("/[^0-9]/",'',$_GET['sid']);
    require "$root/script/content/build_recorder.php";
  }
?>