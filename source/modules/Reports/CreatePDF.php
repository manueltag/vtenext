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
/* crmv@29686 crmv@96742 */

ini_set('max_execution_time','1800');
ini_set('memory_limit','1024M');
require_once("modules/Reports/ReportRun.php");
require_once("modules/Reports/Reports.php");

require_once("include/mpdf/mpdf.php"); // crmv@30066

global $current_user, $theme, $mod_Strings, $app_strings;

$reportid = vtlib_purify($_REQUEST["record"]);
$folderid = vtlib_purify($_REQUEST["folderid"]);
$filtercolumn = vtlib_purify($_REQUEST["stdDateFilterField"]);
$filter = vtlib_purify($_REQUEST["stdDateFilter"]);

$oReport = new Reports($reportid);

//crmv@sdk-25785
$sdkrep = SDK::getReport($reportid, $folderid);
if (!is_null($sdkrep)) {
	require_once($sdkrep['reportrun']);
	$oReportRun = new $sdkrep['runclass']($reportid);
	$oReport->reportname = $oReportRun->reportname;
} else {
	$oReportRun = new ReportRun($reportid);
}
//crmv@sdk-25785e

// crmv@97862
if ($_REQUEST["startdate"] && $_REQUEST["enddate"]) {
	$oReportRun->setStdFilterFromRequest($_REQUEST);
}
// crmv@97862e

$_REQUEST['limit_string'] = 'ALL'; // avoid any limit
if ($_REQUEST['export_report_summary'] == 1)
	$reportcount = $oReportRun->GenerateReport("COUNT",$filterlist);
if ($_REQUEST['export_report_totals'] == 1)
	$reporttotal = $oReportRun->GenerateReport("TOTALHTML",$filterlist);
$oReportRun->_columnslist = false; // force reload of columns
if ($_REQUEST['export_report_main'] == 1)
	$reportdata = $oReportRun->GenerateReport("PDF",$filterlist); // TODO: sistema per report con tante righe
$repcolumns = $reportdata[3]; // numero di colonne

// gestione pagina in base alle colonne del report (-L for landscape)
if ($repcolumns <= 5) {
	$format = 'A4';
} elseif ($repcolumns <= 10) {
	$format = 'A4-L';
} elseif ($repcolumns <= 15) {
	$format = 'A3';
} elseif ($repcolumns <= 20) {
	$format = 'A3-L';
} elseif ($repcolumns <= 25) {
	$format = 'A2';
} elseif ($repcolumns <= 30) {
	$format = 'A2-L';
} elseif ($repcolumns <= 35) {
	$format = 'A1';
} else {
	$format = 'A1-L'; // maximum size allowed
}


$mpdf = new mPDF(
	'', 		// mode
	$format, 	// page size/orientation
	'', 		// default font size
	'Arial',	// default font
	10,			// margin left (mm)
	10,			// margin right
	10,			// margin top
	10,			// margin bottom
	10,			// margin header
	10			// margin footer
);

// performance tips
//$mpdf->shrink_tables_to_fit = 0; // disable font shrinking
// $mpdf->debug = true; // DEBUG INFO

$mpdf->SetAuthor('VTE CRM');
$mpdf->SetAutoFont();
$mpdf->setFooter('{PAGENO}'); // page number
$csspath = "themes/$theme/reportpdf.css";
//$mpdf->WriteHTML(file_get_contents($csspath), 1);
//$mpdf->SetHTMLHeader($header_html);
//$mpdf->SetHTMLFooter($footer_html);


$top_html = "
<html>\n
<head>
<title>Report: {$oReport->reportname}</title>\n
<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"$csspath\" />\n
</head>\n
<body class=\"pdfbody\">\n
<h2 style=\"text-align: center\">{$oReport->reportname}</h2><br /><br />
";
$bottom_html = "
</body>\n
</html>\n
";

/*
$report_html = $top_html . $reportcount . $reportdata[0] . $reporttotal . $bottom_html;
die($report_html);
*/

$mpdf->WriteHTML($top_html);

if (!empty($reportcount)) {
	$mpdf->WriteHTML('<bookmark content="'.$mod_strings['LBL_REPORT_SUMMARY'].'" /><h3>'.$mod_strings['LBL_REPORT_SUMMARY'].'</h3><br />');
	$mpdf->WriteHTML($reportcount);
	$mpdf->WriteHTML('<pagebreak />');
	$mpdf->WriteHTML('<bookmark content="'.$app_strings['Report'].'" /><h3>'.$app_strings['Report'].'</h3><br />');
}
if (is_array($reportdata) && !empty($reportdata[0])) {
	$mpdf->WriteHTML($reportdata[0]);
}
if (!empty($reporttotal)) {
	$mpdf->WriteHTML('<pagebreak />');
	$mpdf->WriteHTML('<bookmark content="'.$mod_strings['LBL_REPORT_TOTALS'].'" /><h3>'.$mod_strings['LBL_REPORT_TOTALS'].'</h3><br />');
	$mpdf->WriteHTML($reporttotal);
}


$mpdf->WriteHTML($bottom_html);

$reportname = 'Report.pdf';

$mpdf->Output('cache/'.$reportname);
$filesize = filesize("./cache/$reportname");

@ob_clean();
header("Pragma: public");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); // fix for IE6
header('Content-Type: application/pdf');
header("Content-length: ".$filesize);
//header("Cache-Control: private");
header("Content-Disposition: attachment; filename=$reportname");
header("Content-Description: PHP Generated Data");

$file = @fopen('cache/'.$reportname,"rb");
$chunksize = 1024*1024; // reads 1M every time
while(!feof($file)) {
	echo @fread($file, $chunksize);
	ob_flush();
	//flush();
}

@unlink("cache/$reportname");
die();
?>