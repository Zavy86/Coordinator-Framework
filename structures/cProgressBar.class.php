<?php
/**
 * Progress Bar
 *
 * Coordinator Structure Class ProgressBar
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Progress Bar class
  */
 class cProgressBar{

  /** Properties */
  protected $id;
  protected $class;
  protected $style;
  protected $tags;
  protected $elements_array;

  /**
   * Progress Bar class
   *
   * @param string $class CSS class
   * @param string $style Custom CSS
   * @param string $tags Custom HTML tags
   * @param string $id ProgressBar ID
   * @return boolean
   */
  public function __construct($class=null,$style=null,$tags=null,$id=null){
   if($id){$this->id="progressBar_".$id;}else{$this->id="progressBar_".md5(rand(1,99999));}
   $this->class=$class;
   $this->style=$style;
   $this->tags=$tags;
   $this->elements_array=array();
   return true;
  }

  /**
   * Add Element
   *
   * @param string $percentage Percentage
   * @param string $content Content
   * @param string $class CSS class
   * @param string $style Custom CSS
   * @param string $tags Custom HTML tags
   * @return boolean
   */
  public function addElement($percentage,$content,$class=null,$style=null,$tags=null){
   if(!is_numeric($percentage)>0){return false;}
   $element=new stdClass();
   $element->percentage=$percentage;
   $element->content=$content;
   $element->class=$class;
   $element->style=$style;
   $element->tags=$tags;
   // add element to elements array
   $this->elements_array[]=$element;
   return true;
  }

  /**
   * Renderize ProgressBar object
   *
   * @return string HTML source code
   */
  public function render(){
   // check for elements
   if(!count($this->elements_array)){return null;}
   // make progressBar tags
   $progressBar_tags=" id=\"".$this->id."\"";
   $progressBar_tags.=" class=\"progress ".$this->class."\"";
   if($this->style){$progressBar_tags.=" style=\"".$this->style."\"";}
   if($this->tags){$progressBar_tags.=$this->tags;}
   // renderize description progressBar
   $return="<!-- progressBar -->\n";
   $return.="<div".$progressBar_tags.">\n";
   foreach($this->elements_array as $element){
    // make element tags
    $element_tags=null;
    $element_tags.=" class=\"progress-bar ".$element->class."\"";
    $element_tags.=" style=\"width:".$element->percentage."%;".$element->style."\"";
    if($element->tags){$element_tags.=" ".$element->tags;}
    // add item to progressBar
    $return.=" <div".$element_tags.">".$element->content."</div>\n";
   }
   $return.="</div><!-- /progressBar -->\n";
   // return html source code
   return $return;
  }

 }

?>