<?php
/**
 * Template
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // set meta tags
 $this->setMetaTag("author","Manuel Zavatta [www.zavynet.org]");
 $this->setMetaTag("copyright","2009-".date("Y")." © Coordinator [www.coordinator.it]");
 $this->setMetaTag("description","Coordinator is an Open Source Modular Framework");
 $this->setMetaTag("owner",$GLOBALS['settings']->owner);
 // add style sheets
 $this->addStylesheet(HELPERS."pace/css/pace-1.0.0-theme-flash.css");
 $this->addStylesheet(HELPERS."font-awesome/css/font-awesome.min.css");
 $this->addStylesheet(HELPERS."font-awesome-animation/css/font-awesome-animation.min.css");
 /** @todo verificare quali caricare sempre e quali solo alla bisogna */
 $this->addStylesheet(HELPERS."justgage/css/justgage-1.2.2.css");
 $this->addStylesheet(HELPERS."select2/css/select2-4.0.5.min.css");
 /** @todo add some css helpers here */
 $this->addStylesheet(HELPERS."bootstrap/css/bootstrap-3.3.7.min.css");
 $this->addStylesheet(HELPERS."bootstrap-faiconpicker/css/bootstrap-faiconpicker-1.3.0.min.css");
 /** @todo definire temi */
 /*if($GLOBALS['session']->user->theme){$this->addStylesheet(HELPERS."bootstrap/css/bootstrap-3.3.7-theme-".$GLOBALS['session']->user->theme.".min.css");}*/
 $this->addStylesheet(HELPERS."bootstrap/css/bootstrap-3.3.7-custom.css");
 // add scripts
 $this->addScript(HELPERS."jquery/js/jquery-1.12.4.min.js",true);
 $this->addScript(HELPERS."pace/js/pace-1.0.0.min.js",true);
 /** @todo verificare quali caricare sempre e quali solo alla bisogna */
 $this->addScript(HELPERS."peity/js/peity-3.2.1.min.js",true);
 $this->addScript(HELPERS."justgage/js/justgage-1.2.2.js",true);
 $this->addScript(HELPERS."chartjs/js/chart-2.7.0.min.js",true);
 $this->addScript(HELPERS."select2/js/select2-4.0.5.min.js",true);
 /** @todo add some javascript helpers here */
 $this->addScript(HELPERS."bootstrap/js/bootstrap-3.3.7.min.js",true);
 $this->addScript(HELPERS."bootstrap-filestyle/js/bootstrap-filestyle-1.2.1.min.js",true);
 $this->addScript(HELPERS."bootstrap-faiconpicker/js/bootstrap-faiconpicker-1.3.0.min.js",true);
 // build header navbar object
 $header_navbar=new strNavbar($GLOBALS['settings']->title,"navbar-default navbar-fixed-top");
 $header_navbar->addNav();
 // check session
 if($GLOBALS['session']->validity){
  $header_navbar->addItem(api_icon("fa-th-large",api_text("nav-dashboard"),"faa-tada animated-hover"),"?mod=dashboard");
  // cycle all menus
  foreach(api_framework_menus(null) as $menu_obj){
   // check for authorization
   if($menu_obj->authorization){
    $authorization=explode("|",$menu_obj->authorization);
    if(!api_checkAuthorization($authorization[1],null,$authorization[0],true,false)){continue;}
   }
   /** @todo menu titles */
   if($menu_obj->icon){$icon_source=api_icon($menu_obj->icon)." ";}else{$icon_source=null;}
   $header_navbar->addItem($icon_source.$menu_obj->label,$menu_obj->url,true,null,null,null,$menu_obj->target);
   foreach(api_framework_menus($menu_obj->id) as $submenu_obj){
    // check for authorization
    $authorized=true;
    if($submenu_obj->authorization){
     $authorization=explode("|",$submenu_obj->authorization);
     if(!api_checkAuthorization($authorization[1],null,$authorization[0],true,false)){$authorized=false;}
    }
    if($submenu_obj->icon){$icon_source=api_icon($submenu_obj->icon)." ";}else{$icon_source=null;}
    $header_navbar->addSubItem($icon_source.$submenu_obj->label,$submenu_obj->url,$authorized,null,null,null,$submenu_obj->target);
   }
  }
  // account and settings
  $header_navbar->addNav("navbar-right");
  // check for administrators
  if($GLOBALS['session']->user->superuser){
   $header_navbar->addItem(api_icon("fa-puzzle-piece"));
   $header_navbar->addSubHeader(api_text("nav-modules"),"text-right");
   // get all modules
   $modules_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__modules` WHERE `id`!='framework' ORDER BY `id`");
   foreach($modules_results as $module){
    if($module->id==MODULE){continue;}
    $module=new cModule($module);
    $header_navbar->addSubItem($module->name,"?mod=".$module->id,true,"text-right");
   }
  }
  $header_navbar->addItem(api_image($GLOBALS['session']->user->avatar,null,20,20,false,"alt='Brand'"));
  $header_navbar->addSubHeader($GLOBALS['session']->user->fullname,"text-right");
  $header_navbar->addSubItem(api_text("nav-own-profile")." ".api_icon("fa-user-circle-o"),"?mod=framework&scr=own_profile",true,"text-right");
  $header_navbar->addSubSeparator();
  // show link for administrators
  if(api_checkAuthorization("framework-settings_manage",null,"framework")){
   $header_navbar->addSubItem(api_text("nav-settings")." ".api_icon("fa-toggle-on"),"?mod=framework&scr=dashboard",true,"text-right");
   $header_navbar->addSubItem(api_text("nav-mails")." ".api_icon("fa-envelope-o"),"?mod=framework&scr=mails_list",true,"text-right");
   $header_navbar->addSubItem(api_text("nav-attachments")." ".api_icon("fa-cloud-upload"),"?mod=framework&scr=attachments_list",true,"text-right");
   if($GLOBALS['session']->user->superuser){$header_navbar->addSubItem(api_text("nav-debug")." ".api_icon("fa-code"),"index.php?debug=".(!$_SESSION['coordinator_debug']),true,"text-right inactive");}
  }
  $header_navbar->addSubItem(api_text("nav-logout")." ".api_icon("fa-sign-out"),"?mod=framework&scr=submit&act=user_logout",true,"text-right");
 }else{
  /** @todo collegamenti per i non loggati.. regolamento? privacy? boh? */
 }
 // set header
 $this->setHeader($header_navbar->render(false));
 // build footer grid
 $footer_grid=new strGrid();
 $footer_grid->addRow();
 $footer_grid->addCol("Copyright 2009-".date("Y")." &copy; Coordinator - All Rights Reserved".(DEBUG?" [ Queries: ".$GLOBALS['database']->query_counter." | Cached queries: ".$GLOBALS['database']->cache_query_counter." | Execution time: ~".number_format((microtime(true)-$_SERVER["REQUEST_TIME_FLOAT"]),2)." secs ]":null),"col-xs-12 text-right");
 // set footer
 $this->setFooter($footer_grid->render());
 // jQuery scripts
 $this->addScript("/* Popover Script */\n$(function(){\$(\"[data-toggle='popover']\").popover({'trigger':'hover'});});");
 $this->addScript("/* Current Row Timeout Script */\n$(function(){setTimeout(function(){\$('.currentrow').removeClass('info');},5000);});");
?>