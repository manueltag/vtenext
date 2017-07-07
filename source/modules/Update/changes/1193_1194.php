<?php
global $adb, $table_prefix;

if (!function_exists('addColumnToTable')) {
	function addColumnToTable($tablename, $columnname, $type, $extra = '') {
		global $adb;

		// check if already present^M
		$cols = $adb->getColumnNames($tablename);
		if (in_array($columnname, $cols)) {
			return;
		}

		$col = $columnname.' '.$type.' '.$extra;
		$adb->alterTable($tablename, $col, 'Add_Column');
	}
}

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
			$res = $adb->pquery("SELECT CONSTRAINT_NAME as cn FROM all_constraints cons	WHERE cons.table_name = ? AND cons.constraint_type = 'P'", array(strtoupper($tablename)));
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

/* crmv@81019 */
/* crmv@82931 */

// languages
SDK::setLanguageEntries('ALERT_ARR', 'GROUPAGE_DUPLICATED', array(
	'it_it'=>'Raggruppamento duplicato per il campo: %s',
	'en_us'=>'Groupage duplated for the field: %s'
));

// fix missing field translations (why?)
if ($adb->isMssql()) {
	$query = "SELECT sdk_language.* FROM sdk_language
	INNER JOIN {$table_prefix}_field ON {$table_prefix}_field.fieldlabel = CAST(sdk_language.label AS varchar(50))
	WHERE sdk_language.trans_label LIKE ''";
} else {
	$query = "SELECT sdk_language.* FROM sdk_language
	INNER JOIN {$table_prefix}_field ON {$table_prefix}_field.fieldlabel = sdk_language.label
	WHERE sdk_language.trans_label = ''";
}
$result = $adb->query($query);
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByASsoc($result,-1,false)) {
		SDK::setLanguageEntry($row['module'], $row['language'], $row['label'], $row['label']);
	}
}

// clear tables
$adb->query("DELETE FROM vte_rep_count_liv1");
$adb->query("DELETE FROM vte_rep_count_liv2");
$adb->query("DELETE FROM vte_rep_count_liv3");
$adb->query("DELETE FROM vte_rep_count_levels");

// alter tables: TODO: check for mssql and oracle
if ($adb->isMysql()) $adb->query("ALTER TABLE vte_rep_count_liv1 CHANGE reportid reportid INT(19) NOT NULL FIRST");

addColumnToTable('vte_rep_count_liv2', 'reportid', 'INT(19) NULL FIRST');
addColumnToTable('vte_rep_count_liv3', 'reportid', 'INT(19) NULL FIRST');
addColumnToTable('vte_rep_count_liv3', 'id_liv3', 'INT(19) NOT NULL AFTER id_liv2');

// change primary key
dropPrimaryKey("vte_rep_count_liv3");
if ($adb->isMssql()) {
	$adb->query("ALTER TABLE vte_rep_count_liv3 ALTER COLUMN id_liv3 int NOT NULL");
	$adb->query("ALTER TABLE vte_rep_count_liv3 ADD CONSTRAINT vte_rep_count_liv3_pk PRIMARY KEY (id_liv3)");
} else {
	$adb->query("ALTER TABLE vte_rep_count_liv3 ADD PRIMARY KEY (id_liv3)");
}

// create other tables
$i = 4;
$tablename = 'vte_rep_count_liv'.$i;
$schema_table =
'<schema version="0.3">
	<table name="'.$tablename.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="reportid" type="I" size="19"/>
		<field name="id_liv1" type="I" size="19"/>
	    <field name="id_liv2" type="I" size="19"/>
	    <field name="id_liv3" type="I" size="19"/>
	    <field name="id_liv4" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="value_liv'.$i.'" type="C" size="255"/>
	    <field name="count_liv'.$i.'" type="I" size="19"/>
	    <field name="formula'.$i.'_sum" type="N" size="15.3"/>
	    <field name="formula'.$i.'_avg" type="N" size="15.3"/>
	    <field name="formula'.$i.'_min" type="N" size="15.3"/>
	    <field name="formula'.$i.'_max" type="N" size="15.3"/>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($tablename)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$i = 5;
$tablename = 'vte_rep_count_liv'.$i;
$schema_table =
'<schema version="0.3">
	<table name="'.$tablename.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="reportid" type="I" size="19"/>
		<field name="id_liv1" type="I" size="19"/>
	    <field name="id_liv2" type="I" size="19"/>
	    <field name="id_liv3" type="I" size="19"/>
	    <field name="id_liv4" type="I" size="19"/>
	    <field name="id_liv5" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="value_liv'.$i.'" type="C" size="255"/>
	    <field name="count_liv'.$i.'" type="I" size="19"/>
	    <field name="formula'.$i.'_sum" type="N" size="15.3"/>
	    <field name="formula'.$i.'_avg" type="N" size="15.3"/>
	    <field name="formula'.$i.'_min" type="N" size="15.3"/>
	    <field name="formula'.$i.'_max" type="N" size="15.3"/>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($tablename)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$i = 6;
