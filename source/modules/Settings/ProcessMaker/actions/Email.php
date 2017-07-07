<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@92272 crmv@96450 crmv@102879 crmv@115268 */

require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('modules/com_vtiger_workflow/VTSimpleTemplate.inc');
require_once('include/Webservices/DescribeObject.php');
require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
require_once(dirname(__FILE__).'/Base.php');

class PMActionEmail extends PMActionBase {
	
	function edit(&$smarty,$id,$elementid,$retrieve,$action_type,$action_id='') {
		$PMUtils = ProcessMakerUtils::getInstance();
		$involvedRecords = $PMUtils->getRecordsInvolved($id,true);
		if (!empty($involvedRecords)) {
			$smarty->assign('INVOLVED_RECORDS', Zend_Json::encode($involvedRecords));
		}
		if ($action_id != '') {
			$vte_metadata = Zend_Json::decode($retrieve['vte_metadata']);
			$vte_metadata_arr = array();
			if (!empty($vte_metadata[$elementid])) {
				$metadata_action = $vte_metadata[$elementid]['actions'][$action_id];
			}
			$smarty->assign('METADATA', $metadata_action);
		}
		require_once('modules/com_vtiger_workflow/VTTaskManager.inc');
		require_once('modules/com_vtiger_workflow/tasks/VTEmailTask.inc');
		$task = new VTEmailTask();
		$metaVariables = $task->getMetaVariables();
		$smarty->assign("META_VARIABLES",$metaVariables);
		
		//crmv@106857
		$otherOptions = array();
		$processDynaFormObj = ProcessDynaForm::getInstance();
		$otherOptions = $processDynaFormObj->getFieldsOptions($id,true);
		$PMUtils->getAllTableFieldsOptions($id, $otherOptions);
		$smarty->assign("OTHER_OPTIONS", Zend_Json::encode($otherOptions));
		//crmv@106857e
		
		$smarty->assign('SDK_CUSTOM_FUNCTIONS',SDK::getFormattedProcessMakerFieldActions());
		
		$elementsActors = $PMUtils->getElementsActors($id,true);
		$smarty->assign('ELEMENTS_ACTORS', Zend_Json::encode($elementsActors));
	}
	
