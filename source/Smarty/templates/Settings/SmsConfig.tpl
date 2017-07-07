<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/menu.js"|resourcever}"></script>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@30683 -->
<tbody><tr>
        <td valign="top"></td>
        <td class="showPanelBg" style="padding: 5px;" valign="top" width="100%"> <!-- crmv@30683 -->
	{if $SMSCONFIG_MODE neq 'edit'}	
	<form action="index.php" method="post" name="SmsServer" id="form" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="smsconfig_mode">
	{else}
	<form action="index.php" method="post" name="SmsServer" id="form" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="server_type" value="sms">
	{/if}
	<input type="hidden" name="module" value="Settings">
	<input type="hidden" name="action">
	<input type="hidden" name="parenttab" value="Settings">
	<input type="hidden" name="return_module" value="Settings">
	<input type="hidden" name="return_action" value="SmsConfig">
	<div align=center>
			
			{include file="SetMenu.tpl"}
			{include file='Buttons_List.tpl'} {* crmv@30683 *} 
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{$IMAGE_PATH}ogsmsserver.gif" alt="{$MOD.LBL_USERS}" width="48" height="48" border=0 title="{$MOD.LBL_USERS}"></td>
					<td class=heading2 valign=bottom><b> {$MOD.LBL_SETTINGS} > {$MOD.LBL_SMS_SERVER_SETTINGS} </b></td> <!-- crmv@30683 -->
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_SMS_SERVER_DESC} </td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$MOD.LBL_SMS_SERVER_SMTP}</strong></td>
						{if $SMSCONFIG_MODE neq 'edit'}	
						<td class="small" align=right>
							<input class="crmButton small edit" title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="this.form.action.value='SmsConfig';this.form.smsconfig_mode.value='edit'" type="submit" name="Edit" value="{$APP.LBL_EDIT_BUTTON_LABEL}">
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
					
					{if $SMSCONFIG_MODE neq 'edit'}	
					<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
					<tr>
					<td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
                          <tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_OUTGOING_SMS_SERVER_TYPE}</strong></td>
                            <td width="80%" class="small cellText"><strong>{$MOD.$SMSSERVERTYPE}&nbsp;</strong></td>
                          </tr>					
                          <tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_OUTGOING_SMS_SERVER}</strong></td>
                            <td width="80%" class="small cellText"><strong>{$SMSSERVER}&nbsp;</strong></td>
                          </tr>
                          <tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_USERNAME}</strong></td>
                            <td class="small cellText">{$USERNAME}&nbsp;</td>
                          </tr>
                          <tr>
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_PASWRD}</strong></td>
                            <td class="small cellText">
				{if $PASSWORD neq ''}
				******
				{/if}&nbsp;
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
                          
                        </table>       
			  {else}
					
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
			<tr>
			<td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
                        <tr>
                            <td width="20%" nowrap class="small cellLabel"><font color="red">*</font>&nbsp;<strong>{$MOD.LBL_OUTGOING_SMS_SERVER_TYPE}</strong></td>
                            <td width="80%" class="small cellText">
                            	<div class="dvtCellInfo">
						            <select name="service_type" id="service_type" class="detailedViewTextBox">
							            {foreach item=arr from=$SERVER_TYPE}
							            {if ($arr eq $SMSSERVERTYPE)}
							                 <option value="{$arr}" selected>
							            {else}
							            	  <option value="{$arr}">    
							            {/if}	   
							                 {$MOD.$arr}
							                 </option>
							            {/foreach} 
						            </select>
								</div>
							</td>
						</tr>
                        <tr>
                            <td width="20%" nowrap class="small cellLabel"><font color="red">*</font>&nbsp;<strong>{$MOD.LBL_OUTGOING_SMS_SERVER}</strong></td>
                            <td width="80%" class="small cellText">
                            	<div class="dvtCellInfo">
									<input type="text" class="detailedViewTextBox small" value="{$SMSSERVER}" name="server">
								</div>
						    </td>
                          </tr>
                          <tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_USERNAME}</strong></td>
                            <td class="small cellText">
                            	<div class="dvtCellInfo">
									<input type="text" class="detailedViewTextBox small" value="{$USERNAME}" name="server_username">
								</div>
			    			</td>
                          </tr>
                          <tr>
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_PASWRD}</strong></td>
                            <td class="small cellText">
								<div class="dvtCellInfo">
									{* crmv@43764 *}
									<input type="password" value="{if !empty($PASSWORD)}********{/if}" class="detailedViewTextBox small" onFocus="this.value='';" onChange="document.getElementById('server_password').value=this.value;" />
									<input type="hidden" id="server_password" name="server_password" value="">
									{* crmv@43764e *}
								</div>
			    			</td>
                          </tr>
                          <tr> 
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_REQUIRES_AUTHENT}</strong></td>
                            <td class="small cellText">
								<input type="checkbox" name="smtp_auth" {$SMTP_AUTH}/>
			    			</td>
                          </tr>
                        </table>
				
			{/if}
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$MOD.LBL_SMS_ADVANCED_SETTINGS}</strong></td>
					</tr>
					</table>
					{if $SMSCONFIG_MODE neq 'edit'}	
					<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
					<tr>
					<td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SMS_DOMAIN}</strong></td>
                            <td width="80%" class="small cellText"><strong>{$ADVDOMAIN}&nbsp;</strong></td>
                          </tr>
                          <tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_SMS_ACCOUNT}</strong></td>
                            <td class="small cellText">{$ADVACCOUNT}&nbsp;</td>
                          </tr>
                          <tr>
                          <tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SMS_PREFIX}</strong></td>
                            <td width="80%" class="small cellText"><strong>{$ADVPREFIX}&nbsp;</strong></td>
                          </tr>					
                          <tr>                          
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_SMS_NAME}</strong></td>
                            <td class="small cellText">{$ADVNAME}&nbsp;</td>
                          </tr>
                        </table>       
			  {else}
		<table width="100%"  border="0" cellspacing="0" cellpadding="5">
                            <td width="20%" nowrap class="small cellLabel"><font color="red">*</font>&nbsp;<strong>{$MOD.LBL_SMS_DOMAIN}</strong></td>
                            <td width="80%" class="small cellText">
								<div class="dvtCellInfo">
									<input type="text" class="detailedViewTextBox small" value="{$ADVDOMAIN}" name="adv_domain">
								</div>
			    			</td>
                          </tr>
                          <tr>
                            <td nowrap class="small cellLabel"><font color="red">*</font>&nbsp;<strong>{$MOD.LBL_SMS_ACCOUNT}</strong></td>
                            <td class="small cellText">
                            	<div class="dvtCellInfo">
									<input type="text" class="detailedViewTextBox small" value="{$ADVACCOUNT}" name="adv_account">
								</div>
			    			</td>
                          </tr>
                        <tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SMS_PREFIX}</strong></td>
                            <td width="80%" class="small cellText">
                            	<div class="dvtCellInfo">
                            		<input type="text" class="detailedViewTextBox small" value="{$ADVPREFIX}" name="adv_prefix">
                            	</div>
			    			</td>
                          </tr>
                        <tr>                          
                          <tr>
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_SMS_NAME}</strong></td>
                            <td class="small cellText">
								<div class="dvtCellInfo">
									<input type="text" class="detailedViewTextBox small" value="{$ADVNAME}" name="adv_name">
								</div>
			  				</td>
                          </tr>
                        </table>			
						
						</td>
					  </tr>
					</table>
				{/if}	
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
function validate_sms_server(form)
{
	if(form.server.value =='')
	{
		{/literal}
                alert("{$APP.SERVERNAME_CANNOT_BE_EMPTY}")
                        return false;
                {literal}
	}
	return true;
}
</script>
{/literal}