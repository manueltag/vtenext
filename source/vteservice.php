<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ********************************************************************************/

if (isset($_REQUEST['service'])) {
	if($_REQUEST['service'] == "outlook") {
		include("soap/vtigerolservice.php");
	} elseif($_REQUEST['service'] == "customerportal") {
		include("soap/customerportal.php");
	} elseif($_REQUEST['service'] == "webforms") {
		include("soap/webforms.php");
	} elseif($_REQUEST['service'] == "firefox") {
		include("soap/firefoxtoolbar.php");
	} elseif($_REQUEST['service'] == "wordplugin") {
		include("soap/wordplugin.php");
	} elseif($_REQUEST['service'] == "thunderbird") {
		include("soap/thunderbirdplugin.php");
	} else {
		echo "No Service Configured for {$_REQUEST['service']}";
	}
} else {
	echo "<h1>VTECRM Soap Services</h1>\n";
	echo "<ul>\n";
	echo "<li>VTECRM Outlook Plugin EndPoint URL -- Click <a href='vteservice.php?service=outlook'>here</a></li>\n";
	echo "<li>VTECRM Word Plugin EndPoint URL -- Click <a href='vteservice.php?service=wordplugin'>here</a></li>\n";
	echo "<li>VTECRM ThunderBird Extenstion EndPoint URL -- Click <a href='vteservice.php?service=thunderbird'>here</a></li>\n";
	echo "<li>VTECRM Customer Portal EndPoint URL -- Click <a href='vteservice.php?service=selfportal'>here</a></li>\n";
	echo "<li>VTECRM Legacy Customer Portal EndPoint URL -- Click <a href='vteservice.php?service=customerportal'>here</a></li>\n";
	echo "<li>VTECRM WebForm EndPoint URL -- Click <a href='vteservice.php?service=webforms'>here</a></li>\n";
	echo "<li>VTECRM FireFox Extension EndPoint URL -- Click <a href='vteservice.php?service=firefox'>here</a></li>\n";
	echo "</ul>\n";
}