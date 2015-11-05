<?php
  session_start();
  include "../../../utils/stutor_helper.php";
  require "$root/utils/commsecure.php";

  $owner = $_SESSION['uid'];

  /* Build Offline Track */
  if(isset($_GET['sid']) && isset($_GET['mcid']) && isset($_GET['ext'])) {
    $sid = preg_replace("/[^0-9]/",'',$_GET['sid']);
    $mcid = $_GET['mcid']; 
    $num_arr = array_filter($mcid, 'is_numeric');
    if($mcid !== $num_arr){
      header($_SERVER['SERVER_PROTOCOL'] . ' 480 TrackGenErr', true, 480);
      exit;
    }
    $extn = preg_replace("/[^0-9]/",'',$_GET['ext']);
    $extension = $extn == 0? ".wav" : ".mp3";
    sort($mcid, SORT_NUMERIC);
    $out = implode("_",$mcid);
    $outfile = "track_".$out.$extension;

    require "$root/utils/dbopen.php";
    $res = pg_query_params("SELECT owner_id,script_id,sequence,character_id,linetrack_id,id FROM line WHERE owner_id = $1 AND script_id = $2 ORDER BY sequence ASC",array($owner,$sid));
    if(!$res) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 470 NoScript', true, 470);
      exit;
    }
    $dir= $filestore."/tracks/".$owner."/".$sid."/";
    chdir($dir);
    $exec = "sox";
    $fileErrors = array();
    $errorCount = 0;
    while($line = pg_fetch_row($res)) {
      /* Check File Errors < 150 bytes*/
      if(filesize($line[4].".wav") < 150) {
        $errorCount = array_push($fileErrors, $line[5]);
      }
      if(in_array($line[3], $mcid)){
        $exec.= " ".$line[4]."s.wav"; 
      }else {
        $exec.= " ".$line[4].".wav"; 
      }
    }
    if($errorCount > 0){
      header($_SERVER['SERVER_PROTOCOL'] . ' 481 TrackFileErr', true, 481);
      echo json_encode($fileErrors);
      exit;
    }
    $exec.= " $outfile ";
    $err = exec($exec);
    //CHECK FOR ERRORS?
    rename($filestore."/tracks/$owner/$sid/$outfile",$filestore."/offlinetracks/$owner/$outfile");
    echo json_encode(array("downlink" => "$outfile"));
    exit;
  }

  /* Retrieve Offline Track */
  if(isset($_GET['name'])) {
    if(!preg_match("/^track_[0-9][_0-9]*\.[0-9a-z]{3}$/",$_GET['name'])){
      session_destroy();
      header($_SERVER['SERVER_PROTOCOL'] . ' 480 TrackGenErr', true, 480);
      exit;
    }
    $path = $filestore."/offlinetracks/$owner/".$_GET['name'];
    $ext = substr($_GET['name'],-3);
    header("X-Sendfile: $path");
    header("Content-Type: audio/".$ext);
    header('Content-Disposition: attachment; filename="'.$_GET['name'].'"');
    exit;
  }
?>