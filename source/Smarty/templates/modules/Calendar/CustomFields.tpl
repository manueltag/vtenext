{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@98866 *} 

{assign var="LBL_CUSTOM_INFORMATION_TRANS" value=$APP.LBL_CUSTOM_INFORMATION}
{if $CUSTOM_FIELDS_DATA|@count > 0 && $CUSTOM_FIELDS_DATA.$LBL_CUSTOM_INFORMATION_TRANS|@count > 0}
<table border="0" cellspacing="0" cellpadding="5" width="100%">
	<tr height="10px">
		<td></td>
	</tr>
	<tr>
		<td colspan="2">
			<b>{$APP.LBL_CUSTOM_INFORMATION}</b>
		</td>
	</tr>
	{include file="DisplayFields.tpl"}
</table>
{/if}
