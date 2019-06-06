<?php
/**
 * Dashboard Functions
 *
 * @package Coordinator\Modules\Dashboard
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 // include classes
 require_once(DIR."modules/dashboard/classes/cDashboardTile.class.php");

 /**
  * Dashboard - User Tiles
  *
  * @param integer $user User object or ID
  * @return object[]|boolean Array of available tiles or false
  */
 function api_dashboard_userTiles($user=null){
  // check parameters
  if(!$user){$user=$GLOBALS['session']->user;}
  // definitions
  $tiles_array=array();
  // get user
  $user_obj=new cUser($user);
  // check user
  if(!$user_obj->id){return false;}
  // get user tiles
  $tiles_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__users__dashboards` WHERE `fkUser`='".$user_obj->id."' ORDER BY `order`");
  foreach($tiles_results as $tile_fobj){$tiles_array[$tile_fobj->id]=new cDashboardTile($tile_fobj);}
  // return
  return $tiles_array;
 }

?>