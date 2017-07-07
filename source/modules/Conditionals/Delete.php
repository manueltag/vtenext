<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is crmvillage.biz.
 * Portions created by crmvillage.biz are Copyright (C) crmvillage.biz.
 * All Rights Reserved.
 *******************************************************************************/
if($_REQUEST['ruleid'] != "") {
	$tabid = getTabid($_REQUEST['chk_module']);
	$delete = "delete from tbl_s_conditionals where ruleid = ".$_REQUEST['ruleid'];
	$result = $adb->query($delete);
}

// crmv@77249
if ($_REQUEST['included'] == true) {
	$params = array(
		'included' => 'true',
		'skip_vte_header' => 'true',
		'skip_footer' => 'true',
		'formodule' => $_REQUEST['formodule'],
		'statusfield' => $_REQUEST['statusfield']
	);
	$otherParams = "&".http_build_query($params);
}
// crmv@77249e

header("Location: index.php?module=Conditionals&action=index&parenttab=Settings".$otherParams);
