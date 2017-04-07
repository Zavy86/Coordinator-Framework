<?php
/**
 * Dashboard - Submit
 *
 * @package Coordinator\Modules\Dashboard
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
// check for actions
if(!defined('ACTION')){die("ERROR EXECUTING SCRIPT: The action was not defined");}
// switch action
switch(ACTION){
 // tiles
 case "tile_save":tile_save();break;
 case "tile_move_up":tile_move("up");break;
 case "tile_move_down":tile_move("down");break;
 case "tile_remove":tile_remove();break;
 case "tile_background_remove":tile_background_remove();break;
 // default
 default:
  api_alerts_add(api_text("alert_submitFunctionNotFound",array(MODULE,SCRIPT,ACTION)),"danger");
  api_redirect("?mod=".MODULE);
}

/**
 * Tile salve
 */
function tile_save(){
 // get objects
 $tile_obj=new cDashboardTile($_REQUEST['idTile']);
 // acquire variables
 $r_element=json_decode($_REQUEST['element']);
 $r_redirect_mod=$_REQUEST['redirect_mod'];
 $r_redirect_scr=$_REQUEST['redirect_scr'];
 $r_redirect_tab=$_REQUEST['redirect_tab'];
 // check parameters
 if(!$r_redirect_mod){$r_redirect_mod="dashboard";$r_redirect_scr="dashboard_edit";$r_redirect_tab=NULL;}
 // build tile query object
 $tile_qobj=new stdClass();
 $tile_qobj->id=$tile_obj->id;
 // check for element
 if($r_element->url){
  $tile_qobj->icon=addslashes($r_element->icon);
  $tile_qobj->label=addslashes($r_element->label);
  $tile_qobj->description=addslashes($r_element->description);
  $tile_qobj->size=addslashes($r_element->size);
  $tile_qobj->url=addslashes($r_element->url);
  $tile_qobj->module=addslashes($r_element->module);
 }else{
  $tile_qobj->icon=addslashes($_REQUEST['icon']);
  $tile_qobj->label=addslashes($_REQUEST['label']);
  $tile_qobj->description=addslashes($_REQUEST['description']);
  $tile_qobj->size=addslashes($_REQUEST['size']);
  $tile_qobj->url=addslashes($_REQUEST['url']);
  $tile_qobj->module=addslashes($_REQUEST['module']);
  $tile_qobj->target=addslashes($_REQUEST['target']);
 }
 // debug
 api_dump($_REQUEST,"_REQUEST");
 api_dump($r_element,"element");
 api_dump($tile_obj,"tile object");
 // check query object
 if(!$tile_qobj->url){api_alerts_add(api_text("dashboard_alert_tileError"),"danger");api_redirect("?mod=".$r_redirect_mod."&scr=".$r_redirect_scr."&tab=".$r_redirect_tab);}
 // check for insert or update
 if($tile_qobj->id){
  // debug
  api_dump($tile_qobj,"tile query object");
  // update tile
  $GLOBALS['database']->queryUpdate("framework_users_dashboards",$tile_qobj);
 }else{
  // get maximum position
  $v_order=$GLOBALS['database']->queryCount("framework_users_dashboards","`fkUser`='".$GLOBALS['session']->user->id."'");
  // set new properties
  $tile_qobj->fkUser=$GLOBALS['session']->user->id;
  $tile_qobj->order=($v_order+1);
  // debug
  api_dump($tile_qobj,"tile query object");
  // insert
  $GLOBALS['database']->queryInsert("framework_users_dashboards",$tile_qobj);
  // get last insert id
  $tile_qobj->id=$GLOBALS['database']->lastInsertedId();
 }
 // upload background
 if(intval($_FILES['background']['size'])>0 && $_FILES['background']['error']==UPLOAD_ERR_OK){
  if(file_exists(ROOT."uploads/dashboard/".$tile_obj->id.".jpg")){unlink(ROOT."uploads/dashboard/".$tile_obj->id.".jpg");}
  if(is_uploaded_file($_FILES['background']['tmp_name'])){move_uploaded_file($_FILES['background']['tmp_name'],ROOT."uploads/dashboard/".$tile_obj->id.".jpg");}
 }
 // redirect
 api_redirect("?mod=".$r_redirect_mod."&scr=".$r_redirect_scr."&tab=".$r_redirect_tab."&idTile=".$tile_qobj->id);
}
/**
 * Tile Move
 *
 * @param string direction
 */
