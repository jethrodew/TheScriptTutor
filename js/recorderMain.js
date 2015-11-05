var audioContext = null,
    recorder = null,
    audioSource = null,
    inputBundle = null,
    muter = null;
function startMedia(stream) {
  audioSource = audioContext.createMediaStreamSource(stream);
  __notify('Microphone Stream initialised...');
  inputBundle = audioContext.createGain();
  __notify('Input Bundle initiliased...');
  audioSource.connect(inputBundle);
  __notify('Input Bundle connected to Stream...');
  recorder = new Recorder(inputBundle);
  __notify('Recorder initialised...');
  muter = audioContext.createGain();
  muter.gain.value = 0.0;
  __notify('Gain Created...');
  inputBundle.connect(muter);
  __notify('Gain Connected to Bundle...');
  muter.connect(audioContext.destination);
  __notify("<strong style='color:#299400;'>Script Recorder Ready</strong>");
}
function initAudio() {
  try {
  navigator.getUserMedia = navigator.getUserMedia ||
                      navigator.webkitGetUserMedia ||
                      navigator.mozGetUserMedia ||
                      navigator.msGetUserMedia;
  window.AudioContext = window.AudioContext || window.webkitAudioContext;
  window.URL = window.URL || window.webkitURL;
  audioContext = new AudioContext();                   
  }catch(e) {
    alert('No Web Audio Support'); // IMPROVE FEEDBACK
  }
  __notify("<strong style='color:#721625;'>Recorder Not Ready, Please Allow Access To Your Microphone From Your Browser.</strong>");
  navigator.getUserMedia({audio:true}, startMedia,
    function(e) {
          alert('Error getting audio.'); // IMPROVE FEEDBACK
          __notify(e);
    });
}
