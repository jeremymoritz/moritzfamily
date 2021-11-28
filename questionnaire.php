<?php	//	Jeremy And Christine Moritz (MoritzFamily.com)
$title = "The Moritz Kids Questionnaire";
require_once('inc/mf.php');

$allQs = array();
$preferredQuestionnaireYear = apiGet('yr', date('Y')); // get user-chosen "yr" or default to current year
foreach($jsonKids as $kid) {
  $lowercaseKidName = strtolower($kid->name);
  $kidQuestionnaires = $questionnairesJSON->$lowercaseKidName;
  $chosenQuestionnaire = null;
  $qDates = array();
  $allKidQuestionnaireYears = array();


  foreach($kidQuestionnaires as $kQ) {
    $kQYear = (string) date('Y', strtotime($kQ->qDate));
    $allKidQuestionnaireYears[] = $kQYear;
  }
  if (!in_array($preferredQuestionnaireYear, $allKidQuestionnaireYears)) {
    $preferredQuestionnaireYear = end($allKidQuestionnaireYears);
  }

  foreach($kidQuestionnaires as $kQ) {
    $kQYear = (string) date('Y', strtotime($kQ->qDate));

    if ($kQYear !== $preferredQuestionnaireYear) {	//	if this isn't the requested year, then link to the requested one
      // phpConsoleLog("$preferredQuestionnaireYear is not $kQYear");
      $qDates[] = "<a href='?yr=$kQYear#$kid->name' class='btn btn-primary'>$kQYear</a>";
    }

    if ($kQYear > $preferredQuestionnaireYear) {
      continue;
    }

    $chosenQuestionnaire = $kQ;
  }

  $chosenQuestionnaire = $chosenQuestionnaire ? $chosenQuestionnaire : end($kidQuestionnaires);
  // die(print_r($chosenQuestionnaire));

  $curAge = getAge(strtotime($kid->birthday), strtotime($chosenQuestionnaire->qDate));	//	age when these questions were asked

    //	get the big picture of this kid
  $tempAge = str_pad($curAge, 2, 0, STR_PAD_LEFT);	//	pad left with zeros to 2 places
  $srcFilename = $lowercaseKidName . "-" . $tempAge . ".jpg";
  $fullPicSrc = "img/family/$srcFilename";
  $ageToShow = ltrim($curAge, 0) == 0 ? 0 : ltrim($curAge, 0);	//	trim leading zeros unless the baby is 0
  $titleAlt = "$kid->name, age $ageToShow";

  $questionnaire = "
    <article>
      <h2>$kid->name Moritz's Q &amp; A Session</h2>
      <aside>
        <img src='$fullPicSrc' alt='$titleAlt' title='$titleAlt'>
      </aside>
      <h3>Exclusive Interview with $kid->name Moritz (age $ageToShow)...</h3>";

  $qNum = 1;
  foreach($chosenQuestionnaire->questions as $qa) {
    if($qa->rating > 1) {
      $questionnaire .= "
        <h4>" . $qNum++ . ".) {$qa->question}</h4>
        <blockquote>{$qa->answer}</blockquote>\n";
    }
  }

  $questionnaire .= "
    </article>";

  $qSection = "
    <section class='questions' id='$kid->name'>
      <h1>Questions for $kid->name</h1>
      $questionnaire";

  if (count($qDates) > 0) {
    $qSection .= "
      <p>{$kid->name}'s Additional Questionnaires:</p>
      <ul class='other-questionnaires'>
        <li>" . implode('</li><li>', $qDates) . "</li>
      </ul>";
  }

  $qSection .= "
      <hr>
    </section>";

  $allQs[] = $qSection;	//	assign it to the array
}

$questionsHTML = implode(' ', $allQs);

?>
<?=$header;?>
<body>
<?=$topnav;?>
<?=$topper;?>
  <section class='main' id='questionnaire'>
    <h1>The Moritz Kids Questionnaire</h1>
    <h2>Questionnaire Date: <?=date('l, F jS, Y', strtotime($chosenQuestionnaire->qDate));?></h2>
    <?=$questionsHTML;?>
  </section>
<?=$footer;?>
</body>
</html>
