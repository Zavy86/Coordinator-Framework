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

 // build settings form
 $form=new Form("?mod=settings&scr=submit&act=settings_framework","POST",NULL,"settings_framework");

 $form->addField("text","title",api_text("settings_framework-title"),$settings->title,api_text("settings_framework-title-placeholder"));
 $form->addField("text","owner",api_text("settings_framework-owner"),$settings->owner,api_text("settings_framework-owner-placeholder"));

 $form->addField("separator");

 $form->addField("splitter");

 $form->addField("radio","sessions_multiple",api_text("settings_framework-sessions_multiple"),$settings->sessions_multiple,NULL,NULL,"radio-inline");
 $form->addFieldOption(0,api_text("no"));
 $form->addFieldOption(1,api_text("yes"));

 $form->addField("text","sessions_idle_timeout",api_text("settings_framework-sessions_idle_timeout"),$settings->sessions_idle_timeout,api_text("settings_framework-sessions_idle_timeout-placeholder"),3);

 $form->addField("separator");

 $form->addField("radio","authentication_method",api_text("settings_framework-authentication_method"),$settings->authentication_method);
 $form->addFieldOption("standard",api_text("settings_framework-authentication_method-standard"));
 $form->addFieldOption("ldap",api_text("settings_framework-authentication_method-ldap")." &rarr; ".api_link("?mod=settings&scr=settings_plugins&plugin=ldap_auth",api_text("settings_framework-settings_plugins"),NULL,NULL,FALSE,NULL,NULL,"_blank")); /** @todo elenco automatico dei metodi disponibili in base ai plugin */

 $form->addField("separator");

 $form->addField("text","sendmail_name",api_text("settings_framework-sendmail_name"),$settings->sendmail_name,api_text("settings_framework-sendmail_name-placeholder"));
 $form->addField("text","sendmail_mail",api_text("settings_framework-sendmail_mail"),$settings->sendmail_mail,api_text("settings_framework-sendmail_mail-placeholder"));
 $form->addField("radio","sendmail_method",api_text("settings_framework-sendmail_method"),$settings->sendmail_method);
 $form->addFieldOption("standard",api_text("settings_framework-sendmail_method-standard"));
 $form->addFieldOption("smtp",api_text("settings_framework-sendmail_method-smtp")." &rarr; ".api_link("?mod=settings&scr=settings_plugins&plugin=ldap_auth",api_text("settings_framework-settings_plugins"),NULL,NULL,FALSE,NULL,NULL,"_blank")); /** @todo elenco automatico dei metodi disponibili in base ai plugin */

 $form->addField("separator");

 $form->addControl("submit",api_text("settings_framework-submit"));
 $form->addControl("reset",api_text("settings_framework-reset"));

 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($form->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render(FALSE));
 // renderize html page
 $html->render();
?>