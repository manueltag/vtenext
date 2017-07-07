<?php
global $adb, $table_prefix;

//crmv@78395
$result = $adb->pquery("SELECT * FROM {$table_prefix}_field WHERE fieldname = ?", array('bu_mc'));
if ($result && $adb->num_rows($result) > 0) {
	/*
	SDK::setUitype(134,'modules/SDK/src/BUMC/134.php','modules/SDK/src/BUMC/134.tpl','','picklist');
	
	$inventoryModules = getInventoryModules();
	$result = $adb->pquery("UPDATE {$table_prefix}_field
		INNER JOIN {$table_prefix}_tab ON {$table_prefix}_tab.tabid = {$table_prefix}_field.tabid
		SET uitype = ?
		WHERE fieldname = ? AND {$table_prefix}_tab.name IN (".generateQuestionMarks($inventoryModules).")", array('134','bu_mc',$inventoryModules));
	*/
	echo "<br>\nAggiornare il pacchetto BUMC, registrare l'uitype 134 e impostarlo per i campi bu_mc dei moduli Inventory. Qui commentato il codice di aggiornamento.<br>\n";
}
//crmv@78395e
?>