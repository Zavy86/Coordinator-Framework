<?php
/**
 * Period Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Period (from number to text)
 *
 * @param integer $period
 * @return string|boolean Textual period
 */
function api_period($period){
	// check parameters
	if(strlen($period)!=6){return false;}
	// definitions
	$year=(int)substr($period,0,4);
	$month=(int)substr($period,4,2);
	// set locale
	setlocale(LC_TIME,$GLOBALS['session']->user->localization);
	// convert month to text
	$return=ucfirst(strftime("%B",strtotime($year."-".$month."-01")))." ".$year;
	// return
	return $return;
}

/**
 * Period Check
 * Check if period is possible
 *
 * @param integer $period
 * @return boolean
 */
function api_period_check($period){
	// check parameters
	if(strlen($period)!=6){return false;}
	// definitions
	$year=(int)substr($period,0,4);
	$month=(int)substr($period,4,2);
	// check year
	if($year<1000||$month>2999){return false;}
	// check month
	if($month<1||$month>12){return false;}
	// return
	return true;
}


/**
 * Period Range
 *
 * @param integer $from Initial period
 * @param integer $to Final period
 * @return string[]|boolean Array of periods
 */
function api_period_range($from,$to){
	// check parameters
	if(strlen($from)!=6 || strlen($to)!=6){return false;}
	// definitions
	$infinite_loop=0;
	$periods_array=array();
	$from_year=(int)substr($from,0,4);
	$from_month=(int)substr($from,4,2);
	$to_year=(int)substr($to,0,4);
	$to_month=(int)substr($to,4,2);
	// check for reverse
	if((int)$from>(int)$to){$reverse=true;}else{$reverse=false;}
	// set loop variables
	$year=($reverse?$to_year:$from_year);
	$month=($reverse?$to_month:$from_month);
	$end=($reverse?$from:$to);
	// years loop
	do{
		// months loop
		do{
			// check for infinite loop
			if($infinite_loop++==1000){break(2);}
			// make period
			$period=$year.str_pad($month,2,"0",STR_PAD_LEFT);
			// add period to return array
			$periods_array[$period]=api_period($period);
			// increment month
			$month++;
			// months loop condition
		}while($month<=12 && $period!=$end);
		// increment year
		$year++;
		// reset month
		$month=1;
		// years loop condition
	}while($period!=$end);
	// check for reverse
	if($reverse){$periods_array=array_reverse($periods_array,true);}
	// debug
	//api_dump($periods_array,"periods array");
	// return
	return $periods_array;
}
