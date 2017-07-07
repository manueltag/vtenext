<?php
global $adb, $table_prefix;

$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'Projects'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['Projects'] = 'packages/vte/optional/Projects.zip';

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

$fields = array(
 'enable_activesync' => array('module'=>'Users', 'block'=>'LBL_USERLOGIN_ROLE', 'name'=>'enable_activesync', 'label'=>'Enable ActiveSync', 'table'=>"{$table_prefix}_users",  'column'=>'enable_activesync', 'columntype'=>'I(1)', 'typeofdata'=>'C~O',  'uitype'=>56, 'readonly'=>1, 'masseditable'=>1, 'quickcreate'=>1, ),
);

createFields($fields);

SDK::setLanguageEntries('Users','Enable ActiveSync',Array(
 'en_us'=>'Enable ActiveSync',
 'it_it'=>'Abilita ActiveSync',
 'pt_br'=>'Ativar ActiveSync')
);

// imposto flag per utenti esistenti
$res = $adb->query("update {$table_prefix}_users set enable_activesync = 1 where status = 'Active'");

SDK::setLanguageEntry('Settings', 'it_it', 'LBL_USR_CANNOT_ACCESS', 'L`utente puo` accedere ai propri record e a quelli di utenti con ruolo più basso');
?>