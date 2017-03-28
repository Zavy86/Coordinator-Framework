<?php
/**
 * authorization
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * authorization class
 *
 * @todo check phpdoc
 */
class authorization{
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
  * authorization class
  *
  * @param integer $authorization authorization object or ID
  * @return boolean
  */
 public function __construct($authorization){
  // get object
  if(is_int($authorization)){$authorization=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework_modules_authorizations` WHERE `id`='".$authorization."'");}
  if(!$authorization->id){return FALSE;}
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
  $groups_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_modules_authorizations_join_groups` WHERE `fkAuthorization`='".$this->id."'"); /** @todo in che ordine?? ORDER BY `name` */
  foreach($groups_results as $group){
   $this->groups_array[$group->fkGroup]=new Group($group->fkGroup);
   $this->groups_level_array[$group->fkGroup]=$group->level;
  }
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
   case "authorization":return $this->authorization;
   default:return FALSE;
  }*/
  return $this->$property;
 }

}
?>