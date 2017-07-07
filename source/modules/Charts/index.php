<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $currentModule;

// crmv@30967
checkFileAccess("modules/$currentModule/ListViewFolder.php");
include_once("modules/$currentModule/ListViewFolder.php");
// crmv@30967e
?>
