{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@92272 *}
<script src="{"modules/Settings/ProcessMaker/resources/ProcessMakerScript.js"|resourcever}" type="text/javascript"></script>

{if $MODE eq ''}
	<table border=0 cellspacing=0 cellpadding=3 width=100%>
		<tr>
			<td colspan="6" align="right">
				<form style="display: inline;" action="index.php?module=Settings&amp;action=ProcessMaker&amp;mode=create&amp;parenttab=Settings" method="POST">
					<input type="submit" class="crmbutton small create" value='{$APP.LBL_NEW}' title='{$APP.LBL_NEW}'>
				</form>
			</td>
		</tr>
	</table>
{/if}
<table border=0 cellspacing=0 cellpadding=3 width=100% class="listTable">
	<tr>
	{foreach item=column from=$HEADER}
		<td class="colHeader small">{$column}</td>
	{/foreach}
	</tr>
	{foreach item=entity from=$LIST}
		<tr>
			{foreach item=column from=$entity}
				<td class="listTableRow small">{$column}</td>
			{/foreach}
		</tr>
	{/foreach}
</table>