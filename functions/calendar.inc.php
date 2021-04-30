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
 * @return array Weekly Days
 */
function api_calendar_weekly_days(){
	// definitions
	$days_array=array(1=>"monday",2=>"tuesday",3=>"wednesday",4=>"thursday",5=>"friday",6=>"saturday",7=>"sunday");
	// return
	return $days_array;
}
