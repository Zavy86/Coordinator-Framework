<?php
/**
 * Authorization
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Authorization class
 */
class cAuthorization{
 protected $id;
 protected $module;
 protected $action;
 protected $name;
 protected $description;
 protected $groups_array;
 protected $groups_level_array;

 /**
  * Debug
  *
  * @return object authorization object
  */
 public function debug(){return $this;}

 /**
  * Authorization class
  *
  * @param integer $authorization Authorization object or ID
  * @return boolean
  */
 public function __construct($authorization){
  // get object
  if(is_int($authorization)){$authorization=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework_modules_authorizations` WHERE `id`='".$authorization."'");}
  if(!$authorization->id){return false;}
  // set properties
  $this->id=$authorization->id;
  $this->module=stripslashes($authorization->module);
  $this->action=stripslashes($authorization->action);
  // load localization
  //$GLOBALS['localization']->load($this->authorization); /** #todo verificare se serve, in teoria avendo caricato il modulo dovrebbe gia esserci */
  // make name and description
  $this->name=api_text($authorization->action);
  $this->description=api_text($authorization->action."-description");
  // get groups
  $this->groups_array=array();
  $this->groups_level_array=array();
  /** @todo fare autorizzazioni anche per tutti i gruppi (fkGroup=null) */
  $groups_results=$GLOBALS['database']->queryObjects("SELECT `framework_modules_authorizations_join_groups`.* FROM `framework_modules_authorizations_join_groups` JOIN `framework_groups` ON `framework_groups`.`id`=`framework_modules_authorizations_join_groups`.`fkGroup` WHERE `fkAuthorization`='".$this->id."' ORDER BY `framework_groups`.`name`");
  foreach($groups_results as $group){
   $this->groups_array[$group->fkGroup]=new cGroup($group->fkGroup);
   $this->groups_level_array[$group->fkGroup]=$group->level;
  }
  return true;
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