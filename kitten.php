<?php	//	Jeremy And Christine Moritz (MoritzFamily.com)
$title = "The Kitten Catastrophe";
$head_content = "<script src='inc/kitten.js'></script>";
require_once('inc/mf.php');
?>
<?=$header;?>
<body>
<?=$topper;?>
	<?=$topnav;?>
	<section class='main' id='kitten'>
		<h1><?=$title;?></h1>
		<section>
			<h2>The Kitten Catastrophe<br>(A Picturebook Melodrama)</h2>
			<noscript>
				<p id='warning'>WARNING: JavaScript must be enabled on your browser to view this story!
					Your browser is currently not configured to display JavaScript.  Please 
					<a href='http://jeremyandchristine.com/_old/_oldpages/kitten-old.php'>Click Here</a> to view the story in another format.</p>
			</noscript>
			<table id='kittentable'>
				<tr>
					<td class='btn'><img onclick='javascript:goBack()' src='img/lmt/backbtn.gif' alt='Back' id='backbtn' class='mouseover'></td>
					<td id='book'>
						<img src='img/kitten/kitten1.jpg' alt='1' id='kittenpage' />
						<?php
							require('inc/kittentext.php');
							foreach($kittenText as $page => $text) {
								echo("<div id='text{$page}'>{$text}</div>\n");
							}
						?>
					</td>
					<td class='btn'><img onclick='javascript:goFwd()' src='img/lmt/fwdbtn.gif' alt='Forward' id='fwdbtn' class='mouseover'></td>
				</tr>
			</table>
		</section>
	</section>
<?=$footer;?>
</body>
</html>
