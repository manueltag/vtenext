<?php
global $adb, $table_prefix;

// some functions
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

$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_alertnot">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="id" type="I" size="19">
				<key/>
			</field>
			<field name="label" type="C" size="50"/>
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_alertnot')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}
$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_alertnot_seen">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="id" type="I" size="19">
				<key/>
			</field>
			<field name="userid" type="I" size="19">
				<key/>
			</field>
			<field name="seen" type="I" size="1">
				<DEFAULT value="0"/>
			</field>
			<field name="seen_date" type="T">
				<DEFAULT value="0000-00-00 00:00:00"/>
		    </field>
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_alertnot_seen')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}

// create the table for the panels
$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_panels">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="panelid" type="I" size="19">
				<key/>
			</field>
			<field name="tabid" type="I" size="19" />
			<field name="panellabel" type="C" size="100" />
			<field name="sequence" type="I" size="10" />
			<field name="visible" type="I" size="1">
				<DEFAULT value="0"/>
			</field>
			<field name="iscustom" type="I" size="1">
				<DEFAULT value="0"/>
			</field>
			<index name="panels_tabid_idx">
				<col>tabid</col>
			</index>
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_panels')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
} else {
	$adb->query("TRUNCATE TABLE {$table_prefix}_panels");
	if (Vtiger_Utils::CheckTable($table_prefix.'_panels_seq')) {
		$adb->query("DROP TABLE {$table_prefix}_panels_seq");
	}
}

// create the table for the order of the relatedlist in every panel
$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_panel2rlist">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="panelid" type="I" size="19">
				<key/>
			</field>
			<field name="relation_id" type="I" size="19">
				<key/>
			</field>
			<field name="sequence" type="I" size="10" />
			<index name="panel2rlist_relid_idx">
				<col>relation_id</col>
			</index>
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_panel2rlist')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
} else {
	$adb->query("TRUNCATE TABLE {$table_prefix}_panel2rlist");
}

addColumnToTable($table_prefix.'_blocks', 'panelid', 'I(19)');

require_once('vtlib/Vtiger/SettingsBlock.php');
require_once('vtlib/Vtiger/SettingsField.php');
$block = Vtiger_SettingsBlock::getInstance('LBL_STUDIO');
$res = $adb->pquery("select fieldid from {$table_prefix}_settings_field where name = ?", array('LBL_PROCESS_MAKER'));
if ($res && $adb->num_rows($res) == 0) {
	$field = new Vtiger_SettingsField();
	$field->name = 'LBL_PROCESS_MAKER';
	$field->iconpath = 'module_maker.png';
	$field->description = 'LBL_PROCESS_MAKER_DESC';
	$field->linkto = 'index.php?module=Settings&action=ProcessMaker&parenttab=Settings';
	$block->addField($field);
}

// remove some tables (needed for rollback to work properly)
if (Vtiger_Utils::CheckTable($table_prefix.'_process_status')) {
	$adb->query("DROP TABLE {$table_prefix}_process_status");
}
if (Vtiger_Utils::CheckTable($table_prefix.'_process_status_seq')) {
	$adb->query("DROP TABLE {$table_prefix}_process_status_seq");
}

require_once('vtlib/Vtiger/Package.php');
$package = new Vtiger_Package();
$package->importByManifest('Processes');

