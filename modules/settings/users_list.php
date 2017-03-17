<?php
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check permissions
 /** @todo check permissions */
 // set html title
 $html->setTitle(api_text("users_list"));

 // build grid object
 $table=new Table(api_text("users_list-tr-unvalued"));

 //$table->addHeader("&nbsp;",NULL,16);
 $table->addHeader("&nbsp;",NULL,16);
 $table->addHeader(api_text("users_list-th-fullname"),"nowrap");
 $table->addHeader(api_text("users_list-th-mail"),NULL,"100%");
 $table->addHeader("&nbsp;",NULL,16);

 $users_array=array();
 $users_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_users` ORDER BY `lastname`,`firstname`",$GLOBALS['debug']);
 foreach($users_results as $user){$users_array[$user->id]=new User($user);}

 foreach($users_array as $user){
  $table->addRow();
  //$table->addRowField(api_link("?mod=settings&scr=users_view&idUser=".$user->id,api_icon("search",api_text("show"))));
  $table->addRowField(api_link("?mod=settings&scr=users_view&idUser=".$user->id,api_image($user->avatar,NULL,18),api_text("users_list-td-view")));
  $table->addRowField($user->fullname,"nowrap");
  $table->addRowField($user->mail);
  $table->addRowField($session_td,"text-right nowrap");
  $table->addRowField(api_link("?mod=settings&scr=users_edit&idUser=".$user->id,api_icon("fa-edit",api_text("users_list-td-edit"),"hidden-link")));
 }

 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($table->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render(FALSE));
 // renderize html
 $html->render();
?>