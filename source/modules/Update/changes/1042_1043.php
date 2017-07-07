<?php
$_SESSION['modules_to_update']['FieldFormulas'] = 'packages/vte/mandatory/FieldFormulas.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

require_once('vtlib/Vtecrm/Module.php');

//crmv@65492 - 2
$module = Vtecrm_Module::getInstance('Services');
$sql = "UPDATE {$table_prefix}_field SET quickcreate=? WHERE fieldname=? AND tabid=?";
$params = array(0,'discontinued',$module->id);
$adb->pquery($sql,$params);
//crmv@65492e - 2


//crmv@65492 - 5
$sql5 = "UPDATE {$table_prefix}_portal SET portalname=?,portalurl=? WHERE portalname=?";
$params5 = array('VTECRM','http://www.vtecrm.com','CRMVILLAGE.BIZ');
$adb->pquery($sql5,$params5);
//crmv@65492e - 2

//crmv@65492 - 6
$modulename = 'ProductLines';
$MyNotesModuleInstance = Vtiger_Module::getInstance('MyNotes');
if ($MyNotesModuleInstance) {
	$MyNotesCommonFocus = CRMEntity::getInstance('MyNotes');
	$MyNotesCommonFocus->addWidgetTo($modulename);
}
//crmv@65492e - 6


//crmv@65492 - 25
SDK::setLanguageEntries('Reports', 'NEWSLETTER_G_UNSUBSCRIBE_DIR', array(
	'it_it'=>'Newsletter Custom Report',
	'en_us'=>'Newsletter Custom Report folder',
	'de_de'=>'Ordner Custom Report Newsletter',
	'nl_nl'=>'Nieuwsbrief Custom Report map',
	'pt_br'=>'Pasta Boletim relatório personalizado',
));
//crmv@65492e - 25