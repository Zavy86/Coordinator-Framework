<?php
/**
 * Framework - Mails List
 *
 * @package Coordinator\Modules\Framework
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 $authorization="framework-mails_manage";
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // set html title
 $html->setTitle(api_text("mails_list"));
 // make recipeint filter
 $mails_filters=array();
 $results=$GLOBALS['database']->queryObjects("SELECT DISTINCT(`recipients_to`) FROM `framework__mails`");
 foreach($results as $result){$mails_filters[$result->recipients_to]=$result->recipients_to;}
 // build filter
 $filter=new strFilter();
 $filter->addSearch(array("recipients_to","recipients_cc","recipients_bcc","subject","message"));
 $filter->addItem(api_text("mails_list-filter-status"),array("inserted"=>api_text("mail-status-inserted"),"sended"=>api_text("mail-status-sended"),"failed"=>api_text("mail-status-failed")),"status","framework__mails");
 $filter->addItem(api_text("mails_list-filter-recipient"),$mails_filters,"recipients_to","framework__mails");
 // build query
 $query=new cQuery("framework__mails",$filter->getQueryWhere());
 $query->addQueryOrderField("sndTimestamp","DESC",null,true);
 // build pagination
 $pagination=new strPagination($query->getRecordsCount());
 // build table
 $table=new strTable(api_text("mails_list-tr-unvalued"));
 $table->addHeader($filter->link(api_icon("fa-filter",api_text("filters-modal-link"),"hidden-link")),"text-center",16);
 $table->addHeader(api_text("mails_list-th-addTimestamp"),"nowrap");
 $table->addHeader(api_text("mails_list-th-recipients"),"nowrap");
 $table->addHeader(api_text("mails_list-th-subject"),null,"100%");
 $table->addHeader(api_text("mails_list-th-sndTimestamp"),"nowrap text-right");
 $table->addHeader("&nbsp;",null,16);
 // get mails
 $mails_array=array();
 //$mails_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__mails` ORDER BY `sndTimestamp` IS NULL DESC,`sndTimestamp` DESC");
 //foreach($mails_results as $mail){$mails_array[$mail->id]=new cMail($mail);}
 foreach($query->getRecords($pagination->getQueryLimits()) as $mail){$mails_array[$mail->id]=new cMail($mail);}
 // cycle all mails
 foreach($mails_array as $mail_fobj){
  // make recipients
  $recipients_td=implode(";",$mail_fobj->recipients_to);
  if(!$recipients_td){$recipients_td=implode(";",$mail_fobj->recipients_cc);}
  if(!$recipients_td){$recipients_td=implode(";",$mail_fobj->recipients_bcc);}
  // build operation button
  $ob=new strOperationsButton();
  $ob->addElement("?mod=".MODULE."&scr=mails_list&act=mail_view&idMail=".$mail_fobj->id,"fa-info-circle",api_text("mails_list-td-view"));
  if($mail_fobj->status=="failed"){$ob->addElement("?mod=".MODULE."&scr=submit&act=mail_retry&idMail=".$mail_fobj->id,"fa-refresh",api_text("mails_list-td-retry"));}
  if($mail_fobj->status!="sended"){$ob->addElement("?mod=".MODULE."&scr=submit&act=mail_remove&idMail=".$mail_fobj->id,"fa-trash",api_text("mails_list-td-remove"),true,api_text("mails_list-td-remove-confirm"));}
  // make tr class
  $tr_class=null;
  if($mail_fobj->status=="failed"){$tr_class="danger";}
  if($mail_fobj->id==$_REQUEST['idMail']){$tr_class="info";}
  // make mail row
  $table->addRow($tr_class);
  $table->addRowField($mail_fobj->getStatus(true,false),"nowrap");
  $table->addRowField(api_timestamp_format($mail_fobj->addTimestamp,api_text("datetime")),"nowrap");
  $table->addRowField($recipients_td,"nowrap");
  //$table->addRowField($mail_fobj->getStatus(true,false));
  $table->addRowField($mail_fobj->subject,"truncate-ellipsis");
  $table->addRowField(api_timestamp_format($mail_fobj->sndTimestamp,api_text("datetime")),"nowrap");
  $table->addRowField($ob->render(),"text-right");
 }
 // mail visualization modal window
 if(ACTION=="mail_view" && $_REQUEST['idMail']){
  $mail_obj=new cMail($_REQUEST['idMail']);
  // build mail description list
  $mail_dl=new strDescriptionList("br","dl-horizontal");
  $mail_dl->addElement(api_text("mails_list-mails-modal-dl-sender"),$mail_obj->sender_name." &lt;".$mail_obj->sender_mail."&gt;");
  if(count($mail_obj->recipients_to)){$mail_dl->addElement(api_text("mails_list-mails-modal-dl-recipients_to"),implode("; ",$mail_obj->recipients_to));}
  if(count($mail_obj->recipients_cc)){$mail_dl->addElement(api_text("mails_list-mails-modal-dl-recipients_cc"),implode("; ",$mail_obj->recipients_cc));}
  if(count($mail_obj->recipients_bcc)){$mail_dl->addElement(api_text("mails_list-mails-modal-dl-recipients_bcc"),implode("; ",$mail_obj->recipients_bcc));}
  $mail_dl->addElement(api_text("mails_list-mails-modal-dl-subject"),api_tag("strong",$mail_obj->subject));
  if(count($mail_obj->attachments)){$mail_dl->addElement(api_text("mails_list-mails-modal-dl-attachments"),implode("<br>",$mail_obj->attachments));}
  if($mail_obj->errors){$mail_dl->addElement(api_text("mails_list-mails-modal-dl-errors"),api_tag("span",$mail_obj->errors,"text-danger"));}
  $mail_dl->addSeparator("hr");
  // build cron informations modal window
  $mails_modal=new strModal(api_text("mails_list-mails-modal-title"),null,"requests_view-mails_modal");
  $mails_modal->setBody($mail_dl->render().$mail_obj->message);
  // add modal to html object
  $html->addModal($mails_modal);
  // jQuery scripts
  $html->addScript("/* Modal window opener */\n$(function(){\$(\"#modal_requests_view-mails_modal\").modal('show');});");
 }
 // build grid object
 $grid=new strGrid();
 $grid->addRow();
 $grid->addCol($filter->render(),"col-xs-12");
 $grid->addRow();
 $grid->addCol($table->render(),"col-xs-12");
 $grid->addRow();
 $grid->addCol($pagination->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render());
 // renderize html
 $html->render();
 // debug
 api_dump($mails_array);
?>