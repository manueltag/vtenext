<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/
 
/* crmv@96742 */

require_once('Smarty_setup.php');
require_once("modules/Reports/ReportRun.php");
require_once("modules/Reports/Reports.php");

global $app_strings, $mod_strings, $theme;

$oPrint_smarty=new vtigerCRM_Smarty;
$reportid = vtlib_purify($_REQUEST["record"]);
$oReport = new Reports($reportid);
$filtercolumn = $_REQUEST["stdDateFilterField"];
$filter = $_REQUEST["stdDateFilter"];
//crmv@sdk-25785
$folderid = vtlib_purify($_REQUEST["folderid"]);
$sdkrep = SDK::getReport($reportid, $folderid);
if (!is_null($sdkrep)) {
	require_once($sdkrep['reportrun']);
	$oReportRun = new $sdkrep['runclass']($reportid);
} else {
	$oReportRun = new ReportRun($reportid);
}
//crmv@sdk-25785e

// crmv@97862
if ($_REQUEST["startdate"] && $_REQUEST["enddate"]) {
	$oReportRun->setStdFilterFromRequest($_REQUEST);
}
// crmv@97862e

$_REQUEST['limit_string'] = 'ALL';

// crmv@29686
if ($_REQUEST['export_report_main'] == 1)
	$arr_values = $oReportRun->GenerateReport("PRINT",$filterlist);
if ($_REQUEST['export_report_totals'] == 1)
	$total_report = $oReportRun->GenerateReport("PRINT_TOTAL",$filterlist);
if ($_REQUEST['export_report_summary'] == 1) {
	$count_total_report = $oReportRun->GenerateReport("COUNT",$filterlist);
	$oPrint_smarty->assign("COUNT_TOTAL_HTML", $count_total_report);
}
// crmv@29686e

$oPrint_smarty->assign("THEME",$theme);
$oPrint_smarty->assign("COUNT",$arr_values[1]);
$oPrint_smarty->assign("APP",$app_strings);
$oPrint_smarty->assign("MOD",$mod_strings);
$oPrint_smarty->assign("REPORT_NAME",$oReport->reportname);
$oPrint_smarty->assign("PRINT_CONTENTS",$arr_values[0]);
$oPrint_smarty->assign("TOTAL_HTML",$total_report);
$oPrint_smarty->display("PrintReport.tpl");
