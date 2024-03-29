<?php
/**
 * Nav
 *
 * Coordinator Structure Class for Navigations
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Nav structure class
  */
 class strNav{

  /** Properties */
  protected $id;
  protected $title;
  protected $class;
  protected $container;
  protected $items_array;
  protected $current_item;

  /**
   * Nav structure class
   *
   * @param string $class CSS class ( nav-tabs | nav-pills | nav-stacked )
   * @param boolean $container Renderize container
   * @param string $id Nav ID, if null randomly generated
   * @return boolean
   */
  public function __construct($class="nav-tabs",$container=true,$id=null){
   $this->id="nav_".($id?$id:api_random());
   $this->class=$class;
   $this->container=$container;
   $this->current_item=0;
   $this->items_array=array();
   return true;
  }

  /**
   * Set Title
   *
   * @return boolean
   */
  public function setTitle($title){
   if(!$title){return false;}
   $this->title=$title;
   return true;
  }

  /**
   * Add Item
   *
   * @param string $label Label
   * @param string $url URL
   * @param string $class CSS class
   * @param boolean $enabled Enabled
   * @return boolean
   */
  public function addItem($label,$url="#",$enabled=true,$class=null,$style=null,$tags=null,$target="_self"){
   $item=new stdClass();
   $item->label=$label;
   $item->url=$url;
   $item->urlParsed=api_parse_url($url);
   $item->enabled=$enabled;
   $item->class=$class;
   $item->style=$style;
   $item->tags=$tags;
   $item->target=$target;
   $item->subItems_array=array();
   // add item to nav
   $this->current_item++;
   $this->items_array[$this->current_item]=$item;
   return true;
  }

  /**
   * Add Sub Item
   *
   * @param string $label Label
   * @param string $url URL
	 * @param boolean $enabled Enabled
	 * @param string $confirm Show confirm alert box
	 * @param string $class CSS class
	 * @param string $style Custom CSS
	 * @param string $tags Custom HTML tags
	 * @param string $target Target window
   * @return boolean
   */
  public function addSubItem($label,$url,$enabled=true,$confirm=null,$class=null,$style=null,$tags=null,$target="_self"){
   if(!$this->current_item){echo "ERROR - Nav->addSubItem - No item defined";return false;}
   $subItem=new stdClass();
   $subItem->typology="item";
   $subItem->label=$label;
   $subItem->url=$url;
   $subItem->urlParsed=api_parse_url($url);
   $subItem->enabled=$enabled;
   $subItem->confirm=$confirm;
   $subItem->class=$class;
   $subItem->style=$style;
   $subItem->tags=$tags;
   $subItem->target=$target;
   // add sub item to item
   $this->items_array[$this->current_item]->subItems_array[]=$subItem;
   return true;
  }

  /**
   * Add Sub Separator
   *
   * @param string $class CSS class
   * @return boolean
   */
  public function addSubSeparator($class=null){
   if(!$this->current_item){echo "ERROR - Nav->addSubSeparator - No item defined";return false;}
   $subSeparator=new stdClass();
   $subSeparator->typology="separator";
   $subSeparator->enabled=true;
   $subSeparator->class=$class;
   // add sub item to item
   $this->items_array[$this->current_item]->subItems_array[]=$subSeparator;
   return true;
  }

  /**
   * Add Sub Header
   *
   * @param string $label Label
   * @param string $class CSS class
   * @return boolean
   */
  public function addSubHeader($label,$class=null){
   if(!$this->current_item){echo "ERROR - Nav->addSubHeader - No item defined";return false;}
   $subHeader=new stdClass();
   $subHeader->typology="header";
   $subHeader->label=$label;
   $subHeader->enabled=true;
   $subHeader->class=$class;
   // add sub item to item
   $this->items_array[$this->current_item]->subItems_array[]=$subHeader;
   return true;
  }

  /**
   * Renderize Nav object
   *
   * @param boolean $echo Echo Nav source code or return
   * @return boolean|string Nav source code
   */
  public function render($echo=true){
   // calculate responsive min-width
   $min_width=strlen((string)$this->title)*16;
   foreach($this->items_array as $item){
    if(substr($item->label,0,2)=="<i"){$min_width+=45;}
    else{$min_width+=(strlen($item->label)*7)+32;}
   }
   // renderize nav
   $return=null;
   // check for container
   if($this->container){
    $return.="<!-- nav container -->\n";
    $return.="<div class='container'>\n";
    $return.=" <!-- nav-responsive -->\n";
    $return.=" <div class=\"nav-responsive\">\n";
    $ident="  ";
   }
   $return.=$ident."<!-- nav -->\n";
   $return.=$ident."<ul id=\"".$this->id."\" class=\"nav ".$this->class."\" style=\"min-width:".$min_width."px;\">\n";
   // title
   if($this->title){$return.=$ident." <li class=\"title\">".$this->title."</li>\n";}
   // cycle all items
   foreach($this->items_array as $item){
    // check for active
    $active=false;
    if($item->urlParsed->query_array['mod']==MODULE && $item->urlParsed->query_array['scr']==SCRIPT){$active=true;}
    if(is_int(strpos($this->class,"nav-pills")) && defined('TAB') && $item->urlParsed->query_array['tab']!=TAB){$active=false;}
    if(count($item->subItems_array)){
     foreach($item->subItems_array as $subItem){
      if($subItem->urlParsed->query_array['mod']==MODULE && $subItem->urlParsed->query_array['scr']==SCRIPT){$active=true;}
      if(is_int(strpos($this->class,"nav-pills")) && defined('TAB') && $subItem->urlParsed->query_array['tab']!=TAB){$active=false;}
     }
    }
    // lock url if active or disabled
    if($active||!$item->enabled){$item->url="#";}
    // make item class
    $item_class=null;
    if($active){$item_class.="active ";}
    if(!$item->enabled){$item_class.="disabled ";}
    if($item->class){$item_class.=$item->class;}
    // make item tags
    $item_tags=null;
    if($item->style){$item_tags.=" style=\"".$item->style."\"";}
    if($item->tags){$item_tags.=" ".$item->tags;}
    // check for sub items
    if(!count($item->subItems_array)){
     $return.=$ident." <li class=\"".$item_class."\"><a href=\"".$item->url."\" target=\"".$item->target."\"".$item_tags.">".$item->label."</a></li>\n";
    }else{
     $return.=$ident." <li class=\"dropdown ".$item_class."\">\n";
     $return.=$ident."  <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">".$item->label." <span class=\"caret\"></span></a>\n";
     $return.=$ident."  <ul class=\"test dropdown-menu ".$item->class."\">\n";
     // cycle all sub items
     foreach($item->subItems_array as $subItem){
      // check for sub active
      /*$sub_active=false;
      if($subItem->urlParsed->query_array['mod']==MODULE && $subItem->urlParsed->query_array['scr']==SCRIPT){$sub_active=true;}
      if(is_int(strpos($this->class,"nav-pills")) && defined('TAB') && $subItem->urlParsed->query_array['tab']!=TAB){$sub_active=false;}*/
      // lock url if disabled
      //if($sub_active||!$subItem->enabled){$subItem->url="#";}
      if(!$subItem->enabled){$subItem->url="#";}
      // make sub item class
      $subItem_class=null;
      //if($sub_active){$subItem_class.="active ";}
      if(!$subItem->enabled){$subItem_class.="disabled ";}
      if($subItem->class){$subItem_class.=$subItem->class;}
      // make sub item tags
      $subItem_tags=null;
      if($subItem->style){$subItem_tags.=" style=\"".$subItem->style."\"";}
      if($subItem->tags){$subItem_tags.=" ".$subItem->tags;}
      // switch sub item typology
      switch($subItem->typology){
       case "item":$return.=$ident."   <li class=\"".$subItem_class."\"><a href=\"".$subItem->url."\" target=\"".$subItem->target."\"".($subItem->confirm?" onClick=\"return confirm('".addslashes($subItem->confirm)."')\"":null).$subItem_tags.">".$subItem->label."</a></li>\n";break;
       case "separator":$return.=$ident."   <li class=\"divider ".$subItem_class."\" role=\"separator\"".$subItem_tags.">&nbsp;</li>\n";break;
       case "header":$return.=$ident."   <li class=\"dropdown-header".$subItem_class."\"".$subItem_tags.">".$subItem->label."</li>\n";break;
      }
     }
     $return.=$ident."  </ul><!-- dropdown -->\n";
     $return.=$ident." </li>\n";
    }
   }
   // renderize closures
   $return.=$ident."</ul><!-- /nav -->\n";
   // check for container
   if($this->container){
    $return.=" </div><!-- /nav-responsive -->\n";
    if(is_int(strpos($this->class,"nav-tabs"))){$return.="<br><!-- line break -->\n";}
    if(is_int(strpos($this->class,"nav-pills"))){$return.="<!-- thematic break -->\n<div class=\"row\"><div class=\"col-xs-12\"><hr></div></div>\n";}
    $return.="</div><!-- /container -->\n\n";
   }
   // echo or return
   if($echo){echo $return;return true;}else{return $return;}
  }

 }

?>