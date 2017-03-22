<?php
/**
 * Module
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Module class
 *
 * @todo check phpdoc
 */
class Module{
 protected $module;
 protected $version;
 protected $enabled;
 protected $name;
 protected $addTimestamp;
 protected $addFkUser;
 protected $updTimestamp;
 protected $updFkUser;
 protected $source_path;
 protected $source_version;

 /**
  * Debug
  *
  * @return object Module object
  */
 public function debug(){return $this;}

 /**
  * Module class
  *
  * @param integer $module Module object or ID
  * @return boolean
  */
 public function __construct($module){
  // get object
  if(is_string($module)){$module=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework_modules` WHERE `module`='".$module."'");}
  if(!$module->module){return FALSE;}
  // set properties
  $this->module=stripslashes($module->module);
  $this->version=stripslashes($module->version);
  $this->addTimestamp=$module->addTimestamp;
  $this->addFkUser=$module->addFkUser;
  $this->updTimestamp=$module->updTimestamp;
  $this->updFkUser=$module->updFkUser;
  $this->enabled=(bool)$module->enabled;
  // make name and description
  $this->name=api_text($module->module);
  $this->description=api_text($module->module."-description");
  // get source version
  $this->source_path=ROOT."modules/".$module->module."/";
  if($module->module=="framework"){$this->source_path=ROOT;}
  $this->source_version=file_get_contents($this->source_path."VERSION.txt");
  return TRUE;
 }

/**
 * Get
 *
 * @param string $property Property name
 * @return string Property value
 */
 public function __get($property){
  // switch
  /*switch($property){
   case "module":return $this->module;
   default:return FALSE;
  }*/
  return $this->$property;
 }

}
?>