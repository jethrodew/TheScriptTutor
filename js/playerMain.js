function Player() {
  this.playing = false;
  this.audio = $(document.createElement("audio")).get(0);
  this.audio.src = "";
  this.buffer = $(document.createElement("audio")).get(0);
  this.buffer.src="";
  this.seq = 0;
  this.charid = 0;
  this.script = 0;
  this.muteCharacters = {};
  this.characterCount = 0;
}
Player.prototype.play = function() {
  $.get(player.audio.src).fail(function(jqXHR, textStatus, errorThrown){
        __authError(errorThrown);
        __fileLineError(errorThrown,$.parseJSON(jqXHR.responseText));
      },"JSON");
  if(player.muteCharacters[player.charid] === true) {
    player.audio.muted = true;
  }else {
    player.audio.muted = false;
  }
  displayLinePlay();
  player.audio.play();
  player.audio.addEventListener('ended', player.trackEnded);
  player.audio.addEventListener('oncanplaythrough',player.audioLoaded);
  if(player.nextTrack()){
      player.buffer.load();
  }
};
Player.prototype.pause = function() {
  this.audio.pause();
};
Player.prototype.nextTrack = function() {
  player.seq++;
  var nextLine = $('li[seq="'+player.seq+'"]').attr('id');
  if(nextLine != null){
    var lineid = nextLine.replace(/[^0-9]/gi,"");
    player.charid = $('li[seq="'+player.seq+'"]').find('.lineCharacter').attr("id").replace(/[^0-9]/gi,"");
    player.buffer.src = "/script/player/track/?commkey="+auth.commkey+"&uid="+auth.uid+"&lid="+lineid+".wav";
    return true;
  }else {
    player.buffer.src = "";
    player.buffer.autobuffer = false;
    return false;
  }
};
Player.prototype.trackEnded = function() {
  if(player.buffer.src != "") {
    player.audio = player.buffer;
    player.buffer = $(document.createElement("audio")).get(0);
    player.play();
  }else {
    player.playing = false;
  }
};
function initPlayer() {
  $('#pageContent').on('click','.playLine',function(){
    if($(this).children('.text').has('#editor').length) {
      return false;
    }
    if(player.playing){
      player.pause();
      player.currentTime = 0;
    }
    player.playing = true;
    lineid = $(this).parent().attr("id").replace(/[^0-9]/gi,"");
    player.charid = $(this).children('.lineCharacter').attr("id").replace(/[^0-9]/gi,"");
    player.seq = $(this).parent().attr("seq");
    player.audio.src = "/script/player/track/?commkey="+auth.commkey+"&uid="+auth.uid+"&lid="+lineid+".wav";
    player.play();
    return false;
  });
  $('#pageContent').on("click",".playChar",function() {
    var charid = $(this).parent().attr("id").replace(/[^0-9]/gi,"");
    if($(this).hasClass('activeChar')) {
      player.muteCharacters[charid] = false;
      $(this).removeClass('activeChar');
    }else {
      $(this).addClass('activeChar');
      player.muteCharacters[charid] = true;
    }
    return false;
  });
  $('#pageContent').on('click','.getOfflineTrack',function() {
    var mutedchars = [];
    var scriptid = $(this).attr('id').replace(/[^0-9]/gi,'');
    var extension = $(this).attr('ext').replace(/[^0-9]/gi,'');
    for (var index in player.muteCharacters) {
      if(player.muteCharacters[index] === true) {
        mutedchars.push(index);
      }
    }
    if(mutedchars.length < player.characterCount) {
      if(mutedchars.length === 0) {
        mutedchars.push('0');
      }
      $.get('/script/player/offline/',{commkey: auth.commkey, uid: auth.uid, sid:scriptid, mcid:mutedchars,ext:extension},function(data) {
        if($('#scriptDownloads').html() === "") {
          $('#scriptDownloads').append("<h2>Track Downloads</h2>");
        }
        $('#scriptDownloads').append("<a href='/script/player/offline/?commkey="+auth.commkey+"&uid="+auth.uid+"&name="+data.downlink+"'>"+data.downlink+"</a>");
      },'json').fail(function(jqXHR, textStatus, errorThrown){
        __authError(errorThrown);
        __fileTrackError(errorThrown,$.parseJSON(jqXHR.responseText));
      },'JSON');
    }else {
      scriptWarning('Silent Track Detected. Please un-mute one or more characters to generate track.');
    }
    return false;
  });
}
var player = new Player();
initPlayer();
