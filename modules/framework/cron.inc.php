<?php
/**
 * Cron
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // definitions
 $logs=array();
 // delete expired sessions
 $GLOBALS['database']->queryExecute("DELETE FROM `framework_sessions` WHERE `startTimestamp`<'".(time()-36000)."'");
 $GLOBALS['database']->queryExecute("DELETE FROM `framework_sessions` WHERE `lastTimestamp`<'".(time()-$GLOBALS['settings']->sessions_idle_timeout)."'");
 // log
 $logs[]="Expired sessions deleted"; /** @todo verificare ed eventualmente migliorare */
 // sendmail
 api_sendmail_process_all();
 // log
 $logs[]="Mails processed"; /** @todo verificare ed eventualmente migliorare */
 // debug
 api_dump($logs,"framework");
?>