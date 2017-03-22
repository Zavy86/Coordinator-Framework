<?php
/**
 * Settings - Users List
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
 $html->setTitle(api_text("users_list"));
 // build grid object
 $table=new Table(api_text("users_list-tr-unvalued"));
 $table->addHeader("&nbsp;",NULL,16);
 $table->addHeader(api_text("users_list-th-fullname"),"nowrap");
 $table->addHeader(api_text("users_list-th-mail"),NULL,"100%");
 $table->addHeader("&nbsp;",NULL,16);
 // get user objects
 $users_array=array();
 $users_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_users` ORDER BY `lastname`,`firstname`",$GLOBALS['debug']);
 foreach($users_results as $user){$users_array[$user->id]=new User($user);}
 // cycle all users
 foreach($users_array as $user){
  // check deleted
  if($user->deleted){$tr_class="deleted";}else{$tr_class=NULL;}
  // make user row
  $table->addRow($tr_class);
  //$table->addRowField(api_link("?mod=settings&scr=users_view&idUser=".$user->id,api_icon("search",api_text("show"))));
  $table->addRowField(api_link("?mod=settings&scr=users_view&idUser=".$user->id,api_image($user->avatar,NULL,18),api_text("users_list-td-view")));
  $table->addRowField($user->fullname,"nowrap");
  $table->addRowField($user->mail);
  $table->addRowField(api_link("?mod=settings&scr=users_edit&idUser=".$user->id,api_icon("fa-edit",api_text("users_list-td-edit"),"hidden-link")));
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