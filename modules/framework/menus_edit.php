<?php
/**
 * Framework - Menus Edit
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 $authorization="framework-menus_manage";
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // get objects
 $menu_obj=new cMenu($_REQUEST['idMenu']);
 // set html title
 $html->setTitle(($menu_obj->id?api_text("menus_edit"):api_text("menus_add")));
 // build profile form
 $form=new cForm("?mod=framework&scr=submit&act=menu_save&idMenu=".$menu_obj->id,"POST",null,"menus_edit");
 $form->addField("select","fkMenu",api_text("menus_edit-fkMenu"),$menu_obj->fkMenu);
 $form->addFieldOption(null,api_text("menus_edit-fkMenu-main"));
 // cycle all first level menus
 foreach(api_framework_menus(null) as $menu_option_obj){
  if($menu_option_obj->id==$menu_obj->id){continue;}
  $form->addFieldOption($menu_option_obj->id,str_repeat("&nbsp;&nbsp;&nbsp;",$menu_option_obj->nesting).$menu_option_obj->label);
 }
 // icon, label and title
 $form->addField("text","icon",api_text("menus_edit-icon"),$menu_obj->icon,api_text("menus_edit-icon-placeholder"));
 $form->addField("text_localized","label_localizations",api_text("menus_edit-label"),$menu_obj->label_localizations,api_text("menus_edit-label-placeholder"));
 $form->addField("text_localized","title_localizations",api_text("menus_edit-title"),$menu_obj->title_localizations,api_text("menus_edit-title-placeholder"));
 $form->addField("select","authorization",api_text("menus_edit-authorization"),$menu_obj->authorization,api_text("menus_edit-authorization-placeholder"));
 foreach(api_framework_authorizations() as $authorization_fobj){$form->addFieldOption($authorization_fobj->module."|".$authorization_fobj->action,$authorization_fobj->action);}
 $form->addField("radio","target",api_text("menus_edit-target"),$menu_obj->target,null,null,"radio-inline");
 $form->addFieldOption("",api_text("menus_edit-target-standard"));
 $form->addFieldOption("_blank",api_text("menus_edit-target-blank"));
 $form->addField("splitter");
 // typologies
 $form->addField("radio","typology",api_text("menus_edit-typology"),($menu_obj->module?"module":"link"),null,null,"radio-inline");
 $form->addFieldOption("link",api_text("menus_edit-typology-link"));
 $form->addFieldOption("module",api_text("menus_edit-typology-module"));
 // link typology
 $form->addField("text","url",api_text("menus_edit-url"),$menu_obj->url,api_text("menus_edit-url-placeholder"));
 // module typology
 $form->addField("select","module",api_text("menus_edit-module"),$menu_obj->module,api_text("menus_edit-module-placeholder"));
 foreach(api_framework_modules(null) as $module_obj){$form->addFieldOption($module_obj->module,$module_obj->name);}
 $form->addField("text","script",api_text("menus_edit-script"),$menu_obj->script,api_text("menus_edit-script-placeholder"));
 $form->addField("text","tab",api_text("menus_edit-tab"),$menu_obj->tab,api_text("menus_edit-tab-placeholder"));
 $form->addField("text","action",api_text("menus_edit-action"),$menu_obj->action,api_text("menus_edit-action-placeholder"));
 // controls
 $form->addControl("submit",api_text("menus_edit-submit"));
 $form->addControl("button",api_text("menus_edit-cancel"),"?mod=framework&scr=menus_list");
 $form->addControl("button",api_text("menus_edit-delete"),"?mod=framework&scr=submit&act=menus_delete&idMenu=".$menu_obj->id,"btn-danger",api_text("menus_edit-delete-confirm"));
 // jQuery script
 $jquery=<<<EOS
/* Toggle Typology Script */
function menus_edit_toggle_typology() {
 switch($("input[name='typology']:checked").val()){
  // link
  case "link":
   $("#form_menus_edit_input_url_form_group").show();
   $("#form_menus_edit_input_module_form_group").hide();
   $("#form_menus_edit_input_script_form_group").hide();
   $("#form_menus_edit_input_tab_form_group").hide();
   $("#form_menus_edit_input_action_form_group").hide();
   break;
  // module
  case "module":
   $("#form_menus_edit_input_url_form_group").hide();
   $("#form_menus_edit_input_module_form_group").show();
   $("#form_menus_edit_input_script_form_group").show();
   $("#form_menus_edit_input_tab_form_group").show();
   $("#form_menus_edit_input_action_form_group").show();
   break;
 }
}
// window load trigger
$(window).load(function(){menus_edit_toggle_typology();});
// typology change trigger
$(function(){\$("input[name='typology']").change(function(){menus_edit_toggle_typology();});});
EOS;
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // add scripts to html
 $html->addScript($jquery);
 $html->addScript("/* Font Awesome Icon Picker */\n$(function(){\$(\"#form_menus_edit_input_icon\").iconpicker();});");
 // renderize html page
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($menu_obj,"menu");}
?>