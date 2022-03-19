<?php // kids quotes
$title = "Quotes from the Kids! Angel, Tony, Harmony, Charity, Chase, and Symphony";
require_once('inc/mf.php');

if (apiSnag('order') == 'ASC') {
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

$searchText = apiSnag('search');
$whereClause = '';

if ($searchText) {
  $searchText = trim($searchText);
  $searchText = "%{$searchText}%";
  $whereClause = 'WHERE quote LIKE :search_text';
}

$sql = "
  SELECT
    id,
    date,
    quote,
    rating
  FROM quo_quotes
  $whereClause
  ORDER BY date $order";
$sth = $dbh->prepare($sql);

if ($searchText) {
  $sth->bindParam(':search_text', $searchText, PDO::PARAM_STR);
}

$sth->execute();
$quotes = sthFetchObjects($sth); // fetch all of the quotes and put them in $quotes array of objects

if($quotes) {
  // pagination
  $defNumPerPage = 40; // default number quotes per page
  $numPerPage = is_numeric(apiSnag('numPerPage')) ? apiSnag('numPerPage') : (apiSnag('numPerPage') == 'all' ? 9999 : $defNumPerPage); // quotes per page (default 40)
  $numQuotes = count($quotes);
  $showAll = $numPerPage > $numQuotes ? true : false;
  $numPages = ceil($numQuotes / $numPerPage); // round up'
  $curPage = (is_numeric(apiSnag('curPage')) && apiSnag('curPage') <= $numPages) ? apiSnag('curPage') : 1; // current page of quotes

  // query string variables (add to query string to ensure nothing is lost)
  $qn = "numPerPage=$numPerPage";
  $qc = "curPage=$curPage";
  $qo = "order=$order";

  if($showAll) {
    $pagination = "<nav class='pagination'><a href='?$qc&amp;$qo&amp;numPerPage=$defNumPerPage'>Click Here for Paginated Quotes</a></nav>";
  } else {
    $pagination = "
      <nav class='quote-pagination'>
        <a
          href='?$qn&amp;$qo&amp;curPage=" . ($curPage - 1) . "'
          class='btn btn-" . ($curPage > 1 ? "info" : "secondary disabled") . " me-2'
        ><i class='bi-chevron-left'></i> Prev</a>
        p. $curPage of $numPages
        <a
          href='?$qn&amp;$qo&amp;curPage=" . ($curPage + 1) . "'
          class='btn btn-" . ($curPage < $numPages ? "info" : "secondary disabled") . " ms-2'
        >Next <i class='bi-chevron-right'></i></a>
      </nav>";
  }

  $star = "<img src='img/singleredstar.gif' alt='*'>"; // image of star
  $firstquote_3star = false; // this is used to force the first quote to be a 3 star

  $quote_section_id = 'display-quotes'; // used for ajax
  $j = 0; // count iterations
  foreach($quotes as $q) {
    $j++;
    if($j <= ($curPage - 1) * $numPerPage || $j > ($curPage * $numPerPage)) {
      continue;
    }

    if(!$firstquote_3star) { // only execute this section if our first quote is not a 3star
      if($q->rating == 3 || $curPage != 1) { $firstquote_3star = true; // this is satisfied, so from now on, quotes will appear normally (this section is skipped)
      } else { continue; // if we haven't seen our first 3-star quote, skip this quote and start over
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
    $ages = '[' . implode(', ', $ages_arr) . ']';
    $pics = "<aside class='thumbs'>" . implode(' ', $pics_arr) . "</aside>";

    // star corner shows number of star rating in top right corner
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
} else {
  $title = "Sorry, No Quotes Available";
}

// embolden names when they speak
$embolden_names = array(
  'Jeremy',
  'Daddy',
  'Dad',
  'Christine',
  'Mommy',
  'Mom',
  'Angel',
  'Tony',
  'Harmony',
  'Charity',
  'Chase',
  'Symphony',
  'Davey',
  'Mindy',
  'Robbie',
  'Grandpa',
  'Mimi',
  'Andrew',
  'Uncle Robbie'
);
foreach($embolden_names as $name) {
  $quote_section = preg_replace("/(<p>|<br>)(" . $name . ")/", '<p><strong>$2</strong>', $quote_section);
}

// select number of quotes per page
$numPerPageOptions = [10, 20, $defNumPerPage, 80, 'all'];
$selectNumPerPage = "
  <select name='numPerPage' onchange='javascript:submit()'>\n";
$numPerPage = $numPerPage >= 999 ? 'all' : $numPerPage;
foreach ($numPerPageOptions as $val) {
  $selectNumPerPage .= "<option value='$val'" . ($numPerPage == $val ? " selected='selected'" : "") . ">$val</option>\n";
}
$selectNumPerPage .= "
  </select>";

// select order
$selectOrder = "
  <select name='order' onchange='javascript:submit()'>
    <option value='$order' selected='selected'>$order_spelled</option>
    <option value='$rev_order'>$rev_order_spelled</option>
  </select>";
?>
<?=$header;?>
<body>
<?=$topnav;?>
<?=$topper;?>
  <section class='main' id='quotes'>
    <h1>The Moritz Family</h1>
    <section id='inner' class="px-2 px-md-3 px-lg-4 px-xxl-5">
      <h1>Quotes from the Kids</h1>
      <h2>Quotes From The Kids!</h2>
      <div class="row">
        <div class="col-md-6 d-flex justify-content-end order-0 order-md-2">
          <fieldset class="card">
            <legend class="card-title ps-2">Legend:</legend>
            <div class="row">
              <div class="col col-auto"><div class='legend-color legend3'></div></div>
              <div class="col"><?=$star;?><?=$star;?><?=$star;?></div>
              <div class="col pe-3">
                <button
                  class='btn btn-primary hider mouseover'
                  id='hide_class_color3'
                >Hide</button>
              </div>
            </div>
            <div class="row">
              <div class="col col-auto"><div class='legend-color legend2'></div></div>
              <div class="col"><?=$star;?><?=$star;?></div>
              <div class="col pe-3">
                <button
                  class='btn btn-primary hider mouseover'
                  id='hide_class_color2'
                >Hide</button>
              </div>
            </div>
            <div class="row">
              <div class="col col-auto"><div class='legend-color legend1'></div></div>
              <div class="col"><?=$star;?></div>
              <div class="col pe-3">
                <button
                  class='btn btn-primary hider mouseover'
                  id='hide_class_color1'
                >Hide</button>
              </div>
            </div>
            <script>
              const waitForQuotesInMillis = 1000;
              setTimeout(function() { // hide 1-star quotes by default
                $("#hide_class_color1").click();
              }, waitForQuotesInMillis);
            </script>
          </fieldset>
        </div>
        <div class="col-md-6 order-md-1">
          <form class="clearfix m-2">
            <label class="form-label" for="search-text">Search:</label>
            <input
              class="form-control"
              id="search-text"
              onkeyup="filterQuotes(event)"
              placeholder="Type a name or quote text."
              type="search"
            >
          </form>
        </div>
      </div>
      <form action='?<?=$_SERVER['PHP_SELF'];?>' method='get'>
        <h4>Currently showing <?=$selectNumPerPage;?> quotes in
          <?=$selectOrder;?> order by date.</h4>
        <input type='hidden' name='curPage' value='<?=$curPage;?>'>
      </form>
      <?= $pagination; ?>
      <div id="<?= $quote_section_id ?>">
        <?=$quote_section;?>
      </div>
      <?= $pagination; ?>
    </section>
  </section>
  <script>
    function filterQuotes(evt) {
      const searchText = evt.target.value.trim();
      const minLengthToSearch = 3;

      if (searchText.length >= minLengthToSearch) {
        $.ajax({
          success: function(data) {
            const filteredDisplayQuotes = $(data).find(
              '#<?= $quote_section_id ?>'
            );
            $('#<?= $quote_section_id ?>').replaceWith(filteredDisplayQuotes);
          },
          type: 'GET',
          url : `<?= $_SERVER['PHP_SELF']; ?>?search=${searchText}`
        });
      }
    }
  </script>
<?=$footer;?>
</body>
</html>
