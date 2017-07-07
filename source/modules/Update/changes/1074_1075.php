<?php
global $adb,$table_prefix;
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['FieldFormulas'] = 'packages/vte/mandatory/FieldFormulas.zip';
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';

//crmv@73751
$result = $adb->query("SELECT * FROM {$table_prefix}_selectcolumn WHERE columnname LIKE '{$table_prefix}_inventorytotals:%'");
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByASsoc($result)) {
		$queryid = $row['queryid'];
		$columnindex = $row['columnindex'];
		$columnname = $row['columnname'];
		$tmp = explode(':',$columnname);
		$module = explode('_',$tmp[2]);
		$tmp[0] = $tmp[0].$module[0];
		$columnname = implode(':',$tmp);
		$adb->pquery("UPDATE {$table_prefix}_selectcolumn SET columnname = ? WHERE queryid = ? AND columnindex = ?", array($columnname, $queryid, $columnindex));
	}
}

$result = $adb->query("SELECT * FROM {$table_prefix}_reportsummary
	INNER JOIN {$table_prefix}_reportmodules ON {$table_prefix}_reportsummary.reportsummaryid = {$table_prefix}_reportmodules.reportmodulesid
	WHERE columnname LIKE '%:{$table_prefix}_inventorytotals:%'");
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByASsoc($result)) {
		$tmp = explode(':',$row['columnname']);
		$tmp[1] = $tmp[1].$row['primarymodule'];
		$columnname = implode(':',$tmp);
		$adb->pquery("UPDATE {$table_prefix}_reportsummary SET columnname = ? WHERE reportsummaryid = ? AND summarytype = ? AND columnname = ?", array($columnname, $row['reportsummaryid'], $row['summarytype'], $row['columnname']));
	}
}
//crmv@73751e

?>