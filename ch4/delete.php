<?php 
require_once('includes/dbconnection.php');

$sql = "DELETE FROM gallery WHERE id=".$_GET['id'];
mysql_query($sql);

header('Location:index.php');

?>
