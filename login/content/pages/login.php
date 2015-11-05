<h1>Log In To Script Tutor</h1>
<form name="login" id="loginForm">   
  <label for="usermail">Email</label>  
  <input id="lemail" type="email" placeholder="myusername@email.com" required>
  <label for="password">Password</label>  
  <input id="lpwd" type="password" placeholder="password" required> 
  <input type="submit" value="Log In">
</form>
<a href="#" id="register_click">Register</a>
<a href="#" id="forgotten_click">Forgot Your Password</a>
<script type="text/javascript">
<!--
  $('#loginForm').submit(function(event) {
    event.preventDefault();
    email = $('#lemail').val();
    pwd = $('#lpwd').val();
    //VALIDATE DATA CLIENT SIDE
    uLogin(email,pwd);
  });
  
-->
</script>
<p>Welcome To The Script Tutor, an online web service to assist line learning of scripts anywhere, anytime.</p>
<div id="compat">
  <div id="compat_head">Please Ensure You Are Using The Latest Version Of One Of The Following Combinations of Operating System And Browser:</div>
  <ul>
    <li>Windows
      <ul>
        <li>Chrome</li>
        <li>Firefox</li>
        <li>Opera</li>
      </ul>
    </li>
    <li>Android
      <ul>
        <li>Chrome</li>
      </ul>
    </li>
    <li>Linux
      <ul>
        <li>Chromium</li>
        <li>Firefox</li>
      </ul>
    </li>
    <li>Apple Mac/Macbook (Apple Mobile Products Unsupported)
      <ul>
        <li>Safari</li>
      </ul>
    </li>

</div>
<p>The Script Tutor provides a line recording facility which allows you to capture the complete script and then 
  filter the playback by character to allow any number of the actors to rehearse their lines with appropriate 
  timed gaps without having to rerecord or manually edit the sound files.</p>
<p>The Script Tutor is currently running as a Demonstration version for testing and evaluation, 
   data and accounts for the service will be removed at regular intervals until the service is ready for public launch.</p>

<p>All property is owned by or permitted for use to Jethro Dew, no copying or redistribution permitted without written consent from Jethro Dew 2014.</p>
