<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Notes/index.php,v 1.3 2005/03/17 15:42:56 samk Exp $
 * Description: TODO:  To be written.
 ********************************************************************************/
/* crmv@90004 */

global $currentModule;

if (file_exists("modules/$currentModule/ListViewFolder.php")) {
	checkFileAccess("modules/$currentModule/ListViewFolder.php");
	include("modules/$currentModule/ListViewFolder.php");
} else {
	include("modules/VteCore/ListViewFolder.php");	//crmv@30967
}
?>
