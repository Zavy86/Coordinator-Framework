<?php
/**
 * Framework - Users View
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 $authorization="framework-users_manage";
 // get objects
 $user_obj=new cUser($_REQUEST['idUser']);
 if(!$user_obj->id){api_alerts_add(api_text("settings_alert_userNotFound"),"danger");api_redirect("?mod=framework&scr=users_list");}

 // include module template
 require_once(MODULE_PATH."template.inc.php");

 // set html title
 $html->setTitle(api_text("users_view"));

 // build groups table
 $groups_table=new cTable(api_text("users_view-groups_table-tr-unvalued"));
 // cycle user groups
 foreach($user_obj->groups_array as $group){
  // make delete and mainize td
  $delete_td=api_link("?mod=framework&scr=submit&act=user_group_remove&idUser=".$user_obj->id."&idGroup=".$group->id,api_icon("fa-trash",api_text("users_view-groups_table-td-delete"),"hidden-link"),NULL,NULL,FALSE,api_text("users_view-groups_table-td-delete-confirm"));
  if($group->id==$user_obj->groups_main){
   $mainize_td=api_icon("fa-star",api_text("users_view-groups_table-td-main"));
   if(count($user_obj->groups_array)>1){$delete_td=NULL;}
  }else{
   $mainize_td=api_link("?mod=framework&scr=submit&act=user_group_mainize&idUser=".$user_obj->id."&idGroup=".$group->id,api_icon("fa-star-o",api_text("users_view-groups_table-td-mainize"),"hidden-link"),NULL,NULL,FALSE,api_text("users_view-groups_table-td-mainize-confirm"));
  }
  // add group row
  $groups_table->addRow();
  $groups_table->addRowField($mainize_td);
  $groups_table->addRowField(api_link("?mod=framework&scr=groups_view&idGroup=".$group->id,$group->fullname,NULL,"hidden-link",FALSE,NULL,NULL,NULL,"_blank"),"truncate-ellipsis");
  $groups_table->addRowField($delete_td);
 }

 // deleted alert
 if($user_obj->deleted){api_alerts_add(api_text("users_view-deleted-alert"),"warning");}

 // avatar delete link
 if(is_numeric(substr($user_obj->avatar,-5,1))){$avatar_delete_link=api_link("#",api_icon("fa-remove",api_text("users_view-avatar-delete"),"hidden-link text-vtop"),NULL,NULL,FALSE,api_text("users_view-avatar-delete-confirm"));}

 // build left user description list
 $dl_left=new cDescriptionList("br","dl-horizontal");
 $dl_left->addElement($user_obj->fullname,api_image($user_obj->avatar,"img-thumbnail",128).$avatar_delete_link);
 $dl_left->addElement("&nbsp;",$user_obj->getStatus());
 $dl_left->addElement(api_text("users_view-mail"),$user_obj->mail);
 $dl_left->addElement(api_text("users_view-localization"),$localization->available_localizations[$user_obj->localization]);
 $dl_left->addElement(api_text("users_view-timezone"),$user_obj->timezone);
 $dl_left->addElement(api_text("users_view-level"),api_text("users_view-level-level",$user_obj->level));

 // build right user description list
 $dl_right=new cDescriptionList("br","dl-horizontal");
 if($user_obj->gender){$dl_right->addElement(api_text("users_view-gender"),$user_obj->getGender(FALSE));}
 if($user_obj->birthday){$dl_right->addElement(api_text("users_view-birthday"),api_timestamp_format(strtotime($user_obj->birthday),api_text("date")));}
 $dl_right->addElement(api_text("users_view-groups"),$groups_table->render());

 // build companies table
 /*$companies_table=new cTable(api_text("users_view-companies-unvalued"));
 $companies_table->addHeader(api_text("users_view-companies-th-company"));
 $companies_table->addHeader(api_text("users_view-companies-th-level"));
 $companies_table->addHeader("&nbsp;");*/

 // check for action group_add
 if(ACTION=="group_add"){
  // build group add form
  $group_add_form=new cForm("?mod=framework&scr=submit&act=user_group_add&idUser=".$user_obj->id,"POST",NULL,"users_view-groups_modal");
  $group_add_form->addField("select","fkGroup",api_text("users_view-groups_modal-ff-group"),NULL,api_text("users_view-groups_modal-ff-group-placeholder"),NULL,NULL,NULL,"required");
  api_tree_to_array($groups_array,"api_framework_groups","id");
  foreach($groups_array as $group_option){$group_add_form->addFieldOption($group_option->id,str_repeat("&nbsp;&nbsp;&nbsp;",$group_option->nesting).$group_option->fullname);}
  $group_add_form->addControl("submit",api_text("users_view-groups_modal-fc-submit"));
  $group_add_form->addControl("button",api_text("users_view-groups_modal-fc-cancel"),"#",NULL,NULL,NULL,"data-dismiss='modal'");
  // build group add modal window
  $groups_modal=new cModal(api_text("users_view-groups_modal-title"),NULL,"users_view-groups_modal");
  $groups_modal->setBody($group_add_form->render());
  // add modal to html object
  $html->addcModal($groups_modal);
  // jQuery scripts
  $html->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_users_view-groups_modal\").modal('show');});");
 }
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 $grid->addCol($dl_left->render(),"col-xs-12 col-sm-5");
 $grid->addCol($dl_right->render(),"col-xs-12 col-sm-7");
 //$grid->addCol($companies_table->render().$groups_table->render(),"col-xs-12 col-sm-7");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($user_obj,"user_obj");}
?>