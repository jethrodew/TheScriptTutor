<?php
  session_start();
  include "../utils/stutor_helper.php";
  require "$root/utils/password.php";
  
  
  if($_POST['lt'] != $_SESSION['loginToken']) {
    session_destroy();
    header($_SERVER['SERVER_PROTOCOL'] . ' 499 CommAuthError', true, 499);
    exit;
  }else {
    $email = strtolower($_POST['email']); //VALIDATE

    require "$root/utils/dbopen.php";
    $res = pg_query_params($conn,"SELECT member_security.password,member_security.token,member.id FROM member INNER JOIN member_security ON member.id = member_security.id WHERE member.email = $1", array($email));
    if(pg_num_rows($res) == 0) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 450 UserLoginReg', true, 450);
      exit;
    }
    elseif(pg_num_rows($res) == 1) {
      $row = pg_fetch_row($res);
      if(password_verify($_POST['pwd'], $row[0])) {
        $token = $row[1];
        $uid = $row[2];
        $_SESSION['uid'] = $uid;
        $_SESSION['commkey'] = $token;
        if(password_needs_rehash($_POST['pwd'])){
          
        }
        echo json_encode(array("commkey" => "$token", "uid"=>"$uid"));
      }else {
        header($_SERVER['SERVER_PROTOCOL'] . ' 451 UserLoginPwd', true, 451);
        exit;
      }
    }else {
      header($_SERVER['SERVER_PROTOCOL'] . ' 551 DataExecError', true, 551);
      exit;
    }
    include "$root/utils/dbclose.php";
  }
?>