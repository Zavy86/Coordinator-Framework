<?php
/**
 * Index
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // include functions
 require_once("functions.inc.php");
 // check session
 if(!$session->validity && !(MODULE=="framework" && SCRIPT=="submit" && (ACTION=="user_login" || ACTION=="user_recovery"))){api_redirect("login.php");}
 // check for password expired
 if($settings->sessions_authentication_method=="standard" && $session->user->pwdExpired && !((MODULE=="framework" && SCRIPT=="own_password") || (MODULE=="framework" && SCRIPT=="submit" && ACTION=="own_password_update"))){api_alerts_add(api_text("alert_passwordExpired"),"warning");api_redirect("?mod=framework&scr=own_password");}
 // load module
 if(file_exists(MODULE_PATH."module.inc.php")){require_once(MODULE_PATH."module.inc.php");}else{die("ERROR LOADING MODULE: File modules/".MODULE."/module.inc.php was not found");}
 if(file_exists(MODULE_PATH."functions.inc.php")){require_once(MODULE_PATH."functions.inc.php");}else{echo "WARNING LOADING MODULE: File modules/".MODULE."/functions.inc.php was not found";}
 $localization->load(MODULE); /** rifare bene con moduli required ecc.. */
 // check script and tab constants or set to default
 if(!defined('SCRIPT')){if($module_default_script){define('SCRIPT',$module_default_script);}else{if(file_exists(MODULE_PATH."dashboard.php")){define('SCRIPT',"dashboard");}else{die("ERROR LOADING MODULE: Default script was not defined and module's dashboard was not found");}}}
 if(!defined('TAB')){if($module_default_tab){define('TAB',$module_default_tab);}}
 // load script if exist
 if(file_exists(MODULE_PATH.SCRIPT.".php")){require_once(MODULE_PATH.SCRIPT.".php");}else{die("ERROR LOADING MODULE: File ".MODULE."/".SCRIPT.".php was not found");}

 /** @todo cancellare dopo i test */
 if($debug){require_once("cron.php");}

 // debug
 api_debug();
?>