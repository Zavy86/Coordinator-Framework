<?php
/**
 * Sendmail Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Sendmail
 *
 * @param string $subject Subject
 * @param
 * @return integer|boolean Return script defined or false
 */
function api_sendmail($subject,$message,$recipients_to=null,$recipients_cc=null,$recipients_bcc=null,$sender_mail=null,$sender_name=null,$attachments=null){
 // build mail query objects
 $mail_qobj=new stdClass();
 $mail_qobj->subject=addslashes($subject);
 $mail_qobj->message=addslashes($message);
 $mail_qobj->recipients_to=str_replace(",",";",api_cleanString(strtolower($recipients_to),"/[^a-z0-9-_.,;@]/"));
 $mail_qobj->recipients_cc=str_replace(",",";",api_cleanString(strtolower($recipients_cc),"/[^a-z0-9-_.,;@]/"));
 $mail_qobj->recipients_bcc=str_replace(",",";",api_cleanString(strtolower($recipients_bcc),"/[^a-z0-9-_.,;@]/"));
 $mail_qobj->sender_mail=str_replace(",",";",api_cleanString(strtolower($sender_mail),"/[^a-z0-9-_.,;@]/"));
 $mail_qobj->sender_name=addslashes($sender_name);
 $mail_qobj->attachments=addslashes($attachments);
 $mail_qobj->status="inserted";
 $mail_qobj->addTimestamp=time();
 $mail_qobj->addFkUser=$GLOBALS['session']->user->id;
 // check for sender
 if(!$mail_qobj->sender_mail){$mail_qobj->sender_mail=$GLOBALS['settings']->sendmail_from_mail;}
 if(!$mail_qobj->sender_name){$mail_qobj->sender_name=$GLOBALS['settings']->sendmail_from_name;}
 // debug
 api_dump($mail_qobj,"mail query object");
 api_debug();
 // execute query
 $mail_qobj->id=$GLOBALS['database']->queryInsert("framework_mails",$mail_qobj);
 // check for mail id
 if(!$mail_qobj->id){return false;}
 // check for asynchronous sendmail option
 if(!$GLOBALS['settings']->sendmail_asynchronous){
  // try to send mail now
  api_sendmail_process($mail_qobj->id);
 }
 // return
 return $mail_qobj->id;
}

/**
 * Sendmail Process
 *
 * @param object|integer $mail Mail object or ID
 */
