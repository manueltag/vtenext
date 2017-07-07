{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@119414 *}

{literal}
	<script type="text/javascript">
		function toggleTodoPeriod1(id) {
			jQuery('#' + id).toggle();
			var open = jQuery('#' + id).is(':visible');
			var materialIcon = open ? 'keyboard_arrow_down' : 'keyboard_arrow_right';
			jQuery('#' + id + '_img').html(materialIcon);
		}
		/*function get_more_todos() {
			jQuery.ajax({
				url: 'index.php?module=SDK&action=SDKAjax&file=src/Todos/GetTodosList&mode=all&fastMode=true',
				type: 'POST',
				success: function(res) {
					jQuery('#ajaxCont').html(res);
				}
			});
		}*/
		function todoShowByDate() {
			jQuery('#todo_btn_date').parent().removeClass('dvtUnSelectedCell').addClass('dvtSelectedCell');
			jQuery('#todo_btn_duration').parent().removeClass('dvtSelectedCell').addClass('dvtUnSelectedCell');
			jQuery('#todos_list').show();
			jQuery('#todos_list_duration').hide();
		}

		function todoShowByDuration() {
			jQuery('#todo_btn_date').parent().removeClass('dvtSelectedCell').addClass('dvtUnSelectedCell');
			jQuery('#todo_btn_duration').parent().removeClass('dvtUnSelectedCell').addClass('dvtSelectedCell');
			jQuery('#todos_list').hide();
			jQuery('#todos_list_duration').show();
		}
	</script>
	
	<style>
		#todos_list, #todos_list_duration {
			position: relative;
			height: calc(100% - 150px);
		}
	</style>
{/literal}

<table class="table">
	<tr>
		<td class="fastPanelTitle">
			<h4>{'Todos'|getTranslatedString:'ModComments'}</h4>
		</td>
		<td align="right" style="vertical-align:middle;border-top:0px none;background-color:#FFF;cursor:default">
			{include file="LoadingIndicator.tpl" LIID="indicatorTodos" LIEXTRASTYLE="display:none;"}&nbsp;
			<!-- <input id="todos_button" type="button" value="{'LBL_ALL'|getTranslatedString}" name="button" class="crmbutton small edit" title="{'LBL_ALL'|getTranslatedString}" onClick="get_more_todos();"> -->
			<input type="button" value="{'LBL_CREATE'|getTranslatedString}" name="button" class="crmbutton small create" title="{'LBL_CREATE'|getTranslatedString}" onClick="fninvsh('todos');NewQCreate('Calendar');">
		</td>
	</tr>
	<tr>
		<td width="50%" align="center" class="dvtSelectedCell" onclick="todoShowByDate()">
			<a href="javascript:;" id="todo_btn_date">
				{"TodoByDate"|getTranslatedString|capitalize}
			</a>
		</td>
		<td width="50%" align="center" class="dvtUnSelectedCell" onclick="todoShowByDuration()">
			<a href="javascript:;" id="todo_btn_duration">
				{"TodoByDuration"|getTranslatedString|capitalize}
			</a>
		</td>
	</tr>
</table>

