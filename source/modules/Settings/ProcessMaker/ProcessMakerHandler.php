<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@92272 crmv@96450 crmv@97566 */

require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');
require_once('modules/Settings/ProcessMaker/ProcessMakerEngine.php');

class ProcessMakerHandler extends VTEventHandler {
	
	var $real_save = true;
	static public $manual_mode = array();	 //crmv@100495
	var $parallel_current_info = '';	//crmv@115579
	static public $running_processes = array();	//crmv@97575
	
	function handleEvent($eventName, $entityData) {
		global $adb, $table_prefix, $current_user;
		global $iAmAProcess; $iAmAProcess = true;	//crmv@105685
		
		// TODO check if module Processes is installed! 
		
		$PMUtils = ProcessMakerUtils::getInstance();
		
		require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
		$util = new VTWorkflowUtils();
		$isNew = $entityData->isNew();
		require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
		$entityCache = new VTEntityCache($current_user);	//$user = $util->adminUser();	//crmv@100320
		$wsModuleName = $util->toWSModuleName($entityData);
		$wsId = vtws_getWebserviceEntityId($wsModuleName,$entityData->getId());
		$PMUtils->setDefaultDataFormat();
		$entityData = $entityCache->forId($wsId);
		$PMUtils->restoreDataFormat();
		
		$moduleName = $entityData->getModuleName();
		$id = $entityData->getId();
		$entity_id = vtws_getIdComponents($id);
		$entity_id = $entity_id[1];
		
		// skip light modules
		require_once('include/utils/ModLightUtils.php'); 
		$MLUtils = ModLightUtils::getInstance();
		$light_modules = $MLUtils->getModuleList();
		if (!empty($light_modules) && in_array($moduleName,$light_modules)) return false;
		
		//crmv@97575
		static $recursion_count = 0;
		$recursion_count++;
		//crmv@97575e
		//crmv@100495
		if ($_REQUEST['run_processes'] == 'yes') {
			unset($_REQUEST['run_processes']);
			self::$manual_mode[$_REQUEST['module']] = true;
		}
		//crmv@100495e
		
		// check start events
		$nextEvents = $PMUtils->getNextEvents($entity_id, $moduleName, $this->parallel_current_info);	//crmv@115579
		$startingEvents = $PMUtils->getStartingEvents($entity_id, $moduleName);
		$events = array_merge($nextEvents,$startingEvents);
		if (!$this->real_save) {
			// search for EVERY_TIME conditions of other records involved in the process
			$otherEvents = $PMUtils->getOtherEvents($entity_id, $moduleName, $this->parallel_current_info);	//crmv@115579
			$events = array_merge($events,$otherEvents);
			$PMUtils->cleanDuplicateEvents($events);
		}
		// echo '<br>HANDLER:'; preprint($events); die;
		if (!empty($events)) {
			foreach($events as $event) {
				$running_process = $event['running_process'];
				$processid = $event['processid'];
				$elementid = $event['elementid'];
				$metaid = $event['metaid'];
				$executionCondition = $event['metadata']['execution_condition'];
				$conditions = $event['metadata']['conditions']; 
				if (!isset($conditions)) continue;
				if (isset($event['entity'])) {
					$id = $event['entity']['id'];
					$entity_id = $event['entity']['entity_id'];
					$moduleName = $event['entity']['moduleName'];
				}
				
				if (!empty($event['dynaformvalues'])) $this->addDynaFormData($entityData, $event);
				switch ($executionCondition) {
					case 'ON_FIRST_SAVE':
						if ($isNew && $this->real_save) {
							$doEvaluate = true;
						} else {
							$doEvaluate = false;
						}
						break;
					case 'ONCE':
						$result = $adb->pquery("SELECT * FROM {$table_prefix}_processmaker_a_once WHERE entity_id=? and processid=?", array($entity_id, $processid));
						/*
						// TODO: implement check for queue
						//Changes
						$result2=$adb->pquery("SELECT * FROM com_".$table_prefix."_workflowtasks 
										INNER JOIN com_".$table_prefix."_workflowtask_queue
										ON com_".$table_prefix."_workflowtasks.task_id= com_".$table_prefix."_workflowtask_queue.task_id
										WHERE processid=? AND entity_id=?",
										array($processid,$entity_id));
						
						if($adb->num_rows($result)===0 && $adb->num_rows($result2)===0)
						*/
						if ($adb->num_rows($result) === 0) {
							$doEvaluate = true;
						} else {
							$doEvaluate = false;
						}
						break;
					case 'ON_EVERY_SAVE':
						if ($this->real_save) {
							$doEvaluate = true;
						} else {
							$doEvaluate = false;
						}
						break;
					case 'ON_MODIFY':
						if (!$isNew && $this->real_save) {
							$doEvaluate = true;
						} else {
							$doEvaluate = false;
						}
					    break;
					case 'EVERY_TIME':
						$doEvaluate = true;
					    break;
					//crmv@97575
					case 'ON_SUBPROCESS':
						continue;
					    break;
					//crmv@97575e
					//crmv@100495
					case 'MANUAL_MODE':
						if (self::$manual_mode[$moduleName]) {
							$doEvaluate = true;
						}
						break;
					//crmv@100495e
					default:
						throw new Exception("Should never come here! Execution Condition:".$executionCondition);
				}
				$evaluated = false;
				
				// check if the condition is preceded by a timer (delay or start)
				$incoming = $PMUtils->getIncoming($processid,$elementid);
				(!empty($incoming)) ? $prev_elementid = $incoming[0]['shape']['id'] : $prev_elementid = false;
				$prev_structure = $PMUtils->getStructureElementInfo($processid,$prev_elementid,'shapes');
				$prevEngineType = $PMUtils->getEngineType($prev_structure);
				if ($prevEngineType == 'TimerStart') {
					continue;	// if is a start timer continue because only the cron can start these processes
				} elseif ($running_process && $prevEngineType == 'TimerIntermediate') {
					$checkTimer = true;
					$timerResult = $adb->pquery("select timer from {$table_prefix}_running_processes_timer where mode = ? and running_process = ? and elementid = ?", array('intermediate',$running_process,$prev_elementid));
					if ($timerResult && $adb->num_rows($timerResult) > 0) {
						while($row=$adb->fetchByAssoc($timerResult,-1,false)) {
							if (strtotime($row['timer']) > time()) {
								// OK
								$checkTimer = false;
								break;
							}
						}
					}
					if (!$checkTimer) continue;
				}
				// end check timer

				if ($doEvaluate) {
					
					/* track condition
					if ($running_process) {
						$current_elementid = $PMUtils->getCurrentElementId($running_process,$processid,$elementid);
						//echo "running_process:$running_process $current_elementid != $elementid<br>";
						if ($current_elementid !== false && $current_elementid != $elementid) {
							$PMEngine = ProcessMakerEngine::getInstance($running_process,$processid,$current_elementid,$elementid,$id,$metaid,$entityCache);
							$PMEngine->trackRecord($PMEngine->entity_id,$PMEngine->metaid,$current_elementid,$elementid);
							$PMEngine->trackProcess($current_elementid,$elementid);
						}
						self::$running_processes[] = array('new'=>false,'running_process'=>$running_process,'processid'=>$processid,'record'=>$entity_id,'metaid'=>$metaid);	//crmv@97575
					} crmv@109685 */

					if ($PMUtils->evaluateCondition($entityCache, $id, $conditions)){
						//echo "evaluateCondition $executionCondition ";
						//var_dump($doEvaluate);
						//echo ' isNew:';var_dump($isNew);
						//preprint($event);
						//echo '<br>';
						//continue;
						$evaluated = true;
						//crmv@100495
						if ($running_process) {
							foreach(self::$running_processes as $i => $info) {
								if ($running_process == $info['running_process']) {
									self::$running_processes[$i]['evaluated'] = $evaluated;
									break;
								}
							}
						}
						//crmv@100495e
						if ($executionCondition == 'ONCE' && $evaluated) {
							$adb->pquery("INSERT INTO {$table_prefix}_processmaker_a_once(entity_id, processid) VALUES (?,?)", array($entity_id, $processid));
						}
						$outgoings = $PMUtils->getOutgoing($processid,$elementid);
						if (!empty($outgoings)) {
							foreach($outgoings as $outgoing) {
								//echo $elementid.' '.$outgoing['shape']['id'].' ';
								// track start condition
								if (isset($event['start']) && $event['start'] === true) {
									$incoming = $PMUtils->getIncoming($processid,$elementid);
									(!empty($incoming)) ? $current_elementid = $incoming[0]['shape']['id'] : $current_elementid = false;
									if ($current_elementid !== false) {
										//crmv@104023
										$check = $PMUtils->getRunningProcess($entity_id,$metaid,$processid);
										if ($check !== false) continue;
										//crmv@104023e
										$PMEngine = ProcessMakerEngine::getInstance($running_process,$processid,$current_elementid,$elementid,$id,$metaid,$entityCache);
										//echo "running_process:$PMEngine->running_process current_elementid: $current_elementid ";
										$PMEngine->trackRecord($PMEngine->entity_id,$PMEngine->metaid,$current_elementid,$elementid);
										$PMEngine->trackProcess($current_elementid,$elementid);
										$running_process = $PMEngine->running_process;
										self::$running_processes[] = array('new'=>true,'running_process'=>$running_process,'processid'=>$processid,'evaluated'=>$evaluated,'record'=>$entity_id,'metaid'=>$metaid);	//crmv@97575	//crmv@100495
									}
								}
								// execute actions
								$engineType = $PMUtils->getEngineType($outgoing['shape']);
								$PMEngine = ProcessMakerEngine::getInstance($running_process,$processid,$elementid,$outgoing['shape']['id'],$id,$metaid,$entityCache);
								$PMEngine->execute($engineType,$outgoing['shape']['type']);
								$running_process = $PMEngine->running_process;
	
								//TODO execute $engineType == 'Condition'
							}
						}
					} else {	// for gateway else condition
						$outgoings = $PMUtils->getOutgoing($processid,$elementid);
						if (!empty($outgoings)) {
							foreach($outgoings as $outgoing) {
								$engineType = $PMUtils->getEngineType($outgoing['shape']);
								if ($engineType == 'Gateway') {
									$vte_metadata = $PMUtils->getMetadata($processid,$elementid);
									//echo "ELSE $executionCondition vteType:$engineType cond_else:".$vte_metadata['cond_else'].'<br>';
									//var_dump($doEvaluate);
									//echo '<br>';
									//continue;
									if ($vte_metadata['cond_else'] != '') {
										$evaluated = true;
										if ($executionCondition == 'ONCE' && $evaluated) {
											$adb->pquery("INSERT INTO {$table_prefix}_processmaker_a_once(entity_id, processid) VALUES (?,?)", array($entity_id, $processid));
										}
										// execute actions
										$PMEngine = ProcessMakerEngine::getInstance($running_process,$processid,$elementid,$outgoing['shape']['id'],$id,$metaid,$entityCache);
										$PMEngine->execute($engineType,$outgoing['shape']['type']);
										$running_process = $PMEngine->running_process;
									}
								}
							}
						}
					}
				}
				
				if (empty($running_process)) continue;
						
				if ($evaluated) {
					// delete boundary timers
					$PMUtils->deleteTimer('boundary',$running_process,$elementid);
					//crmv@93990
					if ($moduleName == 'Processes' && !empty($event['dynaformmetaid'])) $adb->pquery("UPDATE {$table_prefix}_process_dynaform SET done = ? WHERE running_process = ? AND metaid = ?", array(1,$running_process,$event['dynaformmetaid']));
					//if ($event['restore_dynaform_popup'] && !empty($event['dynaformmetaid'])) $adb->pquery("UPDATE {$table_prefix}_process_dynaform SET done = ? WHERE running_process = ? AND metaid = ?", array(0,$running_process,$event['dynaformmetaid']));
					if (!empty($event['end_running_process'])) {
						$adb->pquery("update {$table_prefix}_running_processes set end = ? where id = ?", array(1,$event['end_running_process']));
						$PMEngine->log("End Process ".$event['end_running_process']);
					}
					//crmv@93990e
				} else {
					// set boundary timers
					$structure = $PMUtils->getStructureElementInfo($processid,$elementid,'tree');
					$attachers = $structure['attachers'];
					if (!empty($attachers)) {
						foreach($attachers as $attacher) {
							$attacher_structure = $PMUtils->getStructureElementInfo($processid,$attacher,'shapes');
							if ($attacher_structure['subType'] == 'TimerEventDefinition') {
								$engineType = $PMUtils->getEngineType($attacher_structure);
								$PMEngine = ProcessMakerEngine::getInstance($running_process,$processid,$elementid,$attacher,$id,$metaid,$entityCache);
								$PMEngine->execute($engineType,$attacher_structure['type']);
							}
						}
					}
				}
			}
		}
		//crmv@97575
		$recursion_count--;
		if ($recursion_count == 0) {
			$PMUtils->relateSubProcessesRun(self::$running_processes);
			$iAmAProcess = false;	//crmv@105685
		}
		//crmv@97575e
		//$util->revertUser();	//crmv@100320
//die('end events');
	}
	
	function addDynaFormData(&$entityData, $event) {
		if (empty($entityData->data)) return;
		
		require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
		$processDynaFormObj = ProcessDynaForm::getInstance();
		$blocks = $processDynaFormObj->getStructure($event['processid'], false, $event['dynaformmetaid']);
		if (!empty($blocks)) {
			foreach($blocks as $block) {
				foreach($block['fields'] as $field) {
					$typeDetails = $processDynaFormObj->getFieldTypeDetails($field);
					if ($typeDetails['name'] == 'reference') {
						if (isset($event['dynaformvalues'][$field['fieldname']]) && !empty($event['dynaformvalues'][$field['fieldname']])) {
							(in_array($field['uitype'],array(52,51,50,77))) ? $module = 'Users' : $module = getSalesEntityType($event['dynaformvalues'][$field['fieldname']]);
							$event['dynaformvalues'][$field['fieldname']] = vtws_getWebserviceEntityId($module,$event['dynaformvalues'][$field['fieldname']]);
						}
					}
				}
			}
		}
		$entityData->data = array_merge($entityData->data,$event['dynaformvalues']);
	}
}