<?php
/**
 * Framework - Menus List
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check authorizations
 /** @todo check authorizations */
 // set html title
 $html->setTitle(api_text("menus_list"));
 // build grid object
 $table=new Table(api_text("menus_list-tr-unvalued"));
 $table->addHeader(api_text("menus_list-th-label"),"nowrap");
 $table->addHeader(api_text("menus_list-th-title"),NULL,"100%");
 $table->addHeader("&nbsp;",NULL,16);
 // get menus tree array
 $menus_array=array();
 api_tree_to_array($menus_array,"api_framework_menus","id");
 // cycle all menus
 foreach($menus_array as $menu){
  // check selected
  if($menu->id==$_REQUEST['idMenu']){$tr_class="info currentrow";}else{$tr_class=NULL;}
  // check nesting
  if($menu->nesting>1){$tr_class.=" warning";$nesting_alert=TRUE;}
  // build operation button
  $ob=new OperationsButton();
  $ob->addElement("?mod=framework&scr=submit&act=menu_move_left&idMenu=".$menu->id,"fa-arrow-left",api_text("menus_list-td-move-left"),($menu->fkMenu?TRUE:FALSE));
  $ob->addElement("?mod=framework&scr=submit&act=menu_move_up&idMenu=".$menu->id,"fa-arrow-up",api_text("menus_list-td-move-up"),($menu->order>1?TRUE:FALSE));
  $ob->addElement("?mod=framework&scr=submit&act=menu_move_down&idMenu=".$menu->id,"fa-arrow-down",api_text("menus_list-td-move-down"),(!$menu->nesting_last?TRUE:FALSE));
  $ob->addElement("?mod=framework&scr=menus_edit&idMenu=".$menu->id,"fa-pencil",api_text("menus_list-td-edit"));
  // build menu row
  $table->addRow($tr_class);
  $table->addRowField(str_repeat("&nbsp;&nbsp;&nbsp;",$menu->nesting).api_icon("fa-caret-right").$menu->label,"nowrap");
  $table->addRowField($menu->title,"truncate-ellipsis");
  $table->addRowField($ob->render(),"text-right");
 }
 // add script to html
 $html->addScript("/* Popover Click Script */\n$(function(){\$(\"[data-toggle='popover-click']\").popover({'trigger':'click','placement':'auto top','html':true});});");
 // check nesting alert
 if($nesting_alert){api_alerts_add(api_text("settings_alert_menuNesting"),"warning");}
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($table->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html
 $html->render();
?>