<?php
/**
 * Functions
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 session_start();

 // definitions
 global $debug;
 global $develop;
 global $config;
 global $db;

 global $settings;
 global $sensors;

 $config=new stdClass();

 // reset session logs
 $_SESSION['log']=NULL;

 // include configuration file
 require_once("config.inc.php");

 // include classes
 require_once("classes/database.class.php");
 require_once("classes/html.class.php");
 require_once("classes/grid.class.php");
 require_once("classes/nav.class.php");
 require_once("classes/navbar.class.php");

 // check for debug
 if($_GET['debug']){$debug=TRUE;}

 // show errors
 ini_set("display_errors",($debug||$develop?TRUE:FALSE));
 if($develop){error_reporting(E_ALL & ~E_NOTICE);}else{error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);}

 // build database instance
 $db=new Database();

 /**
  * Renderize a variable dump into a pre tag
  *
  * @param string $variable variable to dump
  * @param string $label dump label
  * @param API_DUMP_PRINTR|API_DUMP_VARDUMP $function dump function
  * @param string $class pre dump class
  */
 function api_dump($variable,$label=NULL,$function=API_DUMP_PRINTR,$class=NULL){
  echo "\n\n<!-- dump -->\n";
  echo "<pre class='".$class."'>\n";
  if($label<>NULL){echo "<strong>".$label."</strong><br>";}
  if(is_string($variable)){$variable=str_replace(array("<",">"),array("&lt;","&gt;"),$variable);}
  switch($function){
   case API_DUMP_PRINTR:print_r($variable);break;
   case API_DUMP_VARDUMP:var_dump($variable);break;
   default:echo $variable."\n";
  }
  echo "</pre>\n<!-- /dump -->\n\n";
 }

 /**
  * api_dump contants
  *
  * @const API_DUMP_PRINTR dump with print_r()
  * @const API_DUMP_VARDUMP dump with var_dump()
  */
 define('API_DUMP_PRINTR',1);
 define('API_DUMP_VARDUMP',2);


 /**
 * Datetime Now
 *
 * @param integer $format coordinator module
 * @return current timestamp
 */
 function api_datetime_now(){
  return date("Y-m-d H:i:s");
 }


 /**
  * Parse URL to standard class                  @todo modificare nome qui e in nav class
  *
  * @param string $url URL to parse
  * @return object Parsed
  */
 function api_parse_url($url=NULL){
  // check url
  if(!$url){$url=(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];}
  // build object
  $return=new stdClass();
  // parse url string into object
  foreach(parse_url($url) as $key=>$value){$return->$key=$value;}
  // parse query to array
  $return->query_array=array();
  parse_str($return->query,$return->query_array);
  // return
  return $return;
 }

?>