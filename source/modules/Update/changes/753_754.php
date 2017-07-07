<?php
global $adb, $table_prefix;

$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));

$res = $adb->pquery("select * from {$table_prefix}_ws_operation where name = ?", array('getmenulist'));
if ($res && $adb->num_rows($res) == 0) {
	vtws_addWebserviceOperation('getmenulist', 'include/Webservices/MenuList.php', 'vtws_getmenulist', 'GET');
}

?>