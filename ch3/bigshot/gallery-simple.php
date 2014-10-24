<!DOCTYPE HTML>
<head>
<title>Bigshot | Gallery Simple</title>
<meta charset="utf-8">
<!-- CSS Files -->
<link rel="stylesheet" type="text/css" media="screen" href="css/style.css">
<link rel="stylesheet" type="text/css" media="screen" href="menu/css/simple_menu.css">
<link rel="stylesheet" href="css/nivo-slider.css" type="text/css" media="screen">
</head>
<?php 
include 'includes/functions.php';
?>

<body>
<div class="header">
  <!-- Logo/Title -->
  <div id="site_title"><a href="index.html"> <img src="img/logo.png" alt=""></a> </div>
  <!-- Main Menu -->
  <ol id="menu">
    <li><a href="index.html">Home</a>
      <!-- sub menu -->
      <ol>
        <li><a href="nivo.html">Nivo Slider</a></li>
        <li><a href="ei-slider.html">EI Slider</a></li>
        <li><a href="fullscreen-gallery.html">Fullscreen Slider</a></li>
        <li><a href="image-frontpage.html">Static Image</a></li>
      </ol>
    </li>
    <!-- end sub menu -->
    <li><a href="#">Pages</a>
      <!-- sub menu -->
      <ol>
        <li><a href="magazine.html">Magazine</a></li>
        <li><a href="blog.html">Blog</a></li>
        <li><a href="full-width.html">Full Width Page</a></li>
        <li><a href="columns.html">Columns</a></li>
      </ol>
    </li>
    <!-- end sub menu -->
    <li><a href="elements.html">Elements</a></li>
    <li><a href="#">Galleries</a>
      <!-- sub menu -->
      <ol>
        <li><a href="gallery-simple.html">Simple</a></li>
        <li><a href="portfolio.html">Filterable</a></li>
        <li><a href="gallery-fader.html">Fade Scroll</a></li>
        <li><a href="video.html">Video</a></li>
        <li class="last"><a href="photogrid.html">PhotoGrid</a></li>
      </ol>
    </li>
    <!-- end sub menu -->
    <li><a href="contact.html">Contact</a></li>
  </ol>
</div>
<!-- END header -->
<div id="container">
  <h1>Simmple Gallery</h1>
  <?php
  $images = getImagesFromDir($image_folder );
  $i=0;
  foreach($images as $file):	$i++;    
  ?>
  <div class="one-fifth <?php if($i%5==0):?>last<?php endif?>">	
    <p> <a title="Caption Text" href="img/demo/<?php echo $file?>" class="portfolio-item-preview" data-rel="prettyPhoto"><img src="img/demo/<?php echo $file?>" alt="" width="158" height="100" class="portfolio-img pretty-box"></a> </p>
  </div>

  <?php endforeach?>
  <div style="clear:both; height: 40px"></div>
  
  <form method="post" action="upload-file.php" enctype="multipart/form-data">
	<label for="file">Filename:</label>
	<input type="file" name="file" id="file"><br>
	<input type="submit" name="submit" value="Submit">
  </form>
</div>
<!-- close container -->
<div id="footer">
  <!-- First Column -->
  <div class="one-fourth">
    <h3>Useful Links</h3>
    <ul class="footer_links">
      <li><a href="#">Lorem Ipsum</a></li>
      <li><a href="#">Ellem Ciet</a></li>
      <li><a href="#">Currivitas</a></li>
      <li><a href="#">Salim Aritu</a></li>
    </ul>
  </div>
  <!-- Second Column -->
  <div class="one-fourth">
    <h3>Terms</h3>
    <ul class="footer_links">
      <li><a href="#">Lorem Ipsum</a></li>
      <li><a href="#">Ellem Ciet</a></li>
      <li><a href="#">Currivitas</a></li>
      <li><a href="#">Salim Aritu</a></li>
    </ul>
  </div>
  <!-- Third Column -->
  <div class="one-fourth">
    <h3>Information</h3>
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent sit amet enim id dui tincidunt vestibulum rhoncus a felis.
    <div id="social_icons"> Theme by <a href="http://www.csstemplateheaven.com">CssTemplateHeaven</a><br>
      Photos © <a href="http://dieterschneider.net">Dieter Schneider</a> </div>
  </div>
  <!-- Fourth Column -->
  <div class="one-fourth last">
    <h3>Socialize</h3>
    <img src="img/icon_fb.png" alt=""> <img src="img/icon_twitter.png" alt=""> <img src="img/icon_in.png" alt=""> </div>
  <div style="clear:both"></div>
</div>
<!-- END footer -->
</body>
</html>
