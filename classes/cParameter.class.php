<?php
/**
 * Parameter
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Parameter class
  */
 class cParameter{

  /** Properties */
  protected $id;
  protected $fkUser;
  protected $parameter;
  protected $value;

  /**
   * Parameter class
   *
   * @param integer|object $parameter Parameter object or ID
   * @return boolean
   */
  public function __construct($parameter){
   // get object
   if(is_numeric($parameter)){$parameter=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__users__parameters` WHERE `id`='".$parameter."'",$GLOBALS['debug']);}
   if(!$parameter->id){return false;}
   // set properties
   $this->id=(int)$parameter->id;
   $this->fkUser=(int)$parameter->fkUser;
   $this->parameter=stripslashes($parameter->parameter);
   $this->value=stripslashes($parameter->value);
   // return
   return true;
  }

  /**
   * Get
   *
   * @param string $property Property name
   * @return string Property value
   */
  public function __get($property){return $this->$property;}

 }

?>