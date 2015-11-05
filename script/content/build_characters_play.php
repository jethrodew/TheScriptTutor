<?php
$characters = array();
$res = pg_query_params($conn, "SELECT id,name,script_id,owner_id FROM character WHERE script_id = $1 AND owner_id = $2 ORDER BY name", array($sid,$owner));
if(pg_num_rows($res)==0) {
  echo "<li class='ownsnocharacters'> This Script Has No Characters.</li>";
}else{
  while($char = pg_fetch_row($res)) {
    $characters[$char[0]] = $char[1];
    echo "<li class='character' id='character$char[0]'><a href='#'class='playChar'>$char[1]</a></li>";
  }
}

?>