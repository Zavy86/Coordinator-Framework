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
global $html;
// reset session logs
$_SESSION['coordinator_logs']=null;
// check for configuration file
if(!file_exists(realpath(dirname(__FILE__))."/config.inc.php")){die("Coordinator Framework is not configured..<br><br>".api_link("setup.php","Setup"));}
// include configuration file
$configuration=new stdClass();
require_once("config.inc.php");
// check for debug from session and parameters
if($_SESSION['coordinator_debug']){$debug=true;}
if(isset($_GET['debug'])){
 if($_GET['debug']==1){$debug=true;$_SESSION['coordinator_debug']=true;}
 else{$debug=false;$_SESSION['coordinator_debug']=false;}
}
// errors configuration
ini_set("display_errors",($debug||$develop?true:false));
if($develop){error_reporting(E_ALL & ~E_NOTICE);}
else{error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);}
// module variables
$r_module=$_REQUEST['mod'];
if(!$r_module){$r_module="dashboard";}
$r_script=$_REQUEST['scr'];
if(!$r_script){$r_script=null;}
$r_action=$_REQUEST['act'];
if(!$r_action){$r_action=null;}
$r_tab=$_REQUEST['tab'];
if(!$r_tab){$r_tab=null;}
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
// include functions
require_once(ROOT."functions/map.inc.php");
require_once(ROOT."functions/sendmail.inc.php");
require_once(ROOT."functions/timestamp.inc.php");
// include classes
require_once(ROOT."classes/cLocalization.class.php");
require_once(ROOT."classes/cDatabase.class.php");
require_once(ROOT."classes/cEvent.class.php");
require_once(ROOT."classes/cFilter.class.php");
require_once(ROOT."classes/cSettings.class.php");
require_once(ROOT."classes/cMail.class.php");
require_once(ROOT."classes/cSession.class.php");
require_once(ROOT."classes/cModule.class.php");
require_once(ROOT."classes/cMenu.class.php");
require_once(ROOT."classes/cAuthorization.class.php");
require_once(ROOT."classes/cUser.class.php");
require_once(ROOT."classes/cGroup.class.php");
require_once(ROOT."classes/cHTML.class.php");
require_once(ROOT."classes/cGrid.class.php");
require_once(ROOT."classes/cNav.class.php");
require_once(ROOT."classes/cNavbar.class.php");
require_once(ROOT."classes/cDashboard.class.php");
require_once(ROOT."classes/cTable.class.php");
require_once(ROOT."classes/cForm.class.php");
require_once(ROOT."classes/cModal.class.php");
require_once(ROOT."classes/cPanel.class.php");
require_once(ROOT."classes/cDescriptionList.class.php");
require_once(ROOT."classes/cOperationsButton.class.php");
require_once(ROOT."classes/cList.class.php");
require_once(ROOT."classes/cTab.class.php");
require_once(ROOT."classes/cProgressBar.class.php");
require_once(ROOT."classes/cGauge.class.php");
require_once(ROOT."classes/cPagination.class.php");
require_once(ROOT."classes/cQuery.class.php");

// build localization instance
$localization=new cLocalization();
// build database instance
$database=new Database();
// build settings instance
$settings=new cSettings();
// build session instance
$session=new cSession();

/**
 * Renderize a variable dump into a pre tag
 *
 * @param string $variable variable to dump
 * @param string $label dump label
 * @param API_DUMP_PRINTR|API_DUMP_VARDUMP $function dump function
 * @param string $class pre dump class
 */
