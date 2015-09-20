<?php	//	Jeremy And Christine Moritz (MoritzFamily.com)
error_reporting(E_ALL);

$title = "The Moritz Family - Our Videos";
require_once('inc/mf.php');

$videos = array(	//	VideoYear | Vimeo ID | Song | Artist
	"2005|44958541|Celebration|Kool &amp; the Gang",
	"2006|49273805|Somebody Like You|Keith Urban",
	"2007|44958542|Accidentally in Love|Counting Crows",
	"2008|45939800|The Boys are Back|High School Musical 3: Senior Year",
	"2009|120920685|What Makes You Beautiful|One Direction",
	"2010|44958543|Dare You to Move|Switchfoot",
	"2011|46391498|We are Family|Chipmunks and Chipettes",
	"2012|80163689|Holding Out for a Hero|Bonnie Tyler",
	"2013|105498026|Everything is Awesome|Tegan and Sara"
);
rsort($videos);
$numVids = count($videos);

$vidWidth = 400;
$vidHeight = 300;
$countVids = 0;	//	counter
$columns = 2;
$vids = "
	<table id='vidTable'>";

foreach($videos as $video) {
		//	for some reason, ie9 is the only browser that seems to have a problem with video tag
	list($yr, $vimeoID, $song, $artist) = explode("|", $video);

	$thumbSection = "";
	foreach($familyJSON as $familyGroup) {	//	2 groups: Adults & Kids
		foreach($familyGroup as $familyMember) {
			$ageAtYearEnd = getAge(strtotime($familyMember->birthday), strtotime("$yr-12-31"), 1);
			$srcFilename = strtolower($familyMember->name) . "-" . str_pad($ageAtYearEnd,2,0,STR_PAD_LEFT) . ".jpg";	//	pad left with zeros to 2 places

			$thumbPicSrc = "img/family/thumbs/$srcFilename";
			$titleAlt = "{$familyMember->name}, age $ageAtYearEnd";

			$thumbSection .= file_exists($thumbPicSrc) ? "<img src='$thumbPicSrc' alt='$titleAlt' class='enlarge' title='$titleAlt'>" : "";
		}
	}
	$thumbSection = "<aside>$thumbSection</aside>";
	$vids .= (++$countVids % $columns != 0 ? "<tr>" : "") . "
		<td" . (($countVids == $numVids) && ($countVids % $columns != 0) ? " colspan='$columns'" : "") . ">
			<div class='vidTitle'>
				$thumbSection
				<h3>" . ($yr == 2005 ? "2004-" : "") . "$yr</h3><br>
				<p>\"$song\" ~ <em>$artist</em></p>
			</div>
			<!--[if IE 9]><div class='hidden'><![endif]-->
			<!--<video width='$vidWidth' height='$vidHeight' controls='controls'" . (file_exists("vid/poster-$yr.jpg") ? " poster='vid/poster-$yr.jpg'" : "") . ">
			  <source src='vid/$yr.mp4' type='video/mp4' />
			  <source src='vid/$yr.webm' type='video/webm' />-->
			<!--[if IE 9]></video></div><![endif]-->
				<!--Fallback to Vimeo-->
			  <iframe src='http://player.vimeo.com/video/$vimeoID?title=0&amp;byline=0&amp;portrait=0' width='$vidWidth' height='$vidHeight'></iframe>
			  <!--<a href='vid/$yr.mp4'>Click to Watch $yr Video Montage</a>-->
			<!--[if IE 9]><div class='hidden'><video class='hidden'><![endif]-->
			</video>
			<!--[if IE 9]></div><![endif]-->
		</td>" . (($countVids % $columns == 0) || ($countVids == $numVids) ? "</tr>" : "");
}
$vids .= "
		<tr>
			<td colspan='2'>
				<div class='vidTitle'>
					<h2 style='font-size:100%'>Jeremy &amp; Christine Wedding Slideshow 2001</h2>
				</div>
				<!--[if IE 9]><div class='hidden'><![endif]-->
				<video width='$vidWidth' height='$vidHeight' controls='controls' poster='img/poster-wedding_slideshow.jpg'>
				  <source src='vid/moritz_wedding_slideshow.mp4' type='video/mp4' />
				  <source src='vid/moritz_wedding_slideshow.webm' type='video/webm' />
				<!--[if IE 9]></video></div><![endif]-->
					<!--Fallback to Vimeo-->
				  <iframe src='http://player.vimeo.com/video/47068724?title=0&amp;byline=0&amp;portrait=0' width='$vidWidth' height='$vidHeight'></iframe>
				  <!--<a href='vid/moritz_wedding_slideshow.mp4'>Click to Watch the Jeremy &amp; Christine Wedding Slideshow</a>-->
				<!--[if IE 9]><div class='hidden'><video class='hidden'><![endif]-->
				</video>
				<!--[if IE 9]></div><![endif]-->
			</td>
		</tr>
	</table>";
?>

<?=$header;?>
<body>
<?=$topper;?>
	<?=$topnav;?>
	<section class='main' id='videos'>
		<h1><?=$title;?></h1>
		<section>
			<hgroup>
				<h2>Montage Videos of our Family over the Years</h2>
			</hgroup>
			<p>(Courtesy of David Moritz at <a href='http://MovieMink.com'>MovieMink.com</a>)</p>
			<?=$vids;?>
			<h2>More Videos Coming Soon!</h2>
		</section>
	</section>
<?=$footer;?>
</body>
</html>
