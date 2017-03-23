<?php
/**
 * Framework - Module Add
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check authorizations
 /** @todo check authorizations */
 // set html title
 $html->setTitle(api_text("users_add"));
 // build profile form
 $form=new Form("?mod=framework&scr=submit&act=module_add","POST",null,"modules_add");
 $form->addField("text","url",api_text("modules_add-url"),NULL,api_text("modules_add-url-placeholder"),NULL,NULL,NULL,"required");
 $form->addField("radio","method",api_text("modules_add-method"),NULL,NULL,NULL,"radio-inline");
 $form->addFieldOption("git",api_text("modules_add-method-git"));
 $form->addFieldOption("zip",api_text("modules_add-method-zip"));
 $form->addControl("submit",api_text("modules_add-submit"));
 $form->addControl("button",api_text("modules_add-cancel"),"?mod=framework&scr=modules_list");
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // jQuery script
 $jquery = <<< EOT
/* Popover Script */
$(function(){
 $("input[name='url']").change(function(){
  var url=$("input[name='url']").val();
  var ext=url.substr(url.length-4).toLowerCase();
  if(ext===".git"){
   $("input[name='method'][value='git']").prop("checked", true)
  }else if(ext===".zip"){
   $("input[name='method'][value='zip']").prop("checked", true)
  }
 });
});
EOT;
 // add script to html
 $html->addScript($jquery);
 // renderize html page
 $html->render();
?>