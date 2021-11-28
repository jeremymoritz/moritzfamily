<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Peterson Chores Generator</title>
  <link rel='shortcut icon' href='favicon.ico'>
  <!-- <link rel='stylesheet' type='text/css' href='inc/mf.css'> -->
  <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.js'></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
  <!-- <script src='inc/mf.js'></script> -->
  <style>
    #chosen-chore {
      margin-top: 100px;
      font-size: 200%;
    }
    .btn-group {
      margin: 40px auto;
    }
    body {
      background: #181818;
      color: #fff;
    }
  </style>
</head>
<body>
  <section class='container main text-center' id='chores'>
    <h2>Random Chore Generator</h2>
    <p>Press a button to generate a chore!</p>

    <div class="btn-group text-center" role="group">
      <button type="button" class="btn btn-lg btn-info" onclick="javascript:generateChore(1)">Small</button>
      <button type="button" class="btn btn-lg btn-success" onclick="javascript:generateChore(2)">Medium</button>
      <button type="button" class="btn btn-lg btn-danger" onclick="javascript:generateChore(3)">Large</button>
    </div>

    <div class="row">
      <div class="col-md-10 offset-md-1 col-lg-6 offset-lg-3">
        <h2 id="chosen-chore"></h2>
      </div>
    </div>
  </section>
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
      $.getJSON('/inc/chores.json').then(function assignChoresToLocalArray(choresJson) {
        _.forEach(choresJson, function eachChore(chore) {
          for (var i = 0; i < chore.frequency; i++) {
            choresList.push(_.pick(chore, ['size', 'task']));
          }
        });

        console.log(choresList);
      });
    });
  </script>
</body>
</html>
