<?php
/**
 * Framework - Dashboard
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $app->setTitle(api_text("framework"));
 // build dashboard object
 $dashboard=new strDashboard();
 $dashboard->addTile("?mod=".MODULE."&scr=settings_edit&tab=general",api_text("settings_edit"),api_text("settings_edit-description"),(api_checkAuthorization("framework-settings_manage")),"1x1","fa-toggle-on");
 $dashboard->addTile("?mod=".MODULE."&scr=menus_list",api_text("menus_list"),api_text("menus_list-description"),(api_checkAuthorization("framework-menus_manage")),"1x1","fa-bars");
 $dashboard->addTile("?mod=".MODULE."&scr=modules_list",api_text("modules_list"),api_text("modules_list-description"),(api_checkAuthorization("framework-modules_manage")),"1x1","fa-puzzle-piece");
 $dashboard->addTile("?mod=".MODULE."&scr=users_list",api_text("users_list"),api_text("users_list-description"),(api_checkAuthorization("framework-users_manage")),"1x1","fa-user");
 $dashboard->addTile("?mod=".MODULE."&scr=groups_list",api_text("groups_list"),api_text("groups_list-description"),(api_checkAuthorization("framework-groups_manage")),"1x1","fa-group");
 $dashboard->addTile("?mod=".MODULE."&scr=sessions_list",api_text("sessions_list"),api_text("sessions_list-description"),(api_checkAuthorization("framework-sessions_manage")),"1x1","fa-random");
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($dashboard->render(),"col-xs-12");
 // add content to html
 $app->addContent($grid->render());
 // renderize html page
 $app->render();
?>