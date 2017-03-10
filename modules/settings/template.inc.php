<?php
/**
 * Settings - Template
 *
 * @package Rasmotic\Modules\Settings
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // build html object
 $html=new HTML($module_title);
 // build navbar object
 $nav=new Nav("nav-tabs");

 $nav->setTitle(api_text("settings"));

 $nav->addItem(api_icon("gift"),"?mod=settings&scr=dashboard");

 // settings
 if(substr(SCRIPT,0,8)=="settings"){
  $nav->addItem(api_text("settings_framework"),"?mod=settings&scr=settings_framework");
 }

 // users
 if(substr(SCRIPT,0,5)=="users"){
  // lists
  $nav->addItem(api_text("users_list"),"?mod=settings&scr=users_list");
  // template operations
  if(in_array(SCRIPT,array("users_view","users_edit")) && $_REQUEST['idUser']){
   $nav->addItem(api_text("nav-operations"));
   $nav->addSubItem(api_text("users_edit"),"?mod=settings&scr=users_edit&idUser=".$_REQUEST['idUser']);
  }else{
   // users add
   $nav->addItem(api_text("users_add"),"?mod=settings&scr=users_add");
  }
 }

 // sessions
 if(substr(SCRIPT,0,8)=="sessions"){
  $nav->addItem(api_text("sessions_list"),"?mod=settings&scr=sessions_list");
 }

 // add nav to html
 $html->addContent($nav->render(FALSE));
?>