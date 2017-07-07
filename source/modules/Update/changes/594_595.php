<?php
global $adb, $table_prefix;

include_once('vtlib/Vtiger/Module.php');
include_once('vtlib/Vtiger/SettingsField.php');

session_start();

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

// aggiunge voce nelle impostazioni
$block = Vtiger_SettingsBlock::getInstance('LBL_STUDIO');

$res = $adb->pquery("select fieldid from {$table_prefix}_settings_field where name = ?", array('LBL_EDIT_LINKED_PICKLIST'));
if ($res && $adb->num_rows($res) == 0) {
	$field = new Vtiger_SettingsField();
	$field->name = 'LBL_EDIT_LINKED_PICKLIST';
	$field->iconpath = 'linkedpicklist.png';
	$field->description = 'LBL_EDIT_LINKED_PICKLIST_DESC';
	$field->linkto = 'index.php?module=Settings&action=LinkedPicklist&parenttab=Settings';
	$block->addField($field);
}

// add uitype 300
$pldir = 'modules/SDK/examples/uitypePicklist/';
SDK::setUitype(300,$pldir.strval(300).'.php',$pldir.strval(300).'.tpl', $pldir.strval(300).'.js');
Vtiger_Link::addLink(getTabid('SDK'),'HEADERSCRIPT','SDKUitype', $pldir.'300Utils.js');


// fix tabella link
if(Vtiger_Utils::CheckTable($table_prefix.'_linkedlist')) {
	addColumnToTable($table_prefix.'_linkedlist', 'module', 'C(100)');
	// now populate them with module
	$linksrc = array();
	$res = $adb->query("
		SELECT distinct vlink.picksrc,	vlink.pickdest, vfield1.fieldid as fieldid1, vfield2.fieldid as fieldid2, vtab1.name as modulename
		from {$table_prefix}_linkedlist vlink
		INNER JOIN {$table_prefix}_field vfield1	ON vfield1.fieldname = vlink.picksrc
		INNER JOIN {$table_prefix}_field vfield2	ON vfield2.fieldname = vlink.pickdest
		INNER JOIN {$table_prefix}_tab vtab1	ON vfield1.tabid = vtab1.tabid
		INNER JOIN {$table_prefix}_tab vtab2	ON vfield2.tabid = vtab2.tabid
		WHERE vtab1.tabid = vtab2.tabid
		ORDER BY vfield1.tabid DESC");

	if ($res) {
		while ($row = $adb->fetchByAssoc($res, -1, false)) {
			$linksrc[$row['picksrc'].'::'.$row['pickdest']] = array($row['modulename']);
		}

		if (count($linksrc) > 0) {
			foreach ($linksrc as $fieldnames=>$fieldids) {
				$fnames = explode('::', $fieldnames);
				$adb->pquery("update {$table_prefix}_linkedlist set module = ? where picksrc = ? and pickdest = ?", array($fieldids[0], $fnames[0], $fnames[1]));
			}
		}
	}

}

// stringhe
$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_EDIT_LINKED_PICKLIST' => 'Editor di Picklist Collegate',
			'LBL_EDIT_LINKED_PICKLIST_DESC' => 'Modifica i collegamenti tra le picklist',
			'LBL_FIRST_PICKLIST' => 'Prima picklist',
			'LBL_SECOND_PICKLIST' => 'Seconda picklist',
			'LBL_SELECT_DIFF_PICKLIST' => 'Seleziona 2 picklist diverse',
			'LBL_ENABLE_ALL' => 'Abilita tutto',
			'LBL_DISABLE_ALL' => 'Disabilita tutto',
			'LBL_SELECT_MODULE_FIRST' => 'Seleziona un modulo prima',
			'LBL_ERROR_CYCLE' => 'Impossibile salvare il collegamento. Le picklist formano un ciclo',
			'LBL_CONFIRM_DELETE' => 'Cancellare il collegamento?',
		),
		'en_us' => array(
			'LBL_EDIT_LINKED_PICKLIST' => 'Linked Picklist Editor',
			'LBL_EDIT_LINKED_PICKLIST_DESC' => 'Edit links between picklist',
			'LBL_FIRST_PICKLIST' => 'First picklist',
			'LBL_SECOND_PICKLIST' => 'Second picklist',
			'LBL_SELECT_DIFF_PICKLIST' => 'Select 2 different picklists',
			'LBL_ENABLE_ALL' => 'Enable all',
			'LBL_DISABLE_ALL' => 'Disable all',
			'LBL_SELECT_MODULE_FIRST' => 'Please select a module',
			'LBL_ERROR_CYCLE' => 'Unable to save the link. The selected picklists create a cycle',
			'LBL_CONFIRM_DELETE' => 'Delete the link?',
		),
		'pt_br' => array(
			'LBL_EDIT_LINKED_PICKLIST' => 'Editor de Picklist Ligadas',
			'LBL_EDIT_LINKED_PICKLIST_DESC' => 'Alterar as conexões entre a lista de opções',
			'LBL_FIRST_PICKLIST' => 'Primeira picklist',
			'LBL_SECOND_PICKLIST' => 'Segunda picklist',
			'LBL_SELECT_DIFF_PICKLIST' => 'Selecione 2 picklist diferente',
			'LBL_ENABLE_ALL' => 'Ativar tudo',
			'LBL_DISABLE_ALL' => 'Desativar tudo',
			'LBL_SELECT_MODULE_FIRST' => 'Selecione um módulo antes',
			'LBL_ERROR_CYCLE' => 'Não é possível salvar o link. A lista de opções formam um ciclo',
			'LBL_CONFIRM_DELETE' => 'Apagar a conexão?',
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
?>