function tile_move($direction){
 // get objects
 $tile_obj=new cDashboardTile($_REQUEST['idTile']);
 // check objects
 if(!$tile_obj->id){api_alerts_add(api_text("dashboard_alert_tileNotFound"),"danger");api_redirect("?mod=dashboard&scr=dashboard_edit");}
 // check parameters
 if(!in_array(strtolower($direction),array("up","down"))){api_alerts_add(api_text("dashboard_alert_tileError"),"warning");api_redirect("?mod=dashboard&scr=dashboard_edit&idTile=".$tile_obj->id);}
 // build tile query objects
 $tile_qobj=new stdClass();
 $tile_qobj->id=$tile_obj->id;
 //switch direction
 switch(strtolower($direction)){
  // up -> order -1
  case "up":
   // set previous order
   $tile_qobj->order=$tile_obj->order-1;
   // check for order
   if($tile_qobj->order<1){api_alerts_add(api_text("dashboard_alert_tileError"),"warning");api_redirect("?mod=dashboard&scr=dashboard_edit&idTile=".$tile_obj->id);}
   // update tile
   $GLOBALS['database']->queryUpdate("framework_users_dashboards",$tile_qobj);
   // rebase other tiles
   api_dump($rebase_query="UPDATE `framework_users_dashboards` SET `order`=`order`+'1' WHERE `order`<'".$tile_obj->order."' AND `order`>='".$tile_qobj->order."' AND `order`<>'0' AND `id`!='".$tile_obj->id."' AND `fkUser`='".$GLOBALS['session']->user->id."'","rebase_query");
   $GLOBALS['database']->queryExecute($rebase_query);
   break;
  // down -> order +1
  case "down":
   // set following order
   $tile_qobj->order=$tile_obj->order+1;
   // update tile
   $GLOBALS['database']->queryUpdate("framework_users_dashboards",$tile_qobj);
   // rebase other tiles
   api_dump($rebase_query="UPDATE `framework_users_dashboards` SET `order`=`order`-'1' WHERE `order`>'".$tile_obj->order."' AND `order`<='".$tile_qobj->order."' AND `order`<>'0' AND `id`!='".$tile_obj->id."' AND `fkUser`='".$GLOBALS['session']->user->id."'","rebase_query");
   $GLOBALS['database']->queryExecute($rebase_query);
   break;
 }
 // debug
 api_dump($_REQUEST,"_REQUEST");
 api_dump($direction,"direction");
 api_dump($tile_obj,"tile_obj");
 api_dump($tile_qobj,"tile_qobj");
 // redirect
 api_redirect("?mod=dashboard&scr=dashboard_edit&idTile=".$tile_obj->id);
}
/**
 * Tile Remove
 */
function tile_remove(){
 // get objects
 $tile_obj=new cDashboardTile($_REQUEST['idTile']);
 // check objects
 if(!$tile_obj->id){api_alerts_add(api_text("dashboard_alert_tileNotFound"),"danger");api_redirect("?mod=dashboard&scr=dashboard_edit");}
 // acquire variables
 $r_redirect_mod=$_REQUEST['redirect_mod'];
 $r_redirect_scr=$_REQUEST['redirect_scr'];
 $r_redirect_tab=$_REQUEST['redirect_tab'];
 // check parameters
 if(!$r_redirect_mod){$r_redirect_mod="dashboard";$r_redirect_scr="dashboard_edit";$r_redirect_tab=NULL;}
 // debug
 api_dump($tile_obj);
 // remove tile
 $GLOBALS['database']->queryDelete("framework_users_dashboards",$tile_obj->id);
 // rebase other tiles
 $GLOBALS['database']->queryExecute("UPDATE `framework_users_dashboards` SET `order`=`order`-1 WHERE `order`>'".$tile_obj->order."' AND `fkUser`='".$GLOBALS['session']->user->id."'");
 // remove background if exist
 if(file_exists(ROOT."uploads/dashboard/".$tile_obj->id.".jpg")){unlink(ROOT."uploads/dashboard/".$tile_obj->id.".jpg");}
 // redirect
 api_redirect("?mod=".$r_redirect_mod."&scr=".$r_redirect_scr."&tab=".$r_redirect_tab."&idTile=".$tile_obj->id);
}
/**
 * Tile Background Remove
 */
function tile_background_remove(){
 // get objects
 $tile_obj=new cDashboardTile($_REQUEST['idTile']);
 // debug
 api_dump($tile_obj);
 // check objects
 if(!$tile_obj->id){api_alerts_add(api_text("dashboard_alert_tileNotFound"),"danger");api_redirect("?mod=dashboard&scr=dashboard_edit");}
 // remove background if exist
 if(file_exists(ROOT."uploads/dashboard/".$tile_obj->id.".jpg")){unlink(ROOT."uploads/dashboard/".$tile_obj->id.".jpg");}
 // redirect
 api_redirect("?mod=dashboard&scr=dashboard_edit&idTile=".$tile_obj->id);
}

?>