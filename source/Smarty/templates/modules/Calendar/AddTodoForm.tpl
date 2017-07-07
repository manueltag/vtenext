{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@20628 *}
{* crmv@98866 *}
{* crmv@103922 *}
{* crmv@112297 *}

{if empty($MODE) || $MODE eq 'edit'}
<script type="text/javascript">
	var fieldname = {$VALIDATION_DATA_FIELDNAME};
	var fieldlabel = {$VALIDATION_DATA_FIELDLABEL};
	var fielddatatype = {$VALIDATION_DATA_FIELDDATATYPE};
	var fielduitype = {$VALIDATION_DATA_FIELDUITYPE};
	var fieldwstype = {$VALIDATION_DATA_FIELDWSTYPE};

	var fieldnameTaskCustom = {$VALIDATION_DATA_CUS_FIELDNAME};
	var fieldlabelTaskCustom = {$VALIDATION_DATA_CUS_FIELDLABEL};
	var fielddatatypeTaskCustom = {$VALIDATION_DATA_CUS_FIELDDATATYPE};
	var fielduitypeTaskCustom = {$VALIDATION_DATA_CUS_FIELDUITYPE};
	var fieldwstypeTaskCustom = {$VALIDATION_DATA_CUS_FIELDWSTYPE};
</script>
{else}
<script type="text/javascript">
	var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
	var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
	var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
	var fielduitype = new Array({$VALIDATION_DATA_FIELDUITYPE});
	var fieldwstype = new Array({$VALIDATION_DATA_FIELDWSTYPE});
</script>
{/if} 

{if empty($MODE) || $MODE eq 'edit'}
{assign var=EditViewForm value='createTodo'} {* crmv@106578 *}
<form name="createTodo" id="createTodo" method="POST" action="index.php">
	<input type="hidden" name="return_action" value="index">
	<input type="hidden" name="return_module" value="Calendar">
	<input type="hidden" name="module" value="Calendar">
	<input type="hidden" name="activity_mode" value="Task">
	<input type="hidden" name="action" value="TodoSave">
	<input type="hidden" name="view" value="{$view}">
	<input type="hidden" name="hour" value="{$hour}">
	<input type="hidden" name="day" value="{$day}">
	<input type="hidden" name="month" value="{$month}">
	<input type="hidden" name="year" value="{$year}">
	<input type="hidden" name="record" value="{$RECORD}">
	<input type="hidden" name="parenttab" value="{$CATEGORY}">
	<input type="hidden" name="mode" value="{$MODE}">
	<input type="hidden" name="time_start" id="time_start">
	<input type="hidden" name="viewOption" value="">
	<input type="hidden" name="subtab" value="">
	<input type="hidden" name="maintab" value="Calendar">
	<input type="hidden" name="ajaxCalendar" value="detailedAdd">
{else}
<form name="DetailView" method="POST" action="index.php">
	<input type="hidden" name="module" value="{$MODULE}">
{/if}

	<div class="col-xs-12 nopadding">
		<div class="col-xs-12" style="margin: 5px auto"></div>
		<div class="col-xs-12 nopadding">
			<div class="col-xs-12 col-md-6 content-left">
				<div class="col-xs-12 nopadding">{include file="modules/Calendar/DisplayFields.tpl"}</div>
			</div>
			<div class="col-xs-12 col-md-6">
				<div class="col-xs-12 nopadding">{include file="modules/Calendar/TodoCollapseUI.tpl"}</div>
			</div>
		</div>
		<div class="col-xs-12" style="margin: 5px auto"></div>
	</div>

{if empty($MODE) || $MODE eq 'edit'}
</form>
{/if}