function api_sendmail_process($mail=null){
 // get object
 $mail_obj=new cMail($mail);
 // check object
 if(!$mail_obj->id){return false;}
 api_dump($mail_obj,"mail object");
 // include phpmailer
 require_once(ROOT."helpers/phpmailer/php/PHPMailer.php");
 require_once(ROOT."helpers/phpmailer/php/Exception.php");
 require_once(ROOT."helpers/phpmailer/php/SMTP.php");
 // build mailer object
 $mailer=new PHPMailer\PHPMailer\PHPMailer(true);
 // try to send mail
 try{
  // set parameters
  $mailer->CharSet="UTF-8";
  $mailer->Port=587;
  // check for smtp
  if($GLOBALS['settings']->sendmail_method=="smtp"){
   $mailer->isSMTP();
   //if($GLOBALS['debug']){$mailer->SMTPDebug=2;}
   $mailer->Host=$GLOBALS['settings']->sendmail_smtp_hostname;
   // check for authentication
   if($GLOBALS['settings']->sendmail_smtp_username){
    $mailer->SMTPAuth=true;
    $mailer->Username=$GLOBALS['settings']->sendmail_smtp_username;
    $mailer->Password=$GLOBALS['settings']->sendmail_smtp_password;
   }else{
    $mailer->SMTPAuth=false;
   }
   // secure
   switch(strtolower($GLOBALS['settings']->sendmail_smtp_encryption)){
    case "tls":$mailer->SMTPSecure="tls";break;
    case "ssl":$mailer->SMTPSecure="ssl";break;
    default:
     $mailer->SMTPSecure=null;
     $mailer->SMTPAutoTLS=false;
   }
  }
  // sender
  $mailer->setFrom($mail_obj->sender_mail,$mail_obj->sender_name);
  /** @todo se non funziona mettere sempre nel form come mittente la mail del dominio smtp */
  $mailer->addReplyTo($mail_obj->sender_mail);
  // recipients
  foreach($mail_obj->recipients_to as $to_f){$mailer->addAddress($to_f);}
  foreach($mail_obj->recipients_cc as $cc_f){$mailer->addCC($cc_f);}
  foreach($mail_obj->recipients_bcc as $bcc_f){$mailer->addBCC($bcc_f);}

  // get message body and add default footer
  //$mail_body=$mail_obj->message."<br><br>--<br>This message was automatically generated by Coordinator for ".$GLOBALS['settings']->owner.", please do not respond.";

  // build placeholders array
  $placeholders=array(
   "{framework-url}"=>URL,
   "{framework-title}"=>$GLOBALS['settings']->title,
   "{framework-owner}"=>$GLOBALS['settings']->owner,
   "{mail-subject}"=>$mail_obj->subject,
   "{mail-content}"=>$mail_obj->message
  );

  //api_dump($GLOBALS['settings']);

  // load template
  $template_source=file_get_contents(ROOT."uploads/framework/mails/template.default.html");
  // replace template placeholders
  $mail_obj->body=str_replace(array_keys($placeholders),$placeholders,$template_source);
  // debug
  api_dump($mail_obj->body,"mail body");

  // content
  $mailer->isHTML(TRUE);
  $mailer->Subject=$mail_obj->subject;
  $mailer->Body=$mail_obj->body;
  $mailer->AltBody=strip_tags(str_replace("<br>","\n",$mail_obj->message));

  //$mailer->Body=$mail_body;
  //$mailer->AltBody=strip_tags(str_replace("<br>","\n",$mail_body));

  // attachments
  foreach($mail_obj->attachments as $attachment_f){if(file_exists(ROOT.$attachment_f)){$mailer->addAttachment(ROOT.$attachment_f);}}
  // try to send mail
  $mail_sended=$mailer->send();
  if($mail_sended){api_dump("Mail #".$mail_obj->id." succesfull sended!","mailer return");}
 }catch(Exception $e){
  api_dump("Mail #".$mail_obj->id." sending failed!","mailer return");
  api_dump($mailer->ErrorInfo,"mailer error informations");
  //api_dump($mailer,"mailer");
 }
 // build mail query objects
 $mail_qobj=new stdClass();
 $mail_qobj->id=$mail_obj->id;
 // check
 if($mail_sended){
  $mail_qobj->status="sended";
  $mail_qobj->errors=null;
  $mail_qobj->sndTimestamp=time();
 }else{
  $mail_qobj->status="failed";
  $mail_qobj->errors=$mailer->ErrorInfo;
  $mail_qobj->sndTimestamp=null;
 }
 // debug
 api_dump($mail_qobj,"mail query object");
 // execute query
 $mail_qobj->id=$GLOBALS['database']->queryUpdate("framework_mails",$mail_qobj);
 // return
 if($mail_sended){return true;}else{return false;}
}

/**
 * Sendmail Process
 */
function api_sendmail_process_all(){
 // get inserted mails
 $mails_array=api_framework_mails("inserted");
 // cycle all mails
 foreach($mails_array as $mail_obj){api_sendmail_process($mail_obj->id);}
}

/**
 * Mails
 *
 * @param boolean $status
 * @return object $return[] Array of mail objects
 */
function api_framework_mails($status=null){  /** @todo levare framework? */
 // definitions
 $return=array();
 $status_array=array();
 // add status to array
 if(is_array($status)){$status_array=$status;}elseif($status){$status_array[]=$status;}
 // make query where
 $query_where="1";
 foreach($status_array as $status_f){$query_where.=" OR `status`='".$status_f."'";}
 if(count($status_array)){$query_where=substr($query_where,5);}
 //api_dump("SELECT * FROM `framework_mails` WHERE ".$query_where." ORDER BY `id` ASC");
 // execute query
 $mails_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_mails` WHERE ".$query_where." ORDER BY `id` ASC");
 foreach($mails_results as $mail){$return[$mail->id]=new cMail($mail);}
 // return groups
 return $return;
}

?>