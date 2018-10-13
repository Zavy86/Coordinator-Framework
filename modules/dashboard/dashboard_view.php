<?php
/**
 * Dashboard - View
 *
 * @package Coordinator\Modules\Dashboard
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 /** @todo check authorizations */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("dashboard_view"));
 // build dashboard
 $dashboard=new cDashboard();
 // get all tiles
 $tiles_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__users_dashboards` WHERE `fkUser`='".$GLOBALS['session']->user->id."' ORDER BY `order`");
 foreach($tiles_results as $tile){
  $tile_obj=new cDashboardTile($tile);
  // add dashboard element
  $dashboard->addTile($tile_obj->url,$tile_obj->label,$tile_obj->description,true,$tile_obj->size,$tile_obj->icon,$tile_obj->counter->count,$tile_obj->counter->class,$tile_obj->background,$tile_obj->target);
 }
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 // check for elements
 if(count($dashboard->elements_array)){$grid->addCol($dashboard->render(),"col-xs-12");}
 else{$grid->addCol(api_tag("p",api_text("dashboard_view-welcome",$GLOBALS['session']->user->fullname)),"col-xs-12");}
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
?>