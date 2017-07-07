<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@80155 */

$small_page_title = getTranslatedString('LBL_EMAIL_TEMPLATE','Settings');
$small_page_buttons = '
	<script type="text/javascript" src="modules/Newsletter/Newsletter.js"></script> 
	<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" style="padding:5px"></td>
	 	<td align="right" style="padding: 5px;" nowrap>
			<input class="crmbutton small save" onclick="submittemplate('.$_REQUEST['record'].','.$_REQUEST['templateid'].',\''.$_REQUEST["templatename"].'\')" type="button" title="'.getTranslatedString('LBL_SELECT').'" value="'.getTranslatedString('LBL_SELECT').'">
			<input class="crmbutton small cancel" onclick="history.back()" type="button" title="'.getTranslatedString('LBL_BACK').'" value="'.getTranslatedString('LBL_BACK').'">
	 	</td>
	 </tr>
	 </table>';
include('themes/SmallHeader.php');

$preview = true;
include('modules/Settings/detailviewemailtemplate.php');

include('themes/SmallFooter.php');
?>