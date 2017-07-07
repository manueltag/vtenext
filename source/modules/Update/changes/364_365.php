<?php
$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';

global $adb;
$sqlarray = $adb->datadict->DropTableSQL('tbl_v_leadimportemails_mapping');
$adb->datadict->ExecuteSQLArray($sqlarray);
$flds = "	
	string C(100) PRIMARY,
	fieldname C(50) PRIMARY
";
$sqlarray = $adb->datadict->CreateTableSQL('tbl_v_leadimportemails_mapping', $flds);
$adb->datadict->ExecuteSQLArray($sqlarray);
$adb->query("insert into tbl_v_leadimportemails_mapping (string, fieldname) values('account:','company')");
$adb->query("insert into tbl_v_leadimportemails_mapping (string, fieldname) values('assigned_to:','assigned_user_id')");
$adb->query("insert into tbl_v_leadimportemails_mapping (string, fieldname) values('first_name:','firstname')");
$adb->query("insert into tbl_v_leadimportemails_mapping (string, fieldname) values('last_name:','lastname')");
?>