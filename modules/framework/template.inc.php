<?php
/**
 * Framework - Template
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // build application
 $app=new strApplication();
 // build nav object
 $nav=new strNav("nav-tabs");
 //$nav->setTitle(api_text(MODULE));
 // dashboard
 $nav->addItem(api_icon("fa-th-large",null,"hidden-link"),"?mod=".MODULE."&scr=dashboard");
 // own
 if(substr(SCRIPT,0,3)=="own"){
  $nav->addItem(api_text("own_profile"),"?mod=".MODULE."&scr=own_profile");
  if($session->user->authentication=="standard"){$nav->addItem(api_text("own_password"),"?mod=".MODULE."&scr=own_password");}
 }
 // settings
 if(substr(SCRIPT,0,8)=="settings"){
  $nav->addItem(api_text("settings_edit"),"?mod=".MODULE."&scr=settings_edit");
 }
 // menus
 if(substr(SCRIPT,0,5)=="menus"){
  // lists
  $nav->addItem(api_text("menus_list"),"?mod=".MODULE."&scr=menus_list");
  // menu edit
  if(in_array(SCRIPT,array("menus_edit")) && $menu_obj->id){
   $nav->addItem(api_text("nav-operations"));
   $nav->addSubItem(api_text("menus_edit"),"?mod=".MODULE."&scr=menus_edit&idMenu=".$menu_obj->id);
  }else{
   // menu add
   $nav->addItem(api_text("menus_edit-add"),"?mod=".MODULE."&scr=menus_edit");
  }
 }
 // modules
 if(substr(SCRIPT,0,7)=="modules"){
  // lists
  $nav->addItem(api_text("modules_list"),"?mod=".MODULE."&scr=modules_list");
  // module operations
  if(in_array(SCRIPT,array("modules_view")) && $module_obj->id){
   $nav->addItem(api_text("nav-operations"),null,null,"active");
   // check enabled
   if($module_obj->id<>"framework"){
    if($module_obj->enabled){$nav->addSubItem(api_text("nav-operations-module_disable"),"?mod=".MODULE."&scr=submit&act=module_disable&idModule=".$module_obj->id,true,api_text("nav-operations-module_disable-confirm"));}
    else{$nav->addSubItem(api_text("nav-operations-module_enable"),"?mod=".MODULE."&scr=submit&act=module_enable&idModule=".$module_obj->id);}
   }else{
    // disabled disable for framework
    $nav->addSubItem(api_text("nav-operations-module_disable"),"#",false);
   }
   // authorizations
   if(count($module_obj->authorizations_array)){
    $nav->addSubSeparator();
    $nav->addSubHeader(api_text("nav-operations-module_authorizations"));
    $nav->addSubItem(api_text("nav-operations-module_authorizations_group_add"),"?mod=".MODULE."&scr=modules_view&act=module_authorizations_group_add&idModule=".$module_obj->id);
    $nav->addSubItem(api_text("nav-operations-module_authorizations_reset"),"?mod=".MODULE."&scr=submit&act=module_authorizations_reset&idModule=".$module_obj->id,true,api_text("nav-operations-module_authorizations_reset-confirm"));
   }
  }else{
   // add module
   $nav->addItem(api_text("modules_add"),"?mod=".MODULE."&scr=modules_add");
  }
 }
 // users
 if(substr(SCRIPT,0,5)=="users"){
  // lists
  $nav->addItem(api_text("users_list"),"?mod=".MODULE."&scr=users_list");
  // users view or edit
  if(in_array(SCRIPT,array("users_view","users_edit"))){
   // users view operations
   if(SCRIPT=="users_view"){
    $nav->addItem(api_text("nav-operations"),null,null,"active");
    // check for deleted
    if($user_obj->deleted){
     $nav->addSubItem(api_text("nav-operations-user_undelete"),"?mod=".MODULE."&scr=submit&act=user_undelete&idUser=".$user_obj->id,true,api_text("nav-operations-user_undelete-confirm"));
    }else{
     // check superuser authorization
     if($GLOBALS['session']->user->superuser && $user_obj->id!=$GLOBALS['session']->user->id){
      $nav->addSubItem(api_text("nav-operations-user_interpret"),"?mod=".MODULE."&scr=submit&act=user_interpret&idUser=".$user_obj->id,true,api_text("nav-operations-user_interpret-confirm"));
      $nav->addSubSeparator();
     }
     $nav->addSubItem(api_text("nav-operations-user_edit"),"?mod=".MODULE."&scr=users_edit&idUser=".$user_obj->id);
     if($user_obj->enabled){$nav->addSubItem(api_text("nav-operations-user_disable"),"?mod=".MODULE."&scr=submit&act=user_disable&idUser=".$user_obj->id);}
     else{$nav->addSubItem(api_text("nav-operations-user_enable"),"?mod=".MODULE."&scr=submit&act=user_enable&idUser=".$user_obj->id);}
     $nav->addSubItem(api_text("nav-operations-user_group_add"),"?mod=".MODULE."&scr=users_view&act=group_add&idUser=".$user_obj->id);
     $nav->addSubItem(api_text("nav-operations-user_parameters_view"),"?mod=".MODULE."&scr=users_view&act=parameters_view&idUser=".$user_obj->id);
    }
   }
   // users edit
   if(SCRIPT=="users_edit"){$nav->addItem(api_text("users_edit"),"?mod=".MODULE."&scr=users_edit");}
  }else{
   // users add
   $nav->addItem(api_text("users_add"),"?mod=".MODULE."&scr=users_add");
  }
 }
 // groups
 if(substr(SCRIPT,0,6)=="groups"){
  // lists
  $nav->addItem(api_text("groups_list"),"?mod=".MODULE."&scr=groups_list");
  // template operations
  if(in_array(SCRIPT,array("groups_view","groups_edit")) && $group_obj->id){
   $nav->addItem(api_text("nav-operations"),null,null,"active");
   $nav->addSubItem(api_text("groups_edit"),"?mod=".MODULE."&scr=groups_edit&idGroup=".$group_obj->id);
  }else{
   // users add
   $nav->addItem(api_text("groups_edit-add"),"?mod=".MODULE."&scr=groups_edit");
  }
 }
 // sessions
 if(substr(SCRIPT,0,8)=="sessions"){
  $nav->addItem(api_text("sessions_list"),"?mod=".MODULE."&scr=sessions_list");
 }
 // mails
 if(substr(SCRIPT,0,5)=="mails"){
  // lists
  $nav->addItem(api_text("mails_list"),"?mod=".MODULE."&scr=mails_list");
  // mails add
  if(SCRIPT=="mails_add"){$nav->addItem(api_text("mails_add"),"?mod=".MODULE."&scr=mails_add");}
 }
 // attachments
 if(substr(SCRIPT,0,11)=="attachments"){
  $nav->addItem(api_text("attachments_list"),"?mod=".MODULE."&scr=attachments_list");
  $nav->addItem(api_text("attachments_add"),"?mod=".MODULE."&scr=attachments_add");
 }
 // add nav to html
 $app->addContent($nav->render(false));
?>