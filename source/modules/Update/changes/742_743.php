<?php
global $adb, $table_prefix;

$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';
$_SESSION['modules_to_update']['Charts'] = 'packages/vte/mandatory/Charts.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['Mobile'] = 'packages/vte/mandatory/Mobile.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));
$_SESSION['modules_to_update']['PBXManager'] = 'packages/vte/mandatory/PBXManager.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan','ProjectMilestone','ProjectTask'));
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';
$_SESSION['modules_to_update']['WSAPP'] = 'packages/vte/mandatory/WSAPP.zip';


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

if (!function_exists('getPrimaryKeyName')) {
	function getPrimaryKeyName($tablename) {
		global $adb, $dbconfig;
		$ret = '';
		if ($adb->isMysql()) {
			// for mysql just check if it exists
			$res = $adb->query("SHOW KEYS FROM {$tablename} WHERE Key_name = 'PRIMARY'");
			if ($res && $adb->num_rows($res) > 0) $ret = 'PRIMARY';
		} elseif ($adb->isMssql()) {
			$res = $adb->pquery("SELECT CONSTRAINT_NAME as cn from INFORMATION_SCHEMA.TABLE_CONSTRAINTS where CONSTRAINT_CATALOG = ? and TABLE_NAME = ? and CONSTRAINT_TYPE = 'PRIMARY KEY'", array($dbconfig['db_name'], $tablename));
			if ($res) $ret = $adb->query_result_no_html($res, 0, 'cn');
		} elseif ($adb->isOracle()) {
			$res = $adb->pquery("SELECT CONSTRAINT_NAME as cn FROM all_constraints cons     WHERE cons.table_name = ? AND cons.constraint_type = 'P'", array(strtoupper($tablename)));
			if ($res) $ret = $adb->query_result_no_html($res, 0, 'cn');
		}
		return $ret;
	}
}

if (!function_exists('dropPrimaryKey')) {
	function dropPrimaryKey($tablename) {
		global $adb;
		if ($adb->isMysql()) {
			$keyname = getPrimaryKeyName($tablename);
			if ($keyname == 'PRIMARY') $adb->query("ALTER TABLE {$tablename} DROP PRIMARY KEY");
		} elseif ($adb->isMssql() || $adb->isOracle()) {
			$keyname = getPrimaryKeyName($tablename);
			$adb->query("ALTER TABLE {$tablename} DROP CONSTRAINT {$keyname}");
		} else {
			echo "Drop Primary key not supported for this database";
		}
	}
}

SDK::clearSessionValues();

// add columns for functions
addColumnToTable("{$table_prefix}_selectcolumn", 'functions', 'C(127)');

// add column in senotesrel
addColumnToTable("{$table_prefix}_senotesrel", 'relmodule', 'C(127)');

// and and index
$sql = $adb->datadict->CreateIndexSQL('senotesrel_relmodule_idx', $table_prefix.'_senotesrel', 'relmodule');
if ($sql) @$adb->datadict->ExecuteSQLArray($sql);

// and now populate the column
$res = $adb->query("select s.crmid,s.notesid, c.setype from {$table_prefix}_senotesrel s inner join {$table_prefix}_crmentity c on c.crmid = s.crmid where c.deleted = 0");
if ($res) {
	while ($row = $adb->FetchByAssoc($res, -1, false)) {
		$setype = $row['setype'];
		if (!empty($setype)) {
			$adb->pquery("update {$table_prefix}_senotesrel set relmodule = ? where crmid = ? and notesid = ?", array($setype, $row['crmid'], $row['notesid']));
		}
	}
}
// and delete orphaned documents
$adb->query("delete from {$table_prefix}_senotesrel where relmodule is null");


// change uitype for campaigns
$campid = GetTabid('Campaigns');
if ($campid > 0) {
	$adb->pquery("update {$table_prefix}_field set uitype = 71 where tabid = ? and fieldname in (?,?,?)", array($campid, array('expectedrevenue', 'budgetcost', 'actualcost')));
}

// be sure the related campaigns uses the new target system
$adb->pquery("UPDATE {$table_prefix}_relatedlists SET name = ? WHERE related_tabid = ? AND tabid IN (4,6,7)", array('get_campaigns_newsletter', getTabid('Campaigns')));

// add modules to hide tab for reports
$hide_mods = array('Messages', 'Emails', 'Events', 'Fax', 'Sms', 'Charts', 'PBXManager');
foreach ($hide_mods as $hmod) {
	$htabid = getTabid($hmod);
	if ($htabid > 0) {
		// check for existence
		$res = $adb->pquery("select tabid from vte_hide_tab where tabid = ?", array($htabid));
		if ($res && $adb->num_rows($res) > 0) {
			// update
			$adb->pquery("update vte_hide_tab set hide_report = ? where tabid = ?", array(1, $htabid));
		} else {
			// insert
			$adb->pquery("insert into vte_hide_tab (tabid, hide_report) values (?,?)", array($htabid, 1));
		}
	}
}

// fix custom modules (not custom)
$notCustom = array('Messages', 'Touch', 'M', 'Mobile', 'WSAPP');
$adb->pquery("update {$table_prefix}_tab set customized = '0' where name in (".generateQuestionMarks($notCustom).")", $notCustom);

