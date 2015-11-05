<?php
  session_start();
  include "../../../utils/stutor_helper.php";
  require "$root/utils/commsecure.php";

  $owner = $_SESSION['uid'];

  if(isset($_GET['lid'])){
    $lid = preg_replace("/[^0-9]/",'',$_GET['lid']);
    require "$root/utils/dbopen.php";
    $res = pg_query_params($conn,"SELECT linetrack_id, script_id,sequence FROM line WHERE owner_id = $1 AND id = $2", array($owner,$lid));
    if(!$res) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 551 DataExecError', true, 551);
      exit;
    }else {
      $line = pg_fetch_row($res);
      $ltid = $line[0];
      $sid = $line[1];
      $seq = $line[2];
      if($ltid == null){
        header($_SERVER['SERVER_PROTOCOL'] . ' 571 LineTrackMissing', true, 571);
        exit;
      }
      $file = $ltid.".wav";
      $path = $filestore."/tracks/".$owner."/".$sid."/".$file;
      /* Check File Errors < 150 bytes*/
      if(filesize($path) < 150) {
        $fileErrors = array($lid);
        header($_SERVER['SERVER_PROTOCOL'] . ' 481 TrackFileErr', true, 481);
        echo json_encode($fileErrors);
        exit;
      }
      header("X-Sendfile: $path");
      header("Content-Type: audio/wav");
      header('Content-Disposition: attachment; filename="'.$seq.'.wav"');
      exit;
    }
    include "$root/utils/dbclose.php";
    
  }
?>