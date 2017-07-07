<?php

if (!function_exists('moveFieldAfter')) {
// SPOSTAMENTO CAMPO
function moveFieldAfter($module, $field, $afterField) {
	global $adb, $table_prefix;
	
	$tabid = getTabid($module);
	if (empty($tabid)) return;
	
	$res = $adb->pquery("SELECT fieldid, sequence FROM {$table_prefix}_field WHERE tabid = ? AND fieldname = ?", array($tabid, $field));
	if ($res && $adb->num_rows($res) > 0) {
		$fieldid1 = intval($adb->query_result_no_html($res, 0, 'fieldid'));
		$sequence1 = intval($adb->query_result_no_html($res, 0, 'sequence'));
	}
	
	$res = $adb->pquery("SELECT fieldid, sequence FROM {$table_prefix}_field WHERE tabid = ? AND fieldname = ?", array($tabid, $afterField));
	if ($res && $adb->num_rows($res) > 0) {
		$fieldid2 = intval($adb->query_result_no_html($res, 0, 'fieldid'));
		$sequence2 = intval($adb->query_result_no_html($res, 0, 'sequence'));
	}
	
	if ($fieldid1 > 0 && $fieldid2 > 0) {
		// get the ids to update
		$updateIds = array();
		$res = $adb->pquery("SELECT fieldid FROM {$table_prefix}_field WHERE tabid = ? AND sequence > ?", array($tabid, $sequence2));
		if ($res && $adb->num_rows($res) > 0) {
			while ($row = $adb->fetchByAssoc($res)) {
				$updateIds[] = intval($row['fieldid']);
			}
		}
		if (count($updateIds) > 0) {
			$adb->pquery("UPDATE {$table_prefix}_field set sequence = sequence + 1 WHERE fieldid IN (".generateQuestionMarks($updateIds).")", $updateIds);
		}
		$adb->pquery("UPDATE {$table_prefix}_field set sequence = ? WHERE tabid = ? AND fieldid = ?", array($sequence2+1, $tabid, $fieldid1));
	}
	
}
}

// crmv@101930

SDK::setUitype(214, 'modules/SDK/src/214/214.php', 'modules/SDK/src/214/214.tpl', '', 'datetime');

// add the field
$fields = array(
	'user_and_time'	=> array('module'=>'MyNotes', 'block'=>'LBL_MYNOTES_INFORMATION',	'name'=>'user_and_time',	'label'=>'Modified Time',		'table'=>"{$table_prefix}_crmentity", 	'column' => 'modifiedtime',	'typeofdata'=>'T~O', 	'uitype'=>214, 'readonly'=>99, 'masseditable'=>0, 'quickcreate'=>0, 'displaytype' => 2),
);

$fieldRet = Update::create_fields($fields);


moveFieldAfter('MyNotes', 'user_and_time', 'modifiedtime');

// hide the modifiedtime
$adb->pquery("UPDATE {$table_prefix}_field SET quickcreate = ? WHERE tabid = ? AND fieldname = ?", array(1, getTabid('MyNotes'), 'modifiedtime'));
