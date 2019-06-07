<?php
/**
 * Framework - Groups List
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // check authorizations
 api_checkAuthorization("framework-groups_manage","dashboard");
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set application title
 $app->setTitle(api_text("groups_list"));
 // build grid object
 $table=new strTable(api_text("groups_list-tr-unvalued"));
 $table->addHeader("&nbsp;",null,16);
 $table->addHeader(api_text("groups_list-th-name"),"nowrap");
 $table->addHeader(api_text("groups_list-th-description"),null,"100%");
 $table->addHeader("&nbsp;",null,16);
 // make array from groups tree
 $groups_array=array();
 api_tree_to_array($groups_array,"api_availableGroups","id");
 // cycle all groups
 foreach($groups_array as $group){
  $table->addRow();
  $table->addRowFieldAction("?mod=".MODULE."&scr=groups_view&idGroup=".$group->id,"fa-search",api_text("groups_list-td-view"));
  $table->addRowField(str_repeat("&nbsp;&nbsp;&nbsp;",$group->nesting).$group->name,"nowrap");
  $table->addRowField($group->description);
  $table->addRowFieldAction("?mod=".MODULE."&scr=groups_edit&idGroup=".$group->id,"fa-edit",api_text("groups_list-td-edit"));
 }
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($table->render(),"col-xs-12");
 // add content to application
 $app->addContent($grid->render());
 // renderize application
 $app->render();
?>