// indexes in crmentityrel
$sql = $adb->datadict->CreateIndexSQL('crmentityrel_crmid_idx', $table_prefix.'_crmentityrel', 'crmid');
if ($sql) @$adb->datadict->ExecuteSQLArray($sql);
$sql = $adb->datadict->CreateIndexSQL('crmentityrel_module_idx', $table_prefix.'_crmentityrel', 'module');
if ($sql) @$adb->datadict->ExecuteSQLArray($sql);
$sql = $adb->datadict->CreateIndexSQL('crmentityrel_relcrmid_idx', $table_prefix.'_crmentityrel', 'relcrmid');
if ($sql) @$adb->datadict->ExecuteSQLArray($sql);
$sql = $adb->datadict->CreateIndexSQL('crmentityrel_relmodule_idx', $table_prefix.'_crmentityrel', 'relmodule');
if ($sql) @$adb->datadict->ExecuteSQLArray($sql);

// chiave primaria (disable die on error)
$oldDieOnError = $adb->dieOnError;
$adb->dieOnError = false;
$adb->query("ALTER TABLE {$table_prefix}_crmentityrel ADD PRIMARY KEY (crmid, relcrmid)");
$adb->dieOnError = $oldDieOnError;


// add column in inventoryproductrel
addColumnToTable("{$table_prefix}_inventoryproductrel", 'relmodule', 'C(127)');

// and and index
$sql = $adb->datadict->CreateIndexSQL('inventoryproductrel_relmodule_idx', $table_prefix.'_inventoryproductrel', 'relmodule');
if ($sql) @$adb->datadict->ExecuteSQLArray($sql);

// and now populate the column
$res = $adb->query("select s.lineitem_id, c.setype from {$table_prefix}_inventoryproductrel s inner join {$table_prefix}_crmentity c on c.crmid = s.id");
if ($res) {
	while ($row = $adb->FetchByAssoc($res, -1, false)) {
		$setype = $row['setype'];
		if (!empty($setype)) {
			$adb->pquery("update {$table_prefix}_inventoryproductrel set relmodule = ? where lineitem_id = ?", array($setype, $row['lineitem_id']));
		}
	}
}

// DELETE FILES:
@unlink('modules/Accounts/updateRelations.php');
@unlink('modules/Assets/updateRelations.php');
@unlink('modules/Campaigns/updateRelations.php');
@unlink('modules/ChangeLog/updateRelations.php');
@unlink('modules/Charts/updateRelations.php');
@unlink('modules/Contacts/updateRelations.php');
@unlink('modules/Ddt/updateRelations.php');
@unlink('modules/Documents/updateRelations.php');
@unlink('modules/Emails/updateRelations.php');
@unlink('modules/Faq/updateRelations.php');
@unlink('modules/HelpDesk/updateRelations.php');
@unlink('modules/Invoice/updateRelations.php');
@unlink('modules/Leads/updateRelations.php');
@unlink('modules/Messages/updateRelations.php');
@unlink('modules/ModComments/updateRelations.php');
@unlink('modules/ModNotifications/updateRelations.php');
@unlink('modules/Newsletter/updateRelations.php');
@unlink('modules/Products/updateRelations.php');
@unlink('modules/ProjectMilestone/updateRelations.php');
@unlink('modules/ProjectPlan/updateRelations.php');
@unlink('modules/ProjectTask/updateRelations.php');
@unlink('modules/PurchaseOrder/updateRelations.php');
@unlink('modules/Quotes/updateRelations.php');
@unlink('modules/SalesOrder/updateRelations.php');
@unlink('modules/ServiceContracts/updateRelations.php');
@unlink('modules/Services/updateRelations.php');
@unlink('modules/Targets/updateRelations.php');
@unlink('modules/Telemarketing/updateRelations.php');
@unlink('modules/Timecards/updateRelations.php');
@unlink('modules/Visitreport/updateRelations.php');
@unlink('vtlib/ModuleDir/5.0/updateRelations.php');

// traduzioni
$trans = array(
	'Reports' => array(
		'it_it' => array(
			'LBL_FOLDER_HAS_REPORTS' => 'Alcune cartelle contengono dei report',
			'LBL_REP_EXTRACT_YEAR' => 'Anno',
			'LBL_REP_EXTRACT_QUARTER' => 'Quadrimestre',
			'LBL_REP_EXTRACT_YEARMONTH' => 'Anno e mese',
			'LBL_REP_EXTRACT_MONTH' => 'Mese',
			'LBL_REP_EXTRACT_WEEK' => 'Settimana',
			'LBL_REP_EXTRACT_DAY' => 'Giorno',
			'FORMULA_FOR' => 'Formula per',
		),
		'en_us' => array(
			'LBL_FOLDER_HAS_REPORTS' => 'Some folders contain reports',
			'LBL_REP_EXTRACT_YEAR' => 'Year',
			'LBL_REP_EXTRACT_QUARTER' => 'Quarter',
			'LBL_REP_EXTRACT_YEARMONTH' => 'Year and month',
			'LBL_REP_EXTRACT_MONTH' => 'Month',
			'LBL_REP_EXTRACT_WEEK' => 'Week',
			'LBL_REP_EXTRACT_DAY' => 'Day',
			'FORMULA_FOR' => 'Function for',
		),
	),

	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_FOLDER_HAS_RECORDS' => 'Alcune cartelle contengono dei record',
		),
		'en_us' => array(
			'LBL_FOLDER_HAS_RECORDS' => 'Some folders contain records',
		),
	),

	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_MAX_REPORT_SECMODS' => 'Hai raggiunto il numero massimo di moduli secondari',
		),
		'en_us' => array(
			'LBL_MAX_REPORT_SECMODS' => 'You reached the maximum number of related modules',
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

?>