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
 protected $avatar;
 protected $enabled;
 protected $addTimestamp; /** @todo ? teniamo cosi? */
 protected $pwdExpired;
 protected $deleted;

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
  if(is_numeric($user)){$user=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `accounts_users` WHERE `id`='".$user."'",$GLOBALS['debug']);}
  if(!$user->id){return FALSE;}
  // set properties
  $this->id=(int)$user->id;
  $this->mail=stripslashes($user->mail);
  $this->firstname=stripslashes($user->firstname);
  $this->lastname=stripslashes($user->lastname);
  $this->fullname=$this->lastname." ".$this->firstname;
  $this->localization=$user->localization;
  $this->timezone=$user->timezone;
  $this->avatar=DIR."uploads/accounts/users/avatar_".$this->id.".jpg";
  $this->enabled=(bool)$user->enabled;
  $this->addTimestamp=$user->addTimestamp;
  $this->deleted=(bool)$user->deleted;
  // check avatar
  if(!file_exists(ROOT.str_replace(DIR,"",$this->avatar))){$this->avatar=DIR."uploads/accounts/users/avatar.jpg";}
  /** @todo check for password expiration */
  $this->pwdExpired=FALSE;
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
   case "mail":return $this->mail;
   case "firstname":return $this->firstname;
   case "lastname":return $this->lastname;
   case "fullname":return $this->fullname;
   case "localization":return $this->localization;
   case "timezone":return $this->timezone;
   case "avatar":return $this->avatar;
   case "enabled":return $this->enabled;
   case "addTimestamp":return $this->addTimestamp;
   case "pwdExpired":return $this->pwdExpired;
   case "deleted":return $this->deleted;
   default:return FALSE;
  }
 }

}
?>