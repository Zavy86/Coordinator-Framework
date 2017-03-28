<?php
/**
 * Framework - Groups List
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
 $html->setTitle(api_text("groups_list"));

 /**
  * Groups tree to table rows
  *
  * @param object $table Table object
  * @param integer $idGroup Start group branch
  * @param integer $level Identation level
  */
 /*function api_groups_tree2table(&$table,$idGroup=NULL,$level=0){
  $groups_tree=api_framework_groups($idGroup);
  foreach($groups_tree as $group){
   $table->addRow();
   $table->addRowField(api_link("#",api_icon("fa-search",api_text("groups_list-td-view"),"hidden-link")));
   $table->addRowField(str_repeat("&nbsp;&nbsp;&nbsp;",$level).$group->name,"nowrap");
   $table->addRowField($group->description);
   $table->addRowField(api_link("?mod=framework&scr=groups_edit&idGroup=".$group->id,api_icon("fa-edit",api_text("groups_list-td-edit"),"hidden-link")));
   api_groups_tree2table($table,$group->id,($level+1));
  }
 }*/

 // build grid object
 $table=new Table(api_text("groups_list-tr-unvalued"));
 $table->addHeader("&nbsp;",NULL,16);
 $table->addHeader(api_text("groups_list-th-name"),"nowrap");
 $table->addHeader(api_text("groups_list-th-description"),NULL,"100%");
 $table->addHeader("&nbsp;",NULL,16);
 // show groups tree
 //api_groups_tree2table($table);

 $groups_array=array();
 api_tree_to_array($groups_array,"api_framework_groups","id");
 foreach($groups_array as $group){
  $table->addRow();
  $table->addRowField(api_link("#",api_icon("fa-search",api_text("groups_list-td-view"),"hidden-link")));
  $table->addRowField(str_repeat("&nbsp;&nbsp;&nbsp;",$group->nesting).$group->name,"nowrap");
  $table->addRowField($group->description);
  $table->addRowField(api_link("?mod=framework&scr=groups_edit&idGroup=".$group->id,api_icon("fa-edit",api_text("groups_list-td-edit"),"hidden-link")));
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