$tablename = 'vte_rep_count_liv'.$i;
$schema_table =
'<schema version="0.3">
	<table name="'.$tablename.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="reportid" type="I" size="19"/>
		<field name="id_liv1" type="I" size="19"/>
	    <field name="id_liv2" type="I" size="19"/>
	    <field name="id_liv3" type="I" size="19"/>
	    <field name="id_liv4" type="I" size="19"/>
	    <field name="id_liv5" type="I" size="19"/>
	    <field name="id_liv6" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="value_liv'.$i.'" type="C" size="255"/>
	    <field name="count_liv'.$i.'" type="I" size="19"/>
	    <field name="formula'.$i.'_sum" type="N" size="15.3"/>
	    <field name="formula'.$i.'_avg" type="N" size="15.3"/>
	    <field name="formula'.$i.'_min" type="N" size="15.3"/>
	    <field name="formula'.$i.'_max" type="N" size="15.3"/>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($tablename)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$i = 7;
$tablename = 'vte_rep_count_liv'.$i;
$schema_table =
'<schema version="0.3">
	<table name="'.$tablename.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="reportid" type="I" size="19"/>
		<field name="id_liv1" type="I" size="19"/>
	    <field name="id_liv2" type="I" size="19"/>
	    <field name="id_liv3" type="I" size="19"/>
	    <field name="id_liv4" type="I" size="19"/>
	    <field name="id_liv5" type="I" size="19"/>
	    <field name="id_liv6" type="I" size="19"/>
	    <field name="id_liv7" type="I" size="19">
	      <KEY/>
	    </field>
	    <field name="value_liv'.$i.'" type="C" size="255"/>
	    <field name="count_liv'.$i.'" type="I" size="19"/>
	    <field name="formula'.$i.'_sum" type="N" size="15.3"/>
	    <field name="formula'.$i.'_avg" type="N" size="15.3"/>
	    <field name="formula'.$i.'_min" type="N" size="15.3"/>
	    <field name="formula'.$i.'_max" type="N" size="15.3"/>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($tablename)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

// general table
dropPrimaryKey("vte_rep_count_levels");
addColumnToTable('vte_rep_count_levels', 'id_liv3', 'INT(19) NOT NULL AFTER id_liv2');
addColumnToTable('vte_rep_count_levels', 'id_liv4', 'INT(19) NOT NULL AFTER id_liv3');
addColumnToTable('vte_rep_count_levels', 'id_liv5', 'INT(19) NOT NULL AFTER id_liv4');
addColumnToTable('vte_rep_count_levels', 'id_liv6', 'INT(19) NOT NULL AFTER id_liv5');
addColumnToTable('vte_rep_count_levels', 'id_liv7', 'INT(19) NOT NULL AFTER id_liv6');

addColumnToTable('vte_rep_count_levels', 'value_liv4', 'VARCHAR(255) NULL AFTER count_liv3');
addColumnToTable('vte_rep_count_levels', 'count_liv4', 'INT(19) NULL AFTER value_liv4');
addColumnToTable('vte_rep_count_levels', 'value_liv5', 'VARCHAR(255) NULL AFTER count_liv4');
addColumnToTable('vte_rep_count_levels', 'count_liv5', 'INT(19) NULL AFTER value_liv5');
addColumnToTable('vte_rep_count_levels', 'value_liv6', 'VARCHAR(255) NULL AFTER count_liv5');
addColumnToTable('vte_rep_count_levels', 'count_liv6', 'INT(19) NULL AFTER value_liv6');
addColumnToTable('vte_rep_count_levels', 'value_liv7', 'VARCHAR(255) NULL AFTER count_liv6');
addColumnToTable('vte_rep_count_levels', 'count_liv7', 'INT(19) NULL AFTER value_liv7');

addColumnToTable('vte_rep_count_levels', 'formula4_sum', 'DECIMAL(15,3) NULL');
addColumnToTable('vte_rep_count_levels', 'formula4_avg', 'DECIMAL(15,3) NULL');
addColumnToTable('vte_rep_count_levels', 'formula4_min', 'DECIMAL(15,3) NULL');
addColumnToTable('vte_rep_count_levels', 'formula4_max', 'DECIMAL(15,3) NULL');

addColumnToTable('vte_rep_count_levels', 'formula5_sum', 'DECIMAL(15,3) NULL');
addColumnToTable('vte_rep_count_levels', 'formula5_avg', 'DECIMAL(15,3) NULL');
addColumnToTable('vte_rep_count_levels', 'formula5_min', 'DECIMAL(15,3) NULL');
addColumnToTable('vte_rep_count_levels', 'formula5_max', 'DECIMAL(15,3) NULL');

