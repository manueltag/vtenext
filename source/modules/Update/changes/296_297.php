<?php
$_SESSION['modules_to_install']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
global $adb;
$sqlarray = $adb->datadict->DropTableSQL('crmnow_pdf_fields');
$adb->datadict->ExecuteSQLArray($sqlarray);
$sqlarray = $adb->datadict->DropTableSQL('crmnow_pdfcolums_active');
$adb->datadict->ExecuteSQLArray($sqlarray);
$sqlarray = $adb->datadict->DropTableSQL('crmnow_pdfcolums_sel');
$adb->datadict->ExecuteSQLArray($sqlarray);
$sqlarray = $adb->datadict->DropTableSQL('crmnow_pdfconfiguration');
$adb->datadict->ExecuteSQLArray($sqlarray);
$sqlarray = $adb->datadict->DropTableSQL('crmnow_pdffonts');
$adb->datadict->ExecuteSQLArray($sqlarray);
$sqlarray = $adb->datadict->DropTableSQL('crmnow_pdfsettings');
$adb->datadict->ExecuteSQLArray($sqlarray);
?>