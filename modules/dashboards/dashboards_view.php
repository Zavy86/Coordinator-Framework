<?php
/**
 * Dashboards - View
 *
 * @package Rasmotic\Modules\Dashboards
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check permissions
 /** @todo check permissions */
 // set html title
 $html->setTitle("Dashboard");
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol("Dashboard","col-xs-12");
 // add content to html
 $html->addContent($grid->render(FALSE));
 // renderize html page
 $html->render();
?>