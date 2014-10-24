<?php 
require_once('includes/dbconnection.php');
?><!DOCTYPE HTML>
<head>
<title>Bigshot | Full Width</title>
<meta charset="utf-8">
<!-- CSS Files -->
<link rel="stylesheet" type="text/css" media="screen" href="css/style.css">


</head>

<body>

<table width=600 cellspacing=0 cellpadding=0>

<thead>
	<tr>
	<th>ID</th>
	<th>Title </th>
	<th>Description</th>
    <th>Position</th>
	<th>Actions</th>
	</tr>
<thead>
<tbody>
<?php
$sql = "SELECT * FROM  `gallery`";

$res  = mysql_query($sql );
while($row = mysql_fetch_assoc($res)):
?>
<tr>
	<td><?php echo $row['id']?></td>
	<td><?php echo $row['title']?></td>
	<td><?php echo $row['description']?></td>
	<td><?php echo $row['position']?></td>
	<td><a href="edit.php?id=<?php echo $row['id']?>">Edit</a> <a onclick="return confirm('Are you sure to delete this record?')" href="delete.php?id=<?php echo $row['id']?>">Delete</a></td>
</tr>

<?php endwhile ?>
</tbody>
</table>
<a href="create.php">Create a New Gallary Entity</a>
</body>
</html>
