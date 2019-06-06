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
   $session_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__sessions` WHERE `id`='".$_SESSION['coordinator_session_id']."'");
   // check if session not exist or is expired
   if(!$_SESSION['coordinator_session_id']||!$session_obj->id||(time()-$session_obj->lastTimestamp)>$GLOBALS['settings']->sessions_idle_timeout){
    // unset session id and return
    unset($_SESSION['coordinator_session_id']);
    api_redirect(PATH."login.php?alert=sessionExpired");   /** @todo salvare $_REQUEST in modo da poter fare redirect dopo il login */
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
     api_redirect(PATH."login.php");
    }
    return false;
   }
   return true;
  }

  /**
   * Build
   *
   * @param integer $account Account User ID
   * @return boolean
   */
  public function build($account){
   // build session object
   $session_obj=new stdClass();
   $session_obj->id=$this->id;
   $session_obj->fkUser=$account;
   $session_obj->address=$_SERVER["REMOTE_ADDR"];
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
  public function destroy(){
   // destroy session
   $_SESSION['coordinator_debug']=null;
   $_SESSION['coordinator_session_id']=null;
   $_SESSION['coordinator_alerts']=null;
   $_SESSION['coordinator_logs']=null;
   /** @todo verificare quale usare e spostare tutto dentro l'array.. verificare se usare hash dell'url o altro..
   $_SESSION['coordinator']=null;
   unset($_SESSION['coordinator']);
   */
  }

 }

?>