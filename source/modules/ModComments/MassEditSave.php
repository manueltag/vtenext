<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $currentModule, $rstart;

// MassEdit is not enabled for this module!

$return_module = vtlib_purify($_REQUEST['massedit_module']);
$return_action = 'index';

if(isset($_REQUEST['start']) && $_REQUEST['start']!=''){
	$rstart = "&start=".vtlib_purify($_REQUEST['start']);
}

$parenttab = getParentTab();
header("Location: index.php?module=$return_module&action=$return_action&parenttab=$parenttab$rstart");
