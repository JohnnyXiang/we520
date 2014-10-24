<!DOCTYPE HTML>
<head>
<title>Create </title>
<meta charset="utf-8">
<!-- CSS Files -->
<link rel="stylesheet" type="text/css" media="screen" href="css/style.css">


</head>

<body>
<h1>Upload An Image </h1>
<form method="post" action="upload-file.php" enctype="multipart/form-data">

	<label for="title">Title</label>
	<input type="text" id="title" name="title" />
	<br>
	
	<label for="description">Description</label>
	<textarea name="description"></textarea><br>
	
	<label for="position">Position</label>
	<input type="text" id="position" name="position" />
	<br>
	
	
	<label for="file">File:</label>
	<input type="file" name="file" id="file"><br>
	<input type="submit" name="submit" value="Submit">
  </form>

</body>
</html>