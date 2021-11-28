<?php
require_once('inc/mf.php');

$specialMessage = '';
$errors = array();
if (apiGet('added')) {
  $specialMessage = "<h2>Project has been added!</h2>";
}

$targetDir = 'uploads/';
$thumbDir = $targetDir . 'thumb/';
$medDir = $targetDir . 'med/';
$fullSizeDir = $targetDir . 'orig/';

if (isset($_POST) && $_POST['data']) {
  $newProject = $_POST['data'];

  phpConsoleLog($newProject);

  $craftersString = preg_replace('/(,\s*and?&*\s*)|(\s+(and|&)\s+)/', ',', trim($newProject['crafters']));
  $craftersString = preg_replace('/[,]+/', ',', $craftersString);
  $newProject['crafters'] = explode(',', $craftersString);
  $newProject['images'] = array();

  $imgFiles = $_FILES['diy-images'];
  $max_orig_filesize = 50 * 1000 * 1000;	//	50MB
  $maxThumbDimensionPixels = 150;
  $maxMedDimensionPixels = 800;
  $acceptableFileTypes = array('jpg', 'jpeg', 'png');
  $totalImagesUploaded = count($imgFiles['name']);

  for ($i = 0; $i < $totalImagesUploaded; $i++) {
    // Check if image file is a actual image or fake image
    if (getimagesize($imgFiles['tmp_name'][$i]) === false) {
      continue;
    }

    $imageBasename = basename($imgFiles['name'][$i]);
    $targetMedFile = $medDir . $imageBasename;
    $targetThumbFile = $thumbDir . $imageBasename;
    $targetFullSizeFile = $fullSizeDir . $imageBasename;
    $uploadOk = true;
    $imageFileType = strtolower(pathinfo($targetMedFile, PATHINFO_EXTENSION));

    if ($imgFiles['size'][$i] > $max_orig_filesize) { // Check file size
      $errors[] = 'Image file is too large.';
    }

    if (!in_array($imageFileType, $acceptableFileTypes)) {	// Allow certain file formats
      $errors[] = 'Only JPG, JPEG, & PNG files are allowed (yours was ' . $imageFileType . ').';
    }

    if (!count($errors)) { // Check if $uploadOk is true.  If everything is ok, try to upload file
      $origFile = $imgFiles['tmp_name'][$i];

      // rotate the image if needed
      $sourceImage = imagecreatefromstring(file_get_contents($origFile));
      $exif = exif_read_data($origFile);
      if(!empty($exif['Orientation'])) {
        switch($exif['Orientation']) {
          case 8:
            $sourceImage = imagerotate($sourceImage, 90, 0);
            break;
          case 3:
            $sourceImage = imagerotate($sourceImage, 180, 0);
            break;
          case 6:
            $sourceImage = imagerotate($sourceImage, -90, 0);
            break;
        }
      }

      // list($width, $height, $type, $attr) = getimagesize($sourceImage);	//	type & attr aren't used
      $width = imagesx($sourceImage);
      $height = imagesy($sourceImage);
      $ratio = $width / $height;

      //	create med file
      if($ratio > 1) {	//	width is bigger than height
        $newMedWidth = $maxMedDimensionPixels;
        $newMedHeight = $maxMedDimensionPixels / $ratio;
      } else {	//	height is at least as big as width
        $newMedWidth = $maxMedDimensionPixels * $ratio;
        $newMedHeight = $maxMedDimensionPixels;
      }
      $destinationMedImage = imagecreatetruecolor($newMedWidth, $newMedHeight);
      imagecopyresampled($destinationMedImage, $sourceImage, 0, 0, 0, 0, $newMedWidth, $newMedHeight, $width, $height);

      //	create thumb file
      if($ratio > 1) {	//	width is bigger than height
        $newThumbWidth = $maxThumbDimensionPixels;
        $newThumbHeight = $maxThumbDimensionPixels / $ratio;
      } else {	//	height is at least as big as width
        $newThumbWidth = $maxThumbDimensionPixels * $ratio;
        $newThumbHeight = $maxThumbDimensionPixels;
      }
      $destinationThumbImage = imagecreatetruecolor($newThumbWidth, $newThumbHeight);
      imagecopyresampled($destinationThumbImage, $sourceImage, 0, 0, 0, 0, $newThumbWidth, $newThumbHeight, $width, $height);

      //	save med and thumb images
      if ($imageFileType === 'png') {
        imagepng($destinationMedImage, $targetMedFile);
        imagepng($destinationThumbImage, $targetThumbFile);
      } else {
        imagejpeg($destinationMedImage, $targetMedFile);
        imagejpeg($destinationThumbImage, $targetThumbFile);
      }

      //	destroy temp images
      imagedestroy($destinationMedImage);
      imagedestroy($destinationThumbImage);
      imagedestroy($sourceImage);

      //	save full-size file
      $resultOfMovingFile = move_uploaded_file($origFile, $targetFullSizeFile);
      if ($resultOfMovingFile) {
        $newProject['images'][] = $imageBasename;
      } else {
        $errors[] = 'There was an error uploading your file.';
      }
    }
  }

  if (count($errors)) {
    $specialMessage = implode("\n - ", $errors);
  } else {
    array_push($diyProjectsJson, $newProject);
    $jsonData = json_encode($diyProjectsJson, JSON_PRETTY_PRINT);

    parse_str($_SERVER['QUERY_STRING'], $queryParams);
    $queryParams['added'] = 1;
    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query($queryParams));
  }
}

