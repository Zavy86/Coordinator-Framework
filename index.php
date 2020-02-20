<?php
/**
 * Index
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // include functions
 require_once("initializations.inc.php");
 // check session
 if(!$session->validity && !(MODULE=="framework" && SCRIPT=="submit" && (ACTION=="session_login" || ACTION=="own_password_recovery"))){api_redirect("login.php");}
 // check for password expired
 if($settings->sessions_authentication_method=="standard" && $session->user->pwdExpired && !((MODULE=="framework" && SCRIPT=="own_password") || (MODULE=="framework" && SCRIPT=="submit" && ACTION=="own_password_update"))){api_alerts_add(api_text("alert_passwordExpired"),"warning");api_redirect("?mod=framework&scr=own_password");}
 // check if module is enabled
 if(!in_array(MODULE,array("dashboard","framework"))){
  $module=new cModule(MODULE);
  if(!$module->id){die("MODULE NOT FOUND");}
  if(!$module->enabled && !DEBUG){die("MODULE DISABLED: The module ".MODULE." is not enabled");}
 }
 // load module
 if(file_exists(MODULE_PATH."module.inc.php")){require(MODULE_PATH."module.inc.php");}else{die("ERROR LOADING MODULE: File modules/".MODULE."/module.inc.php was not found");}
 if(file_exists(MODULE_PATH."functions.inc.php")){require_once(MODULE_PATH."functions.inc.php");}else{echo "WARNING LOADING MODULE: File modules/".MODULE."/functions.inc.php was not found";}
 foreach($module->required_modules_array as $module_f){if(file_exists(DIR."modules/".$module_f."/functions.inc.php")){require_once(DIR."modules/".$module_f."/functions.inc.php");}else{echo "WARNING LOADING REQUIRED MODULE: File modules/".$module_f."/functions.inc.php was not found";}}
 // load module localization
 $localization->load(MODULE);
 // load required module localization
 foreach($module->required_modules_array as $module_f){$localization->load($module_f);}
 // check script and tab constants or set to default
 if(!defined('SCRIPT')){if($module_default_script){define('SCRIPT',$module_default_script);}else{if(file_exists(MODULE_PATH."dashboard.php")){define('SCRIPT',"dashboard");}else{die("ERROR LOADING MODULE: Default script was not defined and module's dashboard was not found");}}}
 if(!defined('TAB')){if($module_default_tab){define('TAB',$module_default_tab);}}
 // check for submit action
 if(SCRIPT=="submit" && !defined('ACTION')){die("ERROR EXECUTING SCRIPT: The action was not defined");}
 // load script if exist
 if(file_exists(MODULE_PATH.SCRIPT.".php")){require_once(MODULE_PATH.SCRIPT.".php");}else{die("ERROR LOADING MODULE: File ".MODULE."/".SCRIPT.".php was not found");}
 // debug
 api_debug();
?>