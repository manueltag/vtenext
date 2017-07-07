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
global $php_max_execution_time;
set_time_limit($php_max_execution_time);

// crmv@30385 - cambio classe php per scrivere i xls - tutto il file
/* crmv@101168 */

require_once('include/PHPExcel/PHPExcel.php');
require_once("modules/Reports/ReportRun.php");
require_once("modules/Reports/Reports.php");

global $tmp_dir, $root_directory, $mod_strings, $app_strings; // crmv@29686

$fname = tempnam($root_directory.$tmp_dir, "merge2.xls");

# Write out the data
$reportid = intval($_REQUEST["record"]);
$folderid = intval($_REQUEST["folderid"]);

$oReport = Reports::getInstance($reportid);

//crmv@sdk-25785

$sdkrep = SDK::getReport($reportid, $folderid);
if (!is_null($sdkrep)) {
	require_once($sdkrep['reportrun']);
	$oReportRun = new $sdkrep['runclass']($reportid);
} else {
	$oReportRun = new ReportRun($reportid);
}

$_REQUEST['limit_string'] = 'ALL'; // crmv@96742

//crmv@sdk-25785e

// crmv@97862
if ($_REQUEST["startdate"] && $_REQUEST["enddate"]) {
	$oReportRun->setStdFilterFromRequest($_REQUEST);
}
// crmv@97862e

// crmv@29686
$temp_xls_report = $oReportRun->GenerateReport("XLS"); // to initialize stuff in reports object
if ($_REQUEST['export_report_main'] == 1)
	$arr_val = $temp_xls_report;
if ($_REQUEST['export_report_totals'] == 1)
	$totalxls = $oReportRun->GenerateReport("TOTALXLS");
if ($_REQUEST['export_report_summary'] == 1)
	$counttotalxls = $oReportRun->GenerateReport("COUNTXLS");
// crmv@29686e

//crmv@36517
if (count($arr_val) > 7000){
	$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	$cacheSettings = array( 'memoryCacheSize' => '8GB');
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
}
//crmv@36517 e
$objPHPExcel = new PHPExcel();
$objPHPExcel->removeSheetByIndex(0); // remove default sheet

$objPHPExcel->getProperties()
	->setCreator("VTE CRM")
	->setLastModifiedBy("VTE CRM")
	->setTitle("Report"); // TODO: report title

$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

$xlsStyle1 = new PHPExcel_Style();
$xlsStyle2 = new PHPExcel_Style();

$xlsStyle1->applyFromArray(
	array('font' => array(
		'name' => 'Arial',
		'bold' => true,
		'size' => 12,
		'color' => array( 'rgb' => '0000FF' )
	),
));

$xlsStyle2->applyFromArray(
	array('font' => array(
		'name' => 'Arial',
		'bold' => true,
		'size' => 11,
	),
));

function addXlsHeader($sheet, $oReportRun) {
	global $xlsStyle1;
	$output = $oReportRun->getOutputClass();
	$head = $output->getSimpleHeaderArray();
	if ($head && count($head) > 0) {
		$count = 0;
		$sheet->setSharedStyle($xlsStyle1, 'A1:'.PHPExcel_Cell::stringFromColumnIndex(count($head)).'1');
		foreach($head as $key) {
			$sheet->setCellValueByColumnAndRow($count, 1, $key);
			$sheet->getColumnDimensionByColumn($count)->setAutoSize(true); // crmv@97862
			$count = $count + 1;
		}
	}
}

// crmv@29686 - riepilogo
if (is_array($counttotalxls) && count($counttotalxls) > 0) {
	$sheet0 = new PHPExcel_Worksheet($objPHPExcel, $mod_strings['LBL_REPORT_SUMMARY']);
	$objPHPExcel->addSheet($sheet0);

	// header
	$colcount = 0;
	$rowcount = 1;
	$sheet0->setSharedStyle($xlsStyle1, 'A1:'.PHPExcel_Cell::stringFromColumnIndex(count($counttotalxls[0])).'1');
	foreach ($counttotalxls[0] as $key=>$v) {
		$sheet0->setCellValueByColumnAndRow($colcount++, $rowcount, $key);
	}

	foreach ($counttotalxls as $xlsrow) {
		++$rowcount;
		$colcount = 0;
		foreach ($xlsrow as $k=>$xlsval) {
			$sheet0->setCellValueByColumnAndRow($colcount++, $rowcount, $xlsval);
		}
	}
} elseif ($_REQUEST['export_report_summary'] == 1 && $oReportRun->hasSummary()) {
	// add an empty sheet with the column names
	$sheet0 = new PHPExcel_Worksheet($objPHPExcel, $mod_strings['LBL_REPORT_SUMMARY']);
	$objPHPExcel->addSheet($sheet0);
	$oReportRun->setReportTab('COUNT');
	addXlsHeader($sheet0, $oReportRun);
}

