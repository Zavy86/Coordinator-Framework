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
 $html->setTitle(api_text("users_add"));
 // build profile form
 $form=new Form("?mod=settings&scr=submit&act=user_add","POST",null,"users_add");
 $form->addField("text","mail",api_text("users_add-mail"),$user->mail,api_text("users_add-mail-placeholder"),NULL,NULL,8,"required");
 $form->addField("text","firstname",api_text("users_add-firstname"),$user->firstname,api_text("users_add-firstname-placeholder"),NULL,NULL,8,"required");
 $form->addField("text","lastname",api_text("users_add-lastname"),$user->lastname,api_text("users_add-lastname-placeholder"),NULL,NULL,NULL,"required");
 $form->addField("select","localization",api_text("users_add-localization"),$session->user->localization,api_text("users_add-localization-placeholder"),NULL,NULL,NULL,"required");
 foreach($localization->available_localizations as $code=>$language){$form->addFieldOption($code,$language." (".$code.")");}
 $form->addField("select","timezone",api_text("users_add-timezone"),$session->user->timezone,api_text("users_add-timezone-placeholder"),NULL,NULL,NULL,"required");
 foreach(timezone_identifiers_list() as $timezone){$form->addFieldOption($timezone,$timezone." (".api_timestamp_format(time(),"H:i",$timezone).")");}
 $form->addControl("submit",api_text("users_add-submit"));
 $form->addControl("button",api_text("users_add-cancel"),"?mod=settings&scr=users_list");
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($form->render("2"),"col-xs-12 col-sm-6");
 /*api_dump(time(),"time()");
 api_dump(api_timestamp_format(time(),"Y-m-d H:i:s","Europe/Rome"),"Europe/Rome");
 api_dump(api_timestamp_format(time(),"Y-m-d H:i:s","America/Los_Angeles"),"America/Los_Angeles");
 api_dump(api_timestamp_format(time(),"Y-m-d H:i:s","Asia/Shanghai"),"Asia/Shanghai");*/
 // add content to html
 $html->addContent($grid->render(FALSE));
 // renderize html page
 $html->render();
?>