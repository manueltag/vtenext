<?php
global $adb, $table_prefix;

SDK::setLanguageEntries('Settings', 'LBL_FORCE_CHECK_RELATED_TO', array('it_it'=>'Forza controllo su Collegato a','en_us'=>'Force check to Related To','pt_br'=>'Verificaчуo de forчa para a Relacionado a','de_de'=>'Kraft Scheck an bezogen auf','nl_nl'=>'Kracht controle naar gekoppeld aan'));

if (!function_exists('addColumnToTable')) {
	function addColumnToTable($tablename, $columnname, $type, $extra = '') {
		global $adb;

		// check if already present
		$cols = $adb->getColumnNames($tablename);
		if (in_array($columnname, $cols)) {
			return;
		}

		$col = $columnname.' '.$type.' '.$extra;
		$adb->alterTable($tablename, $col, 'Add_Column');
	}
}
// add column license_id to vte_version
addColumnToTable($table_prefix.'_mailscanner_rules', 'compare_parentid', 'I(1)', 'DEFAULT 0');

if ($adb->isMssql()) {
	$adb->pquery("UPDATE r
	SET r.compare_parentid = ?
	FROM {$table_prefix}_mailscanner_rules r
	INNER JOIN {$table_prefix}_mailscanner_ruleactions ra ON r.ruleid = ra.ruleid
	INNER JOIN {$table_prefix}_mailscanner_actions a ON a.actionid = ra.actionid
	WHERE a.actiontype = ?", array(1,'UPDATE'));
} else {	
	$adb->pquery("UPDATE {$table_prefix}_mailscanner_rules r
	INNER JOIN {$table_prefix}_mailscanner_ruleactions ra ON r.ruleid = ra.ruleid
	INNER JOIN {$table_prefix}_mailscanner_actions a ON a.actionid = ra.actionid
	SET r.compare_parentid = ?
	WHERE a.actiontype = ?", array(1,'UPDATE'));
}

$adb->pquery("UPDATE {$table_prefix}_links SET linkicon = ?, cond = ? WHERE linktype = ? AND linklabel IN (?,?)",
	array('themes/images/reply_min.png','checkMailScannerInfoRule:include/utils/crmv_utils.php','DETAILVIEWBASIC','Rispondi via mail','Rispondi via mail (info)'));
?>