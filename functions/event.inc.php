<?php
/**
 * Event Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Events table
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

    /** @todo check replace*/
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