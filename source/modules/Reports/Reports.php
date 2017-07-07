<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once('include/database/PearDatabase.php');
require_once('data/CRMEntity.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/GetUserGroups.php');
require_once('include/Webservices/Utils.php');
require_once('include/Webservices/DescribeObject.php');

/* crmv@97862 crmv@100905 crmv@100399 */

class Reports extends SDKExtendableClass {

	var $tab_name = array(); // keep this for WS compatibility
	var $column_fields = Array();

	var $sort_values = Array();

	var $primodule;
	var $secmodule;
	var $columnssummary;
	var $columnscountsummary = array();  // crmv@29686

	var $folderid;

	var $adv_rel_fields = Array();

	var $module_list = Array();
	
	public $max_relation_levels = 4;
	public $max_grouping_levels = 7;
	
	/* IDs for fake fields and blocks */
	protected $maxTabid = 200;
	protected $tabidPB = 200;
	protected $baseFieldIdPB = 10000;
	protected $baseBlockIdPB = 10000;
	protected $baseTaxFieldId = 20000;
	protected $baseTaxBlockId = 20000;
	
	/* internal caches */
	static protected $subuser_cache = array();
	static protected $groups_cache = array();
	static protected $viewable_cache = array();
	static protected $editable_cache = array();
	static protected $exportable_cache = array();
	static protected $report_cache = array();

	// crmv@38798
	// various functions that can be applied to columns to alter the extracted value
	// uitypes are not used yet, but datatype is (see Reports.js)
	// parameters are {column} {param1} {param2}
	var $db_functions = array(
		'extract_year' => array(
			'label' => 'LBL_REP_EXTRACT_YEAR',
			'uitypes' => array(5,6),
			'wstypes' => array('date', 'datetime'),
			'sql' => array(
				'mysql' => 'EXTRACT(YEAR FROM {column})',
				'mssql' => 'DATEPART(YEAR, {column})',
				'oracle'=> 'EXTRACT(YEAR FROM {column})',
			),
		),
		'extract_quarter' => array(
			'label' => 'LBL_REP_EXTRACT_QUARTER',
			'uitypes' => array(5,6),
			'wstypes' => array('date', 'datetime'),
			'sql' => array(
				'mysql' => 'EXTRACT(QUARTER FROM {column})',
				'mssql' => 'DATEPART(QUARTER, {column})',
				'oracle'=> 'TO_CHAR({column}, \'Q\')',
			),
		),
		'extract_yearmonth' => array(
			'label' => 'LBL_REP_EXTRACT_YEARMONTH',
			'uitypes' => array(5,6),
			'wstypes' => array('date', 'datetime'),
			'sql' => array(
				'mysql' => 'DATE_FORMAT({column}, \'%Y-%m\')',
				'mssql' => 'CONVERT(CHAR(7), {column}, 120)',
				'oracle'=> 'TO_CHAR({column}, \'YYYY-MM\')',
			),
		),
		'extract_month' => array(
			'label' => 'LBL_REP_EXTRACT_MONTH',
			'uitypes' => array(5,6),
			'wstypes' => array('date', 'datetime'),
			'sql' => array(
				'mysql' => 'EXTRACT(MONTH FROM {column})',
				'mssql' => 'DATEPART(MONTH, {column})',
				'oracle'=> 'EXTRACT(MONTH FROM {column})',
			),
		),
		'extract_week' => array(
			'label' => 'LBL_REP_EXTRACT_WEEK',
			'uitypes' => array(5,6),
			'wstypes' => array('date', 'datetime'),
			'sql' => array(
				'mysql' => 'EXTRACT(WEEK FROM {column})',
				'mssql' => 'DATEPART(WEEK, {column})',
				'oracle'=> 'TO_CHAR({column}, \'WW\')',
			),
		),
		'extract_day' => array(
			'label' => 'LBL_REP_EXTRACT_DAY',
			'uitypes' => array(5,6),
			'wstypes' => array('date', 'datetime'),
			'sql' => array(
				'mysql' => 'EXTRACT(DAY FROM {column})',
				'mssql' => 'DATEPART(DAY, {column})',
				'oracle'=> 'EXTRACT(DAY FROM {column})',
			),
		),
	);
	// crmv@38798e


	public function __construct() {
		// nothing at the moment
	}
	
	public function getSubordinateUsers($userid = null) {
		global $current_user;
		
		if (!$userid) $userid = $current_user->id;
		
		if (!isset(self::$subuser_cache[$userid])) {
			$subordinate_users = Array();
			$user_array = getRoleAndSubordinateUsers($current_user->roleid,true);
			foreach ($user_array as $userid => $username) {
				$subordinate_users[$userid] = array(
					'userid' => $userid,
					'username' => $username,
					'label' => $username,
					'value' => "users::$userid",
				);
			}

			uasort($subordinate_users, function($a, $b) {
				return strcasecmp($a['label'], $b['label']);
			});

			self::$subuser_cache[$userid] = $subordinate_users;
		}
		
		return self::$subuser_cache[$userid];
	}
	
	public function getUserGroups($userid = null) {
		global $current_user;
		
		if (!$userid) $userid = $current_user->id;
		
		if (!isset(self::$groups_cache[$userid])) {
			
			$userGroups = new GetUserGroups();
			$userGroups->getAllUserGroups($userid);
			$user_groups = array();
			
			foreach ($userGroups->user_groups as $groupid) {
				$ginfo = getGroupDetails($groupid);
				$user_groups[$groupid] = array(
					'groupid' => $groupid,
					'groupname' => $ginfo[1],
					'label' => $ginfo[1],
					'value' => "groups::$groupid",
				);
			}

			uasort($user_groups, function($a, $b) {
				return strcasecmp($a['label'], $b['label']);
			});

			self::$groups_cache[$userid] = $user_groups;
		}
		
		return self::$groups_cache[$userid];
	}

	public function getModuleLabel($module) {
		if ($module == 'Calendar') {
			$trans = getTranslatedString('Tasks', 'APP_STRINGS');
		} elseif ($module == 'ProductsBlock') {
			$trans = getTranslatedString('LBL_RELATED_PRODUCTS', 'Settings');
		} else {
			$trans = getTranslatedString($module,$module);
		}
		return $trans;
	}
	
	public function getAvailableModules() {
		global $adb, $table_prefix;
		
		if (empty($this->module_list)) {
		
			$modules = Array();
			$restricted_tabs = getHideTab('hide_report');	//crmv@27711

			// get available modules
			if (is_array($restricted_tabs) && count($restricted_tabs) > 0) {
				$res = $adb->pquery("SELECT tabid,name FROM {$table_prefix}_tab WHERE presence IN (0,2) AND isentitytype = 1 AND tabid NOt IN (".generateQuestionMarks($restricted_tabs).")", $restricted_tabs);
			} else {
				$res = $adb->query("SELECT tabid,name FROM {$table_prefix}_tab WHERE presence IN (0,2) AND isentitytype = 1");
			}
			while ($row = $adb->FetchByAssoc($res, -1, false)) {
				$module = $row['name'];
				if (isPermitted($module,'index') == "yes") {
					$modules[$module] = $this->getModuleLabel($module);
					// add fake product block module
					if (isInventoryModule($module)) {
						$modules['ProductsBlock'] = getTranslatedString('LBL_RELATED_PRODUCTS', 'Settings');
					}
				}
			}
			// sort by name
			asort($modules);
			$this->module_list = $modules;
		}
			
		return $this->module_list;
	}

	// crmv@38798
	function get_available_functions() {
		$ret = array();
		foreach ($this->db_functions as $fkey => $finfo) {
			$ret[] = array(
				'name'=>$fkey,
				'label'=>getTranslatedString($finfo['label'], 'Reports'),
				'uitypes'=> $finfo['uitypes'],
				'wstypes'=> $finfo['wstypes'],
			);
		}
		return $ret;
	}

	// crmv@38798e


	/** Function to get the Listview of Reports
	 *  This function accepts no argument
	 *  This generate the Reports view page and returns a string
	 *  contains HTML
	 */
	function sgetRptFldr($mode='', $folderid = null) // crmv@30967
	{

		global $adb,$log,$mod_strings,$table_prefix;
		$returndata = Array();
		// crmv@30967
		$params = array($this->getTabId('Reports'));
		$sql = "select * from ".$table_prefix."_crmentityfolder where tabid = ? ";
		if (!is_null($folderid) && $folderid > 0) {
			$sql .= ' and folderid = ? ';
			$params[] = $folderid;
		}

		$sql .= ' order by foldername';
		$result = $adb->pquery($sql, $params);
		
		// Fetch detials of all reports of folder at once
		$reportsInAllFolders = $this->sgetRptsforFldr(false);

		// crmv@30967e
		$reportfldrow = $adb->fetch_array($result);
		if($mode != '')
		{
			do
			{
				if ((is_array($mode) && in_array($reportfldrow["state"], $mode)) || ($mode == $reportfldrow["state"]))
				{
					$details = Array();
					$details['state'] = $reportfldrow["state"];
					$details['id'] = $reportfldrow["folderid"];
					$details['name'] = ($mod_strings[$reportfldrow["foldername"]] == '' ) ? $reportfldrow["foldername"]:$mod_strings[$reportfldrow["foldername"]];
					$details['description'] = $reportfldrow["description"];
					$details['fname'] = popup_decode_html($details['name']);
					$details['fdescription'] = popup_decode_html($reportfldrow["description"]);
					$details['details'] = $reportsInAllFolders[$reportfldrow["folderid"]];
					$returndata[] = $details;
				}
			}while($reportfldrow = $adb->fetch_array($result));
		}else
		{
			do
			{
				$details = Array();
				$details['state'] = $reportfldrow["state"];
				$details['id'] = $reportfldrow["folderid"];
				$details['name'] = ($mod_strings[$reportfldrow["foldername"]] == '' ) ? $reportfldrow["foldername"]:$mod_strings[$reportfldrow["foldername"]];
				$details['description'] = $reportfldrow["description"];
				$details['fname'] = popup_decode_html($details['name']);
				$details['fdescription'] = popup_decode_html($reportfldrow["description"]);
				$details['details'] = $reportsInAllFolders[$reportfldrow["folderid"]];
				$returndata[] = $details;
			}while($reportfldrow = $adb->fetch_array($result));
		}

		$log->info("Reports :: ListView->Successfully returned vtiger_report folder HTML");
		return $returndata;
	}

	// crmv@38798 - overridden
	function countAllRecordsInFolder($module, $folderid) {
		global $adb, $table_prefix;

		// find columnname
		$fieldinfo = array('columnname' => 'folderid', 'tablename'=>$table_prefix.'_report');

		$res = $adb->pquery("select count(*) as cnt from {$fieldinfo['tablename']} where {$fieldinfo['columnname']} = ?", array($folderid));
		if ($res) {
			return $adb->query_result_no_html($res, 0, 'cnt');
		}
		return false;
	}
	// crmv@38798e

	// crmv@30967
	function getFolderContent($folderid) {
		global $adb, $table_prefix, $current_user, $app_strings, $mod_strings;

		$folderall = $this->sgetRptsforFldr($folderid);
		$count = count($folderall);

		// limit to 5
		if (is_array($folderall))
			$folderall = array_slice($folderall, 0, 5); //crmv@30976

		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('FOLDERINFO', $folderinfo);
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MOD', $mod_strings);
		$smarty->assign('TOTALCOUNT', $count);
		$smarty->assign('FOLDERDATA', $folderall);

		$html = $smarty->fetch('modules/Reports/FolderTooltip.tpl');

		return array('count'=>$count, 'html'=>$html);
	}

	function getFolderList() {
		$flds = getEntityFoldersByName(null, 'Reports');
		// translate folder names
		foreach ($flds as $k=>$fold) {
			$flds[$k]['foldername'] = getTranslatedString($fold['foldername'], 'Reports');
			$flds[$k]['description'] = getTranslatedString($fold['description'], 'Reports');
		}
		return $flds;
	}
	// crmv@30967e

