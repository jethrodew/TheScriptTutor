<?php
  session_start();
  include "../../utils/stutor_helper.php";
  require "$root/utils/commsecure.php";

  $owner = $_SESSION['uid'];
  if(isset($_GET['sid'])) {
    $sid = $_GET['sid']; //VALIDATION
    require "$root/utils/dbopen.php";
    $res = pg_query_params($conn,"SELECT sequence FROM line WHERE owner_id = $1 AND script_id = $2 ORDER BY sequence DESC",array($owner,$sid));
    if(pg_num_rows($res) == 0) {
      $seq = 1;
    }else {
      $row = pg_fetch_row($res);
      $lastseq = (int) $row[0];
      $seq = $lastseq + 1;
    }
    echo json_encode(array("seq" => "$seq"));
    include "$root/utils/dbclose.php";
  }
?>