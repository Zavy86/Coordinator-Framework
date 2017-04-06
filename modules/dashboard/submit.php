<?php
/**
 * Dashboards - Submit
 *
 * @package Coordinator\Modules\Dashboards
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
// check for actions
if(!defined('ACTION')){die("ERROR EXECUTING SCRIPT: The action was not defined");}
// switch action
switch(ACTION){

 /** @todo check authorization in all submits function */

 // tiles
 case "tile_save":tile_save();break;

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
 $tile_obj=new stdClass();
 $tile_obj->id=$_REQUEST['idTile'];
 // acquire variables
 $r_element=json_decode($_REQUEST['element']);
 $r_redirect_mod=$_REQUEST['redirect_mod'];
 $r_redirect_scr=$_REQUEST['redirect_scr'];
 $r_redirect_tab=$_REQUEST['redirect_tab'];
 // check parameters
 if(!$r_redirect_mod){$r_redirect_mod="dashboard";$r_redirect_scr="dashboard_view";$r_redirect_tab=NULL;}
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
  //$GLOBALS['database']->queryUpdate("framework_users_dashboards",$tile_qobj);
 }else{
  // get maximum position
  $v_order=$GLOBALS['database']->queryCount("framework_users_dashboard","`fkUser`='".$GLOBALS['session']->user->id."'");
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
 /*if(intval($_FILES['background']['size'])>0 && $_FILES['background']['error']==UPLOAD_ERR_OK){
  if(file_exists("../uploads/uploads/dashboard/".$tile_obj->id.".jpg")){unlink("../uploads/uploads/dashboard/".$tile_obj->id.".jpg");}
  if(is_uploaded_file($_FILES['background']['tmp_name'])){move_uploaded_file($_FILES['background']['tmp_name'],"../uploads/uploads/dashboard/".$tile_obj->id.".jpg");}
 }*/
 // redirect
 api_alerts_add(api_text("dashboard_alert_tileSaved"),"success");
 api_redirect("?mod=".$r_redirect_mod."&scr=".$r_redirect_scr."&tab=".$r_redirect_tab);
}
/**
 * Tile delete
 */
function tile_delete(){
 die();
 // acquire variables
 $tile_obj->id=$_GET['idTile'];
 $g_redirect=$_GET['redirect'];
 if(!$g_redirect){$g_redirect="dashboard_edit.php";}
 // get tile position
 $order=$GLOBALS['db']->queryUniqueValue("SELECT `order` FROM `settings_dashboards` WHERE `id`='".$tile_obj->id."'");
 if($order>0){
  // remove tile
  echo $GLOBALS['db']->execute("DELETE FROM `settings_dashboards` WHERE `id`='".$tile_obj->id."'");
  // moves back tiles located after
  echo $GLOBALS['db']->execute("UPDATE `settings_dashboards` SET `order`=`order`-1 WHERE `order`>'".$order."' AND `idAccount`='".api_account()->id."'");
  // delete background
  if(file_exists("../uploads/uploads/dashboard/".$tile_obj->id.".jpg")){unlink("../uploads/uploads/dashboard/".$tile_obj->id.".jpg");}
 }
 //redirect
 exit(header("location: ".$g_redirect));
}
/**
 * Tile background delete
 */
function tile_background_delete(){
 die();
 // acquire variables
 $tile_obj->id=$_GET['idTile'];
 // delete background
 if(file_exists("../uploads/uploads/dashboard/".$tile_obj->id.".jpg")){unlink("../uploads/uploads/dashboard/".$tile_obj->id.".jpg");}
 // alert and redirect
 $alert="?alert=tileBackgroundDeleted&alert_class=alert-warning";
 exit(header("location: dashboard_edit.php".$alert));
}

?>