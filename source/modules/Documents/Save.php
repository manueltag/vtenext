<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

/* crmv@45561 */

require_once('modules/Documents/Documents.php');
require_once('include/logging.php');

global $root_directory;
$local_log =& LoggerManager::getLogger('index');
global $currentModule;
$focus = $focus = CRMEntity::getInstance('Documents');
//added to fix 4600
setObjectValuesFromRequest($focus);
$search=vtlib_purify($_REQUEST['search_url']);
$params = '';

if(isset($_REQUEST['notecontent']) && $_REQUEST['notecontent'] != "")
	$_REQUEST['notecontent'] = fck_from_html($_REQUEST['notecontent']);

if (!isset($_REQUEST['date_due_flag'])) $focus->date_due_flag = 'off';

if(isset($_REQUEST['parentid']) && $_REQUEST['parentid'] != '')
	$focus->parentid = $_REQUEST['parentid'];
if($_REQUEST['assigntype'] == 'U')  {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_user_id'];
} elseif($_REQUEST['assigntype'] == 'T') {
	$focus->column_fields['assigned_user_id'] = $_REQUEST['assigned_group_id'];
}
//Save the Document

$focus->save($currentModule);

$return_id = $focus->id;

$parenttab = getParentTab();
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != "") $return_module = vtlib_purify($_REQUEST['return_module']);
else $return_module = "Documents";
if(isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != "") $return_action = vtlib_purify($_REQUEST['return_action']);
else $return_action = "DetailView";
if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "") $return_id = vtlib_purify($_REQUEST['return_id']);

$local_log->debug("Saved record with id of ".$return_id);

//Redirect to EditView if the given file is not valid.
$file_upload_error = $_REQUEST['upload_error'];
if($file_upload_error)
{
	$return_module = 'Documents';
	$return_action = 'EditView';
	$params .= '&upload_error=true';
}

//code added for returning back to the current view after edit from list view
if($_REQUEST['return_viewname'] == '') $return_viewname='0';
if($_REQUEST['return_viewname'] != '') $return_viewname=vtlib_purify($_REQUEST['return_viewname']);

//crmv@54375
if($_REQUEST['return2detail'] == 'yes') {
	$return_module = $currentModule;
	$return_action = 'DetailView';
	$return_id = $focus->id;
}
//crmv@54375e

$url = "index.php?action=$return_action&module=$return_module&parenttab=$parenttab&record=$return_id&viewname=$return_viewname&start=".vtlib_purify($_REQUEST['pagenumber']).$params.$search;

$from_module = vtlib_purify($_REQUEST['module']);
if (!empty($from_module)) $url .= "&from_module=$from_module";

header("Location: $url");
?>