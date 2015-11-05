<h1>Recover Your Password</h1>
<form name="forgotten" id="forgottenForm">   
  <label for="usermail">Email</label>  
  <input id="lemail" type="email" placeholder="myusername@email.com" required>  
  <label for="password">Password</label>  
  <input id="lpwd" type="password" placeholder="password" required></li>  
  <input type="submit" value="Log In">
</form>
<a href="#" id="register_click">Register</a>
<a href="#" id="login_click">Log In</a>
<script type="text/javascript">
<!--
  $('#forgottenForm').submit(function(event) {
    event.preventDefault();
    email = $('#lemail').val();
    pwd = $('#lpwd').val();
    //validate data
    uForgot(email,pwd);
  });
-->
</script>
