<?php
/**
 * Settings
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Settings class
 *
 * @todo check phpdoc
 */
class Settings{
 /** @var string $settings_array[] Settings array */
 protected $settings_array;

 /**
  * Debug
  *
  * @return object Settings object
  */
 public function debug(){return $this;}

 /**
  * Settings class
  *
  * @return boolean
  */
 public function __construct(){
  // definitions
  $this->settings_array=array();
  // get settings and build object
  $settings_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_settings` ORDER BY `setting` ASC",$GLOBALS['debug']);
  foreach($settings_results as $setting){$this->settings_array[$setting->setting]=$setting->value;}
  // make logo
  if(file_exists(ROOT."uploads/framework/brand.png")){
   $this->settings_array["logo"]=DIR."uploads/framework/logo.png";
  }else{
   $this->settings_array["logo"]=DIR."uploads/framework/logo.default.png";
  }
  return TRUE;
 }

/**
 * Get
 *
 * @param string $setting Setting name
 * @return string Setting value
 */
 public function __get($setting){
  // check if setting exist
  if(!array_key_exists($setting,$this->settings_array)){return FALSE;}
  // return setting value
  return $this->settings_array[$setting];
 }

}
?>