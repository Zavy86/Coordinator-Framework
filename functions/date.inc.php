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
  $dt->setTimeZone(new DateTimeZone($timezone));
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
  $dt->setTimeZone(new DateTimeZone($timezone));
  // return date time formatted
  return $dt->format($format);
 }

?>