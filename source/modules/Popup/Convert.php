<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@44609 */

global $app_strings;

$from_module = vtlib_purify($_REQUEST['from_module']);
$from_crmid = intval($_REQUEST['from_crmid']);
$to_module = vtlib_purify($_REQUEST['to_module']);
$to_crmid = intval($_REQUEST['to_crmid']);

if (isPermitted($from_module, 'Delete', $from_crmid) != 'yes') die($app_strings['LBL_PERMISSION']);;
if (isPermitted($to_module, 'EditView', $to_crmid) != 'yes') die($app_strings['LBL_PERMISSION']);;

$rm = RelationManager::getInstance();
$relatedIds = $rm->getRelatedIds($from_module, $from_crmid, array(), array('ChangeLog'), false, true);
if (!empty($relatedIds)) {
	foreach ($relatedIds as $mod => $ids) {
		$rm->relate($to_module, $to_crmid, $mod, $ids);
	}
}

$from_focus = CRMEntity::getInstance($from_module);
$from_focus->trash($from_module, $from_crmid);

die('SUCCESS');
?>