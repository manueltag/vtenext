<?php
global $adb, $table_prefix;

if (!function_exists('createFields')) {
	function createFields($list) {
		global $adb, $table_prefix;

		if (!is_array($list)) return;

		$ret = array();
		foreach ($list as $k=>$arr) {

			$Vtiger_Utils_Log = true;

			$modulo = Vtiger_Module::getInstance($arr['module']);

			if (is_null($modulo)) {
				die('ERROR: Module is empty');
			}

			if (empty($arr['blockid'])) {
				$block = Vtiger_Block::getInstance($arr['block'], $modulo);
				if (is_null($block)) {
					die('ERROR: Block is empty');
				}
			} else {
				$block = Vtiger_Block::getInstance($arr['blockid']);
			}


			$field = @Vtiger_Field::getInstance($arr['name'], $modulo);

			if ($field != NULL) {
				$ret[$k] = $field;
				continue;
			} else {
				$field = new Vtiger_Field();
				$ret[$k] = $field;
			}

			// default values
			$field->name = $arr['name'];
			$field->column = $arr['name'];
			$field->label= $arr['label'];
			$field->columntype = 'C(255)';
			$field->typeofdata = 'V~O';
			$field->uitype = 1;
			$field->readonly = 1;
			$field->displaytype = 1;
			$field->masseditable = 0;
			$field->quickcreate = 1;
			$field->table = $modulo->basetable;

			if (isset($arr['table']) && !empty($arr['table']))
				$field->table = $arr['table'];

			if (isset($arr['column']) && !empty($arr['column']))
				$field->column = $arr['column'];

			if (isset($arr['readonly']) && !empty($arr['readonly']))
				$field->readonly = $arr['readonly'];

			if (isset($arr['presence']) && !empty($arr['presence']))
				$field->presence = $arr['presence'];

			if (isset($arr['columntype']) && !empty($arr['columntype']))
				$field->columntype = $arr['columntype'];

			if (isset($arr['typeofdata']) && !empty($arr['typeofdata']))
				$field->typeofdata = $arr['typeofdata'];

			if (isset($arr['uitype']) && !empty($arr['uitype']))
				$field->uitype = $arr['uitype'];

			if (isset($arr['displaytype']) && !empty($arr['displaytype']))
				$field->displaytype = $arr['displaytype'];

			if (isset($arr['quickcreate']))
				$field->quickcreate = $arr['quickcreate'];

			if (isset($arr['masseditable']))
				$field->masseditable = $arr['masseditable'];

			//se picklist aggiungo i valori
			if (isset($arr['picklist']) && !empty($arr['picklist'])){
				$field->setPicklistValues($arr['picklist']);
			}

			$block->addField($field);

			// related modules
			if (isset($arr['relatedModules']) && !empty($arr['relatedModules'])){
				$field->setRelatedModules($arr['relatedModules']);
				if (!empty($arr['relatedModulesAction'])) {
					foreach ($arr['relatedModules'] as $relmod) {
						$relinst = Vtiger_Module::getInstance($relmod);
						$relinst->setRelatedList($modulo, $arr['module'], $arr['relatedModulesAction'][$relmod], 'get_dependents_list');
					}
				}
			}

			// sdk:uitype, we need to change the uitype by hand
			if (isset($arr['sdk_uitype']) && !empty($arr['sdk_uitype'])) {
				$newtype = intval($arr['sdk_uitype']);
				$adb->pquery("update {$table_prefix}_field set uitype = ? where columnname = ? and tabid = ?", array($newtype, $arr['name'], $modulo->id));
			}

		}
		return $ret;
	}
}

$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';
$_SESSION['modules_to_update']['Charts'] = 'packages/vte/mandatory/Charts.zip';

// aggiungi campi per Charts
$fields = array(
	'chart_formula'	=> array('module'=>'Charts', 'block'=>'LBL_CHARTS_INFORMATION',	'name'=>'chart_formula',	'label'=>'Chart Formula',		'table'=>"{$table_prefix}_charts", 	'column'=>'chart_formula',	'columntype'=>'C(20)',	'typeofdata'=>'V~O', 	'uitype'=>15, 'readonly'=>1, 'masseditable'=>1, 'quickcreate'=>0, 'picklist'=>array('COUNT', 'SUM', 'AVG', 'MAX', 'MIN')),
);

if (isModuleInstalled('Charts')) {
	$fieldRet = createFields($fields);
}

$trans = array(
	'Charts' => array(
		'it_it' => array(
			'Chart Formula' => 'Formula',
			'COUNT' => 'Conteggio',
			'SUM' => 'Somma',
			'AVG' => 'Media',
			'MIN' => 'Minimo',
			'MAX' => 'Massimo',
		),
		'en_us' => array(
			'Chart Formula' => 'Formula',
			'COUNT' => 'Count',
			'SUM' => 'Sum',
			'AVG' => 'Average',
			'MIN' => 'Minimum',
			'MAX' => 'Maximum',
		),
		'pt_br' => array(
			'Chart Formula' => 'Fórmula',
			'COUNT' => 'Conta',
			'SUM' => 'Soma',
			'AVG' => 'Media',
			'MIN' => 'Mínimo',
			'MAX' => 'Máximo',
		),
	),
	'Reports' => array(
		'it_it' => array(
			'LBL_REPORT_FOLDER_EXAMPLES' => 'Esempi',
			'LBL_REPORT_FOLDER_EXAMPLES_DESC' => 'Report di esempio',
		),
		'en_us' => array(
			'LBL_REPORT_FOLDER_EXAMPLES' => 'Examples',
			'LBL_REPORT_FOLDER_EXAMPLES_DESC' => 'Example reports',
		),
		'pt_br' => array(
			'LBL_REPORT_FOLDER_EXAMPLES' => 'Exemplos',
			'LBL_REPORT_FOLDER_EXAMPLES_DESC' => 'Exemplo de Report',
		)
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_EXAMPLE' => 'Esempio',
			'LBL_EXAMPLES' => 'Esempi',
			'LBL_ELEMENTS' => 'Elementi',
			'LBL_LIST_SHOW' => 'Mostra',
		),
		'en_us' => array(
			'LBL_EXAMPLE' => 'Example',
			'LBL_EXAMPLES' => 'Examples',
			'LBL_ELEMENTS' => 'Records',
			'LBL_LIST_SHOW' => 'Show',
		),
		'pt_br' => array(
			'LBL_EXAMPLE' => 'Exemplo',
			'LBL_EXAMPLES' => 'Exemplos',
			'LBL_ELEMENTS' => 'Elementos',
			'LBL_LIST_SHOW' => 'Mostrar',
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
?>