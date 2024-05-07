<?php	//	kids quotes
$title = "Quotes from the Kids! Angel, Tony, Harmony, Charity, Chase, and Symphony";
require_once('inc/mf.php');

$sql = "SELECT " .
    "id, " .
    "date, " .
    "quote, " .
    "rating " .
  "FROM quo_quotes " .
  // "WHERE rating > 1 " .
  "ORDER BY date ASC " .
  // "LIMIT 10 " .
  ";";
$sth = $dbh->prepare($sql);
$sth->execute();

$quotes = sthFetchObjects($sth);	//	fetch all of the quotes and put them in $quotes array of objects

if($quotes) {
  //	pagination
  $numPerPage = apiGet('num', 20);	//	quotes per page (default 20) //	change this to 9999
  $curPage = apiGet('page', 1);	//	current page of quotes

  $star = "<img src='img/singleredstar.gif' alt='*'>";	//	image of star

  $j = 0;	// count iterations

  foreach($quotes as $q) {
    $j++;

    if($j <= ($curPage - 1) * $numPerPage || $j > ($curPage * $numPerPage)) {
      continue;
    }

    $q->quote = str_replace("\n", "<br>", trim($q->quote));

    $ages_arr = array();
    $pics_arr = array();

    foreach($jsonKids as $kid) {
      if(strstr($q->quote, $kid->name)) {
        $ages_arr[] = $kid->name . ': ' . compare_dates(strtotime($kid->birthday),strtotime($q->date));
        $pics_arr[] = "<img src='img/family/thumbs/" . strtolower($kid->name) . "-" . getAge($kid->birthday, strtotime($q->date)) . ".jpg' alt='{$kid->name} (" . getAge($kid->birthday, strtotime($q->date), 1) . ")' class='thumbs'>";
      }
    }

    $ages = '[' . implode(', ', $ages_arr) . ']';
    $pics = "<aside class='thumbs'>" . implode(' ', $pics_arr) . "</aside>";

      //	star corner shows number of star rating in top right corner
    $star_corner = "<div class='star_corner'>";

    for($i = 1; $i <= $q->rating; $i++) {
      $star_corner .= $star;
    }

    $star_corner .= "</div>";

    $quote_section .= "
      <section class='quote color" . $q->rating . "'>"
        . $pics
        . $star_corner
        . "<section><p>" . $q->quote . "<br>"
        . "<em>&nbsp;&nbsp;&nbsp;&nbsp;~" . date('F Y', strtotime($q->date)) . " " . $ages . "</em></p></section>
        <div class='clear'></div>
      </section>\n";
  }

  $quote_section .= $pagination;	//	end quotes section with another pagination
}

  // embolden names when they speak
$embolden_names = array("Jeremy","Daddy","Dad","Christine","Mommy","Mom","Angel","Tony","Harmony","Charity","Chase","Symphony","Davey","Mindy","Robbie","Grandpa","Mimi","Andrew","Grandmary","Stacy","Clint");

foreach($embolden_names as $name) {
  $quote_section = preg_replace("/(<p>|<br>)(" . $name . ")/", '<p><strong>$2</strong>', $quote_section);
}

?>


<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name='description' content='The Moritz Family: Jeremy & Christine, Angel, Tony, Harmony, Charity, Chase, and Symphony'>
  <meta name='keywords' content='Moritz, Family, Jeremy, Christine, Angel, Tony, Harmony, Charity, Chase, Symphony'>
  <title>QUOTES-Images</title>
  <link rel='shortcut icon' href='favicon.ico'>
  <link rel='stylesheet' type='text/css' href='inc/mf.css'>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
</head>
<body id="quotes_images_body">
  <section id='quotes_images'>
    <?=$quote_section;?>
  </section>
  <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  <script src='inc/jquery.textfill.js'></script>
  <script src='inc/mf.js'></script>
  <script>
    $('.quote').textfill({
      innerTag: 'section',
      maxFontPixels: 32
    });
  </script>
</body>
</html>
