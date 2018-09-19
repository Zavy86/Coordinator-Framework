<?php
/**
 * Framework - Cron
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // definitions
 $logs=array();
 // delete expired sessions
 $deleted_sessions_1=$GLOBALS['database']->queryExecute("DELETE FROM `framework_sessions` WHERE `startTimestamp`<'".(time()-36000)."'");
 $deleted_sessions_2=$GLOBALS['database']->queryExecute("DELETE FROM `framework_sessions` WHERE `lastTimestamp`<'".(time()-$GLOBALS['settings']->sessions_idle_timeout)."'");
 // log
 $logs[]="Expired sessions deleted (".($deleted_sessions_1+$deleted_sessions_2).")"; /** @todo verificare ed eventualmente migliorare */
 // sendmail
 $processed_mails=api_sendmail_process_all();
 // log
 $logs[]="Mails processed (".$processed_mails.")"; /** @todo verificare ed eventualmente migliorare */
 // debug
 api_dump($logs,"framework");
?>