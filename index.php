<?php
  session_start();
  include "./utils/stutor_helper.php";
?>
<!DOCTYPE HTML>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8"></meta>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Script Tutor</title>
  <ascript src="http://cwilso.github.io/AudioContext-MonkeyPatch/AudioContextMonkeyPatch.js"></ascript>
  <link rel="stylesheet" type="text/css" href="<?php echo $root;?>ScriptTutorStyle.css.css" />
</head>
<body>
  <header>
    <div id="hLeft">
      <a href="<?php echo $root;?>" target="_blank" class="logo"></a>
      <h1 class="siteTitle">Script Tutor</h1>
    </div>
    <div id="hRight">
      <div id="upStatus"></div>
      <div id="siteMenu">
        <a href='#' id="site_menu_click">Menu <span class='optionsGlyph'></span></a>
        <ul>
          <li><a href="#" class='navigate' id="home_click">Home</a></li>
          <li><a href="#" class='navigate' id="scripts_click">Scripts</a></li>
          <li><a href="#" class='navigate' id="account_click">Account</a></li>
        </ul>
      </div>
    </div>
  </header>
  <main>
    <a name="top"></a>
    <div id="pageContent"> 

    </div>
    <!-- End of pageContent-->
    <img style='display:none;' src='<?php echo $root;?>/images/glyphicons.png'/>
    <img style='display:none;' src='<?php echo $root;?>/images/mute.png'/>
    <script type="text/javascript" src="<?php echo $root;?>/js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="<?php echo $root;?>/js/recorderjs/recorder.js"></script>
    <script type="text/javascript" src="<?php echo $root;?>/js/faceMain.js"></script>
    <script type="text/javascript" src="<?php echo $root;?>/js/recorderMain.js"></script>
    <script type="text/javascript" src="<?php echo $root;?>/js/recordingMonitor.js"></script>
    <script type="text/javascript" src="<?php echo $root;?>/js/playerMain.js"></script>
    <script type="text/javascript" src="<?php echo $root;?>/js/scriptMain.js"></script>
    <script type="text/javascript" src="<?php echo $root;?>/js/siteBuilder.js"></script>
    <script type="text/javascript" src="<?php echo $root;?>/js/scriptBuilder.js"></script>
    <script type="text/javascript">
      var auth = null;
      var ltoken = "<?php echo $_SESSION['loginToken'] = randomalphanumeric(10,15); ?>";
      $(document).ready(function() {
        <?php
          if(isset($_SESSION['uid']) && isset($_SESSION['commkey'])) {
            $commkey = $_SESSION['commkey'];
            $uid = $_SESSION['uid'];
            echo "auth = new Auth(\"$commkey\",\"$uid\");";
            echo "getContent('home','#pageContent');";
          }else{
            echo "auth = new Auth('".randomalphanumeric(10,15)."',0);";
            echo "getLoginContent('login','#pageContent');";
          }
        ?>
      });
    </script>
  </main>
</body>