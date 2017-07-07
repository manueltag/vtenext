<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
 
/* crmv@91571 */

require_once('include/BaseClasses.php');

class MassEditUtils extends SDKExtendableUniqueClass {

	public $table;
	public $queueTable;
	
	public $chunkSize = 400;	// retrieve the record to update in chunks of this size
	public $purgeDays = 15;		// old jobs, completed or not, will be purged after this number of days
	public $chunkInterval = 2;	// number of seconds to wait between each chunk of records
	
	const MASS_STATUS_IDLE = 0;
	const MASS_STATUS_PROCESSING = 1;
	const MASS_STATUS_COMPLETE = 2;
	const MASS_STATUS_ERROR = 3;
	
	const MASSQUEUE_STATUS_IDLE = 0;
	const MASSQUEUE_STATUS_PROCESSING = 1;
	const MASSQUEUE_STATUS_COMPLETE = 2;
	const MASSQUEUE_STATUS_ERROR = 3;

	// some private vars
	protected $cachedInstances = array();
	protected $userStack = array();
	
	public function __construct() {
		global $table_prefix;
		
		$this->table = $table_prefix.'_massedit';
		$this->queueTable = $table_prefix.'_massedit_queue';
	}
	
	public function enqueue($userid, $module, $fields, $records, $useWf = true) {
		global $adb;
		
		if (empty($records)) return true;
		
		// get the massedit-id
		$massid = $adb->getUniqueID($this->table);
		
		// save in the main table
		$sql = "INSERT INTO {$this->table} (massid, userid, module, inserttime, workflows, status) VALUES (?,?,?,?,?,?)";
		$params = array($massid, $userid, $module, date('Y-m-d H:i:s'), intval($useWf), self::MASS_STATUS_IDLE);
		$res = $adb->pquery($sql, $params);
		
		$adb->updateClob($this->table, 'fieldvalues', "massid = $massid", Zend_Json::encode($fields));
		
		// get the list of ids
		$inserts = array();
		foreach ($records as $crmid) {
			if (!empty($crmid)) $inserts[] = array($massid, intval($crmid));
		}
		
		// and quickly insert into the db
		$adb->bulkInsert($this->queueTable, array('massid', 'crmid'), $inserts);
		
		return true;
	}
	
	/**
	 * Clean the MassEdit queue from the succesful updates
	 */
	public function cleanQueue($massid = null) {
		global $adb;
		
		if ($massid > 0) {
			$adb->pquery("DELETE FROM {$this->queueTable} WHERE massid = ? AND status = ?", array($massid, self::MASSQUEUE_STATUS_COMPLETE));
		} else {
			$adb->pquery("DELETE FROM {$this->queueTable} WHERE status = ?", array(self::MASSQUEUE_STATUS_COMPLETE));
		}
	}
	
	/**
	 * Remove from the queue everything related to old jobs (failed or not)
	 */
	public function cleanOldJobs() {
		global $adb;
		
		$massids = array();
		$timeLimit = date('Y-m-f H:i:s', time()-($this->purgeDays*24*3600));
		$params = array($timeLimit, self::MASS_STATUS_COMPLETE, self::MASS_STATUS_ERROR, self::MASS_STATUS_PROCESSING);
		$res = $adb->pquery("SELECT massid FROM {$this->table} WHERE inserttime < ? AND status IN (?,?,?)", $params);
		if ($res && $adb->num_rows($res) > 0) {
			while ($row = $adb->FetchByAssoc($res, -1, false)) {
				$massids[] = intval($row['massid']);
			}
		}
		
		if (count($massids) > 0) {
			$adb->pquery("DELETE FROM {$this->queueTable} WHERE massid IN (".generateQuestionMarks($massids).")", array($massids));
			$adb->pquery("DELETE FROM {$this->table} WHERE massid IN (".generateQuestionMarks($massids).")", array($massids));
		}
	}
	
