<?php
/**
 * Framework - Menus Edit
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // check authorizations
 api_checkAuthorization("framework-menus_manage","dashboard");
 // get objects
 $menu_obj=new cMenu($_REQUEST['idMenu']);
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set application title
 $app->setTitle(($menu_obj->id?api_text("menus_edit",$menu_obj->label):api_text("menus_edit-add")));
 // build profile form
 $form=new strForm("?mod=".MODULE."&scr=submit&act=menu_save&idMenu=".$menu_obj->id,"POST",null,null,"menus_edit");
 // typologies
 $form->addField("radio","typology",api_text("menus_edit-typology"),($menu_obj->typology?$menu_obj->typology:"standard"),null,null,"radio-inline");
 $form->addFieldOption("standard",api_text("menus_edit-typology-standard"));
 $form->addFieldOption("link",api_text("menus_edit-typology-link"));
 $form->addFieldOption("group",api_text("menus_edit-typology-group"));
 // main menu
 $form->addField("select","fkMenu",api_text("menus_edit-fkMenu"),$menu_obj->fkMenu);
 $form->addFieldOption(null,api_text("menus_edit-fkMenu-main"));
 // cycle all first level menus
 foreach(api_availableMenus(null) as $menu_option_obj){
  if($menu_option_obj->typology!="group" || $menu_option_obj->id==$menu_obj->id){continue;}
  $form->addFieldOption($menu_option_obj->id,str_repeat("&nbsp;&nbsp;&nbsp;",$menu_option_obj->nesting).$menu_option_obj->label);
 }
 // label, title and icon
 $form->addField("text_localized","label_localizations",api_text("menus_edit-label"),$menu_obj->label_localizations,api_text("menus_edit-label-placeholder"));
 $form->addField("text_localized","title_localizations",api_text("menus_edit-title"),$menu_obj->title_localizations,api_text("menus_edit-title-placeholder"));
 $form->addField("text","icon",api_text("menus_edit-icon"),$menu_obj->icon,api_text("menus_edit-icon-placeholder"),null,null,null,"autocomplete='off'");
 $form->addField("splitter");
 // standard typology
 $form->addField("select","module",api_text("menus_edit-module"),$menu_obj->module,api_text("menus_edit-module-placeholder"));
 foreach(api_availableModules() as $module_obj){$form->addFieldOption($module_obj->id,$module_obj->name);}
 $form->addField("text","script",api_text("menus_edit-script"),$menu_obj->script,api_text("menus_edit-script-placeholder"));
 $form->addField("text","tab",api_text("menus_edit-tab"),$menu_obj->tab,api_text("menus_edit-tab-placeholder"));
 $form->addField("text","action",api_text("menus_edit-action"),$menu_obj->action,api_text("menus_edit-action-placeholder"));
 // link typology
 $form->addField("text","url",api_text("menus_edit-url"),$menu_obj->url,api_text("menus_edit-url-placeholder"));
 // authorization
 $form->addField("select","authorization",api_text("menus_edit-authorization"),(strpos($menu_obj->authorization,"|*")?"module|*":$menu_obj->authorization));
 $form->addFieldOption("",api_text("menus_edit-authorization-none"));
 $form->addFieldOption("module|*",api_text("menus_edit-authorization-module"));
 foreach(api_availableAuthorizations() as $authorization_fobj){$form->addFieldOption($authorization_fobj->fkModule."|".$authorization_fobj->id,$authorization_fobj->id);}
 // target
 $form->addField("radio","target",api_text("menus_edit-target"),$menu_obj->target,null,null,"radio-inline");
 $form->addFieldOption("",api_text("menus_edit-target-standard"));
 $form->addFieldOption("_blank",api_text("menus_edit-target-blank"));
 // controls
 $form->addControl("submit",api_text("menus_edit-submit"));
 $form->addControl("button",api_text("menus_edit-cancel"),"?mod=".MODULE."&scr=menus_list");
 $form->addControl("button",api_text("menus_edit-delete"),"?mod=".MODULE."&scr=submit&act=menus_delete&idMenu=".$menu_obj->id,"btn-danger",api_text("menus_edit-delete-confirm"));
 // jQuery script
 $jquery=<<<EOS
/* Toggle Typology Script */
function menus_edit_toggle_typology() {
 switch($("input[name='typology']:checked").val()){
  // standard
  case "standard":
   $("#form_menus_edit_input_url_form_group").hide();
   $("#form_menus_edit_input_module_form_group").show();
   $("#form_menus_edit_input_script_form_group").show();
   $("#form_menus_edit_input_tab_form_group").show();
   $("#form_menus_edit_input_action_form_group").show();
   $("#form_menus_edit_input_target_form_group").show();
   $("#form_menus_edit_input_authorization_form_group").show();
   $("#form_menus_edit_input_fkMenu").attr("readonly",false);
   $("#form_menus_edit_input_authorization_option_1").attr("disabled",false);
   break;
  // link
  case "link":
   $("#form_menus_edit_input_url_form_group").show();
   $("#form_menus_edit_input_module_form_group").hide();
   $("#form_menus_edit_input_script_form_group").hide();
   $("#form_menus_edit_input_tab_form_group").hide();
   $("#form_menus_edit_input_action_form_group").hide();
   $("#form_menus_edit_input_target_form_group").show();
   $("#form_menus_edit_input_authorization_form_group").show();
   $("#form_menus_edit_input_fkMenu").attr("readonly",false);
   $("#form_menus_edit_input_authorization_option_1").attr("disabled",true);
   break;
  // group
  case "group":
   $("#form_menus_edit_input_url_form_group").hide();
   $("#form_menus_edit_input_module_form_group").hide();
   $("#form_menus_edit_input_script_form_group").hide();
   $("#form_menus_edit_input_tab_form_group").hide();
   $("#form_menus_edit_input_action_form_group").hide();
   $("#form_menus_edit_input_target_form_group").hide();
   $("#form_menus_edit_input_authorization_form_group").hide();
   $("#form_menus_edit_input_fkMenu").attr("readonly",true);
   $("#form_menus_edit_input_fkMenu").val("");
   break;
 }
}
// window load trigger
$(window).load(function(){menus_edit_toggle_typology();});
// typology change trigger
$(function(){\$("input[name='typology']").change(function(){menus_edit_toggle_typology();});});
EOS;
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to application
 $app->addContent($grid->render());
 // add scripts to html
 $app->addScript($jquery);
 $app->addScript("/* Font Awesome Icon Picker */\n$(function(){\$(\"#form_menus_edit_input_icon\").iconpicker();});");
 // renderize application
 $app->render();
 // debug
 api_dump($menu_obj,"menu");
?>