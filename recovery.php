<?php
/**
 * Recovery
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // include functions
 require_once("initializations.inc.php");
 // build application
 $app=new strApplication();
 // set application title
 $app->setTitle("Recovery");
 // build recovery form
 $form=new strForm("?mod=framework&scr=submit&act=own_password_recovery");
 $form->addField("email","mail",api_text("recovery-ff-account"),null,api_text("recovery-ff-account-placeholder"),null,null,null,"required");
 $form->addControl("submit",api_text("recovery-fc-submit"));
 $form->addControl("button",api_text("form-fc-cancel"),"login.php");
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to application
 $app->addContent($grid->render());
 // renderize application
 $app->render();
?>