<?php
/**
 * Framework - Groups Edit
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // check authorizations
 api_checkAuthorization("framework-groups_manage","dashboard");
 // get objects
 $group_obj=new cGroup($_REQUEST['idGroup']);
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set application title
 $app->setTitle(($group_obj->id?api_text("groups_edit"):api_text("groups_edit-add")));
 // definitions
 $groups_array=array();
 // make tree array
 api_tree_to_array($groups_array,"api_availableGroups","id");
 // build form
 $form=new strForm("?mod=".MODULE."&scr=submit&act=group_save&idGroup=".$group_obj->id,"POST",null,"groups_edit");
 $form->addField("select","fkGroup",api_text("groups_edit-fkGroup"),$group_obj->fkGroup);
 $form->addFieldOption(null,api_text("groups_edit-fkGroup-main"));
 foreach($groups_array as $group_fobj){
  if($group_fobj->id==$group_obj->id){continue;}
  $form->addFieldOption($group_fobj->id,str_repeat("&nbsp;&nbsp;&nbsp;",$group_fobj->nesting).$group_fobj->fullname);
 }
 $form->addField("text","name",api_text("groups_edit-name"),$group_obj->name,api_text("groups_edit-name-placeholder"),null,null,null,"required");
 $form->addField("textarea","description",api_text("groups_edit-description"),$group_obj->description,api_text("groups_edit-description-placeholder"));
 $form->addControl("submit",api_text("form-fc-submit"));
 $form->addControl("button",api_text("form-fc-cancel"),"?mod=".MODULE."&scr=groups_list");
 // controls
 if(!$group_obj->deleted){$form->addControl("button",api_text("form-fc-delete"),"?mod=".MODULE."&scr=submit&act=groups_delete&idGroup=".$group_obj->id,"btn-danger",api_text("groups_edit-delete-confirm"));}
 else{$form->addControl("button",api_text("form-fc-undelete"),"?mod=".MODULE."&scr=submit&act=groups_undelete&idGroup=".$group_obj->id,"btn-warning");}
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($form->render("2"),"col-xs-12 col-sm-6");
 // add content to application
 $app->addContent($grid->render());
 // renderize application
 $app->render();
 // debug
 api_dump($group_obj,"group object");
?>