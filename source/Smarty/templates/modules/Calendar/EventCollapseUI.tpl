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
	<ul id="calendar-options" data-content="#calendar-options-content" class="nav nav-tabs col-sm-12 nopadding">
		<li class="col-xs-3 nopadding active" style="text-align:center">
			<a data-toggle="tab" href="#addEventRelatedtoUICont">
				<i class="vteicon avatar">view_list</i><br>
				{$MOD.LBL_RELATEDTO}
			</a>
		</li>
		<li class="col-xs-3 nopadding" style="text-align:center">
			<a data-toggle="tab" href="#addEventInviteUICont">
				<i class="vteicon">group</i><br>
				{$MOD.LBL_INVITE}
			</a>
		</li>
		<li class="col-xs-3 nopadding" style="text-align:center">
			<a data-toggle="tab" href="#addEventAlarmUICont">
				<i class="vteicon">alarm_on</i><br>
				{$MOD.LBL_REMINDER}
			</a>
		</li>
		<li class="col-xs-3 nopadding" style="text-align:center">
			<a data-toggle="tab" href="#addEventRepeatUICont">
				<i class="vteicon">repeat</i><br>
				{$MOD.LBL_REPEAT}
			</a>
		</li>
	</ul>
</div>

<div class="col-sm-12 nopadding">
	<div id="calendar-options-content" class="tab-content" style="padding:15px">
		<div id="addEventRelatedtoUICont" class="tab-pane fade in active">
			<div id="addEventRelatedtoUI" class="calendar-widget" style="width:100%">
			{if empty($MODE) || $MODE eq 'edit'}
				{include file="modules/Calendar/EventRelatedToUI.tpl"}
			{else}
				{include file="modules/Calendar/EventRelatedToUIReadOnly.tpl"}
			{/if}
			</div>
		</div>
		
		<div id="addEventInviteUICont" class="tab-pane fade in">
			<div id="addEventInviteUI" class="calendar-widget" style="width:100%">
			{if empty($MODE) || $MODE eq 'edit'}
				{include file="modules/Calendar/EventInviteUI.tpl"}
			{else}
				{include file="modules/Calendar/EventInviteUIReadOnly.tpl"}
			{/if}
			</div>
		</div>
		
		<div id="addEventAlarmUICont" class="tab-pane fade in">
			<div id="addEventAlarmUI" class="calendar-widget" style="width:100%">
			{if empty($MODE) || $MODE eq 'edit'}
				{include file="modules/Calendar/EventAlarmUI.tpl"}
			{else}
				{include file="modules/Calendar/EventAlarmUIReadOnly.tpl"}
			{/if}
			</div>
		</div>
		
		<div id="addEventRepeatUICont" class="tab-pane fade in">
			<div id="addEventRepeatUI" class="calendar-widget" style="width:100%">
			{if empty($MODE) || $MODE eq 'edit'}
				{include file="modules/Calendar/EventRepeatUI.tpl"}
			{else}
				{include file="modules/Calendar/EventRepeatUIReadOnly.tpl"}
			{/if}
			</div>
		</div>
	</div>
</div>