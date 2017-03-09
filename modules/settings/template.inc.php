<?php
/**
 * Settings - Template
 *
 * @package Rasmotic\Modules\Settings
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // build html object
 $html=new HTML($module_name);
 // build navbar object
 $nav=new Nav("nav-tabs");
 $nav->addItem(api_text("settings_framework"),"?mod=settings&scr=settings_framework");
 $nav->addItem(api_text("settings_modules"),"?mod=settings&scr=settings_modules");
 // add nav to html
 $html->addContent($nav->render(FALSE));
?>