<?php
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  crmvillage.biz CRM Open Source
 * The Initial Developer of the Original Code is crmvillage.biz.
 * Portions created by crmvillage.biz are Copyright (C) crmvillage.biz.
 * All Rights Reserved.
 *
 ********************************************************************************/

require_once('include/utils/utils.php');
require_once('Smarty_setup.php');

$mode = $_REQUEST['mode'];

if($mode == 'Ajax' && !empty($_REQUEST['xmode'])) {
	$mode = $_REQUEST['xmode'];
}

switch ($mode){
	case 'scannow':
		global $root_directory;
		chdir($root_directory.'/plugins/mailconnector/');
		include('mailconnector.php');
	break;	
	case 'edit':
		include('plugins/mailconnector/interface/edit.php');
	break;
	case 'save_server':
		include('plugins/mailconnector/interface/save_server.php');
	break;
	case 'add_mail':
		include('plugins/mailconnector/interface/add_mail.php');
	break;
	case 'save_account':
		include('plugins/mailconnector/interface/save_account.php');
	break;
	case 'delete_account':
		include('plugins/mailconnector/interface/delete_account.php');
	break;
	default:
		include('plugins/mailconnector/interface/detail.php');
	break;
}
die;
?>