<div id="todos_list">
{if count($TODOLIST_DATE) > 0}
<table class="table table-hover">
	{foreach item=todoperiod key=timestampAgo from=$TODOLIST_DATE name="todo"}
		{counter assign=rowid}
		{assign var=rowidstr value="todos_list_tbody_$rowid"}
		{assign var=period_count value=$todoperiod|@count}
		
		{if $period_count >= $TODOLIST_TODOSINPERIOD}
			{assign var=hidenext value=true}
			<tr id="{$rowidstr}_toggle">
				<td colspan="3" onclick="toggleTodoPeriod1('{$rowidstr}');" style="cursor:pointer">
					<div class="inlineBlockMiddle">
						<i id="{$rowidstr}_img" class="vteicon">keyboard_arrow_right</i>
					</div><!-- 
				 	 --><div class="inlineBlockMiddle">&nbsp;{$timestampAgo} ({$period_count})</div>
				</td>
			</tr>
		{else}
			{assign var=hidenext value=false}
		{/if}
		
		<tbody id="{$rowidstr}" style="display:{if $hidenext eq true}none{else}table-row-group{/if}">
			{foreach item=todorow from=$todoperiod}
				<tr id="todos_list_row_{$todorow.activityid}">
					<td width="10%" align="center">
						<div class="checkbox">
							<label for="todo_{$todorow.activityid}">
								<input type="checkbox" id="todo_{$todorow.activityid}" onClick="closeTodo({$todorow.activityid},this.checked);" title="{'LBL_COMPLETED'|getTranslatedString:'Calendar'}" style="cursor: pointer;" />
							</label>
						</div>
					</td>
					<td width="80%" class="{if $todorow.unseen}ModCommUnseen{/if}">
						<a href="index.php?module=Calendar&action=DetailView&record={$todorow.activityid}">{$todorow.subject}</a>
						<br />{$todorow.expired_str} <a href="javascript:;" class="" style="color: gray; text-decoration:none;" title="{$todorow.timestamp}">{$todorow.timestamp_ago}</span>
					</td>
					<td width="10%" class="{if $todorow.unseen}ModCommUnseen{/if}">{$todorow.description}</td>
				</tr>
			{/foreach}
		</tbody>
	{/foreach}
</table>
{else}
	<div class="fastEmptyMask">
		<div class="fastEmptyMaskInner">
			<div class="smallCircle fastMaskIcon">
				<i class="vteicon nohover">assignment_turned_in</i>
			</div>
			<span class="fastMaskText">
				{"LBL_NO_TODOS"|getTranslatedString}
			</span>
		</div>
	</div>
{/if}
</div>

<div id="todos_list_duration" style="display:none">
{if count($TODOLIST_DURATION) > 0}
<table class="table table-hover">
	{foreach item=todoperiod key=duration from=$TODOLIST_DURATION}
		{counter assign=rowid}
		{assign var=rowidstr value="todos_list_tbody_$rowid"}
		{assign var=period_count value=$todoperiod|@count}
		
		{if $period_count >= 2}
			{assign var=hidenext value=true}
		{else}
			{assign var=hidenext value=false}
		{/if}
		
		<tr id="{$rowidstr}_toggle">
			<td colspan="3" onclick="toggleTodoPeriod1('{$rowidstr}');" style="cursor:pointer">
				<div class="inlineBlockMiddle">
					<i id="{$rowidstr}_img" class="vteicon">{if $hidenext}keyboard_arrow_right{else}keyboard_arrow_down{/if}</i>
				</div><!-- 
			 	 --><div class="inlineBlockMiddle">&nbsp;{$timestampAgo} ({$period_count})</div>
			</td>
		</tr>

		<tbody id="{$rowidstr}" style="display:{if $hidenext eq true}none{else}table-row-group{/if}">
		{foreach item=todorow from=$todoperiod}
			<tr id="todos2_list_row_{$todorow.activityid}">
				<td width="10%" align="center">
					<div class="checkbox">
						<label for="todo2_{$todorow.activityid}">
							<input type="checkbox" id="todo2_{$todorow.activityid}" onClick="closeTodo({$todorow.activityid},this.checked);" title="{'LBL_COMPLETED'|getTranslatedString:'Calendar'}" style="cursor: pointer;" />
						</label>
					</div>
				</td>
				<td width="80%" class="{if $todorow.unseen}ModCommUnseen{/if}">
					<a href="index.php?module=Calendar&action=DetailView&record={$todorow.activityid}">{$todorow.subject}</a>
					<br />{$todorow.expired_str} <a href="javascript:;" class="" style="color: gray; text-decoration:none;" title="{$todorow.timestamp}">{$todorow.timestamp_ago}</span>
				</td>
				<td width="10%" class="{if $todorow.unseen}ModCommUnseen{/if}">{$todorow.description}</td>
			</tr>
		{/foreach}
		</tbody>
	{/foreach}
</table>
{else}
	<div class="fastEmptyMask">
		<div class="fastEmptyMaskInner">
			<div class="smallCircle fastMaskIcon">
				<i class="vteicon nohover">assignment_turned_in</i>
			</div>
			<span class="fastMaskText">
				{"LBL_NO_TODOS"|getTranslatedString}
			</span>
		</div>
	</div>
{/if}
</div>
