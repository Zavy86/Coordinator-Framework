<?php
/**
 * Localization
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Localization structure class
 *
 * @todo check phpdoc
 */
class Localization{
 /** @var array $available_localizations[] Array of available localizations */
 protected $available_localizations;

 /** @var array $localized_strings_array[] Array of localized strings */
 protected $localized_strings_array;

 /**
  * Debug
  *
  * @return object Session object
  */
 public function debug(){return $this;}

 /**
  * Localization class
  */
 public function __construct(){
  //
  $this->available_localizations=array();
  $this->localized_strings_array=array();
  // load standard localizations
  $this->load(NULL);
 }

 /**
  * Load
  *
  * @param string $module Module name
  * @return boolean
  */
 public function load($module){
  // definitions
  $xml_files=array();
  // check for module path
  if($module){$path=ROOT."modules/".$module."/localizations/";}else{$path=ROOT."localizations/";}
  // check for directory
  if(!file_exists($path)){return FALSE;}
  // scan all localization files
  foreach(scandir($path) as $file){if(substr(strtolower($file),-4)==".xml"){$xml_files[]=$file;}}
  // cycle all localization files
  foreach($xml_files as $xml_file){
   // make language code from file name
   $language_code=substr($xml_file,0,-4);
   // parse xml file
   $xml_parsed=simplexml_load_file($path.$xml_file);
   // if default localization save into avaiables array
   if(!$module){$this->available_localizations[(string)$xml_parsed->code]=(string)$xml_parsed->localization;}
   // cycle all text keys
   foreach($xml_parsed->text as $text_xml){$this->localized_strings_array[$language_code][(string)$text_xml['key']]=(string)$text_xml;}
  }
  // return
  return TRUE;
 }

/**
 * Get
 *
 * @param string $property Property name
 * @return mixed value
 */
 public function __get($property){
  // switch properties
  switch($property){
   case "available_localizations":return $this->available_localizations;
   default:return FALSE;
  }
 }

/**
 * Available Localizations
 *
 * @return array of available localizations
 */
 //public function available_localizations(){return $this->available_localizations;}

/**
 * Get Localized String
 *
 * @param string $property Property name
 * @return mixed value
 */
 public function getString($key,$localization_code=NULL){
  if(!$localization_code){$localization_code=$GLOBALS['session']->user->localization;}
  $return=$this->localized_strings_array[$localization_code][$key];
  if(!$return){$return=$this->localized_strings_array["default"][$key];}
  if(!$return){$return=FALSE;}
  // return
  return $return;
 }

}
?>