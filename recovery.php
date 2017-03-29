<?php
/**
 * Login
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include functions
 require_once("functions.inc.php");
 // build html object
 $html=new HTML($module_name);
 // set html title
 $html->setTitle("Login");
 // build recovery form
 $form=new Form("?mod=accounts&scr=submit&act=user_recovery");
 $form->addField("email","mail",api_text("recovery-ff-account"),NULL,api_text("recovery-ff-account-placeholder"),NULL,NULL,NULL,"required");
 $form->addControl("submit",api_text("recovery-fc-submit"));
 $form->addControl("button",api_text("form-cancel"),"login.php");
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
?>