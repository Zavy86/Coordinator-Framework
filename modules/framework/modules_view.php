<?php
/**
 * Framework - Modules View
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 $authorization="framework-modules_manage";
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // get objects
 $module_obj=new cModule($_REQUEST['module']);
 // check objects
 if(!$module_obj->module){api_alerts_add(api_text("framework_alert_moduleNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=modules_list");}
 // set html title
 $app->setTitle(api_text("modules_view",$module_obj->name));
 // make version
 $version_td=api_tag("span",$module_obj->version,"label ".($module_obj->enabled?"label-success":"label-default"));
 // build description list
 $dl=new strDescriptionList("br","dl-horizontal");
 $dl->addElement(api_text("modules_view-dt-name"),api_tag("strong",$module_obj->name));
 $dl->addElement(api_text("modules_view-dt-description"),nl2br($module_obj->description));
 $dl->addElement(api_text("modules_view-dt-version"),$version_td);
 $dl->addElement(api_text("modules_view-dt-status"),$module_obj->getEnabled());
 // build grid object
 $table=new strTable(api_text("modules_view-authorizations-tr-unvalued"));
 $table->addHeader(api_text("modules_view-authorizations-th-authorization"),"nowrap");
 $table->addHeader(api_text("modules_view-authorizations-th-groups"),null,"100%");
 if(!$module_obj->module){api_alerts_add(api_text("framework_alert_moduleNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=modules_list");}
 // cycle selected module authorizations
 foreach($module_obj->authorizations_array as $authorization){
  // make groups
  $groups_td=null;
  foreach($authorization->groups_array as $group){$groups_td.=api_link("?mod=".MODULE."&scr=submit&act=module_authorizations_group_remove&module=".$module_obj->module."&fkAuthorization=".$authorization->id."&fkGroup=".$group->id,api_icon("fa-trash",api_text("modules_view-authorizations-td-delete"),"hidden-link"),null,null,false,api_text("modules_view-authorizations-td-delete-confirm"))." ".$group->fullname." (+".$authorization->groups_level_array[$group->id]."&deg;)<br>";}
  if(!$groups_td){$groups_td=api_tag("span",api_text("modules_view-authorizations-td-groups-none"),"disabled")."<br>";}
  // add authorization row
  $table->addRow();
  $table->addRowField(api_link("#",$authorization->name,$authorization->description,"hidden-link",true),"nowrap");
  $table->addRowField(substr($groups_td,0,-4),"truncate-ellipsis");
 }
 // check for action module_authorizations_group_add
 if(ACTION=="module_authorizations_group_add"){
  // build authorization join form
  $authorizations_join_form=new strForm("?mod=".MODULE."&scr=submit&act=module_authorizations_group_add&module=".$module_obj->module,"POST",null,"modules_view-authorizations_modal");
  $authorizations_join_form->addField("select","fkGroup",api_text("modules_view-authorizations_modal-ff-group"),null,api_text("modules_view-authorizations_modal-ff-group-placeholder"),null,null,null,"required");
  api_tree_to_array($groups_array,"api_framework_groups","id");
  foreach($groups_array as $group_option){$authorizations_join_form->addFieldOption($group_option->id,str_repeat("&nbsp;&nbsp;&nbsp;",$group_option->nesting).$group_option->fullname);}
  $authorizations_join_form->addField("select","level",api_text("modules_view-authorizations_modal-ff-level"),$user->level,api_text("modules_view-authorizations_modal-ff-level-placeholder"),null,null,null,"required");
  for($level=1;$level<=$GLOBALS['settings']->users_level_max;$level++){$authorizations_join_form->addFieldOption($level,api_text("modules_view-authorizations_modal-ff-level-fo-level",$level));}
  $authorizations_join_form->addField("checkbox","fkAuthorizations[]",api_text("modules_view-authorizations_modal-ff-authorizations"));
  foreach($module_obj->authorizations_array as $authorization){$authorizations_join_form->addFieldOption($authorization->id,$authorization->name."<br>".$authorization->description);}
  $authorizations_join_form->addControl("submit",api_text("modules_view-authorizations_modal-fc-submit"));
  $authorizations_join_form->addControl("button",api_text("modules_view-authorizations_modal-fc-cancel"),"#",null,null,null,"data-dismiss='modal'");
  // build group add modal window
  $authorizations_modal=new strModal(api_text("modules_view-authorizations_modal-title"),null,"modules_view-authorizations_modal");
  $authorizations_modal->setBody($authorizations_join_form->render());
  // add modal to html object
  $app->addModal($authorizations_modal);
  // jQuery scripts
  $app->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_modules_view-authorizations_modal\").modal('show');});");
 }
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($dl->render(),"col-xs-12 col-xs-5");
 $grid->addCol($table->render(),"col-xs-12 col-xs-7");
 // add content to html
 $app->addContent($grid->render());
 // renderize html
 $app->render();
 // debug
 if($GLOBALS['debug']){api_dump($module_obj,"selected_module");}
?>