<?php
/**
 * Framework - Dashboard
 *
 * @package Rasmotic\Modules\Settings
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("framework"));
 // build dashboard object
 $dashboard=new cDashboard();
 $dashboard->addElement("?mod=framework&scr=settings_edit&tab=general",api_text("settings_edit"),api_text("settings_edit-description"),(api_checkAuthorization(MODULE,"framework-settings_manage")),"1x1","fa-toggle-on");
 $dashboard->addElement("?mod=framework&scr=menus_list",api_text("menus_list"),api_text("menus_list-description"),(api_checkAuthorization(MODULE,"framework-menus_manage")),"1x1","fa-bars");
 $dashboard->addElement("?mod=framework&scr=modules_list",api_text("modules_list"),api_text("modules_list-description"),(api_checkAuthorization(MODULE,"framework-modules_manage")),"1x1","fa-puzzle-piece");
 $dashboard->addElement("?mod=framework&scr=users_list",api_text("users_list"),api_text("users_list-description"),(api_checkAuthorization(MODULE,"framework-users_manage")),"1x1","fa-user");
 $dashboard->addElement("?mod=framework&scr=groups_list",api_text("groups_list"),api_text("groups_list-description"),(api_checkAuthorization(MODULE,"framework-groups_manage")),"1x1","fa-group");
 $dashboard->addElement("?mod=framework&scr=sessions_list",api_text("sessions_list"),api_text("sessions_list-description"),(api_checkAuthorization(MODULE,"framework-sessions_manage")),"1x1","fa-random");
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 $grid->addCol($dashboard->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
?>