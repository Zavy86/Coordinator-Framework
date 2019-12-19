<?php
/**
 * Framework - Attachments List
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gattachment.com>
 * @link    http://www.coordinator.it
 */
 // check authorizations
 api_checkAuthorization("framework-attachments_manage","dashboard");
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set application title
 $app->setTitle(api_text("attachments_list"));
 // build filter
 $filter=new strFilter();
 $filter->addSearch(array("id","name","description","typology"));
 // build query
 $query=new cQuery("framework__attachments",$filter->getQueryWhere());
 $query->addQueryOrderField("addTimestamp","DESC",null,true);
 // build pagination
 $pagination=new strPagination($query->getRecordsCount());
 // build table
 $table=new strTable(api_text("attachments_list-tr-unvalued"));
 $table->addHeader($filter->link(api_icon("fa-filter",api_text("filters-modal-link"),"hidden-link"))." ".api_text("attachments_list-th-id"),"nowrap");
 $table->addHeader(api_text("attachments_list-th-name"),null,"100%");
 $table->addHeader(api_text("attachments_list-th-size"),"nowrap text-right");
 $table->addHeader("&nbsp;",null,16);
 // get attachments
 $attachments_array=array();
 foreach($query->getRecords($pagination->getQueryLimits()) as $attachment){$attachments_array[$attachment->id]=new cAttachment($attachment);}
 // cycle all attachments
 foreach($attachments_array as $attachment_fobj){
  // build operation button
  $ob=new strOperationsButton();
  $ob->addElement("?mod=".MODULE."&scr=attachments_list&act=attachment_view&idAttachment=".$attachment_fobj->id,"fa-info-circle",api_text("attachments_list-td-view"));
  $ob->addElement("?mod=".MODULE."&scr=attachments_list&act=attachment_edit&idAttachment=".$attachment_fobj->id,"fa-pencil",api_text("attachments_list-td-edit"));
  $ob->addElement($attachment_fobj->url,"fa-cloud-download",api_text("attachments_list-td-download"),true,null,null,null,null,"_blank");
  // make table row class
  $tr_class_array=array();
  if($attachment_fobj->id==$_REQUEST['idAttachment']){$tr_class_array[]="currentrow";}
  if($attachment_fobj->deleted){$tr_class_array[]="deleted";}
  // make table row
  $table->addRow(implode(" ",$tr_class_array));
  $table->addRowField(api_tag("samp",$attachment_fobj->id),"nowrap");
  $table->addRowField($attachment_fobj->name,"truncate-ellipsis");
  $table->addRowField(api_number_format($attachment_fobj->size/1000/1000)." MB","nowrap text-right");
  $table->addRowField($ob->render(),"text-right");
 }
 // attachment visualization modal window
 if(ACTION=="attachment_view" && $_REQUEST['idAttachment']){
  $selected_attachment_obj=new cAttachment($_REQUEST['idAttachment']);
  // build attachment description list
  $attachment_dl=new strDescriptionList("br","dl-horizontal");
  $attachment_dl->addElement(api_text("attachments_list-modal-dt-id"),api_tag("samp",$selected_attachment_obj->id));
  $attachment_dl->addElement(api_text("attachments_list-modal-dt-name"),api_tag("strong",$selected_attachment_obj->name));
  if($selected_attachment_obj->description){$attachment_dl->addElement(api_text("attachments_list-modal-dt-description"),nl2br($selected_attachment_obj->description));}
  $attachment_dl->addElement(api_text("attachments_list-modal-dt-typology"),api_tag("samp",$selected_attachment_obj->typology));
  $attachment_dl->addElement(api_text("attachments_list-modal-dt-size"),api_number_format($selected_attachment_obj->size/1000/1000)." MB");
  $attachment_dl->addElement(api_text("attachments_list-modal-dt-public"),($selected_attachment_obj->public?api_text("yes"):api_text("no")));
  $attachment_dl->addElement(api_text("attachments_list-modal-dt-downloads"),$selected_attachment_obj->downloads);
  $attachment_dl->addSeparator("hr");
  $attachment_dl->addElement(api_text("attachments_list-modal-dt-add"),api_text("attachments_list-modal-dd-add",array((new cUser($selected_attachment_obj->addFkUser))->fullname,api_timestamp_format($selected_attachment_obj->addTimestamp,api_text("datetime")))));
  if($selected_attachment_obj->updTimestamp){$attachment_dl->addElement(api_text("attachments_list-modal-dt-upd"),api_text("attachments_list-modal-dd-upd",array((new cUser($selected_attachment_obj->updFkUser))->fullname,api_timestamp_format($selected_attachment_obj->updTimestamp,api_text("datetime")))));}
  // build cron informations modal window
  $attachments_modal=new strModal(api_text("attachments_list-modal-title-view"),null,"requests_view-attachments_modal");
  $attachments_modal->setBody($attachment_dl->render());
  // add modal to application
  $app->addModal($attachments_modal);
  // jQuery scripts
  $app->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_requests_view-attachments_modal\").modal('show');});");
 }
 // attachment editing modal window
 if(ACTION=="attachment_edit" && $_REQUEST['idAttachment']){
  $selected_attachment_obj=new cAttachment($_REQUEST['idAttachment']);
  // build attachment form
  $attachments_form=new strForm("?mod=".MODULE."&scr=submit&act=attachment_save&idAttachment=".$selected_attachment_obj->id,"POST",null,null,"attachments_list-edit");
  $attachments_form->addField("static","id",api_text("attachments_list-modal-ff-id"),$selected_attachment_obj->id);
  $attachments_form->addField("text","name",api_text("attachments_list-modal-ff-name"),$selected_attachment_obj->name);
  $attachments_form->addField("textarea","description",api_text("attachments_list-modal-ff-description"),$selected_attachment_obj->description);
  $attachments_form->addField("radio","public",api_text("attachments_list-modal-ff-public"),$selected_attachment_obj->public,null,null,"radio-inline");
  $attachments_form->addFieldOption(0,api_text("no"));
  $attachments_form->addFieldOption(1,api_text("yes"));
  $attachments_form->addControl("submit",api_text("form-fc-submit"));
  if(!$selected_attachment_obj->deleted){$attachments_form->addControl("button",api_text("form-fc-delete"),"?mod=".MODULE."&scr=submit&act=attachment_delete&idAttachment=".$selected_attachment_obj->id,"btn-danger",api_text("attachments_list-modal-fc-delete-confirm"));}
  else{
   $attachments_form->addControl("button",api_text("form-fc-undelete"),"?mod=".MODULE."&scr=submit&act=attachment_undelete&idAttachment=".$selected_attachment_obj->id,"btn-warning");
   $attachments_form->addControl("button",api_text("form-fc-remove"),"?mod=".MODULE."&scr=submit&act=attachment_remove&idAttachment=".$selected_attachment_obj->id,"btn-danger",api_text("attachments_list-modal-fc-remove-confirm"));
  }
  // build modal window
  $attachments_modal=new strModal(api_text("attachments_list-modal-title-edit"),null,"requests_view-attachments_modal");
  $attachments_modal->setBody($attachments_form->render(2));
  // add modal to application
  $app->addModal($attachments_modal);
  // jQuery scripts
  $app->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_requests_view-attachments_modal\").modal('show');});");
 }
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($filter->render(),"col-xs-12");
 $grid->addRow();
 $grid->addCol($table->render(),"col-xs-12");
 $grid->addRow();
 $grid->addCol($pagination->render(),"col-xs-12");
 // add content to application
 $app->addContent($grid->render());
 // renderize application
 $app->render();
 // debug
 api_dump($attachments_array,"attachments_array");
?>