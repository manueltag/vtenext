{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@35153 *}
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody><tr>
<td valign="top"></td>
<td class="showPanelBg" style="padding: 5px;" valign="top" width="100%">
<form action="index.php" method="post" id="form" onsubmit="VtigerJS_DialogBox.block();">
<input type='hidden' name='module' value='Users'>
<input type='hidden' name='action' value='DefModuleView'>
<input type='hidden' name='return_action' value='ListView'>
<input type='hidden' name='return_module' value='Users'>
<input type='hidden' name='parenttab' value='Settings'>

	<div align=center>
			{include file='SetMenu.tpl'}
			{include file='Buttons_List.tpl'}
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'PrivacySettings.png'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_PRIVACY}" width="48" height="48" border=0 title="{$MOD.LBL_PRIVACY}"></td>
					<td class=heading2 valign=bottom><b> {$MOD.LBL_SETTINGS} > {$MOD.LBL_PRIVACY}</b></td>
				</tr>
				<tr>
					<td valign=top class="small">&nbsp;</td>
				</tr>
				</table>
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
					<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
					<tr>
		         	    <td class="small" valign=top>
		         	    	{$MOD.LBL_PRIVACY_DESC}
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
</td>
<td valign="top"></td>
</tr>
</tbody>
</form>
</table>
{* crmv@35153e *}