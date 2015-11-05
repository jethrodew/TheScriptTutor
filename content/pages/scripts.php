<h1>Your Scripts</h1>
<div id="scriptControl">
  <a href="#" class="addScript">Add Script</a>
</div>
<div id="scriptDialogue">
</div>
<div id="script">
  <ul id="scriptList">
  <?php 
    include "../../utils/stutor_helper.php";
    include"$root/utils/dbopen.php";
    $res = pg_query_params("SELECT * FROM script WHERE owner_id = $1 ORDER BY title ASC",array($_SESSION['uid']));
    if(pg_num_rows($res)==0) {
      echo "<li class='ownsnoscripts'> You Currently Have No Scripts.</li>";
    }
    while($script = pg_fetch_row($res)) {
      echo "<li><a href='#' class='script' id='script$script[0]'>$script[2]</a></li>";
    }
    include"$root/utils/dbclose.php";
  ?>
  </ul>
</div>