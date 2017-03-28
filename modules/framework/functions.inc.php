<?php
/**
 * Framework - Functions
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /** @todo verificare se serve la tree */
 /**
  * Groups tree
  *
  * @param string $idGroup Start group branch
  * @return object $results[] Array of group objects
  */
 function api_framework_groups_tree_OLD($idGroup=NULL,$recursive=TRUE){
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