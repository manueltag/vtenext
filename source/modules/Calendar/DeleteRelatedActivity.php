<?php
//crmv@17001
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.mozilla.org/MPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Activities/Delete.php,v 1.11 2005/04/18 10:37:49 samk Exp $
 * Description:  TODO: To be written.
 ********************************************************************************/

if (isPermitted('Calendar','Delete',$_REQUEST['related_id'] == 'no')) {
	echo "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";	
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) . "' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>$app_strings[LBL_PERMISSION]</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>			   	
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>								   						     </td>
		</tr>
		</tbody></table> 
		</div>";
	echo "</td></tr></table>";
	die;
}

require_once('modules/Calendar/Activity.php');

$focus = CRMEntity::getInstance('Activity');

//Added to fix 4600
$url = getBasic_Advance_SearchURL();

if(!isset($_REQUEST['record']))
	die($mod_strings['ERR_DELETE_RECORD']);

global $adb,$table_prefix;
if ($_REQUEST['type'] == 'children')
	$adb->pquery('delete from '.$table_prefix.'_seactivityrel where crmid = ? and activityid = ?',array($_REQUEST['record'],$_REQUEST['related_id']));
elseif ($_REQUEST['type'] == 'fathers')
	$adb->pquery('delete from '.$table_prefix.'_seactivityrel where crmid = ? and activityid = ?',array($_REQUEST['related_id'],$_REQUEST['record']));

header("Location: index.php?module=Calendar&action=DetailView&record=".$_REQUEST['record']);
//crmv@17001e
?>
