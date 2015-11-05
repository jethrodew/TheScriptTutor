<h1>Your Account</h1>
<div id="accountControl">
  <a href="#" id="logoff_click">Log Off</a>
</div>
<?php 
  include "../../utils/stutor_helper.php";
  require "$root/utils/dbopen.php";
  $res = pg_query_params($conn,"SELECT firstname,surname,email FROM member WHERE id = $1", array($_SESSION['uid']));
  $mem = pg_fetch_row($res);
  include '$root/utils/dbclose.php';
?>
<p>First Name: <?php echo $mem[0]; ?></p>
<p>Second Name: <?php echo $mem[1]; ?></p>
<p>Registered Email: <?php echo $mem[2]; ?></p>
<ul>
  <li>Change Your Password</li>
  <li>Delete Account</li>
  <li></li>
</ul>