<?php
/**
 * Description List
 *
 * Coordinator Structure Class for Description Lists
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Description List structure class
  */
 class strDescriptionList{

  /** Properties */
  protected $separator;
  protected $class;
  protected $elements_array;

  /**
   * Description List structure class
   *
   * @param string $separator Default elements separator ( null | hr | br )
   * @param string $class CSS class
   * @return boolean
   */
  public function __construct($separator=null,$class=null){
   if(!in_array(strtolower($separator),array(null,"hr","br"))){return false;}
   $this->class=$class;
   $this->separator=$separator;
   $this->elements_array=array();
   return true;
  }

  /**
   * Add Element
   *
   * @param string $label Label
   * @param string $content Content
   * @param string $separator Element Separator ( default | null | hr | br )
   * @param string $class CSS class
   * @return boolean
   */
  public function addElement($label,$content,$separator="default",$class=null){
   if(!in_array(strtolower($separator),array(null,"default","hr","br"))){return false;}
   if($separator=="default"){$separator=$this->separator;}
   if(!strlen($content)>0){$content="&nbsp;";}
   $element=new stdClass();
   $element->type="element";
   $element->label=$label;
   $element->content=$content;
   $element->separator=$separator;
   $element->class=$class;
   // add element to elements array
   $this->elements_array[]=$element;
   return true;
  }

  /**
   * Add Separator
   *
   * @todo verificare a che cosa serve... :/
   *
   * @param string $separator Separator ( default | hr | br )
   * @param string $class CSS class
   * @return boolean
   */
  public function addSeparator($separator="default",$class=null){
   if(!in_array(strtolower($separator),array("default","hr","br"))){return false;}
   if($separator=="default"){$separator=$this->separator;}
   $element=new stdClass();
   $element->type="separator";
   $element->separator=$separator;
   $element->class=$class;
   $this->elements_array[]=$element;
   return true;
  }

  /**
   * Renderize DescriptionList object
   *
   * @return string HTML source code
   */
  public function render(){
   // check for elements
   if(!count($this->elements_array)){return null;}
   // renderize description list
   $return="<!-- description-list -->\n";
   $return.="<dl class=\"".$this->class."\">\n";
   foreach($this->elements_array as $index=>$element){
    switch($element->type){
     case "element":
      $return.=" <dt class='".$element->class."'>".$element->label."</dt><dd class='".$element->class."'>".$element->content."</dd>";
      if($element->separator<>null && $this->elements_array[$index+1]->type=="element"){$return.="<".$element->separator.">\n";}else{$return.="\n";}
      break;
     case "separator":
      $return.=" <".$element->separator.">\n";
      break;
    }
   }
   $return.="</dl><!-- /description-list -->\n";
   // return html source code
   return $return;
  }

 }

?>