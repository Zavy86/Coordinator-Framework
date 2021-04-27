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

	/**
	 * Parameters
	 *
	 * @static string $table Database table name
	 * @static boolean $logs Store logs (need a $table__logs database table)
	 * @static boolean $sortable Record sortable (need a order property and field in database table)
	 * @static string[] $sortingGroup Array of grouping properties
	 */
	static protected $table=null;
	static protected $logs=false;
	static protected $sortable=false;
	static protected $sortingGroups=array();

	/** Properties */
	protected $id;
	protected $deleted;

	/**
	 * Check
	 * (Abstract function to be overridden)
	 * Executed automatically before saving
	 *
	 * @return booelan
	 */
	abstract protected function check();

	/**
	 * Decode log properties
	 *
	 * @param string $event Log event
	 * @param object $properties Event properties
	 * @return string decoded properties
	 */
	public static function log_decode($event,$properties){}

	/**
	 * Select
	 *
	 * @return object[] Array of available objects
	 */
	public static function select($where=null,$order=null,$limit=null){     /** @todo protected? */
		// check parameters
		if(!$where){$where="1";}
		if(!$order){$order="`id` ASC";}
		// definitions
		$return_array=array();
		// make query
		$query="SELECT * FROM `".static::$table."` WHERE ".$where." ORDER BY ".$order;
		if(strlen($limit)){$query.=" LIMIT ".$limit;}
		//api_dump($query,static::class."->select query");
		// fetch query results
		$results=$GLOBALS['database']->queryObjects($query);
		foreach($results as $result){$return_array[$result->id]=new static($result);}
		// return
		return $return_array;
	}

	/**
	 * Availables
	 *
	 * @param boolean $deleted Select also deleted objects
	 * @return object[] Array of available objects
	 */
	public static function availables($deleted=false,array $conditions=null,$limit=null){
		// definitions
		$query_where="1";
		// check for deleted
		if(!$deleted){$query_where.=" AND `deleted`='0'";}
		// cycle all conditions
		foreach($conditions as $field=>$value){
			$query_where.=" AND `".$field."`";
			// check for value array
			if(is_array($value)){
				$query_where.=" IN ('".implode("','",$value)."')";
			}else{
				$query_where.="='$value'";
			}
		}
		// debug
		//api_dump($query_where,"where");
		// return
		return static::select($query_where,null,$limit);
	}

	/**
	 * Count
	 *
	 * @param $deleted Count also deleted objects
	 * @return integer Objects count
	 */
	public static function count($deleted=false,array $conditions=null){
		// definitions
		$query_where="1";
		// check for deleted
		if(!$deleted){$query_where.=" AND `deleted`='0'";}
		// cycle all conditions
		foreach($conditions as $field=>$value){
			$query_where.=" AND `".$field."`";
			// check for value array
			if(is_array($value)){
				$query_where.=" IN ('".implode("','",$value)."')";
			}else{
				$query_where.="='$value'";
			}
		}
		// debug
		//api_dump($query_where,"where");
		// get result
		$results=(int)$GLOBALS['database']->queryCount(static::$table,$query_where);
		// return
		return $results;
	}

	/**
	 * Decode
	 *
	 * @param boolean $showIcon Return icon
	 * @param boolean $showText Return text
	 * @param string $iconAlign Icon alignment [left|right]
	 * @return string
	 */
	protected static function decode($code,array $availables,$showIcon=true,$showText=true,$iconAlign="left"){
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
	public function __get($property){
		if(!property_exists($this,$property)){return "{property_not_found|".$property."}";}  /** @todo verificare */
		return $this->$property;
	}

	/**
	 * Get Logs
	 *
	 * @param integer limit Limit number of events
	 * @return object[]|false Array of log objects or false
	 */
	public function getLogs($limit=null){
		// check parameters
		if(!static::$logs){trigger_error("Object events log is not enabled in class: \"".static::class."\"",E_USER_WARNING);return false;}
		// definitions
		$logs_array=array();
		// make query
		$query="SELECT * FROM `".static::$table."__logs` WHERE `fkObject`='".$this->id."' ORDER BY `timestamp` DESC, `id` DESC";
		if(is_integer($limit) && $limit>0){$query.=" LIMIT 0,".$limit;}
		//api_dump($query,static::class."->getLogs query");
		// get logs
		$logs_results=$GLOBALS['database']->queryObjects($query);
		foreach($logs_results as $log_fobj){$logs_array[$log_fobj->id]=new cLog($log_fobj,static::class);}
		// return
		return $logs_array;
	}

	/**
	 * Check if current object exist in database
	 */
	public function exists(){
		if(!$this->id){return false;}
		// make query
		//api_dump("SELECT COUNT(*) FROM `".static::$table."` WHERE `id`='".$this->id."'",static::class."->exists query");
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
		// check for object or try to load from id key
		if(!is_object($object) && strlen($object)){$object=$this->loadFromKey("id",$object);}
		// check object
		if(!$object->id){return false;}
		// cycle object properties
		foreach(get_object_vars($object) as $property=>$value){
			// skip undefined properties
			if(!array_key_exists($property,get_object_vars($this))){continue;}
			// set property value
			$this->$property=trim($value);
		}
		// throw event
		$this->event("trace","loaded");
		// return
		return true;
	}

	/**
	 * Load from Key
	 *
	 * @param string $key Unique index key name
	 * @param string $value Key value
	 * @return boolean
	 */
	public function loadFromKey($key,$value){
		// checks
		if(!$key || !$value){return false;}
		if($this->id){return false;}
		// make query
		$query="SELECT * FROM `".static::$table."` WHERE `".$key."`='".$value."'";
		//api_dump($query,static::class."->loadFromKey query");
		// get object from database
		$object=$GLOBALS['database']->queryUniqueObject($query);
		// call parent load
		return $this->load($object);
	}

	/**
	 * Load from Fields
	 *
	 * @param array $fields Array of fields and values ( ["field_1"=>"value","field_2"=>"value"] )
	 * @return boolean
	 */
	public function loadFromFields($fields){
		// checks
		if(!is_array($fields)){return false;}
		if(!count($fields)){return false;}
		if($this->id){return false;}
		// make query
		$query="SELECT * FROM `".static::$table."` WHERE 1";
		// cycle all fields and add to query
		foreach($fields as $field=>$value){$query.=" AND `".$field."`='".$value."'";}
		// debug
		//api_dump($query,static::class."->loadFromFields query");
		// get object from database
		$object=$GLOBALS['database']->queryUniqueObject($query);
		// call parent load
		return $this->load($object);
	}

	/**
	 * Store
	 *
	 * @param mixed[] $properties Array of properties
	 * @param boolean $log Log event
	 * @return boolean
	 */
	public function store(array $properties,$log=true){
		// definitions
		$event_properties_array=array();
		// cycle all properties
		foreach($properties as $property=>$value){
			// skip undefined properties
			if(!array_key_exists($property,get_object_vars($this))){continue;}
			// check for change
			if($this->$property!==trim($value)){
				// save previous and current value for event
				$event_properties_array[$property]=array("previous"=>$this->$property,"current"=>trim($value));
				// overwrite property value
				$this->$property=trim($value);
			}
		}
		// check properties
		if(!$this->check()){return false;}
		// build query object
		$query_obj=new stdClass();
		// cycle all properties
		foreach(get_object_vars($this) as $property=>$value){
			// skip deleted properties
			if($property=="deleted"){continue;}
			// get property value
			$query_obj->$property=$value;
		}
		// check existence
		if($this->exists()){
			// update object
			api_dump($query_obj,static::class."->store update query object");
			// execute query
			$GLOBALS['database']->queryUpdate(static::$table,$query_obj);
			/* @todo check? */
			// throw event
			$this->event("information","updated",$event_properties_array,$log);
			// return
			return true;
		}else{
			// check for sortable
			if(static::$sortable){
				// check if order property exists
				if(!property_exists(static::class,"order")){throw new Exception("Sortable class need a \"order\" property..");}
				// make sorting where conditions
				$max_order_query="SELECT COALESCE(MAX(`order`),'0')+'1' AS `order` FROM `".static::$table."` WHERE 1";
				// cycle all order grouping properties
				foreach(static::$sortingGroups as $property_f){$max_order_query.=" AND `".$property_f."`='".$this->$property_f."'";}
				// debug
				//api_dump($max_order_query);
				// get maximum order
				$query_obj->order=(int)$GLOBALS['database']->queryUniqueValue($max_order_query,false);
				// check for value
				if(!$query_obj->order){throw new Exception("An error occured whyle trying to get the maximum order value..");}
			}
			// insert object
			api_dump($query_obj,static::class."->store insert query object");
			// execute query
			$this->id=$GLOBALS['database']->queryInsert(static::$table,$query_obj);
			// check
			if(!$this->id){return false;}
			// throw event
			$this->event("information","created",null,$log);
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
	 * @param mixed[] $additional_parameters Array of additional parameters
	 * @param boolean $log Log event
	 * @return boolean
	 */
	public function status($status,array $additional_parameters=null,$log=true){
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
		// make event properties
		$event_properties_array=array_merge(["previous"=>$previous_status,"current"=>$this->status],$additional_parameters);
		// throw event
		$this->event("information","status",$event_properties_array,$log);
		// return
		return true;
	}

	/**
	 * Delete
	 *
	 * @param boolean $log Log event
	 * @return boolean
	 */
	public function delete($log=true){
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
		$this->event("warning","deleted",null,$log);
		// return
		return true;
	}

	/**
	 * Undelete
	 *
	 * @param boolean $log Log event
	 * @return boolean
	 */
	public function undelete($log=true){
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
		$this->event("warning","undeleted",null,$log);
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
	 * Check if object is sortable
	 *
	 * @return boolean
	 * @throws Exception
	 */
	public function sortable(){
		// check for sortable parameter
		if(!static::$sortable){return false;}
		// check if order property exists
		if(!property_exists(static::class,"order")){throw new Exception("Sortable objects need a \"order\" property..");}
		// return
		return true;
	}

	/**
	 * Move
	 *
	 * @param string $direction Order move direction ( up | down )
	 * @param boolean $log Log event
	 * @return boolean
	 * @throws Exception
	 */
	public function move($direction,$log=true){
		// checks parameters
		if(!in_array(strtolower($direction),array("up","down"))){throw new Exception("Move direction not defined..");}
		// check for sortable
		if(!$this->sortable()){throw new Exception(static::class." is unsortable");}
		// check for exists
		if(!$this->exists()){throw new Exception("Unsaved object cannot be moved..");}
		// build query object
		$query_obj=new stdClass();
		$query_obj->id=$this->id;
		// switch order move direction
		switch(strtolower($direction)){
			// up -> order -1
			case "up":
				// set previous order
				$query_obj->order=($this->order-1);
				// check for order
				if($query_obj->order<1){throw new Exception("Order cannot be less than one..");}
				// update object
				api_dump($query_obj,static::class."->move update query object");
				// execute query
				$GLOBALS['database']->queryUpdate(static::$table,$query_obj);
				// rebase other objects
				$rebase_query="UPDATE `".static::$table."` SET `order`=`order`+'1' WHERE `order`<'".$this->order."' AND `order`>='".$query_obj->order."' AND `order`<>'0' AND `id`!='".$this->id."'";
				// cycle all order grouping properties
				foreach(static::$sortingGroups as $property_f){$rebase_query.=" AND `".$property_f."`='".$this->$property_f."'";}
				// debug
				api_dump($rebase_query);
				// execute query
				$GLOBALS['database']->queryExecute($rebase_query);
				break;
			// down -> order +1
			case "down":
				// set following order
				$query_obj->order=($this->order+1);
				// update object
				api_dump($query_obj,static::class."->move update query object");
				// execute query
				$GLOBALS['database']->queryUpdate(static::$table,$query_obj);
				// rebase other objects
				$rebase_query="UPDATE `".static::$table."` SET `order`=`order`-'1' WHERE `order`>'".$this->order."' AND `order`<='".$query_obj->order."' AND `order`<>'0' AND `id`!='".$this->id."'";
				// cycle all order grouping properties
				foreach(static::$sortingGroups as $property_f){$rebase_query.=" AND `".$property_f."`='".$this->$property_f."'";}
				// debug
				api_dump($rebase_query);
				// execute query
				$GLOBALS['database']->queryExecute($rebase_query);
				break;
		}
		// throw event
		$this->event("trace","moved"); /** @todo verificare se trace o informations */
		// return
		return true;
	}

	/**
	 * Count Joined Objects
	 *
	 * @param string $table Join table name
	 * @param string $this_key This class key in join table
	 * @return object[]|false Array of joined objects or false
	 */
	protected function joined_count($table,$this_key){
		// check parameters
		if(!$table || !$this_key){trigger_error("All parameters is mandatory in class: \"".static::class."\" ",E_USER_ERROR);}
		// make query
		$query="SELECT COUNT(*) FROM `".$table."` WHERE `".$this_key."`='".$this->id."'";
		//api_dump($query,static::class."->joined_count query");
		// fetch query result
		$result=intval($GLOBALS['database']->queryUniqueValue($query));
		// return
		return $result;
	}

	/**
	 * Get Joined Objects
	 *
	 * @param string $table Join table name
	 * @param string $this_key This class key in join table
	 * @param string $object_class Joined object class
	 * @param string $object_key Joined object key in join table
	 * @param string $event Event to throw
	 * @param boolean $log Log event
	 * @return object[]|false Array of joined objects or false
	 */
	protected function joined_select($table,$this_key,$object_class,$object_key,$event="joined_select",$log=true){
		// check parameters
		if(!$table || !$this_key || !$object_class || !$object_key){trigger_error("All parameters is mandatory in class: \"".static::class."\" ",E_USER_ERROR);}
		// definitions
		$return_array=array();
		// make query
		$query="SELECT * FROM `".$table."` WHERE `".$this_key."`='".$this->id."'";
		//api_dump($query,static::class."->joined_select query");
		// fetch query results
		$results=$GLOBALS['database']->queryObjects($query);
		foreach($results as $result){$return_array[$result->$object_key]=new $object_class($result->$object_key);}
		// throw event
		$this->event("trace",$event,["table"=>$table],$log);
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
	 * @param boolean $log Log event
	 * @return boolean
	 */
	protected function joined_add($table,$this_key,$object_class,$object_key,$object,$event="joined_added",$log=true){
		// check parameters
		if(!$table || !$this_key || !$object_class || !$object_key || !$object->id){trigger_error("All parameters is mandatory in class: \"".static::class."\" ",E_USER_ERROR);}
		// check object class
		if(!is_a($object,$object_class)){trigger_error("Joined object class must be \"".$object_class."\" in class: \"".static::class."\" ",E_USER_ERROR);}
		// make query
		$query="INSERT IGNORE INTO `".$table."` (`".$this_key."`,`".$object_key."`) VALUES ('".$this->id."','".$object->id."')";
		api_dump($query,static::class."->joined_add query");
		// execute query
		$result=$GLOBALS['database']->queryExecute($query);
		// check query result
		if(!$result){return false;}
		// throw event
		$this->event("information",$event,["_obj"=>$object_class,"_id"=>$object->id],$log);
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
	 * @param boolean $log Log event
	 * @return boolean
	 */
	protected function joined_remove($table,$this_key,$object_class,$object_key,$object,$event="joined_removed",$log=true){
		// check parameters
		if(!$table || !$this_key || !$object_class || !$object_key || !$object->id){trigger_error("All parameters is mandatory in class: \"".static::class."\" ",E_USER_ERROR);}
		// check object class
		if(!is_a($object,$object_class)){trigger_error("Joined object class must be \"".$object_class."\" in class: \"".static::class."\" ",E_USER_ERROR);}
		// make query
		$query="DELETE FROM `".$table."` WHERE `".$this_key."`='".$this->id."' AND `".$object_key."`='".$object->id."'";
		api_dump($query,static::class."->joined_remove query");
		// execute query
		$result=$GLOBALS['database']->queryExecute($query);
		// check query result
		if(!$result){return false;}
		// throw event
		$this->event("warning",$event,["_obj"=>$object_class,"_id"=>$object->id],$log);
		// return
		return true;
	}

	/**
	 * Reset Joined Object
	 *
	 * @param string $table Join table name
	 * @param string $this_key This class key in join table
	 * @param string $event Event to throw
	 * @param boolean $log Log event
	 * @return boolean
	 */
	protected function joined_reset($table,$this_key,$event="joined_resetted",$log=true){
		// check parameters
		if(!$table || !$this_key){trigger_error("All parameters is mandatory in class: \"".static::class."\" ",E_USER_ERROR);}
		// make query
		$query="DELETE FROM `".$table."` WHERE `".$this_key."`='".$this->id."'";
		api_dump($query,static::class."->joined_reset query");
		// execute query
		$result=$GLOBALS['database']->queryExecute($query);
		// check query result
		if(!$result){return false;}
		// throw event
		$this->event("warning",$event,null,$log);
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
	 * @param boolean $log Log event
	 * @return boolean
	 */
	public function event($typology,$action,array $properties=null,$log=true){
		// check parameters
		if(!in_array($typology,array("trace","information","warning"))){trigger_error("Event typology \"".$typology."\" was not defined",E_USER_ERROR);return false;}
		// build event object
		$event_obj=new stdClass();
		$event_obj->typology=$typology;
		$event_obj->action=$action;
		$event_obj->properties=(array)$properties;
		// check logs parameter, typology and action
		if(static::$logs && $log && $typology!="trace" && $action!="removed"){
			// log event to database
			$this->event_log($typology,$action,$properties);
		}
		// triggers an event
		$this->event_triggered($event_obj);
	}

	/**
	 * Log Event
	 *
	 * @return boolean
	 */
	public function event_log($typology,$action,array $properties=null){
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