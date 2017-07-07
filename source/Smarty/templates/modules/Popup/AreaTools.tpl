{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@54707 *}
<table cellpadding="5" cellspacing="0" border="0" width="70%" align="center">
	<tr>
		<td valign="top">-</td>
		<td>
			{'LBL_PROPAGATE_AREA'|getTranslatedString|sprintf:$link}
			<input type="button" value="{'LBL_PROPAGATE_AREA_BUTTON'|getTranslatedString|sprintf:$link}" class="crmButton small edit" onClick="ModuleAreaManager.propagateLayout();" >
		</td>
	</tr>
	<tr>
		<td valign="top">-</td>
		<td>
			<label for="block_area_layout">{'LBL_BLOCK_AREA_LAYOUT'|getTranslatedString}</label>&nbsp;<input type="checkbox" id="block_area_layout" onClick="ModuleAreaManager.blockLayout(this.checked);" {$BLOCK_AREA_LAYOUT} />
		</td>
	</tr>
</table>