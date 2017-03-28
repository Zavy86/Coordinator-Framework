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

 /** @todo da rifare bene */

 // make index
 $index.=api_link("?mod=framework&scr=settings_framework&tab=general",api_tag("h3",api_text("settings_framework")))."<br>\n";
 $index.=api_link("?mod=framework&scr=menus_list",api_tag("h3",api_text("menus_list")))."<br>\n";
 $index.=api_link("?mod=framework&scr=modules_list",api_tag("h3",api_text("modules_list")))."<br>\n";
 $index.=api_link("?mod=framework&scr=users_list",api_tag("h3",api_text("users_list")))."<br>\n";
 $index.=api_link("?mod=framework&scr=groups_list",api_tag("h3",api_text("groups_list")))."<br>\n";
 $index.=api_link("?mod=framework&scr=sessions_list",api_tag("h3",api_text("sessions_list")))."<br>\n";
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($index,"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
?>