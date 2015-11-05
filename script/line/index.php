<?php
  session_start();
  include "../../utils/stutor_helper.php";
  require "$root/utils/commsecure.php";
  
  function checkRes($res,$errno) {
    if(!$res) {
      pg_query("ROLLBACK");
      switch($errno) {
        case 553: header($_SERVER['SERVER_PROTOCOL'] . ' 553 DataSeqErr', true, 553);break;
        case 552: header($_SERVER['SERVER_PROTOCOL'] . ' 552 DataTransError', true, 552);break;
        case 551:
        default: header($_SERVER['SERVER_PROTOCOL'] . ' 551 DataExecError', true, 551);
      }
      exit;
    }
  }

  $owner = $_SESSION['uid'];
 
  /* Delete Line */
  if(isset($_POST['dconf']) && isset($_POST['sid']) && isset($_POST['lid'])) {
    $sid = preg_replace("/[^0-9]/",'',$_POST['sid']);
    $lid = preg_replace("/[^0-9]/",'',$_POST['lid']);
    if(preg_replace("/[^0-9]/",'',$_POST['dconf']) != 1) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 551 DataExecError', true, 551);
      exit;
    }
    require "$root/utils/dbopen.php";
    $res = pg_query("BEGIN");
      checkRes($res,552);
    $res = pg_query_params($conn,"SELECT linetrack_id FROM line WHERE owner_id = $1 AND script_id = $2 AND id = $3",array($owner,$sid,$lid));
      checkRes($res,551);
    $linetrack = pg_fetch_row($res);
    $trackid = $linetrack[0];
    $res = pg_query_params($conn,"DELETE FROM linetrack WHERE owner_id = $1 AND script_id = $2 AND id = $3",array($owner,$sid,$trackid));
      checkRes($res,551);
    $res = pg_query_params($conn,"DELETE FROM line WHERE owner_id = $1 AND script_id = $2 AND id = $3",array($owner,$sid,$lid));
      checkRes($res,551);
    $res = pg_query("COMMIT");
      checkRes($res,552);
    $path = $filestore."/tracks/".$owner."/".$sid."/";
    unlink($path.$trackid.".wav");
    unlink($path.$trackid."s.wav");
    include "$root/utils/resequence.php";
    include "$root/utils/dbclose.php";
    exit;
  }

  /* Update Line Text */
  if( isset($_POST['txt']) && isset($_POST['sid']) && isset($_POST['lid'])) {
    $sid = preg_replace("/[^0-9]/",'',$_POST['sid']);
    $lid = preg_replace("/[^0-9]/",'',$_POST['lid']);
    $text = $_POST['txt']; // Validation?

    require "$root/utils/dbopen.php";
    $res = pg_query_params($conn,"UPDATE line SET text = $1 WHERE owner_id = $2 AND script_id = $3 AND id = $4",array($text,$owner,$sid,$lid));
    if(!$res){
      header($_SERVER['SERVER_PROTOCOL'] . ' 551 DataExecError', true, 551);
      exit;
    }
    include "$root/utils/dbclose.php";
    exit;
  }

  /* Update Line Character */
  if(isset($_POST['lid']) && isset($_POST['cid']) && isset($_POST['sid'])) {
    $sid = preg_replace("/[^0-9]/",'',$_POST['sid']);
    $lid = preg_replace("/[^0-9]/",'',$_POST['lid']);
    $cid = preg_replace("/[^0-9]/",'',$_POST['cid']); // Validation?

    require "$root/utils/dbopen.php";
    $res = pg_query_params($conn,"UPDATE line SET character_id = $1 WHERE owner_id = $2 AND script_id = $3 AND id = $4",array($cid,$owner,$sid,$lid));
    if(!$res){
      header($_SERVER['SERVER_PROTOCOL'] . ' 551 DataExecError', true, 551);
      exit;
    }
    include "$root/utils/dbclose.php";
    exit;
  }

  /* Create Line */
  if(isset($_POST['sid']) && isset($_POST['cid'])) {

    $sid = preg_replace("/[^0-9]/",'',$_POST['sid']);
    $cid = preg_replace("/[^0-9]/",'',$_POST['cid']);

    require "$root/utils/dbopen.php";
    $res = pg_query_params($conn,"SELECT sequence FROM line WHERE owner_id = $1 AND script_id = $2 ORDER BY sequence DESC",array($owner,$sid));
    if(pg_num_rows($res) == 0) {
      $seq = 1;
    }else {
      $row = pg_fetch_row($res);
      $lastseq = (int) $row[0];
      $seq = $lastseq + 1; 
    }
    $res = pg_query_params($conn,"INSERT INTO line(script_id,owner_id,character_id,sequence) VALUES ($1,$2,$3,$4) RETURNING id",array($sid,$owner,$cid,$seq));
    if(!$res) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 551 DataExecError', true, 551);
      exit;
    }else {
      $line = pg_fetch_row($res);
      echo json_encode(array("lid" => "$line[0]"));
    }
    include "$root/utils/dbclose.php";
    exit;
  }

  /* Get Lines */
  if(isset($_GET['sid'])) {
    $sid = preg_replace("/[^0-9]/",'',$_GET['sid']);
    require "$root/utils/dbopen.php";
    $characters = array();
    $res = pg_query_params($conn, "SELECT id,name,script_id,owner_id FROM character WHERE script_id = $1 AND owner_id = $2", array($sid,$owner));
    while($char = pg_fetch_row($res)) {
      $characters[$char[0]] = $char[1];
    }
    require "$root/script/content/build_lines.php";
    include "$root/utils/dbclose.php";
    exit;
  }
?>