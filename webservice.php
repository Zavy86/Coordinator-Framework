<?php
/**
 * Web Service
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // include functions
 require_once("initializations.inc.php");
 // check for debug
 if($_GET['debug']==1){$_GET['debug']=true;}else{$_GET['debug']=false;}
 // load module
 if(!in_array(MODULE,array("dashboard","framework"))){
  $module=new cModule(MODULE);
  if(!$module->id){die("MODULE NOT FOUND");}
  if(!$module->enabled && !DEBUG){die("MODULE DISABLED: The module ".MODULE." is not enabled");}
 }
 if(file_exists(MODULE_PATH."module.inc.php")){require_once(MODULE_PATH."module.inc.php");}else{die("ERROR LOADING MODULE: File modules/".MODULE."/module.inc.php was not found");}
 if(file_exists(MODULE_PATH."functions.inc.php")){require_once(MODULE_PATH."functions.inc.php");}else{echo "WARNING LOADING MODULE: File modules/".MODULE."/functions.inc.php was not found";}
 foreach($module->required_modules_array as $module_f){if(file_exists(DIR."modules/".$module_f."/functions.inc.php")){require_once(DIR."modules/".$module_f."/functions.inc.php");}else{echo "WARNING LOADING REQUIRED MODULE: File modules/".$module_f."/functions.inc.php was not found";}}
 // load module localization
 $localization->load(MODULE);
 // load required module localization
 foreach($module->required_modules_array as $module_f){$localization->load($module_f);}
 // check for module web service
 if(!file_exists(MODULE_PATH."webservice.php")){die("ERROR LOADING MODULE: No Web Service found in this module");}
 // define and load web service
 define('SCRIPT',"webservice");
 require_once(MODULE_PATH."webservice.php");
 // debug
 api_debug();
?>