$projectsList = '';
$thumbnailsToShow = 1;

//	sort DIY projects by date
usort($diyProjectsJson, function($a, $b) {
  return strcmp($a->date, $b->date);
});

function projectHtmlLine($oneProject, $property) {
  if (!$oneProject->$property) {
    return '';
  }

  return "
    <div class='{$property}'>
      <label>" . $oneProject->$property . "</label></div>";
}

foreach(array_reverse($diyProjectsJson) as $projKey => $proj) {
  $medSizeImages = array();
  $medSizeImagesString = "";
  $projectsList .= "
    <li class='project card' id='proj-$projKey'>
      <h3 class='name text-center'>{$proj->name}</h3>
      <h4 class='date text-center'>" . date("n/j/Y", strtotime($proj->date)) . "</h4>
      <div class='row'>
        <div class='col-sm col-md-8 col-lg-9'>
          <ul class='details'>
            <li><label>Location</label> {$proj->location}</li>
            <li><label>Time/Cost</label> {$proj->hours} hrs. / \${$proj->cost}</li>
            <li><label>By</label> " . implode(', ', $proj->crafters) . "</li>
          </ul>
        </div>
        <div class='col-sm col-md-4 col-lg-3 proj-image-thumbnails text-center text-sm-right'>
          <ul>";

  if ($proj->images) {
    foreach($proj->images as $imgKey => $imgSrc) {
      if ($imgKey < $thumbnailsToShow) {
        $projectsList .= "
          <li><img src='{$thumbDir}{$imgSrc}' alt='$imgSrc'
            class='proj-images thumbnails image-$imgKey' onclick='showFullImages($projKey)'></li>";
      } elseif ($imgKey === $thumbnailsToShow) {
        $projectsList .= "<li><a class='clickable' onclick='showFullImages($projKey)'>&hellip;</a></li>";
      }
      $medSizeImages[] = "<li><img src='{$medDir}{$imgSrc}' alt class='proj-images image-$imgKey img-fluid'></li>";
    }

    $medSizeImagesString = "<ul class='med-size-images' hidden
      onclick='hideFullImages($projKey)'>" . implode('', $medSizeImages) . "</ul>";
  }

  $projectsList .= "
          </ul>
        </div>
      </div>
      " . ($proj->description ? "<p class='description'>{$proj->description}</p>" : '')
      . $medSizeImagesString . "
    </li>\n";
}
$projectsList = "
  <ul>
    $projectsList
  </ul>";

?>
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name='description' content='The Moritz Family: Jeremy and Christine, Angel, Tony, Harmony, Charity, Chase and Symphony'>
  <meta name='keywords' content='Moritz, Family, Jeremy, Christine, Angel, Tony, Harmony, Charity, Chase, Symphony'>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Moritz DIY Projects</title>
  <link rel='shortcut icon' href='favicon.ico'>
  <!-- <link rel='stylesheet' type='text/css' href='inc/mf.css'> -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">	<!-- <script src='inc/mf.js'></script> -->
  <link rel="apple-touch-icon" sizes="57x57" href="/img/apple-touch-icons/rosie-57.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/img/apple-touch-icons/rosie-72.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/img/apple-touch-icons/rosie-114.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/img/apple-touch-icons/rosie-144.png">
  <link href="https://fonts.googleapis.com/css?family=Shadows+Into+Light+Two|Special+Elite" rel="stylesheet">
  <style>
    <?php
      $colors = array(
        'ltBrown' => '#ddd8c1',
        'mdBrown' => '#4e2e28',
        'dkBrown' => '#2e1e18'
      );
      $colors->medBrown = '';
    ?>
    body {
      background-color: <?= $colors['dkBrown']; ?>;
      color: #fff;
      background-image: url('img/bg/wood-bg.jpg');
    }
    h4.date {font-size: 1rem;}
    .hidden {display: none;}
    #diy-projects ul {padding: 0;}
    li.project {
      list-style: none;
      display: block;
      border: 3px solid <?= $colors['dkBrown']; ?>;
      background: <?= $colors['ltBrown']; ?>;
      border-radius: 5px;
      margin: 15px 0;
      padding: 5px 8px;
      text-align: left;
      font-family: 'Special Elite', cursive;
      color: #000;
    }
    .details li {
      margin-left: 20px;
      list-style-type: disc;
    }
    li.project h3.name {
      font-family: 'Shadows Into Light Two', cursive;
    }
    .project-title {font-weight: bold;}
    .date-and-age {font-style: italic;}
    .date-and-age .age {font-size: 80%;}
    .date-and-age .date {float: right;}
    .description {
      margin-left: 0.5rem;
      margin-right: 0.5rem;
    }
    .top-pic {border-radius: 10px;}
    .top-pic.float-right {
      -webkit-transform: scaleX(-1);
      transform: scaleX(-1);
    }
    .custom-file-input ~ .custom-file-label::after {
      content: 'Browse Pics';
    }
    .proj-images {
      border: 5px solid <?= $colors['mdBrown']; ?>;
      border-radius: 15px;
      cursor: pointer;
    }
    .proj-images.thumbnails {
      max-height: 70px;
      max-width: 115px;
      border-width: 4px;
      margin-left: 5px;
    }
    .proj-image-thumbnails ul li {list-style: none;}
    .proj-images.thumbnails.image-0 {
      max-height: 110px;
      max-width: 165px;
    }
    .med-size-images:not([hidden]) .proj-images {
      display: block;
      margin: 0.5rem auto;
    }
    .project label {text-transform: uppercase;}
    .project label:after {content: ':';}
    .clickable {cursor: pointer;}
    .btn.btn-primary {
      background-color: <?= $colors['dkBrown']; ?>;
      border-color: <?= $colors['ltBrown']; ?>;
    }
    .btn.btn-primary:hover {
      background-color: <?= $colors['mdBrown']; ?>;
      border-color: <?= $colors['dkBrown']; ?>;
    }
  </style>
  <?=$googleAnalytics;?>
