<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@43147 crmv@95157 */

global $currentModule;

$record = intval($_REQUEST['record']);
$userEmail = $_REQUEST['user_email'];

if ($_FILES['filename']['name'] != '') {
	
	// crmv@109570
	$focus = CRMEntity::getInstance('Documents'); 
	$focus->retrieve_entity_info($record, 'Documents');
	$focus->id = $record;
	// crmv@109570e
	
	$r = $focus->uploadRevision($record, $userEmail);
	
	if ($r) {
		echo '<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>';
		echo "<script type=\"text/javascript\">
				parent.document.location.reload();
			</script>";
	}
	
} else {
	echo "<script type=\"text/javascript\">
			alert('".getTranslatedString('Nessun file selezionato','Documents')."');
			history.back();
		  </script>";
}
