<?php
global $adb;
$result = $adb->query("SELECT * FROM vtiger_field WHERE tabid = 29 AND fieldname = 'default_module'");
if ($result && $adb->num_rows($result)>0) {
} else {
	$fields = array();
	$fields[] = array('module'=>'Users','block'=>'LBL_USERLOGIN_ROLE','name'=>'default_module','label'=>'Default Module','uitype'=>'1','sdk_uitype'=>'201','columntype'=>'C(255)','typeofdata'=>'V~O');
	include('modules/SDK/examples/fieldCreate.php');
}
SDK::setUitype('201','modules/SDK/src/201/201.php','modules/SDK/src/201/201.tpl','modules/SDK/src/201/201.js');
SDK::setLanguageEntries('Users', 'Default Module', array('it_it'=>'Modulo di default','en_us'=>'Default Module'));
?>