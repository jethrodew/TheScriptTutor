<?php
$res = pg_query_params($conn,"SELECT id,script_id,act_id,scene_id,text,character_id,sequence FROM line WHERE script_id = $1 AND owner_id = $2 ORDER BY sequence ASC",array($sid,$owner));
if(pg_num_rows($res)==0) {
  echo "<li class='ownsnolines'> This Script Has No Lines.</li>";
} else{
  while($line = pg_fetch_row($res)) {
    $cName = $characters[$line[5]];
    echo "<li class='line' seq='$line[6]' id='line$line[0]'><a href='#'class='lineOptions'></a><a href='#' class='playLine'><div class='lineCharacter' id='lineCharacter$line[5]'>$cName</div><div class='text'>$line[4]</div></a></li>";
  }
}

?>