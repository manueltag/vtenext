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

class ProjectPlan extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name;
    var $table_index= 'projectid';
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
		'Project Name'=> Array('project', 'projectname'),
		'Start Date'=> Array('project', 'startdate'),
		'Status'=>Array('project','projectstatus'),
		'Type'=>Array('project','projecttype'),
		'Assigned To' => Array('crmentity','smownerid')
    );
    var $list_fields_name = Array(
    /* Format: Field Label => fieldname */
		'Project Name'=> 'projectname',
		'Start Date'=> 'startdate',
		'Status'=>'projectstatus',
		'Type'=>'projecttype',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'projectname';

	// For Popup listview and UI type support
	var $search_fields = Array(
	/* Format: Field Label => Array(tablename, columnname) */
	// tablename should not have prefix 'vtiger_'
	'Project Name'=> Array('project', 'projectname'),
	'Start Date'=> Array('project', 'startdate'),
	'Status'=>Array('project','projectstatus'),
	'Type'=>Array('project','projecttype'),
	);
	var $search_fields_name = Array(
	/* Format: Field Label => fieldname */
	'Project Name'=> 'projectname',
	'Start Date'=> 'startdate',
	'Status'=>'projectstatus',
	'Type'=>'projecttype',
	);

	// For Popup window record selection
	var $popup_fields = Array('projectname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'projectname';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'projectname';

	// Required Information for enabling Import feature
	var $required_fields = Array('projectname'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'projectname';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'projectname');
	
	//crmv@10759
	var $search_base_field = 'projectname';
	//crmv@10759 e	

	function __construct() {
	    global $log, $currentModule;
	    global $table_prefix;
		parent::__construct(); // crmv@37004
		$this->table_name = $table_prefix.'_project';
	    $this->customFieldTable = Array($table_prefix.'_projectcf', 'projectid');
	    $this->tab_name = Array($table_prefix.'_crmentity', $table_prefix.'_project', $table_prefix.'_projectcf');
	    $this->tab_name_index = Array(
		$table_prefix.'_crmentity' => 'crmid',
		$table_prefix.'_project'   => 'projectid',
	    $table_prefix.'_projectcf' => 'projectid');
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

		$query .= $this->getNonAdminAccessControlQuery($thismodule,$current_user);
		$where_auto = " ".$table_prefix."_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";

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
			$sub_query = "SELECT $select_cols FROM  $this->table_name t " .
				" INNER JOIN ".$table_prefix."_crmentity crm ON crm.crmid = t.".$this->table_index;
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
			include_once('vtlib/Vtiger/Module.php');
			$moduleInstance = Vtiger_Module::getInstance($modulename);
			$projectTabid = $moduleInstance->id;

			// Mark the module as Standard module
			$adb->pquery('UPDATE '.$table_prefix.'_tab SET customized=0 WHERE name=?', array($modulename));

			// Add Project module to the related list of Accounts module
			$accountsModuleInstance = Vtiger_Module::getInstance('Accounts');			
			$accountsModuleInstance->setRelatedList($moduleInstance, 'Project Plans', Array('ADD','SELECT'), 'get_dependents_list');
						
			// Add Project module to the related list of Contacts module
			$contactsModuleInstance = Vtiger_Module::getInstance('Contacts');			
			$contactsModuleInstance->setRelatedList($moduleInstance, 'Project Plans', Array('ADD','SELECT'), 'get_dependents_list');
			
			// Add Project module to the related list of Vendors module
			$vendorsModuleInstance = Vtiger_Module::getInstance('Vendors');			
			$vendorsModuleInstance->setRelatedList($moduleInstance, 'ProjectPlan', array('ADD'), 'get_dependents_list');

			$modcommentsModuleInstance = Vtiger_Module::getInstance('ModComments');
			if($modcommentsModuleInstance) {
				include_once 'modules/ModComments/ModComments.php';
				if(class_exists('ModComments')) ModComments::addWidgetTo(array('ProjectPlan'));
			}
			
			//crmv@manuele		
			$adb->query("INSERT INTO ".$table_prefix."_parenttab VALUES (9,'ProjectPlan',9,0,0)");
			
			require_once('vtlib/Vtiger/Menu.php');
			$menu = Vtiger_Menu::getInstance('ProjectPlan');
			$menu->addModule(Vtiger_Module::getInstance('ProjectMilestone'));
			$menu->addModule(Vtiger_Module::getInstance('ProjectTask'));
			$menu->addModule($moduleInstance);
			
			$menu = Vtiger_Menu::getInstance('Support');
			$menu->removeModule(Vtiger_Module::getInstance('ProjectMilestone'));
			$menu->removeModule(Vtiger_Module::getInstance('ProjectTask'));
			$menu->removeModule($moduleInstance);
			
			create_tab_data_file();
			//crmv@manuele-e
			
			//crmv@18829
			$docModuleInstance = Vtiger_Module::getInstance('Documents');
			$docModuleInstance->setRelatedList($moduleInstance,'Project Plans',array('select','add'),'get_documents_dependents_list');
			//crmv@18829e
			
			//crmv@29506
			$HelpDeskModuleInstance = Vtiger_Module::getInstance('HelpDesk');
			$moduleInstance->setRelatedList($HelpDeskModuleInstance,'HelpDesk',array('add','select'),'get_dependents_list');
			//crmv@29506e
		
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
     * Here we override the parent's method,
     * This is done because the related lists for this module use a custom query
     * that queries the child module's table (column of the uitype10 field)
     *
     * @see data/CRMEntity#save_related_module($module, $crmid, $with_module, $with_crmid)
     */
    function save_related_module($module, $crmid, $with_module, $with_crmid) {
         if (!in_array($with_module, array('ProjectMilestone', 'ProjectTask'))) {
             parent::save_related_module($module, $crmid, $with_module, $with_crmid);
             return;
         }
        /** 
         * $_REQUEST['action']=='Save' when choosing ADD from Related list.
         * Do nothing on the project's entity when creating a related new child using ADD in relatedlist
         * by doing nothing we do not insert any line in the crmentity's table when
         * we are relating a module to this module
         */
        if ($_REQUEST['action'] != 'updateRelations') {
            return;
        }
        $_REQUEST['submode'] = 'no_html_conversion';
        //update the child elements' column value for uitype10
        $destinationModule = vtlib_purify($_REQUEST['destination_module']);
        if (!is_array($with_crmid)) $with_crmid = Array($with_crmid);
        foreach($with_crmid as $relcrmid) {
            $child = CRMEntity::getInstance($destinationModule);
            $child->retrieve_entity_info($relcrmid, $destinationModule);
            $child->mode='edit';
            $child->column_fields['projectid']=$crmid;
			//crmv@17662
			$child->id = $relcrmid;
            $child->save($destinationModule);
            //crmv@17662e
            //crmv@29617
			$obj = CRMEntity::getInstance('ModNotifications');
			$obj->saveRelatedModuleNotification($crmid, $module, $relcrmid, $with_module);
			//crmv@29617e
        }
    }
    
    /**
     * Here we override the parent's method
     * This is done because the related lists for this module use a custom query
     * that queries the child module's table (column of the uitype10 field)
     * 
     * @see data/CRMEntity#delete_related_module($module, $crmid, $with_module, $with_crmid)
     */
    function delete_related_module($module, $crmid, $with_module, $with_crmid) {
    	if (!in_array($with_module, array('ProjectMilestone', 'ProjectTask', 'HelpDesk'))) {	//crmv@29506
    		parent::delete_related_module($module, $crmid, $with_module, $with_crmid);
    		return;
    	}
        $destinationModule = vtlib_purify($_REQUEST['destination_module']);
        if (!is_array($with_crmid)) $with_crmid = Array($with_crmid);
        foreach($with_crmid as $relcrmid) {
            $child = CRMEntity::getInstance($destinationModule);
            $child->retrieve_entity_info($relcrmid, $destinationModule);
            $child->mode='edit';
            //crmv@29506
            if ($with_module == 'HelpDesk')
            	$child->column_fields['projectplanid']='';
            else
            //crmv@29506
            	$child->column_fields['projectid']='';
			//crmv@17662
			$child->id = $relcrmid;
            $child->save($destinationModule);
            //crmv@17662e
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

	//crmv@104562
	function getExtraDetailTabs() {
		global $app_strings;
		
		$return = array(
			array('label'=>getTranslatedString('LBL_PROGRESS_CHART'),'href'=>'','onclick'=>"changeDetailTab('{$this->modulename}', '{$this->id}', 'Gantt', this)")
		);
		$others = parent::getExtraDetailTabs() ?: array();

		return array_merge($return, $others);
	}
	function getExtraDetailBlock($selectionProcesses=false) {
		global $mod_strings, $app_strings;
		require_once('Smarty_setup.php');
		require_once('include/utils/EntityColorUtils.php');
		$smarty = new vtigerCRM_Smarty();
		$ECU = EntityColorUtils::getInstance();
		$smarty->assign('CURRENT_PATH','modules/ProjectPlan/thirdparty/jQueryGantt/');
		$smarty->assign('STATUS_COLORS', $ECU->getModuleColors('ProjectTask'));
		$extra = parent::getExtraDetailBlock();
		return $extra.$smarty->fetch('modules/ProjectPlan/GanttTab.tpl');
	}
	function getGanttHolidays($record) {
		global $adb, $table_prefix;
		$holidays = '';
		$result = $adb->pquery("SELECT MIN(startdate) AS \"startdate\", MAX(enddate) AS \"enddate\"
			FROM {$table_prefix}_projecttask pt 
			INNER JOIN {$table_prefix}_crmentity crment ON pt.projecttaskid=crment.crmid 
			WHERE projectid=? AND crment.deleted=0 AND pt.startdate IS NOT NULL AND pt.enddate IS NOT NULL",
			array($record));
		if ($result && $adb->num_rows($result) > 0) {
			$startdate = $adb->query_result($result,0,'startdate');
			$enddate = $adb->query_result($result,0,'enddate');
			$crmv_utils = CRMVUtils::getInstance();
			$holidays = $crmv_utils->getHolidays($startdate,$enddate,'jQueryGantt');
			if (!empty($holidays)) $holidays = '#'.implode('#',$holidays).'#';
		}
		return $holidays;
	}
	function getGanttContent($record,$return_mode='array') {
		global $adb, $table_prefix;
		require_once('include/utils/EntityColorUtils.php');
		$ECU = EntityColorUtils::getInstance();
			
		$tasks = array();
		$resources = array();
		$roles = array();
		// tasks
		$result = $adb->pquery("SELECT pt.*, crment.smownerid, role.roleid, role.rolename
			FROM {$table_prefix}_projecttask pt
			INNER JOIN {$table_prefix}_crmentity crment ON pt.projecttaskid=crment.crmid
			INNER JOIN {$table_prefix}_user2role u2r ON u2r.userid = smownerid
			INNER JOIN {$table_prefix}_role role ON role.roleid = u2r.roleid
			WHERE projectid=? AND crment.deleted=0 AND pt.startdate IS NOT NULL AND pt.enddate IS NOT NULL",
			array($record)) or die("Please install the ProjectMilestone and ProjectTasks modules first.");
		//ORDER BY pt.startdate ASC, pt.enddate DESC
		$resources_ids = array();
		$roles_ids = array();
		while($row=$adb->fetchByAssoc($result)){
			$startdate = $row['startdate'];
			if ($row['projecttaskprogress'] == "--none--") {
				$progress = 0;
			} else {
				$progress = str_replace("%","",$row['projecttaskprogress']);
			}
			$clvColor = $ECU->getEntityColor('ProjectTask',$row['projecttaskid']);
			(!empty($clvColor)) ? $status = 'COLOR_'.str_replace('#','',$clvColor) : $status = 'DEFAULT';
			
			$assigs = array();
			$tasks[] = array(
				'id'=>$row['projecttaskid'],
				'name'=>$row['projecttaskname'],
				'code'=>$row['projecttask_no'],
				'level'=>0,
				'status'=>$status,
				'start'=>strtotime($startdate)*1000,
				'duration'=>$row['working_days'],
				'end'=>strtotime($startdate)*1000 + ($row['working_days']*24*60*60),
				'startIsMilestone'=>false,
				'endIsMilestone'=>false,
				'collapsed'=>false,
				'depends'=>'',
				'hasChild'=>false,
        		'progress'=>$progress,
				'assigs'=>array(
					array(
						'resourceId'=>$row['smownerid'],
			            'id'=>$row['smownerid'],
			            'roleId'=>$row['roleid'],
			            'effort'=>0,	//TODO
					),
				),
			);
			if (!in_array($row['smownerid'],$resources_ids)) {
				$resources_ids[] = $row['smownerid'];
				$resources[] = array(
					'id'=>$row['smownerid'],
		            'name'=>getUserFullName($row['smownerid']),
					'img'=>getUserAvatar($row['smownerid']),
				);
			}
			if (!in_array($row['roleid'],$roles_ids)) {
				$roles_ids[] = $row['roleid'];
				$roles[] = array(
					'id'=>$row['roleid'],
		            'name'=>$row['rolename'],
				);
			}
		}
		// milestones
		$result = $adb->pquery("SELECT pm.*, smownerid FROM {$table_prefix}_projectmilestone pm 
			INNER JOIN {$table_prefix}_crmentity crment on pm.projectmilestoneid=crment.crmid 
			WHERE projectid=? and crment.deleted=0",
			array($record)) or die("Please install the ProjectMilestone and ProjectTasks modules first.");
		while($row=$adb->fetchByAssoc($result)){
			$tasks[] = array(
				'id'=>$row['projectmilestoneid'],
				'name'=>$row['projectmilestonename'],
				'code'=>$row['projectmilestone_no'],
				'level'=>0,
				'status'=>$status,
				'start'=>strtotime($row['projectmilestonedate'])*1000,
				'duration'=>1,
				'end'=>strtotime($row['projectmilestonedate'].' 23:59:59')*1000,
				'isMilestone'=>true,
			);
		}
		usort($tasks, create_function('$a, $b','return ($a[\'start\'] > $b[\'start\']);'));
		$return = array(
			'tasks'=>$tasks,
			'resources'=>$resources,
			'roles'=>$roles,
			'selectedRow'=>0,
			'canWrite'=>false,
			'canWriteOnParent'=>false,
		);
		if ($return_mode == 'json') {
			/* ex.
			 {"tasks":[
		     {"id":100,"name":"Gantt editor","code":"","level":0,"status":"s0","start":1396994400000,"duration":21,"end":1399672799999,"startIsMilestone":false,"endIsMilestone":false,"collapsed":false,"assigs":[],"hasChild":false}
		     ,{"id":2,"name":"coding","code":"","level":0,"status":"s2","start":1396994400000,"duration":10,"end":1398203999999,"startIsMilestone":false,"endIsMilestone":false,"collapsed":false,"assigs":[],"description":"","progress":0,"hasChild":false}
		     ,{"id":32,"name":"gantt part","code":"","level":0,"status":"s3","start":1396994400000,"duration":2,"end":1397167199999,"startIsMilestone":false,"endIsMilestone":false,"collapsed":false,"assigs":[],"depends":"","hasChild":false}
		     ,{"id":45,"name":"editor part","code":"","level":0,"status":"s1","start":1397167200000,"duration":4,"end":1397685599999,"startIsMilestone":false,"endIsMilestone":false,"collapsed":false,"assigs":[],"depends":"","hasChild":false}
		     ,{"id":56,"name":"testing","code":"","level":0,"status":"s1","start":1398981600000,"duration":6,"end":1399672799999,"startIsMilestone":false,"endIsMilestone":false,"collapsed":false,"assigs":[],"depends":"","description":"","progress":0,"hasChild":false}
		     ,{"id":678,"name":"test on safari","code":"","level":0,"status":"","start":1398981600000,"duration":2,"end":1399327199999,"startIsMilestone":false,"endIsMilestone":false,"collapsed":false,"assigs":[],"depends":"","hasChild":false}
		     ,{"id":700,"name":"test on ie","code":"","level":0,"status":"STATUS_UNDEFINED","start":1399327200000,"duration":3,"end":1399586399999,"startIsMilestone":false,"endIsMilestone":false,"collapsed":false,"assigs":[],"depends":"","hasChild":false}
		     ,{"id":8365,"name":"test on chrome","code":"","level":0,"status":"s2","start":1399327200000,"duration":2,"end":1399499999999,"startIsMilestone":false,"endIsMilestone":false,"collapsed":false,"assigs":[],"depends":"","hasChild":false}
		     ],"selectedRow":0,"canWrite":false,"canWriteOnParent":true}
			 */
			return Zend_Json::encode($return);
		} else {
			return $return;
		}
	}
	//crmv@104562e
}
?>