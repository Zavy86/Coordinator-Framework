<?php
/**
 * Dashboard - Dashboard
 *
 * @package Coordinator\Modules\Dashboard
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set application title
 $app->setTitle(api_text(MODULE));
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 // build dashboard
 $dashboard_menu=new strDashboard();
 // cycle all menus
 foreach(api_availableMenus(null) as $menu_obj){
  // check for authorization
  if(!$menu_obj->checkAuthorizations()){continue;}
  // check for typology
  if($menu_obj->typology=="group"){
   // add dashboard container
   $dashboard_menu->addContainer(($menu_obj->icon?api_icon($menu_obj->icon)." ":null).$menu_obj->label,$menu_obj->title);
  }else{
   // add menu tile to dashboard
   $dashboard_menu->addTile($menu_obj->url,$menu_obj->label,$menu_obj->title,true,null,$menu_obj->icon,null,null,null,$menu_obj->target);
  }
  // cycle all sub menus
  foreach(api_availableMenus($menu_obj->id) as $subMenu_obj){
   // check for authorization
   if(!$subMenu_obj->checkAuthorizations()){continue;}
   // add menu tile to dashboard
   $dashboard_menu->addTile($subMenu_obj->url,$subMenu_obj->label,$subMenu_obj->title,true,null,$subMenu_obj->icon,null,null,null,$subMenu_obj->target);
  }
 }
 //
 $grid->addCol($dashboard_menu->render(),"col-xs-12");
 // add content to application
 $app->addContent($grid->render());
 // renderize application
 $app->render();
?>