	/**
	 * Resume failed jobs which are blocked in the processing state
	 */
	public function cleanFailedJobs() {
		global $adb;
		
		/*
			This is the idea: get the jobs started more than X time ago (default 1 hour) and
			see how many records they have processed. Then using a heuristic formula calculate
			how many records should have been processed, and if it seems to be too slow, 
			set it in idle state, so it can continue the normal operation
		*/
		
		$startedHours = 1;		// check only the jobs started this number of hours ago (or earlier)
		$timePerRecords = 0.2;	// max number of seconds that each save() is allowed to consume
		$multiplier = 2;		// if the elapsed time exceeds the calculated time * multiplier assume it's blocked
		
		$timeLimit = date('Y-m-f H:i:s', time()-($startedHours*3600));
		$params = array($timeLimit, self::MASS_STATUS_PROCESSING);
		$res = $adb->pquery("SELECT massid, starttime FROM {$this->table} WHERE starttime IS NOT NULL AND starttime != '0000-00-00 00:00:00' AND starttime < ? AND status = ?", $params);
		if ($res && $adb->num_rows($res) > 0) {
			while ($row = $adb->FetchByAssoc($res, -1, false)) {
				$massid = intval($row['massid']);
				$completed = $this->countCompletedEditJobs($massid);
				
				// calculate the allowed time
				$chunks = ceil($completed/$this->chunkSize);
				$allowedTime = $multiplier * ($completed*$timePerRecords + $chunks * $this->chunkInterval);
				
				// calculate the elapsed time
				$elapsedTime = time() - strtotime($row['starttime']);
				
				if ($elapsedTime > $allowedTime) {
					// suppose it's stuck, resume it!
					$this->log("The massedit job #$massid has been resumed ($elapsedTime vs $allowedTime)");
					$this->cleanQueue($massid);
					$rparams = array(date('Y-m-d H:i:s'), self::MASS_STATUS_IDLE, $massid);
					$adb->pquery("UPDATE {$this->table} SET starttime = ?, status = ? WHERE massid = ?", $rparams);
				}
			}
		}
		
	}
	
	/**
	 * Get the first runnable job (in IDLE state)
	 */
	public function getRunnableJob() {
		global $adb;
		
		$massid = 0;
		$res = $adb->limitpQuery("SELECT massid FROM {$this->table} WHERE status = ? ORDER BY massid ASC", 0, 1, array(self::MASS_STATUS_IDLE));
		if ($res && $adb->num_rows($res) > 0) {
			$massid = intval($adb->query_result_no_html($res, 0, 'massid'));
		}
		
		return $massid;
	}
	
	public function getJobInfo($massid) {
		global $adb;
		
		$info = null;
		$res = $adb->pquery("SELECT * FROM {$this->table} WHERE massid = ?", array($massid));
		if ($res && $adb->num_rows($res) > 0) {
			$info = $adb->FetchByAssoc($res, -1, false);
			if (!empty($info['fieldvalues'])) $info['fieldvalues'] = Zend_Json::decode($info['fieldvalues']);
			if (!empty($info['results'])) $info['results'] = Zend_Json::decode($info['results']);
		}
		
		return $info;
	}
	
	public function setJobStatus($massid, $status) {
		global $adb;
		
		$sql = "UPDATE {$this->table} SET status = ?";
		$params = array('status' => $status);
		
		if ($status == self::MASS_STATUS_PROCESSING) {
			$sql .= ", starttime = ?";
			$params['starttime'] = date('Y-m-d H:i:s');
		} elseif ($status == self::MASS_STATUS_COMPLETE || $status == self::MASS_STATUS_ERROR) {
			$sql .= ", endtime = ?";
			$params['endtime'] = date('Y-m-d H:i:s');
		}
		
		$sql .= " WHERE massid = ?";
		$params['massid'] = $massid;
		
		$adb->pquery($sql, $params);
	}
	
	public function setJobResults($massid, $results) {
		global $adb;
		
		$adb->updateClob($this->table, 'results', "massid = $massid", Zend_Json::encode($results));
	}
	
	/**
	 * Push the specified user on to the user stack
	 * and make it the $current_user
	 *
	 */
	protected function switchUser($userid) {
		global $current_user;
		
		array_push($this->userStack, $current_user);
		$user = CRMEntity::getInstance('Users');
		$user->retrieveCurrentUserInfoFromFile($userid);
		
		$current_user = $user;
		return $user;
	}

	/**
	 * Revert to the previous use on the user stack
	 */
	protected function revertUser(){
		global $current_user;
		if (count($this->userStack) > 0) {
			$current_user = array_pop($this->userStack);
		} else {
			$current_user = null;
		}
		return $current_user;
	}
	
