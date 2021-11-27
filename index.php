<?php	//	Jeremy And Christine Moritz (MoritzFamily.com)
require_once('inc/mf.php');

$cover_pic_alt = 'The Moritz Family: Jeremy and Christine, Angel, Tony, Harmony, Charity, Chase, and Symphony';
$selfies_path = '/img/family/selfies/600';
$selfies = array();
$selfies_dir = __DIR__ . $selfies_path;
if (is_dir($selfies_dir)) {
  $numberToRemoveDots = 2;
  /**
   * Yield array from items in selfies_dir in this form:
   * array('2021-05_moritz-selfie.jpg', '2020-12_moritz-selfie.jpg');
   */
  $selfies = array_slice(scandir($selfies_dir), $numberToRemoveDots);
}
?>
<?=$header;?>
<body>
<?=$topnav;?>
<?=$topper;?>
  <section class="main" id="index">
    <h1>The Moritz Family</h1>
    <div class="px-3" id="logo">
      <img
        alt="The Moritz Family"
        class="img-fluid"
        src="img/logo_moritz-family.png"
      >
    </div>

    <div id="cover-pic-wrapper">
      <img
        alt="<?= $cover_pic_alt ?>"
        class="cover-pic img-fluid"
        id="cover-pic-0"
        src="<?= $selfies_path ?>/<?= end($selfies) ?>"
      >
      <img
        alt="<?= $cover_pic_alt ?>"
        class="cover-pic img-fluid transparent"
        id="cover-pic-1"
        src="<?= $selfies_path ?>/<?= end($selfies) ?>"
      >
      <h3 id="cover-pic-caption" class="clearfix">
        <?
          $date_str = explode('_', end($selfies))[0];
          $selfie_date = date_create($date_str . '-01');

          echo date_format($selfie_date, 'F Y');
        ?>
      </h3>
    </div>

    <div class="mid-page-nav row">
      <div class="list-group col-sm-6 col-md-4 col-lg-3 m-auto">
        <?= $nav_links_html['list-group']; ?>
      </div>
    </div>

    <script>
      // selfie carousel
      const millisecondsBeforePicSwitch = 3000;
      let selfies = [<?php
        foreach($selfies as $selfie) { echo "'$selfie',\n"; }
      ?>];
      let selfieCounter = 0;
      let selfieIndex = 0;
      let picChangeBoolean = false;
      let currentSelfie = '';
      let selfieDate = '';
      let selfieMonth = '';
      const captionDiv = document.querySelector('#cover-pic-caption');
      let currentSelfieImg = document.querySelector(
        `#cover-pic-${Number(picChangeBoolean)}`
      );
      const millisToWaitForImgRender = 100;

      function setCaptionPosition() {
        currentSelfieImg = document.querySelector(
          `#cover-pic-${Number(picChangeBoolean)}`
        );

        captionDiv.style.top = `${currentSelfieImg.clientHeight}px`
      }

      setTimeout(setCaptionPosition, millisToWaitForImgRender);

      setInterval(() => {
        picChangeBoolean = !picChangeBoolean;	//	to toggle 0 or 1
        selfieIndex = selfieCounter++ % selfies.length;

        if (!selfieIndex) {
          selfies = _.shuffle(selfies);
        }

        currentSelfie = selfies[selfieIndex];
        currentSelfieImg = document.querySelector(
          `#cover-pic-${Number(picChangeBoolean)}`
        );
        currentSelfieImg.src = `<?= $selfies_path ?>/${currentSelfie}`;

        setTimeout(setCaptionPosition, millisToWaitForImgRender);

        Array.from(document.querySelectorAll('.cover-pic')).forEach(pic => pic.classList.toggle('transparent'));

        selfieDate = new Date(Date.parse(currentSelfie.split('_')[0] + '-15'));
        selfieMonth = selfieDate.toLocaleString('default', { month: 'long' });
        captionDiv.innerText = selfieMonth + ' ' + selfieDate.getFullYear();
      }, millisecondsBeforePicSwitch);
    </script>
    <div class="preload-pics" hidden>
      <?php foreach($selfies as $selfie) { echo "<img src='$selfies_path/$selfie' alt>\n"; } ?>
    </div>
  </section>
<?=$footer;?>
</body>
</html>
