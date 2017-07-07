<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@92272 crmv@96450 crmv@97566 crmv@102879 crmv@115579 crmv@115268 */
require_once('include/Zend/Json.php');

class ProcessMakerUtils extends SDKExtendableUniqueClass {
	
	var $uploadMaxSize;
	var $importDirectory;
	var $table_name;
	var $edit_permission_mode = 'all';	//crmv@105685 all|assigned
	var $limit_processes = 2;	//crmv@98899
	var $modules_not_supported = array('Documents','Emails','Faq','PBXManager','Users','ChangeLog','ModNotifications','ModComments','Messages','Fax','MyFiles','Processes');	//crmv@37660 crmv@106461 crmv@113771
	var $editoptions_uitypes_not_supported = array(4,15,16,33,53,52,51,50,54,56,26,27,212,300,5,6,23);
	
	var $metadataTypes = array(
		'ConditionTask'=>array(
			'php'=>'modules/Settings/ProcessMaker/Metadata/ConditionTask.php',
			'tpl'=>'Settings/ProcessMaker/Metadata/ConditionTask.tpl',
		),
		'ActionTask'=>array(
			'php'=>'modules/Settings/ProcessMaker/Metadata/ActionTask.php',
			'tpl'=>'Settings/ProcessMaker/Metadata/ActionTask.tpl',
		),
		'ParallelGateway'=>array(
			'php'=>'modules/Settings/ProcessMaker/Metadata/ParallelGateway.php',
			'tpl'=>'Settings/ProcessMaker/Metadata/ParallelGateway.tpl',
		),
		'Gateway'=>array(
			'php'=>'modules/Settings/ProcessMaker/Metadata/Gateway.php',
			'tpl'=>'Settings/ProcessMaker/Metadata/Gateway.tpl',
		),
		'TimerStart'=>array(
			'php'=>'modules/Settings/ProcessMaker/Metadata/TimerStart.php',
			'tpl'=>'Settings/ProcessMaker/Metadata/TimerStart.tpl',
		),
		'TimerIntermediate'=>array(
			'php'=>'modules/Settings/ProcessMaker/Metadata/TimerIntermediate.php',
			'tpl'=>'Settings/ProcessMaker/Metadata/TimerIntermediate.tpl',
		),
		'TimerBoundaryInterr'=>array(
			'php'=>'modules/Settings/ProcessMaker/Metadata/TimerBoundary.php',
			'tpl'=>'Settings/ProcessMaker/Metadata/TimerBoundary.tpl',
		),
		'TimerBoundaryNonInterr'=>array(
			'php'=>'modules/Settings/ProcessMaker/Metadata/TimerBoundary.php',
			'tpl'=>'Settings/ProcessMaker/Metadata/TimerBoundary.tpl',
		),
		//crmv@97575
		'SubProcess'=>array(
			'php'=>'modules/Settings/ProcessMaker/Metadata/SubProcess.php',
			'tpl'=>'Settings/ProcessMaker/Metadata/SubProcess.tpl',
		),
		//crmv@97575e
	);
	var $actionTypes = array(
		'Email' => array(
			'class'=>'PMActionEmail',
			'php_file'=>'modules/Settings/ProcessMaker/actions/Email.php',
			'tpl_file'=>'Settings/ProcessMaker/actions/Email.tpl'
		),
		'Create' => array(
			'class'=>'PMActionCreate',
			'php_file'=>'modules/Settings/ProcessMaker/actions/Create.php',
			'tpl_file'=>'Settings/ProcessMaker/actions/Create.tpl'
		),
		'Update' => array(
			'class'=>'PMActionUpdate',
			'php_file'=>'modules/Settings/ProcessMaker/actions/Update.php',
			'tpl_file'=>'Settings/ProcessMaker/actions/Update.tpl'
		),
		'Delete' => array(
			'class'=>'PMActionDelete',
			'php_file'=>'modules/Settings/ProcessMaker/actions/Delete.php',
			'tpl_file'=>'Settings/ProcessMaker/actions/Delete.tpl'
		),
		//crmv@105685
		'ResetDynaform' => array(
			'class'=>'PMActionResetDynaform',
			'php_file'=>'modules/Settings/ProcessMaker/actions/ResetDynaform.php',
			'tpl_file'=>'Settings/ProcessMaker/actions/ResetDynaform.tpl'
		),
		//crmv@105685e
		'Cycle' => array(
			'class'=>'PMActionCycle',
			'php_file'=>'modules/Settings/ProcessMaker/actions/Cycle.php',
			'tpl_file'=>'Settings/ProcessMaker/actions/Cycle.tpl',
			'actions'=>array('Email','Create','InsertTableRow','DeleteTableRow')
		),
		//crmv@112297
		'DeleteConditionals' => array(
			'class'=>'PMActionDeleteConditionals',
			'php_file'=>'modules/Settings/ProcessMaker/actions/DeleteConditionals.php',
			'tpl_file'=>'Settings/ProcessMaker/actions/DeleteConditionals.tpl'
		),
		//crmv@112297e
		//crmv@113775
		'Relate' => array(
			'class'=>'PMActionRelate',
			'php_file'=>'modules/Settings/ProcessMaker/actions/Relate.php',
			'tpl_file'=>'Settings/ProcessMaker/actions/Relate.tpl'
		),
		//crmv@113775e
		'InsertTableRow' => array(
			'class'=>'PMActionInsertTableRow',
			'php_file'=>'modules/Settings/ProcessMaker/actions/InsertTableRow.php',
			'tpl_file'=>'Settings/ProcessMaker/actions/InsertTableRow.tpl'
		),
		'DeleteTableRow' => array(
			'class'=>'PMActionDeleteTableRow',
			'php_file'=>'modules/Settings/ProcessMaker/actions/DeleteTableRow.php',
			'tpl_file'=>'Settings/ProcessMaker/actions/DeleteTableRow.tpl',
			'hide_main_menu'=>true
		),
	);
	
	function __construct() {
		global $upload_maxsize, $import_dir, $table_prefix;
		$this->uploadMaxSize = $upload_maxsize;
		$this->importDirectory = $import_dir;
		$this->table_name = $table_prefix.'_processmaker';
		eval(Users::m_de_cryption());
		eval($hash_version[22]);
		if (!$this->todoFunctions) {
			unset($this->actionTypes['ResetDynaform']); //crmv@105685
			unset($this->actionTypes['Cycle']);
			unset($this->actionTypes['DeleteConditionals']);
			unset($this->actionTypes['InsertTableRow']);
			//crmv@113775
			unset($this->actionTypes['Relate']);
			$this->modules_not_supported[] = 'MyNotes';
			//crmv@113775e
		}
	}
	//crmv@98899
	function limitProcessesExceeded() {
		global $adb, $table_prefix;
		$result = $adb->query("SELECT COUNT(*) as \"count\" FROM {$table_prefix}_processmaker WHERE active = 1");
		if ($result && $adb->num_rows($result) > 0) {
			$count = $adb->query_result($result,0,'count');
			if ($count > $this->limit_processes) return $count;
		}
		return false;
	}
	//crmv@98899e
	function checkActiveProcesses() {
		global $adb, $table_prefix;
		$result = $adb->query("SELECT COUNT(*) as \"count\" FROM {$table_prefix}_processmaker WHERE active = 1");
		if ($result && $adb->num_rows($result) > 0) {
			$count = $adb->query_result($result,0,'count');
			if ($count < $this->limit_processes) return true;
		}
		return false;
	}
	function formatType($type, $display=false) {
		if (strpos($type,':') !== false) $type = substr($type,strpos($type,':')+1);
		if ($display) $type = "BPMN-$type";
		return $type;
	}
	function getMetadataTypes($type='',$structure=array()) {
		if (!empty($type)) {
			//$metadataType = $this->metadataTypes[$type];
			//if (empty($metadataType)) {
				if ($type == 'Task') {
					$metadataType = $this->metadataTypes['ConditionTask'];
				} elseif (strpos($type,'Task') !== false || $type == 'EndEvent') {
					$metadataType = $this->metadataTypes['ActionTask'];
				} elseif ($type == 'ParallelGateway') {
					$metadataType = $this->metadataTypes['ParallelGateway'];
				} elseif (strpos($type,'Gateway') !== false) {
					$metadataType = $this->metadataTypes['Gateway'];
				} elseif ($type == 'StartEvent' && $structure['subType'] == 'TimerEventDefinition') {
					$metadataType = $this->metadataTypes['TimerStart'];
				} elseif ($type == 'IntermediateCatchEvent' && $structure['subType'] == 'TimerEventDefinition') {
					$metadataType = $this->metadataTypes['TimerIntermediate'];
				} elseif ($type == 'BoundaryEvent' && $structure['subType'] == 'TimerEventDefinition') {
					($structure['cancelActivity']) ? $metadataType = $this->metadataTypes['TimerBoundaryInterr'] : $metadataType = $this->metadataTypes['TimerBoundaryNonInterr'];
				//crmv@97575
				} elseif ($type == 'SubProcess') {
					$metadataType = $this->metadataTypes['SubProcess'];
				}
				//crmv@97575e
			//}
			return $metadataType;
		} else {
			return $this->metadataTypes;
		}
	}
	function getEngineType($structure) {
		$engineType = '';
		if ($structure['type'] == 'Task') {
			$engineType = 'Condition';
		} elseif (strpos($structure['type'],'Task') !== false || $structure['type'] == 'EndEvent') {
			$engineType = 'Action';
		} elseif (strpos($structure['type'],'Gateway') !== false) {
			$engineType = 'Gateway';
		} elseif ($structure['type'] == 'StartEvent' && $structure['subType'] == 'TimerEventDefinition') {
			$engineType = 'TimerStart';
		} elseif ($structure['type'] == 'IntermediateCatchEvent' && $structure['subType'] == 'TimerEventDefinition') {
			$engineType = 'TimerIntermediate';
		} elseif ($structure['type'] == 'BoundaryEvent' && $structure['subType'] == 'TimerEventDefinition') {
			($structure['cancelActivity']) ? $engineType = 'TimerBoundaryInterr' : $engineType = 'TimerBoundaryNonInterr';
		//crmv@97575
		} elseif ($structure['type'] == 'SubProcess') {
			$engineType = 'SubProcess';
		}
		//crmv@97575e
		return $engineType;
	}
//	function getVteType($id,$elementid) {
//		$structure = $this->getStructure($id);
//		$element = $structure['tree'][$elementid];
//		preprint($element);
//	}
	function getActionTypes($type='') {
		$actionTypes = $this->actionTypes;
		foreach($actionTypes as $actionType => $info) {
			$actionTypes[$actionType]['label'] = getTranslatedString('LBL_PM_ACTION_'.$actionType,'Settings');
		}
		$sdkActions = SDK::getProcessMakerActions();
		if (!empty($sdkActions)) {
			foreach($sdkActions as $sdkAction) {
				$actionTypes['SDK:'.$sdkAction['funct']] = $sdkAction;
			}
		}
		if (!empty($type)) {
			return $actionTypes[$type];
		} else {
			return $actionTypes;
		}
	}
	function isStartTask($id,$elementid) {
		$incoming = $this->getIncoming($id,$elementid);
		return ($incoming[0]['shape']['type'] == 'StartEvent');
	}
	function isEndTask($bpmnType) {
		if ($bpmnType == 'EndEvent') {	// TODO add other types of end events
			return true;
		} else {
			return false;
		}
	}
	
	function getHeaderList($relate=false) {
		if ($relate) 
			return array(
				'',
				getTranslatedString('LBL_PROCESS_MAKER_RECORD_NAME','Settings'),
				getTranslatedString('LBL_PROCESS_MAKER_RECORD_DESC','Settings'),
				getTranslatedString('LBL_PM_SUBPROCESSES','Settings'),				
				getTranslatedString('Active')
			);
		else
			return array(
				getTranslatedString('LBL_ACTIONS'),
				getTranslatedString('LBL_PROCESS_MAKER_RECORD_NAME','Settings'),
				getTranslatedString('LBL_PROCESS_MAKER_RECORD_DESC','Settings'),
				getTranslatedString('LBL_PM_SUBPROCESSES','Settings'),
				getTranslatedString('Active')
			);
	}
	
