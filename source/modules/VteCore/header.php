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
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/

/* crmv@75301 */

require_once("config.inc.php");
require_once("include/utils/utils.php");
require_once('modules/SDK/src/Favorites/Utils.php');	//crmv@26986
require_once('include/utils/PageHeader.php');

// by using a class, I can extend it and provide customizations easily
$VPH = VTEPageHeader::getInstance();
//crmv@124738
$VPH->displayHeader(array(
	'hide_menus' => $_REQUEST['hide_menus'],
	'query_string' => $_REQUEST['query_string'],
	'fastmode' => (intval($_REQUEST['fastmode']) == 1) ? true : false,
));
//crmv@124738e