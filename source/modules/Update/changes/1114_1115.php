<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';

// crmv@74560

// create the table for the recalc info
$schema = 
'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_tmp_recalc">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="id" type="I" size="19">
				<key/>
			</field>
			<field name="operation" type="C" size="31">
				<NOTNULL/>
			</field>
			<field name="status" type="C" size="31" />
			<field name="running" type="I" size="1">
				<DEFAULT value="0"/>
			</field>
			<field name="starttime" type="T">
				<DEFAULT value="0000-00-00 00:00:00"/>
			</field>
			<field name="endtime" type="T">
				<DEFAULT value="0000-00-00 00:00:00"/>
			</field>
			<index name="tmp_recalc_opstatus">
				<col>operation</col>
				<col>status</col>
			</index>
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_tmp_recalc')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
	
	// and insert the line
	$adb->pquery("INSERT INTO {$table_prefix}_tmp_recalc (id, operation) VALUES (?,?)", array(1, 'RecalcPrivileges'));
}

// add check cronjob
require_once('include/utils/CronUtils.php');
$cronname = 'RecalcPrivileges';
$CU = CronUtils::getInstance();
// install cronjob
$cj = CronJob::getByName($cronname);
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = $cronname;
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->maxAttempts = 0;	// disable attempts check
}
$cj->timeout = 1800;	// 30 min
$cj->repeat = 60;		// 1min
$cj->fileName = 'cron/modules/Users/RecalcPrivileges.service.php';
$CU->insertCronJob($cj);

