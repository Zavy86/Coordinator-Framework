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
 protected $addTimestamp; /** @todo teniamo cosi? */
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
  if(is_numeric($group)){$group=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework_groups` WHERE `id`='".$group."'",$GLOBALS['debug']);}
  if(!$group->id){return FALSE;}
  // set properties
  $this->id=(int)$group->id;
  $this->fkGroup=$group->fkGroup;
  $this->name=stripslashes($group->name);
  $this->description=stripslashes($group->description);
  $this->addTimestamp=$group->addTimestamp;
  $this->addFkUser=$group->addFkUser;
  $this->updTimestamp=$group->updTimestamp;
  $this->updFkUser=$group->updFkUser;
  $this->deleted=(bool)$group->deleted;
  // make fullname
  $this->fullname=$group->name;
  if($this->description){$this->fullname.=" - ".$group->description;}
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
  switch($property){
   case "id":return $this->id;
   case "fkGroup":return $this->fkGroup;
   case "name":return $this->name;
   case "description":return $this->description;
   case "fullname":return $this->fullname;
   case "addTimestamp":return $this->addTimestamp;
   case "addFkUser":return $this->addFkUser;
   case "updTimestamp":return $this->updTimestamp;
   case "updFkUser":return $this->updFkUser;
   case "deleted":return $this->deleted;
   default:return FALSE;
  }
 }

}
?>