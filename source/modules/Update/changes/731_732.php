<?php
$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';
$_SESSION['modules_to_update']['Charts'] = 'packages/vte/mandatory/Charts.zip';
$_SESSION['modules_to_update']['Conditionals'] = 'packages/vte/mandatory/Conditionals.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter', 'Targets'));
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan', 'ProjectMilestone', 'ProjectTask'));
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';

global $adb, $table_prefix;

// Le belle funzioni:

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


// remove old settings file
@unlink('modules/Vtiger/Settings.php');
@rmdir('modules/Vtiger');

// 0. aggiungo colonna a profile
addColumnToTable("{$table_prefix}_profile", 'mobile', 'I(1)', 'DEFAULT 0');

// 1. aggiungo colonna a role2profile
addColumnToTable("{$table_prefix}_role2profile", 'mobile', 'I(1)', 'DEFAULT 0');
dropPrimaryKey("{$table_prefix}_role2profile");
// now recreate primary key (same for every db)
$adb->query("ALTER TABLE {$table_prefix}_role2profile ADD PRIMARY KEY (roleid, profileid, mobile)");

// 2. aggiungo colonna a profile2field per ordinamento
addColumnToTable("{$table_prefix}_profile2field", 'sequence', 'I(11)', 'DEFAULT 0');

// 3. aggiungo tabella per profili/related
$schema_table =
'<schema version="0.3">
	<table name="'.$table_prefix.'_profile2related">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="profileid" type="R" size="19">
			<KEY/>
		</field>
		<field name="tabid" type="R" size="19">
			<KEY/>
		</field>
		<field name="relationid" type="R" size="19">
			<KEY/>
		</field>
		<field name="visible" type="I" size="1" />
		<field name="sequence" type="I" size="11" />
		<field name="actions" type="C" size="100" />
		<index name="profile2related_prof_idx">
			<col>profileid</col>
		</index>
		<index name="profile2related_proftab_idx">
			<col>profileid</col>
			<col>tabid</col>
		</index>
		<index name="profile2related_seq_idx">
			<col>sequence</col>
		</index>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_profile2related')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

// 4. aggiungo tabella per profili/entityname
$schema_table =
'<schema version="0.3">
	<table name="'.$table_prefix.'_profile2entityname">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="profileid" type="R" size="19">
			<KEY/>
		</field>
		<field name="tabid" type="R" size="19">
			<KEY/>
		</field>
		<field name="fieldname" type="C" size="200" />
		<index name="profile2ename_prof_idx">
			<col>profileid</col>
		</index>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_profile2entityname')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

// 4. aggiungo tabella per profili/mobile info
$schema_table =
'<schema version="0.3">
	<table name="'.$table_prefix.'_profile2mobile">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="profileid" type="R" size="19">
			<KEY/>
		</field>
		<field name="tabid" type="R" size="19">
			<KEY/>
		</field>
		<field name="cvid" type="I" size="19" />
		<field name="sortfield" type="C" size="100" />
		<field name="sortorder" type="C" size="32" />
		<field name="extrafields" type="C" size="255" />
		<field name="mobiletab" type="I" size="5" />
		<index name="profile2mobile_prof_idx">
			<col>profileid</col>
		</index>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_profile2mobile')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

// traduzioni
$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_ASSOCIATED_PROFILE_MOBILE' => 'Profilo associato (Mobile)',
			'LBL_ASSOCIATED_PROFILES_MOBILE' => 'Profili associati (Mobile)',
			'LBL_MOBILE_HANDLING' => 'Gestione Mobile',
			'LBL_MOBILE_PROFILE' => 'Profilo per App Mobile',
			'LBL_PROFILE_TO_BE_USED_FOR_MOBILE' => 'per essere utilizzato come profilo per l\'App Mobile',
			'LBL_ENABLE_ATLEAST_MOBILE_PROF' => 'Abilitare almeno un profilo Mobile',
			'LBL_ENABLE_ATLEAST_MOBILE_PROF' => 'Assegnare un profilo mobile ad almeno un ruolo',
			'LBL_VISIBLE' => 'Visibile',
			'LBL_NOT_VISIBLE' => 'Non visibile',
			'LBL_MAIN_FIELD_FOR_LISTS' => 'Campo principale per liste',
			'LBL_DEFAULT_FILTER_FOR_LISTS' => 'Filtro di default per le liste',
			'LBL_EXTRA_FIELDS' => 'Campi aggiuntivi',
			'LBL_DEFAULT_MOBILE_TAB' => 'Pannello di default',
			'LBL_FILTERS_LIST' => 'Lista filtri',
			'LBL_ORDERED_BY' => 'Ordinato per',
		),
		'en_us' => array(
			'LBL_ASSOCIATED_PROFILE_MOBILE' => 'Associated Profile (Mobile)',
			'LBL_ASSOCIATED_PROFILES_MOBILE' => 'Associated Profiles (Mobile)',
			'LBL_MOBILE_PROFILE' => 'Profile for Mobile App',
			'LBL_PROFILE_TO_BE_USED_FOR_MOBILE' => 'to be used as a profile for Mobile App',
			'LBL_ENABLE_ATLEAST_MOBILE_PROF' => 'Enable at least one Mobile profile',
			'LBL_VISIBLE' => 'Visible',
			'LBL_NOT_VISIBLE' => 'Not visible',
			'LBL_MAIN_FIELD_FOR_LISTS' => 'Main field for lists',
			'LBL_DEFAULT_FILTER_FOR_LISTS' => 'Default filter for lists',
			'LBL_DEFAULT_SORT_FIELD' => 'Default sorting',
			'LBL_EXTRA_FIELDS' => 'Additional fields',
			'LBL_DEFAULT_MOBILE_TAB' => 'Default panel',
			'LBL_FILTERS_LIST' => 'Filters list',
			'LBL_ORDERED_BY' => 'Ordered by',
		),
	),

	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_RECENTS' => 'Recenti',
			'LBL_FILTER' => 'Filtro',
		),
		'en_us' => array(
			'LBL_RECENTS' => 'Recents',
			'LBL_FILTER' => 'Filter',
		),
	)

);

foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}

// traduzioni per revision
global $enterprise_current_version,$enterprise_mode;
SDK::setLanguageEntries('APP_STRINGS', 'LBL_BROWSER_TITLE', array(
	'it_it'=>"$enterprise_mode $enterprise_current_version",
	'en_us'=>"$enterprise_mode $enterprise_current_version",
	'pt_br'=>"$enterprise_mode $enterprise_current_version",
	'de_de'=>"$enterprise_mode $enterprise_current_version"
));


?>