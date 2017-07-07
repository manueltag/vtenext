<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@106857 crmv@112297 */

require_once('include/BaseClasses.php');

class ModLightUtils extends SDKExtendableUniqueClass {
	
	function getModuleList() {
		global $adb, $table_prefix;
		$modules = array();
		$result = $adb->pquery("SELECT {$table_prefix}_tab.name
			FROM {$table_prefix}_tab_info
			INNER JOIN {$table_prefix}_tab ON {$table_prefix}_tab_info.tabid = {$table_prefix}_tab.tabid
			WHERE prefname = ? AND prefvalue = ?", array('is_mod_light',1));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$modules[] = $row['name'];
			}
		}
		return $modules;
	}
	function addTableField($blockid, $addfieldno, $properties) {
		global $adb, $table_prefix;
		$result = $adb->pquery("select name from {$table_prefix}_blocks
			inner join {$table_prefix}_tab on {$table_prefix}_tab.tabid = {$table_prefix}_blocks.tabid
			where blockid = ?", array($blockid));
		if ($result && $adb->num_rows($result) > 0) {
			$relmodule = $adb->query_result($result,0,'name');
			$columns = Zend_Json::decode($properties['columns']);
			
			$fieldid = $adb->getUniqueID($table_prefix.'_modlight');
			
			require_once('modules/Settings/ModuleMaker/ModuleMakerUtils.php');
			require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');
			require_once('modules/Settings/ModuleMaker/ModuleMakerGenerator.php');
			$MMUtils = new ModuleMakerUtils();
			$MMSteps = new ProcessModuleMakerSteps($MMUtils);
			$MMGen = new ModuleMakerGenerator($MMUtils, $MMSteps);
			$newFields = $MMSteps->getNewFields();

			$MMGen->setModuleName('ModLight'.$fieldid);
			
			include_once('vtlib/Vtecrm/Menu.php');
			include_once('vtlib/Vtecrm/Module.php');
			
			// Create module instance and save it first
			$module = new Vtecrm_Module();
			$module->name = 'ModLight'.$fieldid;
			$module->is_mod_light = true;
			$module->save();
			$module->initTables();
			
			// Do not add the module to any Menu
			
			// Add the basic module block
			$block = new Vtecrm_Block();
			$block->label = 'LBL_INFORMATION';
			$module->addBlock($block);
			
			$filter = new Vtecrm_Filter();
			$filter->name = 'All';
			$filter->isdefault = true;
			$module->addFilter($filter);
			
			$i = 0;
			foreach($columns as $column) {
				$field = $this->newField($module,$block,$column);

				// Set at-least one field to identifier of module record
				if ($i == 0) {
					$module->setEntityIdentifier($field);
					$MMGen->mainField = array_merge((array)$field,array('fieldname'=>$field->name,'fieldlabel'=>$field->label));;
				}
				$i++;
				if ($i <= 9) $filter->addField($field,$i);
			}
			
			$field = new Vtecrm_Field();
			$field->name = 'parent_id';
			$field->label = getTranslatedString('SINGLE_'.$relmodule);
			$field->table = $module->basetable;
			$field->uitype = 10;
			$field->readonly = 1;
			$field->presence = 2;
			$field->displaytype = 3;
			$field->typeofdata = 'I~O';
			$field->quickcreate = 3;
			$field->masseditable = 0;
			$MMGen->moduleInfo['fields'][0]['fields'][] = array_merge((array)$field,array('fieldname'=>$field->name,'fieldlabel'=>$field->label));;
			$MMGen->moduleInfo['filters'][0]['columns'][] = $field->name;
			$block->addField($field);
			$field->setRelatedModules(array($relmodule));
			
			$field = new Vtecrm_Field();
			$field->name = 'seq';
			$field->label = 'Sequence';
			$field->table = $module->basetable;
			$field->uitype = 1;
			$field->readonly = 1;
			$field->presence = 2;
			$field->displaytype = 3;
			$field->typeofdata = 'I~O';
			$field->quickcreate = 3;
			$field->masseditable = 0;
			$field->columntype = 'I(19)';
			$MMGen->moduleInfo['fields'][0]['fields'][] = array_merge((array)$field,array('fieldname'=>$field->name,'fieldlabel'=>$field->label));;
			$MMGen->moduleInfo['filters'][0]['columns'][] = $field->name;
			$block->addField($field);
			$field->setRelatedModules(array($relmodule));
			
			$field = new Vtecrm_Field();
			$field->name = 'assigned_user_id';
			$field->label = 'Assigned To';
			$field->table = $table_prefix.'_crmentity';
			$field->column = 'smownerid';
			$field->uitype = 53;
			$field->readonly = 1;
			$field->presence = 2;
			$field->displaytype = 3;
			$field->typeofdata = 'V~M';
			$field->quickcreate = 3;
			$field->masseditable = 0;
			$MMGen->moduleInfo['fields'][0]['fields'][] = array_merge((array)$field,array('fieldname'=>$field->name,'fieldlabel'=>$field->label));;
			$MMGen->moduleInfo['filters'][0]['columns'][] = $field->name;
			$block->addField($field);
			
			$field = new Vtecrm_Field();
			$field->name = 'createdtime';
			$field->label= 'Created Time';
			$field->table = $table_prefix.'_crmentity';
			$field->uitype = 70;
			$field->readonly = 1;
			$field->presence = 2;
			$field->displaytype = 3;
			$field->typeofdata = 'T~O';
			$field->quickcreate = 3;
			$field->masseditable = 0;
			$MMGen->moduleInfo['fields'][0]['fields'][] = array_merge((array)$field,array('fieldname'=>$field->name,'fieldlabel'=>$field->label));;
			$MMGen->moduleInfo['filters'][0]['columns'][] = $field->name;
			$block->addField($field);
			
			$field = new Vtecrm_Field();
			$field->name = 'modifiedtime';
			$field->label= 'Modified Time';
			$field->table = $table_prefix.'_crmentity';
			$field->uitype = 70;
			$field->readonly = 1;
			$field->presence = 2;
			$field->displaytype = 3;
			$field->typeofdata = 'T~O';
			$field->quickcreate = 3;
			$field->masseditable = 0;
			$MMGen->moduleInfo['fields'][0]['fields'][] = array_merge((array)$field,array('fieldname'=>$field->name,'fieldlabel'=>$field->label));;
			$MMGen->moduleInfo['filters'][0]['columns'][] = $field->name;
			$block->addField($field);
			
			// copy dir
			$r = $MMGen->copyBaseFiles();
			if (!$r) return getTranslatedString('LBL_COPY_FILES_ERROR');
			// change file names
			$r = $MMGen->alterFileNames();
			if (!$r) return getTranslatedString('LBL_RENAME_FILE_ERROR');
			// empty the language files
			$dir = 'modules/'.$module->name.'/language/';
			$files = scandir($dir);
			if (!empty($files)) {
				$trans = array(
					$module->name => $properties['label'],
					'SINGLE_'.$module->name => $properties['label'],
					'Assigned To' => 'Assegnato a',
					'Created Time' => 'Orario creazione',
					'Modified Time' => 'Orario modifica',
				);
				foreach($files as $file) {
					if (!in_array($file,array('.','..'))) {
						$buffer = "<?php\n\n";
						$buffer .= "/* Automatically generated translations for module {$module->name} */\n\n";
						$buffer .= '$mod_strings = '.var_export($trans, true).";\n";
						$buffer .= "\n";
						$r = file_put_contents($dir.$file, $buffer);
						if ($r === false) return 'Generation of translations files failed';
					}
				}
				SDK::file2DbLanguages($module->name);
			}
			// calculate the table structure
			$r = $MMGen->calculateTableStructure(true);
			if (!$r) return getTranslatedString('LBL_UT208_GENERIC_ERROR');
			// alter the file content
			$r = $MMGen->alterFileContent();
			if (!$r) return getTranslatedString('ERROR_WHILE_EDITING');
			
			// set sharing access of this module
			$module->setDefaultSharing('Private');
			
			// disable available tools
			$module->disableTools(array('Import','Export','Merge')); 
			
			// initialize webservice
			$module->initWebservice();
			
			Vtecrm_Module::fireEvent($module->name, Vtecrm_Module::EVENT_MODULE_POSTINSTALL);
			
			// recalculate $MMSteps with standard class
			//$MMSteps = new ModuleMakerSteps($MMUtils);
			//$newFields = $MMSteps->getNewFields();
			$fields = array();
			$fields[] = array('module'=>$relmodule,'block'=>$blockid,'name'=>'ml'.$fieldid,'label'=>$properties['label'],'uitype'=>220);
			include('modules/SDK/examples/fieldCreate.php');
		}
	}
	function editTableField($blockid, $editfieldno, $properties) {
		global $adb, $table_prefix, $current_user;
		
		require_once('modules/Settings/ModuleMaker/ModuleMakerUtils.php');
		require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');
		require_once('modules/Settings/ModuleMaker/ModuleMakerGenerator.php');
		$MMUtils = new ModuleMakerUtils();
		$MMSteps = new ProcessModuleMakerSteps($MMUtils);
		$MMGen = new ModuleMakerGenerator($MMUtils, $MMSteps);
		$newFields = $MMSteps->getNewFields();

		$result1 = $adb->pquery("select fieldname, fieldlabel from {$table_prefix}_field where fieldid = ?", array($editfieldno));
		$fieldname = $adb->query_result($result1,0,'fieldname');
		$fieldlabel = $adb->query_result($result1,0,'fieldlabel');
		$modulelightid = str_replace('ml','',$fieldname);
		$modulelightname = 'ModLight'.$modulelightid;
		$tabid = getTabid($modulelightname);
		$module = Vtecrm_Module::getInstance($modulelightname);
		$block = Vtecrm_Block::getInstance('LBL_INFORMATION',$module);
		
		$label = $properties['label'];
		if ($label != $fieldlabel) {
			$adb->pquery("update {$table_prefix}_field set fieldlabel = ? where fieldid = ?", array($label,$editfieldno));
		}

		$filter = Vtecrm_Filter::getInstance('All',$module);
		$adb->pquery("DELETE FROM {$table_prefix}_cvcolumnlist WHERE cvid=?", Array($filter->id));

		$table_fields = array();
		$column_fields = array();
		$result1 = $adb->pquery("select fieldname, displaytype from {$table_prefix}_field where tabid = ?", array($tabid));
		while($row=$adb->fetchByAssoc($result1)) {
			$column_fields[] = $row['fieldname'];
			if ($row['displaytype'] != 3) $table_fields[] = $row['fieldname'];	// skip standard fields (assigned_user_id, parent_id, etc.)
		}
		
		$result = $adb->pquery("select {$table_prefix}_tab.tabid, name from {$table_prefix}_blocks
			inner join {$table_prefix}_tab on {$table_prefix}_tab.tabid = {$table_prefix}_blocks.tabid
			where blockid = ?", array($blockid));
		if ($result && $adb->num_rows($result) > 0) {
			$relmodule = $adb->query_result($result,0,'name');
			$columns = Zend_Json::decode($properties['columns']);
			
			$i = 0;
			$sequence = array();
			foreach($columns as $column) {
				if (!empty($column['fldname']) && in_array($column['fldname'],$column_fields)) {
					// update field
					$updated_fields[] = $column['fldname'];

					$typeofdata = $adb->query_result($adb->pquery("select typeofdata from {$table_prefix}_field where tabid = ? and fieldname = ?", array($tabid,$column['fldname'])), 0,'typeofdata');
					if ($column['mandatory']) $typeofdata = $MMGen->makeTODMandatory($typeofdata);
					else $typeofdata = $MMGen->makeTODOptional($typeofdata);
					$adb->pquery("update {$table_prefix}_field set fieldlabel = ?, readonly = ?, typeofdata = ? where tabid = ? and fieldname = ?", array($column['label'],$column['readonly'],$typeofdata,$tabid,$column['fldname']));
					
					$field = Vtecrm_Field::getInstance($column['fldname'],$module);
					if (isset($column['picklistvalues'])) {
						$picklistid = $adb->query_result($adb->pquery("select picklistid FROM {$table_prefix}_picklist WHERE name = ?",array($column['fldname'])),0,'picklistid');
						$adb->pquery("DELETE FROM {$table_prefix}_role2picklist WHERE picklistid = ? AND picklistvalueid IN (SELECT picklist_valueid FROM {$table_prefix}_{$column['fldname']})", array($picklistid));
						$adb->query("DELETE FROM {$table_prefix}_{$column['fldname']}");
						$picklistValues = array_map('trim', array_unique(explode("\n", $column['picklistvalues'])));
						$field->setPicklistValues($picklistValues);
					}
					if (isset($column['relatedmods'])) {
						$relmods = array_map('trim', array_filter(explode(',', $column['relatedmods'])));
						$field->setRelatedModules($relmods);
					}
					$fieldinfo = array();
					if (isset($column['users'])) {
						$fieldinfo['users'] = $column['users'];
					}
					if (isset($column['newline']) && intval($column['newline']) == 1) {
						$fieldinfo['newline'] = 1;
					}
					$field->setFieldInfo($fieldinfo);
				} else {
					// new
					$field = $this->newField($module,$block,$column);
				}
				$sequence[] = $field->name;
				$i++;
				if ($i <= 9) $filter->addField($field,$i);
			}
			// remove deleted fields
			$remove_fields = array_diff($table_fields,$updated_fields);
			if (!empty($remove_fields)) {
				foreach($remove_fields as $remove_field) {
					$field = Vtecrm_Field::getInstance($remove_field,$module);
					$field->delete();
				}
			}
			// recalculate sequence
			$other_fields = array_diff($column_fields,$sequence);
			$final_fields = array_merge($sequence,$other_fields);
			if (!empty($final_fields)) {
				foreach($final_fields as $seq => $final_field) {
					$adb->pquery("update {$table_prefix}_field set sequence = ? where tabid = ? and fieldname = ?", array($seq+1,$tabid,$final_field));
				}
			}
			// reset at-least one field to identifier of module record
			$module->unsetEntityIdentifier();
			if (!empty($sequence)) $module->setEntityIdentifier(Vtecrm_Field::getInstance($sequence[0],$module));
		}
	}
	function newField($module,$block,$column) {
		global $adb, $table_prefix;
		
		require_once('modules/Settings/ModuleMaker/ModuleMakerUtils.php');
		require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');
		require_once('modules/Settings/ModuleMaker/ModuleMakerGenerator.php');
		$MMUtils = new ModuleMakerUtils();
		$MMSteps = new ProcessModuleMakerSteps($MMUtils);
		$MMGen = new ModuleMakerGenerator($MMUtils, $MMSteps);
		$newFields = $MMSteps->getNewFields();
		
		$field = new Vtecrm_Field();
		$field->name = 'f'.$adb->getUniqueID($table_prefix.'_modlightfield');
		$field->table = $module->basetable;
		$field->label = $column['label'];
		$field->uitype = intval($newFields[$column['fieldno']]['uitype']);
		$column['uitype'] = $field->uitype;
		$field->typeofdata = $MMGen->getTODForField($column);
		if ($column['mandatory']) {
			$field->typeofdata = $MMGen->makeTODMandatory($field->typeofdata);
		}
		$field->columntype = $MMGen->getColumnTypeForField($column);
		$field->readonly = $column['readonly'];
		$field->quickcreate = 3;
		$field->masseditable = 0;
		$MMGen->moduleInfo['fields'][0]['fields'][] = array_merge((array)$field,array('fieldname'=>$field->name,'fieldlabel'=>$field->label));
		$MMGen->moduleInfo['filters'][0]['columns'][] = $field->name;
		$block->addField($field);
		
		if (isset($column['picklistvalues'])) {
			$picklistValues = array_map('trim', array_unique(explode("\n", $column['picklistvalues'])));
			$field->setPicklistValues($picklistValues);
		}
		if (isset($column['relatedmods'])) {
			$relmods = array_map('trim', array_filter(explode(',', $column['relatedmods'])));
			$field->setRelatedModules($relmods);
		}
		$fieldinfo = array();
		if (isset($column['users'])) {
			$fieldinfo['users'] = $column['users'];
		}
		if (isset($column['newline']) && intval($column['newline']) == 1) {
			$fieldinfo['newline'] = 1;
		}
		$field->setFieldInfo($fieldinfo);
		
		return $field;
	}
	function deleteTableField($blockid, $editfieldno) {
		global $adb, $table_prefix;
		
		$field = Vtecrm_Field::getInstance($editfieldno);
		$modulelightid = str_replace('ml','',$field->name);
		$modulelightname = 'ModLight'.$modulelightid;
		
		// delete module
		$module = Vtecrm_Module::getInstance($modulelightname);
		$module->delete();
		
		// delete field
		$field->delete();

		// drop tables
		$adb->query("drop table {$table_prefix}_modlight{$modulelightid}");
		$adb->query("drop table {$table_prefix}_modlight{$modulelightid}cf");
		
		// delete files and folders
		folderDetete("modules/$modulelightname");
		folderDetete("Smarty/templates/modules/$modulelightname");
		folderDetete("cron/modules/$modulelightname");
	}
	function getColumns($module, $fieldname) {
		static $column_cache = array();
		if (isset($column_cache[$module][$fieldname])) return $column_cache[$module][$fieldname];
		
		global $adb, $table_prefix, $current_user;
		require_once('modules/Settings/ModuleMaker/ModuleMakerUtils.php');
		require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');
		$MMUtils = new ModuleMakerUtils();
		$MMSteps = new ProcessModuleMakerSteps($MMUtils);
			
		$modulelightid = str_replace('ml','',$fieldname);
		$modulelightname = 'ModLight'.$modulelightid;
		$tabid = getTabid($modulelightname);
		$result = $adb->pquery("select * from {$table_prefix}_field where tabid = ? and displaytype <> ? order by block, sequence", array($tabid,3));
		$columns = array();
		if ($result && $adb->num_rows($result) > 0) {
			$i = 0;
			//$newFields = $MMSteps->getNewFields();
			// TODO verificare che funzioni tutto anche cosi!!!!!!
			$newFields = $MMSteps->getNewTableFieldColumns();
			while($row=$adb->fetchByAssoc($result)) {
				$webservice_field = WebserviceField::fromArray($adb,$row);
				$fieldno = $MMSteps->getNewFieldNoByUitype($row['uitype']);
				$column_fieldname = $row['fieldname'];
				$properties = array(
					'mandatory' => (strpos($row['typeofdata'],'~M') !== false) ? 1 : 0,
					'readonly' => $row['readonly'],
				);
				$fieldProperties = $newFields[$fieldno]['properties'];
				if (!empty($fieldProperties)) {
					if (in_array('length',$fieldProperties) || in_array('decimals',$fieldProperties)) {
						$metaColumns = $adb->datadict->MetaColumns("{$table_prefix}_modlight{$modulelightid}");
					}
					foreach($fieldProperties as $prop) {
						switch($prop){
							case 'label':
								$properties[$prop] = $row['fieldlabel'];
								break;
							case 'length':
								$properties[$prop] = $metaColumns[strtoupper($column_fieldname)]->max_length;
								break;
							case 'decimals':
								$properties[$prop] = $metaColumns[strtoupper($column_fieldname)]->scale;
								break;
							case 'picklistvalues':
								$values_arr = getAssignedPicklistValues($column_fieldname, $current_user->roleid, $adb, $modulelightname);
								$properties[$prop] = implode("\n",$values_arr);
								break;
							case 'newline':
								$properties[$prop] = 0;
								$fieldinfo = $adb->pquery("select info from {$table_prefix}_field
									inner join {$table_prefix}_fieldinfo on {$table_prefix}_field.fieldid = {$table_prefix}_fieldinfo.fieldid
									where tabid = ? and fieldname = ?", array($tabid,$column_fieldname));
								if ($fieldinfo && $adb->num_rows($fieldinfo) > 0) {
									$info = Zend_Json::decode($adb->query_result_no_html($fieldinfo,0,'info'));
									if (isset($info['newline'])) $properties[$prop] = $info['newline'];
								}
								break;
							//TODO 'relatedmods', 'autoprefix', 'onclick', 'code'
						}
					}
				}
				$tmp = $MMSteps->getNewFieldDefinition($fieldno, $properties, $i, true);
				if ($tmp['uitype'] == 50) {
					require_once('modules/SDK/src/50/50.php');
					$tmp['selected_values'] = array_keys(getCustomUserList($modulelightname, $column_fieldname));
				}
				$tmp['fieldname'] = $column_fieldname;
				$tmp['fieldwstype'] = $webservice_field->getFieldDataType();
				$columns[] = $tmp;
				$i++;
			}
		}
		$column_cache[$module][$fieldname] = $columns;
		return $columns;
	}
	function getValues($module, $record, $fieldname, $columns) {
		global $adb, $table_prefix;
		$moduleLight = 'ModLight'.str_replace('ml','',$fieldname);
		
		$cols = array();
		foreach($columns as $c) {
			$cols[] = $c['fieldname'];
		}
		$focus = CRMEntity::getInstance($moduleLight);
		$result = $adb->pquery("select {$focus->tab_name_index[$focus->table_name]}, ".implode(',',$cols)."
			from {$focus->table_name}
			inner join {$focus->entity_table} on {$focus->entity_table}.{$focus->tab_name_index[$focus->entity_table]} = {$focus->tab_name_index[$focus->table_name]}
			where deleted = 0 and {$focus->table_name}.parent_id = ?
			order by {$focus->table_name}.seq", array($record));
		$values = array();
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$id = $row[$focus->tab_name_index[$focus->table_name]];
				unset($row[$focus->tab_name_index[$focus->table_name]]);
				$values[] = array('id'=>$id, 'row'=>$row);
			}
		}
		return $values;
	}
	function saveTableFields($parentFocus) {
		global $adb, $table_prefix, $table_fields;

		foreach($table_fields[$parentFocus->id] as $fieldname => $table_field) {
			$module = 'ModLight'.str_replace('ml','',$fieldname);
			$focus = CRMEntity::getInstance($module);
			
			$new_ids = array();
			$old_ids = array();
			$result = $adb->pquery("select {$focus->tab_name_index[$focus->table_name]}
				from {$focus->table_name}
				inner join {$focus->entity_table} on {$focus->entity_table}.{$focus->tab_name_index[$focus->entity_table]} = {$focus->tab_name_index[$focus->table_name]}
				where deleted = 0 and {$focus->table_name}.parent_id = ?", array($parentFocus->id));
			if ($result && $adb->num_rows($result) > 0) {
				while($row=$adb->fetchByAssoc($result)) {
					$old_ids[] = $row[$focus->tab_name_index[$focus->table_name]];
				}
			}
			foreach($table_field as $seq => $row) {
				$id = $this->saveRow($module,$seq,$row,$parentFocus);
				$new_ids[] = $id;
				$table_fields[$parentFocus->id][$fieldname][$seq]['id'] = $id;
			}
			$delete_ids = array_diff($old_ids,$new_ids);
			if (!empty($delete_ids)) {
				foreach($delete_ids as $id) {
					$focus->trash($module,$id);
				}
			}
			unset($table_fields[$parentFocus->id][$fieldname]);
		}
	}
	function saveRow($module, $seq=0, $row, $parentFocus) {
		$focus = CRMEntity::getInstance($module);
		if (empty($row['id'])) {
			// create
			$focus->mode = '';
		} else {
			// update
			$focus->mode = 'edit';
			$focus->id = $row['id'];
			$focus->retrieve_entity_info($row['id'],$module);
		}
		foreach($row['row'] as $fieldname => $value) {
			$focus->column_fields[$fieldname] = $value;
		}
		if (empty($focus->column_fields['assigned_user_id'])) $focus->column_fields['assigned_user_id'] = $parentFocus->column_fields['assigned_user_id'];
		if (empty($focus->column_fields['parent_id'])) $focus->column_fields['parent_id'] = $parentFocus->id;
		$focus->column_fields['seq'] = $seq;
		$focus->save($module);
		return $focus->id;
	}
}