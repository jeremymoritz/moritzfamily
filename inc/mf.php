<?php
ob_start();	//	starts output buffer (allows for setting php headers after declaring output, without errors)
putenv("TZ=US/Central");	//	Sets the timezone to be Central

//	VARIABLES
$developerMode = filter_input(INPUT_GET, 'dev') ? true : false;	//	Developer mode (for debugging and early testing stuff)
$thisPage = $_SERVER['PHP_SELF'] . (strlen($_SERVER['QUERY_STRING']) ? "?" . $_SERVER['QUERY_STRING'] : "");	//		returns current page + query string (e.g. "mypage.php?ugly=true")
$thisPageBaseName = pathinfo($_SERVER['PHP_SELF']);
$thisPageBaseName = $thisPageBaseName['filename'];

//	these variables allow me to set differences for individual pages (note: if further changes are needed, define $header variable on page)
$title = isset($title) ? $title : "The Moritz Family: Jeremy &amp; Christine, Angel, Tony, Harmony, Charity, and Chase";	//	title of page
$head_content = isset($head_content) ? $head_content : "";	//	additional head content

$header = "
	<!DOCTYPE html>
	<html lang='en'>
	<head>
		<meta charset='UTF-8'>
		<meta name='description' content='The Moritz Family: Jeremy and Christine, Angel, Tony, Harmony, Charity, and Chase'>
		<meta name='keywords' content='Moritz, Family, Jeremy, Christine, Angel, Tony, Harmony, Charity, Chase'>
		<title>$title</title>
		<link rel='shortcut icon' href='favicon.ico'>
		<link rel='stylesheet' type='text/css' href='inc/mf.css'>
		<!--[if lt IE 9]>
			<link rel='stylesheet' href='inc/ie8.css'>
			<script src='http://html5shim.googlecode.com/svn/trunk/html5.js'></script>
		<![endif]-->
		<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
		<script src='inc/mf.js'></script>
		$head_content
	</head>";

$topper = "
	<header>
		<h1>The Moritz Family</h1>
	</header>
	<section id='content'>
		<h1>Moritz Family: Jeremy &amp; Christine, Angel, Tony, Harmony, Charity, and Chase</h1>";

$topnav = "
	<nav class='mainNav'>
		<h2>Site Navigation</h2>
		<table>
			<tr>
				<td><a href='/' title='Moritz Family Home Page'>Home</a></td>
				<td><a href='about.php' title='Our Family'>Our Family</a></td>
				<td><a href='quotes.php' title='Quotes from the Kids'>Kids Quotes</a></td>
				<td><a href='videos.php' title='Moritz Family Videos'>Our Videos</a></td>
				<td><a href='mission.php' title='The Moritz Family Mission Statement'>Mission Statement</a></td>
				<td><a href='highlights.php' title='Year-By-Year Highlights'>Highlights</a></td>
				<td><a href='work.php' title='Rhythm City, Web Design, Piano Lessons, etc.'>Our Work</a></td>
				<td><a href='fun.php' title='Little Miss Terrific... Kitten Catastrophe... ya know, fun stuff. :-)'>Fun Stuff</a></td>
			</tr>
		</table>
	</nav>";

$footer = "
		<footer>Website Created by <a href='http://www.JeremyMoritz.com'>Jeremy Moritz</a></footer>
	</section>";


	/*********************
	*	Load JSON File	*
	*********************/

//	Ages section to tell how old the kids are at the time of this quote
$familyJSON = json_decode(file_get_contents("inc/family.json"));
$jsonAdults = $familyJSON->adults;
$jsonKids = $familyJSON->kids;
$highlightsJSON = json_decode(file_get_contents("inc/highlights.json"));



	/*********************
	*	PDO Functions		*
	*********************/

//	ARRAYS
//	For configuring passwords, etc. for MySQL
$config = array(
	'host'				=>	'localhost',	//	alias for server508.webhostingpad.com
	'username'			=>	'rhythmci_jeremy',
	'password'			=>	'Gl0rify!',
	'db'				=>	'rhythmci_db'
	);

####	CONNECT TO THE DATABASE		######
try {
	$dbh = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['db'], $config['username'], $config['password'], array(PDO::ATTR_PERSISTENT => true));
} catch (PDOException $e) {
	die($e->getMessage() . "\n Please contact us to tell us about this error... jeremy@jeremyandchristine.com");
}

#takes a pdo statement handle and returns an array of row objects
function sthFetchObjects($sth) {
	$out = array();
	while($o = $sth->fetchObject()) {
		$out[] = $o;
	}
	return $out;
}

	/*********************
	*	API Functions		*
	*********************/

