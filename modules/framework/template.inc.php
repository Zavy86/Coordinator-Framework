<?php
/**
 * Framework - Template
 *
 * @package Rasmotic\Modules\Settings
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // build html object
 $html=new HTML($module_title);
 // build navbar object
 $nav=new Nav("nav-tabs");

 $nav->setTitle(api_text("framework"));

 $nav->addItem(api_icon("fa-th-large",NULL,"test hidden-link"),"?mod=framework&scr=dashboard");

 // settings
 if(substr(SCRIPT,0,8)=="settings"){
  $nav->addItem(api_text("settings_framework"),"?mod=framework&scr=settings_framework");
 }

 // menus
 if(substr(SCRIPT,0,5)=="menus"){
  // lists
  $nav->addItem(api_text("menus_list"),"?mod=framework&scr=menus_list");
  // menu edit
  if(in_array(SCRIPT,array("menus_edit")) && $_REQUEST['idMenu']){
   $nav->addItem(api_text("nav-operations"));
   $nav->addSubItem(api_text("menus_edit"),"?mod=framework&scr=menus_edit&idMenu=".$_REQUEST['idMenu']);
  }else{
   // menu add
   $nav->addItem(api_text("menus_add"),"?mod=framework&scr=menus_edit");
  }
 }

 // own
 if(substr(SCRIPT,0,3)=="own"){
  $nav->addItem(api_text("own_profile"),"?mod=framework&scr=own_profile");
  $nav->addItem(api_text("own_password"),"?mod=framework&scr=own_password"); /** @todo if auth is standard */
 }

 // users
 if(substr(SCRIPT,0,5)=="users"){
  // lists
  $nav->addItem(api_text("users_list"),"?mod=framework&scr=users_list");
  // template operations
  if(in_array(SCRIPT,array("users_view","users_edit")) && $_REQUEST['idUser']){
   $nav->addItem(api_text("nav-operations"),NULL,"active");
   // users view operations
   if(in_array(SCRIPT,array("users_view"))){
    if(1){ /** @todo check administrators authorization */
     $nav->addSubItem(api_text("nav-operations-user_interpret"),"?mod=framework&scr=submit&act=user_interpret&idUser=".$_REQUEST['idUser'],NULL,api_text("nav-operations-user_interpret-confirm"));
     $nav->addSubSeparator();
    }
    /** @todo check authorizations */
    $nav->addSubItem(api_text("nav-operations-user_edit"),"?mod=framework&scr=users_edit&idUser=".$_REQUEST['idUser']);
    $nav->addSubItem(api_text("nav-operations-user_group_add"),"?mod=framework&scr=users_view&idUser=".$_REQUEST['idUser']."&act=group_add");
   }
   // users edit operations
   if(in_array(SCRIPT,array("users_edit"))){

   }
  }else{
   // users add
   $nav->addItem(api_text("users_add"),"?mod=framework&scr=users_add");
  }
 }

 // groups
 if(substr(SCRIPT,0,6)=="groups"){
  // lists
  $nav->addItem(api_text("groups_list"),"?mod=framework&scr=groups_list");
  // template operations
  if(in_array(SCRIPT,array("groups_view","groups_edit")) && $_REQUEST['idGroup']){
   $nav->addItem(api_text("nav-operations"));
   $nav->addSubItem(api_text("groups_edit"),"?mod=framework&scr=groups_edit&idGroup=".$_REQUEST['idGroup']);
  }else{
   // users add
   $nav->addItem(api_text("groups_add"),"?mod=framework&scr=groups_edit");
  }
 }

 // sessions
 if(substr(SCRIPT,0,8)=="sessions"){
  $nav->addItem(api_text("sessions_list"),"?mod=framework&scr=sessions_list");
 }

 // modules
 if(substr(SCRIPT,0,7)=="modules"){
  // lists
  $nav->addItem(api_text("modules_list"),"?mod=framework&scr=modules_list");
  // module operations
  if(in_array(SCRIPT,array("modules_view")) && $_REQUEST['module']){
   $nav->addItem(api_text("nav-operations"),NULL,"active");
   // get module object
   $module_obj=new Module($_REQUEST['module']);
   // check enabled
   if($module_obj->module<>"framework"){
    if($module_obj->enabled){$nav->addSubItem(api_text("nav-operations-module_disable"),"?mod=framework&scr=submit&act=module_disable&module=".$_REQUEST['module'],NULL,api_text("nav-operations-module_disable-confirm"));}
    else{$nav->addSubItem(api_text("nav-operations-module_enable"),"?mod=framework&scr=submit&act=module_enable&module=".$_REQUEST['module']);}
   }
   // authorizations
   if(count($module_obj->authorizations_array)){
    $nav->addSubSeparator();
    $nav->addSubHeader(api_text("nav-operations-module_authorizations"));
    $nav->addSubItem(api_text("nav-operations-module_authorizations_group_add"),"?mod=framework&scr=modules_view&act=module_authorizations_group_add&module=".$_REQUEST['module']);
    $nav->addSubItem(api_text("nav-operations-module_authorizations_reset"),"?mod=framework&scr=submit&act=module_authorizations_reset&module=".$_REQUEST['module'],NULL,api_text("nav-operations-module_authorizations_reset-confirm"));
   }
  }else{
   // add module
   $nav->addItem(api_text("modules_add"),"?mod=framework&scr=modules_add");
  }
 }

 // add nav to html
 $html->addContent($nav->render(FALSE));
?>