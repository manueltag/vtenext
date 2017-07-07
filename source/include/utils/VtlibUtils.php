<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 *************************************************************************************/

/*
 * Check for image existence in themes orelse
 * use the common one.
 */
// Let us create cache to improve performance
if(!isset($__cache_vtiger_imagepath)) {
    $__cache_vtiger_imagepath = Array();
}
function vtecrm_imageurl($imagename, $themename) {
	return vtiger_imageurl($imagename, $themename);
}
function vtiger_imageurl($imagename, $themename) {
	global $__cache_vtiger_imagepath;
	if($__cache_vtiger_imagepath[$imagename]) {
        $imagepath = $__cache_vtiger_imagepath[$imagename];
    } else {
		$imagepath = false;
		// Check in theme specific folder
        if(file_exists("themes/$themename/images/$imagename")) {
            $imagepath =  "themes/$themename/images/$imagename";
		} else if(file_exists("themes/images/$imagename")) {
			// Search in common image folder
			$imagepath = "themes/images/$imagename";
		} else {
			// Not found anywhere? Return whatever is sent
			$imagepath = $imagename;
		}
        $__cache_vtiger_imagepath[$imagename] = $imagepath;
    }
	//crmv@manuele
    if(defined('VTIGER_MODULES_MOBILE'))
		return "../../".$imagepath;
    else
    //crmv@manuele-e
	return $imagepath;
}

/**
 * Get module name by id.
 */
function vtlib_getModuleNameById($tabid) {
	global $adb,$table_prefix;
	$sqlresult = $adb->pquery("SELECT name FROM ".$table_prefix."_tab WHERE tabid = ?",array($tabid));
	if($adb->num_rows($sqlresult)) return $adb->query_result($sqlresult, 0, 'name');
	return null;
}

/**
 * Get module names for which sharing access can be controlled.
 * NOTE: Ignore the standard modules which is already handled.
 */
function vtlib_getModuleNameForSharing() {
	global $adb;
	$std_modules = array('Calendar','Leads','Accounts','Contacts','Potentials',
			'HelpDesk','Campaigns','Quotes','PurchaseOrder','SalesOrder','Invoice','Events');
	$modulesList = getSharingModuleList($std_modules);
	return $modulesList;
}

/**
 * Cache the module active information for performance
 */
$__cache_module_activeinfo = Array();

/**
 * Fetch module active information at one shot, but return all the information fetched.
 */
function vtlib_prefetchModuleActiveInfo($force = true) {
	global $__cache_module_activeinfo,$table_prefix;

	// Look up if cache has information
	$tabrows = VTCacheUtils::lookupAllTabsInfo();

	// Initialize from DB if cache information is not available or force flag is set
	if($tabrows === false || $force) {
		global $adb;
		$tabres = $adb->query("SELECT * FROM ".$table_prefix."_tab");
		$tabrows = array();
		if($tabres) {
			while($tabresrow = $adb->fetch_array($tabres)) {
				$tabrows[] = $tabresrow;
				$__cache_module_activeinfo[$tabresrow['name']] = $tabresrow['presence'];
			}
			// Update cache for further re-use
			VTCacheUtils::updateAllTabsInfo($tabrows);
		}
	}

	return $tabrows;
}

/**
 * Check if module is set active (or enabled)
 */
function vtlib_isModuleActive($module) {
	global $adb, $__cache_module_activeinfo;

	if(in_array($module, vtlib_moduleAlwaysActive())){
		return true;
	}

	if(!isset($__cache_module_activeinfo[$module])) {
		include 'tabdata.php';
		$presence = isset($tab_info_array[$module])? 0: 1;
		$__cache_module_activeinfo[$module] = $presence;
	} else {
		$presence = $__cache_module_activeinfo[$module];
	}

	$active = false;
	if($presence != 1) $active = true;

	return $active;
}

/**
 * Recreate user privileges files.
 */
