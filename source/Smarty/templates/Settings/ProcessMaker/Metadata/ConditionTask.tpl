{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@92272 crmv@96450 *}
{include file="Settings/ProcessMaker/Metadata/Header.tpl"}

<script src="modules/com_vtiger_workflow/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/fieldvalidator.js" type="text/javascript" charset="utf-8"></script>
<script src="{"modules/Settings/ProcessMaker/resources/ConditionTaskScript.js"|resourcever}" type="text/javascript"></script>
<script src="include/js/GroupConditions.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function(){ldelim}
	ConditionTaskScript.init('{$PROCESSID}','{$ID}');
{rdelim});
</script>

<div style="padding:5px;">
	<form class="form-config-shape" shape-id="{$ID}">
		<input type="hidden" id="isStartTask" value="{$IS_START_TASK|intval}">
		<div id="conditions" style="display:none;">{$METADATA.conditions}</div>
		<div id="sdk_custom_functions" style="display:none;">{$SDK_CUSTOM_FUNCTIONS}</div>
		<table border="0" cellpadding="0" cellspacing="5" width="100%">
			<tr>
				<td>
					<span class="dvtCellLabel" style="float:left">{$MOD.LBL_ENTITY}</span>
				</td>
				<td width="100%">
					<div class="dvtCellInfo" style="float:left">
						<select name="moduleName" class="dvtCellInfo detailedViewTextBox">
							{foreach key=k item=i from=$moduleNames}
								<option value="{$k}" {$i.1}>{$i.0}</option>
							{/foreach}
						</select>
					</div>
				</td>
			</tr>
		</table>
		<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td class="big" nowrap="nowrap">
					<strong>{$MOD.LBL_WHEN_TO_RUN_PM_TASK}</strong>
				</td>
			</tr>
		</table>
		<table border="0">
			<tr>
				<td><input type="radio" name="execution_condition" id="execution_condition_{$ID}_1" value="ON_FIRST_SAVE" {if $METADATA.execution_condition eq 'ON_FIRST_SAVE'}checked{/if}/></td> 
				<td><label for="execution_condition_{$ID}_1">{$MOD.LBL_ONLY_ON_FIRST_SAVE}</label></td>
			</tr>
			<tr>
				<td><input type="radio" name="execution_condition" id="execution_condition_{$ID}_3" value="ON_EVERY_SAVE" {if $METADATA.execution_condition eq 'ON_EVERY_SAVE'}checked{/if}/></td>
				<td><label for="execution_condition_{$ID}_3">{$MOD.LBL_EVERYTIME_RECORD_SAVED}</label></td>
			</tr>
			<tr>
				<td><input type="radio" name="execution_condition" id="execution_condition_{$ID}_4" value="ON_MODIFY" {if $METADATA.execution_condition eq 'ON_MODIFY'}checked{/if}/></td>
				<td><label for="execution_condition_{$ID}_4">{$MOD.LBL_ON_MODIFY}</label></td>
			</tr>
			{* forse non serve
			<tr>
				<td><input type="radio" name="execution_condition" id="execution_condition_{$ID}_2" value="ONCE" {if $METADATA.execution_condition eq 'ONCE'}checked{/if} /></td>
				<td><label for="execution_condition_{$ID}_2">{$MOD.LBL_UNTIL_FIRST_TIME_CONDITION_TRUE}</label></td>
			</tr>
			*}
			<tr>
				<td><input type="radio" name="execution_condition" id="execution_condition_{$ID}_5" value="EVERY_TIME" {if $METADATA.execution_condition eq 'EVERY_TIME'}checked{/if} /></td>
				<td><label for="execution_condition_{$ID}_5">{$MOD.LBL_EVERY_TIME_TIME_CONDITION_TRUE}</label></td>
			</tr>
			{* crmv@97575 *}
			{if $IS_START_TASK}
				<tr>
					<td><input type="radio" name="execution_condition" id="execution_condition_{$ID}_6" value="ON_SUBPROCESS" {if $METADATA.execution_condition eq 'ON_SUBPROCESS'}checked{/if} /></td>
					<td><label for="execution_condition_{$ID}_6">{$MOD.LBL_ON_SUBPROCESS}</label></td>
				</tr>
			{/if}
			{* crmv@97575e *}
			{* crmv@100495 *}
			{if $ENABLE_MANUAL_MODE}
				<tr>
					<td><input type="radio" name="execution_condition" id="execution_condition_{$ID}_7" value="MANUAL_MODE" {if $METADATA.execution_condition eq 'MANUAL_MODE'}checked{/if} /></td>
					<td><label for="execution_condition_{$ID}_7">{$MOD.LBL_ON_MANUAL_MODE}</label></td>
				</tr>
			{/if}
			{* crmv@100495e *}
		</table>
		<!-- Workflow Conditions -->
		<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr height="40">
				<td class="big" nowrap="nowrap">
					<strong>{$MOD.LBL_CONDITIONS}</strong>
				</td>
				<td class="small" align="right">
					<span id="group_conditions_loading" style="display:none">{include file="LoadingIndicator.tpl"}</span>
					<input type="button" class="crmButton create small" value="{$MOD.LBL_NEW_GROUP}" id="group_conditions_add" style="display:none"/>
				</td>
			</tr>
		</table>
		<div id="save_conditions"></div>
		<div id="dump" style="display:none;"></div>
	</form>
</div>