<?php
/**
 * Framework - Framework
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
 // check actions
 if(ACTION=="token_cron_randomize"||!$settings->token_cron){$settings->token_cron=md5(date("YmdHis").rand(1,99999));}
 // script tabs
 $tabs=new Nav("nav-pills");
 $tabs->addItem(api_text("settings_framework-general"),"?mod=framework&scr=settings_framework&tab=general");
 $tabs->addItem(api_text("settings_framework-sessions"),"?mod=framework&scr=settings_framework&tab=sessions");
 $tabs->addItem(api_text("settings_framework-sendmail"),"?mod=framework&scr=settings_framework&tab=sendmail");
 $tabs->addItem(api_text("settings_framework-users"),"?mod=framework&scr=settings_framework&tab=users");
 $tabs->addItem(api_text("settings_framework-token"),"?mod=framework&scr=settings_framework&tab=token");
 // build settings form
 $form=new Form("?mod=framework&scr=submit&act=settings_framework&tab=".TAB,"POST",NULL,"settings_framework");
 /**
  * Generals
  */
 if(TAB=="general"){
  $form->addField("text","title",api_text("settings_framework-title"),$settings->title,api_text("settings_framework-title-placeholder"));
  $form->addField("text","owner",api_text("settings_framework-owner"),$settings->owner,api_text("settings_framework-owner-placeholder"));
  $form->addField("radio","maintenance",api_text("settings_framework-maintenance"),$settings->maintenance,NULL,NULL,"radio-inline");
  $form->addFieldOption(0,api_text("no"));
  $form->addFieldOption(1,api_text("settings_framework-maintenance-lock"));
  $form->addField("splitter");
  $form->addField("file","logo",api_text("settings_framework-logo"));
  $form->addField("radio","show",api_text("settings_framework-show"),$settings->show,NULL,NULL,"radio-inline"); /** @todo spostare in impostazioni menu */
  $form->addFieldOption("logo_title",api_text("settings_framework-show-logo_title"));
  $form->addFieldOption("logo",api_text("settings_framework-show-logo"));
  $form->addFieldOption("title",api_text("settings_framework-show-title"));
  $form->addField("static",NULL,NULL,api_image($settings->logo,"img-thumbnail",80));
 }
 /**
  * Sessions
  */
 if(TAB=="sessions"){
  $form->addField("radio","sessions_authentication_method",api_text("settings_framework-sessions_authentication_method"),$settings->sessions_authentication_method,NULL,NULL,"radio-inline");
  $form->addFieldOption("standard",api_text("settings_framework-sessions_authentication_method-standard"));
  $form->addFieldOption("ldap",api_text("settings_framework-sessions_authentication_method-ldap"));
  $form->addField("radio","sessions_multiple",api_text("settings_framework-sessions_multiple"),$settings->sessions_multiple,NULL,NULL,"radio-inline");
  $form->addFieldOption(0,api_text("no"));
  $form->addFieldOption(1,api_text("settings_framework-sessions_multiple-allowed"));
  $form->addField("select","sessions_idle_timeout",api_text("settings_framework-sessions_idle_timeout"),$settings->sessions_idle_timeout,api_text("settings_framework-sessions_idle_timeout-placeholder"));
  $form->addFieldOption(900,api_text("settings_framework-sessions_idle_timeout-15m"));
  $form->addFieldOption(3600,api_text("settings_framework-sessions_idle_timeout-1h"));
  $form->addFieldOption(14400,api_text("settings_framework-sessions_idle_timeout-4h"));
  $form->addFieldOption(28800,api_text("settings_framework-sessions_idle_timeout-8h"));
  $form->addFieldOption(86400,api_text("settings_framework-sessions_idle_timeout-24h"));
  $form->addField("splitter");
  $form->addField("text","sessions_ldap_hostname",api_text("settings_framework-sessions_ldap_hostname"),$settings->sessions_ldap_hostname,api_text("settings_framework-sessions_ldap_hostname-placeholder"));
  $form->addField("text","sessions_ldap_dn",api_text("settings_framework-sessions_ldap_dn"),$settings->sessions_ldap_dn,api_text("settings_framework-sessions_ldap_dn-placeholder"));
  $form->addField("text","sessions_ldap_domain",api_text("settings_framework-sessions_ldap_domain"),$settings->sessions_ldap_domain,api_text("settings_framework-sessions_ldap_domain-placeholder"));
  $form->addField("text","sessions_ldap_userfield",api_text("settings_framework-sessions_ldap_userfield"),$settings->sessions_ldap_userfield,api_text("settings_framework-sessions_ldap_userfield-placeholder"));
  $form->addField("text","sessions_ldap_groups",api_text("settings_framework-sessions_ldap_groups"),$settings->sessions_ldap_groups,api_text("settings_framework-sessions_ldap_groups-placeholder"));
  $form->addField("radio","sessions_ldap_cache",api_text("settings_framework-sessions_ldap_cache"),$settings->sessions_ldap_cache,NULL,NULL,"radio-inline");
  $form->addFieldOption(0,api_text("no"));
  $form->addFieldOption(1,api_text("settings_framework-sessions_ldap_cache-allowed"));
 }
 /**
  * Sendmail
  */
 if(TAB=="sendmail"){
  $form->addField("text","sendmail_from_name",api_text("settings_framework-sendmail_from_name"),$settings->sendmail_from_name,api_text("settings_framework-sendmail_from_name-placeholder"));
  $form->addField("text","sendmail_from_mail",api_text("settings_framework-sendmail_from_mail"),$settings->sendmail_from_mail,api_text("settings_framework-sendmail_from_mail-placeholder"));
  $form->addField("radio","sendmail_asynchronous",api_text("settings_framework-sendmail_asynchronous"),$settings->sendmail_asynchronous,NULL,NULL,"radio-inline");
  $form->addFieldOption(0,api_text("no"));
  $form->addFieldOption(1,api_text("settings_framework-sendmail_asynchronous-enabled",api_link("#",api_icon("question-sign"))));
  $form->addField("radio","sendmail_method",api_text("settings_framework-sendmail_method"),$settings->sendmail_method,NULL,NULL,"radio-inline");
  $form->addFieldOption("standard",api_text("settings_framework-sendmail_method-standard"));
  $form->addFieldOption("smtp",api_text("settings_framework-sendmail_method-smtp"));
  $form->addField("splitter");
  $form->addField("text","sendmail_smtp_hostname",api_text("settings_framework-sendmail_smtp_hostname"),$settings->sendmail_smtp_hostname,api_text("settings_framework-sendmail_smtp_hostname-placeholder"));
  $form->addField("text","sendmail_smtp_username",api_text("settings_framework-sendmail_smtp_username"),$settings->sendmail_smtp_username,api_text("settings_framework-sendmail_smtp_username-placeholder"));
  $form->addField("text","sendmail_smtp_password",api_text("settings_framework-sendmail_smtp_password"),NULL,api_text("settings_framework-sendmail_smtp_password-placeholder"));
  $form->addField("radio","sendmail_smtp_encryption",api_text("settings_framework-sendmail_smtp_encryption"),$settings->sendmail_smtp_encryption,NULL,NULL,"radio-inline");
  $form->addFieldOption("",api_text("settings_framework-sendmail_smtp_encryption-none"));
  $form->addFieldOption("tls",api_text("settings_framework-sendmail_smtp_encryption-tls"));
  $form->addFieldOption("ssl",api_text("settings_framework-sendmail_smtp_encryption-ssl"));
 }
 /**
  * Users
  */
 if(TAB=="users"){
  $form->addField("select","users_password_expiration",api_text("settings_framework-users_password_expiration"),$settings->users_password_expiration,api_text("settings_framework-users_password_expiration-placeholder"));
  $form->addFieldOption(-1,api_text("settings_framework-users_password_expiration-never"));
  $form->addFieldOption(2592000,api_text("settings_framework-users_password_expiration-30days"));
  $form->addFieldOption(5184000,api_text("settings_framework-users_password_expiration-60days"));
  $form->addFieldOption(7776000,api_text("settings_framework-users_password_expiration-90days"));
 }
 /**
  * Tokens
  */
 if(TAB=="token"){
  $form->addField("text","token_cron",api_text("settings_framework-token_cron"),$settings->token_cron,api_text("settings_framework-token_cron-placeholder"));
  $form->addFieldAddonButton("?mod=framework&scr=settings_framework&tab=token&act=token_cron_randomize",api_text("settings_framework-token_cron-randomize"));
 }
 // form controls
 $form->addControl("submit",api_text("settings_framework-submit"));
 $form->addControl("reset",api_text("settings_framework-reset"));
 $form->addControl("button",api_text("settings_framework-cancel"),"?mod=framework&scr=settings_framework");
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to html
 $html->addContent($tabs->render(FALSE));
 $html->addContent($grid->render());
 // renderize html page
 $html->render();
?>