{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@3082m crmv@51684 crmv@57983 crmv@114260 *}
<script>
{literal}
function changeAccountPicklist() {
	var type = jQuery('#account').val();
	
	if (type) {
		// show all
		jQuery('table.hideableTable').show();
	} else {
		// hide all
		jQuery('table.hideableTable').hide();
		return;
	}
	if (type == 'Custom') {
		jQuery('#server_div').show();
		jQuery('#smtp_account').val('Custom');
	} else {
		jQuery('#server_div').hide();
		if (type && jQuery('#smtp_account').val() != 'Custom') {
			jQuery('#smtp_account').val(type);
		}
	}
	changeSmtpAccount();
	
	jQuery('#username')
		.attr('readonly',false)
		.css('background-color',false)
		.val('')
		.focus();
	jQuery('#email').val('');
	jQuery('#password_insert').val('');
	jQuery('#password').val('');
	
	jQuery('#smtp_username')
		.attr('readonly',false)
		.css('background-color',false)
		.val('');
	jQuery('#smtp_password_insert').val('');
	jQuery('#smtp_password').val('');
}
function changeSmtpAccount() {
	var smtp_type = jQuery('#smtp_account').val();

	if (smtp_type == 'Custom') {
		jQuery('#smtp_server_div').show();
	} else {
		jQuery('#smtp_server_div').hide();
	}
}
{/literal}
</script>
<form name="SaveAccount" action="index.php" method="POST">
	<input type="hidden" name="module" value="Messages">
	<input type="hidden" name="action" value="MessagesAjax">
	<input type="hidden" name="file" value="Settings/index">
	<input type="hidden" name="operation" value="SaveAccount">
	<input type="hidden" name="id" value="{$KEY}">
	<table border="0" cellpadding="2" cellspacing="5" width="100%">
		<tr>
			<td align="right" width="40%" style="padding:5px">Account</td>
			<td style="padding:5px">
				<select id="account" name="account" class="detailedViewTextBox" onChange="changeAccountPicklist();">
					{foreach item=av from=$ACCOUNTS_AVAILABLE}
						<option value="{$av.account}" {if $ACCOUNT.account eq $av.account}selected{/if}>{$av.label}</option>
					{/foreach}
				</select>
			</td>
			<td width="20%"></td>
		</tr>
	</table>
	<table border="0" cellpadding="2" cellspacing="5" width="100%" id="account_info" class="hideableTable" {if $ACCOUNT.account eq ''}style="display:none"{/if}>
		<tr>
			<td align="right" width="40%" style="padding:5px">{'LBL_USERNAME'|getTranslatedString:'Settings'}</td>
			<td style="padding:5px">
				<input type="text" id="username" name="username" value="{$ACCOUNT.username}" class="detailedViewTextBox" {if $ACCOUNT.username neq ''}readonly="readonly" style="background-color:#E8E8E8"{/if}/>
			</td>
			<td width="20%"></td>
		</tr>
		{* crmv@50745 *}
		<tr>
			<td align="right" style="padding:5px">{'LBL_EMAIL_ADDRESS'|getTranslatedString:'Settings'}</td>
			<td style="padding:5px">
				<input type="text" id="email" name="email" value="{$ACCOUNT.email}" class="detailedViewTextBox" placeholder="{'LBL_OPTIONAL'|getTranslatedString:'Messages'|strtolower}" />
			</td>
			<td width="20%"></td>
		</tr>
		{* crmv@50745e *}
		<tr>
			<td align="right" style="padding:5px">{'LBL_PASWRD'|getTranslatedString:'Settings'}</td>
			<td style="padding:5px">
				{* crmv@43764 *}
				<input type="password" id="password_insert" value="{if !empty($ACCOUNT.password)}********{/if}" class="detailedViewTextBox" onFocus="this.value='';" onChange="document.getElementById('password').value=this.value;" />
				<input type="hidden" id="password" name="password" value="" />
				{* crmv@43764e *}
			</td>
			<td width="20%"></td>
		</tr>
		<tr>
			<td align="right" style="padding:5px">{'LBL_DESCRIPTION'|getTranslatedString}</td>
			<td style="padding:5px">
				<input type="text" name="description" value="{$ACCOUNT.description}" class="detailedViewTextBox" placeholder="{'LBL_OPTIONAL'|getTranslatedString:'Messages'|strtolower}" />
			</td>
			<td width="20%"></td>
		</tr>
		<tr>
			<td align="right" style="padding:5px">{'LBL_MAIN'|getTranslatedString:'Messages'}</td>
			<td style="padding:5px">
				<div class="dvtCellInfo checkbox">
				<label>
					<input type="checkbox" name="main" class="" {if $ACCOUNT.main eq 1}checked{/if} />
				</label>
				</div>
			</td>
			<td width="20%"></td>
		</tr>
	</table>
	<table border="0" cellpadding="5" cellspacing="5" width="100%" id="server_div" class="hideableTable" {if $ACCOUNT.account neq 'Custom' || $ACCOUNT.account eq ''}style="display:none;"{/if}>
		<tr>
			<td colspan="3" align="center" class="dvInnerHeader">
				<div><b>{'LBL_MAIL_SERVER_IMAP'|getTranslatedString:'Settings'}</b></div>
			</td>
		</tr>
		<tr>
			<td align="right" width="40%" style="padding:5px">{'LBL_OUTGOING_MAIL_SERVER'|getTranslatedString:'Settings'}</td>
			<td style="padding:5px">
				<input type="text" name="server" value="{$ACCOUNT.server}" class="detailedViewTextBox" />
			</td>
			<td width="20%"></td>
		</tr>
		<tr>
			<td align="right" style="padding:5px">{'LBL_OUTGOING_MAIL_PORT'|getTranslatedString:'Settings'}</td>
			<td style="padding:5px">
				<input type="text" name="port" value="{$ACCOUNT.port}" class="detailedViewTextBox" />
			</td>
			<td width="20%"></td>
		</tr>
		<tr>
			<td align="right" style="padding:5px">SSL/TLS</td>
			<td style="padding:5px">
				<select name="ssl_tls" class="detailedViewTextBox">
					<option value="" {if $ACCOUNT.ssl_tls eq ""}selected{/if}>{'LBL_NONE'|getTranslatedString}</option>
					<option value="ssl" {if $ACCOUNT.ssl_tls eq "ssl"}selected{/if}>SSL</option>
					<option value="tls" {if $ACCOUNT.ssl_tls eq "tls"}selected{/if}>TLS</option>
				</select>
			</td>
			<td width="20%"></td>
		</tr>
		<tr>
			<td align="right" style="padding:5px">{'LBL_DOMAIN'|getTranslatedString:'Settings'}</td>
			<td style="padding:5px">
				<input type="text" name="domain" value="{$ACCOUNT.domain}" class="detailedViewTextBox" />
			</td>
			<td width="20%"></td>
		</tr>
	</table>
	<table border="0" cellpadding="2" cellspacing="5" width="100%" class="hideableTable" {if $ACCOUNT.account eq ''}style="display:none"{/if}>
		<tr>
			<td colspan="3" align="center" class="dvInnerHeader">
				<div><b>{'LBL_MAIL_SERVER_SMTP'|getTranslatedString:'Settings'}</b></div>
			</td>
		</tr>
		<tr>
			<td align="right" width="40%" style="padding:5px">{'LBL_SMTP_SERVER'|getTranslatedString:'Messages'}</td>
			<td style="padding:5px">
				<select id="smtp_account" name="smtp_account" class="detailedViewTextBox" onchange="changeSmtpAccount()">
					{foreach item=av from=$SMTP_ACCOUNTS_AVAILABLE}
						<option value="{$av.account}" {if $ACCOUNT.smtp_account eq $av.account}selected{/if}>{$av.label}</option>
					{/foreach}
				</select>
			</td>
			<td width="20%"></td>
		</tr>
	</table>
	<table border="0" cellpadding="5" cellspacing="5" width="100%" id="smtp_server_div" class="hideableTable" {if $ACCOUNT.smtp_account neq 'Custom'}style="display:none;"{/if}>
		<tr>
			<td align="right" width="40%" style="padding:5px">{'LBL_OUTGOING_MAIL_SERVER'|getTranslatedString:'Settings'}</td>
			<td style="padding:5px">
				<input type="text" name="smtp_server" value="{$ACCOUNT.smtp_server}" class="detailedViewTextBox" />
			</td>
			<td width="20%"></td>
		</tr>
		<tr>
			<td align="right" style="padding:5px">{'LBL_OUTGOING_MAIL_PORT'|getTranslatedString:'Settings'}</td>
			<td style="padding:5px">
				<input type="text" name="smtp_port" value="{$ACCOUNT.smtp_port}" class="detailedViewTextBox" />
			</td>
			<td width="20%"></td>
		</tr>
		<tr>
			<td align="right" style="padding:5px">{'LBL_USERNAME'|getTranslatedString:'Settings'}</td>
			<td style="padding:5px">
				<input type="text" id="smtp_username" name="smtp_username" value="{$ACCOUNT.smtp_username}" class="detailedViewTextBox" {if $ACCOUNT.smtp_username neq ''}readonly="readonly" style="background-color:#E8E8E8"{/if}/>
			</td>
			<td width="20%"></td>
		</tr>
		<tr>
			<td align="right" style="padding:5px">{'LBL_PASWRD'|getTranslatedString:'Settings'}</td>
			<td style="padding:5px">
				<input type="password" id="smtp_password_insert" value="{if !empty($ACCOUNT.smtp_password)}********{/if}" class="detailedViewTextBox" onFocus="this.value='';" onChange="jQuery('#smtp_password').val(this.value);" />
				<input type="hidden" id="smtp_password" name="smtp_password" value="" />
			</td>
			<td width="20%"></td>
		</tr>
		<tr>
			<td align="right" style="padding:5px">{'LBL_REQUIRES_AUTHENT'|getTranslatedString:'Settings'}</td>
			<td style="padding:5px">
				<div class="dvtCellInfo checkbox">
					<label>
						<input type="checkbox" name="smtp_auth" class="" {if $ACCOUNT.smtp_auth eq 'true'}checked{/if}/>
					</label>
				</div>
			</td>
			<td width="20%"></td>
		</tr>
	</table>
	{* crmv@44037 *}
	<table border="0" cellpadding="0" cellspacing="5" width="100%" class="hideableTable" {if $ACCOUNT.account eq ''}style="display:none"{/if}>
		<tr>
			<td align="center" class="dvInnerHeader">
				<div><b>{'Signature'|getTranslatedString}</b></div>
			</td>
		</tr>
		<tr>
			<td>
				<textarea class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'" name="signature" onBlur="this.className='detailedViewTextBox'" cols="90" rows="8">{$ACCOUNT.signature}</textarea>
				{if $FCKEDITOR_DISPLAY eq 'true'}
					{* crmv@42752 *}
					<script type="text/javascript">
						/* this is to have it working inside popups */
						window.CKEDITOR_BASEPATH = 'include/ckeditor/';
					</script>
					{* crmv@42752e *}
					<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
					<script type="text/javascript">
						var current_language_arr = "{php} echo $_SESSION['authenticated_user_language']; {/php}".split("_");
						var curr_lang = current_language_arr[0];
						var fldname = 'signature';
						{literal}
						jQuery(document).ready(function() {
							CKEDITOR.replace(fldname, {
								filebrowserBrowseUrl: 'include/ckeditor/filemanager/index.html',
								toolbar : 'Basic',
								language : curr_lang
							});
						});
						{/literal}
					</script>
				{/if}
			</td>
		</tr>
	</table>
	{* crmv@44037e *}
</form>