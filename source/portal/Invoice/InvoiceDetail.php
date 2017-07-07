<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

global $result;
global $client;
global $Server_Path;
echo '<!--Get Invoice Details Information -->';
$customerid = $_SESSION['customer_id'];
$sessionid = $_SESSION['customer_sessionid'];
if($id != '')
{

	//Get the Basic Information
	$block = "Invoice";
	$params = array('id' => "$id", 'block'=>"$block", 'contactid'=>"$customerid",'sessionid'=>"$sessionid");
	$result = $client->call('get_invoice_detail', $params, $Server_Path, $Server_Path);
	// Check for Authorization
	if (count($result) == 1 && $result[0] == "#NOT AUTHORIZED#") {
		echo '<tr>
			<td colspan="6" align="center"><b>'.getTranslatedString('LBL_NOT_AUTHORISED').'</b></td>
		</tr></table></td></tr></table></td></tr></table>';
		die();
	}
	$invinfo = $result[0][$block];
	echo '<table><tr><td><input class="crmbutton small cancel" type="button" value="'.getTranslatedString('LBL_BACK_BUTTON').'" onclick="window.history.back();"/></td></tr></table>';
	echo getblock_fieldlist($invinfo);
	echo '<tr><td colspan ="4"><table width="100%">';
	echo '</table></td></tr>';	
	echo '</table></td></tr></table></td></tr></table>';
	echo '<!-- --End--  -->';

}
?>
