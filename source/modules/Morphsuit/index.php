<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
if ($_REQUEST['module'] == 'Morphsuit' && $_REQUEST['action'] == 'MorphsuitAjax' && !empty($_REQUEST['file'])) {
	// crmv@79022
	$file = str_replace(array('.', ':', '\\', '/'), '', $_REQUEST['file']).'.php';
	if (file_exists($file)) {
		include($file);
	}
	// crmv@79022e
}
exit;
?>
