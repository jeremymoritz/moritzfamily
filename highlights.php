<?php	//	Jeremy And Christine Moritz (MoritzFamily.com)
$title = "Moritz Family Highlights";
require_once('inc/mf.php');

$year = 0;
$highlightsList = "";
$groupedHlArray = array();

foreach($highlightsJSON as $hl) {
	$groupedHlArray[$hl->year][] = $hl;
}

foreach(array_reverse($groupedHlArray, true) as $hlYear => $hlYearGroup) {
	$highlightsList .= "<li class='year'>$hlYear</li>\n";
	$year = $hlYear;

	shuffle($hlYearGroup);

	foreach($hlYearGroup as $hl) {
		$highlightsList .= "<li class='event sig_" . $hl->significance . "'>$hl->event</li>\n";
	}
}

$highlightsList = "
	<ul>
		$highlightsList
	</ul>";

?>
<?=$header;?>
<body>
<?=$topper;?>
	<?=$topnav;?>
	<section class='main' id='<?=$thisPageBaseName;?>'>
		<h1><?=$title;?></h1>
		<section>
			<h2>The Highlight Reel</h2>
			<h3>Some of Each Year's Notable Events in the Moritz Household</h3>
			<?=$highlightsList;?>
		</section>
	</section>
<?=$footer;?>
</body>
</html>
