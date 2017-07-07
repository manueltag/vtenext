<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@41883 crmv@107655 */

class EmailDirectory {
	
	protected $table;
	protected $synctable;
	
	protected $userid;
	protected $modules = array('Users','Contacts','Accounts','Leads','Vendors');	// moduli ordinati per priorità
	protected $uiypes = array(13,104);
	
	function __construct($userid='') {
		global $table_prefix;
		$this->table = $table_prefix.'_email_directory';
		$this->synctable = $table_prefix.'_email_directory_sync';
		if (empty($userid)) {
			global $current_user;
			$this->userid = $current_user->id;
		}
	}
	
	public function getUItypes() {
		return $this->uiypes;
	}
	
	public function getUserid() {
		return $this->userid;
	}
	
	public function getModules() {
		return $this->modules;
	}
	
	public function getRecord($email) {
		global $adb;
		$result = $adb->pquery("select crmid, module from {$this->table} where userid = ? and email = ?",array($this->userid,$email));
		if ($result && $adb->num_rows($result) > 0) {
			return array('crmid'=>$adb->query_result_no_html($result,0,'crmid'),'module'=>$adb->query_result_no_html($result,0,'module')); // crmv@80298
		} else {
			return false;
		}
	}
	
	public function getId($email) {
		$record = $this->getRecord($email);
		if (!empty($record)) {
			return $record['crmid'];
		} else {
			return false;
		}
	}
	
	public function getEmail($crmid) {
		global $adb;
		$email = array();
		$result = $adb->pquery("select email from {$this->table} where userid = ? and crmid = ?",array($this->userid,$crmid));
		if ($result && $adb->num_rows($result) > 0) {
			while ($row=$adb->fetchByAssoc($result, -1, false)) { // crmv@80298
				$email[] = $row['email'];
			}
			return $email;
		} else {
			return false;
		}
	}
	
	public function getAll($getEntityNames = true) {
		global $adb;
		$list = array();
		$result = $adb->pquery("SELECT email, crmid, module FROM {$this->table} WHERE userid = ?",array($this->userid));
		if ($result && $adb->num_rows($result) > 0) {
			while ($row=$adb->fetchByAssoc($result, -1, false)) {
				if ($getEntityNames && $row['crmid'] && $row['module']) {
					$row['entityname'] = getEntityName($row['module'], array($row['crmid']), true);
					// remove html chars
					if ($row['entityname']) {
						$row['entityname'] = html_entity_decode($row['entityname'], ENT_QUOTES, 'UTF-8');
					}
				}
				$list[] = $row;
			}
		}
		return $list;
	}
	
	public function save($email,$crmid=null,$module=null) {
		$record = $this->getRecord($email);
		if (empty($record)) {
			$this->create($email,$crmid,$module);
		} elseif (empty($record['crmid'])) {
			$this->update($email,$crmid,$module);
		} else {
			$this->checkPriority($email,$crmid,$module,$record);
		}
	}
	
	public function create($email,$crmid=null,$module=null) {
		global $adb;
		//crmv@86188 - sometimes the entry is already there, this creates problems with a slave
		if ($adb->isMysql()) {
			$adb->pquery("insert ignore into {$this->table} (userid,email,crmid,module) values (?,?,?,?)",array($this->userid,$email,$crmid,$module)); 
		} else {
			$adb->pquery("insert into {$this->table} (userid,email,crmid,module) values (?,?,?,?)",array($this->userid,$email,$crmid,$module));
		}
		//crmv@86188e
		$this->updateSyncTime();
	}
	
	public function update($email,$crmid,$module) {
		global $adb;
		$adb->pquery("update {$this->table} set crmid = ?, module = ? where userid = ? and email = ?",array($crmid,$module,$this->userid,$email));
		$this->updateSyncTime();
	}
	
	protected function checkPriority($email,$crmid,$module,$current) {
		$current_priority = array_search($current['module'],$this->modules);
		$new_priority = array_search($module,$this->modules);
		if ($new_priority == $current_priority) {
			// scelgo il record più vecchio
		} elseif ($new_priority < $current_priority) {
			$this->update($email,$crmid,$module);
		}
	}
	
	public function deleteById($crmid) {
		global $adb;
		// first get list of users that are going to change
		$users = array();
		$res = $adb->pquery("SELECT DISTINCT userid FROM {$this->table} WHERE crmid = ?", array($crmid));
		while ($row = $adb->fetchByAssoc($res, -1, false)) {
			$users[] = $row['userid'];
		}
		// then delete the rows
		$adb->pquery("delete from {$this->table} where crmid = ?",array($crmid));
		// and update the times
		foreach ($users as $userid) {
			$this->updateSyncTime($userid);
		}
	}
	
	public function deleteByEmail($email) {
		global $adb;
		// first get list of users that are going to change
		$users = array();
		$res = $adb->pquery("SELECT DISTINCT userid FROM {$this->table} WHERE email = ?", array($email));
		while ($row = $adb->fetchByAssoc($res, -1, false)) {
			$users[] = $row['userid'];
		}
		// then delete the rows
		$adb->pquery("delete from {$this->table} where email = ?",array($email));
		// and update the times
		foreach ($users as $userid) {
			$this->updateSyncTime($userid);
		}
	}
	
	public function getLastUpdate() {
		global $adb;
		$res = $adb->pquery("SELECT last_update FROM {$this->synctable} WHERE userid = ?", array($this->userid));
		if ($res && $adb->num_rows($res) > 0) {
			return $adb->query_result_no_html($res, 0, 'last_update');
		}
		return null;
	}
	
	protected function updateSyncTime($userid = null) {
		global $adb;
		if (empty($userid)) $userid = $this->userid;
		$now = date('Y-m-d H:i:s');
		
		$res = $adb->pquery("SELECT userid FROM {$this->synctable} WHERE userid = ?", array($userid));
		if ($res && $adb->num_rows($res) > 0) {
			$adb->pquery("UPDATE {$this->synctable} SET last_update = ? WHERE userid = ?",array($now,$userid));
		} else {
			$adb->pquery("INSERT INTO {$this->synctable} (userid, last_update) VALUES (?,?)",array($userid,$now));
		}
	}
	
}
