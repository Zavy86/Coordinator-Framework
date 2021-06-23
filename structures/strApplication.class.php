<?php
/**
 * Application
 *
 * Coordinator Web Application Structure Class
 *
 * @package Coordinator\Classes
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Application structure class
  */
 class strApplication{

  /** Properties */
  protected $title;
  protected $language;
  protected $charset;
  protected $metaTags_array;
  protected $styleSheets_array;
  protected $scripts_array;
  protected $modals_array;
  protected $content;
  protected $footer;

  /**
   * Application structure class
   *
   * @param string $title Page title
   * @param string $language Page language
   * @param string $charset Page charset
   * @return boolean
   */
  public function __construct($title=null,$language="en",$charset="utf-8"){
   $this->title=$title;
   $this->language=$language;
   $this->charset=$charset;
   $this->metaTags_array=array();
   $this->metaTags_array["viewport"]="width=device-width, initial-scale=1";
   $this->styleSheets_array=array();
   $this->scripts_array=array();
   $this->modals_array=array();

   // set meta tags
   $this->setMetaTag("author","Manuel Zavatta [www.zavy.im]");
   $this->setMetaTag("copyright","2009-".date("Y")." © Coordinator [www.coordinator.it]");
   $this->setMetaTag("description","Coordinator is an Open Source Modular Framework");
   $this->setMetaTag("owner",$GLOBALS['settings']->owner);
   // add style sheets
   $this->addStylesheet(PATH."helpers/font-awesome/css/font-awesome.min.css");
   $this->addStylesheet(PATH."helpers/font-awesome-animation/css/font-awesome-animation.min.css");
   /** @todo verificare quali caricare sempre e quali solo alla bisogna */
   $this->addStylesheet(PATH."helpers/justgage/css/justgage-1.2.2.css");
   $this->addStylesheet(PATH."helpers/select2/css/select2-4.0.5.min.css");
   $this->addStylesheet(PATH."helpers/stickytable/css/stickytable-1.0.0.css");
   /** @todo add some css helpers here */
   $this->addStylesheet(PATH."helpers/bootstrap/css/bootstrap-3.3.7.min.css");
   $this->addStylesheet(PATH."helpers/bootstrap-faiconpicker/css/bootstrap-faiconpicker-1.3.0.min.css");
   /** @todo definire temi */
   /*if($GLOBALS['session']->user->theme){$this->addStylesheet(PATH."helpers/bootstrap/css/bootstrap-3.3.7-theme-".$GLOBALS['session']->user->theme.".min.css");}*/
   $this->addStylesheet(PATH."helpers/bootstrap/css/bootstrap-3.3.7-custom.css");

   // add scripts
   $this->addScript(PATH."helpers/jquery/js/jquery-1.12.4.min.js",true);
   /** @todo verificare quali caricare sempre e quali solo alla bisogna */
   $this->addScript(PATH."helpers/peity/js/peity-3.3.0.min.js",true);
   $this->addScript(PATH."helpers/justgage/js/justgage-1.2.2.js",true);
   $this->addScript(PATH."helpers/chartjs/js/chart-2.7.0.min.js",true);
   $this->addScript(PATH."helpers/select2/js/select2-4.0.5.min.js",true);
   $this->addScript(PATH."helpers/stickytable/js/stickytable-1.0.0.js",true);
   /** @todo add some javascript helpers here */
   $this->addScript(PATH."helpers/bootstrap/js/bootstrap-3.3.7.min.js",true);
   $this->addScript(PATH."helpers/bootstrap-filestyle/js/bootstrap-filestyle-1.2.1.min.js",true);
   $this->addScript(PATH."helpers/bootstrap-faiconpicker/js/bootstrap-faiconpicker-1.3.0.min.js",true);

   return true;
  }

  /**
   * Set Meta Tag
   *
   * @param string $name Meta tag name
   * @param string $value Meta tag value
   * @return boolean
   */
  public function setMetaTag($name,$value=null){
   if(!$name){return false;}
   $this->metaTags_array[$name]=$value;
   return true;
  }

  /**
   * Set Title
   *
   * @param string $title Page title
   * @return boolean
   */
  public function setTitle($title=null){
   if(!$title){return false;}
   $this->title=$title;
   return true;
  }

  /**
   * Set Content
   *
   * @param string $footer Body footer
   * @return boolean
   */
  public function setFooter($footer=null){
   $this->footer=$footer;
   return true;
  }

  /**
   * Set Content
   *
   * @param string $content Body content
   * @return boolean
   */
  public function setContent($content){
   if(!$content){echo "ERROR - HTML->setContent - Content is required";return false;}
   $this->content=$content;
   return true;
  }

  /**
   * Add Style Sheet
   *
   * @param string $url URL of style sheet
   * @return boolean
   */
  public function addStyleSheet($url){
   if(!$url){return false;}
   $this->styleSheets_array[]=$url;
   return true;
  }

  /**
   * Add Script
   *
   * @param string $source Source code or URL
   * @param booelan $url true if source is an URL
   * @return boolean
   */
  public function addScript($source=null,$url=false){
   if(!$source && !$url){return false;}
   // build script class
   $script=new stdClass();
   $script->url=(bool)$url;
   $script->source=$source;
   // add script to scripts array
   $this->scripts_array[]=$script;
   return true;
  }

  /**
   * Remove Script
   *
   * @param integer $index Script index
   * @return boolean
   */
  public function removeScript($index){
   if(!$index){return false;}
   // remove script from scripts array
   unset($this->scripts_array[$index]);
   return true;
  }

  /**
   * Add Modal
   *
   * @param string $modal Modal window object
   * @param booelan $url true if source is an URL
   * @return boolean
   */
  public function addModal($modal){
   if(!is_a($modal,strModal)){return false;}
   // add modal to modals array
   $this->modals_array[$modal->id]=$modal;
   return true;
  }

  /**
   * Add Content
   *
   * @param string $content Body content
   * @return boolean
   */
  public function addContent($content,$separator=null){
   if(!$content){echo "ERROR - HTML->addContent - Content is required";return false;}
   $this->content=$this->content.$separator.$content;
   return true;
  }

  /**
   * Renderize HTML object
   *
   * @param boolean $echo Echo HTML source code or return
   * @return boolean|string HTML source code
   */
  public function render($echo=true){
   // renderize application
   $return="<!DOCTYPE html>\n";
   $return.="<html lang=\"".$this->language."\">\n";
   // renderize head
   $return.=" <!-- heading -->\n";
   $return.=" <head>\n";
   // trackers
   if($GLOBALS['settings']->token_gtag){
    // Google Analytics
    $return.="  <!-- trackers -->\n";
    $return.="  <script async src=\"https://www.googletagmanager.com/gtag/js?id=".$GLOBALS['settings']->token_gtag."\"></script>\n";
    $return.="  <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','".$GLOBALS['settings']->token_gtag."');</script>\n";
   }
   // renderize title
   $return.="  <!-- title and icons -->\n";
   $return.="  <title>".$this->title." - ".$GLOBALS['settings']->title."</title>\n";
   // rendrizer favicon
   $return.="  <link rel=\"icon\" href=\"".PATH."uploads/framework/favicon.default.ico\">\n";
   // renderize meta tags
   $return.="  <!-- meta tags -->\n";
   $return.="  <meta charset=\"".$this->charset."\">\n";
   foreach($this->metaTags_array as $name=>$content){$return.="  <meta name=\"".$name."\" content=\"".$content."\">\n";}
   // renderize style sheets
   $return.="  <!-- style sheets -->\n";
   foreach($this->styleSheets_array as $styleSheet_url){$return.="  <link href=\"".$styleSheet_url."\" rel=\"stylesheet\">\n";}
   $return.="  <style>body{padding-top:70px;}</style>\n"; /** @todo valutare se spostare in css custom */
   if($GLOBALS['settings']->analytics_script){$return.="  ".str_replace("\n","\n  ",$GLOBALS['settings']->analytics_script);}
   $return.=" </head>\n";
   // renderize body
   $return.=" <!-- body -->\n";
   $return.=" <body lang=\"".$this->language."\">\n";
   // renderize header
   $return.="  <!-- header -->\n";
   $return.="  <header>\n";
   // renderize navbar
   $return.="   <!-- navbar -->\n";
   $return.="   <nav class=\"navbar navbar-default navbar-fixed-top\">\n";
   $return.="    <!-- navbar-container -->\n";
   $return.="    <div class=\"container\">\n";
   // renderize navbar-header
   $return.="     <!-- navbar-header -->\n";
   $return.="     <div class=\"navbar-header\">\n";
   $return.="      <button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\" data-target=\"#navbar\" aria-expanded=\"false\" aria-controls=\"navbar\">\n";
   $return.="       <span class=\"sr-only\">Toggle navigation</span>\n";
   $return.="       <span class=\"icon-bar\"></span>\n";
   $return.="       <span class=\"icon-bar\"></span>\n";
   $return.="       <span class=\"icon-bar\"></span>\n";
   $return.="      </button>\n";
   // renderize logo and title if not exist setting
   if(!in_array($GLOBALS['settings']->show,array("logo","title","logo_title"))){
    $return.="      <a class=\"navbar-brand\" id=\"nav_brand_logo\" href=\"#\"><img alt=\"Brand logo\" src=\"".PATH."uploads/framework/logo.default.png"."\" height=\"20\"></a>\n";
    $return.="      <a class=\"navbar-brand\" id=\"nav_brand_title\" href=\"index.php\">Coordinator Framework</a>\n";
   }
   if(in_array($GLOBALS['settings']->show,array("logo","logo_title"))){$return.="      <a class=\"navbar-brand\" id=\"nav_brand_logo\" href=\"#\"><img alt=\"Brand logo\" src=\"".$GLOBALS['settings']->logo."\" height=\"20\"></a>\n";}
   if(in_array($GLOBALS['settings']->show,array("title","logo_title"))){$return.="      <a class=\"navbar-brand\" id=\"nav_brand_title\" href=\"index.php\">".$GLOBALS['settings']->title."</a>\n";}
   if($GLOBALS['session']->validity){$return.="      <a class=\"navbar-brand-small visible-xs\" href=\"?mod=".MODULE."\">".api_text(MODULE)."</a>\n";}
   $return.="     </div><!--/navbar-header -->\n";
   // renderize navbar collapse
   $return.="     <!-- navbar-collapse-->\n";
   $return.="     <div id=\"navbar\" class=\"navbar-collapse collapse\">\n";
   // check for session
   if($GLOBALS['session']->validity){
    // main navigation
    $return.="      <!-- main-nav-->\n";
    $return.="      <ul class=\"nav navbar-nav\">\n";
    // dashboard
    $return.="       <li class=\"hidden-xs\"><a href=\"?mod=dashboard\">".api_icon("fa-th-large",api_text("nav-dashboard"),"faa-tada animated-hover")."</a></li>\n";
    // dashboard xs
    $return.="       <li class=\"visible-xs\"><a href=\"?mod=dashboard\">".api_icon("fa-th-large")."&nbsp;".api_text("nav-dashboard")."</a></li>\n";
    // main menu
    $return.="       <!-- main-menu-->\n";
    $return.="       <li class=\"dropdown hidden-xs\">\n";
    $return.="        <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">".api_icon("fa-th-list",api_text("nav-menu"),"faa-tada animated-hover")." <span class=\"caret\"></span></a>\n";
    $return.="        <ul class=\"dropdown-menu\">\n";
    $return.="         <li class=\"dropdown-header\">".api_text("nav-menu")."</li>\n";
    // cycle all menus
    foreach(api_availableMenus(null) as $menu_obj){
     // check for authorization
     if(!$menu_obj->checkAuthorizations()){continue;}
     // get subitems
     $subMenus_array=api_availableMenus($menu_obj->id);
     // make icon
     if($menu_obj->icon){$icon_source=api_icon($menu_obj->icon)." ";}else{$icon_source=null;}
     // check for sub menus
     if(!count($subMenus_array)){
      $return.="         <li><a href=\"".$menu_obj->url."\" target=\"".$menu_obj->target."\">".$icon_source.$menu_obj->label."</a></li>\n";
     }else{
      // make sub menu source
      $submenu_source=null;
      // cycle all menus
      foreach($subMenus_array as $subMenu_fobj){
       // check for authorization
       if(!$subMenu_fobj->checkAuthorizations()){continue;}
       // make icon
       if($subMenu_fobj->icon){$subicon_source=api_icon($subMenu_fobj->icon)." ";}else{$subicon_source=null;}
       $submenu_source.="           <li><a href=\"".$subMenu_fobj->url."\" target=\"".$subMenu_fobj->target."\">".$subicon_source.$subMenu_fobj->label."</a></li>\n";
      }
      // check for submenu source
      if(strlen($submenu_source)){
       $return.="         <!-- sub-menu-->\n";
       $return.="         <li class=\"dropdown dropdown-submenu\">\n";
       $return.="          <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">".$icon_source.$menu_obj->label." <i class=\"fa fa-fw fa-caret-right\"></i></a>\n";
       $return.="          <ul class=\"dropdown-menu\">\n";
       $return.=$submenu_source;
       $return.="          </ul><!-- dropdown -->\n";
       $return.="         </li><!-- sub-menu-->\n";
      }
     }
    }
    $return.="        </ul><!-- dropdown -->\n";
    $return.="       </li><!-- main-menu-->\n";
    // main menu xs
    $return.="       <li class=\"visible-xs\"><a href=\"#\">".api_icon("fa-th-list")."&nbsp;".api_text("nav-menu")."</a></li>\n";
    // cycle all menus
    foreach(api_availableMenus(null) as $menu_obj){
     // check for authorization
     if(!$menu_obj->checkAuthorizations()){continue;}
     // get subitems
     $subMenus_array=api_availableMenus($menu_obj->id);
     // make icon
     if($menu_obj->icon){$icon_source=api_icon($menu_obj->icon)." ";}else{$icon_source=null;}
     // check for sub menus
     if(!count($subMenus_array)){
      $return.="         <li class=\"visible-xs\"><a href=\"".$menu_obj->url."\" target=\"".$menu_obj->target."\">".$icon_source.$menu_obj->label."</a></li>\n";
     }else{
      // make sub menu source
      $submenu_source=null;
      // cycle all menus
      foreach($subMenus_array as $subMenu_fobj){
       // check for authorization
       if(!$subMenu_fobj->checkAuthorizations()){continue;}
       // make icon
       if($subMenu_fobj->icon){$icon_source=api_icon($subMenu_fobj->icon)." ";}else{$icon_source=null;}
       $submenu_source.="           <li><a href=\"".$subMenu_fobj->url."\" target=\"".$subMenu_fobj->target."\">".$icon_source.$subMenu_fobj->label."</a></li>\n";
      }
      // check for submenu source
      if(strlen($submenu_source)){
       $return.="         <!-- dropdown-->\n";
       $return.="         <li class=\"dropdown visible-xs\">\n";
       $return.="          <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">".$icon_source.$menu_obj->label." <span class=\"caret\"></span></a>\n";
       $return.="          <ul class=\"dropdown-menu\">\n";
       $return.=$submenu_source;
       $return.="          </ul><!-- dropdown -->\n";
       $return.="         </li><!-- sub-menu-->\n";
      }
     }
    }
    // module
    $return.="       <li class=\"navbar-module ".(SCRIPT=="dashboard"?"active":null)." hidden-xs\"><a href=\"?mod=".MODULE."\"><strong>".api_text(MODULE)."</strong></a></li>\n";
    // script
    if(SCRIPT!="dashboard"){
     $return.="       <li class=\"active hidden-xs hidden-sm\"><a href=\"#\">".$this->title."</a></li>\n";
    }
    // close main navigation
    $return.="      </ul><!-- main-nav-->\n";
    // right navigation
    $return.="      <!-- right-nav-->\n";
    $return.="      <ul class=\"nav navbar-nav navbar-right\">\n";
    // check for administrators
    if($GLOBALS['session']->user->superuser){
     // modules menu
     $return.="       <!-- modules-menu-->\n";
     $return.="       <li class=\"dropdown\">\n";
     $return.="        <a href=\"#\" class=\"dropdown-toggle hidden-xs text-right\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">".api_icon("fa-puzzle-piece",api_text("nav-modules"),"faa-tada animated-hover")." <span class=\"caret\"></span></a>\n";
     $return.="        <a href=\"#\" class=\"dropdown-toggle visible-xs text-right\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">".api_text("nav-modules")."&nbsp;".api_icon("fa-puzzle-piece",api_text("nav-modules"),"faa-tada animated-hover")." <span class=\"caret\"></span></a>\n";
     $return.="        <ul class=\"dropdown-menu\">\n";
     $return.="         <li class=\"dropdown-header hidden-xs text-right\">".api_text("nav-modules")."</li>\n";
     // get all modules
     $modules_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__modules` WHERE `id`!='framework' ORDER BY `id`");
     foreach($modules_results as $module){
      if($module->id==MODULE){continue;}
      $module=new cModule($module);
      $return.="         <li class=\"text-right\"><a href=\"?mod=".$module->id."\">".$module->name."</a></li>\n";
     }
     $return.="        </ul><!-- dropdown -->\n";
     $return.="       </li><!-- modules-menu-->\n";
    }
    // own menu
    $return.="       <!-- own-menu-->\n";
    $return.="       <li class=\"dropdown\">\n";
    $return.="        <a href=\"#\" class=\"dropdown-toggle hidden-xs text-right\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">".api_image($GLOBALS['session']->user->avatar,null,20,20)." <span class=\"caret\"></span></a>\n";
    $return.="        <a href=\"#\" class=\"dropdown-toggle visible-xs text-right\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">".$GLOBALS['session']->user->fullname."&nbsp;".api_image($GLOBALS['session']->user->avatar,null,20,20)." <span class=\"caret\"></span></a>\n";
    $return.="        <ul class=\"dropdown-menu\">\n";
    $return.="         <li class=\"dropdown-header hidden-xs text-right\">".$GLOBALS['session']->user->fullname."</li>\n";
    $return.="         <li class=\"text-right\"><a href=\"?mod=framework&scr=own_profile\">".api_text("nav-own-profile")." ".api_icon("fa-user-circle-o")."</a></li>\n";
    if($GLOBALS['session']->interpreter){$return.="         <li class=\"text-right\"><a href=\"?mod=framework&scr=submit&act=session_interpret_terminate\">".api_text("nav-interpret_terminate")." ".api_icon("fa-user-secret")."</a></li>\n";}
    else{$return.="         <li class=\"text-right\"><a href=\"?mod=framework&scr=submit&act=session_logout\">".api_text("nav-logout")." ".api_icon("fa-sign-out")."</a></li>\n";}
    // show link for administrators
    if(api_checkAuthorization("framework-settings_manage",null,"framework")){
     $return.="         <li class=\"divider\" role=\"separator\">&nbsp;</li>\n";
     $return.="         <li class=\"text-right\"><a href=\"?mod=framework&scr=dashboard\">".api_text("nav-settings")." ".api_icon("fa-toggle-on")."</a></li>\n";
     $return.="         <li class=\"text-right\"><a href=\"?mod=framework&scr=mails_list\">".api_text("nav-mails")." ".api_icon("fa-envelope-o")."</a></li>\n";
     $return.="         <li class=\"text-right\"><a href=\"?mod=framework&scr=attachments_list\">".api_text("nav-attachments")." ".api_icon("fa-cloud-upload")."</a></li>\n";
     if($GLOBALS['session']->user->superuser){$return.="         <li class=\"text-right\"><a href=\"?".http_build_query($_GET)."&debug=".(!$_SESSION['coordinator_debug'])."\">".api_text("nav-debug")." ".api_icon("fa-code")."</a></li>\n";}
    }
    $return.="        </ul><!-- dropdown -->\n";
    $return.="       </li><!-- own-menu-->\n";
    // close right navigation
    $return.="      </ul><!-- right-nav-->\n";
   }
   // renderize closures
   $return.="     </div><!-- /navbar-collapse -->\n";
   $return.="    </div><!-- /navbar-container -->\n";
   $return.="   </nav><!-- /navbar -->\n";
   $return.="  </header>\n\n";
   // renderize content
   $return.="  <content>\n\n";
   // add warning and errors log to alerts
   foreach($_SESSION["coordinator_logs"] as $log){if($log[0]!="log"){api_alerts_add($log[1],($log[0]=="error"?"danger":"warning"));}}
   // show alerts
   if(count($_SESSION['coordinator_alerts'])){
    $return.="<!-- grid container -->\n";
    $return.="<div class=\"container\">\n";
    $return.=" <!-- grid-row -->\n";
    $return.=" <div class=\"row\">\n";
    $return.="  <!-- grid-row-col -->\n";
    $return.="  <div class=\"col-xs-12\">\n";
    $return.="   <!-- alert -->\n";
    $return.="   <div class=\"alerts\">\n";
    // cycle all alerts
    foreach($_SESSION['coordinator_alerts'] as $alert){
     $dismissable=true;
		 if(strpos("danger",$alert->class)!==false){$dismissable=false;}
		 if(strpos("info",$alert->class)!==false){$dismissable=false;}
     $return.="   <div class=\"alert".($dismissable?" alert-dismissible":null)." alert-".$alert->class."\" role=\"alert\">\n";
     $return.="    <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
     $return.="    <span>".$alert->message."</span>\n";
     $return.="   </div>\n";
    }
    $return.="   </div><!-- /alert -->\n";
    $return.="  </div><!-- /grid-row-col -->\n";
    $return.=" </div><!-- /grid-row -->\n";
    $return.="</div><!-- /grid container -->\n";
    // reset session alerts
    $_SESSION['coordinator_alerts']=array();
   }
   // show content
   $return.=$this->content;
   $return.="  </content>\n\n";
   // renderize footer
   $return.="  <!-- footer -->\n";
   $return.="  <footer>\n";
   // make execution metrics
   if(DEBUG || $GLOBALS['session']->user->superuser){$execution_metrics=" [ Queries: ".api_tag("b",$GLOBALS['database']->query_counter)." | Cached queries: ".api_tag("b",$GLOBALS['database']->cache_query_counter)." | Execution time: ~".api_tag("b",number_format((microtime(true)-$_SERVER["REQUEST_TIME_FLOAT"]),2)." secs")." ]";}else{$execution_metrics=null;}
   // build footer grid
   $footer_grid=new strGrid();
   $footer_grid->addRow();
   $footer_grid->addCol("Copyright 2009-".date("Y")." &copy; <a href='https://www.coordinator.it' target='_blank'><b>Coordinator</b></a>".(DEBUG || $GLOBALS['session']->user->superuser?" ".VERSION:null)." - All Rights Reserved - <b>".$GLOBALS['settings']->owner."</b>".$execution_metrics,"col-xs-12 text-right");
   // set footer
   $return.=$footer_grid->render();
   // jQuery scripts
   $this->addScript("/* Popover Script */\n$(function(){\$(\"[data-toggle='popover']\").popover({'trigger':'hover'});});");
   $this->addScript("/* Alert Timeout Script */\n$(function(){setTimeout(function(){\$('.alert-dismissible').fadeOut();},8000);});");
   $this->addScript("/* Current Row Timeout Script */\n$(function(){setTimeout(function(){\$('.currentrow').removeClass('currentrow');},9000);});");
   $this->addScript("$(document).on('show.bs.modal','.modal',function(){var zIndex=1040+(10*$('.modal:visible').length);$(this).css('z-index',zIndex);setTimeout(function(){\$('.modal-backdrop').not('.modal-stack').css('z-index',zIndex-1).addClass('modal-stack');},0);});");
   // renderize closures
   $return.="  </footer>\n\n";
   // renderize modals
   if(count($this->modals_array)){
    $return.="<!-- modal-windows -->\n\n";
    foreach($this->modals_array as $modal){$return.=$modal->render()."\n";}
    $return.="<!-- /modal-windows -->\n\n";
   }
   // renderize scripts
   $return.="<!-- external-scripts -->\n";
   foreach($this->scripts_array as $script){if($script->url){$return.="<script type=\"text/javascript\" src=\"".$script->source."\"></script>\n";}} /** @vedere se spostando al fondo non da problemi */
   $return.="<!-- /external-scripts -->\n\n";
   $return.="<!-- internal-scripts -->\n";
   $return.="<script type=\"text/javascript\">\n\n";
   foreach($this->scripts_array as $script){if(!$script->url){$return.=$script->source."\n\n";}}
   $return.="</script><!-- /internal-scripts -->\n\n";
   // renderize closures
   $return.=" </body>\n\n";
   $return.="</html>";
   // echo or return
   if($echo){echo $return;return true;}else{return $return;}
  }

 }

?>