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

  /** @todoissimo check authorization in all submits function */

  // own
  case "own_profile_update":own_profile_update();break;
  case "own_password_update":own_password_update();break;
  case "own_avatar_remove":own_avatar_remove();break;


  // settings
  case "settings_save":settings_save();break;
  case "settings_logo_remove":settings_logo_remove();break;


  // menus
  case "menu_save":menu_save();break;
  case "menu_move_left":menu_move("left");break;
  case "menu_move_up":menu_move("up");break;
  case "menu_move_down":menu_move("down");break;
  // modules
  case "module_add":module_add();break;
  case "module_enable":module_enable(true);break;
  case "module_disable":module_enable(false);break;
  case "module_initialize":module_initialize();break;
  case "module_setup":module_setup();break;
  case "module_update_source":module_update_source();break;
  case "module_update_database":module_update_database();break;
  case "module_authorizations_group_add":module_authorizations_group_add();break;
  case "module_authorizations_group_remove":module_authorizations_group_remove();break;
  case "module_authorizations_reset":module_authorizations_reset();break;

  // users old
  case "user_login":user_login();break;
  case "user_logout":user_logout();break;
  case "user_recovery":user_recovery();break;
  /** @todo ^ check */

  // users
  case "user_add":user_add();break;
  case "user_edit":user_edit();break;
  case "user_enable":user_enabled(true);break;
  case "user_disable":user_enabled(false);break;
  case "user_delete":user_deleted(true);break;
  case "user_undelete":user_deleted(false);break;
  case "user_group_add":user_group_add();break;
  case "user_group_remove":user_group_remove();break;
  case "user_group_mainize":user_group_mainize();break;
  case "user_parameter_save":user_parameter_save();break;


  // groups
  case "group_save":group_save();break;
  /** @todo delete */
  /** @todo undelete */


  // sessions
  case "sessions_terminate":sessions_terminate();break;
  case "sessions_terminate_all":sessions_terminate_all();break;

  // mails
  case "mail_save":mail_save();break;
  case "mail_retry":mail_retry();break;
  case "mail_remove":mail_remove();break;

  // attachments
  case "attachment_save":attachment_save();break;
  case "attachment_delete":attachment_delete(true);break;
  case "attachment_undelete":attachment_delete(false);break;
  case "attachment_remove":attachment_remove();break;

  // default
  default:
   api_alerts_add(api_text("alert_submitFunctionNotFound",array(MODULE,SCRIPT,ACTION)),"danger");
   api_redirect("?mod=".MODULE);
 }

 /**
  * Own Profile Update
  */
 function own_profile_update(){
  // build user query objects
  $user_qobj=new stdClass();
  $user_qobj->id=$GLOBALS['session']->user->id;
  // acquire variables
  $user_qobj->firstname=$_REQUEST['firstname'];
  $user_qobj->lastname=$_REQUEST['lastname'];
  $user_qobj->localization=$_REQUEST['localization'];
  $user_qobj->timezone=$_REQUEST['timezone'];
  $user_qobj->gender=$_REQUEST['gender'];
  $user_qobj->birthday=$_REQUEST['birthday'];
  $user_qobj->theme=$_REQUEST['theme'];
  $user_qobj->updTimestamp=time();
  $user_qobj->updFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($user_qobj);
  // update user
  $GLOBALS['database']->queryUpdate("framework__users",$user_qobj);
  // upload avatar
  if(intval($_FILES['avatar']['size'])>0 && $_FILES['avatar']['error']==UPLOAD_ERR_OK){
   if(file_exists(DIR."uploads/framework/users/avatar_".$user_qobj->id.".jpg")){unlink(DIR."uploads/framework/users/avatar_".$user_qobj->id.".jpg");}
   if(is_uploaded_file($_FILES['avatar']['tmp_name'])){move_uploaded_file($_FILES['avatar']['tmp_name'],DIR."uploads/framework/users/avatar_".$user_qobj->id.".jpg");}
  }
  // redirect
  api_alerts_add(api_text("framework_alert_ownProfileUpdated"),"success");
  api_redirect("?mod=".MODULE."&scr=own_profile");
 }
 /**
  * Own Password Update
  */
 function own_password_update(){
  // retrieve user object
  $user_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__users` WHERE `id`='".$GLOBALS['session']->user->id."'");
  // check
  if(!$user_obj->id){api_alerts_add(api_text("framework_alert_userNotFound"),"danger");api_redirect(PATH."index.php");}
  // acquire variables
  $r_password=$_REQUEST['password'];
  $r_password_new=$_REQUEST['password_new'];
  $r_password_confirm=$_REQUEST['password_confirm'];
  // check old password
  if(md5($r_password)!==$user_obj->password){api_alerts_add(api_text("framework_alert_ownPasswordIncorrect"),"danger");api_redirect("?mod=".MODULE."&scr=own_password");}
  // check new password
  if($r_password_new!==$r_password_confirm){api_alerts_add(api_text("framework_alert_ownPasswordNotMatch"),"danger");api_redirect("?mod=".MODULE."&scr=own_password");}
  if(strlen($r_password_new)<8){api_alerts_add(api_text("framework_alert_ownPasswordWeak"),"danger");api_redirect("?mod=".MODULE."&scr=own_password");}
  // check if new password is equal to oldest password
  if(md5($r_password_new)===$user_obj->password){api_alerts_add(api_text("framework_alert_ownPasswordOldest"),"danger");api_redirect("?mod=".MODULE."&scr=own_password");}
  // build user objects
  $user=new stdClass();
  $user->id=$user_obj->id;
  $user->password=md5($r_password_new);
  $user->pwdTimestamp=time();
  // debug
  api_dump($user);
  // insert user to database
  $GLOBALS['database']->queryUpdate("framework__users",$user);
  // redirect
  api_alerts_add(api_text("framework_alert_ownPasswordUpdated"),"success");
  api_redirect("?mod=".MODULE."&scr=own_profile");
 }
 /**
  * Own Avatar Remove
  */
 function own_avatar_remove(){
  // remove avatar if exist
  if(file_exists(DIR."uploads/framework/users/avatar_".$GLOBALS['session']->user->id.".jpg")){unlink(DIR."uploads/framework/users/avatar_".$GLOBALS['session']->user->id.".jpg");}
  // redirect
  api_alerts_add(api_text("framework_alert_ownAvatarRemoved"),"warning");
  api_redirect("?mod=".MODULE."&scr=own_profile");
 }

 /**
  * Settings Save
  */
 function settings_save(){
  api_dump($_REQUEST);
  // check authorizations
  api_checkAuthorization("framework-settings_manage","dashboard");
  // acquire variables
  $r_tab=$_REQUEST['tab'];
  // definitions
  $settings_array=array();
  $availables_settings_array=array(
   /* generals */
   "maintenance","owner","title","show",
   /* sessions */
   "sessions_authentication_method","sessions_multiple","sessions_idle_timeout","sessions_ldap_hostname",
   "sessions_ldap_dn","sessions_ldap_domain","sessions_ldap_userfield","sessions_ldap_groups","sessions_ldap_cache",
   /* mails */
   "mail_from_name","mail_from_address","mail_asynchronous","mail_method","mail_smtp_hostname","mail_smtp_username",
   "mail_smtp_encryption",
   /* users */
   "users_password_expiration","users_level_max",
   /* tokens */
   "token_cron","token_gtag"
  );
  // cycle all form fields and set availables
  foreach($_REQUEST as $setting=>$value){if(in_array($setting,$availables_settings_array)){$settings_array[$setting]=$value;}}
  // mail smtp password (save password only if change)
  if(isset($settings_array['mail_smtp_username'])){if($settings_array['mail_smtp_username']){if($_REQUEST['mail_smtp_password']){$settings_array['mail_smtp_password']=$_REQUEST['mail_smtp_password'];}}else{$settings_array['mail_smtp_password']=null;}}
  // debug
  api_dump($settings_array);
  // cycle all settings
  foreach($settings_array as $setting=>$value){
   // buil setting query
   $query="INSERT INTO `framework__settings` (`setting`,`value`) VALUES ('".$setting."','".$value."') ON DUPLICATE KEY UPDATE `setting`='".$setting."',`value`='".$value."'";
   // execute setting query
   $GLOBALS['database']->queryExecute($query);
   api_dump($query);
  }
  // downgrade user level out of limit
  if(isset($settings_array["users_level_max"])){$GLOBALS['database']->queryExecute("UPDATE `framework__users` SET `level`='".$settings_array["users_level_max"]."' WHERE `level`>'".$settings_array["users_level_max"]."'");}
  // upload logo
  if(intval($_FILES['logo']['size'])>0 && $_FILES['logo']['error']==UPLOAD_ERR_OK){
   if(file_exists(DIR."uploads/framework/logo.png")){unlink(DIR."uploads/framework/logo.png");}
   if(is_uploaded_file($_FILES['logo']['tmp_name'])){move_uploaded_file($_FILES['logo']['tmp_name'],DIR."uploads/framework/logo.png");}
  }
  // redirect
  api_alerts_add(api_text("framework_alert_settingsUpdated"),"success");
  api_redirect("?mod=".MODULE."&scr=settings_edit&tab=".$r_tab);
 }

 /**
  * Settings Logo Remove
  */
 function settings_logo_remove(){
  api_dump($_REQUEST);
  // check authorizations
  api_checkAuthorization("framework-settings_manage","dashboard");
  // acquire variables
  $r_tab=$_REQUEST['tab'];
  // remove logo if exist
  if(file_exists(DIR."uploads/framework/logo.png")){unlink(DIR."uploads/framework/logo.png");}
  // redirect
  api_alerts_add(api_text("framework_alert_settingsLogoRemoved"),"warning");
  api_redirect("?mod=".MODULE."&scr=settings_edit&tab=".$r_tab);
 }

 /**
  * Mail Save
  */
 function mail_save(){
  // debug
  api_dump($_REQUEST);
  // acquire variables
  $r_sender=addslashes($_REQUEST['sender']);
  $r_recipient=addslashes($_REQUEST['recipient']);
  $r_subject=addslashes($_REQUEST['subject']);
  $r_message=addslashes(nl2br($_REQUEST['message']));
  // save mail
  api_mail_save($r_subject,$r_message,$r_recipient,null,null,$r_sender);
  // make current uri array
  parse_str(parse_url($_SERVER['REQUEST_URI'])['query'],$uri_array);
  $uri_array['mod']=$uri_array['return_mod'];unset($uri_array['return_mod']);
  $uri_array['scr']=$uri_array['return_scr'];unset($uri_array['return_scr']);
  $uri_array['tab']=$uri_array['return_tab'];unset($uri_array['return_tab']);
  unset($uri_array['act']);
  // redirect
  if($uri_array['mod']){api_redirect("?".http_build_query($uri_array));}
  else{api_redirect("?mod=".MODULE."&scr=mails_list");}
 }

 /**
  * Menu Save
  */
 function menu_save(){
  // get menu object
  $menu_obj=new cMenu($_REQUEST['idMenu']);
  // acquire variables
  $r_fkMenu=$_REQUEST['fkMenu'];
  $r_typology=$_REQUEST['typology'];
  $r_icon=addslashes($_REQUEST['icon']);
  $r_label_localizations=$_REQUEST['label_localizations'];
  $r_title_localizations=$_REQUEST['title_localizations'];
  $r_url=addslashes($_REQUEST['url']);
  $r_module=addslashes($_REQUEST['module']);
  $r_script=addslashes($_REQUEST['script']);
  $r_tab=addslashes($_REQUEST['tab']);
  $r_action=addslashes($_REQUEST['action']);
  $r_authorization=addslashes($_REQUEST['authorization']);
  $r_target=addslashes($_REQUEST['target']);
  // check variables
  if(!$r_url){$r_url="#";}
  // build menu query objects
  $menu_qobj=new stdClass();
  $menu_qobj->id=$menu_obj->id;
  $menu_qobj->fkMenu=$r_fkMenu;
  $menu_qobj->icon=$r_icon;
  $menu_qobj->label_localizations=$r_label_localizations;
  $menu_qobj->title_localizations=$r_title_localizations;
  $menu_qobj->target=$r_target;
  $menu_qobj->authorization=$r_authorization;
  // switch menu typology
  switch($r_typology){
   // link
   case "link":
    $menu_qobj->url=$r_url;
    $menu_qobj->module=null;
    $menu_qobj->script=null;
    $menu_qobj->tab=null;
    $menu_qobj->action=null;
    break;
   // module
   case "module":
    $menu_qobj->url=null;
    $menu_qobj->module=$r_module;
    $menu_qobj->script=$r_script;
    $menu_qobj->tab=$r_tab;
    $menu_qobj->action=$r_action;
    break;
  }
  // get last order of new fkMenu
  if(!$menu_obj->id || $menu_qobj->fkMenu<>$menu_obj->fkMenu){
   if($menu_qobj->fkMenu){$order_query_where="`fkMenu`='".$menu_qobj->fkMenu."'";}else{$order_query_where="`fkMenu` IS null";}
   api_dump($order_query="SELECT `order` FROM `framework__menus` WHERE ".$order_query_where." ORDER BY `order` DESC","order_query");
   $v_order=$GLOBALS['database']->queryUniqueValue($order_query);
   $menu_qobj->order=($v_order+1);
  }
  // check menu
  if($menu_obj->id){
   // update menu
   $menu_qobj->updTimestamp=time();
   $menu_qobj->updFkUser=$GLOBALS['session']->user->id;
   // debug
   api_dump($menu_qobj,"menu query object");
   // execute query
   $GLOBALS['database']->queryUpdate("framework__menus",$menu_qobj);
   // check if parent menu is changed
   if($menu_qobj->fkMenu<>$menu_obj->fkMenu){
    // rebase other menus
    if($menu_obj->fkMenu){$rebase_query_where="`fkMenu`='".$menu_obj->fkMenu."'";}else{$rebase_query_where="`fkMenu` IS null";}
    api_dump($rebase_query="UPDATE `framework__menus` SET `order`=`order`-'1' WHERE `order`>'".$menu_obj->order."' AND ".$rebase_query_where." ORDER BY `order` ASC");
    $GLOBALS['database']->queryExecute($rebase_query);
   }
   api_alerts_add(api_text("framework_alert_menuUpdated"),"success");
  }else{
   // insert menu
   $menu_qobj->addTimestamp=time();
   $menu_qobj->addFkUser=$GLOBALS['session']->user->id;
   // debug
   api_dump($menu_qobj,"menu query object");
   // execute query
   $GLOBALS['database']->queryInsert("framework__menus",$menu_qobj);
   api_alerts_add(api_text("framework_alert_menuCreated"),"success");
  }
  // redirect
  api_redirect("?mod=".MODULE."&scr=menus_list&idMenu=".$menu_obj->id);
 }
 /**
  * Menu Move
  *
  * @param string direction
  */
 function menu_move($direction){
  // get objects
  $menu_obj=new cMenu($_REQUEST['idMenu']);
  // check objects
  if(!$menu_obj->id){api_alerts_add(api_text("framework_alert_menuNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=menus_list");}
  // check parameters
  if(!in_array(strtolower($direction),array("left","up","down"))){api_alerts_add(api_text("framework_alert_menuError"),"warning");api_redirect("?mod=".MODULE."&scr=menus_list&idMenu=".$menu_obj->id);}
  // build menu query objects
  $menu_qobj=new stdClass();
  $menu_qobj->id=$menu_obj->id;
  //switch direction
  switch(strtolower($direction)){
   // left -> fkGroup -1
   case "left":
    // check for fkGroup
    if(!$menu_obj->fkMenu){api_alerts_add(api_text("framework_alert_menuError"),"warning");api_redirect("?mod=".MODULE."&scr=menus_list&idMenu=".$menu_obj->id);}
    // get fkMenu of menu fkMenu
    $fkMenu_obj=new cMenu($menu_obj->fkMenu);
    $menu_qobj->fkMenu=$fkMenu_obj->fkMenu;
    // set last order of new fkMenu
    if($menu_qobj->fkMenu){$order_query_where="`fkMenu`='".$menu_qobj->fkMenu."'";}else{$order_query_where="`fkMenu` IS null";}
    api_dump($order_query="SELECT `order` FROM `framework__menus` WHERE ".$order_query_where." ORDER BY `order` DESC","order_query");
    $v_order=$GLOBALS['database']->queryUniqueValue($order_query);
    $menu_qobj->order=($v_order+1);
    // update menu
    $GLOBALS['database']->queryUpdate("framework__menus",$menu_qobj);
    // rebase other menus
    if($menu_obj->fkMenu){$rebase_query_where="`fkMenu`='".$menu_obj->fkMenu."'";}else{$rebase_query_where="`fkMenu` IS null";}
    api_dump($rebase_query="UPDATE `framework__menus` SET `order`=`order`-'1' WHERE `order`>'".$menu_obj->order."' AND ".$rebase_query_where." ORDER BY `order` ASC","rebase_query");
    $GLOBALS['database']->queryExecute($rebase_query);
    break;
   // up -> order -1
   case "up":
    // set previous order
    $menu_qobj->order=$menu_obj->order-1;
    // check for order
    if($menu_qobj->order<1){api_alerts_add(api_text("framework_alert_menuError"),"warning");api_redirect("?mod=".MODULE."&scr=menus_list&idMenu=".$menu_obj->id);}
    // update menu
    $GLOBALS['database']->queryUpdate("framework__menus",$menu_qobj);
    // rebase other menus
    if($menu_obj->fkMenu){$rebase_query_where="`fkMenu`='".$menu_obj->fkMenu."'";}else{$rebase_query_where="`fkMenu` IS null";}
    api_dump($rebase_query="UPDATE `framework__menus` SET `order`=`order`+'1' WHERE `order`<'".$menu_obj->order."' AND `order`>='".$menu_qobj->order."' AND `order`<>'0' AND `id`!='".$menu_obj->id."' AND ".$rebase_query_where,"rebase_query");
    $GLOBALS['database']->queryExecute($rebase_query);
    break;
   // down -> order +1
   case "down":
    // set following order
    $menu_qobj->order=$menu_obj->order+1;
    // update menu
    $GLOBALS['database']->queryUpdate("framework__menus",$menu_qobj);
    // rebase other menus
    if($menu_obj->fkMenu){$rebase_query_where="`fkMenu`='".$menu_obj->fkMenu."'";}else{$rebase_query_where="`fkMenu` IS null";}
    api_dump($rebase_query="UPDATE `framework__menus` SET `order`=`order`-'1' WHERE `order`>'".$menu_obj->order."' AND `order`<='".$menu_qobj->order."' AND `order`<>'0' AND `id`!='".$menu_obj->id."' AND ".$rebase_query_where,"rebase_query");
    $GLOBALS['database']->queryExecute($rebase_query);
    break;
  }
  // debug
  api_dump($_REQUEST,"_REQUEST");
  api_dump($direction,"direction");
  api_dump($menu_obj,"menu_obj");
  api_dump($menu_qobj,"menu_qobj");
  // redirect
  api_redirect("?mod=".MODULE."&scr=menus_list&idMenu=".$menu_obj->id);
 }

 /**
  * Module Add
  */
 function module_add(){
  // disabled for localhost
  if(in_array($_SERVER['HTTP_HOST'],array("localhost","127.0.0.1"))){api_alerts_add(api_text("framework_alert_moduleErrorLocalhost"),"danger");api_redirect("?mod=".MODULE."&scr=modules_list");}
  // acquire variables
  $r_url=$_REQUEST['url'];
  $r_directory=$_REQUEST['directory'];
  $r_method=$_REQUEST['method'];
  // debug
  api_dump($_REQUEST,"_REQUEST");
  // check url
  if(!in_array(substr(strtolower($r_url),0,7),array("http://","https:/"))){api_alerts_add(api_text("framework_alert_moduleAddErrorUrl"),"danger");api_redirect("?mod=".MODULE."&scr=modules_add");}
  if(substr(strtolower($r_url),-3)!=$r_method){api_alerts_add(api_text("framework_alert_moduleAddErrorFormat"),"danger");api_redirect("?mod=".MODULE."&scr=modules_add");}
  // check directory
  if(!$r_directory || is_dir(DIR."modules/".$r_directory)){api_alerts_add(api_text("framework_alert_moduleAddErrorDirectory"),"danger");api_redirect("?mod=".MODULE."&scr=modules_add");}
  // git method
  if($r_method=="git"){
   // exec shell commands
   $shell_output=exec('whoami')."@".exec('hostname').":".shell_exec("cd ".DIR."modules/ ; pwd ; git clone ".$r_url." ./".$r_directory." ; chmod 755 -R ./".$r_directory);
   // debug
   api_dump($shell_output);
  }

  // zip method
  if($r_method=="zip"){
   api_dump("scarico lo zip nella cartella DIR/tmp");
   api_dump("verifico se esiste in la cartella DIR/tmp/module_setup se esiste la cancello");
   api_dump("creo la cartella DIR/tmp/module_setup");
   api_dump("decomprimo il modulo nella cartella DIR/tmp/module_setup");
   api_dump("leggo il file module.inc.php per il {nome-del-modulo}");
   api_dump("creo la cartella DIR/module/{nome-del-modulo}");
   api_dump("copia il contenuto della cartella DIR/tmp/module_setup in DIR/module/{nome-del-modulo}");
   api_dump("imposto i permessi di DIR/module/{nome-del-modulo} ricorsivi a 755");
   api_dump("elimino la cartella DIR/tmp/module_setup");
  }

  // check for module.inc.php
  if(!file_exists(DIR."modules/".$r_directory."/module.inc.php")){api_alerts_add(api_text("framework_alert_moduleAddError"),"danger");api_redirect("?mod=".MODULE."&scr=modules_add");}
  // include module file
  include(DIR."modules/".$r_directory."/module.inc.php");
  // build module query object
  $module_qobj=new stdClass();
  $module_qobj->id=$module_name;
  $module_qobj->version="0";
  $module_qobj->enabled=0;
  $module_qobj->addTimestamp=time();
  $module_qobj->addFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($module_qobj,"module query object");
  // check for module
  if(!$module_qobj->id){api_alerts_add(api_text("framework_alert_moduleAddError"),"danger");api_redirect("?mod=".MODULE."&scr=modules_add");}
  // update module
  $GLOBALS['database']->queryInsert("framework__modules",$module_qobj);
  // alert
  api_alerts_add(api_text("framework_alert_moduleAdded"),"success");
  // redirect
  api_redirect("?mod=".MODULE."&scr=modules_list");
 }
 /**
  * Module Initialize
  */
 function module_initialize(){
  // debug
  api_dump($_REQUEST,"$_REQUEST");
  // get variables
  $module_directory=$_REQUEST['idModule'];
  // check objects
  if(!file_exists(DIR."modules/".$module_directory."/module.inc.php")){api_alerts_add(api_text("framework_alert_moduleNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=modules_list");}
  // include module file
  include(DIR."modules/".$module_directory."/module.inc.php");
  // build module query object
  $module_qobj=new stdClass();
  $module_qobj->id=$module_name;
  $module_qobj->version="0";
  $module_qobj->enabled=0;
  $module_qobj->addTimestamp=time();
  $module_qobj->addFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($module_qobj,"module query object");
  // check for module
  if(!$module_qobj->id){api_alerts_add(api_text("framework_alert_moduleAddError"),"danger");api_redirect("?mod=".MODULE."&scr=modules_add");}
  // update module
  $GLOBALS['database']->queryInsert("framework__modules",$module_qobj);
  // alert
  api_alerts_add(api_text("framework_alert_moduleAdded"),"success");
  // redirect
  api_redirect("?mod=".MODULE."&scr=modules_list");
 }
 /**
  * Module Setup
  */
 function module_setup(){
  // get objects
  $module_obj=new cModule($_REQUEST['idModule']);
  // check objects
  if(!$module_obj->id){api_alerts_add(api_text("framework_alert_moduleNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=modules_list");}
  // debug
  api_dump($module_obj,"module object");
  // load setup dump
  if(!file_exists($module_obj->source_path."queries/setup.sql")){api_alerts_add(api_text("framework_alert_moduleSetupErrorDump"),"danger");api_redirect("?mod=".MODULE."&scr=modules_list");}
  // execute setup dump
  api_sqlDump_import(file($module_obj->source_path."queries/setup.sql"));
  // build module query object
  $module_qobj=new stdClass();
  $module_qobj->id=$module_obj->id;
  $module_qobj->version="0.0.1";
  $module_qobj->updTimestamp=time();
  $module_qobj->updFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($module_qobj,"module query object");
  // update module
  $GLOBALS['database']->queryUpdate("framework__modules",$module_qobj);
  // alert
  api_alerts_add(api_text("framework_alert_moduleEnabled"),"success");
  // redirect
  api_redirect("?mod=".MODULE."&scr=modules_list");
 }
 /**
  * Module Update Source
  */
 function module_update_source(){
  // disabled for localhost and 127.0.0.1
  if(in_array($_SERVER['HTTP_HOST'],array("localhost","127.0.0.1"))){api_alerts_add(api_text("framework_alert_moduleErrorLocalhost"),"danger");api_redirect("?mod=".MODULE."&scr=modules_list");}
  // get objects
  $module_obj=new cModule($_REQUEST['idModule']);
  // check objects
  if(!$module_obj->id){api_alerts_add(api_text("framework_alert_moduleNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=modules_list");}
  /** @tip cycle all selected modules (if multiselect in table) */
  // exec shell commands
  $shell_output=exec('whoami')."@".exec('hostname').":".shell_exec("cd ".$module_obj->source_path." ; pwd ; git stash 2>&1 ; git stash clear ; git pull 2>&1 ; chmod 755 -R ./");
  // debug
  api_dump($shell_output);
  // alert
  if(is_int(strpos(strtolower($shell_output),"up-to-date"))){api_alerts_add(api_text("framework_alert_moduleUpdateSourceAlready"),"success");}
  elseif(is_int(strpos(strtolower($shell_output),"abort"))){api_alerts_add(api_text("framework_alert_moduleUpdateSourceAborted"),"danger");}
  else{api_alerts_add(api_text("framework_alert_moduleUpdateSourceUpdated"),"warning");}
  // redirect
  api_redirect("?mod=".MODULE."&scr=modules_list");
 }
 /**
  * Module Updates Database
  */
 function module_update_database(){
  // disabled for localhost and 127.0.0.1
  if(in_array($_SERVER['HTTP_HOST'],array("localhost","127.0.0.1"))){$localhost_update=true;} /** @todo vedere come fare se inibire le query o meno in localhost */
  if($localhost_update && !DEBUG){api_alerts_add(api_text("framework_alert_moduleErrorLocalhost"),"danger");api_redirect("?mod=".MODULE."&scr=modules_list");}
  // get objects
  $module_obj=new cModule($_REQUEST['idModule']);
  api_dump($module_obj->version,"version");
  api_dump($module_obj->source_version,"source_version");
   // explode current version
  $current_array=explode(".",$module_obj->version);
  $major=$current_array[0];
  $minor=$current_array[1];
  $hotfix=$current_array[2];
  // cycle all possibile version update
  while($major<=99){
   while($minor<=99){
    while($hotfix<=999){
     // make update version
     $update_version=$major.".".$minor.".".$hotfix;
     // check if version is oldest than source version
     if(api_check_version($update_version,$module_obj->source_version)<0){break 3;}
     // check for update sql dump
     if(file_exists($module_obj->source_path."queries/update_".$update_version.".sql")){
      // execute setup dump
      api_dump("Execute DUMP: update_".$update_version.".sql");
      api_sqlDump_import(file($module_obj->source_path."queries/update_".$update_version.".sql"));
     }
     // increment hotfix
     $hotfix++;
    }
    // reset hotfix
    $hotfix=0;
    // increment minor release
    $minor++;
   }
   // reset minor release
   $minor=0;
   // increment major release
   $major++;
  }
  // build module query object
  $module_qobj=new stdClass();
  $module_qobj->id=$module_obj->id;
  $module_qobj->version=$module_obj->source_version;
  $module_qobj->updTimestamp=time();
  $module_qobj->updFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($module_qobj);
  // update module
  $GLOBALS['database']->queryUpdate("framework__modules",$module_qobj);
  // redirect
  api_alerts_add(api_text("framework_alert_moduleUpdateDatabaseUpdated"),"success");
  api_redirect("?mod=".MODULE."&scr=modules_list");
 }
 /**
  * Module Enable
  *
  * param boolean $enable Enable status
  */
 function module_enable($enable){
  // get objects
  $module_obj=new cModule($_REQUEST['idModule']);
  // check objects
  if(!$module_obj->id){api_alerts_add(api_text("framework_alert_moduleNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=modules_list");}
  // build module query object
  $module_qobj=new stdClass();
  $module_qobj->id=$module_obj->id;
  $module_qobj->enabled=($enable?1:0);
  $module_qobj->updTimestamp=time();
  $module_qobj->updFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($module_qobj,"module query object");
  // update module
  $GLOBALS['database']->queryUpdate("framework__modules",$module_qobj);
  // alert
  if($enable){api_alerts_add(api_text("framework_alert_moduleEnabled"),"success");}
  else{api_alerts_add(api_text("framework_alert_moduleDisabled"),"warning");}
  // redirect
  api_redirect("?mod=".MODULE."&scr=modules_view&idModule=".$module_obj->id);
 }
 /**
  * Module Authorizations Group Add
  */
 function module_authorizations_group_add(){
  api_dump($_REQUEST);
  // get objects
  $module_obj=new cModule($_REQUEST['idModule']);
  // check objects
  if(!$module_obj->id){api_alerts_add(api_text("framework_alert_moduleNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=modules_list");}
  // acquire variables
  $r_fkGroup=$_REQUEST['fkGroup'];
  $r_level=$_REQUEST['level'];
  $r_fkAuthorizations_array=$_REQUEST['fkAuthorizations'];
  // check parameters
  if(!$r_fkGroup){api_alerts_add(api_text("framework_alert_moduleError"),"danger");api_redirect("?mod=".MODULE."&scr=modules_view&idModule=".$module_obj->id);}
  if(!is_array($r_fkAuthorizations_array)){$r_fkAuthorizations_array=array();}
  // cycle all module authorization
  foreach($module_obj->authorizations_array as $authorization){
   // remove old group authorization
   $GLOBALS['database']->queryExecute("DELETE FROM `framework__modules__authorizations__groups` WHERE `fkAuthorization`='".$authorization->id."' AND `fkGroup`='".$r_fkGroup."'");
  }
  // cycle all selected authorizations
  foreach($r_fkAuthorizations_array as $fkAuthorization){
   // build user join group query object
   $authorization_join_group_qobj=new stdClass();
   $authorization_join_group_qobj->fkAuthorization=$fkAuthorization;
   $authorization_join_group_qobj->fkGroup=$r_fkGroup;
   $authorization_join_group_qobj->level=$r_level;
   // debug
   api_dump($authorization_join_group_qobj);
   // remove previous group authorization (ridondante con sopra?)
   //$GLOBALS['database']->queryExecute("DELETE FROM `framework__modules__authorizations__groups` WHERE `fkAuthorization`='".$fkAuthorization."' AND `fkGroup`='".$r_fkGroup."'");
   // insert group
   $GLOBALS['database']->queryInsert("framework__modules__authorizations__groups",$authorization_join_group_qobj);
  }
  // build module query object
  $module_qobj=new stdClass();
  $module_qobj->id=$module_obj->id;
  $module_qobj->updTimestamp=time();
  $module_qobj->updFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($module_qobj);
  // update module
  $GLOBALS['database']->queryUpdate("framework__modules",$module_qobj);
  // alert
  api_alerts_add(api_text("framework_alert_moduleAuthorizationGroupAdded"),"success");
  // redirect
  api_redirect("?mod=".MODULE."&scr=modules_view&idModule=".$module_obj->id);
 }
 /**
  * Module Authorizations Group Remove
  */
 function module_authorizations_group_remove(){
  api_dump($_REQUEST);
  // get objects
  $module_obj=new cModule($_REQUEST['idModule']);
  // check objects
  if(!$module_obj->id){api_alerts_add(api_text("framework_alert_moduleNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=modules_list");}
  // acquire variables
  $r_fkAuthorization=$_REQUEST['fkAuthorization'];
  $r_fkGroup=$_REQUEST['fkGroup'];
  // check parameters
  if(!$r_fkAuthorization || !$r_fkGroup){api_alerts_add(api_text("framework_alert_moduleError"),"danger");api_redirect("?mod=".MODULE."&scr=modules_view&idModule=".$module_obj->id);}
  // remove group authorization
  $GLOBALS['database']->queryExecute("DELETE FROM `framework__modules__authorizations__groups` WHERE `fkAuthorization`='".$r_fkAuthorization."' AND `fkGroup`='".$r_fkGroup."'");
  // build module query object
  $module_qobj=new stdClass();
  $module_qobj->id=$module_obj->id;
  $module_qobj->updTimestamp=time();
  $module_qobj->updFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($module_qobj);
  // update module
  $GLOBALS['database']->queryUpdate("framework__modules",$module_qobj);
  // alert
  api_alerts_add(api_text("framework_alert_moduleAuthorizationGroupRemoved"),"warning");
  // redirect
  api_redirect("?mod=".MODULE."&scr=modules_view&idModule=".$module_obj->id);
 }
 /**
  * Module Authorizations Reset
  */
 function module_authorizations_reset(){
  // get objects
  $module_obj=new cModule($_REQUEST['idModule']);
  // check objects
  if(!$module_obj->id){api_alerts_add(api_text("framework_alert_moduleNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=modules_list");}
  // debug
  api_dump($_REQUEST);
  // cycle all authorizations
  foreach($module_obj->authorizations_array as $authorization){
   // remove authorization
   $GLOBALS['database']->queryExecute("DELETE FROM `framework__modules__authorizations__groups` WHERE `fkAuthorization`='".$authorization->id."'");
  }
  // build module query object
  $module_qobj=new stdClass();
  $module_qobj->id=$module_obj->id;
  $module_qobj->updTimestamp=time();
  $module_qobj->updFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($module_qobj);
  // update module
  $GLOBALS['database']->queryUpdate("framework__modules",$module_qobj);
  // alert
  api_alerts_add(api_text("framework_alert_moduleAuthorizationResetted"),"warning");
  // redirect
  api_redirect("?mod=".MODULE."&scr=modules_view&idModule=".$module_obj->id);
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
  $user_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__users` WHERE `mail`='".$username."'");
  // check for user object
  if(!$user_obj->id){return -1;}
  // check password
  if(md5($password)!==$user_obj->password){return -2;}
  // return user object
  return $user_obj->id;
 }
 /**
  * User Authentication LDAP
  *
  * @param string $username Username
  * @param string $password Password
  * @return integer Account User ID or Error Code
  *                 -1 User account was not found
  *                 -2 Binding error
  *                 -3 Groups error
  */
 function user_authentication_ldap($username,$password){
  // definitions
  $binded=false;
  /** @todo check ping or use ldap cache */
  // connect to ldap server
  $ldap=@ldap_connect($GLOBALS['settings']->sessions_ldap_hostname);
  // set ldap options
  @ldap_set_option($ldap,LDAP_OPT_PROTOCOL_VERSION,3);
  @ldap_set_option($ldap,LDAP_OPT_REFERRALS,0);
  // try to bind with specified credentials
  $bind=@ldap_bind($ldap,$username.$GLOBALS['settings']->sessions_ldap_domain,$password);
  // check for bind
  if(!$bind){return -2;}
  // Check ldap groups if defined
  if($GLOBALS['settings']->sessions_ldap_groups){
   // check presence in groups
   $filter="(".$GLOBALS['settings']->sessions_ldap_userfield."=".$username.")"; //
   $attr=array("memberof");
   $result=ldap_search($ldap,$GLOBALS['settings']->sessions_ldap_dn,$filter,$attr);
   $entries=ldap_get_entries($ldap, $result);
   // cycle all ldap memberof user group
   foreach($entries[0]['memberof'] as $groups){if(strpos($groups,$GLOBALS['settings']->sessions_ldap_groups)){$binded=true;}}
  }else{
   // or set binded to true
   $binded=true;
  }
  // disconnect from ldap
  @ldap_unbind($ldap);
  // check for binded value
  if(!$binded){return -3;}
  // retrieve user object
  $user_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__users` WHERE `username`='".$username."'");
  // check for user object
  if(!$user_obj->id){return -1;}
  // check for password caching
  if($GLOBALS['settings']->sessions_ldap_cache){
   // build user query objects
   $user_qobj=new stdClass();
   // acquire variables
   $user_qobj->id=$user_obj->id;
   $user_qobj->password=md5($password);
   $user_qobj->pwdTimestamp=time();
   // debug
   api_dump($user_qobj,"user_qobj");
   // update user
   $GLOBALS['database']->queryUpdate("framework__users",$user_qobj);
  }
  // return user object
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
  api_dump($GLOBALS['session'],"session");
  // switch authentication method
  switch($GLOBALS['settings']->sessions_authentication_method){
   case "ldap":
    // ldap authentication
    $authentication_result=user_authentication_ldap($r_username,$r_password);
    // check authentication
    if($authentication_result==-2){$authentication_result=user_authentication($r_username,$r_password);}
    break;
   default:
    // standard authentication
    $authentication_result=user_authentication($r_username,$r_password);
  }
  // debug authentication result
  api_dump($authentication_result,"authentication_result");
  // check authentication result
  if($authentication_result<1){api_alerts_add(api_text("alert_authenticationFailed"),"warning");api_redirect(PATH."login.php");}
  // build session
  $GLOBALS['session']->build($authentication_result);
  //
  api_dump($_SESSION["coordinator_session_id"],"session_id after");
  api_dump($GLOBALS['session'],"session after");
  // build user query objects
  $user_qobj=new stdClass();
  // acquire variables
  $user_qobj->id=$authentication_result;
  $user_qobj->lsaTimestamp=time();
  // debug
  api_dump($user_qobj,"user_qobj");
  // update user
  $GLOBALS['database']->queryUpdate("framework__users",$user_qobj);
  // redirect
  api_redirect(PATH."index.php");
 }
 /**
  * User Logout
  */
 function user_logout(){
  // destroy session
  $GLOBALS['session']->destroy();
  // redirect
  api_redirect(PATH."index.php");
 }
 /**
  * User Recovery   /** @todo rename in own ?
  */
 function user_recovery(){
  // debug
  api_dump($_REQUEST,"_REQUEST");
  // acquire variables
  $r_mail=$_REQUEST['mail'];
  $r_secret=$_REQUEST['secret'];
  // retrieve user object
  $user_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__users` WHERE `mail`='".$r_mail."'");
  // check user
  if(!$user_obj->id){
   api_alerts_add(api_text("framework_alert_userRecoveryNotFound"),"warning");
   api_redirect(PATH."login.php");
  }
  // remove all user sessions
  $GLOBALS['database']->queryExecute("DELETE FROM `framework__sessions` WHERE `fkUser`='".$user_obj->id."'");
  // check for secret
  if(!$r_secret){
   // generate new secret code and save into database
   $f_secret=md5(date("Y-m-d H:i:s").rand(1,99999));
   $GLOBALS['database']->queryExecute("UPDATE `framework__users` SET `secret`='".$f_secret."' WHERE `id`='".$user_obj->id."'");
   $recoveryLink=URL."index.php?mod=".MODULE."&scr=submit&act=user_recovery&mail=".$r_mail."&secret=".$f_secret;
   // send recovery link
   $mail_id=api_mail_save(api_text("framework_mail-user_recovery-subject",$GLOBALS['settings']->title),api_text("framework_mail-user_recovery-message",array($user_obj->firstname,$GLOBALS['settings']->title,$recoveryLink)),$user_obj->mail);
   // force mail if asynchronous
   if($GLOBALS['settings']->mail_asynchronous){api_mail_process($mail_id);}
   // redirect
   api_alerts_add(api_text("framework_alert_userRecoveryLinkSended"),"success");
   api_redirect(PATH."login.php");
  }else{
   // check secret code
   if($r_secret!==$user_obj->secret){
    api_alerts_add(api_text("framework_alert_userRecoverySecretError"),"warning");
    api_redirect(PATH."login.php?error=userRecoverySecretError");
   }
   // generate new password
   $v_password=substr(md5(date("Y-m-d H:i:s").rand(1,99999)),0,8);
   // update password and reset secret
   $GLOBALS['database']->queryExecute("UPDATE `framework__users` SET `password`='".md5($v_password)."',`secret`=null,`pwdTimestamp`=null WHERE `id`='".$user_obj->id."'");
   // send new password
   $mail_id=api_mail_save(api_text("framework_mail-user_recovery_password-subject",$GLOBALS['settings']->title),api_text("framework_mail-user_recovery_password-message",array($user_obj->firstname,$GLOBALS['settings']->title,URL,$v_password)),$user_obj->mail);
   // force mail if asynchronous
   if($GLOBALS['settings']->mail_asynchronous){api_mail_process($mail_id);}
   // redirect
   api_alerts_add(api_text("framework_alert_userRecoveryPasswordSended"),"success");
   api_redirect(PATH."login.php");
  }
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
  $user_obj->mail=trim($_REQUEST['mail']);
  $user_obj->username=trim($_REQUEST['username']);
  $user_obj->firstname=trim($_REQUEST['firstname']);
  $user_obj->lastname=trim($_REQUEST['lastname']);
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
  $user_obj->id=$GLOBALS['database']->queryInsert("framework__users",$user_obj);
  // check user
  if(!$user_obj->id){api_alerts_add(api_text("framework_alert_userError"),"danger");api_redirect("?mod=".MODULE."&scr=users_list");}
  // send password to user
  $mail_id=api_mail_save(api_text("framework_mail-user_add-subject",$GLOBALS['settings']->title),api_text("framework_mail-user_add-message",array($user_obj->firstname,$GLOBALS['settings']->title,URL,$v_password)),$user_obj->mail);
  // force mail if asynchronous
  if($GLOBALS['settings']->mail_asynchronous){api_mail_process($mail_id);}
  // redirect
  api_alerts_add(api_text("framework_alert_userCreated"),"success");
  api_redirect("?mod=".MODULE."&scr=users_view&idUser=".$user_obj->id);
 }
 /**
  * User Edit
  */
 function user_edit(){
  // get objects
  $user_obj=new cUser($_REQUEST['idUser']);
  // check objects
  if(!$user_obj->id){api_alerts_add(api_text("framework_alert_userNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=users_list");}
  // build user query objects
  $user_qobj=new stdClass();
  // acquire variables
  $user_qobj->id=$user_obj->id;
  //$user_qobj->enabled=$_REQUEST['enabled'];
  //$user_qobj->mail=$_REQUEST['mail'];
  //$user_obj->username=$_REQUEST['username'];
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
  $GLOBALS['database']->queryUpdate("framework__users",$user_qobj);
  // redirect
  api_alerts_add(api_text("framework_alert_userUpdated"),"success");
  api_redirect("?mod=".MODULE."&scr=users_view&idUser=".$user_obj->id);
 }
 /**
  * User Enabled
  *
  * @param boolean $enabled Enabled or Disabled
  */
 function user_enabled($enabled){
  // get objects
  $user_obj=new cUser($_REQUEST['idUser']);
  // check
  if(!$user_obj->id){api_alerts_add(api_text("framework_alert_userNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=users_list");}
  // build user query objects
  $user_qobj=new stdClass();
  $user_qobj->id=$user_obj->id;
  $user_qobj->enabled=($enabled?1:0);
  if(!$enabled){$user_qobj->superuser=0;}
  $user_qobj->updTimestamp=time();
  $user_qobj->updFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($_REQUEST);
  api_dump($user_qobj);
  // update user
  $GLOBALS['database']->queryUpdate("framework__users",$user_qobj);
  // alert
  if($enabled){api_alerts_add(api_text("framework_alert_userEnabled"),"success");}
  else{api_alerts_add(api_text("framework_alert_userDisabled"),"warning");}
  // redirect
  api_redirect("?mod=".MODULE."&scr=users_view&idUser=".$user_obj->id);
 }
 /**
  * User Deleted
  *
  * @param boolean $deleted Deleted or Undeleted
  */
 function user_deleted($deleted){
  api_dump($_REQUEST);
  // get objects
  $user_obj=new cUser($_REQUEST['idUser']);
  // check
  if(!$user_obj->id){api_alerts_add(api_text("framework_alert_userNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=users_list");}
  if($user_obj->id==1){api_alerts_add(api_text("framework_alert_userError"),"danger");api_redirect("?mod=".MODULE."&scr=users_list");}
  if($user_obj->superuser==1){api_alerts_add(api_text("framework_alert_userError"),"danger");api_redirect("?mod=".MODULE."&scr=users_list");}
  // build user query objects
  $user_qobj=new stdClass();
  $user_qobj->id=$user_obj->id;
  $user_qobj->deleted=($deleted?1:0);
  if($deleted){
   $user_qobj->enabled=0;
   $user_qobj->superuser=0;
  }
  $user_qobj->updTimestamp=time();
  $user_qobj->updFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($user_qobj);
  // update user
  $GLOBALS['database']->queryUpdate("framework__users",$user_qobj);
  // alert
  if($deleted){api_alerts_add(api_text("framework_alert_userDeleted"),"warning");}
  else{api_alerts_add(api_text("framework_alert_userUndeleted"),"success");}
  // redirect
  api_redirect("?mod=".MODULE."&scr=users_view&idUser=".$user_obj->id);
 }
 /**
  * User Avatar Remove
  */
 function user_avatar_remove(){
  // get objects
  $user_obj=new cUser($_REQUEST['idUser']);
  // check
  if(!$user_obj->id){api_alerts_add(api_text("framework_alert_userNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=users_list");}
  // remove avatar if exist
  if(file_exists(DIR."uploads/framework/users/avatar_".$user_obj->id.".jpg")){unlink(DIR."uploads/framework/users/avatar_".$user_obj->id.".jpg");}
  // redirect
  api_alerts_add(api_text("framework_alert_userAvatarRemoved"),"success");
  api_redirect("?mod=".MODULE."&scr=users_view&idUser=".$user_obj->id);
 }
 /**
  * User Group Add
  */
 function user_group_add(){
  api_dump($_REQUEST,"_REQUEST");
  // get objects
  $user_obj=new cUser($_REQUEST['idUser']);
  api_dump($user_obj,"user_obj");
  if(!$user_obj->id){api_alerts_add(api_text("framework_alert_userNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=users_list");}
  $group_obj=new cGroup($_REQUEST['fkGroup']);
  api_dump($group_obj,"group_obj");
  if(!$group_obj->id){api_alerts_add(api_text("framework_alert_groupNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=users_view&idUser=".$user_obj->id);}
  // build user join group query object
  $join_qobj=new stdClass();
  $join_qobj->fkUser=$user_obj->id;
  $join_qobj->fkGroup=$group_obj->id;
  $join_qobj->main=(count($user_obj->getAssignedGroups())?0:1);
  // debug
  api_dump($join_qobj,"user_join_group_qobj");
  // delete previous group if exist
  $GLOBALS['database']->queryExecute("DELETE FROM `framework__users__groups` WHERE `fkUser`='".$user_obj->id."' AND `fkGroup`='".$group_obj->id."'");
  // insert group
  $GLOBALS['database']->queryInsert("framework__users__groups",$join_qobj);
  // build user query object
  $user_qobj=new stdClass();
  $user_qobj->id=$user_obj->id;
  $user_qobj->updTimestamp=time();
  $user_qobj->updFkUser=$GLOBALS['session']->user->id;
  api_dump($user_qobj,"user_qobj");
  // update user
  $GLOBALS['database']->queryUpdate("framework__users",$user_qobj);      /** @todo spostare in api event (da fare) */
  // redirect
  api_alerts_add(api_text("framework_alert_userGroupAdded"),"success");
  api_redirect("?mod=".MODULE."&scr=users_view&idUser=".$user_obj->id);
 }
 /**
  * User Group Remove
  */
 function user_group_remove(){
  // get objects
  $user_obj=new cUser($_REQUEST['idUser']);
  // check objects
  if(!$user_obj->id){api_alerts_add(api_text("framework_alert_userNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=users_list");}
  // check if request group is main for user and not only
  if(count($user_obj->getAssignedGroups())>1 && $user_obj->getMainGroup()==$_REQUEST['idGroup']){api_alerts_add(api_text("framework_alert_userGroupError"),"danger");api_redirect("?mod=".MODULE."&scr=users_view&idUser=".$user_obj->id);}
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
  $GLOBALS['database']->queryExecute("DELETE FROM `framework__users__groups` WHERE `fkUser`='".$user_obj->id."' AND `fkGroup`='".$_REQUEST['idGroup']."'");
  // update user
  $GLOBALS['database']->queryUpdate("framework__users",$user_qobj);
  // redirect
  api_alerts_add(api_text("framework_alert_userGroupRemoved"),"warning");
  api_redirect("?mod=".MODULE."&scr=users_view&idUser=".$user_obj->id);
 }
 /**
  * User Group Mainize
  */
 function user_group_mainize(){
  // get objects
  $user_obj=new cUser($_REQUEST['idUser']);
  // check objects
  if(!$user_obj->id){api_alerts_add(api_text("framework_alert_userNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=users_list");}
  if(!array_key_exists($_REQUEST['idGroup'],$user_obj->getAssignedGroups())){api_alerts_add(api_text("framework_alert_userGroupError"),"danger");api_redirect("?mod=".MODULE."&scr=users_view&idUser=".$user_obj->id);}
  // remove previous main groups
  $GLOBALS['database']->queryExecute("UPDATE `framework__users__groups` SET `main`='0' WHERE `fkUser`='".$user_obj->id."'");
  // set new main group
  $GLOBALS['database']->queryExecute("UPDATE `framework__users__groups` SET `main`='1' WHERE `fkUser`='".$user_obj->id."' AND `fkGroup`='".$_REQUEST['idGroup']."'");
  // redirect
  api_alerts_add(api_text("framework_alert_userGroupMainized"),"success");
  api_redirect("?mod=".MODULE."&scr=users_view&idUser=".$user_obj->id);
 }
 /**
  * User Parameter Save
  */
 function user_parameter_save(){
  api_dump($_REQUEST,"_REQUEST");
  // get current user
  $user_obj=new cUser($GLOBALS['session']->user->id);
  if(!$user_obj->id){api_alerts_add(api_text("framework_alert_userNotFound"),"danger");api_redirect("?mod=dashboard");}
  // acquire variables
  $r_module=$_REQUEST['idModule'];
  $r_parameters=$_REQUEST['parameters'];
  // check variables
  if(!$r_module){api_alerts_add(api_text("framework_alert_error"),"danger");api_redirect("?mod=dashboard");}
  if(!is_array($r_parameters)){api_alerts_add(api_text("framework_alert_error"),"danger");api_redirect("?mod=dashboard");}
  // cycle all parameters
  foreach($r_parameters as $parameter_fkey=>$parameter_fvalue){
   // make parameter code
   $parameter_code=$r_module."-".$parameter_fkey;
   // get parameter
   $parameter_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__users__parameters` WHERE `fkUser`='".$user_obj->id."' AND `parameter`='".$parameter_code."'");
   // build user query object
   $parameter_qobj=new stdClass();
   $parameter_qobj->id=$parameter_obj->id;
   $parameter_qobj->fkUser=$user_obj->id;
   $parameter_qobj->parameter=addslashes($parameter_code);
   $parameter_qobj->value=addslashes($parameter_fvalue);
   // debug
   api_dump($parameter_qobj,"parameter query object");
   // check parameter
   if($parameter_obj->id){
    // update parameter
    $GLOBALS['database']->queryUpdate("framework__users__parameters",$parameter_qobj);
   }else{
    // insert parameter
    $GLOBALS['database']->queryInsert("framework__users__parameters",$parameter_qobj);
   }
  }
  // redirect
  api_alerts_add(api_text("framework_alert_userParametersUpdated"),"success");
  api_redirect("?mod=".$r_module."&scr=parameters");
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
   $GLOBALS['database']->queryUpdate("framework__groups",$group);
   api_alerts_add(api_text("framework_alert_groupUpdated"),"success");
  }else{
   // update user
   $GLOBALS['database']->queryInsert("framework__groups",$group);
   api_alerts_add(api_text("framework_alert_groupCreated"),"success");
  }
  // redirect
  api_redirect("?mod=".MODULE."&scr=groups_list");
 }




 /**
  * Sessions Terminate
  */
 function sessions_terminate(){
  api_dump($_REQUEST);
  // check authorizations
  api_checkAuthorization("framework-sessions_manage","dashboard");
  // acquire variables
  $idSession=$_REQUEST['idSession'];
  if(!$idSession){api_alerts_add(api_text("framework_alert_sessionNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=sessions_list");}
  // delete session
  $GLOBALS['database']->queryExecute("DELETE FROM `framework__sessions` WHERE `id`='".$idSession."'");
  // redirect
  api_alerts_add(api_text("framework_alert_sessionTerminated"),"warning");
  api_redirect("?mod=".MODULE."&scr=sessions_list");
 }

 /**
  * Sessions Terminate All
  */
 function sessions_terminate_all(){
  api_dump($_REQUEST);
  // check authorizations
  api_checkAuthorization("framework-sessions_manage","dashboard");
  // delete all sessions
  $GLOBALS['database']->queryExecute("DELETE FROM `framework__sessions`");
  // redirect
  api_alerts_add(api_text("framework_alert_sessionTerminatedAll"),"warning");
  api_redirect(PATH."index.php");
 }




 
 /**
  * Mail Retry
  */
 function mail_retry(){
  // get objects
  $mail_obj=new cMail($_REQUEST['idMail']);
  // check objects
  if(!$mail_obj->id){api_alerts_add(api_text("framework_alert_mailNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=mails_list");}
  // debug
  api_dump($_REQUEST,"_REQUEST");
  api_dump($mail_obj,"mail object");
  // build mail query objects
  $mail_qobj=new stdClass();
  $mail_qobj->id=$mail_obj->id;
  $mail_qobj->status="inserted";
  $mail_qobj->sndTimestamp=null;
  // debug
  api_dump($mail_qobj,"mail query object");
  // execute query
  $GLOBALS['database']->queryUpdate("framework__mails",$mail_qobj);
  // redirect
  api_alerts_add(api_text("framework_alert_mailRetry"),"success");
  api_redirect("?mod=".MODULE."&scr=mails_list&idMail=".$mail_obj->id);
 }
 /**
  * Mail Remove
  */
 function mail_remove(){
  // get objects
  $mail_obj=new cMail($_REQUEST['idMail']);
  // check objects
  if(!$mail_obj->id){api_alerts_add(api_text("framework_alert_mailNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=mails_list");}
  // debug
  api_dump($_REQUEST,"_REQUEST");
  api_dump($mail_obj,"mail object");
  // execute query
  $GLOBALS['database']->queryDelete("framework__mails",$mail_obj->id);
  // redirect
  api_alerts_add(api_text("framework_alert_mailRemoved"),"warning");
  api_redirect("?mod=".MODULE."&scr=mails_list");
 }



 /**
  * Attachment Save
  */
 function attachment_save(){
  api_dump($_REQUEST,"_REQUEST");
  api_dump($_FILES,"_FILES");
  // get object
  $attachment_obj=new cAttachment($_REQUEST['idAttachment']);
  api_dump($attachment_obj,"attachment object");
  // build query objects
  $attachment_qobj=new stdClass();
  // acquire variables
  $attachment_qobj->id=$attachment_obj->id;
  $attachment_qobj->name=strtolower(str_replace(" ","_",$_REQUEST['name']));
  $attachment_qobj->description=$_REQUEST['description'];
  $attachment_qobj->public=($_REQUEST['public']?1:0);
  // check attachment
  if($attachment_qobj->id){
   // update
   $attachment_qobj->updTimestamp=time();
   $attachment_qobj->updFkUser=$GLOBALS['session']->user->id;
   // debug
   api_dump($attachment_qobj);
   // execute query
   $GLOBALS['database']->queryUpdate("framework__attachments",$attachment_qobj);
   // alert
   api_alerts_add(api_text("framework_alert_attachmentUpdated"),"success");
  }else{
   // add
   $attachment_qobj->name=strtolower(str_replace(" ","_",$_FILES['file']['name']));
   $attachment_qobj->typology=$_FILES['file']['type'];
   $attachment_qobj->size=$_FILES['file']['size'];
   $attachment_qobj->addTimestamp=time();
   $attachment_qobj->addFkUser=$GLOBALS['session']->user->id;
   do{
    // generate attachment id
    $attachment_qobj->id=md5(date("YmdHis").rand(1,99999));
    // check for duplicates
    $check_id=$GLOBALS['database']->queryUniqueValue("SELECT `id` FROM `framework__attachments` WHERE `id`='".$attachment_qobj->id."'");
   }while($attachment_qobj->id==$check_id);
   // debug
   api_dump($attachment_qobj);
   // check for file
   if(intval($_FILES['file']['size'])==0 || $_FILES['file']['error']!=UPLOAD_ERR_OK){api_alerts_add(api_text("framework_alert_attachmentError"),"danger");api_redirect("?mod=".MODULE."&scr=attachments_list");}
   // check for id
   if(!$attachment_qobj->id){api_alerts_add(api_text("framework_alert_attachmentError"),"danger");api_redirect("?mod=".MODULE."&scr=attachments_list");}
   // check if file exist and replace
   if(file_exists(DIR."uploads/attachments/".$attachment_qobj->id)){unlink(DIR."uploads/attachments/".$attachment_qobj->id);}
   if(is_uploaded_file($_FILES['file']['tmp_name'])){move_uploaded_file($_FILES['file']['tmp_name'],DIR."uploads/attachments/".$attachment_qobj->id);}
   // check for file
   if(!file_exists(DIR."uploads/attachments/".$attachment_qobj->id)){api_alerts_add(api_text("framework_alert_attachmentError"),"danger");api_redirect("?mod=".MODULE."&scr=attachments_list");}
   // execute query
   $GLOBALS['database']->queryInsert("framework__attachments",$attachment_qobj);
   // alert
   api_alerts_add(api_text("framework_alert_attachmentCreated"),"success");
  }
  // redirect
  api_redirect("?mod=".MODULE."&scr=attachments_list&idAttachment=".$attachment_qobj->id);
 }
 /**
  * Attachment Delete
  *
  * param boolean $delete
  */
 function attachment_delete($deleted){
  api_dump($_REQUEST,"_REQUEST");
  // get objects
  $attachment_obj=new cAttachment($_REQUEST['idAttachment']);
  api_dump($attachment_obj,"attachment object");
  // check objects
  if(!$attachment_obj->id){api_alerts_add(api_text("framework_alert_attachmentNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=attachments_list");}
  // build query objects
  $attachment_qobj=new stdClass();
  // acquire variables
  $attachment_qobj->id=$attachment_obj->id;
  $attachment_qobj->deleted=($deleted?1:0);
  // debug
  api_dump($attachment_qobj,"attachment query object");
  // execute query
  $GLOBALS['database']->queryUpdate("framework__attachments",$attachment_qobj);
  // alert
  if($deleted){api_alerts_add(api_text("framework_alert_attachmentDeleted"),"warning");}
  else{api_alerts_add(api_text("framework_alert_attachmentUndeleted"),"success");}
  // redirect
  api_redirect("?mod=".MODULE."&scr=attachments_list&idAttachment=".$attachment_qobj->id);
 }
 /**
  * Attachment Remove
  */
 function attachment_remove(){
  api_dump($_REQUEST,"_REQUEST");
  // get objects
  $attachment_obj=new cAttachment($_REQUEST['idAttachment']);
  // check objects
  if(!$attachment_obj->id){api_alerts_add(api_text("framework_alert_attachmentNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=attachments_list");}
  // debug
  api_dump($attachment_obj,"attachment object");
  // execute query
  $GLOBALS['database']->queryDelete("framework__attachments",$attachment_obj->id);
  // check if file exist and remove
  if(file_exists(DIR."uploads/attachments/".$attachment_obj->id)){unlink(DIR."uploads/attachments/".$attachment_obj->id);}
  // redirect
  api_alerts_add(api_text("framework_alert_attachmentRemoved"),"warning");
  api_redirect("?mod=".MODULE."&scr=attachments_list");
 }

?>