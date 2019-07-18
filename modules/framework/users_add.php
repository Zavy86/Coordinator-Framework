<?php
/**
 * Framework - Users Add
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // check authorizations
 api_checkAuthorization("framework-users_manage","dashboard");
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set application title
 $app->setTitle(api_text("users_add"));
 // build form
 $form=new strForm("?mod=".MODULE."&scr=submit&act=user_add","POST",null,null,"users_add");
 if($GLOBALS['settings']->sessions_authentication_method=='ldap'){$form->addField("text","username",api_text("users_add-username"),$user->username,api_text("users_add-username-placeholder"));}
 $form->addField("text","mail",api_text("users_add-mail"),$user->mail,api_text("users_add-mail-placeholder"),null,null,null,($GLOBALS['settings']->sessions_authentication_method=='standard'?"required":null));
 $form->addField("text","firstname",api_text("users_add-firstname"),$user->firstname,api_text("users_add-firstname-placeholder"),null,null,8,"required");
 $form->addField("text","lastname",api_text("users_add-lastname"),$user->lastname,api_text("users_add-lastname-placeholder"),null,null,null,"required");
 $form->addField("select","localization",api_text("users_add-localization"),$session->user->localization,api_text("users_add-localization-placeholder"),null,null,null,"required");
 foreach($localization->available_localizations as $code=>$language){$form->addFieldOption($code,$language." (".$code.")");}
 $form->addField("select","timezone",api_text("users_add-timezone"),$session->user->timezone,api_text("users_add-timezone-placeholder"),null,null,null,"required");
 foreach(timezone_identifiers_list() as $timezone){$form->addFieldOption($timezone,$timezone." (".api_timestamp_format(time(),"H:i",$timezone).")");}
 $form->addField("select","level",api_text("users_add-level"),$user->level,api_text("users_add-level-placeholder"),null,null,null,"required");
 for($level=1;$level<=$GLOBALS['settings']->users_level_max;$level++){$form->addFieldOption($level,api_text("users_add-level-level",$level));}
 $form->addControl("submit",api_text("form-fc-submit"));
 $form->addControl("button",api_text("form-fc-cancel"),"?mod=".MODULE."&scr=users_list");
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($form->render("2"),"col-xs-12 col-sm-6");
 // add content to application
 $app->addContent($grid->render());
 // renderize application
 $app->render();
?>