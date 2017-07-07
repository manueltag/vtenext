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
<form id="UnifiedSearch_moduleform" name="UnifiedSearch_moduleform">
	<table border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr height="34">
			<td style="padding:5px" class="level3Bg">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="80%"><b>{$APP.LBL_SELECT_MODULES_FOR_SEARCH}</b></td>
					<td width="20%" align="right">
						<input type='button' class='crmbutton small create' value='{$APP.LBL_APPLY_BUTTON_LABEL}' onclick='UnifiedSearch_SelectModuleSave();'>
					</td>
				</tr>
				</table>
			</td>
		</tr>
	</table>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="mailClient mailClientBg">
	<tr>
		<td>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">
			<tr>
				<td align=right background="{'qcBg.gif'|@vtiger_imageurl:$THEME}" class="mailSubHeader" width="100%">
					<a href='javascript:void(0);' onclick="UnifiedSearch_SelectModuleToggle(true);">{$APP.LBL_SELECT_ALL}</a> |
					<a href='javascript:void(0);' onclick="UnifiedSearch_SelectModuleToggle(false);">{$APP.LBL_UNSELECT_ALL}</a>					
				</td>
			</tr>
			</table>
			<table width="100%" cellspacing="0" cellpadding="5" border="0" class="small">
				{foreach item=SEARCH_MODULEINFO key=SEARCH_MODULENAME from=$ALLOWED_MODULES name=allowed_modulesloop}
				{if $smarty.foreach.allowed_modulesloop.index % 3 == 0}
				<tr valign=top>	
				{/if}
					<td class="dvtCellLabel"><input type='checkbox' name='search_onlyin' class='small' value='{$SEARCH_MODULENAME}'
					{if $SEARCH_MODULEINFO.selected}checked=true{/if}>{$SEARCH_MODULEINFO.label}</td>
				{if $smarty.foreach.allowed_modulesloop.index % 3 == 2}
				</tr>
				{/if}
				{/foreach}
			</table>
		</td>
	</tr>
	</table>
	<div class="closebutton" onClick="UnifiedSearch_SelectModuleCancel();"></div>
</form>