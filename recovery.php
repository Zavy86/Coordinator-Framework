<?php
/**
 * Recovery
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include functions
 require_once("functions.inc.php");
 // build html object
 $html=new strApplication($module_name);
 // set html title
 $html->setTitle("Recovery");
 // build recovery form
 $form=new strForm("?mod=framework&scr=submit&act=user_recovery");
 $form->addField("email","mail",api_text("recovery-ff-account"),null,api_text("recovery-ff-account-placeholder"),null,null,null,"required");
 $form->addControl("submit",api_text("recovery-fc-submit"));
 $form->addControl("button",api_text("form-fc-cancel"),"login.php");
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
?>