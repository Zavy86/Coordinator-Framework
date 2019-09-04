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

?>