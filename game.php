<?php
  require_once('inc/mf.php');
?>
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name='description' content='The Moritz Family: Jeremy and Christine, Angel, Tony, Harmony, Charity, Chase, and Symphony'>
  <meta name='keywords' content='Moritz, Family, Jeremy, Christine, Angel, Tony, Harmony, Charity, Chase, Symphony'>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Moritz Kid Quote Game</title>
  <link rel='shortcut icon' href='favicon.ico'>
  <!-- <link rel='stylesheet' type='text/css' href='inc/mf.css'> -->
  <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.js'></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
  <!-- <script src='inc/mf.js'></script> -->
  <link rel="apple-touch-icon" sizes="57x57" href="/img/apple-touch-icons/2017-11-kids-57.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/img/apple-touch-icons/2017-11-kids-72.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/img/apple-touch-icons/2017-11-kids-114.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/img/apple-touch-icons/2017-11-kids-144.png">
  <style>
    body {
      background: #484848;
      color: #fff;
      height: 85vh;
      /* overflow: hidden; */
    }
    #quote-game {
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    #quote-game > h2 {
      margin: 0;
      font-size: 0.8rem;
      background: #f00;
      color: #fff;
      width: 100%;
      border-bottom: 1px solid #000;
    }
    #kid-pics {
      height: 80px;
      margin-bottom: 1rem;
    }
    #kid-pics > div {
      display: flex;
      justify-content: space-between;
    }
    #kid-pics > div img {
      width: 60px;
      height: 80%;
    }
    .select-age {
      max-height: 15vh;
      justify-self: flex-end;
    }
    .select-age ul {
      display: grid;
      margin: 0;
      padding: 0;
      grid-template-columns: repeat(4, 1fr);
      grid-gap: 0.5rem;
    }
    .select-age li {
      list-style: none;
      background: #ddd;
      padding: 0.25rem;
      flex: 1;
      color: black;
      font-weight: bold;
      font-family: monospace;
      font-size: 2rem;
      cursor: pointer;
    }
    .select-age li:hover {
      box-shadow: 0 0 5px #fff;
    }
    .select-age li.active {
      background: red;
      color: #fff;
    }
    #featured-kid {
      height: 50%;
      padding: 0.25rem 0;
      position: relative;
      flex: 1 1 55vh;
    }
    .select-age li:hover {
      box-shadow: 0 0 5px #fff;
    }
    #featured-kid img {
      border: 5px solid #f00;
      box-shadow: -2px 2px 5px #fff8;
      height: 50vh;
    }
    #featured-kid h2 {
      color: #ff6;
      position: absolute;
      right: 0;
      bottom: 0;
      font-size: 10rem;
      border-radius: 100px;
      background: #3338;
      width: 10rem;
      height: 10rem;
      text-shadow: 2px 2px 0 #000, 2px -2px 0 #000, -2px 2px 0 #000, -2px -2px 0 #000;
    }
    h2 .small {
      display: none;
    }
    #featured-kid h3 {
      position: relative;
      z-index: 20;
      text-shadow: 2px 2px 0 #000, 2px -2px 0 #000, -2px 2px 0 #000, -2px -2px 0 #000;
      font-size: 4rem;
      text-align: left;
      padding-left: 1rem;
      margin-top: -2rem;
      transform: scaleX(0.8);
      transform-origin: left;
    }
  </style>
  <?=$googleAnalytics;?>
</head>
<body>
  <section class='container main text-center' id='quote-game'>
    <h2>Moritz Kid Quote Game</h2>

    <div id="featured-kid">

    </div>

    <div id="kid-pics">
<?php
  $minAge = 3;
  $maxAge = 9;

  for ($i = $minAge; $i <= $maxAge; $i++) {
    echo "<div class='age-0$i' hidden></div>\n";
  }
?>
    </div>

    <div class="select-age">
      <ul>
<?php
  for ($i = $minAge; $i <= $maxAge; $i++) {
    echo "<li onclick=\"chooseAge('0$i')\" class=\"age-0$i\">$i</li>\n";
  }
?>
      </ul>
    </div>
  </section>

  <div id="preload-large-pics" hidden>
<?php
for ($i = $minAge; $i <= $maxAge; $i++) {
  echo "<div class='age-0$i' hidden></div>\n";
}
?>
  </div>

  <script>
    function generateKid() {
      const chosenKid = _.sample(kidsList);

      $('#chosen-kid').html(_.startCase(chosenKid));
      $('#chosen-kid').addClass('alert');
      $('#kid-pic').html('<img src="img/family/' + kidsImages[chosenKid] + '.jpg" alt="' + chosenKid + '" class="img-thumbnail">');
    }

    let kidsList = [];
    let kidsImages = {};
    // _.forEach(kidsList, (kid) => $('#preload').append('<img src="img/' + kid + '.png" alt="' + kid + '">'));

    $(function documentReady() {
      $.getJSON('/inc/family.json').then(function assignKidsToLocalArray(familyJson) {
        kidsList = _.map(familyJson.kids, 'name');
        console.log(kidsList);
      });

      kidsImages = {
<?php
  //	KIDS SECTION
$lastKid = end($jsonKids); // just the last element of the array
foreach($jsonKids as $kid) {
  $curAge = getAge(strtotime($kid->birthday));

  echo "{$kid->name}: [";

  for ($i = $curAge; $i >= $minAge; $i--) {
    if ($i > $maxAge) {
      continue;
    }

    $srcFilename = strtolower($kid->name) . "-" . str_pad($i, 2, 0, STR_PAD_LEFT); //	pad left with zeros to 2 places
    echo "'$srcFilename'" . ($i > $minAge ? ',' : '');	//	adding these names to the javascript array
  }

  echo "]";

  if ($kid != $lastKid) {
    echo ", ";
  }
}
?>
      };


      console.log(kidsImages);

      _.forEach(kidsImages, oneKidImages => {
        _.forEach(oneKidImages, imgSrc => {
          $(`#kid-pics .age-${imgSrc.slice(-2)}`).append(`<img
            src="img/family/thumbs/${imgSrc}.jpg"
            alt
            onclick="updatedFeaturedKid('${imgSrc}')"
          >`);
          // $(`#preload-large-pics .age-${imgSrc.slice(-2)}`).append(`<img src="img/family/${imgSrc}.jpg" alt>`);
        });
      });
    });

    function updatedFeaturedKid(imgSrc) {
      const kidName = _.startCase(imgSrc.split('-')[0]);
      $(`#featured-kid`).empty().append(`<img
        src="img/family/${imgSrc}.jpg"
        alt
      >`).append(`
        <h2>${imgSrc.slice(-1)}<span class="small">yo</span></h2>
      `).append(`
        <h3>${kidName}</h3>
      `);
    }

    function chooseAge(age) {
      $(`#kid-pics > div`).attr('hidden', true);
      $(`#kid-pics .age-${age}`).removeAttr('hidden');

      $('.select-age li').removeClass('active');
      $(`.select-age li.age-${age}`).addClass('active');

      if ($('#featured-kid > img').length) {
        const currentKid = $('#featured-kid > img').attr('src').split('/')[2].split('-')[0]
        updatedFeaturedKid(`${currentKid}-${age}`);
      }
    }

    chooseAge(<?= "'0$minAge'"; ?>);
  </script>
</body>
</html>
