<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('modules/Reports/Reports.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

global $adb;
global $default_charset;
global $table_prefix;
$local_log =& LoggerManager::getLogger('index');
$focus = new Reports();

$rfid = vtlib_purify($_REQUEST['record']);
$mode = vtlib_purify($_REQUEST['savemode']);
$foldername = vtlib_purify($_REQUEST["foldername"]);
$foldername = function_exists(iconv) ? @iconv("UTF-8",$default_charset, $foldername) : $foldername;
$folderdesc = vtlib_purify($_REQUEST["folderdesc"]);
$foldername = str_replace('*amp*','&',$foldername);
$folderdesc = str_replace('*amp*','&',$folderdesc);

if($mode=="Save")
{
	if($rfid=="")
	{
		// crmv@30967
		$result = addEntityFolder('Reports', trim($foldername), $fldrdescription, $current_user->id, 'CUSTOMIZED');
		// crmv@30967e
		if($result!=false)
		{
			header("Location: index.php?action=ReportsAjax&file=ListView&mode=ajax&module=Reports");
		}else
		{
			include('modules/VteCore/header.php');	//crmv@30447

			$errormessage = "<font color='red'><B>Error Message<ul>
			<li><font color='red'>Error while inserting the record</font>
			</ul></B></font> <br>" ;
			echo $errormessage;
		}
	}
}elseif($mode=="Edit")
{
	if($rfid != "")
	{
		// crmv@30967
		$result = editEntityFolder($rfid, trim($foldername), $folderdesc);
		// crmv@30967e
		if($result!=false)
		{
			header("Location: index.php?action=ReportsAjax&file=ListView&mode=ajax&module=Reports");
		}else
		{
			include('modules/VteCore/header.php');	//crmv@30447
			$errormessage = "<font color='red'><B>Error Message<ul>
			<li><font color='red'>Error while updating the record</font>
			</ul></B></font> <br>" ;
			echo $errormessage;
		}
	}
}

?>