<?php
/**
 * Uploads Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Check uploaded file
  *
  * @param string[] $upload Element of $_FILES array
  * @return boolean
  */
 function api_uploads_check($upload){
  // checks
  if(!is_array($upload)){return false;}
  if($upload["error"]){return false;}
  if(!$upload["size"]){return false;}
  if(!$upload["name"]){return false;}
  if(!$upload["tmp_name"]){return false;}
  if(!is_uploaded_file($upload["tmp_name"])){return false;}
  // return
  return true;
 }

 /**
  * Store uploaded file
  *
  * @param string[] $upload Element of $_FILES array
  * @param string $path Directory starting after /uploads/
  * @param string $name File name or maintain uploaded name
  * @param boolean $replace If file exist replace or false
  * @return boolean
  */
 function api_uploads_store($upload,$path,$name=null,$replace=false){
  // check parameters
  if(!api_uploads_check($upload) || !trim($path,"/")){return false;}
  // make uploads directory and file name
  $directory=DIR."uploads/".trim($path,"/")."/";
  $file=($name?$name:$upload["name"]);
  // check for directory
  if(!is_dir($directory)){mkdir($directory,0775,true);}
  // check if file exists and delete if replace is true
  if(file_exists($directory.$file)){if($replace){unlink($directory.$file);}else{return false;}}
  // upload new file
  if(!move_uploaded_file($upload['tmp_name'],$directory.$file)){return false;}
  // check for uploaded file
  if(!file_exists($directory.$file)){return false;}
  // return
  return true;
 }

 /**
  * Remove file from uploads
  *
  * @param string $path Directory starting after /uploads/
  * @param string $file File to remove
  * @return boolean
  */
 function api_uploads_remove($path,$file){
  // check parameters
  if(!trim($path,"/")){return false;}
  // make uploads directory
  $directory=DIR."uploads/".trim($path,"/")."/";
  // check if file exists
  if(!file_exists($directory.$file)){return false;}
  // try to remove file
  if(!unlink($directory.$file)){return false;}
  // return
  return true;
 }

 /**
  * List of uploaded files
  *
  * @param string $path Directory starting after /uploads/
  * @param string $file File to remove
  * @return boolean
  */
 function api_uploads_list($path,$file){
  // check parameters
  if(!trim($path,"/")){return false;}
  // make uploads directory
  $directory=DIR."uploads/".trim($path,"/")."/";
  // scan directory
  $files_array=scandir($directory);
  if(!is_array($files_array)){$files_array=array();}
  // remove directories
  foreach($files_array as $key=>$file){
   if(in_array($file,array(".",".."))){unset($files_array[$key]);}
   if(is_dir($file)){unset($files_array[$key]);}
  }
  // return
  return $files_array;
 }

 /**
  * Size of uploaded file
  *
  * @param string $path Directory starting after /uploads/
  * @param string $file File name
  * @param string $format Format size with unit or return bytes
  * @return double|string|false
  */
 function api_uploads_size($path,$file,$format=false){
  // check parameters
  if(!trim($path,"/")){return false;}
  // make uploads directory
  $directory=DIR."uploads/".trim($path,"/")."/";
  // check if file exists
  if(!file_exists($directory.$file)){return false;}
  // get file size
  $size=filesize($directory.$file);
  // format size
  if($format){
   if($size>=1073741824){
    $return=round($size/1073741824,2)." GB";
   }elseif($size>=1048576){
    $return=round($size/1048576,2)." MB";
   }elseif($size>=1024){
    $return=round($size/1024)." KB";
   }else{
    $return=$size." Byte";
   }
  }else{
   $return=$size;
  }
  // return
  return $return;
 }

 /**
  * Read uploaded file
  *
  * @param string $path Directory starting after /uploads/
  * @param string $file File name
  * @return boolean
  */
 function api_uploads_read($path,$file){
  // check parameters
  if(!trim($path,"/")){return false;}
  // make uploads directory
  $directory=DIR."uploads/".trim($path,"/")."/";
  // check if file exists
  if(!file_exists($directory.$file)){return false;}
  // get file content
  $content=readfile($directory.$file);
  // return
  return $content;
 }

?>