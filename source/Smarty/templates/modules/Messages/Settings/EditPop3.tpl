{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@3082m *}
<form name="EditPop3" action="index.php">
	<input type="hidden" name="module" value="Messages">
	<input type="hidden" name="action" value="MessagesAjax">
	<input type="hidden" name="file" value="Settings/index">
	<input type="hidden" name="operation" value="SavePop3">
	<input type="hidden" name="id" value="{$ID}">
	<table border="0" cellpadding="0" cellspacing="5" width="100%" align="center" style="padding-top:20px">
		<tr>
			<td align="right" width="40%">{'LBL_OUTGOING_MAIL_SERVER'|getTranslatedString:'Settings'}</td>
			<td align="left" width="60%"><input type="text" size="32" name="server" value="{$SERVER}"></td>
		</tr>
		<tr>
			<td align="right">{'LBL_OUTGOING_MAIL_PORT'|getTranslatedString:'Settings'}</td>
			<td align="left"><input type="text" size="32" name="port" value="{$PORT}"></td>
		</tr>
		<tr>
			<td align="right">{'LBL_USERNAME'|getTranslatedString:'Settings'}</td>
			<td align="left"><input type="text" size="32" name="username" value="{$USERNAME}"></td>
		</tr>
		<tr>
			<td align="right">{'LBL_PASWRD'|getTranslatedString:'Settings'}</td>
			<td align="left">
				{* crmv@43764 *}
				<input type="password" value="{if !empty($PASSWORD)}********{/if}" class="small" onFocus="this.value='';" onChange="document.getElementById('password').value=this.value;" />
				<input type="hidden" id="password" name="password" value="">
				{* crmv@43764e *}
			</td>
		</tr>
		<tr>
			<td align="right">SSL/TLS</td>
			<td align="left">
				<select name="secure">
	    			<option value="" {if $SECURE eq ''}selected{/if}>--Nessuno--</option>
	    			<option value="ssl" {if $SECURE eq 'ssl'}selected{/if}>SSL</option>
	    			<option value="tls" {if $SECURE eq 'tls'}selected{/if}>TLS</option>
	    		</select>
			</td>
		</tr>
		<tr>
			<td align="right">{'LBL_FETCH'|getTranslatedString:'Messages'} {'LBL_POP3_FETCH_IN'|getTranslatedString:'Messages'}</td>
			<td align="left">
				{$ACCOUNTS}
				<span id="folderpicklistcontainer">
					<select name="folder">
		    			{foreach key=value item=info from=$FOLDER_LIST}
		    				<option value="{$value}" {if $FOLDER eq $value}selected{/if}>{$value}</option>
		    			{/foreach}
		    		</select>
		    	</span>
			</td>
		</tr>
		<tr>
			<td align="right"><label for="lmos">{'LBL_LEAVES_FETCH_IN'|getTranslatedString:'Messages'}</label></td>
			<td align="left"><input type="checkbox" id="lmos" name="lmos" {if $LMOS eq '1'}checked{/if}></td>
		</tr>
		<tr>
			<td align="right"><label for="active">{'LBL_ACTIVE'|getTranslatedString:'Settings'}</label></td>
			<td align="left"><input type="checkbox" id="active" name="active" {if $ACTIVE eq '1'}checked{/if}></td>
		</tr>
	</table>
</form>