<?php
/**
 * Functions
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */
 session_start();
 // definitions
 global $debug;
 global $develop;
 global $configuration;
 global $localization;
 global $database;
 global $settings;
 global $session;
 // reset session logs
 $_SESSION['coordinator_logs']=NULL;
 // check for configuration file
 if(!file_exists(realpath(dirname(__FILE__))."/config.inc.php")){die("Coordinator Framework is not configured..<br><br>".api_link("setup.php","Setup"));}
 // include configuration file
 $configuration=new stdClass();
 require_once("config.inc.php");
 // check for debug from session and parameters
 if($_SESSION['coordinator_debug']){$debug=TRUE;}
 if(isset($_GET['debug'])){
  if($_GET['debug']==1){$debug=TRUE;$_SESSION['coordinator_debug']=TRUE;}
  else{$debug=FALSE;$_SESSION['coordinator_debug']=FALSE;}
 }
 // errors configuration
 ini_set("display_errors",($debug||$develop?TRUE:FALSE));
 if($develop){error_reporting(E_ALL & ~E_NOTICE);}
 else{error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);}
 // module variables
 $r_module=$_REQUEST['mod'];
 if(!$r_module){$r_module="dashboards";}
 $r_script=$_REQUEST['scr'];
 if(!$r_script){$r_script=NULL;}
 $r_action=$_REQUEST['act'];
 if(!$r_action){$r_action=NULL;}
 $r_tab=$_REQUEST['tab'];
 if(!$r_tab){$r_tab=NULL;}
 // constants definitions
 define('DIR',$configuration->dir);
 define('URL',(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['HTTP_HOST'].$GLOBALS['configuration']->dir);
 define('ROOT',realpath(dirname(__FILE__))."/");
 define('HELPERS',DIR."helpers/");
 define('MODULE',$r_module);
 define('MODULE_PATH',ROOT."modules/".MODULE."/");
 if($r_script){define("SCRIPT",$r_script);}
 if($r_action){define("ACTION",$r_action);}
 if($r_tab){define("TAB",$r_tab);}
 // include classes
 require_once(ROOT."classes/localization.class.php");
 require_once(ROOT."classes/database.class.php");
 require_once(ROOT."classes/settings.class.php");
 require_once(ROOT."classes/session.class.php");
 require_once(ROOT."classes/module.class.php");
 require_once(ROOT."classes/menu.class.php");
 require_once(ROOT."classes/authorization.class.php");
 require_once(ROOT."classes/user.class.php");
 require_once(ROOT."classes/group.class.php");
 require_once(ROOT."classes/html.class.php");
 require_once(ROOT."classes/grid.class.php");
 require_once(ROOT."classes/nav.class.php"); /** fare classe tabs/tabbable per copia - e integrare tab nella nav */
 require_once(ROOT."classes/navbar.class.php");
 require_once(ROOT."classes/table.class.php");
 require_once(ROOT."classes/form.class.php");
 require_once(ROOT."classes/modal.class.php");
 require_once(ROOT."classes/dl.class.php");
 require_once(ROOT."classes/operations-button.class.php");

 // load modules  /** @todo fare funzione */

 // build localization instance
 $localization=new Localization();

 // build database instance
 $database=new Database();

 // build settings instance
 $settings=new Settings();

 // build session instance
 $session=new Session();

 /**
  * Renderize a variable dump into a pre tag
  *
  * @param string $variable variable to dump
  * @param string $label dump label
  * @param API_DUMP_PRINTR|API_DUMP_VARDUMP $function dump function
  * @param string $class pre dump class
  */
 function api_dump($variable,$label=NULL,$function=API_DUMP_PRINTR,$class=NULL){
  echo "\n\n<!-- dump -->\n";
  echo "<pre class='".$class."'>\n";
  if($label<>NULL){echo "<strong>".$label."</strong><br>";}
  if(is_string($variable)){$variable=str_replace(array("<",">"),array("&lt;","&gt;"),$variable);}
  switch($function){
   case API_DUMP_PRINTR:print_r($variable);break;
   case API_DUMP_VARDUMP:var_dump($variable);break;
   default:echo $variable."\n";
  }
  echo "</pre>\n<!-- /dump -->\n\n";
 }
 /**
  * api_dump contants
  *
  * @const API_DUMP_PRINTR dump with print_r()
  * @const API_DUMP_VARDUMP dump with var_dump()
  */
 define('API_DUMP_PRINTR',1);
 define('API_DUMP_VARDUMP',2);

 /**
  * Debug
  */
 function api_debug(){
  if($GLOBALS['debug']){
   api_dump($GLOBALS['session']->debug(),"session");
   api_dump($GLOBALS['settings']->debug(),"settings");
   api_dump(get_defined_constants(true)["user"],"contants");
   api_dump($_SESSION["coordinator_logs"],"logs");
  }
 }

 /**
  * Redirect
  *
  * @param string $location Location URL
  */
 function api_redirect($location){
  if($GLOBALS['debug']){die(api_link($location,$location));}
  exit(header("location: ".$location));
 }

 /**
  * Tag
  *
  * @param string $tag HTML Tag
  * @param string $text Content
  * @param string $class CSS class
  * @param string $style Style tags
  * @param string $tags Custom HTML tags
 * @return string|boolean Tag HTML source code or false
  */
 function api_tag($tag,$text,$class=NULL,$style=NULL,$tags=NULL){
  if(!$text){return FALSE;}
  if(!$tag){return $text;}
  $html="<".$tag;
  if($class){$html.=" class=\"".$class."\"";}
  if($style){$html.=" style=\"".$class."\"";}
  if($tags){$html.=" ".$tag;}
  $html.=">".$text."</".$tag.">";
  return $html;
 }

/**
 * Text
 *
 * @param string $key Text key
 * @param array $parameters[] Array of parameters
 * @return string|boolean Localized text with parameters or false
 */
function api_text($key,$parameters=NULL,$localization=NULL){
 if(!$key){return false;}
 if(!is_array($parameters)){if(!$parameters){$parameters=array();}else{$parameters=array($parameters);}}
 // get text by key from locale array
 $text=$GLOBALS['localization']->getString($key,$localization);
 // if key not found
 if(!$text){$text=str_replace("|}","}","{".$key."|".implode("|",$parameters)."}");}
 // replace parameters
 foreach($parameters as $key=>$parameter){$text=str_replace("{".$key."}",$parameter,$text);}
 // return
 return $text;
}

 /**
  * Link
  * @param string $url URL
  * @param string $label Label
  * @param string $title Title
  * @param string $class CSS class
  * @param booelan $popup Show popup title
  * @param string $confirm Show confirm alert box
  * @param string $style Style tags
  * @param string $tags Custom HTML tags
  * @param string $target Target window
  * @param string $id Link ID or random created
  * @return string link
  */
 function api_link($url,$label,$title=NULL,$class=NULL,$popup=FALSE,$confirm=NULL,$style=NULL,$tags=NULL,$target="_self",$id=NULL){
  if(!$url){return FALSE;}
  if(!$label){return FALSE;}
  if(!$id){$id=rand(1,99999);}
  if(substr($url,0,1)=="?"){$url="index.php".$url;}
  $return="<a id=\"link_".$id."\" href=\"".$url."\"";
  if($class){$return.=" class=\"".$class."\"";}
  if($style){$return.=" style=\"".$style."\"";}
  if($title){
   if($popup){$return.=" data-toggle=\"popover\" data-placement=\"top\" data-content=\"".$title."\"";}
   else{$return.=" title=\"".$title."\"";}
  }
  if($confirm){$return.=" onClick=\"return confirm('".addslashes($confirm)."')\"";}
  if($tags){$return.=" ".$tags;}
  $return.=" target=\"".$target."\">".$label."</a>";
  return $return;
 }

/**
 * Image
 *
 * @param string $path Image path
 * @param string $class CSS class
 * @param string $width Width
 * @param string $height Height
 * @param booelan $refresh Add random string for cache refresh
 * @param string $tags HTML tags
 * @return string|boolean Image html source code or false
 */
function api_image($path,$class=NULL,$width=NULL,$height=NULL,$refresh=FALSE,$tags=NULL){
 if(!$path){return false;}
 if($refresh){$refresh="?".rand(1,99999);}
 $return="<img src=\"".$path.$refresh."\"";
 if($class){$return.=" class=\"".$class."\"";}
 if($width){$return.=" width=\"".$width."\"";}
 if($height){$return.=" height=\"".$height."\"";}
 if($tags){$return.=" ".$tags;}
 $return.=">";
 return $return;
}

/**
 * Icon
 *
 * @param string $icon Glyphs
 * @param string $title Title
 * @param string $class CSS class
 * @param string $style Custom CSS
 * @param string $tags Custom HTML tags
 * @return string|boolean Icon html source code or false
 */
function api_icon($icon,$title=NULL,$class=NULL,$style=NULL,$tags=NULL){
 if($icon==NULL){return FALSE;}
 if(substr($icon,0,2)=="fa"){$icon="fa fa-fw ".$icon;}
 else{$icon="glyphicon glyphicon-".$icon;}
 if(is_int(strpos($class,"hidden-link"))){$icon.=" faa-tada animated-hover";}
 $return="<i class='".$icon." ".$class."'";
 if($title){$return.="title='".$title."'";}
 if($style){$return.="style='".$style."'";}
 if($tags){$return.=" ".$tags."";}
 $return.=" aria-hidden='true'></i>";
 return $return;
}

 /**
  * Parse URL to standard class                  @todo modificare nome qui e in nav class
  *
  * @param string $url URL to parse
  * @return object Parsed
  */
 function api_parse_url($url=NULL){
  // check url
  if(!$url){$url=(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];}
  // build object
  $return=new stdClass();
  // parse url string into object
  foreach(parse_url($url) as $key=>$value){$return->$key=$value;}
  // parse query to array
  $return->query_array=array();
  parse_str($return->query,$return->query_array);
  // return
  return $return;
 }

 /**
  * Datetime Now
  *
  * @return current datetime
  */
 /*function api_datetime_now(){
  return date("Y-m-d H:i:s");
 }*/

 /**
  * Timestamp Format
  *
  * @param integer $timestamp Unix timestamp
  * @param string $format Date Time format (see php.net/manual/en/function.date.php)
  * @return string|boolean Formatted timestamp or false
  */
 function api_timestamp_format($timestamp,$format="Y-m-d H:i:s",$timezone=NULL){
  if(!is_numeric($timestamp)){return FALSE;}
  if(!$timezone){$timezone=$GLOBALS['session']->user->timezone;}
  // build date time object
  $datetime=new DateTime("@".$timestamp);
  // set date time timezone
  $datetime->setTimeZone(new DateTimeZone($timezone));
  // return date time formatted
  return $datetime->format($format);
 }

 /**
  * Alerts Add
  *
  * @param string $message alert message
  * @param string $class alert class
  * @return boolean alert saved status
  */
 function api_alerts_add($message,$class="info"){
  // checks
  if(!$message){return FALSE;}
  if(!is_array($_SESSION['coordinator_alerts'])){$_SESSION['coordinator_alerts']=array();}
  // build alert object
  $alert=new stdClass();
  $alert->timestamp=time();
  $alert->message=$message;
  $alert->class=$class;
  $_SESSION['coordinator_alerts'][]=$alert;
  // return
  return TRUE;
 }

 /**
  * Check Version
  *
  * @param type $current Version to check
  * @param type $new New version for check
  * @return int -1 oldest,
  *              0 equal,
  *              1 new major,
  *              2 new minor version,
  *              3 new hotfix,
  */
 function api_check_version($current,$new){
  $current_t=explode(".",$current);
  $new_t=explode(".",$new);
  // check major version
  if($new_t[0]>$current_t[0]){return 1;}
  if($new_t[0]<$current_t[0]){return -1;}
  // check minor version
  if($new_t[1]>$current_t[1]){return 2;}
  if($new_t[1]<$current_t[1]){return -1;}
  // check hotfix
  if($new_t[2]>$current_t[2]){return 3;}
  if($new_t[2]<$current_t[2]){return -1;}
  // same version
  return 0;
 }






 /**
  * Modules
  *
  * @return object $return[] Array of module objects
  */
 function api_framework_modules(){
  // definitions
  $return=array();
  // execute query
  $return["framework"]=new Module("framework");
  $modules_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_modules` WHERE `module`!='framework' ORDER BY `module`");
  foreach($modules_results as $module){$return[$module->module]=new Module($module);}
  // return modules
  return $return;
 }

 /**
  * Menus
  *
  * @param string $idMenu Start menu branch
  * @return object $return[] Array of menu objects
  */
 function api_framework_menus($idMenu=NULL){
  // definitions
  $return=array();
  // query where
  if(!$idMenu){$query_where="`fkMenu` IS NULL";}else{$query_where="`fkMenu`='".$idMenu."'";}
  // execute query
  $menus_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_menus` WHERE ".$query_where." ORDER BY `order` ASC");
  foreach($menus_results as $menu){$return[$menu->id]=new Menu($menu);}
  // return menus
  return $return;
 }

 /**
  * Groups
  *
  * @param string $idGroup Start group branch
  * @return object $return[] Array of group objects
  */
 function api_framework_groups($idGroup=NULL){
  // definitions
  $return=array();
  // query where
  if(!$idGroup){$query_where="`fkGroup` IS NULL";}else{$query_where="`fkGroup`='".$idGroup."'";}
  // execute query
  $groups_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework_groups` WHERE ".$query_where." ORDER BY `name` ASC");
  foreach($groups_results as $group){$return[$group->id]=new Group($group);}
  // return groups
  return $return;
 }







             /**
              *
              * @param type $recipient
              * @param type $subject
              * @param type $message
              */
             function api_sendmail($recipient,$subject,$message){
               /** @todo fare funzione con phpmailer */
              mail($recipient,$subject,$message);
             }


             /** @todo check */

            /**
             * Tree to array
             *
             * @param type $return Array for results
             * @param type $function User function for tree branch
             * @param type $idField Branch field ID
             * @param type $fkId Foreign key ID
             * @param type $nesting Nesting level
             */
            function api_tree_to_array(&$return,$function,$idField,$fkId=NULL,$nesting=0){
             // check for array
             if(!is_array($return)){$return=array();}
             // call user funciton with foreign key id
             $results=call_user_func($function,$fkId);
             // last nesting item
             $last_id=NULL;
             // cycle all branch results
             foreach($results as $result){
              // increment last nesting
              $last_id=$result->$idField;
              // add level to result
              $result->nesting=$nesting;
              // add result to results array
              $return[$result->$idField]=$result;
              // recursive call with incremented level
              api_tree_to_array($return,$function,$idField,$result->$idField,($nesting+1));
             }
             if($last_id){$return[$last_id]->nesting_last=TRUE;}
            }

?>