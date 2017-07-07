<?php
global $adb, $table_prefix;

SDK::setUitype(204,'modules/SDK/src/204/204.php','modules/SDK/src/204/204.tpl','');

$moduleInstance = Vtiger_Module::getInstance('HelpDesk');

$result = $adb->pquery("select * from {$table_prefix}_field where tabid = ? and fieldname = ?",array($moduleInstance->id, 'mailscanner_action'));
if ($adb->num_rows($result) == 0) {
	$fields = array();
	$fields[] = array('module'=>'HelpDesk','block'=>'LBL_TICKET_INFORMATION','name'=>'mailscanner_action','label'=>'Mail Converter Action','uitype'=>'204','columntype'=>'I(10)','typeofdata'=>'V~O','readonly'=>'99');
	include('modules/SDK/examples/fieldCreate.php');
}

$fieldInstance = Vtiger_Field::getInstance('parent_id', $moduleInstance);
$fieldInstance->setRelatedModules(Array('Leads'));
$leadsModuleInstance = Vtiger_Module::getInstance('Leads');
$leadsModuleInstance->setRelatedList(Vtiger_Module::getInstance('HelpDesk'), 'HelpDesk', Array('ADD'), 'get_dependents_list');

$picklistValues = vtlib_getPicklistValues('leadsource');
if (!in_array('Mail Converter', $picklistValues)) {
	$moduleLeadsInstance = Vtiger_Module::getInstance('Leads');
	$fieldInstance = Vtiger_Field::getInstance('leadsource', $moduleLeadsInstance);
	$fieldInstance->setPicklistValues(array('Mail Converter'));
}

Vtiger_Link::deleteLink($moduleInstance->id, 'DETAILVIEWBASIC', 'LBL_DO_NOT_IMPORT_ANYMORE');
Vtiger_Link::addLink($moduleInstance->id, 'DETAILVIEWBASIC', 'LBL_DO_NOT_IMPORT_ANYMORE', "javascript:doNotImportAnymore('\$MODULE\$',\$RECORD\$,'DetailView');", 'themes/images/small_spam.png',0,'checkMailScannerInfoRule:include/utils/crmv_utils.php');
Vtiger_Link::deleteLink($moduleInstance->id, 'LISTVIEWBASIC', 'LBL_DO_NOT_IMPORT_ANYMORE');
Vtiger_Link::addLink($moduleInstance->id, 'LISTVIEWBASIC', 'LBL_DO_NOT_IMPORT_ANYMORE', "javascript:doNotImportAnymore('\$MODULE\$','','MassListView');",'',0,'checkMailScannerInfoRule:include/utils/crmv_utils.php');

if (!function_exists('addColumnToTable')) {
	function addColumnToTable($tablename, $columnname, $type, $extra = '') {
		global $adb;

		// check if already present
		$cols = $adb->getColumnNames($tablename);
		if (in_array($columnname, $cols)) {
			return;
		}

		$col = $columnname.' '.$type.' '.$extra;
		$adb->alterTable($tablename, $col, 'Add_Column');
	}
}

// add column for main_account
addColumnToTable($table_prefix.'_mailscanner_folders', 'spam', 'I(1)', 'DEFAULT 0');

$schema_table =
'<schema version="0.3">
	<table name="'.$table_prefix.'_mailscanner_spam">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="scannername" type="C" size="30">
			<KEY/>
		</field>
		<field name="xuid" type="I" size="10">
			<KEY/>
		</field>
		<field name="folder" type="C" size="255">
			<KEY/>
		</field>
		<field name="spam_folder" type="C" size="255" />
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_mailscanner_spam')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

SDK::setLanguageEntries('HelpDesk', 'Mail Converter Action', array(
	'it_it'=>'Regola Mail Converter',
	'en_us'=>'Mail Converter Action',
	'pt_br'=>'Regra Mail Converter',
	'de_de'=>'Mail Converter Aktion',
	'nl_nl'=>'Mail convertor Actie',
));
SDK::setLanguageEntries('Settings', 'LBL_SELECT_SPAM_FOLDER', array(
	'it_it'=>'Seleziona la cartella di Spam',
	'en_us'=>'Select the folder for Spam',
	'pt_br'=>'Selecionar o pasta do Spam',
	'de_de'=>'Wählen Sie den Ordner für Spam',
	'nl_nl'=>'Selecteer de map voor spam',
));
SDK::setLanguageEntries('HelpDesk', 'LBL_DO_NOT_IMPORT_ANYMORE', array(
	'it_it'=>'Segna come indesiderata',
	'en_us'=>'Mark as Spam',
	'pt_br'=>'Marcar como spam',
	'de_de'=>'Als Spam markieren',
	'nl_nl'=>'Markeren als spam',
));
SDK::setLanguageEntries('Settings', 'LBL_MAILSCANNER_NAME_DUPLICATED', array(
	'it_it'=>'Esiste già una configurazione con questo nome',
	'en_us'=>'There is already a configuration with this name',
	'pt_br'=>'Já existe uma configuração com este nome',
	'de_de'=>'Es gibt bereits eine Konfiguration mit diesem Namen',
	'nl_nl'=>'Er is al een configuratie met deze naam',
));
SDK::setLanguageEntries('Settings', 'SELECT_ATLEAST_ONE_MAILSCANNER', array(
	'it_it'=>'Selezionare almeno un record proveniente da Mail Connector',
	'en_us'=>'Please select at least one entity created by Mail Connector',
	'pt_br'=>'Selecione pelo menos um registro de Mail Connector',
	'de_de'=>'Bitte wählen Sie mindestens eine Einheit erstellt von Mail Connector',
	'nl_nl'=>'Gelieve ten minste een entiteit gecreëerd door mailconnector',
));
?>