if (is_array($arr_val) && is_array($arr_val[0])) {
	$count = 0;
	$sheet1 = new PHPExcel_Worksheet($objPHPExcel, $app_strings['Report']);
	$objPHPExcel->addSheet($sheet1);
	// crmv@29686e

	$sheet1->setSharedStyle($xlsStyle1, 'A1:'.PHPExcel_Cell::stringFromColumnIndex(count($arr_val[0])).'1');
	foreach($arr_val[0] as $key=>$value) {
		$sheet1->setCellValueByColumnAndRow($count, 1, $key);
		$sheet1->getColumnDimensionByColumn($count)->setAutoSize(true); // crmv@97862
		$count = $count + 1;
	}


	$rowcount=2;
	foreach($arr_val as $key=>$array_value)
	{
		$dcount = 0;
		foreach($array_value as $hdr=>$value)
		{
			$value = decode_html($value);
			if (strpos($value,'=') === 0) $value = "'".$value;	//crmv@52501

			//crmv@29016
			//check for strings that looks like numbers (starting with 0)
			if (is_numeric($value) && $value !== '0' && substr(strval($value), 0, 1) == '0' && !preg_match('/[,.]/', $value)) { // crmv@30385 crmv@98764
				$sheet1->setCellValueExplicitByColumnAndRow($dcount, $rowcount, $value, PHPExcel_Cell_DataType::TYPE_STRING);
			// crmv@38798 - currency fields
			} elseif (preg_match('/^([€$]) (-?[0-9.,]+)$/u', $value, $matches)) {
				$symbol = $matches[1];
				$value = $matches[2];
				$sheet1->setCellValueExplicitByColumnAndRow($dcount, $rowcount, $value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
				if ($symbol == '$') {
					$numberFormat = PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;
				} else {
					$numberFormat = PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE;
				}
				$sheet1->getStyleBycolumnAndRow($dcount, $rowcount)->getNumberFormat()->setFormatCode($numberFormat);
			// crmv@38798e
			} else {
				$sheet1->setCellValueByColumnAndRow($dcount, $rowcount, $value);
			}
			//crmv@29016e
			$dcount = $dcount + 1;
		}
		$rowcount++;
	}
} elseif ($_REQUEST['export_report_main'] == 1) {
	$sheet1 = new PHPExcel_Worksheet($objPHPExcel, $app_strings['Report']);
	$objPHPExcel->addSheet($sheet1);
	$oReportRun->setReportTab('MAIN');
	addXlsHeader($sheet1, $oReportRun);
}


$rowcount = 1; // crmv@29686
$count=1;
if (is_array($totalxls)) {
	if(is_array($totalxls[0])) {
		$sheet2 = new PHPExcel_Worksheet($objPHPExcel, $mod_strings['LBL_REPORT_TOTALS']);
		$objPHPExcel->addSheet($sheet2);

		$sheet2->setSharedStyle($xlsStyle1, 'A1:'.PHPExcel_Cell::stringFromColumnIndex(count($totalxls[0])).'1');
		foreach($totalxls[0] as $key=>$value) {
			$chdr=substr($key,-3,3);
			$sheet2->setCellValueByColumnAndRow($count++, $rowcount, $mod_strings[$chdr]);
		}
	}
	$rowcount++;
	foreach($totalxls as $key=>$array_value) {
		$dcount = 1;
		foreach($array_value as $hdr=>$value) {
			if ($dcount==1)	{
				$sheet2->setCellValueByColumnAndRow(0, $rowcount, substr($hdr,0,strlen($hdr)-4));
			}
			$value = decode_html($value);
			$sheet2->setCellValueByColumnAndRow($dcount++, $rowcount, $value);
		}
		$rowcount++; //crmv@36517
	}
} elseif ($_REQUEST['export_report_totals'] == 1 && $oReportRun->hasTotals()) {
	// add an empty sheet with the column names
	$sheet2 = new PHPExcel_Worksheet($objPHPExcel, $mod_strings['LBL_REPORT_TOTALS']);
	$objPHPExcel->addSheet($sheet2);
	$oReportRun->setReportTab('TOTAL');
	addXlsHeader($sheet2, $oReportRun);
}


// add an empty sheet if none inserted, otherwise MS Excel won't open the file
if ($objPHPExcel->getSheetCount() == 0) {
	$sheet = new PHPExcel_Worksheet($objPHPExcel, $app_strings['Report']);
	$objPHPExcel->addSheet($sheet);
}

$objPHPExcel->setActiveSheetIndex(0);	// crmv@112208

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); // replace with Excel2007 and change extension to xlsx for the new format
$objWriter->save($fname);


if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
{
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
}
header("Content-Type: application/vnd.ms-excel");
header("Content-Length: ".@filesize($fname));
header('Content-disposition: attachment; filename="Reports.xls"');
$fh=fopen($fname, "rb");
fpassthru($fh);
//unlink($fname);
exit;
