<?php
/**
 * Settings - Framework
 *
 * @package Rasmotic\Modules\Settings
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check permissions
 /** @todo check permissions */
 // set html title
 $html->setTitle(api_text("settings_framework"));

 /* todo fare tabbable o carousel o a pagine separate */

 // build settings form
 $form=new Form("?mod=settings&scr=submit&act=settings_framework","POST",NULL,"settings_framework");

 if(TAB=="generals"){

  $form->addField("text","title",api_text("settings_framework-title"),$settings->title,api_text("settings_framework-title-placeholder"));
  $form->addField("text","owner",api_text("settings_framework-owner"),$settings->owner,api_text("settings_framework-owner-placeholder"));

 }

 if(TAB=="sessions"){

  $form->addField("static",NULL,api_text("settings_framework-online"),api_text("settings_framework-online-counter",array($session->countOnlineUsers(),$session->countAllSessions()))." &rarr; ".api_link("?mod=accounts&scr=submit&act=user_logout_forced",api_text("settings_framework-reset"),NULL,NULL,FALSE,api_text("settings_framework-online-reset-confirm")));

  $form->addField("radio","maintenance",api_text("settings_framework-maintenance"),$settings->maintenance,NULL,NULL,"radio-inline");
  $form->addFieldOption(0,api_text("no"));
  $form->addFieldOption(1,api_text("settings_framework-maintenance-lock"));

  $form->addField("select","sessions_idle_timeout",api_text("settings_framework-sessions_idle_timeout"),$settings->sessions_idle_timeout,api_text("settings_framework-sessions_idle_timeout-placeholder"));
  $form->addFieldOption(900,api_text("settings_framework-sessions_idle_timeout-15m"));
  $form->addFieldOption(3600,api_text("settings_framework-sessions_idle_timeout-1h"));
  $form->addFieldOption(14400,api_text("settings_framework-sessions_idle_timeout-4h"));
  $form->addFieldOption(28800,api_text("settings_framework-sessions_idle_timeout-8h"));
  $form->addFieldOption(86400,api_text("settings_framework-sessions_idle_timeout-24h"));

  $form->addField("radio","sessions_multiple",api_text("settings_framework-sessions_multiple"),$settings->sessions_multiple,NULL,NULL,"radio-inline");
  $form->addFieldOption(0,api_text("no"));
  $form->addFieldOption(1,api_text("settings_framework-sessions_multiple-allowed"));

  $form->addField("radio","authentication_method",api_text("settings_framework-authentication_method"),$settings->authentication_method);
  $form->addFieldOption("standard",api_text("settings_framework-authentication_method-standard"));
  $form->addFieldOption("ldap",api_text("settings_framework-authentication_method-ldap")." &rarr; ".api_link("?mod=settings&scr=settings_plugins&plugin=ldap_auth",api_text("settings_framework-settings_plugins"),NULL,NULL,FALSE,NULL,NULL,"_blank")); /** @todo elenco automatico dei metodi disponibili in base ai plugin */

 }

 if(TAB=="sendmail"){

  $form->addField("text","sendmail_name",api_text("settings_framework-sendmail_name"),$settings->sendmail_name,api_text("settings_framework-sendmail_name-placeholder"));
  $form->addField("text","sendmail_mail",api_text("settings_framework-sendmail_mail"),$settings->sendmail_mail,api_text("settings_framework-sendmail_mail-placeholder"));
  $form->addField("radio","sendmail_method",api_text("settings_framework-sendmail_method"),$settings->sendmail_method);
  $form->addFieldOption("standard",api_text("settings_framework-sendmail_method-standard"));
  $form->addFieldOption("smtp",api_text("settings_framework-sendmail_method-smtp")." &rarr; ".api_link("?mod=settings&scr=settings_plugins&plugin=smtp",api_text("settings_framework-settings_plugins"),NULL,NULL,FALSE,NULL,NULL,"_blank")); /** @todo elenco automatico dei metodi disponibili in base ai plugin */

  $form->addField("separator");

 }

 $form->addControl("submit",api_text("settings_framework-submit"));
 $form->addControl("reset",api_text("settings_framework-reset"));

 $nav2=new Nav("nav-pills");
 $nav2->addItem(api_text("settings_framework-generals"),"?mod=settings&scr=settings_framework&tab=generals");
 $nav2->addItem(api_text("settings_framework-sessions"),"?mod=settings&scr=settings_framework&tab=sessions");
 $nav2->addItem(api_text("settings_framework-sendmail"),"?mod=settings&scr=settings_framework&tab=sendmail");

 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to html
 $html->addContent($nav2->render(FALSE));
 $html->addContent($grid->render(FALSE));
 // renderize html page
 $html->render();
?>