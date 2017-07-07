<?php
global $adb;
$adb->query("UPDATE vtiger_cvadvfilter SET comparator = 'h' WHERE (columnname LIKE 'vtiger_crmentity:modifiedtime:modifiedtime%' OR columnname LIKE 'vtiger_crmentity:createdtime:createdtime%') AND comparator = 'e'");

$_SESSION['modules_to_install']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';

$pt_br = array(
	'ChangeLog'=>'Change Log',
	'SINGLE_ChangeLog'=>'Change Log',
	'LBL_CHANGELOG_INFORMATION'=>'Informaчуo ChangeLog',
	'LBL_CUSTOM_INFORMATION'=>'Informaчуo Customizada',
	'Audit No'=>'Codigo Revisуo',
	'Assigned To'=>'Responsсvel',
	'Created Time'=>'Hora Criaчуo',
	'Modified Time'=>'Hora Modificaчуo',
	'Related To'=>'Relacionado р',
	'Modified fields'=>'Alteraчѕes',
	'Modified by'=>'Modificado da',
	'Field'=>'Campo',
	'Earlier value'=>'Valor anterior',
	'Actual value'=>'Valor atual',
);
foreach($pt_br as $key => $value){
	SDK::setLanguageEntry('ChangeLog', 'pt_br', $key, $value);
}

SDK::setLanguageEntries('CustomView', 'LBL_SETDEFAULT', array('it_it'=>'Imposta come Default','en_us'=>'Set as Default','pt_br'=>'Definir como Padrуo'));
?>