{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@3082m crmv@114260 *}
<table border="0" cellpadding="0" cellspacing="5" width="100%" id="account_list">
	<tr>
		<td width="120" align="center" class="lvtCol">{'LBL_ACTIONS'|getTranslatedString}</td>
		<td width="15%" align="center" class="lvtCol">Account</td>
		<td align="center" class="lvtCol">{'LBL_USERNAME'|getTranslatedString:'Settings'}</td>
		<td align="center" class="lvtCol">{'LBL_DESCRIPTION'|getTranslatedString}</td>
		<td width="80" align="center" class="lvtCol">{'LBL_MAIN'|getTranslatedString:'Messages'}</td>
		<td width="80" align="center" class="lvtCol">{'LBL_SMTP_SERVER'|getTranslatedString:'Messages'}</td>
	</tr>
	{foreach item=ACCOUNT from=$ACCOUNTS}
		{assign var=KEY value=$ACCOUNT.id}
		{include file='modules/Messages/Settings/Account.tpl' ACCOUNT=$ACCOUNT}
	{/foreach}
</table>
<script>
{if empty($ACCOUNTS)}
	addAccount();
{/if}
{literal}
function addAccount() {
	location.href='index.php?module=Messages&action=MessagesAjax&file=Settings/index&operation=EditAccount&id=';
}
{/literal}
</script>