function vtlib_RecreateUserPrivilegeFiles() {
	global $adb,$table_prefix;
	$userres = $adb->query('SELECT id FROM '.$table_prefix.'_users WHERE deleted = 0');
	if($userres && $adb->num_rows($userres)) {
		while($userrow = $adb->fetch_array($userres)) {
			createUserPrivilegesfile($userrow['id']);
			createUserPrivilegesfile($userrow['id'], 1); // crmv@39110
		}
	}
}

/**
 * Get list module names which are always active (cannot be disabled)
 */
function vtlib_moduleAlwaysActive() {
	$modules = Array (
		'Administration', 'CustomView', 'Settings', 'Users', 'Migration',
		'Utilities', 'uploads', 'Import', 'System', 'com_vtiger_workflow', 'PickList','Picklistmulti'
	);
	//crmv@offline
	global $offline_mode;
	if ($offline_mode)
		$modules[] = 'Offline';
	//crmv@offline end
	return $modules;
}

/**
 * Toggle the module (enable/disable)
 */
function vtlib_toggleModuleAccess($module, $enable_disable) {
	global $adb, $metaLogs, $__cache_module_activeinfo,$table_prefix; // crmv@49398

	include_once('vtlib/Vtecrm/Module.php');

	$event_type = false;

	if($enable_disable === true) {
		$enable_disable = 0;
		$event_type = Vtiger_Module::EVENT_MODULE_ENABLED;
	} else if($enable_disable === false) {
		$enable_disable = 1;
		$event_type = Vtiger_Module::EVENT_MODULE_DISABLED;
	}

	$adb->pquery("UPDATE ".$table_prefix."_tab set presence = ? WHERE name = ?", array($enable_disable,$module));

	$__cache_module_activeinfo[$module] = $enable_disable;

	create_tab_data_file();
	create_parenttab_data_file();

	// UserPrivilege file needs to be regenerated if module state is changed from
	// vtiger 5.1.0 onwards
	global $vtiger_current_version;
	if(version_compare($vtiger_current_version, '5.0.4', '>')) {
		vtlib_RecreateUserPrivilegeFiles();
	}

	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_EDITMODULE, getTabid($module), array('module'=>$module)); // crmv@49398

	Vtiger_Module::fireEvent($module, $event_type);
}

/**
 * Get list of module with current status which can be controlled.
 */
function vtlib_getToggleModuleInfo() {
	global $adb,$table_prefix;

	$modinfo = Array();

	$sqlresult = $adb->query("SELECT name, presence, customized, isentitytype, hide_module_manager FROM ".$table_prefix."_tab LEFT JOIN vte_hide_tab ON ".$table_prefix."_tab.tabid = vte_hide_tab.tabid WHERE name NOT IN ('Users') AND presence IN (0,1) ORDER BY name");	//crmv@27711
	$num_rows  = $adb->num_rows($sqlresult);
	for($idx = 0; $idx < $num_rows; ++$idx) {
		//crmv@27711
		$hide_module_manager=$adb->query_result($sqlresult, $idx, 'hide_module_manager');
		if ($hide_module_manager == '1') {
			continue;
		}
		//crmv@27711e
		$module = $adb->query_result($sqlresult, $idx, 'name');
		$presence=$adb->query_result($sqlresult, $idx, 'presence');
		$customized=$adb->query_result($sqlresult, $idx, 'customized');
		$isentitytype=$adb->query_result($sqlresult, $idx, 'isentitytype');
		$hassettings=file_exists("modules/$module/Settings.php");

		$modinfo[$module] = Array( 'customized'=>$customized, 'presence'=>$presence, 'hassettings'=>$hassettings, 'isentitytype' => $isentitytype );
	}
	return $modinfo;
}

/**
 * Get list of language and its current status.
 */
function vtlib_getToggleLanguageInfo() {
	global $adb,$table_prefix;

	// The table might not exists!
	$old_dieOnError = $adb->dieOnError;
	$adb->dieOnError = false;

	$langinfo = Array();
	$sqlresult = $adb->query("SELECT * FROM ".$table_prefix."_language");
	if($sqlresult) {
		for($idx = 0; $idx < $adb->num_rows($sqlresult); ++$idx) {
			$row = $adb->fetch_array($sqlresult);
			$langinfo[$row['prefix']] = Array( 'label'=>$row['label'], 'active'=>$row['active'] );
		}
	}
	$adb->dieOnError = $old_dieOnError;
	return $langinfo;
}

