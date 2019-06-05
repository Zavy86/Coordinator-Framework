<?php
/**
 * Framework - Users View
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 api_checkAuthorization("framework-users_manage","dashboard");
  // get objects
 $user_obj=new cUser($_REQUEST['idUser']);
 if(!$user_obj->id){api_alerts_add(api_text("framework_alert_userNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=users_list");}
 // deleted alert
 if($user_obj->deleted){api_alerts_add(api_text("users_view-deleted-alert"),"warning");}
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $app->setTitle(api_text("users_view"));
 // avatar delete link
 if(is_numeric(substr($user_obj->avatar,-5,1))){$avatar_delete_link=api_link("#",api_icon("fa-remove",api_text("users_view-avatar-delete"),"hidden-link text-vtop"),null,null,false,api_text("users_view-avatar-delete-confirm"));}
 // build left user description list
 $dl_left=new strDescriptionList("br","dl-horizontal");
 $dl_left->addElement(api_tag("strong",$user_obj->fullname),api_image($user_obj->avatar,"img-thumbnail",128).$avatar_delete_link);
 $dl_left->addElement("&nbsp;",$user_obj->getStatus());
 $dl_left->addElement(api_text("users_view-mail"),$user_obj->mail);
 $dl_left->addElement(api_text("users_view-timezone"),$user_obj->timezone);
 $dl_left->addElement(api_text("users_view-localization"),$localization->available_localizations[$user_obj->localization]);
 // build groups table
 $groups_table=new strTable(api_text("users_view-groups_table-tr-unvalued"));
 // cycle all assigned groups
 foreach($user_obj->getAssignedGroups() as $group_f){
  // get group object
  $group_obj=new cGroup($group_f->id);
  // make mainize td
  if($group_f->main){$mainize_td=api_icon("fa-star",api_text("users_view-groups_table-td-main"));}
  else{$mainize_td=api_link("?mod=".MODULE."&scr=submit&act=user_group_mainize&idUser=".$user_obj->id."&idGroup=".$group_obj->id,api_icon("fa-star-o",api_text("users_view-groups_table-td-mainize"),"hidden-link"),null,null,false,api_text("users_view-groups_table-td-mainize-confirm"));}
  // make delete td
  if($group_f->main && $user_obj->getAssignedGroups()==1){$delete_td=null;}
  else{$delete_td=api_link("?mod=".MODULE."&scr=submit&act=user_group_remove&idUser=".$user_obj->id."&idGroup=".$group_obj->id,api_icon("fa-trash",api_text("users_view-groups_table-td-delete"),"hidden-link"),null,null,false,api_text("users_view-groups_table-td-delete-confirm"));}
  // add group row
  $groups_table->addRow();
  $groups_table->addRowField($mainize_td);
  $groups_table->addRowField(api_link("?mod=".MODULE."&scr=groups_view&idGroup=".$group_obj->id,$group_obj->fullname,$group_obj->getPath("string"),"hidden-link",true,null,null,null,"_blank"),"truncate-ellipsis");
  $groups_table->addRowField($delete_td);
 }
 // build right user description list
 $dl_right=new strDescriptionList("br","dl-horizontal");
 if($user_obj->gender){$dl_right->addElement(api_text("users_view-gender"),$user_obj->getGender(false));}
 if($user_obj->birthday){$dl_right->addElement(api_text("users_view-birthday"),api_timestamp_format(strtotime($user_obj->birthday),api_text("date")));}
 $dl_right->addElement(api_text("users_view-level"),api_text("users_view-level-level",$user_obj->level));
 $dl_right->addElement(api_text("users_view-groups"),$groups_table->render());

 // build companies table
 /*$companies_table=new strTable(api_text("users_view-companies-unvalued"));
 $companies_table->addHeader(api_text("users_view-companies-th-company"));
 $companies_table->addHeader(api_text("users_view-companies-th-level"));
 $companies_table->addHeader("&nbsp;");*/

 // check for action group_add
 if(ACTION=="group_add"){
  // build group add form
  $group_add_form=new strForm("?mod=".MODULE."&scr=submit&act=user_group_add&idUser=".$user_obj->id,"POST",null,"users_view-groups_modal");
  $group_add_form->addField("select","fkGroup",api_text("users_view-groups_modal-ff-group"),null,api_text("users_view-groups_modal-ff-group-placeholder"),null,null,null,"required");
  api_tree_to_array($groups_array,"api_framework_groups","id");
  foreach($groups_array as $group_option){$group_add_form->addFieldOption($group_option->id,str_repeat("&nbsp;&nbsp;&nbsp;",$group_option->nesting).$group_option->fullname);}
  $group_add_form->addControl("submit",api_text("users_view-groups_modal-fc-submit"));
  $group_add_form->addControl("button",api_text("users_view-groups_modal-fc-cancel"),"#",null,null,null,"data-dismiss='modal'");
  // build group add modal window
  $groups_modal=new strModal(api_text("users_view-groups_modal-title"),null,"users_view-groups_modal");
  $groups_modal->setBody($group_add_form->render());
  // add modal to html object
  $app->addModal($groups_modal);
  // jQuery scripts
  $app->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_users_view-groups_modal\").modal('show');});");
 }
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($dl_left->render(),"col-xs-12 col-sm-5");
 $grid->addCol($dl_right->render(),"col-xs-12 col-sm-7");
 // add content to html
 $app->addContent($grid->render());
 // renderize html page
 $app->render();
 // debug
 api_dump($user_obj,"user_obj");
?>