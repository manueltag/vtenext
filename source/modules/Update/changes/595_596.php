<?php
/* crmv@30014 - parte 1 (solo patch) */
/* patch varie per la gestione dei grafici */
global $adb, $table_prefix;

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

// uitypes
SDK::setUitype(206, 'modules/SDK/src/206/206.php', 'modules/SDK/src/206/206.tpl', 'modules/SDK/src/206/206.js', 'integer');

// colonna per i pannelli in home page
addColumnToTable($table_prefix.'_homestuff', 'size', 'I(19)', 'DEFAULT 0');


?>