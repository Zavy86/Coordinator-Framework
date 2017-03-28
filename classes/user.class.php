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
 *
 * @todo check phpdoc
 */
class User{
 /** @var string $settings_array[] User array */
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
 public function __construct($user){
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
   $this->groups_array[$group->fkGroup]=new Group($group->fkGroup);
   if($group->main){$this->groups_main=$group->fkGroup;}
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
   case "id":return $this->id;
   case "mail":return $this->mail;
   case "firstname":return $this->firstname;
   case "lastname":return $this->lastname;
   case "fullname":return $this->fullname;
   case "localization":return $this->localization;
   case "timezone":return $this->timezone;
   case "avatar":return $this->avatar;
   case "enabled":return $this->enabled;
   case "addTimestamp":return $this->addTimestamp;
   case "pwdExpiration":return $this->pwdExpiration;
   case "pwdExpired":return $this->pwdExpired;
   case "deleted":return $this->deleted;
   case "groups_main":return $this->groups_main;
   case "groups_array":return $this->groups_array;
   default:return FALSE;
  }*/
  return $this->$property;
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

}
?>