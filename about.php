<?php	//	Jeremy And Christine Moritz (MoritzFamily.com)
$title = "The Moritz Family";
require_once('inc/mf.php');

$picSection = array();
$maxPreviewQs = 3;	//	number of preview questions to show in questionnaire

	//	KIDS SECTION
foreach($jsonKids as $kid) {
	$latestQuestionnaire = end($kid->questionnaires);
	$curAge = getAge(strtotime($kid->birthday));
	$qAge = getAge(strtotime($kid->birthday),strtotime($latestQuestionnaire->qDate));	//	age when these questions were asked
	$ageToShow = ltrim($qAge,0) == 0 ? 0 : ltrim($qAge,0);	//	trim leading zeros unless the baby is 0
	
	$questionnaire = "
		<article>
			<h3>$kid->name's Q &amp; A (at $ageToShow years old)...</h3>";
	
	shuffle($latestQuestionnaire->questions);
	$cntPreviewQs = 0;
	foreach($latestQuestionnaire->questions as $thisQ) {
		if($thisQ->rating >= 3) {
			$questionnaire .= "
				<h4>{$thisQ->question}</h4>
				<blockquote>{$thisQ->answer}</blockquote>\n";
			
			if(++$cntPreviewQs == $maxPreviewQs) {
				break;
			}
		}
	}
	
	$questionnaire .= "
			<h3><a href='questionnaire.php?#$kid->name'>More Questions...</a></h3>
		</article>";
	
	$infoSection = "
		<section class='infoSection kids'>
			<h1>Pictures and Information about $kid->name</h1>
			<table>
				<tr>
					<td class='thumbs'>
						<h2 class='hidden'>Past photos of $kid->name</h2>\n";
	for($i = 0; $i <= $curAge; $i++) {
		$tempAge = str_pad($i,2,0,STR_PAD_LEFT);
		$srcFilename = strtolower($kid->name) . "-" . $tempAge . ".jpg";	//	pad left with zeros to 2 places

		if($i == $curAge && !file_exists("img/family/$srcFilename")) {	//	if no recent pic has been uploaded, use the old pic
			break;
		}
		
		$thumbPicSrc = "img/family/thumbs/$srcFilename";
		$fullPicSrc = "img/family/$srcFilename";
		$titleAlt = "{$kid->name}, age $i";
		
		if($i != $curAge && file_exists($thumbPicSrc)) {
			$infoSection .= "<div><img src='$thumbPicSrc' alt='$titleAlt' class='enlarge' title='$titleAlt'><br>Age $i</div>";
		}
	}
	$ageToShow = ltrim($curAge,0) == 0 ? 0 : ltrim($curAge,0);	//	trim leading zeros unless the baby is 0
	$infoSection .= "</td>
					<td class='currentPic'>
						<h2 class='hidden'>$titleAlt</h2>
						<img src='$fullPicSrc' alt='$titleAlt' title='$titleAlt'>
					</td>
					<td class='verbiage'>
						<h2>$kid->name</h2>
						<h3>$kid->fullName (Age $ageToShow)</h3>
						$questionnaire
					</td>
				</tr>
			</table>
		</section>";
	$picSection[] = $infoSection;	//	assign it to the array
}

	//	GROWN-UPS SECTION
foreach($jsonAdults as $adult) {
	$curAge = getAge(strtotime($adult->birthday));
	
	$infoSection = "
		<section class='infoSection adults'>
			<h1>Pictures and Information about $adult->name</h1>
			<table>
				<tr>
					<td class='thumbs' colspan='2'>
						<h2 class='hidden'>Past photos of $adult->name</h2>\n";
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
			$infoSection .= "
				<div>
					<h3 class='hidden'>$titleAlt</h3>
					<img src='$thumbPicSrc' alt='$titleAlt' class='enlarge' title='$titleAlt'><br>
					Age $i
				</div>";
		}
	}
	$infoSection .= "</td>
					<td class='currentPic verbiage'>
						<h2>$adult->name</h2>
						<h3>$adult->fullName (Age " . ltrim($curAge,0) . ")</h3>
						<h2 class='hidden'>$titleAlt</h2>
						<img src='$fullPicSrc' alt='$titleAlt' title='$titleAlt'>
					</td>
				</tr>
			</table>
		</section>";
	$picSection[] = $infoSection;	//	assign it to the array
}

$familySection = "
	<section id='familySection'>
		<h1>Pictures and Information about each member of the Moritz Family</h1>"
		. implode(' ', $picSection)
	. "</section>"


?>
<?=$header;?>
<body>
<?=$topper;?>
	<?=$topnav;?>
	<section class='main' id='about'>
		<h1><?=$title;?></h1>
		<section>
			<h2>The Moritz Family</h2>
			<?=$familySection;?>
		</section>
	</section>
<?=$footer;?>
</body>
</html>
