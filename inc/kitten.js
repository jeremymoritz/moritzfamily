/*	JAVASCRIPT FOR PAGE CATASTROPHE STORY!	*/

function goFwd() {
  var totalpages = 25;
  var page = document.getElementById('kittenpage');
  var backbtn = document.getElementById('backbtn');
  var fwdbtn = document.getElementById('fwdbtn');
  var num = parseInt(page.alt) + 1;

  page.alt = num;
  page.src = 'img/kitten/kitten' + num + '.jpg';

  if (num > 2) {
    //	this part is unneccessary if we're navigating away FROM page 1
    var oldtext = document.getElementById('text' + (num - 1));
    oldtext.style.display = 'none';
  }

  var text = document.getElementById('text' + num);
  text.style.display = 'block';

  backbtn.style.display = 'inline';

  if (num >= totalpages) {
    fwdbtn.style.display = 'none';
  }
}
function goBack() {
  var page = document.getElementById('kittenpage');
  var fwdbtn = document.getElementById('fwdbtn');
  var backbtn = document.getElementById('backbtn');
  var num = parseInt(page.alt) - 1;

  page.alt = num;
  page.src = 'img/kitten/kitten' + num + '.jpg';

  var oldtext = document.getElementById('text' + (num + 1));
  oldtext.style.display = 'none';

  if (num > 1) {
    //	this part is unneccessary if we're navigating away TO page 1
    var text = document.getElementById('text' + num);
    text.style.display = 'block';
  }

  fwdbtn.style.display = 'inline';

  if (num <= 1) {
    backbtn.style.display = 'none';
  }
}
