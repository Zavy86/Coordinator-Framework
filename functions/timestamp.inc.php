<?php
/**
 * Timestamp Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Timestamp Format
 *
 * @param integer $timestamp Unix timestamp
 * @param string $format Date Time format (see php.net/manual/en/function.date.php)
 * @return string|boolean Formatted timestamp or false
 */
function api_timestamp_format($timestamp,$format="Y-m-d H:i:s",$timezone=null){
 if(!is_numeric($timestamp) || $timestamp==0){return false;}
 if(!$timezone){$timezone=$GLOBALS['session']->user->timezone;}
 // build date time object
 $datetime=new DateTime("@".$timestamp);
 // set date time timezone
 $datetime->setTimeZone(new DateTimeZone($timezone));
 // return date time formatted
 return $datetime->format($format);
}

/**
* Timestamp Difference From
* @param string $timestamp Timestamp from
* @param string $difference difference in textual form ("+1 day","-1 month,..)
* @param string $format timestamp format
* @return string formatted timestamp difference
*/
function api_timestampDifferenceFrom($timestamp,$difference,$format="Y-m-d H:i:s"){/** @todo verificare il nome fa schifo ed è poco significativo */
 if(!is_numeric($timestamp)){return false;}
 $datetime=new DateTime("@".$timestamp);
 $datetime->modify($difference);
 return $datetime->getTimestamp();
}

/**
 * Timestamp Difference Format
 *
 * @param integer $difference Number of seconds
 * @param boolean $showSeconds Show seconds
 * @return string Formatted timestamp difference
 */
function api_timestampDifferenceFormat($difference,$showSeconds=true){ /** @todo verificare il nome fa schifo ed è poco significativo */
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

?>