</head>
<body>
  <h1 class="hidden">Moritz DIY Projects</h1>
  <section class='container main text-center' id='diy-projects'>
    <div class="clearfix mb-3">
      <img src="/img/apple-touch-icons/rosie-57.png" alt="" class="top-pic float-left">
      <img src="/img/apple-touch-icons/rosie-57.png" alt="" class="top-pic float-right">
      <h2>Moritz DIY Projects</h2>
    </div>

    <?php
      if ($specialMessage) {
        echo "<div class='alert alert-" . (count($errors) > 0 ? 'danger' : 'success')
          . " special-message alert-dismissible fade show' role='alert'>
            $specialMessage
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </div>";
      }
    ?>
    <?php
      if (apiGet('admin')) {
    ?>
        <form action="<?= "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"; ?>" method="post" enctype="multipart/form-data">
          <div class="row">
            <div class="col-md">
              <div class="form-group">
                <label for="name" class="sr-only">Project Name</label>
                <input type="text" class="form-control" id="name"
                  placeholder="Project Name" name="data[name]" required>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="date" class="sr-only">Date</label>
                <input type="date" class="form-control" id="date" placeholder="<?=date('Y-m-d');?>" value="<?=date('Y-m-d');?>" name="data[date]">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md">
              <div class="form-group">
                <label for="location" class="sr-only">Location</label>
                <input type="text" class="form-control" id="location"
                  placeholder="Location" name="data[location]" required>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="crafters" class="sr-only">Crafter(s)</label>
                <input type="text" class="form-control" id="crafters"
                  placeholder="Crafter(s)" name="data[crafters]" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md">
              <div class="form-group">
                <label for="hours" class="sr-only">Hours</label>
                <input type="number" class="form-control" id="hours" min="0"
                  step="1" placeholder="Hours" name="data[hours]" required>
              </div>
            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="cost" class="sr-only">Cost</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                  </div>
                  <input type="number" class="form-control" placeholder="Approx. Cost"
                     min="0" step="1" name="data[cost]" id="cost" required>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md form-group">
              <div class="custom-file" id="file-input-wrapper">
                <input type="file" class="custom-file-input" id="diy-images" name="diy-images[]" multiple>
                <label class="custom-file-label" for="diy-images">Select Project Image...</label>
                <div id="image-list"></div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="description" class="sr-only">Description (optional)</label>
            <textarea name="data[description]" class="form-control" id="description" placeholder="Description (optional)"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Submit Project</button>
        </form>
    <?php
      }
    ?>
    <div class="project-list">
      <?=$projectsList;?>
    </div>
  </section>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.js'></script>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script>
    function handleFileSelect(evt) {
      const files = evt.target.files; // FileList object
      const imageListItems = [];

      for (let i = 0; i < files.length ; i++) {
        let f = files[i];
        let fName = escape(f.name);
        let fType = f.type || 'n/a';
        let fModified = f.lastModifiedDate ? f.lastModifiedDate.toLocaleDateString() : 'n/a';

        imageListItems.push(`
          <li>
            <strong>${fName}</strong>
            (${fType}) - ${f.size} bytes,
            last modified: ${fModified}
          </li>
        `);
      }
      document.getElementById('image-list').innerHTML = '<ul>' + imageListItems.join('') + '</ul>';
    }

    function showFullImages(projectId) {
      document.querySelector(`#proj-${projectId} .med-size-images`).removeAttribute('hidden');
      document.querySelector(`#proj-${projectId} .proj-image-thumbnails`).setAttribute('hidden', true);
    }

    function hideFullImages(projectId) {
      document.querySelector(`#proj-${projectId} .med-size-images`).setAttribute('hidden', true);
      document.querySelector(`#proj-${projectId} .proj-image-thumbnails`).removeAttribute('hidden');
    }

    document.getElementById('diy-images').addEventListener('change', handleFileSelect, false);
  </script>
</body>
</html>
