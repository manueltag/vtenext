<?php
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';


require_once('vtlib/Vtiger/SettingsBlock.php');
require_once('vtlib/Vtiger/SettingsField.php');

// useful functions
if (!function_exists('getPrimaryKeyName')) {
	function getPrimaryKeyName($tablename) {
		global $adb, $dbconfig;
		$ret = '';
		if ($adb->isMysql()) {
			// for mysql just check if it exists
			$res = $adb->query("SHOW KEYS FROM {$tablename} WHERE Key_name = 'PRIMARY'");
			if ($res && $adb->num_rows($res) > 0) $ret = 'PRIMARY';
		} elseif ($adb->isMssql()) {
			$res = $adb->pquery("SELECT CONSTRAINT_NAME as cn from INFORMATION_SCHEMA.TABLE_CONSTRAINTS where CONSTRAINT_CATALOG = ? and TABLE_NAME = ? and CONSTRAINT_TYPE = 'PRIMARY KEY'", array($dbconfig['db_name'], $tablename));
			if ($res) $ret = $adb->query_result_no_html($res, 0, 'cn');
		} elseif ($adb->isOracle()) {
			$res = $adb->pquery("SELECT CONSTRAINT_NAME as cn FROM all_constraints cons     WHERE cons.table_name = ? AND cons.constraint_type = 'P'", array(strtoupper($tablename)));
			if ($res) $ret = $adb->query_result_no_html($res, 0, 'cn');
		}
		return $ret;
	}
}

if (!function_exists('dropPrimaryKey')) {
	function dropPrimaryKey($tablename) {
		global $adb;
		if ($adb->isMysql()) {
			$keyname = getPrimaryKeyName($tablename);
			if ($keyname == 'PRIMARY') $adb->query("ALTER TABLE {$tablename} DROP PRIMARY KEY");
		} elseif ($adb->isMssql() || $adb->isOracle()) {
			$keyname = getPrimaryKeyName($tablename);
			$adb->query("ALTER TABLE {$tablename} DROP CONSTRAINT {$keyname}");
		} else {
			echo "Drop Primary key not supported for this database";
		}
	}
}


/* crmv@64542 - Module Maker */

// add the link in the settings
$block = Vtiger_SettingsBlock::getInstance('LBL_STUDIO');

$res = $adb->pquery("select fieldid from {$table_prefix}_settings_field where name = ?", array('LBL_MODULE_MAKER'));
if ($res && $adb->num_rows($res) == 0) {
	$field = new Vtiger_SettingsField();
	$field->name = 'LBL_MODULE_MAKER';
	$field->iconpath = 'module_maker.png';
	$field->description = 'LBL_MODULE_MAKER_DESC';
	$field->linkto = 'index.php?module=Settings&action=ModuleMaker&parenttab=Settings';
	$block->addField($field);
}


// create the table for the saved modules
$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_modulemaker">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="id" type="I" size="19">
				<key/>
			</field>
			<field name="modulename" type="C" size="63">
				<NOTNULL/>
			</field>
			<field name="createdtime" type="T">
				<NOTNULL/>
				<DEFAULT value="0000-00-00 00:00:00"/>
			</field>
			<field name="modifiedtime" type="T">
				<NOTNULL/>
				<DEFAULT value="0000-00-00 00:00:00"/>
			</field>
			<field name="installed" type="I" size="1">
				<NOTNULL/>
				<DEFAULT value="0"/>
			</field>
			<field name="useredit" type="I" size="1">
				<NOTNULL/>
				<DEFAULT value="0"/>
			</field>
			<field name="showlogs" type="I" size="1">
				<NOTNULL/>
				<DEFAULT value="0"/>
			</field>
			<field name="moduleinfo" type="XL"/>
			<field name="fields" type="XL"/>
			<field name="filters" type="XL"/>
			<field name="relations" type="XL"/>
			<field name="labels" type="XL"/>
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_modulemaker')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}

// create the folder for the scripts
@mkdir('storage/custom_modules');

// fix the table with module properties

$oldDieOnError = $adb->dieOnError;
$adb->dieOnError = false;

// shorten the column
Vtiger_Utils::AlterTable($table_prefix.'_tab_info','prefname C(63)');

// drop indexes
$idxs_tabinfo = array_keys($adb->database->MetaIndexes($table_prefix.'_tab_info'));
$del_indexes = array(
	array("{$table_prefix}_tab_info", "fk_1_vte_tab_info"),
);
foreach($del_indexes as $index) {
	if (in_array($index[1], $idxs_tabinfo)) $adb->datadict->ExecuteSQLArray((Array)$adb->datadict->DropIndexSQL($index[1], $index[0]));
}

// change the primary key
dropPrimaryKey("{$table_prefix}_tab_info");
$adb->query("ALTER TABLE {$table_prefix}_tab_info ADD PRIMARY KEY (tabid, prefname)");

// restore doe
$adb->dieOnError = $oldDieOnError;

// insert inventory modules
$defaultInventory = array('Quotes', 'SalesOrder', 'PurchaseOrder', 'Invoice', 'Ddt');
foreach ($defaultInventory as $imod) {
	$tabid = getTabid($imod);
	if ($tabid > 0) {
		$tabResult = $adb->pquery("SELECT tabid FROM ".$table_prefix."_tab_info WHERE tabid=? AND prefname='is_inventory'", array($tabid));
		if ($adb->num_rows($tabResult) > 0) {
			$adb->pquery("UPDATE ".$table_prefix."_tab_info SET prefvalue=? WHERE tabid=? AND prefname='is_inventory'", array(1,$tabid));
		} else {
			$adb->pquery('INSERT INTO '.$table_prefix.'_tab_info(tabid, prefname, prefvalue) VALUES (?,?,?)', array($tabid, 'is_inventory', 1));
		}
	}
}

// insert product modules
$defaultProducts = array('Products', 'Services');
foreach ($defaultProducts as $imod) {
	$tabid = getTabid($imod);
	if ($tabid > 0) {
		$tabResult = $adb->pquery("SELECT tabid FROM ".$table_prefix."_tab_info WHERE tabid=? AND prefname='is_product'", array($tabid));
		if ($adb->num_rows($tabResult) > 0) {
			$adb->pquery("UPDATE ".$table_prefix."_tab_info SET prefvalue=? WHERE tabid=? AND prefname='is_product'", array(1,$tabid));
		} else {
			$adb->pquery('INSERT INTO '.$table_prefix.'_tab_info(tabid, prefname, prefvalue) VALUES (?,?,?)', array($tabid, 'is_product', 1));
		}
	}
}

// rebuild tabdata
Vtiger_Module::syncfile();


// translations

