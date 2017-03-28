<?php
/**
 * Framework - Submit
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
// check for actions
if(!defined('ACTION')){die("ERROR EXECUTING SCRIPT: The action was not defined");}
// switch action
switch(ACTION){
 // settings
 case "settings_framework":settings_framework();break;

 // menus
 case "menu_save":menu_save();break;

 // users old
 case "user_login":user_login();break;
 case "user_logout":user_logout();break;
 case "user_recovery":user_recovery();break;
 /** @todo ^ check */

 // own
 case "own_profile_update":own_profile_update();break;
 case "own_password_update":own_password_update();break;

 // users
 case "user_add":user_add();break;
 case "user_edit":user_edit();break;
 case "user_delete":user_deleted(TRUE);break;
 case "user_undelete":user_deleted(FALSE);break;
 case "user_group_add":user_group_add();break;
 case "user_group_remove":user_group_remove();break;

 // groups
 case "group_save":group_save();break;
 /** @todo delete */
 /** @todo undelete */

 // sessions
 case "sessions_terminate":sessions_terminate();break;
 case "sessions_terminate_all":sessions_terminate_all();break;

 // modules
 case "module_add":module_add();break;
 case "module_enable":module_enable(TRUE);break;
 case "module_disable":module_enable(FALSE);break;
 case "module_update_source":module_update_source();break;
 case "module_update_database":module_update_database();break;
 case "module_authorizations_group_add":module_authorizations_group_add();break;
 case "module_authorizations_group_remove":module_authorizations_group_remove();break;
 case "module_authorizations_reset":module_authorizations_reset();break;

 // default
 default:
  api_alerts_add(api_text("alert_submitFunctionNotFound",array(MODULE,SCRIPT,ACTION)),"danger");
  api_redirect("?mod=".MODULE);
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
  /* users */
  "users_password_expiration","users_level_max",
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
  $query="INSERT INTO `framework_settings` (`setting`,`value`) VALUES ('".$setting."','".$value."') ON DUPLICATE KEY UPDATE `setting`='".$setting."',`value`='".$value."'";
  // execute setting query
  $GLOBALS['database']->queryExecute($query,$GLOBALS['debug']);
  api_dump($query);
 }

 // downgrade user level out of limit
 if(isset($settings_array["users_level_max"])){$GLOBALS['database']->queryExecute("UPDATE `framework_users` SET `level`='".$settings_array["users_level_max"]."' WHERE `level`>'".$settings_array["users_level_max"]."'");}

 /** @todo caricamento logo */

 // redirect
 api_alerts_add(api_text("settings_alert_settingsUpdated"),"success");
 api_redirect("?mod=framework&scr=settings_framework&tab=".$r_tab);
}

/**
 * Menu Save
 */
