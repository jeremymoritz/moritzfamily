<?php
	require_once('../../inc/mf.php');
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='UTF-8'>
	<meta name='description' content='Tenpenny Family Kid Generator'>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Kid Generator</title>
	<link rel='shortcut icon' href='favicon.ico'>
	<!-- <link rel='stylesheet' type='text/css' href='inc/mf.css'> -->
	<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.js'></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
	<!-- <script src='inc/mf.js'></script> -->
	<link rel="apple-touch-icon" sizes="57x57" href="img/apple-touch-icons/tenpenny-kids-57.png">
	<link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icons/tenpenny-kids-72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icons/tenpenny-kids-114.png">
	<link rel="apple-touch-icon" sizes="144x144" href="img/apple-touch-icons/tenpenny-kids-144.png">
	<style>
		#chosen-kid {
			font-size: 200%;
		}
		#kid-pic {text-align: center;}
		#kid-pic img {
			max-width: 320px;
			max-height: 220px;
		}
		.btn-group {margin: 40px auto;}
		body {
			/*background: #181818;*/
			background: #484848;
			color: #fff;
		}
		.hidden {display: none;}
	</style>
	<?=$googleAnalytics;?>
</head>
<body>
	<section class='container main text-center' id='chores'>
		<h2>Tenpenny Kid Generator</h2>
		<p>Press the button to get a random Tenpenny kid!</p>

		<div class="btn-group text-center" role="group">
			<button type="button" class="btn btn-lg btn-primary" onclick="javascript:generateKid()">Click!</button>
		</div>

		<div class="row">
			<div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3">
				<h2 id="chosen-kid"></h2>
				<div id="kid-pic"></div>
			</div>
		</div>
		<div id="preload" class="hidden">
		</div>
	</section>
	<script>
		function generateKid() {
			const chosenKid = _.sample(kids);

			$('#chosen-kid').html(_.startCase(chosenKid));
			$('#chosen-kid').removeClass().addClass('alert').addClass('alert-' + kidStyles[chosenKid]);
			$('#kid-pic').html('<img src="img/' + chosenKid + '.png" alt="' + chosenKid + '" class="img-thumbnail">');
		}

		const kids = ['zadie', 'zaiya'];
		const kidStyles = {
			zadie: 'info',
			zaiya: 'success'
		};
		_.forEach(kids, (kid) => $('#preload').append('<img src="img/' + kid + '.png" alt="' + kid + '">'));
	</script>
</body>
</html>
