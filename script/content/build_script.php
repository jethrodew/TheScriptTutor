
<?php
  require "$root/utils/dbopen.php";
    $res = pg_query_params($conn,"SELECT title,id FROM script WHERE id = $1 AND owner_id = $2",array($sid,$owner));
    $row = pg_fetch_row($res);
    echo "<h1><a href='#' class='script' id='script".$sid."'>$row[0]</a></h1>";
    echo "<div id='scriptTabs'>";
    echo "<a href='#' class='script navigate' id='script$row[1]'>Summary</a>";
    echo "<a href='#' class='playScript navigate' id='script$row[1]'>Script</a>";
    echo "<a href='#' class='recordScript navigate' id='script$row[1]'>Record New</a>";
    echo "</div>";
    echo "<div id='scriptContent'>";
    echo "<h2>Summary</h2>";
    echo "<div id='manageScript'>";
    echo "<a href='#' class='editTitle' id='script$row[1]'>Edit Script Title</a>";
    echo "<a href='#' class='deleteScript' id='script$row[1]'>Delete This Script</a>";
    echo "<a href='#' class='createCharacter' id='script$row[1]'>Create Character</a>";
    echo "</div>";
    echo "<div id='scriptDialogue'></div>";
    echo "<div id='scriptCharacters'>";
    echo "<h2>Characters</h2>";
    echo"<ul id='characterList'>";
      require "build_characters_edit.php";
    echo "</ul></div>";
  include "$root/utils/dbclose.php";
    echo "</div>"; //END Script Content
?>