	function getList($relate=false, $father='', $record_checked='') {
		global $adb, $theme, $app_strings;
		$list = array();
		$query = "select * from {$this->table_name}";
		$params = array();
		if ($relate) {
			$query .= " where id <> ?";
			$params[] = $father;
		}
		$result = $adb->pquery($query, $params);
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result,-1,false)) {
				$subprocesses_text = array();
				$subprocesses = $this->getSubprocesses($row['id']);
				if (!empty($subprocesses)) {
					foreach($subprocesses as $elementid => $subprocess) {
						if ($relate) {
							$subprocesses_text[] = textlength_check($subprocess['name']);
						} else {
							$subprocesses_text[] = '<a href="index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&parenttab=Settings&mode=detail&id='.$subprocess['subprocess'].'">'.textlength_check($subprocess['name']).'</a>';							
						}
					}
				}
				if (count($subprocesses_text) > 3) $subprocesses_text = $subprocesses_text[0].', '.$subprocesses_text[1].', '.$subprocesses_text[2].', ...';
				else $subprocesses_text = implode(',',$subprocesses_text);
				if ($relate) {
					(!empty($record_checked) && $record_checked == $row['id']) ? $checked = 'checked' : $checked = '';
					$list[] = array(
						'<input type="radio" name="subprocess" id="subprocess_'.$row['id'].'" value="'.$row['id'].'" '.$checked.'/>',
						'<a href="javascript:;"><label for="subprocess_'.$row['id'].'" style="font-weight:normal">'.textlength_check($row['name']).'</label></a>',
						textlength_check($row['description']),
						$subprocesses_text,
						($row['active'] == 1) ? $app_strings['yes'] : $app_strings['no'],
					);
				} else {
					$list[] = array(
						'<a href="javascript:ProcessMakerScript.confirmdelete(\'index.php?module=Settings&action=ProcessMaker&parenttab=Settings&mode=delete&id='.$row['id'].'\')"><img src="'.vtiger_imageurl('small_delete.png',$theme).'" title="'.getTranslatedString('LBL_DELETE_BUTTON').'" border="0" /></a>
						<a href="index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=download&format=bpmn&id='.$row['id'].'"><img src="modules/Messages/src/img/download.png" title="'.getTranslatedString('LBL_DOWNLOAD_BPMN','Settings').'" border="0" /></a>
						<a href="index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=download&format=vtebpmn&id='.$row['id'].'"><img src="modules/Messages/src/img/download.png" title="'.getTranslatedString('LBL_DOWNLOAD_VTEBPMN','Settings').'" border="0" /></a>',
						'<a href="index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&parenttab=Settings&mode=detail&id='.$row['id'].'">'.textlength_check($row['name']).'</a>',
						textlength_check($row['description']),
						$subprocesses_text,
						($row['active'] == 1) ? $app_strings['yes'] : $app_strings['no'],
					);
				}
			}
		}
		return $list;
	}
	
	//crmv@100972
	function checkUploadBPMN(&$err='') {
		if (isset($_FILES['bpmnfile']['tmp_name']) && !empty($_FILES['bpmnfile']['tmp_name'])) {
			$xml = file_get_contents($_FILES['bpmnfile']['tmp_name']);
		}
		if (!empty($xml)) {
			$ext = pathinfo($_FILES['bpmnfile']['name'], PATHINFO_EXTENSION);
			if (!in_array($ext,array('bpmn','vtebpmn'))) {
				$err = getTranslatedString('LBL_INVALID_FILE_EXTENSION', 'Settings');
				return false;
			}
			if(!is_uploaded_file($_FILES['bpmnfile']['tmp_name'])) {
				$err = getTranslatedString('LBL_FILE_UPLOAD_FAILED', 'Import');
				return false;
			}
			if ($_FILES['bpmnfile']['size'] > $this->uploadMaxSize) {
				$err = getTranslatedString('LBL_IMPORT_ERROR_LARGE_FILE', 'Import').' $uploadMaxSize.'.getTranslatedString('LBL_IMPORT_CHANGE_UPLOAD_SIZE', 'Import');
				return false;
			}
			if(!is_writable($this->importDirectory)) {
				$err = getTranslatedString('LBL_IMPORT_DIRECTORY_NOT_WRITABLE', 'Import');
				return false;
			}
		}
		return true;
	}
	
	function readUploadedBPMN(&$smarty) {
		$name = vtlib_purify($_REQUEST['name']);
		$description = vtlib_purify($_REQUEST['description']);
		if (isset($_FILES['bpmnfile']['tmp_name']) && !empty($_FILES['bpmnfile']['tmp_name'])) {
			$xml = file_get_contents($_FILES['bpmnfile']['tmp_name']);
		}
		$smarty->assign("FILE", $xml);
		if (stripos($xml,'<vtebpmn>') !== false) {
			$xmlObj = new SimpleXMLElement($xml);
			$xml = base64_decode($xmlObj->bpmn);
		}
		$smarty->assign("NAME", $name);
		$smarty->assign("DESCRIPTION", $description);
		$smarty->assign("XML", $xml);
	}
	//crmv@100972e
	
	function create($name,$description,$xml,$vte_metadata,$structure,$helper,$metarec=array(),$dynameta=array()) {
		global $adb, $table_prefix;
		$id = $adb->getUniqueID($this->table_name);
		$adb->pquery("insert into {$this->table_name}(id,name,description,vte_metadata,structure,helper) values(?,?,?,?,?,?)", array($id,$name,$description,$vte_metadata,$structure,$helper));
		$adb->updateClob($this->table_name,'xml',"id=$id",$xml);
		// save records involved
		if (!empty($metarec)) {
			foreach($metarec as $r) {
				$r['processid'] = $id;
				$adb->pquery("insert into {$table_prefix}_processmaker_metarec(".implode(',',array_keys($r)).") values (".generateQuestionMarks($r).")", $r);
			}
		}
		// save dynaform informations
		if (!empty($dynameta)) {
			foreach($dynameta as $r) {
				$r['processid'] = $id;
				$adb->pquery("insert into {$table_prefix}_process_dynaform_meta(".implode(',',array_keys($r)).") values (".generateQuestionMarks($r).")", $r);
			}
		}
		return $id;
	}
	function edit($id,$data) {
		global $adb, $table_prefix;
		$columns = array('name','description','active');
		$update = array();
		foreach($columns as $column) {
			if (isset($data[$column])) $update["$column=?"] = vtlib_purify($data[$column]);
		}
		$retrieve = $this->retrieve($id);

		if (isset($data['vte_metadata'])) {
			(empty($retrieve['vte_metadata'])) ? $vte_metadata = array() : $vte_metadata = $vte_metadata_old = Zend_Json::decode($retrieve['vte_metadata']);
			(empty($data['vte_metadata'])) ? $vte_metadata_new = array() : $vte_metadata_new = Zend_Json::decode($data['vte_metadata']);
			$vte_metadata = array_merge($vte_metadata, $vte_metadata_new);
			$update["vte_metadata=?"] = Zend_Json::encode($vte_metadata);
		} else {
			$vte_metadata = Zend_Json::decode($retrieve['vte_metadata']);
		}
		if (isset($data['helper'])) {
			(empty($retrieve['helper'])) ? $helper = array() : $helper = Zend_Json::decode($retrieve['helper']);
			(empty($data['helper'])) ? $helper_new = array() : $helper_new = Zend_Json::decode($data['helper']);
			$helper = array_merge($helper, $helper_new);
			$update["helper=?"] = Zend_Json::encode($helper);
		}
		$adb->pquery("update {$this->table_name} set ".implode(',',array_keys($update))." where id = ?", array($update,$id));

		// save records involved
		if (isset($vte_metadata)) {
			//$this->clearRecordsInvolved($id);
			$structure = $this->getStructure($id);
			foreach($structure['shapes'] as $shapeid => $shape) {
				// for all start events search the first task
				if ($shape['type'] == 'StartEvent') {
					$outgoing = $this->getOutgoing($id,$shapeid);
					foreach($outgoing as $out) {
						$metadata = $vte_metadata[$out['shape']['id']];
						$this->setRecordInvolved($id,$out['shape']['id'],$out['shape']['text'],$out['shape']['type'],$metadata['moduleName'],0,1);
					}
				}
				//crmv@97575
				if ($shape['type'] == 'SubProcess') {
					$subprocess = $vte_metadata[$shapeid]['subprocess'];
					if (!empty($subprocess)) $this->setSubprocess($id,$shapeid,$subprocess);
				}
				//crmv@97575e
			}
			foreach($vte_metadata as $elementid => $m) {
				if (!empty($m['actions'])) {
					foreach($m['actions'] as $action_id => $a) {
						if ($a['action_type'] == 'Create') {
							$start = intval($this->isStartTask($a['id'],$a['elementid']));
							$this->setRecordInvolved($a['id'],$a['elementid'],$structure['shapes'][$elementid]['text'],$structure['shapes'][$elementid]['type'],$a['form_module'],$action_id,$start);
						}
					}
				}
			}
		}
		
		// save dynaform
		if (!empty($helper)) {
			foreach($helper as $elementid => $h) {
				if (!empty($h['dynaform']['mmaker_blocks'])) {
					$result = $adb->pquery("select * from {$table_prefix}_process_dynaform_meta where processid = ? and elementid = ?", array($id,$elementid));
					if ($result && $adb->num_rows($result) == 0) {
						$structure = $this->getStructureElementInfo($id,$elementid,'shapes');
						$metaid = $adb->getUniqueID("{$table_prefix}_process_dynaform_meta");
						$adb->pquery("insert into {$table_prefix}_process_dynaform_meta(id,processid,elementid,text,type) values(?,?,?,?,?)", array($metaid,$id,$elementid,$structure['text'],$structure['type']));
					}
				}
			}
		}
		
		// if there is a start timer schedule the running process
		$startElementid = '';
		$isTimerProcess = $this->isTimerProcess($id,$startElementid);
		if ($isTimerProcess && (($retrieve['active'] == 0 && $data['active'] == 1) || ($retrieve['active'] == 1 && $this->isChangedTimerCondition($vte_metadata_new[$startElementid],$vte_metadata_old[$startElementid])))) {
			// cancello eventuali processi schedulati non ancora partiti
			$result = $adb->pquery("SELECT running_process FROM {$table_prefix}_running_processes_timer
				INNER JOIN {$table_prefix}_running_processes ON {$table_prefix}_running_processes.id = {$table_prefix}_running_processes_timer.running_process
				WHERE mode = ? and {$table_prefix}_running_processes.processmakerid = ?", array('start',$id));
			$delete = array();
			if ($result && $adb->num_rows($result) > 0) {
				while($row=$adb->fetchByAssoc($result)) {
					$delete[] = $row['running_process'];
				}
			}
			if (!empty($delete)) {
				$adb->pquery("delete from {$table_prefix}_running_processes where id in (".generateQuestionMarks($delete).")", $delete);
				$this->deleteTimer('start',$delete);
			}
			// schedule the first occourence
			$date_start = $vte_metadata[$startElementid]['date_start'].' '.$vte_metadata[$startElementid]['starthr'].':'.$vte_metadata[$startElementid]['startmin'].':00';
			($vte_metadata['date_end_mass_edit_check'] == 'on') ? $date_end = getValidDBInsertDateValue($vte_metadata['date_end']).' '.$vte_metadata['endhr'].':'.$vte_metadata['endmin'] : $date_end = false;
			$timer = $this->getTimerRecurrences($date_start,$date_end,($vte_metadata[$startElementid]['recurrence'] == 'on'),$vte_metadata[$startElementid]['cron_value'],1);
			if (!empty($timer[0])) {
				$running_process = $adb->getUniqueID("{$table_prefix}_running_processes");
				$adb->pquery("insert into {$table_prefix}_running_processes(id,processmakerid,current) values(?,?,?)", array($running_process,$id,$startElementid));
				$info = array('processid'=>$id,'elementid'=>$startElementid,'running_process'=>$running_process,'calculate_next_occourence'=>true);
				$this->createTimer('start',$timer,$running_process,null,$startElementid,$info);
			}
		}
	}
	function retrieve($id) {
		global $adb;
		$result = $adb->pquery("select * from {$this->table_name} where id = ?", array($id));
		if ($result && $adb->num_rows($result) > 0) {
			return $adb->fetchByAssoc($result,-1,false);
		}
	}
	function delete($id) {
		global $adb, $table_prefix;
		$adb->pquery("delete from {$this->table_name} where id = ?", array($id));
		$adb->pquery("delete from {$table_prefix}_processmaker_metarec where processid = ?", array($id));
		$adb->pquery("delete from {$table_prefix}_process_dynaform_meta where processid = ?", array($id));
	}
	function getMetadata($id,$elementid='') {
		$data = $this->retrieve($id);
		$vte_metadata = Zend_Json::decode($data['vte_metadata']);
		if (!empty($elementid))
			return $vte_metadata[$elementid];
		else
			return $vte_metadata;
	}
	function saveMetadata($id,$elementid,$vte_metadata,$helper=array(),$dynaform=null) {
		$data = array();
		$vte_metadata = Zend_Json::decode($vte_metadata);
		
		// format values
		$structure = $this->getStructureElementInfo($id,$elementid,'shapes');
		if ($structure['type'] == 'StartEvent' && $structure['subType'] == 'TimerEventDefinition') {
			$vte_metadata['date_start'] = getValidDBInsertDateValue($vte_metadata['date_start']);
			$vte_metadata['date_end'] = getValidDBInsertDateValue($vte_metadata['date_end']);
		}
		
		if (!empty($vte_metadata)) {
			$data['vte_metadata'] = Zend_Json::encode(array($elementid=>$vte_metadata));
		}
		if (!empty($helper)) {
			$helper = Zend_Json::decode($helper);
			if ($helper['assigntype'] == 'U') $helper['assigned_user_id'] = $helper['assigned_user_id'];
			elseif($helper['assigntype'] == 'T') $helper['assigned_user_id'] = $helper['assigned_group_id'];
			elseif($helper['assigntype'] == 'O') $helper['assigned_user_id'] = $helper['other_assigned_user_id'];
			unset($helper['assigntype']); unset($helper['assigned_user_id_display']); unset($helper['assigned_group_id']); unset($helper['assigned_group_id_display']); unset($helper['other_assigned_user_id']);
			if (!empty($dynaform)) $helper['dynaform'] = $dynaform;
			//crmv@106856
			if ($helper['assigned_user_id'] == 'advanced_field_assignment') {
				$tmp = $this->getAdvancedFieldAssignment('assigned_user_id');;
				if (!empty($tmp)) $helper['advanced_field_assignment']['assigned_user_id'] = $tmp;
			}
			//crmv@106856e
			$data['helper'] = Zend_Json::encode(array($elementid=>$helper));
		}
		$this->edit($id,$data);
	}
	//crmv@97575
	function setSubprocess($id,$elementid,$subprocess) {
		global $adb, $table_prefix;
		
		// TODO use only the table _processmaker_rel and delete the _subprocesses
		$adb->pquery("delete from {$table_prefix}_subprocesses where processid = ? and elementid = ?",array($id,$elementid));
		$adb->pquery("insert into {$table_prefix}_subprocesses(processid,elementid,subprocess) values(?,?,?)",array($id,$elementid,$subprocess));
		
		$result = $adb->pquery("select id from {$table_prefix}_processmaker_rel where processid = ? and elementid = ?",array($id,$elementid));
		if ($result && $adb->num_rows($result) > 0) {
			$adb->pquery("update {$table_prefix}_processmaker_rel set related = ?, related_role = ? where id = ?",array($subprocess,'son',$adb->query_result($result,0,'id')));
		} else {
			$adb->pquery("insert into {$table_prefix}_processmaker_rel(id,processid,elementid,related,related_role) values(?,?,?,?,?)",array($adb->getUniqueID("{$table_prefix}_processmaker_rel"),$id,$elementid,$subprocess,'son'));
		}
		$result = $adb->pquery("select id from {$table_prefix}_processmaker_rel where related = ? and elementid = ?",array($id,$elementid));
		if ($result && $adb->num_rows($result) > 0) {
			$adb->pquery("update {$table_prefix}_processmaker_rel set processid = ?, related_role = ? where id = ?",array($subprocess,'father',$adb->query_result($result,0,'id')));
		} else {
			$adb->pquery("insert into {$table_prefix}_processmaker_rel(id,processid,elementid,related,related_role) values(?,?,?,?,?)",array($adb->getUniqueID("{$table_prefix}_processmaker_rel"),$subprocess,$elementid,$id,'father'));
		}
	}
	function getSubprocesses($id,$elementid='') {
		global $adb, $table_prefix;
		$query = "select {$table_prefix}_subprocesses.*, {$table_prefix}_processmaker.name
			from {$table_prefix}_subprocesses
			inner join {$table_prefix}_processmaker on {$table_prefix}_processmaker.id = {$table_prefix}_subprocesses.subprocess
			where {$table_prefix}_subprocesses.processid = ?";
		$params = array($id);
		if (!empty($elementid)) {
			$query .= " and elementid = ?";
			$params[] = $elementid;
		}
		$result = $adb->pquery($query,$params);
		if ($result && $adb->num_rows($result) > 0) {
			$return = array();
			while($row=$adb->fetchByAssoc($result)) {
				if (!empty($elementid)) return $row;
				else $return[$row['elementid']] = $row;
			}
			return $return;
		}
		return false;
	}
	function getRelatedProcess($id,$meta='') {
		global $adb, $table_prefix;
		$query = "select {$table_prefix}_processmaker_rel.*, {$table_prefix}_processmaker.name
			from {$table_prefix}_processmaker_rel
			inner join {$table_prefix}_processmaker on {$table_prefix}_processmaker.id = {$table_prefix}_processmaker_rel.related
			where {$table_prefix}_processmaker_rel.processid = ?";
		$params = array($id);
		if (!empty($meta)) {
			$query .= " and {$table_prefix}_processmaker_rel.id = ?";
			$params[] = $meta;
		}
		$result = $adb->pquery($query,$params);
		if ($result && $adb->num_rows($result) > 0) {
			$return = array();
			while($row=$adb->fetchByAssoc($result)) {
				$return[$row['related']] = $row;
			}
			return $return;
		}
		return false;
	}
	function unsetSubProcesses($processid,&$vte_metadata) {
		$vte_metadata = Zend_Json::decode($vte_metadata);
		$structure = $this->getStructure($processid);
		foreach($vte_metadata as $elementid => &$metadata) {
			if ($structure['shapes'][$elementid]['type'] == 'SubProcess') {
				unset($metadata['subprocess']);
			}
		}
		$vte_metadata = Zend_Json::encode($vte_metadata);
	}
	function relateSubProcessesRun($running_processes) {
		global $adb, $table_prefix;
		if (!empty($running_processes)) {
			$all_running_process = array();
			foreach($running_processes as $info) {
				$all_running_process[$info['running_process']] = $info['processid'];
			}
			foreach($running_processes as $info) {
				if ($info['new']) {
					$new_running_process = $info['running_process'];
					$result = $adb->pquery("SELECT processid FROM {$table_prefix}_subprocesses
						INNER JOIN {$table_prefix}_running_processes ON {$table_prefix}_running_processes.processmakerid = {$table_prefix}_subprocesses.subprocess
						WHERE {$table_prefix}_running_processes.id = ?", array($new_running_process));
					if ($result && $adb->num_rows($result) > 0) {
						$father_processid = $adb->query_result($result,0,'processid');
						$father_running_process = array_search($father_processid, $all_running_process);
						if ($father_running_process !== false) {
							// set the father of the new running process
							$adb->pquery("update {$table_prefix}_running_processes set father = ? where id = ?", array($father_running_process,$new_running_process));
							// set the father in the Processes record (if exists)
							$father = $this->getProcessFatherRun($new_running_process);
							if (!empty($father)) {
								$adb->pquery("update {$table_prefix}_processes set father = ? where running_process = ?", array($father,$new_running_process));
							}
						}
					}
				}
			}
		}
	}
	function getProcessFatherRun($running_process='') {
		global $adb, $table_prefix;
		$result = $adb->pquery("SELECT {$table_prefix}_processes.processesid
			FROM {$table_prefix}_running_processes
			INNER JOIN {$table_prefix}_processes ON {$table_prefix}_processes.running_process = {$table_prefix}_running_processes.father
			WHERE {$table_prefix}_running_processes.id = ?", array($running_process));
		if ($result && $adb->num_rows($result) > 0) {
			return $adb->query_result($result,0,'processesid');
		}
		return false;
	}
	function getRelatedRunningProcess($running_process, $processid, $meta_processid) {
		global $adb, $table_prefix;
		$this->relateSubProcessesRun(ProcessMakerHandler::$running_processes);	// try to set sub processes
		$related = $this->getRelatedProcess($processid,$meta_processid);
		if ($related !== false) {
			$related = array_shift($related);
			if ($related['related_role'] == 'father') {
				$result = $adb->pquery("SELECT rel.id
					FROM {$table_prefix}_running_processes running_processes
					INNER JOIN {$table_prefix}_running_processes rel ON rel.id = running_processes.father
					WHERE running_processes.id = ? AND rel.processmakerid = ?",
					array($running_process,$related['related']));
			} elseif ($related['related_role'] == 'son') {
				$result = $adb->pquery("SELECT rel.id
					FROM {$table_prefix}_running_processes running_processes
					INNER JOIN {$table_prefix}_running_processes rel ON rel.father = running_processes.id
					WHERE running_processes.id = ? AND rel.processmakerid = ?",
					array($running_process,$related['related']));								
			}
			if ($result && $adb->num_rows($result) > 0) {
				$running_process = $adb->query_result($result,0,'id');
			}
			return $running_process;
		}
		return false;
	}
	//crmv@97575e
	function clearRecordsInvolved($id) {
		global $adb, $table_prefix;
		$adb->pquery("delete from {$table_prefix}_processmaker_metarec where processid = ?", array($id));
	}
	function setRecordInvolved($id,$elementid,$text,$type,$module,$action,$start=0) {
		global $adb, $table_prefix;
		$result = $adb->pquery("select id from {$table_prefix}_processmaker_metarec where processid = ? and elementid = ? and action = ?", array($id,$elementid,$action));
		if ($result && $adb->num_rows($result) > 0) {
			$metarecid = $adb->query_result($result,0,'id');
			$adb->pquery("update {$table_prefix}_processmaker_metarec set module = ?, action = ?, start = ? where id = ? and processid = ? and elementid = ?", array($module,$action,$start,$metarecid,$id,$elementid));
		} else {
			$metarecid = $adb->getUniqueID("{$table_prefix}_processmaker_metarec");
			$adb->pquery("insert into {$table_prefix}_processmaker_metarec values (?,?,?,?,?,?,?,?)", array($metarecid,$id,$elementid,$text,$type,$module,$action,$start));
		}
	}
	function getRecordsInvolvedLabel($processid,$metaid,$row=array(),$related=false) {
		if (empty($row)) {
			global $adb, $table_prefix;
			$result = $adb->pquery("select * from {$table_prefix}_processmaker_metarec where id = ? and processid = ?", array($metaid,$processid));
			if ($result && $adb->num_rows($result) > 0) {
				$row = $adb->fetch_array_no_html($result);
			}
		}
		$label = '[$'.$row['id'].'] '.getTranslatedString($row['module'],$row['module']).' ('.$this->formatType($row['type'],true).': '.trim($row['text']).')';
		if ($related !== false) $label = $info['name'].' '.$label;
		return $label;
	}
	function getRecordsInvolved($id,$related=false,$elementid='',$action='') {
		global $adb, $table_prefix;
		$query = "select * from {$table_prefix}_processmaker_metarec where processid = ?";
		$params = array($id);
		if (!empty($elementid)) {
			$query .= " and elementid = ?";
			$params[] = $elementid;
		}
		if ($action !== '') {
			$query .= " and action = ?";
			$params[] = $action;
		}
		$result = $adb->pquery($query,$params);
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$records[] = array(
					'seq'=>$row['id'],
					'id'=>$row['elementid'],
					'text'=>$row['text'],
					'type'=>$row['type'],
					'module'=>$row['module'],
					'action'=>$row['action'],
					'translatedModule'=>getTranslatedString($row['module'],$row['module']),
					'label'=>$this->getRecordsInvolvedLabel($id,$row['id'],$row),
				);
			}
		}
		//crmv@98809
		if ($related) {
			$processes = $this->getRelatedProcess($id);
			if (!empty($processes)) {
				foreach($processes as $relid => $info) {
					$result = $adb->pquery("select * from {$table_prefix}_processmaker_metarec where processid = ?", array($relid));
					if ($result && $adb->num_rows($result) > 0) {
						while($row=$adb->fetchByAssoc($result)) {
							$records[] = array(
								'meta_processid'=>$info['id'],
								'seq'=>$row['id'],
								'id'=>$row['elementid'],
								'text'=>$row['text'],
								'type'=>$row['type'],
								'module'=>$row['module'],
								'action'=>$row['action'],
								'translatedModule'=>getTranslatedString($row['module'],$row['module']),
								'label'=>$this->getRecordsInvolvedLabel($id,$row['id'],$row,$info),
							);
						}
					}
				}
			}
		}
		//crmv@98809e
		return $records;
	}
	function getRecordsInvolvedOptions($id, $selected_value='', $startTask=false, $excluded_values=array()) {
		$records = $this->getRecordsInvolved($id);
		$values = array(''=>array(getTranslatedString('LBL_PLEASE_SELECT'),''));
		if ($startTask) {
			($selected_value == 'current') ? $selected = 'selected' : $selected = '';
			$values['current'] = array(getTranslatedString('LBL_PMH_CURRENT_ENTITY','Settings'), $selected);
		} else {
			if (!empty($records)) {
				foreach($records as $r) {
					$key = $r['seq'].':'.$r['module'];
					if (!empty($excluded_values) && in_array($key,$excluded_values)) continue;
					($selected_value == $key) ? $selected = 'selected' : $selected = '';
					$values[$key] = array($r['label'], $selected);
				}
			}
		}
		return $values;
	}
	function getOwnerFieldOptions($id, $selected_value='', $startTask=false, $related=false) {
		global $adb, $table_prefix, $app_strings;
		$records = $this->getRecordsInvolved($id,$related);
		$values = array();
		$values[''][''] = array(getTranslatedString('LBL_PLEASE_SELECT'),'');
		if ($startTask) {
			($selected_value == 'current') ? $selected = 'selected' : $selected = '';
			$values['']['current'] = array(getTranslatedString('LBL_PMH_CURRENT_ENTITY','Settings'), $selected);
		} else {
			if (!empty($records)) {
				foreach($records as $r) {
					$moduleInstance = Vtiger_Module::getInstance($r['module']);
					$result = $adb->pquery("select fieldname, fieldlabel from {$table_prefix}_field where tabid = ? and uitype in (?,?,?,?,?)", array($moduleInstance->id,53,52,51,50,77));	//crmv@101683
					if ($result && $adb->num_rows($result) > 0) {
						while($row=$adb->fetchByAssoc($result)) {
							$key = $r['meta_processid'].':'.$r['seq'].':'.$r['module'].':'.$row['fieldname'];
							($selected_value == $key) ? $selected = 'selected' : $selected = '';
							$values[$r['label']][$key] = array(getTranslatedString($row['fieldlabel'],$r['module']), $selected);
						}
					}
				}
			}
		}
		//crmv@100591
		$elementsActors = $this->getElementsActors($id);
		if (!empty($elementsActors)) {
			foreach($elementsActors as $key => $value) {
				($selected_value == $key) ? $selected = 'selected' : $selected = '';
				$values[$app_strings['LBL_PM_ELEMENTS_ACTORS']][$key] = array($value, $selected);
			}
		}
		//crmv@100591e
		return $values;
	}
	
	/*
	 * crmv@109589
	 * if vte_metadata is not empty remove shapes deleted from vte_metadata, helper, vte_processmaker_metarec, vte_process_dynaform_meta, ecc.
	 */
	function saveStructure($id,$value) {
		global $adb, $table_prefix;
		$columns = array('structure = ?');
		$values = array($value);
		
		$structure = Zend_Json::decode($value);
		$shapes = array_keys($structure['shapes']);
		
		$retrieve = $this->retrieve($id);
		$vte_metadata = Zend_Json::decode($retrieve['vte_metadata']);
		if (!empty($vte_metadata)) {
			foreach($vte_metadata as $elementid => $info) {
				if (!in_array($elementid,$shapes)) {
					unset($vte_metadata[$elementid]);
				}
			}
			$columns[] = 'vte_metadata = ?';
			$values[] = Zend_Json::encode($vte_metadata);
		}
		$helper = Zend_Json::decode($retrieve['helper']);
		if (!empty($helper)) {
			foreach($helper as $elementid => $info) {
				if (!in_array($elementid,$shapes)) {
					unset($helper[$elementid]);
				}
			}
			$columns[] = 'helper = ?';
			$values[] = Zend_Json::encode($helper);
		}
		$adb->pquery("update {$this->table_name} set ".implode(',',$columns)." where id = ?", array($values,$id));
		
		// clean _processmaker_metarec
		$result = $adb->pquery("select id, elementid from {$table_prefix}_processmaker_metarec where processid = ?", array($id));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				if (!in_array($row['elementid'],$shapes)) {
					// TODO delete from other tables (ex. _processmaker_rec)
					$adb->pquery("delete from {$table_prefix}_processmaker_metarec where id = ? and processid = ?", array($row['id'],$id));
				}
			}
		}
		// clean _process_dynaform_meta
		$result = $adb->pquery("select id, elementid from {$table_prefix}_process_dynaform_meta where processid = ?", array($id));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				if (!in_array($row['elementid'],$shapes)) {
					// TODO delete from other tables (ex. _process_dynaform)
					$adb->pquery("delete from {$table_prefix}_process_dynaform_meta where id = ? and processid = ?", array($row['id'],$id));
				}
			}
		}
	}
	function getStructure($id) {
		global $adb;
		$result = $adb->pquery("select structure from {$this->table_name} where id = ?", array($id));
		if ($result && $adb->num_rows($result) > 0) {
			return Zend_Json::decode($adb->query_result_no_html($result,0,'structure'));
		}
		return false;
	}
	function getStructureElementInfo($id,$elementId,$type) {
		$structure = $this->getStructure($id);
		return array_merge(array('id'=>$elementId),$structure[$type][$elementId]);
	}
	function getIncoming($id,$shapeid) {
		$return = array();
		$structure = $this->getStructure($id);
		$outgoing = $structure['tree'][$shapeid]['incoming'];
		if (!empty($outgoing)) {
			foreach($outgoing as $connection => $shape) {
				$return[] = array('connection'=>$this->getStructureElementInfo($id,$connection,'connections'),'shape'=>$this->getStructureElementInfo($id,$shape,'shapes'));
			}
		}
		return $return;
	}
	function getOutgoing($id,$shapeid) {
		$return = array();
		$structure = $this->getStructure($id);
		$outgoing = $structure['tree'][$shapeid]['outgoing'];
		if (!empty($outgoing)) {
			foreach($outgoing as $connection => $shape) {
				$connection_info = $this->getStructureElementInfo($id,$connection,'connections');
				if ($connection_info['type'] == 'SequenceFlow') {	// manage only this type
					$return[] = array('connection'=>$connection_info,'shape'=>$this->getStructureElementInfo($id,$shape,'shapes'));
				}
			}
		}
		return $return;
	}
	//crmv@103534
	function getParallelFlowSons($id,$gateway,$elementid,&$elementsons,&$conditionssons) {
		$structure = $this->getStructureElementInfo($id,$elementid,'shapes');
		if ($elementid == $gateway || $this->isEndTask($structure['type']) || $structure['type'] == 'ParallelGateway' || ($this->getEngineType($structure) == 'Gateway' && $elementid == $this->getClosingParallelGateway($id,$gateway))) {
			if ($structure['type'] == 'ParallelGateway') {	// add also the next Parallel Gateway before end the recursion
				$elementsons[] = $elementid;
			}
			return;
		} else {
			if ($this->getEngineType($structure) == 'Action') {
				$elementsons[] = $elementid;
			}
			if ($this->getEngineType($structure) == 'Condition') {
				$conditionssons[] = $elementid;
			}
			$outgoings = $this->getOutgoing($id,$elementid);
			if (!empty($outgoings)) {
				foreach($outgoings as $outgoing) {
					$this->getParallelFlowSons($id,$gateway,$outgoing['shape']['id'],$elementsons,$conditionssons);
				}
			}
		}
	}
	function getClosingParallelGateway($id,$gateway) {
		$metadata = $this->getMetadata($id,$gateway);
		return $metadata['closing_gateway'];
	}
	function searchParentParallelFlow($running_process,$gateway) {
		global $adb, $table_prefix;
		$result = $adb->pquery("select processesid, elementsons from {$table_prefix}_process_gateway_conn where running_process = ? and elementid <> ?", array($running_process,$gateway));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result,-1,false)) {
				$elementsons = Zend_Json::decode($row['elementsons']);
				if (in_array($gateway,$elementsons)) {
					// the gateway is son of this flow
					return $row['processesid'];
				}
			}
		}
		return false;
	}
	//crmv@103534e
	
	function actionEdit($id,$elementid,$action_type,$action_id='', $actionOptions = array()){
		global $mod_strings, $app_strings, $theme;
		
		$actionType = $this->getActionTypes($action_type);
		require_once($actionType['php_file']);
		$action = new $actionType['class']($actionOptions);
		
		$smarty = new vtigerCRM_Smarty();
		
		$smarty->assign('THEME',$theme);
		$smarty->assign('APP',$app_strings);
		$smarty->assign('MOD',$mod_strings);
		$smarty->assign("PAGE_TITLE", $mod_strings['LBL_PM_ACTION'].": ".$mod_strings['LBL_PM_ACTION_'.$action_type]);
		$smarty->assign("HEADER_Z_INDEX", 1);
		$buttons = '
			<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
				<tr>
					<td class="small" align="right">
						<input type="button" class="crmButton small save" value="'.$app_strings['LBL_SAVE_BUTTON_LABEL'].'" onclick="ActionTaskScript.saveaction(\''.$id.'\',\''.$elementid.'\',\''.$action_type.'\',\''.$action_id.'\',jQuery(\'#action_title\').val());">
						<input type="button" class="crmbutton small cancel" value="'.$app_strings['LBL_CANCEL_BUTTON_LABEL'].'" onclick="history.back(-1);">
					</td>
				</tr>
			</table>';
		$smarty->assign("BUTTON_LIST", $buttons);
		
		$smarty->assign('ID',$id);
		$smarty->assign('ELEMENTID',$elementid);
		$smarty->assign('ACTIONTYPE',$action_type);
		$smarty->assign('ACTIONID',$action_id);
		$smarty->assign('TEMPLATE',$actionType['tpl_file']);
		$action->edit($smarty,$id,$elementid,$this->retrieve($id),$action_type,$action_id);
		$smarty->display("Settings/ProcessMaker/Metadata/ActionTaskEdit.tpl");
		
		include('themes/SmallFooter.php');
	}
	//crmv@108227
	function actionSave($request){
		global $adb, $table_prefix, $currentModule;
		$id = vtlib_purify($request['id']);
		$elementid = vtlib_purify($request['elementid']);
		$action_id = vtlib_purify($request['action_id']);
		$action_type = vtlib_purify($request['meta_action']['action_type']);
		if ($action_type == 'Cycle') $action_type = vtlib_purify($request['meta_action']['cycle_action']);

		if (in_array($action_type,array('Create','InsertTableRow'))) {
			$currentModule_bkp = $currentModule;
			if ($action_type == 'InsertTableRow') {
				$currentModule = $request['meta_action']['form']['module'];
				list($metaid,$fieldname) = explode(':',$request['meta_action']['inserttablerow_field']);
				$module_mode = (stripos($fieldname,'ml') !== false);
			} else {
				$currentModule = $request['meta_action']['form_module'];
				$module_mode = true;
			}
			if (!$module_mode) {
				// insert row in a field table of a dynaform
				require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
				$processDynaFormObj = ProcessDynaForm::getInstance();
				$meta = $processDynaFormObj->getMeta($id, false, $metaid);
				$blocks = $processDynaFormObj->getStructure($id, $meta['elementid']);
				if (!empty($blocks)) {
					foreach($blocks as $blockid => $block) {
						if (!empty($block['fields'])) {
							foreach($block['fields'] as $field) {
								if ($field['fieldname'] == $fieldname) {
									$columns = Zend_Json::decode($field['columns']);
									if (!empty($columns)) {
										$tmp = array();
										foreach($columns as $column) {
											$column_fieldname = $column['fieldname'];
											$field = WebserviceField::fromArray($adb,$column);
											if ($field->getFieldDataType() == 'reference') {
												if ($request['meta_action'][$column_fieldname.'_type'] == 'Other') $request['meta_action'][$column_fieldname] = $request['meta_action']['other_'.$column_fieldname];
											} elseif ($field->getFieldDataType() == 'date') {
												if (strpos($request['meta_action'][$column_fieldname],'$') === false) $request['meta_action'][$column_fieldname] = getValidDBInsertDateValue($request['meta_action'][$column_fieldname]);	//crmv@116011
											}
											//crmv@106856
											if ($request['meta_action'][$column_fieldname] == 'advanced_field_assignment') {
												$request['meta_action']['advanced_field_assignment'][$column_fieldname] = $this->getAdvancedFieldAssignment($column_fieldname);
											}
											//crmv@106856e
											//crmv@113527
											if (isset($request['meta_action']['sdk_params_'.$column_fieldname])) {
												$request['meta_action']['sdk_params'][$column_fieldname] = $request['meta_action']['sdk_params_'.$column_fieldname];
											}
											//crmv@113527e
										}
									}
									break 2;
								}
							}
						}
					}
				}
			} else {
				if ($currentModule == 'Calendar' && $request['meta_action']['activity_mode'] == 'Events') $currentModule = 'Events';
				$tabid = getTabid($currentModule);
				$focus = CRMEntity::getInstance($currentModule);
				$_REQUEST = $request['meta_action']['form'];
				// set correct owner
				if ($_REQUEST['assigntype'] == 'U') $_REQUEST['assigned_user_id'] = $_REQUEST['assigned_user_id'];
				elseif($_REQUEST['assigntype'] == 'T') $_REQUEST['assigned_user_id'] = $_REQUEST['assigned_group_id'];
				elseif($_REQUEST['assigntype'] == 'O') $_REQUEST['assigned_user_id'] = $_REQUEST['other_assigned_user_id'];
				// end
				// save reference fields
				$i=0;
				$result = $adb->pquery("select * from {$table_prefix}_field where tabid=?",array($tabid));
				while($row=$adb->fetchByASsoc($result,-1,false)) {
					$fieldname = $row['fieldname'];
					$field = WebserviceField::fromQueryResult($adb,$result,$i);
					if ($field->getFieldDataType() == 'reference') {
						(in_array($currentModule,array('Calendar','Events')) && $fieldname == 'parent_id') ? $fieldname_type = 'parent_type' : $fieldname_type = $fieldname.'_type';
						if ($_REQUEST[$fieldname_type] == 'Other') $_REQUEST[$fieldname] = $_REQUEST['other_'.$fieldname];
					} elseif ($field->getFieldDataType() == 'date') {
						//crmv@120769
						$_REQUEST[$fieldname] = Zend_Json::encode(array(
							'options'=>$_REQUEST[$fieldname.'_options'],
							'custom'=>getValidDBInsertDateValue($_REQUEST[$fieldname]),	//crmv@116011
							'operator'=>$_REQUEST[$fieldname.'_opt_operator'],
							'num'=>$_REQUEST[$fieldname.'_opt_num'],
							'unit'=>$_REQUEST[$fieldname.'_opt_unit'],
						));
						//crmv@120769e
					}
					if (in_array($currentModule,array('Calendar','Events')) && in_array($fieldname,array('time_start','time_end'))) {
						if ($fieldname == 'time_start') $custom = $_REQUEST['starthr'].':'.$_REQUEST['startmin'].''.$_REQUEST['startfmt'];
						else $custom = $_REQUEST['endhr'].':'.$_REQUEST['endmin'].''.$_REQUEST['endfmt'];
						$_REQUEST[$fieldname] = Zend_Json::encode(array(
							'options'=>$_REQUEST[$fieldname.'_options'],
							'custom'=>$custom,
							'operator'=>$_REQUEST[$fieldname.'_opt_operator'],
							'num'=>$_REQUEST[$fieldname.'_opt_num'],
							'unit'=>$_REQUEST[$fieldname.'_opt_unit'],
						));
					}
					//crmv@106856
					if ($_REQUEST[$fieldname] == 'advanced_field_assignment') {
						$request['meta_action']['advanced_field_assignment'][$fieldname] = $this->getAdvancedFieldAssignment($fieldname);
					}
					//crmv@106856e
					//crmv@113527
					if (isset($_REQUEST['sdk_params_'.$fieldname])) {
						$request['meta_action']['sdk_params'][$fieldname] = $request['meta_action']['form']['sdk_params_'.$fieldname];
					}
					//crmv@113527e
					$i++;
				}
				// end
				setObjectValuesFromRequest($focus);
				$request['meta_action']['form'] = $focus->column_fields;
			}
			$currentModule = $currentModule_bkp;
		} elseif ($action_type == 'Update') {
			$currentModule_bkp = $currentModule; $currentModule = $request['meta_action']['form']['module'];
			if ($currentModule == 'Calendar' && $request['meta_action']['form']['activity_mode'] == 'Events') $currentModule = 'Events';
			$tabid = getTabid($currentModule);
			require_once('include/utils/MassEditUtils.php');
			$massEditUtils = MassEditUtils::getInstance();
			// set correct owner
			if ($request['meta_action']['form']['assigntype'] == 'U') $request['meta_action']['form']['assigned_user_id'] = $request['meta_action']['form']['assigned_user_id'];
			elseif($request['meta_action']['form']['assigntype'] == 'T') $request['meta_action']['form']['assigned_user_id'] = $request['meta_action']['form']['assigned_group_id'];
			elseif($request['meta_action']['form']['assigntype'] == 'O') $request['meta_action']['form']['assigned_user_id'] = $request['meta_action']['form']['other_assigned_user_id'];
			$request['meta_action']['form']['assigntype'] = 'U';
			// end
			// save reference fields
			$i=0;
			$result = $adb->pquery("select * from {$table_prefix}_field where tabid=?",array($tabid));
			while($row=$adb->fetchByASsoc($result,-1,false)) {
				$fieldname = $row['fieldname'];
				$field = WebserviceField::fromQueryResult($adb,$result,$i);
				if ($field->getFieldDataType() == 'reference') {
					(in_array($currentModule,array('Calendar','Events')) && $fieldname == 'parent_id') ? $fieldname_type = 'parent_type' : $fieldname_type = $fieldname.'_type';
					if ($request['meta_action']['form'][$fieldname_type] == 'Other') $request['meta_action']['form'][$fieldname] = $request['meta_action']['form']['other_'.$fieldname];
				} elseif ($field->getFieldDataType() == 'date') {
					//crmv@120769
					$request['meta_action']['form'][$fieldname] = Zend_Json::encode(array(
						'options'=>$request['meta_action']['form'][$fieldname.'_options'],
						'custom'=>getValidDBInsertDateValue($request['meta_action']['form'][$fieldname]),
						'operator'=>$request['meta_action']['form'][$fieldname.'_opt_operator'],
						'num'=>$request['meta_action']['form'][$fieldname.'_opt_num'],
						'unit'=>$request['meta_action']['form'][$fieldname.'_opt_unit'],
					));
					//crmv@120769e
				}
				//crmv@106856
				if ($request['meta_action']['form'][$fieldname] == 'advanced_field_assignment') {
					$request['meta_action']['advanced_field_assignment'][$fieldname] = $this->getAdvancedFieldAssignment($fieldname);
				}
				//crmv@106856e
				//crmv@113527
				if (isset($request['meta_action']['form']['sdk_params_'.$fieldname])) {
					$request['meta_action']['sdk_params'][$fieldname] = $request['meta_action']['form']['sdk_params_'.$fieldname];
				}
				//crmv@113527e
				$i++;
			}
			// end
			$form = $massEditUtils->extractValuesFromRequest($currentModule, $request['meta_action']['form']);
			if (in_array($currentModule,array('Calendar','Events'))) {
				$time_fields = array('time_start','time_end');
				foreach($time_fields as $fieldname) {
					if (array_key_exists($fieldname,$form)) {
						if ($fieldname == 'time_start') $custom = $request['meta_action']['form']['starthr'].':'.$request['meta_action']['form']['startmin'].''.$request['meta_action']['form']['startfmt'];
						else $custom = $request['meta_action']['form']['endhr'].':'.$request['meta_action']['form']['endmin'].''.$request['meta_action']['form']['endfmt'];
						$form[$fieldname] = Zend_Json::encode(array(
							'options'=>$request['meta_action']['form'][$fieldname.'_options'],
							'custom'=>$custom,
							'operator'=>$request['meta_action']['form'][$fieldname.'_opt_operator'],
							'num'=>$request['meta_action']['form'][$fieldname.'_opt_num'],
							'unit'=>$request['meta_action']['form'][$fieldname.'_opt_unit'],
						));
					}
				}
			}
			$request['meta_action']['form'] = $form;
			$currentModule = $currentModule_bkp;
		} elseif ($action_type == 'SDK') {
			$sdkActions = SDK::getProcessMakerActions();
			$request['meta_action']['action_title'] = $sdkActions[$request['meta_action']['function']]['label'];
		}

		$retrieve = $this->retrieve($id);
		$element_metadata = Zend_Json::decode($retrieve['vte_metadata']);
		if ($action_id != '')
			$element_metadata[$elementid]['actions'][$action_id] = $request['meta_action'];
		else
			$element_metadata[$elementid]['actions'][] = $request['meta_action'];
		$retrieve['vte_metadata'] = Zend_Json::encode($element_metadata);
		$this->edit($id,$retrieve);
	}
	//crmv@108227e
	function actionDelete($id,$elementid,$action_id){
		$retrieve = $this->retrieve($id);
		$element_metadata = Zend_Json::decode($retrieve['vte_metadata']);
		unset($element_metadata[$elementid]['actions'][$action_id]);
		$retrieve['vte_metadata'] = Zend_Json::encode($element_metadata);
		$this->edit($id,$retrieve);
		
		$PMUtils = ProcessMakerUtils::getInstance();
		$record_involved = $this->getRecordsInvolved($id,false,$elementid,$action_id);
		$metaid = $record_involved[0]['seq'];
		if ($metaid !== '') {
			global $adb, $table_prefix;
			$adb->pquery("delete from {$table_prefix}_processmaker_metarec where id = ? and processid = ?", array($metaid,$id));
			$adb->pquery("delete from {$table_prefix}_processmaker_rec where id = ?", array($metaid));
		}
	}
	
	//crmv@108227 crmv@106857
	function getModuleList($mode='',$selectedvalue='') {
		global $adb, $table_prefix;
		$modules_not_supported = $this->modules_not_supported;
		// skip also light modules
		require_once('include/utils/ModLightUtils.php'); 
		$MLUtils = ModLightUtils::getInstance();
		$light_modules = $MLUtils->getModuleList();
		if (!empty($light_modules)) $modules_not_supported = array_merge($modules_not_supported,$light_modules);

		$sql="select distinct {$table_prefix}_field.tabid, name
				from {$table_prefix}_field 
				inner join {$table_prefix}_tab on {$table_prefix}_field.tabid={$table_prefix}_tab.tabid 
				where {$table_prefix}_tab.name not in (".generateQuestionMarks($modules_not_supported).") and {$table_prefix}_tab.isentitytype=1 and {$table_prefix}_tab.presence in (0,2)";
		$it = new SqlResultIterator($adb, $adb->pquery($sql,array($modules_not_supported)));
		if ($mode == 'picklist') {
			$modules = array(''=>array(gettranslatedString('LBL_NONE'),''));
			foreach($it as $row){
				($selectedvalue == $row->name) ? $selected = 'selected' : $selected = '';
				$modules[$row->name] = array(getSingleModuleName($row->name),$selected);
			}
			asort($modules);
		} else {
			$modules = array();
			foreach($it as $row){
				$modules[] = $row->name;
			}
		}
		return $modules;
	}
	//crmv@108227e crmv@106857e
	
	function translateConditionFieldname($fieldname, $module, $processmakerid='', $metaid='') {
		global $adb, $table_prefix;
		$related_module = false;
		if (strpos($fieldname,' : ') !== false && strpos($fieldname,' (') !== false && strpos($fieldname,') ') !== false) {
			list($columnname,$tmp) = explode(' : ',$fieldname);
			list($module,$fieldname) = explode(') ',$tmp);
			$module = ltrim($module,'(');
			$related_module = true;
		}
		if ($module == 'DynaForm') {
			require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
			$processDynaFormObj = ProcessDynaForm::getInstance();
			$blocks = $processDynaFormObj->getStructure($processmakerid, false, $metaid);
			if (!empty($blocks)) {
				foreach($blocks as $block) {
					foreach($block['fields'] as $field) {
						if ($field['fieldname'] == $fieldname) {
							$trans_field = $field['label'];
							if ($related_module) $trans_field .= ' ('.getSingleModuleName($module).')';
							break(2);
						}
					}
				}
			}
		} else {
			$moduleInstance = Vtiger_Module::getInstance($module);
			$result = $adb->pquery("select fieldlabel from {$table_prefix}_field where tabid = ? and fieldname = ?", array($moduleInstance->id,$fieldname));
			if ($result && $adb->num_rows($result) > 0) {
				$trans_field = getTranslatedString($adb->query_result($result,0,'fieldlabel'),$module);
				if ($related_module) $trans_field .= ' ('.getSingleModuleName($module).')';
			}
		}
		return $trans_field;
	}
	function translateConditionOperation($value) {
		global $adb, $current_language;
		$labels = array(
			'is'=>'EQUALS',
			'equal to'=>'EQUALS',
			'is not'=>'NOT_EQUALS_TO',
			'does not equal'=>'NOT_EQUALS_TO',
			'has changed'=>'HAS_CHANGED',	//crmv@56962
			'contains'=>'CONTAINS',
			'does not contain'=>'DOES_NOT_CONTAINS',
			'starts with'=>'STARTS_WITH',
			'ends with'=>'ENDS_WITH',	//crmv@56962
			'less than'=>'LESS_THAN',
			'greater than'=>'GREATER_THAN',
			'less than or equal to'=>'LESS_OR_EQUALS',
			'greater than or equal to'=>'GREATER_OR_EQUALS',
		);
		static $trans_labels = array();
		if (empty($trans_labels)) {
			$res = $adb->pquery("select module, label, trans_label from sdk_language where language = ? and module = ? and label in (".generateQuestionMarks($labels).") order by module", array($current_language,'ALERT_ARR',$labels));
			if ($res && $adb->num_rows($res) > 0) {
				while($row=$adb->fetchByAssoc($res,-1,false)) {
					$trans_labels[$row['label']] = $row['trans_label'];
				}
			}
		}
		return $trans_labels[$labels[$value]];
	}
	function translateConditionValue($fieldname, $module, $value) {
		global $adb, $table_prefix;
		$moduleInstance = Vtiger_Module::getInstance($module);
		$result = $adb->pquery("select * from {$table_prefix}_field where tabid = ? and fieldname = ?", array($moduleInstance->id,$fieldname));
		$wsField = WebserviceField::fromQueryResult($adb,$result,0);
		if ($wsField->getFieldDataType() == 'picklist') {
			$value = getTranslatedString($value,$module);
		}
		if ($value == '') $value = getTranslatedString('LBL_EMPTY_LABEL','Charts');
		return $value;
	}
	function translateConditionGlue($glue) {
		static $trans_glue = array();
		if (empty($trans_glue)) {
			$trans_glue = array(
				'and'=>getTranslatedString('LBL_AND'),
				'or'=>getTranslatedString('LBL_OR'),
			);
		}
		return $trans_glue[$glue];
	}
	function translateConditions($id,$elementid,$metadata='') {
		if (empty($metadata)) $metadata = $this->getMetadata($id,$elementid);
		$conditions = Zend_Json::decode($metadata['conditions']);
		if (strpos($metadata['moduleName'],':') !== false) {
			list($entityId,$module) = explode(':',$metadata['moduleName']);
		} else {
			$module = $metadata['moduleName'];
		}
		$c = array();
		if (!empty($conditions)) {
			$i=0;
			foreach($conditions as $condition) {
				$sub_conditions = $condition['conditions'];
				$label = '';
				foreach($sub_conditions as $ii => $sub_condition) {
					$label .= $this->translateConditionFieldname($sub_condition['fieldname'],$module,$id,$entityId).' '.$this->translateConditionOperation($sub_condition['operation']).' <i>'.$this->translateConditionValue($sub_condition['fieldname'],$module,$sub_condition['value']).'</i>';
					if ($ii < count($sub_conditions)) $label .= ' '.$this->translateConditionGlue($sub_condition['glue']).' ';
				}
				if ($i < count($conditions)-1) {
					if (count($sub_conditions) > 1) $label = "($label)";
					$label .= ' '.$this->translateConditionGlue($condition['glue']);
				}
				$c[] = $label;
				$i++;
			}
		}
		return $c;
	}
	function getGatewayConditions($id,$elementid,$vte_metadata_arr,&$show_required2go_check) {
		$incoming = $this->getIncoming($id,$elementid);
		$groups = array();
		$j = 0;
		$enable_cond_else = true;	//crmv@114116
		foreach($incoming as $inc) {
			$metadata = $this->getMetadata($id,$inc['shape']['id']);
			if ($metadata['execution_condition'] == 'EVERY_TIME') $enable_cond_else = false;	//crmv@114116
			$conditions = Zend_Json::decode($metadata['conditions']);
			if (strpos($metadata['moduleName'],':') !== false) {
				list($entityId,$module) = explode(':',$metadata['moduleName']);
			} else {
				$module = $metadata['moduleName'];
			}
			$c = array();
			if (!empty($conditions)) {
				$all_or = true;
				for($i=0;$i<count($conditions)-1;$i++) {
					if ($conditions[$i]['glue'] != 'or') $all_or = false;
				}
				if (!$all_or) {
					$c[] = array('label'=>getTranslatedString('LBL_EXCLUSIVEGATEWAY_SUCCESS','Settings'),'cond'=>'cond_all','elementid'=>$vte_metadata_arr['cond_all']);
				} else {
					$i=0;
					foreach($conditions as $condition) {
						$sub_conditions = $condition['conditions'];
						$label = '';
						foreach($sub_conditions as $ii => $sub_condition) {
							$label .= $this->translateConditionFieldname($sub_condition['fieldname'],$module,$id,$entityId).' '.$this->translateConditionOperation($sub_condition['operation']).' <i>'.$this->translateConditionValue($sub_condition['fieldname'],$module,$sub_condition['value']).'</i>';
							if ($ii < count($sub_conditions)) $label .= ' '.$this->translateConditionGlue($sub_condition['glue']).' ';
						}
						$c[] = array(
							'label'=>$label,
							'cond'=>"cond_{$j}_{$i}",
							'elementid'=>$vte_metadata_arr["cond_{$j}_{$i}"],
							'json_condition'=>Zend_Json::encode($sub_conditions)
						);
						$i++;
					}
				}
			}
			if (!empty($c)) {
				$groups[$j] = array(
					'elementid'=>$inc['shape']['id'],
					'name'=>$inc['shape']['text'],
					'conditions'=>$c,
					//'required2go'=>$vte_metadata_arr["required2go_{$j}"],
				);
			}
			$j++;
		}
		if (!empty($groups) && $enable_cond_else) {	//crmv@114116
			$groups[] = array(
				'name'=>'',
				'conditions'=>array(array('label'=>getTranslatedString('LBL_EXCLUSIVEGATEWAY_OTHER','Settings'),'cond'=>'cond_else','elementid'=>$vte_metadata_arr['cond_else'])),
			);
		}
		if ($j > 1) {
			$show_required2go_check = true;
		}
		return $groups;
	}
	
	//crmv@106857
	function evaluateCondition($entityCache, $id, $conditions, $cycleIndex=null) {
		global $current_user;
		$conditions = Zend_Json::decode($conditions);
		if (empty($conditions)) return true;
		
		$PMUtils = ProcessMakerUtils::getInstance();
		$PMUtils->setDefaultDataFormat();
		$entityData = $entityCache->forId($id);
		$PMUtils->restoreDataFormat();
		$data = $entityData->getData();
		$i = 0;
		$string = "\$result = ";
		foreach($conditions as $condition) {
			$string .= '(';
			$j = 0;
			foreach($condition['conditions'] as $sub_condition) {
				if (strpos($sub_condition['fieldname'],'sdk:') !== false) {
					$sdkTaskConditions = SDK::getProcessMakerTaskConditions();
					list($tmp,$sdkId) = explode(':',$sub_condition['fieldname']);
					if (isset($sdkTaskConditions[$sdkId])) {
						require_once($sdkTaskConditions[$sdkId]['src']);
						$return = call_user_func_array($sdkTaskConditions[$sdkId]['funct'], array($entityData->moduleName,$id,$entityData->data));
						$entityData->data[$sub_condition['fieldname']] = $return;
					}
				}
				if (strpos($sub_condition['fieldname'],'::') !== false) {
					list($tfield, $tcol) = explode('::',$sub_condition['fieldname']);
					if (stripos($tfield,'ml') !== false) {
						if (empty($id)) {
							$replacement = $this->applyTableFieldFunct('', $entityData->data[$tfield], $tfield, $tcol.':'.$sub_condition['tabfieldopt'].':'.$sub_condition['tabfieldseq'], $cycleIndex);
						} else {
							$replacement = $this->replaceTableFieldTag($id, $tfield, $tcol.':'.$sub_condition['tabfieldopt'].':'.$sub_condition['tabfieldseq']);
						}
					} else {
						$replacement = $this->applyTableFieldFunct('dynaform', $entityData->data[$tfield], $tfield, $tcol.':'.$sub_condition['tabfieldopt'].':'.$sub_condition['tabfieldseq'], $cycleIndex);
					}
					require_once("modules/Settings/ProcessMaker/actions/Cycle.php");
					require_once("modules/com_vtiger_workflow/VTJsonCondition.inc");
					$tmpEntityCache = new PMCycleWorkflowEntity($current_user, array($sub_condition['fieldname']=>$replacement));
					$workflow_condition = new VTJsonCondition();
					$string .= ($workflow_condition->checkCondition($tmpEntityCache, $sub_condition) === true) ? 'true' : 'false';
				} else {
					if (in_array($sub_condition['operation'],array('has exactly','has more than','has less than')) && stripos($sub_condition['fieldname'],'ml') !== false && !is_array($data[$sub_condition['fieldname']])) {
						list($wsModId,$recordId) = explode('x',$id);
						$module = getSalesEntityType($recordId);
						static $tableFieldValues = array();
						if (!isset($tableFieldValues[$sub_condition['fieldname']][$recordId])) {
							require_once('include/utils/ModLightUtils.php'); 
							$MLUtils = ModLightUtils::getInstance();
							$columns = $MLUtils->getColumns($module,$sub_condition['fieldname']);
							$tableFieldValues[$sub_condition['fieldname']][$recordId] = array();
							$values = $MLUtils->getValues($module,$recordId,$sub_condition['fieldname'],$columns);
							if (!empty($values)) {
								foreach($values as $tmp) {
									array_push($tableFieldValues[$sub_condition['fieldname']][$recordId],$tmp['row']);
								}
							}
						}
						$entityCache->cache[$id]->data[$sub_condition['fieldname']] = $tableFieldValues[$sub_condition['fieldname']][$recordId];
					}
					require_once('modules/com_vtiger_workflow/VTWorkflowManager.inc');
					$workflow = new Workflow();
					$workflow->test = Zend_Json::encode(array($sub_condition));
					$string .= ($workflow->evaluate($entityCache, $id) === true) ? 'true' : 'false';
				}				
				if ($j < count($condition['conditions'])-1) $string .= ($sub_condition['glue'] == 'and') ? '&&' : '||';
				$j++;
			}
			$string .= ')';
			if ($i < count($conditions)-1) $string .= ($condition['glue'] == 'and') ? '&&' : '||';
			$i++;
		}
		$string .= ";";
		eval($string);
		return $result;
	}
	//crmv@106857e
	
	function getStartingEvents($record, $module='', $processmakerid='', $executionCondition='') {	//crmv@111639
		global $adb, $table_prefix;
		$col = 'start';
		$adb->format_columns($col);
		$query = "select {$table_prefix}_processmaker_metarec.id, {$table_prefix}_processmaker_metarec.processid, {$table_prefix}_processmaker_metarec.elementid
			from {$table_prefix}_processmaker_metarec
			inner join {$table_prefix}_processmaker ON {$table_prefix}_processmaker_metarec.processid = {$table_prefix}_processmaker.id
			where $col = ? and active = ?";
		$params = array(1,1);
		if (!empty($module)) {
			$query .= " and module = ?";
			$params[] = $module;
		}
		if (!empty($processmakerid)) {
			$query .= " and {$table_prefix}_processmaker.id = ?";
			$params[] = $processmakerid;
		}
		$result = $adb->pquery($query,$params);
		$return = array();
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				// check if the process is already started for this record
				if ($record !== false) {
					$check = $this->getRunningProcess($record,$row['id'],$row['processid']);
					if ($check !== false) {
						/*
						//crmv@93990 : se esiste un processo ma e' fermo per popup nascosta
						$result_popup = $adb->pquery("SELECT meta.id AS dynaformmetaid, elementid
							FROM {$table_prefix}_process_dynaform dynaform
							INNER JOIN {$table_prefix}_process_dynaform_meta meta ON meta.id = dynaform.metaid AND meta.processid = ?
							WHERE dynaform.running_process = ? AND done = ?", array($row['processid'],$check,2));
						if ($result_popup && $adb->num_rows($result_popup) > 0) {
							$result_current_dynaform = $adb->pquery("select current_dynaform from {$table_prefix}_running_processes where id = ?", array($check));
							if ($adb->query_result($result_popup,0,'elementid') == $adb->query_result($result_current_dynaform,0,'current_dynaform')) {
								// termino il processo e ne faccio ripartire un altro
								$return[] = array(
									'start'=>true,
									'running_process'=>false,
									'processid'=>$row['processid'],
									'elementid'=>$row['elementid'],
									'metaid'=>$row['id'],
									'metadata'=>$this->getMetadata($row['processid'],$row['elementid']),
									'end_running_process'=>$check,
								);
							}
						}
						//crmv@93990e
						*/
						continue;
					}
				}
				//crmv@111639
				$metadata = $this->getMetadata($row['processid'],$row['elementid']);
				if (empty($executionCondition) || $executionCondition == $metadata['execution_condition']) {
					$return[] = array(
						'start'=>true,
						'running_process'=>false,
						'processid'=>$row['processid'],
						'elementid'=>$row['elementid'],
						'metaid'=>$row['id'],
						'metadata'=>$metadata,
					);
				}
				//crmv@111639e
			}
		}
		return $return;
	}
	
	function getNextEvents($record, $module, $parallel_current_info='', $executionCondition='') {
		global $adb, $table_prefix;
		$col = 'current';
		$adb->format_columns($col);
		$result = $adb->pquery("select
			{$table_prefix}_running_processes.id as running_process, {$table_prefix}_running_processes.processmakerid, {$table_prefix}_running_processes.$col, {$table_prefix}_processmaker_rec.id
  			from {$table_prefix}_running_processes
  			inner join {$table_prefix}_processmaker ON {$table_prefix}_running_processes.processmakerid = {$table_prefix}_processmaker.id
			inner join {$table_prefix}_processmaker_rec on {$table_prefix}_running_processes.id = {$table_prefix}_processmaker_rec.running_process
			inner join {$table_prefix}_processmaker_metarec on {$table_prefix}_processmaker_rec.id = {$table_prefix}_processmaker_metarec.id and {$table_prefix}_running_processes.processmakerid = {$table_prefix}_processmaker_metarec.processid
			where {$table_prefix}_processmaker.active = ? and {$table_prefix}_running_processes.active = ? and crmid = ?",
			array(1,1,$record)
		);
		$return = array();
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$structure = $this->getStructure($row['processmakerid']);
				$current_list = explode('|##|',$row['current']);
				foreach($current_list as $current) {
					if ($this->getEngineType($structure['shapes'][$current]) == 'Condition') {
						// exclude conditions of other modules
						$metadata = $this->getMetadata($row['processmakerid'],$current);
						if (strpos($metadata['moduleName'],':') !== false) {
							list($entityId,$moduleName) = explode(':',$metadata['moduleName']);
						} else {
							$moduleName = $metadata['moduleName'];
						}
						if ($module == $moduleName) {
							if (empty($executionCondition) || $executionCondition == $metadata['execution_condition']) {
								if (empty($parallel_current_info) || ($parallel_current_info['running_process'] == $row['running_process'] && $parallel_current_info['elementid'] == $current)) {
									$return[] = array(
										'running_process'=>$row['running_process'],
										'processid'=>$row['processmakerid'],
										'elementid'=>$current,
										'metaid'=>$row['id'],
										'metadata'=>$metadata
									);
								}
							}
						}
					}
					/* via questo puo' comportare anomalie
					$outgoings = $this->getOutgoing($row['processmakerid'],$current);
					if (!empty($outgoings)) {
						foreach($outgoings as $outgoing) {
							if ($this->getEngineType($outgoing['shape']) == 'Condition') {
								// exclude conditions of other modules
								$metadata = $this->getMetadata($row['processmakerid'],$outgoing['shape']['id']);
								if (strpos($metadata['moduleName'],':') !== false) {
									list($entityId,$moduleName) = explode(':',$metadata['moduleName']);
								} else {
									$moduleName = $metadata['moduleName'];
								}
								if ($module == $moduleName) {
									$return[] = array(
										'running_process'=>$row['running_process'],
										'processid'=>$row['processmakerid'],
										'elementid'=>$outgoing['shape']['id'],
										'metaid'=>$row['id'],
										'metadata'=>$metadata
									);
								}
							}
						}
					}*/
				}
			}
		}
		//crmv@105312
		$processes_ids = array();
		if ($module == 'Processes') {
			$processes_ids[] = $record;
		} else {
			$result = $adb->pquery("SELECT processesid
				FROM {$table_prefix}_processes p
				INNER JOIN {$table_prefix}_crmentity c ON c.crmid = p.processesid
				INNER JOIN {$table_prefix}_processmaker pm ON p.processmaker = pm.id
				INNER JOIN {$table_prefix}_processmaker_rec r ON r.running_process = p.running_process
				WHERE pm.active = ? and c.deleted = ? AND r.crmid = ?", array(1,0,$record));
			if ($result && $adb->num_rows($result) > 0) {
				while($row=$adb->fetchByAssoc($result)) {
					$processes_ids[] = $row['processesid'];
				}
			}
		}
		if (!empty($processes_ids)) {
			require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
			$processDynaFormObj = ProcessDynaForm::getInstance();
			foreach($processes_ids as $processes_id) {
				$result = $adb->pquery("select {$table_prefix}_processes.processmaker, {$table_prefix}_running_processes.$col, {$table_prefix}_processes.running_process
					from {$table_prefix}_processes
					inner join {$table_prefix}_running_processes on {$table_prefix}_running_processes.id = {$table_prefix}_processes.running_process
					inner join {$table_prefix}_processmaker ON {$table_prefix}_running_processes.processmakerid = {$table_prefix}_processmaker.id
					where processesid = ? and {$table_prefix}_processmaker.active = ? and {$table_prefix}_running_processes.active = ?",
					array($processes_id,1,1)
				);
				if ($result && $adb->num_rows($result) > 0) {
					$processmaker = $adb->query_result($result,0,'processmaker');
					$running_process = $adb->query_result($result,0,'running_process');
					$structure = $this->getStructure($processmaker);
					$current_list = explode('|##|',$adb->query_result($result,0,'current'));
					foreach($current_list as $current) {
						if ($this->getEngineType($structure['shapes'][$current]) == 'Condition') {
							$metadata = $this->getMetadata($processmaker,$current);
							list($metaid,$mod) = explode(':',$metadata['moduleName']);
							if ($mod == 'DynaForm') {
								$dynaformvalues = $processDynaFormObj->getValues($running_process, $metaid);
								if (empty($executionCondition) || $executionCondition == $metadata['execution_condition']) {
									if (empty($parallel_current_info) || ($parallel_current_info['running_process'] == $running_process && $parallel_current_info['elementid'] == $current)) {
										$return[] = array(
											'running_process'=>$running_process,
											'processid'=>$processmaker,
											'elementid'=>$current,
											'metaid'=>false,
											'metadata'=>$metadata,
											'dynaformmetaid'=>$metaid,
											'dynaformvalues'=>$dynaformvalues
										);
									}
								}
							}
						}
						/* TODO verificare se questo serve ancora!
						$outgoings = $this->getOutgoing($processmaker,$current);
						if (!empty($outgoings)) {
							foreach($outgoings as $outgoing) {
								if ($this->getEngineType($outgoing['shape']) == 'Condition') {
									$metadata = $this->getMetadata($processmaker,$outgoing['shape']['id']);
									list($metaid,$mod) = explode(':',$metadata['moduleName']);
									if ($mod == 'DynaForm') {
										$dynaformvalues = $processDynaFormObj->getValues($running_process, $metaid);
										$return[] = array(
											'running_process'=>$running_process,
											'processid'=>$processmaker,
											'elementid'=>$outgoing['shape']['id'],
											'metaid'=>false,
											'metadata'=>$metadata,
											'dynaformmetaid'=>$metaid,
											'dynaformvalues'=>$dynaformvalues
										);
									}
								}
							}
						}*/
					}
				}
			}
		}
		//crmv@105312e
		return $return;
	}
	function getOtherEvents($record, $module, $parallel_current_info='') {
		global $adb, $table_prefix;
		$return = array();
		$running_processes = array();
		$result = $adb->pquery("SELECT running_process
			FROM {$table_prefix}_processmaker_rec
			INNER JOIN {$table_prefix}_running_processes ON {$table_prefix}_running_processes.id = {$table_prefix}_processmaker_rec.running_process
			INNER JOIN {$table_prefix}_processmaker ON {$table_prefix}_running_processes.processmakerid = {$table_prefix}_processmaker.id
			WHERE crmid = ? AND {$table_prefix}_processmaker.active = ? AND {$table_prefix}_running_processes.active = ?", array($record,1,1));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$running_processes[] = $row['running_process'];
			}
		}
		if ($module == 'Processes') {
			$result = $adb->pquery("SELECT {$table_prefix}_processes.running_process
				FROM {$table_prefix}_processes
				inner join {$table_prefix}_running_processes on {$table_prefix}_running_processes.id = {$table_prefix}_processes.running_process
				inner join {$table_prefix}_processmaker ON {$table_prefix}_running_processes.processmakerid = {$table_prefix}_processmaker.id
				WHERE processesid = ? and {$table_prefix}_processmaker.active = ? and {$table_prefix}_running_processes.active = ?", array($record,1,1));
			if ($result && $adb->num_rows($result) > 0) {
				$running_processes[] = $adb->query_result($result,0,'running_process');
			}
		}
		foreach($running_processes as $running_process) {
			$result1 = $adb->pquery("SELECT {$table_prefix}_processmaker_rec.crmid, setype
				FROM {$table_prefix}_processmaker_rec
				INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_processmaker_rec.crmid = {$table_prefix}_crmentity.crmid
				WHERE running_process = ? AND {$table_prefix}_processmaker_rec.crmid <> ?", array($running_process,$record));
			if ($result1 && $adb->num_rows($result1) > 0) {
				while($row1=$adb->fetchByAssoc($result1)) {
					//crmv@111639
					$nextEvents = $this->getNextEvents($row1['crmid'], $row1['setype'], $parallel_current_info, 'EVERY_TIME');
					$startingEvents = $this->getStartingEvents($row1['crmid'], $row1['setype'], '', 'EVERY_TIME');
					$events = array_merge($nextEvents,$startingEvents);
					//crmv@111639e
					if (!empty($events)) {
						foreach($events as &$event) {
							if (!empty($event['metaid'])) {
								$crmid = ProcessMakerEngine::getCrmid($event['metaid'],$event['running_process']);
							} elseif (!empty($event['dynaformmetaid'])) {
								require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
								$processDynaFormObj = ProcessDynaForm::getInstance();
								$crmid = $processDynaFormObj->getProcessesId($event['running_process'],$event['dynaformmetaid']);
							}
							if (!empty($crmid)) {
								$moduleName = getSalesEntityType($crmid);
								$webserviceObject = VtigerWebserviceObject::fromName($adb,$moduleName);
								$id = vtws_getId($webserviceObject->getEntityId(),$crmid);
								$event['entity'] = array('id'=>$id,'entity_id'=>$crmid,'moduleName'=>$moduleName);
							}
						}
					}
					$return = array_merge($return,$events);
				}
			}
		}
		return $return;
	}
	function cleanDuplicateEvents(&$events) {
		$new_events = array();
		if (!empty($events)) {
			foreach($events as $e) {
				$check = $this->checkDuplicateEvent($new_events,$e);
				if (!$check) $new_events[] = $e;
			}
		}
		$events = $new_events;
	}
	function checkDuplicateEvent($events,$event) {
		if (!empty($events)) {
			foreach($events as $e) {
				if (
					$e['running_process'] == $event['running_process'] &&
					$e['processid'] == $event['processid'] &&
					$e['elementid'] == $event['elementid'] &&
					$e['metaid'] == $event['metaid'] &&
					$e['dynaformmetaid'] == $event['dynaformmetaid']
				) return true;
			}
		}
		return false;
	}
	
	function getCurrentElementId($running_process,$processid='',$elementid='') {
		global $adb, $table_prefix;
		$current = false;
		$result = $adb->pquery("select current from {$table_prefix}_running_processes where id = ?", array($running_process));
		if ($result && $adb->num_rows($result) > 0) {
			$current = $adb->query_result($result,0,'current');
		}
		// if parallels ways search current using the $element
		if (stripos($current,'|##|') !== false) {
			$currents = explode('|##|',$current);
			if (in_array($elementid,$currents)) {
				$current = $elementid;
			} else {	//TODO forse questo else non serve piu'
				$PMUtils = ProcessMakerUtils::getInstance();
				$incomings = $PMUtils->getIncoming($processid,$elementid);
				if (!empty($incomings)) {
					foreach($incomings as $incoming) {
						if (in_array($incoming['shape']['id'],$currents)) {
							$current = $incoming['shape']['id'];
							break;
						}
					}
				}
			}
			if (stripos($current,'|##|') !== false) {
				$current = false;
			}
		}
		return $current;
	}
	
	function getRunningProcess($crmid,$metaid,$processid) {
		// il check lo faccio sui processi in corso non su quelli salvati
		$running_process = false;
		$processes = ProcessMakerHandler::$running_processes;
		if (!empty($processes)) {
			foreach($processes as $p) {
				if ($p['new'] && $crmid == $p['record'] && $metaid == $p['metaid'] && $processid == $p['processid']) {
					$running_process = $p['running_process'];
					break;
				}
			}
		}
		/*
		global $adb, $table_prefix;
		$running_process = false;
		$result = $adb->pquery(
			"select running_process from {$table_prefix}_processmaker_rec
			inner join {$table_prefix}_running_processes on {$table_prefix}_processmaker_rec.running_process = {$table_prefix}_running_processes.id
			where {$table_prefix}_processmaker_rec.crmid = ? and {$table_prefix}_processmaker_rec.id = ? and {$table_prefix}_running_processes.processmakerid = ?
			order by running_process desc",	//crmv@93990
			//and {$table_prefix}_running_processes.end = 0 
			array($crmid,$metaid,$processid)
		);
		if ($result && $adb->num_rows($result) > 0) {
			$running_process = $adb->query_result($result,0,'running_process');
		}*/
		return $running_process;
	}
	
	//crmv@105312
	function checkTimerExists($mode,$running_process,$prev_elementid,$elementid,&$occurrence) {
		global $adb, $table_prefix;
		// check occurrence
		$occurrence = 0;
		$result = $adb->pquery("select id from {$table_prefix}_running_processes_logs where running_process = ? and elementid = ?", array($running_process,$prev_elementid));
		if ($result && $adb->num_rows($result) > 0) $occurrence = $adb->num_rows($result)-1;
		$result = $adb->pquery("select id from {$table_prefix}_running_processes_timer where mode = ? and running_process = ? and prev_elementid = ? and elementid = ? and occurrence = ?", array($mode,$running_process,$prev_elementid,$elementid,$occurrence));
		return ($result && $adb->num_rows($result) > 0);
	}
	function createTimer($mode,$timer,$running_process,$prev_elementid,$elementid,$occurrence,$info=array()) {
		global $adb, $table_prefix;
		(empty($info)) ? $info = null : $info = Zend_Json::encode($info);
		$adb->pquery("insert into {$table_prefix}_running_processes_timer(id,mode,timer,running_process,prev_elementid,elementid,occurrence,info) values(?,?,?,?,?,?,?,?)",
			array($adb->getUniqueID("{$table_prefix}_running_processes_timer"),$mode,$timer,$running_process,$prev_elementid,$elementid,$occurrence,$info)
		);
	}
	//crmv@105312e
	function deleteTimer($mode,$running_process,$prev_elementid='',$elementid='') {
		global $adb, $table_prefix;
		if (!is_array($running_process)) $running_process = array($running_process);
		$query = "delete from {$table_prefix}_running_processes_timer where mode = ? and running_process in (".generateQuestionMarks($running_process).")";
		$params = array($mode,$running_process);
		if (!empty($prev_elementid)) {
			$query .= " and prev_elementid = ?";
			$params[] = $prev_elementid;
		}
		if (!empty($elementid)) {
			$query .= " and elementid = ?";
			$params[] = $elementid;			
		}
		$adb->pquery($query,$params);
	}
	function includeCronDependencies() {
		require_once 'modules/Settings/ProcessMaker/thirdparty/cron-expression/src/Cron/FieldInterface.php';
		require_once 'modules/Settings/ProcessMaker/thirdparty/cron-expression/src/Cron/AbstractField.php';
		require_once 'modules/Settings/ProcessMaker/thirdparty/cron-expression/src/Cron/CronExpression.php';
		require_once 'modules/Settings/ProcessMaker/thirdparty/cron-expression/src/Cron/DayOfMonthField.php';
		require_once 'modules/Settings/ProcessMaker/thirdparty/cron-expression/src/Cron/DayOfWeekField.php';
		require_once 'modules/Settings/ProcessMaker/thirdparty/cron-expression/src/Cron/FieldFactory.php';
		require_once 'modules/Settings/ProcessMaker/thirdparty/cron-expression/src/Cron/HoursField.php';
		require_once 'modules/Settings/ProcessMaker/thirdparty/cron-expression/src/Cron/MinutesField.php';
		require_once 'modules/Settings/ProcessMaker/thirdparty/cron-expression/src/Cron/MonthField.php';
		require_once 'modules/Settings/ProcessMaker/thirdparty/cron-expression/src/Cron/YearField.php';
	}
	function previewTimerStart($vte_metadata) {
		$return = array();
		$date_start = getValidDBInsertDateValue($vte_metadata['date_start']).' '.$vte_metadata['starthr'].':'.$vte_metadata['startmin'];
		($vte_metadata['date_end_mass_edit_check'] == 'on') ? $date_end = getValidDBInsertDateValue($vte_metadata['date_end']).' '.$vte_metadata['endhr'].':'.$vte_metadata['endmin'] : $date_end = false;
		$return = $this->getTimerRecurrences($date_start,$date_end,($vte_metadata['recurrence'] == 'on'),$vte_metadata['cron_value'],5);
		if (!empty($return)) {
			foreach($return as &$date) {
				$date = getDisplayDate($date);
			}
		}
		return $return;
	}
	function getTimerRecurrences($date_start,$date_end=false,$recurrence=false,$cron_string='',$iterations=1) {
		$return = array();
		$i=0;
		if (!$recurrence) {
			$return[] = $date_start;
		} elseif(!empty($cron_string)) {
			$this->includeCronDependencies();
			$cron = Cron\CronExpression::factory($cron_string);
			$runDates = $cron->getMultipleRunDates($iterations*2, $date_start, false, true);
			if (!empty($runDates)) {
				foreach($runDates as $runDate) {
					$runDate = $runDate->format('Y-m-d H:i:s');
					if ($date_end === false || strtotime($runDate) <= strtotime($date_end)) {
						$return[] = $runDate;
						$i++;
						if ($iterations == $i) break;
					}
				}
			}
		}
		return $return;
	}
	function isTimerProcess($id,&$shapeid) {
		$structure = $this->getStructure($id);
		foreach($structure['shapes'] as $shapeid => $shape) {
			if ($shape['type'] == 'StartEvent') {
				return ($shape['subType'] == 'TimerEventDefinition');
			}
		}
		return false;
	}
	function isChangedTimerCondition($vte_metadata_new,$vte_metadata) {
		if (empty($vte_metadata_new) && empty($vte_metadata)) return false;
		else {
			foreach($vte_metadata_new as $k => $v) {
				if ($v != $vte_metadata[$k]) return true;
			}
		}
		return false;
	}
	
	function getElementTitle($structure) {
		$text = $structure['text'];
		$subType = $this->formatType($structure['subType']);
		$cancelActivity = $structure['cancelActivity'];
		
		$title = $this->formatType($structure['type'],true);
		if (!empty($subType)) {
			$title .= "($subType";
			if (isset($cancelActivity)) ($cancelActivity) ? $title .= ': Interrupting' : $title .= ': Non-Interrupting';
			$title .= ")";
		}
		if (!empty($text)) $title .= ': '.trim($text);
		
		return $title;
	}
	
	//crmv@100495
	function showRunProcessesButton($module, $record='') {
		return false;
	}
	//crmv@100495e
	
	//crmv@100591
	function getElementsActors($processid,$email_fields=false) {
		$actors = array();
		if ($email_fields) {
			global $adb, $table_prefix;
			$fieldnames = array();
			$result = $adb->pquery("SELECT fieldname, fieldlabel FROM {$table_prefix}_field LEFT JOIN {$table_prefix}_ws_fieldtype ON {$table_prefix}_field.uitype = {$table_prefix}_ws_fieldtype.uitype WHERE tabid = ? AND ({$table_prefix}_field.uitype = ? OR fieldtype = ?)", array(29,104,'email'));
			if ($result && $adb->num_rows($result) > 0) {
				while($row=$adb->fetchByASsoc($result)) {
					$fieldnames[$row['fieldname']] = $row['fieldlabel'];
				}
			}
		}
		$structure = $this->getStructure($processid);
		if (!empty($structure['shapes'])) {
			foreach($structure['shapes'] as $elementid => $structure) {
				if ($this->getEngineType($structure) == 'Condition') {
					if ($email_fields) {
						foreach($fieldnames as $fieldname => $fieldlabel) {
							$actors['$ACTOR-'.$elementid.'-'.$fieldname] = $this->getElementTitle($structure).' - '.getTranslatedString($fieldlabel,'Users');
						}
					} else {
						$actors['$ACTOR-'.$elementid] = $this->getElementTitle($structure);
					}
				}
			}
		}
		return $actors;
	}
	function getActor($running_process, $elementid, $fieldname='') {
		global $adb, $table_prefix;
		$result = $adb->limitpQuery("SELECT userid FROM {$table_prefix}_running_processes_logs WHERE running_process = ? AND prev_elementid = ? ORDER BY logtime DESC", 0, 1, array($running_process, $elementid));
		if ($result && $adb->num_rows($result) > 0) {
			if (!empty($fieldname)) {
				$user = CRMEntity::getInstance('Users');
				$user->retrieveCurrentUserInfoFromFile($adb->query_result($result,0,'userid'));
				return $user->column_fields[$fieldname];
			} else {
				return $adb->query_result($result,0,'userid');
			}
		}
		return false;
	}
	function getProcessActors($running_process) {
		global $adb, $table_prefix;
		$actors = array();
		$result = $adb->pquery("SELECT DISTINCT userid FROM {$table_prefix}_running_processes_logs WHERE running_process = ?", array($running_process));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$actors[] = $row['userid'];
			}
		}
		return $actors;
	}
	//crmv@100591e
	
	//crmv@103450
	function getElementsExecutedByActors($processid, $running_process) {
		global $adb, $table_prefix;
		$structure = $this->getStructure($processid);
		$return = array();
		$result = $adb->pquery("SELECT userid, prev_elementid FROM {$table_prefix}_running_processes_logs WHERE running_process = ? ORDER BY id", array($running_process));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				if (empty($return[$row['userid']]) || !in_array($row['prev_elementid'],$return[$row['userid']])) {
					if ($structure['shapes'][$row['prev_elementid']]['type'] == 'Task')
						$return[$row['userid']][] = $row['prev_elementid'];
				}
			}
		}
		return $return;
	}
	//crmv@103450e
	
	//crmv@100731
	function getTranslatedProcessResource($processid,$value) {
		global $adb, $table_prefix;
		if (is_numeric($value)) {
			$ownerType = getOwnerType($value);
			if ($ownerType == 'Users') {
				global $showfullusername;
				$name = getUserName($value,$showfullusername);
			} else {
				$tmp = getGroupName($value);
				$name = $tmp[0];
			}
			$value = $name;
		} else {
			if (strpos($value,':') == 3) {	// old mode
				list($meta_processid,$metaid,$module,$fieldname) = explode(':',$value);
				$moduleInstance = Vtiger_Module::getInstance($module);
				$result = $adb->pquery("select fieldlabel from {$table_prefix}_field where tabid = ? and fieldname = ?", array($moduleInstance->id,$fieldname));
				$fieldlabel = getTranslatedString($adb->query_result($result,0,'fieldlabel'),$module);
				
				$structure = $this->getStructure($processid);
				$value = $fieldlabel.' '.getTranslatedString('LBL_OF','Settings').' '.$this->getRecordsInvolvedLabel($processid,$metaid);
			} else {
				if (stripos($value,'$ACTOR-') !== false) {
					$structure = $this->getStructure($processid);
					list($actor,$elementid) = explode('$ACTOR-',$value);
					$value = getTranslatedString('LBL_PM_PARTECIPANT_OF','Settings').' '.$this->getElementTitle($structure['shapes'][$elementid]);
				} elseif (stripos($value,'$sdk:') !== false) {
					$sdkFieldConditions = SDK::getProcessMakerFieldActions();
					$tmp_sdk_function = str_replace('$sdk:','',$value);
					$funct = substr($tmp_sdk_function,0,strpos($tmp_sdk_function,'('));
					if (isset($sdkFieldConditions[$funct])) {
						$value = getTranslatedString('LBL_PM_SDK_CUSTOM_FUNCTION','Settings').': '.$sdkFieldConditions[$funct]['label'];
					}
				} elseif (stripos($value,'$DF') !== false) {
					$tmp = str_replace('$DF','',$value);
					list($dynaform_metaid,$dynaform_fieldname) = explode('-',$tmp);
					if (strpos($dynaform_metaid,':') !== false) {
						list($processid,$dynaform_metaid) = explode(':',$dynaform_metaid);
					}
					require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
					$processDynaFormObj = ProcessDynaForm::getInstance();
					$options = $processDynaFormObj->getOptions($processid);
					$dflabel = $options["$dynaform_metaid:DynaForm"][0];
					$fieldOptions = $processDynaFormObj->getFieldsOptions($processid);
					$fieldlabel = $fieldOptions['all'][$dflabel][$value];
					$value = $fieldlabel.' '.getTranslatedString('LBL_OF','Settings').' '.$dflabel;
				} else {
					$tmp = str_replace('$','',$value);
					list($metaid,$fieldname) = explode('-',$tmp);
					if (strpos($metaid,':') !== false) {
						list($processid,$metaid) = explode(':',$metaid);
					}
					$records = $this->getRecordsInvolved($processid);
					foreach($records as $r) {
						if ($r['seq'] == $metaid) {
							$moduleInstance = Vtiger_Module::getInstance($r['module']);
							$result = $adb->pquery("select fieldlabel from {$table_prefix}_field where tabid = ? and fieldname = ?", array($moduleInstance->id,$fieldname));
							if ($result && $adb->num_rows($result) > 0) {
								$fieldlabel = getTranslatedString($adb->query_result($result,0,'fieldlabel'),$module);
								$value = $fieldlabel.' '.getTranslatedString('LBL_OF','Settings').' '.$r['label'];
							}
							break;
						}
					}
					if (empty($fieldlabel)) $value = $fieldname.' '.getTranslatedString('LBL_OF','Settings').' '.$this->getRecordsInvolvedLabel($processid,$metaid);
				}
			}
		}
		return $value;
	}
	function getAdvancedPermissions($return_mode) {
		global $adb, $table_prefix, $current_user;
		static $ids = array();
		if (empty($ids)) {
			require('user_privileges/requireUserPrivileges.php');
			if (empty($current_user_groups)) {
				$userGroupFocus = new GetUserGroups();
				$userGroupFocus->getAllUserGroups($current_user->id);
				$current_user_groups = $userGroupFocus->user_groups;
			}
			$smowners = array($current_user->id);
			if (!empty($current_user_groups)) $smowners = array_filter(array_merge($smowners, $current_user_groups));
			$result = $adb->pquery("SELECT crmid, read_perm, write_perm FROM {$table_prefix}_process_adv_permissions WHERE resource in (".generateQuestionMarks($smowners).")", array($smowners));
			$tmp = array();
			if ($result && $adb->num_rows($result) > 0) {
				while($row=$adb->fetchByAssoc($result)) {
					$tmp[$row['crmid']][] = array('read_perm'=>$row['read_perm'],'write_perm'=>$row['write_perm']);
				}
				foreach($tmp as $id => $permissions) {
					foreach($permissions as $permission) {
						// if there are more conditions verified (ex. I'm part of groups) select the most restrictive
						if (!isset($ids[$id]) || $permission['read_perm'] < $ids[$id]['read_perm'] || $permission['write_perm'] < $ids[$id]['write_perm']) {
							$ids[$id] = $permission;
						}
					}
				}
			}
		}
		if ($return_mode == 'sql') {
			(empty($ids)) ? $sql = '' : $sql = " OR {$table_prefix}_crmentity.crmid IN (".implode(',',array_keys($ids)).")";
			return $sql;
		} elseif ($return_mode == 'array') {
			return $ids;
		}
	}
	function checkAdvancedPermissions($module,$actionname,$record_id) {
		$return = '';
		$actionid = getActionid($actionname);
		$permissions = $this->getAdvancedPermissions('array');
		if (isset($permissions[$record_id])) {
			if ($actionid == 4) {	// detailview
				($permissions[$record_id]['read_perm'] == 1) ? $return = 'yes' : $return = 'no';
			}
			if ($actionid == 0 || $actionid == 1) {	// save, edit
				($permissions[$record_id]['write_perm'] == 1) ? $return = 'yes' : $return = 'no';
			}
		}
		return $return;
	}
	function getAdvancedPermissionsResources($record) {
		global $adb, $table_prefix;
		$resources = array();
		$result = $adb->pquery("SELECT * FROM {$table_prefix}_process_adv_permissions WHERE crmid = ?", array($record));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				if ($row['read_perm'] == 1 && $row['write_perm'] == 1) {
					$visibility = getTranslatedString('Read/Write','Settings');
				} elseif ($row['read_perm'] == 1) {
					$visibility = getTranslatedString('Read Only ','Settings');
				}
				if ($row['resource_type'] == 'T') {
					$group = getGroupName($row['resource']);
					$img = getGroupAvatar();
					$name = '&nbsp;';
					$fullname = $group[0];
				} else {
					$img = getUserAvatar($row['resource']);
					$name = getUserName($row['resource'],false);
					$fullname = getUserFullName($row['resource']);
				}
				$resources[] = array(
					'id'=>$row['resource'],
					'img'=>$img,
					'name'=>$name,
					'fullname'=>$fullname,
					'read_perm'=>$row['read_perm'],
					'write_perm'=>$row['write_perm'],
					'alt'=>getTranslatedString('LBL_PM_ADVANCED_PERMISSIONS_VISIBILITY','Settings').': '.$visibility,
				);
			}
		}
		return $resources;
	}
	//crmv@100731e
	//crmv@93990
	function getProcessRelatedTo($record, $field) {
		global $adb, $table_prefix, $current_user;
		static $relatedTo = array();
		if (!isset($relatedTo[$record])) {
			$relatedTo[$record]['processesid'] = false;
			require('user_privileges/requireUserPrivileges.php');
			if (empty($current_user_groups)) {
				$userGroupFocus = new GetUserGroups();
				$userGroupFocus->getAllUserGroups($current_user->id);
				$current_user_groups = $userGroupFocus->user_groups;
			}
			$smowners = array($current_user->id);
			if (!empty($current_user_groups)) $smowners = array_filter(array_merge($smowners, $current_user_groups));
			$result = $adb->limitpQuery("SELECT processesid, processmaker, current_dynaform, dynaform_meta.id AS dynaformmetaid
				FROM {$table_prefix}_processes
				INNER JOIN {$table_prefix}_crmentity ON crmid = {$table_prefix}_processes.processesid
				INNER JOIN {$table_prefix}_running_processes ON {$table_prefix}_running_processes.id = {$table_prefix}_processes.running_process
				INNER JOIN {$table_prefix}_process_dynaform_meta dynaform_meta ON dynaform_meta.processid = {$table_prefix}_processes.processmaker AND dynaform_meta.elementid = current_dynaform
				INNER JOIN {$table_prefix}_process_dynaform dynaform ON dynaform.running_process = {$table_prefix}_processes.running_process AND dynaform.metaid = dynaform_meta.id
				WHERE deleted = 0 AND end = 0 AND related_to = ? AND smownerid in (".generateQuestionMarks($smowners).") AND dynaform.done = 0
				ORDER BY createdtime ASC", 0, 1, array($record, $smowners));
			if ($result && $adb->num_rows($result) > 0) {
				$processesid = $adb->query_result($result,0,'processesid');
				$processmakerid = $adb->query_result($result,0,'processmaker');
				$current_dynaform = $adb->query_result($result,0,'current_dynaform');
				
				$data = $this->retrieve($processmakerid);
				$helper = Zend_Json::decode($data['helper']);
				$helper = $helper[$current_dynaform];
				if ($helper['related_to_popup'] == 'on') {
					$relatedTo[$record] = array(
						'processesid'=>$adb->query_result($result,0,'processesid'),
						'dynaformmetaid'=>$adb->query_result($result,0,'dynaformmetaid'),
						'current_dynaform'=>$current_dynaform,
						'related_to_popup'=>$helper['related_to_popup'],
						'related_to_popup_opt'=>$helper['related_to_popup_opt'],
					);
				}
			}
		}
		return $relatedTo[$record][$field];
	}
	//crmv@93990e
	
	//crmv@103450
	function getProcessHelperDefault($processid,$elementid,$type) {
		if ($this->isEndTask($type))
			return 'Ended';
		else {
			$structure = $this->getStructureElementInfo($processid,$elementid,'tree');
			$attachers = $structure['attachers'];
			if (!empty($attachers)) {
				foreach($attachers as $attacher) {
					$attacher_structure = $this->getStructureElementInfo($processid,$attacher,'shapes');
					if ($attacher_structure['subType'] == 'TimerEventDefinition') {
						return 'Waiting';
					}
				}
			}
		}
		return 'Running';
	}
	//crmv@103450e
	
	//crmv@106856
	function addConditionTranslations(&$rules, $processmakerid) {
		if (!empty($rules)) {
			foreach($rules as &$a) {
				$conditions = $a['conditions'];
				list($entityId,$module) = explode(':',$a['meta_record']);
				if ($module == 'DynaForm') {
					require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
					$processDynaFormObj = ProcessDynaForm::getInstance();
					$label = $processDynaFormObj->getLabel($processmakerid,$entityId).' ';
				} else {
					$label = $this->getRecordsInvolvedLabel($processmakerid,$entityId).' ';
				}
				$i=0;
				foreach($conditions as $condition) {
					$sub_conditions = $condition['conditions'];
					if (count($sub_conditions) > 1) $label .= '(';
					foreach($sub_conditions as $ii => $sub_condition) {
						$label .= $this->translateConditionFieldname($sub_condition['fieldname'],$module,$processmakerid,$entityId).' '.$this->translateConditionOperation($sub_condition['operation']).' <i>'.$this->translateConditionValue($sub_condition['fieldname'],$module,$sub_condition['value']).'</i>';
						if ($ii < count($sub_conditions)) $label .= ' '.$this->translateConditionGlue($sub_condition['glue']).' ';
					}
					$i++;
					if (count($sub_conditions) > 1) $label .= ')';
					if ($i < count($conditions)) $label .= ' '.$this->translateConditionGlue($condition['glue']).' ';
				}
				$a['conditions_translate'] = $label;
			}
		}
	}
	function getAdvancedFieldAssignment($fieldname) {
		return $_SESSION['AdvancedFieldAssignment'][$fieldname];
	}
	function setAdvancedFieldAssignment($fieldname, $rules) {
		$_SESSION['AdvancedFieldAssignment'][$fieldname] = $rules;
	}
	function unsetAdvancedFieldAssignment($fieldname='') {
		if (empty($fieldname)) unset($_SESSION['AdvancedFieldAssignment']);
		else unset($_SESSION['AdvancedFieldAssignment'][$fieldname]);
	}
	function getReloadAdvancedFieldAssignment($fieldname) {
		return $_SESSION['AdvancedFieldAssignmentReload'][$fieldname];
	}
	function setReloadAdvancedFieldAssignment($fieldname,$val) {
		$_SESSION['AdvancedFieldAssignmentReload'][$fieldname] = $val;
	}
	function unsetReloadAdvancedFieldAssignment($fieldname='') {
		if (empty($fieldname)) unset($_SESSION['AdvancedFieldAssignmentReload']);
		else unset($_SESSION['AdvancedFieldAssignmentReload'][$fieldname]);
	}
	function saveAdvancedFieldAssignment($fieldname,$action,$info) {
		$rules = $this->getAdvancedFieldAssignment($fieldname);
		if ($action == 'condition') {
			$ruleid = $info[0];
			$meta_record = $info[1];
			$conditions = Zend_Json::decode($info[2]);
			if ($ruleid === '') {
				$rules[] = array(
					'meta_record' => $meta_record,
					'conditions' => $conditions,
				);
			} else {
				$rules[$ruleid]['meta_record'] = $meta_record;
				$rules[$ruleid]['conditions'] = $conditions;
			}
		} elseif ($action == 'values') {
			$form = Zend_Json::decode($info[0]);
			$count = $form['conditions_count'];
			if (!empty($count)) {
				for($i=0;$i<$count;$i++) {
					if (isset($form['assigntype'.$i])) {
						$assigntype = $form['assigntype'.$i];
						if ($assigntype == 'U') $value = $form['assigned_user_id'.$i];
						elseif ($assigntype == 'T') $value = $form['assigned_group_id'.$i];
						elseif ($assigntype == 'O') $value = $form['other_assigned_user_id'.$i];
						$rules[$i]['value'] = $value;
						$rules[$i]['assigntype'] = $assigntype;
						$rules[$i]['sdk_params'] = $form['sdk_params_assigned_user_id'.$i];
					} else {
						$rules[$i]['value'] = $form[$fieldname.$i];
					}
				}
			}
		/*
		} elseif ($action == 'db') {
			$id = $info[0];
			$elementid = $info[1];
			$actionid = $info[2];
			$data = $this->retrieve($id);
			$vte_metadata = Zend_Json::decode($data['vte_metadata']);
			$vte_metadata[$elementid]['actions'][$actionid]['advanced_field_assignment'][$fieldname] = $rules;
			$this->saveMetadata($id,$elementid,Zend_Json::encode($vte_metadata[$elementid]));
		*/
		}
		$this->setAdvancedFieldAssignment($fieldname, $rules);
	}
	function removeAdvancedFieldAssignment($processmakerid,$elementid,$actionid,$fieldname,$ruleid) {
		/*
		$data = $this->retrieve($processmakerid);
		$vte_metadata = Zend_Json::decode($data['vte_metadata']);
		unset($vte_metadata[$elementid]['actions'][$actionid]['advanced_field_assignment'][$fieldname][$ruleid]);
		$vte_metadata[$elementid]['actions'][$actionid]['advanced_field_assignment'][$fieldname] = array_values($vte_metadata[$elementid]['actions'][$actionid]['advanced_field_assignment'][$fieldname]);
		$this->saveMetadata($processmakerid,$elementid,Zend_Json::encode($vte_metadata[$elementid]));
		$this->setAdvancedFieldAssignment($fieldname, $vte_metadata[$elementid]['actions'][$actionid]['advanced_field_assignment'][$fieldname]);
		*/
		$rules = $this->getAdvancedFieldAssignment($fieldname);
		unset($rules[$ruleid]);
		$this->setAdvancedFieldAssignment($fieldname, array_values($rules));
	}
	//crmv@106856e
	
	//crmv@106857
	function getAllTableFields($processmaker) {
		global $adb, $table_prefix;
		$tfields = array();
		$records = $this->getRecordsInvolved($processmaker);
		if (!empty($records)) {
			foreach($records as $r) {
				$bfields = array();
				$key = $r['seq'].':'.$r['module'];
				$moduleInstance = Vtecrm_Module::getInstance($r['module']);
				$result = $adb->pquery("select fieldname, fieldlabel from {$table_prefix}_field where tabid = ? and uitype = ?", array($moduleInstance->id,220));
				if ($result && $adb->num_rows($result) > 0) {
					while($row=$adb->fetchByAssoc($result)) {
						$fkey = $r['seq'].':'.$row['fieldname'];
						$bfields[$fkey] = $row['fieldlabel'];
					}
				}
				if (!empty($bfields)) {
					$tfields[$key] = array('label'=>$r['label'], 'fields'=>$bfields);
				}
			}
		}
		return $tfields;
	}
	function getAllTableFieldsOptions($processmaker, &$return) {
		global $adb, $table_prefix;
		$records = $this->getRecordsInvolved($processmaker);
		if (!empty($records)) {
			foreach($records as $r) {
				$moduleInstance = Vtecrm_Module::getInstance($r['module']);
				$result = $adb->pquery("select fieldname, fieldlabel from {$table_prefix}_field where tabid = ? and uitype = ?", array($moduleInstance->id,220));
				if ($result && $adb->num_rows($result) > 0) {
					while($row=$adb->fetchByAssoc($result)) {
						$this->getTableFieldsOptions($processmaker, $r['seq'], $row['fieldname'], $return, $row['fieldlabel']);
					}
				}
			}
		}
		return $return;
	}
	function getTableFieldsOptions($processmaker, $metaid, $fieldname, &$return, $fieldlabel='') {
		require_once('include/utils/ModLightUtils.php');
		global $adb, $table_prefix;
		$modulelightid = str_replace('ml','',$fieldname);
		if (empty($fieldlabel)) {
			$result = $adb->pquery("select fieldlabel from {$table_prefix}_field where fieldname = ? and uitype = ?", array($fieldname,220));
			if ($result && $adb->num_rows($result) > 0) {
				$fieldlabel = $adb->query_result($result,0,'fieldlabel');
			}
		}
		$MLUtils = ModLightUtils::getInstance();
		$processDynaForm = ProcessDynaForm::getInstance();
		$columns = $MLUtils->getColumns('', $fieldname);
		if (!empty($columns)) {
			$groupLabel = $this->getRecordsInvolvedLabel($processmaker,$metaid)." : $fieldlabel";
			foreach($columns as $column) {
				$value = "\${$metaid}-{$fieldname}::".$column['fieldname'];
				$processDynaForm->categorizeFieldByType($return, $column, $groupLabel, $value);
			}
		}
		return $return;
	}
	
	function replaceTableFieldTag($parent, $tfield, $tcol, $cycleIndex=null) {
		require_once('include/utils/ModLightUtils.php');
		list($wsModule,$parent_id) = explode('x',$parent);
		$parent_module = getSalesEntityType($parent_id);
		
		static $tableFieldValues = array();
		if (!isset($tableFieldValues[$tfield][$parent_id])) {
			$MLUtils = ModLightUtils::getInstance();
			$columns = $MLUtils->getColumns($parent_module,$tfield);
			$tableFieldValues[$tfield][$parent_id] = array();
			$values = $MLUtils->getValues($parent_module,$parent_id,$tfield,$columns);
			if (!empty($values)) {
				foreach($values as $tmp) {
					array_push($tableFieldValues[$tfield][$parent_id],$tmp['row']);
				}
			}
		}
		$values = array_values($tableFieldValues[$tfield][$parent_id]);
		$replace = $this->applyTableFieldFunct('modulelight', $values, $tfield, $tcol, $cycleIndex);
		return $replace;
	}
	function applyTableFieldFunct($mode, $values, $tfield, $tcol, $cycleIndex=null) {
		if(!function_exists("array_column"))
		{
			function array_column($array,$column_name)
			{
				return array_map(function($element) use($column_name){return $element[$column_name];}, $array);
			}
		}
		list($tcol, $funct, $seq) = explode(':',$tcol);
		$replace = '';
		if (!empty($values) && isset($values[0][$tcol])) {
			switch ($funct) {
				case 'sum':
				case 'min':
				case 'max':
				case 'average':
					$col_values = array_column($values,$tcol);
					if ($funct == 'sum') $replace = array_sum($col_values);
					elseif ($funct == 'min') $replace = min($col_values);
					elseif ($funct == 'max') $replace = max($col_values);
					elseif ($funct == 'average') $replace = array_sum($col_values) / count($col_values);
					break;
				case 'last':
					$row = end($values);
					$replace = $row[$tcol];
					break;
				case 'seq':
					$row = array_slice($values, $seq-1, 1);
					$replace = $row[0][$tcol];
					break;
				case 'curr':
				case '':
					if (!is_null($cycleIndex)) {
						if ($mode == 'modulelight') {
							static $retrievedObject = array();
							if (!isset($retrievedObject[$cycleIndex])) {
								$modulelightname = 'ModLight'.str_replace('ml','',$tfield);
								$retrievedObject[$cycleIndex] = CRMEntity::getInstance($modulelightname);
								$retrievedObject[$cycleIndex]->retrieve_entity_info_no_html($cycleIndex,$modulelightname);
							}
							$replace = $retrievedObject[$cycleIndex]->column_fields[$tcol];
						} else {
							$row = $values[$cycleIndex];
							if (is_array($row) && array_key_exists($tcol, $row)) {
								$replace = $row[$tcol];
							}
						}
					}
					break;
			}
		}
		return $replace;
	}
	//crmv@106857e
	
	//crmv@112539
	function getLogElement($running_process,$elementid) {
		global $adb, $table_prefix;
		$return = array();
		$result = $adb->pquery("select info from {$table_prefix}_running_processes_logsi where running_process = ? and elementid = ?", array($running_process, $elementid));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$return[] = Zend_Json::decode($row['info']);
			}
		}
		return $return;
	}
	function deleteRecord($processesid,$elementid,$module,$record) {
		global $adb, $table_prefix;
		
		$focusProcesses = CRMEntity::getInstance('Processes');
		$focusProcesses->retrieve_entity_info_no_html($processesid,'Processes');
		
		$result = $adb->pquery("select id, info from {$table_prefix}_running_processes_logsi where running_process = ? and elementid = ?", array($focusProcesses->column_fields['running_process'],$elementid));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$info = Zend_Json::decode($row['info']);
				if ($info['crmid'] == $record) {
					$adb->pquery("delete from {$table_prefix}_running_processes_logsi where id = ?", array($row['id']));
				}
			}
		}

		$adb->pquery("delete from {$table_prefix}_processmaker_rec where crmid = ?", array($record));
		
		$focus = CRMEntity::getInstance($module);
		$focus->trash($module,$record);
	}
	function rollback($mode,$focusProcesses,$elementid='') {
		global $adb, $table_prefix, $current_user;
		require_once('modules/Settings/ProcessMaker/ProcessMakerEngine.php');
		require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
		require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
		$entityCache = new VTEntityCache($current_user);
		
		$prev_elementid = $this->getCurrentElementId($focusProcesses->column_fields['running_process']);
		$prev_elementid_info = $this->getStructureElementInfo($focusProcesses->column_fields['processmaker'],$prev_elementid,'shapes');
		if ($mode == 'continue_execution') {
			$elementid = $prev_elementid;
			$info = $prev_elementid_info;
		} else {
			$info = $this->getStructureElementInfo($focusProcesses->column_fields['processmaker'],$elementid,'shapes');
		}
		$engineType = $this->getEngineType($info);
		if (!in_array($engineType,array('Condition','Action')) || $this->isStartTask($focusProcesses->column_fields['processmaker'],$elementid)) return false;

		// calculate metaid and wsId
		if ($engineType == 'Condition') {
			$data = $this->retrieve($focusProcesses->column_fields['processmaker']);
			$vte_metadata = Zend_Json::decode($data['vte_metadata']);
			$metadata = $vte_metadata[$elementid];
			if (strpos($metadata['moduleName'],':') === false) {
				$result = $adb->pquery("SELECT id FROM {$table_prefix}_processmaker_metarec WHERE processid = ? AND elementid = ?", array($focusProcesses->column_fields['processmaker'],$elementid));
				if ($result && $adb->num_rows($result) > 0) {
					$metaid = $adb->query_result($result,0,'id');
					$related_to = ProcessMakerEngine::getCrmid($metaid,$focusProcesses->column_fields['running_process']);
					if (!empty($related_to)) {
						$wsId = 'x'.$related_to;
					}
				}
			} else {
				list($metaid,$module) = explode(':',$metadata['moduleName']);
				if ($module == 'DynaForm') {
					$processDynaFormObj = ProcessDynaForm::getInstance();
					$crmid = $processDynaFormObj->getProcessesId($focusProcesses->column_fields['running_process'],$metaid);
					$metaid = '';
					$wsId = 'x'.$crmid;
				} else {
					$related_to = ProcessMakerEngine::getCrmid($metaid,$focusProcesses->column_fields['running_process']);
					if (!empty($related_to)) {
						$wsId = 'x'.$related_to;
					}
				}
			}
		} else {
			// search record in the element
			$result = $adb->pquery("SELECT rec.id, rec.crmid
				FROM {$table_prefix}_processmaker_metarec metarec
				INNER JOIN {$table_prefix}_processmaker_rec rec ON metarec.id = rec.id AND metarec.processid = ? AND rec.running_process = ?
				WHERE metarec.elementid = ?", array($focusProcesses->column_fields['processmaker'],$focusProcesses->column_fields['running_process'],$elementid));
			if ($adb->num_rows($result) == 0) {
				// if do not found get the last record
				$result = $adb->limitpQuery("SELECT id, crmid FROM {$table_prefix}_processmaker_rec WHERE running_process = ? ORDER BY crmid DESC", 0, 1, array($focusProcesses->column_fields['running_process']));
			}
			if ($result && $adb->num_rows($result) > 0) {
				$metaid = $adb->query_result($result,0,'id');
				$wsId = 'x'.$adb->query_result($result,0,'crmid');
			}
		}
		
		//echo $focusProcesses->column_fields['running_process'].','.$focusProcesses->column_fields['processmaker'].",$prev_elementid,$elementid,$wsId,$metaid";die;
		$PMEngine = ProcessMakerEngine::getInstance($focusProcesses->column_fields['running_process'],$focusProcesses->column_fields['processmaker'],$prev_elementid,$elementid,$wsId,$metaid,$entityCache);
		
		$processEnded = $PMEngine->isEndProcess($prev_elementid_info['type']);
		if ($processEnded) $PMEngine->endProcess(0);
		
		if ($mode == 'continue_execution') {
			$PMEngine->activateProcess();
			$PMEngine->execute($engineType,$info['type']);
		} elseif ($mode == 'change_position') {
			$PMEngine->log_rollback = $current_user->id;
			$PMEngine->activateProcess(false);
			$PMEngine->trackProcess($prev_elementid,$elementid);
		}
		
		return true;
	}
	function isEnableRollback() {
		require('user_privileges/requireUserPrivileges.php'); // crmv@39110
		return ($this->todoFunctions && $is_admin);
	}
	function isActiveRunningProcess($running_process) {
		global $adb, $table_prefix;
		$result = $adb->pquery("select active from {$table_prefix}_running_processes where id = ?", array($running_process));
		$active = false;
		if ($result && $adb->num_rows($result) > 0) {
			$active = ($adb->query_result($result,0,'active') == '1');
		}
		return $active;
	}
	//crmv@112539e
	function getAllConditionals($record) {
		require_once('modules/Settings/ProcessMaker/ProcessMakerEngine.php');
		global $adb, $table_prefix;
		static $conditionals = array();
		static $cache = false;
		if (!$cache) {
			$result = $adb->pquery("select {$table_prefix}_running_processes.processmakerid, {$table_prefix}_processmaker_conditionals.running_process, {$table_prefix}_processmaker_conditionals.elementid
				from {$table_prefix}_processmaker_conditionals
				inner join {$table_prefix}_running_processes on {$table_prefix}_running_processes.id = {$table_prefix}_processmaker_conditionals.running_process
				inner join {$table_prefix}_processmaker on {$table_prefix}_processmaker.id = {$table_prefix}_running_processes.processmakerid
				where {$table_prefix}_processmaker.active = ? and {$table_prefix}_running_processes.active = ? and {$table_prefix}_processmaker_conditionals.crmid = ?",
				array(1,1,$record));
			if ($result && $adb->num_rows($result) > 0) {
				while($row=$adb->fetchByAssoc($result,-1,false)) {
					$processmakerid = $row['processmakerid'];
					$running_process = $row['running_process'];
					$elementid = $row['elementid'];
					
					$data = $this->retrieve($processmakerid);
					$vte_metadata = Zend_Json::decode($data['vte_metadata']);
					$vte_metadata_conditionals = $vte_metadata[$elementid]['conditionals'];
					if (!empty($vte_metadata_conditionals)) {
						foreach($vte_metadata_conditionals as $tmp) {
							list($metaid,$module) = explode(':',$tmp['moduleName']);
							$crmid = ProcessMakerEngine::getCrmid($metaid,$running_process);
							if ($record == $crmid) {
								$conditionals[] = $tmp;
							}
						}
					}
				}
			}
			$cache = true;
		}
		return $conditionals;
	}
	function getConditionalPermissions($conditionals, &$column_fields) {
		global $adb, $table_prefix, $current_user;
		$column_fields_bkp = $column_fields;
		$record = $column_fields['record_id'];
		$module = $column_fields['record_module'];
		$webserviceObject = VtigerWebserviceObject::fromName($adb,$module);
		$wsRecord = vtws_getId($webserviceObject->getEntityId(),$record);

		// force cache with column_fields of $_REQUEST in order to manage the live conditionals
		$cache_column_fields = $column_fields;
		require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
		$entityCache = new VTEntityCache($current_user);
		$entityCache->forId($wsRecord);
		unset($cache_column_fields['record_id']);
		unset($cache_column_fields['record_module']);
		$cache_column_fields['id'] = $wsRecord;
		$entityCache->cache[$wsRecord]->data = $cache_column_fields;

		// get fields informations
		$column_fields_check = $column_fields_bkp;	// for table fields check
		$fields = array();
		$result = $adb->pquery("select * from {$table_prefix}_field where tabid = ?", array(getTabid($module)));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$fields[$row['fieldname']] = WebserviceField::fromArray($adb,$row);
				if ($fields[$row['fieldname']]->getFieldDataType() == 'table') {
					if (is_array($column_fields[$row['fieldname']])) {
						// retrieve from column_fields (EditViewConditionals)
						$column_fields_check[$row['fieldname']] = $column_fields[$row['fieldname']]['rows'];
					} else {
						// retrieve from db
						require_once('include/utils/ModLightUtils.php');
						$MLUtils = ModLightUtils::getInstance();
						$columns = $MLUtils->getColumns($module,$row['fieldname']);
						$column_fields_check[$row['fieldname']] = array();
						$values = $MLUtils->getValues($module,$record,$row['fieldname'],$columns);
						if (!empty($values)) {
							foreach($values as $tmp) {
								array_push($column_fields_check[$row['fieldname']],$tmp['row']);
							}
						}
					}
				}
			}
		}

		$role_grp_checks = array();
		//tutti
		$role_grp_checks[] = 'ALL';
		//ruoli
		$role_grp_checks[] = "roles::".$current_user->roleid;
		//ruoli e subordinati
		$subordinates=getRoleAndSubordinatesInformation($current_user->roleid);
		$parent_role=$subordinates[$current_user->roleid][1];
		if (!is_array($parent_role)){
			$parent_role = explode('::',$parent_role);
			foreach ($parent_role as $parent_role_value){
				$role_grp_checks[] = "rs::".$parent_role_value;
			}
		}
		//gruppi
		require('user_privileges/requireUserPrivileges.php'); // crmv@39110
		if (is_array($current_user_groups)){
			foreach ($current_user_groups as $current_user_groups_value){
				$role_grp_checks[] = "groups::".$current_user_groups_value;
			}
		}

		// split standard and table-field conditionals
		$conditionals_std = array();
		$conditionals_tabs = array();
		foreach($conditionals as $i => $conditional) {
			$tab = false;
			if (!empty($conditional['conditions'])) {
				foreach($conditional['conditions'] as $subconditions) {
					if (!empty($subconditions['conditions'])) {
						foreach($subconditions['conditions'] as $subcondition) {
							if (isset($subcondition['tabfieldopt'])) {
								$tab = true;
								break;
							}
						}
					}
				}
			}
			($tab) ? $conditionals_tabs[] = $conditionals[$i] : $conditionals_std[] = $conditionals[$i];
		}
		if (!empty($conditionals_tabs)) {
			$actionType = $this->getActionTypes('Cycle');
			require_once($actionType['php_file']);
			$actionCycle = new $actionType['class']();
		}

		global $edit_view_conditionals_mode;
		$edit_view_conditionals_mode = true;
		$permissions = array();
		$i = 0;
		if (!empty($conditionals_std)) {
			foreach($conditionals_std as $conditional) {
				$role_grp_check = $conditional['role_grp_check'];
				if (in_array($role_grp_check,$role_grp_checks)) {
					$conditions = Zend_Json::encode($conditional['conditions']);
					if ($this->evaluateCondition($entityCache, $wsRecord, $conditions)) {
						foreach($column_fields_check as $fieldname => $value) {
							$perm = $conditional['fpofv'][$fieldname];
							$this->setFieldConditionalPermissions($perm, $i, $fieldname, $permissions);
							$field = $fields[$fieldname];
							if (isset($fields[$fieldname]) && $field->getFieldDataType() == 'table') {
								if (is_array($value)) {
									foreach($value as $seq => $row) {
										foreach($row as $column => $column_value) {
											$perm = $conditional['fpofv'][$fieldname.'::'.$column];
											$this->setFieldConditionalPermissions($perm, $i, $fieldname.'_'.$column.'_'.$seq, $permissions);
										}
									}
								}
							}
						}
						$i++;
					}
				}
			}
		}
		// check for conditionals in table-fields
		if (!empty($conditionals_tabs)) {
			foreach($column_fields_check as $fieldname => $value) {
				$field = $fields[$fieldname];
				if (isset($fields[$fieldname]) && $field->getFieldDataType() == 'table') {
					if (is_array($value)) {
						foreach($value as $seq => $row) {
							foreach($conditionals_tabs as $conditional) {
								$role_grp_check = $conditional['role_grp_check'];
								if (in_array($role_grp_check,$role_grp_checks)) {
									$conditions = Zend_Json::encode($conditional['conditions']);
									if ($actionCycle->checkRowConditions(null, $column_fields_check, $conditions, $seq)) {
										// applico permessi alla riga e anche agli altri campi del modulo
										$fpofv = $conditional['fpofv'];
										foreach($fpofv as $f => $fp) {
											// if it is a column of the current table-field OK
											// if it is a column of another table-field SKIP
											// if it is a standard field OK
											if (strpos($f,'::') !== false) {
												list($f1,$f2) = explode('::',$f);
												if ($f1 == $fieldname) {
													$this->setFieldConditionalPermissions($fp, $i, $f1.'_'.$f2.'_'.$seq, $permissions);
												}
											} else {
												$this->setFieldConditionalPermissions($fp, $i, $f, $permissions);
											}
										}
										$i++;
									}
								}
							}
						}
					}
				}
			}
		}
		
		// set in request cache
		$cache = RCache::getInstance();
		$cache->set('conditional_permissions', $permissions);
		
		$conditional_permissions = array();
		if (!empty($permissions)) {
			// permissions for table field are managed in TableFieldUtils::generateRowVars
			$fieldids = array();
			$result = $adb->pquery("select fieldid, fieldname from {$table_prefix}_field where tabid = ? and fieldname in (".generateQuestionMarks(array_keys($permissions)).")", array(getTabid($module),array_keys($permissions)));
			if ($result && $adb->num_rows($result) > 0) {
				while($row=$adb->fetchByAssoc($result)) {
					$fieldids[$row['fieldname']] = $row['fieldid'];
				}
			}
			foreach($permissions as $fieldname => $permission) {
				if (!isset($fieldids[$fieldname])) continue;
				if ($permission['readonly'] == 99) {
					$f2fp_visible = 1;
					$f2fp_editable = 0;
				} elseif ($permission['readonly'] == 100) {
					$f2fp_visible = 0;
					$f2fp_editable = 0;
				} else {
					$f2fp_visible = 1;
					$f2fp_editable = 1;
				}
				$conditional_permissions[$fieldids[$fieldname]] = Array(
					'f2fp_visible'=>$f2fp_visible,
					'f2fp_editable'=>$f2fp_editable,
					'f2fp_mandatory'=>($permission['mandatory'] == 1)?1:0,
				);
				if (isset($permission['value'])) {
					$column_fields[$fieldname] = $this->replaceTags($permission['value'],$column_fields_bkp);
				}
			}
		}
		return $conditional_permissions;
	}
	//crmv@105312 crmv@112297
	function setFieldConditionalPermissions($perm, $i, $fieldname, &$permissions) {
		if ($perm['FpovManaged'] == 1) {
			if ($i == 0) {
				$permissions[$fieldname]['readonly'] = 1;
				$permissions[$fieldname]['mandatory'] = false;
			}
			if ($perm['FpovReadPermission'] == 1) {
				if ($perm['FpovWritePermission'] == 1) {
					$readonly = 1;
					if ($perm['FpovMandatoryPermission'] == 1) {
						$permissions[$fieldname]['mandatory'] = true;
					}
				} else {
					$readonly = 99;
				}
			} else {
				$readonly = 100;
			}
			//crmv@103826
			// the first conditional overwrite the standard permissions
			// or if there are more conditionals verified set the most restrictive rule
			if ($i == 0 || $readonly > $permissions[$fieldname]['readonly']) {
				$permissions[$fieldname]['readonly'] = $readonly;
			}
			if ($perm['FpovValueActive'] == 1) $permissions[$fieldname]['value'] = $perm['FpovValueStr'];
			//crmv@103826e
		}
	}
	function replaceTags($value, $columns, $selector='/(\$([a-zA-Z0-9_]+))/') {
		// apply sdk functions
		preg_match_all('/(\$sdk:([a-zA-Z0-9_)(:.,"\'$]+))/', $value, $matches, PREG_SET_ORDER);
		if (!empty($matches)) {
			$sdkFieldConditions = SDK::getProcessMakerFieldActions();
			foreach($matches as $match) {
				$tag = trim($match[0]);
				$sdk_function = $match[2];
				$funct = substr($sdk_function,0,strpos($sdk_function,'('));
				if (isset($sdkFieldConditions[$funct])) {
					$params = substr($sdk_function,strpos($sdk_function,'(')+1);
					$params = substr($params,0,strpos($params,')'));
					$params = trim($params);
					if (!empty($params)) {
						$params = explode(',',$params);
						array_walk($params, create_function('&$v,$k', '$v = trim($v);'));
						// repalce tags
						foreach($params as &$param) {
							preg_match_all($selector, $param, $matches, PREG_SET_ORDER);
							if (!empty($matches)) {
								$fieldname = str_replace('$','',$param);
								$param = $columns[$fieldname];
							}
						}
					} else {
						$params = array();
					}
					require_once($sdkFieldConditions[$funct]['src']);
					$replacement = call_user_func_array($funct, $params);
				} else {
					$replacement = '';
				}
				$value = str_replace($tag,$replacement,$value);
			}
		}
		// replace tags
		preg_match_all($selector, $value, $matches, PREG_SET_ORDER);
		if (!empty($matches)) {
			foreach($matches as $match) {
				$fieldname = str_replace('$','',$match[0]);
				$fieldvalue = $columns[$fieldname];
				$value = str_replace($match[0],$fieldvalue,$value);
			}
		}
		return $value;
	}
	//crmv@105312e crmv@112297e
	
	//crmv@115268
	function preserveRequest() {
		$this->preserved_request = $_REQUEST;
		$_REQUEST = array();
	}
	function restoreRequest() {
		$_REQUEST = $this->preserved_request;
	}
	function setDefaultDataFormat() {
		global $current_user;
		$this->preserved_date_format = $current_user->date_format;
		$current_user->date_format = 'yyyy-mm-dd';
		$current_user->column_fields['date_format'] = 'yyyy-mm-dd';
	}
	function restoreDataFormat() {
		global $current_user;
		$current_user->date_format = $this->preserved_date_format;
		$current_user->column_fields['date_format'] = $this->preserved_date_format;
	}
	//crmv@115268e
}

