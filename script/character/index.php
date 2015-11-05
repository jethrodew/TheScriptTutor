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

  /* Update Character Name */
  if(isset($_POST['sid']) && isset($_POST['cid']) && isset($_POST['cnm'])) {
    $cname = $_POST['cnm']; //Validation?
    $cid = preg_replace("/[^0-9]/",'',$_POST['cid']);
    $sid = preg_replace("/[^0-9]/",'',$_POST['sid']);
    require "$root/utils/dbopen.php";
    $res = pg_query_params($conn,"UPDATE character SET name = $1 WHERE script_id = $2 AND id = $3",array($cname,$sid,$cid));
    if(!$res) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 551 DataExecError', true, 551);
      exit;
    }

    include "$root/utils/dbclose.php";
    exit;
  }

  /* Delete Character */
  if(isset($_POST['sid']) && isset($_POST['cid']) && isset($_POST['tcid'])) {
    $sid = preg_replace("/[^0-9]/",'',$_POST['sid']);
    $cid = preg_replace("/[^0-9]/",'',$_POST['cid']);
    $tcid = preg_replace("/[^-0-9]/",'',$_POST['tcid']);
    require "$root/utils/dbopen.php";

    if($tcid === '-1') {
      $res = pg_query("BEGIN");
      checkRes($res,552);
      $res = pg_query_params($conn,"SELECT linetrack_id FROM line WHERE character_id = $1 AND script_id = $2 AND owner_id = $3",array($cid,$sid,$owner));
      checkRes($res,551);
      $linetracks = array();
      while($line = pg_fetch_row($res)) {
        array_push($linetracks, $line[0]);
      }
      foreach($linetracks as $trackid) {
        $res = pg_query_params($conn,"DELETE FROM linetrack WHERE owner_id = $1 AND script_id = $2 AND id = $3",array($owner,$sid,$trackid));
          checkRes($res,551);
      }
      $res = pg_query_params($conn,"DELETE FROM line WHERE owner_id = $1 AND script_id = $2 AND character_id = $3",array($owner,$sid,$cid));
      checkRes($res,551);
      $res = pg_query_params($conn,"DELETE FROM character WHERE owner_id = $1 AND script_id = $2 AND id = $3",array($owner,$sid,$cid));
      checkRes($res,551);
      $res = pg_query("COMMIT");
      checkRes($res,552);
      $path = $filestore."/tracks/".$owner."/".$sid."/";
      foreach($linetracks as $trackid) {
        unlink($path.$trackid.".wav");
        unlink($path.$trackid."s.wav");
      }
      include "$root/utils/dbclose.php";
      exit;
    }

    $res = pg_query("BEGIN");
      checkRes($res,552);
    $res = pg_query_params($conn,"UPDATE line SET character_id = $1 WHERE character_id = $2 AND script_id = $3 AND owner_id = $4",array($tcid,$cid,$sid,$owner));
      checkRes($res,551);
    $res = pg_query_params($conn,"DELETE FROM character WHERE id = $3 AND script_id = $1 AND owner_id = $2 ",array($sid,$owner,$cid));
      checkRes($res,551);
    $res = pg_query("COMMIT");
      checkRes($res,552);
    include "$root/utils/dbclose.php";
    exit;
  }

  /* Create Character */
  if(isset($_POST['cn']) && isset($_POST['sid'])) {
    $cname = $_POST['cn']; //VALIDATION
    $sid = preg_replace("/[^0-9]/",'',$_POST['sid']);
    require "$root/utils/dbopen.php";
    $res = pg_query_params($conn,"INSERT INTO character(name,owner_id,script_id) VALUES ($1,$2,$3) RETURNING id",array($cname,$owner,$sid));
    if(!$res) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 551 DataExecError', true, 551);
      exit;
    }
    $character = pg_fetch_row($res);
    $id = $character[0];
    echo json_encode(array("cid" => "$id"));
    include "$root/utils/dbclose.php";
    exit;
  }

  /* Get All Other Characters */
  if(isset($_GET['sid']) && isset($_GET['cid'])) {
    $sid = preg_replace("/[^0-9]/",'',$_GET['sid']);
    $cid = preg_replace("/[^0-9]/",'',$_GET['cid']);
    require "$root/utils/dbopen.php";
    $characters = array();
    $res = pg_query_params($conn, "SELECT id,name FROM character WHERE script_id = $1 AND owner_id = $2 AND id <> $3 ORDER BY name", array($sid,$owner,$cid));
    if(!$res) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 551 DataExecError', true, 551);
      exit;
    }
    while($char = pg_fetch_row($res)) {
    $characters[$char[0]] = $char[1];
    }
    include "$root/utils/dbclose.php";
    echo json_encode($characters);
    exit;
  }

  /* Get Characters */
  if(isset($_GET['sid'])) {
    $sid = preg_replace("/[^0-9]/",'',$_GET['sid']);
    require "$root/utils/dbopen.php";
    require "$root/script/content/build_characters_edit.php";
    include "$root/utils/dbclose.php";
    exit;
  }

?>