/**
 * Toggle the language (enable/disable)
 */
function vtlib_toggleLanguageAccess($langprefix, $enable_disable) {
	global $adb,$table_prefix;

	// The table might not exists!
	$old_dieOnError = $adb->dieOnError;
	$adb->dieOnError = false;

	if($enable_disable === true) $enable_disable = 1;
	else if($enable_disable === false) $enable_disable = 0;

	$adb->pquery('UPDATE '.$table_prefix.'_language set active = ? WHERE prefix = ?', Array($enable_disable, $langprefix));

	$adb->dieOnError = $old_dieOnError;
}

/**
 * Get help information set for the module fields.
 */
function vtlib_getFieldHelpInfo($module) {
	global $adb,$table_prefix;
	$fieldhelpinfo = Array();
	if(in_array('helpinfo', $adb->getColumnNames($table_prefix.'_field'))) {
		$result = $adb->pquery('SELECT fieldname,helpinfo FROM '.$table_prefix.'_field WHERE tabid=?', Array(getTabid($module)));
		if($result && $adb->num_rows($result)) {
			while($fieldrow = $adb->fetch_array($result)) {
				$helpinfo = decode_html($fieldrow['helpinfo']);
				if(!empty($helpinfo)) {
					$fieldhelpinfo[$fieldrow['fieldname']] = getTranslatedString($helpinfo, $module);
				}
			}
		}
	}
	return $fieldhelpinfo;
}

/**
 * Setup mandatory (requried) module variable values in the module class.
 */
