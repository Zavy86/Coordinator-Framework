<?php
/**
 * Attachment
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Attachment class
  */
 class cAttachment{

  /** Properties */
  protected $id;
  protected $name;
  protected $description;
  protected $typology;
  protected $size;
  protected $public;
  protected $downloads;
  protected $url;
  protected $addTimestamp;
  protected $addFkUser;
  protected $updTimestamp;
  protected $updFkUser;
  protected $deleted;

  /**
   * Attachment class
   *
   * @param mixed $attachment Attachment object or ID
   * @return boolean
   */
  public function __construct($attachment){
   // get object
   if(is_string($attachment)){$attachment=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__attachments` WHERE `id`='".$attachment."'",$GLOBALS['debug']);}
   if(!$attachment->id){return false;}
   // set properties
   $this->id=stripslashes($attachment->id);
   $this->name=stripslashes($attachment->name);
   $this->description=stripslashes($attachment->description);
   $this->typology=stripslashes($attachment->typology);
   $this->size=(int)$attachment->size;
   $this->public=(int)$attachment->public;
   $this->downloads=(int)$attachment->downloads;
   $this->addTimestamp=(int)$attachment->addTimestamp;
   $this->addFkUser=(int)$attachment->addFkUser;
   $this->updTimestamp=(int)$attachment->updTimestamp;
   $this->updFkUser=(int)$attachment->updFkUser;
   $this->deleted=(int)$attachment->deleted;
   // make link
   $this->url=URL."download.php?attachment=".$this->id;
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