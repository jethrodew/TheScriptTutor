<?php
$res = pg_query_params($conn,"SELECT resequence($1,$2);",array($sid,$owner));
  checkRes($res,553);
?>