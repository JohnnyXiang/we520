<?php 
require_once('includes/dbconnection.php');
$image_folder = ROOT_DIR."\img";
  
$allowedExts = array("gif", "jpeg", "jpg", "png");
$temp = explode(".", $_FILES["file"]["name"]);
$extension = strtolower(end($temp));


if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/jpg")
|| ($_FILES["file"]["type"] == "image/pjpeg")
|| ($_FILES["file"]["type"] == "image/x-png")
|| ($_FILES["file"]["type"] == "image/png"))
&& ($_FILES["file"]["size"] < 20000000)
&& in_array($extension, $allowedExts)) {
  if ($_FILES["file"]["error"] > 0) {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
  } else {
	move_uploaded_file($_FILES["file"]["tmp_name"],$image_folder.'/'.$_FILES["file"]["name"] );	
  }
} 



		
//
if(isset($_POST['id'])){
	$sql = "UPDATE  `gallery` SET  `title` =  '{$_POST['title']}', description = '{$_POST['description']}', position = '{$_POST['position']}'";
	
	if($_FILES["file"]["name"]){
		$sql .=" , path= '{$_FILES["file"]["name"]}'";
	}
	
	$sql .= " WHERE id =".$_POST['id'];
	
	mysql_query($sql);
	
}elseif($_FILES["file"]["name"]){

$sql = "INSERT INTO `gallery` ( `title`, `description`, `path`, `position`) 
			VALUES ( '{$_POST['title']}', '{$_POST['description']}', '{$_FILES["file"]["name"]}', '{$_POST['position']}')";
    
			$result = mysql_query($sql);
			
			
}

header('Location:index.php');

?>
