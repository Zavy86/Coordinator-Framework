<?php
/**
 * Framework - Functions
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Modules
  *
  * @return object $return[] Array of module objects
  */
 function api_framework_modules(){
  // definitions
  $return=array();
  // execute query
  $return["framework"]=new Module("framework");
  $modules_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_modules` WHERE `module`!='framework' ORDER BY `module`");
  foreach($modules_results as $module){$return[$module->module]=new Module($module);}
  // return modules
  return $return;
 }

 /**
  * Menus
  *
  * @param string $idMenu Start menu branch
  * @return object $return[] Array of menu objects
  */
 function api_framework_menus($idMenu=NULL){
  // definitions
  $return=array();
  // query where
  if(!$idMenu){$query_where="`fkMenu` IS NULL";}else{$query_where="`fkMenu`='".$idMenu."'";}
  // execute query
  $menus_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_menus` WHERE ".$query_where." ORDER BY `order` ASC");
  foreach($menus_results as $menu){$return[$menu->id]=new Menu($menu);}
  // return menus
  return $return;
 }

 /**
  * Groups
  *
  * @param string $idGroup Start group branch
  * @return object $return[] Array of group objects
  */
 function api_framework_groups($idGroup=NULL){
  // definitions
  $return=array();
  // query where
  if(!$idGroup){$query_where="`fkGroup` IS NULL";}else{$query_where="`fkGroup`='".$idGroup."'";}
  // execute query
  $groups_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_groups` WHERE ".$query_where." ORDER BY `name` ASC");
  foreach($groups_results as $group){$return[$group->id]=new Group($group);}
  // return groups
  return $return;
 }






 /** @todo verificare se serve la tree */
 /**
  * Groups tree
  *
  * @param string $idGroup Start group branch
  * @return object $results[] Array of group objects
  */
 function api_framework_groups_tree($idGroup=NULL,$recursive=TRUE){
  // definitions
  $tree_array=array();
  // query where
  if(!$idGroup){$query_where="`fkGroup` IS NULL";}else{$query_where="`fkGroup`='".$idGroup."'";}
  // execute query
  $groups_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_groups` WHERE ".$query_where." ORDER BY `name` ASC");
  foreach($groups_results as $group){
   $tree_array[$group->id]=$group;
   if($recursive){$tree_array[$group->id]->subGroups=api_groups_tree($group->id);}
  }
  // return categories tree
  return $tree_array;
 }


?>