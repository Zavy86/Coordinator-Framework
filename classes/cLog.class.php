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
   $this->alert=$event->alert;
   $this->event=$event->event;
   $this->properties=json_decode($event->properties_json);
   $this->class=$class;
  }

  /**
   * Get
   *
   * @param string $property Property name
   * @return string
   */
  public function __get($property){return $this->$property;}

  /**
   * Get User
   *
   * @return object
   */
  public function getUser(){return new cUser($this->fkUser);}

  /**
   * Get Event
   *
   * @return string Event name
   */
  public function getEvent(){return api_text($this->class."-event-".$this->event);}

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
   * Decode properties
   *
   * @return string
   */
  public function decodeProperties(){
   // definitions
   $return=null;
   // check for properties
   if(count($this->properties)){
    // check for decode function
    if(method_exists($this->class,"log_decode")){
     $return=call_user_func_array(array($this->class,"log_decode"),array($this->event,$this->properties));
    }
    // check for return
    if(!$return){$return=json_encode($this->properties);}
   }
   // return
   return $return;
  }

 }

?>