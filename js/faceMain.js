function __notify(notification) {
  $('#recordingUploads').prepend("<p>"+notification+"</p>");
}
function __authError(errorThrown) {
  if(errorThrown == 'CommAuthError') {
    window.location.replace("/");
  }
}
function __fileTrackError(error,lineErrors) {
  if(error == 'TrackFileErr') {
    var i=0;
    while(lineErrors[i]) {
      var line = '#line'+lineErrors[i];
      $(line).addClass('trackErr');
      i++;
    }
    scriptWarning("Your Script Has "+lineErrors.length+" Invalid Line Recordings, Remove or Re-Record the highlighted Lines Using Line Options before generating a complete track.");
  }
}
function __fileLineError(error,lineErrors) {
  if(error == 'TrackFileErr') {
    var line = '#line'+lineErrors[0];
    $(line).addClass('trackErr');
    scriptWarning("Line Track Missing Or Recording Was invalid, Remove or Re-Record the Highlighted Line Below Using Line Options.");
  }
}
function scriptWarning(error) {
  $('#scriptDialogue').html("<p class='scriptError'>"+error+"</p>");
}
function updateUploadDisplay() {
  if(recMon.pending > 0) {
    $('#upStatus').html('File Uploads Remaining: '+recMon.pending);
  }else {
    $('#upStatus').html('');
  }
  buildLines(recMon.scriptid);
}
function clearLinePlay() {
  $('#linePlaying').remove();
}
function displayLinePlay() {
  clearLinePlay();
  $('li[seq="'+player.seq+'"]').prepend("<span id='linePlaying'></span>");
}
function valLoginPwd() {
  $('#lemail').removeClass('invalidLogin');
  $('#lpwd').addClass('invalidLogin');
}
function valLoginReg() {
  $('#lemail').addClass('invalidLogin');
  $('#lpwd').removeClass('invalidLogin');
}
function showRecordingControls() {
  $('.pauseRecording').show();
  $('.stopRecording').show();
  $('#recCharacters').show();
}
function hideRecordingControls() {
  $('.pauseRecording').hide();
  $('.stopRecording').hide();
  $('#recCharacters').hide();
}
function trackGenerationLinks() {
  if($('#lineList').children('li').first().hasClass('ownsnolines')){
    $('.getOfflineTrack').hide();
  }
}
function scriptError(error) {
  var scriptid = $('#pageContent').children('h1').children('a').attr('id').replace(/[^0-9]/gi,'');
  $.get('/script/',{commkey:auth.commkey, uid:auth.uid, sid:scriptid},function(data){
    $('#pageContent').html(data);
    $('#pageContent').removeClass();
    $('#pageContent').addClass('script');
    $('#scriptDialogue').html("<p class='scriptError'>"+error+"</p>");
  }).fail(function(jqXHR, textStatus, errorThrown){
    __authError(errorThrown);
  });
  return false;
}
function fillTitle() {
  var title = $('#pageContent').children('h1').children('a.script').html();
  $('#rstitle').val(title);
}
function clearCharOptions() {
  $('#charOpB').remove();
}
function buildCharOptions() {
  $('#charOpB').html("<a href='#' class='renameChar'>Rename</a><a href='#' class='deleteChar'>Delete</a>");
}