<?php
/*
 * crmv@37679
 */

require_once('modules/SDK/src/208/208Utils.php');
global $sdk_mode;

$uitype208 = new EncryptedUitype();
if (empty($fieldname)) $fieldname = $fieldName; // for list
if (empty($module)) $module = $module_name; // for editview
$fieldid = $uitype208->getFieldIdFromName($module, $fieldname);

$permitted = $uitype208->isPermitted($fieldid);
if ($permitted) {
	$pwd = $uitype208->getCachedPassword($fieldid);
	$_SESSION['uitype208'][$fieldid]['permitted'] = true;
} else {
	$pwd = false;
	$uitype208->setCachedPassword($fieldid, '', false);
}

$str_not_accessible = '<font color="red">'.getTranslatedString('LBL_NOT_ACCESSIBLE').'</font>';
$str_encoded = '<font color="green">'.getTranslatedString('LBL_CIPHERED').'</font>';
switch($sdk_mode) {
	case 'insert':
		$fldvalue = $this->column_fields[$fieldname];

		$isAjax = ($_REQUEST['file'] == 'DetailViewAjax' && $_REQUEST['ajxaction'] == 'DETAILVIEW');
		$isValidAjax = (!$isAjax || ($isAjax && $_REQUEST['fldName'] == $fieldname));

		if ($pwd != false && $readonly != 100 && $isValidAjax) {
			$fldvalue = $uitype208->encryptData($fldvalue, $pwd);
			$maxlen = $uitype208->getColumnLength($table_name, $columname);
			if ($maxlen < strlen($fldvalue)) {
				$sdk_skipfield = true;
				// inglorious death, but avoid data corruption
				die(':#:FAILURE : Column is too short to hold crypted data.');
			}
		} else {
			// prevent db update
			$sdk_skipfield = true;
		}
		break;
	case 'detail':
		$val = $col_fields[$fieldname];

		if ($pwd != false) {
			$val = $uitype208->decryptData($val, $pwd);
		} else {
			$val = ($permitted ? $str_encoded : $str_not_accessible);
		}
		$label_fld[] = getTranslatedString($fieldlabel,$module);
		$label_fld[] = $val;
		break;
	case 'edit':
		$encodedValue = $value;

		if ($pwd != false) {
			$value = $uitype208->decryptData($encodedValue, $pwd);
		} else {
			$value = ($permitted ? $str_encoded : $str_not_accessible);
		}

		$editview_label[] = getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $value;
		break;
	case 'relatedlist':
	case 'list':
		if ($sdk_mode == 'list')
			$encodedValue = $rawValue;
		else
			$encodedValue = $field_val;

		if ($pwd != false) {
			$value = $uitype208->decryptData($encodedValue, $pwd);
		} else {
			$value = ($permitted ? $str_encoded : $str_not_accessible);
		}

		break;
}

?>