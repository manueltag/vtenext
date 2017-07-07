<?php
global $adb;

SDK::setLanguageEntries('Emails', 'LBL_SINGLE_MODE', array('it_it'=>'Singolo','en_us'=>'Single'));
SDK::setLanguageEntries('Emails', 'LBL_MULTIPLE_MODE', array('it_it'=>'Multiplo','en_us'=>'Multiple'));
SDK::setLanguageEntries('Emails', 'Send Mode', array('it_it'=>'Metodo di invio','en_us'=>'Send Mode'));
SDK::setLanguageEntries('Emails', 'LBL_SEND_MODE_INFO', array('it_it'=>'<b>Singolo</b>: invia una mail visualizzando tutti i destinatari per A, CC e CCN.<br /><b>Multiplo</b>: invia una mail distinta per ogni destinatario in A, includendo sempre CC e CCN.','en_us'=>'Send Mode Info'));
$fields = array();
$fields[] = array('module'=>'Emails','block'=>'LBL_EMAIL_INFORMATION','name'=>'send_mode','label'=>'Send Mode','uitype'=>'1','helpinfo'=>'LBL_SEND_MODE_INFO');
include('modules/SDK/examples/fieldCreate.php');
$adb->query("UPDATE vtiger_field SET tablename='vtiger_emaildetails', helpinfo='LBL_SEND_MODE_INFO' WHERE tabid = 10 and fieldname = 'send_mode'");
$sqlarray = $adb->datadict->DropColumnSQL('vtiger_activity','send_mode');
$adb->datadict->ExecuteSQLArray($sqlarray);
$sqlarray = $adb->datadict->AddColumnSQL('vtiger_emaildetails','send_mode C(50)');
$adb->datadict->ExecuteSQLArray($sqlarray);
$adb->query("UPDATE vtiger_emaildetails SET send_mode = 'multiple'");

$where = "templatename='Nuova Release in uscita'";
$result = $adb->query('select body from vtiger_emailtemplates where '.$where.' and body LIKE \'%$contacts-lastname$%\'');
if ($result && $adb->num_rows($result) > 0) {
	$body = str_replace('$contacts-lastname$','$Contacts||lastname$',$adb->query_result_no_html($result,0,'body'));
	$adb->updateClob('vtiger_emailtemplates','body',$where,$body);
}
$where = "templatename='Fatture non pagate'";
$result = $adb->query('select body from vtiger_emailtemplates where '.$where.' and body LIKE \'%$contacts-lastname$%\'');
if ($result && $adb->num_rows($result) > 0) {
	$body = str_replace('$contacts-lastname$','$Contacts||lastname$',$adb->query_result_no_html($result,0,'body'));
	$adb->updateClob('vtiger_emailtemplates','body',$where,$body);
}
$where = "templatename='Proposta accettata'";
$result = $adb->query('select body from vtiger_emailtemplates where '.$where.' and body LIKE \'%$contacts-lastname$%\'');
if ($result && $adb->num_rows($result) > 0) {
	$body = str_replace('$contacts-lastname$','$Contacts||lastname$',$adb->query_result_no_html($result,0,'body'));
	$adb->updateClob('vtiger_emailtemplates','body',$where,$body);
}
$where = "templatename='Ordine accettato'";
$result = $adb->query('select body from vtiger_emailtemplates where '.$where.' and body LIKE \'%$contacts-lastname$%\'');
if ($result && $adb->num_rows($result) > 0) {
	$body = str_replace('$contacts-lastname$','$Contacts||lastname$',$adb->query_result_no_html($result,0,'body'));
	$adb->updateClob('vtiger_emailtemplates','body',$where,$body);
}
$where = "templatename='Cambio indirizzo'";
$result = $adb->query('select body from vtiger_emailtemplates where '.$where.' and body LIKE \'%$contacts-lastname$%\'');
if ($result && $adb->num_rows($result) > 0) {
	$body = str_replace('$contacts-lastname$','$Contacts||lastname$',$adb->query_result_no_html($result,0,'body'));
	$adb->updateClob('vtiger_emailtemplates','body',$where,$body);
}
$where = "templatename='Successione'";
$result = $adb->query('select body from vtiger_emailtemplates where '.$where.' and body LIKE \'%$contacts-lastname$%\'');
if ($result && $adb->num_rows($result) > 0) {
	$body = str_replace('$contacts-lastname$','$Contacts||lastname$',$adb->query_result_no_html($result,0,'body'));
	$adb->updateClob('vtiger_emailtemplates','body',$where,$body);
}
$where = "templatename='Obiettivo raggiunto!'";
$result = $adb->query('select body from vtiger_emailtemplates where '.$where.' and body LIKE \'%$contacts-lastname$%\'');
if ($result && $adb->num_rows($result) > 0) {
	$body = str_replace('$contacts-lastname$','$Contacts||lastname$',$adb->query_result_no_html($result,0,'body'));
	$adb->updateClob('vtiger_emailtemplates','body',$where,$body);
}
$where = "templatename='Ringraziamenti'";
$result = $adb->query('select body from vtiger_emailtemplates where '.$where.' and body LIKE \'%$contacts-lastname$%\'');
if ($result && $adb->num_rows($result) > 0) {
	$body = str_replace('$contacts-lastname$','$Contacts||lastname$',$adb->query_result_no_html($result,0,'body'));
	$adb->updateClob('vtiger_emailtemplates','body',$where,$body);
}
$where = "templatename='Il contratto di assistenza scade tra una settimana'";
$result = $adb->query('select body from vtiger_emailtemplates where '.$where.' and body LIKE \'%$contacts-lastname$%\'');
if ($result && $adb->num_rows($result) > 0) {
	$body = str_replace('$contacts-lastname$','$Contacts||lastname$',$adb->query_result_no_html($result,0,'body'));
	$adb->updateClob('vtiger_emailtemplates','body',$where,$body);
}
$where = "templatename='Il contratto di assistenza scade tra un mese'";
$result = $adb->query('select body from vtiger_emailtemplates where '.$where.' and body LIKE \'%$contacts-lastname$%\'');
if ($result && $adb->num_rows($result) > 0) {
	$body = str_replace('$contacts-lastname$','$Contacts||lastname$',$adb->query_result_no_html($result,0,'body'));
	$adb->updateClob('vtiger_emailtemplates','body',$where,$body);
}
?>