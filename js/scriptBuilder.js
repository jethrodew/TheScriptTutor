function buildLines(scriptid) {
  $.get('/script/line/',{commkey: auth.commkey, uid: auth.uid, sid:scriptid},function(data) {
    $('#lineList').html(data);
  }).fail(function(jqXHR, textStatus, errorThrown){
    __authError(errorThrown);
  });
}
function buildCharacters(scriptid) {
  $.get('/script/character/',{commkey: auth.commkey, uid: auth.uid, sid:scriptid},function(data) {
    $('#characterList').html(data);
  }).fail(function(jqXHR, textStatus, errorThrown){
    __authError(errorThrown);
  });
}
function deleteCharacter(scriptid,charid,transcharid) {
  $.post('/script/character/',{commkey: auth.commkey, uid: auth.uid, sid:scriptid, cid:charid, tcid: transcharid },function() {
    $('.script').trigger('click');
  }).fail(function(jqXHR, textStatus, errorThrown) {
    __authError();
  });
}
function scriptBuilderForms() {
  $('#pageContent').on("submit","#newScriptForm",function(event) {
    event.preventDefault();
    var title = $('#nstitle').val();
    var ferror = 0;
    if(title.length > 100) {
      $('.nstitlefeedback').html("Your Script Title is Too Long (max length 100 characters)");
      ferror++;
    }
    if(title.length < 1) {
      $('.nstitlefeedback').html("Please Give Your Script a Title");
      ferror++;
    }
    if(ferror > 0) {
      return false;
    }else {
      $.post('/script/',{commkey: auth.commkey, uid: auth.uid, st: title},function(data){
        $('.ownsnoscripts').remove();
        $('#nstitle').val("");
        $('#scriptDialogue').removeClass();
        $('#scriptDialogue').html("");
        $('.addScript').html("Add Script");
        var outputS = "<li ><a href='#' class='script' id='script"+data.sid+"'>"+title+"</a></li>";
        $('#script ul').prepend(outputS);
      },"json").fail(function(jqXHR, textStatus, errorThrown){
        __authError(errorThrown);
      });
    }
    return false;
  });
  $('#pageContent').on("submit","#renameScriptForm",function(event) {
    event.preventDefault();
    var title = $('#rstitle').val();
    var scriptid = $('#pageContent').children('h1').children('a.script').attr('id').replace(/[^0-9]/gi,'');
    var ferror = 0;
    if(title.length > 100) {
      $('.rstitlefeedback').html("Your Script Title is Too Long (max length 100 characters)");
      ferror++;
    }
    if(title.length < 1) {
      $('.rstitlefeedback').html("Please Give Your Script a Title");
      ferror++;
    }
    if(ferror > 0) {
      return false;
    }else {
      $.post('/script/',{commkey: auth.commkey, uid: auth.uid, sid: scriptid, ust: title},function(){
        var title = $('#rstitle').val();
        $('#pageContent').children('h1').children('a.script').html(title);
        $('#scriptDialogue').removeClass();
        $('#scriptDialogue').html("");
        $('.editTitle').html("Edit Script Title");
      }).fail(function(jqXHR, textStatus, errorThrown){
        __authError(errorThrown);
      });
    }
    return false;
  });
  $('#pageContent').on("submit","#renameCharacterForm",function(event) {
    event.preventDefault();
    $('#charOpB').find('.subCharName').trigger('click');
    return false;
  });
  
  $('#pageContent').on("submit","#newCharacterForm",function(event) {
    event.preventDefault();
    var name = $('#ncname').val();
    ferror = 0;
    if(name.length > 15) {
      $('.ncnamefeedback').html("Your Character Name is Too Long (max length 15 characters)");
      ferror++;
    }
    if(name.length < 1) {
      $('.ncnamefeedback').html("Please Give Your Character a Name");
      ferror++;
    }
    if(ferror > 0) {
      return false;
    }else {
      var scriptid = $('.createCharacter').attr('id').replace(/[^0-9]/gi,'');
      $.post('/script/character/',{commkey: auth.commkey, uid: auth.uid, cn: name, sid: scriptid},function(data){
        $('.ownsnocharacters').remove();
        $('#scriptDialogue').removeClass().html('');
        $('.createCharacter').html("Create Character");
        buildCharacters(scriptid);
      },"json").fail(function(jqXHR, textStatus, errorThrown){
        __authError(errorThrown);
      });
    }
    return false;
  });
}
function scriptInteract() {
  $('#pageContent').on("click",".addScript",function(){
    if($('#scriptDialogue').hasClass('newScript')) {
      $('#scriptDialogue').removeClass();
      $('#scriptDialogue').html("");
      $('.addScript').html("Add Script");
    }else {
      getContent('newScript','#scriptDialogue');
      $('.addScript').html("Hide Add Script");
      $('#nstitle').focus();
    }
    return false;
  });
  $('#pageContent').on("click",".deleteScript",function(){
    if($('#scriptDialogue').hasClass('delScript')) {
      $('#scriptDialogue').removeClass();
      $('#scriptDialogue').html("");
    }else {
      var scriptid = $(this).attr('id').replace(/[^0-9]/gi,'');
      $('#scriptDialogue').html("<p>Delete This Script? <a href='#' class='confDelScript' id='delConf"+scriptid+"'>Confirm</a><a href='#' class='cancDelScript'>Cancel</a></p>");
      $('#scriptDialogue').addClass('delScript');
    }
    return false;
  });
  $('#pageContent').on("click",".confDelScript",function(){
    var scriptid = $(this).attr('id').replace(/[^0-9]/gi,'');
    $.post('/script/',{commkey: auth.commkey, uid: auth.uid, sid:scriptid, csd:'1'},function() {
      getContent('scripts','#pageContent');
    }).fail(function(jqXHR, textStatus, errorThrown) {
      __authError();
    });
    return false;
  });
  $('#pageContent').on("click",".cancDelScript",function(){
    $('#scriptDialogue').removeClass();
    $('#scriptDialogue').html("");
    return false;
  });
  $('#pageContent').on("click",".createCharacter",function(){
    if($('#scriptDialogue').hasClass('newCharacter')) {
      $('#scriptDialogue').removeClass();
      $('#scriptDialogue').html("");
      $('.createCharacter').html("Create Character");
    }else {
      getContent('newCharacter','#scriptDialogue');
      $('.createCharacter').html("Hide Character Creation Form");
      $('#ncname').focus();
    }
    return false;
  });
  $('#pageContent').on("click",".editTitle",function(){
    if($('#scriptDialogue').hasClass('renameScript')) {
      $('#scriptDialogue').removeClass();
      $('#scriptDialogue').html("");
      $('.editTitle').html("Edit Script Title");
    }else {
      getContent('renameScript','#scriptDialogue',fillTitle);
      $('.editTitle').html("Cancel Editing Title");
    }
    return false;
  });
  $('#pageContent').on("click",".charOptions",function(){
    if($(this).children('.characterName').has('#charRename').length){
      return false;
    }
    checkAuth();
    if(!($(this).parent().has('#charOpB').length)) {
      clearCharOptions();
      $(this).parent().append("<div id='charOpB'></div>");
      buildCharOptions();
    }else {
      clearCharOptions();
    }
    return false;
  });
  $('#pageContent').on('click','.renameChar',function(){
    checkAuth();
    var name = $(this).parent().siblings('.character').children('.characterName').html();
    $(this).parent().siblings('.character').children('.characterName').html("<form id='renameCharacterForm'><input id='charRename' type='text' size='15' placeholder='Name...' required/><input type='submit' value='' style='display:none;'></form>");
    $('#charRename').val(name);
    $('#charOpB').html("<a href='#' class='subCharName'>Submit</a><a href='#' class='cancCharName'>Cancel</a>");
    return false;
  });
  $('#pageContent').on('click','.subCharName',function(){
    var charid = $(this).parent().siblings('.character').attr('id').replace(/[^0-9]/gi,'');
    var charname = $('#charOpB').parent().find('#charRename').val();
    var scriptid = $('#pageContent').children('h1').children('a').attr('id').replace(/[^0-9]/gi,'');
    $.post('/script/character/',{commkey: auth.commkey, uid: auth.uid, sid: scriptid, cnm: charname, cid: charid },function() {
      var name = $('#charOpB').parent().find('#charRename').val();
      $('#charOpB').parent().find('.characterName').html(name);
      buildCharOptions();
    }).fail(function(jqXHR, textStatus, errorThrown) {
      __authError();
    });
    return false;
  });

  $('#pageContent').on('click','.cancCharName',function(){
    var name = $('#charOpB').parent().find('#charRename').val();
    $('#charOpB').parent().find('.characterName').html(name);
    buildCharOptions();
    return false;
  });
  $('#pageContent').on("click",".deleteChar",function(){
    //IF HAS LINES?!
    checkAuth();
    $('#charOpB').html("Action for Associated Lines: <a href='#' class='delCharAll'>Delete</a><a href='#' class='delCharNone'>Transfer To Another Character</a><a href='#' class='delCancel'>Cancel</a>");
    return false;
  });
  $('#pageContent').on("click",".delCharAll",function(){
    var scriptid = $('#pageContent').children('h1').children('a').attr('id').replace(/[^0-9]/gi,'');
    var charid = $(this).parent().siblings('.character').attr('id').replace(/[^0-9]/gi,'');
    var transcharid = '-1';
    deleteCharacter(scriptid,charid,transcharid);
    return false;
  });
  $('#pageContent').on("click",".delCharNone",function(){
    var scriptid = $('#pageContent').children('h1').children('a').attr('id').replace(/[^0-9]/gi,'');
    var charid = $(this).parent().siblings('.character').attr('id').replace(/[^0-9]/gi,'');
    $.get('/script/character/',{commkey: auth.commkey, uid: auth.uid, sid:scriptid, cid:charid},function(data){
      var output = "Transfer Lines To: ";
      var length = 0;
      for(var character in data) {
        output += "<a href='#' class='delTransChar' id='transchar"+character+"'>"+data[character]+"</a>";
        length++;
      }
      output += "<a href='#' class='delCancel'>Cancel</a>";
      if(length > 0){
        $('#charOpB').html(output);
      }else{
        $('#charOpB').html("<span class='optionsError'>No Characters Available To Transfer To</span> <a href='#' class='delCancel'>Return</a>");
      }
    },'JSON').fail(function(jqXHR, textStatus, errorThrown){
      __authError(errorThrown);
    });
    return false;
  });
  $('#pageContent').on("click",".delTransChar",function(){
    var scriptid = $('#pageContent').children('h1').children('a').attr('id').replace(/[^0-9]/gi,'');
    var charid = $(this).parent().siblings('.character').attr('id').replace(/[^0-9]/gi,'');
    var transcharid = $(this).attr('id').replace(/[^0-9]/gi,'');
    deleteCharacter(scriptid,charid,transcharid);
    return false;
  });

  $('#pageContent').on("click",".delCancel",function(){
    buildCharOptions();
    return false;
  });
}
function scriptBuilder() {
  $('#pageContent').on("click",".script",function(){
    var scriptid = $(this).attr('id').replace(/[^0-9]/gi,'');
    $.get('/script/',{commkey: auth.commkey, uid: auth.uid, sid:scriptid},function(data){
      $('#pageContent').html(data);
      $('#pageContent').removeClass();
      $('#pageContent').addClass('script');
    }).fail(function(jqXHR, textStatus, errorThrown){
      __authError(errorThrown);
    });
    return false;
  });
  $('#pageContent').on("click",".recordScript",function(){
    var scriptid = $(this).attr('id').replace(/[^0-9]/gi,'');
    if(recMon){
      //CHECK QUEUE TO PREVENT EXESSIVE UPLOAD COUNT
      if(recMon.scriptid != scriptid && recMon.pending > 3) {
        return scriptError("You are still pending uploads from another script, please wait for them to complete before attempting to record another script.");
      }else if(recMon.pending > 0) {
        return scriptError("Still pending uploads from your previous recording session, please wait for them to complete before continuing recording.");
      }else if(recMon.scriptid != scriptid) {
        recMon = new RecordingMonitor(scriptid);
      }
    }
    else {
      recMon = new RecordingMonitor(scriptid);
    }
      //CHECK SEQUENCE POPULATED CORRECTLY (-1 = error occured)
    $.get('/script/recorder/',{commkey: auth.commkey, uid: auth.uid, sid:scriptid},function(data){
      $('#scriptContent').html(data);
      if(!recorder) {
        initAudio();
      }else {
        __notify("<strong style='color:#299400;'>Script Recorder Ready</strong>");
      }
      hideRecordingControls();
    }).fail(function(jqXHR, textStatus, errorThrown){
      __authError(errorThrown);
      switch(errorThrown) {
        case 'NoScriptChar': return scriptError("Recording Requires Characters. Please Add Characters And Try Again.");break;
        default: return scriptError("Cannot Reach Recording Stream, Please Check Your Connection And Try Again.");
      }
    });
    return false;
  });
  $('#pageContent').on("click",".playScript", function() {
    player = new Player();
    var scriptid = $(this).attr('id').replace(/[^0-9]/gi,'');
    $.get('/script/player/',{commkey: auth.commkey, uid: auth.uid, sid:scriptid},function(data){
      if($('#characterList').children('li').first().hasClass('ownsnocharacters')){
        return scriptError("Your Script Has no Contents, Start By Adding Some Characters.");
      }
      $('#scriptContent').html(data);
      trackGenerationLinks();
      $('#characterList').children().each(function(){
        var cid = $(this).attr('id').replace(/[^0-9]/gi,'');
        player.muteCharacters[cid] = false;
        player.characterCount++;
      });
    }).fail(function(jqXHR, textStatus, errorThrown){
      __authError(errorThrown);
      return scriptError("Error Retrieving Script Contents.");
    });
    return false;
  });
  $('#pageContent').on('click','.navigate',function() {
    recMon && recMon.stopRecording();
    player.pause();
    return false;
  });
}
function initRecorderControls() {
  $('#pageContent').on('click','.startRecording',function() {
    if(recMon && recMon.recording === false && recorder) {
      checkAuth();
      if(!$(this).hasClass('paused')){
        recMon.stopRecording();
      }
      recMon.startRecording();
      updateUploadDisplay();
      __notify('Started Recording');
      showRecordingControls();
      $('.startRecording').removeClass('paused');
      $('.startRecording').html('<span class="sRecording"></span>  Recording');
      $('.startRecording').addClass('recording');
    }
    return false;
  });
  $('#pageContent').on("click",".stopRecording",function() {
    if(recMon && recMon.recording === true && recorder) {
      recMon.stopRecording();
      updateUploadDisplay();
      hideRecordingControls();
      __notify("Finished Recording");
      $('.startRecording').removeClass("recording");
      $('.startRecording').html('Start Recording');
    }
    return false;
  });
  $('#pageContent').on('click','.pauseRecording',function() {
    if(recMon && recMon.recording === true && recorder) {
        recMon.pauseRecording();
        __notify('Paused Recording');
        $('.startRecording').removeClass('recording');
        $('.startRecording').addClass('paused');
        $('.startRecording').html('Resume Recording');
        $(this).hide();
    }
    return false;
  });
  $('#pageContent').on("click",".recChar",function() {
    var charid = $(this).attr('id').replace(/[^0-9]/gi,"");
    if(recMon && recMon.recording === true && recorder) {
        recMon.splitRecording(charid);
        updateUploadDisplay();
        __notify("<span style='color:#3284DD'>Recorded Track: "+ $(this).html()+"</span>");
      }
    return false;
  });
}
scriptBuilder();
scriptInteract();
scriptBuilderForms();
initRecorderControls();