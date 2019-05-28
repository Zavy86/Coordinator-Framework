<?php
/**
 * User
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * User class
 */
class cUser{

 /** Properties */
 protected $id;
 protected $mail;
 protected $username;
 protected $firstname;
 protected $lastname;
 protected $fullname;
 protected $localization;
 protected $timezone;
 protected $gender;
 protected $birthday;
 protected $avatar;
 protected $enabled;
 protected $superuser;
 protected $level;
 protected $addTimestamp;
 protected $addFkUser;
 protected $updTimestamp;
 protected $updFkUser;
 protected $lsaTimestamp;
 protected $pwdExpiration;
 protected $pwdExpired;
 protected $deleted;
 protected $authorizations_array;

 /**
  * Debug
  *
  * @return object User object
  */
 public function debug(){return $this;}

 /**
  * User class
  *
  * @param integer $user User object or ID
  * @return boolean
  */
 public function __construct($user,$loadAuthorizations=false){
  // get object
  if(is_numeric($user)){$user=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__users` WHERE `id`='".$user."'",$GLOBALS['debug']);}
  if(!$user->id){return false;}
  // set properties
  $this->id=(int)$user->id;
  $this->mail=stripslashes($user->mail);
  $this->username=stripslashes($user->username);
  $this->firstname=stripslashes($user->firstname);
  $this->lastname=stripslashes($user->lastname);
  $this->fullname=$this->lastname." ".$this->firstname;
  $this->localization=$user->localization;
  $this->timezone=$user->timezone;
  $this->gender=$user->gender;
  $this->birthday=$user->birthday;
  $this->avatar=DIR."uploads/framework/users/avatar_".$this->id.".jpg";
  $this->enabled=(bool)$user->enabled;
  $this->superuser=(bool)$user->superuser;
  $this->level=(int)$user->level;
  $this->addTimestamp=(int)$user->addTimestamp;
  $this->addFkUser=(int)$user->addFkUser;
  $this->updTimestamp=(int)$user->updTimestamp;
  $this->updFkUser=(int)$user->updFkUser;
  $this->lsaTimestamp=(int)$user->lsaTimestamp;
  $this->deleted=(bool)$user->deleted;
  // check for password expiration
  if($GLOBALS['settings']->users_password_expiration>-1){
   $this->pwdExpiration=$GLOBALS['settings']->users_password_expiration-(time()-$user->pwdTimestamp);
   if($this->pwdExpiration<0){$this->pwdExpired=true;}
  }
  // make avatar
  if(!file_exists(str_replace("//","/",ROOT.str_replace(DIR,"/",$this->avatar)))){
   switch($this->gender){
    case "man":$this->avatar=DIR."uploads/framework/users/avatar_man.jpg";break;
    case "woman":$this->avatar=DIR."uploads/framework/users/avatar_woman.jpg";break;
    default:$this->avatar=DIR."uploads/framework/users/avatar.jpg";
   }
  }
  // load authorizations
  if($loadAuthorizations){$this->authorizations_array=$this->loadAuthorizations();}
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
  * Get Status
  *
  * @param boolean $showIcon show icon
  * @param boolean $showText show text
  * @return string status text and icon
  */
 public function getStatus($showIcon=true,$showText=true){
  if($this->deleted){
   $icon=api_icon("fa-trash",api_text("user-status-deleted"));
   $text=api_text("user-status-deleted");
  }else{
   if($this->enabled){
    if($this->superuser){
     $icon=api_icon("fa-diamond",api_text("user-status-superuser"));
     $text=api_text("user-status-superuser");
    }else{
     $icon=api_icon("fa-check",api_text("user-status-enabled"));
     $text=api_text("user-status-enabled");
    }
   }else{
    $icon=api_icon("fa-remove",api_text("user-status-disabled"));
    $text=api_text("user-status-disabled");
   }
  }
  // return
  if($showIcon){if($showText){$return.=$icon." ".$text;}else{$return=$icon;}}else{$return=$text;}
  return $return;
 }

 /**
  * Get Gender
  *
  * @param boolean $showIcon show icon
  * @param boolean $showText show text
  * @return string gender text and icon
  */
 public function getGender($showIcon=true,$showText=true){
  // switch gender
  switch($this->gender){
   case "man":$icon=api_icon("fa-male",api_text("user-gender-man"));$text=api_text("user-gender-man");break;
   case "woman":$icon=api_icon("fa-female",api_text("user-gender-woman"));$text=api_text("user-gender-woman");break;
   default:return null;
  }
  // return
  if($showIcon){if($showText){$return.=$icon." ".$text;}else{$return=$icon;}}else{$return=$text;}
  return $return;
 }

 /**
  * Get Assigned Groups
  *
  * @return array Array of groups assigned to user (key is group id)
  */
 public function getAssignedGroups(){
  // definitions
  $groups_array=array();
  // get groups
  //$groups_results=$GLOBALS['database']->queryObjects("SELECT `framework__users__groups`.* FROM `framework__users__groups` LEFT JOIN `framework__groups` ON `framework__groups`.`id`=`framework__users__groups`.`fkGroup` WHERE `framework__users__groups`.`fkUser`='".$this->id."' ORDER BY `framework__users__groups`.`main` DESC,`framework__groups`.`name` ASC",$GLOBALS['debug']);
  $groups_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__users__groups` WHERE `fkUser`='".$this->id."' ORDER BY `main` DESC",$GLOBALS['debug']);
  foreach($groups_results as $result_f){
   $group=new stdClass();
   $group->id=$result_f->fkGroup;
   $group->main=$result_f->main;
   $groups_array[$group->id]=$group;
  }
  // return
  return $groups_array;
 }

 /**
  * Get Main Group
  *
  * @return integer id of main group
  */
 public function getMainGroup(){
  return $GLOBALS['database']->queryUniqueValue("SELECT `fkGroup` FROM `framework__users__groups` WHERE `fkUser`='".$this->id."' AND `main`='1'",$GLOBALS['debug']);
 }

 /**
  * Load Authorization
  *
  * @return authorizations array
  */
 private function loadAuthorizations(){
  // definitions
  $return=array();
  $groups_array=array();
  $groups_recursive_array=array();
  // cycle all user groups
  foreach(array_keys($this->getAssignedGroups()) as $group_f){
   // get group object
   $group_obj=new cGroup($group_f);
   $groups_array[$group_obj->id]=$group_obj->id;
   // get recursive groups
   $fkGroup=$group_obj->fkGroup;
   while($fkGroup){
    $group=$GLOBALS['database']->queryUniqueObject("SELECT `id`,`fkGroup` FROM `framework__groups` WHERE `id`='".$fkGroup."'");
    $groups_recursive_array[$group->id]=$group->id;
    $fkGroup=$group->fkGroup;
   }
  }
  // remove deuplicated keys from recursive
  foreach($groups_recursive_array as $group){if(array_key_exists($group,$groups_array)){unset($groups_recursive_array[$group]);}}
  // make groups query where
  foreach($groups_array as $group){$authorizations_groups_where.="`framework__modules__authorizations__groups`.`fkGroup`='".$group."' OR ";}
  // make authorization query
  $authorizations_query="SELECT `framework__modules__authorizations`.`id`,`framework__modules__authorizations`.`module`,`framework__modules__authorizations`.`action`
   FROM `framework__modules__authorizations__groups`
   JOIN `framework__modules__authorizations` ON `framework__modules__authorizations`.`id`=`framework__modules__authorizations__groups`.`fkAuthorization`
   WHERE `framework__modules__authorizations__groups`.`level`<='".$this->level."' AND
    ( ".substr($authorizations_groups_where,0,-4)." )
   GROUP BY `framework__modules__authorizations`.`id`";
  // get authorizations
  $authorizations_results=$GLOBALS['database']->queryObjects($authorizations_query);
  foreach($authorizations_results as $authorization){$return[$authorization->module][$authorization->action]="authorized";}
  // check for recursive groups
  if(count($groups_recursive_array)){
   // make recursive groups query where
   foreach($groups_recursive_array as $group){$authorizations_groups_recursive_where.="`framework__modules__authorizations__groups`.`fkGroup`='".$group."' OR ";}
   // make inherited authorizations query
   $authorizations_query="SELECT `framework__modules__authorizations`.`id`,`framework__modules__authorizations`.`module`,`framework__modules__authorizations`.`action`,'1' as `inherited`
    FROM `framework__modules__authorizations__groups`
    JOIN `framework__modules__authorizations` ON `framework__modules__authorizations`.`id`=`framework__modules__authorizations__groups`.`fkAuthorization`
    WHERE `framework__modules__authorizations__groups`.`level`<='".$this->level."' AND
     ( ".substr($authorizations_groups_recursive_where,0,-4)." )
    GROUP BY `framework__modules__authorizations`.`id`";
   // get inherited authorizations
   $authorizations_recursive_results=$GLOBALS['database']->queryObjects($authorizations_query);
   // merge authorizations
   foreach($authorizations_recursive_results as $authorization){
    if(!array_key_exists($authorization->action,$return[$authorization->module])){
     $return[$authorization->module][$authorization->action]="inherited";
    }
   }
  }
  return $return;
 }

}
?>