<?php
/**
 * Week Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Numeric Week
 *
 * @param string $week Week number in ISO format 2021-W01 (or 202101)
 * @return string|false Week number in numeric format 202101
 */
function api_week_numeric($week){
	if(strlen($week)!=6&&strlen($week)!=8){return false;}
	return substr($week,0,4).substr($week,-2);
}

// * @param
/**
 * Format Week
 *
 * @param string $week Week number in ISO format 2021-W01 (or 202101)
 * @param string $format [Y,y,W,w]
 * @return string|false Week number formatted
 */
function api_week_format($week,$format){
	if(strlen($week)!=6&&strlen($week)!=8){return false;}
	if(!strlen($format)){return false;}
	$year_full=substr($week,0,4);
	$year_mini=substr($week,2,2);
	$week_full=substr($week,-2);
	$week_mini=(int)substr($week,-2);
	// replace and return
	return str_replace(array("Y","y","W","w"),array($year_full,$year_mini,$week_full,$week_mini),$format);
}

/**
 * Check if week exists in year
 *
 * @param string $week Week number in ISO format 2021-W01 (or 202101)
 * @return boolean
 */
function api_week_check($week){
	if(strlen($week)!=6&&strlen($week)!=8){return false;}
	// make year and week
	$year=substr($week,0,4);
	$week=(int)substr($week,-2);
	// get last week of year
	$last_week=date('W',strtotime($year.'-12-28'));
	// check
	if($week>0&&$week<=$last_week){
		return true;
	}else{
		return false;
	}
}
