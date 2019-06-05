<?php
/**
 * Pagination
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Pagination class
  */
 class strPagination{

  /** Properties */
  protected $id;
  protected $page;
  protected $show;
  protected $pages;
  protected $records;
  protected $uri_array;
  protected $query_limits;

  /**
   * Pagination class
   *
   * @param integer $records Total number of records
   * @param integer $show Number of record to show
   * @param string $id Pagination ID, if null randomly generated
   * @return boolean
   */
  public function __construct($records,$show=20,$id=null){
   // check parameters
   if($id){$this->id="pagination_".$id;}else{$this->id="pagination_".md5(rand(1,99999));}
   if($records===null){return false;}
   if(!$show){$show=20;}
   // parse current url
   parse_str(parse_url($_SERVER['REQUEST_URI'])['query'],$this->uri_array);
   // get pagination properties
   if($this->uri_array['pnr']){$this->page=$this->uri_array['pnr'];}else{$this->page=1;}
   if($this->uri_array['psr']){$this->show=$this->uri_array['psr'];}else{$this->show=$show;}
   // unset action if exist
   unset($this->uri_array['act']);

   // check for tab pagination
   /*if(strlen($tab) && $this->uri_array['tab']!=$tab){
    $this->page=1;
    $this->show=$show;
    $this->uri_array['tab']=$tab;
   }*/

   // total records
   $this->records=$records;
   // calculate total pages and make query limits
   if($this->show=="all"){
    $this->page=1;
    $this->pages=1;
    $this->query_limits=null;
   }else{
    $this->pages=ceil($this->records/$this->show);
    if(!$this->pages){$this->pages=1;}
    if($this->page>$this->pages){$this->page=$this->pages;}
    $this->query_limits="LIMIT ".(($this->page-1)*$this->show).",".$this->show;
   }
   // return
   return true;
  }

  /**
   * Get Query Limits
   *
   * @return string SQL Query limits
   */
  public function getQueryLimits(){
   return $this->query_limits;
  }

  /**
   * Renderize pagination object
   *
   * @return string HTML source code
   */
  public function render(){
   $return.="<!-- pagination -->\n";
   $return.="<div class=\"row\">\n";
   // page viewer
   $return.=" <div class=\"col-xs-12 col-md-6\">\n";
   $return.="  <nav>\n";
   $return.="   <ul class=\"pagination pagination-sm\" style=\"margin:0 0 16px 0;\">\n";
   $return.="    <li><a class=\"hidden-link\">".api_text("pagination-shows")."</a></li>\n";
   // check for not exceted page shown records
   if(!in_array($this->show,array(20,100,"all"))){$return.="    <li class=\"active\"><a href=\"#\">".$this->show."</a></li>\n";}
   // 20 page shown records
   if($this->show==20){$return.="    <li class=\"active\"><a href=\"#\">20</a></li>\n";}
   else{
    $v_uri_array=$this->uri_array;
    $v_uri_array['psr']=20;
    $v_url="?".http_build_query($v_uri_array);
    $return.="    <li><a href=\"".$v_url."\">20</a></li>\n";
   }
   // 100 page shown records
   if($this->show==100){$return.="    <li class=\"active\"><a href=\"#\">100</a></li>\n";}
   else{
    $v_uri_array=$this->uri_array;
    $v_uri_array['psr']=100;
    $v_url="?".http_build_query($v_uri_array);
    $return.="    <li><a href=\"".$v_url."\">100</a></li>\n";
   }
   // all page shown records
   if($this->show=="all"){$return.="    <li class=\"active\"><a href=\"#\">".api_text("pagination-all")."</a></li>\n";}
   else{
    $v_uri_array=$this->uri_array;
    $v_uri_array['psr']="all";
    $v_url="?".http_build_query($v_uri_array);
    $return.="    <li><a href=\"".$v_url."\">".api_text("pagination-all")."</a></li>\n";
   }
   $return.="   </ul>\n";
   $return.="  </nav>\n";
   $return.=" </div><!-- /col -->\n";
   // check for all page shown records
   if($this->show!="all"){
    // page changer
    $return.=" <div class=\"col-xs-12 col-md-6 text-right\">\n";
    $return.="  <nav>\n";
    $return.="   <ul class=\"pagination pagination-sm\" style=\"margin:0 0 16px 0;\">\n";
    // previous
    if($this->page==1){$return.="    <li class=\"disabled\"><a href=\"#\">&laquo; ".api_text("pagination-previous")."</a></li>\n";}
    else{
     $v_uri_array=$this->uri_array;
     $v_uri_array['pnr']=($this->page-1);
     $v_url="?".http_build_query($v_uri_array);
     $return.="    <li><a href=\"".$v_url."\">&laquo; ".api_text("pagination-previous")."</a></li>\n";
    }
    // page
    $return.="    <li class=\"active\"><a href=\"#\">".api_text("pagination-page",array($this->page,$this->pages))."</a></li>\n";
    // next
    if($this->page==$this->pages){$return.="    <li class=\"disabled\"><a href=\"#\">".api_text("pagination-next")." &raquo;</a></li>\n";}
    else{
     $v_uri_array=$this->uri_array;
     $v_uri_array['pnr']=($this->page+1);
     $v_url="?".http_build_query($v_uri_array);
    $return.="    <li><a href=\"".$v_url."\">".api_text("pagination-next")." &raquo;</a></li>\n";
    }
    $return.="   </ul>\n";
    $return.="  </nav>\n";
    $return.=" </div><!-- /col -->\n";
   }
   $return.="</div><!-- /pagination -->\n";
   // return HTML code
   return $return;
  }

 }

?>