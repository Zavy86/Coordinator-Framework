<?php
/**
 * Framework - Groups Edit
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 $authorization="framework-groups_manage";
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // get objects
 $group=new cGroup($_REQUEST['idGroup']);
 // set html title
 $app->setTitle(($group->id?api_text("groups_edit"):api_text("groups_add")));

 /**
  * Groups tree to form select option
  *
  * @param object $form Form object
  * @param integer $skip Group to skip
  * @param integer $idGroup Start group branch
  * @param integer $level Identation level
  */
 /*function api_groups_tree2selectOption(&$form,$skip=null,$idGroup=null,$level=0){
  $groups_tree=api_framework_groups($idGroup);
  foreach($groups_tree as $group){
   if($group->id==$skip){continue;}
   $form->addFieldOption($group->id,str_repeat("&nbsp;&nbsp;&nbsp;",$level).$group->name);
   api_groups_tree2selectOption($form,$skip,$group->id,($level+1));
  }
 }*/

 // build profile form
 $form=new strForm("?mod=".MODULE."&scr=submit&act=group_save&idGroup=".$group->id,"POST",null,"groups_edit");
 $form->addField("select","fkGroup",api_text("groups_edit-fkGroup"),$group->fkGroup);
 $form->addFieldOption(null,api_text("groups_edit-fkGroup-main"));
 //api_groups_tree2selectOption($form,$group->id);

 api_tree_to_array($groups_array,"api_framework_groups","id");
 foreach($groups_array as $group_option){
  if($group_option->id==$group->id){continue;}
  $form->addFieldOption($group_option->id,str_repeat("&nbsp;&nbsp;&nbsp;",$group_option->nesting).$group_option->fullname);
 }

 $form->addField("text","name",api_text("groups_edit-name"),$group->name,api_text("groups_edit-name-placeholder"),null,null,null,"required");
 $form->addField("textarea","description",api_text("groups_edit-description"),$group->description,api_text("groups_edit-description-placeholder"));
 $form->addControl("submit",api_text("groups_edit-submit"));
 $form->addControl("button",api_text("groups_edit-cancel"),"?mod=".MODULE."&scr=groups_list");

 if(!$group->deleted){$form->addControl("button",api_text("groups_edit-delete"),"?mod=".MODULE."&scr=submit&act=groups_delete&idGroup=".$group->id,"btn-danger",api_text("groups_edit-delete-confirm"));}
  else{$form->addControl("button",api_text("groups_edit-undelete"),"?mod=".MODULE."&scr=submit&act=groups_undelete&idGroup=".$group->id,"btn-warning");}
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($form->render("2"),"col-xs-12 col-sm-6");
 // add content to html
 $app->addContent($grid->render());
 // renderize html page
 $app->render();
 // debug
 api_dump($group,"group");
?>