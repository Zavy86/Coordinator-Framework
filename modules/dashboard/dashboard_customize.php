<?php
/**
 * Dashboard - Edit
 *
 * @package Coordinator\Modules\Dashboard
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("dashboard_customize"));

 // get objects
 $selected_tile_obj=new cDashboardTile($_REQUEST['idTile']);

 // build table
 $table=new cTable(api_text("dashboard_customize-tr-unvalued"));
 // build table header
 $table->addHeader("&nbsp;",null,16);
 $table->addHeader(api_text("dashboard_customize-th-label"),"nowrap");
 $table->addHeader("&nbsp;","nowrap");
 $table->addHeader(api_text("dashboard_customize-th-url"),null,"100%");
 $table->addHeader("&nbsp;",null,16);
 // build table rows
 $tiles_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__users_dashboards` WHERE `fkUser`='".$GLOBALS['session']->user->id."' ORDER BY `order`");
 foreach($tiles_results as $tile){
  $tile_obj=new cDashboardTile($tile);
  // build operations button
  $ob=new cOperationsButton();
  $ob->addElement("?mod=dashboard&scr=dashboard_customize&act=editTile&idTile=".$tile_obj->id,"fa-pencil",api_text("dashboard_customize-td-edit"));
  $ob->addElement("?mod=dashboard&scr=submit&act=tile_move_up&idTile=".$tile_obj->id,"fa-arrow-up",api_text("dashboard_customize-td-move-up"),($tile_obj->order>1?true:false));
  $ob->addElement("?mod=dashboard&scr=submit&act=tile_move_down&idTile=".$tile_obj->id,"fa-arrow-down",api_text("dashboard_customize-td-move-down"),($tile_obj->order<count($tiles_results)?true:false));
  $ob->addElement("?mod=dashboard&scr=submit&act=tile_remove&idTile=".$tile_obj->id,"fa-trash",api_text("dashboard_customize-td-delete"),true,api_text("dashboard_customize-td-delete-confirm"));
  // check deleted
  if($user_obj->deleted){$tr_class="deleted";}else{$tr_class=null;}
  // build table row
  $table->addRow(($selected_tile_obj->id==$tile_obj->id?"info":null));
  // build table fields
  $table->addRowFieldAction("?mod=dashboard&scr=dashboard_customize&idTile=".$tile_obj->id,api_icon("fa-search",api_text("dashboard_customize-td-preview"),"hidden-link"));
  $table->addRowField($tile_obj->label,"nowrap");
  $table->addRowField(api_icon($tile_obj->icon),"nowrap text-center");
  $table->addRowField($tile_obj->url,"truncate-ellipsis");
  $table->addRowField($ob->render(),"text-right");
 }
 // check for actions
 if(in_array(ACTION,array("addTile","editTile"))){
  // build form
  $tile_form=new cForm("?mod=dashboard&scr=submit&act=tile_save&idTile=".$selected_tile_obj->id,"POST",null,"dashboard_customize_tile");
  $tile_form->addField("hidden","redirect_mod",null,"dashboard");
  $tile_form->addField("hidden","redirect_scr",null,"dashboard_customize");
  $tile_form->addField("text","icon",api_text("dashboard_customize-tile-ff-icon"),$selected_tile_obj->icon,api_text("dashboard_customize-tile-ff-icon-placeholder"));
  $tile_form->addField("text","label",api_text("dashboard_customize-tile-ff-label"),$selected_tile_obj->label,api_text("dashboard_customize-tile-ff-label-placeholder"),null,null,null,"required");
  $tile_form->addField("text","description",api_text("dashboard_customize-tile-ff-description"),$selected_tile_obj->description,api_text("dashboard_customize-tile-ff-description-placeholder"));
  $tile_form->addField("select","size",api_text("dashboard_customize-tile-ff-size"),$selected_tile_obj->size,api_text("dashboard_customize-tile-ff-size-placeholder"),null,null,null,"required");
  for($size_1=1;$size_1<=6;$size_1++){$tile_form->addFieldOption($size_1."x1",$size_1."x1");}
  $tile_form->addField("text","url",api_text("dashboard_customize-tile-ff-url"),$selected_tile_obj->url,api_text("dashboard_customize-tile-ff-url-placeholder"),null,null,null,"required");
  $tile_form->addField("hidden","module",null,$selected_tile_obj->module);
  $tile_form->addField("select","target",api_text("dashboard_customize-tile-ff-target"),$selected_tile_obj->target);
  $tile_form->addFieldOption("",api_text("dashboard_customize-tile-fo-target-standard"));
  $tile_form->addFieldOption("_blank",api_text("dashboard_customize-tile-fo-target-blank"));
  $tile_form->addField("file","background",api_text("dashboard_customize-tile-ff-background"));
  if(file_exists(ROOT."uploads/dashboard/".$selected_tile_obj->id.".jpg")){
   $background_field=api_image(DIR."uploads/dashboard/".$selected_tile_obj->id.".jpg","img-polaroid",128,null,true);
   $background_field.=api_link("?mod=dashboard&scr=submit&act=tile_background_remove&idTile=".$selected_tile_obj->id,api_icon("fa-remove",api_text("dashboard_customize-tile-ff-background-delete"),"hidden-link text-vtop"),null,null,false,api_text("dashboard_customize-tile-ff-background-confirm"));
   $tile_form->addField("static",null,"&nbsp;",$background_field);
  }
  $tile_form->addControl("submit",api_text("form-fc-submit"));
  $tile_form->addControl("button",api_text("form-fc-cancel"),"#",null,null,null,"data-dismiss='modal'");
  if($selected_tile_obj->id){$tile_form->addControl("button",api_text("form-fc-delete"),"?mod=dashboard&scr=submit&act=tile_remove&idTile=".$selected_tile_obj->id,"btn-danger",api_text("form-fc-delete-confirm"));}
  // build group add modal window
  $tile_form_modal=new cModal(api_text("dashboard_customize-tile-modal-title"),null,"dashboard_customize-tile_form-modal");
  $tile_form_modal->setBody($tile_form->render(2));
  // add modal to html object
  $html->addModal($tile_form_modal);
  // jQuery scripts
  $html->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_dashboard_customize-tile_form-modal\").modal('show');});");
  $html->addScript("/* Font Awesome Icon Picker */\n$(function(){\$(\"#form_dashboard_customize_tile_input_icon\").iconpicker();});");
 }
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 // add table to grid
 $grid->addCol($table->render(),"col-xs-12 col-sm-8");
 // check for selected tile
 if($selected_tile_obj->id){
  // build preview dashbaord
  $dashboard=new cDashboard(api_text("dashboard_customize-dashboard-preview"));
  $dashboard->addTile($selected_tile_obj->url,$selected_tile_obj->label,$selected_tile_obj->description,true,$selected_tile_obj->size,$selected_tile_obj->icon,$selected_tile_obj->counter->count,$selected_tile_obj->counter_class,$selected_tile_obj->background,$selected_tile_obj->target);
  // add preview to grid
  $grid->addCol($dashboard->render(),"col-xs-12 col-sm-4");
 }
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($selected_tile_obj,"selected tile");}
?>