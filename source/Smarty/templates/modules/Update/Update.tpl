{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}

<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/menu.js"|resourcever}"></script>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody><tr>
	<td class="showPanelBg" style="padding: 5px;" valign="top" width="100%">

	<form action="index.php" method="post" name="Update" id="form" onsubmit="VtigerJS_DialogBox.block();">

	<input type="hidden" name="module" value="Update">
	<input type="hidden" name="action">
	<input type="hidden" name="parenttab" value="Settings">
	
	<input type="hidden" name="change_login" value="">
	<input type="hidden" name="server" value="{$MAILSERVER}">
	<input type="hidden" name="server_username" value="{$USERNAME}">
	<input type="hidden" name="server_password" value="{$PASSWORD}">
	<input type="hidden" name="max_version" value="{$MAX_VERSION}">
	
	<div align=center>
			
		{include file="SetMenu.tpl"}

		<!-- DISPLAY -->
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
		<tr>
			{* <td width=50 rowspan=2 valign=top><img src="{$IMAGE_PATH}workflow.gif" alt="{$MOD.LBL_UPDATE}" width="48" height="48" border=0 title="{$MOD.LBL_UPDATE}"></td> *}
			<td class=heading2 valign=bottom><b>{$SMOD.LBL_SETTINGS}</a> > {$MOD.LBL_UPDATE} </b></td> <!-- crmv@30683 -->
		</tr>
		<tr>
			<td valign=top class="small">{$MOD.LBL_UPDATE_DESC} </td>
		</tr>
		</table>
		
		<br>
		<table border=0 cellspacing=0 cellpadding=10 width=100% >
		<tr>
		<td>
		
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
			{*
			<tr>
				<td class="big"><strong>{$MOD.LBL_SIGN_IN_DETAILS}</strong></td>
				<td class="small" align=right>
					<input title="{$MOD.LBL_SIGN_IN_CHANGE}" accessKey="{$MOD.LBL_SIGN_IN_CHANGE}" class="crmButton small save" onclick="this.form.change_login.value='true';this.form.action.value='Login';" type="submit" name="button" value="{$MOD.LBL_SIGN_IN_CHANGE}" >&nbsp;&nbsp;
				</td>
			</tr>
			*}
			{if $ERROR_MSG neq ''}
			<tr>
				{$ERROR_MSG}
			</tr>
			{/if}
			</table>
					
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
			<tr>
			<td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
				{*
            	<tr>
					<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_URL}</strong></td>
					<td width="80%" class="small cellText">{$MAILSERVER}</td>
				</tr>
				<tr valign="top">
					<td nowrap class="small cellLabel"><strong>{$MOD.LBL_USERNAME}</strong></td>
					<td class="small cellText">{$USERNAME}</td>
				</tr>
				<tr>
					<td nowrap class="small cellLabel"><strong>{$MOD.LBL_PASWRD}</strong></td>
					<td class="small cellText">******</td>
				</tr>
				*}
				<tr>
					<td width="20%" nowrap class="small cellLabel">{$MOD.LBL_CURRENT_VERSION}</td>
					<td width="80%">
						<input type="text" class="dvtCellInfo" style="width:10%;" value="{$CURRENT_VERSION}" name="current_version">
					</td>
				</tr>
				<!-- TODO -->
				{*
				<tr>
					<td nowrap class="small cellLabel"><strong>{$MOD.LBL_MAX_VERSION}</strong></td>
					<td class="small cellText">{$MAX_VERSION}</td>
				</tr>
				*}
				<!-- TODO e -->
				</table>
			</td>
			</tr>
			</table>
			{*
			<br />
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
			<tr>
				<td class="big"><strong>{$MOD.LBL_UPDATE_DETAILS}</strong></td>
				<td class="small" align=right></td>
			</tr>
			</table>
			*}
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
			<tr>
			<td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
            	<tr>
					<td width="20%" nowrap class="small cellLabel">{$MOD.LBL_UPDATE_TO}</td>
					<td width="80%" class="small cellText">
					<!-- TODO : settare come default la versione max e poi fare controlli seul campo specificied_version (deve essere numerico, <= versione max e >= versione corrente) -->
					{*
						<input type="radio" value="max_version" name="type_update" onclick="change_type_update(this);" checked> {$MOD.LBL_MAX_VERSION}
						<br />
						<input type="radio" value="specific_version" name="type_update" onclick="change_type_update(this);" checked> {$MOD.LBL_SPECIFIC_VERSION}
						<br />
					*}
						<input type="hidden" value="specific_version" name="type_update">
						<input type="text" class="dvtCellInfo" style="width:10%;" value="" name="specificied_version" id="specificied_version">
					<!-- TODO e -->
				    </td>
				</tr>
				</table>
			</td>
			</tr>
			</table>
			
			<table border=0 cellspacing=0 cellpadding=5 width=100%>
			<tr>
				<td class="small" align=right>
					<input title="{$MOD.LBL_UPDATE_BUTTON}" accessKey="{$MOD.LBL_UPDATE_BUTTON}" class="crmButton small save" onclick="this.form.action.value='DoUpdate';" type="submit" name="button" value="{$MOD.LBL_UPDATE_BUTTON}">&nbsp;&nbsp;
				</td>
			</tr>
			</table>
			
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
</tr>
</tbody>
</table>
{literal}
<script>
function change_type_update(obj)
{
	if(obj.value == 'specific_version')
		getObj('specificied_version').style.display = 'block';
	else
		getObj('specificied_version').style.display = 'none';
}
</script>
{/literal}