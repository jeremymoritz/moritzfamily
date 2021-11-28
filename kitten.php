<?php	//	Jeremy And Christine Moritz (MoritzFamily.com)
$title = "The Kitten Catastrophe";
$head_content = "<script src='inc/kitten.js'></script>";
require_once('inc/mf.php');
?>
<?=$header;?>
<body>
<?=$topnav;?>
<?=$topper;?>
  <section class='main px-3 px-md-4' id='kitten'>
    <h1><?=$title;?></h1>
    <section>
      <h2>The Kitten Catastrophe</h2>
      <h3 class="text-center">(A Picturebook Melodrama)</h3>
      <div id='kittentable'>
        <button
          class="btn btn-primary"
          id='backbtn'
          onclick='javascript:goBack()'
        ><i class="bi-arrow-left"></i></button>
        <button
          class="btn btn-primary"
          id='fwdbtn'
          onclick='javascript:goFwd()'
        ><i class="bi-arrow-right"></i></button>
        <div class="my-2" id="book">
          <img
            src='img/kitten/kitten1.jpg'
            alt='1'
            id='kittenpage'
            class="img-fluid"
          >
          <?php
            require('inc/kittentext.php');
            foreach($kittenText as $page => $text) {
              echo("<div id='text{$page}'>{$text}</div>\n");
            }
          ?>
        </div>
      </div>

    </section>
  </section>
<?=$footer;?>
</body>
</html>
