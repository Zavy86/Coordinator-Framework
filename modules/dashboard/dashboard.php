<?php
/**
 * Dashboard - Dashboard
 *
 * @package Coordinator\Modules\Dashboard
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("dashboard_view"));
 // get user tiles
 $tiles_array=api_crm_userTiles();
 // check for tiles
 if(count($tiles_array)){
  // build dashboard
  $dashboard=new strDashboard();
  // cycle all tiles
  foreach($tiles_array as $tile_fobj){
   // add dashboard element
   $dashboard->addTile($tile_fobj->url,$tile_fobj->label,$tile_fobj->description,true,$tile_fobj->size,$tile_fobj->icon,$tile_fobj->counter->count,$tile_fobj->counter->class,$tile_fobj->background,$tile_fobj->target);
  }
 }
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 // check for elements
 if(count($tiles_array)){$grid->addCol($dashboard->render(),"col-xs-12");}
 else{$grid->addCol(api_tag("p",api_text("dashboard_view-welcome",$GLOBALS['session']->user->fullname)),"col-xs-12");}
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
?>