<?php
/**
 * Framework - Framework
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // check authorizations
 api_checkAuthorization("framework-settings_manage","dashboard");
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set application title
 $app->setTitle(api_text("settings_edit"));
 // check actions
 if(ACTION=="token_cron_randomize"||!$settings->token_cron){$settings->token_cron=api_random_id();}
 // make logo remove link
 if(substr($settings->logo,-8)=="logo.png"){$logo_remove_link=api_link("?mod=".MODULE."&scr=submit&act=settings_logo_remove&tab=generals",api_icon("fa-remove",api_text("settings_edit-logo-remove"),"hidden-link text-vtop"),null,null,false,api_text("settings_edit-logo-remove-confirm"));}
 // build settings tabs
 $tabs_nav=new strNav("nav-pills"); /** @tip modificare api form in modo da poter renderizzare separatamente nei tabs */
 $tabs_nav->addItem(api_text("settings_edit-generals"),"?mod=".MODULE."&scr=settings_edit&tab=generals");
 $tabs_nav->addItem(api_text("settings_edit-sessions"),"?mod=".MODULE."&scr=settings_edit&tab=sessions");
 $tabs_nav->addItem(api_text("settings_edit-mails"),"?mod=".MODULE."&scr=settings_edit&tab=mails");
 $tabs_nav->addItem(api_text("settings_edit-users"),"?mod=".MODULE."&scr=settings_edit&tab=users");
 $tabs_nav->addItem(api_text("settings_edit-tokens"),"?mod=".MODULE."&scr=settings_edit&tab=tokens");
 $tabs_nav->addItem(api_text("settings_edit-analytics"),"?mod=".MODULE."&scr=settings_edit&tab=analytics");
 // build settings form
 $form=new strForm("?mod=".MODULE."&scr=submit&act=settings_save&tab=".TAB,"POST",null,null,"settings_edit");
 // generals
 if(TAB=="generals"){
  $form->addField("radio","maintenance",api_text("settings_edit-maintenance"),(int)$settings->maintenance,null,null,"radio-inline");
  $form->addFieldOption(0,api_text("no"));
  $form->addFieldOption(1,api_text("settings_edit-maintenance-lock"));
  $form->addField("text","owner",api_text("settings_edit-owner"),$settings->owner,api_text("settings_edit-owner-placeholder"));
  $form->addField("text","title",api_text("settings_edit-title"),$settings->title,api_text("settings_edit-title-placeholder"));
  $form->addField("radio","show",api_text("settings_edit-show"),$settings->show,null,null,"radio-inline");
  $form->addFieldOption("logo_title",api_text("settings_edit-show-logo_title"));
  $form->addFieldOption("logo",api_text("settings_edit-show-logo"));
  $form->addFieldOption("title",api_text("settings_edit-show-title"));
  $form->addField("splitter");
  $form->addField("file","logo",api_text("settings_edit-logo"),null,null,null,null,null,"accept='.png'");
  $form->addField("static",null,null,api_image($settings->logo,"img-thumbnail",80).$logo_remove_link);
 }
 // sessions
 if(TAB=="sessions"){
  $form->addField("radio","sessions_authentication_method",api_text("settings_edit-sessions_authentication_method"),$settings->sessions_authentication_method,null,null,"radio-inline");
  $form->addFieldOption("standard",api_text("settings_edit-sessions_authentication_method-standard"));
  $form->addFieldOption("ldap",api_text("settings_edit-sessions_authentication_method-ldap"));
  $form->addField("radio","sessions_multiple",api_text("settings_edit-sessions_multiple"),(int)$settings->sessions_multiple,null,null,"radio-inline");
  $form->addFieldOption(0,api_text("no"));
  $form->addFieldOption(1,api_text("settings_edit-sessions_multiple-allowed"));
  $form->addField("select","sessions_idle_timeout",api_text("settings_edit-sessions_idle_timeout"),(int)$settings->sessions_idle_timeout,api_text("settings_edit-sessions_idle_timeout-placeholder"));
  $form->addFieldOption(900,api_text("settings_edit-sessions_idle_timeout-15m"));
  $form->addFieldOption(3600,api_text("settings_edit-sessions_idle_timeout-1h"));
  $form->addFieldOption(14400,api_text("settings_edit-sessions_idle_timeout-4h"));
  $form->addFieldOption(28800,api_text("settings_edit-sessions_idle_timeout-8h"));
  $form->addFieldOption(86400,api_text("settings_edit-sessions_idle_timeout-24h"));
  $form->addField("splitter");
  $form->addField("text","sessions_ldap_hostname",api_text("settings_edit-sessions_ldap_hostname"),$settings->sessions_ldap_hostname,api_text("settings_edit-sessions_ldap_hostname-placeholder"));
  $form->addField("text","sessions_ldap_dn",api_text("settings_edit-sessions_ldap_dn"),$settings->sessions_ldap_dn,api_text("settings_edit-sessions_ldap_dn-placeholder"));
  $form->addField("text","sessions_ldap_domain",api_text("settings_edit-sessions_ldap_domain"),$settings->sessions_ldap_domain,api_text("settings_edit-sessions_ldap_domain-placeholder"));
  $form->addField("text","sessions_ldap_userfield",api_text("settings_edit-sessions_ldap_userfield"),$settings->sessions_ldap_userfield,api_text("settings_edit-sessions_ldap_userfield-placeholder"));
  $form->addField("text","sessions_ldap_groups",api_text("settings_edit-sessions_ldap_groups"),$settings->sessions_ldap_groups,api_text("settings_edit-sessions_ldap_groups-placeholder"));
  $form->addField("radio","sessions_ldap_cache",api_text("settings_edit-sessions_ldap_cache"),(int)$settings->sessions_ldap_cache,null,null,"radio-inline");
  $form->addFieldOption(0,api_text("no"));
  $form->addFieldOption(1,api_text("settings_edit-sessions_ldap_cache-allowed"));
 }
 // mails
 if(TAB=="mails"){
  $form->addField("text","mail_from_name",api_text("settings_edit-mail_from_name"),$settings->mail_from_name,api_text("settings_edit-mail_from_name-placeholder"));
  $form->addField("text","mail_from_address",api_text("settings_edit-mail_from_address"),$settings->mail_from_address,api_text("settings_edit-mail_from_address-placeholder"));
  $form->addField("radio","mail_asynchronous",api_text("settings_edit-mail_asynchronous"),(int)$settings->mail_asynchronous,null,null,"radio-inline");
  $form->addFieldOption(0,api_text("no"));
  $form->addFieldOption(1,api_text("settings_edit-mail_asynchronous-enabled",api_link("?mod=".MODULE."&scr=settings_edit&tab=mails&act=cron_informations",api_icon("fa-question-circle"),null,"hidden-link")));
  $form->addField("radio","mail_method",api_text("settings_edit-mail_method"),$settings->mail_method,null,null,"radio-inline");
  $form->addFieldOption("standard",api_text("settings_edit-mail_method-standard"));
  $form->addFieldOption("smtp",api_text("settings_edit-mail_method-smtp"));
  $form->addField("splitter");
  $form->addField("text","mail_smtp_hostname",api_text("settings_edit-mail_smtp_hostname"),$settings->mail_smtp_hostname,api_text("settings_edit-mail_smtp_hostname-placeholder"));
  $form->addField("text","mail_smtp_username",api_text("settings_edit-mail_smtp_username"),$settings->mail_smtp_username,api_text("settings_edit-mail_smtp_username-placeholder"));
  $form->addField("text","mail_smtp_password",api_text("settings_edit-mail_smtp_password"),null,api_text("settings_edit-mail_smtp_password-placeholder"));
  $form->addField("radio","mail_smtp_encryption",api_text("settings_edit-mail_smtp_encryption"),$settings->mail_smtp_encryption,null,null,"radio-inline");
  $form->addFieldOption(null,api_text("settings_edit-mail_smtp_encryption-none"));
  $form->addFieldOption("tls",api_text("settings_edit-mail_smtp_encryption-tls"));
  $form->addFieldOption("ssl",api_text("settings_edit-mail_smtp_encryption-ssl"));
  // cron informations
  if(ACTION=="cron_informations"){
   // build cron informations modal window
   $cron_informations_modal=new strModal(api_text("settings_edit-mail_asynchronous-modal-title"),null,"requests_view-cron_informations_modal");
   $cron_informations_modal->setBody(api_text("settings_edit-mail_asynchronous-modal-body",array(URL,$settings->token_cron)));
   // add modal to application
   $app->addModal($cron_informations_modal);
   // jQuery scripts
   $app->addScript("/* Cron informations modal window opener */\n$(function(){\$(\"#modal_requests_view-cron_informations_modal\").modal('show');});");
  }
 }
 // users
 if(TAB=="users"){
  $form->addField("select","users_password_expiration",api_text("settings_edit-users_password_expiration"),(int)$settings->users_password_expiration,api_text("settings_edit-users_password_expiration-placeholder"));
  $form->addFieldOption(-1,api_text("settings_edit-users_password_expiration-never"));
  $form->addFieldOption(2592000,api_text("settings_edit-users_password_expiration-30days"));
  $form->addFieldOption(5184000,api_text("settings_edit-users_password_expiration-60days"));
  $form->addFieldOption(7776000,api_text("settings_edit-users_password_expiration-90days"));
  $form->addField("splitter");
  $form->addField("number","users_level_max",api_text("settings_edit-users_level_max"),$settings->users_level_max,api_text("settings_edit-users_level_max-placeholder"),null,null,null,"min='1' max='99'");
 }
 // tokens
 if(TAB=="tokens"){
  $form->addField("text","token_cron",api_text("settings_edit-token_cron"),$settings->token_cron,api_text("settings_edit-token_cron-placeholder"));
  $form->addFieldAddonButton("?mod=".MODULE."&scr=settings_edit&tab=tokens&act=token_cron_randomize",api_text("settings_edit-token_cron-randomize"));
  $form->addField("text","token_gtag",api_text("settings_edit-token_gtag"),$settings->token_gtag,api_text("settings_edit-token_gtag-placeholder"));
 }
 // analytics
 if(TAB=="analytics"){
	$form->addField("textarea","analytics_script",api_text("settings_edit-analytics_script"),$settings->analytics_script,api_text("settings_edit-analytics_script-placeholder"),null,null,null,"rows='9'");
 }
 // controls
 $form->addControl("submit",api_text("form-fc-save"));
 $form->addControl("reset",api_text("settings_edit-reset"));
 $form->addControl("button",api_text("settings_edit-cancel"),"?mod=".MODULE."&scr=dashboard");
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to application
 $app->addContent($tabs_nav->render(false));
 $app->addContent($grid->render());
 // renderize application
 $app->render();
?>