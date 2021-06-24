<?php
/**
 * Framework - Groups View
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 *
 * @var $app strApplication
 */
// check authorizations
api_checkAuthorization("framework-groups_manage","dashboard");
// get objects
$group_obj=new cGroup($_REQUEST['idGroup']);
$fathergroup_obj=new cGroup($group_obj->fkGroup);
// check objects
//if(!$group_obj->id){api_alerts_add(api_text("framework_alert_groupNotFound"),"danger");api_redirect("?mod=".MODULE."&scr=groups_list");}
// include module template
require_once(MODULE_PATH."template.inc.php");
// set application title
$app->setTitle(api_text("groups_view",$group_obj->name));
// make content
function groups_view_makeContent($group_obj){
	$content=api_link(api_url(["scr"=>"groups_view","idGroup"=>$group_obj->id]),$group_obj->name,$group_obj->description,"hidden-link text-strong",true);
	$content.="<hr>".api_tag("div",implode("<br>",groups_view_getArrayOfAssignedUsers($group_obj)));
	return $content;
}
// get assigned users
function groups_view_getArrayOfAssignedUsers($group_obj){
	$users_array=array();
	foreach($group_obj->getAssignedUsers() as $assignedUser_f){
		$user_obj=new cUser($assignedUser_f->id);
		if(!$user_obj->enabled || $user_obj->deleted){continue;}
		$users_array[$user_obj->fullname.$user_obj->id]=api_link(api_url(["scr"=>"users_view","idUser"=>$user_obj->id]),$user_obj->fullname,null,"hidden-link",false,null,null,null,"_blank");
	}
	ksort($users_array);
	return $users_array;
}
// check for father
if($fathergroup_obj->id){
	// build tree starting from father
	$tree=new strTree(groups_view_makeContent($fathergroup_obj));
}else{
	// build tree starting from "home"
	$tree=new strTree(api_link(api_url(["scr"=>"groups_view"]),api_icon("fa-home",api_text("groups_view-home")),null,"hidden-link"),(!$group_obj->id?"active":null));
}
// check for group
if($group_obj->id){
	// build group tree starting from father
	$group_tree=$tree->addNode(groups_view_makeContent($group_obj),"active");
}else{
	// group tree is "home"
	$group_tree=$tree;
}
// cycle all subgroups
foreach(api_availableGroups($group_obj->id) as $subgroup_fobj){
	// add content to node
	$sub_tree=$group_tree->addNode(groups_view_makeContent($subgroup_fobj));
	// cycle all subgroups
	foreach(api_availableGroups($subgroup_fobj->id) as $subsubgroup_fobj){
		// add content to node
		$sub_sub_tree=$sub_tree->addNode(groups_view_makeContent($subsubgroup_fobj));
		// check for other subgroups
		if(count(api_availableGroups($subsubgroup_fobj->id))){
			$sub_sub_tree->addNode(api_link(api_url(["scr"=>"groups_view","idGroup"=>$subsubgroup_fobj->id]),api_icon("fa-list",api_text("groups_view-subwalk"),"hidden-link")));
		}
	}
}
// build grid object
$grid=new strGrid();
$grid->addRow();
$grid->addCol($tree->render(),"col-xs-12");
// add content to application
$app->addContent($grid->render());
// renderize application
$app->render();
// debug
api_dump($group_obj,"group object");
api_dump($fathergroup_obj,"father group object");
api_dump($tree);
