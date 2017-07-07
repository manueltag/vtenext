<?php

/* crmv@96233 */

require_once('vtlib/Vtiger/SettingsBlock.php');
require_once('vtlib/Vtiger/SettingsField.php');

require_once('include/utils/WizardUtils.php');

$tablename = $table_prefix.'_wizards';
$schema_table =
'<schema version="0.3">
	<table name="'.$tablename.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="wizardid" type="R" size="19">
			<KEY/>
		</field>
		<field name="tabid" type="I" size="19"/>
	    <field name="createdtime" type="T">
			<DEFAULT value="0000-00-00 00:00:00"/>
		</field>
		<field name="modifiedtime" type="T">
			<DEFAULT value="0000-00-00 00:00:00"/>
		</field>
	    <field name="enabled" type="I" size="1"/>
	    <field name="name" type="C" size="127"/>
	    <field name="description" type="C" size="255"/>
	    <field name="src" type="C" size="127"/>
	    <field name="template" type="C" size="127"/>
	    <field name="config" type="XL"/>
		<index name="wizards_tabid_idx">
			<col>tabid</col>
		</index>
		<index name="wizards_name_idx">
			<col>name</col>
		</index>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($tablename)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

// insert the example wizards
$WU = WizardUtils::getInstance();
$WU->populateDefaultWizards();


$block = Vtiger_SettingsBlock::getInstance('LBL_STUDIO');
$res = $adb->pquery("select fieldid from {$table_prefix}_settings_field where name = ?", array('LBL_WIZARD_MAKER'));
if ($res && $adb->num_rows($res) == 0) {
	$field = new Vtiger_SettingsField();
	$field->name = 'LBL_WIZARD_MAKER';
	$field->iconpath = 'module_maker.png';
	$field->description = 'LBL_WIZARD_MAKER_DESC';
	$field->linkto = 'index.php?module=Settings&action=WizardMaker&parenttab=Settings';
	$block->addField($field);
}

// translations
$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_WIZARDLABEL' => 'Nome wizard',
			'LBL_WIZARDLABEL_DESC' => 'Scegli un nome per questo wizard, ad esempio: "Crea Azienda"',
			'LBL_WIZARD_MAKER' => 'Creazione wizard',
			'LBL_WIZARD_MAKER_STEP1' => 'Informazioni Wizard',
			'LBL_WIZARD_MAKER_STEP2' => 'Modulo principale',
			'LBL_WIZARD_MAKER_STEP3' => 'Campi',
			'LBL_WIZARD_MAKER_STEP4' => 'Relazioni',
			'LBL_WMAKER_STEP1_INTRO' => 'Scegli il modulo di partenza e il nome del wizard',
			//'LBL_WMAKER_STEP2_INTRO' => '',
			'LBL_WMAKER_STEP3_INTRO' => 'Scegli i campi presenti nel wizard',
			'LBL_PARENT_MODULE_DESC' => 'Scegli il modulo di partenza (opzionale)',
			'LBL_MAIN_MODULE_DESC' => 'Il modulo da creare con questo wizard',
			'LBL_RELATIONS_DESC' => 'Scegli altri moduli collegati'
		),
		'en_us' => array(
			'LBL_WIZARDLABEL' => 'Wizard\'s name',
			'LBL_WIZARDLABEL_DESC' => 'Choose a name for this wizard, for example: "Create Account"',
			'LBL_WIZARD_MAKER' => 'Wizard maker',
			'LBL_WIZARD_MAKER_STEP1' => 'Wizard information',
			'LBL_WIZARD_MAKER_STEP2' => 'Main module',
			'LBL_WIZARD_MAKER_STEP3' => 'Fields',
			'LBL_WIZARD_MAKER_STEP4' => 'Relations',
			'LBL_WMAKER_STEP1_INTRO' => 'Choose the parent module and the wizard\'s name',
			//'LBL_WMAKER_STEP2_INTRO' => '',
			'LBL_WMAKER_STEP3_INTRO' => 'Choose the fields for the wizard',
			'LBL_PARENT_MODULE_DESC' => 'Choose the starting module (optional)',
			'LBL_MAIN_MODULE_DESC' => 'The module to create with this wizard',
			'LBL_RELATIONS_DESC' => 'Choose other linked modules'
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_NO_AVAILABLE_WIZARDS' => 'Nessun wizard disponibile',
			'LBL_FIELDS' => 'Campi',
			'LBL_CHOOSE' => 'Scegli',
			'LBL_PARENT_MODULE' => 'Modulo padre',
		),
		'en_us' => array(
			'LBL_NO_AVAILABLE_WIZARDS' => 'No available wizards',
			'LBL_FIELDS' => 'Fields',
			'LBL_CHOOSE' => 'Choose',
			'LBL_PARENT_MODULE' => 'Parent module',
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


/* crmv@96155 */

// translations
$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_CHOOSE_MODHOME_BLOCK_TYPE' => 'Scegli il tipo di blocco',
			'LBL_NO_AVAILABLE_CHARTS' => 'Nessun grafico disponibile per questo modulo',
			'LBL_NO_AVAILABLE_FILTERS' => 'Nessun filtro disponibile per questo modulo'
		),
		'en_us' => array(
			'LBL_CHOOSE_MODHOME_BLOCK_TYPE' => 'Choose the block type',
			'LBL_NO_AVAILABLE_CHARTS' => 'No charts available for this module',
			'LBL_NO_AVAILABLE_FILTERS' => 'No filters available for this module',
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