{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@3082m crmv@57983 *}
<tr id="account_div_{$KEY}">
	<td align="center" nowrap>
		{* crmv@114260 *}
		<a href="index.php?module=Messages&action=MessagesAjax&file=Settings/index&operation=EditAccount&id={$KEY}"><i class="vteicon md-link" title="{'LBL_EDIT'|getTranslatedString}">create</i></a>
		<a href="index.php?module=Messages&action=MessagesAjax&file=Settings/index&operation=DeleteAccount&id={$KEY}"><i class="vteicon md-link" title="{'LBL_DELETE'|getTranslatedString}">delete</i></a>
		<a href="index.php?module=Messages&action=MessagesAjax&file=Settings/index&operation=SyncAccount&id={$KEY}"><i class="vteicon md-link" title="{'LBL_FORCE_SYNC'|getTranslatedString}">autorenew</i></a>	{* crmv@51684 *}
		{* crmv@114260e *}
	</td>
	<td align="center">
		{foreach item=av from=$ACCOUNTS_AVAILABLE}
			{if $ACCOUNT.account eq $av.account}
				{assign var=label value=$av.label}
			{/if}
		{/foreach}
		{$label}
	</td>
	<td align="center">
		{$ACCOUNT.username}
	</td>
	<td align="center">
		{$ACCOUNT.description}
	</td>
	<td align="center">
		{if $ACCOUNT.main eq 1}{'LBL_YES'|getTranslatedString}{else}{'LBL_NO'|getTranslatedString}{/if}
	</td>
	{* crmv@114260 *}
	<td align="center">
		{if $ACCOUNT.smtp_account eq ''}Default{else}{$ACCOUNT.smtp_account}{/if}
	</td>
	{* crmv@114260e *}
</tr>
<tr id="server_div_{$KEY}" {if $ACCOUNT.account neq 'Custom'}style="display:none;"{/if}>
	<td></td>
	<td colspan="3">
		<table border="0" cellpadding="0" width="100%">
			<tr>
				<td>{'LBL_OUTGOING_MAIL_SERVER'|getTranslatedString:'Settings'}</td>
				<td>
					{$ACCOUNT.server}
				</td>
			</tr>
			<tr>
				<td>{'LBL_OUTGOING_MAIL_PORT'|getTranslatedString:'Settings'}</td>
				<td>
					{$ACCOUNT.port}
				</td>
			</tr>
			<tr>
				<td>SSL/TLS</td>
				<td>
					{if $ACCOUNT.ssl_tls eq ""}
						{'LBL_NONE'|getTranslatedString}
					{elseif $ACCOUNT.ssl_tls eq "ssl"}
						SSL
					{elseif $ACCOUNT.ssl_tls eq "tls"}
						TLS
					{/if}
				</td>
			</tr>
			<tr>
				<td>{'LBL_DOMAIN'|getTranslatedString:'Settings'}</td>
				<td>
					{$ACCOUNT.domain}
				</td>
			</tr>
		</table>
	</td>
	<td colspan="2"></td> {* crmv@114260 *}
</tr>