//	THESE REPLACE THE $_GET, $_POST, etc. (can pass in a default value if none is found)
function apiGet($key,$default=false) {return filter_input(INPUT_GET, $key) ? filter_input(INPUT_GET, $key) : $default;}
function apiPost($key,$default=false) {return filter_input(INPUT_POST, $key) ? filter_input(INPUT_POST, $key) : $default;}
function apiCookie($key,$default=false) {return filter_input(INPUT_COOKIE, $key) ? filter_input(INPUT_COOKIE, $key) : $default;}
function apiSession($key,$default=false) {return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;}
	//	Check to see if a parameter has been set and if so, return it
function apiSnag($key,$default=false) {
	if(apiGet($key,$default)) {return apiGet($key,$default);
	} elseif(apiPost($key,$default)) {return apiPost($key,$default);
	} elseif(apiCookie($key,$default)) {return apiCookie($key,$default);
	} elseif(apiSession($key,$default)) {return apiSession($key,$default);
	} else {return $default;
	}
}
	//	try to snag a value; if it's not there, use the default passed-in; also allows for explicit typing of varName to make sure people are passing the correct type (i.e. integer)
function apiSnagType($varName,$default=false,$type=false) {
	$retVal = $default;
	if(apiSnag($varName)) {
		if(!$type || gettype(apiSnag($varName)) == $type) {	//	if type is set, make sure it's the right type
			$retVal = apiSnag($varName);
		}
	}
	return $retVal;
}

//	Function used to compare the date of the quote with the child's birthday to get child's age
function compare_dates($date1, $date2) {
	$blocks = array(
		array('name' => 'year',		'amount' => 60*60*24*365),
		array('name' => 'month',	'amount' => 60*60*24*30	),
		array('name' => 'week',		'amount' => 60*60*24*7	),
		array('name' => 'day',		'amount' => 60*60*24		),
		array('name' => 'hour',		'amount' => 60*60			),
		array('name' => 'minute',	'amount' => 60				),
		array('name' => 'second',	'amount' => 1				)
		);

	$diff = abs($date2-$date1);

	$levels = 2;
	$current_level = 1;
	$result = array();
	foreach($blocks as $block) {
		if ($current_level++ > $levels && !empty($result)) {
			break;
		}
		if ($diff / $block['amount'] >= 1) {
			$amount = floor($diff / $block['amount']);
			$plural = $amount > 1 ? 's' : '';
			$result[] = $amount . ' ' . $block['name'] . $plural;
			$diff -= $amount * $block['amount'];

			if($block['name'] == 'year' && $amount >= 3) { break; }	//	if 3 years old, stop counting months
		}
	}
	return ($date2 > $date1 ? '' : '-') . implode(' ',$result) . ' old';
}

	//	return the age in years if given a birthday and (optionally) another date (also may optionally pad it to a certain length like 2)
function getAge($iBirthdayTimestamp, $iCurrentTimestamp = false, $padZeros = 2) {	//	by default, it pads it left to a length of 2 (zerofill)
	$iBirthdayTimestamp = preg_match('/^\d{4}-\d{2}-\d{2}$/', $iBirthdayTimestamp) ? strtotime($iBirthdayTimestamp) : $iBirthdayTimestamp;
	$iCurrentTimestamp = $iCurrentTimestamp ? (preg_match('/^\d{4}-\d{2}-\d{2}$/', $iCurrentTimestamp) ? strtotime($iCurrentTimestamp) : $iCurrentTimestamp) : time();	//	default is today

	$iDiffYear  = date('Y', $iCurrentTimestamp) - date('Y', $iBirthdayTimestamp);
	$iDiffMonth = date('n', $iCurrentTimestamp) - date('n', $iBirthdayTimestamp);
	$iDiffDay   = date('j', $iCurrentTimestamp) - date('j', $iBirthdayTimestamp);

	// If birthday has not happen yet for this year, subtract 1.
	if ($iDiffMonth < 0 || ($iDiffMonth == 0 && $iDiffDay < 0)) {
		$iDiffYear--;
	}

	$iDiffYear = str_pad($iDiffYear, $padZeros, '0', STR_PAD_LEFT);	//	pad the age
	return $iDiffYear;
}

//	Checks if an email is valid
function isValidEmail ($email) {
	return preg_match("/^[a-zA-Z]\w+(\.\w+)*\@\w+(\.[0-9a-zA-Z]+)*\.[a-zA-Z]{2,4}$/", $email);
}

	//	converts "<a href='mailto:abc@example.com'>abc@example.com</a>" to "<a href='mailt&#111;:abc&#64;example.c&#111;m'>abc&#64;example.c&#111;m</a>" to hide from spamBots
function disguiseMail($mail) {
	return str_replace('@','&#64;',str_replace('o','&#111;',$mail));
}
?>