addColumnToTable('vte_rep_count_levels', 'formula6_sum', 'DECIMAL(15,3) NULL');
addColumnToTable('vte_rep_count_levels', 'formula6_avg', 'DECIMAL(15,3) NULL');
addColumnToTable('vte_rep_count_levels', 'formula6_min', 'DECIMAL(15,3) NULL');
addColumnToTable('vte_rep_count_levels', 'formula6_max', 'DECIMAL(15,3) NULL');

addColumnToTable('vte_rep_count_levels', 'formula7_sum', 'DECIMAL(15,3) NULL');
addColumnToTable('vte_rep_count_levels', 'formula7_avg', 'DECIMAL(15,3) NULL');
addColumnToTable('vte_rep_count_levels', 'formula7_min', 'DECIMAL(15,3) NULL');
addColumnToTable('vte_rep_count_levels', 'formula7_max', 'DECIMAL(15,3) NULL');

// change a column
if ($adb->isMysql()) $adb->query("ALTER TABLE vte_rep_count_levels CHANGE value_liv3 value_liv3 VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL");


/* crmv@83340 */
/* HomePage dei moduli */
require_once('include/utils/ModuleHomeView.php');

$tablename = "{$table_prefix}_modulehome";
$schema_table =
'<schema version="0.3">
	<table name="'.$tablename.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="modhomeid" type="I" size="19">
			<KEY/>
		</field>
		<field name="userid" type="I" size="19">
			<NOTNULL/>
		</field>
		<field name="tabid" type="I" size="19">
			<NOTNULL/>
		</field>
	    <field name="name" type="C" size="127"/>
	    <index name="modhome_usertab_idx">
			<col>userid</col>
			<col>tabid</col>
		</index>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($tablename)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$tablename = "{$table_prefix}_modulehome_blocks";
$schema_table =
'<schema version="0.3">
	<table name="'.$tablename.'">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="blockid" type="I" size="19">
			<KEY/>
		</field>
		<field name="modhomeid" type="I" size="19">
			<NOTNULL/>
		</field>
	    <field name="type" type="C" size="31">
			<NOTNULL/>
		</field>
	    <field name="size" type="I" size="11"/>
	    <field name="title" type="C" size="255"/>
	    <field name="sequence" type="I" size="11"/>
	    <field name="config" type="X" />
	    <index name="modhome_homeid_idx">
			<col>modhomeid</col>
		</index>
  </table>
</schema>';
if(!Vtiger_Utils::CheckTable($tablename)) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

// add some test blocks
$MHW = ModuleHomeView::getInstance('SDK');

$MHW->deleteAllViews();
$MHW->populateDefaultViews();

/* crmv@OPER6317 */
require_once('vtlib/Vtecrm/Module.php');
$moduleInstance = Vtecrm_Module::getInstance('SDK');
Vtecrm_Link::addLink($moduleInstance->id, 'HEADERSCRIPT', 'Wizard', 'include/js/Wizard.js');

SDK::setLanguageEntries('APP_STRINGS', 'Wizard', array('it_it'=>'Wizard','en_us'=>'Wizard'));

SDK::setLanguageEntries('APP_STRINGS', 'WIZARD_NEW_OPPORTUNITY', array('it_it'=>'Nuova opportunità','en_us'=>'New opportunity'));
SDK::setLanguageEntries('APP_STRINGS', 'WZ_ChooseAccount', array('it_it'=>'Scegli un\'azienda','en_us'=>'Choose an account'));
SDK::setLanguageEntries('APP_STRINGS', 'WZ_ChooseProducts', array('it_it'=>'Scegli prodotti','en_us'=>'Choose products'));
SDK::setLanguageEntries('APP_STRINGS', 'WZ_PotentialData', array('it_it'=>'Campi opportunità','en_us'=>'Potential fields'));

SDK::setLanguageEntries('APP_STRINGS', 'WIZARD_NEW_TICKET', array('it_it'=>'Nuovo ticket','en_us'=>'New ticket'));
SDK::setLanguageEntries('APP_STRINGS', 'WZ_ChooseProduct', array('it_it'=>'Scegli un prodotto','en_us'=>'Choose a product'));
SDK::setLanguageEntries('APP_STRINGS', 'WZ_UploadDocuments', array('it_it'=>'Carica documenti','en_us'=>'Upload documents'));
SDK::setLanguageEntries('APP_STRINGS', 'WZ_TicketData', array('it_it'=>'Campi ticket','en_us'=>'Ticket fields'));

$moduleInstance = Vtecrm_Module::getInstance('Potentials');
Vtecrm_Link::addLink($moduleInstance->id, 'LISTVIEWBASIC', 'WIZARD_NEW_OPPORTUNITY', "javascript:Wizard.openWizard('Potentials',1);");

$moduleInstance = Vtecrm_Module::getInstance('HelpDesk');
Vtecrm_Link::addLink($moduleInstance->id, 'LISTVIEWBASIC', 'WIZARD_NEW_TICKET', "javascript:Wizard.openWizard('HelpDesk',2);");
