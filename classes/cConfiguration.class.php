<?php
/**
 * Configuration
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Configuration class
 */
class cConfiguration{

	/** Properties */
	private $stored_fkUser;
	private $stored_timestamp;

	/**
	 * Configuration
	 *
	 * @return boolean
	 */
	public function __construct(){
		return $this->load();
	}

	/**
	 * Load from JSON
	 *
	 * @return bool
	 */
	private function load(){
		if(!$this->exists()){return false;}
		$configuration_datas=json_decode(file_get_contents(MODULE_PATH."configuration.json"),true);
		// cycle configuration datas
		foreach($configuration_datas as $property=>$value){
			// skip undefined properties
			if(!array_key_exists($property,get_object_vars($this))){continue;}
			// set property value
			$this->$property=$value;
		}
		return true;
	}

	/**
	 * Check for exists
	 *
	 * @return bool
	 */
	public function exists(){if(file_exists(MODULE_PATH."configuration.json")){return true;}else{return false;}}

	/**
	 * Store
	 *
	 * @param mixed[] $properties Array of properties
	 * @throws Exception
	 */
	public function store(array $properties){
		// build configuration
		$configuration=new stdClass();
		$configuration->stored_fkUser=$GLOBALS['session']->user->id;
		$configuration->stored_timestamp=time();
		// cycle all properties
		foreach($properties as $property=>$value){
			// skip undefined properties
			if(!array_key_exists($property,get_object_vars($this))){continue;}
			// overwrite property value
			$configuration->$property=$value;
		}
		// debug
		api_dump($configuration,static::class."->store configuration");
		// store configuration
		$return=file_put_contents(DIR."modules/chimneys/configuration.json",json_encode($configuration,JSON_PRETTY_PRINT));
		if(!$return){throw new Exception("An error occured whyle trying to store configuration..");}
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
	 * Get store label
	 *
	 * @return string|false
	 */
	public function getStoreLabel(){
		if(!$this->stored_fkUser || !$this->stored_timestamp){return false;}
		return (new cUser($this->stored_fkUser))->fullname." &rarr; ".api_timestamp_format($this->stored_timestamp,api_text("datetime"));
	}

}
