<?php
require_once("modules/Update/Update.php");
global $adb;

if ($adb->table_exist('vtiger_ddtcondizioneconsegna')) {
	$sql1 = $adb->datadict->RenameTableSQL('vtiger_ddtcondizioneconsegna','vtiger_ddtcondcons');
	$adb->datadict->ExecuteSQLArray($sql1);
	if ($adb->table_exist('vtiger_ddtcondcons')) {
		$ddt = Vtiger_Module::getInstance('Ddt');
		$adb->pquery("UPDATE vtiger_field SET fieldname = 'ddtcondcons' WHERE fieldname = 'ddtcondizioneconsegna' AND tabid = ?",array($ddt->id));
		$adb->pquery("UPDATE vtiger_field SET columnname = 'ddtcondcons' WHERE columnname = 'ddtcondizioneconsegna' AND tabid = ?",array($ddt->id));
		$adb->query("UPDATE vtiger_picklist SET name = 'ddtcondcons' WHERE name = 'ddtcondizioneconsegna'");
		$sql2 = $adb->datadict->RenameColumnSQL('vtiger_ddtcondcons','ddtcondizioneconsegna','ddtcondcons','ddtcondizioneconsegna C(200)');
		$adb->datadict->ExecuteSQLArray($sql2);
		$sql3 = $adb->datadict->RenameColumnSQL('vtiger_ddtcondcons','ddtcondizioneconsegnaid','ddtcondconsid','ddtcondizioneconsegnaid I(19)');
		$adb->datadict->ExecuteSQLArray($sql3);
		$result = $adb->query('SELECT * FROM vtiger_ddtcondcons ORDER BY ddtcondconsid desc');
		if ($result && $adb->num_rows($result)>0) {
			$max = $adb->query_result($result,0,'ddtcondconsid');
			for ($i=0;$i<$max;$i++) {
				$adb->getUniqueID("vtiger_ddtcondcons");
			}
		}
		$sql4 = $adb->datadict->RenameColumnSQL('vtiger_ddt','ddtcondizioneconsegna','ddtcondcons','ddtcondizioneconsegna C(255)');
		$adb->datadict->ExecuteSQLArray($sql4);
	}
}
if ($adb->table_exist('vtiger_projectmilestonetype')) {
	$sql1 = $adb->datadict->RenameTableSQL('vtiger_projectmilestonetype','vtiger_projectmilestype');
	$adb->datadict->ExecuteSQLArray($sql1);
	if ($adb->table_exist('vtiger_projectmilestype')) {
		$ProjectMilestone = Vtiger_Module::getInstance('ProjectMilestone');
		$adb->pquery("UPDATE vtiger_field SET fieldname = 'projectmilestype' WHERE fieldname = 'projectmilestonetype' AND tabid = ?",array($ProjectMilestone->id));
		$adb->pquery("UPDATE vtiger_field SET columnname = 'projectmilestype' WHERE columnname = 'projectmilestonetype' AND tabid = ?",array($ProjectMilestone->id));
		$adb->query("UPDATE vtiger_picklist SET name = 'projectmilestype' WHERE name = 'projectmilestonetype'");
		$sql2 = $adb->datadict->RenameColumnSQL('vtiger_projectmilestype','projectmilestonetype','projectmilestype','projectmilestonetype C(200)');
		$adb->datadict->ExecuteSQLArray($sql2);
		$sql3 = $adb->datadict->RenameColumnSQL('vtiger_projectmilestype','projectmilestonetypeid','projectmilestypeid','projectmilestonetypeid I(19)');
		$adb->datadict->ExecuteSQLArray($sql3);
		$result = $adb->query('SELECT * FROM vtiger_projectmilestype ORDER BY projectmilestypeid desc');
		if ($result && $adb->num_rows($result)>0) {
			$max = $adb->query_result($result,0,'projectmilestypeid');
			for ($i=0;$i<$max;$i++) {
				$adb->getUniqueID("vtiger_projectmilestype");
			}
		}
		$sql4 = $adb->datadict->RenameColumnSQL('vtiger_projectmilestone','projectmilestonetype','projectmilestype','projectmilestonetype C(100)');
		$adb->datadict->ExecuteSQLArray($sql4);
	}
}
?>