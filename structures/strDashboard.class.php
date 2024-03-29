<?php
/**
 * Dashboard
 *
 * Coordinator Structure Class for Dashboards
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Dashboard structure class
  */
 class strDashboard{

  /** Properties */
  protected $id;
  protected $label;
  protected $description;
  protected $class;
  protected $style;
  protected $tags;
  protected $elements_array;

  /**
   * Dashboard structure class
   *
   * @param string $label Default container label
   * @param string $description Default container description
   * @param string $class CSS class
   * @param string $style Custom CSS
   * @param string $tags Custom HTML tags
   * @param string $id Dashboard ID, if null randomly generated
   * @return boolean
   */
  public function __construct($label=null,$description=null,$class=null,$style=null,$tags=null,$id=null){
   $this->id="dashboard_".($id?$id:api_random());
   $this->label=$label;
   $this->description=$description;
   $this->class=$class;
   $this->style=$style;
   $this->tags=$tags;
   $this->elements_array=array();
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
   * Add Container
   *
   * @param string $label Label
   * @param string $description Description
   * @param string $class CSS class
   * @param string $style Custom CSS
   * @param string $tags Custom HTML tags
   * @return boolean
   */
  public function addContainer($label,$description=null,$class=null,$style=null,$tags=null){
   if(!$label){return false;}
   $element=new stdClass();
   $element->type="container";
   $element->label=$label;
   $element->description=$description;
   $element->class=$class;
   $element->style=$style;
   $element->tags=$tags;
   $this->elements_array[]=$element;
   // add element to elements array
   return true;
  }

  /**
   * Add Tile
   *
   * @param string $url URL
   * @param string $label Label
   * @param string $description Description
   * @param string $enabled Enabled
   * @param string $size Size ( 1x1 | 2x1 | 3x1 | 4x1 | 5x1 | 6x1 )
   * @param string $icon Bottom left icon
   * @param string $counter Bottom right counter
   * @param string $counter_class Counter CSS class
   * @param string $background Background image path
   * @param string $target Link target
   * @param string $class CSS class
   * @param string $style Custom CSS
   * @param string $tags Custom HTML tags
   * @return boolean
   */
  public function addTile($url,$label,$description=null,$enabled=true,$size="1x1",$icon=null,$counter=null,$counter_class=null,$background=null,$target="_self",$class=null,$style=null,$tags=null,$id=null){
   if(!$url||!$label){return false;}
   if(!in_array(strtolower((string)$size),array("1x1","2x1","3x1","4x1","5x1","6x1"))){$size="1x1";}
   if(!$target){$target="_self";}
   $element=new stdClass();
   $element->id="tile_".($id?$id:api_random());
   $element->type="tile";
   $element->url=$url;
   $element->label=$label;
   $element->description=$description;
   $element->enabled=$enabled;
   $element->size=strtolower($size);
   $element->icon=$icon;
   $element->counter=$counter;
   $element->counter_class=$counter_class;
   $element->background=$background;
   $element->target=$target;
   $element->class=$class;
   $element->style=$style;
   $element->tags=$tags;
   // add element to elements array
   $this->elements_array[]=$element;
   return $element->id;
  }

  /**
   * Renderize dashboard object
   *
   * @return string HTML source code
   */
  public function render(){
   // renderize dashboard
   $return="\n<!-- dashboard -->\n";
   $return.="<div class=\"dashboard ".$this->class."\">\n";
   // renderize default container
   $return.=" <!-- dashboard-container -->\n";
   $return.=" <div class=\"dashboard-container ".$this->class."\">\n";
   // check for label
   if($this->label){
    // renderize default container title
    $return.="  <div class=\"dashboard-container-title\">\n";
    $return.="   <div class=\"dashboard-container-label\">".$this->label."</div>\n";
    if($this->description){$return.="   <div class=\"dashboard-container-description\">".$this->description."</div>\n";}
    $return.="  </div>\n";
   }
   // cycle all elements
   foreach($this->elements_array as $element){
    switch($element->type){
     // dashboard container
     case "container":
      // close default container
      $return.=" </div><!-- /dashboard-container -->\n";
      // open new container
      $return.=" <!-- dashboard-container -->\n";
      $return.=" <div class=\"dashboard-container ".$element->class."\">\n";
      $return.="  <div class=\"dashboard-container-title\">\n";
      $return.="   <div class=\"dashboard-container-label\">".$element->label."</div>\n";
      if($element->description){$return.="   <div class=\"dashboard-container-description\">".$element->description."</div>\n";}
      $return.="  </div>\n";
      break;
     // dashboard element
     case "tile":
      // check if tile is starred if not in dashboard
      if(MODULE<>"dashboard"){
       $starred_tile_id=$GLOBALS['database']->queryUniqueValue("SELECT `id` FROM `framework__users__dashboards` WHERE `fkUser`='".$GLOBALS['session']->user->id."' AND `module`='".MODULE."' AND `url`='".$element->url."'");
       // make starred link
       if($starred_tile_id>0){
        $starred_link=api_link("?mod=dashboard&scr=submit&act=tile_remove&idTile=".$starred_tile_id."&redirect_mod=".MODULE."&redirect_scr=".SCRIPT."&redirect_tab=".TAB,api_icon("fa-star",api_text("dashboard-tile-remove"),"hidden-link"))." ";
       }elseif($element->enabled){
        $element->module=MODULE;
        $starred_link=api_link("?mod=dashboard&scr=submit&act=tile_save&redirect_mod=".MODULE."&redirect_scr=".SCRIPT."&redirect_tab=".TAB."&element=".urlencode(json_encode($element)),api_icon("fa-star-o",api_text("dashboard-tile-add"),"hidden-link"))." ";
       }else{
        $starred_link=null;
       }
      }
      // make hyperlink reference
      $href="window.open('".($element->enabled?$element->url:"#")."','".$element->target."');";
      // make background css style
      if($element->background && file_exists(DIR.$element->background)){
       $background_style=" style=\"background-image:url('".$element->background."?rand=".md5(rand(1,99999))."')\"";
       $background_class="dashboard-element-background-alpha";
      }else{
       $background_style=null;
       $background_class=null;
      }
      // renderize dashboard element
      $return.="  <!-- dashboard-element -->\n";
      $return.="  <div class=\"dashboard-element dashboard-element-size-".$element->size." ".(!$element->enabled?"dashboard-element-disabled":null)."\" onclick=\"".$href."\"".$background_style." id=\"".$element->id."\">\n";
      $return.="   <p class=\"dashboard-element-label ".$background_class."\">".$starred_link.$element->label."</p>\n";
      $return.="   <p class=\"dashboard-element-description ".$background_class."\">".$element->description."</p>\n";
      if($element->icon){$return.="   <span class=\"dashboard-element-icon\">".api_icon($element->icon)."</span>\n";}
      if($element->counter){$return.="   <span class=\"dashboard-element-counter ".$element->counter_class."\">".$element->counter."</span>\n";}
      $return.="  </div><!-- /dashboard-element -->\n";
      break;
    }
   }
   $return.=" </div><!-- /dashboard-container -->\n";
   $return.="</div><!-- /dashboard -->\n";
   // return html source code
   return $return;
  }

 }

?>