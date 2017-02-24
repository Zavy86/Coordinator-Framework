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

 // acqurie variables
 $r_module=$_REQUEST['mod'];
 if(!$r_module){$r_module="dashboards";}

 $r_script=$_REQUEST['scr'];
 if(!$r_script){$r_script=NULL;}

 $r_action=$_REQUEST['act'];
 if(!$r_action){$r_action=NULL;}

 // defines constants
 define('DIR',$config->dir);
 define('ROOT',realpath(dirname(__FILE__))."/");
 define('HELPERS',DIR."helpers/");
 define('MODULE',$r_module);
 define('MODULE_PATH',ROOT."modules/".MODULE."/");
 if($r_script){define("SCRIPT",$r_script);}
 if($r_action){define("ACTION",$r_action);}

 // load module
 if(file_exists(MODULE_PATH."module.inc.php")){require_once(MODULE_PATH."module.inc.php");}else{die("ERROR LOADING MODULE: File modules/".MODULE."/module.inc.php was not found");}

 // check script contant or set to default
 if(!defined('SCRIPT')){if($module_default_script){define('SCRIPT',$module_default_script);}else{die("ERROR LOADING MODULE: Default scipt was not defined");}}
 
 // load script if exist
 if(file_exists(MODULE_PATH.SCRIPT.".php")){require_once(MODULE_PATH.SCRIPT.".php");}else{die("ERROR LOADING MODULE: File ".MODULE."/".SCRIPT.".php was not found");}

 // debug
 if($debug){api_dump(get_defined_constants(true)["user"]);}

?>