<?php
global $php_max_execution_time;
set_time_limit($php_max_execution_time);

require_once('include/utils/utils.php');
global $adb,$table_prefix;
$reportid = intval($_REQUEST['reportid']);
if ($reportid > 0) {
	global $table_prefix;
	$folderid = getSingleFieldValue($table_prefix.'_report', 'folderid', 'reportid', $reportid);
	$sdkrep = SDK::getReport($reportid, $folderid);
	if (!is_null($sdkrep)) {
		require_once($sdkrep['reportrun']);
		$oReportRun = new $sdkrep['runclass']($reportid);
	} else {
		require_once('modules/Reports/ReportRun.php');
		$oReportRun = new ReportRun($reportid);
	}
	// crmv@108210
	$oReportRun->setCVInfo(array('module'=>$_REQUEST["relatedmodule"]));
	$oReportRun->setReportTab("CV");
	$oReportRun->GenerateReport();
	// crmv@108210e
	//ho la tab temp ora devo fare un query per prendermi gli id
	$customview = CRMEntity::getInstance('CustomView');
	$tableNameTmp = $customview->getReportFilterTableName($reportid,$current_user->id);
	$sql = "select crmid from {$table_prefix}_crmentity";
	$sql .= " INNER JOIN $tableNameTmp ON $tableNameTmp.id = {$table_prefix}_crmentity.crmid";	
	$sql .= " where {$table_prefix}_crmentity.deleted = 0";
	// crmv@86325
	if (!PerformancePrefs::getBoolean('USE_TEMP_TABLES', true)) {
		$sql .= " AND $tableNameTmp.reportid = $reportid AND userid = ".$current_user->id;
	}
	// crmv@86325e
	$res = $adb->query($sql);
	if ($res){
		$ids = Array();
		$focus = CRMEntity::getInstance('Targets');
		while($row = $adb->fetchByAssoc($res,-1,false)){
			$ids[] = $row['crmid'];
		}
		$focus->save_related_module('Targets', $_REQUEST['return_id'], $_REQUEST["relatedmodule"], $ids);
	}
}
header("Location: index.php?module=Targets&action=TargetsAjax&file=CallRelatedList&ajax=true&".
"record=".vtlib_purify($_REQUEST['return_id']));
