<h1>Register For Script Tutor</h1>
<form name="register" id="registerForm">   
  <label>First Name</label>  
  <input id="rfname" type="text" placeholder="FirstName" required>
  <label>Second Name</label>  
  <input id="rsname" type="text" placeholder="SecondName" required>
  <label>Email</label>  
  <input id="remail" type="email" placeholder="myusername@email.com" required>
  <label>Confirm Email</label>  
  <input id="remail2" type="email" placeholder="myusername@email.com" required>   
  <label>Password</label>  
  <input id="rpwd" type="password" placeholder="password" required>
  <label>Confirm Password</label>  
  <input id="rpwd2" type="password" placeholder="password" required>  
  <input type="submit" value="Register">
</form>
<a href="#" id="login_click">Log In</a>
<a href="#" id="forgotten_click">Forgot Your Password</a>

<script type="text/javascript">
<!--
  $('#registerForm').submit(function(event) {
    event.preventDefault();
    fname = $('#rfname').val();
    sname = $('#rsname').val();
    email = $('#remail').val();
    email2 = $('#remail2').val();
    pwd = $('#rpwd').val();
    pwd2 = $('#rpwd2').val();
    //validate data
    uRegister(fname,sname,email,email2,pwd,pwd2);
  });
-->
</script>
