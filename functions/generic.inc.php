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
  * @param string $class Dump class
  */
 function api_dump($variable,$label=null,$class=null){
  if(!DEBUG){return false;}
  echo "\n\n<!-- dump -->\n";
  echo "<pre class='debug ".$class."'>\n";
  if($label<>null){echo "<strong>".$label."</strong><br>";}
  if(is_string($variable)){$variable=str_replace(array("<",">"),array("&lt;","&gt;"),$variable);}
  print_r($variable);
  echo "</pre>\n<!-- /dump -->\n\n";
 }

 /**
  * Renderize logs, constants, sessions and globals variables for debug
  */
 function api_debug(){
  // check for debug
  if(DEBUG){
   // cycle all logs and dump warning and errors
   foreach($_SESSION["coordinator_logs"] as $log){if($log[0]!="log"){api_dump($log[1],strtoupper($log[0]),$log[0]);}}
   // dump constants, session and globals variables
   api_dump(get_defined_constants(true)["user"],"constants");
   api_dump($GLOBALS['session'],"session");
   api_dump($GLOBALS['settings'],"settings");
   //api_dump($GLOBALS['localization'],"localization");
   api_dump($_SESSION["coordinator_logs"],"logs");
  }
 }

 /**
  * Redirect or show redirection link in debug mode
  *
  * @param string $location Location URL
  */
 function api_redirect($location){
  // check for debug
  if(DEBUG){
   // renderize redirect link
   echo "<div class='redirect'>".api_tag("strong","REDIRECT")."<br>".api_link($location,$location)."</div>\n";
   echo "<link href=\"".PATH."helpers/bootstrap/css/bootstrap-3.3.7-custom.css\" rel=\"stylesheet\">\n";
   // renderize debug
   api_debug();
   // block application
   die();
  }
  // direct redirect
  exit(header("location: ".$location));
 }

 /**
  * Dump exception, set alert and redirect
  *
  * @param string $exception Exception object
  * @param string $location Location URL
  */
 function api_redirect_exception($exception,$location,$alert=null){
  api_dump($exception,"EXCEPTION: ".$exception->getMessage(),"error");
  if($alert){api_alerts_add(api_text($alert),"danger");}
  api_redirect($location);
 }

 /**
  * Get localized text from key
  *
  * @param string $key Text key
  * @param string[] $parameters Array of parameters
  * @param string $localization_code Localization code (Default current)
  * @return string|boolean Localized text with parameters or false
  */
 function api_text($key,$parameters=null,$localization_code=null){
  if(!$key){return false;}
  if(!is_array($parameters)){if(!$parameters){$parameters=array();}else{$parameters=array($parameters);}}
  // get text by key from locale array
  $text=$GLOBALS['localization']->getString($key,$localization_code);
  // if key not found
  if(!$text){$text=str_replace("|}","}","{".$key."|".implode("|",$parameters)."}");}
  // replace parameters
  foreach($parameters as $key=>$parameter){$text=str_replace("{".$key."}",$parameter,$text);}
  // return
  return $text;
 }

 /**
  * Make an HTML tag source code
  *
  * @param string $tag HTML Tag
  * @param string $text Content
  * @param string $class CSS class
  * @param string $style Style tags
  * @param string $tags Custom HTML tags
  * @return string|boolean HTML tag source code or false
  */
 function api_tag($tag,$text,$class=null,$style=null,$tags=null,$id=null){
  // check parameters
  if(!strlen($text)){return false;}
  if(!$tag){return $text;}
  // make html source code
  $html="<".$tag;
  if($id){$html.=" id=\"".$id."\"";}
  if($class){$html.=" class=\"".$class."\"";}
  if($style){$html.=" style=\"".$style."\"";}
  if($tags){$html.=" ".$tags;}
  $html.=">".$text."</".$tag.">";
  // return
  return $html;
 }

 /**
  * Make an HTML link source code
  *
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
  * @return string|boolean HTML link source code or false
  */
 function api_link($url,$label,$title=null,$class=null,$popup=false,$confirm=null,$style=null,$tags=null,$target="_self",$id=null){
  // check parameters
  if(!$url){return false;}
  if(!$label){return false;}
  if(!$id){$id=api_random();}
  if(substr($url,0,1)=="?"){$url="index.php".$url;}
  // make html source code
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
  // return
  return $return;
 }

 /**
  * Make an HTML mail link source code
  *
  * @param string $address Mail address
  * @param string $label Link label
  * @param string $title Title
  * @param string $class CSS class
  * @param string $style Style tags
  * @param string $tags Custom HTML tags
  * @param string $target Target window
  * @param string $id Link ID or random created
  * @return string|boolean HTML mail link source code or false
  */
 function api_mail_link($address,$label=null,$title=null,$class=null,$style=null,$tags=null,$target="_self",$id=null){
  // check parameters
  if(!$address){return false;}
  if(!$label){$label=$address;}
  if(!$id){$id=api_random();}
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
  // make html source code
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
  * Make an HTML image source code
  *
  * @param string $path Image path
  * @param string $class CSS class
  * @param string $width Width
  * @param string $height Height
  * @param booelan $refresh Add random string for cache refresh
  * @param string $tags HTML tags
  * @return string|boolean HTML image source code or false
  */
 function api_image($path,$class=null,$width=null,$height=null,$refresh=false,$tags=null){
  // check parameters
  if(!$path){return false;}
  if($refresh){$refresh="?".api_random();}
  // make html source code
  $return="<img src=\"".$path.$refresh."\"";
  if($class){$return.=" class=\"".$class."\"";}
  if($width){$return.=" width=\"".$width."\"";}
  if($height){$return.=" height=\"".$height."\"";}
  if($tags){$return.=" ".$tags;}
  $return.=">";
  // return
  return $return;
 }

 /**
  * Make an HTML icon source code
  *
  * @param string $icon Glyphs
  * @param string $title Title
  * @param string $class CSS class
  * @param string $style Custom CSS
  * @param string $tags Custom HTML tags
  * @return string|boolean HTML icon source code or false
  */
 function api_icon($icon,$title=null,$class=null,$style=null,$tags=null){
  // check parameters
  if($icon==null){return false;}
  if(substr($icon,0,2)=="fa"){$icon="fa fa-fw ".$icon;}
  else{$icon="glyphicon glyphicon-".$icon;}
  if(is_int(strpos($class,"hidden-link"))){$icon.=" faa-tada animated-hover";}
  // make html source code
  $return="<i class=\"".$icon." ".$class."\"";
  if($title){$return.=" title=\"".$title."\"";}
  if($style){$return.=" style=\"".$style."\"";}
  if($tags){$return.=" ".$tags."";}
  $return.=" aria-hidden=\"true\"></i>";
  // return
  return $return;
 }

 /**
  * Parse URL to standard class
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
  // check parameters
  if(!$message){return false;}
  if(!is_array($_SESSION['coordinator_alerts'])){$_SESSION['coordinator_alerts']=array();}
  // build alert object
  $alert=new stdClass();
  $alert->timestamp=time();
  $alert->message=$message;
  $alert->class=$class;
  // add alert to session alerts array
  $_SESSION['coordinator_alerts'][]=$alert;
  // dump alert for submit and controller
  if(in_array(SCRIPT,array("submit","controller"))){api_dump($alert->message,"ALERT","alert-".$alert->class);}
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
  * @param string $authorization Authorization to check
  * @param string $redirect If unauthorized redirect to this script
  * @param string $module Module (default current module)
  * @param booelan $inherited If true check also in hinerited permissions
  * @param booelan $superuser If true return true if user is superuser
  * @return boolean authorized or not
  */
 function api_checkAuthorization($authorization,$redirect=null,$module=null,$inherited=true,$superuser=true){
  // check parameters
  if(!$authorization){return false;}
  if(!$module){$module=MODULE;}
  // check for all actions
  if($authorization=="*"){
   // get all module action authorizations
   $results_array=$GLOBALS['session']->user->authorizations_array[$module];
   // check all actions
   foreach($results_array as $result){
    // check for action
    if($result=="authorized"){return true;}
    if($inherited && $result=="inherited"){return true;}
   }
  }else{
   // get module action authorization
   $result=$GLOBALS['session']->user->authorizations_array[$module][$authorization];
   // check for action
   if($result=="authorized"){return true;}
   if($inherited && $result=="inherited"){return true;}
  }
  // check superuser
  if($superuser && $GLOBALS['session']->user->superuser){
   //if(DEBUG){api_alerts_add("Check permission [".$module."][".$authorization."] = SUPERUSER","warning");} /** @todo fare un array specifico e farlo vedere prima del debug */
   return true;
  }
  // unauthorized redirection to script
  if($redirect){
   api_alerts_add(api_text("alert_unauthorized",array($module,$authorization)),"danger");
   api_redirect("?mod=".$module."&scr=".$redirect);
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

 /*
  * Random ID generator
  */
 function api_random_id(){
  // generate random code
  return api_random(32);
 }

 /*
  * Random Color generator
  */
 function api_random_color(){
  // generate random code
  return "#".substr(md5(rand()),0,6);
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
  * Return Script   *** OBSOLETO api_return(array) ***
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
  * URL (from array)
  *
  * @param string[] $array Array of url example: array("mod"=>MODULE,"scr"=>SCRIPT,"idObject"=>1)
  * @return string|boolean Return url or false
  */
 function api_url(array $array){
  // check parameters
  if(!$array['scr']){return false;}
  // definitions
  $static=array("mod"=>MODULE,"scr"=>null);
  // merge arrays
  $url_array=array_merge($static,$array);
  // make url
  $url="?".http_build_query($url_array);
  // debug
  /*api_dump($array,"array");
  api_dump($url_array,"url_array");
  api_dump($url,"url");*/
  // return
  return $url;
 }

 /**
  * Return array
  *
  * @param string[] $default Array of default return url example: array("mod"=>MODULE,"scr"=>SCRIPT,"idObject"=>1)
  * @return string|boolean Return url or false
  */
 function api_return(array $default){
  // check parameters
  if(!strlen($default['scr'])){return false;}
  // acquire variables
  $request=$_REQUEST['return'];
  // check for return and merge
  if(is_array($request) && strlen($request['scr'])){$return_array=array_merge($default,$request);}
  else{$return_array=array_merge($default);}
  // debug
  /*api_dump($default,"default");
  api_dump($return,"return");
  api_dump($url_array,"url_array");*/
  // return
  return $return_array;
 }

 /**
  * Return URL
  *
  * @param string[] $default Array of default return url example: array("mod"=>MODULE,"scr"=>SCRIPT,"idObject"=>1)
  * @return string|boolean Return url or false
  */
 function api_return_url(array $default){
  return api_url(api_return($default));
 }

 /**
  * Script Prefix
  *
  * @param string[] $divider Script prefix divider
  * @return string Script prefix
  */
 function api_script_prefix($divider="_"){
  return explode($divider,SCRIPT)[0];
 }

 /**
  * Clean a string
  *
  * @param string $string string to clean
  * @param string $pattern pattern to clean
  * @param string $null returned string if null
  * @return string cleaned string
  */
 function api_cleanString($string,$pattern="/[^A-Za-zÀ-ÿ0-9-_.,:;' ]/",$null=null){
  // remove multiple spaces and apply patter
  $return=preg_replace($pattern,"",preg_replace("!\s+!"," ",$string));
  // check for null
  if(!strlen($return)){$return=$null;}
  // return
  return $return;
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
  // return
  return $parameter_obj->value;
 }

 /**
  * Update a database object
  *
  * @param string $table Database table
  * @param mixed $id Object id
  * @param string $idKey Key field name
  * @return boolean
  */
 function api_object_update($table,$id,$idKey="id"){
  // check parameters
  if(!$table || !$id || !$idKey){return false;}
  // build division query object
  $update_qobj=new stdClass();
  $update_qobj->$idKey=$id;
  $update_qobj->updTimestamp=time();
  $update_qobj->updFkUser=$GLOBALS['session']->user->id;
  // debug
  api_dump($update_qobj,"update query object");
  // update object
  $GLOBALS['database']->queryUpdate($table,$update_qobj);
  // return
  return true;
 }

 /**
  * Available Modules
  *
  * @return object[] Array of module objects
  */
 function api_availableModules(){
  // definitions
  $return=array();
  // execute query
  $return["framework"]=new cModule("framework");
  $modules_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__modules` WHERE `id`!='framework' ORDER BY `id`");
  foreach($modules_results as $module_fobj){$return[$module_fobj->id]=new cModule($module_fobj);}
  // return modules
  return $return;
 }

 /**
  * Available Menus
  *
  * @param string $idMenu Start menu branch
  * @return object[] Array of menu objects
  */
 function api_availableMenus($idMenu=null){
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
  * Available Users
  *
  * @param boolean $disabled Show disabled users
  * @param boolean $deleted Show deleted users
  * @return object[] Array of user objects
  */
 function api_availableUsers($disabled=false,$deleted=false){
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
  * Available Groups
  *
  * @param string $idGroup Start group branch
  * @return object[] Array of group objects
  */
 function api_availableGroups($idGroup=null){
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
  * Available Authorizations
  *
  * @param string $module Module
  * @return object[] Array of authorization objects
  */
 function api_availableAuthorizations($module=null){
  // definitions
  $return=array();
  // query where
  if($module){$query_where="`fkModule`='".$module."'";}else{$query_where="1";}
  // execute query
  $authorizations_results=$GLOBALS['database']->queryObjects("SELECT * FROM `framework__modules__authorizations` WHERE ".$query_where." ORDER BY `order`");
  foreach($authorizations_results as $authorization){$return[$authorization->id]=new cAuthorization($authorization);}
  // return groups
  return $return;
 }

 /**
  * Sort Objects Array
  *
  * @param array $objects_array Array of objects to sort
  * @param string $property Property name for sorting
  * @param boolean $reverse Reverse order
  * @return objects[]|false Array of sorted objects or false
  */
 function api_sortObjectsArray(array $objects_array,$property,$reverse=false){
  // check properties
  if(!$property){return false;}
  // define and set global variable
  global $sort_property;
  $sort_property=$property;
  // sort objects array
  uasort($objects_array,"api_sortObjectsArray_compare");
  // reverse
  if($reverse){$objects_array=array_reverse($objects_array,true);}
  // return
  return $objects_array;
 }
 // Comparing function
 function api_sortObjectsArray_compare($a,$b){return strcasecmp($a->$GLOBALS['sort_property'],$b->$GLOBALS['sort_property']);}

 /**
  * Join array elements with a string
  *
  * @param string $glue Defaults to an empty string
  * @param array $pieces The array of strings to implode
  * @param string $unvalued String returned if array is empty
  * @return string Returns a string containing a string representation of all the array elements in the same order, with the glue string between each element
  */
 function api_implode($glue,array $pieces,$unvalued=null){
  // check parameters
  if(!count($pieces)){return $unvalued;}
  // return imploded pieces
  return implode($glue,$pieces);
 }


 function api_label($label,$class,$style,$tags){
  return api_tag("span",$label,"label ".$class,$style,$tags);
 }

 /**
  * Load Required modules recursively
  *
  * @param type $modules_array
  */
 function api_load_required_modules_recursively($modules_array){
  foreach($modules_array as $module_f){
   // check and load functions
   if(file_exists(DIR."modules/".$module_f."/functions.inc.php")){require_once(DIR."modules/".$module_f."/functions.inc.php");}
   else{echo "WARNING LOADING REQUIRED MODULE: File modules/".$module_f."/functions.inc.php was not found";}
   // check for recursive required modules
   require_once(DIR."modules/".$module_f."/module.inc.php");
   if($module_required_modules){api_load_required_modules_recursively($module_required_modules);}
  }
 }

?>