<?php
/**
 * Dashboard - Template
 *
 * @package Coordinator\Modules\Dashboard
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // build application
 $app=new strApplication();
 // build nav object
 $nav=new strNav("nav-tabs");
 $nav->addItem(api_icon("fa-th-large",null,"hidden-link"),"?mod=".MODULE."&scr=dashboard");
 // dashboard own
 $nav->addItem(api_text("dashboard_own"),"?mod=".MODULE."&scr=dashboard_own");
 if(SCRIPT=="dashboard_own"){$nav->addItem(api_text("dashboard_customize"),"?mod=".MODULE."&scr=dashboard_customize");}
 // dashboard customize
 if(SCRIPT=="dashboard_customize"){
  $nav->addItem(api_text("nav-operations"),null,"active");
  $nav->addSubItem(api_text("nav-dashboard-add"),"?mod=".MODULE."&scr=dashboard_customize&act=addTile");
 }
 // add nav to application
 $app->addContent($nav->render(false));
?>