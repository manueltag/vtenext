<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@44179 */

global $currentModule;
$status = 'SUCCESS';
$record = vtlib_purify($_REQUEST['record']);
$flag = vtlib_purify($_REQUEST['flag']);
$value = vtlib_purify($_REQUEST['value']);
$focus = CRMEntity::getInstance($currentModule);
$focus->id = $record;
$focus->retrieve_entity_info($record, $currentModule);
if ($flag == 'delete') {
	$focus->trash($currentModule,$record);
} else {
	$focus->setFlag($flag,$value) ? $status = 'SUCCESS' : $status = 'ERROR';
}
echo $status;
exit;
?>