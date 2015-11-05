function Auth(comm_key,uid) {
  this.commkey = comm_key;
  this.uid = uid;
}
function uLogin(identity,password) {
  $.post(
    '/login/',
    {email:identity, pwd:password, lt:ltoken},
    function(data){
      auth = new Auth(data.commkey,data.uid);
      getContent('home','#pageContent');
    },
    "json").fail(function(jqXHR, textStatus, errorThrown){
      switch(errorThrown) {
        case 'CommAuthError': window.location.replace("/");break;
        case 'UserLoginReg': valLoginReg();break;
        case 'UserLoginPwd': valLoginPwd();break;
        case 'DataExecEror': return 551;break; //ISSUE WITH DATABASE (too many rows returned)
        default: return 000; //UNKNOWN LOGIN FAILURE// 
      }
    });
}
function uRegister(firstname,secondname,identity,identity2,password,password2) {
  $.post('/login/register/', {fname:firstname, sname:secondname,email:identity,email2:identity2,pwd:password,pwd2:password2,lt:ltoken},function(data) {
    auth = new Auth(data.commkey, data.uid);
    getContent('home','#pageContent');
  },"json").fail(function(jqXHR, textStatus, errorThrown){
      switch(errorThrown) {
        case 'CommAuthError': window.location.replace("/");break;
        case 'UserRegExist': return 490;break;
        case 'UserRegEMatch': return 492;break;
        case 'UserRegPMatch':return 493;break;
        case 'UserRegSError': return 491;break; //ISSUE WITH DATABASE
        case 'DataTransEror': return 552;break; //ISSUE WITH DATABASE
        default: return 000; //UNKNOWN REGISTRATION FAILURE// 
      }
    });
}
function uForgot() {
  alert('Not Yet Implemented');
}
function checkAuth() {
  $.get('/checkAuth/',{commkey: auth.commkey,uid:auth.uid}).fail(function(){
      window.location.replace("/");
  });
}
function getLoginContent(dest, update_target) {
  $.get('/login/content/', {destination:dest, lt:ltoken}, function(data) {
    $(update_target).html(data);
    $(update_target).removeClass();
  }).fail(function(jqXHR, textStatus, errorThrown){
      if(errorThrown == 'CommAuthError') {
        window.location.replace("/");
      }
  });
}
function getContent(dest, update_target, successFunction) {
  $.get('/content/',{commkey: auth.commkey, uid: auth.uid, destination: dest}, function(data) {
      $(update_target).html(data);
      $(update_target).removeClass();
      $(update_target).addClass(dest);
      successFunction && successFunction();
  }).fail(function(jqXHR, textStatus, errorThrown){
        __authError(errorThrown);
  });
}

function initBuilder() {
  $('#home_click').click(function(){
    if(!$('#pageContent').hasClass('home')) {
     getContent('home','#pageContent');
    }
    if($('#site_menu_click').hasClass('show')) {
      $('#site_menu_click').trigger('click');
    }
    return false;
  });
  $('#scripts_click').click(function(){
    if(!$('#pageContent').hasClass('scripts')) {
      getContent('scripts','#pageContent');
    }
    if($('#site_menu_click').hasClass('show')) {
      $('#site_menu_click').trigger('click');
    }
    return false;
  });
  $('#account_click').click(function(){
    if(!$('#pageContent').hasClass('account')) {
      getContent('account','#pageContent');
    }
    if($('#site_menu_click').hasClass('show')) {
      $('#site_menu_click').trigger('click');
    }
    return false;
  });
  $('#pageContent').on("click","#register_click",function(){
    getLoginContent('register','#pageContent');
    return false;
  });
  $('#pageContent').on("click","#forgotten_click",function(){
    getLoginContent('forgotten','#pageContent');
    return false;
  });
  $('#pageContent').on("click","#login_click",function(){
    getLoginContent('login','#pageContent');
    return false;
  });
  $('#pageContent').on("click","#logoff_click",function(){
    $.post('/logout/',function(data) {
    getLoginContent('login','#pageContent');
    });
    return false;
  });

  /* Responsive */
  $('#site_menu_click').click(function(){
    if($(this).hasClass('show')){
      $('#siteMenu ul').removeAttr('style');
      $(this).removeClass('show');
    }else {
      $('#siteMenu ul').show();
      $(this).addClass('show');
    }
    return false;
  });
}
window.addEventListener('load', initBuilder );