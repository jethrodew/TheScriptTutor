
var recMon = null;

function RecordingMonitor(sid) {
  this.recording = false;
  this.scriptid = parseInt(sid);
  this.queue = new Array();
  this.pending = 0;
  this.qProcessor = 0;
  this.sequence = 0;
  this.initSequenceStart();
}
RecordingMonitor.prototype.getSeq = function() {
  var temp = this.sequence;
  this.sequence++;
  return temp;
}
RecordingMonitor.prototype.startRecording = function() {
  recorder && recorder.record();
  this.recording = true;
}
RecordingMonitor.prototype.pauseRecording = function() {
  recorder && recorder.stop();
  this.recording = false;
}
RecordingMonitor.prototype.stopRecording = function() {
  recorder && recorder.stop();
  this.recording = false;
  recorder.clear();
}
RecordingMonitor.prototype.splitRecording = function(charid) {
  recorder && recorder.stop();
  recorder.exportWAV(function(blob) {
    var recQI = new RecordingQueueItem(blob,charid,recMon.getSeq());
    recMon.queue.push(recQI);
  });
  if(this.qProcessor == 0){
    window.setInterval(checkUploads,1000);
  }
  this.pending++;
  recorder.clear();
  recorder && recorder.record();
}
RecordingMonitor.prototype.initSequenceStart = function(){
  $.get('/script/line/sequence.php',{commkey:auth.commkey,uid:auth.uid,sid:this.scriptid},function(data){
    recMon.sequence = data.seq;
  },"json").fail(function() {
    __authError(errorThrown);
    recMon.sequence = -1;
  });
}
RecordingMonitor.prototype.uploadSuccess = function() {
  this.pending--;
  updateUploadDisplay();
}
function RecordingQueueItem(blob,charid,seq) {
  this.blob = blob;
  this.seq = seq;
  this.charid = charid;
  this.lid = 0;
}
function checkUploads() {
  if (recMon.queue.length > 0){
    uploadQI();
  }else {
    clearInterval(recMon.qProcessor);
    recMon.qProcessor = 0;
  }
}
function uploadQI() {
  var recQI = recMon.queue.shift();
  var data = new FormData();
  data.append('file', recQI.blob);
  data.append('commkey',auth.commkey);
  data.append('uid',auth.uid);
  data.append('sid',recMon.scriptid);
  data.append('seq',recQI.seq);
  data.append('cid',recQI.charid);
  $.ajax({
    url :  "/script/linetrack/",
    type: 'POST',
    data: data,
    contentType: false,
    processData: false,
    success: function(data) {
      recMon.uploadSuccess();
    },
    error: function(jqXHR, textStatus, errorThrown) {
      __notify('Upload Failed'); //HANDLE REQUIRED
      __authError(errorThrown);
      switch(errorThrown) {
        case 'DataLineFile': return 560; break;
        case 'FileTransErr': break;
      }
      //ERROR HANDLING REQUIRED
      recMon.queue.push(recQI);
    }
  });
}
