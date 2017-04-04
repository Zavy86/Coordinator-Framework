<?php
/**
 * Framework - Modules List
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 $authorization="framework-modules_manage";
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("modules_list"));
 // build grid object
 $table=new Table(api_text("modules_list-tr-unvalued"));
 $table->addHeader("&nbsp;",NULL,16);
 $table->addHeader(api_text("modules_list-th-name"),"nowrap");
 $table->addHeader(api_text("modules_list-th-installed"),"nowrap text-right");
 $table->addHeader(api_text("modules_list-th-repository"),"nowrap text-right");
 $table->addHeader(api_text("modules_list-th-description"),NULL,"100%");
 $table->addHeader("&nbsp;",NULL,16);
 // get module objects
 $modules_array=array();
 $modules_array["framework"]=new Module("framework");
 $modules_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_modules` WHERE `module`!='framework' ORDER BY `module`");
 foreach($modules_results as $module){$modules_array[$module->module]=new Module($module);}
 // cycle all modules
 foreach($modules_array as $module){
  // get last released version from GitHub
  //$repository_version=file_get_contents("https://raw.githubusercontent.com/Zavy86/Coordinator-Framework/master/VERSION.txt");
  $repository_version="1.0.2";

  // check if repository version is updated among source version
  if(api_check_version($module->source_version,$repository_version)>0){$repository_updated=TRUE;}else{$repository_updated=FALSE;}
  // check if source version is updated among installed version
  if(api_check_version($module->version,$module->source_version)>0){$source_updated=TRUE;}else{$source_updated=FALSE;}

  //
  if($source_updated){
   $action_btn=api_link("?mod=framework&scr=submit&act=module_update_database&module=".$module->module,api_text("modules_list-td-update_database",$module->source_version),NULL,"btn btn-warning btn-xs");
  }elseif($repository_updated){
   // check for git
   if(file_exists($module->source_path."/.git/config")){
    $action_btn=api_link("?mod=framework&scr=submit&act=module_update_source&module=".$module->module,api_text("modules_list-td-update_source"),NULL,"btn btn-info btn-xs",FALSE,api_text("modules_list-td-update_source-confirm"));
   }else{
    $action_btn=api_link("@todo module url".$module->url,api_text("modules_list-td-update_source-manual"),NULL,"btn btn-info btn-xs",FALSE,NULL,NULL,NULL,"_blank");
   }
  }else{
   $action_btn=NULL;
  }

  //
  $table->addRow();
  $table->addRowField(api_link("?mod=framework&scr=modules_view&module=".$module->module,api_icon("search",api_text("show"))));
  $table->addRowField($module->name,"nowrap");
  $table->addRowField(api_tag("span",$module->version,"label ".($source_updated?"label-warning":"label-success")),"nowrap text-right");
  $table->addRowField(api_tag("span",$repository_version,"label ".($repository_updated?"label-info":"label-success")),"nowrap text-right");
  $table->addRowField($module->description,"truncate-ellipsis");
  $table->addRowField($action_btn,"nowrap text-right");


 }
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($table->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html
 $html->render();
?>