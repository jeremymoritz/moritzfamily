<?php

require_once('inc/mf.php');

$choreSize = apiGet('size');
if (!$choreSize) {
  $choreSize = 'medium';
}
switch($choreSize) {
  case 'small':
    $choreSizeNum = 1;
    break;
  case 'large':
    $choreSizeNum = 3;
    break;
  case 'medium': // fall through
  default:
    $choreSizeNum = 2;
}

$choresAdjustedForFrequency = array();

foreach($choresJson as $thisChore) {
  if($thisChore->size == $choreSizeNum) {	//	only if correct size
    for ($i = 0; $i < $thisChore->frequency; $i++) {	//	add it as many times as needed for frequency
      $choresAdjustedForFrequency[] = $thisChore;
    }
  }
}

shuffle($choresAdjustedForFrequency);	//	randomized chores

// die(print_r($choresAdjustedForFrequency));	//	testing that everything is as expected

$returnArray = array(
  'code' => 500,
  'message' => 'FAILURE'
);
foreach($choresAdjustedForFrequency as $thisHereChore) {
  $returnArray = array(
    'code' => 200,
    'message' => 'SUCCESS',
    'chore' => $thisHereChore->task,
    'size' => $thisHereChore->size,
    'frequency' => $thisHereChore->frequency
  );

  break;
}

header('Content-Type: application/json');
exit(json_encode($returnArray));

/***


  <script>
    function generateChore(choreSize) {
      var chosenChore = _.sample(_.filter(choresList, {size: choreSize}));

      $('#chosen-chore').html(chosenChore.task);
      $('#chosen-chore').removeClass().addClass('alert').addClass('alert-' + choreStyles[choreSize - 1]);
    }

    var choresList = [];
    var choreStyles = [
      'info',
      'success',
      'danger'
    ];

    $(function documentReady() {
      $.getJSON('inc/chores.json').then(function assignChoresToLocalArray(choresJson) {
        _.forEach(choresJson, function eachChore(chore) {
          for (var i = 0; i < chore.frequency; i++) {
            choresList.push(_.pick(chore, ['size', 'task']));
          }
        });

        console.log(choresList);
      });
    });
  </script>

*/

?>
