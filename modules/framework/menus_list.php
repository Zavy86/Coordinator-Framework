<?php
/**
 * Framework - Menus List
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // check authorizations
 api_checkAuthorization("framework-menus_manage","dashboard");
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set application title
 $app->setTitle(api_text("menus_list"));
 // build grid object
 $table=new strTable(api_text("menus_list-tr-unvalued"));
 $table->addHeader(api_text("menus_list-th-label"),"nowrap");
 $table->addHeader(api_text("menus_list-th-title"),null,"100%");
 $table->addHeader("&nbsp;",null,16);
 // get menus tree array
 $menus_array=array();
 api_tree_to_array($menus_array,"api_availableMenus","id");
 // cycle all menus
 foreach($menus_array as $menu){
  // check selected
  if($menu->id==$_REQUEST['idMenu']){$tr_class="info currentrow";}else{$tr_class=null;}
  // check nesting
  if($menu->nesting>1){$tr_class.=" warning";$nesting_alert=true;}
  // build operation button
  $ob=new strOperationsButton();
  $ob->addElement("?mod=".MODULE."&scr=submit&act=menu_move_left&idMenu=".$menu->id,"fa-arrow-left",api_text("menus_list-td-move-left"),($menu->fkMenu?true:false));
  $ob->addElement("?mod=".MODULE."&scr=submit&act=menu_move_up&idMenu=".$menu->id,"fa-arrow-up",api_text("menus_list-td-move-up"),($menu->order>1?true:false));
  $ob->addElement("?mod=".MODULE."&scr=submit&act=menu_move_down&idMenu=".$menu->id,"fa-arrow-down",api_text("menus_list-td-move-down"),(!$menu->nesting_last?true:false));
  $ob->addElement("?mod=".MODULE."&scr=menus_edit&idMenu=".$menu->id,"fa-pencil",api_text("menus_list-td-edit"));
  // build menu row
  $table->addRow($tr_class);
  $table->addRowField(str_repeat("&nbsp;&nbsp;&nbsp;",$menu->nesting).api_icon("fa-caret-right").$menu->label,"nowrap");
  $table->addRowField($menu->title,"truncate-ellipsis");
  $table->addRowField($ob->render(),"text-right");
 }
 // add script to html
 $app->addScript("/* Popover Click Script */\n$(function(){\$(\"[data-toggle='popover-click']\").popover({'trigger':'click','placement':'auto top','html':true});});");
 // check nesting alert
 if($nesting_alert){api_alerts_add(api_text("framework_alert_menuNesting"),"warning");}
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($table->render(),"col-xs-12");
 // add content to application
 $app->addContent($grid->render());
 // renderize application
 $app->render();
?>