function vtlib_setup_modulevars($module, $focus) {

	$checkfor = Array('table_name', 'table_index', 'related_tables', 'popup_fields', 'IsCustomModule');
	foreach($checkfor as $check) {
		if(!isset($focus->$check)) $focus->$check = __vtlib_get_modulevar_value($module, $check);
	}
}
function __vtlib_get_modulevar_value($module, $varname) {
	global $table_prefix;
	$mod_var_mapping =
		Array(
			'Accounts' =>
			Array(
				'IsCustomModule'=>false,
				'table_name'  => $table_prefix.'_account',
				'table_index' => 'accountid',
				// related_tables variable should define the association (relation) between dependent tables
				// FORMAT: related_tablename => Array ( related_tablename_column[, base_tablename, base_tablename_column] )
				// Here base_tablename_column should establish relation with related_tablename_column
				// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
				'related_tables' => Array(
					$table_prefix.'_accountbillads' => Array ('accountaddressid', $table_prefix.'_account', 'accountid'),
					$table_prefix.'_accountshipads' => Array ('accountaddressid', $table_prefix.'_account', 'accountid'),
				),
				'popup_fields' => Array('accountname'), // TODO: Add this initialization to all the standard module
			),
			'Contacts' =>
			Array(
				'IsCustomModule'=>false,
				'table_name'  => $table_prefix.'_contactdetails',
				'table_index' => 'contactid',
				'related_tables'=> Array(
					$table_prefix.'_account' => Array ('accountid' ),
					//crmv@91078
					$table_prefix.'_contactaddress' => Array ( 'contactaddressid', $table_prefix.'_contactdetails', 'contactid' ), 
					$table_prefix.'_contactsubdetails' => Array ( 'contactsubscriptionid', $table_prefix.'_contactdetails', 'contactid' ), 
					$table_prefix.'_customerdetails' => Array ( 'customerid', $table_prefix.'_contactdetails', 'contactid' ), 
					//crmv@91078e
				),
				'popup_fields' => Array ('lastname'),
			),
			'Leads' =>
			Array(
				'IsCustomModule'=>false,
				'table_name'  => $table_prefix.'_leaddetails',
				'table_index' => 'leadid',
				'related_tables' => Array (
					$table_prefix.'_leadsubdetails' => Array ( 'leadsubscriptionid', $table_prefix.'_leaddetails', 'leadid' ),
					$table_prefix.'_leadaddress'    => Array ( 'leadaddressid', $table_prefix.'_leaddetails', 'leadid' ),
				),
				'popup_fields'=> Array ('lastname'),
			),
			'Campaigns' =>
			Array(
				'IsCustomModule'=>false,
				'table_name'  => $table_prefix.'_campaign',
				'table_index' => 'campaignid',
				'popup_fields' => Array ('campaignname'),
			),
			'Potentials' =>
			Array(
				'IsCustomModule'=>false,
				'table_name' => $table_prefix.'_potential',
				'table_index'=> 'potentialid',
				// NOTE: UIType 10 is being used instead of direct relationship from 5.1.0
				//'related_tables' => Array ('vtiger_account' => Array('accountid')),
				'popup_fields'=> Array('potentialname'),
			),
			'Quotes' =>
			Array(
				'IsCustomModule'=>false,
				'table_name' => $table_prefix.'_quotes',
				'table_index'=> 'quoteid',
				'related_tables' => Array ($table_prefix.'_account' => Array('accountid')),
				'popup_fields'=>Array('subject'),
			),
			'SalesOrder'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => $table_prefix.'_salesorder',
				'table_index'=> 'salesorderid',
				'related_tables'=> Array ($table_prefix.'_account' => Array('accountid')),
				'popup_fields'=>Array('subject'),
			),
			'PurchaseOrder'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => $table_prefix.'_purchaseorder',
				'table_index'=> 'purchaseorderid',
				'popup_fields'=>Array('subject'),
			),
			'Invoice'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => $table_prefix.'_invoice',
				'table_index'=> 'invoiceid',
				'popup_fields'=> Array('subject'),
			),
			'HelpDesk'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => $table_prefix.'_troubletickets',
				'table_index'=> 'ticketid',
				'popup_fields'=> Array('ticket_title')
			),
			'Faq'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => $table_prefix.'_faq',
				'table_index'=> 'id',
			),
			'Documents'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => $table_prefix.'_notes',
				'table_index'=> 'notesid',
			),
			'Products'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => $table_prefix.'_products',
				'table_index'=> 'productid',
				'popup_fields'=> Array('productname'),
			),
			'PriceBooks'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => $table_prefix.'_pricebook',
				'table_index'=> 'pricebookid',
				'popup_fields'=> Array('bookname'), //crmv@21387
			),
			'Vendors'=>
			Array(
				'IsCustomModule'=>false,
				'table_name' => $table_prefix.'_vendor',
				'table_index'=> 'vendorid',
				'popup_fields'=>Array('vendorname'),
			)
		);
	//crmv@33985
	if ($varname == 'popup_fields' && !isset($mod_var_mapping[$module][$varname])){
		global $table_prefix,$adb;
		static $module_identifier_cache =Array();
		if (isset($module_identifier_cache[$module])){
			$mod_var_mapping[$module][$varname] = Array($module_identifier_cache[$module]);
		}
		else{
			$sql = "select fieldname from ".$table_prefix."_entityname where modulename = ?";
			$params = Array($module);
			$res = $adb->pquery($sql,$params);
			if ($res){
				$module_identifier_cache[$module] = $adb->query_result_no_html($res,0,'fieldname');
				$mod_var_mapping[$module][$varname] = Array($module_identifier_cache[$module]);
			}
		}
	}
	//crmv@33985e
	return $mod_var_mapping[$module][$varname];
}

/**
 * Convert given text input to singular.
 */
function vtlib_tosingular($text) {
	//crmv@31457
	return "SINGLE_".$text;
	//crmv@31457e
	$lastpos = strripos($text, 's');
	if($lastpos == strlen($text)-1)
		return substr($text, 0, -1);
	return $text;
}

/**
 * Get picklist values that is accessible by all roles.
 */
