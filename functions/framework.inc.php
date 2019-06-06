<?php
/**
 * Framework Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Modules
  *
  * @return object $return[] Array of module objects
  */
 function api_framework_modules(){  /** @todo levare framework? */
  // definitions
  $return=array();
  // execute query
  $return["framework"]=new cModule("framework");
  $modules_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__modules` WHERE `module`!='framework' ORDER BY `module`");
  foreach($modules_results as $module){$return[$module->id]=new cModule($module);}
  // return modules
  return $return;
 }

 /**
  * Menus
  *
  * @param string $idMenu Start menu branch
  * @return object $return[] Array of menu objects
  */
 function api_framework_menus($idMenu=null){  /** @todo levare framework? */
  // definitions
  $return=array();
  // query where
  if(!$idMenu){$query_where="`fkMenu` IS null";}else{$query_where="`fkMenu`='".$idMenu."'";}
  // execute query
  $menus_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__menus` WHERE ".$query_where." ORDER BY `order` ASC");
  foreach($menus_results as $menu){$return[$menu->id]=new cMenu($menu);}
  // return menus
  return $return;
 }

 /**
  * Users
  *
  * @param boolean $disabled Show disabled users
  * @param boolean $deleted Show deleted users
  * @return object $return[] Array of user objects
  */
 function api_framework_users($disabled=false,$deleted=false){  /** @todo levare framework? */
  // definitions
  $return=array();
  // query where
  $query_where="1";
  if(!$disabled){$query_where.=" AND `enabled`='1'";}
  if(!$deleted){$query_where.=" AND `deleted`='0'";}
  // execute query
  $users_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__users` WHERE ".$query_where." ORDER BY `lastname` ASC,`firstname` ASC");
  foreach($users_results as $user){$return[$user->id]=new cUser($user);}
  // return groups
  return $return;
 }

 /**
  * Groups
  *
  * @param string $idGroup Start group branch
  * @return object $return[] Array of group objects
  */
 function api_framework_groups($idGroup=null){  /** @todo levare framework? */
  // definitions
  $return=array();
  // query where
  if(!$idGroup){$query_where="`fkGroup` IS null";}else{$query_where="`fkGroup`='".$idGroup."'";}
  // execute query
  $groups_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__groups` WHERE ".$query_where." ORDER BY `name` ASC");
  foreach($groups_results as $group){$return[$group->id]=new cGroup($group);}
  // return groups
  return $return;
 }

 /**
  * Authorizations
  *
  * @param string $module Module authorizations
  * @return object $return Array of authorization objects
  */
 function api_framework_authorizations($module=null){  /** @todo levare framework? */
  // definitions
  $return=array();
  // query where
  if($module){$query_where="`fkModule`='".$module."'";}else{$query_where="1";}
  // execute query
  $authorizations_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__modules__authorizations` WHERE ".$query_where." ORDER BY `order`");
  foreach($authorizations_results as $authorization){$return[$authorization->id]=new cAuthorization($authorization);}
  // return groups
  return $return;
 }

  /**                          /** @todo spostare nelle functions
   * Count all Sessions
   */
  /*public function countAllSessions(){
   return $GLOBALS['database']->queryUniqueValue("SELECT COUNT(`id`) FROM `framework__sessions`");
  }*/

  /**
   * Count Online Users
   */
  /*public function countOnlineUsers(){
   return $GLOBALS['database']->queryUniqueValue("SELECT COUNT(DISTINCT(`fkUser`)) FROM `framework__sessions`");
  }*/

?>