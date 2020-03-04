<?php
/**
 * Search Bar
 *
 * Coordinator Structure Class for Search Bars
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Search bar structure class
  */
 class strSearchBar{

  /** Properties */
  protected $id;
  protected $action;
  protected $class;
  protected $style;
  protected $tags;

  /**
   * Search Bar structure class
   *
   * @param string $action Submit URL
   * @param string $class CSS class
   * @param string $style Custom CSS
   * @param string $tags Custom HTML tags
   * @param string $id Search Bar ID, if null randomly generated
   * @return boolean
   */
  public function __construct($action,$class=null,$style=null,$tags=null,$id=null){
   if(substr($action,0,1)=="?"){$action="index.php".$action;}
   if(!$action){return false;}
   $this->id="searchbar_".($id?$id:api_random());
   $this->action=$action;
   $this->class=$class;
   $this->style=$style;
   $this->tags=$tags;
   return true;
  }

  /**
   * Renderize Search Bar object
   *
   * @return string HTML source code
   */
  public function render(){
   // renderize search bar
   $return.="<!-- search bar -->\n";
   $return.="<form class=\"form-inline ".$this->class."\"";
   $return.=" action=\"".$this->action."\"";
   $return.=" method=\"POST\"";
   $return.=" id=\"".$this->id."\"";
   if($this->style){$return.=" style=\"".$this->style."\"";}
   if($this->tags){$return.=" ".$this->tags;}
   $return.=">\n";
   $return.=" <div class=\"form-group\" style=\"width:100%;\">\n";
   $return.="  <div class=\"input-group\" style=\"width:100%;\">\n";
   $return.="   <input type=\"text\" class=\"form-control\" name=\"searchbar_query\" id=\"".$this->id."_input_search\" placeholder=\"".api_text("form-fc-search")."\" value=\"".$_REQUEST['searchbar_query']."\">\n";
   $return.="   <div class=\"input-group-btn\">\n";
   $return.="    <a role=\"button\" href=\"#\" class=\"btn btn-primary\" onClick=\"document.getElementById('".$this->id."').submit();\">".api_text("form-fc-search")."</a>\n";
   $return.="   </div><!-- input-group-btn -->\n";
   $return.="  </div><!-- /input-group -->\n";
   $return.=" </div><!-- /form-group -->\n";
   $return.="</form><!-- /form -->\n";
   // return HTML source
   return $return;
  }

 }

?>