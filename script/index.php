<?php
  session_start();
  include "../utils/stutor_helper.php";
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

  /* Update Script Title*/
  if(isset($_POST['sid']) && isset($_POST['ust'])) {
    $stitle = $_POST['ust'];
    $sid = preg_replace("/[^0-9]/",'',$_POST['sid']);
    require "$root/utils/dbopen.php";
    $res = pg_query_params($conn,"UPDATE script SET title = $1 WHERE owner_id = $2 AND id = $3",array($stitle,$owner,$sid));
    if(!$res) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 551 DataExecError', true, 551);
      exit;
    }
    include "$root/utils/dbclose.php";
    exit;
  }

  /* Delete Script */
  if(isset($_POST['sid']) && isset($_POST['csd'])) {
    if($_POST['csd'] !== '1') {
      header($_SERVER['SERVER_PROTOCOL'] . ' 470 NoScript', true, 470);
      exit;
    }
    $sid = preg_replace("/[^0-9]/",'',$_POST['sid']);
    require "$root/utils/dbopen.php";
    $res = pg_query("BEGIN");
      checkRes($res,552);
    $res = pg_query_params($conn,"DELETE FROM linetrack WHERE script_id = $1 AND owner_id = $2",array($sid,$owner));
      checkRes($res,551);
    $res = pg_query_params($conn,"DELETE FROM line WHERE script_id = $1 AND owner_id = $2",array($sid,$owner));
      checkRes($res,551);
    $res = pg_query_params($conn,"DELETE FROM character WHERE script_id = $1 AND owner_id = $2",array($sid,$owner));
      checkRes($res,551);
    $res = pg_query_params($conn,"DELETE FROM script WHERE id = $1 AND owner_id = $2",array($sid,$owner));
      checkRes($res,551);
    $res = pg_query("COMMIT");
      checkRes($res,552);
    include "$root/utils/dbclose.php";

    $dir =$filestore."/tracks/$owner/";
    chdir($dir);
    $exec = "rm -rf $sid";
    exec($exec); // NOT DELETED WARNING TO ADMIN
    exit;
  }
  
  /* Create Script */
  if(isset($_POST['st'])) {
    $stitle = $_POST['st']; //VALIDATION!!!
    require "$root/utils/dbopen.php";
    $res = pg_query_params($conn,"INSERT INTO script(title,owner_id) VALUES ($1,$2) RETURNING id",array($stitle,$owner));
    if(!$res) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 551 DataExecError', true, 551);
      exit;
    }
    $script = pg_fetch_row($res);
    $id = $script[0];
    mkdir($filestore."/tracks/".$owner."/".$id."/");
    echo json_encode(array("sid" => "$id"));
    include "$root/utils/dbclose.php";
    exit;
  }

  /* Display Script */
  if(isset($_GET['sid'])) {
    $sid = preg_replace("/[^0-9]/",'',$_GET['sid']);
    require "./content/build_script.php";
    exit;
  }
?>