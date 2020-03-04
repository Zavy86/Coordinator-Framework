<?php
/**
 * Date Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Date (from timestamp)
  *
  * @param integer $timestamp Unix timestamp (Default now)
  * @param string $timezone Time Zone
  * @return string|boolean Date in format YYYY-MM-DD or false
  */
 function api_date($timestamp=null,$timezone=null){
  if(!$timestamp){$timestamp=time();}
  if(!$timezone){$timezone=$GLOBALS['session']->user->timezone;}
  // build date time object
  $dt=new DateTime();
  // set date time timezone
  if($timezone){$dt->setTimeZone(new DateTimeZone($timezone));}
  $dt->setTimestamp($timestamp);
  // return date formatted
  return $dt->format("Y-m-d");
 }

 /**
  * Date Format
  *
  * @param string $datetime Date Time in format YYYY-MM-DD [HH:II:SS]
  * @param string $format Date Time format (see php.net/manual/en/function.date.php)
  * @param string $timezone Time Zone
  * @return string|boolean Formatted date or false
  */
 function api_date_format($datetime,$format="Y-m-d",$timezone=null){
   if(!$datetime){return false;}
  if(!$timezone){$timezone=$GLOBALS['session']->user->timezone;}
  // build date time object
  $dt=new DateTime($datetime);
  // set date time timezone
  if($timezone){$dt->setTimeZone(new DateTimeZone($timezone));}
  // return date time formatted
  return $dt->format($format);
 }

 /**
  * Date Difference
  *
  * @param string $datetime_a Date Time in format YYYY-MM-DD [HH:II:SS]
  * @param string $datetime_b Date Time in format YYYY-MM-DD [HH:II:SS]
  * @param string $format Return format (d days | s seconds)
  * @return double|false Date difference in selected format or false
  */
 function api_date_difference($datetime_a=null,$datetime_b=null,$format="d"){
  // check parameters
  if(!$datetime_a || !$datetime_b){return false;}
  if(!in_array($format,array("d","s"))){return false;}
  // days format
  if($format=="d"){
   $datediff_a=strtotime(substr($datetime_a,0,10));
   $datediff_b=strtotime(substr($datetime_b,0,10));
   $difference=round(($datediff_b-$datediff_a)/60/60/24);
  }
  // seconds format
  if($format=="s"){
   $datediff_a=strtotime($datetime_a);
   $datediff_b=strtotime($datetime_b);
   $difference=$datediff_b-$datediff_a;
  }
  // return
  return $difference;
 }

?>