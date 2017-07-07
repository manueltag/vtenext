<?php
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';

global $adb, $table_prefix;

$result = $adb->pquery("SELECT tabid FROM {$table_prefix}_tab WHERE name = ?",array('Visitreport'));
if ($result && $adb->num_rows($result) > 0) {
	$adb->pquery("UPDATE {$table_prefix}_field SET fieldlabel = ? WHERE tabid = ? and fieldname = ?",array('Related To',$adb->query_result($result,0,'tabid'),'accountid'));
	SDK::setLanguageEntries('Visitreport', 'Related To', array('it_it'=>'Collegato a','en_us'=>'Related To'));
}

$result = $adb->query("SELECT relation_id, name FROM {$table_prefix}_relatedlists WHERE tabid = 7 AND related_tabid = 26");
if ($result && $adb->num_rows($result) > 0) {
	$relation_id = $adb->query_result($result,0,'relation_id');
	$method = $adb->query_result($result,0,'name');
	SDK::setTurboliftCount($relation_id, $method);
}

SDK::setLanguageEntry('Settings', 'it_it', 'LBL_LAYOUT_EDITOR_DESCRIPTION', 'Reinventa e modifica il layout di ciascun modulo');
@unlink('modules/PDFMaker/pdfmaker_vte.pdf');
?>