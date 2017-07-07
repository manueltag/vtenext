<?php
//crmv@9010
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/ldap/Ldap.php');

switch ($_REQUEST['command'])
{
	case "LdapSearchUser":
		echo SearchUser($_REQUEST['user']);
		break;

	case "LdapSelectUser":
		echo GetUserValues($_REQUEST['user']);
		break;
}

function SearchUser($user)
{
	global $mod_strings;
	$AUTHCFG = get_config_ldap();
	
	if (function_exists('ldap_connect')) {
		
		if (empty($user))
			return "";
		$userArray = ldapSearchUserAccountAndName($user);
		if (empty($userArray))
			return "Error=".$mod_strings["LBL_NO_LDAP_MATCHES"];
			
		if (count($userArray) == 1)
		{
			$accounts = array_keys($userArray);
			return GetUserValues($accounts[0]);
		}
		
		asort($userArray);
		
		foreach ($userArray as $account => $fullname)
		{
			$sOpt .= "\n$account\t$fullname";
		}
		return "Options=\t-----" . $sOpt;
	}
	else {
		return "Warn=".$mod_strings["LBL_NO_LDAP_MODULE"];
	}
}

function GetUserValues($account)
{
	$AUTHCFG = get_config_ldap();
	
	if (empty($account))
		return "";
	$valueArray = ldapGetUserValues($account, array_keys($AUTHCFG[fields]));
	
	if (empty($valueArray))
		return "";
		
	// Some users only have a fullname but the forename and/or lastname is not stored on the server.
	// In this case write the full name into the lastname field. The admin has to correct this manually.
	// It is not possible to do this automaticallly because a user may have two forenames and one lastname or vice versa!
	if (($valueArray['ldap_forename'] == "" || $valueArray['ldap_lastname'] == "") && $valueArray['ldap_fullname'] != "")
		 $valueArray['ldap_lastname'] = $valueArray['ldap_fullname'];
	
	foreach ($AUTHCFG[fields] as $key => $input)
	{
		$value = $valueArray[$key];
		$sVal .= "\n$input\t$value";
	}

	// LDAP does not require to store a password, but it is a mandatory field -> store dummy password into mySql base
	$sVal .= "\nuser_password\tvte12345";
	$sVal .= "\nconfirm_password\tvte12345";
	
	//get default LDAP role
	$sVal .= "\nuser_role\t{$AUTHCFG[role]}";
	$rolename = getRoleName($AUTHCFG[role]);
	$sVal .= "\nrole_name\t$rolename";
	
	return "Values=" . $sVal;
}
//crmv@9010e
?>