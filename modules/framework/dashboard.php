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
 // check authorizations
 /** @todo check authorizations */
 // set html title
 $html->setTitle(api_text("framework"));
 // build list object
 $list=new cList();
 $list->addElement(api_link("?mod=framework&scr=settings_edit&tab=general",api_text("settings_edit")));
 $list->addElement(api_link("?mod=framework&scr=menus_list",api_text("menus_list")));
 $list->addElement(api_link("?mod=framework&scr=modules_list",api_text("modules_list")));
 $list->addElement(api_link("?mod=framework&scr=users_list",api_text("users_list")));
 $list->addElement(api_link("?mod=framework&scr=groups_list",api_text("groups_list")));
 $list->addElement(api_link("?mod=framework&scr=sessions_list",api_text("sessions_list")));
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($list->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
?>