// translations
$trans = array(
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_NEW_CONDITION_BUTTON_LABEL' => 'Nuova Condizione',
			'LBL_REMOVE_GROUP_CONDITION'=>'Cancella Gruppo',
			'LBL_PMH_SELECT_RELATED_TO'=>'Seleziona un\'entità collegata al Process Helper oppure disattivalo',
			'LBL_PM_CHECK_ACTIVE'=>'Il processo non è ancora attivo. Vuoi attivarlo adesso?',
		),
		'en_us' => array(
			'LBL_NEW_CONDITION_BUTTON_LABEL' => 'New Condition',
			'LBL_REMOVE_GROUP_CONDITION'=>'Delete group',
			'LBL_PMH_SELECT_RELATED_TO'=>'Select a related record to the Process Helper or disable it',
			'LBL_PM_CHECK_ACTIVE'=>'The process is not yet active. Do you want to activate it now?',
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'DynaForm'=>'Form dinamica',
		),
		'en_us' => array(
			'DynaForm'=>'Dynamic form',
		),
	),
	'Settings' => array(
		'it_it' => array(
			'LBL_PROCESS_MAKER' => 'Process manager',
			'LBL_PROCESS_MAKER_DESC' => 'Permette di creare processi custom',
			'LBL_IMPORT_BPMN' => 'Importa BPMN',
			'LBL_IMPORT_VTEBPMN' => 'Importa VTE BPMN',
			'LBL_UPLOAD_FILE_BPMN' => 'Seleziona il file',
			'LBL_PROCESS_MAKER_RECORD_NAME' => 'Nome',
			'LBL_PROCESS_MAKER_RECORD_DESC' => 'Descrizione',
			'LBL_PROCESS_MAKER_PASTE_CODE' => 'oppure clicca qui per incollare direttamente il codice',
			'LBL_PROCESS_MAKER_ALERT_EMPTY_BPMN' => 'Seleziona il file da caricare oppure incolla il codice',
			'LBL_DOWNLOAD_BPMN' => 'Download BPMN',
			'LBL_DOWNLOAD_VTEBPMN' => 'Download VTE BPMN',
			'LBL_EXCLUSIVEGATEWAY_SUCCESS'=>'Se la condizione precedente è soddisfatta vai a',
			'LBL_EXCLUSIVEGATEWAY_OTHER'=>'altrimenti vai a',
			'LBL_PM_ACTION_Email'=>'Email',
			'LBL_PM_ACTION_Create'=>'Crea entità',
			'LBL_PM_ACTION_Update'=>'Aggiorna entità',
			'LBL_PM_ACTION_Delete'=>'Elimina entità',
			'LBL_PM_ACTION'=>'Azione',
			'LBL_PM_CREATE_ACTION'=>'Nuova azione',
			'LBL_PM_CREATE_ACTION_OF_TYPE'=>'Crea una nuova azione di tipo',
			'LBL_PROCESS_HELPER'=>'Process Helper',
			'LBL_PMH_RELATEDTO'=>'Collegato a',
			'LBL_PMH_DESCRIPTION'=>'Istruzioni',
			'LBL_PMH_CURRENT_ENTITY'=>'Entità corrente',
			'LBL_PM_SDK_CUSTOM_FUNCTIONS'=>'Funzioni SDK',
			'LBL_PROCESS_MAKER_REQUIRED_TO_GO' => 'Necessario per procedere',
			'LBL_EVERY_TIME_TIME_CONDITION_TRUE' => 'Ogni volta che la condizione risulti vera',
			'LBL_PROCESS_MAKER_REQUIRED_TO_GO_ALL' => 'Attendi tutte le risposte per procedere',
			'LBL_PROCESS_MAKER_MANAGE_OTHER_RECORD' => 'Gestisci altre entità nel processo',
			'LBL_PM_ACTIONS'=>'Azioni',
			'LBL_PM_NO_ACTIONS'=>'Nessuna azione configurata',
			'LBL_PM_MANAGE_DYNAFORM'=>'Gestisci form dinamica',
			'LBL_WHEN_TO_RUN_PM_TASK'=>'Quando eseguire il controllo',
			'LBL_PM_WAIT'=>'Attendi',
			'LBL_PM_AFTER'=>'Se il processo resta fermo in questo per più di',
			'LBL_PM_TO_GO_TO_NEXT_STEP'=>'prima di andare a',
			'LBL_PM_GO_TO_NEXT_STEP'=>'vai a',
			'LBL_PM_ENABLE_RECURRENCE'=>'Abilita ricorrenza',
			'LBL_PM_CRON_VALUE_SELECTED'=>'Ricorrenza impostata',
			'LBL_PM_PREVIEW_RECURRENCE'=>'Anteprima ricorrenze',
			'LBL_PM_START_EVENT_NOTE'=>'Configurando le condizioni temporali e impostando il flag Attivo nel Process manager il processo sarà schedulato. Basterà modificare i seguenti parametri per rischedularlo.',
			'LBL_PM_SUBPROCESSES'=>'Sottoprocessi',
			'LBL_PM_CHECK_TIMER_START_DATE'=>'Non è permesso impostare un timer di partenza con orario passato',
			'LBL_PM_CHECK_TIMER_START_GREATER_THAN_END'=>'L\'orario di fine deve essere maggiore a quello di inizio',
			'LBL_PM_IMPORT_BLOCK'=>'Importa blocco',
			'LBL_PM_IMPORT_BLOCKS_TITLE'=>'Importa blocchi da altre form dinamiche',
		),
		'en_us' => array(
			'LBL_PROCESS_MAKER' => 'Process manager',
			'LBL_PROCESS_MAKER_DESC' => 'Allow to create custom processes',
			'LBL_IMPORT_BPMN' => 'Import BPMN',
			'LBL_IMPORT_VTEBPMN' => 'Import VTE BPMN',
			'LBL_UPLOAD_FILE_BPMN' => 'Select the file',
			'LBL_PROCESS_MAKER_RECORD_NAME' => 'Name',
			'LBL_PROCESS_MAKER_RECORD_DESC' => 'Description',
			'LBL_PROCESS_MAKER_PASTE_CODE' => 'or click here and paste the code',
			'LBL_PROCESS_MAKER_ALERT_EMPTY_BPMN' => 'Select the file to upload or paste the code',
			'LBL_DOWNLOAD_BPMN' => 'Download BPMN',
			'LBL_DOWNLOAD_VTEBPMN' => 'Download VTE BPMN',
			'LBL_EXCLUSIVEGATEWAY_SUCCESS'=>'If the previous condition is satisfied go to',
			'LBL_EXCLUSIVEGATEWAY_OTHER'=>'else go to',
			'LBL_PM_ACTION_Email'=>'Email',
			'LBL_PM_ACTION_Create'=>'Create entity',
			'LBL_PM_ACTION_Update'=>'Update entity',
			'LBL_PM_ACTION_Delete'=>'Delete entity',
			'LBL_PM_ACTION'=>'Action',
			'LBL_PM_CREATE_ACTION'=>'New action',
			'LBL_PM_CREATE_ACTION_OF_TYPE'=>'Create a new action',
			'LBL_PROCESS_HELPER'=>'Process Helper',
			'LBL_PMH_RELATEDTO'=>'Related to',
			'LBL_PMH_DESCRIPTION'=>'Instructions',
			'LBL_PMH_CURRENT_ENTITY'=>'Current entity',
			'LBL_PM_SDK_CUSTOM_FUNCTIONS'=>'SDK Functions',
			'LBL_PROCESS_MAKER_REQUIRED_TO_GO' => 'Required to go on',
			'LBL_EVERY_TIME_TIME_CONDITION_TRUE' => 'Every time the condition is true',
			'LBL_PROCESS_MAKER_REQUIRED_TO_GO_ALL' => 'Wait all responses to proceed',
			'LBL_PROCESS_MAKER_MANAGE_OTHER_RECORD' => 'Use other records in the process',
			'LBL_PM_ACTIONS'=>'Actions',
			'LBL_PM_NO_ACTIONS'=>'No actions configured',
			'LBL_PM_MANAGE_DYNAFORM'=>'Manage dynamic form',
			'LBL_WHEN_TO_RUN_PM_TASK'=>'When to run the check',
			'LBL_PM_WAIT'=>'Wait',
			'LBL_PM_AFTER'=>'If the process is still in this state for more than',
			'LBL_PM_TO_GO_TO_NEXT_STEP'=>'to go on',
			'LBL_PM_GO_TO_NEXT_STEP'=>'go on',
			'LBL_PM_ENABLE_RECURRENCE'=>'Enable recurrence',
			'LBL_PM_CRON_VALUE_SELECTED'=>'Recurrence selected',
			'LBL_PM_PREVIEW_RECURRENCE'=>'Recurrences preview',
			'LBL_PM_START_EVENT_NOTE'=>'Configure the temporal conditions and set the flag Active of Process manager in order to schedule the process. Just change the following parameters to schedule it again.',
			'LBL_PM_SUBPROCESSES'=>'Subprocesses',
			'LBL_PM_CHECK_TIMER_START_DATE'=>'It is not allowed to set a past time in the start timer',
			'LBL_PM_CHECK_TIMER_START_GREATER_THAN_END'=>'The end time must be greater than the start time',
			'LBL_PM_IMPORT_BLOCK'=>'Import block',
			'LBL_PM_IMPORT_BLOCKS_TITLE'=>'Import blocks from other dynamic forms',
		),
	),
);
foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}