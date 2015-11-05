<?php
  require "$root/utils/dbopen.php";
    echo "<h2>Recording</h2>";
    //Check Has Characters
    $res = pg_query_params($conn,"SELECT id,name FROM character WHERE script_id = $1 AND owner_id = $2",array($sid,$owner));
    if(pg_num_rows($res)==0){
      header($_SERVER['SERVER_PROTOCOL'] . ' 475 NoScriptChar', true, 475);
      exit;
    }
    echo "<div id='recordingControls'>";
    echo "<a href='#' class='startRecording'> Start Recording </a>";
    //echo "<a href='#' class='pauseRecording'> Pause Recording </a>";
    echo "<a href='#' class='stopRecording'> Stop Recording </a>";
      echo "<div id='recCharacters'><p>Save Current Line Recording To Character:</p>";
      while($char = pg_fetch_row($res)) {
        echo "<a href='#' class='recChar' id='record$char[0]'>$char[1]</a>";
      }
      echo "</div>";
    echo "</div>";
    echo "<h3>Recorder Log</h3>";
    echo "<div id='recordingUploads'></div>";

  include "$root/utils/dbclose.php";
?>

