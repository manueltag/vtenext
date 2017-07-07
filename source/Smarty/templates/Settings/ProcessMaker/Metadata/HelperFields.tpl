{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@96450 *}

<script type="text/javascript" src="{"modules/Settings/ProcessMaker/resources/ActionTaskScript.js"|resourcever}"></script>

{* javascript for the module maker *}
<script type="text/javascript" src="modules/Settings/ModuleMaker/ModuleMaker.js"></script>
<script type="text/javascript">
{literal}
var parentAjaxCall = ModuleMakerFields.ajaxCall;
ModuleMakerFields.ajaxCall = function(action, params, callback, options) {
	if (typeof(options) == 'object') options.processMakerMode = true; else options = {processMakerMode:true};
	parentAjaxCall.call(ModuleMakerFields, action, params, callback, options);
}
{/literal}
</script>

{* some CSS *}
<style type="text/css">
{literal}
	.mmaker_step_field_cell {
		min-height: 40px;
		height: 40px;
	}
	.floatingDiv {
		display:none;
		position: fixed;
	}
	.floatingHandle {
		padding: 5px;
		cursor: move;
	}
	.newFieldMnu {
		text-decoration: none;
		color: black;
		display: block;
		padding-top: 5px;
		padding-bottom: 5px;
		padding-left: 5px;
		background-repeat: no-repeat;
		background-position: left;
	}
	.newFieldMnuSelected {
		background-color: #0099ff;
		color: white;
	}
	.newfieldprop {
		display:none;
	}
{/literal}
</style>

{* include blocks table *}
<div id="mmaker_div_allblocks">
{if $MODE eq 'openimportdynaformblocks'}
	<textarea style="display:none" id="mmaker">{$MMAKER}</textarea>
	{foreach key=DYNAELEMENT item=STEPVARS from=$STEPVARS_ARR}
		<br>
		<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td class="dvInnerHeader">
					<input type="checkbox" class="small" id="importall_{$DYNAELEMENT}" name="importall_{$DYNAELEMENT}" onChange="ProcessHelperScript.checkAllDynaformBlocks('{$DYNAELEMENT}',this.checked)" />
					<strong><label for="importall_{$DYNAELEMENT}">{$TITLES[$DYNAELEMENT]}</label></strong>
				</td>
			</tr>
			<tr>
				<td>
					{include file="Settings/ModuleMaker/Step2Fields.tpl"}
				</td>
			</tr>
		</table>
	{/foreach}
{else}
	{include file="Settings/ModuleMaker/Step2Fields.tpl"}
{/if}
</div>