<?php
/**
 * Framework - Users List
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 $authorization="framework-users_manage";
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("users_list"));
 // build grid object
 $table=new cTable(api_text("users_list-tr-unvalued"));
 $table->addHeader("&nbsp;",null,16);
 $table->addHeader(api_text("users_list-th-fullname"),"nowrap");
 $table->addHeader("&nbsp;",null,16);
 $table->addHeader(api_text("users_list-th-mail"),null,"100%");
 $table->addHeader("&nbsp;",null,16);
 // get user objects
 $users_array=array();
 $users_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_users` ORDER BY `lastname`,`firstname`",$GLOBALS['debug']);
 foreach($users_results as $user){$users_array[$user->id]=new cUser($user);}
 // cycle all users
 foreach($users_array as $user_obj){
  // build operation button
  $ob=new cOperationsButton();
  $ob->addElement("?mod=framework&scr=users_edit&idUser=".$user_obj->id,"fa-pencil",api_text("users_list-td-edit"));
  if($user_obj->deleted){$ob->addElement("?mod=framework&scr=submit&act=user_undelete&idUser=".$user_obj->id,"fa-trash-o",api_text("users_list-td-undelete"),true,api_text("users_list-td-undelete-confirm"));}
  else{
   if($user_obj->enabled){$ob->addElement("?mod=framework&scr=submit&act=user_disable&idUser=".$user_obj->id,"fa-remove",api_text("users_list-td-disable"),true,api_text("users_list-td-disable-confirm"));}
   else{$ob->addElement("?mod=framework&scr=submit&act=user_enable&idUser=".$user_obj->id,"fa-check",api_text("users_list-td-enable"),true,api_text("users_list-td-enable-confirm"));}
   $ob->addElement("?mod=framework&scr=submit&act=user_delete&idUser=".$user_obj->id,"fa-trash",api_text("users_list-td-delete"),true,api_text("users_list-td-delete-confirm"));
  }
  // check deleted
  if($user_obj->deleted){$tr_class="deleted";}else{$tr_class=null;}
  // make user row
  $table->addRow($tr_class);
  $table->addRowField(api_link("?mod=framework&scr=users_view&idUser=".$user_obj->id,api_image($user_obj->avatar,null,18),api_text("users_list-td-view")));
  $table->addRowField($user_obj->fullname,"nowrap");
  $table->addRowField($user_obj->getStatus(true,false));
  $table->addRowField($user_obj->mail,"truncate-ellipsis");
  $table->addRowField($ob->render(),"text-right");
 }
 // build grid object
 $grid=new cGrid();
 $grid->addRow();
 $grid->addCol($table->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html
 $html->render();
?>