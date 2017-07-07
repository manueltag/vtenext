<?php
global $adb, $table_prefix;

//crmv@69922
// add the pdfmaker widget, since sometimes is not installed
if (isModuleInstalled('PDFMaker')) {
	$result1 = $adb->query("SELECT module FROM ".$table_prefix."_pdfmaker GROUP BY module");
	while ($row = $adb->fetchByAssoc($result1, -1, false)) {
		$relModuleInstance = Vtiger_Module::getInstance($row["module"]);
		if ($relModuleInstance && $relModuleInstance->id > 0) {
			Vtiger_Link::addLink($relModuleInstance->id, 'LISTVIEWBASIC', 'PDF Export', "getPDFListViewPopup2(this,'$"."MODULE$');", '', 1);
			Vtiger_Link::addLink($relModuleInstance->id, 'DETAILVIEWWIDGET', 'PDFMaker', "module=PDFMaker&action=PDFMakerAjax&file=getPDFActions&record=$"."RECORD$", '', 1);
		}
	}
}
//crmv@69922e