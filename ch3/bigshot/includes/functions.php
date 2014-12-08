<?php 
define('ROOT_DIR',realpath(dirname(dirname(__FILE__))));
$image_folder = ROOT_DIR."\img\demo";

function getImagesFromDir($dir){
	$images =array();
	$files = scandir($dir);  
    $i=0;
    foreach($files as $file){
		if(!@getimagesize($dir .DIRECTORY_SEPARATOR.$file)){
			continue;
		}
		
		$images[] = $file;
	}
	
	return $images;
}

 
  