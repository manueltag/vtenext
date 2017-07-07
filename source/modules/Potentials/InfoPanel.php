<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@44187 crmv@44323 */

global $adb, $table_prefix;
global $currentModule, $current_user;

$focus = CRMEntity::getInstance($currentModule);
$record = intval($_REQUEST['record']);

if ($record > 0) {
	$focus->retrieve_entity_info($record, $currentModule);
	$focus->id = $record;
}

echo $focus->getExtraDetailBlock();
?>