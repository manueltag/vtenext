{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@104566 *}
{if $line.log.type eq 'edit'}
	<a style="text-decoration:none;" href="javascript:void(0);" onclick="ModNotificationsCommon.toggleChangeLog('{$line.crmid}');">
		<i class="vteicon" id="img_{$line.crmid}">keyboard_arrow_down</i><span style="position: relative; bottom: 7px;">{'LBL_DETAILS'|@getTranslatedString:'ModNotifications'}</span>
	</a>
	<div id="div_{$line.crmid}" style="display:block;">
		<table class="table">
			<tr>
				<td style="width: 33%;"><b>{'Field'|@getTranslatedString:'ChangeLog'}</b></td>
				<td style="width: 33%;"><b>{'Earlier value'|@getTranslatedString:'ChangeLog'}</b></td>
				<td style="width: 33%;"><b>{'Actual value'|@getTranslatedString:'ChangeLog'}</b></td>
			</tr>
			{foreach item=field from=$line.log.info}
				<tr>
					<td>{$field.fieldname_trans}</td>
					<td>{$field.previous}</td>
					<td>{$field.current}</td>
				</tr>
			{/foreach}
		</table>
	</div>
{else}
	<div style="height:28px">&nbsp;</div>
{/if}