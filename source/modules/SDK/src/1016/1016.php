<?php
/* +*************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 * ************************************************************************************* */

// crmv@104567

global $sdk_mode, $table_prefix;
switch ($sdk_mode) {
	case 'detail' :
		$label_fld[] = getTranslatedString($fieldlabel, $module);
		$img_path = $col_fields[$fieldname];
		if ($img_path != '' && file_exists($img_path)) {
			$value = $img_path;
		} else {
			$value = getTranslatedString('NO_SIGNATURE_IMAGE', $module_name);
		}
		$label_fld[] = $value;
		break;
	case 'edit' :
		$editview_label[] = getTranslatedString($fieldlabel, $module_name);
		$img_path = $col_fields[$fieldname];
		if ($img_path != '' && file_exists($img_path)) {
			$value = $img_path;
		} else {
			$value = getTranslatedString('NO_SIGNATURE_IMAGE', $module_name);
		}
		$fieldvalue[] = $value;
		break;
	case 'relatedlist' :
	case 'list' :
		$value = $sdk_value;
		break;
	case 'pdfmaker' :
		$no_image_lbl = getTranslatedString('NO_SIGNATURE_IMAGE', $module);
		if ($value == $no_image_lbl) {
			$value = '';
		} else {
			if (!empty($value) && file_exists($value)) {
				$value = '<img src="' . $value . '" style="width:auto;height:2.5cm;" />';
			}
		}
		break;
}

?>