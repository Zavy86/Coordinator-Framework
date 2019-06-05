<?php
/**
 * Dashboard - Tile
 *
 * @package Coordinator\Modules\Dashboard\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Dashboard Tile class
  */
 class strDashboardTile{

  /** Properties */
  protected $id;
  protected $fkUser;
  protected $order;
  protected $icon;
  protected $label;
  protected $description;
  protected $size;
  protected $url;
  protected $module;
  protected $target;
  protected $counter;
  protected $counter_function;

  /**
   * Dashboard Tile class
   *
   * @param integer $tile Dashboard Tile object or ID
   * @return boolean
   */
  public function __construct($tile){
   // get object
   if(is_numeric($tile)){$tile=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__users__dashboards` WHERE `id`='".$tile."'");}
   if(!$tile->id){return false;}
   // set properties
   $this->id=(int)$tile->id;
   $this->fkUser=(int)$tile->fkUser;
   $this->order=(int)$tile->order;
   $this->icon=stripslashes($tile->icon);
   $this->label=stripslashes($tile->label);
   $this->description=stripslashes($tile->description);
   $this->size=stripslashes($tile->size);
   $this->url=stripslashes($tile->url);
   $this->module=stripslashes($tile->module);
   $this->target=stripslashes($tile->target);
   $this->counter_function=stripslashes($tile->counter_function);
   // make background
   if(file_exists(ROOT."uploads/dashboard/".$this->id.".jpg")){$this->background="uploads/dashboard/".$this->id.".jpg";}
   // make counter
   /** @todo get from counter_function */
   $this->counter=new stdClass();
   $this->counter->count=null;
   $this->counter->class=null;
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