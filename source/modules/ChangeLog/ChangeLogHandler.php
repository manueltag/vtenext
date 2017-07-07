<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@82326 crmv@104566 */
class ChangeLogHandler extends VTEventHandler {
	function handleEvent($eventName, $entityData) {
		global $table_prefix;
		global $log, $adb,$current_user,$record_init,$record_last,$currentModule;
		
		$currentModule_tmp = $currentModule;
		$currentModule = 'ChangeLog';
		$moduleName = $entityData->getModuleName();
		$entityData->date = date('Y-m-d H:i:s');
		$id = $entityData->getId();
		$obj = CRMEntity::getInstance('ChangeLog');
		
		if($moduleName == 'Activity'){
			$moduleName = 'Calendar';
		}
		
		if ($moduleName != 'ChangeLog' && $obj->isEnabled($moduleName)) { //crmv@47905
			if($moduleName == 'Calendar'){
				if($_REQUEST['activity_mode'] == 'Events'){
					$moduleName = 'Events';
				}
			}
			if ($moduleName != 'Users' && isRecordExists($id)){	//crmv@115268
				if ($entityData->isNew()) {
					if ($eventName == 'history_last') {
						$proj = CRMEntity::getInstance($moduleName);
						$proj->retrieve_entity_info_no_html($id,$moduleName);
						$obj = CRMEntity::getInstance('ChangeLog');
						$obj->column_fields['modified_date'] = $proj->column_fields['createdtime'];
						$obj->column_fields['audit_no'] = $obj->get_revision_id($id);
						$obj->column_fields['assigned_user_id'] = $current_user->id;
						$obj->column_fields['parent_id'] = $id;
						$obj->column_fields['user_name'] = $current_user->column_fields['user_name'];
						$obj->column_fields['description'] = Zend_Json::encode(array('ChangeLogCreation'));
						$obj->save('ChangeLog',false,false,false); //crmv@47905
					}
				} else {
					if($eventName == 'history_first' && empty($record_init[$moduleName][$id]) && empty($record_last[$moduleName][$id])) { //se il record è modificato
						$proj = CRMEntity::getInstance($moduleName);
						$proj->mode = 'edit';
						$proj->id = $id;
						$proj->retrieve_entity_info_no_html($id,$moduleName);
						$record_init[$moduleName][$id] = $proj->column_fields; //scrivo la column_fields attuale in json nel campo description
					}
					elseif($eventName == 'history_last' && !empty($record_init[$moduleName][$id])){
						$data = array_filter($entityData->getData());
						$data_encoded = Zend_Json::encode($data);
						$id = $entityData->getId();
						$obj = CRMEntity::getInstance('ChangeLog');
						$nr_rev = $obj->get_revision_id($id);	//crmv@103534
						$obj->column_fields['audit_no'] = $nr_rev;//versione record calcolata
						$obj->column_fields['assigned_user_id'] = $current_user->id; //utente corrente
						$obj->column_fields['parent_id'] = $id; //id entità collegata
						$proj = CRMEntity::getInstance($moduleName);
						$proj->mode = 'edit';
						$proj->id = $id;
						$proj->retrieve_entity_info_no_html($id,$moduleName);
						$record_last[$moduleName][$id] = $proj->column_fields;
						$result = array_diff_assoc($record_init[$moduleName][$id], $record_last[$moduleName][$id]);
						$final_record = Array();
						$campi = array();
						$obj->column_fields['user_name'] = $current_user->column_fields['user_name'];
						$q = "SELECT fieldname, fieldlabel, fieldtype, readonly, {$table_prefix}_field.uitype FROM ".$table_prefix."_field LEFT JOIN ".$table_prefix."_ws_fieldtype ON ".$table_prefix."_field.uitype = ".$table_prefix."_ws_fieldtype.uitype WHERE tabid = (SELECT tabid FROM ".$table_prefix."_tab WHERE name = ?)"; // crmv@31240 crmv@37679
						$ress = $adb->pquery($q, array($moduleName));
						// crmv@109801
						$label = array();
						$types = array();
						$readonly = array();
						$uitypes = array();
						while($row = $adb->fetchByAssoc($ress)){
							$label[$row['fieldname']] =  $row['fieldlabel'];
							$types[$row['fieldname']] =  $row['fieldtype'];
							$readonly[$row['fieldname']] =  $row['readonly']; // crmv@31240
							$uitypes[$row['fieldname']] = $row['uitype'];
						}
						// crmv@109801e
						
						$reference_changelogs = array();
						foreach ($result as $key=>$value){
	
							if($readonly[$key] == '100') continue; // crmv@31240
	
							$previous_value = $record_init[$moduleName][$id][$key];
							$current_value = $record_last[$moduleName][$id][$key];
	
							if (strtolower($key) == 'modifiedtime') {
								$obj->column_fields['modified_date'] = $proj->column_fields[$key]; 
							} elseif ($obj->isFieldSkipped($moduleName, $key, $uitypes[$key])) { // crmv@109801
								// skip field, don't save it!
							} else {
								$campi[] = array($label[$key], $previous_value, $current_value, $key, $types[$key]);
							}
							
							// save also a changelog in the linked record
							if ($types[$key] == 'reference' && (!empty($current_value) || !empty($previous_value))) {
								$reference_changelogs[] = array($previous_value,$current_value,$key);
							}
						}
						$record_init[$moduleName][$id] = '';
						$record_last[$moduleName][$id] = '';
						if (!empty($campi)) {	//se non c'è nessuna differenza non creo il ChangeLog
							$obj->column_fields['description'] = Zend_Json::encode($campi);
							$obj->save('ChangeLog',false,false,false); //crmv@47905
							/* crmv@103534
							if ($moduleName == 'Processes') {
								require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
								$processDynaFormObj = ProcessDynaForm::getInstance();
								$processDynaFormObj->propagateParallelsChangeLog($id,$obj);
							}
							crmv@103534e */
						}
						if (!empty($reference_changelogs)) {
							foreach($reference_changelogs as $tmp) {
								$previous_value = $tmp[0];
								$current_value = $tmp[1];
								$key = $tmp[2];
								if (!empty($previous_value)) {
									$obj1 = CRMEntity::getInstance('ChangeLog');
									$obj1->column_fields['modified_date'] = $obj->column_fields['modified_date'];
									$obj1->column_fields['audit_no'] = $obj1->get_revision_id($previous_value);
									$obj1->column_fields['assigned_user_id'] = $current_user->id;
									$obj1->column_fields['parent_id'] = $previous_value;
									$obj1->column_fields['user_name'] = $current_user->column_fields['user_name'];
									$obj1->column_fields['description'] = Zend_Json::encode(array('ChangeLogRemoveRelation1N',$id,$moduleName,$key,$previous_value,$current_value));
									$obj1->save('ChangeLog',false,false,false); //crmv@47905
								}
								if (!empty($current_value)) {
									$obj1 = CRMEntity::getInstance('ChangeLog');
									$obj1->column_fields['modified_date'] = $obj->column_fields['modified_date'];
									$obj1->column_fields['audit_no'] = $obj1->get_revision_id($current_value);
									$obj1->column_fields['assigned_user_id'] = $current_user->id;
									$obj1->column_fields['parent_id'] = $current_value;
									$obj1->column_fields['user_name'] = $current_user->column_fields['user_name'];
									$obj1->column_fields['description'] = Zend_Json::encode(array('ChangeLogRelation1N',$id,$moduleName,$current_value,getSalesEntityType($current_value),$key));
									$obj1->save('ChangeLog',false,false,false); //crmv@47905
								}
							}
						}
					}
				}
			}
		}
		
		$currentModule = $currentModule_tmp;
	}
}
