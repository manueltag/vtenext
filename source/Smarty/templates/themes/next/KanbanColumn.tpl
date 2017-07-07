{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@OPER6288 *}

{assign var=ENTRIES value=$KANBAN_COL.entries}
{assign var=OTHER_INFO value=$KANBAN_COL.other_information}
{if !empty($ENTRIES)}
	{foreach key=ID item=entry from=$ENTRIES}
		<li id="{$ID}" class="kanbanSortableItem">
			{assign var=listentry1 value=""}
			{assign var=listentry2 value=""}
			{foreach name=listentry key=index item=value from=$entry}
				{if $smarty.foreach.listentry.iteration-1 eq $KANBAN_COL.user_field_position && $KANBAN_COL.user_field_position neq ''} {* crmv@115214 *}
					{assign var=smownerid value=$value}
				{elseif $smarty.foreach.listentry.iteration-1|@in_array:$KANBAN_COL.name_field_position}
					{if empty($listentry1)}
						{assign var=listentry1 value=$value}
					{else}
						{assign var=listentry1 value=$listentry1|cat:" "|cat:$value}
					{/if}
				{elseif $value neq '' && $index neq 'clv_color' && $index neq 'clv_status'} {* crmv@105538 *}
					{if empty($listentry2)}
						{assign var=listentry2 value=$value}
					{else}
						{assign var=listentry2 value=$listentry2|cat:", "|cat:$value}
					{/if}
				{/if}
			{/foreach}
			{* crmv@105538 *}
			{if $entry.clv_color}
				<div class="kanbanColorBar" style="background-color:{$entry.clv_color}" title="{$entry.clv_status}"></div>
			{/if}
			{* crmv@105538e *}
			<table class="kanbanCard" border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr valign="middle">
					<td align="left" class="kanbanCardIcon">
						<div>{$smownerid|getUserAvatarImg}</div>
					</td>
					<td align="left" class="kanbanCardTitle">
						<div class="listMessageSubject">
							<a href="javascript:;" onCliCk="KanbanView.showPreView('{$MODULE}','{$ID}')">{$listentry1|strip_tags}</a>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="kanbanCardDesc">
						<div class="gray linkNoPropagate">{$listentry2}</div>
					</td>
				</tr>
				<tr>
					<td align="right" colspan="2">
						{if $OTHER_INFO.$ID.related_count neq ''}
							<span class="badge pull-right" title="{$OTHER_INFO.$ID.related_module}">{$OTHER_INFO.$ID.related_count}</span>
						{/if}
					</td>
				</tr>
			</table>
		</li>
	{/foreach}
{else}
	{*
	<table border="0" cellspacing="0" cellpadding="5" width="100%">
		<tr><td>{$APP.LBL_NO_M} {$APP.LBL_RECORDS} {$APP.LBL_FOUND}</td></tr>
	</table>
	*}
{/if}