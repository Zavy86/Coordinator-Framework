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
 $table->addHeader("&nbsp;",NULL,16);
 $table->addHeader(api_text("menus_list-th-label"),"nowrap");
 $table->addHeader(api_text("menus_list-th-title"),NULL,"100%");
 $table->addHeader("&nbsp;",NULL,16);
 // get menus tree array
 $menus_array=array();
 api_tree_to_array($menus_array,"api_framework_menus","id");
 // cycle all menus
 foreach($menus_array as $menu){
  $table->addRow();
  $table->addRowField(api_link("#",api_icon("fa-arrows-v","@todo","hidden-link")));
  $table->addRowField(str_repeat("&nbsp;&nbsp;&nbsp;",$menu->nesting).$menu->label,"nowrap");
  $table->addRowField($menu->title);
  $table->addRowField(api_link("?mod=framework&scr=menus_edit&idMenu=".$menu->id,api_icon("fa-edit",api_text("menus_list-td-edit"),"hidden-link")));
 }
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($table->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html
 $html->render();
?>