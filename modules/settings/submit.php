<?php
/**
 * Settings - Submit
 *
 * @package Coordinator\Modules\Accounts
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
// check for actions
if(!defined('ACTION')){die("ERROR EXECUTING SCRIPT: The action was not defined");}

// switch action
switch(ACTION){
 // settings
 case "settings_framework":settings_framework();break;

 // users
 case "user_login":user_login();break;
 case "user_logout":user_logout();break;
 case "user_recovery":user_recovery();break;

// own
 case "own_profile_update":own_profile_update();break;
 case "own_password_update":own_password_update();break;

 // sessions
 case "sessions_terminate":sessions_terminate();break;
 case "sessions_terminate_all":sessions_terminate_all();break;

 // default
 default:
  /** @todo alerts */
  /*$alert="?alert=submitFunctionNotFound&alert_class=alert-warning&act=".$act;
  exit(header("location: ".DIR."index.php".$alert));*/
  die("ERROR EXECUTING SCRIPT: The action ".ACTION." was not found");
}

/**
 * Settings Framework
 */
function settings_framework(){
 // acquire variables
 $r_tab=$_REQUEST['tab'];
 // definitions
 $settings_array=array();
 $availables_settings_array=array(
  /* general */
  "maintenance","title","owner",
  /* sessions */
  "sessions_authentication_method","sessions_multiple","sessions_idle_timeout",
  "sessions_ldap_hostname","sessions_ldap_dn","sessions_ldap_domain",
  "sessions_ldap_userfield","sessions_ldap_groups","sessions_ldap_cache",
  /* sendmail */
  "sendmail_from_name","sendmail_from_mail","sendmail_asynchronous","sendmail_method",
  "sendmail_smtp_hostname","sendmail_smtp_username","sendmail_smtp_encryption",
  /* tokens */
  "token_cron"
 );

 api_dump($_REQUEST);

 // cycle all form fields and set availables
 foreach($_REQUEST as $setting=>$value){if(in_array($setting,$availables_settings_array)){$settings_array[$setting]=$value;}}

 // sendmail smtp password (save password only if change)
 if(isset($settings_array['sendmail_smtp_username'])){if($settings_array['sendmail_smtp_username']){if($_REQUEST['sendmail_smtp_password']){$settings_array['sendmail_smtp_password']=$_REQUEST['sendmail_smtp_password'];}}else{$settings_array['sendmail_smtp_password']=NULL;}}

 api_dump($settings_array);

 // cycle all settings
 foreach($settings_array as $setting=>$value){
  // buil setting query
  $query="INSERT INTO `settings_settings` (`setting`,`value`) VALUES ('".$setting."','".$value."') ON DUPLICATE KEY UPDATE `setting`='".$setting."',`value`='".$value."'";
  // execute setting query
  $GLOBALS['database']->queryExecute($query,$GLOBALS['debug']);
  api_dump($query);
 }
 // redirect
 api_redirect("?mod=settings&scr=settings_framework&tab=".$r_tab."&alert=settingsFrameworkUpdated"); /** @todo rifare alert */
}






/**
 * User Authentication
 *
 * @param string $username Username (Mail address)
 * @param string $password Password
 * @return integer Account User ID or Error Code
 *                 -1 User account was not found
 *                 -2 Password does not match
 */
function user_authentication($username,$password){
 // retrieve user object
 $user_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `accounts_users` WHERE `mail`='".$username."'",$GLOBALS['debug']);
 if(!$user_obj->id){return -1;}
 if(md5($password)!==$user_obj->password){return -2;}
 return $user_obj->id;
}

/**
 * User Login
 */
function user_login(){
 // acquire variables
 $r_username=$_REQUEST['username'];
 $r_password=$_REQUEST['password'];
 //
 api_dump($_SESSION["coordinator_session_id"],"session_id");
 api_dump($GLOBALS['session']->debug(),"session");

 // switch authentication method
 switch($GLOBALS['settings']->sessions_authentication_method){
  case "ldap":
   /** @todo ldap auth */
   break;
  default:
   // standard authentication
   $authentication_result=user_authentication($r_username,$r_password);
 }
 // check authentication result
 if($authentication_result<1){api_redirect(DIR."login.php?alert=authenticationFailed");} /** @todo fare bene l'alert */
 // try to authenticate user
 $GLOBALS['session']->build($authentication_result);
 //
 api_dump($_SESSION["coordinator_session_id"],"session_id after");
 api_dump($GLOBALS['session']->debug(),"session after");
 // redirect
 api_redirect(DIR."index.php");
}

/**
 * User Logout
 */
function user_logout(){
 // destroy session  /** @todo cercare un nome decente.. */
 $GLOBALS['session']->destroy();
 // redirect
 api_redirect(DIR."index.php");
}

/**
 * User Recovery   /** @todo rename in own ?
 */
