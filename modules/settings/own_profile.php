<?php
/**
 * Settings - Users Profile
 *
 * @package Coordinator\Modules\Settings
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check permissions
 /** @todo check permissions */
 // set html title
 $html->setTitle(api_text("own_profile"));
 // get objects
 $user=$session->user;
 // build profile form
 $form=new Form("?mod=settings&scr=submit&act=own_profile_update","POST",null,"own_profile");
 $form->addField("static",NULL,$user->fullname,api_image($user->avatar,"img-thumbnail",128));
 $form->addField("static",NULL,api_text("own_profile-mail"),$user->mail);
 $form->addField("text","firstname",api_text("own_profile-firstname"),$user->firstname,api_text("own_profile-firstname-placeholder"),NULL,NULL,8,"required");
 $form->addField("text","lastname",api_text("own_profile-lastname"),$user->lastname,api_text("own_profile-lastname-placeholder"),NULL,NULL,NULL,"required");
 $form->addField("select","localization",api_text("own_profile-localization"),$user->localization,api_text("own_profile-localization-placeholder"),NULL,NULL,NULL,"required");
 foreach($localization->available_localizations as $code=>$language){$form->addFieldOption($code,$language." (".$code.")");}
 $form->addField("select","timezone",api_text("own_profile-timezone"),$user->timezone,api_text("own_profile-timezone-placeholder"),NULL,NULL,NULL,"required");
 foreach(timezone_identifiers_list() as $timezone){$form->addFieldOption($timezone,$timezone." (".api_timestamp_format(time(),"H:i",$timezone).")");}
 $form->addField("file","avatar",api_text("own_profile-avatar"));
 $form->addControl("submit",api_text("own_profile-submit"));
 // build comapnies table
 $companies_table=new Table(api_text("own_profile-companies-unvalued"));
 $companies_table->addHeader(api_text("own_profile-companies-th-company"));
 $companies_table->addHeader(api_text("own_profile-companies-th-level"));
 $companies_table->addHeader("&nbsp;");
 // build groups table
 $groups_table=new Table(api_text("own_profile-groups-unvalued"));
 $groups_table->addHeader(api_text("own_profile-groups-th-group"));
 $groups_table->addHeader(api_text("own_profile-groups-th-level"),"text-right");
 $groups_table->addHeader("&nbsp;");
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($form->render("2"),"col-xs-12 col-sm-6");
 $grid->addCol($companies_table->render().$groups_table->render(),"col-xs-12 col-sm-6");
 // add content to html
 $html->addContent($grid->render(FALSE));
 // renderize html page
 $html->render();
?>