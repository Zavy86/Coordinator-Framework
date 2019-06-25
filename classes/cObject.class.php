<?php
/**
 * Object
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Object class
  */
 abstract class cObject{

  /** Parameters */
  static protected $module=null;
  static protected $object=null;
  static protected $table=null;
  static protected $logs=false;

  /** Properties */
  protected $id;
  protected $deleted;

  /**
   * Check
   * Abstract function to be overridden (executed before saving or manually)
   *
   * @return booelan
   */
  abstract protected function check();

  /**
   * Availables
   *
   * @return object[] Array of available objects
   */
  public static function availables($where=null,$order=null,$limit=null){
   // check parameters
   if(!$where){$where="1";}
   if(!$order){$order="`id` ASC";}
   // definitions
   $return_array=array();
   // make query
   $query="SELECT * FROM `".static::$table."` WHERE ".$where." ORDER BY ".$order;
   if(strlen($limit)){$query.=" LIMIT ".$limit;}
   //api_dump($query,static::class."->availables query");
   // fetch query results
   $results=$GLOBALS['database']->queryObjects($query);
   foreach($results as $result){$return_array[$result->id]=new static($result);}
   // return
   return $return_array;
  }

  /**
   * Object class
   *
   * @param mixed $object Object or ID
   * @return boolean
   */
  public function __construct($object=null){
   // check parameters
   if(!static::$object){trigger_error("Object was not defined in class: \"".static::class."\"",E_USER_ERROR);}
   if(!static::$module){trigger_error("Object module was not defined in class: \"".static::class."\"",E_USER_ERROR);}
   if(!static::$table){trigger_error("Object database table was not defined in class: \"".static::class."\"",E_USER_ERROR);}
   // load object
   if($object){$this->load($object);}
  }

  /**
   * Get
   *
   * @param string $property Property name
   * @return string Property value
   */
  public function __get($property){return $this->$property;}

  /**
   * Get Logs
   *
   * @param integer limit Limit number of events
   * @return object[]|false Array of event objects or false
   */
  public function getLogs($limit=null){
   // check parameters
   if(!static::$logs){trigger_error("Object events log is not enabled in class: \"".static::class."\"",E_USER_WARNING);return false;}
   // definitions
   $events_array=array();
   // make query
   $query="SELECT * FROM `".static::$table."__logs` WHERE `fkObject`='".$this->id."' ORDER BY `timestamp` DESC";
   if(is_integer($limit) && $limit>0){$query.=" LIMIT 0,".$limit;}
   //api_dump($query,static::class."->getLogs query");
   // get customer events
   $events_results=$GLOBALS['database']->queryObjects($query);
   foreach($events_results as $event){$events_array[$event->id]=new cLog($event,static::$module,static::$object);}
   // return
   return $events_array;
  }

  /**
   * Set Properties
   *
   * @param mixed[] $properties Array of properties
   */
  public function setProperties(array $properties){
   // cycle all properties
   foreach($properties as $property=>$value){
    // skip undefined properties
    if(!array_key_exists($property,get_object_vars($this))){continue;}
    // set property value
    $this->$property=trim($value);
   }
  }

  /**
   * Convert Available
   *
   * @param boolean $showIcon Return icon
   * @param boolean $showText Return text
   * @param string $iconAlign Icon alignment [left|right]
   * @return string
   */
  protected function convertAvailable($code,array $availables,$showIcon=true,$showText=true,$iconAlign="left"){
   // check parameters
   if(!array_key_exists($code,$availables)){return false;}
   if(!in_array($iconAlign,array("left","right"))){$iconAlign="left";}
   // get from availables by code
   $result=$availables[$code];
   // make return
   if($showIcon){$return=$result->icon;}
   if($showText){$return=$result->text;}
   if($showIcon && $showText){
    if($iconAlign=="left"){$return=$result->icon." ".$result->text;}
    elseif($iconAlign=="right"){$return=$result->text." ".$result->icon;}
   }
   // return
   return $return;
  }

  /**
   * Check if current object exist in database
   */
  public function exists(){
   // make query
   $query="SELECT COUNT(*) FROM `".static::$table."` WHERE `id`='".$this->id."'";
   //api_dump($query,static::class."->exists query");
   // count row with current key
   $count=$GLOBALS['database']->queryCount(static::$table,"id='".$this->id."'");
   // make exists
   $exists=($count>0?true:false);
   // throw event
   $this->event("trace","exists",array("exists"=>$exists));
   // return
   return $exists;
  }

  /**
   * Load
   *
   * @return boolean
   */
  public function load($object){
   // check parameters
   if(!is_object($object) && strlen($object)){
    // make query
    $query="SELECT * FROM `".static::$table."` WHERE `id`='".$object."'";
    //api_dump($query,static::class."->load query");
    // get object from database
    $object=$GLOBALS['database']->queryUniqueObject($query);
   }
   // check object
   if(!$object->id){
    /* thrown? */
    return false;
   }
   // set properties
   $this->setProperties((array)$object);
   // throw event
   $this->event("trace","loaded");
   // return
   return true;
  }

  /**
   * Save
   *
   * @return boolean
   */
  public function save(){
   // build query object
   $query_obj=new stdClass();

   //--- @todo rifare meglio
   foreach(get_object_vars($this) as $property=>$value){
    if($property=="deleted"){continue;}
    $query_obj->$property=$value;
   } //---

   // check properties
   if(!$this->check()){return false;}
   // check existence
   if($this->exists()){
    // update object
    api_dump($query_obj,static::class." update query object");
    // execute query
    $GLOBALS['database']->queryUpdate(static::$table,$query_obj);
    /* check? */
    // throw event
    $this->event("information","updated");
    // return
    return true;
   }else{
    // insert object
    api_dump($query_obj,static::class." insert query object");
    // execute query
    $this->id=$GLOBALS['database']->queryInsert(static::$table,$query_obj);
    // check
    if(!$this->id){return false;}
    // throw event
    $this->event("information","created");
    // return
    return true;
   }
   // return
   return false;
  }

  /**
   * Delete
   *
   * @return boolean
   */
  public function delete(){
   // check existence
   if(!$this->exists()){return false;}
   // build query object
   $query_obj=new stdClass();
   $query_obj->id=$this->id;
   $query_obj->deleted=1;
   // debug
   api_dump($query_obj,static::class."->delete query object");
   // execute query
   $GLOBALS['database']->queryUpdate(static::$table,$query_obj);
   /* check? */
   // throw event
   $this->event("warning","deleted");
   // return
   return true;
  }

  /**
   * Undelete
   *
   * @return boolean
   */
  public function undelete(){
   // check existence
   if(!$this->exists()){return false;}
   // build query object
   $query_obj=new stdClass();
   $query_obj->id=$this->id;
   $query_obj->deleted=0;
   // debug
   api_dump($query_obj,static::class."->undelete query object");
   // execute query
   $GLOBALS['database']->queryUpdate(static::$table,$query_obj);
   /* check? */
   // throw event
   $this->event("warning","undeleted");
   // return
   return true;
  }

  /**
   * Remove
   *
   * @return boolean
   */
  public function remove(){
   // check existence
   if(!$this->exists()){return false;}
   // debug
   api_dump("DELETE FROM `".static::$table."` WHERE `id`='".$this->id."'",static::class."->remove query");
   // remove from database
   $GLOBALS['database']->queryDelete(static::$table,$this->id);
   // throw event
   $this->event("warning","removed");
   // return
   return true;
  }

  /**
   * Event
   * Trigger an event and log into database
   *
   * @param string $typology Event typology (traces will not be logged) [trace|information|warning]
   * @param string $action Action occurred
   * @param mixed[] $properties Array of additional properties (key=>value)
   * @return boolean
   */
  public function event($typology,$action,array $properties=null){
   // check parameters
   if(!in_array($typology,array("trace","information","warning"))){trigger_error("Event typology \"".$typology."\" was not defined",E_USER_ERROR);return false;}
   // build event object
   $event_obj=new stdClass();
   $event_obj->typology=$typology;
   $event_obj->action=$action;
   $event_obj->properties=$properties;
   // triggers an event
   $this->event_triggered($event_obj);
   // check logs parameter, typology and action
   if(static::$logs && $typology!="trace" && $action!="removed"){
    // log event to database
    $this->event_log($typology,$action,$properties);
   }
  }

  /**
   * Log Event
   *
   * @return boolean
   */
  private function event_log($typology,$action,$properties=null){
   // check parameters
   if(!static::$logs){trigger_error("Object events logs in not enabled in class: \"".static::class."\"",E_USER_WARNING);return false;}
   if(!in_array($typology,array("information","warning"))){trigger_error("Event typology \"".$typology."\" will not be logged",E_USER_ERROR);return false;}
   // ^ in teoria non serve
   if(!$action){trigger_error("Event action is mandatory",E_USER_ERROR);return false;}
   // build query object
   $query_obj=new stdClass();
   $query_obj->fkObject=$this->id;
   $query_obj->fkUser=$GLOBALS['session']->user->id;
   $query_obj->timestamp=time();
   $query_obj->alert=($typology=="warning"?1:0);
   $query_obj->event=$action;
   $query_obj->properties_json=json_encode($properties);
   // event object
   api_dump($query_obj,static::class." event query object");
   // execute query
   $event_id=$GLOBALS['database']->queryInsert(static::$table."__logs",$query_obj); /** @todo decidere se tenere __logs oppure _logs */
   // return
   if($event_id){return true;}
   // return
   return false;
  }

  /**
   * Event trigger (for overrides)
   *
   * @param object $event Event object
   */
  protected function event_triggered($event){}

 }

?>