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
 */
class cSession{

 /** Properties */
 protected $id;
 protected $validity;
 protected $duration;
 protected $idle;
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
   default:return false;
  }
 }

 /**
  * Load
  *
  * @return boolean
  */
 public function load(){
  // retrieve session from database
  $session_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__sessions` WHERE `id`='".$_SESSION['coordinator_session_id']."'",$GLOBALS['debug']);
  // check if session not exist or is expired
  if(!$session_obj->id||(time()-$session_obj->lastTimestamp)>$GLOBALS['settings']->sessions_idle_timeout){
   // unset session id and return
   unset($_SESSION['coordinator_session_id']);
   api_redirect(DIR."login.php?alert=sessionExpired");
   return false;
  }
  // set session
  $this->id=$session_obj->id;
  $this->duration=time()-$session_obj->startTimestamp;
  $this->idle=time()-$session_obj->lastTimestamp;
  $this->validity=true;
  // update last timestamp
  $GLOBALS['database']->queryExecute("UPDATE `framework__sessions` SET `lastTimestamp`='".time()."' WHERE `id`='".$this->id."'");
  // build user object
  $this->user=new cUser($session_obj->fkUser,true);
  // check maintenance
  if($GLOBALS['settings']->maintenance){
   if($this->user->superuser){
    api_alerts_add(api_text("alert_maintenance_enabled"),"warning");
   }else{
    unset($_SESSION['coordinator_session_id']);
    api_alerts_add(api_text("alert_maintenance"),"danger");
    api_redirect(DIR."login.php");
   }
   return false;
  }
  return true;
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
  if(!$GLOBALS['settings']->sessions_multiple){$GLOBALS['database']->queryExecute("DELETE FROM `framework__sessions` WHERE `fkUser`='".$session_obj->fkUser."'");}
  // insert session to database
  $GLOBALS['database']->queryInsert("framework__sessions",$session_obj);
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
  //session_destroy();  /** @todo ora distruggo solo variabili di coordinator non tutta la sessione vedere se parametrizzare o lasciare cosi */
  //session_start();
  $_SESSION['coordinator_session_id']=null;
  $_SESSION['coordinator_logs']=null;
 }

 /**
  * Count all Sessions
  */
 /*public function countAllSessions(){
  /** @todo cercare un nome decente..
  return $GLOBALS['database']->queryUniqueValue("SELECT COUNT(`id`) FROM `framework__sessions`");
 }*/

 /**
  * Count Online Users
  */
 /*public function countOnlineUsers(){
  /** @todo cercare un nome decente..
  return $GLOBALS['database']->queryUniqueValue("SELECT COUNT(DISTINCT(`fkUser`)) FROM `framework__sessions`");
 }*/

}
?>