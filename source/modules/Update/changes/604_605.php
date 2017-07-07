<?php
global $adb, $table_prefix;

$_SESSION['modules_to_update']['Charts'] = 'packages/vte/mandatory/Charts.zip';


if (isModuleInstalled('Charts')) {
	$cfolders = getEntityFoldersByName('Charts', 'Charts');
	if (empty($cfolders)) {
		addEntityFolder('Charts', 'Default', '', 1);
	}
}


?>