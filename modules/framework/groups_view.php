<?php
/**
 * Framework - Groups View
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */

/** @todo rifare vista in modalità organigramma */

$authorization="framework-groups_manage";

// include module template
require_once(MODULE_PATH."template.inc.php");
// get object
$group_obj=new cGroup($_REQUEST['idGroup']);
if(!$group_obj->id){api_alerts_add(api_text("framework_alert_groupNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=groups_list");}
// deleted alert
if($group_obj->deleted){api_alerts_add(api_text("groups_view-deleted-alert"),"warning");}
// set html title
$html->setTitle(api_text("groups_view",$group_obj->name));
// build left group description list
$dl_left=new strDescriptionList("br","dl-horizontal");
$dl_left->addElement(api_text("groups_view-name"),api_tag("strong",$group_obj->name));
if($group_obj->description){$dl_left->addElement(api_text("groups_view-description"),$group_obj->description);}
// build users table
$users_table=new strTable(api_text("groups_view-users_table-tr-unvalued"));
// cycle all assigned users
foreach($group_obj->getAssignedUsers() as $assigend_user_f){
 // get user object
 $user_obj=new cUser($assigend_user_f->id);
 // add group row
 $users_table->addRow();
 $users_table->addRowField(api_link("?mod=".MODULE."&scr=users_view&idUser=".$user_obj->id,$user_obj->fullname." (".$user_obj->level.")",null,"hidden-link",true,null,null,null,"_blank"),"truncate-ellipsis");
}
// build right group description list
$dl_right=new strDescriptionList("br","dl-horizontal");
$dl_right->addElement(api_text("groups_view-users"),$users_table->render());
// build grid object
$grid=new strGrid();
$grid->addRow();
$grid->addCol($dl_left->render(),"col-xs-12 col-sm-5");
$grid->addCol($dl_right->render(),"col-xs-12 col-sm-7");
//$grid->addCol($companies_table->render().$users_table->render(),"col-xs-12 col-sm-7");
// add content to html
$html->addContent($grid->render());
// renderize html page
$html->render();
// debug
if($GLOBALS['debug']){api_dump($group_obj,"group_obj");}
?>