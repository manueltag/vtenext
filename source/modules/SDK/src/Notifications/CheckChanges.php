<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@OPER5904 */
$plugin = vtlib_purify($_REQUEST['plugin']);
if (strpos($plugin,',') !== false) {
	$plugins = explode(',',$plugin);
} else {
	$plugins = array($plugin);
}
$res = array();
foreach ($plugins as $plugin) {
	ob_start();
	include('modules/SDK/src/Notifications/plugins/'.$plugin.'CheckChanges.php');
	$count = ob_get_contents();
	ob_end_clean();
	$res[$plugin] = $count;
}
echo Zend_Json::encode($res);
exit;
?>