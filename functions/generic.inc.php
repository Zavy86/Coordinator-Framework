<?php
/**
 * Generic Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 /**
  * Renderize a variable dump
  *
  * @param string $variable Variable to dump
  * @param string $label Dump label
  * @param string $function Dump function [API_DUMP_PRINTR|API_DUMP_VARDUMP]
  * @param string $class Dump class
  */
 function api_dump($variable,$label=null,$function=API_DUMP_PRINTR,$class=null){
  if(!$GLOBALS['debug']){return false;}
  echo "\n\n<!-- dump -->\n";
  echo "<pre class='debug ".$class."'>\n";
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
  * Constants for api_dump()
  *
  * @const API_DUMP_PRINTR Dump with print_r()
  * @const API_DUMP_VARDUMP Dump with var_dump()
  */
 define('API_DUMP_PRINTR',1);
 define('API_DUMP_VARDUMP',2);

 /**
  * Renderize sessions and globals variables for debug
  */
 function api_debug(){
  if($GLOBALS['debug']){    /** @todo commentare meglio */
   foreach($_SESSION["coordinator_logs"] as $log){if($log[0]!="log"){api_dump($log[1],strtoupper($log[0]),API_DUMP_PRINTR,$log[0]);}}
   api_dump(get_defined_constants(true)["user"],"contants");
   api_dump($GLOBALS['session'],"session");
   api_dump($GLOBALS['settings'],"settings");
   //api_dump($GLOBALS['localization'],"localization");
   api_dump($_SESSION["coordinator_logs"],"logs");
  }
 }

 /**
  * Redirect
  *
  * @param string $location Location URL
  */
 function api_redirect($location){
  if($GLOBALS['debug']){
   echo "<div class='redirect'>".api_tag("strong","REDIRECT")."<br>".api_link($location,$location)."</div>";
   echo "<link href=\"".HELPERS."bootstrap/css/bootstrap-3.3.7-custom.css\" rel=\"stylesheet\">\n";
   api_debug();
   die();
  }
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
  // make current uri array
  parse_str(parse_url($_SERVER['REQUEST_URI'])['query'],$uri_array);
  $uri_array['return_mod']=$uri_array['mod'];unset($uri_array['mod']);
  $uri_array['return_scr']=$uri_array['scr'];unset($uri_array['scr']);
  $uri_array['return_tab']=$uri_array['tab'];unset($uri_array['tab']);
  // make mail link
  //$return="<a id=\"link_".$id."\" href=\"mailto:".$address."\"";
  $link="index.php?mod=framework&scr=mails_add";
  $link.="&recipient=".$address;
  $link.="&".http_build_query($uri_array);
  $return="<a id=\"link_".$id."\" href=\"".$link."\"";
  if($title){$return.=" title=\"".$title."\"";}
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
  * Alerts Add
  *
  * @param string $message alert message
  * @param string $class alert class [info|warning|error]
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
  * @param string $action Action to check
  * @param string $redirect If unauthorized redirect to this script
  * @param string $module Module (default current module)
  * @param booelan $inherited If true check also in hinerited permissions
  * @param booelan $superuser If true return true if user is superuser
  * @return boolean authorized or not
  */
 function api_checkAuthorization($action,$redirect=null,$module=null,$inherited=true,$superuser=true){
  // check parameters
  if(!$action){return false;}
  if(!$module){$module=MODULE;}
  // check authorization
  $authorization=$GLOBALS['session']->user->authorizations_array[$module][$action];
  if($authorization=="authorized"){return true;}
  if($inherited && $authorization=="inherited"){return true;}
  // check superuser
  if($superuser && $GLOBALS['session']->user->superuser){
   if($GLOBALS['debug']){api_alerts_add("Check permission [".$module."][".$action."] = SUPERUSER","warning");}
   return true;
  }
  // unauthorized redirection to script
  if($redirect){
   api_alerts_add(api_text("alert_unauthorized",array(MODULE,$action)),"danger");
   api_redirect("?mod=".MODULE."&scr=".$redirect);
  }
  // unauthorized return
  return false;
 }

 /*
  * Random generator
  *
  * @param integer $lenght Number of characters
  */
 function api_random($lenght=9){
  // check parameters
  if(!is_int($lenght)){$lenght=9;}
  // definitions
  $return=null;
  $chars=array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f","g","h",
               "i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",
               "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R",
               "S","T","U","V","W","X","Y","Z");
  // pick random character
  for($i=0;$i<$lenght;$i++){$return.=$chars[array_rand($chars)];}
  // return
  return $return;
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
  * @param array $modules Array of module names
  */
 function api_requireModules($modules=null){                     /** @todo integrare dentro al module.inc.php e nella classe cModule */
  // check parameters
  if(!is_array($modules)){$modules=array($modules);}
   if(!$modules[0]){unset($modules[0]);}
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
  * Parameter default
  *
  * @param string $parameter Parameter name
  * @param string $module Module name (Default current module)
  * @param integer $user User ID
  * @return boolean
  */
 function api_parameter_default($parameter,$module=null,$user=null){
  // check parameters
  if(!$parameter){return false;}
  if(!$module){$module=MODULE;}
  if(!$user){$user=$GLOBALS['session']->user->id;}
  // make parameter id
  $parameter_code=$module."-".$parameter;
  // get parameter
  $parameter_result=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__users__parameters` WHERE `fkUser`='".$user."' AND `parameter`='".$parameter_code."'");
  //api_dump($parameter_result,"parameter result");
  $parameter_obj=new cParameter($parameter_result);
  //api_dump($parameter_obj,"parameter object");
  // check and converts
  if(!$parameter_obj->id){return false;}
  $parameter_obj->parameter=stripslashes($parameter_obj->parameter);
  $parameter_obj->value=stripslashes($parameter_obj->value);
  // return
  return $parameter_obj->value;
 }

?>