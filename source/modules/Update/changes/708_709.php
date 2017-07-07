<?php
$_SESSION['modules_to_update']['M'] = 'packages/vte/mandatory/M.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));

SDK::setLanguageEntries('Settings', 'LBL_ERR_DELETE_MAIL_TEMPLATE_NEWSLETTER', array('it_it'=>'Uno o più template selezionati sono collegati a Newsletter. L\'eliminazione potrebbe causare problemi durante l\'invio delle mail. Vuoi procedere comunque?','en_us'=>'Some templates are linked to newsletters. Delete them might produce problems. Do you want to proceed?','pt_br'=>'Alguns modelos estão ligados a newsletters. Excluí-los podem produzir problemas. Você quer continuar?'));

global $adb, $table_prefix;
$result = $adb->query("SELECT id FROM crmv_potential_line_rel WHERE listprice > 0");
if ($result && $adb->num_rows($result) > 0) {
	//do nothing
} else {
	$sqlarray = $adb->datadict->DropTableSQL('crmv_potential_line_rel');
	$adb->datadict->ExecuteSQLArray($sqlarray);
}
$result = $adb->query("SELECT product_linesid FROM {$table_prefix}_product_lines");
if ($result && $adb->num_rows($result) > 0) {
	//do nothing
} else {
	$sqlarray = $adb->datadict->DropTableSQL("{$table_prefix}_product_lines");
	$adb->datadict->ExecuteSQLArray($sqlarray);
}
$result = $adb->query('SELECT id FROM crmv_budget');
if ($result && $adb->num_rows($result) > 0) {
	//do nothing
} else {
	$sqlarray = $adb->datadict->DropTableSQL('crmv_budget');
	$adb->datadict->ExecuteSQLArray($sqlarray);
}
$adb->pquery("DELETE FROM {$table_prefix}_settings_field WHERE name = ?",array('LBL_REPORT_TITLE'));
$adb->pquery("DELETE FROM {$table_prefix}_crmentityfolder WHERE foldername = ?",array('LBL_REPORT_FOLDER_PRODLINES'));
@unlink('modules/Settings/ProductLines.php');
@unlink('Smarty/templates/Settings/ProductLines.tpl');
@unlink('themes/images/report_icon.gif');
@unlink('themes/images/report_icon_small.png');
@unlink('include/utils/vtigerstudio_budget.php');
@unlink('modules/Dashboard/BudgetHome.php');

$translations = array(
	'APP_STRINGS'=>array('LBL_LINE_DETAILS','LBL_LINE_NAME','LBL_ADD_LINE','LBL_PRODUCT_LINES','LBL_PRODUCT_LINES_REPORT'),
	'ALERT_ARR'=>array('PRODUCT_LINE_NAME','BUDGET_VALUE'),
	'Dashboard'=>array('LBL_BUDGET_TITLE','LBL_BUDGET','LBL_EXPORTXL_BUTTON','LBL_P_LINE','LBL_BUDG','LBL_TOT_OPP','LBL_CHOOSE_YEAR'),
	'Settings'=>array('LBL_REPORT_PROG','LBL_REPORT_TITLE','LBL_ADD_LINE_BUTTON','LBL_PRODUCT_LINE_SETTINGS','LBL_NO_LINE_AVAILABLE','PRODUCT_LINE_NAME','BUDGET_VALUE','PRODUCT_LINE_ENDIS','LBL_ALREADY','LBL_ERR_REP','LBL_PROD_DESC'),
);
foreach($translations as $module => $trans) {
	foreach($trans as $label) {
		SDK::deleteLanguageEntry($module,'',$label);
	}
}

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
addColumnToTable($table_prefix.'_links', 'status', 'I(1)', 'DEFAULT 1');
addColumnToTable($table_prefix.'_links', 'cond', 'C(200)');
addColumnToTable('sdk_menu_fixed', 'cond', 'C(200)');
addColumnToTable('sdk_menu_contestual', 'cond', 'C(200)');

@unlink('modules/Users/SaveMenuView.php');
?>