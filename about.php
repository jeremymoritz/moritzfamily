<?php	//	Jeremy And Christine Moritz (MoritzFamily.com)
$title = "The Moritz Family";
require_once('inc/mf.php');

$picSection = array();
$maxPreviewQs = 3;	//	number of preview questions to show in questionnaire
$minQuestionRating = 3;
$truncateLength = 100;

  //	KIDS SECTION
foreach($jsonKids as $kid) {
  $lowercaseKidName = strtolower($kid->name);
  $latestQuestionnaire = end($questionnairesJSON->$lowercaseKidName);
  $curAge = getAge(strtotime($kid->birthday));
  $qAge = getAge(strtotime($kid->birthday),strtotime($latestQuestionnaire->qDate));	//	age when these questions were asked
  $ageToShow = ltrim($qAge,0) == 0 ? 0 : ltrim($qAge,0);	//	trim leading zeros unless the baby is 0
  $moreQuestionsUrl = "questionnaire.php?yr=" . date(
    'Y',
    strtotime($latestQuestionnaire->qDate)
  ) . "#$kid->name";

  $questionnaire = "
    <a
      class='d-lg-none text-danger'
      href='$moreQuestionsUrl'
    >$kid->name Questionnaire</a>
    <article class='d-none d-lg-block'>
      <h3>$kid->name's Q &amp; A (at $ageToShow years old)...</h3>";

  shuffle($latestQuestionnaire->questions);
  $cntPreviewQs = 0;
  foreach($latestQuestionnaire->questions as $thisQ) {
    if($thisQ->rating >= $minQuestionRating) {
      $truncAnswer = mb_strimwidth($thisQ->answer, 0, $truncateLength, '...');
      $questionnaire .= "
        <h4>{$thisQ->question}</h4>
        <blockquote>{$truncAnswer}</blockquote>\n";

      if(++$cntPreviewQs == $maxPreviewQs) {
        break;
      }
    }
  }

  $questionnaire .= "
      <h3><a href='$moreQuestionsUrl'>More Questions...</a></h3>
    </article>";

  $thumbPicsHtml = '';

  $infoSection = "
    <section class='infoSection kids'>
      <h1>Pictures and Information about $kid->name</h1>
      <div class='family-grid'>\n";

  for($i = 0; $i <= $curAge; $i++) {
    $tempAge = str_pad($i, 2, 0, STR_PAD_LEFT);
    $srcFilename = strtolower($kid->name) . "-" . $tempAge . ".jpg";	//	pad left with zeros to 2 places

    if($i == $curAge && !file_exists("img/family/$srcFilename")) {	//	if no recent pic has been uploaded, use the old pic
      break;
    }

    $thumbPicSrc = "img/family/thumbs/$srcFilename";
    $fullPicSrc = "img/family/$srcFilename";
    $titleAlt = "{$kid->name}, age $i";

    if($i != $curAge && file_exists($thumbPicSrc)) {
      $thumbPicsHtml .= "<div><img
        alt='$titleAlt'
        class='enlarge'
        data-bs-target='#enlarged-pic-modal'
        data-bs-toggle='modal'
        src='$thumbPicSrc'
        title='$titleAlt'
      ><br>Age $i</div>";
    }
  }

  $ageToShow = ltrim($curAge, 0) == 0 ? 0 : ltrim($curAge, 0);	//	trim leading zeros unless the baby is 0
  $infoSection .= "
      <div class='currentPic'>
        <h2 class='hidden'>$titleAlt</h2>
        <img class='img-fluid' src='$fullPicSrc' alt='$titleAlt' title='$titleAlt'>
      </div>
      <div class='verbiage'>
        <h2 class='text-center text-lg-start'>$kid->name</h2>
        <h3 class='text-center text-lg-start'>$kid->fullName (Age $ageToShow)</h3>
        $questionnaire
      </div>
      <div class='thumbs'>
        <h2 class='hidden'>Past photos of $kid->name</h2>
          $thumbPicsHtml
        </div>
      </div>
    </section>
    <hr class='my-3' />";
  $picSection[] = $infoSection;	//	assign it to the array
}

  //	GROWN-UPS SECTION
foreach($jsonAdults as $adult) {
  $curAge = getAge(strtotime($adult->birthday));
  $thumbPicsHtml = '';

  $infoSection = "
    <section class='infoSection adults'>
      <h1>Pictures and Information about $adult->name</h1>
      <div class='family-grid'>\n";

  for($i = 0; $i <= $curAge; $i++) {
    $tempAge = str_pad($i,2,0,STR_PAD_LEFT);
    $srcFilename = strtolower($adult->name) . "-" . $tempAge . ".jpg";	//	pad left with zeros to 2 places

    if($i == $curAge && !file_exists("img/family/$srcFilename")) {	//	if no recent pic has been uploaded, use the old pic
      break;
    }

    $thumbPicSrc = "img/family/thumbs/$srcFilename";
    $fullPicSrc = "img/family/$srcFilename";
    $titleAlt = "$adult->name, age $i";

    if($i != $curAge && file_exists($thumbPicSrc)) {
      $thumbPicsHtml .= "
        <div>
          <h3 class='hidden'>$titleAlt</h3>
          <img
            alt='$titleAlt'
            class='enlarge'
            data-bs-target='#enlarged-pic-modal'
            data-bs-toggle='modal'
            src='$thumbPicSrc'
            title='$titleAlt'
          ><br>
          Age $i
        </div>";
    }
  }

  $infoSection .= "
        <div class='currentPic'>
          <img
            alt='$titleAlt'
            class='img-fluid'
            src='$fullPicSrc'
            title='$titleAlt'
          >
          <div class='verbiage'>
            <h2 class='text-center text-lg-start mt-2 mt-lg-0'>$adult->name</h2>
            <h3 class='text-center text-lg-start'>$adult->fullName (Age " . ltrim($curAge,0) . ")</h3>
            <h2 class='hidden'>$titleAlt</h2>
          </div>
        </div>
        <div class='thumbs'>
          <h2 class='hidden'>Past photos of $adult->name</h2>
          $thumbPicsHtml
        </div>
      </div>
    </section>
    <hr class='my-3' />";
  $picSection[] = $infoSection;	//	assign it to the array
}

$familySection = "
  <section id='familySection'>
    <h1>Pictures and Information about each member of the Moritz Family</h1>"
    . implode(' ', $picSection)
  . "</section>"


?>
<?=$header;?>
<body class="about-page">
<?=$topnav;?>
<?=$topper;?>
  <section class='main' id='about'>
    <h1><?=$title;?></h1>
    <section>
      <h2>The Moritz Family</h2>
      <?=$familySection;?>
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
