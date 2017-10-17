<?php
/**
 * Gauge
 *
 * Coordinator Structure Class Gauge
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * Gauge
 */
class cGauge{
 protected $id;
 protected $class;
 protected $style;
 protected $tags;
 /** options */
 public $options=array(
  "id"=>null,
  "value"=>50,
  "valueFontColor"=>"#666666",
  "title"=>"",
  "titlePosition"=>"above",
  "titleFontColor"=>"#333333",
  "label"=>"",
  "labelFontColor"=>"#666666",
  "symbol"=>"",
  "min"=>0,
  "max"=>100,
  "decimals"=>0,
  "reverse"=>false,
  "gaugeWidthScale"=>0.8,
  "gaugeColor"=>"#ffffff",
  "levelColors"=>array("#a9d70b","#f9c802","#ff0000"),
  "donut"=>false,
  "donutStartAngle"=>90,
  "pointer"=>true,
  "pointerOptions"=>array(
   "toplength"=>-18,
   "bottomlength"=>9,
   "bottomwidth"=>9,
   "color"=>"#666666",
   "stroke"=>"#ffffff",
   "stroke_width"=>3,
   "stroke_linecap"=>"round",
   "stroke_linecap"=>true
  ),
  "counter"=>true,
  "humanFriendly"=>true,
  "humanFriendlyDecimal"=>0,
  "hideValue"=>false,
  "hideMinMax"=>false,
  "noGradient"=>false
 );

 /**
  * Debug
  *
  * @return object Gauge object
  */
 public function debug(){return $this;}

 /**
  * Gauge
  *
  * @param string $class CSS class
  * @param string $style Custom CSS
  * @param string $tags Custom HTML tags
  * @param string $id Gauge ID
  * @return boolean
  */
 public function __construct($class=NULL,$style=NULL,$tags=NULL,$id=NULL){
  if($id){$this->id="gauge_".$id;}else{$this->id="gauge_".md5(rand(1,99999));}
  $this->class=$class;
  $this->style=$style;
  $this->tags=$tags;
  $this->options["id"]=$this->id;
  return TRUE;
 }

 /**
  * Get Gauge script
  *
  * @return string JavaScript source code
  */
 public function getScript(){
  // make gauge script
  $script="/* Gauge Script */\nvar ".$this->id."=new JustGage(".json_encode($this->options).");";
  // return html source code
  return $script;
 }

 /**
  * Renderize Gauge object
  *
  * @return string HTML source code
  */
 public function render(){
  // make gauge tags
  $gauge_tags=" id=\"".$this->id."\"";
  $gauge_tags.=" class=\"justgage ".$this->class."\"";
  if($this->style){$gauge_tags.=" style=\"".$this->style."\"";}
  if($this->tags){$gauge_tags.=$this->tags;}
  // renderize gauge
  $return="<!-- gauge -->\n";
  $return.="<div ".$gauge_tags.">\n";
  $return.="</div><!-- /gauge -->\n";
  // return html source code
  return $return;
 }

}
?>