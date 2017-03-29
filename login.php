<?php
/**
 * Login
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include functions
 require_once("functions.inc.php");
 // build html object
 $html=new HTML($module_name);
 // set html title
 $html->setTitle("Login");
 // build login form manually
 $form_source="<!-- form -->\n";
 $form_source.="<h2>".$settings->title."</h2>\n"; /** @todo prendere il titolo dal database */
 $form_source.="<form class=\"\" action=\"index.php?mod=framework&scr=submit&act=user_login\" method=\"POST\" id=\"form_login\">\n";
 $form_source.=" <div class=\"form-group\">\n";
 $form_source.="  <input type=\"text\" name=\"username\" class=\"form-control\" id=\"form_login_input_username\" placeholder=\"".api_text("login-ff-account")."\" required autofocus>\n";
 $form_source.=" </div><!-- /form-group -->\n";
 $form_source.=" <div class=\"form-group\">\n";
 $form_source.="  <input type=\"password\" name=\"password\" class=\"form-control\" id=\"form_login_input_password\" placeholder=\"".api_text("login-ff-password")."\" required>\n";
 $form_source.=" </div><!-- /form-group -->\n";
 $form_source.=" <div class=\"form-group\">\n";
 $form_source.="  <button type=\"submit\" class=\"btn btn-primary\" id=\"form_login_control_submit\">".api_text("login-fc-login")."</button>\n";
 $form_source.="  <a href=\"recovery.php\" class=\"btn btn-link\" id=\"form_login_control_recovery\">".api_text("login-fc-recovery")."</a>\n";
 $form_source.=" </div><!-- /form-group -->\n";
 $form_source.="</form><!-- /form -->\n";
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($form_source,"col-xs-12 col-sm-offset-4 col-sm-4 col-sm-offset-4");
 // add content to html
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
 // debug
 api_debug();
?>