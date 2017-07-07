<?php
global $adb, $table_prefix;
// COMMIT crmv@42024

$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['Conditionals'] = 'packages/vte/mandatory/Conditionals.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

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

SDK::clearSessionValues();

// output a notice to update VTEEntity.php to add the news extends
if (file_exists('modules/SDK/src/VTEEntity.php')) {
	include_once('modules/SDK/src/VTEEntity.php');
	if (class_exists('VTEEntity')) {
		echo "<br>\n<b>Notice:</b> A custom VTEEntity file has been found, please update the class signature to <code style=\"background-color:#ccc\">class VTEEntity extends SDKExtendableClass</code><br>\n";
	}
}

// add column for total without taxes
addColumnToTable("{$table_prefix}_inventoryproductrel", 'total_notaxes', 'N(25.3)');

// now recalculate prices without taxes
$res = $adb->query("select * from {$table_prefix}_inventoryproductrel");
if ($res && $adb->num_rows($res) > 0) {
	while ($row = $adb->FetchByAssoc($res)) {
		$listprice = floatval($row['listprice']);
		$quantity = floatval($row['quantity']);
		if (empty($listprice) || empty($quantity)) continue;

		$price = $listprice * $quantity;
		if (!empty($row['discount_amount'])) {
			$price -= floatval($row['discount_amount']);
		} elseif (!empty($row['discount_percent'])) {
			if ($row['discount_percent'][0] != '+' && $row['discount_percent'][0] != '-') $row['discount_percent'] = '+'.$row['discount_percent'];
			$discounts = preg_split('/([-+])/', $row['discount_percent'], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			$discounts = array_chunk($discounts, 2);
			foreach ($discounts as $d) {
				$disc_perc = floatval($d[0].$d[1])/100.0;
				if ($disc_perc) {
					$price -= $price*$disc_perc;
				}
			}
		}
		// now we have the price without taxes
		$adb->pquery("update {$table_prefix}_inventoryproductrel set total_notaxes = ? where lineitem_id = ?", array($price, $row['lineitem_id']));
	}
}

// alter inventory modules table in order to accept multiple discounts
Vtiger_Utils::AlterTable($table_prefix.'_quotes','discount_percent C(127)');
Vtiger_Utils::AlterTable($table_prefix.'_salesorder','discount_percent C(127)');
Vtiger_Utils::AlterTable($table_prefix.'_purchaseorder','discount_percent C(127)');
Vtiger_Utils::AlterTable($table_prefix.'_invoice','discount_percent C(127)');
Vtiger_Utils::AlterTable($table_prefix.'_ddt','discount_percent C(127)');

// add decimal separator fields
// aggiungo campo
$fields = array(
	'decimal_separator'		=> array('module'=>'Users', 'block'=>'LBL_MORE_INFORMATION', 'name'=>'decimal_separator', 'label'=>'DecimalSeparator',   'table'=>"{$table_prefix}_users",	'columntype'=>'C(8)', 'typeofdata'=>'V~O',    'uitype'=>15, 'picklist'=>array('SeparatorDot', 'SeparatorComma')),
	'thousands_separator'	=> array('module'=>'Users', 'block'=>'LBL_MORE_INFORMATION', 'name'=>'thousands_separator', 'label'=>'ThousandsSeparator',   'table'=>"{$table_prefix}_users",	'columntype'=>'C(8)', 'typeofdata'=>'V~O',    'uitype'=>15, 'picklist'=>array('SeparatorDot', 'SeparatorComma', 'SeparatorSpace', 'SeparatorQuote', 'SeparatorNone')),
	'decimals_num'			=> array('module'=>'Users', 'block'=>'LBL_MORE_INFORMATION', 'name'=>'decimals_num', 'label'=>'DecimalsNumber',   'table'=>"{$table_prefix}_users",	'columntype'=>'I(4)', 'typeofdata'=>'I~O',    'uitype'=>7, ),
);

$fieldRet = createFields($fields);

// update it for all users (old style separators)
$adb->pquery("update {$table_prefix}_users set decimal_separator = ?, thousands_separator = ?, decimals_num = ?", array('.', '', 2));

// add variables in config.inc
$configInc = file_get_contents('config.inc.php');
if (empty($configInc)) {
	echo "\nWARNING: Unable to get config.inc.php contents, please modify it manually.\n";
} else {

	if (strpos($configInc, '$default_decimal_separator') === false) {
		// backup it (only if it doesn't exist
		$newConfigInc = 'config.inc.744.php';
		if (!file_exists($newConfigInc)) {
			file_put_contents($newConfigInc, $configInc);
		}
		// alter config inc (add it after default language)
		$addString =
"

// crmv@42024 separators to display float values
\$default_decimal_separator = '.';
\$default_thousands_separator = '';
\$default_decimals_num = 2;
// crmv@42024e";
		$configInc = preg_replace('/^\$default_language.*$/m', '\0'.$addString, $configInc);
		if (is_writable('config.inc.php')) {
			file_put_contents('config.inc.php', $configInc);
		} else {
			echo "\nWARNING: Unable to update config.inc.php, please modify it manually.\n";
		}
	}
}

// clear SugarBean (but keep it, in case custom modules are including the file)
if (is_writable('data/SugarBean.php')) @file_put_contents('data/SugarBean.php', '<?php ?>');

// distruzione files
@unlink('include/InventoryPDF.php');
@unlink('include/InventoryPDFController.php');
@unlink('vtlib/ModuleDir/5.0/Delete.php');

// delete Delete.php
$deleteMods = array(
	'Accounts', 'Assets', 'Calendar', 'Campaigns', 'Charts', 'Contacts', 'Ddt', 'Documents',
	'Faq', 'Fax', 'HelpDesk', 'Invoice', 'Leads', 'Messages', 'Newsletter', 'PBXManager',
	'Potentials', 'ProjectMilestone', 'ProjectPlan', 'ProjectTask', 'PurchaseOrder', 'Quotes',
	'SalesOrder', 'ServiceContracts', 'Services', 'Sms', 'Targets', 'Vendors', 'Visitreport'
);
foreach ($deleteMods as $dm) {
	if (is_writable("modules/$dm/Delete.php")) @unlink("modules/$dm/Delete.php");
}


// translations
$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'DecimalSeparator' => 'Separatore decimali',
			'ThousandsSeparator' => 'Separatore migliaia',
			'DecimalsNumber' => 'Numero di decimali',

		),
		'en_us' => array(
			'DecimalSeparator' => 'Decimal point separator',
			'ThousandsSeparator' => 'Thousands separator',
			'DecimalsNumber' => 'Decimal places',
		),
	),
	'Users' => array(
		'it_it' => array(
			'SeparatorDot' => '.',
			'SeparatorComma' => ',',
			'SeparatorSpace' => '[SPAZIO]',
			'SeparatorQuote' => '\'',
			'SeparatorNone' => '[NESSUNO]',
		),
		'en_us' => array(
			'SeparatorDot' => '.',
			'SeparatorComma' => ',',
			'SeparatorSpace' => '[SPACE]',
			'SeparatorQuote' => '\'',
			'SeparatorNone' => '[NONE]',
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