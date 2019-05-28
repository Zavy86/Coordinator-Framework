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
 $table=new cTable(api_text("modules_list-tr-unvalued"));
 $table->addHeader("&nbsp;",null,16);
 $table->addHeader(api_text("modules_list-th-name"),"nowrap");
 $table->addHeader(api_text("modules_list-th-installed"),"nowrap text-right");
 $table->addHeader(api_text("modules_list-th-repository"),"nowrap text-right");
 $table->addHeader("&nbsp;",null,16);
 $table->addHeader(api_text("modules_list-th-description"),null,"100%");
 $table->addHeader("&nbsp;",null,16);
 // get module objects
 $modules_array=array();
 $modules_array["framework"]=new cModule("framework");
 $modules_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__modules` WHERE `module`!='framework' ORDER BY `module`");
 foreach($modules_results as $module){$modules_array[$module->module]=new cModule($module);}
 // cycle all modules
 foreach($modules_array as $module){
  // get last released version from GitHub
  $repository_version=null;
  if($module->repository_version_url){$repository_version=file_get_contents($module->repository_version_url."?".rand(1,99999));}
  if(!is_numeric(substr($repository_version,0,1))){$repository_version=null;}
  // check if module is installed
  if($module->version!="0"){$module_installed=true;}else{$module_installed=false;}
  // check if repository version is updated among source version
  if(api_check_version($module->source_version,$repository_version)>0){$repository_updated=true;}else{$repository_updated=false;}
  // check if source version is updated among installed version
  if(api_check_version($module->version,$module->source_version)>0){$source_updated=true;}else{$source_updated=false;}
  // make action button
  $action_btn=null;
  // check module status
  if($module_installed){
   if($source_updated){
    $action_btn=api_link("?mod=".MODULE."&scr=submit&act=module_update_database&module=".$module->module,api_text("modules_list-td-update_database",$module->source_version),null,"btn btn-warning btn-xs");
   }elseif($repository_updated){
    // check for git
    if(file_exists($module->source_path."/.git/config")){
     $action_btn=api_link("?mod=".MODULE."&scr=submit&act=module_update_source&module=".$module->module,api_text("modules_list-td-update_source"),null,"btn btn-info btn-xs",false,api_text("modules_list-td-update_source-confirm"));
    }else{
     $action_btn=api_link("@todo module url".$module->url,api_text("modules_list-td-update_source-manual"),null,"btn btn-info btn-xs",false,null,null,null,"_blank");
    }
   }elseif($repository_version===null){
    // check for git
    if(file_exists($module->source_path."/.git/config")){
     $action_btn=api_link("?mod=".MODULE."&scr=submit&act=module_update_source&module=".$module->module,api_text("modules_list-td-update_source_force"),null,"btn btn-default btn-xs",false,api_text("modules_list-td-update_source_force-confirm"));
    }
   }
  }else{
   $action_btn=api_link("?mod=".MODULE."&scr=submit&act=module_setup&module=".$module->module,api_text("modules_list-td-setup"),null,"btn btn-success btn-xs");
  }
  // build table row
  $table->addRow();
  // build table fields
  $table->addRowFieldAction("?mod=".MODULE."&scr=modules_view&module=".$module->module,"search",api_text("show")); /** @todo verificare non esiste */
  $table->addRowField($module->name,"nowrap");
  $table->addRowField(api_tag("span",$module->version,"label ".($source_updated?"label-warning":"label-success")),"nowrap text-right");
  $table->addRowField(api_tag("span",$repository_version,"label ".($repository_updated?"label-info":"label-success")),"nowrap text-right");
  $table->addRowField($module->getEnabled(true,false));
  $table->addRowField($module->description,"truncate-ellipsis");
  $table->addRowField($action_btn,"nowrap text-right");
 }
 // check for local uninstalled modules
 $module_directory_array=array();
 $dir_handle=opendir(ROOT."modules/");
 while($module_directory=readdir($dir_handle)){
  if(in_array($module_directory,array(".","..","index.php","dashboard","framework"))){continue;}
  if(array_key_exists($module_directory,$modules_array)){continue;}
  if(!file_exists(ROOT."modules/".$module_directory."/module.inc.php")){continue;}
  // make action button
  $action_btn=api_link("?mod=".MODULE."&scr=submit&act=module_initialize&module=$module_directory",api_text("modules_list-td-initialize"),null,"btn btn-info btn-xs");
  // build table row
  $table->addRow();
  // build table fields
  $table->addRowField("&nbsp;");
  $table->addRowField($module_directory,"nowrap",null,"colspan='5'");
  $table->addRowField($action_btn,"nowrap text-right");
 }
 closedir($dir_handle);
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 $grid->addCol($table->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html
 $html->render();
?>