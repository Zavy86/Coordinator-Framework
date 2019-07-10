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
   * Convert Available
   *
   * @param boolean $showIcon Return icon
   * @param boolean $showText Return text
   * @param string $iconAlign Icon alignment [left|right]
   * @return string
   */
  protected static function convertAvailable($code,array $availables,$showIcon=true,$showText=true,$iconAlign="left"){
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
   * Object class
   *
   * @param mixed $object Object or ID
   * @return boolean
   */
  public function __construct($object=null){
   // check parameters
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
   foreach($events_results as $event){$events_array[$event->id]=new cLog($event,static::class);}
   // return
   return $events_array;
  }

           /**
            * Set Properties
            *
            * @param mixed[] $properties Array of properties
            */
           public function setProperties__deprecated(array $properties){
            // cycle all properties
            foreach($properties as $property=>$value){
             // skip undefined properties
             if(!array_key_exists($property,get_object_vars($this))){continue;}
             // set property value
             $this->$property=trim($value);
            }
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
           public function save_deprecated(){
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
             /* @todo check? */
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
   * Store
   *
   * @param mixed[] $properties Array of properties
   * @return boolean
   */
  public function store(array $properties){
   // cycle all properties
   foreach($properties as $property=>$value){
    // skip undefined properties
    if(!array_key_exists($property,get_object_vars($this))){continue;}
    // set property value
    $this->$property=trim($value);
   }
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
    api_dump($query_obj,static::class."->store update query object");
    // execute query
    $GLOBALS['database']->queryUpdate(static::$table,$query_obj);
    /* @todo check? */
    // throw event
    $this->event("information","updated");
    // return
    return true;
   }else{
    // insert object
    api_dump($query_obj,static::class."->store insert query object");
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
   * Status
   *
   * @param string $status New status code
   * @param string $note Event note
   * @return boolean
   */
  public function status($status,$note=null){
   // check for status property
   if(!array_key_exists("status",get_object_vars($this))){trigger_error("Status property does not exist in class: \"".static::class."\"",E_USER_ERROR);}
   // check parameters
   if(!$status){trigger_error("Status parameter cannot be null in class: \"".static::class."\"",E_USER_ERROR);}
   // check existence
   if(!$this->exists()){return false;}
   // get current status
   $previous_status=$this->status;
   // change current status
   $this->status=$status;
   // check properties
   if(!$this->check()){return false;}
   // build query object
   $query_obj=new stdClass();
   $query_obj->id=$this->id;
   $query_obj->status=$this->status;
   // debug
   api_dump($query_obj,static::class."->status query object");
   // execute query
   $GLOBALS['database']->queryUpdate(static::$table,$query_obj);
   /* @todo check? */
   // throw event
   $this->event("information","status",array("previous"=>$previous_status,"current"=>$this->status,"note"=>$note));
   // return
   return true;
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
   /* @todo check? */
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
   /* @todo check? */
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
   * Get Joined Objects
   *
   * @param string $table Join table name
   * @param string $this_key This class key in join table
   * @param string $object_class Joined object class
   * @param string $object_key Joined object key in join table
   * @param string $event Event to throw
   * @return object[]|false Array of joined objects or false
   */
  protected function joined_availables($table,$this_key,$object_class,$object_key,$event="joined_loaded"){
   // check parameters
   if(!$table || !$this_key || !$object_class || !$object_key){trigger_error("All parameters is mandatory in class: \"".static::class."\" ",E_USER_ERROR);}
   // definitions
   $return_array=array();
   // make query
   $query="SELECT * FROM `".$table."` WHERE `".$this_key."`='".$this->id."'";
   //api_dump($query,static::class."->get_joined_objects query");
   // fetch query results
   $results=$GLOBALS['database']->queryObjects($query);
   foreach($results as $result){$return_array[$result->$object_key]=new $object_class($result->$object_key);}
   // throw event
   $this->event("trace",$event,array("table"=>$table));
   // return
   return $return_array;
  }

  /**
   * Add Joined Object
   *
   * @param string $table Join table name
   * @param string $this_key This class key in join table
   * @param string $object_class Joined object class
   * @param string $object_key Joined object key in join table
   * @param object $object Object to add
   * @param string $event Event to throw
   * @return boolean
   */
  protected function joined_add($table,$this_key,$object_class,$object_key,$object,$event="joined_added"){
   // check parameters
   if(!$table || !$this_key || !$object_class || !$object_key || !$object->id){trigger_error("All parameters is mandatory in class: \"".static::class."\" ",E_USER_ERROR);}
   // check object class
   if(!is_a($object,$object_class)){trigger_error("Joined object class must be \"".$object_class."\" in class: \"".static::class."\" ",E_USER_ERROR);}
   // make query
   $query="INSERT IGNORE INTO `".$table."` (`".$this_key."`,`".$object_key."`) VALUES ('".$this->id."','".$object->id."')";
   api_dump($query,static::class."->addJoinedObject query");
   // execute query
   $result=$GLOBALS['database']->queryExecute($query);
   // check query result
   if(!$result){return false;}
   // throw event
   $this->event("information",$event,array("class"=>$object_class,"id"=>$object->id));
   // return
   return true;
  }

  /**
   * Remove Joined Object
   *
   * @param string $table Join table name
   * @param string $this_key This class key in join table
   * @param string $object_class Joined object class
   * @param string $object_key Joined object key in join table
   * @param object $object Object to remove
   * @param string $event Event to throw
   * @return boolean
   */
  protected function joined_remove($table,$this_key,$object_class,$object_key,$object,$event="joined_removed"){
   // check parameters
   if(!$table || !$this_key || !$object_class || !$object_key || !$object->id){trigger_error("All parameters is mandatory in class: \"".static::class."\" ",E_USER_ERROR);}
   // check object class
   if(!is_a($object,$object_class)){trigger_error("Joined object class must be \"".$object_class."\" in class: \"".static::class."\" ",E_USER_ERROR);}
   // make query
   $query="DELETE FROM `".$table."` WHERE `".$this_key."`='".$this->id."' AND `".$object_key."`='".$object->id."'";
   api_dump($query,static::class."->removeJoinedObject query");
   // execute query
   $result=$GLOBALS['database']->queryExecute($query);
   // check query result
   if(!$result){return false;}
   // throw event
   $this->event("warning",$event,array("class"=>$object_class,"id"=>$object->id));
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
  private function event_log($typology,$action,array $properties=null){
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
   $query_obj->properties_json=(count($properties)?json_encode($properties):null);
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