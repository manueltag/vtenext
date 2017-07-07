<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

$title = $enterprise_mode. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . "Setup Cancelled";
$sectionTitle = "Setup Cancelled";

include_once "install/templates/overall/header.php";

?>

<div id="config" class="col-xs-12">
	<div id="config-inner" class="col-xs-12 content-padding">
		<p id="config-content">The setup has been cancelled, you can safely close this browser window.</p>
		<div class="spacer-20"></div>
	</div>
</div>
			
<?php include_once "install/templates/overall/footer.php"; ?>