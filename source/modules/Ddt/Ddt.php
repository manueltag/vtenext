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

class Ddt extends CRMEntity {
	var $db, $log, $table_prefix; // Used in class functions of CRMEntity

	var $table_name;
	var $table_index= 'ddtid';
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
		'ddt Name'=> Array('ddt', 'subject'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'ddt Name'=> 'subject',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'subject';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'ddt Name'=> Array('ddt', 'subject')
	);
	var $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'ddt Name'=> 'subject'
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

	var $default_order_by = 'subject';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'subject');
	//crmv@10759
	var $search_base_field = 'subject';
	//crmv@10759 e
	function Ddt() {
		global $log, $currentModule,$table_prefix;
		parent::__construct(); // crmv@37004
		$this->table_name = $table_prefix.'_ddt';
		$this->customFieldTable = Array($table_prefix.'_ddtcf', 'ddtid');
		$this->tab_name = Array($table_prefix.'_crmentity', $table_prefix.'_ddt', $table_prefix.'_ddtcf');
		$this->tab_name_index = Array(
		$table_prefix.'_crmentity' => 'crmid',
		$table_prefix.'_ddt'   => 'ddtid',
	    $table_prefix.'_ddtcf' => 'ddtid');
		$this->column_fields = getColumnFields($currentModule);
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
	}

	function save_module($module) {
		global $table_prefix;
		//crmv@18498
		//in ajax save we should not call this function, because this will delete all the existing product values
		if($_REQUEST['action'] != 'DdtAjax' && $_REQUEST['ajxaction'] != 'DETAILVIEW' && $_REQUEST['action'] != 'MassEditSave')
		{
			$InventoryUtils = InventoryUtils::getInstance(); // crmv@42024
			//Based on the total Number of rows we will save the product relationship with this entity
			$InventoryUtils->saveInventoryProductDetails($this, 'Ddt');
		}

		// Update the currency id and the conversion rate for the sales order
		$update_query = "update ".$table_prefix."_ddt set currency_id=?, conversion_rate=? where ddtid=?";
		$update_params = array($this->column_fields['currency_id'], $this->column_fields['conversion_rate'], $this->id);
		$this->db->pquery($update_query, $update_params);
		//crmv@18498e
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
		global $current_user, $table_prefix;
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
		global $adb,$table_prefix;
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
		global $current_user, $adb,$table_prefix;
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
	function vtlib_handler($moduleName, $event_type) {
		global $table_prefix;
		if($event_type == 'module.postinstall') {
			global $adb;
			$adb->pquery('UPDATE '.$table_prefix.'_tab SET customized=0 WHERE name=?', array($moduleName));

			// crmv@64542
			$tabid = getTabid($moduleName);
			if ($tabid > 0) {
				$tabResult = $adb->pquery("SELECT tabid FROM ".$table_prefix."_tab_info WHERE tabid=? AND prefname='is_inventory'", array($tabid));
				if ($adb->num_rows($tabResult) > 0) {
					$adb->pquery("UPDATE ".$table_prefix."_tab_info SET prefvalue=? WHERE tabid=? AND prefname='is_inventory'", array(1,$tabid));
				} else {
					$adb->pquery('INSERT INTO '.$table_prefix.'_tab_info(tabid, prefname, prefvalue) VALUES (?,?,?)', array($tabid, 'is_inventory', 1));
				}
			}
			// crmv@64542e

			// Initialize module sequence for the module
			$adb->pquery("INSERT into ".$table_prefix."_modentity_num values(?,?,?,?,?,?)",array($adb->getUniqueId($table_prefix."_modentity_num"),$moduleName,'DDT',1,1,1));

			require_once('vtlib/Vtiger/Module.php');
			$moduleInstance = Vtiger_Module::getInstance('Ddt');
			$docModuleInstance = Vtiger_Module::getInstance('Documents');
			$docModuleInstance->setRelatedList($moduleInstance,'Ddt',array('SELECT','ADD'),'get_documents_dependents_list');
			$accModuleInstance = Vtiger_Module::getInstance('Accounts');
			$accModuleInstance->setRelatedList($moduleInstance,'Ddt',array(''),'get_dependents_list');
			$salModuleInstance = Vtiger_Module::getInstance('SalesOrder');
			$salModuleInstance->setRelatedList($moduleInstance,'Ddt',array(''),'get_dependents_list');
			$invModuleInstance = Vtiger_Module::getInstance('Invoice');
			$invModuleInstance->setRelatedList($moduleInstance,'Ddt',array('SELECT'),'get_related_list');
			//crmv@26896
			Vtiger_Link::addLink($moduleInstance->id,'DETAILVIEWBASIC','Add Invoice','index.php?module=Invoice&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&record=$RECORD$&convertmode=ddttoinvoice');
			Vtiger_Link::addLink($salModuleInstance->id,'DETAILVIEWBASIC','Add Ddt','index.php?module=Ddt&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&record=$RECORD$&convertmode=sotoddt');
			//crmv@26896e

			//crmv@69922
			// add the pdfmaker widget, since sometimes is not installed
			if (isModuleInstalled('PDFMaker')) {
				$result1 = $adb->query("SELECT module FROM ".$table_prefix."_pdfmaker GROUP BY module");
				while ($row = $adb->fetchByAssoc($result1, -1, false)) {
					$relModuleInstance = Vtiger_Module::getInstance($row["module"]);
					if ($relModuleInstance && $relModuleInstance->id > 0) {
						Vtiger_Link::addLink($relModuleInstance->id, 'LISTVIEWBASIC', 'PDF Export', "getPDFListViewPopup2(this,'$"."MODULE$');", '', 1);
						Vtiger_Link::addLink($relModuleInstance->id, 'DETAILVIEWWIDGET', 'PDFMaker', "module=PDFMaker&action=PDFMakerAjax&file=getPDFActions&record=$"."RECORD$", '', 1);
					}
				}
			}
			//crmv@69922e
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

	//crmv@18498
	function getConvertDdtToInvoice($focus)
	{
		$focus->column_fields['subject'] = $this->column_fields['subject'];
		$focus->column_fields['customerno'] = $this->column_fields['customerno'];
		$focus->column_fields['duedate'] = $this->column_fields['ddt_data'];
		$focus->column_fields['salesorder_id'] = $this->column_fields['salesorderid'];
		$focus->column_fields['account_id'] = $this->column_fields['accountid'];
		if ($this->column_fields['accountid'] != '') {
			require_once('modules/Accounts/Accounts.php');
	        $account_id = $this->column_fields['accountid'];
	        $account_focus = CRMEntity::getInstance('Accounts');
	        $account_focus->id = $account_id;
	        $account_focus->retrieve_entity_info($account_id,"Accounts");

	        $focus->column_fields['bill_street'] = $account_focus->column_fields['bill_street'];
			$focus->column_fields['ship_street'] = $account_focus->column_fields['ship_street'];
			$focus->column_fields['bill_city'] = $account_focus->column_fields['bill_city'];
			$focus->column_fields['ship_city'] = $account_focus->column_fields['ship_city'];
			$focus->column_fields['bill_state'] = $account_focus->column_fields['bill_state'];
			$focus->column_fields['ship_state'] = $account_focus->column_fields['ship_state'];
			$focus->column_fields['bill_code'] = $account_focus->column_fields['bill_code'];
			$focus->column_fields['ship_code'] = $account_focus->column_fields['ship_code'];
			$focus->column_fields['bill_country'] = $account_focus->column_fields['bill_country'];
			$focus->column_fields['ship_country'] = $account_focus->column_fields['ship_country'];
			$focus->column_fields['bill_pobox'] = $account_focus->column_fields['bill_pobox'];
			$focus->column_fields['ship_pobox'] = $account_focus->column_fields['ship_pobox'];
		}
		$focus->column_fields['description'] = $this->column_fields['description'];
		$focus->column_fields['terms_conditions'] = $this->column_fields['terms_conditions'];
	    $focus->column_fields['currency_id'] = $this->column_fields['currency_id'];
	    $focus->column_fields['conversion_rate'] = $this->column_fields['conversion_rate'];
		return $focus;
	}

	function getConvertSalesOrderToDdt($so_focus)
	{
	    $this->column_fields['salesorderid'] = $so_focus->id;
		$this->column_fields['subject'] = $so_focus->column_fields['subject'];
		$this->column_fields['customerno'] = $so_focus->column_fields['customerno'];
		$this->column_fields['ddt_data'] = $so_focus->column_fields['duedate'];
		$this->column_fields['accountid'] = $so_focus->column_fields['account_id'];
		$this->column_fields['description'] = $so_focus->column_fields['description'];
		$this->column_fields['terms_conditions'] = $so_focus->column_fields['terms_conditions'];
	    $this->column_fields['currency_id'] = $so_focus->column_fields['currency_id'];
	    $this->column_fields['conversion_rate'] = $so_focus->column_fields['conversion_rate'];
	}
	//crmv@18498e
	
	// crmv@97237 - removed report function

	//crmv@47459
	function generateReportsSecQuery($module,$secmodule,$reporttype){
		global $table_prefix;

		$vtiger_inventoryproductrelDdt = substr($table_prefix.'_inventoryproductrelDdt',0,29);

		if ($reporttype != 'COLUMNSTOTOTAL') {
			$productjoins = " left join {$table_prefix}_inventoryproductrel $vtiger_inventoryproductrelDdt on {$table_prefix}_ddt.ddtid = $vtiger_inventoryproductrelDdt.id
			left join {$table_prefix}_products {$table_prefix}_productsDdt on {$table_prefix}_productsDdt.productid = ".substr("{$table_prefix}_inventoryproductrelDdt", 0, 29).".productid
			left join {$table_prefix}_service {$table_prefix}_serviceDdt on {$table_prefix}_serviceDdt.serviceid = ".substr("{$table_prefix}_inventoryproductrelDdt", 0,29).".productid ";
		}

		$query = $this->getRelationQuery($module,$secmodule,$table_prefix."_ddt","ddtid");
		$query .= "
		left join {$table_prefix}_ddtcf on {$table_prefix}_ddt.ddtid = {$table_prefix}_ddtcf.ddtid		
		$productjoins
		left join {$table_prefix}_groups {$table_prefix}_groupsDdt on {$table_prefix}_groupsDdt.groupid = {$table_prefix}_crmentityDdt.smownerid
		left join {$table_prefix}_users {$table_prefix}_usersDdt on {$table_prefix}_usersDdt.id = {$table_prefix}_crmentityDdt.smownerid
		";
		return $query;
	}
	//crmv@47459e
}
?>
