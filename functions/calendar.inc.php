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
	return array(1=>"monday",2=>"tuesday",3=>"wednesday",4=>"thursday",5=>"friday",6=>"saturday",7=>"sunday");
}

/**
 * Calendar Months
 *
 * @return array Months
 */
function api_calendar_months(){
	return array(1=>"january",2=>"february",3=>"march",4=>"april",5=>"may",6=>"june",7=>"july",8=>"august",9=>"september",10=>"october",11=>"november",12=>"december");
}

/**
 * Calendar Previous month
 *
 * @return int Previous month
 */
function api_calendar_previous_month($month){
	if($month<1||$month>12){return 1;}
	if(--$month<1){return 12;}
	return $month;
}
