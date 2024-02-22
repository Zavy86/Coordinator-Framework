<?php
/**
 * Transcoding
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Transcoding class
 */
abstract class cTranscoding{

	/** Properties */
	protected $code;
	protected $text;
	protected $icon;
	protected $color;

	/**
	 * Transcoding datas
	 *
	 * Example:
	 * return array(
	 *     ["code","text","icon","color"],
	 *     ["fa","Font Awesome","fa-font-awesome","#286090"]
	 * );
	 *
	 * @return array
	 */
	protected static function datas(){
		return array(
		 ["code","text","icon","color"]
		);
	}

	/**
	 * Availables
	 *
	 * @return object[] Array of available transcodings
	 */
	public static function availables(){
		// definitions
		$return_array=array();
		// cycle all datas
		foreach(static::datas() as $data){
			// build new self
			$transcoding_obj=new static();
			$transcoding_obj->build($data[0],$data[1],$data[2],$data[3]);
			// check object properties
			if(!$transcoding_obj->code || !$transcoding_obj->text){continue;}
			// add object to array
			$return_array[$transcoding_obj->code]=$transcoding_obj;
		}
		// return
		return $return_array;
	}

	/**
	 * Transcoding class
	 *
	 * @param mixed $transcoding Transcoding object or code
	 * @return boolean
	 */
	public function __construct($transcoding=null){
		// check for object or try to load from code
		if(!is_object($transcoding) && strlen($transcoding??'')){$transcoding=$this->load($transcoding);}
		// check object and properties
		if(!$transcoding->code){return false;}
		if(!$transcoding->text){return false;}
		// set properties
		$this->code=$transcoding->code;
		$this->text=$transcoding->text;
		$this->icon=$transcoding->icon;
		$this->color=$transcoding->color;
		// return
		return true;
	}

	/**
	 * Get
	 *
	 * @param string $property Property name
	 * @return string Property value
	 */
	public function __get($property){
		if(!property_exists($this,$property)){return "{property_not_found|".$property."}";}
		return $this->$property;
	}

	/**
	 * @param boolean $text Show text
	 * @param boolean $icon Show icon
	 * @param string $position Icon position ( left | right )
	 * @param boolean $code Show code
	 * @param string $separator Separator between code and text
	 * @return string
	 */
	public function getLabel($text=true,$icon=true,$position="left",$code=false,$separator=" - "){
		// check parameters
		if(!in_array($position,array("left","right"))){$position="left";}
		// make label
		if($text && $code){$label=$this->code.$separator.$this->text;}
		elseif($text){$label=$this->text;}
		elseif($code){$label=$this->code;}
		else{$label=null;}
		// add icon
		if($icon && $this->icon){
			if($position=="right"){$label.=" ".api_icon($this->icon,$this->text);}
			else{$label=api_icon($this->icon,$this->text)." ".$label;}
		}
		// return
		return trim($label);
	}

	/**
	 * Build
	 *
	 * @param string $code Transcoding code
	 * @param string $text Textual description
	 * @param string $icon Optional icon
	 * @return boolean
	 */
	private function build($code,$text,$icon=null,$color=null){
		// check parameters
		if(!$code){return false;}
		if(!$text){return false;}
		// set properties
		$this->code=$code;
		$this->text=$text;
		$this->icon=$icon;
		$this->color=($color?:"#333333");
		// return
		return true;
	}

	/**
	 * Load
	 *
	 * @param string $code Transcoding code
	 * @return boolean
	 */
	private function load($code){
		// check parameters
		if(!$code){return false;}
		// get from availables
		$available_obj=static::availables()[$code];
		// check for code
		if($available_obj->code!=$code){return false;}
		// build transcoding object
		$this->build($available_obj->code,$available_obj->text,$available_obj->icon);
		// return
		return true;
	}

}
