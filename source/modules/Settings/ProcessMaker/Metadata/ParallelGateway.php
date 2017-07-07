<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@103534 */
$gateway_list = array();
foreach($structure['shapes'] as $elid => $info) {
	if ($elid != $elementid && strpos($info['type'],'Gateway') !== false) {
		if (empty($info['text'])) $info['text'] = $elid;
		$gateway_list[$elid] = $PMUtils->getElementTitle($info);
	}
}
$smarty->assign("GATEWAY_LIST", $gateway_list);