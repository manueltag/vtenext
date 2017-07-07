<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@92272 crmv@96450 crmv@120769 */

require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('modules/com_vtiger_workflow/VTSimpleTemplate.inc');
require_once('include/Webservices/DescribeObject.php');

class PMActionUpdate {
	
	function edit(&$smarty,$id,$elementid,$retrieve,$action_type,$action_id='') {
		$PMUtils = ProcessMakerUtils::getInstance();
		$record_involved = '';
		if ($action_id != '') {
			$vte_metadata = Zend_Json::decode($retrieve['vte_metadata']);
			if (!empty($vte_metadata[$elementid])) {
				$metadata_action = $vte_metadata[$elementid]['actions'][$action_id];
				$record_involved = $metadata_action['record_involved'];
			}
			$smarty->assign('METADATA', $metadata_action);
		}
		$records_pick = $PMUtils->getRecordsInvolvedOptions($id, $record_involved);
		$smarty->assign("RECORDS_INVOLVED", $records_pick);
		
		$smarty->assign('SDK_CUSTOM_FUNCTIONS',SDK::getFormattedProcessMakerFieldActions());
		$smarty->assign('ADVANCED_FIELD_ASSIGNMENT',$PMUtils->todoFunctions);	//crmv@106856
	}
	
	function execute($engine,$actionid) {
		$action = $engine->vte_metadata['actions'][$actionid];
		list($metaid,$module) = explode(':',$action['record_involved']);
		$record = $engine->getCrmid($metaid);

		if (!empty($action['form'])) {
			$PMUtils = ProcessMakerUtils::getInstance();

			$engine->log("Action Update","action $actionid - {$action['action_title']}");
			
			// init variabiles to replace tags
			global $log, $adb;
			
			$util = new VTWorkflowUtils();
			$admin = $util->adminUser();
			$entityCache = new VTEntityCache($admin);
	
			$webserviceObject = VtigerWebserviceObject::fromName($adb,$module);
			$handlerPath = $webserviceObject->getHandlerPath();
			$handlerClass = $webserviceObject->getHandlerClass();
			require_once $handlerPath;
			$handler = new $handlerClass($webserviceObject,$admin,$adb,$log);
			$meta = $handler->getMeta();
			$referenceFields = $meta->getReferenceFieldDetails();
			if (!empty($referenceFields)) $referenceFields = array_keys($referenceFields);
			$ownerFields = $meta->getOwnerFields();
			$dataFields = $meta->getDataFields();
			$util->revertUser();
			// end
			
			// track record
			$engine->trackRecord($record,$metaid,$engine->prev_elementid,$engine->elementid);

			$PMUtils->preserveRequest();
			
			// update record
			$focus = CRMEntity::getInstance($module);
			$focus->retrieve_entity_info_no_html($record,$module);
			$focus->mode = 'edit';
			foreach($action['form'] as $fieldname => $value) {
				$value = $engine->replaceTags($fieldname,$value,$referenceFields,$ownerFields,$actionid);	//crmv@106856
				$focus->column_fields[$fieldname] = $value;
			}
			//crmv@108227
			
			$date_fields = array();
			if (!empty($dataFields)) {
				foreach($dataFields as $dataField) {
					$date_fields[$dataField] = '';
				}
			}
			if (in_array($module,array('Calendar','Events'))) {
				$date_fields['date_start'] = 'time_start';
				$date_fields['due_date'] = 'time_end';
			}
			if (!empty($date_fields)) {
				foreach($date_fields as $date_field => $time_field) {
					if (array_key_exists($date_field,$action['form'])) {
						$date_arr = Zend_Json::decode($focus->column_fields[$date_field]);
						if ($date_arr['options'] == 'custom') {
							$date = $date_arr['custom'];
						} else {
							if ($date_arr['options'] == 'now') {
								$date = date('Y-m-d');
							} else {
								//$date = $engine->replaceTags($date_field,$date_arr['options'],$referenceFields,$ownerFields,$actionid,$this->cycleIndex);
								$date = $date_arr['options'];
							}
							if (!empty($date_arr['num'])) {
								$advanced = (($date_arr['operator']=='add')?'+':'-').' '.$date_arr['num'].' '.$date_arr['unit'];
							}
						}
						$date = date('Y-m-d',strtotime("$date $advanced"));
						$focus->column_fields[$date_field] = $date;
					}
					if (array_key_exists($time_field,$action['form'])) {
						if ($module == 'Calendar' && $time_field == 'time_end') {
							$focus->column_fields[$time_field] = '';
						} else {
							$time_arr = Zend_Json::decode($focus->column_fields[$time_field]);
							if ($time_arr['options'] == 'custom') {
								$time = $time_arr['custom'];
							} else {
								if ($time_arr['options'] == 'now') {
									$time = date('H:i');
								} else {
									//$time = $engine->replaceTags($time_field,$time_arr['options'],$referenceFields,$ownerFields,$actionid,$this->cycleIndex);
									$time = $time_arr['options'];
								}
								if (!empty($time_arr['num'])) {
									$advanced = (($time_arr['operator']=='add')?'+':'-').' '.$time_arr['num'].' '.$time_arr['unit'];
								}
							}
							$time = date('H:i',strtotime("$time $advanced"));
							$focus->column_fields[$time_field] = $time;
						}
					}
				}
			}
			//crmv@108227e
			$focus->save($module);

			$PMUtils->restoreRequest();

			//crmv@112539
			$engine->logElement($engine->elementid, array(
				'action_type'=>'Update',
				'action_title'=>$action['action_title'],
				'metaid'=>$metaid,
				'crmid'=>$record,
				'module'=>$module,
			));
			//crmv@112539e
			$entityCache->setResetCache(vtws_getWebserviceEntityId($module,$record));	//crmv@105312
		}
	}
}