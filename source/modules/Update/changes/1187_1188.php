<?php

global $adb, $table_prefix;

/* crmv@82770 */
/* Aggiornamento libreria grafici: ChartJS */

// hide unsupported fields
$res = $adb->pquery("UPDATE {$table_prefix}_field SET readonly = 100 WHERE tabid = ? AND fieldname IN (?)", array(getTabid('Charts'), 'chart_exploded'));

// restore removed field (not needed)
//$res = $adb->pquery("UPDATE {$table_prefix}_field SET readonly = 1 WHERE tabid = ? AND fieldname IN (?,?)", array(getTabid('Charts'), 'chart_values', 'chart_labels'));


// remove chart type 3D pie and 3D ring
$res = $adb->pquery("UPDATE {$table_prefix}_charts SET chart_type = ? WHERE chart_type = ?", array('Pie', 'Pie3D'));
$res = $adb->pquery("UPDATE {$table_prefix}_charts SET chart_type = ? WHERE chart_type = ?", array('Ring', 'Ring3D'));

// get the picklistid
$res = $adb->pquery("select picklist_valueid from {$table_prefix}_chart_type where chart_type = ?", array('Pie3D'));
$picklistid = $adb->query_result_no_html($res, 0, "picklist_valueid");
if ($picklistid > 0) {
	// delete
	$adb->pquery("delete from {$table_prefix}_chart_type where chart_type = ?", array('Pie3D'));
	$adb->pquery("delete from {$table_prefix}_role2picklist where picklistvalueid = ?", array($picklistid));
}

// get the picklistid
$res = $adb->pquery("select picklist_valueid from {$table_prefix}_chart_type where chart_type = ?", array('Ring3D'));
$picklistid = $adb->query_result_no_html($res, 0, "picklist_valueid");
if ($picklistid > 0) {
	// delete
	$adb->pquery("delete from {$table_prefix}_chart_type where chart_type = ?", array('Ring3D'));
	$adb->pquery("delete from {$table_prefix}_role2picklist where picklistvalueid = ?", array($picklistid));
}

// add vtetheme schema color and set it as default for existing bar charts
$result = $adb->pquery("SELECT picklist_valueid FROM {$table_prefix}_chart_palette WHERE chart_palette = ?", array('vtetheme'));
if ($result && $adb->num_rows($result) == 0) {
	$fieldInstance = Vtiger_Field::getInstance('chart_palette',Vtiger_Module::getInstance('Charts'));
	$fieldInstance->setPicklistValues(array('vtetheme'));
}
$res = $adb->pquery("UPDATE {$table_prefix}_charts SET chart_palette = ? WHERE chart_type IN (?,?)", array('vtetheme', 'BarVertical', 'BarHorizontal'));

// and set some languages
$trans = array(
	'Charts' => array(
		'it_it' => array(
			'vtetheme' => 'Tema VTE',
		),
		'en_us' => array(
			'vtetheme' => 'VTE Theme',
		),
	),
);


foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}
