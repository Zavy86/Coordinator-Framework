<?php
/**
 * Attachment Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Attachment Upload
  *
  * @param array $file Uploaded file
  * @param string $name File name with extension
  * @param string $description Attachment description
  * @return boolean
  */
 function api_attachment_upload($file,$name=null,$description=null){
  // check parameters
  if(!is_array($file)){return false;}
  // check for file
  if(intval($file['size'])==0 || $file['error']!=UPLOAD_ERR_OK){return false;}
  // build query objects
  $attachment_qobj=new stdClass();
  $attachment_qobj->id=null;
  $attachment_qobj->name=($name?$name:strtolower(str_replace(" ","_",$file['name'])));
  $attachment_qobj->description=$description;
  $attachment_qobj->typology=$file['type'];
  $attachment_qobj->size=$file['size'];
  $attachment_qobj->addTimestamp=time();
  $attachment_qobj->addFkUser=$GLOBALS['session']->user->id;
  // loop for new id
  do{
   // generate attachment id
   $attachment_qobj->id=md5(date("YmdHis").rand(1,99999));
   // check for duplicates
   $check_id=$GLOBALS['database']->queryUniqueValue("SELECT `id` FROM `framework__attachments` WHERE `id`='".$attachment_qobj->id."'");
  }while($attachment_qobj->id==$check_id);
  // debug
  api_dump($attachment_qobj);
  // check for id
  if(!$attachment_qobj->id){return false;}
  // check if file exist and replace
  if(file_exists(ROOT."uploads/attachments/".$attachment_qobj->id)){unlink(ROOT."uploads/attachments/".$attachment_qobj->id);}
  if(is_uploaded_file($file['tmp_name'])){move_uploaded_file($file['tmp_name'],ROOT."uploads/attachments/".$attachment_qobj->id);}
  // check for uploaded file
  if(!file_exists(ROOT."uploads/attachments/".$attachment_qobj->id)){return false;}
  // execute query
  $result=$GLOBALS['database']->queryInsert("framework__attachments",$attachment_qobj);
  // check for result
  if($result===false){return false;}
  // return attachment id
  return $attachment_qobj->id;
 }

 /**
  * Attachment Remove
  *
  * @param mixed $attachment Attachment object or ID
  */
 function api_attachment_remove($attachment){
  // get object
  $attachment_obj=new cAttachment($attachment);
  // debug
  api_dump($attachment_obj,"attachment object");
  // check object
  if(!$attachment_obj->id){return false;}
  // execute query
  $GLOBALS['database']->queryDelete("framework__attachments",$attachment_obj->id);
  // check if file exist and remove
  if(file_exists(ROOT."uploads/attachments/".$attachment_obj->id)){unlink(ROOT."uploads/attachments/".$attachment_obj->id);}
  // return
  return true;
 }

?>