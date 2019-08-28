<?php
/**
 * Event Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Logs table
  *
  * @param objects $logs_array Array of log objects
  * @return object Return strTable object
  */
 function api_logs_table($logs_array){
  // build logs table
  $logs_table=new strTable(api_text("logs-tr-unvalued"));
  if($_REQUEST['all_logs']){$logs_table->addHeader("&nbsp;",null,16);}
  else{$logs_table->addHeader(api_link(api_url(array_merge($_GET,["all_logs"=>1])),api_icon("fa-archive",api_text("logs-th-all"),"hidden-link")),"text-center",16);}
  $logs_table->addHeader(api_text("logs-th-timestamp"),"nowrap");
  $logs_table->addHeader(api_text("logs-th-event"),"nowrap");
  $logs_table->addHeader("&nbsp;",null,"100%");
  $logs_table->addHeader(api_text("logs-th-user"),"nowrap text-right");
  if($GLOBALS['session']->user->superuser){$logs_table->addHeader("&nbsp;",null,16);}
  // check parameters
  if(is_array($logs_array)){
   // cycle logs
   foreach($logs_array as $log_fobj){
    // make table row class
    $tr_class_array=array();
    if($log_fobj->id==$_REQUEST['idLog']){$tr_class_array[]="info";}
    if($log_fobj->alert){$tr_class_array[]="warning";}
    // make area row
    $logs_table->addRow(implode(" ",$tr_class_array));
    $logs_table->addRowField($log_fobj->getLevel(true,false),"nowrap");
    $logs_table->addRowField(api_timestamp_format($log_fobj->timestamp,api_text("datetime")),"nowrap");
    $logs_table->addRowField($log_fobj->getEvent(),"nowrap");
    $logs_table->addRowField($log_fobj->decodeProperties(),"truncate-ellipsis");
    $logs_table->addRowField((new cUser($log_fobj->fkUser))->fullname,"nowrap text-right");
    // check for superuser
    if($GLOBALS['session']->user->superuser){
     /*
     // build operation button
     $ob=new strOperationsButton();
     */
     // check for properties
     if($log_fobj->properties){
      // build log modal
      $log_modal=new strModal(api_text("logs-modal-title",$log_fobj->id));
      $log_modal->setBody("<pre style='background:#ffffff'>".print_r(json_encode($log_fobj->properties,JSON_PRETTY_PRINT),true)."</pre>");
      //
      $logs_table->addRowField($log_modal->link(api_tag("samp","#".$log_fobj->id),null,"btn btn-xs btn-default"));
      /*
      // add modal link operation button
      $ob->addElement("#".$log_modal->id,"fa-code","#".$log_fobj->id,true,null,null,null,"data-toggle='modal'");
      */
      // add modal to application
      $GLOBALS['app']->addModal($log_modal);
     }
     //
     else{$logs_table->addRowField(api_tag("samp","#".$log_fobj->id),"text-center");}
     /*
     // add operation buttons to table
     $ob->addElement(api_url(["mod"=>"framework","scr"=>"logs_edit","log_mod"=>$_REQUEST['mod'],"log_class"=>$log_fobj->class,"log_id"=>$log_fobj->id]),"fa-pencil",api_text("table-td-edit"),true,null,null,null,null,"_blank");
     $logs_table->addRowField($ob->render(),"text-right");
     */
    }
   }
  }
  // return
  return $logs_table;
 }





 /**
  * Events table      @deprecated api_logs_table
  *
  * @param objects $events_array Array of event objects
  * @return object Return strTable object
  */
 function api_events_table($events_array){
  // build events table
  $events_table=new strTable(api_text("events-tr-unvalued"));
  $events_table->addHeader("&nbsp;",null,16);
  $events_table->addHeader(api_text("events-th-timestamp"),"nowrap");
  $events_table->addHeader(api_text("events-th-event"),"nowrap");
  $events_table->addHeader(api_text("events-th-note"),null,"100%");
  $events_table->addHeader(api_text("events-th-fkUser"),"nowrap text-right");
  // check parameters
  if(is_array($events_array)){
   // cycle events
   foreach($events_array as $event_fobj){
    // switch level
    switch($event_fobj->level){
     case "debug":$tr_class="success";break;
     case "warning":$tr_class="warning";break;
     case "error":$tr_class="error";break;
     default:$tr_class=null;
    }
    // check selected
    if($event_fobj->id==$_REQUEST['idEvent']){$tr_class="info";}
    // make note
    $note_td=$event_fobj->note;
    if(strpos($note_td,"{")!==false){
     // key substring
     $key_start=(strpos($note_td,"{")+1);
     $key_end=strpos($note_td,"}");
     if(strpos($note_td,"|")!==false){
      // parameter substring
      $param_start=(strpos($note_td,"|")+1);
      $param_end=($key_end);
      $parameter=substr($note_td,$param_start,($param_end-$param_start));
      // update key end
      $key_end=(strpos($note_td,"|"));
     }
     $key=substr($note_td,$key_start,($key_end-$key_start));
     // check for parameter
     if(strlen($parameter)){$note_td=api_text($key,$parameter);}
     else{$note_td=api_text($key);}
    }
    // add event row
    $events_table->addRow($tr_class);
    $events_table->addRowField($event_fobj->getLevel(true,false),"nowrap");
    $events_table->addRowField(api_timestamp_format($event_fobj->timestamp,api_text("datetime")),"nowrap");
    $events_table->addRowField($event_fobj->getEvent(),"nowrap");
    $events_table->addRowField($note_td,"truncate-ellipsis");
    $events_table->addRowField((new cUser($event_fobj->fkUser))->fullname,"nowrap text-right");
   }
  }
  // return
  return $events_table;
 }

?>