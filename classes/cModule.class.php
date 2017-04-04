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
 */
class Module{

 /** Properties */
 protected $module;
 protected $version;
 protected $enabled;
 protected $name;
 protected $description;
 protected $addTimestamp;
 protected $addFkUser;
 protected $updTimestamp;
 protected $updFkUser;
 protected $source_path;
 protected $source_version;
 protected $authorizations_array;

 /**
  * Debug
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
  // load localization
  $GLOBALS['localization']->load($this->module);
  // make name and description
  $this->name=api_text($module->module);
  $this->description=api_text($module->module."-description");
  // get source version
  $this->source_path=ROOT."modules/".$this->module."/";
  if($this->module=="framework"){$this->source_path=ROOT;}
  $this->source_version=file_get_contents($this->source_path."VERSION.txt");
  // get authorizations
  $this->authorizations_array=array();
  $authorizations_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_modules_authorizations` WHERE `module`='".$this->module."'"); /** @todo in che ordine?? ORDER BY `action` */
  foreach($authorizations_results as $authorization){$this->authorizations_array[$authorization->id]=New cAuthorization($authorization);}
  return TRUE;
 }

 /**
  * Get
  *
  * @param string $property Property name
  * @return string Property value
  */
 public function __get($property){return $this->$property;}

}
?>