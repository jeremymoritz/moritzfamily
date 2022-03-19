<?php	//	Jeremy And Christine Moritz (MoritzFamily.com)
$title = "Moritz Family Fun Stuff";
require_once('inc/mf.php');
?>
<?=$header;?>
<body>
<?=$topnav;?>
<?=$topper;?>
  <section class='main' id='fun'>
    <h1><?=$title;?></h1>
    <section class="px-3 px-lg-4">
      <h2>Fun Stuff</h2>
      <div class="row">
        <div class="col-md-6 my-2 p-2">
          <a
            class="m-auto text-center"
            href='terrific.php'
          ><h3 class="text-center">Little Miss Terrific</h3>
          <img
            class="img-fluid"
            src='img/lmt_cover.jpg'
            alt='Little Miss Terrific'
          ></a>
        </div>
        <div class="col-md-6 my-2 p-2">
          <a
            class="m-auto text-center"
            href='kitten.php'
          ><h3 class="text-center">The Kitten Catastrophe</h3>
          <img
            class="img-fluid"
            src='img/kitten_cover.jpg'
            alt='The Kitten Catastrophe'
          ></a>
        </div>
      </div>
    </section>
  </section>
<?=$footer;?>
</body>
</html>