function vtlib_getPicklistValues_AccessibleToAll($field_columnname) {
	global $adb,$table_prefix;

	$columnname =  $adb->sql_escape_string($field_columnname);
	$tablename = $table_prefix."_$columnname";

	// Gather all the roles (except H1 which is organization role)
	$roleres = $adb->query("SELECT roleid FROM ".$table_prefix."_role WHERE roleid != 'H1'");
	$roleresCount= $adb->num_rows($roleres);
	$allroles = Array();
	if($roleresCount) {
		for($index = 0; $index < $roleresCount; ++$index)
			$allroles[] = $adb->query_result($roleres, $index, 'roleid');
	}
	sort($allroles);

	// Get all the picklist values associated to roles (except H1 - organization role).
	$picklistres = $adb->query(
		"SELECT $columnname as pickvalue, roleid FROM $tablename
		INNER JOIN ".$table_prefix."_role2picklist ON $tablename.picklist_valueid=".$table_prefix."_role2picklist.picklistvalueid
		WHERE roleid != 'H1'");

	$picklistresCount = $adb->num_rows($picklistres);

	$picklistval_roles = Array();
	if($picklistresCount) {
		for($index = 0; $index < $picklistresCount; ++$index) {
			$picklistval = $adb->query_result($picklistres, $index, 'pickvalue');
			$pickvalroleid=$adb->query_result($picklistres, $index, 'roleid');
			$picklistval_roles[$picklistval][] = $pickvalroleid;
		}
	}
	// Collect picklist value which is associated to all the roles.
	$allrolevalues = Array();
	foreach($picklistval_roles as $picklistval => $pickvalroles) {
		sort($pickvalroles);
		$diff = array_diff($pickvalroles,$allroles);
		if(empty($diff)) $allrolevalues[] = $picklistval;
	}

	return $allrolevalues;
}

/**
 * Get all picklist values for a non-standard picklist type.
 */
function vtlib_getPicklistValues($field_columnname) {
	global $adb,$table_prefix;

	$columnname =  $adb->sql_escape_string($field_columnname);
	$tablename = $table_prefix."_$columnname";

	$picklistres = $adb->query("SELECT $columnname as pickvalue FROM $tablename");

	$picklistresCount = $adb->num_rows($picklistres);

	$picklistvalues = Array();
	if($picklistresCount) {
		for($index = 0; $index < $picklistresCount; ++$index) {
			$picklistvalues[] = $adb->query_result($picklistres, $index, 'pickvalue');
		}
	}
	return $picklistvalues;
}

/**
 * Check for custom module by its name.
 */
function vtlib_isCustomModule($moduleName) {
	//crmv@sdk-24185
	$sdkClass = SDK::getClass($module);
	if (!empty($sdkClass)) {
		$moduleFile = $sdkClass['src'];
		$moduleName = $sdkClass['module'];
	} else {
		$moduleFile = "modules/$moduleName/$moduleName.php";
	}
	//crmv@sdk-24185e
	if(file_exists($moduleFile)) {
		if(function_exists('checkFileAccess')) {
			checkFileAccess($moduleFile);
		}
		include_once($moduleFile);
		$focus = new $moduleName();
		return (isset($focus->IsCustomModule) && $focus->IsCustomModule);
	}
	return false;
}

/**
 * Get module specific smarty template path.
 */
function vtlib_getModuleTemplate($module, $templateName) {
	return ("modules/$module/$templateName");
}

/**
 * Check if give path is writeable.
 */
function vtlib_isWriteable($path) {
	if(is_dir($path)) {
		return vtlib_isDirWriteable($path);
	} else {
		return is_writable($path);
	}
}

/**
 * Check if given directory is writeable.
 * NOTE: The check is made by trying to create a random file in the directory.
 */
function vtlib_isDirWriteable($dirpath) {
	if(is_dir($dirpath)) {
		do {
			$tmpfile = 'vtiger' . time() . '-' . rand(1,1000) . '.tmp';
			// Continue the loop unless we find a name that does not exists already.
			$usefilename = "$dirpath/$tmpfile";
			if(!file_exists($usefilename)) break;
		} while(true);
		$fh = @fopen($usefilename,'a');
		if($fh) {
			fclose($fh);
			unlink($usefilename);
			return true;
		}
	}
	return false;
}

/** HTML Purifier global instance */
$__htmlpurifier_instance = false;
/**
 * Purify (Cleanup) malicious snippets of code from the input
 *
 * @param String $value
 * @param Boolean $ignore Skip cleaning of the input
 * @return String
 */
function vtlib_purify($input, $ignore=false) {
	global $__htmlpurifier_instance, $root_directory, $default_charset;

	$use_charset = $default_charset;
	$use_root_directory = $root_directory;

	$value = $input;
	if(!$ignore) {

		//crmv@26710
		$input = preg_replace('/into.+(outfile|dumpfile)/is', ' XXXXXX ', $input); // prevent sql injection
  		$input = preg_replace('/load.+infile/is', ' XXXXXX ', $input); // prevent sql injection
  		//crmv@26710e

  		// Initialize the instance if it has not yet done
		if($__htmlpurifier_instance == false) {
			if(empty($use_charset)) $use_charset = 'UTF-8';
			if(empty($use_root_directory)) $use_root_directory = dirname(__FILE__) . '/../..';

			try {
				include_once ('include/htmlpurifier/library/HTMLPurifier.auto.php');
			} catch (Exception $e) {
				// probably autoload failed
				if (class_exists('HTMLPurifier_Bootstrap')) {
					spl_autoload_register(array('HTMLPurifier_Bootstrap', 'autoload'));
				}
			}

			// crmv@81269
			$config = HTMLPurifier_Config::createDefault();
	    	$config->set('Core.Encoding', $use_charset);
	    	$config->set('Cache.SerializerPath', "$use_root_directory/test/vtlib");
	    	// crmv@81269e

			$__htmlpurifier_instance = new HTMLPurifier($config);
		}
		//crmv@29386
		if($__htmlpurifier_instance) {
			// Composite type
			if (is_array($input)) {
				$value = array();
				foreach ($input as $k => $v) {
					$value[$k] = vtlib_purify($v, $ignore);
				}
			} else { // Simple type
				$value = $__htmlpurifier_instance->purify($input);
			}
		}
		//crmv@29386e
	}
	return $value;
}

/**
 * Process the UI Widget requested
 * @param Vtiger_Link $widgetLinkInfo
 * @param Current Smarty Context $context
 * @return
 */
function vtlib_process_widget($widgetLinkInfo, $context = false) {
	if (preg_match("/^block:\/\/(.*)/", $widgetLinkInfo->linkurl, $matches)) {
		list($widgetControllerClass, $widgetControllerClassFile) = explode(':', $matches[1]);
		if (!class_exists($widgetControllerClass)) {
			checkFileAccess($widgetControllerClassFile);
			include_once $widgetControllerClassFile;
		}
		if (class_exists($widgetControllerClass)) {
			$widgetControllerInstance = CRMEntity::getInstance($widgetControllerClass);	//crmv@sdk-24185
			$widgetInstance = $widgetControllerInstance->getWidget($widgetLinkInfo->linklabel);
			if ($widgetInstance) {
				return $widgetInstance->process($context);
			}
		}
	}
	return "";
}
function vtlib_widget_title($widgetLinkInfo) {
	if (preg_match("/^block:\/\/(.*)/", $widgetLinkInfo->linkurl, $matches)) {
		list($widgetControllerClass, $widgetControllerClassFile) = explode(':', $matches[1]);
		if (!class_exists($widgetControllerClass)) {
			checkFileAccess($widgetControllerClassFile);
			include_once $widgetControllerClassFile;
		}
		if (class_exists($widgetControllerClass)) {
			$widgetControllerInstance = CRMEntity::getInstance($widgetControllerClass);	//crmv@sdk-24185
			$widgetInstance = $widgetControllerInstance->getWidget($widgetLinkInfo->linklabel);
			if ($widgetInstance && method_exists($widgetInstance,'title')) {
				return $widgetInstance->title($context);
			}
		}
	}
	return "";
}
?>