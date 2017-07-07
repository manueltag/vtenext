<?php

// crmv@120138
// fix ids for modulehome demo blocks

if (Vtiger_Utils::CheckTable($table_prefix.'_modulehome_blocks')) {
	$res = $adb->pquery("SELECT cvid FROM {$table_prefix}_customview WHERE viewname = ? AND entitytype = ?", array('All', 'HelpDesk'));
	if ($res && $adb->num_rows($res) > 0) {
		$cvidHelpdesk = intval($adb->query_result_no_html($res, 0, 'cvid'));
		$params = array('{"cvid":'.$cvidHelpdesk.'}', 1,'Filter', '{"cvid":7}');
		$adb->pquery("UPDATE {$table_prefix}_modulehome_blocks SET config = ? WHERE modhomeid = ? AND type = ? AND config = ?", $params);
	}

	$res = $adb->pquery("SELECT cvid FROM {$table_prefix}_customview WHERE viewname = ? AND entitytype = ?", array('All', 'Potentials'));
	if ($res && $adb->num_rows($res) > 0) {
		$cvidPotentials = intval($adb->query_result_no_html($res, 0, 'cvid'));
		$params = array('{"cvid":'.$cvidPotentials.'}', 3,'Filter', '{"cvid":4}');
		$adb->pquery("UPDATE {$table_prefix}_modulehome_blocks SET config = ? WHERE modhomeid = ? AND type = ? AND config = ?", $params);
	}
}
