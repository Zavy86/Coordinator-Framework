<?php
/**
 * Framework - Modules View
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check authorizations
 /** @todo check authorizations */
 // get objects
 $module_obj=new Module($_REQUEST['module']);
 // check objects
 if(!$module_obj->module){api_alerts_add(api_text("settings_alert_moduleNotFound"),"danger");api_redirect("?mod=framework&scr=modules_list");}
 // set html title
 $html->setTitle(api_text("modules_view",$module_obj->name));

 /* decidere se farlo vedere anche qui */
 /** @todo modificare con lettura */
 /*$repository_version="1.0.1";
 // check if repository version is updated among source version
 if(api_check_version($module_obj->source_version,$repository_version)>0){$repository_updated=TRUE;}else{$repository_updated=FALSE;}
 // check if source version is updated among installed version
 if(api_check_version($module_obj->version,$module_obj->source_version)>0){$source_updated=TRUE;}else{$source_updated=FALSE;}
 // make version
 if($source_updated){$version_td=api_link("#",api_tag("span",$module_obj->version,"label label-warning"),api_text("modules_view-dd-version-source_update"),"hidden-link",TRUE);}
 elseif($repository_updated){$version_td=api_link("#",api_tag("span",$module_obj->version,"label label-info"),api_text("modules_view-dd-version-repository_update",$repository_version),"hidden-link",TRUE);}
 else{$version_td=api_tag("span",$module_obj->version,"label ".($module_obj->enabled?"label-success":"label-default"));}*/

 // make version
 $version_td=api_tag("span",$module_obj->version,"label ".($module_obj->enabled?"label-success":"label-default"));

 // make status
 if($module_obj->enabled){$status_dd=api_icon("fa-check")." ".api_text("modules_view-dd-enabled");}
 else{$status_dd=api_icon("fa-remove")." ".api_text("modules_view-dd-disabled");}

 // build description list
 $dl=new DescriptionList("br","dl-horizontal");
 $dl->addElement(api_text("modules_view-dt-name"),api_tag("strong",$module_obj->name));
 $dl->addElement(api_text("modules_view-dt-description"),$module_obj->description);
 $dl->addElement(api_text("modules_view-dt-version"),$version_td);
 $dl->addElement(api_text("modules_view-dt-status"),$status_dd);

 // build grid object
 $table=new Table(api_text("modules_view-authorizations-tr-unvalued"));
 $table->addHeader(api_text("modules_view-authorizations-th-authorization"),"nowrap");
 $table->addHeader(api_text("modules_view-authorizations-th-groups"),NULL,"100%");
 if(!$module_obj->module){api_alerts_add(api_text("settings_alert_moduleNotFound"),"danger");api_redirect("?mod=framework&scr=modules_list");}

 // cycle selected module authorizations
 foreach($module_obj->authorizations_array as $authorization){
  // make groups
  $groups_td=NULL;
  foreach($authorization->groups_array as $group){$groups_td.=api_link("?mod=framework&scr=submit&act=module_authorizations_group_remove&module=".$module_obj->module."&fkAuthorization=".$authorization->id."&fkGroup=".$group->id,api_icon("fa-trash",api_text("modules_view-authorizations-td-delete"),"hidden-link"),NULL,NULL,FALSE,api_text("modules_view-authorizations-td-delete-confirm"))." ".$group->fullname."<br>";}
  if(!$groups_td){$groups_td=api_tag("span",api_text("modules_view-authorizations-td-groups-none"),"disabled")."<br>";}
  // add authorization row
  $table->addRow();
  $table->addRowField(api_link("#",$authorization->name,$authorization->description,"hidden-link",TRUE),"nowrap");
  $table->addRowField(substr($groups_td,0,-4),"truncate-ellipsis");
 }

 // check for action module_authorizations_group_add
 if(ACTION=="module_authorizations_group_add"){
  // build authorization join form
  $authorizations_join_form=new Form("?mod=framework&scr=submit&act=module_authorizations_group_add&module=".$module_obj->module,"POST",NULL,"modules_view-authorizations_modal");
  $authorizations_join_form->addField("select","fkGroup",api_text("modules_view-authorizations_modal-ff-group"),NULL,api_text("modules_view-authorizations_modal-ff-group-placeholder"),NULL,NULL,NULL,"required");
  api_tree_to_array($groups_array,"api_framework_groups","id");
  foreach($groups_array as $group_option){$authorizations_join_form->addFieldOption($group_option->id,str_repeat("&nbsp;&nbsp;&nbsp;",$group_option->nesting).$group_option->fullname);}

  $authorizations_join_form->addField("checkbox","fkAuthorizations[]",api_text("modules_view-authorizations_modal-ff-authorizations"),NULL,NULL,NULL,NULL,NULL,"required");
  foreach($module_obj->authorizations_array as $authorization){$authorizations_join_form->addFieldOption($authorization->id,$authorization->name."<br>".$authorization->description);}

  $authorizations_join_form->addControl("submit",api_text("modules_view-authorizations_modal-fc-submit"));
  $authorizations_join_form->addControl("button",api_text("modules_view-authorizations_modal-fc-cancel"),"#",NULL,NULL,NULL,"data-dismiss='modal'");
  // build group add modal window
  $authorizations_modal=new Modal(api_text("modules_view-authorizations_modal-title"),NULL,"modules_view-authorizations_modal");
  $authorizations_modal->setBody($authorizations_join_form->render());
  // add modal to html object
  $html->addModal($authorizations_modal);
  // jQuery scripts
  $html->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_modules_view-authorizations_modal\").modal('show');});");
 }

 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($dl->render(),"col-xs-12 col-xs-5");
 //$grid->addCol(api_tag("h4",api_text("modules_view-permissions")).$table->render(),"col-xs-12 col-xs-7");
 $grid->addCol($table->render(),"col-xs-12 col-xs-7");
 // add content to html
 $html->addContent($grid->render());
 // renderize html
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($module_obj,"selected_module");}
?>