<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
********************************************************************************/
require_once('Smarty_setup.php');
$smarty = new VTECRM_Smarty();
$block = 'HelpDesk';

(file_exists("$block/header.html")) ? $header = "$block/header.html" : $header = 'VteCore/header.html';
include($header);

$showmodule = array();
// Look if we have the information already
if(isset($_SESSION['__permitted_modules'])) {
	$showmodule = $_SESSION['__permitted_modules'];
} else {
	// Get the information from server
	$params = array();
	$showmodule = $client->call('get_modules',$params,$Server_path,$Server_path);
	// Store for further use.
	$_SESSION['__permitted_modules'] = $showmodule;
}

$smarty->assign("SHOWMODULE", $showmodule);

(file_exists("$block/footer.html")) ? $footer = "$block/footer.html" : $footer = 'VteCore/footer.html';
include($footer);

$smarty->assign("CUSTERMID", $customerid);

$smarty->display('Welcome.tpl');
?>