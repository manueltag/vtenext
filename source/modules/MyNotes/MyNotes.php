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

class MyNotes extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name;
	var $table_index= 'mynotesid';
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
		'Subject'=> 'subject',
		'Assigned To' => 'assigned_user_id'
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

	var $default_order_by = 'modifiedtime';
	var $default_sort_order='DESC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vte_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'subject');
	//crmv@10759
	var $search_base_field = 'subject';
	//crmv@10759 e
	
	var $skip_modules = array('Calendar','Events','Emails','Fax','Sms','ChangeLog','ModComments','ModNotifications','Charts','MyFiles','MyNotes','Messages');
	var $only_widget_modules = array('Calendar','Events');

	function __construct() {
		global $log, $currentModule, $table_prefix;
		parent::__construct(); // crmv@37004
		$this->table_name = $table_prefix.'_mynotes';
		$this->customFieldTable = Array($table_prefix.'_mynotescf', 'mynotesid');
		$this->entity_table = $table_prefix."_crmentity";
		$this->tab_name = Array($table_prefix.'_crmentity', $table_prefix.'_mynotes', $table_prefix.'_mynotescf');
		$this->tab_name_index = Array(
			$table_prefix.'_crmentity' => 'crmid',
			$table_prefix.'_mynotes'   => 'mynotesid',
			$table_prefix.'_mynotescf' => 'mynotesid'
		);
		$this->list_fields = Array(
			/* Format: Field Label => Array(tablename, columnname) */
			// tablename should not have prefix 'vte_'
			'Subject'=> Array($table_prefix.'_mynotes', 'subject'),
			'Assigned To' => Array($table_prefix.'_crmentity','smownerid')
		);
		$this->search_fields = Array(
			/* Format: Field Label => Array(tablename, columnname) */
			// tablename should not have prefix 'vte_'
			'Subject'=> Array($table_prefix.'_mynotes', 'subject')
		);
		$this->column_fields = getColumnFields($currentModule);

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
	
	function getQueryExtraWhere() {
		global $current_user;
		$sql = " and {$this->entity_table}.smownerid = $current_user->id";
		return $sql;
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		global $adb,$table_prefix;
		if($event_type == 'module.postinstall') {
		
			$adb->pquery("UPDATE {$table_prefix}_tab SET customized=0 WHERE name=?", array($modulename));

			//crmv@29617
			$result = $adb->pquery('SELECT isentitytype FROM '.$table_prefix.'_tab WHERE name = ?',array($modulename));
			if ($result && $adb->num_rows($result) > 0 && $adb->query_result($result,0,'isentitytype') == '1') {
				$ChangeLogModuleInstance = Vtiger_Module::getInstance('ChangeLog');
				if ($ChangeLogModuleInstance) {
					$ChangeLogFocus = CRMEntity::getInstance('ChangeLog');
					$ChangeLogFocus->enableWidget($modulename);
				}
				
			}
			//crmv@29617e
			
			$em = new VTEventsManager($adb);
			$em->registerHandler('vtiger.entity.beforesave', "modules/{$modulename}/{$modulename}Handler.php", "{$modulename}Handler");
			
			SDK::setLanguageEntries('APP_STRINGS', 'MyNotes', array('it_it'=>'Note','en_us'=>'Notes'));
			SDK::setMenuButton('fixed','MyNotes',"openPopup('index.php?module=MyNotes&action=SimpleView');",'description','checkPermissionSDKButton:modules/MyNotes/widgets/Utils.php');
			
			$this->addWidgetToAll();
			
			// reload tabdata and other files to prevent errors in migrateNotebook2MyNotes
			$tmp_skip_recalculate = $_SESSION['skip_recalculate'];
			$_SESSION['skip_recalculate'] = 0;
			Vtiger_Access::syncSharingAccess();
			Vtiger_Menu::syncfile();
			Vtiger_Module::syncfile();
			$_SESSION['skip_recalculate'] = $tmp_skip_recalculate;
			
			$this->migrateNotebook2MyNotes();

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
	
	function save_related_module($module, $crmid, $with_module, $with_crmid, $skip_check=false) {
		return parent::save_related_module($module, $crmid, $with_module, $with_crmid, true);
	}
		
	static function getWidget($name) {
		if ($name == 'DetailViewMyNotesWidget' &&
				isPermitted('MyNotes', 'DetailView') == 'yes') {
			require_once dirname(__FILE__) . '/widgets/DetailViewMyNotesWidget.php';
			return (new MyNotes_DetailViewMyNotesWidget());
		}
		return false;
	}

	// crmv@106527
	function moduleHasNotes($module) {
		global $adb, $table_prefix;
		$tabid = getTabid($module);
		$res = $adb->pquery("SELECT linkid FROM {$table_prefix}_links WHERE tabid = ? AND linktype = ? AND linkurl = ?", array($tabid, 'DETAILVIEWWIDGET', 'block://MyNotes:modules/MyNotes/MyNotes.php'));
		return ($res && $adb->num_rows($res) > 0);
	}
	// crmv@106527e
	
	function addWidgetToAll() {
		global $adb,$table_prefix;
		$result = $adb->pquery('SELECT name FROM '.$table_prefix.'_tab WHERE isentitytype = 1 AND name NOT IN ('.generateQuestionMarks($this->skip_modules).')',$this->skip_modules);
		if ($result && $adb->num_rows($result) > 0) {
			$modcomm_module = array();
			while($row=$adb->fetchByAssoc($result)) {
				$modcomm_module[] = $row['name'];
			}
			$this->addWidgetTo($modcomm_module);
		}
		if (!empty($this->only_widget_modules)) {
			$this->addWidgetTo($this->only_widget_modules,true);
		}
	}
	
	function addWidgetTo($moduleNames, $onlyWidget=false, $widgetType='DETAILVIEWWIDGET', $widgetName='DetailViewMyNotesWidget') {
		if (empty($moduleNames)) return;
		
		global $adb, $table_prefix;
		include_once 'vtlib/Vtecrm/Module.php';
		
		$currentModuleInstance = Vtecrm_Module::getInstance('MyNotes');
		
		if (is_string($moduleNames)) $moduleNames = array($moduleNames);
		foreach($moduleNames as $moduleName) {
			$module = Vtecrm_Module::getInstance($moduleName);
			if($module) {
				$module->addLink($widgetType, $widgetName, "block://MyNotes:modules/MyNotes/MyNotes.php");
				if ($onlyWidget) continue;
				$check = $adb->pquery("SELECT * FROM ".$table_prefix."_relatedlists WHERE tabid=? AND related_tabid=? AND name=? AND label=?", 
					Array($currentModuleInstance->id, $module->id, 'get_related_list', $moduleName));
				if ($check && $adb->num_rows($check) > 0) {
					// do nothing
				} else {					
					$currentModuleInstance->setRelatedList($module, $moduleName, Array('SELECT','ADD'), 'get_related_list');
				}
			}
		}
	}
	
	/* 
	 * crmv@56114 if private mode I only my notes
	 * crmv@68000
	*/
	function getRelNotes($crmid,$limit='') {
		global $adb, $table_prefix, $current_user;
		$return = array();
		
		$parentModule = getSalesEntityType($crmid);
		if ($parentModule == 'Documents' || $parentModule == 'Products') {
			$relatedInstance = CRMEntity::getInstance($parentModule);
		} else {
			$relatedInstance = null;
		}
		if (!empty($relatedInstance)) {
			$relationTab = $relatedInstance->relation_table;
			$relationId = $relatedInstance->relation_table_id;
			$relationIdOther = $relatedInstance->relation_table_otherid;
			$relationModule = $relatedInstance->relation_table_othermodule;
		} else {
			$relationTab = "{$table_prefix}_crmentityrel";
			$relationId = 'relcrmid';
			$relationIdOther = 'crmid';
			$relationModule = 'relmodule';
		}
		
		$query = "SELECT {$table_prefix}_mynotes.mynotesid
					FROM {$table_prefix}_mynotes
					INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_mynotes.mynotesid = {$table_prefix}_crmentity.crmid
					INNER JOIN $relationTab ON {$table_prefix}_mynotes.mynotesid = {$relationTab}.{$relationIdOther}
					INNER JOIN {$table_prefix}_crmentity relEntity ON {$relationTab}.{$relationId} = relEntity.crmid
					WHERE {$table_prefix}_crmentity.deleted = 0 AND {$relationTab}.{$relationId} = ?";
		$params = array($crmid);
		
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
		if (empty($defaultOrgSharingPermission)) $defaultOrgSharingPermission = getAllDefaultSharingAction();
		if($defaultOrgSharingPermission[getTabid('MyNotes')] == 3) {
			$query .= " AND {$table_prefix}_crmentity.smownerid = ?";
			$params[] = $current_user->id;
		}
		
		$query .= " ORDER BY {$table_prefix}_crmentity.modifiedtime desc";
		
		if (!empty($limit)) {
			$result = $adb->limitPquery($query,0,$limit,$params);
		} else {
			$result = $adb->pquery($query,$params);
		}
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$return[] = $row['mynotesid'];
			}
		}
		return $return;
	}
	
	function getDetailViewNavigation($crmid) {
		$rel_notes = $this->getRelNotes($crmid);
		if (!empty($rel_notes)) {
			$current = array_search($this->id,$rel_notes);
			$str_current = ($current+1);
			$total = count($rel_notes);
			if ($total > 1) {
				$string = $str_current.' '.getTranslatedString('LBL_LIST_OF').' '.$total;
				$prev = $rel_notes[$current-1];
				$succ = $rel_notes[$current+1];
			}
		}
		return array($string,$prev,$succ);
	}
	
	function migrateNotebook2MyNotes() {
		global $adb, $table_prefix;
		if(Vtiger_Utils::CheckTable($table_prefix.'_notebook_contents')) {
			$query = "SELECT {$table_prefix}_notebook_contents.userid, stufftitle, contents
						FROM {$table_prefix}_homestuff
						INNER JOIN {$table_prefix}_notebook_contents ON {$table_prefix}_homestuff.stuffid = {$table_prefix}_notebook_contents.notebookid
						WHERE stufftype = ?";
			$result = $adb->pquery($query,array('Notebook'));
			$num_notebooks = $adb->num_rows($result);
			$num_mynotes = 0;
			if ($result && $num_notebooks > 0) {
				while($row=$adb->fetchByAssoc($result)) {
					$focus = CRMEntity::getInstance('MyNotes');
					$focus->column_fields['assigned_user_id'] = $row['userid'];
					$focus->column_fields['subject'] = $row['stufftitle'];
					$focus->column_fields['description'] = $row['contents'];
					$focus->save('MyNotes');
					if (!empty($focus->id)) {
						$num_mynotes++;
					}
				}
			}
			if ($num_mynotes == $num_notebooks) {
				$adb->pquery("delete from {$table_prefix}_homestuff where stufftype = ?",array('Notebook'));
				$sqlarray = $adb->datadict->DropTableSQL($table_prefix.'_notebook_contents');
				$adb->datadict->ExecuteSQLArray($sqlarray);
			}
		}
	}
	
	/* crmv@53684 crmv@56114 */
	function getAdvancedPermissionFunction($is_admin,$module,$actionname,$record_id) {
		if (!empty($record_id)) {
			global $current_user;
			
			require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
			if (empty($defaultOrgSharingPermission)) $defaultOrgSharingPermission = getAllDefaultSharingAction();
			if ($defaultOrgSharingPermission[getTabid('MyNotes')] != 3 && $actionname == 'DetailView') {
				return '';
			}
			
			$recordOwnerArr=getRecordOwnerId($record_id);
			foreach($recordOwnerArr as $type=>$id)
			{
				$recOwnType=$type;
				$recOwnId=$id;
			}
			if($current_user->id != $recOwnId) {
				return 'no';
			}
		}
	}
}
?>