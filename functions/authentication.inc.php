<?php
/**
 * Authentication Functions
 *
 * @package Coordinator\Functions
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.coordinator.it
 */

/**
 * User Authentication
 *
 * @param string $username Username (Mail address)
 * @param string $password Password
 * @return integer Account User ID or Error Code
 *                 -1 User account was not found
 *                 -2 Password does not match
 */
function api_authentication($username,$password){
	// retrieve user object
	$user_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__users` WHERE `username`='".$username."' OR `mail`='".$username."'");
	// check for user object
	if(!$user_obj->id){return -1;}
	// check password
	if(md5($password)!==$user_obj->password){return -2;}
	// return user object
	return $user_obj->id;
}

/**
 * Authentication LDAP
 *
 * @param string $username Username
 * @param string $password Password
 * @return integer Account User ID or Error Code
 *                 -1 User account was not found
 *                 -2 Binding error
 *                 -3 Groups error
 */
function api_authentication_ldap($username,$password){
	// definitions
	$binded=false;
	/** @todo check ping or use ldap cache */
	// connect to ldap server
	$ldap=@ldap_connect($GLOBALS['settings']->sessions_ldap_hostname);
	// set ldap options
	@ldap_set_option($ldap,LDAP_OPT_PROTOCOL_VERSION,3);
	@ldap_set_option($ldap,LDAP_OPT_REFERRALS,0);
	// try to bind with specified credentials
	$bind=@ldap_bind($ldap,$username.$GLOBALS['settings']->sessions_ldap_domain,$password);
	// check for bind
	if(!$bind){return -2;}
	// Check ldap groups if defined
	if($GLOBALS['settings']->sessions_ldap_groups){
		// check presence in groups
		$filter="(".$GLOBALS['settings']->sessions_ldap_userfield."=".$username.")"; //
		$attr=array("memberof");
		$result=ldap_search($ldap,$GLOBALS['settings']->sessions_ldap_dn,$filter,$attr);
		$entries=ldap_get_entries($ldap, $result);
		// cycle all ldap memberof user group
		foreach($entries[0]['memberof'] as $groups){if(strpos($groups,$GLOBALS['settings']->sessions_ldap_groups)){$binded=true;}}
	}else{
		// or set binded to true
		$binded=true;
	}
	// disconnect from ldap
	@ldap_unbind($ldap);
	// check for binded value
	if(!$binded){return -3;}
	// retrieve user object
	$user_obj=$GLOBALS['database']->queryUniqueObject("SELECT * FROM `framework__users` WHERE `username`='".$username."'");
	// check for user object
	if(!$user_obj->id){return -1;}
	// check for password caching
	if($GLOBALS['settings']->sessions_ldap_cache){
		// build user query objects
		$user_qobj=new stdClass();
		// acquire variables
		$user_qobj->id=$user_obj->id;
		$user_qobj->password=md5($password);
		$user_qobj->pwdTimestamp=time();
		// debug
		api_dump($user_qobj,"user_qobj");
		// update user
		$GLOBALS['database']->queryUpdate("framework__users",$user_qobj);
	}
	// return user object
	return $user_obj->id;
}
