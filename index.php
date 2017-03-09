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
 if(!$session->validity && !(MODULE=="accounts" && SCRIPT=="submit" && ACTION=="user_login")){api_redirect("login.php");}

 // load module
 if(file_exists(MODULE_PATH."module.inc.php")){require_once(MODULE_PATH."module.inc.php");}else{die("ERROR LOADING MODULE: File modules/".MODULE."/module.inc.php was not found");}
 if(file_exists(MODULE_PATH."functions.inc.php")){require_once(MODULE_PATH."functions.inc.php");}else{echo "WARNING LOADING MODULE: File modules/".MODULE."/functions.inc.php was not found";}
 $localization->load(MODULE); /** rifare bene con moduli required ecc.. */

 // check script and tab constants or set to default
 if(!defined('SCRIPT')){if($module_default_script){define('SCRIPT',$module_default_script);}else{die("ERROR LOADING MODULE: Default scipt was not defined");}}
 if(!defined('TAB')){if($module_default_tab){define('TAB',$module_default_tab);}}

 // load script if exist
 if(file_exists(MODULE_PATH.SCRIPT.".php")){require_once(MODULE_PATH.SCRIPT.".php");}else{die("ERROR LOADING MODULE: File ".MODULE."/".SCRIPT.".php was not found");}

 // debug
 if($debug){
  //api_dump($session->debug(),"session",API_DUMP_VARDUMP);
  api_dump($session->debug(),"session");
  api_dump($settings->debug(),"settings");
  api_dump(get_defined_constants(true)["user"],"contants");
  api_dump($_SESSION["coordinator_logs"],"logs");
 }

?>