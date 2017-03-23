<?php
/**
 * Framework - Modules Authorizations
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check authorizations
 /** @todo check authorizations */
 // set html title
 $html->setTitle(api_text("modules_authorizations"));
 // build grid object
 $table=new Table(api_text("modules_authorizations-tr-unvalued"));
 $table->addHeader(api_text("modules_authorizations-th-authorization"),"nowrap");
 $table->addHeader(api_text("modules_authorizations-th-groups"),NULL,"100%");
 // get module objects
 $modules_array=array();
 $modules_array["framework"]=new Module("framework");
 $modules_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_modules` WHERE `module`!='framework' ORDER BY `module`");
 foreach($modules_results as $module){$modules_array[$module->module]=new Module($module);}
 // build module nav object
 $tabs=new Nav("nav-pills nav-stacked",FALSE);
 foreach($modules_array as $module){$tabs->addItem($module->name,"?mod=framework&scr=modules_authorizations&tab=".$module->module);}
 // set selected module
 $selected_module=$modules_array[TAB];
 if(!$selected_module->module){api_alerts_add(api_text("settings_alert_moduleNotFound"),"danger");}

 // cycle selected module authorizations
 foreach($selected_module->authorizations_array as $authorization){
  // make groups
  $groups_td=NULL;
  foreach($authorization->groups_array as $group){
   $groups_td.=api_icon("fa-trash")." ".$group->name."<br>";
  }

  //
  $table->addRow();
  $table->addRowField(api_link("#",$authorization->name,$authorization->description,"hidden-link",TRUE),"nowrap");
  $table->addRowField(substr($groups_td,0,-4),"truncate-ellipsis");
 }

 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($tabs->render(FALSE),"col-xs-12 col-sm-3");
 $grid->addCol($table->render(),"col-xs-12 col-sm-9");
 // add content to html
 $html->addContent($grid->render());
 // renderize html
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($selected_module,"selected_module");}
?>