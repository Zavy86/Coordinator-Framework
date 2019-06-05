<?php
/**
 * Number Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Number Format
  *
  * @param string $number Number
  * @param string $decimals Number of decimals
  * @param string $currency Currency sign
  * @param string $small_decimals Decimal in small format
  * @param string $hide_unsignificat Hide unsignificant decimals
  * @return string Formatted number or false
  */
 function api_number_format($number,$decimals=2,$currency=null,$small_decimals=false,$hide_unsignificat=false){
  // check parameters
  if(!is_numeric($number)){return false;}
  if(!is_numeric($decimals)){return false;}
  // format number
  $return=number_format($number,$decimals,",",".");
  // check for currency
  if($currency){$return=$currency." ".$return;}
  // check for hide unsignificant decimals
  if($decimals && $hide_unsignificat){
   // remove unsignificant decimals
   do{
    $num=substr($return,-1);
    if($num=="0" || $num==","){$return=substr($return,0,-1);}
   }while($num=="0" && $num!=",");
  }
  // check for small decimals
  if($decimals && $small_decimals){
   $real=explode(",",$return)[0];
   $decimals=explode(",",$return)[1];
   $return=api_tag("span",$real.api_tag("small",",".$decimals));
  }
  // return
  return $return;
 }

?>