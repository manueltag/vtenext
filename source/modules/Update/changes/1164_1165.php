<?php

$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

global $adb, $table_prefix;

/* crmv@91579 */
$adb->query("UPDATE {$table_prefix}_users SET user_hash = ''");


/* crmv@91571 */

// create the table for the massedit queue
$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_massedit">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="massid" type="I" size="19">
				<key/>
			</field>
			<field name="userid" type="I" size="19">
				<NOTNULL/>
			</field>
			<field name="module" type="C" size="63">
				<NOTNULL/>
			</field>
			<field name="inserttime" type="T">
				<NOTNULL/>
				<DEFAULT value="0000-00-00 00:00:00"/>
			</field>
			<field name="starttime" type="T">
				<NOTNULL/>
				<DEFAULT value="0000-00-00 00:00:00"/>
			</field>
			<field name="endtime" type="T">
				<NOTNULL/>
				<DEFAULT value="0000-00-00 00:00:00"/>
			</field>
			<field name="workflows" type="I" size="1">
				<NOTNULL/>
				<DEFAULT value="1"/>
			</field>
			<field name="status" type="I" size="3">
				<NOTNULL/>
				<DEFAULT value="0"/>
			</field>
			<field name="fieldvalues" type="XL" />
			<field name="results" type="XL" />
			<index name="massedit_status_idx">
				<col>status</col>
			</index>
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_massedit')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}

$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_massedit_queue">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="massid" type="I" size="19">
				<key/>
			</field>
			<field name="crmid" type="I" size="19">
				<key/>
			</field>
			<field name="status" type="I" size="3">
				<NOTNULL/>
				<DEFAULT value="0"/>
			</field>
			<field name="info" type="C" size="255" />
			<index name="massedit_queue_status_idx">
				<col>status</col>
			</index>
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_massedit_queue')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}

// add massedit cronjob
require_once('include/utils/CronUtils.php');
$cronname = 'MassEdit';
$CU = CronUtils::getInstance();
// install cronjob
$cj = CronJob::getByName($cronname);
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = $cronname;
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->timeout = 1800;	// 30 min
	$cj->repeat = 120;		// 2 min
	$cj->fileName = 'cron/modules/MassEdit/MassEdit.service.php';
	$CU->insertCronJob($cj);
}

// add notification type
$modNot = CRMEntity::getInstance('ModNotifications');
if (method_exists($modNot, 'addNotificationType')) {
	$modNot->addNotificationType('MassEdit', 'MassEdit', 0);
	$modNot->addNotificationType('MassEditError', 'MassEditError', 0);
} else {
	$params = array($adb->getUniqueID("{$table_prefix}_modnotifications_types"), 'MassEdit', 'MassEdit', 0);
	$adb->pquery("INSERT INTO {$table_prefix}_modnotifications_types (id, type, action, custom) VALUES (?, ?, ?, ?)", $params);
	$params = array($adb->getUniqueID("{$table_prefix}_modnotifications_types"), 'MassEditError', 'MassEditError', 0);
	$adb->pquery("INSERT INTO {$table_prefix}_modnotifications_types (id, type, action, custom) VALUES (?, ?, ?, ?)", $params);
}


$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_MASSEDIT_OK' => 'MassEdit completato correttamente. {num_records} elementi sono stati modificati.',
			'LBL_MASSEDIT_ERROR' => 'Si sono verificati degli errori durante il massedit. {num_fail_records} elementi non sono stati salvati correttamente, su un totale di {num_records} elementi. Controllare i file di log per i dettagli.',
			'LBL_MASSEDIT_OK_SUBJECT' => '[VTECRM] MassEdit completato',
			'LBL_MASSEDIT_OK_DESC' => "Il MassEdit sul modulo {module} e' stato completato correttamente.<br>\n{num_records} elementi sono stati modificati.",
			'LBL_MASSEDIT_ERROR_SUBJECT' => '[VTECRM] Errore MassEdit',
			'LBL_MASSEDIT_ERROR_DESC' => "Si sono verificati degli errori durante il massedit sul modulo {module}.<br>\n{num_fail_records} elementi non sono stati salvati correttamente, su un totale di {num_records} elementi. Controllare i file di log per i dettagli.",
		),
		'en_us' => array(
			'LBL_MASSEDIT_OK' => 'MassEdit completed correctly. {num_records} records have been modified.',
			'LBL_MASSEDIT_ERROR' => 'Some errors occurred during MassEdit. {num_fail_records} records have not been saved correctly, on a total of {num_records} records. Please check the logfiles for details.',
			'LBL_MASSEDIT_OK_SUBJECT' => '[VTECRM] MassEdit completed',
			'LBL_MASSEDIT_OK_DESC' => "MassEdit for the module {module} has been completed correctly.<br>\n{num_records} records have been modified.",
			'LBL_MASSEDIT_ERROR_SUBJECT' => '[VTECRM] MassEdit error',
			'LBL_MASSEDIT_ERROR_DESC' => "Some errors occurred during MassEdit for the module {module}.<br>\n{num_fail_records} records have not been saved correctly, on a total of {num_records} records. Please check the logfiles for details.",
		),
	),
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_MASS_EDIT_ENQUEUE' => 'Hai selezionato più di {max_records} elementi. L\'elaborazione verrà eseguita in background e verrai notificato al termine.',
		),
		'en_us' => array(
			'LBL_MASS_EDIT_ENQUEUE' => 'You selected more than {max_records} items. The process will continue in background and you\'ll be notified at the end.',
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
