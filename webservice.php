<?php
/**
 * Web Service
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // include functions
 require_once("functions.inc.php");
 // load module
 if(file_exists(MODULE_PATH."module.inc.php")){require_once(MODULE_PATH."module.inc.php");}else{die("ERROR LOADING MODULE: File modules/".MODULE."/module.inc.php was not found");}
 if(file_exists(MODULE_PATH."functions.inc.php")){require_once(MODULE_PATH."functions.inc.php");}else{echo "WARNING LOADING MODULE: File modules/".MODULE."/functions.inc.php was not found";}
 $localization->load(MODULE); /** rifare bene con moduli required ecc.. */
 // check for module web service
 if(!file_exists(MODULE_PATH."webservice.php")){die("ERROR LOADING MODULE: No Web Service found in this module");}
 // define and load web service
 define('SCRIPT',"webservice");
 require_once(MODULE_PATH."webservice.php");
 // debug
 api_debug();
?>