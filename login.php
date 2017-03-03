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
 $form_source.="<h2>Coordinator</h2>\n"; /** @todo prendere il titolo dal database */
 $form_source.="<form class=\"\" action=\"index.php?mod=accounts&scr=submit&act=user_login\" method=\"POST\" id=\"form_login\">\n";
 $form_source.=" <div class=\"form-group\">\n";
 $form_source.="  <input type=\"text\" name=\"username\" class=\"form-control\" id=\"form_login_input_username\" placeholder=\"Account\" required>\n";
 $form_source.=" </div><!-- /form-group -->\n";
 $form_source.=" <div class=\"form-group\">\n";
 $form_source.="  <input type=\"password\" name=\"password\" class=\"form-control\" id=\"form_login_input_password\" placeholder=\"Password\" required>\n";
 $form_source.=" </div><!-- /form-group -->\n";
 $form_source.=" <div class=\"form-group\">\n";
 $form_source.="  <button type=\"submit\" class=\"btn btn-primary\" id=\"form_login_control_submit\">Login</button>\n";
 $form_source.="  <a href=\"recovery.php\" class=\"btn btn-link\" id=\"form_login_control_recovery\">Forgot password?</a>\n";
 $form_source.=" </div><!-- /form-group -->\n";
 $form_source.="</form><!-- /form -->\n";
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($form_source,"col-xs-12 col-sm-offset-4 col-sm-4 col-sm-offset-4");
 // add content to html
 $html->addContent($grid->render(FALSE));
 // renderize html page
 $html->render();

 // debug
 if($debug){
  api_dump($_SESSION["coordinator_session_id"],"session_id");
  //api_dump($session->debug(),"session",API_DUMP_VARDUMP);
  api_dump($session->debug(),"session");
  api_dump($settings->debug(),"settings");
  api_dump(get_defined_constants(true)["user"],"contants");
  api_dump($_SESSION["coordinator_logs"],"logs");
 }
?>