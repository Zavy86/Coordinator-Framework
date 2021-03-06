<?php
/**
 * Event
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Event class
  */
 class cEvent{

  /** Properties */
  protected $id;
  protected $fkUser;
  protected $timestamp;
  protected $level;
  protected $event;
  protected $note;
  protected $module;

  /**
   * Event class
   *
   * @param object|integer $event Event object or ID
   * @param string $module Module for event
   * @return object Return query object for extensions
   */
  public function __construct($event,$module="framework"){
   // get object
   if(!$event->id){return false;}
   // set properties
   $this->id=(int)$event->id;
   $this->fkUser=(int)$event->fkUser;
   $this->timestamp=(int)$event->timestamp;
   $this->level=stripslashes($event->level);
   $this->event=stripslashes($event->event);
   $this->note=stripslashes($event->note);
   $this->module=$module;
   // return
   return $event;
  }

  /**
   * Get
   *
   * @param string $property Property name
   * @return string Property value
   */
  public function __get($property){return $this->$property;}

  /**
   * Get Level
   *
   * @param boolean $showIcon Return level icon
   * @param boolean $showText Return level text
   * @return string Level icon and/or text
   */
  public function getLevel($showIcon=true,$showText=true){
   // make text
   $text=api_text("event-level-".$this->level);
   // make icon
   switch($this->level){
    case "debug":$icon=api_icon("fa-bug",$text);break;
    case "information":$icon=api_icon("fa-info-circle",$text);break;
    case "warning":$icon=api_icon("fa-warning",$text);break;
    case "error":$icon=api_icon("fa-window-close",$text);break;
   }
   // make return
   if($showIcon){$return=$icon;}
   if($showText){$return=$text;}
   if($showIcon && $showText){$return=$icon." ".$text;}
   return $return;
  }

  /**
   * Get Event
   *
   * @return string Event name
   */
  public function getEvent(){
   // make return
   $return=api_text($this->module."_event-".$this->event);
   // return
   return $return;
  }

 }

?>