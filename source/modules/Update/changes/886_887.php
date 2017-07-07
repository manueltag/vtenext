<?php
global $adb, $table_prefix;

// check button duplicates
$res = $adb->pquery("SELECT id FROM sdk_menu_contestual WHERE title = ? AND module = ? AND action = ?",array('LBL_AREAS_SETTINGS','Area','index'));
if ($res && $adb->num_rows($res) > 1) {
	for($i=1;$i<$adb->num_rows($res);$i++) {
		$id = $adb->query_result($res,$i,'id');
		SDK::unsetMenuButton('contestual', $id);
	}
}
?>