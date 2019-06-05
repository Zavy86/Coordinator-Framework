<?php
/**
 * Navbar
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Navbar class
  */
 class strNavbar{

  /** Properties */
  protected $title;
  protected $class;
  protected $navs_array;
  protected $current_nav;
  protected $current_item;

  /**
   * Navbar class
   *
   * @param string $title Navbar title
   * @param string $class Navbar class
   * @return boolean
   */
  public function __construct($title=null,$class="navbar-default"){
   $this->title=$title;
   $this->class=$class;
   $this->current_nav=0;
   $this->current_item=0;
   $this->navs_array=array();
   return true;
  }

  /**
   * Set Title
   *
   * @param string $title Navbar title
   * @return boolean
   */
  public function setTitle($title=null){
   if(!$title){return false;}
   $this->title=$title;
   return true;
  }

  /**
   * Add Nav
   *
   * @param string $class Item css class
   * @return boolean
   */
  public function addNav($class=null){
   $nav=new stdClass();
   $nav->class=$class;
   $nav->items_array=array();
   // add nav to navbar
   $this->current_nav++;
   $this->navs_array[$this->current_nav]=$nav;
   return true;
  }

  /**
   * Add Item
   *
   * @param string $label Item label
   * @param string $url Item url
   * @param string $class Item css class
   * @param boolean $enabled Enabled
   * @return boolean
   */
  public function addItem($label,$url="#",$enabled=true,$class=null,$style=null,$tags=null,$target="_self"){
   if(!$this->current_nav){echo "ERROR - Navbar->addItem - No nav defined";return false;}
   $item=new stdClass();
   $item->label=$label;
   $item->url=$url;
   $item->enabled=$enabled;
   $item->class=$class;
   $item->style=$style;
   $item->tags=$tags;
   $item->target=$target;
   $item->subItems_array=array();
   // check, parse and convert
   if(substr($item->url,0,1)=="?"){$item->url="index.php".$item->url;}
   $item->urlParsed=api_parse_url($url);
   // add item to nav
   $this->current_item++;
   $this->navs_array[$this->current_nav]->items_array[$this->current_item]=$item;
   return true;
  }

  /**
   * Add Sub Item
   *
   * @param string $label Item label
   * @param string $url Item url
   * @param string $class Item css class
   * @param boolean $enabled Enabled
   * @return boolean
   */
  public function addSubItem($label,$url,$enabled=true,$class=null,$style=null,$tags=null,$target="_self"){
   if(!$this->current_item){echo "ERROR - Navbar->addSubItem - No item defined";return false;}
   $subItem=new stdClass();
   $subItem->typology="item";
   $subItem->label=$label;
   $subItem->url=$url;
   $subItem->urlParsed=api_parse_url($url);
   $subItem->enabled=$enabled;
   $subItem->class=$class;
   $subItem->style=$style;
   $subItem->tags=$tags;
   $subItem->target=$target;
   // add sub item to item
   $this->navs_array[$this->current_nav]->items_array[$this->current_item]->subItems_array[]=$subItem;
   return true;
  }

  /**
   * Add Sub Separator
   *
   * @param string $class Separator css class
   * @return boolean
   */
  public function addSubSeparator($class=null){
   if(!$this->current_item){echo "ERROR - Navbar->addSubSeparator - No item defined";return false;}
   $subSeparator=new stdClass();
   $subSeparator->typology="separator";
   $subSeparator->enabled=true;
   $subSeparator->class=$class;
   // add sub item to item
   $this->navs_array[$this->current_nav]->items_array[$this->current_item]->subItems_array[]=$subSeparator;
   return true;
  }

  /**
   * Add Sub Header
   *
   * @param string $label Item label
   * @param string $class Separator css class
   * @return boolean
   */
  public function addSubHeader($label,$class=null){
   if(!$this->current_item){echo "ERROR - Navbar->addSubHeader - No item defined";return false;}
   $subHeader=new stdClass();
   $subHeader->typology="header";
   $subHeader->label=$label;
   $subHeader->enabled=true;
   $subHeader->class=$class;
   // add sub item to item
   $this->navs_array[$this->current_nav]->items_array[$this->current_item]->subItems_array[]=$subHeader;
   return true;
  }

  /**
   * Renderize Navbar object
   *
   * @param boolean $echo Echo Navbar source code or return
   * @return boolean|string Navbar source code
   */
  public function render($echo=true){
   // renderize navbar
   $return="<!-- navbar -->\n";
   $return.="<nav class=\"navbar ".$this->class."\">\n";
   $return.=" <!-- navbar-container -->\n";
   $return.=" <div class=\"container\">\n";
   // renderize navbar-header
   $return.="  <!-- navbar-header -->\n";
   $return.="  <div class=\"navbar-header\">\n";
   $return.="   <button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\" data-target=\"#navbar\" aria-expanded=\"false\" aria-controls=\"navbar\">\n";
   $return.="    <span class=\"sr-only\">Toggle navigation</span>\n";
   $return.="    <span class=\"icon-bar\"></span>\n";
   $return.="    <span class=\"icon-bar\"></span>\n";
   $return.="    <span class=\"icon-bar\"></span>\n";
   $return.="   </button>\n";
   if(!in_array($GLOBALS['settings']->show,array("logo","title","logo_title"))){
    $return.="   <a class=\"navbar-brand\" id=\"nav_brand_logo\" href=\"#\"><img alt=\"Brand logo\" src=\"".DIR."uploads/framework/logo.default.png"."\" height=\"20\"></a>\n";
    $return.="   <a class=\"navbar-brand\" id=\"nav_brand_title\" href=\"index.php\">Coordinator Framework</a>\n";
   }
   if(in_array($GLOBALS['settings']->show,array("logo","logo_title"))){ $return.="   <a class=\"navbar-brand\" id=\"nav_brand_logo\" href=\"#\"><img alt=\"Brand logo\" src=\"".$GLOBALS['settings']->logo."\" height=\"20\"></a>\n";}
   if(in_array($GLOBALS['settings']->show,array("title","logo_title"))){$return.="   <a class=\"navbar-brand\" id=\"nav_brand_title\" href=\"index.php\">".$this->title."</a>\n";}
   $return.="  </div><!--/navbar-header -->\n";
   // renderize navbar collapse
   $return.="  <!-- navbar-collapse-->\n";
   $return.="  <div id=\"navbar\" class=\"navbar-collapse collapse\">\n";
   // cycle all navs
   foreach($this->navs_array as $nav){
    $return.="   <ul class=\"nav navbar-nav ".$nav->class."\">\n";
    // cycle all items
    foreach($nav->items_array as $item){
     // check for active
     $active=false;
     if($item->urlParsed->query_array['mod']==MODULE){$active=true;}
     if($item->urlParsed->query_array['scr']&&$item->urlParsed->query_array['scr']!=SCRIPT){$active=false;}
     elseif(count($item->subItems_array)){
      foreach($item->subItems_array as $subItem){
       if($subItem->urlParsed->query_array['mod']==MODULE){$active=true;}
       if($subItem->urlParsed->query_array['scr']&&$subItem->urlParsed->query_array['scr']!=SCRIPT){$sub_active=false;}
       if($active){break;}
      }
     }
     if(is_int(strpos($item->class,"inactive"))){$active=false;}
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
      $return.="    <li class=\"".$item_class."\"".$item_tags."><a href=\"".$item->url."\" target=\"".$item->target."\">".$item->label."</a></li>\n";
     }else{
      $return.="    <li class=\"dropdown ".$item_class."\">\n";
      $return.="     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">".$item->label." <span class=\"caret\"></span></a>\n";
      $return.="     <ul class=\"dropdown-menu ".$item->class."\">\n";
      // cycle all sub items
      foreach($item->subItems_array as $subItem){
       // check for sub active
       if($subItem->urlParsed->query_array['mod']==MODULE){$sub_active=true;}else{$sub_active=false;}
       if($subItem->urlParsed->query_array['scr']&&$subItem->urlParsed->query_array['scr']!=SCRIPT){$sub_active=false;}
       if(is_int(strpos($subItem->class,"inactive"))){$sub_active=false;}
       // lock url if active or disabled
       if($sub_active||!$subItem->enabled){$subItem->url="#";}
       // make sub item class
       $subItem_class=null;
       if($sub_active){$subItem_class.="active ";}
       if(!$subItem->enabled){$subItem_class.="disabled ";}
       if($subItem->class){$subItem_class.=$subItem->class;}
       // make sub item tags
       $subItem_tags=null;
       if($subItem->style){$subItem_tags.=" style=\"".$subItem->style."\"";}
       if($subItem->tags){$subItem_tags.=" ".$subItem->tags;}
       // switch typology
       switch($subItem->typology){
        case "item":$return.="      <li class=\"".$subItem_class."\"><a href=\"".$subItem->url."\" target=\"".$subItem->target."\">".$subItem->label."</a></li>\n";break;
        case "separator":$return.="      <li class=\"divider ".$subItem_class."\" role=\"separator\">&nbsp;</li>\n";break;
        case "header":$return.="      <li class=\"dropdown-header ".$subItem_class."\">".$subItem->label."</li>\n";break;
       }
      }
      $return.="     </ul><!-- dropdown -->\n";
      $return.="    </li>\n";
     }
    }
    $return.="   </ul>\n";
   }
   // renderize closures
   $return.="  </div><!-- /navbar-collapse -->\n";
   $return.=" </div><!-- /navbar-container -->\n";
   $return.="</nav><!-- /navbar -->\n\n";
   // echo or return
   if($echo){echo $return;return true;}else{return $return;}
  }

 }

?>