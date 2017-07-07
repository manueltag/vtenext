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

{if $LABEL.reminder_time != ''}
	<table class="{$tableClass}" width="100%" cellpadding="5" cellspacing="0" border="0">
		<tr>
			<td width="30%" align=right><b>{$MOD.LBL_SENDREMINDER}</b></td>
			<td width="70%" align=left>{$ACTIVITYDATA.set_reminder}</td>
		</tr>
		{if $ACTIVITYDATA.set_reminder != 'No'}
		<tr>
			<td width="30%" align=right><b>{$MOD.LBL_RMD_ON}</b></td>
			<td width="70%" align=left>{$ACTIVITYDATA.reminder_str}</td>
		</tr>
		{/if}
	</table>
{/if}