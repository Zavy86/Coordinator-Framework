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
 foreach($groups_array as $group_fobj){
  // build operation button
  $ob=new strOperationsButton();
  $ob->addElement("?mod=".MODULE."&scr=groups_list&act=group_view&idGroup=".$group_fobj->id,"fa-info-circle",api_text("groups_list-td-view"));
  $ob->addElement("?mod=".MODULE."&scr=groups_edit&idGroup=".$group_fobj->id."&return_scr=groups_list","fa-pencil",api_text("groups_list-td-edit"));
  $ob->addElement("?mod=".MODULE."&scr=submit&act=group_remove&idGroup=".$group_fobj->id,"fa-trash-o",api_text("groups_list-td-remove"),true,api_text("groups_list-td-remove-confirm"));
  // make table row class
  $tr_class_array=array();
  if($group_fobj->id==$_REQUEST['idGroup']){$tr_class_array[]="currentrow";}
  if($group_fobj->deleted){$tr_class_array[]="deleted";}
  // make table row
  $table->addRow(implode(" ",$tr_class_array));
  $table->addRowFieldAction("?mod=".MODULE."&scr=groups_view&idGroup=".$group_fobj->id,"fa-search",api_text("groups_list-td-tree"));
  $table->addRowField(str_repeat("&nbsp;&nbsp;&nbsp;",$group_fobj->nesting).$group_fobj->name,"nowrap");
  $table->addRowField($group_fobj->description);
  $table->addRowField($ob->render(),"text-right");
 }
 // check for group view action
 if(ACTION=="group_view"){
  // get selected group object
  $selected_group_obj=new cGroup($_REQUEST['idGroup']);
  // build users table
  $users_table=new strTable(api_text("groups_list-modal-users-tr-unvalued"));
  // cycle all assigned users
  foreach($selected_group_obj->getAssignedUsers() as $assigend_user_f){
   // get user object
   $user_obj=new cUser($assigend_user_f->id);
   // add group row
   $users_table->addRow();
   $users_table->addRowField(api_link("?mod=".MODULE."&scr=users_view&idUser=".$user_obj->id,$user_obj->fullname." (".$user_obj->level.")",null,"hidden-link",true,null,null,null,"_blank"),"truncate-ellipsis");
  }
  // build parameters description list
  $group_dl=new strDescriptionList("br","dl-horizontal");
  $group_dl->addElement(api_text("groups_list-modal-dt-name"),$selected_group_obj->name);
  if($selected_group_obj->description){$group_dl->addElement(api_text("groups_list-modal-dt-description"),$selected_group_obj->description);}
  $group_dl->addElement(api_text("groups_list-modal-dt-users"),$users_table->render());
  // build group add modal window
  $groups_modal=new strModal(api_text("groups_list-modal-title",$selected_group_obj->name),null,"groups_list-modal");
  $groups_modal->setBody($group_dl->render());
  // add modal to application
  $app->addModal($groups_modal);
  // jQuery scripts
  $app->addScript("/* Group Modal window opener */\n$(function(){\$(\"#modal_groups_list-modal\").modal('show');});");
 }
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($table->render(),"col-xs-12");
 // add content to application
 $app->addContent($grid->render());
 // renderize application
 $app->render();
 // debug
 if(is_object($selected_group_obj)){api_dump($selected_group_obj,"selected group");}
 api_dump($groups_array,"groups array");
?>