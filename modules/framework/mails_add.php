<?php
/**
 * Framework - Users Edit
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("users_edit"));
 // acquire variables
 $r_recipient=$_REQUEST['recipient'];
 /*$return_mod=$_REQUEST['return_mod'];
 $return_scr=$_REQUEST['return_scr'];
 $return_tab=$_REQUEST['return_tab'];*/
 // make current uri array
 parse_str(parse_url($_SERVER['REQUEST_URI'])['query'],$uri_array);
 unset($uri_array['mod']);
 unset($uri_array['scr']);
 unset($uri_array['tab']);
 unset($uri_array['recipient']);

 api_dump("?mod=framework&scr=submit&act=mail_save&".http_build_query($uri_array));

 // build profile form
 $form=new cForm("?mod=framework&scr=submit&act=mail_save&".http_build_query($uri_array),"POST",null,"mails_add");
 /*$form->addField("hidden","return_mod",null,$return_mod);
 $form->addField("hidden","return_scr",null,$return_scr);
 $form->addField("hidden","return_tab",null,$return_act);*/
 $form->addField("select","sender",api_text("users_edit-sender"),null,api_text("users_edit-sender-placeholder"),null,null,null,"required");
 $form->addFieldOption($GLOBALS['session']->user->mail,$GLOBALS['session']->user->fullname);
 $form->addFieldOption($GLOBALS['settings']->sendmail_from_mail,$GLOBALS['settings']->sendmail_from_name);
 $form->addField("text","recipient",api_text("users_edit-recipient"),$r_recipient,api_text("users_edit-recipient-placeholder"),null,null,null,"required");
 $form->addField("text","subject",api_text("users_edit-subject"),null,api_text("users_edit-subject-placeholder"),null,null,null,"required");
 $form->addField("textarea","message",api_text("users_edit-message"),null,api_text("users_edit-message-placeholder"),null,null,null,"required rows='9'");
 // controls
 $form->addControl("submit",api_text("users_edit-submit"));
 $form->addControl("button",api_text("users_edit-cancel"),"?mod=framework&scr=users_view&idUser=".$user->id);
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