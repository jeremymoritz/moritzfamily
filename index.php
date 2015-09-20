<?php	//	Jeremy And Christine Moritz (MoritzFamily.com)
//die('test');
require_once('inc/mf.php');

//	array of acceptable cover pics
$picArray = array(
	"2014-10", 
	"2015-03", 
	"2015-04"
);

?>
<?=$header;?>
<body>
<?=$topper;?>
	<?=$topnav;?>
	<section class='main' id='index'>
		<h1>The Moritz Family</h1>
		<h2><img src='img/logo_moritzfamily.png' alt='The Moritz Family' id='logo'></h2>
		<img src='img/family/group/moritzfamily-<?=$picArray[rand(0, (count($picArray) - 1))];?>.jpg' alt='The Moritz Family: Jeremy &amp; Christine, Angel, Tony, Harmony, Charity, and Chase' id='coverPic'>
	</section>
	<?=$topnav;?>
<?=$footer;?>
</body>
</html>
