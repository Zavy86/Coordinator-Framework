<?php
/**
 * Framework - Users Edit
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // check authorizations
 api_checkAuthorization("framework-users_manage","dashboard");
 // get objects
 $user_obj=new cUser($_REQUEST['idUser'],true);
 if(!$user_obj->id){api_alerts_add(api_text("framework_alert_userNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=users_list");}
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set application title
 $app->setTitle(api_text("users_edit"));
 // build profile form
 $form=new strForm("?mod=".MODULE."&scr=submit&act=user_edit&idUser=".$user_obj->id,"POST",null,null,"users_edit");
 /*if(!$user_obj->deleted){
  $form->addField("checkbox","enabled","&nbsp;",$user_obj->enabled);
  $form->addFieldOption(1,api_text("users_edit-enabled"));
 }*/
 if($user_obj->username){$form->addField("static",null,api_text("users_edit-username"),$user_obj->username);}
 $form->addField("static",null,api_text("users_edit-mail"),$user_obj->mail);
 $form->addField("text","firstname",api_text("users_edit-firstname"),$user_obj->firstname,api_text("users_edit-firstname-placeholder"),null,null,null,"required");
 $form->addField("text","lastname",api_text("users_edit-lastname"),$user_obj->lastname,api_text("users_edit-lastname-placeholder"),null,null,null,"required");
 $form->addField("select","localization",api_text("users_edit-localization"),$user_obj->localization,api_text("users_edit-localization-placeholder"),null,null,null,"required");
 foreach($localization->available_localizations as $code=>$language){$form->addFieldOption($code,$language." (".$code.")");}
 $form->addField("select","timezone",api_text("users_edit-timezone"),$user_obj->timezone,api_text("users_edit-timezone-placeholder"),null,null,null,"required");
 foreach(timezone_identifiers_list() as $timezone){$form->addFieldOption($timezone,$timezone." (".api_timestamp_format(time(),"H:i",$timezone).")");}
 $form->addField("select","level",api_text("users_edit-level"),$user_obj->level,api_text("users_edit-level-placeholder"),null,null,null,"required");
 for($level=1;$level<=$GLOBALS['settings']->users_level_max;$level++){$form->addFieldOption($level,api_text("users_edit-level-level",$level));}
 if(!$user_obj->deleted){
  $form->addField("checkbox","superuser","&nbsp;",(int)$user_obj->superuser);
  $form->addFieldOption(1,api_text("users_edit-superuser"));
 }
 // optionals
 $form->addField("splitter");
 $form->addField("radio","gender",api_text("users_edit-gender"),$user_obj->gender,null,null,"radio-inline");
 $form->addFieldOption(null,api_text("users_edit-gender-none"));
 $form->addFieldOption("man",api_text("users_edit-gender-man"));
 $form->addFieldOption("woman",api_text("users_edit-gender-woman"));
 $form->addField("date","birthday",api_text("users_edit-birthday"),$user_obj->birthday);
 // controls
 $form->addControl("submit",api_text("form-fc-submit"));
 $form->addControl("button",api_text("form-fc-cancel"),"?mod=".MODULE."&scr=users_view&idUser=".$user_obj->id);
 if(!$user_obj->deleted){$form->addControl("button",api_text("form-fc-delete"),"?mod=".MODULE."&scr=submit&act=user_delete&idUser=".$user_obj->id,"btn-danger",api_text("users_edit-delete-confirm"));}
 else{$form->addControl("button",api_text("form-fc-undelete"),"?mod=".MODULE."&scr=submit&act=user_undelete&idUser=".$user_obj->id,"btn-warning");}
 // jQuery script
 $jquery="/* Superuser Alert */$(function(){\$(\"input[name='superuser']\").change(function(){if($(\"input[name='superuser']:checked\").val()){alert(\"".api_text("users_edit-superuser-alert")."\");}});});";
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to application
 $app->addContent($grid->render());
 // add script to html
 $app->addScript($jquery);
 // renderize application
 $app->render();
 // debug
 api_dump($user_obj,"user object");
?>