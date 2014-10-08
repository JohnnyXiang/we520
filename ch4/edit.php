<?php 
require_once('includes/dbconnection.php');
$sql ="SELECT * FROM gallery WHERE id=".$_GET['id'];
$res = mysql_query($sql);
$rowData = mysql_fetch_assoc($res);
?>
<!DOCTYPE HTML>
<head>
<title>Edit </title>
<meta charset="utf-8">
<!-- CSS Files -->
<link rel="stylesheet" type="text/css" media="screen" href="css/style.css">


</head>

<body>
<h1>Upload An Image </h1>
<form method="post" action="upload-file.php" enctype="multipart/form-data">
	
	<input type="hidden" value="<?php echo $rowData['id']?>" name="id" />
	<label for="title">Title</label>
	<input type="text" id="title" name="title" value="<?php echo $rowData['title']?>" />
	<br>
	
	<label for="description">Description</label>
	<textarea name="description"><?php echo $rowData['description']?></textarea><br>
	
	<label for="position">Position</label>
	<input type="text" id="position" name="position"  value="<?php echo $rowData['position']?>"/>
	<br>
	
	
	<label for="file">File:</label>
	<input type="file" name="file" id="file">
	<img src="img/<?php echo $rowData['path']?>" /> 
	<br>
	<input type="submit" name="submit" value="Submit">
  </form>

</body>
</html>