function menu_save(){
 // get menu object
 $menu_obj=new Menu($_REQUEST['idMenu']);
 // acquire variables
 $r_fkMenu=$_REQUEST['fkMenu'];
 $r_typology=$_REQUEST['typology'];
 $r_icon=addslashes($_REQUEST['icon']);
 $r_label=addslashes($_REQUEST['label']);
 $r_title=addslashes($_REQUEST['title']);
 $r_url=addslashes($_REQUEST['url']);
 $r_module=addslashes($_REQUEST['module']);
 $r_script=addslashes($_REQUEST['script']);
 $r_tab=addslashes($_REQUEST['tab']);
 $r_action=addslashes($_REQUEST['action']);
 $r_target=addslashes($_REQUEST['target']);
 // check variables
 if(!$r_url){$r_url="#";}
 // build menu query objects
 $menu_qobj=new stdClass();
 $menu_qobj->id=$menu_obj->id;
 $menu_qobj->fkMenu=$r_fkMenu;
 $menu_qobj->icon=$r_icon;
  // switch menu typology
 switch($r_typology){
  // link
  case "link":
   $menu_qobj->label=$r_label;
   $menu_qobj->title=$r_title;
   $menu_qobj->url=$r_url;
   $menu_qobj->module=NULL;
   $menu_qobj->script=NULL;
   $menu_qobj->tab=NULL;
   $menu_qobj->action=NULL;
   $menu_qobj->target=NULL;
   break;
  // module
  case "module":
   $menu_qobj->label="{".$r_module."}";
   $menu_qobj->title="{".$r_module."-description}";
   $menu_qobj->url=NULL;
   $menu_qobj->module=$r_module;
   $menu_qobj->script=$r_script;
   $menu_qobj->tab=$r_tab;
   $menu_qobj->action=$r_action;
   $menu_qobj->target=$r_target;
   break;
 }
 // make order
 if(!$menu_obj->id || $menu_qobj->fkMenu<>$menu_obj->fkMenu){
  if($menu_qobj->fkMenu){$order_query_where="`fkMenu`='".$menu_qobj->fkMenu."'";}else{$order_query_where="`fkMenu` IS NULL";}
  $v_order=$GLOBALS['database']->queryUniqueValue("SELECT `order` FROM `framework_menus` WHERE ".$order_query_where." ORDER BY `order` DESC");
  $menu_qobj->order=($v_order+1);
 }
 // debug
 api_dump($menu_qobj);
 // check menu
 if($menu_obj->id){
  // update menu
  $menu_qobj->updTimestamp=time();
  $menu_qobj->updFkUser=$GLOBALS['session']->user->id;
  $GLOBALS['database']->queryUpdate("framework_menus",$menu_qobj);
  // check if parent menu is changed
  if($menu_qobj->fkMenu<>$menu_obj->fkMenu){
   // rebase other menus
   if($menu_obj->fkMenu){$order_query_where="`fkMenu`='".$menu_obj->fkMenu."'";}else{$order_query_where="`fkMenu` IS NULL";}
   api_dump("UPDATE `framework_menus` SET `order`=`order`-'1' WHERE `order`>'".$menu_obj->order."' AND ".$order_query_where." ORDER BY `order` ASC");
   $GLOBALS['database']->queryExecute("UPDATE `framework_menus` SET `order`=`order`-'1' WHERE `order`>'".$menu_obj->order."' AND ".$order_query_where." ORDER BY `order` ASC");
  }
  api_alerts_add(api_text("settings_alert_menuUpdated"),"success");
 }else{
  // insert menu
  $menu_qobj->addTimestamp=time();
  $menu_qobj->addFkUser=$GLOBALS['session']->user->id;
  $GLOBALS['database']->queryInsert("framework_menus",$menu_qobj);
  api_alerts_add(api_text("settings_alert_menuCreated"),"success");
 }
 // redirect
 api_redirect("?mod=framework&scr=menus_list");
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
 $user_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework_users` WHERE `mail`='".$username."'",$GLOBALS['debug']);
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
 if($authentication_result<1){api_alerts_add(api_text("alert_authenticationFailed"),"warning");api_redirect(DIR."login.php");}
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
 $user_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework_users` WHERE `mail`='".$r_mail."'",$GLOBALS['debug']);
 // check user
 if(!$user_obj->id){api_redirect(DIR."login.php?error=userNotFound");} /** @todo sistemare error alert */
 // remove all user sessions
 $GLOBALS['database']->queryExecute("DELETE FROM `framework_sessions` WHERE `fkUser`='".$user_obj->id."'");
 // check for secret
 if(!$r_secret){
  // generate new secret code and save into database
  $f_secret=md5(date("Y-m-d H:i:s").rand(1,99999));
  $GLOBALS['database']->queryExecute("UPDATE `framework_users` SET `secret`='".$f_secret."' WHERE `id`='".$user_obj->id."'");
  $recoveryLink=URL."index.php?mod=framework&scr=submit&act=user_recovery&mail=".$r_mail."&secret=".$f_secret;
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
  $GLOBALS['database']->queryExecute("UPDATE `framework_users` SET `password`='".md5($f_password)."',`secret`=NULL,`pwdTimestamp`=NULL WHERE `id`='".$user_obj->id."'");
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
 $GLOBALS['database']->queryUpdate("framework_users",$user);
 // upload avatar
 if(intval($_FILES['avatar']['size'])>0 && $_FILES['avatar']['error']==UPLOAD_ERR_OK){
  if(!is_dir(ROOT."uploads/framework/users")){mkdir(ROOT."uploads/framework/users",0777,TRUE);}
  if(file_exists(ROOT."uploads/framework/users/avatar_".$user->id.".jpg")){unlink(ROOT."uploads/framework/users/avatar_".$user->id.".jpg");}
  if(is_uploaded_file($_FILES['avatar']['tmp_name'])){move_uploaded_file($_FILES['avatar']['tmp_name'],ROOT."uploads/framework/users/avatar_".$user->id.".jpg");}
 }
 // redirect
 api_alerts_add(api_text("settings_alert_ownProfileUpdated"),"success");
 api_redirect("?mod=framework&scr=own_profile");
}
/**
 * Own Password Update
 */
function own_password_update(){
 // retrieve user object
 $user_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework_users` WHERE `id`='".$GLOBALS['session']->user->id."'",$GLOBALS['debug']);
 // check
 if(!$user_obj->id){api_alerts_add(api_text("settings_alert_userNotFound"),"danger");api_redirect(DIR."index.php");}
 // acquire variables
 $r_password=$_REQUEST['password'];
 $r_password_new=$_REQUEST['password_new'];
 $r_password_confirm=$_REQUEST['password_confirm'];
 // check old password
 if(md5($r_password)!==$user_obj->password){api_alerts_add(api_text("settings_alert_ownPasswordIncorrect"),"danger");api_redirect("?mod=framework&scr=own_password");}
 // check new password
 if($r_password_new!==$r_password_confirm){api_alerts_add(api_text("settings_alert_ownPasswordNotMatch"),"danger");api_redirect("?mod=framework&scr=own_password");}
 if(strlen($r_password_new)<8){api_alerts_add(api_text("settings_alert_ownPasswordWeak"),"danger");api_redirect("?mod=framework&scr=own_password");}
 // check if new password is equal to oldest password
 if(md5($r_password_new)===$user_obj->password){api_alerts_add(api_text("settings_alert_ownPasswordOldest"),"danger");api_redirect("?mod=framework&scr=own_password");}
 // build user objects
 $user=new stdClass();
 $user->id=$user_obj->id;
 $user->password=md5($r_password_new);
 $user->pwdTimestamp=time();
 // debug
 api_dump($user);
 // insert user to database
 $GLOBALS['database']->queryUpdate("framework_users",$user);
 // redirect
 api_alerts_add(api_text("settings_alert_ownPasswordUpdated"),"success");
 api_redirect("?mod=framework&scr=own_profile");
}

/**
 * User Add
 */
function user_add(){
 // make password
 $v_password=substr(md5(date("Y-m-d H:i:s").rand(1,99999)),0,8);
 // build user objects
 $user_obj=new stdClass();
 // acquire variables
 $user_obj->mail=$_REQUEST['mail'];
 $user_obj->firstname=$_REQUEST['firstname'];
 $user_obj->lastname=$_REQUEST['lastname'];
 $user_obj->localization=$_REQUEST['localization'];
 $user_obj->timezone=$_REQUEST['timezone'];
 $user_obj->level=$_REQUEST['level'];
 $user_obj->password=md5($v_password);
 $user_obj->enabled=1;
 $user_obj->addTimestamp=time();
 $user_obj->addFkUser=$GLOBALS['session']->user->id;
 // debug
 api_dump($_REQUEST);
 api_dump($user_obj);
 // update user
 $user_obj->id=$GLOBALS['database']->queryInsert("framework_users",$user_obj);
 // check user
 if(!$user_obj->id){api_alerts_add(api_text("settings_alert_userError"),"danger");api_redirect("?mod=framework&scr=users_list");}
 // send password to user
 api_sendmail($user_obj->mail,"Coordinator new user welcome","Your access password is:\n\n".$v_password); /** @todo fare mail come si deve */
 // redirect
 api_alerts_add(api_text("settings_alert_userCreated"),"success");
 api_redirect("?mod=framework&scr=users_view&idUser=".$user_obj->id);
}
/**
 * User Edit
 */
function user_edit(){
 // get objects
 $user_obj=new User($_REQUEST['idUser']);
 // check objects
 if(!$user_obj->id){api_alerts_add(api_text("settings_alert_userNotFound"),"danger");api_redirect("?mod=framework&scr=users_list");}
 // build user query objects
 $user_qobj=new stdClass();
 // acquire variables
 $user_qobj->id=$user_obj->id;
 $user_qobj->enabled=$_REQUEST['enabled'];
 $user_qobj->mail=$_REQUEST['mail'];
 $user_qobj->firstname=$_REQUEST['firstname'];
 $user_qobj->lastname=$_REQUEST['lastname'];
 $user_qobj->localization=$_REQUEST['localization'];
 $user_qobj->timezone=$_REQUEST['timezone'];
 $user_qobj->level=$_REQUEST['level'];
 $user_qobj->superuser=$_REQUEST['superuser'];
 $user_qobj->gender=$_REQUEST['gender'];
 $user_qobj->birthday=$_REQUEST['birthday'];
 $user_qobj->updTimestamp=time();
 $user_qobj->updFkUser=$GLOBALS['session']->user->id;
 // debug
 api_dump($_REQUEST,"_REQUEST");
 api_dump($user_qobj,"user_qobj");
 // update user
 $GLOBALS['database']->queryUpdate("framework_users",$user_qobj);
 // redirect
 api_alerts_add(api_text("settings_alert_userUpdated"),"success");
 api_redirect("?mod=framework&scr=users_view&idUser=".$user_obj->id);
}
/**
 * User Deleted
 *
 * @param boolean $deleted Deleted or Undeleted
 */
function user_deleted($deleted){
 // get objects
 $user_obj=new User($_REQUEST['idUser']);
 // check
 if(!$user_obj->id){api_alerts_add(api_text("settings_alert_userNotFound"),"danger");api_redirect("?mod=framework&scr=users_list");}
 // build user query objects
 $user_qobj=new stdClass();
 $user_qobj->id=$user_obj->id;
 $user_qobj->deleted=($deleted?1:0);
 if($deleted){$user_qobj->enabled=0;}
 $user_qobj->updTimestamp=time();
 $user_qobj->updFkUser=$GLOBALS['session']->user->id;
 // debug
 api_dump($_REQUEST);
 api_dump($user_qobj);
 // update user
 $GLOBALS['database']->queryUpdate("framework_users",$user_qobj);
 // alert
 if($deleted){api_alerts_add(api_text("settings_alert_userDeleted"),"warning");}
 else{api_alerts_add(api_text("settings_alert_userUndeleted"),"success");}
 // redirect
 api_redirect("?mod=framework&scr=users_view&idUser=".$user_obj->id);
}
/**
 * User Group Add
 */
function user_group_add(){
 // get objects
 $user_obj=new User($_REQUEST['idUser']);
 // check objects
 if(!$user_obj->id){api_alerts_add(api_text("settings_alert_userNotFound"),"danger");api_redirect("?mod=framework&scr=users_list");}
 // check for duplicates
 if(array_key_exists($_REQUEST['fkGroup'],$user_obj->groups_array)){api_alerts_add(api_text("settings_alert_userGroupDuplicated"),"warning");api_redirect("?mod=framework&scr=users_view&idUser=".$user_obj->id);}
 // build user join group query object
 $user_join_group_qobj=new stdClass();
 $user_join_group_qobj->fkUser=$user_obj->id;
 $user_join_group_qobj->fkGroup=$_REQUEST['fkGroup'];
 $user_join_group_qobj->main=(count($user_obj->groups_array)?0:1);
 // build user query object
 $user_qobj=new stdClass();
 $user_qobj->id=$user_obj->id;
 $user_qobj->updTimestamp=time();
 $user_qobj->updFkUser=$GLOBALS['session']->user->id;
 // debug
 api_dump($_REQUEST,"_REQUEST");
 api_dump($user_obj,"user_obj");
 api_dump($user_join_group_qobj,"user_join_group_qobj");
 api_dump($user_qobj,"user_qobj");
 // insert group
 $GLOBALS['database']->queryInsert("framework_users_join_groups",$user_join_group_qobj);
 // update user
 $GLOBALS['database']->queryUpdate("framework_users",$user_qobj);
 // redirect
 api_alerts_add(api_text("settings_alert_userGroupAdded"),"success");
 api_redirect("?mod=framework&scr=users_view&idUser=".$user_obj->id);
}
/**
 * User Group Remove
 */
function user_group_remove(){
 // get objects
 $user_obj=new User($_REQUEST['idUser']);
 // check objects
 if(!$user_obj->id){api_alerts_add(api_text("settings_alert_userNotFound"),"danger");api_redirect("?mod=framework&scr=users_list");}
 // check if user is in request group
 if(!array_key_exists($_REQUEST['idGroup'],$user_obj->groups_array)){api_alerts_add(api_text("settings_alert_userGroupNotFound"),"danger");api_redirect("?mod=framework&scr=users_view&idUser=".$user_obj->id);}
 // check if request group is main for user and not only
 if(count($user_obj->groups_array)>1 && $user_obj->groups_main==$_REQUEST['idGroup']){api_alerts_add(api_text("settings_alert_userGroupError"),"danger");api_redirect("?mod=framework&scr=users_view&idUser=".$user_obj->id);}
 // build user query object
 $user_qobj=new stdClass();
 $user_qobj->id=$user_obj->id;
 $user_qobj->updTimestamp=time();
 $user_qobj->updFkUser=$GLOBALS['session']->user->id;
 // debug
 api_dump($_REQUEST,"_REQUEST");
 api_dump($user_obj,"user_obj");
 api_dump($user_qobj,"user_qobj");
 // delete group
 $GLOBALS['database']->queryExecute("DELETE FROM `framework_users_join_groups` WHERE `fkUser`='".$user_obj->id."' AND `fkGroup`='".$_REQUEST['idGroup']."'");
 // update user
 $GLOBALS['database']->queryUpdate("framework_users",$user_qobj);
 // redirect
 api_alerts_add(api_text("settings_alert_userGroupRemoved"),"warning");
 api_redirect("?mod=framework&scr=users_view&idUser=".$user_obj->id);
}

/**
 * Group Save
 */
function group_save(){
 // build group objects
 $group=new stdClass();
 // acquire variables
 $group->id=$_REQUEST['idGroup'];
 $group->fkGroup=$_REQUEST['fkGroup'];
 $group->name=$_REQUEST['name'];
 $group->description=$_REQUEST['description'];
 $group->updTimestamp=time();
 $group->updFkUser=$GLOBALS['session']->user->id;
 // debug
 api_dump($group);
 // check group
 if($group->id){
  // update user
  $GLOBALS['database']->queryUpdate("framework_groups",$group);
  api_alerts_add(api_text("settings_alert_groupUpdated"),"success");
 }else{
  // update user
  $GLOBALS['database']->queryInsert("framework_groups",$group);
  api_alerts_add(api_text("settings_alert_groupCreated"),"success");
 }
 // redirect
 api_redirect("?mod=framework&scr=groups_list");
}

/**
 * Sessions Terminate
 */
function sessions_terminate(){
 $idSession=$_REQUEST['idSession'];
 if(!$idSession){api_alerts_add(api_text("settings_alert_sessionNotFound"),"danger");api_redirect("?mod=framework&scr=sessions_list");}
 // delete session
 $GLOBALS['database']->queryExecute("DELETE FROM `framework_sessions` WHERE `id`='".$idSession."'");
 // redirect
 api_alerts_add(api_text("settings_alert_sessionTerminated"),"warning");
 api_redirect("?mod=framework&scr=sessions_list");
}
/**
 * Sessions Terminate All
 */
function sessions_terminate_all(){
 // delete all sessions
 $GLOBALS['database']->queryExecute("DELETE FROM `framework_sessions`");
 // redirect
 api_alerts_add(api_text("settings_alert_sessionTerminatedAll"),"warning");
 api_redirect(DIR."index.php");
}

/**
 * Module Add
 */
function module_add(){
 // disabled for localhost and 127.0.0.1 /** @todo verificare se serve */
 //if(in_array($_SERVER['HTTP_HOST'],array("localhost","127.0.0.1"))){api_alerts_add(api_text("settings_alert_moduleUpdatesGitLocalhost"),"danger");api_redirect("?mod=framework&scr=modules_list");}
 // acquire variables
 $r_url=$_REQUEST['url'];
 $r_method=$_REQUEST['method'];
 // check url
 if(!in_array(substr(strtolower($r_url),0,7),array("http://","https:/"))){api_alerts_add(api_text("settings_alert_moduleAddErrorUrl"),"danger");api_redirect("?mod=framework&scr=modules_list");}
 if(substr(strtolower($r_url),-3)!=$r_method){api_alerts_add(api_text("settings_alert_moduleAddErrorFormat"),"danger");api_redirect("?mod=framework&scr=modules_list");}
 // make and check directory
 $directory="@todo";
 api_dump("verifico se esiste la directory ROOT/modules/{directory} se esiste blocco");

 // debug
 api_dump($_REQUEST);
 // git method
 if($r_method=="git"){
  api_dump("eseguo il comando: cd ".ROOT."modules/ ; pwd ; git clone ".$r_url." ./".$directory." : chmod 755 -R ./".$directory);
 }

 // zip method
 if($r_method=="zip"){
  api_dump("scarico lo zip nella cartella ROOT/tmp");
  api_dump("verifico se esiste in la cartella ROOT/tmp/module_setup se esiste la cancello");
  api_dump("creo la cartella ROOT/tmp/module_setup");
  api_dump("decomprimo il modulo nella cartella ROOT/tmp/module_setup");
  api_dump("leggo il file module.inc.php per il {nome-del-modulo}");
  /*api_dump("verifico se esiste la cartella ROOT/module/{nome-del-modulo} se esiste la cancello");*/
  api_dump("creo la cartella ROOT/module/{nome-del-modulo}");
  api_dump("copia il contenuto della cartella ROOT/tmp/module_setup in ROOT/module/{nome-del-modulo}");
  api_dump("imposto i permessi di ROOT/module/{nome-del-modulo} ricorsivi a 755");
  api_dump("elimino la cartella ROOT/tmp/module_setup");
 }

 // alert
 api_alerts_add(api_text("settings_alert_moduleAdded"),"success");

 // redirect
 api_redirect("?mod=framework&scr=modules_list");
}
/**
 * Module Enable
 *
 * param boolean $enable Enable status
 */
function module_enable($enable){
 // get objects
 $module_obj=new Module($_REQUEST['module']);
 // check objects
 if(!$module_obj->module){api_alerts_add(api_text("settings_alert_moduleNotFound"),"danger");api_redirect("?mod=framework&scr=modules_list");}
 // build module query object
 $module_qobj=new stdClass();
 $module_qobj->module=$module_obj->module;
 $module_qobj->enabled=($enable?1:0);
 $module_qobj->updTimestamp=time();
 $module_qobj->updFkUser=$GLOBALS['session']->user->id;
 // debug
 api_dump($module_qobj,"module query object");
 // update module
 $GLOBALS['database']->queryUpdate("framework_modules",$module_qobj,"module");
 // alert
 if($enable){api_alerts_add(api_text("settings_alert_moduleEnabled"),"success");}
 else{api_alerts_add(api_text("settings_alert_moduleDisabled"),"warning");}
 // redirect
 api_redirect("?mod=framework&scr=modules_view&module=".$module_obj->module);
}
/**
 * Module Update Source
 */
function module_update_source(){
 // disabled for localhost and 127.0.0.1
 if(in_array($_SERVER['HTTP_HOST'],array("localhost","127.0.0.1"))){api_alerts_add(api_text("settings_alert_moduleUpdateGitLocalhost"),"danger");api_redirect("?mod=framework&scr=modules_list");}
 // get objects
 $module_obj=new Module($_REQUEST['module']);
 // check objects
 if(!$module_obj->module){api_alerts_add(api_text("settings_alert_moduleNotFound"),"danger");api_redirect("?mod=framework&scr=modules_list");}
 /** @todo cycle all selected modules (multiselect in table) */
 // exec shell commands
 $shell_output=exec('whoami')."@".exec('hostname').":".shell_exec("cd ".$module_obj->source_path." ; pwd ; git stash ; git stash clear ; git pull ; chmod 755 -R ./");
 // debug
 api_dump($shell_output);
 // alert
 if(is_int(strpos(strtolower($shell_output),"up-to-date"))){api_alerts_add(api_text("settings_alert_moduleUpdateSourceAlready"),"success");}
 elseif(is_int(strpos(strtolower($shell_output),"abort"))){api_alerts_add(api_text("settings_alert_moduleUpdateSourceAborted"),"danger");}
 else{api_alerts_add(api_text("settings_alert_moduleUpdateSourceUpdated"),"warning");}
 // redirect
 api_redirect("?mod=framework&scr=modules_list");
}
/**
 * Module Updates Database
 */
function module_update_database(){
 // disabled for localhost and 127.0.0.1
 if(in_array($_SERVER['HTTP_HOST'],array("localhost","127.0.0.1"))){api_alerts_add(api_text("settings_alert_moduleUpdateGitLocalhost"),"danger");api_redirect("?mod=framework&scr=modules_list");}
 /** @todo execute .sql file and update version in database */
 // alert
 api_alerts_add(api_text("settings_alert_moduleUpdateDatabaseUpdated"),"success");
 // redirect
 api_redirect("?mod=framework&scr=modules_list");
}
/**
 * Module Authorizations Group Add
 */
function module_authorizations_group_add(){
 // get objects
 $module_obj=new Module($_REQUEST['module']);
 // check objects
 if(!$module_obj->module){api_alerts_add(api_text("settings_alert_moduleNotFound"),"danger");api_redirect("?mod=framework&scr=modules_list");}
 // acquire variables
 $r_fkGroup=$_REQUEST['fkGroup'];
 $r_fkAuthorizations_array=$_REQUEST['fkAuthorizations'];
 // debug
 api_dump($_REQUEST);
 // check parameters
 if(!$r_fkGroup){api_alerts_add(api_text("settings_alert_moduleError"),"danger");api_redirect("?mod=framework&scr=modules_view&module=".$module_obj->module);}
 if(!is_array($r_fkAuthorizations_array)){$r_fkAuthorizations_array=array();}
 // remove old group authorization
 $GLOBALS['database']->queryExecute("DELETE FROM `framework_modules_authorizations_join_groups` WHERE `fkGroup`='".$r_fkGroup."'");
 // cycle all selected authorizations
 foreach($r_fkAuthorizations_array as $fkAuthorization){
  // build user join group query object
  $authorization_join_group_qobj=new stdClass();
  $authorization_join_group_qobj->fkAuthorization=$fkAuthorization;
  $authorization_join_group_qobj->fkGroup=$r_fkGroup;
  // debug
  api_dump($authorization_join_group_qobj);
  // insert group
  $GLOBALS['database']->queryInsert("framework_modules_authorizations_join_groups",$authorization_join_group_qobj);
 }
 // build module query object
 $module_qobj=new stdClass();
 $module_qobj->module=$module_obj->module;
 $module_qobj->updTimestamp=time();
 $module_qobj->updFkUser=$GLOBALS['session']->user->id;
 // debug
 api_dump($module_qobj);
 // update module
 $GLOBALS['database']->queryUpdate("framework_modules",$module_qobj,"module");
 // alert
 api_alerts_add(api_text("settings_alert_moduleAuthorizationGroupAdded"),"success");
 // redirect
 api_redirect("?mod=framework&scr=modules_view&module=".$module_obj->module);
}
/**
 * Module Authorizations Group Remove
 */
function module_authorizations_group_remove(){
 // get objects
 $module_obj=new Module($_REQUEST['module']);
 // check objects
 if(!$module_obj->module){api_alerts_add(api_text("settings_alert_moduleNotFound"),"danger");api_redirect("?mod=framework&scr=modules_list");}
 // acquire variables
 $r_fkAuthorization=$_REQUEST['fkAuthorization'];
 $r_fkGroup=$_REQUEST['fkGroup'];
 // debug
 api_dump($_REQUEST);
 // check parameters
 if(!$r_fkAuthorization || !$r_fkGroup){api_alerts_add(api_text("settings_alert_moduleError"),"danger");api_redirect("?mod=framework&scr=modules_view&module=".$module_obj->module);}
 // remove group authorization
 $GLOBALS['database']->queryExecute("DELETE FROM `framework_modules_authorizations_join_groups` WHERE `fkAuthorization`='".$r_fkAuthorization."' AND `fkGroup`='".$r_fkGroup."'");
 // build module query object
 $module_qobj=new stdClass();
 $module_qobj->module=$module_obj->module;
 $module_qobj->updTimestamp=time();
 $module_qobj->updFkUser=$GLOBALS['session']->user->id;
 // debug
 api_dump($module_qobj);
 // update module
 $GLOBALS['database']->queryUpdate("framework_modules",$module_qobj,"module");
 // alert
 api_alerts_add(api_text("settings_alert_moduleAuthorizationGroupRemoved"),"warning");
 // redirect
 api_redirect("?mod=framework&scr=modules_view&module=".$module_obj->module);
}
/**
 * Module Authorizations Reset
 */
function module_authorizations_reset(){
 // get objects
 $module_obj=new Module($_REQUEST['module']);
 // check objects
 if(!$module_obj->module){api_alerts_add(api_text("settings_alert_moduleNotFound"),"danger");api_redirect("?mod=framework&scr=modules_list");}
 // debug
 api_dump($_REQUEST);
 // cycle all authorizations
 foreach($module_obj->authorizations_array as $authorization){
  // remove authorization
  $GLOBALS['database']->queryExecute("DELETE FROM `framework_modules_authorizations_join_groups` WHERE `fkAuthorization`='".$authorization->id."'");
 }
 // build module query object
 $module_qobj=new stdClass();
 $module_qobj->module=$module_obj->module;
 $module_qobj->updTimestamp=time();
 $module_qobj->updFkUser=$GLOBALS['session']->user->id;
 // debug
 api_dump($module_qobj);
 // update module
 $GLOBALS['database']->queryUpdate("framework_modules",$module_qobj,"module");
 // alert
 api_alerts_add(api_text("settings_alert_moduleAuthorizationResetted"),"warning");
 // redirect
 api_redirect("?mod=framework&scr=modules_view&module=".$module_obj->module);
}

?>