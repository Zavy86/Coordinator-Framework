<?php
/**
 * Framework - Own Password
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set application title
 $app->setTitle(api_text("own_password"));
 // alert for non standard authenticated accounts
 if($session->user->authentication!="standard"){api_alerts_add(api_text("own_password-authentication-alert"),"warning");}
 // build profile form
 $form=new strForm("?mod=".MODULE."&scr=submit&act=own_password_update","POST",null,null,"own_password");
 $form->addField("password","password",api_text("own_password-password"),null,api_text("own_password-password-placeholder"),null,null,null,"required");
 $form->addField("password","password_new",api_text("own_password-password_new"),null,api_text("own_password-password_new-placeholder"),null,null,null,"required");
 $form->addField("password","password_confirm",api_text("own_password-password_confirm"),null,api_text("own_password-password_confirm-placeholder"),null,null,null,"required");
 $form->addControl("submit",api_text("form-fc-submit"));
 $form->addControl("button",api_text("form-fc-cancel"),"?mod=".MODULE."&scr=own_profile");
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to application
 $app->addContent($grid->render());
 // renderize application
 $app->render();
?>