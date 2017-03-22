<?php
/**
 * Framework - Users View
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check permissions
 /** @todo check permissions */
 // set html title
 $html->setTitle(api_text("users_view"));
 // get objects
 $user=new User($_REQUEST['idUser']);
 if(!$user->id){api_alerts_add(api_text("settings_alert_userNotFound"),"danger");api_redirect("?mod=framework&scr=users_list");}
 // make status
 if($user->deleted){
  api_alerts_add(api_text("users_view-deleted-alert"),"warning");
  $status_td=api_icon("fa-trash")." ".api_text("users_view-deleted");
 }else{
  if($user->enabled){$status_td=api_icon("fa-check")." ".api_text("users_view-enabled");}
  else{$status_td=api_icon("fa-remove")." ".api_text("users_view-disabled");}
 }
 // build user description list
 $dl=new DescriptionList("br","dl-horizontal");
 $dl->addElement($user->fullname,api_image($user->avatar,"img-thumbnail",128));
 $dl->addElement("&nbsp;",$status_td);
 $dl->addElement(api_text("users_view-mail"),$user->mail);
 $dl->addElement(api_text("users_view-localization"),$localization->available_localizations[$user->localization]);
 $dl->addElement(api_text("users_view-timezone"),$user->timezone);
 // build comapnies table
 $companies_table=new Table(api_text("users_add-companies-unvalued"));
 $companies_table->addHeader(api_text("users_add-companies-th-company"));
 $companies_table->addHeader(api_text("users_add-companies-th-level"));
 $companies_table->addHeader("&nbsp;");
 // build groups table
 $groups_table=new Table(api_text("users_add-groups_table-tr-unvalued"));
 $groups_table->addHeader("&nbsp;",NULL,16);
 $groups_table->addHeader(api_text("users_add-groups_table-th-name"),"nowrap","100%");
 if(1){$groups_table->addHeader("&nbsp;",NULL,16);} /** @todo check permission */
 // cycle user groups
 foreach($user->groups_array as $group){
  // make delete and mainize td
  $delete_td=api_link("?mod=framework&scr=submit&act=user_group_remove&idUser=".$user->id."&idGroup=".$group->id,api_icon("fa-trash",api_text("users_add-groups_table-td-delete"),"hidden-link"),NULL,NULL,FALSE,api_text("users_add-groups_table-td-delete-confirm"));
  if($group->id==$user->groups_main){
   $mainize_td=api_icon("fa-star",api_text("users_add-groups_table-td-main"));
   if(count($user->groups_array)>1){$delete_td=NULL;}
  }else{
   /** @todo check permission */
   if(!(1)){$mainize_td=api_icon("fa-star-o");}
   else{$mainize_td=api_link("?mod=framework&scr=submit&act=user_group_mainize&idUser=".$user->id."&idGroup=".$group->id,api_icon("fa-star-o",api_text("users_add-groups_table-td-mainize"),"hidden-link"),NULL,NULL,FALSE,api_text("users_add-groups_table-td-mainize-confirm"));}
  }
  // add group row
  $groups_table->addRow();
  $groups_table->addRowField($mainize_td);
  $groups_table->addRowField(api_link("?mod=framework&scr=groups_view&idGroup=".$group->id,$group->fullname,NULL,"hidden-link",FALSE,NULL,NULL,NULL,"_blank"),"truncate-ellipsis");
  if(1){$groups_table->addRowField($delete_td);} /** @todo check permission */
 }
 // check for action group_add
 if(ACTION=="group_add"){
  // build group add form
  $group_add_form=new Form("?mod=framework&scr=submit&act=user_group_add&idUser=".$user->id,"POST",NULL,"users_add-groups_modal");
  $group_add_form->addField("select","fkGroup",api_text("users_add-groups_modal-ff-group"),NULL,api_text("users_add-groups_modal-ff-group-placeholder"),NULL,NULL,NULL,"required");
  api_tree_to_array($groups_array,"api_settings_groups","id");
  foreach($groups_array as $group_option){$group_add_form->addFieldOption($group_option->id,str_repeat("&nbsp;&nbsp;&nbsp;",$group_option->nesting).$group_option->fullname);}
  $group_add_form->addControl("submit",api_text("users_add-groups_modal-fc-submit"));
  $group_add_form->addControl("button",api_text("users_add-groups_modal-fc-cancel"),"#",NULL,NULL,NULL,"data-dismiss='modal'");
  // build group add modal window
  $groups_modal=new Modal(api_text("users_add-groups_modal-title"),NULL,"users_add-groups_modal");
  $groups_modal->setBody($group_add_form->render());
  // add modal to html object
  $html->addModal($groups_modal);
  // jQuery scripts
  $html->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_users_add-groups_modal\").modal('show');});");
 }
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($dl->render(),"col-xs-12 col-sm-6");
 $grid->addCol($companies_table->render().$groups_table->render(),"col-xs-12 col-sm-6");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($user,"user");}
?>