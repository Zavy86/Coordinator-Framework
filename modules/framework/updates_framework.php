<?php
/**
 * Framework - Update Framework
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check permissions
 /** @todo check permissions */
 // set html title
 $html->setTitle(api_text("updates_framework"));
 // get local version
 $local_version=file_get_contents(ROOT."VERSION.txt");
 // get last released version from GitHub
 //$repository_version=file_get_contents("https://raw.githubusercontent.com/Zavy86/Coordinator-Framework/master/VERSION.txt");
 $repository_version="1.0.2";
 /** ^ @todo modificare con url dopo il release della 1.0 */
 // check for source update
 if(api_check_version($local_version,$repository_version)){
  // check for git
  if(file_exists(ROOT.".git/config")){
   $download_btn="&nbsp;&nbsp;&nbsp;".api_link("?mod=framework&scr=submit&act=update_source",api_text("updates_framework-pull"),NULL,"btn btn-success btn-xs",FALSE,api_text("updates_framework-pull-confirm"));
  }else{
   $download_btn="&nbsp;&nbsp;&nbsp;".api_link("https://github.com/Zavy86/Coordinator-Framework",api_text("updates_framework-link"),NULL,"btn btn-warning btn-xs",FALSE,NULL,NULL,NULL,"_blank");
  }
 }
 // check for database update
 if(api_check_version($settings->version,$local_version)){
  $update_btn="&nbsp;&nbsp;&nbsp;".api_link("?mod=framework&scr=submit&act=update_database",api_text("updates_framework-update",$settings->version),NULL,"btn btn-warning btn-xs");
 }
 // build description list
 $dl=new DescriptionList("br","dl-horizontal");
 $dl->addElement(api_text("updates_framework-repository"),$repository_version.$download_btn);
 $dl->addElement(api_text("updates_framework-installed"),$local_version.$update_btn);

 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($dl->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html
 $html->render();
?>