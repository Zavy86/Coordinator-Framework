<?php
/**
 * Framework - Mail Add
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // check authorizations
 api_checkAuthorization("framework-mails_manage","dashboard");
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set application title
 $app->setTitle(api_text("mails_add"));
 // acquire variables
 $r_recipient=$_REQUEST['recipient'];
 // make current uri array
 parse_str(parse_url($_SERVER['REQUEST_URI'])['query'],$uri_array);
 unset($uri_array['mod']);
 unset($uri_array['scr']);
 unset($uri_array['tab']);
 unset($uri_array['recipient']);
 //api_dump("?mod=".MODULE."&scr=submit&act=mail_save&".http_build_query($uri_array));
 // build profile form
 $form=new strForm("?mod=".MODULE."&scr=submit&act=mail_save&".http_build_query($uri_array),"POST",null,null,"mails_add");
 $form->addField("select","sender",api_text("mails_add-sender"),null,api_text("mails_add-sender-placeholder"),null,null,null,"required");
 $form->addFieldOption($GLOBALS['session']->user->mail,$GLOBALS['session']->user->fullname);
 $form->addFieldOption($GLOBALS['settings']->mail_from_address,$GLOBALS['settings']->mail_from_name);
 $form->addField("email","recipient",api_text("mails_add-recipient"),$r_recipient,api_text("mails_add-recipient-placeholder"),null,null,null,"required");
 $form->addField("text","subject",api_text("mails_add-subject"),null,api_text("mails_add-subject-placeholder"),null,null,null,"required");
 $form->addField("textarea","message",api_text("mails_add-message"),null,api_text("mails_add-message-placeholder"),null,null,null,"required rows='9'");
 // controls
 $form->addControl("submit",api_text("form-fc-submit"));
 $form->addControl("button",api_text("form-fc-cancel"),"?mod=".MODULE."&scr=mails_view&idMail=".$mail->id);
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to application
 $app->addContent($grid->render());
 // renderize application
 $app->render();
?>