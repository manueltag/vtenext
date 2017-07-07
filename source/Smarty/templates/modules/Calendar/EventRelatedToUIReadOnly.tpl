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
	{if $LABEL.parent_id neq ''}
	<tr>
		<td width="30%" align=right valign="top"><b>{$LABEL.parent_id}</b></td>
		<td width="70%" align=left valign="top">{$ACTIVITYDATA.parent_name}</td>
	</tr>
	{/if}
	<tr>
		<td width="30%" valign="top" align=right><b>{$MOD.LBL_CONTACT_NAME}</b></td>
		<td width="70%" valign="top" align=left>
			{foreach item=contactname key=cntid from=$CONTACTS}
				{if $IS_PERMITTED_CNT_FNAME == '0'}
					{$contactname.2}{$contactname.1}<br />
				{/if}
			{/foreach}
		</td>
	</tr>
</table>