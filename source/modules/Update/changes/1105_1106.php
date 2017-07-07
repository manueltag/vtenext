<?php
/* crmv@80155 */

$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter' => 'Targets'));

global $adb;

$columns = array_keys($adb->datadict->MetaColumns($table_prefix."_emailtemplates"));
if (!in_array(strtoupper('use_signature'),$columns)) {
	$sql = $adb->datadict->AddColumnSQL($table_prefix."_emailtemplates",'use_signature INT(1) DEFAULT 0');
	$adb->datadict->ExecuteSQLArray($sql);
	$adb->query("update {$table_prefix}_emailtemplates set use_signature = 0");
}
if (!in_array(strtoupper('overwrite_message'),$columns)) {
	$sql = $adb->datadict->AddColumnSQL($table_prefix."_emailtemplates",'overwrite_message INT(1) DEFAULT 0');
	$adb->datadict->ExecuteSQLArray($sql);
	$adb->query("update {$table_prefix}_emailtemplates set overwrite_message = 1");
}

$res = $adb->query("select * from {$table_prefix}_field where fieldname = 'bu_mc'");
if ($res && $adb->num_rows($res) > 0) {
	$sql = $adb->datadict->AddColumnSQL($table_prefix."_emailtemplates",'bu_mc C(100)');
	$adb->datadict->ExecuteSQLArray($sql);
	$adb->query("update {$table_prefix}_emailtemplates set bu_mc=''");
}

SDK::setLanguageEntries('Settings', 'LBL_USE_SIGNATURE', array(
	'it_it'=>'Usa firma utente',
	'en_us'=>'Use user signature',
	'de_de'=>'Verwenden Sie das Signatur',
	'nl_nl'=>'Handtekening gebruik gebruiker',
	'pt_br'=>'Usar assinatura do usuário',
));
SDK::setLanguageEntries('Settings', 'LBL_OVERWRITE_MESSAGE', array(
	'it_it'=>'Sostituisci tutto il corpo del messaggio',
	'en_us'=>'Overwrite all the body of the message',
	'de_de'=>'Überschreiben Sie den ganzen Text der Nachricht',
	'nl_nl'=>'Overschrijven van het gehele lichaam van het bericht',
	'pt_br'=>'Substituir todo o corpo da mensagem',
));
?>