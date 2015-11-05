<?php
  function authError() {
    header($_SERVER['SERVER_PROTOCOL'] . ' 499 CommAuthError', true, 499);
    exit;
  }

  if(isset($_GET['uid']) && isset($_GET['commkey'])) {
    $incoming_uid = preg_replace('/[^0-9]/','',$_GET['uid']);
    $incoming_commkey = preg_replace('/[^a-zA-Z0-9]/','',$_GET['commkey']);
  }elseif(isset($_POST['uid']) && isset($_POST['commkey'])){
    $incoming_uid = preg_replace('/[^0-9]/','',$_POST['uid']);
    $incoming_commkey = preg_replace('/[^a-zA-Z0-9]/','',$_POST['commkey']);
  }else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 498 CommAuthMissing', true, 498);
    exit;
  }
  
  if(!(isset($_SESSION['commkey']) && isset($_SESSION['uid']))) {
    authError();
  }
  if(!($_SESSION['commkey'] == $incoming_commkey && $_SESSION['uid'] == $incoming_uid)) {
    include 'dbopen.php';
    $res = pg_query_params("SELECT id,token FROM member_security WHERE id = $1 AND token = $2", array($incoming_uid,$incoming_commkey));
    if(pg_num_rows($res) == 0) {
      authError();
    }
    include 'dbclose.php';
  }
?>