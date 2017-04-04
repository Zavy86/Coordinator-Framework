<?php
/**
 * Framework - Own Profile
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("own_profile"));
 // get objects
 $user=$session->user;
 // make avatar delete link
 if(is_numeric(substr($user->avatar,-5,1))){$avatar_delete_link=api_link("?mod=framework&scr=submit&act=own_avatar_remove",api_icon("fa-remove",api_text("own_profile-avatar-delete"),"hidden-link text-vtop"),NULL,NULL,FALSE,api_text("own_profile-avatar-delete-confirm"));}
 // build profile form
 $form=new cForm("?mod=framework&scr=submit&act=own_profile_update","POST",null,"own_profile");
 $form->addField("static",NULL,api_text("own_profile-mail"),$user->mail);
 $form->addField("text","firstname",api_text("own_profile-firstname"),$user->firstname,api_text("own_profile-firstname-placeholder"),NULL,NULL,8,"required");
 $form->addField("text","lastname",api_text("own_profile-lastname"),$user->lastname,api_text("own_profile-lastname-placeholder"),NULL,NULL,NULL,"required");
 $form->addField("select","localization",api_text("own_profile-localization"),$user->localization,api_text("own_profile-localization-placeholder"),NULL,NULL,NULL,"required");
 foreach($localization->available_localizations as $code=>$language){$form->addFieldOption($code,$language." (".$code.")");}
 $form->addField("select","timezone",api_text("own_profile-timezone"),$user->timezone,api_text("own_profile-timezone-placeholder"),NULL,NULL,NULL,"required");
 foreach(timezone_identifiers_list() as $timezone){$form->addFieldOption($timezone,$timezone." (".api_timestamp_format(time(),"H:i",$timezone).")");}
 $form->addField("radio","gender",api_text("own_profile-gender"),$user->gender,NULL,NULL,"radio-inline");
 $form->addFieldOption("",api_text("own_profile-gender-none"));
 $form->addFieldOption("man",api_text("own_profile-gender-man"));
 $form->addFieldOption("woman",api_text("own_profile-gender-woman"));
 $form->addField("splitter");
 $form->addField("date","birthday",api_text("own_profile-birthday"),$user->birthday);
 $form->addField("file","avatar",api_text("own_profile-avatar"));
 $form->addField("static",NULL,"&nbsp;",api_image($user->avatar,"img-thumbnail",128).$avatar_delete_link);
 // controls
 $form->addControl("submit",api_text("own_profile-submit"));
 $form->addControl("button",api_text("own_profile-cancel"),"?mod=dashboards");
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
?>