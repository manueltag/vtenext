<?php
/* crmv@104180 crmv@106857 crmv@112297 crmv@115268 */

global $sdk_mode, $current_module, $table_fields;

require_once('modules/SDK/src/220/220Utils.php');

switch($sdk_mode) {
	case 'insert':
		if (isset($_REQUEST[$fieldname.'_lastrowno']) && empty($table_fields[$this->id][$fieldname])) {
			require_once('include/utils/ModLightUtils.php');
			$MLUtils = ModLightUtils::getInstance();
			$columns = $MLUtils->getColumns($current_module, $fieldname);
			$field = array(
				'fieldname' => $fieldname,
				'columns' => Zend_Json::encode($columns),
			);
			require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
			$dynaform = ProcessDynaForm::getInstance();
			$table = $dynaform->parseTableField($field, $_REQUEST, true);
			$module = 'ModLight'.str_replace('ml','',$fieldname);
			$table_fields[$this->id][$fieldname] = array();
			if (!empty($table)) {
				foreach($table as $row) {
					$table_fields[$this->id][$fieldname][] = $row;
				}
			}
		}
		break;
	case 'detail':
		$TFUtils = new TableFieldUtils();
		if (!empty($dynaform_info)) {
			$columns = Zend_Json::decode($dynaform_info['columns']);
			$value = $col_fields[$fieldname];
			if (!empty($value)) {
				foreach($value as $i => $row) {
					$value[$i] = array('row'=>$row);
				}
			}
		} else {
			require_once('include/utils/ModLightUtils.php');
			$MLUtils = ModLightUtils::getInstance();
			$columns = $MLUtils->getColumns($module,$fieldname);
			$value = $MLUtils->getValues($module,$col_fields['record_id'],$fieldname,$columns);
		}
		$single_line = true;
		foreach($columns as $column) {
			if ($column['newline'] == 1) {
				$single_line = false;
				break;
			}
		}
		if (is_array($value)) {
			$htmlrows = $TFUtils->generateHtmlRowsDetail($fieldname, $columns, $value, $single_line);
		} else {
			// empty
			$value = array();
			$htmlrows = '';
		}
		$label_fld[] = getTranslatedString($fieldlabel,$module);
		$label_fld[] = $value;
		$label_fld['options']['htmlrows'] = $htmlrows;
		$label_fld['options']['columns'] = $columns;
		$label_fld['options']['single_line'] = $single_line;
		$label_fld['options']['show_actions'] = false;
		break;
	case 'edit':
		$cache = RCache::getInstance();
		$permissions = $cache->get('conditional_permissions');
		if (isset($permissions[$fieldname])) {	// overwrite readonly and mandatory
			$readonly = $permissions[$fieldname]['readonly'];
			// ? $mandatory = ($permissions[$fieldname]['mandatory'] == '1')?'M':'O';
		}
		$TFUtils = new TableFieldUtils();
		if (!empty($dynaform_info)) {
			$columns = Zend_Json::decode($dynaform_info['columns']);
			if (!empty($value)) {
				foreach($value as $i => $row) {
					$value[$i] = array('row'=>$row);
				}
			}
		} else {
			require_once('include/utils/ModLightUtils.php');
			$MLUtils = ModLightUtils::getInstance();
			$columns = $MLUtils->getColumns($module_name,$fieldname);
			if (is_array($col_fields[$fieldname])) {
				// retrieve from column_fields (EditViewConditionals)
				$value = array();
				if (!empty($col_fields[$fieldname]['rows'])) {
					foreach($col_fields[$fieldname]['rows'] as $seq => $table_columns) {
						//$value[$col_fields[$fieldname][$fieldname.'_rowid_'.$seq]] = $table_columns;
						$value[$seq] = array('id'=>$col_fields[$fieldname][$fieldname.'_rowid_'.$seq], 'row'=>$table_columns);
					}
				}
			} else {
				// retrieve from db
				$value = $MLUtils->getValues($module,$col_fields['record_id'],$fieldname,$columns);
			}
		}
		$single_line = true;
		if (!empty($columns)) {
			$ModuleMakerGenerator = new ProcessModuleMakerGenerator();
			foreach($columns as &$column) {
				$column['typeofdata'] = $ModuleMakerGenerator->getTODForField($column);
				if ($column['mandatory']) $column['typeofdata'] = $ModuleMakerGenerator->makeTODMandatory($column['typeofdata']);
				if ($column['newline'] == 1) $single_line = false;
			}
		}
		$editview_label[] = getTranslatedString($fieldlabel, $module_name);
		if (is_array($value)) {
			$fieldvalue[] = $value;
			$rowsEdit = $TFUtils->generateHtmlRowsEdit($fieldname, $columns, $value, $single_line, $readonly);
			$fieldvalue['htmlrows'] = $rowsEdit['html'];
			$fieldvalue['typeofdata'] = $rowsEdit['typeofdata'];
		} else {
			$fieldvalue[] = array();
			$fieldvalue['htmlrows'] = '';
			$fieldvalue['typeofdata'] = '';
		}
		$fieldvalue['columns'] = $columns;
		$fieldvalue['single_line'] = $single_line;
		$fieldvalue['show_actions'] = (!in_array($readonly,array(99,100)));
		break;
	default:
		// not supported!
		break;
}