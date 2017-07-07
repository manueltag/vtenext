{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@97566 *}
<table width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td class="detailedViewHeader" nowrap="nowrap">
			<b>{$MOD.LBL_PM_PREVIEW_RECURRENCE}</b>
		</td>
	</tr>
	{foreach item=PREVIEW from=$PREVIEWS}
		<tr><td style="padding:5px;">{$PREVIEW}</td></tr>
	{/foreach}
</table>