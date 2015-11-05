<?php
  session_start();
  if($_GET['lt'] != $_SESSION['loginToken']) {
    session_destroy();
    header($_SERVER['SERVER_PROTOCOL'] . ' 499 CommAuthError', true, 499);
    exit;
  }else {
    switch($_GET['destination']) {
      case 'login': include './pages/login.php';break;
      case 'register': include './pages/register.php';break;
      case 'forgotten': include './pages/forgotten.php';break;
      default: include './pages/login.php';break;
    }
  }
  
?>