$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_CREATE_NEW_MODULE' => 'Crea nuovo modulo',
			'LBL_ADD_NEW_FIELD' => 'Aggiungi campo',
			'LBL_ADD_NEW_RELATED_FIELD' => 'Aggiungi campo relazione',
			'LBL_MODULE_MAKER' => 'Creazione moduli',
			'LBL_MODULE_MAKER_DESC' => 'Permette di creare moduli personalizzati',
			'LBL_MODULE_MAKER_STEP1' => 'Informazioni modulo',
			'LBL_MODULE_MAKER_STEP2' => 'Blocchi e campi',
			'LBL_MODULE_MAKER_STEP3' => 'Filtro di default',
			'LBL_MODULE_MAKER_STEP4' => 'Relazioni',
			'LBL_MODULE_MAKER_STEP5' => 'Etichette',
			'LBL_MODULE_MAKER_STEP6' => 'Avanzate',
			'LBL_NO_CUSTOM_MODULES' => 'Nessun modulo personalizzato, premi Aggiungi per crearne uno nuovo',
			'LBL_MODULELABEL' => 'Etichetta modulo',
			'LBL_MODULESINGLELABEL' => 'Etichetta modulo (singolare)',
			'LBL_MODULENAME' => 'Nome modulo (automatico)',
			'LBL_RECORD_IDENTIFIER' => 'Campo principale',
			'LBL_INVENTORYMODULE' => 'Modulo con blocco prodotti',
			'LBL_MODULELABEL_DESC' => 'Il nome del nuovo modulo',
			'LBL_MODULESINGLELABEL_DESC' => 'L\'etichetta del modulo al singolare, che identifica un singolo record. Ad esempio, se il modulo si chiama "Spese", questa etichetta potrebbe essere "Spesa"',
			'LBL_MODULENAME_DESC' => 'Il nome del modulo, usato internamente. Questo campo è calcolato automaticamente',
			'LBL_RECORD_IDENTIFIER_DESC' => 'Il nome del campo principale del modulo, ad esempio "Nome Spesa"',
			'LBL_AREA_DESC' => 'L\'area di appartenenza del modulo (opzionale) ',
			'LBL_INVENTORYMODULE_DESC' => 'Selezionare la casella se il modulo dovrà avere il blocco prodotti',
			'LBL_MMAKER_STEP1_INTRO' => 'Inserisci le informazioni di base per il nuovo modulo',
			'LBL_MMAKER_STEP2_INTRO' => 'Aggiungi o modifica i campi per il modulo',
			'LBL_MMAKER_STEP3_INTRO' => 'Configura i campi per il filtro di default',
			'LBL_MMAKER_STEP4_INTRO' => '',
			'LBL_MMAKER_STEP5_INTRO' => 'Qui puoi modificare le etichette dei campi e del modulo per le lingue installate',
			'LBL_MMAKER_STEP6_INTRO' => 'Opzioni avanzate per il modulo',
			'LBL_MMAKER_IMPORT_INTRO' => 'Seleziona il file con il modulo che desideri importare. Una volta caricato potrai modificarne le impostazioni prima di installarlo.',
			'LBL_CREATE_NEW_FIELD' => 'Aggiungi Campo',
			'LBL_RELATED_MODULES' => 'Moduli relazionati',
			'LBL_SELECT_FIELD_TO_MOVE_IN_BLOCK' => 'Selezionare i campi da spostare in questo blocco',
			'LBL_NO_FIELDS_TO_MOVE_IN_BLOCK' => 'Non ci sono campi disponibili da spostare in questo blocco',
			'LBL_FIELD_PROPERTIES' => 'Proprietà campo',
			'LBL_NO_FIELDS_FOR_FILTER' => 'Nessun campo selezionato per il filtro',
			'LBL_DUPLICATE_FIELDS_FOR_FILTER' => 'Ci sono dei campi duplicati nel filtro',
			'LBL_NO_RELATIONS' => 'Nessuna relazione, premi Aggiungi per crearne una',
			'LBL_RELATIONS_NTO1' => ' Relazioni N a 1, tramite un campo in questo modulo',
			'LBL_OTHER_RELATIONS' => 'Altre relazioni',
			'LBL_RELATION_TYPE' => 'Tipo di relazione',
			'LBL_RELATION_TYPE_NTO1' => 'N a 1',
			'LBL_RELATION_TYPE_1TON' => '1 a N',
			'LBL_RELATION_TYPE_NTON' => 'N a N',
			'LBL_RELATION_TYPE_DESC' => 'Scegli il tipo di relazione. 1 a N significa che ogni record di questo modulo può essere collegato a più record del modulo di destinazione, tramite un nuovo campo in quest\'ultimo. N a N permette invece il collegamento libero.',
			'LBL_RELATION_MODULE_DESC' => 'Il modulo relazionato',
			'LBL_RELATION_BLOCK_DESC' => 'Il blocco in cui creare il nuovo campo relazione',
			'LBL_RELATION_FIELD_DESC' => 'Il nome del nuovo campo nel modulo selezionato',
			'LBL_MMAKER_ERR_SAMEMODULERELATED' => 'Il modulo %s è presente in più di una relazione. E\' possibile avere solo 1 relazione per ogni modulo',
			'LBL_MMAKER_CANT_DELETE_INSTALLED' => 'Non è possibile eliminare un modulo installato',
			'LBL_NO_RELATIONS_FOUND' => 'Nessuna relazione presente. Crea un campo Relazione nel passaggio 2 o premi Aggiungi per creare una nuova relazione.',
			'LBL_NO_OTHER_RELATIONS_FOUND' => 'Nessun\'altra relazione presente. Premi Aggiungi per crearne una nuova.',
			'LBL_ENABLE_QUICKCREATE' => 'Abilita creazione veloce',
			'LBL_ENABLE_QUICKCREATE_DESC' => '',
			'LBL_ENABLE_IMPORT' => 'Abilita importazioni',
			'LBL_ENABLE_IMPORT_DESC' => '',
			'LBL_ENABLE_EXPORT' => 'Abilita esportazioni',
			'LBL_ENABLE_EXPORT_DESC' => '',
			'LBL_ENABLE_DUPCHECK' => 'Abilita controllo duplicati',
			'LBL_ENABLE_DUPCHECK_DESC' => '',
			'LBL_FIELD_AUTONUMBER' => 'Numerazione automatica',
			'LBL_MMAKER_INSTALLSCRIPT' => 'Script di installazione',
			'LBL_MMAKER_UNINSTALLSCRIPT' => 'Script di disinstallazione',
			'LBL_EDIT_MODULE_SCRIPTS' => 'Modifica script di installazione',
			'LBL_BUTTON_EDIT_SCRIPTS' => 'Apri editor',
			'LBL_RESTORE_CHANGED_FILES' => 'Ripristina files',
			'LBL_USEREDIT_EDIT_CODE_DESC' => 'Alcuni files sono stati modificati, pertanto non è possibile apportare modifiche alla configurazione del modulo. Per ripristinare gli script originale, premere Riprstina files',
			'LBL_NOT_ALLOWED_UPLOAD_SCRIPTS' => 'Non sei abilitato al caricamento di moduli con script personalizzati',
			'LBL_FLAG_FOR_ALL_PROFILES' => 'Questa opzione vale per tutti i profili. Una volta che il modulo sarà installato, si potrà modificare dalle Impostazioni',
			'LBL_NEXT_FLAGS_FOR_ALL_PROFILES' => 'Le seguenti opzioni verranno applicate a tutti i profili. Una volta che il modulo sarà installato, si potranno modificare dalle Impostazioni',
			'LBL_SELECT_INSTALLATION_LOG' => 'Seleziona il logfile che vuoi vedere',
			'LBL_MODULE_ALREADY_INSTALLED' => 'Il modulo è già installato',
			'LBL_RELATED_PRODUCTS' => 'Blocco prodotti',
			'LBL_MMAKER_INSTALL_ERROR' => 'Errore durante l\'installazione. Controllare i log per i dettagli',
			'LBL_MMAKER_UNINSTALL_ERROR' => 'Errore durante la rimozione. Controllare i log per i dettagli',
			'LBL_MMAKER_INSTALLING_MODULE' => 'E\' in corso l\'installazione del modulo. L\'operazione potrebbe richiedere qualche minuto.',
			'LBL_MMAKER_UNINSTALLING_MODULE' => 'E\' in corso la rimozione del modulo. Attendere.',
			'LBL_ERROR_LANGUAGE_RENAME' => 'Errore durante la creazione dei file di lingua',
			'LBL_ERROR_CREATING_INSTALL_SCRIPT' => 'Errore durante la creazione degli script di installazione',
			'LBL_EDIT_FIELD_PROPERTY_DESC' => 'Seleziona le opzioni da cambiare per il campo scelto',
			'LBL_MMAKER_RELATED_FILTER' => 'Seleziona i campi per le related lists',
			'LBL_FIRST_MODULE_DESC' => 'Il primo modulo della relazione',
		),
		'en_us' => array(
			'LBL_CREATE_NEW_MODULE' => 'Create new module',
			'LBL_ADD_NEW_FIELD' => 'Add field',
			'LBL_ADD_NEW_RELATED_FIELD' => 'Add Related field',
			'LBL_MODULE_MAKER' => 'Module Maker',
			'LBL_MODULE_MAKER_DESC' => 'Allows to create custom modules',
			'LBL_MODULE_MAKER_STEP1' => 'Module information',
			'LBL_MODULE_MAKER_STEP2' => 'Blocks and fields',
			'LBL_MODULE_MAKER_STEP3' => 'Default filter',
			'LBL_MODULE_MAKER_STEP4' => 'Relations',
			'LBL_MODULE_MAKER_STEP5' => 'Labels',
			'LBL_MODULE_MAKER_STEP6' => 'Advanced',
			'LBL_NO_CUSTOM_MODULES' => 'No custom modules found, press Add to create a new one',
			'LBL_MODULELABEL' => 'Module label',
			'LBL_MODULESINGLELABEL' => 'Singular Module label',
			'LBL_MODULENAME' => 'Module name (automatic)',
			'LBL_RECORD_IDENTIFIER' => 'Main field',
			'LBL_INVENTORYMODULE' => 'Module with products block',
			'LBL_MODULELABEL_DESC' => 'The label of the new module',
			'LBL_MODULESINGLELABEL_DESC' => 'The module\'s label in the singular form, to identify a single record. For example, if the module is called "Expenses", this label shall be "Expense"',
			'LBL_MODULENAME_DESC' => 'The name of the module, used internally. This field is calculated automatically',
			'LBL_RECORD_IDENTIFIER_DESC' => 'The main field for the module, for example "Expense Name"',
			'LBL_AREA_DESC' => 'The Area the module belongs to (optional) ',
			'LBL_INVENTORYMODULE_DESC' => 'Select the checkbox if the module will have the products block',
			'LBL_MMAKER_STEP1_INTRO' => 'Insert the basic informations for the new module',
			'LBL_MMAKER_STEP2_INTRO' => 'Add or edit the fields for the module',
			'LBL_MMAKER_STEP3_INTRO' => 'Configure the fields for the default filter',
			'LBL_MMAKER_STEP4_INTRO' => '',
			'LBL_MMAKER_STEP5_INTRO' => 'Here you can change the fields\' labels in the available languages',
			'LBL_MMAKER_STEP6_INTRO' => 'Advanced options for the module',
			'LBL_MMAKER_IMPORT_INTRO' => 'Choose the file containing the module you want to import. Once loaded, it will be possible to change its settings before installing it',
			'LBL_CREATE_NEW_FIELD' => 'Create Field',
			'LBL_RELATED_MODULES' => 'Related modules',
			'LBL_SELECT_FIELD_TO_MOVE_IN_BLOCK' => 'Select fields to move inside this block',
			'LBL_NO_FIELDS_TO_MOVE_IN_BLOCK' => 'There are no available fields to move inside this block',
			'LBL_FIELD_PROPERTIES' => 'Field properties',
			'LBL_NO_FIELDS_FOR_FILTER' => 'There are no selected fields for the filter',
			'LBL_DUPLICATE_FIELDS_FOR_FILTER' => 'There are duplicate fields in the filter',
			'LBL_NO_RELATIONS' => 'No relations, press Add to create a new one',
			'LBL_RELATIONS_NTO1' => ' Relations N to 1, through a field in this module',
			'LBL_OTHER_RELATIONS' => 'Other relations',
			'LBL_RELATION_TYPE' => 'Relation type',
			'LBL_RELATION_TYPE_NTO1' => 'N to 1',
			'LBL_RELATION_TYPE_1TON' => '1 to N',
			'LBL_RELATION_TYPE_NTON' => 'N to N',
			'LBL_RELATION_TYPE_DESC' => 'Choose the relation type. 1 to N means that every record of this module can be linked to several records of the destination module, by creating a new field in the latter. N to N allows to freely link the two modules.',
			'LBL_RELATION_MODULE_DESC' => 'The related module',
			'LBL_RELATION_BLOCK_DESC' => 'The block in which create the new relation field',
			'LBL_RELATION_FIELD_DESC' => 'The name of the new field in the selected module',
			'LBL_MMAKER_ERR_SAMEMODULERELATED' => 'The module %s is present in more than one relation. It\'s possible to have only one relation for each module',
			'LBL_MMAKER_CANT_DELETE_INSTALLED' => 'It\'s not possible to delete an installed module',
			'LBL_NO_RELATIONS_FOUND' => 'No relations are present. Create a Relation field in the step 2 or press Add do create a new relation.',
			'LBL_NO_OTHER_RELATIONS_FOUND' => 'No other relation is present. Press Add to create a new one.',
			'LBL_ENABLE_QUICKCREATE' => 'Enable Quick Create',
			'LBL_ENABLE_QUICKCREATE_DESC' => '',
			'LBL_ENABLE_IMPORT' => 'Enable Import',
			'LBL_ENABLE_IMPORT_DESC' => '',
			'LBL_ENABLE_EXPORT' => 'Enable Export',
			'LBL_ENABLE_EXPORT_DESC' => '',
			'LBL_ENABLE_DUPCHECK' => 'Enable Duplicates check',
			'LBL_ENABLE_DUPCHECK_DESC' => '',
			'LBL_FIELD_AUTONUMBER' => 'Auto record numbering',
			'LBL_MMAKER_INSTALLSCRIPT' => 'Install script',
			'LBL_MMAKER_UNINSTALLSCRIPT' => 'Uninstall script',
			'LBL_EDIT_MODULE_SCRIPTS' => 'Edit module\'s scripts',
			'LBL_BUTTON_EDIT_SCRIPTS' => 'Open editor',
			'LBL_RESTORE_CHANGED_FILES' => 'Restore files',
			'LBL_USEREDIT_EDIT_CODE_DESC' => 'Some files has been modified, so it\'s not possibile to change the module\'s configuration. To restore the original scripts, press Restore files',
			'LBL_NOT_ALLOWED_UPLOAD_SCRIPTS' => 'You are not allowed to load modules with custom scripts',
			'LBL_FLAG_FOR_ALL_PROFILES' => 'This option affect all profiles. Once the module is installed, it can be changed from the Settings page',
			'LBL_NEXT_FLAGS_FOR_ALL_PROFILES' => 'The following options will affect all profiles. Once the module is installed, they can be changed from the Settings page',
			'LBL_SELECT_INSTALLATION_LOG' => 'Select the log you want to see',
			'LBL_MODULE_ALREADY_INSTALLED' => 'The module is already installed',
			'LBL_RELATED_PRODUCTS' => 'Products block',
			'LBL_MMAKER_INSTALL_ERROR' => 'Error during installation. See the log for details',
			'LBL_MMAKER_UNINSTALL_ERROR' => 'Error during module removal. See the log for details',
			'LBL_MMAKER_INSTALLING_MODULE' => 'The module is being installed. This operation may take several minutes. Please wait',
			'LBL_MMAKER_UNINSTALLING_MODULE' => 'The module is being removed, please wait',
			'LBL_ERROR_LANGUAGE_RENAME' => 'Error during language files creation',
			'LBL_ERROR_CREATING_INSTALL_SCRIPT' => 'Error during install scripts creation',
			'LBL_EDIT_FIELD_PROPERTY_DESC' => 'Select the properties to change for this field',
			'LBL_MMAKER_RELATED_FILTER' => 'Select the fields for the related lists',
			'LBL_FIRST_MODULE_DESC' => 'The first module in the relation',
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_AREA' => 'Area',
			'LBL_TOO_LONG' => 'è troppo lungo',
			'LBL_NO_SPECIAL_CHARS_IN_FIELD' => 'Non sono ammessi caratteri speciali per il campo %s',
			'LBL_MODULE_EXISTING' => 'Esiste già un modulo con questo nome',
			'LBL_INSTALL' => 'Installa',
			'LBL_UNINSTALL' => 'Disinstalla',
			'LBL_INSTALLED' => 'Installato',
			'LBL_DESCRIPTION_INFORMATION' => 'Informazioni Descrizione',
			'LBL_SAVE_FILE' => 'Salva il file',
			'LBL_CHOOSE_FILE' => 'Scegli il file',
			'LBL_NOT_ALLOWED_OPERATION' => 'Non sei autorizzato a compiere questa operazione',
			'LBL_INSTALLATION_LOGS' => 'Log di installazione',
			'LBL_INSTALLATION' => 'Installazione',
			'LBL_UNINSTALLATION' => 'Disinstallazione',
			'LBL_DIRECTORY_NOT_WRITEABLE' => 'La directory non ha i permessi di scrittura',
			'LBL_DIRECTORY_NOT_READABLE' => 'La directory non ha i permessi di lettura',
			'LBL_DIRECTORY_NOT_WRITEABLE_N' => 'La directory %s non ha i permessi di scrittura',
			'LBL_DIRECTORY_NOT_READABLE_N' => 'La directory %s non ha i permessi di lettura',
			'LBL_COPY_FILES_ERROR' => 'Errore durante la copia dei files',
			'LBL_RENAME_FILE_ERROR' => 'Errore durante la rinominazione del file',
			'LBL_NUMBER_WITH_DECIMALS' => 'Numero con decimali',
			'LBL_RELATIONS' => 'Relazioni',
			'LBL_FIRST_MODULE' => 'Primo modulo',
		),
		'en_us' => array(
			'LBL_AREA' => 'Area',
			'LBL_TOO_LONG' => 'is too long',
			'LBL_NO_SPECIAL_CHARS_IN_FIELD' => 'Special characters are not allowed for the field %s',
			'LBL_MODULE_EXISTING' => 'There is already a module with this name',
			'LBL_INSTALL' => 'Install',
			'LBL_UNINSTALL' => 'Uninstall',
			'LBL_INSTALLED' => 'Installed',
			'LBL_DESCRIPTION_INFORMATION' => 'Description Information',
			'LBL_SAVE_FILE' => 'Save the file',
			'LBL_CHOOSE_FILE' => 'Choose the file',
			'LBL_NOT_ALLOWED_OPERATION' => 'You are not allowed to do this operation',
			'LBL_INSTALLATION_LOGS' => 'Installation logs',
			'LBL_INSTALLATION' => 'Install',
			'LBL_UNINSTALLATION' => 'Uninstall',
			'LBL_DIRECTORY_NOT_WRITABLE' => 'The directory is not writeable',
			'LBL_DIRECTORY_NOT_READABLE' => 'The directory is not readable',
			'LBL_DIRECTORY_NOT_WRITABLE_N' => 'The directory %s is not writeable',
			'LBL_DIRECTORY_NOT_READABLE_N' => 'The directory %s is not readable',
			'LBL_COPY_FILES_ERROR' => 'Error during files copy',
			'LBL_RENAME_FILE_ERROR' => 'Errore durante file rename',
			'LBL_NUMBER_WITH_DECIMALS' => 'Number with decimals',
			'LBL_RELATIONS' => 'Relations',
			'LBL_FIRST_MODULE' => 'First module',
		)
	),
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_TOO_LONG' => '%s e` troppo lungo',
			'LBL_NAME' => 'Nome',
			'LBL_NAME_S' => 'Nome %s',
			'LBL_FILL_ALL_FIELDS' => 'Inserire i valori per tutti i campi richiesti',
			'LBL_FILTER_FIELD_MORE_THAN_ONCE' => 'Hai selezionato lo stesso campo pi&ugrave; di una volta. I campi devono essere diversi',
			'LBL_SELECT_AT_LEAST_ONE_FIELD' => 'Seleziona almeno un campo',
			'LBL_MMAKER_CONFIRM_RESET' => 'Sicuro di voler ripristinare i files allo stato iniziale? Verranno perse eventuali modifiche.',
			'LBL_WANT_TO_SAVE_PENDING_CHANGES' => 'Vuoi salvare le modifiche effettuate?',
			'LBL_SURE_TO_UNINSTALL_MODULE' => 'Disinstallando il modulo, ne verranno rimossi tutti i record. Procedere?',
			'LBL_TOO_MANY_UITYPE4' => 'E\' presente pi&ugrave; di un campo di tipo Numerazione Automatica. E\' possibile crearne solo uno per modulo',
			'LBL_SAMEMODULERELATED' => 'Il modulo %s &egrave; presente in pi&ugrave; di un campo relazione. E\' possible avere solo una relazione per ogni modulo collegato',
		),
		'en_us' => array(
			'LBL_TOO_LONG' => '%s is too long',
			'LBL_NAME' => 'Name',
			'LBL_NAME_S' => '%s name',
			'LBL_FILL_ALL_FIELDS' => 'Please fill all the required fields',
			'LBL_FILTER_FIELD_MORE_THAN_ONCE' => 'You selected the same field more than once. The fields must be all different',
			'LBL_SELECT_AT_LEAST_ONE_FIELD' => 'Please select at least one field',
			'LBL_MMAKER_CONFIRM_RESET' => 'Are you sure to restore the files to their original state? All modifications will be lost.',
			'LBL_WANT_TO_SAVE_PENDING_CHANGES' => 'Do you want to save the pending modifications?',
			'LBL_SURE_TO_UNINSTALL_MODULE' => 'Uninstalling the module will remove all of its records. Do you want to proceed?',
			'LBL_TOO_MANY_UITYPE4' => 'There is more than one Auto Numbering field. It\'s possible to create only one of them per module',
			'LBL_SAMEMODULERELATED' => 'The module %s is present in more than one relation field. It\'s possible to have only one relation for each module',
		)
	),
);


foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}


// labels for language editor and module maker
SDK::setLanguageEntries('Settings','LBL_TRANS_ALL',Array('en_us'=>'All','it_it'=>'Tutti'));
SDK::setLanguageEntries('Settings','LBL_TRANS_LANGUAGE',Array('en_us'=>'Language','it_it'=>'Lingua'));
SDK::setLanguageEntries('Settings','LBL_TRANS_MANDATORY',Array('en_us'=>'Fields marked with * are required','it_it'=>'I campi contrassegnati con * sono obbligatori'));
SDK::setLanguageEntries('Settings','LBL_TRANS_MODULE',Array('en_us'=>'Module','it_it'=>'Modulo'));
SDK::setLanguageEntries('Settings','LBL_TRANS_LABEL',Array('en_us'=>'System label','it_it'=>'Etichetta di sistema'));
SDK::setLanguageEntries('Settings','LBL_TRANS_SEARCH',Array('en_us'=>'Search','it_it'=>'Cerca'));
SDK::setLanguageEntries('Settings','LBL_TRANS_ACTIONS',Array('en_us'=>'Actions','it_it'=>'Azioni'));
SDK::setLanguageEntries('Settings','LBL_TRANS_SHOW_ONLY_FIELDS',Array('en_us'=>'Show only module fields/fieldvalues','it_it'=>'Visualizza solamente i campi/valori dei moduli'));
SDK::setLanguageEntries('Settings','LBL_TRANS_PICKLIST_FIELDS',Array('en_us'=>'Picklist fields','it_it'=>'Campi picklist'));
SDK::setLanguageEntries('Settings','LBL_TRANS_DUPLICATE_MESSAGE_BEFORE',Array('en_us'=>'The Label you selected is already translated with values','it_it'=>'L\'etichetta che hai selezionato risulta gia\' tradotta con valori'));
SDK::setLanguageEntries('Settings','LBL_TRANS_DUPLICATE_MESSAGE_AFTER',Array('en_us'=>'Do you want to overwrite?','it_it'=>'Vuoi sovrascrivere?'));
SDK::setLanguageEntries('Settings','LBL_TRANS_ERR',Array('en_us'=>'Generic Error','it_it'=>'Errore generico'));
SDK::setLanguageEntries('Settings','LBL_TRANS_APP_STRINGS',Array('en_us'=>'General','it_it'=>'Generale'));
SDK::setLanguageEntries('Settings','LBL_TRANS_APP_LIST_STRINGS',Array('en_us'=>'JSON Strings','it_it'=>'Liste JSON'));
SDK::setLanguageEntries('Settings','LBL_TRANS_APP_CURRENCY_STRINGS',Array('en_us'=>'Currency strings','it_it'=>'Stringhe valuta'));
SDK::setLanguageEntries('Settings','LBL_TRANS_NONE',Array('en_us'=>'None','it_it'=>'Nessuno'));
SDK::setLanguageEntries('Settings','LBL_TRANS_SHOW_ONLY_NOT_TRANSLATED',Array('en_us'=>'Show untranslated entries','it_it'=>'Visualizza voci non tradotte'));
SDK::setLanguageEntries('Settings','LBL_TRANS_FILTER_NAME',Array('en_us'=>'Restrictions','it_it'=>'Restrizioni'));
SDK::setLanguageEntries('Settings','LBL_TRANS_FILTER_ALL',Array('en_us'=>'None','it_it'=>'Nessuna'));
SDK::setLanguageEntries('Settings','LBL_TRANS_FILTER_FIELDS',Array('en_us'=>'Show only fields','it_it'=>'Mostra solamente i campi'));
SDK::setLanguageEntries('Settings','LBL_TRANS_FILTER_FIELDVALUES',Array('en_us'=>'Show only fieldvalues','it_it'=>'Mostra solamente i valori delle picklist dei campi'));
SDK::setLanguageEntries('Settings','LBL_TRANS_FILTER_OTHER',Array('en_us'=>'Show other','it_it'=>'Mostra altro'));
SDK::setLanguageEntries('Settings','LBL_TRANS_LANGUAGEEDITOR',Array('en_us'=>'Languages Editor','it_it'=>'Editor Lingue'));
SDK::setLanguageEntries('Settings','LBL_TRANS_LANGUAGEEDITOR_DES',Array('en_us'=>'Manage translation of all entries in CRM','it_it'=>'Gestisci le traduzioni di tutte le voci presenti nel CRM'));




/* crmv@65455 - data importer */


// add the link in the settings
$block = Vtiger_SettingsBlock::getInstance('LBL_STUDIO');

$res = $adb->pquery("select fieldid from {$table_prefix}_settings_field where name = ?", array('LBL_DATA_IMPORTER'));
if ($res && $adb->num_rows($res) == 0) {
	$field = new Vtiger_SettingsField();
	$field->name = 'LBL_DATA_IMPORTER';
	$field->iconpath = 'data_import.png';
	$field->description = 'LBL_DATA_IMPORTER_DESC';
	$field->linkto = 'index.php?module=Settings&action=DataImporter&parenttab=Settings';
	$block->addField($field);
}

// create dataimport folder
@mkdir('dataimport', 0755);
// create the htaccess file
file_put_contents('dataimport/.htaccess', "Deny from All\n");

@mkdir('plugins/dataimporter', 0755);
file_put_contents('plugins/dataimporter/.htaccess', "Deny from All\n");

// create the table for the saved imports
$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_dataimporter">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="id" type="I" size="19">
				<key/>
			</field>
			<field name="module" type="C" size="63">
				<NOTNULL/>
			</field>
			<field name="invmodule" type="C" size="63" />
			<field name="createdtime" type="T">
				<NOTNULL/>
				<DEFAULT value="0000-00-00 00:00:00"/>
			</field>
			<field name="modifiedtime" type="T">
				<NOTNULL/>
				<DEFAULT value="0000-00-00 00:00:00"/>
			</field>
			<field name="enabled" type="I" size="1">
				<NOTNULL/>
				<DEFAULT value="0"/>
			</field>
			<field name="running" type="I" size="1">
				<NOTNULL/>
				<DEFAULT value="0"/>
			</field>
			<field name="lastimport" type="T">
				<NOTNULL/>
				<DEFAULT value="0000-00-00 00:00:00"/>
			</field>
			<field name="notifyto" type="I" size="19">
				<NOTNULL/>
				<DEFAULT value="0"/>
			</field>
			<field name="override_runnow" type="I" size="1">
				<NOTNULL/>
				<DEFAULT value="0"/>
			</field>
			<field name="override_abort" type="I" size="1">
				<NOTNULL/>
				<DEFAULT value="0"/>
			</field>
			<field name="errors" type="I" size="1">
				<NOTNULL/>
				<DEFAULT value="0"/>
			</field>
			<field name="srcinfo" type="XL"/>
			<field name="mapping" type="XL"/>
			<field name="scheduling" type="XL"/>
			<index name="dataimporter_enabled_idx">
				<col>enabled</col>
			</index>
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_dataimporter')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}


// add notification type
$modNot = CRMEntity::getInstance('ModNotifications');
if (method_exists($modNot, 'addNotificationType')) {
	$modNot->addNotificationType('Import Error', 'Import Error', 0);
} else {
	$adb->pquery("INSERT INTO {$table_prefix}_modnotifications_types(id, type, action, custom) VALUES(?, ?, ?, ?)", array($adb->getUniqueID("{$table_prefix}_modnotifications_types"), 'Import Error', 'Import Error', 0));
}

// add check cronjob
require_once('include/utils/CronUtils.php');
$cronname = 'DataImporterCheck';
$CU = CronUtils::getInstance();
// install cronjob
$cj = CronJob::getByName($cronname);
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = $cronname;
	$cj->active = 1;
	$cj->singleRun = false;
}
$cj->timeout = 1200;	// 20 min
$cj->repeat = 600;		// 10min
$cj->fileName = 'cron/modules/DataImporter/DataImporterCheck.service.php';
$CU->insertCronJob($cj);


// translations
$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_DATA_IMPORTER' => 'Importazione dati',
			'LBL_DATA_IMPORTER_DESC' => 'Configura le importazioni dati automatizzate da fonti esterne',
			'LBL_NO_DATA_IMPORTER' => 'Nessuna importazione configurata, premi Aggiungi per crearne una nuova',
			'LBL_DATA_IMPORTER_STEP1' => 'Modulo',
			'LBL_DATA_IMPORTER_STEP2' => 'Sorgente dati',
			'LBL_DATA_IMPORTER_STEP3' => 'Parametri sorgente',
			'LBL_DATA_IMPORTER_STEP4' => 'Tabella sorgente',
			'LBL_DATA_IMPORTER_STEP5' => 'Mappatura campi',
			'LBL_DATA_IMPORTER_STEP6' => 'Pianifica avvio',
			'LBL_DATA_IMPORTER_STEP7' => 'Notifiche',
			'LBL_DIMPORT_STEP1_INTRO' => '',
			'LBL_DIMPORT_STEP2_INTRO' => 'Scegli il tipo di sorgente dati',
			'LBL_DIMPORT_STEP3_INTRO' => 'Imposta i parametri di connessione alla sorgente dati scelta',
			'LBL_DIMPORT_STEP4_INTRO' => 'Scegli la tabella di origine da cui importare i dati',
			'LBL_DIMPORT_STEP5_INTRO' => 'Imposta la mappatura campi per l\'importazione. E\' possibile scegliere il formato per alcuni tipi di dato e delle semplici operazioni su di essi',
			'LBL_DIMPORT_STEP6_INTRO' => 'Quando vuoi avviare questa importazione automatica?',
			'LBL_DIMPORT_STEP7_INTRO' => 'Scegli a quale utente inviare notifiche di eventuali errori o malfunzionamenti dell\'importazione',
			'LBL_DIMPORT_MODULE_DESC' => 'Seleziona il modulo di destinazione dei dati da importare',
			'LBL_DIMPORT_MODULE_DESC_RO' => 'Questo è il modulo di destinazione dei dati. Non è più modificabile',
			'LBL_INVALID_MODULE' => 'Modulo non valido',
			'LBL_INVALID_SOURCETYPE' => 'Tipo di sorgente dati non valido',
			'LBL_DIMPORT_CSVPATH' => 'Percorso del file CSV',
			'LBL_DIMPORT_CSVPATH_DESC' => 'Il percorso relativo alla directory dataimport/ del file CSV da importare, ad esempio file.csv (dataimport/file.csv). Il nome può contenere caratteri jolly, come * o ? per includere più files. In caso di più files, la codifica e la struttura deve essere la stessa per tutti.',
			'LBL_CSVPATH_MUST_BE_ABSOLUTE' => 'Il percorso del file CSV deve essere assoluto',
			'LBL_CSVPATH_MUST_NOT_BE_ABSOLUTE' => 'Il percorso del file CSV non può essere assoluto',
			'LBL_UNABLE_TO_CONNECT_TO_DB' => 'Impossibile collegarsi al database, verificare i parametri di connessione',
			'LBL_IMPORT_TABLE' => 'Tabella di origine',
			'LBL_OR_WRITE_IMPORT_QUERY' => 'In alternativa, scrivi la query di importazione',
			'LBL_INVALID_QUERY' => 'La query non è valida',
			'LBL_NO_QUERY_RESULT' => 'La query non ha restituito risultati.',
			'LBL_ALTER_QUERY_TO_GET_ROWS' => 'Modifica la query o la tabella sorgente in modo da ottenere almeno una riga.',
			'LBL_VALUE_FORMAT' => 'Formato del valore',
			'LBL_CANT_DISABLE_RUNNING_IMPORT' => 'Non e` possibile disabilitare un import in corso',
			'LBL_CANT_DELETE_RUNNING_IMPORT' => 'Non e` possibile eliminare un import in corso',
			'LBL_LAST_IMPORT' => 'Ultima importazione',
			'LBL_NEXT_IMPORT_TIME' => 'Prossimo avvio',
			'LBL_REPEAT_EVERY' => 'Ripeti ogni',
			'LBL_START_EVERY' => 'Avvia ogni',
			'LBL_DIMPORT_NOTIFYTO_DESC' => 'L\'utente scelto riceverà una notifica tramite email o tramite le notifiche del VTE, a seconda delle impostazioni dell\'utente',
			'LBL_CHOOSE_CSV_TITLE' => 'File da importare',
			'LBL_CHOOSE_CSV_DESC' => 'Scegli il file che vuoi importare. Se la lista è vuota, carica i files nella cartella dataimport/',
			'LBL_INVALID_FILE_EXTENSION' => 'Estensione del file non valida',
			'LBL_NO_MATCHING_FILES' => 'Nessun file corrispondente trovato',
			'LBL_DEFAULT_FIELDS_CREATE' => 'Campi di default in creazione',
			'LBL_DEFAULT_FIELDS_UPDATE' => 'Campi di default in modifica',
			'LBL_LAST_IMPORT_LOG' => 'Resoconto ultima importazione',
			'LBL_IMPORT_IS_ALREADY_RUNNING' => 'L\'importazione è già in corso',
			'LBL_ABORT_IMPORT' => 'Interrompi',
			'LBL_IMPORT_ERROR_SUBJECT' => 'Errore Importazione Automatica',
			'LBL_IMPORT_ERROR_NOTIF_DESC' => 'Controllare i log dell\'importazione da <a href="index.php?module=Settings&action=DataImporter">questa</a> pagina',
			'LBL_IMPORT_NO_LOCAL_INFILE' => 'Affinchè le importazioni funzionino è necessario inserire il parametro local-infile=1 nel file my.cnf del server MySql',
			'LBL_IMPORT_LINKKEY_FIELD' => 'Campo di collegamento',
			'LBL_CANT_IMPORT_PRODUCT_ROWS' => 'Non è possibile importare le righe prodotto se non sono configurate importazioni per i prodotti e per un modulo con il blocco prodotti',
			'LBL_ASSOCIATED_MODULE_DESC' => 'Seleziona il modulo che contiene il blocco prodotti da importare. Deve essere un modulo già configurato per l\'importazione',
			'LBL_ASSOCIATED_MODULE_DESC_RO' => 'Il modulo che contiene il blocco prodotti da importare',
			'LBL_DIMPORT_INV_ROWS_SQL_NOTE' => 'Nota: assicurati che nella query di estrazione, le righe dei prodotti siano ordinate secondo la testata di appartenenza',
			'Line Total' => 'Totale riga',
			'LBL_RELATED_PRODUCTS' => 'Blocco Prodotti',
			'LBL_ADD_NEW_FIELD' => 'Aggiungi campo',
			// formats
			'LBL_DIMPORT_FORMAT_PHONE' => 'Formato Telefono',
			'LBL_DIMPORT_FORMAT_EMAIL' => 'Formato Email',
			'LBL_DIMPORT_FORMAT_INTEGER_01' => 'Intero 0/1',
			'LBL_DIMPORT_FORMAT_INTEGER_NULL' => 'Valore vuoto/non vuoto',
			'LBL_DIMPORT_FORMAT_DATETIME' => 'Data e Ora',
			// formulas
			'LBL_DIMPORT_FORMULA_PREPEND' => 'Anteponi',
			'LBL_DIMPORT_FORMULA_APPEND' => 'Accoda',
			'LBL_DIMPORT_FORMULA_ADD' => 'Aggiungi',
			'LBL_DIMPORT_FORMULA_SUBTRACT' => 'Sottrai',
			'LBL_DIMPORT_FORMULA_YEAR' => 'Estrai Anno',
			'LBL_DIMPORT_FORMULA_YEARMONTH' => 'Estrai Anno e Mese',
		),
		'en_us' => array(
			'LBL_DATA_IMPORTER' => 'Data Import',
			'LBL_DATA_IMPORTER_DESC' => 'Configure automatic data import from external sources',
			'LBL_NO_DATA_IMPORTER' => 'No import configured, press Add to create a new one',
			'LBL_DATA_IMPORTER_STEP1' => 'Module',
			'LBL_DATA_IMPORTER_STEP2' => 'Data source',
			'LBL_DATA_IMPORTER_STEP3' => 'Source parameters',
			'LBL_DATA_IMPORTER_STEP4' => 'Source table',
			'LBL_DATA_IMPORTER_STEP5' => 'Field mapping',
			'LBL_DATA_IMPORTER_STEP6' => 'Schedule import',
			'LBL_DATA_IMPORTER_STEP7' => 'Notifications',
			'LBL_DIMPORT_STEP1_INTRO' => '',
			'LBL_DIMPORT_STEP2_INTRO' => 'Choose the type of data source',
			'LBL_DIMPORT_STEP3_INTRO' => 'Choose the connection parameters to the selected data source',
			'LBL_DIMPORT_STEP4_INTRO' => 'Choose the source table from which import the data',
			'LBL_DIMPORT_STEP5_INTRO' => 'Set the field mapping for the import. It\'s possible to choose the input format for some data type and some basic operations on them',
			'LBL_DIMPORT_STEP6_INTRO' => 'When do you want to execute this automatic import',
			'LBL_DIMPORT_STEP7_INTRO' => 'Choose the user that will receive notifications about errors or failures in the import process',
			'LBL_DIMPORT_MODULE_DESC' => 'Select the destination module for the data to import',
			'LBL_DIMPORT_MODULE_DESC_RO' => 'This is the destination module. It can\'t be changed now',
			'LBL_INVALID_MODULE' => 'Invalid module',
			'LBL_INVALID_SOURCETYPE' => 'Invalid data source type',
			'LBL_DIMPORT_CSVPATH' => 'Path of the CSV file',
			'LBL_DIMPORT_CSVPATH_DESC' => 'The path relative to the dataimport/ directory where the CSV file is, for example file.csv (dataimport/file.csv). The path can contain wildcard character, like * or ?, to include several files. In case of more than one file, all the files must have the same structure and encoding',
			'LBL_CSVPATH_MUST_BE_ABSOLUTE' => 'The CSV path must be absolute',
			'LBL_CSVPATH_MUST_NOT_BE_ABSOLUTE' => 'The CSV path must not be absolute',
			'LBL_UNABLE_TO_CONNECT_TO_DB' => 'Unable to connect to database, please verify connection parameters',
			'LBL_IMPORT_TABLE' => 'Source table',
			'LBL_OR_WRITE_IMPORT_QUERY' => 'Or write your own import query',
			'LBL_INVALID_QUERY' => 'The query is not valid',
			'LBL_NO_QUERY_RESULT' => 'The query didn\'t return any result.',
			'LBL_ALTER_QUERY_TO_GET_ROWS' => 'Please alter the query or the source table tin order to obtain at least one row.',
			'LBL_VALUE_FORMAT' => 'Value format',
			'LBL_CANT_DISABLE_RUNNING_IMPORT' => 'It\'s not possible to disable a running import',
			'LBL_CANT_DELETE_RUNNING_IMPORT' => 'It\'s not possible to delete a running import',
			'LBL_LAST_IMPORT' => 'Last import',
			'LBL_NEXT_IMPORT_TIME' => 'Next import',
			'LBL_REPEAT_EVERY' => 'Repeat every',
			'LBL_START_EVERY' => 'Start every',
			'LBL_DIMPORT_NOTIFYTO_DESC' => 'The user will receive the notification by email or by VTE Notifications, depending on its settings',
			'LBL_CHOOSE_CSV_TITLE' => 'File to import',
			'LBL_CHOOSE_CSV_DESC' => 'Choose the file you want to import. If the list is empty, upload a file in the dataimport/ folder.',
			'LBL_INVALID_FILE_EXTENSION' => 'Invalid file extension',
			'LBL_NO_MATCHING_FILES' => 'No matching files found',
			'LBL_DEFAULT_FIELDS_CREATE' => 'Default fields in creation',
			'LBL_DEFAULT_FIELDS_UPDATE' => 'Default fields in update',
			'LBL_LAST_IMPORT_LOG' => 'Last import log',
			'LBL_IMPORT_IS_ALREADY_RUNNING' => 'The import is already running',
			'LBL_ABORT_IMPORT' => 'Abort',
			'LBL_IMPORT_ERROR_SUBJECT' => 'Automatic Import Error',
			'LBL_IMPORT_ERROR_NOTIF_DESC' => 'Check the import log from <a href="index.php?module=Settings&action=DataImporter">this</a> page',
			'LBL_IMPORT_NO_LOCAL_INFILE' => 'To make sure the imports work, it\'s necessary to enable the parameter local-infile=1 in the file my.cnf in the the MySql server',
			'LBL_IMPORT_LINKKEY_FIELD' => 'Link field',
			'LBL_CANT_IMPORT_PRODUCT_ROWS' => 'It\'s not possible to import product rows without a configured import for Products and a module with the product block',
			'LBL_ASSOCIATED_MODULE_DESC' => 'Select the module which contains the products block to import. It must be a module already configured for the import',
			'LBL_ASSOCIATED_MODULE_DESC_RO' => 'The module which contains the products block to import',
			'LBL_DIMPORT_INV_ROWS_SQL_NOTE' => 'Note: in the query, be sure to order the products rows by their heading record',
			'Line Total' => 'Line Total',
			'LBL_RELATED_PRODUCTS' => 'Products Block',
			'LBL_ADD_NEW_FIELD' => 'Add field',
			// formats
			'LBL_DIMPORT_FORMAT_PHONE' => 'Phone Validator',
			'LBL_DIMPORT_FORMAT_EMAIL' => 'Email Validator',
			'LBL_DIMPORT_FORMAT_INTEGER_01' => 'Integer 0/1',
			'LBL_DIMPORT_FORMAT_INTEGER_NULL' => 'Empty/Non Empty value',
			'LBL_DIMPORT_FORMAT_DATETIME' => 'Date and Time',
			// formulas
			'LBL_DIMPORT_FORMULA_PREPEND' => 'Prepend',
			'LBL_DIMPORT_FORMULA_APPEND' => 'Append',
			'LBL_DIMPORT_FORMULA_ADD' => 'Add',
			'LBL_DIMPORT_FORMULA_SUBTRACT' => 'Subtract',
			'LBL_DIMPORT_FORMULA_YEAR' => 'Extract Year',
			'LBL_DIMPORT_FORMULA_YEARMONTH' => 'Extract Year and Month',
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_SOURCE' => 'Sorgente',
			'LBL_DATABASE_TYPE' => 'Tipo Database',
			'LBL_HOSTNAME' => 'Host Name',
			'LBL_DATABASE_NAME' => 'Nome Database',
			'LBL_FILE_NOT_READABLE' => 'Il file non è leggibile',
			'LBL_KEY_FIELD' => 'Campo Chiave',
			'LBL_AT_HOUR' => 'alle',
			'LBL_AT_MINUTE' => 'al minuto',
			'LBL_DAY' => 'Giorno',
			'LBL_DAYS' => 'Giorni',
			'LBL_HOURS' => 'Ore',
			'LBL_MINUTE' => 'Minuto',
			'LBL_MINUTES' => 'Minuti',
			'LBL_START_NOW' => 'Avvia ora',
			'LBL_ASSOCIATED_MODULE' => 'Modulo associato',
			'LBL_FORWARD' => 'Avanti',
			'LBL_AUTOMATIC' => 'Automatico',
		),
		'en_us' => array(
			'LBL_SOURCE' => 'Source',
			'LBL_DATABASE_TYPE' => 'Database Type',
			'LBL_HOSTNAME' => 'Host Name',
			'LBL_DATABASE_NAME' => 'Database Name',
			'LBL_FILE_NOT_READABLE' => 'The file is not readable',
			'LBL_KEY_FIELD' => 'Key Field',
			'LBL_AT_HOUR' => 'at',
			'LBL_AT_MINUTE' => 'at minute',
			'LBL_DAY' => 'Day',
			'LBL_DAYS' => 'Days',
			'LBL_HOURS' => 'Hours',
			'LBL_MINUTE' => 'Minute',
			'LBL_MINUTES' => 'Minutes',
			'LBL_START_NOW' => 'Start now',
			'LBL_ASSOCIATED_MODULE' => 'Parent module',
			'LBL_FORWARD' => 'Next',
			'LBL_AUTOMATIC' => 'Automatic',
		)
	),
	'ModNotifications' => array(
		'it_it' => array(
			'Import Error' => 'Errore importazione',
		),
		'en_us' => array(
			'Import Error' => 'Import Error',
		)
	),
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_PLEASE_SELECT_MODULE' => 'Seleziona un modulo',
			'LBL_PLEASE_SELECT_VALUE' => 'Seleziona un valore',
			'LBL_FIELD_IS_NUMERIC' => 'Il campo %s deve essere un numero',
			'LBL_FIELD_IS_INVALID' => 'Il campo %s non &egrave; corretto',
			'LBL_CSVPATH_MUST_NOT_BE_ABSOLUTE' => 'Il percorso del file CSV non pu&ograve; essere assoluto',
			'LBL_VALUE_TOO_SMALL' => 'Valore troppo piccolo',
			'LBL_VALUE_TOO_BIG' => 'Valore troppo grande',
			'LBL_INVALID_VALUE' => 'Valore non valido',
			'LBL_CONTINUE_WITHOUT_KEY_FIELD' => 'Non &egrave; stato selezionato nessun campo come chiave. Ad ogni importazione i record verranno aggiunti al CRM. Confermi?',
			'LBL_DATA_IMPORT_SCHEDULED_NOW' => 'L\'importazione &egrave; stata messa in coda. Entro pochi minuti inizier&agrave; automaticamente',
			'LBL_DATA_IMPORT_ABORTED' => 'L\'importazione &egrave; stata annullata. In caso fosse gi&agrave; iniziata, verr&agrave; interrotta entro pochi minuti',
			'LBL_SELECT_TABLE_OR_QUERY' => 'Se vuoi usare una query personalizzata, scegli "Nessuno" come tabella',
			'LBL_CANT_USE_DEFAULT_MAPPED_FIELD' => 'Non puoi usare un default in creazione o modifica per un campo che &egrave; mappato nell\'importazione',
		),
		'en_us' => array(
			'LBL_PLEASE_SELECT_MODULE' => 'Please select one module',
			'LBL_PLEASE_SELECT_VALUE' => 'Please select a value',
			'LBL_FIELD_IS_NUMERIC' => 'The field %s must be a number',
			'LBL_FIELD_IS_INVALID' => 'The field %s is not correct',
			'LBL_CSVPATH_MUST_NOT_BE_ABSOLUTE' => 'The CSV path must not be absolute',
			'LBL_VALUE_TOO_SMALL' => 'Value too small',
			'LBL_VALUE_TOO_BIG' => 'Value too big',
			'LBL_INVALID_VALUE' => 'Invalid value',
			'LBL_CONTINUE_WITHOUT_KEY_FIELD' => 'No key field has been selected. On every import run, the records will be added to the CRM. Proceed?',
			'LBL_DATA_IMPORT_SCHEDULED_NOW' => 'The import has been queued. It will start automatically in a few minutes',
			'LBL_DATA_IMPORT_ABORTED' => 'The import has been canceled. If the process has already started, it will be interrupted in a few minutes',
			'LBL_SELECT_TABLE_OR_QUERY' => 'If you want to use a custom query, please select "None" as a table',
			'LBL_CANT_USE_DEFAULT_MAPPED_FIELD' => 'You can\'y use a default field if it is already mapped for the import',
		)
	),
);


foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}
