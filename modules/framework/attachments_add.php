<?php
/**
 * Framework - Attachments Add
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gattachment.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("attachments_add"));
 // build profile form
 $form=new cForm("?mod=".MODULE."&scr=submit&act=attachment_save","POST",null,"attachments_add");
 $form->addField("file","file",api_text("attachments_add-ff-file"),null,null,null,null,null,"required");
 $form->addField("textarea","description",api_text("attachments_add-ff-description"),null,api_text("attachments_add-ff-description-placeholder"));
 $form->addField("radio","public",api_text("attachments_add-ff-public"),0,null,null,"radio-inline");
 $form->addFieldOption(0,api_text("no"));
 $form->addFieldOption(1,api_text("yes"));
 // controls
 $form->addControl("submit",api_text("form-fc-submit"));
 $form->addControl("button",api_text("form-fc-cancel"),"?mod=".MODULE."&scr=attachments_list");
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($attachment,"attachment");}
?>