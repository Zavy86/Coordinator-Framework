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
 require_once(ROOT."functions/generic.inc.php");
 require_once(ROOT."functions/framework.inc.php");  /** @todo verificare se mantenere framework davanti o se toglierlo */
 require_once(ROOT."functions/attachment.inc.php");
 require_once(ROOT."functions/calendar.inc.php");
 require_once(ROOT."functions/date.inc.php");
 require_once(ROOT."functions/event.inc.php");
 require_once(ROOT."functions/map.inc.php");
 require_once(ROOT."functions/number.inc.php");
 require_once(ROOT."functions/sendmail.inc.php");
 require_once(ROOT."functions/timestamp.inc.php");

 // include structures
 require_once(ROOT."structures/strApplication.class.php");
 require_once(ROOT."structures/strDashboard.class.php");
 require_once(ROOT."structures/strDescriptionList.class.php");
 require_once(ROOT."structures/strFilter.class.php");
 require_once(ROOT."structures/strForm.class.php");
 require_once(ROOT."structures/strGauge.class.php");
 require_once(ROOT."structures/strGrid.class.php");
 require_once(ROOT."structures/strList.class.php");
 require_once(ROOT."structures/strModal.class.php");
 require_once(ROOT."structures/strNav.class.php");
 require_once(ROOT."structures/strNavbar.class.php");
 require_once(ROOT."structures/strOperationsButton.class.php");
 require_once(ROOT."structures/strPagination.class.php");
 require_once(ROOT."structures/strPanel.class.php");
 require_once(ROOT."structures/strParametersForm.class.php");
 require_once(ROOT."structures/strProgressBar.class.php");
 require_once(ROOT."structures/strTab.class.php");
 require_once(ROOT."structures/strTable.class.php");

 // include classes
 require_once(ROOT."classes/cAttachment.class.php");
 require_once(ROOT."classes/cAuthorization.class.php");
 require_once(ROOT."classes/cEvent.class.php");
 require_once(ROOT."classes/cGroup.class.php");
 require_once(ROOT."classes/cLocalization.class.php");
 require_once(ROOT."classes/cMail.class.php");
 require_once(ROOT."classes/cMenu.class.php");
 require_once(ROOT."classes/cModule.class.php");
 require_once(ROOT."classes/cSettings.class.php");
 require_once(ROOT."classes/cSession.class.php");
 require_once(ROOT."classes/cUser.class.php");
 require_once(ROOT."classes/cQuery.class.php");

 // include database class
 require_once(ROOT."classes/cDatabase.class.php");

 // build localization instance
 $localization=new cLocalization();
 // build database instance
 $database=new cDatabase();
 // build settings instance
 $settings=new cSettings();
 // build session instance
 $session=new cSession();

?>