<?php
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';

global $adb;
$result = $adb->query('SELECT id,start_hour FROM vtiger_users');
while($row = $adb->fetchByAssoc($result)) {
	if ($row['start_hour'] == '') {
		$adb->pquery('update vtiger_users set start_hour = ? where id = ?',array('08:00',$row['id']));
	}
}
?>