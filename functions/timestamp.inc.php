<?php
/**
 * Timestamp Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Timestamp (from date or datetime)
  *
  * @param string $datetime Date in format YYYY-MM-DD [HH:II:SS]
  * @param string $timezone Time Zone
  * @return integer|boolean Unix timestamp or false
  */
 function api_timestamp($datetime,$timezone=null){
  if(!$datetime){return false;}
  if(!$timezone){$timezone=$GLOBALS['session']->user->timezone;}
  // build date time object
  $dt=new DateTime($datetime);
  // set date time timezone
  if($timezone){$dt->setTimeZone(new DateTimeZone($timezone));}
  // return timestamp
  return $dt->getTimestamp();
 }

 /**
  * Timestamp Format
  *
  * @param integer $timestamp Unix timestamp
  * @param string $format Date Time format (see php.net/manual/en/function.date.php)
  * @param string $timezone Time Zone
  * @return string|boolean Formatted timestamp or false
  */
 function api_timestamp_format($timestamp,$format="Y-m-d H:i:s",$timezone=null){
  if(!is_numeric($timestamp) || $timestamp==0){return false;}
  if(!$timezone){$timezone=$GLOBALS['session']->user->timezone;}
  // build date time object
  $datetime=new DateTime("@".$timestamp);
  // set date time timezone
  if($timezone){$datetime->setTimeZone(new DateTimeZone($timezone));}
  // return date time formatted
  return $datetime->format($format);
 }

 /**
  * Timestamp of Day
  *
  * @param string $timestamp Timestamp
  * @param string $timezone Time Zone
  * @return array|boolean Array with start and and of day
  */
 function api_timestamp_dayRange($timestamp,$timezone=null){
  if(!is_int($timestamp)){return false;}
  if(!$timezone){$timezone=$GLOBALS['session']->user->timezone;}
  // build date time object
  $dt=new DateTime();
  // set date time timezone
  if($timezone){$dt->setTimeZone(new DateTimeZone($timezone));}
  // set timestamp
  $dt->setTimestamp($timestamp);
  // make begin
  $dt_begin=clone $dt;
  $dt_begin->modify('today');
  // make end
  $dt_end=clone $dt_begin;
  $dt_end->modify('tomorrow');
  $dt_end->modify('-1 second');
  // debug
  /*api_dump(
   array(
    'input'=>$dt->getTimestamp()." -> ".$dt->format('Y-m-d H:i:s e'),
    'begin'=>$dt_begin->getTimestamp()." -> ".$dt_begin->format('Y-m-d H:i:s e'),
    'end  '=>$dt_end->getTimestamp()." -> ".$dt_end->format('Y-m-d H:i:s e')
   )
  );*/
  // return
  return array("begin"=>$dt_begin->getTimestamp(),"end"=>$dt_end->getTimestamp());
 }

 /**
  * Timestamp Interval Textual
  *
  * @param integer $difference Number of seconds
  * @param boolean $showSeconds Show seconds
  * @return string Formatted timestamp difference
  */
 function api_timestamp_intervalTextual($difference,$showSeconds=true){ /** @tip fare anche interalFormat */
  if($difference===null){return false;}
  $return=null;
  $days=intval(intval($difference)/(3600*24));
  if($days==1){$return.=$days." ".api_text("day").", ";}
  elseif($days>1){$return.=$days." ".api_text("days").", ";}
  $hours=(intval($difference)/3600)%24;
  if($hours==1){$return.=$hours." ".api_text("hour").", ";}
  elseif($hours>1){$return.=$hours." ".api_text("hours").", ";}
  $minutes=(intval($difference)/60)%60;
  if($minutes==1){$return.=$minutes." ".api_text("minute").", ";}
  elseif($minutes>1){$return.=$minutes." ".api_text("minutes").", ";}
  if($showSeconds || intval($difference)<60){
   $seconds=intval($difference)%60;
   if($seconds==1){$return.=$seconds." ".api_text("second").", ";}
   elseif($seconds>1){$return.=$seconds." ".api_text("seconds").", ";}
   else{$return.="0 ".api_text("seconds").", ";}
  }
  return substr($return,0,-2);
 }


 /*        ********          @todo verificare quelle che servono e quelle che no           *************              */



 /**
  * Timestamp Difference From
  * @param string $timestamp Timestamp from
  * @param string $difference difference in textual form ("+1 day","-1 month",..)
  * @param string $format timestamp format
  * @return string formatted timestamp difference
  */
 function api_timestampDifferenceFrom($timestamp,$difference,$format=null){/** @todo verificare il nome fa schifo ed è poco significativo */
  if(!is_numeric($timestamp)){return false;}
  $datetime=new DateTime("@".$timestamp);
  $datetime->modify($difference);
  if(!$format){
   // return timetamp
   return $datetime->getTimestamp();
  }
  else{
   // return date time formatted
   return $datetime->format($format);
  }
 }

 /**
  * Timestamp Difference Days
  *
  * @param integer $difference Number of days
  * @return string Formatted timestamp difference
  */
 function api_timestampDifferenceDays($difference){
  if($difference===null){return false;}
  $days=intval(intval($difference)/(3600*24));
  if($days==1){$return.=$days." ".api_text("day");}
  elseif($days>1){$return.=$days." ".api_text("days");}
  return $return;
 }

?>