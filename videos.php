<?php // Jeremy And Christine Moritz (MoritzFamily.com)
error_reporting(E_ALL);

$title = "The Moritz Family - Our Videos";
require_once('inc/mf.php');


$videos = array( // VideoYear | YouTube ID | Song | Artist | Height
  "2005|oVMapzrEkNc|Celebration|Kool &amp; the Gang|225",
  "2006|9QJQ4g7MQEw|Somebody Like You|Keith Urban|225",
  "2007|ApnfgkspU60|Accidentally in Love|Counting Crows|225",
  "2008|Ddshtnct5LE|The Boys are Back|High School Musical 3: Senior Year|225",
  "2009|_-6p7Lsd7Kg|What Makes You Beautiful|One Direction|225",
  "2010|AXtg2lTG8ng|Dare You to Move|Switchfoot|225",
  "2011|wtLDgSzLn9Y|We are Family|Chipmunks and Chipettes|225",
  "2012|CnNj3Ed3c4k|Holding Out for a Hero|Bonnie Tyler|225",
  "2013|AFtVpknr5xs|Everything is Awesome|Tegan and Sara|225",
  "2014|jvdO4xsIcls|Let It Go|Demi Levato|225",
  "2015|cip_E18SXc0|Uptown Funk|Mark Ronson|225",
  "2016|CVE3MitHRtM|Shake It Off|Taylor Swift|225",
  "2017|H6twFn4tmpY|Can't Stop the Feeling|Justin Timberlake|225",
  "2018|XUQ9c_shJ7w|Good To Be Alive (Hallelujah)|Andy Grammer|225",
  "2019|FAT1O-KLEDA|Happy|Pharrell Williams|225",
  "2020|23NKC_PZbbc|Help|The Beatles|225",
);

// $videosWithVimeoIds = array( // VideoYear | Vimeo ID (THIS IS NOT USED ANYMORE!!)
//  "2005|44958541",
//  "2006|49273805",
//  "2007|44958542",
//  "2008|45939800",
//  "2009|120920685",
//  "2010|44958543",
//  "2011|46391498",
//  "2012|80163689",
//  "2013|105498026",
//  "2014|149347021",
//  "2015|171333161",
//  "2016|199075924",
// );


rsort($videos);
$numVids = count($videos);

$vidWidth = 400;
$vidHeight = 300;
$countVids = 0; // counter
$columns = 2;
$vids = "
  <div class='mx-3 mx-md-4 vid-grid'>";

foreach($videos as $video) {
  // for some reason, ie9 is the only browser that seems to have a problem with video tag
  list($yr, $videoId, $song, $artist, $vidHeight) = explode("|", $video);

  $thumbSection = "";
  foreach($familyJSON as $familyGroup) { // 2 groups: Adults & Kids
    foreach($familyGroup as $familyMember) {
      $ageAtYearEnd = getAge(strtotime($familyMember->birthday), strtotime("$yr-12-31"), 1);
      $srcFilename = strtolower($familyMember->name) . "-" . str_pad($ageAtYearEnd,2,0,STR_PAD_LEFT) . ".jpg"; // pad left with zeros to 2 places

      $thumbPicSrc = "img/family/thumbs/$srcFilename";
      $titleAlt = "{$familyMember->name}, age $ageAtYearEnd";

      $thumbSection .= file_exists($thumbPicSrc) ? "<img
        alt='$titleAlt'
        class='enlarge'
        data-bs-target='#enlarged-pic-modal'
        data-bs-toggle='modal'
        src='$thumbPicSrc'
        title='$titleAlt'
      >" : "";
    }
  }
  $thumbSection = "<aside>$thumbSection</aside>";

  $posterPath = "";
  if (file_exists("vid/poster-$yr.jpg")) {
    $posterPath = " poster='vid/poster-$yr.jpg'";
  } elseif (file_exists("vid/poster-$yr.png")) {
    $posterPath = " poster='vid/poster-$yr.png'";
  }

  $vids .= "<div class='video-card'>
      <div class='vid-title'>
        $thumbSection
        <h3>" . ($yr == 2005 ? "2004-" : "") . "$yr</h3><br>
        <p>\"$song\" ~ <em>$artist</em></p>
      </div>
      <!--<video width='$vidWidth' height='$vidHeight' controls='controls'$posterPath>
        <source src='vid/moritz-montage-$yr.mp4' type='video/mp4' />-->
        <!--Fallback to YouTube-->
        <iframe
          allow='autoplay; encrypted-media'
          allowfullscreen
          class='mx-auto'
          frameborder='0'
          height='$vidHeight'
          src='https://www.youtube.com/embed/$videoId'
          width='$vidWidth'
        ></iframe>
        <!--<a href='vid/moritz-montage-$yr.mp4'>Click to Watch $yr Video Montage</a>-->
      <!--</video>-->
    </div>";
}

$vids .= "
  </div>
  <div class='vid-grid wedding-video'>
    <div class='video-card'>
      <div class='vid-title'>
        <h4 style='font-size:100%'>Jeremy &amp; Christine Wedding Slideshow 2001</h4>
      </div>
      <video width='$vidWidth' height='$vidHeight' controls='controls' poster='img/poster-wedding_slideshow.jpg'>
        <source src='vid/moritz-wedding-slideshow-2001.mp4' type='video/mp4' />

        <!--Fallback to YouTube-->
        <iframe
          allow='autoplay; encrypted-media'
          allowfullscreen
          class='mx-auto'
          frameborder='0'
          height='$vidHeight'
          src='moritz-wedding-slideshow-2001'
          width='$vidWidth'
        ></iframe>

        <!--Fallback to Vimeo-->
        <!--<iframe src='http://player.vimeo.com/video/47068724?title=0&amp;byline=0&amp;portrait=0' width='$vidWidth' height='$vidHeight'></iframe>-->
        <!--<a href='vid/moritz-wedding-slideshow-2001.mp4'>Click to Watch the Jeremy &amp; Christine Wedding Slideshow</a>-->
      </video>
    </div>
  </div>";
?>

<?=$header;?>
<body>
<?=$topnav;?>
<?=$topper;?>
  <section class='main' id='videos'>
    <h1><?=$title;?></h1>
    <section>
      <hgroup>
        <h2>Moritz Montages</h2>
        <h3 class="text-center">Montage Videos of our Family over the Years</h3>
      </hgroup>
      <p>(Video Editing by Jeremy, Christine, and David Moritz)</p>
      <?=$vids;?>
    </section>
  </section>
<?=$footer;?>

<div
  aria-hidden="true"
  class="modal fade"
  id="enlarged-pic-modal"
  tabindex="-1"
>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <button
          type="button"
          class="btn-close float-end"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
        <div class="clearfix"></div>
        <img alt id="large-pic" class="img-fluid" src>
      </div>
    </div>
  </div>
</div>
<style>
  /** Include this here to only apply this style on THIS page. */
  @media (max-width: 991px) { /* less than large (< lg) */
    .overlarge {
      display: none !important;
    }
  }
</style>
</body>
</html>
