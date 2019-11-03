<?php	//	kids quotes
$title = "Quotes from the Kids! Angel, Tony, Harmony, Charity, Chase, and Symphony";
require_once('inc/mf.php');

if(apiSnag('order') == 'ASC') {
	$order = 'ASC';
	$order_spelled = 'ascending';
	$rev_order = 'DESC';
	$rev_order_spelled = 'descending';
} else {
	$order = 'DESC';
	$order_spelled = 'descending';
	$rev_order = 'ASC';
	$rev_order_spelled = 'ascending';
}
$sql = "
	SELECT
		id,
		date,
		quote,
		rating
	FROM quo_quotes
	ORDER BY date $order";
$sth = $dbh->prepare($sql);
$sth->execute();

$quotes = sthFetchObjects($sth);	//	fetch all of the quotes and put them in $quotes array of objects

if($quotes) {
	//	pagination
	$defNumPerPage = 40;	//	default number quotes per page
	$numPerPage = is_numeric(apiSnag('numPerPage')) ? apiSnag('numPerPage') : (apiSnag('numPerPage') == 'all' ? 9999 : $defNumPerPage);	//	quotes per page (default 40)
	$numQuotes = count($quotes);
	$showAll = $numPerPage > $numQuotes ? true : false;
	$numPages = ceil($numQuotes / $numPerPage);	//	round up'
	$curPage = (is_numeric(apiSnag('curPage')) && apiSnag('curPage') <= $numPages) ? apiSnag('curPage') : 1;	//	current page of quotes

	//	query string variables (add to query string to ensure nothing is lost)
	$qn = "numPerPage=$numPerPage";
	$qc = "curPage=$curPage";
	$qo = "order=$order";

	if($showAll) {
		$pagination = "<nav class='pagination'><a href='?$qc&amp;$qo&amp;numPerPage=$defNumPerPage'>Click Here for Paginated Quotes</a></nav>";
	} else {
		$pagination = "
			<nav class='pagination'>"
				. ($curPage > 1 ? "<a href='?$qn&amp;$qo&amp;curPage=1'><img src='img/btn/btn_first.png' alt='1st' class='mouseover'></a>" : "<img src='img/btn/btn_first_gray.png' alt='1st'>")
				. ($curPage > 1 ? "<a href='?$qn&amp;$qo&amp;curPage=" . ($curPage - 1) . "'><img src='img/btn/btn_prev.png' alt='Prev' class='mouseover'></a>" : "<img src='img/btn/btn_prev_gray.png' alt='Prev'>")
				. "p. $curPage of $numPages"
				. ($curPage < $numPages ? "<a href='?$qn&amp;$qo&amp;curPage=" . ($curPage + 1) . "'><img src='img/btn/btn_next.png' alt='Next' class='mouseover'></a>" : "<img src='img/btn/btn_next_gray.png' alt='Next'>")
				. ($curPage < $numPages ? "<a href='?$qn&amp;$qo&amp;curPage=$numPages'><img src='img/btn/btn_last.png' alt='Last' class='mouseover'></a>" : "<img src='img/btn/btn_last_gray.png' alt='Last'>")
			. "</nav>";
	}

	$star = "<img src='img/singleredstar.gif' alt='*'>";	//	image of star
	$firstquote_3star = false;	//	this is used to force the first quote to be a 3 star

	$quote_section = $pagination;	//	initiate section with pagination
	$j = 0;	// count iterations
	foreach($quotes as $q) {
		$j++;
		if($j <= ($curPage - 1) * $numPerPage || $j > ($curPage * $numPerPage)) {
			continue;
		}

		if(!$firstquote_3star) {	//	only execute this section if our first quote is not a 3star
			if($q->rating == 3 || $curPage != 1) { $firstquote_3star = true;	//	this is satisfied, so from now on, quotes will appear normally (this section is skipped)
			} else { continue;	//	if we haven't seen our first 3-star quote, skip this quote and start over
			}
		}

		$q->quote = str_replace(array("\r\n", "\n", "\r"), "<br>", trim($q->quote));

		$ages_arr = array();
		$pics_arr = array();
		foreach($jsonKids as $kid) {
			if(strstr($q->quote, $kid->name)) {
				$ages_arr[] = $kid->name . ': ' . compare_dates(strtotime($kid->birthday),strtotime($q->date));
				$pics_arr[] = "<img src='img/family/thumbs/" . strtolower($kid->name) . "-" . getAge($kid->birthday, strtotime($q->date)) . ".jpg' alt='{$kid->name} (" . getAge($kid->birthday, strtotime($q->date), 1) . ")' class='thumbs'>";
			}
		}
		$ages = '[' . implode($ages_arr,', ') . ']';
		$pics = "<aside class='thumbs'>" . implode($pics_arr,' ') . "</aside>";

			//	star corner shows number of star rating in top right corner
		$star_corner = "<div class='star_corner'>";
		for($i = 1; $i <= $q->rating; $i++) {
			$star_corner .= $star;
		}
		$star_corner .= "</div>";

		$quote_section .= "
			<section class='color" . $q->rating . "'>"
				. $star_corner
				. $pics
				. "<p>" . $q->quote . "<br>"
				. "<em>&nbsp;&nbsp;&nbsp;&nbsp;~" . date('F Y', strtotime($q->date)) . " " . $ages . "</em></p>
				<div class='clear'></div>
			</section>\n";
	}

	$quote_section .= $pagination;	//	end quotes section with another pagination
} else {
	$title = "Sorry, No Quotes Available";
}

	// embolden names when they speak
