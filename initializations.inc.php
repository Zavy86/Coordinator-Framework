<?php
/**
 * Initializations
 *
 * @package Coordinator
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

 // start session
 session_start();

 // definitions
 global $debug;
 global $develop;
 global $configuration;
 global $localization;
 global $database;
 global $settings;
 global $session;
 global $app;

 // reset session logs
 $_SESSION['coordinator_logs']=null;

 // check for configuration file
 if(!file_exists(realpath(dirname(__FILE__))."/config.inc.php")){die("Coordinator Framework is not configured..<br><br><a href='setup.php'>Setup</a>");}

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
 define('DEBUG',$debug);
 define('VERSION',file_get_contents("VERSION.txt"));
 define("PATH",$configuration->path);
 define('HOST',(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['HTTP_HOST']);
 define('ROOT',rtrim(str_replace("\\","/",realpath(dirname(__FILE__))."/"),PATH));
 define('URL',HOST.PATH);
 define('DIR',ROOT.PATH);
 define('MODULE',$r_module);
 define('MODULE_PATH',DIR."modules/".MODULE."/");
 if($r_script){define("SCRIPT",$r_script);}
 if($r_action){define("ACTION",$r_action);}
 if($r_tab){define("TAB",$r_tab);}

 // include functions
 require_once(DIR."functions/generic.inc.php");
 require_once(DIR."functions/attachment.inc.php");
 require_once(DIR."functions/authentication.inc.php");
 require_once(DIR."functions/calendar.inc.php");
 require_once(DIR."functions/date.inc.php");
 require_once(DIR."functions/event.inc.php");
 require_once(DIR."functions/mail.inc.php");
 require_once(DIR."functions/map.inc.php");
 require_once(DIR."functions/number.inc.php");
 require_once(DIR."functions/timestamp.inc.php");

 // include structures
 require_once(DIR."structures/strApplication.class.php");
 require_once(DIR."structures/strDashboard.class.php");
 require_once(DIR."structures/strDescriptionList.class.php");
 require_once(DIR."structures/strFilter.class.php");
 require_once(DIR."structures/strForm.class.php");
 require_once(DIR."structures/strGauge.class.php");
 require_once(DIR."structures/strGrid.class.php");
 require_once(DIR."structures/strList.class.php");
 require_once(DIR."structures/strModal.class.php");
 require_once(DIR."structures/strNav.class.php");
 //require_once(DIR."structures/strNavbar.class.php");
 require_once(DIR."structures/strOperationsButton.class.php");
 require_once(DIR."structures/strPagination.class.php");
 require_once(DIR."structures/strPanel.class.php");
 require_once(DIR."structures/strParametersForm.class.php");
 require_once(DIR."structures/strProgressBar.class.php");
 require_once(DIR."structures/strTab.class.php");
 require_once(DIR."structures/strTable.class.php");

 // include classes
 require_once(DIR."classes/cObject.class.php");
 require_once(DIR."classes/cLog.class.php");
 require_once(DIR."classes/cAttachment.class.php");
 require_once(DIR."classes/cAuthorization.class.php");
 require_once(DIR."classes/cEvent.class.php");
 require_once(DIR."classes/cGroup.class.php");
 require_once(DIR."classes/cLocalization.class.php");
 require_once(DIR."classes/cMail.class.php");
 require_once(DIR."classes/cMenu.class.php");
 require_once(DIR."classes/cModule.class.php");
 require_once(DIR."classes/cParameter.class.php");
 require_once(DIR."classes/cSettings.class.php");
 require_once(DIR."classes/cSession.class.php");
 require_once(DIR."classes/cUser.class.php");
 require_once(DIR."classes/cQuery.class.php");

 // include database class
 require_once(DIR."classes/cDatabase.class.php");

 // build localization instance
 $localization=new cLocalization();
 // build database instance
 $database=new cDatabase();
 // build settings instance
 $settings=new cSettings();
 // build session instance
 $session=new cSession();

?>