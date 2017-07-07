{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@3082m *}
<form name="EditFilter" action="index.php">
	<input type="hidden" name="module" value="Messages">
	<input type="hidden" name="action" value="MessagesAjax">
	<input type="hidden" name="file" value="Settings/index">
	<input type="hidden" name="operation" value="SaveFilter">
	<input type="hidden" name="sequence" value="{$SEQUENCE}">
	<input type="hidden" name="account" value="{$ACCOUNT}">
	<table border="0" cellpadding="0" cellspacing="5" width="100%" align="center" style="padding-top:20px">
		<tr>
			<td align="right" width="40%">{'LBL_FILTER_WHERE'|getTranslatedString}</td>
			<td align="left" width="60%">
				<select name="filter_where">
					{foreach key=value item=label from=$WHERE_LIST}
						<option value="{$value}" {if $FILTER_WHERE eq $value}selected{/if}>{$label.label}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td align="right">{'LBL_FILTER_WHAT'|getTranslatedString}</td>
			<td align="left"><input type="text" size="32" name="filter_what" value="{$FILTER_WHAT}"></td>
		</tr>
		<tr>
			<td align="right">{'LBL_FILTER_FOLDER'|getTranslatedString}</td>
			<td align="left">
				<select name="filter_folder">
	    			{foreach key=value item=info from=$FOLDER_LIST}
	    				<option value="{$value}" {if $FILTER_FOLDER eq $value}selected{/if}>{$value}</option>
	    			{/foreach}
	    		</select>
			</td>
		</tr>
	</table>
</form>