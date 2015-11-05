function recordLine(lineid,sid) {
  recLine = lineid;
  curScript = sid;
  //CHECK RECORDER READY
  recorder && recorder.stop();
  recorder && recorder.clear();
  recorder && recorder.record();
}
function finishLine() {
  $('#lineOpB').html("Pending Upload...");
  recorder && recorder.stop();
  recorder.exportWAV(function(blob) {
    var data = new FormData();
    data.append('file',blob);
    data.append('commkey',auth.commkey);
    data.append('uid',auth.uid);
    data.append('sid',curScript);
    data.append('lid',recLine);
    $.ajax({
      url : "/script/linetrack/",
      type: 'POST',
      data: data,
      contentType: false,
      processData: false,
      success: function() {
        resetRecorder();
        buildLineOptions();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        __authError(errorThrown);
        //ERROR HANDLE
      }
    });
  });
}
function clearEditor(){
  var edit = $('#editor').get(0);
  $(edit).parent().html(lineText);
  lineText = "";
}
function resetRecorder() {
  recLine = 0;
  curScript = 0;
  recorder && recorder.clear();
}
function clearLineOptions() {
  clearEditor();
  resetRecorder();
  $('#lineOpB').remove();
}
function buildLineOptions() {
  $('#lineOpB').html("<a href='#' class='reRecordLine'>Record</a><a href='#' class='editLineText'>Edit Text</a><a href='#' class='changeLineChar'>Change Character</a><a href='#' class='deleteLine'>Delete</a>");
}
function initScript() {
  $('#pageContent').on('click','.lineOptions',function() {
    checkAuth();
    if(!($(this).parent().has('#lineOpB').length)) {
      clearLineOptions();
      $(this).parent().append("<div id='lineOpB'></div>");
      buildLineOptions();
    }else {
      clearLineOptions();
    }
    return false;
  });
  
  /* DELETE LINE */
  $('#pageContent').on('click','.deleteLine',function() {
    checkAuth();
    $('#lineOpB').html("Delete This Line? <a href='#' class='confDelete'>Confirm</a><a href='#' class='cancDelete'>Cancel</a>");
    return false;
  });
  $('#pageContent').on('click','.confDelete',function() {
    var lineid = $(this).parent().parent().attr('id').replace(/[^0-9]/gi,'');
    var scriptid = $('#pageContent').children('h1').children('a').attr('id').replace(/[^0-9]/gi,'');
    $.post('/script/line/',{commkey: auth.commkey, uid: auth.uid, sid:scriptid, lid:lineid, dconf:'1'},function() {
      buildLines(scriptid);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        __authError(errorThrown);
        //ERROR HANDLE
      });
    return false;
  });
  $('#pageContent').on('click','.cancDelete',function() {
    buildLineOptions();
    return false;
  });

  /* EDIT LINE */
  $('#pageContent').on('click','.editLineText',function() {
    checkAuth();
    clearEditor();
    lineText = $(this).parent().siblings('.playLine').children('.text').html();
    $(this).parent().siblings('.playLine').children('.text').html("<textarea id='editor'>" + lineText + "</textarea>");
    $('#lineOpB').html("<a href='#' class='submitText'>Accept</a><a href='#' class='cancelText'>Cancel</a>");
    return false;
  });
  $('#pageContent').on('click','.submitText',function() {
    var lineid = $(this).parent().parent().attr('id').replace(/[^0-9]/gi,'');
    var scriptid = $('#pageContent').children('h1').children('a').attr('id').replace(/[^0-9]/gi,'');
    text = $('#editor').val();
    if(text != lineText) {
      lineText = text;
      $.post('/script/line/',{commkey: auth.commkey, uid: auth.uid, sid:scriptid, lid:lineid,txt:lineText},function(data) {
        clearEditor();
      }).fail(function(jqXHR, textStatus, errorThrown) {
        __authError(errorThrown);
        //ERROR HANDLE
      });
    }else{
      lineText = text;
      clearEditor();
    }
    buildLineOptions();
    return false;
  });
  $('#pageContent').on('click','.cancelText',function() {
    clearEditor();
    buildLineOptions();
    return false;
  });

  /* RERECORD LINE */
  $('#pageContent').on('click','.reRecordLine', function() {
    checkAuth();
    $('#lineOpB').html("<a href='#' class='startLRec'>Start Recording</a><a href='#' class='stopLRec'>Finish Recording</a><a href='#' class='cancLRec'>Cancel Recording</a>");
    $('#lineOpB').children('.stopLRec').hide();
    if(recorder === null) {
      initAudio();
    }
    return false;
  });
  $('#pageContent').on('click','.startLRec',function() {
    checkAuth();
    if((!recMon) || (!recorder)) {
      $('#lineOpB').prepend("<span class='optionsError' id='micLineError'>Mic not ready, please allow access via your browser.</span>");
      return false;
    }else {
      $('#micLineError').remove();
    }
    var lineid = $(this).parent().parent().attr('id').replace(/[^0-9]/gi,'');
    var scriptid = $('#pageContent').children('h1').children('a').attr('id').replace(/[^0-9]/gi,'');
    if(recLine === 0 && curScript === 0){
      recordLine(lineid,scriptid);
      $(this).parent().prepend("<span class='lrecording'></span>"); //FEEDBACK
      $(this).hide();
      $(this).siblings('.stopLRec').show();
    }else {
      //FEEDBACK! NOT READY YET
    }
    return false;
  });
  $('#pageContent').on('click','.stopLRec',function() {
    finishLine();
    $(this).parent().children('.lrecording').remove(); //FEEDBACK
    $(this).hide();
    $(this).siblings('.startLRec').show();
    return false;
  });
  $('#pageContent').on('click','.cancLRec',function() {
    resetRecorder();
    buildLineOptions();
    return false;
  });
  
  /* CHANGE CHARACTER */
  $('#pageContent').on('click','.changeLineChar',function() {
    $('#lineOpB').html("");
    $('.character').each(function() {
      $('#lineOpB').append("<a href='#' id='"+$(this).attr("id")+"'class='mLineChar'>"+$(this).children().html()+"</a>");
    });
    $('#lineOpB').append("<a href='#' class='cLineChar'>Cancel</a>");
    return false;
  });
  $('#pageContent').on('click','.mLineChar',function() {
    var lineid = $(this).parent().parent().attr('id').replace(/[^0-9]/gi,'');
    curScript = $('#pageContent').children('h1').children('a').attr('id').replace(/[^0-9]/gi,'');
    var ocid = $(this).parent().siblings('.playLine').children('.lineCharacter').attr('id').replace(/[^0-9]/gi,'');
    var ncid = $(this).attr('id').replace(/[^0-9]/gi,'');
    if(ncid != ocid) {
      $.post('/script/line/',{commkey: auth.commkey, uid: auth.uid, sid:curScript, lid:lineid,cid:ncid},function(data) {
        buildLines(curScript);
        curScript = 0;
      }).fail(function(jqXHR, textStatus, errorThrown) {
        __authError(errorThrown);
        //ERROR HANDLE
        curScript = 0;
      });
    }
    return false;
  });
  $('#pageContent').on('click','.cLineChar',function() {
    buildLineOptions();
    curscript = 0;
    return false;
  });
}
var recLine = 0;
var curScript = 0;
var lineText = "";
initScript();