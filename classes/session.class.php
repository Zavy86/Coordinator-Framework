<?php
/**
 * Session
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Session class
 *
 * @todo check phpdoc
 */
class Session{
 /** @var string $id Session ID */
 protected $id;
 /** @var boolean $valid Session validity */
 protected $validity;
 /** @var boolean $duration Session duration */
 protected $duration;
 /** @var boolean $idle Session idle */
 protected $idle;
 /** @var \User $user Account User class */
 protected $user;

 /**
  * Debug
  *
  * @return object Session object
  */
 public function debug(){return $this;}

 /**
  * Session class
  *
  */
 public function __construct(){
  // check for session
  if($_SESSION['coordinator_session_id']){
   // try to load session
   $this->load();
  }else{
   // generate new session id
   $this->id=md5(date("YmdHis").rand(1,99999));
  }
 }

/**
 * Get
 *
 * @param string $property Property name
 * @return mixed value
 */
 public function __get($property){
  // switch properties
  switch($property){
   case "id":return $this->id;
   case "validity":return $this->validity;
   case "duration":return $this->duration;
   case "idle":return $this->idle;
   case "user":return $this->user;
   default:return FALSE;
  }
 }

 /**
  * Load
  *
  * @return boolean
  */
 public function load(){
  // retrieve session from database
  $session_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework_sessions` WHERE `id`='".$_SESSION['coordinator_session_id']."'",$GLOBALS['debug']);
  // check if session not exist or is expired
  if(!$session_obj->id||(time()-$session_obj->lastTimestamp)>$GLOBALS['settings']->sessions_idle_timeout){
   // unset session id and return
   unset($_SESSION['coordinator_session_id']);
   api_redirect(DIR."login.php?alert=sessionExpired");
   return FALSE;
  }
  // set session
  $this->id=$session_obj->id;
  $this->duration=time()-$session_obj->startTimestamp;
  $this->idle=time()-$session_obj->lastTimestamp;
  $this->validity=TRUE;
  // update last timestamp
  $GLOBALS['database']->queryExecute("UPDATE `framework_sessions` SET `lastTimestamp`='".time()."' WHERE `id`='".$this->id."'");

  // build user object
  $this->user=new User($session_obj->fkUser);

  // check maintenance
  if($GLOBALS['settings']->maintenance){ /** @ and user not administrator */
   unset($_SESSION['coordinator_session_id']);
   api_redirect(DIR."login.php?alert=maintenance");
   return FALSE;
  }

  return TRUE;
 }

 /**
  * Build
  *
  * @param string $account Account User ID
  * @return boolean
  */
 public function build($account){
  // build session object
  $session_obj=new stdClass();
  $session_obj->id=$this->id;
  $session_obj->fkUser=$account;
  $session_obj->ipAddress=$_SERVER["REMOTE_ADDR"];
  $session_obj->startTimestamp=time();
  $session_obj->lastTimestamp=time();
  // debug
  api_dump($session_obj,"session_obj");
  // if multiple sessions are not allowed delete previous account sessions
  if(!$GLOBALS['settings']->sessions_multiple){$GLOBALS['database']->queryExecute("DELETE FROM `framework_sessions` WHERE `fkUser`='".$session_obj->fkUser."'");}
  // insert session to database
  $GLOBALS['database']->queryInsert("framework_sessions",$session_obj);
  // set coordinator session id
  $_SESSION['coordinator_session_id']=$session_obj->id;
  // load session
  $this->load();
 }

 /**
  * Destroy
  */
 public function destroy(){ /** @todo cercare un nome decente.. */
  // destroy session
  //session_destroy();  /** @todo distruggere solo variabili di coordinator */
  //session_start();
  $_SESSION['coordinator_session_id']=NULL;
  $_SESSION['coordinator_logs']=NULL;
 }

 /**
  * Count all Sessions
  */
 public function countAllSessions(){ /** @todo cercare un nome decente.. */
  return $GLOBALS['database']->queryUniqueValue("SELECT COUNT(`id`) FROM `framework_sessions`");
 }

 /**
  * Count Online Users
  */
 public function countOnlineUsers(){ /** @todo cercare un nome decente.. */
  return $GLOBALS['database']->queryUniqueValue("SELECT COUNT(DISTINCT(`fkUser`)) FROM `framework_sessions`");
 }

}
?>