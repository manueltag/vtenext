<?php
global $adb;
$sqlarray = $adb->datadict->RenameTableSQL('vtiger_pdfmaker_ignorepicklistvalues','vtiger_pdfmaker_ignpickvals');
$adb->datadict->ExecuteSQLArray($sqlarray);
$sqlarray = $adb->datadict->RenameTableSQL('vtiger_pdfmaker_productbloc_tpl','vtiger_pdfmaker_prodbloc_tpl');
$adb->datadict->ExecuteSQLArray($sqlarray);

$sql = "select * from vtiger_ws_fieldtype where uitype = ?";
$res = $adb->pquery($sql,Array(70));
if ($res && $adb->num_rows($res) == 0){
	$fieldtypeid = $adb->getUniqueID('vtiger_ws_fieldtype');
	$adb->pquery('INSERT INTO vtiger_ws_fieldtype(fieldtypeid,uitype,fieldtype)VALUES(?,?,?)',array($fieldtypeid,70,'datetime'));
}
?>