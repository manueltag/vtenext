<?php

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

// aggiungo campo
$fields = array(
	'exp_duration'	=> array('module'=>'Calendar', 'block'=>'LBL_TASK_INFORMATION',	'name'=>'exp_duration',	'label'=>'ExpDuration',		'table'=>"{$table_prefix}_activity", 	'column'=>'exp_duration',	'columntype'=>'C(100)',	'typeofdata'=>'V~O', 	'uitype'=>15, 'readonly'=>1, 'masseditable'=>1, 'quickcreate'=>0, 'picklist'=>array('Duration15', 'Duration30', 'Duration45', 'Duration60', 'DurationMore')),
);

$fieldRet = createFields($fields);

// etichette

$trans = array(
	'Calendar' => array(
		'it_it' => array(
			'ExpDuration' => 'Durata',
			'Duration15' => '15 minuti',
			'Duration30' => '30 minuti',
			'Duration45' => '45 minuti',
			'Duration60' => '1 ora',
			'DurationMore' => 'Altro',
			'TodoByDate' => 'per scadenza',
			'TodoByDuration' => 'per durata',
		),
		'en_us' => array(
			'ExpDuration' => 'Duration',
			'Duration15' => '15 minutes',
			'Duration30' => '30 minutes',
			'Duration45' => '45 minutes',
			'Duration60' => '1 hour',
			'DurationMore' => 'Other',
			'TodoByDate' => 'by date',
			'TodoByDuration' => 'by duration',
		),
		'pt_br' => array(
			'ExpDuration' => 'Duraчуo',
			'Duration15' => '15 minutos',
			'Duration30' => '30 minutos',
			'Duration45' => '45 minutos',
			'Duration60' => '1 hora',
			'DurationMore' => 'Outro',
			'TodoByDate' => 'por data',
			'TodoByDuration' => 'por duraчуo',
		),
	),

	'APP_STRINGS' => array(
		'it_it' => array(
			'TodoByDate' => 'per scadenza',
			'TodoByDuration' => 'per durata',
		),
		'en_us' => array(
			'TodoByDate' => 'by date',
			'TodoByDuration' => 'by duration',
		),
		'pt_br' => array(
			'TodoByDate' => 'por data',
			'TodoByDuration' => 'por duraчуo',
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