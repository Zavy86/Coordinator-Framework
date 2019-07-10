<?php
/**
 * Log
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Log class
  */
 class cLog{

  /** Properties */
  protected $id;
  protected $fkObject;
  protected $fkUser;
  protected $timestamp;
  protected $alert;
  protected $event;
  protected $properties;

  private $class;

  /**
   * Log class
   *
   * @param object|integer $event Event object
   * @param string $class Object class
   */
  public function __construct($event,$class){
   // set properties
   $this->id=$event->id;
   $this->fkObject=$event->fkObject;
   $this->fkUser=$event->fkUser;
   $this->timestamp=$event->timestamp;
   $this->alert=$event->level;
   $this->event=$event->event;
   $this->properties=json_decode($event->properties_json);
   $this->class=$class;
   $this->note=$event->properties_json;
  }

  /**
   * Get
   *
   * @param string $property Property name
   * @return string
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
   if($this->alert){$text=api_text("event-level-warning");}
   else{$text=api_text("event-level-information");}
   // make icon
   if($this->alert){$icon=api_icon("fa-warning",$text);}
   else{$icon=api_icon("fa-info-circle",$text);}
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
  public function getEvent(){return api_text($this->class."-event-".$this->event);}

 }

?>