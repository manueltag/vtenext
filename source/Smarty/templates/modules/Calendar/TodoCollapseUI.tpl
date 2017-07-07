{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@98866 *}

<div class="col-sm-12 nopadding">
	<ul id="todo-options" data-content="#todo-options-content" class="nav nav-tabs col-sm-12 nopadding">
		<li class="col-xs-3 nopadding active" style="text-align: center">
			<a data-toggle="tab" href="#addTodoRelatedtoUICont">
				<i class="vteicon avatar">view_list</i>
				<br>
				{$MOD.LBL_RELATEDTO}
			</a>
		</li>
	</ul>
</div>

<div class="col-sm-12 nopadding">
	<div id="todo-options-content" class="tab-content" style="padding: 15px">
		<div id="addTodoRelatedtoUICont" class="tab-pane fade in active">
			<div id="addTaskRelatedtoUI" class="calendar-widget" style="width: 100%">
				{if empty($MODE) || $MODE eq 'edit'} 
					{include file="modules/Calendar/TodoRelatedToUI.tpl"} 
				{else} 
					{include file="modules/Calendar/TodoRelatedToUIReadOnly.tpl"} 
				{/if}
			</div>
		</div>
	</div>
</div>
