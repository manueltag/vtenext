<?php

$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';

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

$schema_table =
'<schema version="0.3">
	<table name="'.$table_prefix.'_cronjobs">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="cronid" type="R" size="19">
			<KEY/>
		</field>
		<field name="cronname" type="C" size="100" unique="1" />
		<field name="active" type="I" size="1" />
		<field name="singlerun" type="I" size="1">
			<default value="0"/>
		</field>
		<field name="status" type="C" size="100" />
		<field name="lastrun" type="T">
			<default value="0000-00-00 00:00:00" />
		</field>
		<field name="attempts" type="I" size="11" />
		<field name="pid" type="I" size="19" />
		<field name="filename" type="C" size="255" />
		<field name="starttime" type="T">
			<default value="0000-00-00 00:00:00" />
		</field>
		<field name="endtime" type="T">
			<default value="0000-00-00 00:00:00" />
		</field>
		<field name="timeout" type="I" size="11" />
		<field name="max_attempts" type="I" size="11">
			<default value="5" />
		</field>
		<field name="repeat_sec" type="I" size="11" />
		<field name="run_hours" type="C" size="255" />
		<index name="cronjobs_name_idx">
			<col>cronname</col>
			<unique/>
		</index>
		<index name="cronjobs_status_idx">
			<col>status</col>
		</index>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_cronjobs')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}


// cartella per i log
@mkdir('logs/cron/');

// now setup cronjobs for Messsages
require_once('include/utils/CronUtils.php');
$CU = CronUtils::getInstance();

$cj = CronJob::getByName('Messages'); // to update if existing
if (empty($cj)) $cj = new CronJob();
$cj->name = 'Messages';
$cj->active = 1;
$cj->singleRun = false;
$cj->fileName = 'cron/modules/Messages/Messages.service.php';
$cj->timeout = 300;		// 5min timeout
$cj->repeat = 600;		// run every 10 min
$CU->insertCronJob($cj);

$cj = CronJob::getByName('MessagesPop3');
if (empty($cj)) $cj = new CronJob();
$cj->name = 'MessagesPop3';
$cj->active = 1;
$cj->singleRun = false;
$cj->fileName = 'cron/modules/Messages/Pop3.service.php';
$cj->timeout = 300;		// 5min timeout
$cj->repeat = 900;		// run every 15 min
$CU->insertCronJob($cj);
// crmv@42264e

// crmv@43448
addColumnToTable('vte_notifications', 'forced', 'I(1)', 'DEFAULT 0');

// crmv@43592
$trans = array(
	'Users' => array(
		'it_it' => array(
			'LBL_USE_FIELDS_TO_CHANGE_PWD' => 'Utilizza i campi sottostanti per reimpostare la tua password',
			'LBL_PASSWORD_CHANGED' => 'Password cambiata',
			'LBL_WAIT_FOR_LOGIN' => 'Entro pochi secondi verrà automaticamente effettuato l\'accesso con la nuova password. In caso di problemi, accedi con il pulsante sottostante.',
		),
		'en_us' => array(
			'LBL_USE_FIELDS_TO_CHANGE_PWD' => 'Use the following fields to change your password',
			'LBL_PASSWORD_CHANGED' => 'Password changed',
			'LBL_WAIT_FOR_LOGIN' => 'In a few seconds you\'ll be authenticated automatically with the new password. In case of problems, use the following button.',
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