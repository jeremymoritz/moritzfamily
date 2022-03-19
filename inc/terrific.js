/*	JAVASCRIPT FOR LITTLE MISS TERRIFIC STORY!	*/
window.onload = initAll;

var totalpages = 28;
var lmtPages = new Array(); //	all images

function initAll() {
  document.getElementById('backbtn').onclick = goBack;
  document.getElementById('fwdbtn').onclick = goFwd;
  document.onkeydown = keyHit; //	allow use of arrow keys to advance story also!

  //	preLoad Images
  for (var j = 1; j <= totalpages; j++) {
    lmtPages[j] = new Image();
    lmtPages[j].src = 'img/lmt/lmt' + j + '.jpg';
  }
}

function keyHit(evt) {
  var thisKey;
  var leftArrow = 37; //	numeric value for left arrow key
  var rightArrow = 39; //	numeric value for right arrow key

  if (evt) {
    thisKey = evt.which;
  } else {
    thisKey = window.event.keyCode;
  }

  if (thisKey == leftArrow) {
    goBack();
  } else if (thisKey == rightArrow) {
    goFwd();
  }
  return false;
}

function goFwd() {
  var page = document.getElementById('lmtpage');
  var backbtn = document.getElementById('backbtn');
  var fwdbtn = document.getElementById('fwdbtn');

  page.alt = parseInt(page.alt) + 1;
  //page.src = '/images/lmt/lmt' + page.alt + '.jpg';
  page.src = lmtPages[page.alt].src;

  backbtn.style.display = 'inline';

  if (page.alt >= totalpages) {
    fwdbtn.style.display = 'none';
  }
}

function goBack() {
  var page = document.getElementById('lmtpage');
  var backbtn = document.getElementById('backbtn');
  var fwdbtn = document.getElementById('fwdbtn');

  page.alt = parseInt(page.alt) - 1;
  //page.src = '/images/lmt/lmt' + page.alt + '.jpg';
  page.src = lmtPages[page.alt].src;

  fwdbtn.style.display = 'inline';

  if (page.alt <= 1) {
    backbtn.style.display = 'none';
  }
}
