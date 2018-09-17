<?php
/**
 * Mail
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Mail class
 */
class cMail{

 /** Properties */
 protected $id;
 protected $recipients_to;
 protected $recipients_cc;
 protected $recipients_bcc;
 protected $sender_mail;
 protected $sender_name;
 protected $subject;
 protected $message;
 protected $attachments;
 protected $errors;
 protected $status;
 protected $addTimestamp;
 protected $addFkUser;
 protected $sndTimestamp;

 /**
  * Debug
  *
  * @return object Mail object
  */
 public function debug(){return $this;}

 /**
  * Mail class
  *
  * @param integer $mail Mail object or ID
  * @return boolean
  */
 public function __construct($mail){
  // get object
  if(is_numeric($mail)){$mail=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework_mails` WHERE `id`='".$mail."'",$GLOBALS['debug']);}
  if(!$mail->id){return false;}
  // set properties
  $this->id=(int)$mail->id;
  if(!$mail->recipients_to){$this->recipients_to=array();}
  else{$this->recipients_to=explode(";",stripslashes($mail->recipients_to));}
  if(!$mail->recipients_cc){$this->recipients_cc=array();}
  else{$this->recipients_cc=explode(";",stripslashes($mail->recipients_cc));}
  if(!$mail->recipients_bcc){$this->recipients_bcc=array();}
  else{$this->recipients_bcc=explode(";",stripslashes($mail->recipients_bcc));}
  $this->sender_mail=stripslashes($mail->sender_mail);
  $this->sender_name=stripslashes($mail->sender_name);
  $this->subject=stripslashes($mail->subject);
  $this->message=stripslashes($mail->message);
  if(!$mail->attachments){$this->attachments=array();}
  else{$this->attachments=explode(";",stripslashes($mail->attachments));}
  $this->errors=stripslashes($mail->errors);
  $this->status=stripslashes($mail->status);
  $this->addTimestamp=(int)$mail->addTimestamp;
  $this->addFkUser=(int)$mail->addFkUser;
  $this->sndTimestamp=(int)$mail->sndTimestamp;
  // return
  return true;
 }

 /**
  * Get
  *
  * @param string $property Property name
  * @return string Property value
  */
 public function __get($property){return $this->$property;}

}
?>