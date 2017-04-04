<?php
/**
 * Cron
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // include functions
 require_once("functions.inc.php");
 // check token
 //if($_REQUEST['token']!==$GLOBALS['settings']->token_cron){die("TOKEN ERROR");} /** @todo attivare dopo i test */

 // delete expired sessions
 $GLOBALS['database']->queryExecute("DELETE FROM `framework_sessions` WHERE `startTimestamp`<'".(time()-36000)."'");
 $GLOBALS['database']->queryExecute("DELETE FROM `framework_sessions` WHERE `lastTimestamp`<'".(time()-$GLOBALS['settings']->sessions_idle_timeout)."'");

 api_dump("CRON EXECUTED");

 // debug
 if($debug){
  /*api_dump($GLOBALS['settings']->debug(),"settings");
  api_dump(get_defined_constants(true)["user"],"contants");
  api_dump($_SESSION["coordinator_logs"],"logs");*/
  /** @todo attivare dopo i test */
 }
?>