<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ************************************************************************************/

require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class ChangeLog extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name;
	var $table_index= 'changelogid';
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
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Nr. revisione'=> Array('changelog', 'audit_no'),
		'Utente'=> Array('changelog', 'user_name'),
		'Data di modifica'=> Array('changelog', 'modified_date'),
		'Elementi cambiati'=> Array('crmentity', 'description'),

		//'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Nr. revisione'=> 'audit_no',
		'Utente'=> 'user_name',
		'Data di modifica'=> 'modified_date',
		'Elementi cambiati'=> 'description',

//		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'audit_no';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Name'=> Array('changelog', 'audit_no')
	);
	var $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Name'=> 'audit_no'
	);

	// For Popup window record selection
	var $popup_fields = Array('audit_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'audit_no';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'audit_no';

	// Required Information for enabling Import feature
	var $required_fields = Array('audit_no'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'audit_no';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'audit_no');
	
	var $search_base_field = 'audit_no';	//crmv@10759

	// crmv@109801	
	// fields to jump in every module
	public $fields_to_jump = array();
	
	//fields to jump in specific modules
	public $fields_to_jump_module = array(
		'HelpDesk' => array('modifiedtime','time_elapsed','time_remaining','start_sla','end_sla','time_refresh','time_change_status','time_elapsed_change_status','ended_sla','time_elapsed_idle','time_elapsed_out_sla'),
		'Events' => array('duration_hours','duration_minutes'),
		'MorphsuitServer' => array('morphsuit_key','morphsuit_new_key'),
	);
	
	// fields to be saved, but not shown in the list of changes (global)
	public $fields_to_hide = array();
	
	// fields to be saved, but not shown in the list of changes (per module)
	public $fields_to_hide_module = array();
	// crmv@109801e

	function ChangeLog() {
		global $log, $currentModule;
		global $table_prefix;
		parent::__construct(); // crmv@37004
		$this->table_name = $table_prefix.'_changelog';
		$this->customFieldTable = Array($table_prefix.'_changelogcf', 'changelogid');
		$this->tab_name = Array($table_prefix.'_crmentity', $table_prefix.'_changelog', $table_prefix.'_changelogcf');
		$this->tab_name_index = Array(
			$table_prefix.'_crmentity' => 'crmid',
			$table_prefix.'_changelog'   => 'changelogid',
		    $table_prefix.'_changelogcf' => 'changelogid');
		$this->column_fields = getColumnFields($currentModule);
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
	}	

	function save_module($module) {
	}

	// crmv@109801
	public function isFieldSkipped($module, $fieldname, $uitype = 1) {
		return 
			in_array($fieldname, $this->fields_to_jump)
			|| (is_array($this->fields_to_jump_module[$module]) && in_array($fieldname,$this->fields_to_jump_module[$module]))
			|| $uitype == 208;
	}
	
	public function isFieldHidden($module, $fieldname) {
		return in_array($fieldname, $this->fields_to_hide) || 
			(is_array($this->fields_to_hide_module[$module]) && in_array($fieldname,$this->fields_to_hide_module[$module]));
	}
	// crmv@109801e

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	function getQueryByModuleField($module, $fieldname, $srcrecord) {
		// $srcrecord could be empty
	}

	/**
	 * Get list view query (send more WHERE clause condition if required)
	 */
	function getListQuery($module, $where='') {
		global $current_user;
		global $table_prefix;
		$query = "SELECT * ";

		// Select Custom Field Table Columns if present
//		if(!empty($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$query .= " FROM $this->table_name";

		$query .= "	INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = $this->table_name.$this->table_index";

		// Consider custom table join as well.
		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index"; 
		}
		$query .= " LEFT JOIN ".$table_prefix."_users ON ".$table_prefix."_users.id = ".$table_prefix."_crmentity.smownerid";
		$query .= " LEFT JOIN ".$table_prefix."_groups ON ".$table_prefix."_groups.groupid = ".$table_prefix."_crmentity.smownerid";

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM ".$table_prefix."_field" .
				" INNER JOIN ".$table_prefix."_fieldmodulerel ON ".$table_prefix."_fieldmodulerel.fieldid = ".$table_prefix."_field.fieldid" .
				" WHERE uitype='10' AND ".$table_prefix."_fieldmodulerel.module=?", array($module));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);
		
		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');
			
			$other = CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);
			
			$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
		}
		$query .= $this->getNonAdminAccessControlQuery($module,$current_user);
		$query .= "	WHERE ".$table_prefix."_crmentity.deleted = 0 ".$where;
		$query = $this->listQueryNonAdminChange($query, $module);	
		return $query;
	}

	/**
	 * Create query to export the records.
	 */
	function create_export_query($where,$oCustomView,$viewId)	//crmv@31775
	{
		global $current_user;
		global $table_prefix;
		$thismodule = $_REQUEST['module'];
		
		include_once("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery($thismodule, "detail_view");
		
		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, ".$table_prefix."_users.user_name AS user_name 
					FROM ".$table_prefix."_crmentity INNER JOIN $this->table_name ON ".$table_prefix."_crmentity.crmid=$this->table_name.$this->table_index";

		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index"; 
		}

		$query .= " LEFT JOIN ".$table_prefix."_groups ON ".$table_prefix."_groups.groupid = ".$table_prefix."_crmentity.smownerid";
		$query .= " LEFT JOIN ".$table_prefix."_users ON ".$table_prefix."_crmentity.smownerid = ".$table_prefix."_users.id and ".$table_prefix."_users.status='Active'";
		
		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM ".$table_prefix."_field" .
				" INNER JOIN ".$table_prefix."_fieldmodulerel ON ".$table_prefix."_fieldmodulerel.fieldid = ".$table_prefix."_field.fieldid" .
				" WHERE uitype='10' AND ".$table_prefix."_fieldmodulerel.module=?", array($thismodule));
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

		$where_auto = " ".$table_prefix."_crmentity.deleted=0";

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
		global $current_user;
		global $table_prefix;
		$query = "SELECT ".$table_prefix."_crmentity.crmid, case when (".$table_prefix."_users.user_name is not null) then ".$table_prefix."_users.user_name else ".$table_prefix."_groups.groupname end as user_name, $this->table_name.* FROM $this->table_name
			INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = $this->table_name.$this->table_index
			LEFT JOIN ".$table_prefix."_users_last_import ON ".$table_prefix."_users_last_import.bean_id=".$table_prefix."_crmentity.crmid
			LEFT JOIN ".$table_prefix."_users ON ".$table_prefix."_users.id = ".$table_prefix."_crmentity.smownerid
			LEFT JOIN ".$table_prefix."_groups ON ".$table_prefix."_groups.groupid = ".$table_prefix."_crmentity.smownerid
			WHERE ".$table_prefix."_users_last_import.assigned_user_id='$current_user->id'
			AND ".$table_prefix."_users_last_import.bean_type='$module'
			AND ".$table_prefix."_users_last_import.deleted=0";
		return $query;
	}

	/**
	 * Delete the last imported records.
	 */
	function undo_import($module, $user_id) {
		global $adb;
		global $table_prefix;
		$count = 0;
		$query1 = "select bean_id from ".$table_prefix."_users_last_import where assigned_user_id=? AND bean_type='$module' AND deleted=0";
		$result1 = $adb->pquery($query1, array($user_id)) or die("Error getting last import for undo: ".mysql_error()); 
		while ( $row1 = $adb->fetchByAssoc($result1))
		{
			$query2 = "update ".$table_prefix."_crmentity set deleted=1 where crmid=?";
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
		global $current_user, $adb;
		global $table_prefix;
		$record_user = $this->column_fields["assigned_user_id"];
		
		if($record_user != $current_user->id){
			$sqlresult = $adb->pquery("select id from ".$table_prefix."_users where id = ? union select groupid as id from ".$table_prefix."_groups where groupid = ?", array($record_user, $record_user));
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
		$select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, ".$table_prefix."_users_last_import.deleted,".$table_cols;

		// Select Custom Field Table Columns if present
		if(isset($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$from_clause = " FROM $this->table_name";

		$from_clause .= "	INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = $this->table_name.$this->table_index";

		// Consider custom table join as well.
		if(isset($this->customFieldTable)) {
			$from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index"; 
		}
		$from_clause .= " LEFT JOIN ".$table_prefix."_users ON ".$table_prefix."_users.id = ".$table_prefix."_crmentity.smownerid
						LEFT JOIN ".$table_prefix."_groups ON ".$table_prefix."_groups.groupid = ".$table_prefix."_crmentity.smownerid";
		
		$where_clause = "	WHERE ".$table_prefix."_crmentity.deleted = 0";
		$where_clause .= $this->getListViewSecurityParameter($module);
					
		if (isset($select_cols) && trim($select_cols) != '') {
			$sub_query = "SELECT $select_cols FROM  $this->table_name AS t " .
				" INNER JOIN ".$table_prefix."_crmentity AS crm ON crm.crmid = t.".$this->table_index;
			// Consider custom table join as well.
			if(isset($this->customFieldTable)) {
				$sub_query .= " INNER JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
			}
			$sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";	
		} else {
			$sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
		}
		
		$query = $select_clause . $from_clause .
					" LEFT JOIN ".$table_prefix."_users_last_import ON ".$table_prefix."_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
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
		if($event_type == 'module.postinstall') {
			global $table_prefix;
			global $adb;
			$moduleInstance = Vtiger_Module::getInstance($modulename);
			$moduleInstance->hide(array('hide_module_manager'=>1,'hide_profile'=>1,'hide_report'=>1));
			$adb->pquery("UPDATE {$table_prefix}_def_org_share SET editstatus = ? WHERE tabid = ?",array(2,$moduleInstance->id));
			
			$adb->pquery('UPDATE '.$table_prefix.'_tab SET customized=0 WHERE name=?', array($modulename));
						
			$em = new VTEventsManager($adb);
			$em->registerHandler('history_first', 'modules/ChangeLog/ChangeLogHandler.php', 'ChangeLogHandler');
			$em->registerHandler('history_last', 'modules/ChangeLog/ChangeLogHandler.php', 'ChangeLogHandler');
			
			self::enableWidgetToAll();
			
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

	static function addWidgetTo($moduleNames, $widgetType='DETAILVIEWWIDGET', $widgetName='DetailViewBlockChangeLogWidget') {
		if (empty($moduleNames)) return;
		
		include_once 'vtlib/Vtiger/Module.php';
		
		if (is_string($moduleNames)) $moduleNames = array($moduleNames);
		
		$commentWidgetCount = 0; 
		foreach($moduleNames as $moduleName) {
			$module = Vtiger_Module::getInstance($moduleName);
			if($module) {
				$module->addLink($widgetType, $widgetName, "block://ChangeLog:modules/ChangeLog/ChangeLog.php");
				++$commentWidgetCount;
			}
		}
		
	}
	
	function enableWidgetToAll() {
		global $adb;
		global $table_prefix;
		$skip_modules = array('Emails','Fax','Sms','Events','ModComments','ChangeLog','Targets');
		$result = $adb->pquery('SELECT name FROM '.$table_prefix.'_tab WHERE isentitytype = 1 AND name NOT IN ('.generateQuestionMarks($skip_modules).')',$skip_modules);
		if ($result && $adb->num_rows($result) > 0) {
			unset($_SESSION['ChangeLogModules']);
			$modules = array();
			while($row=$adb->fetchByAssoc($result)) {
				if (!self::isEnabled($row['name'])) {
					$modules[] = $row['name'];
				}
			}
			if (!empty($modules)) {
				self::enableWidget($modules);
			}
		}
	}

	function disableRelatedForAll() {
		global $adb, $table_prefix;
		$changelogModule = Vtiger_Module::getInstance('ChangeLog');
		$skip_modules = array('Emails','Fax','Sms','Events','ModComments','ChangeLog','Targets');
		$result = $adb->pquery('SELECT name FROM '.$table_prefix.'_tab WHERE isentitytype = 1 AND name NOT IN ('.generateQuestionMarks($skip_modules).')',$skip_modules);
		if ($result && $adb->num_rows($result) > 0) {
			$modules = array();
			while($row=$adb->fetchByAssoc($result)) {
				$relModule = Vtiger_Module::getInstance($row['name']);
				if ($relModule) {
					$relModule->unsetRelatedList($changelogModule,'ChangeLog','get_changelog_list');
				}
			}

		}
	}
	
	function disableWidgetToAll() {
		global $adb;
		global $table_prefix;
		$skip_modules = array('Emails','Fax','Sms','Events','ModComments','ChangeLog','Targets');
		$result = $adb->pquery('SELECT name FROM '.$table_prefix.'_tab WHERE isentitytype = 1 AND name NOT IN ('.generateQuestionMarks($skip_modules).')',$skip_modules);
		if ($result && $adb->num_rows($result) > 0) {
			unset($_SESSION['ChangeLogModules']);
			$modules = array();
			while($row=$adb->fetchByAssoc($result)) {
				if (self::isEnabled($row['name'])) {
					$modules[] = $row['name'];
				}
			}
			if (!empty($modules)) {
				self::disableWidget($modules);
			}
		}
	}
	
	function enableWidget($moduleNames){
		global $adb, $table_prefix;
		$changelogModule = Vtiger_Module::getInstance('ChangeLog');
		if(is_array($moduleNames)){
			foreach($moduleNames as $module){
				$relModule = Vtiger_Module::getInstance($module);
				$result = $adb->pquery("select * from {$table_prefix}_relatedlists where tabid = ? and related_tabid = ?",array($relModule->id,$changelogModule->id));
				if (!$adb->num_rows($result)) {
					//$relModule->setRelatedList($changelogModule,'ChangeLog',Array(),'get_changelog_list');
					$fieldInstance = Vtiger_Field::getInstance('parent_id', $changelogModule);
					$fieldInstance->setRelatedModules(array($module));
				}
			}
		}else{
			if($moduleNames !=''){
				$relModule = Vtiger_Module::getInstance($moduleNames);
				$result = $adb->pquery("select * from {$table_prefix}_relatedlists where tabid = ? and related_tabid = ?",array($relModule->id,$changelogModule->id));
				if (!$adb->num_rows($result)) {
					//$relModule->setRelatedList($changelogModule,'ChangeLog',Array(),'get_changelog_list');
					$fieldInstance = Vtiger_Field::getInstance('parent_id', $changelogModule);
					$fieldInstance->setRelatedModules(array($moduleNames));
				}
			}
		}
		unset($_SESSION['ChangeLogModules']);
	}
	
	function disableWidget($moduleNames){
		
		$changelogModule = Vtiger_Module::getInstance('ChangeLog');
		if(is_array($moduleNames)){
			foreach($moduleNames as $module){
				//$relModule = Vtiger_Module::getInstance($module);
				//$relModule->unsetRelatedList($changelogModule,'ChangeLog','get_changelog_list');
				
				$fieldInstance = Vtiger_Field::getInstance('parent_id', $changelogModule);
				$fieldInstance->unsetRelatedModules(array($module));
			}
		}else{
			if($moduleNames !=''){
				//$relModule = Vtiger_Module::getInstance($moduleNames);
				//$relModule->unsetRelatedList($changelogModule,'ChangeLog','get_changelog_list');
				
				$fieldInstance = Vtiger_Field::getInstance('parent_id', $changelogModule);
				$fieldInstance->unsetRelatedModules(array($moduleNames));
			}
		}
		unset($_SESSION['ChangeLogModules']);
	}
	
	function isEnabled($module){
		global $adb, $table_prefix;
		if(!is_array($_SESSION['ChangeLogModules'])){
			$changelogModule = Vtiger_Module::getInstance('ChangeLog');
			$fieldInstance = Vtiger_Field::getInstance('parent_id', $changelogModule);
			$res = $adb->pquery("SELECT relmodule FROM {$table_prefix}_fieldmodulerel WHERE fieldid=? AND module=?", array($fieldInstance->id, 'ChangeLog'));
			unset($_SESSION['ChangeLogModules']);
			$_SESSION['ChangeLogModules'] = array();
			while($row = $adb->fetchByAssoc($res)){
				$_SESSION['ChangeLogModules'][] = $row['relmodule'];
			}
		}
		if(in_array($module, $_SESSION['ChangeLogModules'])){
			return true;
		}else{
			return false;
		}
	}
	
	function getWidget($label){
		return new ChangeLog();
	}
	
	function process($context = false) {
		global $adb, $table_prefix, $currentModule; // crmv@105520
		include_once('include/utils/utils.php');
		
		$this->context = $context;
		$record = $_REQUEST['record'];
		
		$query = "SELECT description FROM ".$table_prefix."_crmentity WHERE deleted = 0 AND crmid = ".$record;
		$res = $adb->query($query);
		$description = $adb->query_result_no_html($res,$i,"description");
		
		$html = '';
		$html .= '<table class="small" width="100%" border="0" cellspacing="0" cellpadding="0">';
		$html .= '<tr><td class="dvInnerHeader"><b>'.getTranslatedString('Modified fields','ChangeLog').'</b></td></tr></table>';
		$html .= $this->getFieldsTable($description, $currentModule);
		
		return $html;
	}
	
	// crmv@31780	crmv@53684	crmv@57348	crmv@104566
	function getFieldsTable($description, $module, $nohtml=false, &$log_type=''){
		global $app_strings;

		$html = '';
		$ret = array();
		$description_elements = Zend_Json::decode($description);
		if(is_array($description_elements)) {
			if ($description_elements[0] == 'GenericChangeLog') {
				$log_type = 'generic';
				($nohtml) ? $ret = $description_elements[1] : $html .= $description_elements[1];
			} elseif ($description_elements[0] == 'ChangeLogCreation') {
				$log_type = 'create';
			} elseif ($description_elements[0] == 'ChangeLogRemoveRelation1N') {
				$log_type = 'remove_relation';
				$record1 = $description_elements[1];
				$module1 = $description_elements[2];
				if (isPermitted($module1,'DetailView',$record1) !== 'no') {
					$name1 = getEntityName($module1,array($record1));
					if ($nohtml) {
						$ret = array(
							'record1' => $record1,
							'module1' => $module1,
						);
					} else {
						$html = sprintf(getTranslatedString('LBL_HAS_REMOVED_LINK_WITH_RECORD','ChangeLog'), "<a href='{$match1[1]}'>{$name1[$record1]}</a> (".getSingleModuleName($module1).")");
					}
				} else {
					if ($nohtml) {
						$ret = false;
					} else {
						$html = "<font color='red'>".getTranslatedString('LBL_NOT_ACCESSIBLE')."</font>";
					}
				}
			} elseif ($description_elements[0] == 'ModNotification_Relation' || $description_elements[0] == 'ChangeLogRelationNN' || $description_elements[0] == 'ChangeLogRelation1N') {
				$log_type = 'relation';
				if ($description_elements[0] == 'ModNotification_Relation') {
					$tmp = $description_elements[1];
					$tmp = explode(' LBL_LINKED_TO ',$tmp);
	
					$tmp1 = substr($tmp[0],0,strpos($tmp[0],' ('));
					$url1 = preg_match("/<a href='(.+)'>/", $tmp1, $match1);
					$info1 = parse_url($match1[1]);
					parse_str($info1['query'], $info1);
					$module1 = $info1['module'];
					$record1 = $info1['record'];
					
					$tmp2 = substr($tmp[1],0,strpos($tmp[1],' ('));
					$url2 = preg_match("/<a href='(.+)'>/", $tmp2, $match2);
					$info2 = parse_url($match2[1]);
					parse_str($info2['query'], $info2);
					$module2 = $info2['module'];
					$record2 = $info2['record'];
				} else {
					$record1 = $description_elements[1];
					$module1 = $description_elements[2];
					$record2 = $description_elements[3];
					$module2 = $description_elements[4];
				}					
				if (isPermitted($module1,'DetailView',$record1) !== 'no' && isPermitted($module2,'DetailView',$record2) !== 'no') {
					$name1 = getEntityName($module1,array($record1));
					$name2 = getEntityName($module2,array($record2));
					if ($nohtml) {
						$ret = array(
							'record1' => $record1,
							'module1' => $module1,
							'record2' => $record2,
							'module2' => $module2,
						);
					} else {
						$html = "<a href='{$match1[1]}'>{$name1[$record1]}</a> (".getSingleModuleName($module1).") ".getTranslatedString('LBL_LINKED_TO','ChangeLog')." <a href='{$match2[1]}'>{$name2[$record2]}</a> (".getSingleModuleName($module2).")";
					}
				} else {
					if ($nohtml) {
						$ret = false;
					} else {
						$html = "<font color='red'>".getTranslatedString('LBL_NOT_ACCESSIBLE')."</font>";
					}
				}
			} else {
				$log_type = 'edit';
				if (!$nohtml) {
					$html .= '<table class="table">';
					$html .= '<tr>
							 	<td style="width: 33%;"><b>'.getTranslatedString('Field','ChangeLog').'</b></td>
							    <td style="width: 33%;"><b>'.getTranslatedString('Earlier value','ChangeLog').'</b></td>
							    <td style="width: 33%;"><b>'.getTranslatedString('Actual value','ChangeLog').'</b></td>
							  </tr>';
				}
				// crmv@109801
				$rowsAdded = 0;
				foreach($description_elements as $value){
					if ($value[3] && $this->isFieldHidden($module, $value[3])) continue;
					$previous_value = $current_value = '';
					if ($current_user->is_admin == 'on' || getFieldVisibilityPermission($module, $current_user->id, $value[3]) == '0') { // crmv@107449 crmv@108128
						if(isset($value[4]) && $value[4] == 'boolean'){
							if($value[1] == 1){
								$previous_value = $app_strings['yes'];
							}else{
								$previous_value = $app_strings['no'];
							}
							if($value[2] == 1){
								$current_value = $app_strings['yes'];
							}else{
								$current_value = $app_strings['no'];
							}
						}elseif(isset($value[4]) && $value[4] == 'reference'){
							//previous value
							if($value[1] != '' && $value[1] !='0'){
								if ($value[3] == 'folderid') {
									$entityFolder = getEntityFolder($value[1]);
									if (!empty($entityFolder['foldername'])) $previous_value = $entityFolder['foldername'];
								} else {
									$relation_previuos_module = getSalesEntityType($value[1]);
									$tmp = getEntityName($relation_previuos_module,array($value[1]));
									$previous_value = $tmp[$value[1]];
									if (!empty($previous_value)) {
										$previous_value = '<a href="index.php?module='.$relation_previuos_module.'&action=DetailView&record='.$value[1].'">'.getModuleImg($relation_previuos_module).' '.$previous_value.'</a>';
									}
								}
								if (empty($previous_value)) $previous_value = getTranslatedString('LBL_RECORD_DELETE');
							}
							//current value
							if($value[2] != '' && $value[2] !='0'){
								if ($value[3] == 'folderid') {
									$entityFolder = getEntityFolder($value[2]);
									if (!empty($entityFolder['foldername'])) $current_value = $entityFolder['foldername'];
								} else {
									$relation_current_module = getSalesEntityType($value[2]);
									$tmp = getEntityName($relation_current_module,array($value[2]));
									$current_value = $tmp[$value[2]];
									if (!empty($current_value)) {
										$current_value = '<a href="index.php?module='.$relation_current_module.'&action=DetailView&record='.$value[2].'">'.getModuleImg($relation_current_module).' '.$current_value.'</a>';
									}
								}
								if (empty($current_value)) $current_value = getTranslatedString('LBL_RECORD_DELETE');
							}
						}elseif(isset($value[4]) && $value[4] == 'owner'){
							if (is_numeric($value[2])) {
								$group_name = getGroupName($value[1]);
								($group_name['0'] !='') ? $previous_value = $group_name['0'] : $previous_value = getUserName($value[1],true);
								$group_name = getGroupName($value[2]);
								($group_name['0'] !='') ? $current_value = $group_name['0'] : $current_value = getUserName($value[2],true);
							} else {
								$previous_value = getTranslatedString($value[1], $module);
								$current_value = getTranslatedString($value[2], $module);
							}
						}else{
							$previous_value = getTranslatedString($value[1], $module);
							$current_value = getTranslatedString($value[2], $module);
						}
					} else {
						$previous_value = "<font color='red'>".getTranslatedString('LBL_NOT_ACCESSIBLE')."</font>";
						$current_value = "<font color='red'>".getTranslatedString('LBL_NOT_ACCESSIBLE')."</font>";
					}
					// crmv@107449e
					if ($nohtml) {
						$ret[] = array(
							'fieldname' => $value[0],
							'fieldname_trans' => getTranslatedString($value[0], $module),
							'previous' => $previous_value,
							'current' => $current_value,
						);
					} else {
						$html .= '<tr>
								    <td>'.getTranslatedString($value[0], $module).'</td>
								    <td>'.$previous_value.'</td>
							    	<td>'.$current_value.'</td>
							  	</tr>';
					}
					++$rowsAdded;
				}
				if (!$nohtml) $html .= '</table>';
				if ($rowsAdded == 0) $html = '';
				// crmv@109801e
			}
		}
		if ($nohtml) {
			return $ret;
		} else {
			return $html;
		}
	}
	// crmv@31780e	crmv@53684e	crmv@57348e	crmv@104566e
	
	//crmv@103534
	function get_revision_id($id){
		global $adb,$current_user;
		global $table_prefix;
		$current_user_backup = $current_user->id;
		$current_user->id = 1;
		$last_autid_no = 1.0;
		$sql = "SELECT audit_no FROM ".$table_prefix."_changelog 
			INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = ".$table_prefix."_changelog.changelogid 
			INNER JOIN ".$table_prefix."_changelogcf ON ".$table_prefix."_changelogcf.changelogid = ".$table_prefix."_changelog.changelogid 
			LEFT JOIN ".$table_prefix."_users ON ".$table_prefix."_users.id = ".$table_prefix."_crmentity.smownerid 
			LEFT JOIN ".$table_prefix."_groups ON ".$table_prefix."_groups.groupid = ".$table_prefix."_crmentity.smownerid 
			WHERE ".$table_prefix."_changelog.changelogid > 0 AND ".$table_prefix."_crmentity.deleted = 0 AND ".$table_prefix."_changelog.parent_id= ? ORDER BY audit_no DESC";
		$res = $adb->pquery($sql,Array($id));
		if ($res && $adb->num_rows($res) > 0){
			$last_autid_no = (float)$adb->query_result_no_html($res,0,'audit_no')+0.1;
		}
		$current_user->id = $current_user_backup;
		return $last_autid_no;
	}
	//crmv@103534e
	
	//crmv@104566
	function get_history_query($module, $record) {
		global $adb, $table_prefix, $currentModule, $onlyquery;
		if ($module == 'Events') $module = 'Calendar';
		$tmp_currentModule = $currentModule;
		$currentModule = $module;
		$onlyquery = true;
		$focus = CRMEntity::getInstance($module);
		$focus->get_changelog_list($record, getTabid($module), getTabid('ChangeLog'), false);
		$currentModule = $tmp_currentModule;
		$query = $_SESSION[strtolower($currentModule)."_listquery"];
		if(strripos($query,'ORDER BY') > 0) $query = substr($query, 0, strripos($query,'ORDER BY'));
		$query .= ' ORDER BY crmid DESC ';
		// TODO limit and pagination
		$query_result = $adb->query($query);
		if ($query_result && $adb->num_rows($query_result) > 0) {
			return $query_result;
		} else {
			return false;
		}
	}
	function get_history_log($module, $record, $query_result, $format='array') {
		global $adb;
		if (!$query_result || $adb->num_rows($query_result) == 0) return false;
		
		$return = array();
		while($row=$adb->fetchByAssoc($query_result)) {
			$log_type = '';
			$log_text = '&nbsp;';
			$log_info = $this->getFieldsTable($row['description'], $module, true, $log_type); // crmv@105520
			if ($log_type == 'generic') {
				$log_text = $log_info;
			} elseif ($log_type == 'create') {
				$log_text = getTranslatedString('LBL_HAS_CREATED_THE_RECORD','ChangeLog');
			} elseif ($log_type == 'edit') {
				if (is_array($log_info) && count($log_info) == 0) continue; // crmv@109801
				$log_text = getTranslatedString('LBL_HAS_CHANGED_THE_RECORD','ChangeLog');
			} elseif ($log_type == 'remove_relation') {
				$log_text = sprintf(getTranslatedString('LBL_HAS_REMOVED_LINK_WITH_RECORD','ChangeLog'), '<a href="index.php?module='.$log_info['module1'].'&action=DetailView&record='.$log_info['record1'].'">'.getEntityName($log_info['module1'],$log_info['record1'],true).'</a>');
			} elseif ($log_type == 'relation') {
				$log_text = sprintf(getTranslatedString('LBL_HAS_LINKED_THE_RECORD','ChangeLog'), '<a href="index.php?module='.$log_info['module1'].'&action=DetailView&record='.$log_info['record1'].'">'.getEntityName($log_info['module1'],$log_info['record1'],true).'</a>');
			}
			$return[] = array(
				'crmid' => $row['crmid'],
				'version' => $row['audit_no'],
				'user' => array(
					'id'=>$row['smownerid'],
					'user_name'=>$row['user_name'],
					'full_name'=>getUserFullName($row['smownerid']),
					'img'=>getUserAvatar($row['smownerid']),
				),
				'date' => array(
					'db' => $row['modified_date'],
					'formatted' => getDisplayDate($row['modified_date']),
					'friendly' => getFriendlyDate($row['modified_date']),
				),
				'log' => array(
					'type'=>$log_type,
					'text'=>$log_text,
					'info'=>$log_info,
					'img'=>$this->getLogImg($log_type,$log_info),
				),
			);
		}
		if ($format == 'json') {
			$return = Zend_Json::encode($return);
		}
		return $return;
	}
	function getLogImg($type,$info) {
		if ($type == 'generic') {
			$return = array(
				'element'=>'i',
				'class'=>'vteicon',
				'html'=>'!',
			);
		} elseif ($type == 'create') {
			$return = array(
				'element'=>'i',
				'class'=>'vteicon',
				'html'=>'add',
			);
		} elseif ($type == 'edit') {
			$return = array(
				'element'=>'i',
				'class'=>'vteicon',
				'html'=>'edit',
			);
		} elseif ($type == 'relation' || $type == 'remove_relation') {
			$module = $info['module1'];
			$return = array(
				'element'=>'i',
				'class'=>'vteicon icon-module icon-'.strtolower($module),
				'data_first_letter'=>strtoupper(substr(getTranslatedString($module,$module),0,1)),
			);
		}
		return $return;
	}
	//crmv@104566e
	
	/** 
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// function save_related_module($module, $crmid, $with_module, $with_crmid) { }
	
	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
}
?>