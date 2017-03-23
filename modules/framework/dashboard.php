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
 $index.=api_link("?mod=framework&scr=settings_framework&tab=general","<h3>Impostazioni</h3>")."<br>\n";
 $index.=api_link("?mod=framework&scr=modules_list","<h3>Moduli</h3>")."<br>\n";
 $index.=api_link("?mod=framework&scr=users_list","<h3>Utenti</h3>")."<br>\n";
 $index.=api_link("?mod=framework&scr=groups_list","<h3>Gruppi</h3>")."<br>\n";
 $index.=api_link("?mod=framework&scr=sessions_list","<h3>Sessioni</h3>")."<br>\n";
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($index,"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
?>