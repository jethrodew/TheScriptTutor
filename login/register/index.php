<?php
function checkRes($res,$errno) {
  if(!$res) {
    pg_query("ROLLBACK");
    switch($errno) {
      case 552: header($_SERVER['SERVER_PROTOCOL'] . ' 552 DataTransError', true, 552);break;
      case 490: header($_SERVER['SERVER_PROTOCOL'] . ' 490 UserRegExist', true, 490);break;
      case 491: header($_SERVER['SERVER_PROTOCOL'] . ' 491 UserRegSError', true, 491);break;
      default: header($_SERVER['SERVER_PROTOCOL'] . ' 551 DataExecError', true, 551);
    }
    exit;
  }
}
  session_start();
  if($_POST['lt'] != $_SESSION['loginToken']) {
    session_destroy();
    header($_SERVER['SERVER_PROTOCOL'] . ' 499 CommAuthError', true, 499);
    exit;
  }else {
    include "../../utils/stutor_helper.php";
    require "$root/utils/password.php";
    if(strtolower($_POST['email']) != strtolower($_POST['email2'])) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 492 UserRegEMatch', true, 492);
      exit;
    }
    if($_POST['pwd'] != $_POST['pwd2']) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 493 UserRegPMatch', true, 493);
      exit;
    }
    //ACTIVATION EMAIL CONFIRMATION!
    $fname = preg_replace("/[^a-zA-Z-]/",'',$_POST['fname']);
    $sname = preg_replace("/[^a-zA-Z-]/",'',$_POST['sname']);
    $email = strtolower($_POST['email']); //VALIDATE
    $pwdhash = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
    $token = randomalphanumeric(10,20);

    require "$root/utils/dbopen.php";
    $res = pg_query($conn,"BEGIN");
      checkRes($res,552);
    $res = pg_query_params($conn,"INSERT INTO member(email,firstname,surname) VALUES ($1,$2,$3) RETURNING id",array($email,$fname,$sname));
      checkRes($res,490);
    $row = pg_fetch_row($res);
    $uid = $row[0];
    $res = pg_query_params($conn,"INSERT INTO member_security(id,password,token) VALUES ($1,$2,$3)", array($uid,$pwdhash,$token));
      checkRes($res,491);
    $res = pg_query("COMMIT");
      checkRes($res,552);
    include "$root/utils/dbclose.php";
    
    mkdir($filestore."/tracks/".$uid."/");
    mkdir($filestore."/offlinetracks/".$uid."/");
    $_SESSION['commkey'] = $token;
    $_SESSION['uid'] = $uid;
    echo json_encode(array("commkey"=>"$token", "uid"=>"$uid"));
  }
?>