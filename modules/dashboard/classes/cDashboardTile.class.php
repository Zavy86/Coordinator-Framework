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
class cDashboardTile{

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
  * Debug
  *
  * @return object DashboardTile object
  */
 public function debug(){return $this;}

 /**
  * DashboardTile class
  *
  * @param integer $tile Dashboard Tile object or ID
  * @return boolean
  */
 public function __construct($tile){
  // get object
  if(is_numeric($tile)){$tile=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework_users_dashboards` WHERE `id`='".$tile."'",$GLOBALS['debug']);}
  if(!$tile->id){return FALSE;}
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
  $this->counter->count=NULL;
  $this->counter->class=NULL;
  return TRUE;
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