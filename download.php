<?php
/**
 * Download
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // check for debug
 if($_GET['debug']==1){$_GET['debug']=true;}else{$_GET['debug']=false;}
 // include functions
 require_once("initializations.inc.php");
 // debug
 api_dump($_REQUEST,"_REQUEST");
 // get attachment
 $attachment_obj=new cAttachment($_REQUEST['attachment']);
 api_dump($attachment_obj);
 // check attachment
 if(!$attachment_obj->id){die("INVALID_LINK");}
 // check session
 if(!$attachment_obj->public && !$GLOBALS['session']->validity){die("INVALID_SESSION");}
 // check deleted
 if($attachment_obj->deleted){die("ATTACHMENT_DELETED");}
 // make file path
 $file_path=DIR."uploads/attachments/".$attachment_obj->id;
 // chekc for file
 if(!file_exists($file_path)){die("FILE_NOT_EXIST");}
 // increment downloads
 $GLOBALS['database']->queryExecute("UPDATE `framework__attachments` SET `downloads`=`downloads`+1 WHERE `id`='".$attachment_obj->id."'");
 // check disposition
 if(in_array($_REQUEST['disposition'],["inline","attachment"])){$disposition=$_REQUEST['disposition'];}else{$disposition="attachment";}
 // debug
 api_debug();
 // build header
 header("Content-Description: File Transfer");
 //header("Content-Type: application/octet-stream");
 header("Content-Type: ".$attachment_obj->typology);
 header("Content-Disposition: ".$disposition.";filename=\"".$attachment_obj->name."\"");
 header("Expires: 0");
 header("Cache-Control: must-revalidate");
 header("Pragma: public");
 header("Content-Length: ".filesize($file_path));
 // get file
 readfile($file_path);
?>