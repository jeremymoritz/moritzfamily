<?php	//	Jeremy And Christine Moritz (MoritzFamily.com)
$title = "Little Miss Terrific";
$head_content = "<script src='inc/terrific.js'></script>";
require_once('inc/mf.php');
?>
<?=$header;?>
<body>
<?=$topnav;?>
<?=$topper;?>
  <section class='main' id='terrific'>
    <h1><?=$title;?></h1>
    <section>
      <h2>Little Miss Terrific</h2>
      <noscript>
        <p id='warning'>WARNING: JavaScript must be enabled on your browser to view this story! Your browser is currently not configured to display JavaScript.  Please <a href='http://jeremyandchristine.com/_old/_oldpages/terrific-old.php'>Click Here</a> to view the story in another format.</p>
      </noscript>
      <table id='lmttable'>
        <tr>
          <td class='btn'><img src='img/lmt/backbtn.gif' alt='Back' id='backbtn' class='mouseover'></td>
          <td><img src='img/lmt/lmt1.jpg' alt='1' id='lmtpage'></td>
          <td class='btn'><img src='img/lmt/fwdbtn.gif' alt='Forward' id='fwdbtn' class='mouseover'></td>
        </tr>
      </table>
    </section>
  </section>
<?=$footer;?>
</body>
</html>
