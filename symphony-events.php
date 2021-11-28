<?php
require_once('inc/mf.php');

$specialMessage = '';
if (apiGet('added')) {
  $specialMessage = "<h2>Event has been added!</h2>";
}

if (isset($_POST) && $_POST['data']) {
  $newEvent = $_POST['data'];

  phpConsoleLog($newEvent);
  $eventsPath = './inc/symphony-events.json';

  $inp = file_get_contents($eventsPath);
  $events = json_decode($inp);
  array_push($events, $newEvent);
  $jsonData = json_encode($events, JSON_PRETTY_PRINT);
  file_put_contents($eventsPath, $jsonData);

  header('Location: ' . $_SERVER['PHP_SELF'] . '?added=1') ;
}

$eventsList = "";

function dateDifference($date_1 , $date_2 , $differenceFormat = '') {
  $datetime1 = date_create($date_1);
  $datetime2 = date_create($date_2);

  $interval = date_diff($datetime1, $datetime2);
  $numberOfYears = intval($interval->format('%y'));
  $numberOfMonths = intval($interval->format('%m'));
  $numberOfDays = intval($interval->format('%d'));

  if (($numberOfYears + $numberOfMonths + $numberOfDays) == 0) {
    return 'Newborn';
  }

  if ($numberOfYears > 0) {
    $differenceFormat .= '%y Year' . ($numberOfYears > 1 ? 's' : '') . ' ';
  }

  if ($numberOfMonths > 0) {
    $differenceFormat .= '%m Month' . ($numberOfMonths > 1 ? 's' : '') . ' ';
  }

  if ($numberOfDays > 0) {
    $differenceFormat .= '%d Day' . ($numberOfDays > 1 ? 's' : '') . ' ';
  }

  $differenceFormat .= 'old';

  return $interval->format($differenceFormat);
}

//	sort symphony events by date
usort($symphonyEventsJson, function($a, $b) {
  return strcmp($a->date, $b->date);
});

foreach(array_reverse($symphonyEventsJson) as $ev) {
  $ageAtTime = dateDifference(end($jsonKids)->birthday, $ev->date);

  $eventsList .= "
    <li class='event'>
      <div class='event-title'>$ev->event</div>
      " . ($ev->description ? "<div class='description'>$ev->description</div>" : '') . "
      <div class='date-and-age'>
        <span class='date'>" . date("n/j", strtotime($ev->date)) . "</span>
        <span class='age'>$ageAtTime</span>
      </div>
    </li>\n";
}
$eventsList = "
  <ul>
    $eventsList
  </ul>";

?>
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name='description' content='The Moritz Family: Jeremy and Christine, Angel, Tony, Harmony, Charity, Chase and Symphony'>
  <meta name='keywords' content='Moritz, Family, Jeremy, Christine, Angel, Tony, Harmony, Charity, Chase, Symphony'>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Symphony</title>
  <link rel='shortcut icon' href='favicon.ico'>
  <!-- <link rel='stylesheet' type='text/css' href='inc/mf.css'> -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
  <!-- <script src='inc/mf.js'></script> -->
  <link rel="apple-touch-icon" sizes="57x57" href="/img/apple-touch-icons/symphony-baby-57.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/img/apple-touch-icons/symphony-baby-72.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/img/apple-touch-icons/symphony-baby-114.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/img/apple-touch-icons/symphony-baby-144.png">
  <link href="https://fonts.googleapis.com/css?family=Acme" rel="stylesheet">
  <style>
    body {
      background-color: #222;
      color: #fff;
    }
    .hidden {display: none;}
    #symphony-events ul {padding: 0;}
    li.event {
      list-style: none;
      display: block;
      border: 1px solid #00f;
      background: #fcc;
      border-radius: 5px;
      margin: 5px 0;
      padding: 5px 8px;
      text-align: left;
      font-family: 'Acme', sans-serif;
      color: #000;
    }
    .event-title {font-weight: bold;}
    .date-and-age {font-style: italic;}
    .date-and-age .age {font-size: 80%;}
    .date-and-age .date {float: right;}
    .top-pic {border-radius: 10px;}
    .top-pic.float-right {
      -webkit-transform: scaleX(-1);
      transform: scaleX(-1);
    }
  </style>
  <?=$googleAnalytics;?>
</head>
<body>
  <h1 class="hidden">Symphony Firsts</h1>
  <section class='container main text-center' id='symphony-events'>
    <div class="clearfix mb-3">
      <img src="/img/apple-touch-icons/symphony-baby-57.png" alt="" class="top-pic float-left">
      <img src="/img/apple-touch-icons/symphony-baby-57.png" alt="" class="top-pic float-right">
      <h2>Symphony Firsts</h2>
    </div>

    <?php
      if ($specialMessage) {
        echo $specialMessage;
      }
    ?>

    <form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
      <div class="row">
        <div class="col-md">
          <div class="form-group">
            <label for="event" class="sr-only">Symphony First Event</label>
            <input class="form-control" id="event" placeholder="Event" name="data[event]" required>
          </div>
        </div>
        <div class="col-md">
          <div class="form-group">
            <label for="date" class="sr-only">Date</label>
            <input type="date" class="form-control" id="date" placeholder="<?=date('Y-m-d');?>" value="<?=date('Y-m-d');?>" name="data[date]">
          </div>
        </div>
      </div>
      <div class="form-group">
        <label for="description" class="sr-only">Description (optional)</label>
        <textarea name="data[description]" class="form-control" id="description" placeholder="Description (optional)"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <div class="event-list">
      <?=$eventsList;?>
    </div>
  </section>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.js'></script>
  <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>
