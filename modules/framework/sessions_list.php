<?php
/**
 * Framework - Sessions List
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // check authorizations
 api_checkAuthorization("framework-sessions_manage","dashboard");
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set application title
 $app->setTitle(api_text("sessions_list"));
 // build grid object
 $table=new strTable(api_text("accounts_list-tr-unvalued"));
 $table->addHeader("&nbsp;",null,16);
 $table->addHeader(api_text("sessions_list-th-fullname"),null,"100%");
 $table->addHeader(api_text("sessions_list-th-idle"),"nowrap text-right");
 $table->addHeader(api_text("sessions_list-th-start"),"nowrap");
 $table->addHeader(api_text("sessions_list-th-address"),"nowrap");
 $table->addHeader(api_text("sessions_list-th-id"),"nowrap","100%");
 $table->addHeader(api_link("?mod=".MODULE."&scr=submit&act=sessions_terminate_all",api_icon("remove",api_text("sessions_list-th-terminate")),null,null,false,api_text("sessions_list-th-terminate-confirm")),"text-center",16);
 // definitions
 $users_array=array();
 // acquire sessions
 $sessions_results=$GLOBALS['database']->queryObjects("SELECT `framework__sessions`.* FROM `framework__sessions` JOIN `framework__users` ON `framework__users`.`id`=`framework__sessions`.`fkUser` ORDER BY `lastname`,`firstname`,`lastTimestamp`");
 foreach($sessions_results as $session_r){
  if(!array_key_exists($session_r->fkUser,$users_array)){
   $users_array[$session_r->fkUser]=new cUser($session_r->fkUser);
   $users_array[$session_r->fkUser]->sessions=array();
  }
  // add session to user
  $users_array[$session_r->fkUser]->sessions[]=$session_r;
 }
 // cycle all users
 foreach($users_array as $user){
  $table->addRow();
  $table->addRowField(api_image($user->avatar,null,18),null,null,"rowspan=\"".count($user->sessions)."\"");
  $table->addRowField($user->fullname,null,null,"rowspan=\"".count($user->sessions)."\"");
  // cycle all user sessions
  foreach($user->sessions as $count=>$session_r){
   if($count){$table->addRow();}
   $table->addRowField(api_timestamp_intervalTextual(time()-$session_r->lastTimestamp),"nowrap text-right");
   $table->addRowField(api_timestamp_format($session_r->startTimestamp,"Y-m-d H:i"),"nowrap");
   $table->addRowField($session_r->address,"nowrap");
   $table->addRowField(api_tag("samp",$session_r->id),"nowrap");
   $table->addRowFieldAction("?mod=".MODULE."&scr=submit&act=sessions_terminate&idSession=".$session_r->id,"remove",api_text("sessions_list-td-terminate"),api_text("sessions_list-td-terminate-confirm"));
  }
 }
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($table->render(),"col-xs-12");
 // add content to application
 $app->addContent($grid->render());
 // renderize application
 $app->render();
?>