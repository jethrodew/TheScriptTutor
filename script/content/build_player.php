
<?php
  require "$root/utils/dbopen.php";
    echo "<h2>Player</h2>"; 
    echo "<div id='scriptDialogue'></div>";
    echo "<div id='playerFilter'>";
    echo "<h2>Mute Character Tracks</h2>";
    echo"<ul id='characterList'>";
      require "build_characters_play.php";
    echo "</ul>";
    echo "<a href='#' class='getOfflineTrack' id='".$sid."' ext='1'>Generate Offline MP3 Track</a>";
    echo "<a href='#' class='getOfflineTrack' id='".$sid."' ext='0'>Generate Offline WAV Track</a>";
    echo "</div>";
    echo "<div id='scriptDownloads'></div>";
    echo "<div id='scriptLines'>";
    echo "<h2>Lines</h2>";
    echo"<ul id='lineList'>";
      require "build_lines.php";
    echo "</ul></div>";
  include "$root/utils/dbclose.php";
?>

