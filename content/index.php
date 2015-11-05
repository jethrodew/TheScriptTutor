<?php
  session_start();
  include_once "../utils/stutor_helper.php";
  require "$root/utils/commsecure.php";
  switch($_GET['destination']) {
    case 'home': include './pages/home.php';break;
    case 'scripts': include './pages/scripts.php';break;
    case 'account': include './pages/account.php';break;
    case 'newScript': include './pages/newScript.php';break;
    case 'renameScript': include './pages/renameScript.php';break;
    case 'newCharacter': include './pages/newCharacter.php';break;
    default: include './pages/home.php';break;
  }
?>