$embolden_names = array("Jeremy","Daddy","Dad","Christine","Mommy","Mom","Angel","Tony","Harmony","Charity","Chase","Symphony","Davey","Mindy","Robbie","Grandpa","Mimi","Andrew");
foreach($embolden_names as $name) {
	$quote_section = preg_replace("/(<p>|<br>)(" . $name . ")/", '<p><strong>$2</strong>', $quote_section);
}

	//	select number of quotes per page
$numPerPageOptions = array(10,20,40,80,'all');
$selectNumPerPage = "
	<select name='numPerPage' onchange='javascript:submit()'>\n";
$numPerPage = $numPerPage >= 999 ? 'all' : $numPerPage;
foreach ($numPerPageOptions as $val) {
	$selectNumPerPage .= "<option value='$val'" . ($numPerPage == $val ? " selected='selected'" : "") . ">$val</option>\n";
}
$selectNumPerPage .= "
	</select>";

	//	select order
$selectOrder = "
	<select name='order' onchange='javascript:submit()'>
		<option value='$order' selected='selected'>$order_spelled</option>
		<option value='$rev_order'>$rev_order_spelled</option>
	</select>";
?>
<?=$header;?>
<body>
<?=$topper;?>
	<?=$topnav;?>
	<section class='main' id='quotes'>
		<h1>The Moritz Family</h1>
		<section id='inner'>
			<h1>Quotes from the Kids</h1>
			<fieldset>
				<legend>Legend:</legend>
				<table>
					<tr>
						<td><div class='legend3'></div></td>
						<td><?=$star;?><?=$star;?><?=$star;?></td>
						<td><img src='img/btn/btn_hide.png' alt='Hide' class='mouseover hider' id='hide_class_color3'></td>
					</tr><tr>
						<td><div class='legend2'></div></td>
						<td><?=$star;?><?=$star;?></td>
						<td><img src='img/btn/btn_hide.png' alt='Hide' class='mouseover hider' id='hide_class_color2'></td>
					</tr><tr>
						<td><div class='legend1'></div></td>
						<td><?=$star;?></td>
						<td><img src='img/btn/btn_hide.png' alt='Hide' class='mouseover hider' id='hide_class_color1'></td>
					</tr>
				</table>
				<script>
					setTimeout(function() {
						$("#hide_class_color1").click();	//	hide 1-star quotes by default
					}, 1000);
				</script>
			</fieldset>
			<h3>Quotes From The Kids!</h3>
			<form action='?<?=$_SERVER['PHP_SELF'];?>' method='get'>
				<h4>Currently showing <?=$selectNumPerPage;?> quotes in <?=$selectOrder;?> order by date.</h4>
				<input type='hidden' name='curPage' value='<?=$curPage;?>'>
			</form>
			<?=$quote_section;?>
		</section>
	</section>
<?=$footer;?>
</body>
</html>