	public function processCron($massid = 0) {
	
		if (empty($massid)) {
			// clean queue and do generic stuff
			$this->cleanOldJobs();
			$this->cleanFailedJobs();
			
			// get the massid
			$massid = $this->getRunnableJob();
		}
		
		if (empty($massid)) {
			// nothing to do!
			return true;
		}
		
		// do the job!
		$r = $this->process($massid);
		
		return $r;
	}
	
	public function process($massid) {
		global $adb;
		
		$info = $this->getJobInfo($massid);
		
		$module = $info['module'];
		$result = array(
			'processed' => 0,
			'completed' => 0,
			'error' => 0,
			'message' => '',
		);
		
		if (!isModuleInstalled($module) || !vtlib_isModuleActive($module)) {
			$result['message'] = "Module $module is not active";
			$this->setJobStatus($massid, self::MASS_STATUS_ERROR);
			$this->setJobResults($massid, $result);
			$this->notifyUser($info, $result);
			return false;
		}
		
		$this->setJobStatus($massid, self::MASS_STATUS_PROCESSING);
		
		// disable die on error, enable exception
		$oldDieOnError = $adb->dieOnError;
		$adb->setDieOnError(false);
		$adb->setExceptOnError(true);
		
		// change to the user who made the request
		$this->switchUser($info['userid']);
		
		$rtot = true;
		$list = $this->getRecordsChunk($massid);
		while (count($list) > 0) {
			foreach ($list as $editjob) {
				$error = '';
				$r = $this->processEditJob($massid, $info, $editjob, $error);
				$rtot &= $r;
				$result['processed']++;
				if ($r) {
					$result['completed']++;
				} else {
					$result['error']++;
				}
			}
			// wait a couple of seconds between the chunks
			sleep($this->chunkInterval);
			// retrieve the next chunk
			$list = $this->getRecordsChunk($massid);
		}
		
		$this->revertUser();
		
		// restore die on error
		$adb->setDieOnError($oldDieOnError);
		
		// now check for jobs in processing status (something went very wrong during the process and it was resumed)
		$processing = $this->countProcessingEditJobs($massid);
		if ($processing > 0) {
			$rtot = false;
			$result['error'] += $processing;
			$result['processed'] += $processing;
			$this->error("Some records have not been saved corectly, and caused the script to terminate.");
			$this->error("The process resumed, but the status of those records is unknown. Check the table massedit_queue.");
			$adb->pquery("UPDATE {$this->queueTable} SET status = ? WHERE massid = ? AND status = ?", array(self::MASSQUEUE_STATUS_ERROR, $massid, self::MASSQUEUE_STATUS_PROCESSING));
		}
		
		$this->notifyUser($info, $result);
		$this->setJobResults($massid, $result);
		
		if ($rtot) {
			// everything ok
			$this->setJobStatus($massid, self::MASS_STATUS_COMPLETE);
		} else {
			// some errors
			$this->setJobStatus($massid, self::MASS_STATUS_ERROR);
		}
		
		// clean the queue
		$this->cleanQueue($massid);
		
		return $rtot;
	}
	
	public function getRecordsChunk($massid) {
		global $adb;
		
		$list = array();
		$params = array($massid, self::MASSQUEUE_STATUS_IDLE);
		$res = $adb->limitpQuery("SELECT * FROM {$this->queueTable} WHERE massid = ? AND status = ?", 0, $this->chunkSize, $params);
		while ($row = $adb->FetchByAssoc($res, -1, false)) {
			$list[] = $row;
		}
		return $list;
	}
	
	public function notifyUser($massinfo, $result) {
		global $current_user;
		
		$success = ($result['error'] == 0);
		
		$focus = CRMEntity::getInstance('ModNotifications');
		if ($success) {
			$desc = "\n".getTranslatedString('LBL_MASSEDIT_OK_DESC', 'APP_STRINGS');
			$desc = str_replace(
				array('{num_records}', '{num_fail_records}', '{module}'), 
				array($result['processed'], $result['error'], getTranslatedString($massinfo['module'], $massinfo['module'])), 
				$desc
			);
			$notifInfo = array(
				'assigned_user_id' => $massinfo['userid'],
				'mod_not_type' => 'MassEdit',
				'related_to' => $massinfo['massid'],
				'subject' => getTranslatedString('LBL_MASSEDIT_OK_SUBJECT', 'APP_STRINGS'),
				'description' => $desc,
				'from_email' => $current_user->email1 ?: $current_user->email2,
				'from_email_name' => getUserFullName($current_user->id),
			);
		} else {
			$desc = "\n".getTranslatedString('LBL_MASSEDIT_ERROR_DESC', 'APP_STRINGS');
			$desc = str_replace(
				array('{num_records}', '{num_fail_records}', '{module}'), 
				array($result['processed'], $result['error'], getTranslatedString($massinfo['module'], $massinfo['module'])), 
				$desc
			);
			$notifInfo = array(
				'assigned_user_id' => $massinfo['userid'],
				'mod_not_type' => 'MassEditError',
				'related_to' => $massinfo['massid'],
				'subject' => getTranslatedString('LBL_MASSEDIT_ERROR_SUBJECT', 'APP_STRINGS'),
				'description' => $desc,
				'from_email' => $current_user->email1 ?: $current_user->email2,
				'from_email_name' => getUserFullName($current_user->id),
			);
		}
		
		$focus->saveFastNotification($notifInfo);
		
		return true;
	}
	
