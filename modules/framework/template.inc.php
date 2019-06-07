<?php
/**
 * Framework - Template
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */

 // build application object
 $app=new strApplication();
 // build nav object
 $nav=new strNav("nav-tabs");
 $nav->setTitle(api_text(MODULE));
 // dashboard
 $nav->addItem(api_icon("fa-th-large",null,"hidden-link"),"?mod=".MODULE."&scr=dashboard");
 // settings
 if(substr(SCRIPT,0,8)=="settings"){
  $nav->addItem(api_text("settings_edit"),"?mod=".MODULE."&scr=settings_edit");
 }





 // menus
 if(substr(SCRIPT,0,5)=="menus"){
  // lists
  $nav->addItem(api_text("menus_list"),"?mod=".MODULE."&scr=menus_list");
  // menu edit
  if(in_array(SCRIPT,array("menus_edit")) && $_REQUEST['idMenu']){
   $nav->addItem(api_text("nav-operations"));
   $nav->addSubItem(api_text("menus_edit"),"?mod=".MODULE."&scr=menus_edit&idMenu=".$_REQUEST['idMenu']);
  }else{
   // menu add
   $nav->addItem(api_text("menus_add"),"?mod=".MODULE."&scr=menus_edit");
  }
 }

 // own
 if(substr(SCRIPT,0,3)=="own"){
  $nav->addItem(api_text("own_profile"),"?mod=".MODULE."&scr=own_profile");
  if($GLOBALS['settings']->sessions_authentication_method=="standard"){$nav->addItem(api_text("own_password"),"?mod=".MODULE."&scr=own_password");}
 }

 // users
 if(substr(SCRIPT,0,5)=="users"){
  // lists
  $nav->addItem(api_text("users_list"),"?mod=".MODULE."&scr=users_list");
  // users view or edit
  if(in_array(SCRIPT,array("users_view","users_edit"))){
   // users view operations
   if(SCRIPT=="users_view"){  /** @todo check authorizations */
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
     /** @todo popup parametri personali */
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
  if(in_array(SCRIPT,array("groups_view","groups_edit")) && $_REQUEST['idGroup']){
   $nav->addItem(api_text("nav-operations"),null,null,"active");
   $nav->addSubItem(api_text("groups_edit"),"?mod=".MODULE."&scr=groups_edit&idGroup=".$_REQUEST['idGroup']);
  }else{
   // users add
   $nav->addItem(api_text("groups_add"),"?mod=".MODULE."&scr=groups_edit");
  }
 }

 // sessions
 if(substr(SCRIPT,0,8)=="sessions"){
  $nav->addItem(api_text("sessions_list"),"?mod=".MODULE."&scr=sessions_list");
 }

 // modules
 if(substr(SCRIPT,0,7)=="modules"){
  // lists
  $nav->addItem(api_text("modules_list"),"?mod=".MODULE."&scr=modules_list");
  // module operations
  if(in_array(SCRIPT,array("modules_view")) && $_REQUEST['idModule']){
   $nav->addItem(api_text("nav-operations"),null,null,"active");
   // get module object
   $module_obj=new cModule($_REQUEST['idModule']);
   // check enabled
   if($module_obj->id<>"framework"){
    if($module_obj->enabled){$nav->addSubItem(api_text("nav-operations-module_disable"),"?mod=".MODULE."&scr=submit&act=module_disable&idModule=".$_REQUEST['idModule'],true,api_text("nav-operations-module_disable-confirm"));}
    else{$nav->addSubItem(api_text("nav-operations-module_enable"),"?mod=".MODULE."&scr=submit&act=module_enable&idModule=".$_REQUEST['idModule']);}
   }else{
    // disabled disable for framework
    $nav->addSubItem(api_text("nav-operations-module_disable"),"#",false);
   }
   // authorizations
   if(count($module_obj->authorizations_array)){
    $nav->addSubSeparator();
    $nav->addSubHeader(api_text("nav-operations-module_authorizations"));
    $nav->addSubItem(api_text("nav-operations-module_authorizations_group_add"),"?mod=".MODULE."&scr=modules_view&act=module_authorizations_group_add&idModule=".$_REQUEST['idModule']);
    $nav->addSubItem(api_text("nav-operations-module_authorizations_reset"),"?mod=".MODULE."&scr=submit&act=module_authorizations_reset&idModule=".$_REQUEST['idModule'],true,api_text("nav-operations-module_authorizations_reset-confirm"));
   }
  }else{
   // add module
   $nav->addItem(api_text("modules_add"),"?mod=".MODULE."&scr=modules_add");
  }
 }

 // mails
 if(substr(SCRIPT,0,5)=="mails"){
  // lists
  $nav->addItem(api_text("mails_list"),"?mod=".MODULE."&scr=mails_list");
  // menu edit
  /*if(in_array(SCRIPT,array("mails_edit")) && $_REQUEST['idMenu']){
   $nav->addItem(api_text("nav-operations"));
   $nav->addSubItem(api_text("menus_edit"),"?mod=".MODULE."&scr=menus_edit&idMenu=".$_REQUEST['idMenu']);
  }else{
   // menu add
   $nav->addItem(api_text("menus_add"),"?mod=".MODULE."&scr=menus_edit");
  }*/
 }

 // attachments
 if(substr(SCRIPT,0,11)=="attachments"){
  $nav->addItem(api_text("attachments_list"),"?mod=".MODULE."&scr=attachments_list");
  $nav->addItem(api_text("attachments_add"),"?mod=".MODULE."&scr=attachments_add");
 }

 // add nav to html
 $app->addContent($nav->render(false));
?>