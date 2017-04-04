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
 protected $pwdExpiration;
 protected $pwdExpired;
 protected $deleted;
 protected $groups_main;
 protected $groups_array;
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
 public function __construct($user,$loadAuthorizations=FALSE){
  // get object
  if(is_numeric($user)){$user=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework_users` WHERE `id`='".$user."'",$GLOBALS['debug']);}
  if(!$user->id){return FALSE;}
  // set properties
  $this->id=(int)$user->id;
  $this->mail=stripslashes($user->mail);
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
  $this->deleted=(bool)$user->deleted;
  // make avatar
  if(!file_exists(ROOT.str_replace(DIR,"",$this->avatar))){
   switch($this->gender){
    case "man":$this->avatar=DIR."uploads/framework/users/avatar_man.jpg";break;
    case "woman":$this->avatar=DIR."uploads/framework/users/avatar_woman.jpg";break;
    default:$this->avatar=DIR."uploads/framework/users/avatar.jpg";
   }
  }
  /** @todo check for password expiration */
  if($GLOBALS['settings']->users_password_expiration>-1){
   $this->pwdExpiration=$GLOBALS['settings']->users_password_expiration-(time()-$user->pwdTimestamp);
   if($this->pwdExpiration<0){$this->pwdExpired=TRUE;}
  }
  // get user groups
  $this->groups_array=array();
  $groups_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_users_join_groups` WHERE `fkUser`='".$user->id."' ORDER BY `main` DESC",$GLOBALS['debug']);
  foreach($groups_results as $group){
   $this->groups_array[$group->fkGroup]=new cGroup($group->fkGroup);
   if($group->main){$this->groups_main=$group->fkGroup;}
  }
  // load authorizations
  if($loadAuthorizations){$this->authorizations_array=$this->loadAuthorizations();}
  return TRUE;
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
 public function getStatus($showIcon=TRUE,$showText=TRUE){
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
 public function getGender($showIcon=TRUE,$showText=TRUE){
  // switch gender
  switch($this->gender){
   case "man":$icon=api_icon("fa-male",api_text("user-gender-man"));$text=api_text("user-gender-man");break;
   case "woman":$icon=api_icon("fa-female",api_text("user-gender-woman"));$text=api_text("user-gender-woman");break;
   default:return NULL;
  }
  // return
  if($showIcon){if($showText){$return.=$icon." ".$text;}else{$return=$icon;}}else{$return=$text;}
  return $return;
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
  foreach(array_keys($this->groups_array) as $idGroup){
   $groups_array[$idGroup]=$idGroup;
   // get recursive groups
   $fkGroup=$this->groups_array[$idGroup]->fkGroup;
   while($fkGroup){
    $group=$GLOBALS['database']->queryUniqueObject("SELECT `id`,`fkGroup` FROM `framework_groups` WHERE `id`='".$fkGroup."'");
    $groups_recursive_array[$group->id]=$group->id;
    $fkGroup=$group->fkGroup;
   }
  }
  // remove deuplicated keys from recursive
  foreach($groups_recursive_array as $group){if(array_key_exists($group,$groups_array)){unset($groups_recursive_array[$group]);}}
  // make groups query where
  foreach($groups_array as $group){$authorizations_groups_where.="`framework_modules_authorizations_join_groups`.`fkGroup`='".$group."' OR ";}
  // make authorization query
  $authorizations_query="SELECT `framework_modules_authorizations`.`id`,`framework_modules_authorizations`.`module`,`framework_modules_authorizations`.`action`
   FROM `framework_modules_authorizations_join_groups`
   JOIN `framework_modules_authorizations` ON `framework_modules_authorizations`.`id`=`framework_modules_authorizations_join_groups`.`fkAuthorization`
   WHERE `framework_modules_authorizations_join_groups`.`level`<='".$this->level."' AND
    ( ".substr($authorizations_groups_where,0,-4)." )
   GROUP BY `framework_modules_authorizations`.`id`";
  // get authorizations
  $authorizations_results=$GLOBALS['database']->queryObjects($authorizations_query);
  foreach($authorizations_results as $authorization){$return[$authorization->module][$authorization->action]="authorized";}
  // check for recursive groups
  if(count($groups_recursive_array)){
   // make recursive groups query where
   foreach($groups_recursive_array as $group){$authorizations_groups_recursive_where.="`framework_modules_authorizations_join_groups`.`fkGroup`='".$group."' OR ";}
   // make inherited authorizations query
   $authorizations_query="SELECT `framework_modules_authorizations`.`id`,`framework_modules_authorizations`.`module`,`framework_modules_authorizations`.`action`,'1' as `inherited`
    FROM `framework_modules_authorizations_join_groups`
    JOIN `framework_modules_authorizations` ON `framework_modules_authorizations`.`id`=`framework_modules_authorizations_join_groups`.`fkAuthorization`
    WHERE `framework_modules_authorizations_join_groups`.`level`<='".$this->level."' AND
     ( ".substr($authorizations_groups_recursive_where,0,-4)." )
    GROUP BY `framework_modules_authorizations`.`id`";
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