<?php
/**
 * Dashboards - Template
 *
 * @package Rasmotic\Modules\Dashboards
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // build html object
 $html=new cHTML($module_name);
 // load html template
 //$html->loadTemplate();
 // build navbar object
 $nav=new cNav("nav-tabs");
 $nav->addItem("Dashboard","?mod=dashboards&scr=dashboards_view");
 $nav->addItem("Customize","?mod=dashboards&scr=dashboards_customize");
 // add nav to html
 $html->addContent($nav->render(FALSE));
?>