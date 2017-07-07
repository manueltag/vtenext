{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@92272 crmv@112297 *}

{include file='CachedValues.tpl'}	{* crmv@26316 *}

<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script language="JavaScript" type="text/javascript" src="include/js/vtlib.js"></script>	{* crmv@92272 *}
<script src="{"include/js/ListView.js"|resourcever}" type="text/javascript" charset="utf-8"></script>
{literal}
<style type="text/css">
	/* crmv@112299 */
	.populateField, .populateFieldGroup {
		font-size:12px;
	}
	.populateFieldGroup option {
		font-weight:bold;
	}
	.populateFieldGroup option:nth-child(1) {
		font-weight:normal;
	}
	/* crmv@112299e */
</style>
{/literal}

<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<td align=right width=15% nowrap="nowrap">
			{include file="FieldHeader.tpl" mandatory=true label=$MOD.LBL_ENTITY}
		</td>
		<td align="left">
			<div class="dvtCellInfo">
				<select name="record_involved" class="detailedViewTextBox" onchange="AlertNotifications.alert(1, null, ActionUpdateScript.loadForm, [this.value,'{$ID}','{$ELEMENTID}','{$ACTIONTYPE}','{$ACTIONID}'])">
					{foreach key=k item=i from=$RECORDS_INVOLVED}
						<option value="{$k}" {$i.1}>{$i.0}</option>
					{/foreach}
				</select>
			</div>
		</td>
		<td align=right width=15% nowrap="nowrap">&nbsp;</td>
	</tr>
</table>
<br>
<select id='task-fieldnames' class="notdropdown" style="display:none;">
	<option value="">{'LBL_PM_SELECT_OPTION_FIELD'|getTranslatedString:'Settings'}</option>
	<option value="back">{'LBL_PM_FIELD_GO_BACK'|getTranslatedString:'Settings'}</option> {* crmv@112299 *}
	{if !empty($SDK_CUSTOM_FUNCTIONS)}
		<optgroup label="{$MOD.LBL_PM_SDK_CUSTOM_FUNCTIONS}">
			{foreach key=k item=i from=$SDK_CUSTOM_FUNCTIONS}
				<option value="{$k}">{$i}</option>
			{/foreach}
		</optgroup>
	{/if}
</select>
<select id='task-pickfieldnames' class="notdropdown" style="display:none;">
	{if !empty($SDK_CUSTOM_FUNCTIONS)}
		<optgroup label="{$MOD.LBL_PM_SDK_CUSTOM_FUNCTIONS}">
			{foreach key=k item=i from=$SDK_CUSTOM_FUNCTIONS}
				<option value="{$k}">{$i}</option>
			{/foreach}
		</optgroup>
	{/if}
</select>
<select id='task-smownerfieldnames' class="notdropdown" style="display:none;">
	<option value="">{'LBL_PM_SELECT_OPTION_FIELD'|getTranslatedString:'Settings'}</option>
	{* crmv@106856 *}
	{if $ADVANCED_FIELD_ASSIGNMENT}
		<option value="advanced_field_assignment">{'LBL_PM_ADVANCED_FIELD_ASSIGNMENT'|getTranslatedString:'Settings'}</option>
	{/if}
	{* crmv@106856e *}
	{if !empty($SDK_CUSTOM_FUNCTIONS)}
		<optgroup label="{$MOD.LBL_PM_SDK_CUSTOM_FUNCTIONS}">
			{foreach key=k item=i from=$SDK_CUSTOM_FUNCTIONS}
				<option value="{$k}">{$i}</option>
			{/foreach}
		</optgroup>
	{/if}
</select>
<select id='task-referencefieldnames' class="notdropdown" style="display:none;">
	<option value="">{'LBL_PM_SELECT_OPTION_FIELD'|getTranslatedString:'Settings'}</option>
	{if !empty($SDK_CUSTOM_FUNCTIONS)}
		<optgroup label="{$MOD.LBL_PM_SDK_CUSTOM_FUNCTIONS}">
			{foreach key=k item=i from=$SDK_CUSTOM_FUNCTIONS}
				<option value="{$k}">{$i}</option>
			{/foreach}
		</optgroup>
	{/if}
</select>
<select id='task-booleanfieldnames' class="notdropdown" style="display:none;">
	{if !empty($SDK_CUSTOM_FUNCTIONS)}
		<optgroup label="{$MOD.LBL_PM_SDK_CUSTOM_FUNCTIONS}">
			{foreach key=k item=i from=$SDK_CUSTOM_FUNCTIONS}
				<option value="{$k}">{$i}</option>
			{/foreach}
		</optgroup>
	{/if}
</select>
{* crmv@108227 *}
<select id='task-datefieldnames' class="notdropdown" style="display:none;">
	{if !empty($SDK_CUSTOM_FUNCTIONS)}
		<optgroup label="{$MOD.LBL_PM_SDK_CUSTOM_FUNCTIONS}">
			{foreach key=k item=i from=$SDK_CUSTOM_FUNCTIONS}
				<option value="{$k}">{$i}</option>
			{/foreach}
		</optgroup>
	{/if}
</select>
{* crmv@108227e *}
<div id="editForm"></div>
<script type="text/javascript">
{if $ACTIONID neq ''}
	jQuery(document).ready(function() {ldelim}
		ActionUpdateScript.loadForm('{$METADATA.record_involved}','{$ID}','{$ELEMENTID}','{$ACTIONTYPE}','{$ACTIONID}');
	{rdelim});
{/if}
</script>