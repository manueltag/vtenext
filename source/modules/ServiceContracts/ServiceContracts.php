<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class ServiceContracts extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name;
	var $table_index= 'servicecontractsid';
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
		'Subject' => Array('servicecontracts', 'subject'),
		//crmv@16644
		'Service' => Array('servicecontracts', 'service_id'),
		'Tracking Unit' => Array('servicecontracts', 'tracking_unit'),
		'Total Units' => Array('servicecontracts', 'total_units'),
		'Used Units' => Array('servicecontracts', 'used_units'),
		//crmv@16644e
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Subject' => 'subject',
		//crmv@16644
		'Service' => 'service_id',
		'Tracking Unit' => 'tracking_unit',
		'Total Units' => 'total_units',
		'Used Units' => 'used_units',
		//crmv@16644e
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view 
	var $list_link_field = 'subject';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Subject' => Array('servicecontracts', 'subject')
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Subject' => 'subject'
	);

	// For Popup window record selection
	var $popup_fields = Array ('subject');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'subject';

	// Required Information for enabling Import feature
	var $required_fields = Array ('assigned_user_id'=>1);

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('subject','assigned_user_id');
	
	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'subject';
	var $default_sort_order='ASC';
	//crmv@10759
	var $search_base_field = 'subject';
	//crmv@10759 e
	function __construct() {
		global $log;
		global $table_prefix;
		parent::__construct(); // crmv@37004
		$this->customFieldTable = Array($table_prefix.'_servicecontractscf', 'servicecontractsid');
		$this->tab_name = Array($table_prefix.'_crmentity', $table_prefix.'_servicecontracts', $table_prefix.'_servicecontractscf');
		$this->tab_name_index = Array(
			$table_prefix.'_crmentity' => 'crmid',
			$table_prefix.'_servicecontracts' => 'servicecontractsid',
			$table_prefix.'_servicecontractscf'=>'servicecontractsid');
		$this->table_name = $table_prefix.'_servicecontracts';
		$this->column_fields = getColumnFields('ServiceContracts');
		$this->db = new PearDatabase();
		$this->log = $log;
	}

	function save_module($module) {
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
		global $current_user,$currentModule;
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
			// crmv@82938
			if ($other->table_name == $table_prefix.'_contactdetails') {
				$alias = substr($other->table_name.$columnname, 0, 29);
				$query .= " LEFT JOIN {$other->table_name} $alias ON {$this->table_name}.{$columnname} = {$alias}.{$other->table_index} ";
			}
			// crmv@82938e
		}
		
		//crmv@31775
		$reportFilter = $oCustomView->getReportFilter($viewId);
		if ($reportFilter) {
			$tableNameTmp = $oCustomView->getReportFilterTableName($reportFilter,$current_user->id);
			$query .= " INNER JOIN $tableNameTmp ON $tableNameTmp.id = {$table_prefix}_crmentity.crmid";
		}
		//crmv@31775e
		
		$query .= $this->getNonAdminAccessControlQuery($thismodule,$current_user);
		$where_auto = " ".$table_prefix."_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";
		$query = $this->listQueryNonAdminChange($query, $thismodule);
		return $query;
	}

	/**
	 * Initialize this instance for importing.
	 */
	function initImport($module) {
		$this->db = new PearDatabase();
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

/*
     * Function to get the relation tables for related modules 
     * @param - $secmodule secondary module name
     * returns the array with table names and fieldnames storing relations between module and this module
     */
    function setRelationTables($secmodule){
    	global $table_prefix;
        $rel_tables = array (
            "Documents" => array($table_prefix."_senotesrel"=>array("crmid","notesid"),$table_prefix."_servicecontracts"=>" servicecontractsid"),
        );
        return $rel_tables[$secmodule];
    }
    
	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/	
	function vtlib_handler($moduleName, $eventType) {
 					
		require_once('include/utils/utils.php');			
		global $adb, $table_prefix;
 		if($eventType == 'module.postinstall') {
			require_once('vtlib/Vtiger/Module.php');
				
			$moduleInstance = Vtiger_Module::getInstance($moduleName);
			
			$accModuleInstance = Vtiger_Module::getInstance('Accounts');
			$accModuleInstance->setRelatedList($moduleInstance,'Service Contracts',array('add'),'get_dependents_list');
			
			$conModuleInstance = Vtiger_Module::getInstance('Contacts');
			$conModuleInstance->setRelatedList($moduleInstance,'Service Contracts',array('add'),'get_dependents_list');
			
			$conModuleInstance = Vtiger_Module::getInstance('Documents');
			$conModuleInstance->setRelatedList($moduleInstance,'Service Contracts',array('select','add'),'get_documents_dependents_list');
			
			// Initialize module sequence for the module
			$adb->pquery("INSERT into ".$table_prefix."_modentity_num values(?,?,?,?,?,?)",array($adb->getUniqueId($table_prefix."_modentity_num"),$moduleName,'SERCON',1,1,1));
			
			// Make the picklist value 'Complete' for status as non-editable
			$adb->query("UPDATE ".$table_prefix."_contract_status SET presence=0 WHERE contract_status='Complete'");
			
			// Mark the module as Standard module
			$adb->pquery('UPDATE '.$table_prefix.'_tab SET customized=0 WHERE name=?', array($moduleName));
			
			//crmv@16644
			$sorder = Vtiger_Module::getInstance('SalesOrder');
			$sorder->setRelatedList($moduleInstance, 'Service Contracts', '', 'get_dependents_list');
			//crmv@16644e
			
			$adb->pquery("update {$table_prefix}_tracking_unit set presence = 0 where tracking_unit in (?,?)",array('Hours','Days'));
			
			//crmv@29988
			$HelpDeskInstance = Vtiger_Module::getInstance('HelpDesk');
			$HelpDeskInstance->setRelatedList($moduleInstance,'Service Contracts',array('add','select'),'get_related_list');
			//crmv@29988e
			
		} else if($eventType == 'module.disabled') {
			$em = new VTEventsManager($adb);
			$em->setHandlerInActive('ServiceContractsHandler');

		} else if($eventType == 'module.enabled') {
			$em = new VTEventsManager($adb);
			$em->setHandlerActive('ServiceContractsHandler');

		} else if($eventType == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
		// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
		// TODO Handle actions after this module is updated.
		}
 	}

	/** 
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	function save_related_module($module, $crmid, $with_module, $with_crmid) { 
	 	parent::save_related_module($module, $crmid, $with_module, $with_crmid);
	 	if ($with_module == 'HelpDesk') {
	 		$this->updateHelpDeskRelatedTo($crmid,$with_crmid);
	 		$this->updateServiceContractState($crmid);
	 	}	
	 }
	 
	 // Function to Update the parent_id of HelpDesk with sc_related_to of ServiceContracts if the parent_id is not set.
	 function updateHelpDeskRelatedTo($focusId, $entityIds) {
		global $log;
		global $table_prefix;
		$log->debug("Entering into function updateHelpDeskRelatedTo(".$entityIds.").");
		
		if(!is_array($entityIds)) $entityIds = array($entityIds);
		$selectTicketsQuery = "SELECT ticketid FROM ".$table_prefix."_troubletickets WHERE (parent_id IS NULL OR parent_id = 0) AND ticketid IN (" . generateQuestionMarks($entityIds) .")";
		$selectTicketsResult = $this->db->pquery($selectTicketsQuery, array($entityIds));
		$noOfTickets = $this->db->num_rows($selectTicketsResult);
		for($i=0; $i < $noOfTickets; ++$i) {
			$ticketId = $this->db->query_result($selectTicketsResult,$i,'ticketid');
			 if ($this->db->isMssql()){
				$updateQuery = "UPDATE ".$table_prefix."_troubletickets SET parent_id=".$table_prefix."_servicecontracts.sc_related_to" .
						" from ".$table_prefix."_troubletickets, ".$table_prefix."_servicecontracts WHERE ".$table_prefix."_servicecontracts.sc_related_to IS NOT NULL AND ".$table_prefix."_servicecontracts.sc_related_to <> 0" .
						" AND ".$table_prefix."_servicecontracts.servicecontractsid = ? AND ".$table_prefix."_troubletickets.ticketid = ?";
			 }
			 else {
				$updateQuery = "UPDATE ".$table_prefix."_troubletickets, ".$table_prefix."_servicecontracts SET parent_id=".$table_prefix."_servicecontracts.sc_related_to" .
						" WHERE ".$table_prefix."_servicecontracts.sc_related_to IS NOT NULL AND ".$table_prefix."_servicecontracts.sc_related_to <> 0" .
						" AND ".$table_prefix."_servicecontracts.servicecontractsid = ? AND ".$table_prefix."_troubletickets.ticketid = ?";				
			 }

			$updateResult = $this->db->pquery($updateQuery, array($focusId, $ticketId));
		}
		
		$log->debug("Exit from function updateHelpDeskRelatedTo(".$entityIds.")");
	}
	 
	// Function to Compute and Update the Used Units and Progress of the Service Contract based on all the related Trouble tickets.
	function updateServiceContractState($focusId) {
		//crmv@47905
		global $table_prefix, $current_user;
		$this->id = $focusId;
		$this->retrieve_entity_info($focusId,'ServiceContracts');
		//crmv@63349 crmv@101363
		$focusId = intval($focusId);
		// removed join with user table, shouldn't be done
		/*$tmodreltables = TmpUserModRelTables::getInstance();
		$tabname = $this->setupTemporaryRelTable('ServiceContracts','HelpDesk',$focusId);
		if ($tabname == $tmodreltables->tmpTable) {
			$joinCondition = $tmodreltables->getJoinCondition('ServiceContracts', 'HelpDesk', $current_user->id, $focusId, '?', 't.ticketid', 'r');
		} else {
			$joinCondition = "r.relcrmid = t.ticketid and r.crmid = ?";
		}
		$sql = "select t.days,t.hours from {$table_prefix}_troubletickets t
			inner join {$table_prefix}_crmentity c on c.crmid = t.ticketid
			inner join {$tabname} r on $joinCondition
			where c.deleted = 0 and t.status = ?";
		*/
		$sql = 
			"SELECT days, hours FROM {$table_prefix}_crmentityrel
			INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = {$table_prefix}_crmentityrel.relcrmid
			INNER JOIN {$table_prefix}_troubletickets ON {$table_prefix}_troubletickets.ticketid = {$table_prefix}_crmentity.crmid
			WHERE module = 'ServiceContracts' AND {$table_prefix}_crmentityrel.crmid = ?
				AND relmodule = 'HelpDesk' AND deleted = 0 AND {$table_prefix}_troubletickets.status = ?
			UNION ALL
			SELECT days, hours FROM {$table_prefix}_crmentityrel
			INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = {$table_prefix}_crmentityrel.crmid
			INNER JOIN {$table_prefix}_troubletickets ON {$table_prefix}_troubletickets.ticketid = {$table_prefix}_crmentity.crmid
			WHERE relmodule = 'ServiceContracts' AND {$table_prefix}_crmentityrel.relcrmid = ?
				AND module = 'HelpDesk' AND deleted = 0 AND {$table_prefix}_troubletickets.status = ?";
		
		//crmv@63349e crmv@101363e

		$contractTicketsResult = $this->db->pquery($sql,Array($focusId,'Closed',$focusId,'Closed'));
		$totalUsedUnits = 0;
		while($row = $this->db->fetchByAssoc($contractTicketsResult,-1,false)){
			$totalUsedUnits += $this->computeUsedUnits($row);
		}
		$this->updateUsedUnits($totalUsedUnits);
		$this->updateResidualUnits();	//crmv@19400
		$this->calculateProgress();
		//crmv@47905 e		
	}
	
	// Function to Upate the Used Units of the Service Contract based on the given Ticket id.
	function computeUsedUnits($ticketData, $operator='+') {
		$trackingUnit = strtolower($this->column_fields['tracking_unit']);
		$workingHoursPerDay = 24;
		
		$usedUnits = 0;
		if ($trackingUnit == 'incidents') {
			$usedUnits = 1;
		} elseif ($trackingUnit == 'days') {
			if(!empty($ticketData['days'])) {
				$usedUnits = $ticketData['days'];
			} elseif(!empty($ticketData['hours'])) {
				$usedUnits = $ticketData['hours'] / $workingHoursPerDay;
			} 						
		} elseif ($trackingUnit == 'hours') {
			if(!empty($ticketData['hours'])) {
				$usedUnits = $ticketData['hours'];
			} elseif(!empty($ticketData['days'])) {
				$usedUnits = $ticketData['days'] * $workingHoursPerDay;
			} 
		}
		return $usedUnits;
	}
	
	// Function to Upate the Used Units of the Service Contract.
	function updateUsedUnits($usedUnits) {
		global $table_prefix;
		$this->column_fields['used_units'] = $usedUnits;
		$updateQuery = "UPDATE ".$table_prefix."_servicecontracts SET used_units = $usedUnits WHERE servicecontractsid = ?";
		$this->db->pquery($updateQuery, array($this->id));
	}

	//crmv@19400
	function updateResidualUnits() {
		global $table_prefix;
		$residualUnits = $this->column_fields['total_units'] - $this->column_fields['used_units'];
		$this->column_fields['residual_units'] = $residualUnits;
		$updateQuery = "UPDATE ".$table_prefix."_servicecontracts SET residual_units = $residualUnits WHERE servicecontractsid = ?";
		$this->db->pquery($updateQuery, array($this->id));
	}
	//crmv@19400e
	
	// Function to Calculate the End Date, Planned Duration, Actual Duration and Progress of a Service Contract
	function calculateProgress() {
		global $table_prefix;				
		$updateCols = array();
		$updateParams = array();
		
		$startDate = $this->column_fields['start_date'];
		$dueDate = $this->column_fields['due_date'];
		$endDate = $this->column_fields['end_date'];
		
		$usedUnits = $this->column_fields['used_units'];
		$totalUnits = $this->column_fields['total_units'];
		
		$contractStatus = $this->column_fields['contract_status'];
		
		// Update the End date if the status is Complete or if the Used Units reaches/exceeds Total Units 
		// We need to do this first to make sure Actual duration is computed properly
		if($contractStatus == 'Complete' || (!empty($usedUnits) && !empty($totalUnits) && $usedUnits >= $totalUnits)) {
			if(empty($endDate)) {
				$endDate = date('Y-m-d');
				$this->db->pquery('UPDATE '.$table_prefix.'_servicecontracts SET end_date=? WHERE servicecontractsid = ?', array(date('Y-m-d'), $this->id));
			}
		} else {
			$endDate = null;
			$this->db->pquery('UPDATE '.$table_prefix.'_servicecontracts SET end_date=? WHERE servicecontractsid = ?', array(null, $this->id));
		}
		
		// Calculate the Planned Duration based on Due date and Start date. (in days)
		if(!empty($dueDate) && !empty($startDate)) {
			$duration = intval((strtotime(substr($dueDate,0,10))-strtotime(substr($startDate,0,10)))/ (60 * 60 * 24));
			$plannedDurationUpdate = " planned_duration = $duration";
		} else {
			$plannedDurationUpdate = " planned_duration = ''";
		}
		array_push($updateCols, $plannedDurationUpdate);
		
		// Calculate the Actual Duration based on End date and Start date. (in days)
		if(!empty($endDate) && !empty($startDate)) {
			$duration = intval((strtotime(substr($dueDate,0,10))-strtotime(substr($startDate,0,10)))/ (60 * 60 * 24));
			$actualDurationUpdate = "actual_duration = $duration";
		} else {
			$actualDurationUpdate = "actual_duration = ''";					
		}
		array_push($updateCols, $actualDurationUpdate);
		
		// Update the Progress based on Used Units and Total Units (in percentage)
		if(!empty($usedUnits) && !empty($totalUnits) && $totalUnits != 0) {	//crmv@22378
			$progressUpdate = 'progress = ?';
			$progressUpdateParams = floatval(($usedUnits * 100) / $totalUnits);
		} else {
			$progressUpdate = 'progress = ?';
			$progressUpdateParams = null;			
		}
		array_push($updateCols, $progressUpdate);
		array_push($updateParams, $progressUpdateParams);
		
		if(count($updateCols) > 0) {
			$updateQuery = 'UPDATE '.$table_prefix.'_servicecontracts SET '. implode(",", $updateCols) .' WHERE servicecontractsid = ?';
			array_push($updateParams, $this->id);
			$this->db->pquery($updateQuery, $updateParams);
		}
	}

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	function delete_related_module($module, $crmid, $with_module, $with_crmid) {
	 	parent::delete_related_module($module, $crmid, $with_module, $with_crmid);
	 	if ($with_module == 'HelpDesk') {
	 		$this->updateServiceContractState($crmid);
	 	}
	}

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
	
	//crmv@29988
	function unlinkRelationship($id, $return_module, $return_id) {
		parent::unlinkRelationship($id, $return_module, $return_id);
		if ($return_module == 'HelpDesk') {
	 		$this->updateServiceContractState($id);
	 	}
	}
	//crmv@29988e
}
?>