	/** Function to get the Reports inside each modules
	 *  This function accepts the folderid
	 *  This Generates the Reports under each Reports module
	 *  This Returns a HTML sring
	 */
	function sgetRptsforFldr($rpt_fldr_id,$module=false)	//crmv@31775
	{
		$srptdetails="";
		global $adb, $table_prefix;
		global $log;
		global $mod_strings,$current_user;

		$returndata = Array();

		require_once('include/utils/UserInfoUtil.php');

		$sql = 
			"SELECT 
				r.*, rc.module, cf.folderid, cf.foldername
			FROM {$table_prefix}_report r
			LEFT JOIN {$table_prefix}_reportconfig rc ON rc.reportid = r.reportid
			INNER JOIN {$table_prefix}_crmentityfolder cf on cf.folderid = r.folderid";

		$params = array();

		// If information is required only for specific report folder?
		if($rpt_fldr_id !== false) {
			$sql .= " WHERE cf.folderid = ?";
			$params[] = $rpt_fldr_id;
			$haswhere = true;
		}

		require('user_privileges/requireUserPrivileges.php');
		require_once('include/utils/GetUserGroups.php');
		$userGroups = new GetUserGroups();
		$userGroups->getAllUserGroups($current_user->id);
		$user_groups = $userGroups->user_groups;
		if(!empty($user_groups) && $is_admin==false){
			$user_group_query = " (shareid IN (".generateQuestionMarks($user_groups).") AND setype='groups') OR";
			array_push($params, $user_groups);
		}

		$non_admin_query = " r.reportid IN (SELECT reportid from ".$table_prefix."_reportsharing WHERE $user_group_query (shareid=? AND setype='users'))";
		if($is_admin==false){
			$sql .= ($haswhere ? ' and ' : ' where ')." ( (".$non_admin_query.") or r.sharingtype='Public' or r.owner = ? or r.owner in(select ".$table_prefix."_user2role.userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%'))";
			array_push($params, $current_user->id);
			array_push($params, $current_user->id);
			$haswhere = true;	//crmv@31775
		}
		$query = $adb->pquery("select userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%'",array());
		$subordinate_users = Array();
		for($i=0;$i<$adb->num_rows($query);$i++){
			$subordinate_users[] = $adb->query_result($query,$i,'userid');
		}

		// order reports by name
		$sql .= " ORDER BY r.reportname";

		$result = $adb->pquery($sql, $params);

		while ($report = $adb->FetchByAssoc($result)) {
			$modules = $this->getAllModules($report["reportid"]);
			if ($module && !in_array($module, $modules)) continue;
			
			$report_details = Array();
			$report_details ['customizable'] = $report["customizable"];
			$report_details ['reportid'] = $report["reportid"];
			$report_details ['folderid'] = $report["folderid"];
			$report_details ['owner'] = getUserName($report["owner"]);
			$report_details ['module'] = $report["module"];
			$report_details ['primarymodule'] = $report["module"]; // kept for compatibility
			$report_details ['modules'] = $modules;
			$report_details ['state'] = $report["state"];
			$report_details ['description'] = $report["description"];
			$report_details ['reportname'] = $report["reportname"];
			$report_details ['sharingtype'] = $report["sharingtype"];
			$report_details ['foldername'] = $report["foldername"]; // crmv@30967
			if($is_admin==true || in_array($report["owner"],$subordinate_users) || $report["owner"]==$current_user->id)
				$report_details ['editable'] = 'true';
			else
				$report_details['editable'] = 'false';

			if(isPermitted($report["module"],'index') == "yes")
				$returndata[$report["folderid"]][] = $report_details;
		}

		// crmv@30967	//crmv@31775
		if($module === false) {
			if($rpt_fldr_id !== false) {
				$returndata = $returndata[$rpt_fldr_id];
			}
		}
		// crmv@30967e	//crmv@31775e

		$log->info("Reports :: ListView->Successfully returned vtiger_report details HTML");
		return $returndata;
	}

	// crmv@67929
	function generateTaxNames() {
		global $table_prefix;
		
		if (empty($this->taxNames) || empty($this->taxProdNames)) {
			$IUtils = InventoryUtils::getInstance();
			$allTaxes = $IUtils->getAllTaxes('all');
		
			$taxnames = array();
			$taxprodnames = array();
			foreach ($allTaxes as $tax) {
				$taxnames[$tax['taxname']] = getTranslatedString('LBL_TAX').' ('.$tax['taxlabel'].')';
				$taxprodnames[$tax['taxname']] = getTranslatedString('LBL_TAX').' '.getTranslatedString('LBL_PRODUCT').' ('.$tax['taxlabel'].')';
			}
			$taxnames['tax_total'] = getTranslatedString('LBL_TAX').' ('.getTranslatedString('LBL_TOTAL').')';
			$taxprodnames['tax_total'] = getTranslatedString('LBL_TAX').' '.getTranslatedString('LBL_PRODUCT').' ('.getTranslatedString('LBL_TOTAL').')';
			$this->taxNames = $taxnames;
			$this->taxProdNames = $taxprodnames;
		}
	}
	// crmv@67929e

	public function getStdFilterOptions($reportid, $selected = '') {
		$customview = new CustomView();
		$opts = $customview->getStdFilterCriteria($selected);
		return $opts;
	}
	
	/**
	 * Get a list of all the fields usable for standard filters
	 * Valid fields are those choosable among all the involved modules
	 */
	public function getStdFilterFields($reportid) {
		$config = $this->loadReport($reportid);
		
		$allfields = array();
		$allmods = $this->getAllModules($reportid);
		
		$chains = $this->getAllChains($reportid);
		
		foreach ($chains as $chainmod) {
			$list = $this->getStdFiltersFieldsListForChain($reportid, $chainmod['chain']);
			
			if (is_array($list)) {
				foreach($list as &$group) {
					foreach ($group['fields'] as &$fld) {
						$fld['label'] = $this->getModuleLabel($fld['module']) .' - ' . $fld['label'];
					}
					unset($fld);
				}
				unset($group);
			}
			$allfields += $list;
		}
		
		return $allfields;
	}

