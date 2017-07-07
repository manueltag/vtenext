{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@98866 *}

{if !$disableStyle}
	{assign var="tableClass" value="table"}
{else}
	{assign var="tableClass" value=""}
{/if}

<table class="{$tableClass}" width="100%" cellpadding="5" cellspacing="0" border="0">
	<tr>
		{if $LABEL.parent_id neq ''}
		<td width="30%" align=right>
			<b>{$LABEL.parent_id}</b>
		</td>
		<td width="70%" align=left>{$ACTIVITYDATA.parent_name}</td>
		{/if}
	</tr>
	<tr>
		{if $LABEL.contact_id neq ''}
		<td width="30%" align=right>
			<b>{$MOD.LBL_CONTACT_NAME}</b>
		</td>
		<td width="70%" align=left>
			<a href="{$ACTIVITYDATA.contact_idlink}">{$ACTIVITYDATA.contact_id}</a>
		</td>
		{/if}
	</tr>
</table>
