{*
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
   *
 ********************************************************************************/
*}
{* crmv@9010 *}
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@30683 -->
<tbody><tr>
        <td valign="top"></td>
        <td class="showPanelBg" style="padding: 5px;" valign="top" width="100%"> <!-- crmv@30683 -->
	<div align=center>
			{include file="SetMenu.tpl"}
			{include file='Buttons_List.tpl'} {* crmv@30683 *} 
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<form action="index.php" method="post" name="tandc">
				<input type="hidden" name="server_type" value="ldap">
				<input type="hidden" name="module" value="Settings">
				<input type="hidden" name="action" value="index">
				<input type="hidden" name="delete" value="0">
				<input type="hidden" name="ldap_server_mode">
				<input type="hidden" name="parenttab" value="Settings">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{$IMAGE_PATH}ldap.gif" alt="{$MOD.LBL_LDAP}" width="48" height="48" border=0 title="{$MOD.LBL_LDAP}"></td>
					<td class=heading2 valign=bottom><b> {$MOD.LBL_SETTINGS} > {$MOD.LBL_LDAP_SERVER_SETTINGS} </b></td> <!-- crmv@30683 -->
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_LDAP_SERVER_DESC} </td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$MOD.LBL_LDAP_SERVER_SETTINGS}<br>{$ERROR_MSG}</strong></td>
						{if $LDAP_SERVER_MODE neq 'edit'}
							<td class="small" align=right>
								<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmButton small edit" onclick="this.form.action.value='LdapConfig';this.form.ldap_server_mode.value='edit'" type="submit" name="Edit" value="{$APP.LBL_EDIT_BUTTON_LABEL}">
							</td>
						{else}
							<td class="small" align=right>
								<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="this.form.action.value='SaveLdap'; return validate()">&nbsp;&nbsp;
							    <input title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmButton small cancel" onclick="javascript:document.location.href='index.php?module=Settings&action=LdapConfig&parenttab=Settings'" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
							</td>
						{/if}
					</tr>
					</table>
				
			{if $LDAP_SERVER_MODE eq 'edit'}	
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
			    <tr>
			    	<td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
	                </td>
                </tr>
			    <tr>
				<tr valign="top">
					<td nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_ACTIVE}</strong></td>
					<td class="small cellText">
						<input type="checkbox" value="1" name="ldap_active" id="ldap_active" {if ($LDAPACTIVE eq 1)} checked {/if}>
					</td>
				</tr>			    
				<tr valign="top">
					<td nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_LDAP_SERVER_ADDRESS} </strong></td>
					<td width="80%" class="small cellText">
						<div class="dvtCellInfo">
							{if $smarty.request.ldap_host neq ''}
								<input type="text" class="detailedViewTextBox small" value="{$smarty.request.ldap_host}" name="ldap_host" id="ldap_host">
							{else}
								<input type="text" class="detailedViewTextBox small" value="{$LDAPHOST}" name="ldap_host" id="ldap_host">
							{/if}
						</div>
						{$MOD.LDAP_EXAMPLE_SERVER_ADDRESS} {* esempi *}
			    	</td>
				</tr>
				<tr valign="top">
	                <td width="20%" nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_LDAP_PORT} </strong></td>
	                <td width="80%" class="small cellText">
	                	<div class="dvtCellInfo">
							{if $smarty.request.ldap_port neq ''}
								<input type="text" class="detailedViewTextBox small" value="{$smarty.request.ldap_port}" name="ldap_port" id="ldap_port">
							{else}
								<input type="text" class="detailedViewTextBox small" value="{$LDAPPORT}" name="ldap_port" id="ldap_port">
							{/if}
						</div>
						{$MOD.LDAP_EXAMPLE_PORT} {* esempi *}
					</td>
				</tr>
			    <tr valign="top">
	                <td width="20%" nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_LDAP_LDAPBSEDN} </strong></td>
	                <td width="80%" class="small cellText">
	                	<div class="dvtCellInfo">
							{if $smarty.request.ldap_basedn neq ''}
								<input type="text" class="detailedViewTextBox small" value="{$smarty.request.ldap_basedn}" name="ldap_basedn" id="ldap_basedn">
							{else}
		                    	<input type="text" class="detailedViewTextBox small" value="{$LDAPBSEDN}" name="ldap_basedn" id="ldap_basedn">
							{/if}
						</div>
						{$MOD.LDAP_EXAMPLE_LDAPBSEDN} {* esempi *}
					</td>
				</tr>
                <tr valign="top">
					<td nowrap class="small cellLabel"><strong>{$MOD.LBL_USERNAME}</strong></td>
					<td class="small cellText">
						<div class="dvtCellInfo">
							{if $smarty.request.ldap_username neq ''}
								<input type="text" class="detailedViewTextBox small" value="{$smarty.request.ldap_username}" name="ldap_username" id="ldap_username">
							{else}
								<input type="text" class="detailedViewTextBox small" value="{$LDAPSUSER}" name="ldap_username" id="ldap_username">
							{/if}
						</div>
						{$MOD.LDAP_EXAMPLE_USERNAME} {* esempi *}
			    	</td>
				</tr>
				<tr valign="top">
					<td nowrap class="small cellLabel"><strong>{$MOD.LBL_PASWRD}</strong></td>
					<td class="small cellText">
						<div class="dvtCellInfo">
							{* crmv@43764 *}
							<input type="password" value="{if !empty($LDAPSPASSWORD)}********{/if}" class="detailedViewTextBox small" onFocus="this.value='';" onChange="document.getElementById('ldap_pass').value=this.value;" />
							<input type="hidden" id="ldap_pass" name="ldap_pass" value="">
							{* crmv@43764e *}
						</div>
			    	</td>
				</tr>
				<tr valign="top">
                	<td width="20%" nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_LDAP_OBJCLASS} </strong></td>
                	<td width="80%" class="small cellText">
                		<div class="dvtCellInfo">
							{if $smarty.request.ldap_objclass neq ''}
								<input type="text" class="detailedViewTextBox small" value="{$smarty.request.ldap_objclass}" name="ldap_objclass" id="ldap_objclass">
							{else}
								<input type="text" class="detailedViewTextBox small" value="{$LDAPOBJCLASS}" name="ldap_objclass" id="ldap_objclass">
							{/if}
						</div>
						{$MOD.LDAP_EXAMPLE_OBJCLASS} {* esempi *}
			    	</td>
				</tr>
				<tr valign="top">
					<td width="20%" nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_LDAP_LDAPACCOUNT} </strong></td>
					<td width="80%" class="small cellText">
						<div class="dvtCellInfo">
							{if $smarty.request.ldap_account neq ''}
								<input type="text" class="detailedViewTextBox small" value="{$smarty.request.ldap_account}" name="ldap_account" id="ldap_account">
							{else}
								<input type="text" class="detailedViewTextBox small" value="{$LDAPACCOUNT}" name="ldap_account" id="ldap_account">
							{/if}
						</div>
						{$MOD.LDAP_EXAMPLE_LDAPACCOUNT} {* esempi *}
			    	</td>
				</tr>
				<tr valign="top">
					<td width="20%" nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_LDAP_LDAPFULLNAME} </strong></td>
					<td width="80%" class="small cellText">
						<div class="dvtCellInfo">
							{if $smarty.request.ldap_fullname neq ''}
							<input type="text" class="detailedViewTextBox small" value="{$smarty.request.ldap_fullname}" name="ldap_fullname" id="ldap_fullname">
							{else}
							<input type="text" class="detailedViewTextBox small" value="{$LDAPFULLNAME}" name="ldap_fullname" id="ldap_fullname">
							{/if}
						</div>
						{$MOD.LDAP_EXAMPLE_LDAPFULLNAME} {* esempi *}
		    		</td>
				</tr>
				<tr valign="top">
					<td width="20%" nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_LDAP_LDAPFILTER} </strong></td>
					<td width="80%" class="small cellText">
						<div class="dvtCellInfo">
							{if $smarty.request.ldap_userfilter neq ''}
							<input type="text" class="detailedViewTextBox small" value="{$smarty.request.ldap_userfilter}" name="ldap_userfilter" id="ldap_userfilter">
							{else}
							<input type="text" class="detailedViewTextBox small" value="{$LDAPFILTER}" name="ldap_userfilter" id="ldap_userfilter">
							{/if}
						</div>
						{$MOD.LDAP_EXAMPLE_LDAPFILTER} {* esempi *}
			    	</td>
				</tr>
				<tr valign="top">
                	<td width="20%" nowrap class="small cellLabel" align=left><font color="red">*</font><strong>{$MOD.LBL_LDAP_LDAPROLE}</strong></td>
                	<td width="80%" align=left>
						<div class="dvtCellInfoOff" style="position:relative">
							<input name="role_name" id="role_name" readonly class="detailedViewTextBox" tabindex="{$vt_tab}" value="{$secondvalue}" type="text">&nbsp;
							<input name="user_role" id="user_role" value="{if $smarty.request.role neq ''}{$smarty.request.roleid}{else}{$roleid}{/if}" type="hidden">
							<div class="dvtCellInfoImgRx">
								<a href="javascript:open_Popup();"><i class="vteicon">view_list</i></a>{* crmv@21048m *}
							</div>
						</div>
					{$MOD.LDAP_EXAMPLE_LDAPROLE} {* esempi *}
					</td>
				</tr>
				</table>
			{else}
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
			<tr>
	         	    <td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
						<tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_ACTIVE}</strong></td>
                            <td class="small cellText">
								{if ($LDAPACTIVE eq 1)}
									{$MOD.LBL_YES}
								{else} 
									{$MOD.LBL_NO}
								{/if}
			    			</td>
                        </tr>
                        <tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_LDAP_SERVER_ADDRESS} </strong></td>
                            <td width="80%" class="small cellText"><strong>{$LDAPHOST}&nbsp;</strong></td>
                        </tr>
						<tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_LDAP_PORT}</strong></td>
                            <td class="small cellText">{$LDAPPORT}&nbsp;</td>
                        </tr>
                        <tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_LDAP_LDAPBSEDN}</strong></td>
                            <td class="small cellText">{$LDAPBSEDN}&nbsp;</td>
                        </tr>
                        <tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_USERNAME}</strong></td>
                            <td class="small cellText">{$LDAPSUSER}&nbsp;</td>
                        </tr>
                        <tr>
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_PASWRD}</strong></td>
                            <td class="small cellText">
							{if $LDAPSPASSWORD neq ''}
							******
							{/if}&nbsp;
						</tr>
						<tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_LDAP_OBJCLASS}</strong></td>
                            <td class="small cellText">{$LDAPOBJCLASS}&nbsp;</td>
                        </tr>
                        <tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_LDAP_LDAPACCOUNT}</strong></td>
                            <td class="small cellText">{$LDAPACCOUNT}&nbsp;</td>
                        </tr>
                        <tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_LDAP_LDAPFULLNAME}</strong></td>
                            <td class="small cellText">{$LDAPFULLNAME}&nbsp;</td>
                        </tr>
                        <tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_LDAP_LDAPFILTER}</strong></td>
                            <td class="small cellText">{$LDAPFILTER}&nbsp;</td>
                        </tr>
						<tr valign="top">
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_LDAP_LDAPROLE}</strong></td>
                            <td class="small cellText">{$secondvalue}&nbsp;</td>
                        </tr>                      
                        </table>
					
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
	</form>
	</table>
		
	</div>
</td>
        <td valign="top"></td>
   </tr>
</tbody>
</table>
{literal}
<script>
function validate() {
	if (!emptyCheck("ldap_host","LDAP Server Name","text")) return false
	if (!emptyCheck("ldap_port","Port Number","text")) return false
	if (!emptyCheck("ldap_basedn","BaseDn","text")) return false
	if (!emptyCheck("ldap_objclass","Objclass","text")) return false
	if (!emptyCheck("ldap_account","User Account","text")) return false
	if(isNaN(document.tandc.ldap_port.value)){
		alert(alert_arr.LBL_ENTER_VALID_PORT);
		return false;
	}
			return true;
}
//crmv@21048m
function open_Popup(){
	openPopup("index.php?module=Users&action=UsersAjax&file=RolePopup&parenttab=Settings","roles_popup_window","height=425,width=640,toolbar=no,menubar=no,dependent=yes,resizable =no",'',640,425);
}
//crmv@21048me
</script>
{/literal}
<!--crmv@9010e-->