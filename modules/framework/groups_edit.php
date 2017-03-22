<?php
/**
 * Settings - Groups Edit
 *
 * @package Coordinator\Modules\Settings
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check permissions
 /** @todo check permissions */
 // get objects
 $group=new Group($_REQUEST['idGroup']);
 // set html title
 $html->setTitle(($group->id?api_text("groups_edit"):api_text("groups_add")));

 /**
  * Groups tree to form select option
  *
  * @param object $form Form object
  * @param integer $skip Group to skip
  * @param integer $idGroup Start group branch
  * @param integer $level Identation level
  */
 /*function api_groups_tree2selectOption(&$form,$skip=NULL,$idGroup=NULL,$level=0){
  $groups_tree=api_settings_groups($idGroup);
  foreach($groups_tree as $group){
   if($group->id==$skip){continue;}
   $form->addFieldOption($group->id,str_repeat("&nbsp;&nbsp;&nbsp;",$level).$group->name);
   api_groups_tree2selectOption($form,$skip,$group->id,($level+1));
  }
 }*/

 // build profile form
 $form=new Form("?mod=framework&scr=submit&act=group_save&idGroup=".$group->id,"POST",null,"groups_edit");
 $form->addField("select","fkGroup",api_text("groups_edit-fkGroup"),$group->fkGroup);
 $form->addFieldOption(NULL,api_text("groups_edit-fkGroup-main"));
 //api_groups_tree2selectOption($form,$group->id);

 api_tree_to_array($groups_array,"api_settings_groups","id");
 foreach($groups_array as $group_option){
  if($group_option->id==$group->id){continue;}
  $form->addFieldOption($group_option->id,str_repeat("&nbsp;&nbsp;&nbsp;",$group_option->nesting).$group_option->fullname);
 }

 $form->addField("text","name",api_text("groups_edit-name"),$group->name,api_text("groups_edit-name-placeholder"),NULL,NULL,NULL,"required");
 $form->addField("textarea","description",api_text("groups_edit-description"),$group->description,api_text("groups_edit-description-placeholder"));
 $form->addControl("submit",api_text("groups_edit-submit"));
 $form->addControl("button",api_text("groups_edit-cancel"),"?mod=framework&scr=groups_list");

 if(!$group->deleted){$form->addControl("button",api_text("groups_edit-delete"),"?mod=framework&scr=submit&act=groups_delete&idUser=".$group->id,"btn-danger",api_text("groups_edit-delete-confirm"));}
  else{$form->addControl("button",api_text("groups_edit-undelete"),"?mod=framework&scr=submit&act=groups_undelete&idUser=".$group->id,"btn-warning");}
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($form->render("2"),"col-xs-12 col-sm-6");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($group,"group");}
?>