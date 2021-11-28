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
    <section>
      <h2><img src='img/fun_stuff.png' alt='Fun Stuff'></h2>
      <table>
        <tr>
          <td>Little Miss Terrific<br><a href='terrific.php'><img src='img/lmt_cover.jpg' alt='Little Miss Terrific'></a>
          <td>The Kitten Catastrophe<br><a href='kitten.php'><img src='img/kitten_cover.jpg' alt='The Kitten Catastrophe'></a>
        </tr>
      </table>
    </section>
  </section>
<?=$footer;?>
</body>
</html>
