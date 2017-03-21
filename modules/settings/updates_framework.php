<?php
/**
 * Settings - Update Git
 *
 * @package Coordinator\Modules\Settings
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check permissions
 /** @todo check permissions */
 // set html title
 $html->setTitle(api_text("updates_framework"));

 // get last released version from GitHub
 //$repository_version=file_get_contents("https://raw.githubusercontent.com/Zavy86/Coordinator-Framework/master/VERSION.txt");
 $repository_version="1.0.1";
 /** ^ @todo modificare con url dopo il release della 1.0 */

 // check for update
 if($repository_version>$settings->version){
  // check for git
  if(file_exists(ROOT.".git/config")){
   $repository_version.="&nbsp;&nbsp;&nbsp;".api_link("?mod=settings&scr=submit&act=updates_git",api_text("updates_framework-pull"),NULL,"btn btn-default btn-xs",FALSE,api_text("updates_framework-pull-confirm"));
  }else{
   $repository_version.="&nbsp;&nbsp;&nbsp;".api_link("https://github.com/Zavy86/Coordinator-Framework",api_text("updates_framework-link"),NULL,"btn btn-default btn-xs",FALSE,NULL,NULL,NULL,"_blank");
  }
 }

 // build description list
 $dl=new DescriptionList("br","dl-horizontal");
 $dl->addElement(api_text("updates_framework-version"),$settings->version);
 $dl->addElement(api_text("updates_framework-repository"),$repository_version);

 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($dl->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html
 $html->render();
?>