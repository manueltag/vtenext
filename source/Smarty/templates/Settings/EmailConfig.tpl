<!--
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 *********************************************************************************/
-->
{* crmv@32079 *}

<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/menu.js"|resourcever}"></script>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@30683 -->
<tbody><tr>
<td valign="top"></td>
<td class="showPanelBg" style="padding: 5px;" valign="top" width="100%"> <!-- crmv@30683 -->

	{if $EMAILCONFIG_MODE neq 'edit'}	
		<form action="index.php" method="post" name="MailServer" id="form" onsubmit="VtigerJS_DialogBox.block();">
		<input type="hidden" name="emailconfig_mode">
	{else}
		{literal}
		<form action="index.php" method="post" name="MailServer" id="form" onsubmit="if(validate_mail_server(MailServer)){ VtigerJS_DialogBox.block(); return true; } else { return false; }">
		{/literal}
		<input type="hidden" name="server_type" value="email">
	{/if}
	<input type="hidden" name="module" value="Settings">
	<input type="hidden" name="action">
	<input type="hidden" name="parenttab" value="Settings">
	<input type="hidden" name="return_module" value="Settings">
	<input type="hidden" name="return_action" value="EmailConfig">
	<div align=center>
			
		{include file="SetMenu.tpl"}
		{include file='Buttons_List.tpl'} {* crmv@30683 *}
		 
			<!-- DISPLAY -->
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
			<tr>
				<td width=50 rowspan=2 valign=top><img src="{$IMAGE_PATH}ogmailserver.gif" alt="{$MOD.LBL_USERS}" width="48" height="48" border=0 title="{$MOD.LBL_USERS}"></td>
				<td class=heading2 valign=bottom><b> {$MOD.LBL_SETTINGS} > {$MOD.LBL_MAIL_SERVER_SETTINGS} </b></td> <!-- crmv@30683 -->
			</tr>
			<tr>
				<td valign=top class="small">{$MOD.LBL_MAIL_SERVER_DESC} </td>
			</tr>
			</table>
			
			<br>
			<table border=0 cellspacing=0 cellpadding=10 width=100% >
			<tr>
			<td>
			
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<tr>
					<td class="big"><strong>{$MOD.LBL_MAIL_SERVER_SMTP}</strong></td>
					{if $EMAILCONFIG_MODE neq 'edit'}	
					<td class="small" align=right>
						<input class="crmButton small edit" title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="this.form.action.value='EmailConfig';this.form.emailconfig_mode.value='edit'" type="submit" name="Edit" value="{$APP.LBL_EDIT_BUTTON_LABEL}">
					</td>
					{else}
					<td class="small" align=right>
						<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" onclick="this.form.action.value='Save';" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" >&nbsp;&nbsp;
    					<input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmButton small cancel" onclick="window.history.back()" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
					</td>
					{/if}
				</tr>
				{if $ERROR_MSG neq ''}
				<tr>
					{$ERROR_MSG}
				</tr>
				{/if}
				</table>
					
		{if $EMAILCONFIG_MODE neq 'edit'}
			
					<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
					<tr>
					<td class="small" valign=top><table width="100%"  border="0" cellspacing="0" cellpadding="5">
						  {if $SMTP_EDITABLE eq 1}	{* crmv@94084 *}
                          <tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>Account</strong></td>
                            <td width="80%" class="small cellText">{if $ACCOUNT_SMTP eq ''}{$MOD.LBL_ACCOUNT_MAIL_UNDEFINED}{elseif $ACCOUNT_SMTP eq 'Other'}{$MOD.LBL_ACCOUNT_MAIL_OTHER}{else}{$ACCOUNT_SMTP}{/if}</td>
                          </tr>
						  {/if}	{* crmv@94084 *}
                          {if $ACCOUNT_SMTP neq ''}
	                          {* crmv@94084 *}
	                          <tr>
	                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_OUTGOING_MAIL_SERVER}</strong></td>
	                            <td width="80%" class="small cellText">{$MAILSERVER}</td>
	                          </tr>
	                          {if $SMTP_EDITABLE eq 1}
	                          <tr>
								<td nowrap class="small cellLabel"><strong>{$MOD.LBL_OUTGOING_MAIL_PORT}</strong></td>
								<td class="small cellText">{$MAILSERVERPORT}</td>
							  </tr>
	                          <tr valign="top">
	                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_USERNAME}</strong></td>
	                            <td class="small cellText">{$USERNAME}</td>
	                          </tr>
	                          <tr>
	                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_PASWRD}</strong></td>
	                            <td class="small cellText">
									{if $PASSWORD neq ''}
									******
									{/if}
							     </td>
	                          </tr>
	                          <tr> 
	                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_REQUIRES_AUTHENT}</strong></td>
	                            <td class="small cellText">
								{if $SMTP_AUTH eq 'checked'}
									{$MOD.LBL_YES}
								{else}
									{$MOD.LBL_NO}
								{/if}
							    </td>
								</tr>
								{/if}
								{* crmv@94084e *}
							{/if}
							</table>
			                <table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
							<tr>
								<td class="big"><strong>{$MOD.LBL_MAIL_SERVER_IMAP}</strong></td>
							</tr>
							</table>
							{if empty($IMAP_ACCOUNTS)}
								<table width="100%" border="0" cellspacing="0" cellpadding="5">
									<tr>
										<td class="small cellText">{$MOD.LBL_ACCOUNT_MAIL_UNDEFINED}</td>
									</tr>
								</table>
							{else}
								<table width="100%" border="0" cellspacing="0" cellpadding="5">
									<tr>
										<td width="20%" nowrap class="small cellLabel"><strong>Account</strong></td>
										<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_OUTGOING_MAIL_SERVER}</strong></td>
										<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_OUTGOING_MAIL_PORT}</strong></td>
										<td width="20%" nowrap class="small cellLabel"><strong>SSL/TLS</strong></td>
										<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_DOMAIN}</strong></td>
									</tr>
									{foreach item=account from=$IMAP_ACCOUNTS}
										<tr>
											<td class="small cellText">{if $account.account_type eq 'Other'}{$MOD.LBL_ACCOUNT_MAIL_OTHER}{else}{$account.account_type}{/if}</td>
											<td class="small cellText">{$account.server}</td>
											<td class="small cellText">{$account.port}</td>
											<td class="small cellText">{if $account.ssl eq 'ssl'}SSL{elseif $account.ssl eq 'tls'}TLS{/if}</td>
											<td class="small cellText">{$account.domain}</td>
										</tr>
									{/foreach}
								</table>
							{/if}
		{else}
					
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
			<tr>
			<td class="small" valign=top>
				{* crmv@94084 *}
				{if $SMTP_EDITABLE eq 1}
					<table width="100%"  border="0" cellspacing="0" cellpadding="5">
	                	<tr>
							<td width="20%" nowrap class="small cellLabel"><strong>Account</strong></td>
							<td width="80%" class="small cellText">
								<div class="dvtCellInfo">
									<select name="account_smtp" onchange="calculateAccount(this.value,'smtp');" class="detailedViewTextBox">
										{foreach key=i item=v from=$ACCOUNT_SMTP_LIST}
											<option value="{$i}" {if $ACCOUNT_SMTP eq $i}selected{/if}>{$v}</option>
										{/foreach}
									</select>
								</div>
				    		</td>
						</tr>
					</table>
					<div id="account_container_smtp">
						{if $ACCOUNT_SMTP neq ''}
							{assign var="SERVER_ACCOUNT" value="smtp"}
							{include file="Settings/EmailConfigAccount.tpl"}
						{/if}
					</div>
				{else}
					<table width="100%"  border="0" cellspacing="0" cellpadding="5">
						<tr>
							<td width="20%" nowrap class="small cellLabel"><strong>* {$MOD.LBL_OUTGOING_MAIL_SERVER}</strong></td>
							<td width="80%" class="small cellText">
								<div class="dvtCellInfoOff">
									<input type="text" class="detailedViewTextBox small" value="{$MAILSERVER}" name="server" readonly>
								</div>
						    </td>
						</tr>
					</table>
				{/if}
				{* crmv@94084e *}
				
				<!-- crmv@16265 -->
                <table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<tr>
					<td class="big"><strong>{$MOD.LBL_MAIL_SERVER_IMAP}</strong></td>
				</tr>
				</table>
				<table border=0 cellspacing=0 cellpadding=0 width=100%>
				<tr>
				<td class="small" valign=top>
					<table width="100%" border="0" cellspacing="0" cellpadding="5" id="account_container_imap">
						<tr>
							<td class="small cellLabel"><strong>{'LBL_ACTIONS'|getTranslatedString}</strong></td>
							<td width="20%" class="small cellLabel"><strong>Account</strong></td>
							<td width="20%" class="small cellLabel"><strong>{$MOD.LBL_OUTGOING_MAIL_SERVER}</strong></td>
							<td width="20%" class="small cellLabel"><strong>{$MOD.LBL_OUTGOING_MAIL_PORT}</strong></td>
							<td width="20%" class="small cellLabel"><strong>SSL/TLS</strong></td>
							<td width="20%" class="small cellLabel"><strong>{$MOD.LBL_DOMAIN}</strong></td>
						</tr>
						{assign var="SERVER_ACCOUNT" value="imap"}
						{foreach key=i item=account from=$IMAP_ACCOUNTS}
							{include file="Settings/EmailConfigAccount.tpl"}
						{/foreach}
					</table>
					<table border="0" cellpadding="0" cellspacing="5" width="100%">
						<tr>
							<td>
								<input type="button" class="crmbutton small create" value="{'LBL_ADD_BUTTON'|getTranslatedString}" onclick="addAccount('{$SERVER_ACCOUNT}');">
							</td>
						</tr>
					</table>
				</td>
				</tr>
				</table>
                <!-- crmv@16265e -->
                
			{/if}
						
						</td>
					  </tr>
					</table>
					<!--table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr>
					  <td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
					</tr>
					</table-->
				</td>
				</tr>
				</table>
			
			
			
			</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
		
	</div>
	</form>
	
</td>
<td valign="top"></td>
</tr>
</tbody>
</table>
{literal}
<script>
function validate_mail_server(form) {
	if(form.account_smtp.value != '' && form.server.value == '') {
		{/literal}
		alert("{$APP.SERVERNAME_CANNOT_BE_EMPTY}")
		return false;
		{literal}
	}
	return true;
}
function calculateAccount(value,account,seq) {
	if (account == 'imap') {
		var container = 'imap_account_div_'+seq;
	} else {
		var container = 'account_container_'+account;
	}
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=EmailConfig&mode=ajax&calculate_account='+value+'&account_type='+account+'&seq='+seq,
			onComplete: function(response) {
				$(container).innerHTML = response.responseText;
				$("status").style.display="none";
			}
		}
	);
}
function addAccount(account) {
	var num = jQuery('[id^="imap_account_div_"]').length;
	jQuery.ajax({
		url: 'index.php?module=Settings&action=SettingsAjax&file=EmailConfig&mode=ajax&calculate_account=&account_type='+account+'&seq='+num,
		type: 'post',
		success: function(data) {
			jQuery('#account_container_imap').append(data);
		}
	});
}
</script>
{/literal}