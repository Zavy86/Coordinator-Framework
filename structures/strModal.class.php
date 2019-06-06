<?php
/**
 * Modal
 *
 * Coordinator Structure Class for Modals
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Modal structure class
  */
 class strModal{

  /** Properties */
  protected $id;
  protected $title;
  protected $class;
  protected $header;
  protected $body;
  protected $footer;
  protected $size;

  /**
   * Modal structure class
   *
   * @param string $title Title
   * @param string $class CSS class
   * @param string $id Modal window ID, if null randomly generated
   * @return boolean
   */
  public function __construct($title=null,$class=null,$id=null){
   $this->id="modal_".($id?$id:api_random());
   $this->title=$title;
   $this->class=$class;
   $this->size="normal";
   return true;
  }

  /**
   * Get
   *
   * @param string $property Property name
   * @return string Property value
   */
  public function __get($property){return $this->$property;}

  /**
   * Set Title
   *
   * @param string $title Modal window title
   * @return boolean
   */
  public function setTitle($title){
   if(!$title){return false;}
   $this->title=$title;
   return true;
  }

  /**
   * Set Header
   *
   * @param string $content Content of the header
   * @return boolean
   */
  public function setHeader($content){
   if(!$content){return false;}
   $this->header=$content;
   return true;
  }

  /**
   * Set Body
   *
   * @param string $content Content of the body
   * @return boolean
   */
  public function SetBody($content){
   if(!$content){return false;}
   $this->body=$content;
   return true;
  }

  /**
   * Set Footer
   *
   * @param string $content Content of the footer
   * @return boolean
   */
  public function SetFooter($content){
   if(!$content){return false;}
   $this->footer=$content;
   return true;
  }

  /**
   * Set Size
   *
   * @param string $size Modal size [normal,small,large]
   * @return boolean
   */
  public function setSize($size){
   if(!in_array($size,array("normal","small","large"))){return false;}
   $this->size=strtolower($size);
   return true;
  }

  /**
   * Link
   * @param string $label Label
   * @param string $title Title
   * @param string $class CSS class
   * @param string $confirm Show confirm alert box
   * @param string $style Style tags
   * @param string $tags Custom HTML tags
   * @return string Link HTML source code
   */
  public function link($label,$title=null,$class=null,$confirm=null,$style=null,$tags=null){
   return api_link("#".$this->id,$label,$title,$class,false,$confirm,$style,"data-toggle='modal' ".$tags,"_self",$this->id);
  }

  /**
   * Renderize Modal object
   *
   * @return string HTML source code
   */
  public function render(){
   // make size
   switch($this->size){
    case "small":$size_class=" modal-sm";break;
    case "large":$size_class=" modal-lg";break;
    default:$size_class=null;
   }
   // build html source coide
   $return="<!-- ".$this->id." -->\n";
   $return.="<div class=\"modal fade ".$this->class."\" id=\"".$this->id."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"".$this->id."-label\">\n";
   $return.=" <div class=\"modal-dialog".$size_class."\" role=\"document\">\n";
   $return.="  <div class=\"modal-content\">\n";
   // renderize modal window header
   if($this->header || $this->title){
    $return.="   <div class=\"modal-header\">\n";
    $return.="    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
    // show title
    if($this->title){$return.="     <h4 class=\"modal-title\" id=\"".$this->id."-label\">".$this->title."</h4>\n";}
    $return.=$this->header."   </div>\n";
   }
   // renderize modal window body
   $return.="   <div class=\"modal-body\">\n".$this->body."   </div>\n";
   // renderize modal window footer
   if($this->footer){$return.="   <div class=\"modal-footer\">\n".$this->footer."   </div>\n";}
   $return.="  </div>\n";
   $return.=" </div>\n";
   $return.="</div><!-- /".$this->id." -->\n";
   // return html source code
   return $return;
  }

 }

?>