<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class MessagesCore extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name;
	var $table_index= 'messagesid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = true;

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array();

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array();

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array();

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array ();
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Date' => 'mdate',
		'Subject'=> 'subject',
		'From Name'=> 'mfrom_f'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'subject';

	// For Popup listview and UI type support
	var $search_fields = Array();
	var $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Subject'=> 'subject'
	);

	// For Popup window record selection
	var $popup_fields = Array('subject');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'subject';

	// Required Information for enabling Import feature
	var $required_fields = Array('subject'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'mdate';
	var $default_sort_order='DESC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vte_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'subject');
	//crmv@10759
	var $search_base_field = 'subject';
	//crmv@10759 e

	function __construct() {
		global $log, $currentModule, $table_prefix;
		parent::__construct(); // crmv@37004
		$this->table_name = $table_prefix.'_messages';
		$this->customFieldTable = Array($table_prefix.'_messagescf', 'messagesid');
		$this->entity_table = $table_prefix."_crmentity";
		$this->tab_name = Array($table_prefix.'_crmentity', $table_prefix.'_messages', $table_prefix.'_messagescf');
		$this->tab_name_index = Array(
			$table_prefix.'_crmentity' => 'crmid',
			$table_prefix.'_messages'   => 'messagesid',
			$table_prefix.'_messagescf' => 'messagesid'
		);
		$this->list_fields = Array(
			/* Format: Field Label => Array(tablename, columnname) */
			// tablename should not have prefix 'vte_'
			'Date'=> Array($table_prefix.'_messages', 'mdate'),
			'Subject'=> Array($table_prefix.'_messages', 'subject'),
			'From Name'=> Array($table_prefix.'_messages', 'mfrom_f'),
		);
		$this->search_fields = Array(
			/* Format: Field Label => Array(tablename, columnname) */
			// tablename should not have prefix 'vte_'
			'Subject'=> Array($table_prefix.'_messages', 'subject')
		);
		if (empty($this->column_fields)) {
			$this->column_fields = getColumnFields($currentModule);
		}
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
	}

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	function getQueryByModuleField($module, $fieldname, $srcrecord) {
		// $srcrecord could be empty
	}

	/**
	 * Create query to export the records.
	 */
	function create_export_query($where,$oCustomView,$viewId)	//crmv@31775
	{
		global $current_user,$table_prefix;
		$thismodule = $_REQUEST['module'];

		include_once("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery($thismodule, "detail_view");

		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, {$table_prefix}_users.user_name AS user_name
					FROM {$table_prefix}_crmentity INNER JOIN $this->table_name ON {$table_prefix}_crmentity.crmid=$this->table_name.$this->table_index";

		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index";
		}

		$query .= " LEFT JOIN {$table_prefix}_groups ON {$table_prefix}_groups.groupid = {$table_prefix}_crmentity.smownerid";
		$query .= " LEFT JOIN {$table_prefix}_users ON {$table_prefix}_crmentity.smownerid = {$table_prefix}_users.id and {$table_prefix}_users.status='Active'";

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM {$table_prefix}_field" .
				" INNER JOIN {$table_prefix}_fieldmodulerel ON {$table_prefix}_fieldmodulerel.fieldid = {$table_prefix}_field.fieldid" .
				" WHERE uitype='10' AND {$table_prefix}_fieldmodulerel.module=?", array($thismodule));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other = CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);

			$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
		}

		//crmv@31775
		$reportFilter = $oCustomView->getReportFilter($viewId);
		if ($reportFilter) {
			$tableNameTmp = $oCustomView->getReportFilterTableName($reportFilter,$current_user->id);
			$query .= " INNER JOIN $tableNameTmp ON $tableNameTmp.id = {$table_prefix}_crmentity.crmid";
		}
		//crmv@31775e

		$where_auto = " {$table_prefix}_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";

		require('user_privileges/requireUserPrivileges.php'); // crmv@39110
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

		// Security Check for Field Access
		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[7] == 3)
		{
			//Added security check to get the permitted records only
			$query = $query." ".getListViewSecurityParameter($thismodule);
		}
		return $query;
	}

	/**
	 * Initialize this instance for importing.
	 */
	function initImport($module) {
		$this->db = PearDatabase::getInstance();
		$this->initImportableFields($module);
	}

	/**
	 * Create list query to be shown at the last step of the import.
	 * Called From: modules/Import/UserLastImport.php
	 */
	function create_import_query($module) {
		global $current_user,$table_prefix;
		$query = "SELECT {$table_prefix}_crmentity.crmid, case when ({$table_prefix}_users.user_name is not null) then {$table_prefix}_users.user_name else {$table_prefix}_groups.groupname end as user_name, $this->table_name.* FROM $this->table_name
			INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = $this->table_name.$this->table_index
			LEFT JOIN {$table_prefix}_users_last_import ON {$table_prefix}_users_last_import.bean_id={$table_prefix}_crmentity.crmid
			LEFT JOIN {$table_prefix}_users ON {$table_prefix}_users.id = {$table_prefix}_crmentity.smownerid
			LEFT JOIN {$table_prefix}_groups ON {$table_prefix}_groups.groupid = {$table_prefix}_crmentity.smownerid
			WHERE {$table_prefix}_users_last_import.assigned_user_id='$current_user->id'
			AND {$table_prefix}_users_last_import.bean_type='$module'
			AND {$table_prefix}_users_last_import.deleted=0";
		return $query;
	}

	/**
	 * Delete the last imported records.
	 */
	function undo_import($module, $user_id) {
		global $adb,$table_prefix;
		$count = 0;
		$query1 = "select bean_id from {$table_prefix}_users_last_import where assigned_user_id=? AND bean_type='$module' AND deleted=0";
		$result1 = $adb->pquery($query1, array($user_id)) or die("Error getting last import for undo: ".mysql_error());
		while ( $row1 = $adb->fetchByAssoc($result1))
		{
			$query2 = "update {$table_prefix}_crmentity set deleted=1 where crmid=?";
			$result2 = $adb->pquery($query2, array($row1['bean_id'])) or die("Error undoing last import: ".mysql_error());
			$count++;
		}
		return $count;
	}

	/**
	 * Transform the value while exporting
	 */
	function transform_export_value($key, $value) {
		return parent::transform_export_value($key, $value);
	}

	/**
	 * Function which will set the assigned user id for import record.
	 */
	function set_import_assigned_user()
	{
		global $current_user, $adb,$table_prefix;
		$record_user = $this->column_fields["assigned_user_id"];

		if($record_user != $current_user->id){
			$sqlresult = $adb->pquery("select id from {$table_prefix}_users where id = ? union select groupid as id from {$table_prefix}_groups where groupid = ?", array($record_user, $record_user));
			if($this->db->num_rows($sqlresult)!= 1) {
				$this->column_fields["assigned_user_id"] = $current_user->id;
			} else {
				$row = $adb->fetchByAssoc($sqlresult, -1, false);
				if (isset($row['id']) && $row['id'] != -1) {
					$this->column_fields["assigned_user_id"] = $row['id'];
				} else {
					$this->column_fields["assigned_user_id"] = $current_user->id;
				}
			}
		}
	}

	/**
	 * Function which will give the basic query to find duplicates
	 */
	function getDuplicatesQuery($module,$table_cols,$field_values,$ui_type_arr,$select_cols='') {
		global $table_prefix;
		$select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, {$table_prefix}_users_last_import.deleted,".$table_cols;

		// Select Custom Field Table Columns if present
		if(isset($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$from_clause = " FROM $this->table_name";

		$from_clause .= "	INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = $this->table_name.$this->table_index";

		// Consider custom table join as well.
		if(isset($this->customFieldTable)) {
			$from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index";
		}
		$from_clause .= " LEFT JOIN {$table_prefix}_users ON {$table_prefix}_users.id = {$table_prefix}_crmentity.smownerid
						LEFT JOIN {$table_prefix}_groups ON {$table_prefix}_groups.groupid = {$table_prefix}_crmentity.smownerid";

		$where_clause = "	WHERE {$table_prefix}_crmentity.deleted = 0";
		$where_clause .= $this->getListViewSecurityParameter($module);

		if (isset($select_cols) && trim($select_cols) != '') {
			$sub_query = "SELECT $select_cols FROM  $this->table_name AS t " .
				" INNER JOIN {$table_prefix}_crmentity AS crm ON crm.crmid = t.".$this->table_index;
			// Consider custom table join as well.
			if(isset($this->customFieldTable)) {
				$sub_query .= " INNER JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
			}
			$sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";
		} else {
			$sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
		}

		$query = $select_clause . $from_clause .
					" LEFT JOIN {$table_prefix}_users_last_import ON {$table_prefix}_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
					" INNER JOIN (" . $sub_query . ") temp ON ".get_on_clause($field_values,$ui_type_arr,$module) .
					$where_clause .
					" ORDER BY $table_cols,". $this->table_name .".".$this->table_index ." ASC";

		return $query;
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		global $adb,$table_prefix;
		if($event_type == 'module.postinstall') {
			
			$messagesModule = Vtiger_Module::getInstance($modulename);
			$msgId = $messagesModule->id;

			$adb->pquery("UPDATE {$table_prefix}_tab SET customized=0 WHERE name=?", array($modulename));
			
			//crmv@61173
			$adb->pquery("insert into {$table_prefix}_org_share_action2tab values(?,?)",array(8,$msgId));
			$adb->pquery("delete from {$table_prefix}_org_share_action2tab where tabid = ? and share_action_id in (1,2)",array($msgId));
			//crmv@61173e

			//crmv@29617
			$result = $adb->pquery('SELECT isentitytype FROM '.$table_prefix.'_tab WHERE name = ?',array($modulename));
			if ($result && $adb->num_rows($result) > 0 && $adb->query_result($result,0,'isentitytype') == '1') {
				$ModCommentsFocus = CRMEntity::getInstance('ModComments');
				$ModCommentsFocus->addWidgetTo($modulename);
			}
			//crmv@29617e

			// add related
			$excludemods = array(
				'ModNotifications', 'ModComments', 'ChangeLog',
				'Emails', 'Sms', 'Fax', 'Newsletter', 'Messages', 'PBXManager', 'Charts', 'Faq', 'Projects',
			);
			$res = $adb->pquery("select {$table_prefix}_tab.name from {$table_prefix}_tab left join {$table_prefix}_relatedlists rl on rl.tabid = {$table_prefix}_tab.tabid and rl.related_tabid = ? where isentitytype = 1 and rl.tabid is null and {$table_prefix}_tab.name not in (".generateQuestionMarks($excludemods).")", array($msgId, $excludemods));
			if ($res) {
				while ($row = $adb->FetchByAssoc($res, -1, false)) {
					$lmod = $row['name'];
					$modInst = Vtiger_Module::getInstance($lmod);
					$modInst->setRelatedList($messagesModule, $modulename, Array('ADD'), 'get_messages_list');
				}
			}

			// edit quick create fields
			/*
			 * 1 : not active
			 * 0 : active
			 */
			$qcreate = array(
				'HelpDesk' => array(
					'description' => 1,
				),
			);
			foreach ($qcreate as $mod=>$qlist) {
				foreach ($qlist as $fname=>$qval) {
					$adb->pquery("update {$table_prefix}_field set quickcreate = ? where fieldname = ? and tabid = ?", array($qval, $fname, getTabid($mod)));
				}
			}
			/* crmv@124735
			$adb->query("update tbl_s_menu_modules set sequence = sequence + 1 where fast = 1 and sequence > 1");
			$adb->pquery('insert into tbl_s_menu_modules (tabid,fast,sequence) values (?,?,?)',array($messagesModule->id,1,2));
			*/
			$messagesModule->hide(array('hide_report'=>1)); // crmv@38798

			// crmv@42264	crmv@49395	crmv@51862
			// now setup cronjobs
			if (Vtiger_Utils::CheckTable($table_prefix.'_cronjobs')) {
				require_once('include/utils/CronUtils.php');
				$CU = CronUtils::getInstance();
				
				$cj = new CronJob();
				$cj->name = 'MessagesPop3';
				$cj->active = 1;
				$cj->singleRun = false;
				$cj->fileName = 'cron/modules/Messages/Pop3.service.php';
				$cj->timeout = 300;		// 5min timeout
				$cj->repeat = 900;		// run every 15 min
				$CU->insertCronJob($cj);

				$cj = new CronJob();
				$cj->name = 'MessagesUids';
				$cj->active = 1;
				$cj->singleRun = false;
				$cj->fileName = 'cron/modules/Messages/MessagesUids.service.php';
				$cj->timeout = 1800;
				$cj->repeat = 600;
				$cj->maxAttempts = 2147483647;
				$CU->insertCronJob($cj);
				
				$cj = new CronJob();
				$cj->name = 'Messages';
				$cj->active = 1;
				$cj->singleRun = false;
				$cj->fileName = 'cron/modules/Messages/Messages.service.php';
				$cj->timeout = 600;
				$cj->repeat = 60;
				$cj->maxAttempts = 2147483647;
				$CU->insertCronJob($cj);

				$cj = new CronJob();
				$cj->name = 'MessagesInboxUids';
				$cj->active = 1;
				$cj->singleRun = false;
				$cj->fileName = 'cron/modules/Messages/InboxUids.service.php';
				$cj->timeout = 600;
				$cj->repeat = 60;
				$cj->maxAttempts = 2147483647;
				$CU->insertCronJob($cj);
				
				$cj = new CronJob();
				$cj->name = 'MessagesInbox';
				$cj->active = 1;
				$cj->singleRun = false;
				$cj->fileName = 'cron/modules/Messages/Inbox.service.php';
				$cj->timeout = 600;
				$cj->repeat = 60;
				$cj->maxAttempts = 2147483647;
				$CU->insertCronJob($cj);
				
				$cj = new CronJob();
				$cj->name = 'MessagesPropagateToImap';
				$cj->active = 1;
				$cj->singleRun = false;
				$cj->fileName = 'cron/modules/Messages/PropagateToImap.service.php';
				$cj->timeout = 300;			// 5 min timeout
				$cj->repeat = 60;			// run every 1 min
				$cj->maxAttempts = 2147483647;
				$CU->insertCronJob($cj);
				
				$cj = new CronJob();
				$cj->name = 'MessagesSend';
				$cj->active = 1;
				$cj->singleRun = false;
				$cj->fileName = 'cron/modules/Messages/SendMessages.service.php';
				$cj->timeout = 300;			// 5 min timeout
				$cj->repeat = 60;			// run every 1 min
				$cj->maxAttempts = 2147483647;
				$CU->insertCronJob($cj);
				
				$cj = new CronJob();
				$cj->name = 'MessagesSyncFolders';
				$cj->active = 1;
				$cj->singleRun = false;
				$cj->fileName = 'cron/modules/Messages/SyncFolders.service.php';
				$cj->timeout = 600;
				$cj->repeat = 120;
				$cj->maxAttempts = 2147483647;
				$CU->insertCronJob($cj);
				
				$cj = new CronJob();
				$cj->name = 'MessagesAllUids';
				$cj->active = 1;
				$cj->singleRun = false;
				$cj->fileName = 'cron/modules/Messages/AllUids.service.php';
				$cj->timeout = 5400;
				$cj->repeat = 600;
				$CU->insertCronJob($cj);
			}
			// crmv@42264e	crmv@49395e	crmv@51862e

			// crmv@85493 not needed anymore
			/*
			$idxs_messages = array_keys($adb->database->MetaIndexes($table_prefix.'_messages'));
			$indexes = array(
				array("{$table_prefix}_messages", "{$table_prefix}_messages_adoptchildren", 'folder, mreferences(200)'),
				array("{$table_prefix}_messages", "{$table_prefix}_messages_referencechildren_idx", 'mdate, folder, mreferences(200)'),
			);
			foreach($indexes as $index) {
				if (!in_array($index[1], $idxs_messages)) {
					$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL($index[1], $index[0], $index[2]));
				}
			}
			*/

		} else if($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} else if($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}

	function trash($module, $id) {
		global $adb, $table_prefix;
		$adb->pquery("delete from {$table_prefix}_messages_recipients where messagesid = ?",array($id));
		if ($this->column_fields['mtype'] == 'Webmail') {
			$this->beforeTrashFunctions($id);
		}
		parent::trash($module, $id);
	}

	function getFixedOrderBy($module,$order_by,$sorder){
		$tablename = getTableNameForField($module, $order_by);
		$tablename = ($tablename != '')? ($tablename . '.') : '';
		return  ' ORDER BY ' . $tablename . $order_by . ' ' . $sorder;
	}
}