	/** Function to form a javascript to determine the start date and end date for a standard filter
	 *  This function is to form a javascript to determine
	 *  the start date and End date from the value selected in the combo lists
	 */
	function getCriteriaJS()
	{

		$today = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
		$tomorrow  = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
		$yesterday  = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")));

		$currentmonth0 = date("Y-m-d",mktime(0, 0, 0, date("m"), "01",   date("Y")));
		$currentmonth1 = date("Y-m-t");
		$lastmonth0 = date("Y-m-d",mktime(0, 0, 0, date("m")-1, "01",   date("Y")));
		//crmv@50067
		//$lastmonth1 = date("Y-m-t", strtotime("-1 Month"));
		$lastmonth1 = date("Y-m-t", strtotime($lastmonth0));
		//crmv@50067e
		$nextmonth0 = date("Y-m-d",mktime(0, 0, 0, date("m")+1, "01",   date("Y")));
		$nextmonth1 = date("Y-m-t", strtotime("+1 Month"));

		$lastweek0 = date("Y-m-d",strtotime("-2 week Sunday"));
		$lastweek1 = date("Y-m-d",strtotime("-1 week Saturday"));

		$thisweek0 = date("Y-m-d",strtotime("-1 week Sunday"));
		$thisweek1 = date("Y-m-d",strtotime("this Saturday"));

		$nextweek0 = date("Y-m-d",strtotime("this Sunday"));
		$nextweek1 = date("Y-m-d",strtotime("+1 week Saturday"));

		$next7days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+6, date("Y")));
		$next30days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+29, date("Y")));
		$next60days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+59, date("Y")));
		$next90days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+89, date("Y")));
		$next120days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+119, date("Y")));

		$last7days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-6, date("Y")));
		$last30days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-29, date("Y")));
		$last60days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-59, date("Y")));
		$last90days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-89, date("Y")));
		$last120days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-119, date("Y")));

		$currentFY0 = date("Y-m-d",mktime(0, 0, 0, "01", "01",   date("Y")));
		$currentFY1 = date("Y-m-t",mktime(0, 0, 0, "12", date("d"),   date("Y")));
		$lastFY0 = date("Y-m-d",mktime(0, 0, 0, "01", "01",   date("Y")-1));
		$lastFY1 = date("Y-m-t", mktime(0, 0, 0, "12", date("d"), date("Y")-1));
		$nextFY0 = date("Y-m-d",mktime(0, 0, 0, "01", "01",   date("Y")+1));
		$nextFY1 = date("Y-m-t", mktime(0, 0, 0, "12", date("d"), date("Y")+1));

		if(date("m") <= 3)
		{
			$cFq = date("Y-m-d",mktime(0, 0, 0, "01","01",date("Y")));
			$cFq1 = date("Y-m-d",mktime(0, 0, 0, "03","31",date("Y")));
			$nFq = date("Y-m-d",mktime(0, 0, 0, "04","01",date("Y")));
			$nFq1 = date("Y-m-d",mktime(0, 0, 0, "06","30",date("Y")));
			$pFq = date("Y-m-d",mktime(0, 0, 0, "10","01",date("Y")-1));
			$pFq1 = date("Y-m-d",mktime(0, 0, 0, "12","31",date("Y")-1));
		}else if(date("m") > 3 and date("m") <= 6)
		{
			$pFq = date("Y-m-d",mktime(0, 0, 0, "01","01",date("Y")));
			$pFq1 = date("Y-m-d",mktime(0, 0, 0, "03","31",date("Y")));
			$cFq = date("Y-m-d",mktime(0, 0, 0, "04","01",date("Y")));
			$cFq1 = date("Y-m-d",mktime(0, 0, 0, "06","30",date("Y")));
			$nFq = date("Y-m-d",mktime(0, 0, 0, "07","01",date("Y")));
			$nFq1 = date("Y-m-d",mktime(0, 0, 0, "09","30",date("Y")));

		}else if(date("m") > 6 and date("m") <= 9)
		{
			$nFq = date("Y-m-d",mktime(0, 0, 0, "10","01",date("Y")));
			$nFq1 = date("Y-m-d",mktime(0, 0, 0, "12","31",date("Y")));
			$pFq = date("Y-m-d",mktime(0, 0, 0, "04","01",date("Y")));
			$pFq1 = date("Y-m-d",mktime(0, 0, 0, "06","30",date("Y")));
			$cFq = date("Y-m-d",mktime(0, 0, 0, "07","01",date("Y")));
			$cFq1 = date("Y-m-d",mktime(0, 0, 0, "09","30",date("Y")));
		}
		else if(date("m") > 9 and date("m") <= 12)
		{
			$nFq = date("Y-m-d",mktime(0, 0, 0, "01","01",date("Y")+1));
			$nFq1 = date("Y-m-d",mktime(0, 0, 0, "03","31",date("Y")+1));
			$pFq = date("Y-m-d",mktime(0, 0, 0, "07","01",date("Y")));
			$pFq1 = date("Y-m-d",mktime(0, 0, 0, "09","30",date("Y")));
			$cFq = date("Y-m-d",mktime(0, 0, 0, "10","01",date("Y")));
			$cFq1 = date("Y-m-d",mktime(0, 0, 0, "12","31",date("Y")));

		}

		$sjsStr = '<script language="JavaScript" type="text/javaScript">
			function showDateRange( type )
			{
				if (type!="custom")
				{
					if (document.NewReport.startdate) {
						document.NewReport.startdate.readOnly=true;
					}
					if (document.NewReport.enddate) {
						document.NewReport.enddate.readOnly=true;
					}
					jQuery("#jscal_trigger_date_start").css("visibility", "hidden");
					jQuery("#jscal_trigger_date_end").css("visibility", "hidden");
				}
				else {
					if (document.NewReport.startdate) {
						document.NewReport.startdate.readOnly=false;
					}
					if (document.NewReport.enddate) {
						document.NewReport.enddate.readOnly=false;
					}
					jQuery("#jscal_trigger_date_start").css("visibility", "visible");
					jQuery("#jscal_trigger_date_end").css("visibility", "visible");
				}
				
				if (!document.NewReport.startdate) return;
				if (!document.NewReport.enddate) return;
				
				if( type == "today" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($today).'";
					document.NewReport.enddate.value = "'.getDisplayDate($today).'";
				}
				else if( type == "yesterday" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($yesterday).'";
					document.NewReport.enddate.value = "'.getDisplayDate($yesterday).'";
				}
				else if( type == "tomorrow" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($tomorrow).'";
					document.NewReport.enddate.value = "'.getDisplayDate($tomorrow).'";
				}
				else if( type == "thisweek" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($thisweek0).'";
					document.NewReport.enddate.value = "'.getDisplayDate($thisweek1).'";
				}
				else if( type == "lastweek" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($lastweek0).'";
					document.NewReport.enddate.value = "'.getDisplayDate($lastweek1).'";
				}
				else if( type == "nextweek" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($nextweek0).'";
					document.NewReport.enddate.value = "'.getDisplayDate($nextweek1).'";
				}

				else if( type == "thismonth" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($currentmonth0).'";
					document.NewReport.enddate.value = "'.getDisplayDate($currentmonth1).'";
				}

				else if( type == "lastmonth" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($lastmonth0).'";
					document.NewReport.enddate.value = "'.getDisplayDate($lastmonth1).'";
				}
				else if( type == "nextmonth" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($nextmonth0).'";
					document.NewReport.enddate.value = "'.getDisplayDate($nextmonth1).'";
				}
				else if( type == "next7days" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($today).'";
					document.NewReport.enddate.value = "'.getDisplayDate($next7days).'";
				}
				else if( type == "next30days" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($today).'";
					document.NewReport.enddate.value = "'.getDisplayDate($next30days).'";
				}
				else if( type == "next60days" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($today).'";
					document.NewReport.enddate.value = "'.getDisplayDate($next60days).'";
				}
				else if( type == "next90days" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($today).'";
					document.NewReport.enddate.value = "'.getDisplayDate($next90days).'";
				}
				else if( type == "next120days" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($today).'";
					document.NewReport.enddate.value = "'.getDisplayDate($next120days).'";
				}
				else if( type == "last7days" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($last7days).'";
					document.NewReport.enddate.value =  "'.getDisplayDate($today).'";
				}
				else if( type == "last30days" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($last30days).'";
					document.NewReport.enddate.value = "'.getDisplayDate($today).'";
				}
				else if( type == "last60days" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($last60days).'";
					document.NewReport.enddate.value = "'.getDisplayDate($today).'";
				}
				else if( type == "last90days" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($last90days).'";
					document.NewReport.enddate.value = "'.getDisplayDate($today).'";
				}
				else if( type == "last120days" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($last120days).'";
					document.NewReport.enddate.value = "'.getDisplayDate($today).'";
				}
				else if( type == "thisfy" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($currentFY0).'";
					document.NewReport.enddate.value = "'.getDisplayDate($currentFY1).'";
				}
				else if( type == "prevfy" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($lastFY0).'";
					document.NewReport.enddate.value = "'.getDisplayDate($lastFY1).'";
				}
				else if( type == "nextfy" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($nextFY0).'";
					document.NewReport.enddate.value = "'.getDisplayDate($nextFY1).'";
				}
				else if( type == "nextfq" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($nFq).'";
					document.NewReport.enddate.value = "'.getDisplayDate($nFq1).'";
				}
				else if( type == "prevfq" )
				{

					document.NewReport.startdate.value = "'.getDisplayDate($pFq).'";
					document.NewReport.enddate.value = "'.getDisplayDate($pFq1).'";
				}
				else if( type == "thisfq" )
				{
					document.NewReport.startdate.value = "'.getDisplayDate($cFq).'";
					document.NewReport.enddate.value = "'.getDisplayDate($cFq1).'";
				}
				else
				{
					document.NewReport.startdate.value = "";
					document.NewReport.enddate.value = "";
				}
			}
		</script>';

		return $sjsStr;
	}
	
	//crmv@26161
	function getAdvFilterOptions() {
		$adv_filter_options = array(
			"e" => getTranslatedString('equals', 'CustomView'),
			"n" => getTranslatedString('not equal to', 'CustomView'),
			"s" => getTranslatedString('starts with', 'CustomView'),
			"ew"=> getTranslatedString('ends with', 'CustomView'),
			"c" => getTranslatedString('contains', 'CustomView'),
			"k" => getTranslatedString('does not contain', 'CustomView'),
			"l" => getTranslatedString('less than', 'CustomView'),
			"g" => getTranslatedString('greater than', 'CustomView'),
			"m" => getTranslatedString('less or equal', 'CustomView'),
			"h" => getTranslatedString('greater or equal', 'CustomView'),
			"bw" => getTranslatedString('between', 'CustomView'),
			"a" => getTranslatedString('after', 'CustomView'),
			"b" => getTranslatedString('before', 'CustomView'),
		);
		array_walk($adv_filter_options, function(&$label) {
			$label = strtolower($label);
		});
		return $adv_filter_options;
	}
	//crmv@26161e

	//crmv@3085m
	function getEntityPreview($id,$module='Reports') {
		global $adb, $table_prefix;
		$this->Reports($id);
		$name = $this->reportname;
		$preview = array(
			'id'=>$id,
			'module'=>'Reports',
			'name'=>$name,
			'onclick'=>"location.href='index.php?module=Reports&action=SaveAndRun&tab=Charts&record=$id';",
		);
		$details = array();
		$preview['details'] = $details;
		return $preview;
	}
	//crmv@3085me
	
	public function isViewable($reportid) {
		global $current_user;
		
		$userid = $current_user->id;
		
		if (!isset(self::$viewable_cache[$reportid][$userid])) {
			$config = $this->loadReport($reportid);
			
			$owner = $config['owner'];
			$sharingType = $config['sharingtype'];
			
			$permitted = true;
			require('user_privileges/requireUserPrivileges.php');
			
			if (!$is_admin && $sharingType != 'Public' && $owner != $userid) {
				
				if ($sharingType == 'Private') {
					// I'm not admin and it's not mine, nobody else can see it
					// TODO: maybe upper users in the role chain??
					$permitted = false;
				} elseif ($sharingType == 'Shared') {
					// shared
					$subusers = $this->getSubordinateUsers($userid);
					$user_groups = $this->getUserGroups($userid);
				
					if (array_key_exists($owner, $subusers)) {
						$permitted = true;
					} elseif (is_array($config['sharing'])) {
						// check the sharing
						$permitted = false;
						foreach ($config['sharing'] as $share) {
							if ($share['setype'] == 'users' && $share['shareid'] == $userid) {
								// ok
								$permitted = true;
								break;
							} elseif ($share['setype'] == 'groups' && array_key_exists($share['shareid'], $user_groups)) {
								// ok
								$permitted = true;
								break;
							}
						}
					} else {
						$permitted = false;
					}
				}
			}

			// check single modules
			if (!$is_admin && $permitted) {
				$modules = $this->getAllModules($reportid);
				foreach ($modules as $mod) {
					if ($mod == 'ProductsBlock') continue;
					if (!vtlib_isModuleActive($mod) || isPermitted($mod,'index') != 'yes') {
						$permitted = false;
						break;
					}
				}
			}
			
			self::$viewable_cache[$reportid][$userid] = $permitted;
		}
		
		return self::$viewable_cache[$reportid][$userid];
	}
	
	public function isEditable($reportid, $userid = null) {
		global $current_user;
		if (!$userid) $userid = $current_user->id;
		
		if (!isset(self::$editable_cache[$reportid][$userid])) {
			$config = $this->loadReport($reportid);
			$subusers = $this->getSubordinateUsers($userid);
			$sharing = $config['sharingtype'];
		
			require('user_privileges/requireUserPrivileges.php');
			if ($is_admin==true || array_key_exists($config["owner"],$subusers) || $config["owner"]==$userid) {
				self::$editable_cache[$reportid][$userid] = true;
			} else {
				self::$editable_cache[$reportid][$userid] = false;
			}
		}
		
		return self::$editable_cache[$reportid][$userid];
	}
	
	public function isExportable($reportid) {
		global $current_user;
		
		$userid = $current_user->id;
		
		if (!isset(self::$exportable_cache[$reportid][$userid])) {
			$modules = $this->getAllModules($reportid);
			
			$permitted = true;
			foreach ($modules as $mod) {
				if ($mod == 'ProductsBlock') continue;
				if (!vtlib_isModuleActive($mod) || isPermitted($mod,'Export') != 'yes') {
					$permitted = false;
					break;
				}
			}
			
			self::$exportable_cache[$reportid][$userid] = $permitted;
		}
		
		return self::$exportable_cache[$reportid][$userid];
	}
	
	/**
	 * Return all the involved modules for this report
	 */
	public function getAllModules($reportid) {
		
		$modules = array();
		$config = $this->loadReport($reportid);
		
		$modules[] = $config['module'];
		if (is_array($config['relations'])) {
			foreach ($config['relations'] as $rel) {
				$mod = $rel['module'];
				$modules[] = $mod;
			}
		}
		
		$modules = array_unique($modules);
		
		return $modules;
	}
	
	/**
	 * Return all the involved modules and the relation chain for each
	 */
	public function getAllChains($reportid) {
		$modules = array();
		$config = $this->loadReport($reportid);
		
		$modules[$config['module']] = array(
			'module' => $config['module'],
			'chain' => array($config['module']),
		);
		if (is_array($config['relations'])) {
			foreach ($config['relations'] as $rel) {
				$mod = $rel['module'];
				if ($mod != 'ProductsBlock') {
					$modules[$rel['module']] = array(
						'module' => $rel['module'],
						'relation' => $rel['name'],
						'chain' => $this->getChainFromRelations($rel['name'], $config['relations']),
					);
				}
			}
		}
		
		$modules = array_values($modules);
		
		return $modules;
	}
	
	/**
	 * Load data of the specified report from the DB
	 */
	public function loadReport($reportid) {
		global $adb, $table_prefix;
		
		if (!self::$report_cache[$reportid]) {
		
			$blobs = array('relations', 'fields', 'stdfilters', 'advfilters', 'totals', 'summary');
			
			$config = null;
			$res = $adb->pquery("SELECT * FROM {$table_prefix}_report r INNER JOIN {$table_prefix}_reportconfig rc ON r.reportid = rc.reportid WHERE r.reportid = ?", array($reportid));
			if ($res && $adb->num_rows($res) > 0) {
				$config = $adb->fetchByAssoc($res, -1, false);
				unset($config['reportid']);
				foreach ($blobs as $column) {
					if (!empty($config[$column])) {
						$config[$column] = Zend_Json::decode($config[$column]);
					}
				}
				// add the sharing info
				if ($config['sharingtype'] == 'Shared') {
					$sharing = array();
					$res = $adb->pquery("SELECT * FROM {$table_prefix}_reportsharing WHERE reportid = ?", array($reportid));
					while ($row = $adb->FetchByAssoc($res, -1, false)) {
						unset($row['reportid']);
						$row['value'] = $row['setype'].'::'.$row['shareid'];
						$row['label'] = getOwnerName($row['shareid']);
						$sharing[] = $row;
					}
					$config['sharing'] = $sharing;
				}
			}
			
			self::$report_cache[$reportid] = $config;
		}
		
		return self::$report_cache[$reportid];
	}
	
	public function createChart($reportid, $chartinfo) {
		global $current_user;

		$focus = CRMEntity::getInstance('Charts');
		$focus->mode = '';
		
		$focus->column_fields['assigned_user_id'] = $current_user->id;
		$focus->column_fields['reportid'] = $reportid;
		
		foreach($focus->column_fields as $fieldname => $val) {
			if(isset($chartinfo[$fieldname])) {
				$value = trim($chartinfo[$fieldname]);
				$focus->column_fields[$fieldname] = $value;
			}
		}

		$focus->save('Charts');
		return $focus->id;
	}
	
	/**
	 * Checks whether a report with the provided id exists
	 */
	public function reportExists($reportid) {
		global $adb, $table_prefix;
		
		$res = $adb->pquery("SELECT r.reportid FROM {$table_prefix}_report r INNER JOIN {$table_prefix}_reportconfig rc ON r.reportid = rc.reportid WHERE r.reportid = ?", array($reportid));
		return ($res && $adb->num_rows($res) > 0);
	}
	
	/**
	 * Save the report to DB (update if existing)
	 */
	public function saveReport($reportid, $config) {
		if (!empty($reportid) && $this->reportExists($reportid)) {
			$r = $this->updateReport($reportid, $config);
		} else {
			$r = $this->insertReport($config);
		}

		return $r;
	}
	
	/**
	 * Insert a new report in the db
	 */
	public function insertReport($config) {
		global $adb, $table_prefix, $current_user;
		
		$reportid = $adb->getUniqueId($table_prefix.'_report');
		
		$columns1 = array(
			'reportid' => $reportid,
			'folderid' => intval($config['folderid']),
			'reportname' => $config['reportname'],
			'description' => $config['description'],
			'reporttype' => $config['reporttype'] ?: 'tabular',
			'state' => $config['state'] ?: 'CUSTOM',
			'customizable' => isset($config['customizable']) ? intval($config['customizable']) : 1,
			'owner' => $config['owner'] ?: $current_user->id,
			'sharingtype' => $config['sharingtype'] ?: 'Public',
		);
		
		$columns = array_keys($columns1);
		$adb->format_columns($columns);
		$adb->pquery("INSERT INTO {$table_prefix}_report (".implode(',',$columns).") VALUES (".generateQuestionMarks($columns1).")", $columns1);
		
		$columns2 = array(
			'reportid' => $reportid,
			'module' => $config['module'],
		);
		$columns = array_keys($columns2);
		$adb->format_columns($columns);
		$adb->pquery("INSERT INTO {$table_prefix}_reportconfig (".implode(',',$columns).") VALUES (".generateQuestionMarks($columns2).")", $columns2);
		
		$blobs = array(
			'relations' => $config['relations'],
			'fields' => $config['fields'],
			'stdfilters' => $config['stdfilters'],
			'advfilters' => $config['advfilters'],
			'totals' => $config['totals'],
			'summary' => $config['summary'],
		);
		
		foreach ($blobs as $column => $data) {
			if (!empty($data)) {
				$adb->updateClob($table_prefix.'_reportconfig',$column,"reportid = $reportid",Zend_Json::encode($data));
			}
		}
		
		// sharing
		if ($config['sharingtype'] == 'Shared' && is_array($config['sharing'])) {
			foreach ($config['sharing'] as $share) {
				$rshare = array(
					'reportid' => $reportid,
					'shareid' => $share['shareid'],
					'setype' => $share['setype'],
				);
				$adb->pquery("INSERT INTO {$table_prefix}_reportsharing (".implode(',', array_keys($rshare)).") VALUES (".generateQuestionMarks($rshare).")", $rshare);
			}
		}
		
		self::$report_cache[$reportid] = $config;
		
		return $reportid;
	}
	
	/**
	 * Update an existing report
	 */
	public function updateReport($reportid, $config) {
		global $adb, $table_prefix;
		
		$reportid = intval($reportid);
		
		// fields that must be passed always passed
		$columns1 = array(
			'folderid' => intval($config['folderid']),
			'reportname' => $config['reportname'],
			'description' => $config['description'],
		);
		
		// optional fields
		if ($config['reporttype']) $columns1['reporttype'] = $config['reporttype'];
		if (isset($config['customizable'])) $columns1['customizable'] = intval($config['customizable']);
		if ($config['state']) $columns1['state'] = $config['state'];
		if ($config['owner']) $columns1['owner'] = $config['owner'];
		if ($config['sharingtype']) $columns1['sharingtype'] = $config['sharingtype'];
		
		$upd = array();
		foreach ($columns1 as $col => $value) {
			$upd[] = "$col = ?";
		}
		
		$sql = "UPDATE {$table_prefix}_report SET ".implode(',', $upd)." WHERE reportid = ?";
		$adb->pquery($sql, array($columns1, $reportid));
		
		$columns2 = array(
			'module' => $config['module'],
		);
		
		$upd = array();
		foreach ($columns2 as $col => $value) {
			$upd[] = "$col = ?";
		}
		
		$sql = "UPDATE {$table_prefix}_reportconfig SET ".implode(',', $upd)." WHERE reportid = ?";
		$adb->pquery($sql, array($columns2, $reportid));
		
		$blobs = array(
			'relations' => $config['relations'],
			'fields' => $config['fields'],
			'stdfilters' => $config['stdfilters'],
			'advfilters' => $config['advfilters'],
			'totals' => $config['totals'],
			'summary' => $config['summary'],
		);
		
		foreach ($blobs as $column => $data) {
			if (empty($data)) {
				$adb->pquery("UPDATE {$table_prefix}_reportconfig SET $column = NULL WHERE reportid = ?", array($reportid));
			} else {
				$adb->updateClob($table_prefix.'_reportconfig',$column,"reportid = $reportid",Zend_Json::encode($data));
			}
		}
		
		// sharing
		$adb->pquery("DELETE FROM {$table_prefix}_reportsharing WHERE reportid = ?", array($reportid));
		if ($config['sharingtype'] == 'Shared' && is_array($config['sharing'])) {
			foreach ($config['sharing'] as $share) {
				$rshare = array(
					'reportid' => $reportid,
					'shareid' => $share['shareid'],
					'setype' => $share['setype'],
				);
				$adb->pquery("INSERT INTO {$table_prefix}_reportsharing (".implode(',', array_keys($rshare)).") VALUES (".generateQuestionMarks($rshare).")", $rshare);
			}
		}
		
		self::$report_cache[$reportid] = $config;
		
		return $reportid;
	}
	
	public function deleteReport($reportid) {
		global $adb,$table_prefix;
		
		unset(self::$report_cache[$reportid]);
		
		$adb->pquery("DELETE FROM ".$table_prefix."_report WHERE reportid = ?", array($reportid));
		$adb->pquery("DELETE FROM ".$table_prefix."_reportconfig WHERE reportid = ?", array($reportid));
		$adb->pquery("DELETE FROM ".$table_prefix."_reportsharing WHERE reportid = ?", array($reportid));

		// crmv@42024 - delete chart and summary data
		for ($i=1; $i<=$this->max_grouping_levels; ++$i) {
			$adb->pquery("DELETE FROM vte_rep_count_liv{$i} where reportid = ?", array($reportid));
		}
		$adb->pquery("DELETE FROM vte_rep_count_levels where reportid = ?", array($reportid));
		$res = $adb->pquery("SELECT {$table_prefix}_charts.chartid FROM {$table_prefix}_charts inner join {$table_prefix}_crmentity on {$table_prefix}_crmentity.crmid = {$table_prefix}_charts.chartid where deleted = 0 and reportid = ?", array($reportid));
		if ($res && $adb->num_rows($res) > 0) {
			$chartFocus = CRMEntity::getInstance('Charts');
			while ($row = $adb->FetchByAssoc($res, -1, false)) {
				$chid = $row['chartid'];
				$chartFocus->id = $chid;
				DeleteEntity('Charts', 'Charts', $chartFocus, $chid, null);
			}
		}
		// crmv@42024e
		
		$adb->pquery("UPDATE ".$table_prefix."_customview SET reportid = 0 WHERE reportid = ?", array($reportid)); //crmv@40613
	}

	// crmv@101691
	/**
	 * Remove a field from all the reports
	 */
	public function deleteFieldFromAll($fieldid) {
		global $adb, $table_prefix;
		
		$sql = 
			"SELECT r.reportid FROM {$table_prefix}_report r
			INNER JOIN {$table_prefix}_reportconfig rc ON rc.reportid = r.reportid
			WHERE r.state != 'SDK'";
		$res = $adb->query($sql);
		if ($res && $adb->num_rows($res) > 0) {
			while ($row = $adb->FetchByAssoc($res, -1, false)) {
				$this->deleteFieldFromReport($row['reportid'], $fieldid);
			}
		}
	}
	
	/**
	 * Remove a field from a report
	 */
	public function deleteFieldFromReport($reportid, $fieldid) {
		$config = $this->loadReport($reportid);
		
		$newFields = array();
		foreach ($config['fields'] as $field) {
			if ($field['fieldid'] != $fieldid) {
				$newFields[] = $field;
			}
		}
		$config['fields'] = $newFields;
		
		// stdfilters
		if (is_array($config['stdfilters'])) {
			$newStdFilters = array();
			foreach ($config['stdfilters'] as $cond) {
				if ($cond['fieldid'] != $fieldid) {
					$newStdFilters[] = $field;
				}
			}
			$config['stdfilters'] = $newStdFilters;
		}
		
		// advanced filters
		if (is_array($config['advfilters'])) {
			$newAdvFilters = array();
			foreach ($config['advfilters'] as $group) {
				if ($group['conditions']) {
					$newConds = array();
					foreach ($group['conditions'] as $cond) {
						if ($cond['fieldid'] != $fieldid && (empty($cond['ref_fieldid']) || $cond['ref_fieldid'] != $fieldid)) {
							$newConds[] = $cond;
						}
					}
					$group['conditions'] = $newConds;
					$newAdvFilters[] = $group;
				}
			}
			$config['advfilters'] = $newAdvFilters;
		}
		
		// totals and summary
		if (is_array($config['totals'])) {
			$newTotals = array();
			foreach ($config['totals'] as $field) {
				if ($field['fieldid'] != $fieldid) {
					$newTotals[] = $field;
				}
			}
			$config['totals'] = $newTotals;
		}
		
		// now save it again
		
		return $this->updateReport($reportid, $config);
	}
	// crmv@101691e

	
	public function getRelations($reportid) {
		$config = $this->loadReport($reportid);
		
		return $config['relations'];
		
		/*
		Format of Relations:
		
		array(
			array(
				'name' => 'Contacts',		// name of the relation, if it's the root, then the module name
				'module' => 'Contacts',		// module of the relation, can be the special ProductsBlock
				'parent' => null,			// parent relation, null for the root
			),
			array(
				// this is a NtoN relation
				'name' => 'Contacts_Products_rel_21',	// relation name, which sould be [Module1]_[Module2]_rel_[RelationID]
				'module' => 'Products',					// module
				'parent' => 'Contacts',					// name of the parent relation
				'type' => ModuleRelation::$TYPE_NTON,	// type of the relation, should be NTON
				'relationid' => 21,						// id of the relation, (can be a fake id)
			),
			array(
				'name' => 'Contacts_Accounts_fld_75',	// for 1-N or N-1 the last part is _fld_[FIELDID]
				'module' => 'Accounts',				
				'parent' => 'Contacts',
				'type' => ModuleRelation::$TYPE_NTO1,	// here is NTO1 or 1TON
				'fieldid' => 75,						// here the fieldid must be specified, the relationid is not used
			),
		);
		
		*/
	}
	
	public function getColumns($reportid) {
		$config = $this->loadReport($reportid);
		return $config['fields'];
		
		/*
		Format of Fields:
		
		array(
			array(
				'fieldid' => 449,			// basic form, just the fieldid
											// if no parent is specified, the root module is used
			),
			array(
				'fieldid' => 453,
				'parent' => 'Contacts_Accounts_fld_75',		// the name of the parent relation
			),
			/*array(
				'fieldid' => 22,			
				'group' => true,				// group by this field
				'sortorder' => 'ASC',			// with this order
				'summary' => true,				// show it in summary as well
			),
			array(
				'module' => 'Events',
				'fieldid' => 268,
				'formula' => 'extract_year',	// apply a formula to the field
			),
		);
		*/
	}
	
	public function getTotalColumns($reportid) {
		$config = $this->loadReport($reportid);
		return $config['totals'];
		
		/*
		Format of Totals:
		
		array(
			array(
				'fieldid' => 9,					// fieldid
				'relation' => null,				// you can also specify the relation
				'aggregator' => 'SUM',			// which formula to use, to use more than one, specify the field more than once
			),
		);		
		*/
	}
	
	public function getSummaryColumns($reportid) {
		$config = $this->loadReport($reportid);
		return $config['summary'];
		
		/*
		Format of Summary columns:
		
		You can have maximum one column here
		
		array(
			array(
				'fieldid' => 9,									// the fieldid
				'aggregators' => array('SUM', 'MAX', 'MIN'),	// the aggregator formulas to use
			),
		);
		*/
	}
	
	public function getStdFilters($reportid) {
		$config = $this->loadReport($reportid);
		return $config['stdfilters'];
		
		/*
		Format of Std filters:
		array(
			array(
				'fieldid' => 98,				// fieldid
				'relation' => null,				// you can specify the relation
				'type' => 'datefilter',			// type of the std filter, only "datefilter" is supported
				'value' => 'thisfy',			// value for the datefilter
				'startdate' => '',				// if custom, here is the start date
				'enddate' => '',				// and end date
			)
		);
		*/
	}
	
	public function getAdvFilters($reportid) {
		$config = $this->loadReport($reportid);
		return $config['advfilters'];
		
		/*
		Format of advanced filters:
		
		$advfilters = array(
			// groups
			array(
				'conditions' => array(					// conditions for this group
					array(
						'fieldid' => 70,				// fieldid
						'comparator' => 'c',			// comparator
						'value' => 'b',					// value to compare with
						'glue' => 'or'					// glue condition for the next field, you can omit if it's the last field
					),
					array(
						'fieldid' => 90,
						'comparator' => 'e',
						'value' => 'admin',
					),
				),
				'glue' => 'and',						// glue for the next group
			),
			array(
				'conditions' => array(
					array(
						'fieldid' => 78,
						'comparator' => 'k',
						'reference' => true,			// this is a reference comparison, no need to use "value"
						'ref_fieldid' => 123,			// but you should specify the comparison fieldid
						'ref_relation' => 'ddfgdfg'		// and relation name
					),
				),
			),
		);
		*/
	}
	
	/**
	 * Prepare the report config for the edit panel, adding necessary fields and other informations
	 */
	public function prepareForEdit(&$config) {
		
		// fields
		foreach ($config['fields'] as &$field) {
			$finfo = $this->getFieldInfoById($field['fieldid']);
			if ($finfo['label']) {
				$field['label'] = $finfo['label'];
			} else {
				$field['label'] = getTranslatedString($finfo['fieldlabel'], $finfo['module']);
			}
			// value used in the <option> tag
			$chain = $this->getChainFromRelations($field['relation'], $config['relations']);
			$name = array(
				'fieldid' => intval($field['fieldid']),
				'chain' => $chain,
			);
			if ($field['formula']) {
				$name['formula'] = $field['formula'];
			}
			if ($field['group']) {
				$name['group'] = true;
				$name['sortorder'] = strtoupper($field['sortorder']) ?: 'ASC';
				if ($field['summary']) {
					$name['summary'] = true;
				}
			}
			$field['name'] = Zend_Json::encode($name);
			$field['module'] = $finfo['module'];
			if ($finfo['module'] == 'ProductsBlock') {
				$field['single_label'] = getTranslatedString('LBL_RELATED_PRODUCTS', 'Settings');
			} else {
				$field['single_label'] = getTranslatedString('SINGLE_'.$finfo['module'], $finfo['module']);
			}
			$field['fieldname'] = $finfo['fieldname'];
			$field['wstype'] = $finfo['wstype'];
			$field['uitype'] = intval($finfo['uitype']);
		}
		unset($field);
		
		// stdfilters
		if (is_array($config['stdfilters'])) {
			foreach ($config['stdfilters'] as &$cond) {
				$finfo = $this->getFieldInfoById($cond['fieldid']);
				if ($finfo['label']) {
					$cond['label'] = $finfo['label'];
				} else {
					$cond['label'] = getTranslatedString($finfo['fieldlabel'], $finfo['module']);
				}
				$chain = $this->getChainFromRelations($cond['relation'], $config['relations']);
				$name = array(
					'fieldid' => intval($finfo['fieldid']),
					'chain' => $chain,
				);
				$cond['name'] = Zend_Json::encode($name);
				$cond['module'] = $finfo['module'];
				$cond['fieldname'] = $finfo['fieldname'];
				$cond['wstype'] = $finfo['wstype'];
				$cond['uitype'] = intval($finfo['uitype']);

				// crmv@106298
				if ($cond['value'] == 'custom') {
					// convert date from db format
					if ($cond['startdate']) $cond['startdate'] = getDisplayDate($cond['startdate']);
					if ($cond['enddate']) $cond['enddate'] = getDisplayDate($cond['enddate']);
				}
				// crmv@106298e

				// add the list of fields and modules chain
				$listmodules = array();
				for ($i=0; $i<count($chain); ++$i) {
					$subchain = array_slice($chain, 0, $i+1);
					$modules = $this->getModulesListForChain($config['reportid'], $subchain);
					$listmodules[] = array(
						'selected' => end($subchain),
						'list' => $modules,
					);
				}
				$listfields = $this->getStdFiltersFieldsListForChain($config['reportid'], $chain);
				$cond['listmodules'] = $listmodules;
				$cond['listfields'] = $listfields;
			}
			unset($cond);
		}
		
		// advanced filters
		if (is_array($config['advfilters'])) {
			foreach ($config['advfilters'] as &$group) {
				if ($group['conditions']) {
					foreach ($group['conditions'] as &$cond) {
						$finfo = $this->getFieldInfoById($cond['fieldid']);
						if ($finfo['label']) {
							$cond['label'] = $finfo['label'];
						} else {
							$cond['label'] = getTranslatedString($finfo['fieldlabel'], $finfo['module']);
						}
						$chain = $this->getChainFromRelations($cond['relation'], $config['relations']);
						$name = array(
							'fieldid' => intval($finfo['fieldid']),
							'chain' => $chain,
						);
						$cond['name'] = Zend_Json::encode($name);
						$cond['module'] = $finfo['module'];
						$cond['fieldname'] = $finfo['fieldname'];
						$cond['wstype'] = $finfo['wstype'];
						$cond['uitype'] = intval($finfo['uitype']);
						
						if ($cond['wstype'] == 'date' || $cond['wstype'] == 'datetime') {
							if ($cond['wstype'] == 'datetime') {
								// timezone adjustment
								$cond['value'] = adjustTimezone($cond['value'], 0, null, false);
							}
							// convert the date to the user format
							$cond['value'] = getDisplayDate($cond['value']);
							if ($cond['value2']) {
								if ($cond['wstype'] == 'datetime') {
									// timezone adjustment
									$cond['value2'] = adjustTimezone($cond['value2'], 0, null, false);
								}
								// convert the date to the user format
								$cond['value2'] = getDisplayDate($cond['value2']);
							}
						} elseif ($cond['wstype'] == 'time') {
							// adjust timezone for time fields
							$cond['value'] = adjustTimezone($cond['value'], 0, null, false);
							if ($cond['value2']) {
								$cond['value2'] = adjustTimezone($cond['value2'], 0, null, false);
							}
						}
						
						// reference
						if ($cond['reference']) {
							$relfinfo = $this->getFieldInfoById($cond['ref_fieldid']);
							if ($relfinfo['label']) {
								$cond['reflabel'] = $relfinfo['label'];
							} else {
								$cond['reflabel'] = getTranslatedString($relfinfo['fieldlabel'], $relfinfo['module']);
							}
							$refchain = $this->getChainFromRelations($cond['ref_relation'], $config['relations']);
							$name = array(
								'fieldid' => intval($cond['ref_fieldid']),
								'chain' => $refchain,
							);
							$cond['refvalue'] = Zend_Json::encode($name);
						}

						// add the list of fields and modules chain
						$listmodules = array();
						for ($i=0; $i<count($chain); ++$i) {
							$subchain = array_slice($chain, 0, $i+1);
							$modules = $this->getModulesListForChain($config['reportid'], $subchain);
							$listmodules[] = array(
								'selected' => end($subchain),
								'list' => $modules,
							);
						}
						$listfields = $this->getAdvFiltersFieldsListForChain($config['reportid'], $chain);
						$cond['listmodules'] = $listmodules;
						$cond['listfields'] = $listfields;
					}
					unset($cond);
				}
			}
			unset($group);
		}
		
		// totals and summary
		if (is_array($config['totals'])) {
			$totals = array();
			foreach ($config['totals'] as $field) {
				$finfo = $this->getFieldInfoById($field['fieldid']);
				if ($finfo['label']) {
					$field['label'] = $finfo['label'];
				} else {
					$field['label'] = getTranslatedString($finfo['fieldlabel'], $finfo['module']);
				}
				// value used in the <option> tag
				$chain = $this->getChainFromRelations($field['relation'], $config['relations']);
				$name = array(
					'fieldid' => intval($field['fieldid']),
					'chain' => $chain,
				);
				$field['name'] = Zend_Json::encode($name);
				$field['module'] = $finfo['module'];
				$field['fieldname'] = $finfo['fieldname'];
				$field['wstype'] = $finfo['wstype'];
				$field['uitype'] = intval($finfo['uitype']);
				
				// check if it's in summary
				if (is_array($config['summary'])) {
					foreach ($config['summary'] as $sfield) {
						if ($sfield['fieldid'] == $field['fieldid']) $field['summary'] = true;
					}
				}
				
				// add the list of fields and modules chain
				$listmodules = array();
				for ($i=0; $i<count($chain); ++$i) {
					$subchain = array_slice($chain, 0, $i+1);
					$modules = $this->getModulesListForChain($config['reportid'], $subchain);
					$listmodules[] = array(
						'selected' => end($subchain),
						'list' => $modules,
					);
				}
				$listfields = $this->getTotalsFieldsListForChain($config['reportid'], $chain);
				$field['listmodules'] = $listmodules;
				$field['listfields'] = $listfields;
				
				if (array_key_exists($field['name'], $totals)) {
					// add the formula
					$totals[$field['name']]['aggregators'][] = $field['aggregator'];
				} else {
					$field['aggregators'] = array($field['aggregator']);
					unset($field['aggregator']);
					$totals[$field['name']] = $field;
				}
			}
			$config['totals'] = array_values($totals);
		}
	}
	
	/**
	 * Prepare the data from the request, in the config format, ready to be saved
	 */
	public function prepareForSave($reportid, &$request) {
		$config = array();
		
		$mode = ($reportid > 0 ? 'edit' : 'create');
		
		if ($mode == 'create') {
			$oldConfig = array();
			$config['module'] = vtlib_purify($request['primarymodule']);
			if (empty($config['module'])) throw new Exception("Primary module not specified");
			if (isPermitted($config['module'], 'index') != 'yes') {
				throw new Exception("You don't have the permission to read this module");
			}
			
		} else {
			$oldConfig = $this->loadReport($reportid);
			$config['module'] = $oldConfig['module'];
			$config['state'] = $oldConfig['state'];
			$config['owner'] = $oldConfig['owner'];
		}

		$config['reportname'] = vtlib_purify($request['reportname']);
		if (empty($config['reportname'])) throw new Exception("No report name specified");
		
		if (!empty($request['reportnewfolder'])) {
			// create the folder
			$folderid = $this->createFolder($request['reportnewfolder']);
			if (!$folderid) throw new Exception("Unable to create the report folder");
			$config['folderid'] = $folderid;
		} elseif (!empty($request['reportfolder'])) {
			$config['folderid'] = intval($request['reportfolder']);
		} else {
			throw new Exception("No folder specified");
		}
		
		$config['description'] = vtlib_purify($request['reportdes']);
		
		if (!empty($request['rep_assigned_to'])) {
			$config['owner'] = $request['rep_assigned_to'];
		}
		
		$config['reporttype'] = strtolower($request['reportType']) == 'summary' ? 'summary' : 'tabular';
		
		$fields = Zend_Json::decode($request['selectedfields']);
		
		if (empty($fields) || !is_array($fields)) {
			throw new Exception("No fields specified");
		}
		
		// now build the relations from the fields
		$relations = array();
		
		// add primary module
		$relations[$config['module']] = array(
			'name' => $config['module'],
			'module' => $config['module'],
		);
		
		$cfgFields = array();
		foreach ($fields as $fld) {
			$cfield = array(
				'fieldid' => $fld['fieldid'],
			);
			if ($fld['chain'] && count($fld['chain']) > 1) {
				$fieldrels = $this->getRelationsFromChain($fld['chain']);
				$lastrel = end($fieldrels);
				$relations = array_merge($relations, $fieldrels);
				$cfield['relation'] = $lastrel['name'];
			}
			if ($fld['group']) {
				$cfield['group'] = true;
				$cfield['sortorder'] = ($fld['sortorder'] == 'DESC' ? 'DESC' : 'ASC');
				if ($fld['summary']) {
					$cfield['summary'] = true;
				}
			}
			if ($fld['formula']) {
				$cfield['formula'] = $fld['formula'];
			}
			
			$cfgFields[] = $cfield;
		}
		
		// stdfilters
		$stdfilters = Zend_Json::decode($request['stdfilters']);
		if ($stdfilters) {
			foreach ($stdfilters as &$cond) {
				$newcond = array(
					'fieldid' => $cond['fieldid'],
					'type' => 'datefilter',
					'value' => $cond['value'],
				);
				if ($cond['value'] == 'custom') {
					// crmv@106298
					// convert date from db format
					if ($cond['startdate']) $cond['startdate'] = getValidDBInsertDateTimeValue($cond['startdate']);
					if ($cond['enddate']) $cond['enddate'] = getValidDBInsertDateTimeValue($cond['enddate']);
					// crmv@106298e
					$newcond['startdate'] = $cond['startdate'];
					$newcond['enddate'] = $cond['enddate'];
				}
				if ($cond['chain'] && count($cond['chain']) > 1) {
					$fieldrels = $this->getRelationsFromChain($cond['chain']);
					$lastrel = end($fieldrels);
					$relations = array_merge($relations, $fieldrels);
					$newcond['relation'] = $lastrel['name'];
				}
				$cond = $newcond;
			}
			unset($cond);
			$config['stdfilters'] = $stdfilters;
		}
		
		// advfilters
		$advfilters = Zend_Json::decode($request['advfilters']);
		if ($advfilters) {
			foreach ($advfilters as &$group) {
				if ($group['conditions']) {
					$newconditions = array();
					foreach ($group['conditions'] as $cond) {
						$finfo = $this->getFieldInfoById($cond['fieldid']);
						
						if ($finfo['wstype'] == 'date' || $finfo['wstype'] == 'datetime') {
							// convert the date from the user format to the db format
							// timezone is already converted in the function
							$cond['value'] = getValidDBInsertDateTimeValue($cond['value']);
						} elseif ($finfo['wstype'] == 'time') {
							$cond['value'] = adjustTimezone($cond['value'], 0, null, true);
						}
						
						$newcond = array(
							'fieldid' => $cond['fieldid'],
							'value' => $cond['value'],
							'comparator' => $cond['comparator'],
							'glue' => $cond['glue'],
						);
						if ($cond['comparator'] == 'bw' && $cond['value2']) {
							if ($finfo['wstype'] == 'date' || $finfo['wstype'] == 'datetime') {
								// convert the date from the user format to the db format
								// timezone is already converted in the function
								$cond['value2'] = getValidDBInsertDateTimeValue($cond['value2']);
							} elseif ($finfo['wstype'] == 'time') {
								// adjust timezone
								$cond['value2'] = adjustTimezone($cond['value2'], 0, null, true);
							}
							$newcond['value2'] = $cond['value2'];
						}
						if ($cond['chain'] && count($cond['chain']) > 1) {
							$fieldrels = $this->getRelationsFromChain($cond['chain']);
							$lastrel = end($fieldrels);
							$relations = array_merge($relations, $fieldrels);
							$newcond['relation'] = $lastrel['name'];
						}
						
						// reference
						if ($cond['reference'] && $cond['reffieldid'] && $cond['refchain']) {
							$newcond['reference'] = true;
							$newcond['ref_fieldid'] = $cond['reffieldid'];
							unset($newcond['value']);
							if (count($cond['refchain']) > 1) {
								$fieldrels = $this->getRelationsFromChain($cond['refchain']);
								$lastrel = end($fieldrels);
								$relations = array_merge($relations, $fieldrels);
								$newcond['ref_relation'] = $lastrel['name'];
							}
						}
						
						$newconditions[] = $newcond;
					}
					$group['conditions'] = $newconditions;
				}
			}
			unset($group);
			$config['advfilters'] = $advfilters;
		}
		
		// TODO order relations by nesting level
		
		$config['relations'] = array_values($relations);
		$config['fields'] = $cfgFields;
		
		// totals
		$totals = Zend_Json::decode($request['totals']);
		if ($totals) {
			// TODO: and the chain ??
			$config['totals'] = $totals;
		}
		
		// summary
		$summary = Zend_Json::decode($request['summary']);
		if ($summary) {
			$config['summary'] = $summary;
		}
		
		$config['sharingtype'] = $request['sharingtype'];
		if ($config['sharingtype'] == 'Shared') {
			// get the shared infos
			$sharing = array();
			$shareinfo = Zend_Json::decode($request['sharinginfo']);
			if (empty($shareinfo) || !is_array($shareinfo)) throw new Exception("No sharing members provided");
			foreach ($shareinfo as $member) {
				list($type, $id) = explode('::', $member);
				if ($type != 'users' && $type != 'groups') throw new Exception("Invalid member type: $type");
				$id = intval($id);
				if ($id > 0) {
					// TODO: check if allowed!
					$sharing[] = array(
						'shareid' => $id,
						'setype' => strtolower($type),
					);
				}
			}
			$config['sharing'] = $sharing;
		}
		
		return $config;
	}
	
	public function createFolder($name, $description = '') {
		global $current_user;
		
		// check if it exists
		$folderinfo = getEntityFoldersByName($name, 'Reports');

		if (!empty($folderinfo)) {
			throw new Exception(getTranslatedString('FOLDER_NAME_ALREADY_EXISTS'));
		}

		$folderid = addEntityFolder('Reports', $name, $description, $current_user->id, 'CUSTOMIZED');
		return $folderid;
	}
	
	protected function getChainFromRelations($entry, &$relations) {
		$chain = array();
		
		//search the main module
		foreach ($relations as $rel) {
			if (!$rel['parent']) {
				$mainModule = $rel['module'];
				break;
			}
		}
		
		if (!$entry) {
			$chain[] = $mainModule;
		} else {
			$rchain = array();
			do {
				$foundRel = false;
				foreach ($relations as $rel) {
					if ($rel['name'] == $entry) {
						$foundRel = $rel;
						$rchain[] = $rel['name'];
						break;
					}
				}
				$entry = ($foundRel ? $foundRel['parent'] : false);
			} while ($entry);
			$chain = array_reverse($rchain);
		}
		return $chain;
	}
	
	public function getRelationsFromChain($chain) {
		$list = array();
		$mainModule = reset($chain);

		if (count($chain) == 1) {
			return $list;
		} elseif (count($chain) > $this->max_relation_levels) {
			throw new Exception("Maximum level of relation nesting reached");
		}
		
		array_shift($chain);
		$previous = array(
			'name' => $mainModule,
			'module' => $mainModule,
		);
		foreach ($chain as $relname) {
			$rel = $this->parseRelationName($relname);
			$orel = $this->generateRelation($rel, $previous);
			$list[$orel['name']] = $orel;
			$previous = $orel;
		}
		
		return $list;
	}
	
	/**
	 *
	 */
	public function getModulesListForChain($reportid, $chain) {
		$list = null;
		
		if (count($chain) >= $this->max_relation_levels) {
			return $list;
		}

		$RR = RelationManager::getInstance();
		$RR->enablePBRelations();
		
		$availmods = $this->getAvailableModules();
		$availmods = array_keys($availmods);
		
		$last = $chain[count($chain)-1];
		$duplist = array();
		
		if (count($chain) == 1) {
			$prevmodule = $last;
		} else {
			$prevrel = $this->parseRelationName($last);
			if ($prevrel) {
				$prevmodule = $prevrel->getSecondModule();
				foreach ($chain as $c) {
					$pieces = explode('_', $c);
					$duplist[] = implode('_', array_slice($pieces, -2));
					// for N-N the relation ids are different, so I need to use the modules
					if (preg_match('/_rel_/', $c)) {
						$duplist[] = implode('_', array_reverse(array_slice($pieces, 0,2)));
					}
				}
			}
		}

		$rels = $RR->getRelations($prevmodule, null, $availmods);
		
		$list['none'] = array(
			'label' => '-- '.getTranslatedString('LBL_USE_THE_FIELDS_OF', 'Reports').' '.$this->getModuleLabel($prevmodule).' --',
			'value' => '',
		);
		if ($rels) {
			foreach ($rels as $rel) {

				$relmod = $rel->getSecondModule();
				
				// exclude some combinations of modules
				if (!$this->isRelationUsable($rel, $prevmodule)) {
					continue;
				}
				
				// generate label and name for the relation
				$relname = $this->generateRelationName($rel);
				$label = $this->getModuleLabel($relmod);
				
				if ($relmod == 'ProductsBlock') {
					$label = getTranslatedString('LBL_RELATED_PRODUCTS', 'Settings');
				} elseif ($prevmodule == 'ProductsBlock') {
					// use the module label
				} elseif ($rel->getType() != ModuleRelation::$TYPE_NTON) {
					$finfo = $this->getFieldInfoById($rel->getFieldId());
					$flabel = $finfo['label'] ?: getTranslatedString($finfo['fieldlabel'], $finfo['module']);
					$label .= " (".strtolower(getTranslatedString('Field', 'APP_STRINGS')).' '.$flabel.')';
				}
				
				// check for already used relations
				$pieces = explode('_', $relname);
				if (preg_match('/_rel_/', $relname)) {
					$dupkey = implode('_', array_slice($pieces, 0, 2));
				} else {
					$dupkey = implode('_', array_slice($pieces, -2));
				}

				if (!array_key_exists($relname, $list) && !in_array($dupkey, $duplist)) {
					$list[$relname] = array(
						'label' => $label,
						'value' => $relname,
					);
				}
			}
		}
		
		$list = array_values($list);
		
		// order by label
		usort($list, function($a, $b) {
			return strcasecmp($a['label'], $b['label']);
		});

		return $list;
	}
	
	/**
	 * Check if a particular relation is usable in the report
	 */
	protected function isRelationUsable(&$rel, $prevmodule = null) {
		
		$relmod = $rel->getSecondModule();
		if ($rel->getType() == ModuleRelation::$TYPE_NTON) {
			if (($prevmodule == 'Accounts' && $relmod == 'Potentials') || ($prevmodule == 'Potentials' && $relmod == 'Accounts')) {
				return false;
			} elseif (($prevmodule == 'Contacts' && $relmod == 'Potentials') || ($prevmodule == 'Potentials' && $relmod == 'Contacts')) {
				return false;
			} elseif ($prevmodule == 'Products' && $relmod == 'Products') {
				// bundle products not supported
				return false;
			} elseif (($prevmodule == 'Calendar' && $relmod == 'Contacts') || ($prevmodule == 'Contacts' && $relmod == 'Calendar')) {
				// In tasks, it's a normal uitype 10
				return false;
			}
		} else {
			// calendar-contacts relation, with field (but it's a N-N)
			if (($prevmodule == 'Events' && $relmod == 'Contacts') || ($prevmodule == 'Contacts' && $relmod == 'Events')) {
				return false;
			}
		}
		
		return true;
	}
	
	public function isCalendarModule($module) {
		return in_array($module, array('Calendar', 'Events'));
	}
	
	public function getFieldsListForChain($reportid, $chain) {
		$list = array();
		
		$last = $chain[count($chain)-1];

		if (count($chain) == 1) {
			$prevmodule = $last;
		} else {
			$prevrel = $this->parseRelationName($last);
			if ($prevrel) {
				$prevmodule = $prevrel->getSecondModule();
			}
		}
		
		if ($prevmodule) {
			$list = $this->getFieldsForModule($prevmodule, $chain);
		}

		return $list;
	}
	
	public function getStdFiltersFieldsListForChain($reportid, $chain) {
		
		$useUitypes = array(5,6,23,70);
		
		$list = $this->getFieldsListForChain($reportid, $chain);
		
		// remove unwanted fields
		foreach ($list as &$block) {
			$newfields = array();
			foreach ($block['fields'] as $field) {
				if (in_array($field['wstype'], array('date', 'datetime')) && in_array($field['uitype'], $useUitypes)) {
					// by default, the modified time is selected
					if ($field['fieldname'] == 'modifiedtime') $field['selected'] = true;
					$newfields[] = $field;
				}
			}
			$block['fields'] = $newfields;
		}
		
		return $list;
	}
	
	public function getAdvFiltersFieldsListForChain($reportid, $chain) {
		return $this->getFieldsListForChain($reportid, $chain);
	}
	
	public function getTotalsFieldsListForChain($reportid, $chain) {

		$skipUitypes = array(50,70,10,26,51,52,53,77, 57,58,59, 66,68, 73,75,76,78,80,117);
		
		$list = $this->getFieldsListForChain($reportid, $chain);
		// TODO: this field: worktime
		
		// remove unwanted fields
		foreach ($list as &$block) {
			$newfields = array();
			foreach ($block['fields'] as $field) {
				if (in_array($field['wstype'], array('integer', 'double', 'currency')) && !in_array($field['uitype'], $skipUitypes)) {
					$newfields[] = $field;
				} elseif ($field['module'] == 'Timecards' && $field['fieldname'] == 'worktime') {
					$newfields[] = $field;
				}
			}
			$block['fields'] = $newfields;
		}
		
		return $list;
	}
	
	public function getFieldsForModule($module, $chain = null) {
		global $adb, $table_prefix, $current_user;

		$list = array();
		$flist = array();
		
		if ($module == 'ProductsBlock') {
			$ifields = $this->getPBFields();
			foreach ($ifields as $ifield) {
				$blockid = intval($ifield['block']);
				if (!$blockid) continue;
				if (!$this->isFieldAvailable($module, $ifield)) continue;
				$fieldval = array(
					'fieldid' => intval($ifield['fieldid']),
					'chain' => $chain
				);
				$flist[$blockid][] = array(
					'value' => Zend_Json::encode($fieldval),
					'label' => $ifield['label'],
					'single_label' => getTranslatedString('LBL_RELATED_PRODUCTS', 'Settings'),
					'uitype' => $ifield['uitype'],
					'wstype' => $ifield['wstype'],
					'module' => $ifield['module'],
					'trans_module' => $ifield['trans_module'],
					'fieldname' => $ifield['fieldname'],
					'sequence' => intval($ifield['sequence']),
				);
			}
		} else {
			$info = vtws_describe($module, $current_user);
			if ($info && $info['fields']) {
				foreach ($info['fields'] as $wsfield) {
					$blockid = intval($wsfield['blockid']);
					if (!$blockid) continue;
					if (!$this->isFieldAvailable($module, $wsfield)) continue;
					$fieldval = array(
						'fieldid' => intval($wsfield['fieldid']),
						'chain' => $chain
					);

					// alter some labels
					if ($module == 'Events' && $wsfield['name'] == 'eventstatus') {
						$wsfield['label'] = getTranslatedString('LBL_ACTIVITY_STATUS', $module);
					} elseif ($module == 'Calendar' && $wsfield['name'] == 'taskstatus') {
						$wsfield['label'] = getTranslatedString('LBL_TASK_STATUS', $module);
					}

					$flist[$blockid][] = array(
						'value' => Zend_Json::encode($fieldval),
						'label' => $wsfield['label'],
						'single_label' => getTranslatedString('SINGLE_'.$module, $module),
						'uitype' => $wsfield['uitype'],
						'wstype' => $wsfield['type']['name'],
						'module' => $module,
						'trans_module' => $this->getModuleLabel($module),
						'fieldname' => $wsfield['name'],
						'sequence' => intval($wsfield['sequence']),
					);
				}
				// add tax fields for the whole record
				if (isInventoryModule($module)) {
					$taxFields = $this->getTaxFields($module);
					foreach ($taxFields as &$tax) {
						$blockid = $tax['block'];
						$fieldval = array(
							'fieldid' => intval($tax['fieldid']),
							'chain' => $chain
						);
						$tax['value'] = Zend_Json::encode($fieldval);
					}
					unset($tax);
					$flist[$blockid] = array_values($taxFields);
				}
			}
		}

		// TODO: special fields / permissions
		
		// now get the blocks informations
		foreach ($flist as $blockid => $fields) {
			$blockinfo = $this->getBlockInfoById($blockid);
			// order fields
			usort($fields, function($a, $b) {
				return $a['sequence'] - $b['sequence'];
			});
			$list[] = array(
				'blockid' => $blockid,
				'sequence' => intval($blockinfo['sequence']),
				'label' => $blockinfo['label'],
				'fields' => $fields
			);
		}
		
		usort($list, function($a, $b) {
			return $a['sequence'] - $b['sequence'];
		});
		
		return $list;
	}
	
	/**
	 * Return true if the field is enabled to be used in the report (either columns, filters, totals...)
	 */
	protected function isFieldAvailable($module, $finfo) {
		
		// list of fields not available in reports
		$skipFields = array(
			'ProductsBlock' => array('id'),
			'Calendar' => array('eventstatus', 'ical_uuid', 'recurr_idx', 'reminder_time', 'exp_duration'),
			'Events' => array('taskstatus', 'ical_uuid', 'recurr_idx', 'reminder_time', 'exp_duration', 'contact_id'),
		);
		
		$fieldname = $finfo['name'] ?: $finfo['fieldname'];
		if (is_array($skipFields[$module]) && in_array($fieldname, $skipFields[$module])) {
			return false;
		}
		
		return true;
	}
	
	protected function generateRelation(&$relation, &$parent = null) {
		$rel = array();
		
		$rel['module'] = $relation->getSecondModule();
		$rel['name'] = $this->generateRelationName($relation);
		$rel['type'] = $relation->getType();
		if ($rel['type'] == ModuleRelation::$TYPE_NTON) {
			$rel['relationid'] = $relation->relationid;
		} else {
			$rel['fieldid'] = $relation->getFieldId();
		}
		
		if ($parent) {
			$rel['parent'] = $parent['name'];
		}
		return $rel;
	}
	
	protected function generateRelationName(&$relation) {
		global $table_prefix;
		
		$parentmod = $relation->getFirstModule();
		$relmod = $relation->getSecondModule();
		if ($relation->getType() == ModuleRelation::$TYPE_NTON) {
			$relname = $parentmod.'_'.$relmod.'_rel_'.$relation->relationid;
		} else {
			$relname = $parentmod.'_'.$relmod.'_fld_'.$relation->getFieldId();
		}
		return $relname;
	}
	
	protected function parseRelationName($relname) {
		
		if (preg_match('/_fld_([0-9]+)$/', $relname, $matches)) {
			$fieldid = $matches[1];
			$rname = preg_replace('/_fld_.*/', '', $relname);
			list($module1, $module2) = explode('_', $rname);
			$rels = ModuleRelation::createFromFieldId($fieldid);
			// find the correct one
			foreach ($rels as $frel) {
				if (
					($frel->getFirstModule() == $module1 && $frel->getSecondModule() == $module2) ||
					($frel->getFirstModule() == $module2 && $frel->getSecondModule() == $module1)
				) {
					$rel = $frel;
					break;
				}
			}
		} elseif (preg_match('/_rel_([0-9]+)$/', $relname, $matches)) {
			$relationid = $matches[1];
			$rname = preg_replace('/_rel_.*/', '', $relname);
			list($module1, $module2) = explode('_', $rname);
			$rel = ModuleRelation::createFromRelationId($relationid);
		} else {
			throw new Exception("Unable to find a relation for $relname");
		}
		
		// invert the relation if needed
		if ($rel && $module1 == $rel->getSecondModule() && $module2 == $rel->getFirstModule()) {
			$rel->invert();
		}
		
		return $rel;
	}
	
	function getVisibleCriteria($recordid='') {
		global $adb, $table_prefix;

		$filter = array();
		if ($recordid!='') {
			$config = $this->loadReport($recordid);
			$selcriteria = $config['sharingtype'] ?: 'Public';
		} else {
			$selcriteria = 'Public';
		}
		$res = $adb->query("SELECT name FROM ".$table_prefix."_reportvisibility");
		while ($row = $adb->FetchByAssoc($res, -1, false)) {
			$filtername = trim($row['name']);
			if ($filtername == 'Private') {
				$FilterValue=getTranslatedString('PRIVATE_FILTER');
			} elseif($filtername=='Shared') {
				$FilterValue=getTranslatedString('SHARE_FILTER');
			} else {
				$FilterValue=getTranslatedString('PUBLIC_FILTER');
			}
			$shtml['value'] = $filtername;
			$shtml['text'] = $FilterValue;
			$shtml['selected'] = ($filtername == $selcriteria ? "selected" : "");
			$filter[] = $shtml;
		}		
		return $filter;
	}

	function getShareInfo($recordid=''){
		global $adb,$table_prefix;
		$member_query = $adb->pquery("SELECT ".$table_prefix."_reportsharing.setype,".$table_prefix."_users.id,".$table_prefix."_users.user_name FROM ".$table_prefix."_reportsharing INNER JOIN ".$table_prefix."_users on ".$table_prefix."_users.id = ".$table_prefix."_reportsharing.shareid WHERE ".$table_prefix."_reportsharing.setype='users' AND ".$table_prefix."_reportsharing.reportid = ?",array($recordid));
		$noofrows = $adb->num_rows($member_query);
		if($noofrows > 0){
			for($i=0;$i<$noofrows;$i++){
				$userid = $adb->query_result($member_query,$i,'id');
				$username = $adb->query_result($member_query,$i,'user_name');
				$setype = $adb->query_result($member_query,$i,'setype');
				$member_data[] = Array('id'=>$setype."::".$userid,'name'=>$setype."::".$username);
			}
		}
		
		$member_query = $adb->pquery("SELECT ".$table_prefix."_reportsharing.setype,".$table_prefix."_groups.groupid,".$table_prefix."_groups.groupname FROM ".$table_prefix."_reportsharing INNER JOIN ".$table_prefix."_groups on ".$table_prefix."_groups.groupid = ".$table_prefix."_reportsharing.shareid WHERE ".$table_prefix."_reportsharing.setype='groups' AND ".$table_prefix."_reportsharing.reportid = ?",array($recordid));
		$noofrows = $adb->num_rows($member_query);
		if($noofrows > 0){
			for($i=0;$i<$noofrows;$i++){
				$grpid = $adb->query_result($member_query,$i,'groupid');
				$grpname = $adb->query_result($member_query,$i,'groupname');
				$setype = $adb->query_result($member_query,$i,'setype');
				$member_data[] = Array('id'=>$setype."::".$grpid,'name'=>$setype."::".$grpname);
			}
		}
		return $member_data;
	}
	
	/**
	 * Return the tabid event if the module is not active
	 */
	public function getTabId($module) {
		global $adb, $table_prefix;
		$res = $adb->pquery("SELECT tabid FROM {$table_prefix}_tab WHERE name = ?", array($module));
		$tabid = intval($adb->query_result_no_html($res, 0, 'tabid'));
		return $tabid;
	}
	
	public function getFieldInfoById($fieldid) {
		global $adb, $table_prefix;
		
		if (!is_array($this->fields_cache_id)) $this->fields_cache_id = array();
		
		if (!$this->fields_cache_id[$fieldid]) {
			if ($this->isPBField(array('fieldid' => $fieldid))) {
				$finfo = $this->getPBFieldInfoById($fieldid);
			} elseif ($this->isInventoryTaxField(array('fieldid' => $fieldid))) {
				$finfo = $this->getTaxFieldInfoById($fieldid);
			} else {
				$res = $adb->pquery("SELECT * FROM {$table_prefix}_field WHERE fieldid = ?", array($fieldid));
				if ($res && $adb->num_rows($res) > 0) {
					$finfo = $adb->FetchByAssoc($res, -1, false);
					
					$wsfield = WebserviceField::fromArray($adb,$finfo);
					
					// fixes for the stupid calendar
					if ($finfo['fieldname'] == 'eventstatus') {
						$finfo['fieldlabel'] = 'LBL_ACTIVITY_STATUS';
					} elseif ($finfo['fieldname'] == 'taskstatus') {
						$finfo['fieldlabel'] = 'LBL_TASK_STATUS';
					} elseif ($finfo['fieldname'] == 'date_start') {
						$finfo['fieldlabel'] = 'Start Date';
					}
			
					$finfo['module'] = getTabname($finfo['tabid']);
					$finfo['wstype'] = $wsfield->getFieldDataType();
					$finfo['typeofdata'] = $wsfield->getTypeOfData();
					$finfo['is_reference'] = ($finfo['wstype'] == 'reference');
					$finfo['is_entityname'] = $wsfield->isEntityNameField();
					if ($finfo['uitype'] == 10) {
						// add related modules
						$res2 = $adb->pquery("SELECT relmodule FROM {$table_prefix}_fieldmodulerel WHERE fieldid = ?",array($fieldid));
						if ($res2 && $adb->num_rows($res2) > 0) {
							$relmods = array();
							while ($row2 = $adb->FetchByAssoc($res2, -1, false)) {
								$relmods[] = $row2['relmodule'];
							}
							$finfo['relmodules'] = array_unique($relmods);
						}
					}
					if (in_array($finfo['wstype'], array('picklist', 'multipicklist', 'picklistmultilanguage'))) {
						$aval = array();
						$allowedValues = $wsfield->getPicklistDetails();
						foreach ($allowedValues as $av) {
							$aval[$av['value']] = $av;
						}
						$finfo['allowed_values'] = $aval;
					}
					
				}
			}
			$this->fields_cache_id[$fieldid] = $finfo;
		}
		
		return $this->fields_cache_id[$fieldid];
	}
	
	public function getFieldInfoByName($module, $fieldname) {
		global $adb, $table_prefix;
		
		if (!is_array($this->fields_cache_name)) $this->fields_cache_name = array();
		
		if (!$this->fields_cache_name[$module][$fieldname]) {
			if ($module == 'ProductsBlock') {
				$finfo = $this->getPBFieldInfo($fieldname);
			} elseif (isInventoryModule($module) && substr($fieldname, 0, 3) == 'tax') {
				$finfo = $this->getTaxFieldInfo($module, $fieldname);
			} else {
				$tabid = $this->getTabid($module);
				$res = $adb->pquery("SELECT fieldid FROM {$table_prefix}_field WHERE tabid = ? AND fieldname = ?", array($tabid, $fieldname));
				if ($res && $adb->num_rows($res) > 0) {
					$fieldid = $adb->query_result_no_html($res, 0, 'fieldid');
					$finfo = $this->getFieldInfoById($fieldid);
				}
			}
			if ($finfo) {
				$this->fields_cache_name[$module][$finfo['fieldname']] = $finfo;
			}
		}
		
		return $this->fields_cache_name[$module][$fieldname];
		
	}
	
	public function getBlockInfoById($blockid) {
		global $adb, $table_prefix, $current_language;
		
		if (!is_array($this->blocks_cache_id)) $this->blocks_cache_id = array();
		
		if (!$this->blocks_cache_id[$blockid]) {
			if ($this->isPBBlock(array('blockid' => $blockid))) {
				$binfo = $this->getPBBlockInfoById($blockid);
			} elseif ($this->isInventoryTaxBlock(array('blockid' => $blockid))) {
				$binfo = $this->getInventoryTaxBlockInfoById($blockid);
			} else {
				$res = $adb->pquery("SELECT * FROM {$table_prefix}_blocks WHERE blockid = ?", array($blockid));
				if ($res && $adb->num_rows($res) > 0) {
					$binfo = $adb->FetchByAssoc($res, -1, false);
					if ($binfo) {
						$module = getTabname($binfo['tabid']);
						if ($this->isCalendarModule($module) && empty($binfo['blocklabel'])) {
							// why do I always have to deal with that stupid calendar??
							//if (in_array($block['blockid'], array())
							$binfo['blocklabel'] = 'LBL_DESCRIPTION_INFORMATION';
						}
						$binfo['label'] = getTranslatedString($binfo['blocklabel'], $module);
						if ($binfo['label'] == $binfo['blocklabel']) {
							$binfo['label'] = getTranslatedString($binfo['blocklabel'], 'APP_STRINGS');
						}
					}
				}
			}
			$this->blocks_cache_id[$blockid] = $binfo;
		}
		
		return $this->blocks_cache_id[$blockid];
	}
	
	
	/* functions for PB blocks */
	
	public function isPBBlock($block) {
		return ($block['blockid'] >= $this->baseBlockIdPB && $block['blockid'] < $this->baseBlockIdPB + 100);
	}
	
	public function getPBBlocks() {
		$baseBlockid = $this->baseBlockIdPB;
		
		++$baseBlockid;
		$infos = array(
			array(
				'blockid' => $baseBlockid,
				'blocklabel' => 'LBL_RELATED_PRODUCTS',
				'label' => getTranslatedString('LBL_RELATED_PRODUCTS', 'Settings'),
				'sequence' => 100,	// if merged into other modules, make sure it's the last
			),
		);
		
		foreach ($infos as &$info) {
			$info['module'] = 'ProductsBlock';
		}
		
		return $infos;
	}
	
	public function getPBBlockInfoById($blockid) {
		$infos = $this->getPBBlocks();
		foreach ($infos as $info) {
			if ($info['blockid'] == $blockid) {
				return $info;
			}
		}
		return false;
	}
	
	
	/* functions for tax block */
	
	public function isInventoryTaxBlock($block) {
		$maxblockid = $this->baseTaxBlockId + $this->maxTabid * $this->maxTabid;
		return ($block['blockid'] >= $this->baseTaxBlockId && $block['blockid'] < $maxblockid);
	}
	
	public function getInventoryTaxBlocks($module) {
		$tabid = $this->getTabid($module);
		$baseBlockid = $this->baseTaxBlockId + $this->maxTabid * $tabid;
		
		++$baseBlockid;
		$infos = array(
			array(
				'module' => $module,
				'blockid' => $baseBlockid,
				'blocklabel' => 'LBL_TAX',
				'label' => getTranslatedString('LBL_TAX', 'APP_STRINGS'),
				'sequence' => 100,
			),
		);
		
		return $infos;
	}
	
	public function getInventoryTaxBlockInfoById($blockid) {
		$tabid = (int)(($blockid - $this->baseTaxBlockId)/$this->maxTabid);
		$module = getTabname($tabid);
		$infos = $this->getInventoryTaxBlocks($module);
		foreach ($infos as $info) {
			if ($info['blockid'] == $blockid) {
				return $info;
			}
		}
		return false;
	}
	
	
	/* functions for PB fields */
	
	public function isPBField($fieldinfo) {
		$maxpbid = $this->baseFieldIdPB + 500;
		return ($fieldinfo['fieldid'] >= $this->baseFieldIdPB && $fieldinfo['fieldid'] < $maxpbid);
	}
	
	public function getPBFields() {
		global $adb, $table_prefix;
		
		$tabid = $this->tabidPB;
		$baseFieldid = $this->baseFieldIdPB;
		
		$blockinfo = $this->getPBBlocks();
		$blockinfo = $blockinfo[0];
		
		$infos = array(
			'id' => array(
				'fieldid' => ++$baseFieldid,
				'columnname' => 'id',
				'fieldlabel' => 'Id',
				'typeofdata' => 'I',
				'uitype' => 10,
				'wstype' => 'reference',
				'relmodules' => getInventoryModules(),
			),
			'productid' => array(
				'fieldid' => ++$baseFieldid,
				'columnname' => 'productid',
				'fieldlabel' => 'Product Name',
				'typeofdata' => 'I',
				'uitype' => 10,
				'wstype' => 'reference',
				'relmodules' => getProductModules(),
			),
			'quantity' => array(
				'fieldid' => ++$baseFieldid,
				'columnname' => 'quantity',
				'fieldlabel' => 'Quantity',
				'typeofdata' => 'N',
				'uitype' => 7,
				'wstype' => 'double',
			),
			'listprice' => array(
				'fieldid' => ++$baseFieldid,
				'columnname' => 'listprice',
				'fieldlabel' => 'List Price',
				'typeofdata' => 'N',
				'uitype' => 71,
				'wstype' => 'double',
			),
			'discount' => array(
				'fieldid' => ++$baseFieldid,
				'columnname' => 'discount',
				'fieldlabel' => 'Discount',
				'typeofdata' => 'N',
				'uitype' => 7,
				'wstype' => 'double',
			),
			'total_notaxes' => array(
				'fieldid' => ++$baseFieldid,
				'columnname' => 'total_notaxes',
				'fieldlabel' => 'LBL_TOTAL_AFTER_DISCOUNT',
				'typeofdata' => 'N',
				'uitype' => 71,
				'wstype' => 'double',
			),
			'comment' => array(
				'fieldid' => ++$baseFieldid,
				'columnname' => 'comment',
				'fieldlabel' => 'Comments',
				'typeofdata' => 'V',
				'uitype' => 1,
				'wstype' => 'string',
			),
			'description' => array(
				'fieldid' => ++$baseFieldid,
				'columnname' => 'description',
				'fieldlabel' => 'Description',
				'typeofdata' => 'V',
				'uitype' => 19,
				'wstype' => 'text',
			),
			'linetotal' => array(
				'fieldid' => ++$baseFieldid,
				'columnname' => 'linetotal',
				'fieldlabel' => 'Net Price',
				'typeofdata' => 'N',
				'uitype' => 71,
				'wstype' => 'double',
			),
		);
		
		$taxFields = $this->getTaxFields('ProductsBlock');
		$infos = array_merge($infos, $taxFields);
		
		$seq = 0;
		foreach ($infos as &$info) {
			$info['tabid'] = $tabid;
			$info['module'] = 'ProductsBlock';
			$info['fieldname'] = $info['columnname'];
			$info['label'] = getTranslatedString($info['fieldlabel'], 'Quotes');
			$info['tablename'] = $table_prefix.'_inventoryproductrel';
			$info['block'] = $blockinfo['blockid'];
			$info['sequence'] = ++$seq;
		}
		
		return $infos;
	}
	
	public function getPBFieldInfo($fieldname) {
		$infos = $this->getPBFields();
		$info = $infos[$fieldname];
		return $info;
	}
	
	public function getPBFieldInfoById($fieldid) {
		$infos = $this->getPBFields();
		foreach ($infos as $info) {
			if ($info['fieldid'] == $fieldid) {
				return $info;
			}
		}
		return false;
	}
	
	
	/* functions for tax fields (for PB or inventory modules */
	
	public function isInventoryTaxField($fieldinfo) {
		$maxtaxId = $this->baseTaxFieldId + $this->maxTabid * $this->maxTabid;
		return ($fieldinfo['fieldid'] >= $this->baseTaxFieldId && $fieldinfo['fieldid'] < $maxtaxId);
	}
	
	public function getTaxFields($module) {
		global $table_prefix;
		
		$IUtils = InventoryUtils::getInstance();
		
		$infos = array();
		
		if ($module == 'ProductsBlock') {
			$tabid = $this->tabidPB;
			$table = $table_prefix.'_inventoryproductrel';
			$prodLabel = ' '.getTranslatedString('LBL_PRODUCT');
			$baseTaxFieldid = $this->baseFieldIdPB + 100;
			$blockid = 0;	// overwritten later
		} else {
			$tabid = $this->getTabid($module);
			$table = $table_prefix.'_inventorytotals';
			$prodLabel = '';
			$baseTaxFieldid = $this->baseTaxFieldId + $this->maxTabid * $tabid;
			
			$blockinfo = $this->getInventoryTaxBlocks($module);
			$blockinfo = $blockinfo[0];
			$blockid = $blockinfo['blockid'];
		}
		
		// taxes for the single product line
		$allTaxes = $IUtils->getAllTaxes('all');
		// add total:
		$allTaxes[] = array(
			'taxid' => 100,
			'taxname' => 'tax_total',
			'taxlabel' => getTranslatedString('LBL_TOTAL')
		);
		$seq = 100;
		foreach ($allTaxes as $tax) {
			$taxname = $tax['taxname'];
			$infos[$taxname] = array(
				'tabid' => $tabid,
				'block' => $blockid,
				'module' => $module,
				'trans_module' => $this->getModuleLabel($module),
				'fieldid' => $baseTaxFieldid + $tax['taxid'],
				'tablename' => $table,
				'columnname' => $taxname,
				'fieldname' => $taxname,
				'fieldlabel' => getTranslatedString('LBL_TAX').$prodLabel.' ('.$tax['taxlabel'].')',
				'label' => getTranslatedString('LBL_TAX').$prodLabel.' ('.$tax['taxlabel'].')',
				'typeofdata' => 'N',
				'uitype' => 71,
				'wstype' => 'double',
				'sequence' => $seq++,
			);
		}
		
		return $infos;
	}
	
	public function getTaxFieldInfo($module, $fieldname) {
		$infos = $this->getTaxFields($module);
		$info = $infos[$fieldname];
		return $info;
	}
	
	public function getTaxFieldInfoById($fieldid) {
		$tabid = (int)(($fieldid - $this->baseTaxFieldId)/$this->maxTabid);
		$module = getTabname($tabid);
		$infos = $this->getTaxFields($module);
		foreach ($infos as $info) {
			if ($info['fieldid'] == $fieldid) {
				return $info;
			}
		}
		return false;
	}
	
	
	/** Function to get the reports under a report folder
	 *  @ param $folderid : Type Integer
	 *  This Returns $reports_array in the following format
	 *    $reports_array = array ($reportid=>$reportname,$reportid=>$reportname1,...,$reportidn=>$reportname)
	 */
	static public function getReportsinFolder($folderid) {
		global $adb, $table_prefix;

		$query = 'select reportid,reportname from '.$table_prefix.'_report where folderid=?';
		$result = $adb->pquery($query, array($folderid));
		$reports_array = Array();
		for($i=0;$i < $adb->num_rows($result);$i++) {
			$reportid = $adb->query_result_no_html($result,$i,'reportid');
			$reportname = $adb->query_result($result,$i,'reportname');
			$reports_array[$reportid] = $reportname;
		}
		return (count($reports_array) > 0 ? $reports_array : false);
	}

}

// here for compatibility only
function getReportsinFolder($folderid) {
	return Reports::getReportsinFolder($folderid);
}

