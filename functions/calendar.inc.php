<?php
/**
 * Calendar Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Calendar Weekly Days
  *
  * @param integer $start number of day to start
  * @return array Weekly Days
  */
 function api_calendar_weekly_days($start=null){
  // definitions
  $days_array=array(1=>"monday",2=>"tuesday",3=>"wednesday",4=>"thursday",5=>"friday",6=>"saturday",0=>"sunday");
  /** @todo fare tramite impostazioni con primo giorno della settimana 0 o 1 */
  // return
  return $days_array;
 }

?>