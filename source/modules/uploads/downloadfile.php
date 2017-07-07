<?php
/********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

/* crmv@95157 */

require_once('modules/Documents/storage/StorageBackendUtils.php');

$attachmentsid = intval($_REQUEST['fileid']);
$entityid = intval($_REQUEST['entityid']);
$returnmodule = $_REQUEST['return_module'];

if (isPermitted($returnmodule, 'DetailView', $entityid) != 'yes') {
	die('Not permitted');
}

$SBU = StorageBackendUtils::getInstance();
$SBU->downloadFile($returnmodule, $entityid, $attachmentsid);

