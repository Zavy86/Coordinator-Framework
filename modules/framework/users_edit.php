<?php
/**
 * Framework - Users Edit
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 $authorization="framework-users_manage";
 // get objects
 $user=new cUser($_REQUEST['idUser']);
 if(!$user->id){api_alerts_add(api_text("framework_alert_userNotFound"),"danger");api_redirect("?mod=framework&scr=users_list");}

 // include module template
 require_once(MODULE_PATH."template.inc.php");

 // set html title
 $html->setTitle(api_text("users_edit"));
 // build profile form
 $form=new cForm("?mod=framework&scr=submit&act=user_edit&idUser=".$user->id,"POST",null,"users_edit");
 /*if(!$user->deleted){
  $form->addField("checkbox","enabled","&nbsp;",$user->enabled);
  $form->addFieldOption(1,api_text("users_edit-enabled"));
 }*/
 $form->addField("static",null,api_text("users_edit-mail"),$user->mail);
 $form->addField("text","firstname",api_text("users_edit-firstname"),$user->firstname,api_text("users_edit-firstname-placeholder"),null,null,8,"required");
 $form->addField("text","lastname",api_text("users_edit-lastname"),$user->lastname,api_text("users_edit-lastname-placeholder"),null,null,null,"required");
 $form->addField("select","localization",api_text("users_edit-localization"),$user->localization,api_text("users_edit-localization-placeholder"),null,null,null,"required");
 foreach($localization->available_localizations as $code=>$language){$form->addFieldOption($code,$language." (".$code.")");}
 $form->addField("select","timezone",api_text("users_edit-timezone"),$user->timezone,api_text("users_edit-timezone-placeholder"),null,null,null,"required");
 foreach(timezone_identifiers_list() as $timezone){$form->addFieldOption($timezone,$timezone." (".api_timestamp_format(time(),"H:i",$timezone).")");}
 $form->addField("select","level",api_text("users_edit-level"),$user->level,api_text("users_edit-level-placeholder"),null,null,null,"required");
 for($level=1;$level<=$GLOBALS['settings']->users_level_max;$level++){$form->addFieldOption($level,api_text("users_edit-level-level",$level));}
 if(!$user->deleted){
  $form->addField("checkbox","superuser","&nbsp;",$user->superuser);
  $form->addFieldOption(1,api_text("users_edit-superuser"));
 }
 // optionals
 $form->addField("splitter");
 $form->addField("radio","gender",api_text("users_edit-gender"),$user->gender,null,null,"radio-inline");
 $form->addFieldOption("",api_text("users_edit-gender-none"));
 $form->addFieldOption("man",api_text("users_edit-gender-man"));
 $form->addFieldOption("woman",api_text("users_edit-gender-woman"));
 $form->addField("date","birthday",api_text("users_edit-birthday"),$user->birthday);

 // controls
 $form->addControl("submit",api_text("users_edit-submit"));
 $form->addControl("button",api_text("users_edit-cancel"),"?mod=framework&scr=users_view&idUser=".$user->id);
 if(!$user->deleted){$form->addControl("button",api_text("users_edit-delete"),"?mod=framework&scr=submit&act=user_delete&idUser=".$user->id,"btn-danger",api_text("users_edit-delete-confirm"));}
 else{$form->addControl("button",api_text("users_edit-undelete"),"?mod=framework&scr=submit&act=user_undelete&idUser=".$user->id,"btn-warning");}
 // jQuery script
 $jquery="/* Superuser Alert */$(function(){\$(\"input[name='superuser']\").change(function(){if($(\"input[name='superuser']:checked\").val()){alert(\"".api_text("users_edit-superuser-alert")."\");}});});";
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // add script to html
 $html->addScript($jquery);
 // renderize html page
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($user,"user");}
?>