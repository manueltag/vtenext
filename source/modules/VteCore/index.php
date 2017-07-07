<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@83340 crmv@102334 */

global $currentModule;

$indexFile = 'HomeView';
if (file_exists("modules/$currentModule/$indexFile.php")) {
	checkFileAccess("modules/$currentModule/$indexFile.php");
	require("modules/$currentModule/$indexFile.php");
} else {
	require("modules/VteCore/$indexFile.php");
}
