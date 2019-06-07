<?php
/**
 * Framework - Groups View
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 // check authorizations
 api_checkAuthorization("framework-groups_manage","dashboard");
 // get object
 $group_obj=new cGroup($_REQUEST['idGroup']);
 if(!$group_obj->id){api_alerts_add(api_text("framework_alert_groupNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=groups_list");}
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set application title
 $app->setTitle(api_text("groups_view",$group_obj->name));
 die("@todo fare vista in modalità organigramma");
 // debug
 api_dump($group_obj,"group_obj");
?>