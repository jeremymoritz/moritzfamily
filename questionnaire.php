<?php	//	Jeremy And Christine Moritz (MoritzFamily.com)
$title = "The Moritz Kids Questionnaire";
require_once('inc/mf.php');

$allQs = array();
foreach($jsonKids as $kid) {
	$latestQuestionnaire = end($kid->questionnaires);
	$curAge = getAge(strtotime($kid->birthday),strtotime($latestQuestionnaire->qDate));	//	age when these questions were asked
			
		//	get the big picture of this kid
	$tempAge = str_pad($curAge,2,0,STR_PAD_LEFT);	//	pad left with zeros to 2 places
	$srcFilename = strtolower($kid->name) . "-" . $tempAge . ".jpg";
	$fullPicSrc = "img/family/$srcFilename";
	$ageToShow = ltrim($curAge,0) == 0 ? 0 : ltrim($curAge,0);	//	trim leading zeros unless the baby is 0
	$titleAlt = "$kid->name, age $ageToShow";
	
	$questionnaire = "
		<article>
			<h2>$kid->name Moritz's Q &amp; A Session</h2>
			<aside>
				<img src='$fullPicSrc' alt='$titleAlt' title='$titleAlt'>
			</aside>
			<h3>Exclusive Interview with $kid->name Moritz (age $ageToShow)...</h3>";
	
	$qNum = 1;
	foreach($latestQuestionnaire->questions as $qa) {
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
			$questionnaire
			<hr>
		</section>";
		
	$allQs[] = $qSection;	//	assign it to the array
}

$questionsHTML = implode(' ', $allQs);

?>
<?=$header;?>
<body>
<?=$topper;?>
	<?=$topnav;?>
	<section class='main' id='questionnaire'>
		<h1>The Moritz Kids Questionnaire</h1>
		<?=$questionsHTML;?>
	</section>
<?=$footer;?>
</body>
</html>
