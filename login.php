<?php
/**
 * Login
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // include functions
 require_once("initializations.inc.php");
 // build application
 $app=new strApplication();
 // set application title
 $app->setTitle("Login");
 // get cookie
 $c_username=$_COOKIE['login-username'];
 // build login form manually
 $form_source="<!-- form -->\n";
 $form_source.="<h2>".$GLOBALS['settings']->title."</h2>\n";
 $form_source.="<form class=\"\" action=\"index.php?mod=framework&scr=submit&act=session_login\" method=\"POST\" id=\"form_login\">\n";
 $form_source.=" <div class=\"form-group\">\n";
 $form_source.="  <input type=\"text\" name=\"username\" class=\"form-control\" id=\"form_login_input_username\" value=\"".$c_username."\" placeholder=\"".api_text("login-ff-account")."\" required ".(!strlen($c_username)?" autofocus" :null).">\n";
 $form_source.=" </div><!-- /form-group -->\n";
 $form_source.=" <div class=\"form-group\">\n";
 $form_source.="  <input type=\"password\" name=\"password\" class=\"form-control\" id=\"form_login_input_password\" placeholder=\"".api_text("login-ff-password")."\" required ".(strlen($c_username)?" autofocus" :null).">\n";
 $form_source.=" </div><!-- /form-group -->\n";
 $form_source.=" <div class=\"form-group\">\n";
 $form_source.="  <button type=\"submit\" class=\"btn btn-primary\" id=\"form_login_control_submit\">".api_text("login-fc-login")." ".api_icon("fa-sign-in")."</button>\n";
 $form_source.="  <a href=\"recovery.php\" class=\"btn btn-link\" id=\"form_login_control_recovery\">".api_text("login-fc-recovery")."</a>\n";
 $form_source.=" </div><!-- /form-group -->\n";
 $form_source.="</form><!-- /form -->\n";
 // submit button disabler script
 $app->addScript("$(document).on('submit','#form_login',function(){\$('#form_login_control_submit').attr('disabled',true);});");
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($form_source,"col-xs-12 col-sm-offset-4 col-sm-4 col-sm-offset-4");
 // add content to application
 $app->addContent($grid->render());
 // renderize application
 $app->render();
 // debug
 api_debug();
?>