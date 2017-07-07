<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
*
 ********************************************************************************/


global $Server_Path;
global $Portal_Path;

//This is the vtiger server path ie., the url to access the vtiger server in browser
//Ex. i access my vtiger as http://mickie:90/vtiger/index.php so i will give as http://mickie:90/vtiger
$Server_Path = "";

//This is the customer portal path ie., url to access the customer portal in browser 
//Ex. i access my portal as http://mickie:90/customerportal/login.php so i will give as http://mickie:90/customerportal
$Authenticate_Path = "";

include('../config.inc.php');
global $site_URL;
if ($site_URL)
	$Server_Path = $site_URL;
global $PORTAL_URL;
if ($PORTAL_URL)
	$Authenticate_Path = $PORTAL_URL;	
	
//Give a temporary directory path which is used when we upload attachment
$upload_dir = '/tmp';

//These are the Proxy Settings parameters
$proxy_host = ''; //Host Name of the Proxy
$proxy_port = ''; //Port Number of the Proxy
$proxy_username = ''; //User Name of the Proxy
$proxy_password = ''; //Password of the Proxy

//The character set to be used as character encoding for all soap requests
$default_charset = 'UTF-8';//'ISO-8859-1';

$default_language = 'en_us';

/*crmv@57342*/
$languages = Array('en_us'=>'US English','it_it'=>'IT Italian');

$welcome_page = '';
/*crmv@57342e*/
?>