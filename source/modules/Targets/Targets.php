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

class Targets extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name;
	var $table_index= 'targetsid';
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
		'Target Name'=> Array('targets', 'targetname'),
		'Target Type'=> Array('targets', 'target_type'),
		'Target State'=> Array('targets', 'target_state'),
		'End Time'=> Array('targets', 'target_endtime'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Target Name'=> 'targetname',
		'Target Type'=> 'target_type',
		'Target State'=> 'target_state',
		'End Time'=> 'target_endtime',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'targetname';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Target Name'=> Array('targets', 'targetname'),
		'Target Type'=> Array('targets', 'target_type'),
		'Target State'=> Array('targets', 'target_state'),
		'End Time'=> Array('targets', 'target_endtime'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Target Name'=> 'targetname',
		'Target Type'=> 'target_type',
		'Target State'=> 'target_state',
		'End Time'=> 'target_endtime',
		'Assigned To' => 'assigned_user_id'
	);

	// For Popup window record selection
	var $popup_fields = Array('targetname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'targetname';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'targetname';

	// Required Information for enabling Import feature
	var $required_fields = Array('targetname'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'targetname';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'targetname');
	//crmv@10759
	var $search_base_field = 'targetname';
	//crmv@10759 e
	function __construct() {
		global $log;
		global $table_prefix;
		parent::__construct(); // crmv@37004
		$this->table_name = $table_prefix.'_targets';
		$this->customFieldTable = Array($table_prefix.'_targetscf', 'targetsid');
		$this->tab_name = Array($table_prefix.'_crmentity', $table_prefix.'_targets', $table_prefix.'_targetscf');
		$this->tab_name_index = Array(
				$table_prefix.'_crmentity' => 'crmid',
				$table_prefix.'_targets'   => 'targetsid',
			    $table_prefix.'_targetscf' => 'targetsid');
		$this->column_fields = getColumnFields('Targets');
		$this->db = PearDatabase::getInstance();
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
		global $table_prefix;
		global $current_user;
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

			global $adb;
			global $table_prefix;
			$adb->pquery('UPDATE '.$table_prefix.'_tab SET customized=0 WHERE name=?', array($modulename));

			$targetsModule = Vtiger_Module::getInstance($modulename);
			$accountsModule = Vtiger_Module::getInstance('Accounts');
			$accountsModule->setRelatedList($targetsModule, 'Targets', Array(' '));
			$contactsModule = Vtiger_Module::getInstance('Contacts');
			$contactsModule->setRelatedList($targetsModule, 'Targets', Array(' '));
			$leadsModule = Vtiger_Module::getInstance('Leads');
			$leadsModule->setRelatedList($targetsModule, 'Targets', Array(' '));
			$campaignsModule = Vtiger_Module::getInstance('Campaigns');
			$campaignsModule->setRelatedList($targetsModule, 'Targets', Array('ADD','SELECT'));

			$campaignsModule->unsetRelatedList(Vtiger_Module::getInstance('Accounts'), 'Accounts', 'get_accounts');
			$campaignsModule->unsetRelatedList(Vtiger_Module::getInstance('Contacts'), 'Contacts', 'get_contacts');
			$campaignsModule->unsetRelatedList(Vtiger_Module::getInstance('Leads'), 'Leads', 'get_leads');

			$i=1;
			$adb->query("UPDATE ".$table_prefix."_relatedlists SET sequence = $i WHERE tabid = 26 AND label = 'Targets'");
			$res = $adb->query("SELECT * FROM ".$table_prefix."_relatedlists WHERE tabid = 26 AND label <> 'Targets' ORDER BY sequence");
			while($row=$adb->fetchByAssoc($res)) {
				$i++;
				$adb->pquery("UPDATE ".$table_prefix."_relatedlists SET sequence = $i WHERE relation_id = ?",array($row['relation_id']));
			}

			$this->setModuleSeqNumber('configure', 'Targets', 'TRG-', 1);
			
			//crmv@88671
			$result = $adb->pquery("SELECT relation_id, name FROM {$table_prefix}_relatedlists WHERE tabid = ? AND related_tabid = ?", array($targetsModule->id, $targetsModule->id));
			if ($result && $adb->num_rows($result) > 0) {
				$relation_id = $adb->query_result($result,0,'relation_id');
				$method = $adb->query_result($result,0,'name');
				SDK::setTurboliftCount($relation_id, $method);
			}
			//crmv@88671e

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
	function save_related_module($module, $crmid, $with_module, $with_crmid, $skip_check=false) {

		parent::save_related_module($module, $crmid, $with_module, $with_crmid, $skip_check);
		
		//crmv@52391
		if (isModuleInstalled('Fiere') && vtlib_isModuleActive('Fiere') && in_array($with_module,array('Leads','Contacts','Accounts')) && !empty($with_crmid)) {
			$fiereFocus = CRMEntity::getInstance('Fiere');
			foreach($with_crmid as $id) {
				$fiereFocus->create_fiera_to_entity($crmid,$id);
			}
		}
		if (isModuleInstalled('Telemarketing') && vtlib_isModuleActive('Telemarketing') && in_array($with_module,array('Leads','Contacts','Accounts')) && !empty($with_crmid)) {
			$tlmktFocus = CRMEntity::getInstance('Telemarketing');
			foreach($with_crmid as $id) {
				$tlmktFocus->create_tlmkt_to_entity($crmid,$id);
			}
		}
		//crmv@52391e		
	}

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	/* crmv@45027 */
	function delete_related_module($module, $crmid, $with_module, $with_crmid) {
		parent::delete_related_module($module, $crmid, $with_module, $with_crmid);
		if (in_array($with_module,array('Leads','Accounts','Contacts'))) {
			global $adb, $table_prefix;
			$withInstance = CRMEntity::getInstance($with_module);
			if(!is_array($with_crmid)) $with_crmid = Array($with_crmid);
			foreach($with_crmid as $relcrmid) {
				$query = "DELETE FROM {$withInstance->relation_table} WHERE {$withInstance->relation_table_id} = ? AND {$withInstance->relation_table_otherid} = ?";
				$params = array($crmid, $relcrmid);
				if (!empty($withInstance->relation_table_module)) {
					$query .= " AND {$withInstance->relation_table_module} = ?";
					$params[] = $module;
				}
				if (!empty($withInstance->relation_table_othermodule)) {
					$query .= " AND {$withInstance->relation_table_othermodule} = ?";
					$params[] = $with_module;
				}
				$res = $adb->pquery($query, $params);
			}
		}
	}

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

	function get_related_list_target($id, $cur_tab_id, $rel_tab_id, $actions=false) {

		global $currentModule, $app_strings, $singlepane_view;
		global $table_prefix,$theme; //crmv@36539
		$parenttab = getParentTab();

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		$other = CRMEntity::getInstance($related_module);

		// Some standard module class doesn't have required variables
		// that are used in the query, they are defined in this generic API
		vtlib_setup_modulevars($currentModule, $this);
		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$button = '';
		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('EMAIL', $actions)) {
				// Send mail button for selected elements
				$button .= "<input title='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' class='crmbutton small edit' value='".getTranslatedString('LBL_SEND_MAIL_BUTTON')."' type='button' name='button' onclick='rel_eMail(\"$currentModule\",this,\"$related_module\")'>&nbsp;&nbsp;";
			}
			if(in_array('LOAD', $actions)) {
				/* To get CustomView -START */
				require_once('modules/CustomView/CustomView.php');
				$ahtml = "<select id='".$related_module."_cv_list' class='small hide_turbolift'><option value='None'>-- ".getTranslatedString('Select One')." --</option>";	//crmv@64719
				$oCustomView = new CustomView($related_module);
				$viewid = $oCustomView->getViewId($related_module);
				$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid, false);
				$ahtml .= $customviewcombo_html;
				$ahtml .= "</select>";
				$ahtml .= '&nbsp;&nbsp;';
				/* To get CustomView -END */
				$button .= $ahtml."<input title='".getTranslatedString('LBL_LOAD_LIST',$currentModule)."' class='crmbutton small edit' value='".getTranslatedString('LBL_LOAD_LIST',$currentModule)."' type='button' name='button' onclick='loadCvListTargets(\"$related_module\",\"$id\")'>";
				$button .= '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			// crmv@43864
			$button .= $this->get_related_buttons($currentModule, $id, $related_module, $actions);
			//crmv@36539
			$add_button = '
			<div class="hide_turbolift">
				<input id="report'.$related_module.'" name="report'.$related_module.'" type="hidden" value="">
				<div class="dvtCellInfo" style="float:left;width:50%">
					<input id="report'.$related_module.'_display" name="report'.$related_module.'_display" type="text" value="'.getTranslatedString('LBL_SEARCH_STRING').'" class="detailedViewTextBox detailedViewReference" />
				</div>
				<script type="text/javascript">
					initAutocomplete(\'report'.$related_module.'\',\'report'.$related_module.'_display\',encodeURIComponent(\'module=Reports&action=ReportsAjax&file=AutocompleteRL&field=report'.$related_module.'&cvmodule='.$related_module.'\'));
				</script>
				<i class="vteicon md-link valign-bottom" title="'.getTranslatedString('LBL_SELECT').'" onclick=\'jQuery( this ).blur(); jQuery("#report'.$related_module.'_display").autocomplete("search","ALL");\'>view_list</i>
				<i class="vteicon md-link valign-bottom" title="'.getTranslatedString('LBL_CLEAR').'" onClick="jQuery(\'#report'.$related_module.'\').val(\'\');jQuery(\'#report'.$related_module.'_display\').val(\'\'); enableReferenceField(jQuery(\'#report'.$related_module.'_display\')[0]); return false;">highlight_off</i>
				<i class="vteicon md-link valign-bottom" title="'.getTranslatedString('LBL_CREATE').'" onClick="popupReport_rl(\'new\',\''.$related_module.'\',jQuery(\'#report'.$related_module.'_display\').val(),\'report'.$related_module.'\');">add</i>
				<i class="vteicon md-link valign-bottom" title="'.getTranslatedString('LBL_EDIT').'" onClick="popupReport_rl(\'edit\',\''.$related_module.'\',jQuery(\'#report'.$related_module.'_display\').val(),\'report'.$related_module.'\');">create</i>
				<input type="button" onclick="loadReportListTargets(jQuery(\'#report'.$related_module.'\').val(),\''.$id.'\',\''.$related_module.'\')" name="button" value="'.getTranslatedString('LBL_LOAD_REPORT','Targets').'" class="crmbutton small edit" title="Carica Report">
			</div>';
			// crmv@43864e
			$button = "<table><tr><td nowrap>".$button."</td></tr><tr><td nowrap>".$add_button."</td></tr></table>";
			//crmv@36539 e
		}

		// To make the edit or del link actions to return back to same view.
		if($singlepane_view == 'true') $returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
		else $returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";

		$query = "SELECT ".$table_prefix."_crmentity.crmid";
		//crmv@fix query
		foreach ($other->list_fields as $label=>$arr){
			foreach ($arr as $table=>$field){
				if ($table != 'crmentity' && !is_numeric($table) && $field){
					if (strpos($table,$table_prefix.'_') !== false)
						$query.=",$table.$field";
					else
						$query.=",".$table_prefix."_$table.$field";
				}
			}
		}
		//crmv@fix query end
		$query .= ", CASE WHEN (".$table_prefix."_users.user_name is not null) THEN ".$table_prefix."_users.user_name ELSE ".$table_prefix."_groups.groupname END AS user_name";

		$more_relation = '';
		if(!empty($other->related_tables)) {
			foreach($other->related_tables as $tname=>$relmap) {
				// Setup the default JOIN conditions if not specified
				if(empty($relmap[1])) $relmap[1] = $other->table_name;
				if(empty($relmap[2])) $relmap[2] = $relmap[0];
				$more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
			}
		}

		$query .= " FROM $other->table_name";
		$query .= " INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = $other->table_name.$other->table_index";
		//crmv@24527
		if (!empty($other->customFieldTable)) {
			$query .= " INNER JOIN ".$other->customFieldTable[0]." ON $other->table_name.$other->table_index = ".$other->customFieldTable[0].".".$other->customFieldTable[1];
		}
		//crmv@24527e
		if ($related_module == 'Products'){
			$query .= " INNER JOIN ".$table_prefix."_seproductsrel ON (".$table_prefix."_seproductsrel.crmid = ".$table_prefix."_crmentity.crmid OR ".$table_prefix."_seproductsrel.productid = ".$table_prefix."_crmentity.crmid)";
		}
		elseif ($related_module == 'Documents'){
			$query .= " INNER JOIN ".$table_prefix."_senotesrel ON (".$table_prefix."_senotesrel.notesid = ".$table_prefix."_crmentity.crmid OR ".$table_prefix."_senotesrel.crmid = ".$table_prefix."_crmentity.crmid)";
		}
		else {
			//$query .= " INNER JOIN ".$table_prefix."_crmentityrel ON (".$table_prefix."_crmentityrel.relcrmid = ".$table_prefix."_crmentity.crmid OR ".$table_prefix."_crmentityrel.crmid = ".$table_prefix."_crmentity.crmid)";
			$query .= " INNER JOIN ".$table_prefix."_crmentityrel ON ".$table_prefix."_crmentityrel.relcrmid = ".$table_prefix."_crmentity.crmid";
		}
		$query .= " LEFT  JOIN $this->table_name   ON $this->table_name.$this->table_index = ".$table_prefix."_crmentityrel.crmid";
		$query .= $more_relation;
		$query .= " LEFT  JOIN ".$table_prefix."_users ON ".$table_prefix."_users.id = ".$table_prefix."_crmentity.smownerid";
		$query .= " LEFT  JOIN ".$table_prefix."_groups       ON ".$table_prefix."_groups.groupid = ".$table_prefix."_crmentity.smownerid";
		if ($related_module == 'Products'){
			$query .= " WHERE ".$table_prefix."_crmentity.deleted = 0 AND (".$table_prefix."_seproductsrel.crmid = $id OR ".$table_prefix."_seproductsrel.productid = $id)";
		}
		elseif ($related_module == 'Documents'){
			$query .= " WHERE ".$table_prefix."_crmentity.deleted = 0 AND (".$table_prefix."_senotesrel.crmid = $id OR ".$table_prefix."_senotesrel.notesid = $id)";
		}
		else {
			//$query .= " WHERE ".$table_prefix."_crmentity.deleted = 0 AND (".$table_prefix."_crmentityrel.crmid = $id OR ".$table_prefix."_crmentityrel.relcrmid = $id)";
			$query .= " WHERE ".$table_prefix."_crmentity.deleted = 0 AND ".$table_prefix."_crmentityrel.crmid = $id";
		}
		$return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		return $return_value;
	}

	function getFathers($include_myself=false) {
		$fathers = array();
		if ($include_myself) {
			$fathers[] = $this->id;
		}
		$this->get_father($fathers,$this->id);
		return $fathers;
	}

	function get_father($fathers,$id) {
		global $adb;
		global $table_prefix;
		$result = $adb->query("select crmid from ".$table_prefix."_crmentityrel WHERE module = 'Targets' AND relmodule = 'Targets' AND relcrmid = $id");
		if ($result && $adb->num_rows($result)>0) {
			$father = $adb->query_result($result,0,'crmid');
			$fathers[] = $father;
			$this->get_father($fathers,$father);
		}
	}

	function getChildren() {
		global $adb;
		global $table_prefix;
		$children = array();
		$result = $adb->query("select relcrmid from ".$table_prefix."_crmentityrel WHERE module = 'Targets' AND relmodule = 'Targets' AND crmid = $this->id");
		if ($result && $adb->num_rows($result)>0) {
			while($row=$adb->fetchByAssoc($result)) {
				$children[] = $row['relcrmid'];
			}
			return $children;
		}
	}
}
?>