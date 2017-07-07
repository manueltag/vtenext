{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@OPER6288 crmv@102334 *}
{if $smarty.request.ajax neq ''}
&#&#&#{$ERROR}&#&#&#
{/if}
{if $KANBAN_NOT_AVAILABLE}
	{$APP.LBL_KANBAN_NOT_AVAILABLE}
{else}
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="level3Bg" align="center" id="kanban_grid_h">
		<tr bgcolor="#FFFFFF" class="small" valign="top">
			{foreach name=kanban_foreach item=KANBAN_COL from=$KANBAN_ARR}
				{math equation=x/y x=100 y=$smarty.foreach.kanban_foreach.total+1 format=%d assign=width}
				<td width="{$width}%" style="padding:5px">{$KANBAN_COL.label}</td>
			{/foreach}
			<td width="{$width}%" id="previewContainer_Summary_h" style="padding:5px; display:none"></td>
		</tr>
	</table>
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="small" align="center" id="kanban_grid_b">
		<tr bgcolor="#FFFFFF" valign="top">
			{foreach name=kanban_foreach key=KANBAN_ID item=KANBAN_COL from=$KANBAN_ARR}
				{math equation=x/y x=100 y=$smarty.foreach.kanban_foreach.total+1 format=%d assign=width}
				<td width="{$width}%">
					<ul id="{$KANBAN_ID}" lastpageapppended="{$LAST_PAGE_APPENDED}" class="kanbanSortableList" style="list-style:none; margin:0px;">
					{include file='KanbanColumn.tpl'}
					</ul>
				</td>
			{/foreach}
			<td width="{$width}%" id="previewContainer_Summary" style="display:none"><div id="previewContainer_Summary_scroll"></div></td>
		</tr>
	</table>
{/if}
<script type="text/javascript" id="init_kanban_script">
{if $smarty.request.ajax eq 'true'}
	KanbanView.init('{$MODULE}','{$VIEWID}');
{else}
	jQuery(window).load(function(){ldelim}
		KanbanView.init('{$MODULE}','{$VIEWID}');
	{rdelim});
{/if}
</script>