<?php 

/* crmv@104568 */

require_once('vtlib/Vtecrm/Module.php');
require_once('vtlib/Vtecrm/Panel.php');

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

// hide the 3dots icon

$CU = CRMVUtils::getInstance();
$CU->setConfigurationLayout('default_detail_view', '');
$CU->setConfigurationLayout('enable_switch_detail_view', 0);

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
}


addColumnToTable($table_prefix.'_blocks', 'panelid', 'I(19)');

// create default tabs and move blocks
initDefaultPanels();


function initDefaultPanels() {
	global $adb, $table_prefix;
	
	$adb->query("TRUNCATE TABLE {$table_prefix}_panels");
	$res = $adb->query("select distinct b.tabid, t.name from {$table_prefix}_blocks b
	inner join {$table_prefix}_tab t on b.tabid = t.tabid
	order by b.tabid");
	if ($res && $adb->num_rows($res) > 0) {
		while ($row = $adb->fetchByAssoc($res, -1, false)) {
			$module = Vtecrm_Module::getInstance($row['tabid']);
			if ($module) {
				$tab = new Vtecrm_Panel();
				$tab->label = 'LBL_TAB_MAIN';
				$tab->save($module);
				
				$inv = isInventoryModule($module);
				
				// TODO: move the blocks in a better way!
				// move all blocks into this tab!
				$blocks = Vtecrm_Block::getAllForModule($module);
				if (is_array($blocks)) {
					$bnames = array();
					foreach ($blocks as $binst) {
						/*if ($binst->label == 'LBL_RELATED_PRODUCTS') {
							// create a special tab and move the block here!
							$tabprod = new Vtecrm_Panel();
							$tabprod->label = 'LBL_TAB_RELATED_PRODUCTS';
							$tabprod->save($module);
							$tabprod->moveHereBlocks(array($binst->label));
						} else {
							$bnames[] = $binst->label;
						}*/
						$bnames[] = $binst->label;
					}
					$tab->moveHereBlocks($bnames);
				}
				
			}
		}
	}
	
}

// translations
$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_PRESENCE_AND_ORDERING'=>'Visibilità e ordinamento',
			'LBL_SORT_BY_COUNT' => 'Ordina per conteggio',
			'LBL_ADD_TAB' => 'Aggiungi tab',
			'LBL_EDIT_TAB' => 'Modifica tab',
			'LBL_TAB_NAME' => 'Nome tab',
			'LBL_CHOOSE_TRANSFER_TAB' => 'Dove vuoi spostare i blocchi di questo tab?',
			'LBL_MOVE_TO_ANOTHER_TAB' => 'Sposta in altro tab',
			'LBL_MOVE_BLOCK' => 'Sposta blocco',
			'LBL_CHOOSE_TRANSFER_TAB_BLOCK' => 'Dove vuoi spostare questo blocco?',
			'LBL_ADD_RELATEDLIST' => 'Aggiungi RelatedList',
			'LBL_MANAGE_RELATEDLIST' => 'Gestisci RelatedList',
		),
		'en_us' => array(
			'LBL_PRESENCE_AND_ORDERING'=>'Visibility and ordering',
			'LBL_SORT_BY_COUNT' => 'Sort by count',
			'LBL_ADD_TAB' => 'Add tab',
			'LBL_EDIT_TAB' => 'Edit tab',
			'LBL_TAB_NAME' => 'Tab name',
			'LBL_CHOOSE_TRANSFER_TAB' => 'Where do you want to move the blocks of this tab?',
			'LBL_MOVE_TO_ANOTHER_TAB' => 'Move to another tab',
			'LBL_MOVE_BLOCK' => 'Move block',
			'LBL_CHOOSE_TRANSFER_TAB_BLOCK' => 'Where do you want to move this block?',
			'LBL_ADD_RELATEDLIST' => 'Add RelatedList',
			'LBL_MANAGE_RELATEDLIST' => 'Manage RelatedList',
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_TAB_MAIN'=>'Informazioni',
			'LBL_TAB_RELATED_PRODUCTS'=>'Prodotti',
		),
		'en_us' => array(
			'LBL_TAB_MAIN'=>'Informations',
			'LBL_TAB_RELATED_PRODUCTS'=>'Products',
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