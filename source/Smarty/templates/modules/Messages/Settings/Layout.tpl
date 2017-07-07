{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
<form name="Layout" action="index.php">
	<input type="hidden" name="module" value="Messages">
	<input type="hidden" name="action" value="MessagesAjax">
	<input type="hidden" name="file" value="Settings/index">
	<input type="hidden" name="operation" value="SaveLayout">
	<table border="0" cellpadding="0" cellspacing="5" width="100%" align="center" style="padding-top:20px">
		<tr>
			<td align="right" width="40%"><input type="checkbox" id="list_descr_preview" name="list_descr_preview" {if $SETTINGS.list_descr_preview eq '1'}checked{/if}></td>
			<td align="left" width="60%"><label for="list_descr_preview">{'LBL_LIST_DESCR_PREVIEW'|getTranslatedString:'Messages'}</label></td>
		</tr>
		<tr>
			<td align="right" width="40%"><input type="checkbox" id="thread" name="thread" {if $SETTINGS.thread eq '1'}checked{/if}></td>
			<td align="left" width="60%"><label for="thread">{'LBL_THREAD_VIEW'|getTranslatedString:'Messages'}</label></td>
		</tr>
	</table>
</form>