	public function processEditJob($massid, $massinfo, $editjob, &$error = '') {
		global $adb;
		
		$module = $massinfo['module'];
		$crmid = intval($editjob['crmid']);
		$values = $massinfo['fieldvalues'];
		$useWf = ($massinfo['workflows'] == '1');
		
		$adb->pquery("UPDATE {$this->queueTable} SET status = ? WHERE massid = ? AND crmid = ?", array(self::MASSQUEUE_STATUS_PROCESSING, $massid, $crmid));
		
		$error = '';
		$r = true;
		try {
			$r = $this->saveRecord($module, $crmid, $values, $useWf, $error);
		} catch (Exception $e) {
			$r = false;
			$error = 'EXCEPTION: '.$e->getMessage();
		}
		
		if ($r) {
			$adb->pquery("UPDATE {$this->queueTable} SET status = ? WHERE massid = ? AND crmid = ?", array(self::MASSQUEUE_STATUS_COMPLETE, $massid, $crmid));
		} else {
			$adb->pquery("UPDATE {$this->queueTable} SET status = ?, info = ? WHERE massid = ? AND crmid = ?", array(self::MASSQUEUE_STATUS_ERROR, $error, $massid, $crmid));
			// error log
			if ($error == 'NOT_PERMITTED') {
				$this->error("User doesn't have the permission to edit the record $crmid");
			} elseif ($error == 'LBL_RECORD_DELETE') {
				$this->error("The record $crmid has been deleted");
			} elseif ($error == 'LBL_RECORD_NOT_FOUND') {
				$this->error("The record $crmid was not found");
			} else {
				$this->error("Error while saving record $crmid ($module):");
				$this->error($error);
			}
		}
		
		return $r;
	}
	
	public function countCompletedEditJobs($massid) {
		global $adb;
		
		$count = 0;
		$res = $adb->pquery("SELECT COUNT(*) as count FROM {$this->queueTable} WHERE massid = ? AND status = ?", array($massid, self::MASSQUEUE_STATUS_COMPLETE));
		if ($res && $adb->num_rows($res) > 0) {
			$count = intval($adb->query_result_no_html($res, 0, 'count'));
		}
		
		return $count;
	}
	
	public function countProcessingEditJobs($massid) {
		global $adb;
		
		$count = 0;
		$res = $adb->pquery("SELECT COUNT(*) as count FROM {$this->queueTable} WHERE massid = ? AND status = ?", array($massid, self::MASSQUEUE_STATUS_PROCESSING));
		if ($res && $adb->num_rows($res) > 0) {
			$count = intval($adb->query_result_no_html($res, 0, 'count'));
		}
		
		return $count;
	}
	
	public function getModuleInstance($module) {
		if (!array_key_exists($module, $this->cachedInstances)) {
			$crmModule = $module;
			if ($module == 'Events') $crmModule = 'Calendar';
			$this->cachedInstances[$module] = CRMEntity::getInstance($crmModule);
			vtlib_setup_modulevars($module, $this->cachedInstances[$module]);
		}
		
		// reset some internal values
		if ($this->cachedInstances[$module]) {
			$this->cachedInstances[$module]->id = null;
			$this->cachedInstances[$module]->mode = null;
			$this->cachedInstances[$module]->parentid = null;
		}
		
		return $this->cachedInstances[$module];
	}
	
