<?php
/*
 * crmv@39110
 */
global $sdk_mode;
global $default_charset; // crmv@97841

switch($sdk_mode) {
	case 'insert':
		break;
	case 'detail':
		$val = $col_fields[$fieldname];
		$label_fld[] = getTranslatedString($fieldlabel,$module);
		if ($_REQUEST['file'] == 'DetailViewBlocks') {
			$label_fld[] = $val; //crmv@81269 - no need to convert chars here, thay have to stay like they were in the db
		} else {
			// WTF??!!!! Why on earth these 2 cases are different?
			$label_fld[] = html_entity_decode($val, ENT_COMPAT, $default_charset);
		}
		break;
	case 'edit':
		$editview_label[] = getTranslatedString($fieldlabel, $module_name);
		$fieldvalue[] = html_entity_decode($value, ENT_COMPAT, $default_charset);
		break;
	case 'relatedlist':
		$recordId = $entity_id;
	case 'list':
		$sdk_value = ($sdk_mode == 'list' ? $rawValue : $field_val);
		$entity_id = $recordId;
		if (!empty($sdk_value)) {
			$tmp_val = preg_replace("/(<\/?)(\w+)([^>]*>)/i","",$sdk_value);
			$tmp_val = trim(html_entity_decode($tmp_val, ENT_QUOTES, $default_charset));
			$value = textlength_check(strip_tags($tmp_val));
			if ($sdk_value != '' && strlen($tmp_val) > $listview_max_textlength) {
				$value .= '&nbsp;<a href="javascript:;"><img onmouseout="getObj(\'content_'.$fieldname.'_'.$entity_id.'\').hide();" onmouseover="getObj(\'content_'.$fieldname.'_'.$entity_id.'\').show();" src="themes/softed/images/readmore.png" border="0"></a>';
				$value .= '<div id="content_'.$fieldname.'_'.$entity_id.'" class="layerPopup" style="width:300px;z-index:10000001;display:none;position:absolute;" onmouseout="getObj(\'content_'.$fieldname.'_'.$entity_id.'\').hide();" onmouseover="getObj(\'content_'.$fieldname.'_'.$entity_id.'\').show();">
				<table style="background-color:#F2F2F2;" align="center" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr><td class="small">'.$tmp_val.'</td></tr>
				</table></div>';
			}
		} else {
			$value = '';
		}
		break;
	//crmv@65492 - 28
	case 'pdfmaker':
		if($_REQUEST['file'] == 'SendPDFMail' || ($_REQUEST['file'] == 'CreatePDFFromTemplate' && $_REQUEST['mode'] == 'content')) //avoid formatting in send email pdf and editing pdf before export
			$value = $sdk_value;
		else
			$value = html_entity_decode($sdk_value, ENT_COMPAT, $default_charset);
			
		break;
	case 'report':
		$fieldvalue = strip_tags(htmlspecialchars_decode($sdk_value));
		break;
	//crmv@65492e - 28
}

?>