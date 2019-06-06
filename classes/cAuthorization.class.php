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

  /** Properties */
  protected $id;
  protected $fkModule;
  protected $name;
  protected $description;
  protected $groups_array;
  protected $groups_level_array;

  /**
   * Authorization class
   *
   * @param integer $authorization Authorization object or ID
   * @return boolean
   */
  public function __construct($authorization){
   // get object
   if(is_int($authorization)){$authorization=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__modules__authorizations` WHERE `id`='".$authorization."'");}
   if(!$authorization->id){return false;}
   // set properties
   $this->id=stripslashes($authorization->id);
   $this->fkModule=stripslashes($authorization->fkModule);
   // make name and description
   $this->name=api_text($authorization->id);
   $this->description=api_text($authorization->id."-description");
   // get groups
   $this->groups_array=array();
   $this->groups_level_array=array();
   // get authorized groups
   $groups_results=$GLOBALS['database']->queryObjects("SELECT `framework__modules__authorizations__groups`.* FROM `framework__modules__authorizations__groups` JOIN `framework__groups` ON `framework__groups`.`id`=`framework__modules__authorizations__groups`.`fkGroup` WHERE `fkAuthorization`='".$this->id."' ORDER BY `framework__groups`.`name`");
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