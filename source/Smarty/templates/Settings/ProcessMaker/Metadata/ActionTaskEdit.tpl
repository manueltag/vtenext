{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@92272 crmv@104180 crmv@115268 *}
{include file="SmallHeader.tpl"}

<script src="{"modules/Settings/ProcessMaker/resources/ProcessMakerScript.js"|resourcever}" type="text/javascript"></script>
<script src="{"modules/Settings/ProcessMaker/resources/ActionTaskScript.js"|resourcever}" type="text/javascript"></script>
<script src="{"modules/Settings/ProcessMaker/resources/ConditionTaskScript.js"|resourcever}" type="text/javascript"></script>

{if $SHOW_ACTION_CONDITIONS}
<script src="modules/com_vtiger_workflow/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/fieldvalidator.js" type="text/javascript" charset="utf-8"></script>
<script src="include/js/GroupConditions.js" type="text/javascript"></script>
{/if}

<form id="actionform" method="post" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="id" value="{$ID}">
	<input type="hidden" name="elementid" value="{$ELEMENTID}">
	<input type="hidden" name="metaid" value="{$METAID}">
	<input type="hidden" name="action_type" value="{$ACTIONTYPE}">
	<input type="hidden" name="cycle_action" value="{$CYCLE_ACTION}">
	<input type="hidden" name="cycle_field" value="{$CYCLE_FIELD}">
	<input type="hidden" name="inserttablerow_field" value="{$INSERT_TABLEROW_FIELD}">
	<table border="0" cellpadding="2" cellspacing="0" width="100%">
		<tr>
			<td align=right width=15% nowrap="nowrap">
				{include file="FieldHeader.tpl" mandatory=true label="LBL_PM_ACTION_TITLE"|getTranslatedString:'Settings'}
			</td>
			<td align="left">
				<div class="dvtCellInfo">
					<input type="text" class="detailedViewTextBox" id="action_title" name="action_title" value="{$METADATA.action_title}">
				</div>
			</td>
			<td align=right width=15% nowrap="nowrap">&nbsp;</td>
		</tr>
		{if !empty($INSERT_TABLEROW_LABEL)}
			<tr>
				<td></td>
				<td>{$INSERT_TABLEROW_LABEL}</td>
				<td></td>
			</tr>
		{/if}
	</table>
	{if $SHOW_ACTION_CONDITIONS}
		<!-- Workflow Conditions -->
		<br>
		<div style="padding: 0px 13px">
			<div id="conditions" style="display:none;">{$ACTION_CONDITIONS}</div>
			<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
				<tr height="40">
					<td class="big detailedViewHeader" nowrap="nowrap">
						<strong>{$MOD.LBL_CONDITIONS}{$CYCLE_FIELDLABEL}</strong>
					</td>
					<td class="small detailedViewHeader" align="right">
						<span id="group_conditions_loading" style="display:none">{include file="LoadingIndicator.tpl"}</span>
						<input type="button" class="crmButton create small" value="{$MOD.LBL_NEW_GROUP}" id="group_conditions_add" style="display:none"/>
					</td>
				</tr>
			</table>
			<div id="save_conditions"></div>
			<div id="dump" style="display:none;"></div>
		</div>
		<hr>
	{/if}
	{include file="$TEMPLATE"}
</form>

{if $SHOW_ACTION_CONDITIONS}
<script type="text/javascript">
jQuery(document).ready(function(){ldelim}
	ActionConditionScript.init('{$ID}','{$ELEMENTID}','{$METAID}','{$CYCLE_FIELDNAME}');
{rdelim});
</script>
{/if}