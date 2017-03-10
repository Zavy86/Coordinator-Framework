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
 $this->setMetaTag("copyright","2009-".date("Y")." &copy; Coordinator [www.coordinator.it]");
 $this->setMetaTag("description","Coordinator is an Open Source Modular Framework");
 $this->setMetaTag("owner",$GLOBALS['settings']->owner);
 // add style sheets
 $this->addStylesheet(HELPERS."bootstrap/css/bootstrap-3.3.7.min.css");
 $this->addStylesheet(HELPERS."bootstrap/css/bootstrap-3.3.7-theme.min.css");
 /** @todo add some helpders here */
 $this->addStylesheet(HELPERS."bootstrap/css/bootstrap-3.3.7-custom.css");
 // add scripts
 $this->addScript(HELPERS."jquery/jquery-1.12.0.min.js");
 /** @todo add some helpders here */
 $this->addScript(HELPERS."bootstrap/js/bootstrap-3.3.7.min.js");
 $this->addScript(HELPERS."bootstrap-filestyle/js/bootstrap-filestyle-1.2.1.min.js");

 // build header navbar object
 $header_navbar=new Navbar($GLOBALS['settings']->title,"navbar-default navbar-static-top");
 $header_navbar->addNav("navclass");

 // check session
 if($GLOBALS['session']->validity){
  $header_navbar->addItem("Dashboard","?mod=dashboards");
  /** @todo load menu from database */
  $header_navbar->addItem("Test","?mod=test");
  $header_navbar->addItem("Drop","#","dropitemclass");
  $header_navbar->addSubItem("Accounts","?mod=accounts");
  // account and settings
  $header_navbar->addNav("navbar-right");
  $header_navbar->addItem(api_image($GLOBALS['session']->user->avatar,NULL,20,20,FALSE,"alt='Brand'"));
  $header_navbar->addSubHeader("Firstname Lastname","text-right");
  $header_navbar->addSubItem("Profilo personale","?mod=accounts&scr=users_profile","text-right");
  $header_navbar->addSubSeparator();
  $header_navbar->addSubItem("Settings","?mod=settings","text-right");
  $header_navbar->addSubItem("Logout","?mod=accounts&scr=submit&act=user_logout","text-right");

 }else{

 }

 // set header
 $this->setHeader($header_navbar->render(FALSE));
 // build footer grid
 $footer_grid=new Grid();
 $footer_grid->addRow();
 $footer_grid->addCol("Copyright 2009-".date("Y")." &copy; Coordinator - All Rights Reserved".($GLOBALS['debug']?" [ Queries: ".$GLOBALS['database']->query_counter." | Execution time: ~".number_format((microtime(true)-$_SERVER["REQUEST_TIME_FLOAT"]),2)." secs ]":NULL),"col-xs-12 text-right");
 // set footer
 $this->setFooter($footer_grid->render(FALSE));

?>