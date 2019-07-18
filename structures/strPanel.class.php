<?php
/**
 * Panel
 *
 * Coordinator Structure Class for Panels
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Panel structure class
  */
 class strPanel{

  /** Properties */
  protected $id;
  protected $title;
  protected $header;
  protected $body;
  protected $footer;
  protected $panel_class;
  protected $header_class;
  protected $body_class;
  protected $footer_class;

  /**
   * Panel structure class
   *
   * @param string $title Title
   * @param string $class CSS class
   * @param string $id Panel ID, if null randomly generated
   * @return boolean
   */
  public function __construct($title=null,$class=null,$id=null){
   $this->id="panel_".($id?$id:api_random());
   $this->title=$title;
   $this->panel_class=$class;
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
   * @param string $title Panel window title
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
   * @param string $class CSS class
   * @return boolean
   */
  public function setHeader($content,$class){
   if(!$content){return false;}
   $this->header=$content;
   $this->header_class=$class;
   return true;
  }

  /**
   * Set Body
   *
   * @param string $content Content of the body
   * @param string $class CSS class
   * @return boolean
   */
  public function setBody($content,$class){
   if(!$content){return false;}
   $this->body=$content;
   $this->body_class=$class;
   return true;
  }

  /**
   * Set Footer
   *
   * @param string $content Content of the footer
   * @param string $class CSS class
   * @return boolean
   */
  public function setFooter($content,$class){
   if(!$content){return false;}
   $this->footer=$content;
   $this->footer_class=$class;
   return true;
  }

  /**
   * Renderize Panel object
   *
   * @return string HTML source code
   */
  public function render(){
   $return="<!-- ".$this->id." -->\n";
   $return.="<div class=\"panel panel-default ".$this->panel_class."\" id=\"".$this->id."\">\n";
   // renderize panel header
   if($this->header || $this->title){
    $return.=" <div class=\"panel-heading ".$this->header_class."\">\n";
    // show title
    if($this->title){$return.="  <h4 class=\"panel-title\">".$this->title."</h4>\n";}
    $return.=$this->header." </div><!-- /panel-heading -->\n";
   }
   // renderize panel window body
   if($this->body){$return.=" <div class=\"panel-body ".$this->body_class."\">\n".$this->body." </div>\n";}
   // renderize panel window footer
   if($this->footer){$return.=" <div class=\"panel-footer ".$this->footer_class."\">\n".$this->footer." </div>\n";}
   $return.="</div><!-- /".$this->id." -->\n";
   // return html source code
   return $return;
  }

 }

?>