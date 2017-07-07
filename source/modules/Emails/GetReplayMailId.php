<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

//crmv@2043m

global $adb, $onlyquery,$table_prefix;
$record = '';
$focus = CRMEntity::getInstance('HelpDesk');
if ($_REQUEST['user'] == 'mailconverter') {
	$focus->retrieve_entity_info($_REQUEST['record'],'HelpDesk');
	if ($focus->column_fields['helpdesk_from'] == '') {
		die('helpdesk_from_empty');
	}
}
$onlyquery = true;
$focus->get_messages_list($_REQUEST['record'], 13, getTabid('Messages'));
$onlyquery = false;
$query = substr($_SESSION['messages_listquery'],0,strpos($_SESSION['messages_listquery'],'ORDER BY')).' ORDER BY '.$table_prefix.'_crmentity.crmid DESC';
$result = $adb->limitQuery($query,0,1);
if ($result && $adb->num_rows($result) > 0) {
	$record = $adb->query_result($result,0,'crmid');
}
echo $record;
exit;
?>