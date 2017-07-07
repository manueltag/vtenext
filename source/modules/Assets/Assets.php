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

class Assets extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name;
	var $table_index= 'assetsid';
	var $column_fields = Array();

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
	var $list_fields = Array(
   		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Asset No'=>Array('assets'=>'asset_no'),
        'Asset Name'=>Array('assets'=>'assetname'),
		'Customer Name'=>Array('account'=>'account'),
        'Product Name'=>Array('products'=>'product'),
	);
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Asset No'=>'asset_no',
        'Asset Name'=>'assetname',
		'Customer Name'=>'account',
        'Product Name'=>'product',
	);

	// Make the field link to detail view
	var $list_link_field= 'assetname';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Asset No'=>Array('assets'=>'asset_no'),
        'Asset Name'=>Array('assets'=>'assetname'),
		'Customer Name'=>Array('account'=>'account'),
		'Product Name'=>Array('products'=>'product')
	);
	var $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Asset No'=>'asset_no',
        'Asset Name'=>'assetname',
		'Customer Name'=>'account',
		'Product Name'=>'product'
	);

	// For Popup window record selection
	var $popup_fields = Array ('assetname','account','product');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'assetname';

	// Required Information for enabling Import feature
	var $required_fields = Array('assetname'=>1);

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('assetname', 'product');
	
	//crmv@10759
	var $search_base_field = 'assetname';
	//crmv@10759 e

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'assetname';
	var $default_sort_order='ASC';

	var $unit_price;

	/**	Constructor which will set the column_fields in this object
	 */
	function __construct() {
		global $log, $table_prefix;
		parent::__construct(); // crmv@37004
		$this->customFieldTable = Array($table_prefix.'_assetscf', 'assetsid');
		$this->tab_name = Array($table_prefix.'_crmentity',$table_prefix.'_assets',$table_prefix.'_assetscf');
		$this->tab_name_index = Array(
			$table_prefix.'_crmentity'=>'crmid',
			$table_prefix.'_assets'=>'assetsid',
			$table_prefix.'_assetscf'=>'assetsid');
		$this->table_name = $table_prefix.'_assets';
		$this->column_fields = getColumnFields('Assets');
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
	}

	function save_module($module){
		//module specific save
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
		//crmv@51914
		$query .= " LEFT JOIN ".$table_prefix."_account ON ".$table_prefix."_assets.account = ".$table_prefix."_account.accountid";
		$query .= " LEFT JOIN ".$table_prefix."_products ON ".$table_prefix."_assets.product = ".$table_prefix."_products.productid";
		//crmv@51914e
		
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
		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[getTabid('Assets')] == 3)
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
		if($key == 'owner') return getOwnerName($value);
		return parent::transform_export_value($key, $value);
	}

	/**
	 * Function which will set the assigned user id for import record.
	 */
	function set_import_assigned_user()
	{
		global $current_user, $adb, $table_prefix;
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


	/*
	 * Function to get the primary query part of a report
	 * @param - $module primary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsQuery($module){
		global $current_user, $table_prefix;
			//crmv@21249
			$query = "from ".$table_prefix."_assets
				inner join ".$table_prefix."_crmentity on ".$table_prefix."_crmentity.crmid=".$table_prefix."_assets.assetsid
				left join ".$table_prefix."_assetscf on ".$table_prefix."_assets.assetsid = ".$table_prefix."_assetscf.assetsid
				left join ".$table_prefix."_account ".$table_prefix."_accountAssets on ".$table_prefix."_accountAssets.accountid=".$table_prefix."_assets.account
				left join ".$table_prefix."_products ".$table_prefix."_productAssets on ".$table_prefix."_productAssets.productid=".$table_prefix."_assets.product
				left join ".$table_prefix."_invoice ".$table_prefix."_invoiceAssets on ".$table_prefix."_invoiceAssets.invoiceid=".$table_prefix."_assets.invoiceid
				left join ".$table_prefix."_users ".$table_prefix."_usersAssets on ".$table_prefix."_usersAssets.id=".$table_prefix."_crmentity.smownerid
				left join ".$table_prefix."_groups ".$table_prefix."_groupsAssets on ".$table_prefix."_groupsAssets.groupid=".$table_prefix."_crmentity.smownerid";
			//crmv@21249e
			return $query;
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	//crmv@38798
	function generateReportsSecQuery($module,$secmodule){
		global $current_user,$table_prefix;
		$query = $this->getRelationQuery($module,$secmodule,$table_prefix."_assets","assetsid");
		//crmv@21249
		$query .= " left join ".$table_prefix."_assetscf on ".$table_prefix."_assets.assetsid = ".$table_prefix."_assetscf.assetsid
			left join ".$table_prefix."_account ".$table_prefix."_accountAssets on ".$table_prefix."_accountAssets.accountid=".$table_prefix."_assets.account
            left join ".$table_prefix."_products ".$table_prefix."_productAssets on ".$table_prefix."_productAssets.productid=".$table_prefix."_assets.product
            left join ".$table_prefix."_invoice ".$table_prefix."_invoiceAssets on ".$table_prefix."_invoiceAssets.invoiceid=".$table_prefix."_assets.invoiceid
            left join ".$table_prefix."_users ".$table_prefix."_usersAssets on ".$table_prefix."_usersAssets.id=".$table_prefix."_crmentity.smownerid
            left join ".$table_prefix."_groups ".$table_prefix."_groupsAssets on ".$table_prefix."_groupsAssets.groupid=".$table_prefix."_crmentity.smownerid ";
		//crmv@21249e
		return $query;
	}
	//crmv@38798e


	// Function to unlink all the dependent entities of the given Entity by Id
	function unlinkDependencies($module, $id) {
		global $log;
		parent::unlinkDependencies($module, $id);
	}

 	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
		require_once('include/utils/utils.php');
		global $adb,$table_prefix;

 		if($eventType == 'module.postinstall') {
			//Add Assets Module to Customer Portal
			//crmv@16644 : Sposto questa operazione direttamente nell'installazione CustomerPortal
			/*
			global $adb;
			$visible=1;
			$query = $adb->pquery("SELECT max(sequence) AS max_tabseq FROM vtiger_customerportal_tabs",array());
			$maxtabseq = $adb->query_result($query, 0, 'max_tabseq');
			$newTabSeq = ++$maxtabseq;
			$tabid = getTabid('Assets');
			$adb->pquery("INSERT INTO vtiger_customerportal_tabs(tabid, visible, sequence) VALUES(?,?,?)", array($tabid,$visible,$newTabSeq));
			*/
			//crmv@16644e
			
			include_once('vtlib/Vtiger/Module.php');

			// Mark the module as Standard module
			$adb->pquery('UPDATE '.$table_prefix.'_tab SET customized=0 WHERE name=?', array($moduleName));

			$assetInstance = Vtiger_Module::getInstance('Assets');
			$assetLabel = 'Assets';
			
			//adds sharing accsess
			Vtiger_Access::setDefaultSharing($assetInstance);

			//Showing Assets module in the related modules in the More Information Tab
			$accountInstance = Vtiger_Module::getInstance('Accounts');
			$accountInstance->setRelatedlist($assetInstance,$assetLabel,array(ADD),'get_dependents_list');

			$productInstance = Vtiger_Module::getInstance('Products');
			$productInstance->setRelatedlist($assetInstance,$assetLabel,array(ADD),'get_dependents_list');

			$InvoiceInstance = Vtiger_Module::getInstance('Invoice');
			$InvoiceInstance->setRelatedlist($assetInstance,$assetLabel,array(ADD),'get_dependents_list');
			
			//crmv@16644
			$SalesorderInstance = Vtiger_Module::getInstance('SalesOrder');
			$SalesorderInstance->setRelatedlist($assetInstance,$assetLabel,'','get_dependents_list');
			//crmv@16644e
			
			//crmv@21786
			$HelpDeskInstance = Vtiger_Module::getInstance('HelpDesk');
			$HelpDeskInstance->setRelatedlist($assetInstance,$assetLabel,array('ADD','SELECT'),'get_related_list');
			//crmv@21786e
			
			//crmv@58540
			$docModuleInstance = Vtiger_Module::getInstance('Documents');
			$docModuleInstance->setRelatedList($assetInstance,$assetLabel,array('select','add'),'get_documents_dependents_list');
			//crmv@58540e

		} else if($eventType == 'module.disabled') {
		// TODO Handle actions when this module is disabled.
		} else if($eventType == 'module.enabled') {
		// TODO Handle actions when this module is enabled.
		} else if($eventType == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
		// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
		// TODO Handle actions after this module is updated.
		}
 	}
}
?>