function user_recovery(){
 // acquire variables
 $r_mail=$_REQUEST['mail'];
 $r_secret=$_REQUEST['secret'];
 // retrieve user object
 $user_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `accounts_users` WHERE `mail`='".$r_mail."'",$GLOBALS['debug']);
 // check user
 if(!$user_obj->id){api_redirect(DIR."login.php?error=userNotFound");} /** @todo sistemare error alert */
 // remove all user sessions
 $GLOBALS['database']->queryExecute("DELETE FROM `coordinator_sessions` WHERE `fkUser`='".$user_obj->id."'");
 // check for secret
 if(!$r_secret){
  // generate new secret code and save into database
  $f_secret=md5(date("Y-m-d H:i:s").rand(1,99999));
  $GLOBALS['database']->queryExecute("UPDATE `accounts_users` SET `secret`='".$f_secret."' WHERE `id`='".$user_obj->id."'");
  $recoveryLink=URL."index.php?mod=settings&scr=submit&act=user_recovery&mail=".$r_mail."&secret=".$f_secret;
  // send recovery link
  api_sendmail($r_mail,"Coordinator password recovery",$recoveryLink); /** @todo fare mail come si deve */
  // redirect
  api_redirect(DIR."login.php?error=userRecoveryLinkSended"); /** @todo sistemare error alert */
 }else{
  // check secret code
  if($r_secret!==$user_obj->secret){api_redirect(DIR."login.php?error=userRecoverySecretError");} /** @todo sistemare error alert */
  // generate new password
  $f_password=substr(md5(date("Y-m-d H:i:s").rand(1,99999)),0,8);
  // update password and reset secret
  $GLOBALS['database']->queryExecute("UPDATE `accounts_users` SET `password`='".md5($f_password)."',`secret`=NULL,`pwdTimestamp`=NULL WHERE `id`='".$user_obj->id."'");
  // send new password
  api_sendmail($r_mail,"Coordinator new password",$f_password); /** @todo fare mail come si deve */
  // redirect
  api_redirect(DIR."login.php?error=userRecoveryPasswordSended"); /** @todo sistemare error alert */
 }
}

/**
 * Own Profile Update
 */
function own_profile_update(){
 // build user objects
 $user=new stdClass();
 $user->id=$GLOBALS['session']->user->id;
 // acquire variables
 $user->firstname=$_REQUEST['firstname'];
 $user->lastname=$_REQUEST['lastname'];
 $user->localization=$_REQUEST['localization'];
 $user->timezone=$_REQUEST['timezone'];
 $user->updTimestamp=time();
 $user->updFkUser=$GLOBALS['session']->user->id;
 // debug
 api_dump($user);
 // update user
 $GLOBALS['database']->queryUpdate("accounts_users",$user);
 // upload avatar
 if(intval($_FILES['avatar']['size'])>0 && $_FILES['avatar']['error']==UPLOAD_ERR_OK){
  if(!is_dir(ROOT."uploads/accounts/users")){mkdir(ROOT."uploads/accounts/users",0777,TRUE);}
  if(file_exists(ROOT."uploads/accounts/users/avatar_".$user->id.".jpg")){unlink(ROOT."uploads/accounts/users/avatar_".$user->id.".jpg");}
  if(is_uploaded_file($_FILES['avatar']['tmp_name'])){move_uploaded_file($_FILES['avatar']['tmp_name'],ROOT."uploads/accounts/users/avatar_".$user->id.".jpg");}
 }
 // redirect
 api_redirect("?mod=settings&scr=own_profile&alert=userProfileUpdated"); /** @todo sistemare error alert */
}

/**
 * Own Password Update
 */
function own_password_update(){
 // retrieve user object
 $user_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `accounts_users` WHERE `id`='".$GLOBALS['session']->user->id."'",$GLOBALS['debug']);
 // check
 if(!$user_obj->id){api_redirect(DIR."index.php?alert=userNotFound");} /** @todo sistemare error alert */
 // acquire variables
 $r_password=$_REQUEST['password'];
 $r_password_new=$_REQUEST['password_new'];
 $r_password_confirm=$_REQUEST['password_confirm'];
 // check old password
 if(md5($r_password)!==$user_obj->password){api_redirect("?mod=settings&scr=own_password&alert=userPasswordIncorrect");} /** @todo sistemare error alert */
 // check new password
 if(!$r_password_new||$r_password_new!==$r_password_confirm){api_redirect("?mod=settings&scr=own_password&alert=userPasswordNotMatch");} /** @todo sistemare error alert */
 // build user objects
 $user=new stdClass();
 $user->id=$user_obj->id;
 $user->password=md5($r_password_new);
 $user->pwdTimestamp=time();
 // debug
 api_dump($user);
 // insert user to database
 $GLOBALS['database']->queryUpdate("accounts_users",$user);
 // redirect
 api_redirect("?mod=settings&scr=own_profile&alert=userProfileUpdated"); /** @todo sistemare error alert */
}


/**
 * Sessions Terminate
 */
function sessions_terminate(){
 $idSession=$_REQUEST['idSession'];
 if(!$idSession){api_redirect(DIR."index.php?alert=sessionNotFound");} /** @todo sistemare error alert */
 // delete session
 $GLOBALS['database']->queryExecute("DELETE FROM `coordinator_sessions` WHERE `id`='".$idSession."'");
 // redirect
 api_redirect("?mod=settings&scr=sessions_list&alert=sessionTerminated");
}
/**
 * Sessions Terminate All
 */
function sessions_terminate_all(){
 // delete all sessions
 $GLOBALS['database']->queryExecute("DELETE FROM `coordinator_sessions`");
 // redirect
 api_redirect(DIR."index.php");
}

?>