function api_dump($variable,$label=null,$function=API_DUMP_PRINTR,$class=null){
 if(!$GLOBALS['debug']){return false;}
 echo "\n\n<!-- dump -->\n";
 echo "<pre class='".$class."'>\n";
 if($label<>null){echo "<strong>".$label."</strong><br>";}
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
 * Dump Coordinator Logs
 */
function api_dump_logs(){
 if(!$GLOBALS['debug']){return false;}
 api_dump($_SESSION['coordinator_logs'],"LOGS");
}

/**
 * Debug
 */
function api_debug(){
 if($GLOBALS['debug']){
  api_dump(get_defined_constants(true)["user"],"contants");
  api_dump($GLOBALS['session']->debug(),"session");
  api_dump($GLOBALS['settings']->debug(),"settings");
  api_dump($GLOBALS['localization']->debug(),"localization");
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
function api_tag($tag,$text,$class=null,$style=null,$tags=null){
 if(!strlen($text)){return false;}
 if(!$tag){return $text;}
 $html="<".$tag;
 if($class){$html.=" class=\"".$class."\"";}
 if($style){$html.=" style=\"".$style."\"";}
 if($tags){$html.=" ".$tags;}
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
function api_text($key,$parameters=null,$localization=null){
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
 * Number Format
 *
 * @param string $number Number
 * @param string $decimals Number of decimals
 * @param string $currency Currency sign
 * @return string Formatted number or false
 */
function api_number_format($number,$decimals=2,$currency=null,$small_decimals=false){
 // check parameters
 if(!is_numeric($number)){return false;}
 if(!is_numeric($decimals)){return false;}
 // format number
 $return=number_format($number,$decimals,",",".");
 // check for currency
 if($currency){$return=$currency." ".$return;}
 // check for small decimals
 if($decimals && $small_decimals){
  $real=explode(",",$return)[0];
  $decimals=explode(",",$return)[1];
  $return=api_tag("span",$real.api_tag("small",",".$decimals));
 }
 // return
 return $return;
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
function api_link($url,$label,$title=null,$class=null,$popup=false,$confirm=null,$style=null,$tags=null,$target="_self",$id=null){
 if(!$url){return false;}
 if(!$label){return false;}
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
 * Mail Link
 * @param string $address Mail address
 * @param string $label Link label
 * @param string $title Title
 * @param string $class CSS class
 * @param string $style Style tags
 * @param string $tags Custom HTML tags
 * @param string $target Target window
 * @param string $id Link ID or random created
 * @return string mail link
 */
function api_mail_link($address,$label=null,$title=null,$class=null,$style=null,$tags=null,$target="_self",$id=null){
 // check parameters
 if(!$address){return false;}
 if(!$label){$label=$address;}
 if(!$id){$id=rand(1,99999);}
 // make mail link
 $return="<a id=\"link_".$id."\" href=\"mailto:".$address."\"";
 if($class){$return.=" class=\"".$class."\"";}
 if($style){$return.=" style=\"".$style."\"";}
 if($tags){$return.=" ".$tags;}
 $return.=" target=\"".$target."\">".$label."</a>";
 // return
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
function api_image($path,$class=null,$width=null,$height=null,$refresh=false,$tags=null){
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
function api_icon($icon,$title=null,$class=null,$style=null,$tags=null){
 if($icon==null){return false;}
 if(substr($icon,0,2)=="fa"){$icon="fa fa-fw ".$icon;}
 else{$icon="glyphicon glyphicon-".$icon;}
 if(is_int(strpos($class,"hidden-link"))){$icon.=" faa-tada animated-hover";}
 $return="<i class=\"".$icon." ".$class."\"";
 if($title){$return.=" title=\"".$title."\"";}
 if($style){$return.=" style=\"".$style."\"";}
 if($tags){$return.=" ".$tags."";}
 $return.=" aria-hidden=\"true\"></i>";
 return $return;
}

/**
 * Parse URL to standard class                  @todo modificare nome qui e in nav class
 *
 * @param string $url URL to parse
 * @return object Parsed
 */
function api_parse_url($url=null){
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
/*function api_datetime_now(){     /** @todo verificare
 return date("Y-m-d H:i:s");
}*/

/**
 * Weekly Days
 *
 * @param integer $start number of day to start
 * @return array Weekly Days
 */
function api_weekly_days($start=null){  /** @todo verificare */
 // definitions
 //$days_array=array();
 /** @todo fare tramite impostazioni con primo giorno della settimana 0 o 1 */
 $days_array=array(1=>"monday",2=>"tuesday",3=>"wednesday",4=>"thursday",5=>"friday",6=>"saturday",0=>"sunday");
 return $days_array;
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
 if(!$message){return false;}
 if(!is_array($_SESSION['coordinator_alerts'])){$_SESSION['coordinator_alerts']=array();}
 // build alert object
 $alert=new stdClass();
 $alert->timestamp=time();
 $alert->message=$message;
 $alert->class=$class;
 $_SESSION['coordinator_alerts'][]=$alert;
 // return
 return true;
}

/**
 * Check Version
 *
 * @param type $current Version to check
 * @param type $new New version for check
 * @return int|boolean -1 oldest,
 *                      0 equal,
 *                      1 new major,
 *                      2 new minor version,
 *                      3 new hotfix,
 *                  false on error
 */
function api_check_version($current,$new){
 if(!strlen($current) || !strlen($new)){return false;}
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
 * Check Authorization
 *
 * @param string $module Module
 * @param string $action Action
 * @param booelan $inherited If true check also in hinerited permissions
 * @param booelan $superuser If true return true if user is superuser
 * @return boolean authorized or not
 */
function api_checkAuthorization($module,$action,$inherited=true,$superuser=true){
 /** @todo levare gli alert e i dump */
 // check authorization
 $authorization=$GLOBALS['session']->user->authorizations_array[$module][$action];
 if($authorization=="authorized"){/*api_dump("Check permission [".$module."][".$action."] = AUTORIZED");*/return true;}
 if($inherited && $authorization=="inherited"){/*api_dump("Check permission [".$module."][".$action."] = HINERITED");*/return true;}
 // check superuser
 if($superuser && $GLOBALS['session']->user->superuser){
  if($GLOBALS['debug']){api_alerts_add("Check permission [".$module."][".$action."] = SUPERUSER","warning");}
  return true;
 }
 // unauthorized
 /*api_dump("Check permission [".$module."][".$action."] = NOT");*/
 return false;
}

/**
 * Tree to array
 *
 * @param type $return Array for results
 * @param type $function User function for tree branch
 * @param type $idField Branch field ID
 * @param type $fkId Foreign key ID
 * @param type $nesting Nesting level
 */
function api_tree_to_array(&$return,$function,$idField,$fkId=null,$nesting=0){
 // check for array
 if(!is_array($return)){$return=array();}
 // call user funciton with foreign key id
 $results=call_user_func($function,$fkId);
 // last nesting item
 $last_id=null;
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
 if($last_id){$return[$last_id]->nesting_last=true;}
}

/**
 * SQL Dump Import
 *
 * @param string $sql_dump SQL Dump
 */
function api_sqlDump_import($sql_dump){
 // cycle all queries
 foreach($sql_dump as $line){
  // skip comments
  if(substr($line,0,2)=="--" || $line==""){continue;}
  $sql_query=$sql_query.$line;
  // search for query end signal
  if(substr(trim($line),-1,1)==';'){
   // debug
   api_dump($sql_query);
   // execute query
   $GLOBALS['database']->queryExecute($sql_query);
   // reset query
   $sql_query="";
  }
 }
}



/**
 * Modules
 *
 * @return object $return[] Array of module objects
 */
function api_framework_modules(){  /** @todo levare framework? */
 // definitions
 $return=array();
 // execute query
 $return["framework"]=new cModule("framework");
 $modules_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__modules` WHERE `module`!='framework' ORDER BY `module`");
 foreach($modules_results as $module){$return[$module->module]=new cModule($module);}
 // return modules
 return $return;
}

/**
 * Menus
 *
 * @param string $idMenu Start menu branch
 * @return object $return[] Array of menu objects
 */
function api_framework_menus($idMenu=null){  /** @todo levare framework? */
 // definitions
 $return=array();
 // query where
 if(!$idMenu){$query_where="`fkMenu` IS null";}else{$query_where="`fkMenu`='".$idMenu."'";}
 // execute query
 $menus_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__menus` WHERE ".$query_where." ORDER BY `order` ASC");
 foreach($menus_results as $menu){$return[$menu->id]=new cMenu($menu);}
 // return menus
 return $return;
}

/**
 * Users
 *
 * @param boolean $disabled Show disabled users
 * @param boolean $deleted Show deleted users
 * @return object $return[] Array of user objects
 */
function api_framework_users($disabled=false,$deleted=false){  /** @todo levare framework? */
 // definitions
 $return=array();
 // query where
 $query_where="1";
 if(!$disabled){$query_where.=" AND `enabled`='1'";}
 if(!$deleted){$query_where.=" AND `deleted`='0'";}
 // execute query
 $users_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__users` WHERE ".$query_where." ORDER BY `lastname` ASC,`firstname` ASC");
 foreach($users_results as $user){$return[$user->id]=new cUser($user);}
 // return groups
 return $return;
}

/**
 * Groups
 *
 * @param string $idGroup Start group branch
 * @return object $return[] Array of group objects
 */
function api_framework_groups($idGroup=null){  /** @todo levare framework? */
 // definitions
 $return=array();
 // query where
 if(!$idGroup){$query_where="`fkGroup` IS null";}else{$query_where="`fkGroup`='".$idGroup."'";}
 // execute query
 $groups_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__groups` WHERE ".$query_where." ORDER BY `name` ASC");
 foreach($groups_results as $group){$return[$group->id]=new cGroup($group);}
 // return groups
 return $return;
}

/**
 * Authorizations
 *
 * @param string $module Module authorizations
 * @return object $return Array of authorization objects
 */
function api_framework_authorizations($module=null){  /** @todo levare framework? */
 // definitions
 $return=array();
 // query where
 if($module){$query_where="`module`='".$module."'";}else{$query_where="1";}
 // execute query
 $authorizations_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__modules_authorizations` WHERE ".$query_where." ORDER BY `action`");
 foreach($authorizations_results as $authorization){$return[$authorization->id]=new cAuthorization($authorization);}
 // return groups
 return $return;
}

/**
 * Return Script
 *
 * @param string $default Default script
 * @return string|boolean Return script defined or false
 */
function api_return_script($default){
 if(!$default){return false;}
 // get return script
 $return=$_REQUEST['return_scr'];
 // if not found return default
 if(!$return){$return=$default;}
 // return
 return $return;
}

/**
 * Clean a string
 *
 * @param string $string string to clean
 * @param string $pattern pattern to clean
 * @param string $null returned string if null
 * @return string cleaned string
 */
function api_cleanString($string,$pattern="/[^A-Za-zÀ-ÿ0-9-_.,:;' ]/",$null=NULL){
 // remove multiple spaces and apply patter
 $return=preg_replace($pattern,"",preg_replace("!\s+!"," ",$string));
 // check for null
 if(!strlen($return)){$return=$null;}
 // return
 return $return;
}

/**
 * Require modules
 *
 * @param string $modules[] Array of module names
 */
function api_requireModules($modules){                     /** @todo integrare dentro al module.inc.php e nella classe cModule */
 // check parameters
 if(!is_array($modules)){$modules=array($modules);}
 // cycle all required module
 foreach($modules as $module_f){
  if(!file_exists(ROOT."modules/".$module_f."/functions.inc.php")){api_alerts_add(api_text("alert_requiredModuleNotFound",$module_f),"danger");continue;}
  // include module functions and classes
  require_once(ROOT."modules/".$module_f."/functions.inc.php");
  // load module localization
  $GLOBALS['localization']->load($module_f);
 }
}


/**
 * Events table
 *
 * @param objects $events_array Array of event objects
 * @return object Return cTable object
 */
function api_events_table($events_array){
 // build events table
 $events_table=new cTable(api_text("events-tr-unvalued"));
 $events_table->addHeader("&nbsp;",null,16);
 $events_table->addHeader(api_text("events-th-timestamp"),"nowrap");
 $events_table->addHeader(api_text("events-th-event"),"nowrap");
 $events_table->addHeader(api_text("events-th-note"),null,"100%");
 $events_table->addHeader(api_text("events-th-fkUser"),"nowrap text-right");
 // check parameters
 if(is_array($events_array)){
  // cycle events
  foreach($events_array as $event_fobj){
   // switch level
   switch($event_fobj->level){
    case "debug":$tr_class="success";break;
    case "warning":$tr_class="warning";break;
    case "error":$tr_class="error";break;
    default:$tr_class=null;
   }
   // check selected
   if($event_fobj->id==$_REQUEST['idEvent']){$tr_class="info";}
   // make note
   $note_td=$event_fobj->note;

   /** @todo check replace*/
   if(strpos($note_td,"{")!==false){
    // key substring
    $key_start=(strpos($note_td,"{")+1);
    $key_end=strpos($note_td,"}");
    if(strpos($note_td,"|")!==false){
     // parameter substring
     $param_start=(strpos($note_td,"|")+1);
     $param_end=($key_end);
     $parameter=substr($note_td,$param_start,($param_end-$param_start));
     // update key end
     $key_end=(strpos($note_td,"|"));
    }
    $key=substr($note_td,$key_start,($key_end-$key_start));
    // check for parameter
    if(strlen($parameter)){$note_td=api_text($key,$parameter);}
    else{$note_td=api_text($key);}
   }

   // add event row
   $events_table->addRow($tr_class);
   $events_table->addRowField($event_fobj->getLevel(true,false),"nowrap");
   $events_table->addRowField(api_timestamp_format($event_fobj->timestamp,api_text("datetime")),"nowrap");
   $events_table->addRowField($event_fobj->getEvent(),"nowrap");
   $events_table->addRowField($note_td,"truncate-ellipsis");
   $events_table->addRowField((new cUser($event_fobj->fkUser))->fullname,"nowrap text-right");
  }
 }
 // return
 return $events_table;
}

?>