	function execute($engine,$actionid) {
		$action = $engine->vte_metadata['actions'][$actionid];

		$engine->log("Action Email","action $actionid - {$action['action_title']}");
		
		$params = array(
			'subject'=>$action['subject'],
			'description'=>$action['content'],
			'mfrom'=>$action['sender'],
			'mto'=>$action['recepient'],
			'mcc'=>$action['emailcc'],
			'mbcc'=>$action['emailbcc'],
			//'assigned_user_id'=>1,
			//'parent_id'=>"126@1|",	//TODO collego agli altri elementi?
		);
		
		// init variabiles to replace tags
		global $log, $adb;
		static $cacheWsEntities = array();
		
		$util = new VTWorkflowUtils();
		$admin = $util->adminUser();
		$entityCache = new VTEntityCache($admin);
		$util->revertUser();
		// end
		
		$PMUtils = ProcessMakerUtils::getInstance();
		
		(!empty($this->cycleRow['id'])) ? $cycleIndex = $this->cycleRow['id'] : $cycleIndex = $this->cycleIndex;
		
		// replace tags
		foreach($params as $fieldname => &$value) {
			preg_match_all('/(\$([0-9:]+)-([a-zA-Z0-9_)( :]+))/', $value, $matches, PREG_SET_ORDER);
			if (!empty($matches)) {
				foreach($matches as $match) {
					$tag = trim($match[0]);
					$tag_metaid = $match[2];
					$simpleTag = str_replace('$'.$tag_metaid.'-','$',$tag);
					if (strpos($tag_metaid,':') === false) {
						$running_process = $engine->running_process;
					} else {
						list($meta_processid,$tag_metaid) = explode(':',$tag_metaid);
						$running_process = $PMUtils->getRelatedRunningProcess($engine->running_process,$engine->processid,$meta_processid);
					}
					if (!isset($cacheWsEntities[$running_process][$tag_metaid])) {
						$mrecord = $engine->getCrmid($tag_metaid,$running_process);
						if (!empty($mrecord)) {
							$cacheWsEntities[$running_process][$tag_metaid] = vtws_getWebserviceEntityId(getSalesEntityType($mrecord),$mrecord);
						}
					}
					$entityId = $cacheWsEntities[$running_process][$tag_metaid];
					if (!empty($entityId)) {
						//crmv@106857
						if (strpos($match[3],'::') !== false) {
							// table field
							list($tfield, $tcol) = explode('::',$match[3]);
							if (($sp = strpos($tcol, ' ')) !== false) {
								$tcol = substr($tcol, 0, $sp);
							}
							$replacement = $PMUtils->replaceTableFieldTag($entityId, $tfield, $tcol, $cycleIndex);
							$value = str_replace($tag,$replacement,$value);
						} else {
							$st = new VTSimpleTemplate($simpleTag);
							$replacement = $st->render($entityCache,$entityId);
							$value = str_replace($tag,$replacement,$value);
						}
						//crmv@106857e
					}
				}
			}
			// replace dynaform tags
			//preg_match_all('/(\$DF([0-9]+)-([a-zA-Z0-9_)( :]+))/', $value, $matches, PREG_SET_ORDER);
			preg_match_all('/(\$DF([0-9:]+)-([a-zA-Z0-9_)( :]+))/', $value, $matches, PREG_SET_ORDER);
			if (!empty($matches)) {
				foreach($matches as $match) {
					$tag = trim($match[0]);
					$dynaform_metaid = $match[2];
					$dynaform_fieldname = trim($match[3]);
					if (strpos($dynaform_metaid,':') === false) {
						$running_process = $engine->running_process;
					} else {
						list($meta_processid,$dynaform_metaid) = explode(':',$dynaform_metaid);
						$running_process = $PMUtils->getRelatedRunningProcess($engine->running_process,$engine->processid,$meta_processid);
					}
					$processDynaFormObj = ProcessDynaForm::getInstance();
					$dynaform_values = $processDynaFormObj->getValues($running_process,$dynaform_metaid);
					//crmv@106857
					if (strpos($dynaform_fieldname, '::') !== false) {
						// table field
						list($tfield, $tcol) = explode('::', $dynaform_fieldname);
						if (($sp = strpos($tcol, ' ')) !== false) {
							$tcol = substr($tcol, 0, $sp);
						}
						$replacement = $PMUtils->applyTableFieldFunct('dynaform', $dynaform_values[$tfield], $tfield, $tcol, $this->cycleIndex);
						$value = str_replace($tag,$replacement,$value);
					} else {
						$value = str_replace($tag,$dynaform_values[$dynaform_fieldname],$value);
					}
					//crmv@106857e
					
				}
			}
			// end
			//crmv@100591 replace actors tags
			preg_match_all('/(\$ACTOR-([a-zA-Z0-9_-]+))/', $value, $matches, PREG_SET_ORDER);
			if (!empty($matches)) {
				foreach($matches as $match) {
					$tag = trim($match[0]);
					$elementid = trim($match[2]);
					$fieldname = substr($elementid, strrpos($elementid,'-')+1);
					$elementid = substr($elementid, 0, strrpos($elementid,'-'));
					$replacement = $PMUtils->getActor($engine->running_process, $elementid, $fieldname);
					if (!empty($replacement)) {
						if ($tag != $replacement
							&& ((!empty($referenceFields) && in_array($fieldname,$referenceFields)) || (!empty($ownerFields) && in_array($fieldname,$ownerFields)))
							&& stripos($replacement,'x') !== false
						) {
							list($wsModule,$value) = explode('x',$replacement);
						}
						$value = str_replace($tag,$replacement,$value);
					}
				}
			}
			//crmv@100591e
			// apply sdk functions
			preg_match_all('/(\$sdk:([a-zA-Z0-9_)(:.,"\']+))/', $value, $matches, PREG_SET_ORDER);
			if (!empty($matches)) {
				$sdkFieldConditions = SDK::getProcessMakerFieldActions();
				foreach($matches as $match) {
					$tag = trim($match[0]);
					$sdk_function = $match[2];
					$funct = substr($sdk_function,0,strpos($sdk_function,'('));
					if (isset($sdkFieldConditions[$funct])) {
						$funct_params = substr($sdk_function,strpos($sdk_function,'(')+1);
						$funct_params = substr($funct_params,0,strpos($funct_params,')'));
						$funct_params = trim($funct_params);
						if (!empty($funct_params)) {
							$funct_params = explode(',',$funct_params);
							array_walk($funct_params, create_function('&$v,$k', '$v = trim($v);'));
						} else {
							$funct_params = array();
						}
						require_once($sdkFieldConditions[$funct]['src']);
						$replacement = call_user_func_array($funct, $funct_params);
					} else {
						$replacement = '';
					}
					$value = str_replace($tag,$replacement,$value);
				}
			}
			// end
			if (in_array($fieldname,array('mto','mcc','mbcc'))) {
				$value = explode(',',$value);
				if (!empty($value)) {
					$tmp = array();
					foreach($value as $t) {
						$tmp[] = trim($t);
					}
					$value = implode(',',array_filter($tmp));
				}
			}
		}
		if(strlen(trim($params['mto'], " \t\n,")) == 0 && strlen(trim($params['mcc'], " \t\n,")) == 0 && strlen(trim($params['mbcc'], " \t\n,")) == 0) {
			$engine->log("Action Email","action $actionid FAILED: recepients empty");
			return;
		}
		$ct = new VTSimpleTemplate($params['description']);
		$params['description'] = $ct->render($entityCache,'');
		// end
		
		$focus = CRMentity::getInstance('Messages');
		$mail_status = $focus->send($params,false);
		if ($mail_status == 1) {
			$engine->log("Action Email","action $actionid SUCCESS");
		} else {
			$engine->log("Action Email","action $actionid FAILED: $mail_status");
		}
	}
}