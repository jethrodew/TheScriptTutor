<?php
  session_start();
  include "../../utils/stutor_helper.php";
  require "$root/utils/commsecure.php";

  function checkRes($res,$errno) {
    if(!$res) {
      pg_query("ROLLBACK");
      switch($errno) {
        case 552: header($_SERVER['SERVER_PROTOCOL'] . ' 552 DataTransError', true, 552);break;
        case 551:
        default: header($_SERVER['SERVER_PROTOCOL'] . ' 551 DataExecError', true, 551);
      }
      exit;
    }
  }

  $owner = $_SESSION['uid'];
  
  /* Line Creation From Recording */
  if(isset($_POST['sid']) && isset($_POST['seq']) && isset($_POST['cid']) && (isset($_FILES['file']) && !$_FILES['file']['error'])) {

    $sid = preg_replace("/[^0-9]/",'',$_POST['sid']); //VALIDATION
    $cid = preg_replace("/[^0-9]/",'',$_POST['cid']); //VALIDATION
    $seq = preg_replace("/[^0-9]/",'',$_POST['seq']); //VALIDATION

    require "$root/utils/dbopen.php";
    $res = pg_query($conn,"BEGIN");
      checkRes($res,552);
    $res = pg_query_params($conn,"INSERT INTO linetrack(owner_id,script_id) VALUES ($1,$2) RETURNING id",array($owner,$sid));
      checkRes($res,551);
    $linetrack = pg_fetch_row($res);
    $fname = $linetrack[0] . ".wav";
    $path = $filestore."/tracks/".$owner."/".$sid."/";
    $moved = move_uploaded_file($_FILES['file']['tmp_name'], $path.$fname);
    if(!$moved) {
      pg_query("ROLLBACK");
      header($_SERVER['SERVER_PROTOCOL'] . ' 560 DataLineFile', true, 560);
      exit;
    }

    $newdir =$filestore."/tracks/$owner/$sid/";
    chdir($newdir);
    $lengthe = "sox $fname -n stat 2>&1 | sed -n 's#^Length (seconds):[^0-9]*\([0-9.]*\)$#\\1#p'";
    $length = shell_exec($lengthe);
    $exec = "sox -n -r 44100 -c 2 ".$linetrack[0]."s.wav trim 0 $length";
    exec($exec);
    $moved = file_exists($linetrack[0]."s.wav");
    if(!$moved) {
      pg_query("ROLLBACK");
      header($_SERVER['SERVER_PROTOCOL'] . ' 560 DataLineFile', true, 560);
      exit;
    }

    $res = pg_query_params($conn,"INSERT INTO line(owner_id,script_id,sequence,character_id,linetrack_id) VALUES ($1,$2,$3,$4,$5)",array($owner,$sid,$seq,$cid,$linetrack[0]));
      checkRes($res,551);
    $res = pg_query("COMMIT");
      checkRes($res,552);
    include "$root/utils/dbclose.php";
  }

  /* Store Linetrack Against Line */
  if(isset($_POST['sid']) && isset($_POST['lid']) && (isset($_FILES['file']) && !$_FILES['file']['error'])) {

    $sid = preg_replace("/[^0-9]/",'',$_POST['sid']);
    $lid = preg_replace("/[^0-9]/",'',$_POST['lid']);

    require "$root/utils/dbopen.php";
    $res = pg_query($conn,"BEGIN");
      checkRes($res,552);
    $res = pg_query_params($conn,"SELECT linetrack_id,id FROM line WHERE id = $1 AND owner_id = $2 AND script_id = $3",array($lid,$owner,$sid));
      checkRes($res,551);
    $linetrack = pg_fetch_row($res);
    if($linetrack[0] != null){
      $fname = $linetrack[0] . ".wav";
    }else {
      $res = pg_query_params($conn,"INSERT INTO linetrack(owner_id,script_id) VALUES ($1,$2) RETURNING id",array($owner,$sid));
        checkRes($res,551);
      $linetrack = pg_fetch_row($res);
      $fname = $linetrack[0] . ".wav";
      $res = pg_query_params($conn,"UPDATE line SET linetrack_id = $1 WHERE id= $2 AND owner_id = $3 AND script_id = $4",array($linetrack[0],$lid,$owner,$sid));
        checkRes($res,551);
    }

    $path = $filestore."/tracks/".$owner."/".$sid."/";
    $moved = move_uploaded_file($_FILES['file']['tmp_name'], $path.$fname);
    if(!$moved) {
      pg_query("ROLLBACK");
      header($_SERVER['SERVER_PROTOCOL'] . ' 560 DataLineFile', true, 560);
      exit;
    }
    $newdir =$filestore."/tracks/$owner/$sid/";
    chdir($newdir);
    $lengthe = "sox $fname -n stat 2>&1 | sed -n 's#^Length (seconds):[^0-9]*\([0-9.]*\)$#\\1#p'";
    $length = shell_exec($lengthe);
    $exec = "sox -n -r 22050 -c 2 ".$linetrack[0]."s.wav trim 0 $length";
    exec($exec);
    $moved = file_exists($linetrack[0]."s.wav");
    if(!$moved) {
      pg_query("ROLLBACK");
      header($_SERVER['SERVER_PROTOCOL'] . ' 560 DataLineFile', true, 560);
      exit;
    }
    $res = pg_query("COMMIT");
      checkRes($res,552);
    include "$root/utils/dbclose.php";
  }

  /* File Trans Error */
  if($_FILES['file']['error']) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 496 FileTransErr', true, 496);
    exit;
  }
?>