	public function extractValuesFromRequest($module, &$request) {
		$focus = $this->getModuleInstance($module);
		
		$massValues = array();
		foreach($focus->column_fields as $fieldname => $val) {
			if(isset($request[$fieldname."_mass_edit_check"])) {
				if (!isProductModule($module) && $fieldname == 'assigned_user_id') {
					if($request['assigntype'] == 'U')  {
						$value = $request['assigned_user_id'];
					} elseif($request['assigntype'] == 'T') {
						$value = $request['assigned_group_id'];
					}
				} else {
					if(is_array($request[$fieldname]))
						$value = $request[$fieldname];
					else
						$value = trim($request[$fieldname]);
				}
				$massValues[$fieldname] = $value;
			}
		}

		// crmv@77878 fix for calendar
		if ($module == 'Calendar' && isset($request["date_start_mass_edit_check"])) {
			if (!empty($request['starthr']) && !empty($request['startmin'])) {
				$value = $request['starthr'].':'.$request['startmin'];
				$massValues['time_start'] = $value;
			}
		}
		// crmv@77878e 
		
		return $massValues;
	}
	
	// crmv@93052 crmv@108612
	public function saveRecord($module, $crmid, $values, $useWf = true, &$error = '') {
		global $adb, $table_prefix;
		
		if (isPermitted($module,'EditView',$crmid) != 'yes') {
			$error = 'NOT_PERMITTED';
			return false;
		}

		$focus = $this->getModuleInstance($module);

		$saveModule = $module;
		if ($module == 'Calendar') {
			$actType = getSingleFieldValue($table_prefix."_activity", 'activitytype', 'activityid', $crmid);
			if($actType == 'Task'){
				$saveModule = $actType;
			} else {
				$saveModule = 'Events';
			}
		}
		
		// Save each module record with update value.
		$r = $focus->retrieve_entity_info($crmid, $module, false);
		if (in_array($r, array('LBL_RECORD_DELETE', 'LBL_RECORD_NOT_FOUND'))) {
			$error = $r;
			return false;
		}
		
		$focus->mode = 'edit';
		$focus->id = $crmid;
		foreach($focus->column_fields as $fieldname => $val) {
			// change the status field for that stupid calendar!
			if ($fieldname == 'taskstatus' && $saveModule == 'Events'){
				$fieldname = 'eventstatus';
			}

			if (array_key_exists($fieldname, $values)) {
				$focus->column_fields[$fieldname] = $values[$fieldname];
			} else {
				$focus->column_fields[$fieldname] = decode_html($focus->column_fields[$fieldname]);
			}
		}

		//crmv@107307
		if(isInventoryModule($module)){
			$_REQUEST['action'] = 'MassEditSave';
			
		}
		//crmv@107307e

		//crmv@27096
		if ($useWf) {
			$focus->save($module);
		} else {
			$focus->save($module,false,false,false);
		}
		//crmv@27096e
		
		return true;
	}
	// crmv@93052e crmv@108612e
	
	public function getNotificationInfo($massid) {
		$info = $this->getJobInfo($massid);
		
		$notInfo = array(
			'status' => $info['status'],
			'num_records' => $info['results']['processed'],
			'num_fail_records' => $info['results']['error'],
			'num_ok_records' => $info['results']['completed'],
		);
		
		return $notInfo;
	}
	
	public function getNotificationHtml($massid, $html = '') {

		$info = $this->getNotificationInfo($massid);
		
		if ($info['status'] == self::MASS_STATUS_ERROR) {
			$desc = getTranslatedString('LBL_MASSEDIT_ERROR', 'APP_STRINGS');
			$desc = str_replace(array('{num_records}', '{num_fail_records}'), array($info['num_records'], $info['num_fail_records']), $desc);
			$html = "<b>MassEdit Error</b> ".$desc;
		} else {
			$desc = getTranslatedString('LBL_MASSEDIT_OK', 'APP_STRINGS');
			$desc = str_replace('{num_records}', $info['num_ok_records'], $desc);
			$html = "<b>MassEdit</b> ".$desc;
		}
		
		return $html;
	}
	
	// logging function
	protected function log($msg) {
		$this->outputLog('[INFO] '.$msg);
		return true;
	}
	
	protected function warning($msg) {
		$this->outputLog('[WARNING] '.$msg);
		return true;
	}
	
	protected function error($msg) {
		$this->outputLog('[ERROR] '.$msg);
		return false;
	}
	
	protected function outputLog($msg) {
		echo $msg."\n";
	}
	
}