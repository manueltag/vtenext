{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@97566 *}
{include file="Settings/ProcessMaker/Metadata/Header.tpl"}
<div style="padding:5px;">
	<form class="form-config-shape" shape-id="{$ID}">
		<table border=0 align="center">
			<tr>
				<td>{$START_LABEL} </td>
				{foreach name="timerOptions" item=val_arr from=$TIMEROPTIONS}
					<td>
						{assign var=start value="$val_arr[0]"}
						{assign var=end value="$val_arr[1]"}
						{assign var=sendname value="$val_arr[2]"}
						{assign var=disp_text value="$val_arr[3]"}
						{assign var=sel_val value="$val_arr[4]"}
						<select name="{$sendname}" class="detailedViewTextBox">
						{section name=reminder start=$start max=$end loop=$end step=1 }
							{if $smarty.section.reminder.index eq $sel_val}
								{assign var=sel_value value="SELECTED"}
							{else}
								{assign var=sel_value value=""}
							{/if}
							<OPTION VALUE="{$smarty.section.reminder.index}" {$sel_value}>{$smarty.section.reminder.index}</OPTION>
						{/section}
						</select>
					</td>
					<td>{$disp_text}{if $smarty.foreach.timerOptions.last} {$END_LABEL} <b>{$NEXT_ELEMENT}</b>{/if}</td>
				{/foreach}
			</tr>
		</table>
	</form>
</div>