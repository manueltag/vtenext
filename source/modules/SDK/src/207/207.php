<?php
global $sdk_mode;
switch($sdk_mode) {
	case 'detail':
		$label_fld[] = getTranslatedString($fieldlabel,$module);
		$label_fld[] = $col_fields[$fieldname];
		break;
	case 'edit':
		$editview_label[] = getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = $value;
		break;
	case 'relatedlist':
	case 'list':
		if (!empty($sdk_value)) {
			$value = '<a href="'.$sdk_value.'" target="_blank">'.$sdk_value.'</a>'; // crmv@80653
		} else {
			$value = '';
		}
		break;
}
?>