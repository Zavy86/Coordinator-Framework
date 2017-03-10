<?php
/**
 * Accounts - Users Profile
 *
 * @package Coordinator\Modules\Accounts
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check permissions
 /** @todo check permissions */
 // set html title
 $html->setTitle(api_text("users_edit"));
 // get objects
 $user=new User($_REQUEST['idUser']);
 if(!$user->id){die("USER NOT FOUND");} /** @todo rifare alert come si deve */
 // build profile form
 $form=new Form("?mod=accounts&scr=submit&act=user_profile_update","POST",null,"users_edit");
 $form->addField("static",NULL,$user->fullname,api_image($user->avatar,"img-thumbnail",128));
 $form->addField("checkbox","enabled","&nbsp;",$user->enabled);
 $form->addFieldOption(1,api_text("users_edit-enabled"),$user->enabled);
 $form->addField("text","mail",api_text("users_edit-mail"),$user->mail,api_text("users_edit-firstname-placeholder"),NULL,NULL,8,"required readonly");
 $form->addField("text","firstname",api_text("users_edit-firstname"),$user->firstname,api_text("users_edit-firstname-placeholder"),NULL,NULL,8,"required");
 $form->addField("text","lastname",api_text("users_edit-lastname"),$user->lastname,api_text("users_edit-lastname-placeholder"),NULL,NULL,NULL,"required");
 $form->addField("select","localization",api_text("users_edit-localization"),$user->localization,api_text("users_edit-localization-placeholder"),NULL,NULL,NULL,"required");
 foreach($localization->available_localizations as $code=>$language){$form->addFieldOption($code,$language." (".$code.")");}
 $form->addField("select","timezone",api_text("users_edit-timezone"),$user->timezone,api_text("users_edit-timezone-placeholder"),NULL,NULL,NULL,"required");
 foreach(timezone_identifiers_list() as $timezone){$form->addFieldOption($timezone,$timezone." (".api_timestamp_format(time(),"H:i",$timezone).")");}
 $form->addControl("submit",api_text("users_edit-submit"));
 $form->addControl("button",api_text("users_edit-cancel"),"?mod=settings&scr=users_list");
 $form->addControl("button",api_text("users_edit-delete"),"?mod=settings&scr=submit&act=users_delete&idUser=".$user->id,"btn-danger"); /** @todo fare undelete */
 $form->addControl("button",api_text("users_edit-interpret"),"?mod=settings&scr=submit&act=users_interpret&idUser=".$user->id,"btn-success"); /** @todo check intepret permissions */
 // build comapnies table
 $companies_table=new Table(api_text("users_edit-companies-unvalued"));
 $companies_table->addHeader(api_text("users_edit-companies-th-company"));
 $companies_table->addHeader(api_text("users_edit-companies-th-level"));
 $companies_table->addHeader("&nbsp;");
 // build groups table
 $groups_table=new Table(api_text("users_edit-groups-unvalued"));
 $groups_table->addHeader(api_text("users_edit-groups-th-group"));
 $groups_table->addHeader(api_text("users_edit-groups-th-level"),"text-right");
 $groups_table->addHeader("&nbsp;");
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($form->render("2"),"col-xs-12 col-sm-6");
 $grid->addCol($companies_table->render().$groups_table->render(),"col-xs-12 col-sm-6");
 /*api_dump(time(),"time()");
 api_dump(api_timestamp_format(time(),"Y-m-d H:i:s","Europe/Rome"),"Europe/Rome");
 api_dump(api_timestamp_format(time(),"Y-m-d H:i:s","America/Los_Angeles"),"America/Los_Angeles");
 api_dump(api_timestamp_format(time(),"Y-m-d H:i:s","Asia/Shanghai"),"Asia/Shanghai");*/
 // add content to html
 $html->addContent($grid->render(FALSE));
 // renderize html page
 $html->render();
?>