require_once('modules/Settings/ModuleMaker/ModuleMakerGenerator.php');
class ProcessModuleMakerGenerator extends ModuleMakerGenerator {
	function __construct() {}
	function getTODForField($field) {
		return parent::getTODForField($field);
	}
	function makeTODMandatory($tod) {
		return parent::makeTODMandatory($tod);
	}
}

require_once('modules/Settings/ModuleMaker/ModuleMakerSteps.php');
class ProcessModuleMakerSteps extends ModuleMakerSteps {
	function getNewFields() {
		$unsupported_uitypes = array(1015,4);
		$fields = parent::getNewFields();
		foreach($fields as $i => $field) {
			if(in_array($field['uitype'],$unsupported_uitypes)) unset($fields[$i]);
		}
		//crmv@98570
		if (SDK::isUitype(213)) {
			$fields[] = array(
				'uitype' => 213,
				'label' => getTranslatedString('LBL_FIELD_BUTTON'),
				'vteicon2' => 'fa-hand-pointer-o',
				'properties' => array('label','onclick','code'),
				'defaults' => array('onclick'=>'function(view[,param])'),
			);
		}
		//crmv@98570e
		// crmv@102879
		$PMUtils = ProcessMakerUtils::getInstance();
		if ($PMUtils->todoFunctions && SDK::isUitype(220)) {
			$fields[] = array(
				'uitype' => 220,
				'label' => getTranslatedString('LBL_FIELD_TABLE'),
				'vteicon' => 'grid_on',
				'properties' => array('label','columns'),
			);
		}
		// crmv@102879e
		$fields = array_values($fields);	//crmv@106857
		return $fields;
	}
	//crmv@106857
	function getNewTableFieldColumns() {
		$unsupported_uitypes = array(213,220,10,29);
		$fields = $this->getNewFields();
		foreach($fields as $i => $field) {
			if(in_array($field['uitype'],$unsupported_uitypes)) {
				unset($fields[$i]);
				continue;
			}
			// add other properties
			$fields[$i]['properties'][] = 'readonly';
			$fields[$i]['properties'][] = 'mandatory';
			$fields[$i]['properties'][] = 'newline';
			// add defaults for other properties
			$fields[$i]['defaults']['readonly'] = 1;
			$fields[$i]['defaults']['mandatory'] = false;
		}
		return $fields;
	}
	//crmv@106857e
}