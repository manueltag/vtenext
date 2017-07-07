{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
 
{* crmv@119414 *}
{assign var=overrides value=$THEME_CONFIG.tpl_overrides}
{if !empty($overrides[$smarty.template])}
	{include file=$overrides[$smarty.template]}
	{php}return;{/php}
{/if}
{* crmv@119414e *}
 
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
{if $HIDE_CUSTOM_LINKS neq '1'}
	<div class="drop_mnu" id="customLinks" onmouseover="fnShowDrop('customLinks');" onmouseout="fnHideDrop('customLinks');" style="width:150px;">
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
			{* crmv@22259 *}
			{if $ALL eq 'All'}
				<tr>
					<td><a class="drop_down" href="index.php?module={$MODULE}&action=CustomView&duplicate=true&record={$VIEWID}&parenttab={$CATEGORY}&return_action=index">{$APP.LNK_CV_DUPLICATE}</a></td>
				</tr>
				<tr>
					<td><a class="drop_down" href="index.php?module={$MODULE}&action=CustomView&parenttab={$CATEGORY}&return_action=index">{$APP.LNK_CV_CREATEVIEW}</a></td>
				</tr>
		    {else}
				{if $CV_EDIT_PERMIT eq 'yes'}
					<tr>
						<td><a class="drop_down" href="index.php?module={$MODULE}&action=CustomView&record={$VIEWID}&parenttab={$CATEGORY}&return_action=index">{$APP.LNK_CV_EDIT}</a></td>
					</tr>
				{/if}
				<tr>
					<td><a class="drop_down" href="index.php?module={$MODULE}&action=CustomView&duplicate=true&record={$VIEWID}&parenttab={$CATEGORY}&return_action=index">{$APP.LNK_CV_DUPLICATE}</a></td>
				</tr>
				{if $CV_DELETE_PERMIT eq 'yes'}
					<tr>
						<td><a class="drop_down" href="javascript:confirmdelete('index.php?module=CustomView&action=Delete&dmodule={$MODULE}&record={$VIEWID}&parenttab={$CATEGORY}&return_action=index')">{$APP.LNK_CV_DELETE}</a></td>
					</tr>
				{/if}
				{if $CUSTOMVIEW_PERMISSION.ChangedStatus neq '' && $CUSTOMVIEW_PERMISSION.Label neq ''}
					<tr>
				   		<td><a class="drop_down" href="#" id="customstatus_id" onClick="ChangeCustomViewStatus({$VIEWID},{$CUSTOMVIEW_PERMISSION.Status},{$CUSTOMVIEW_PERMISSION.ChangedStatus},'{$MODULE}','{$CATEGORY}')">{$CUSTOMVIEW_PERMISSION.Label}</a></td>
				   	</tr>
				{/if}
				<tr>
					<td><a class="drop_down" href="index.php?module={$MODULE}&action=CustomView&parenttab={$CATEGORY}&return_action=index">{$APP.LNK_CV_CREATEVIEW}</a></td>
				</tr>
		    {/if}
		    {* crmv@22259e *}
		</table>
	</div>
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
