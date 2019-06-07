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
    $this->id=api_random_id();
   }
  }

  /**
   * Get
   *
   * @param string $property Property name
   * @return string Property value
   */
  public function __get($property){return $this->$property;}

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
   * Logout
   */
  public function logout(){
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

  /**
   * Login
   *
   * @param string $username Username or mail address
   * @param string $password Account password
   * @return boolean
   */
  public function login($username,$password){
   // generate new session id
   $this->id=api_random_id();
   $_SESSION['coordinator_session_id']=$this->id;
   // debug
   api_dump($GLOBALS['settings']->sessions_authentication_method,"authentication method");
   // switch authentication method
   switch($GLOBALS['settings']->sessions_authentication_method){
    case "ldap":
     // ldap authentication
     $authentication_result=api_authentication_ldap($username,$password);
     // if binding error try with standard authentication
     if($authentication_result==-2){$authentication_result=api_authentication($username,$password);}
     break;
    default:
     // standard authentication
     $authentication_result=api_authentication($username,$password);
   }
   // debug authentication result
   api_dump($authentication_result,"authentication_result");
   // check authentication result
   if($authentication_result<1){return false;}
   // set user id
   $user_id=$authentication_result;
   // build session
   $this->build($user_id);
   // build user query objects
   $user_qobj=new stdClass();
   // acquire variables
   $user_qobj->id=$user_id;
   $user_qobj->lsaTimestamp=time();
   // debug
   api_dump($user_qobj,"user query object");
   // update user
   $GLOBALS['database']->queryUpdate("framework__users",$user_qobj);
   // return
   return true;
  }

  /**
   * Build
   *
   * @param integer $account Account User ID
   * @return boolean
   */
  private function build($account){
   // build session object
   $session_qobj=new stdClass();
   $session_qobj->id=$this->id;
   $session_qobj->fkUser=$account;
   $session_qobj->address=$_SERVER["REMOTE_ADDR"];
   $session_qobj->startTimestamp=time();
   $session_qobj->lastTimestamp=time();
   // debug
   api_dump($session_qobj,"session query object");
   // if multiple sessions are not allowed delete previous account sessions
   if(!$GLOBALS['settings']->sessions_multiple){$GLOBALS['database']->queryExecute("DELETE FROM `framework__sessions` WHERE `fkUser`='".$session_qobj->fkUser."'");}
   // insert session to database
   $GLOBALS['database']->queryInsert("framework__sessions",$session_qobj);
   // set coordinator session id
   $_SESSION['coordinator_session_id']=$session_qobj->id;
   // load session
   $this->load();
  }

 }

?>