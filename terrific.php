<?php	//	Jeremy And Christine Moritz (MoritzFamily.com)
$title = "Little Miss Terrific";
$head_content = "<script src='inc/terrific.js'></script>";
require_once('inc/mf.php');
?>
<?=$header;?>
<body>
<?=$topnav;?>
<?=$topper;?>
  <section class='main px-3 px-md-4' id='terrific'>
    <h1><?=$title;?></h1>
    <section>
      <h2>Little Miss Terrific</h2>
      <button id='backbtn' class="btn btn-primary"><i class="bi-arrow-left"></i></button>
      <button id='fwdbtn' class="btn btn-primary"><i class="bi-arrow-right"></i></button>
      <div class="main-image">
        <img src='img/lmt/lmt1.jpg' alt='1' id='lmtpage' class="img-fluid">
      </div>
    </section>
  </section>
<?=$footer;?>
</body>
</html>
