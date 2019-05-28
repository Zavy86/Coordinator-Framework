<?php
/**
 * Group
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Group class
 */
class cGroup{

 /** Properties */
 protected $id;
 protected $fkGroup;
 protected $name;
 protected $description;
 protected $fullname;
 protected $addTimestamp;
 protected $addFkUser;
 protected $updTimestamp;
 protected $updFkUser;
 protected $deleted;

 /**
  * Debug
  *
  * @return object Group object
  */
 public function debug(){return $this;}

 /**
  * Group class
  *
  * @param integer $group Group object or ID
  * @return boolean
  */
 public function __construct($group){
  // get object
  if(is_numeric($group)){$group=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__groups` WHERE `id`='".$group."'",$GLOBALS['debug']);}
  if(!$group->id){return false;}
  // set properties
  $this->id=(int)$group->id;
  $this->fkGroup=(int)$group->fkGroup;
  $this->name=stripslashes($group->name);
  $this->description=stripslashes($group->description);
  $this->addTimestamp=(int)$group->addTimestamp;
  $this->addFkUser=(int)$group->addFkUser;
  $this->updTimestamp=(int)$group->updTimestamp;
  $this->updFkUser=(int)$group->updFkUser;
  $this->deleted=(bool)$group->deleted;
  // make fullname
  $this->fullname=$group->name;
  if($this->description){$this->fullname.=" - ".$group->description;}
  return true;
 }

 /**
  * Get
  *
  * @param string $property Property name
  * @return string Property value
  */
 public function __get($property){return $this->$property;}

 /**
  * Get Assigned Users
  *
  * @return array Array of users assigned to user (key is user id)
  */
 public function getAssignedUsers(){
  // definitions
  $users_array=array();
  // get users
  $users_results=$GLOBALS['database']->queryObjects("SELECT `framework__users__groups`.* FROM `framework__users__groups` LEFT JOIN `framework__users` ON `framework__users`.`id`=`framework__users__groups`.`fkUser` WHERE `framework__users__groups`.`fkGroup`='".$this->id."' ORDER BY `framework__users`.`level` ASC,`framework__users`.`lastname` ASC,`framework__users`.`firstname` ASC",$GLOBALS['debug']);
  foreach($users_results as $result_f){
   $user=new stdClass();
   $user->id=$result_f->fkUser;
   $user->main=$result_f->main;
   $users_array[$user->id]=$user;
  }
  // return
  return $users_array;
 }

 /**
  * Get Path
  *
  * @param string $modality Return modality [array|string]
  * @return mixed Group path array, string or false
  */
 public function getPath($modality){
  // check parameters
  if(!in_array($modality,array("array","string"))){return false;}
  // definitions
  $groups_array=array($this->id=>$this->name);
  $fkGroup=$this->fkGroup;
  // cycle all parent
  while($fkGroup){
   $group=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__groups` WHERE `id`='".$fkGroup."'",$GLOBALS['debug']);
   $groups_array[$group->id]=$group->name;
   $fkGroup=$group->fkGroup;
  }
  // switch modality
  switch($modality){
   case "array":$return=array_reverse($groups_array,true);break;
   case "string":$return=implode(" &rarr; ",array_reverse($groups_array));break;
  }
  // return
  return $return;
 }

}
?>