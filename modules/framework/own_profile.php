<?php
/**
 * Framework - Own Profile
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set application title
 $app->setTitle(api_text("own_profile"));
 // get objects
 $user_obj=$session->user;
 // make avatar delete link
 if(is_numeric(substr($user_obj->avatar,-5,1))){$avatar_delete_link=api_link("?mod=".MODULE."&scr=submit&act=own_avatar_remove",api_icon("fa-remove",api_text("own_profile-avatar-delete"),"hidden-link text-vtop"),null,null,false,api_text("own_profile-avatar-delete-confirm"));}
 // build profile form
 $form=new strForm("?mod=".MODULE."&scr=submit&act=own_profile_update","POST",null,null,"own_profile");
 if($user_obj->username){$form->addField("static",null,api_text("own_profile-username"),$user_obj->username);}
 $form->addField("static",null,api_text("own_profile-mail"),$user_obj->mail);
 $form->addField("text","firstname",api_text("own_profile-firstname"),$user_obj->firstname,null,null,null,null,"required");
 $form->addField("text","lastname",api_text("own_profile-lastname"),$user_obj->lastname,null,null,null,null,"required");
 $form->addField("select","localization",api_text("own_profile-localization"),$user_obj->localization,api_text("own_profile-localization-placeholder"),null,null,null,"required");
 foreach($localization->available_localizations as $key=>$label){$form->addFieldOption($key,$label." (".$key.")");}
 $form->addField("select","timezone",api_text("own_profile-timezone"),$user_obj->timezone,api_text("own_profile-timezone-placeholder"),null,null,null,"required");
 foreach(timezone_identifiers_list() as $timezone_f){$form->addFieldOption($timezone_f,$timezone_f." (".api_timestamp_format(time(),"H:i",$timezone_f).")");}
 $form->addField("radio","gender",api_text("own_profile-gender"),$user_obj->gender,null,null,"radio-inline");
 $form->addFieldOption(null,api_text("own_profile-gender-none"));
 $form->addFieldOption("man",api_text("own_profile-gender-man"));
 $form->addFieldOption("woman",api_text("own_profile-gender-woman"));
 $form->addField("splitter");
 $form->addField("date","birthday",api_text("own_profile-birthday"),$user_obj->birthday);
 $form->addField("file","avatar",api_text("own_profile-avatar"),null,null,null,null,null,"accept='.jpg'");
 $form->addField("static",null,"&nbsp;",api_image($user_obj->avatar,"img-thumbnail",128).$avatar_delete_link);
 // controls
 $form->addControl("submit",api_text("form-fc-save"));
 $form->addControl("button",api_text("form-fc-cancel"),"?mod=dashboard");
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to application
 $app->addContent($grid->render());
 // renderize application
 $app->render();
 // debug
 api_dump($user_obj,"user");
?>