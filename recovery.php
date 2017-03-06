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
 $form->addField("email","mail","Indirizzo mail",NULL,"Inserisci il tuo idirizzo mail..",NULL,NULL,NULL,"required");
 $form->addControl("submit","Send recovery link");
 $form->addControl("button","Cancel","login.php");
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render(FALSE));
 // renderize html page
 $html->render();
?>