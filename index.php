<?php	//	Jeremy And Christine Moritz (MoritzFamily.com)
require_once('inc/mf.php');

$cover_pic_alt = 'The Moritz Family: Jeremy and Christine, Angel, Tony, Harmony, Charity, Chase, and Symphony';
$selfies_path = '/img/family/selfies/600';
$selfies = array();
$selfies_dir = __DIR__ . $selfies_path;
if (is_dir($selfies_dir)) {
	$numberToRemoveDots = 2;
	$selfies = array_slice(scandir($selfies_dir), $numberToRemoveDots);
}
?>
<?=$header;?>
<body>
<?=$topper;?>
	<?=$topnav;?>
	<section class='main' id='index'>
		<h1>The Moritz Family</h1>
		<h2><img src='img/logo_moritz-family.png' alt='The Moritz Family' id='logo'></h2>

		<div id="cover-pic-wrapper">
			<img src="<?= $selfies_path ?>/<?= end($selfies) ?>" alt="<?= $cover_pic_alt ?>" id="cover-pic-0" class="cover-pic">
			<img src="<?= $selfies_path ?>/<?= end($selfies) ?>" alt="<?= $cover_pic_alt ?>" id="cover-pic-1" class="cover-pic transparent">
		</div>

		<script>
			// selfie carousel
			const millisecondsBeforePicSwitch = 2500;
			let selfies = [<?php foreach($selfies as $selfie) { echo "'$selfie',\n"; } ?>];
			let selfieCounter = 0;
			let selfieIndex = 0;
			let picChangeBoolean = false;

			setInterval(() => {
				picChangeBoolean = !picChangeBoolean;	//	to toggle 0 or 1
				selfieIndex = selfieCounter++ % selfies.length;
				if (!selfieIndex) {
					selfies = _.shuffle(selfies);
				}
				document.querySelector(`#cover-pic-${Number(picChangeBoolean)}`).src = `<?= $selfies_path ?>/${selfies[selfieIndex]}`;
				Array.from(document.querySelectorAll('.cover-pic')).forEach(pic => pic.classList.toggle('transparent'));
			}, millisecondsBeforePicSwitch);
		</script>
		<div class="preload-pics" hidden>
			<?php foreach($selfies as $selfie) { echo "<img src='$selfies_path/$selfie' alt>\n"; } ?>
		</div>
	</section>
	<?=$topnav;?>
<?=$footer;?>
</body>
</html>
