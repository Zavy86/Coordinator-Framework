<?php
/**
 * Settings - Dashboard
 *
 * @package Rasmotic\Modules\Settings
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check permissions
 /** @todo check permissions */
 // set html title
 $html->setTitle(api_text("settings"));

 /** @todo da rifare bene */

 // make index
 $index=api_link("?mod=settings&scr=own_profile","<h3>Profilo personale</h3>")."<br>\n";
 $index.=api_link("?mod=settings&scr=settings_framework","<h3>Impostazioni</h3>")."<br>\n";
 $index.=api_link("?mod=settings&scr=users_list","<h3>Gestione Utenti</h3>")."<br>\n";
 $index.=api_link("?mod=settings&scr=groups_list","<h3>Gestione Gruppi</h3>")."<br>\n";
 $index.=api_link("?mod=settings&scr=sessions_list","<h3>Sessioni</h3>")."<br>\n";
 $index.=api_link("?mod=settings&scr=updates_framework","<h3>Aggiornamenti</h3>")."<br>\n";
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($index,"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
?>