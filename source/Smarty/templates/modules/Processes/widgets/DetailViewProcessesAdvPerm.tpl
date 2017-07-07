{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@100731 *}
{foreach item=RESOURCE from=$RESOURCES}
	<span id="ModCommentsUsers_list_{$RESOURCE.id}" class="addrBubble" style="cursor:pointer" title="{$RESOURCE.alt}">
		<table cellpadding="3" cellspacing="0" class="small">
		<tr>
			<td rowspan="2"><img src="{$RESOURCE.img}" class="userAvatar" /></td>
			<td>{$RESOURCE.fullname}</td>
		</tr>
		<tr>
			<td>{$RESOURCE.name}</td>
		</tr>
		</table>
	</span>
{/foreach}