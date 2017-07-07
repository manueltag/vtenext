{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@99316 crmv@100731 crmv@109685 *}
{include file="Settings/ProcessMaker/Metadata/Header.tpl"}

<script src="{"modules/Settings/ProcessMaker/resources/ActionTaskScript.js"|resourcever}" type="text/javascript"></script>
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script language="JavaScript" type="text/javascript" src="include/js/vtlib.js"></script>	{* crmv@92272 *}
<script type="text/javascript">
	jQuery(document).ready(function(){ldelim}
		ProcessHelperScript.initPopulateField('{$JSON_INVOLVED_RECORDS}','{$JSON_DYNAFORM_OPTIONS}','{$JSON_ELEMENTS_ACTORS}');
	{rdelim});
</script>
{*
<select id='task-fieldnames' class="notdropdown" style="display:none;">
	<option value="">{'LBL_SELECT_OPTION_DOTDOTDOT'|getTranslatedString:'com_vtiger_workflow'}</option>
	{if !empty($SDK_CUSTOM_FUNCTIONS)}
		<optgroup label="{$MOD.LBL_PM_SDK_CUSTOM_FUNCTIONS}">
			{foreach key=k item=i from=$SDK_CUSTOM_FUNCTIONS}
				<option value="{$k}">{$i}</option>
			{/foreach}
		</optgroup>
	{/if}
</select>
*}
<select id='task-smownerfieldnames' class="notdropdown" style="display:none;">
	<option value="">{'LBL_SELECT_OPTION_DOTDOTDOT'|getTranslatedString:'com_vtiger_workflow'}</option>
	{if !empty($SDK_CUSTOM_FUNCTIONS)}
		<optgroup label="{$MOD.LBL_PM_SDK_CUSTOM_FUNCTIONS}">
			{foreach key=k item=i from=$SDK_CUSTOM_FUNCTIONS}
				<option value="{$k}">{$i}</option>
			{/foreach}
		</optgroup>
	{/if}
</select>

<div style="padding:5px;">
	<form name="EditView" class="form-config-shape" shape-id="{$ID}">
		{* crmv@112297 *}
		<br>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td class="big">
					{$MOD.LBL_PM_CONDITIONALS}
				</td>
				<td align="right">
					<input type="button" class="crmButton create small" value="{'LBL_NEW_FPOFV_BUTTON_TITLE'|getTranslatedString:'Conditionals'}" onclick="ProcessMakerScript.editConditional('{$PROCESSID}','{$ID}','')"/>
				</td>
			</tr>
		</table>
		{if empty($METADATA.conditionals)}
			<div class="popupLinkListNoData">{$MOD.LBL_PM_NO_RULES}</div>
		{else}
			<table class="listTable" width="100%" border="0" cellspacing="1" cellpadding="5">
				<tr>
					<td class="colHeader small" width="80px">
						{$MOD.LBL_LIST_TOOLS}
					</td>
					<td class="colHeader small">
						{$MOD.LBL_PM_RULE}
					</td>
				</tr>
				{foreach key=RULEID item=RULE from=$METADATA.conditionals}
				<tr>
					<td class="listTableRow small">
						<a href="javascript:ProcessMakerScript.editConditional('{$PROCESSID}','{$ID}','{$RULEID}');">
							<i class="vteicon" title="{$APP.LBL_EDIT}">create</i>
						</a>
						<a href="javascript:ProcessMakerScript.deleteConditional('{$PROCESSID}','{$ID}','{$RULEID}');">
							<i class="vteicon" title="{$APP.LBL_DELETE}">clear</i>
						</a>
					</td>
					<td class="listTableRow small">{$RULE.title}</td>
				</tr>
				{/foreach}
			</table>
		{/if}
		{* crmv@112297e *}
		<br>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td class="big">
					{$MOD.LBL_PM_DYNAFORM_CONDITIONALS}
				</td>
				<td align="right">
					<input type="button" class="crmButton create small" value="{'LBL_NEW_FPOFV_BUTTON_TITLE'|getTranslatedString:'Conditionals'}" onclick="ProcessMakerScript.editDynaFormConditional('{$PROCESSID}','{$ID}','')"/>
				</td>
			</tr>
		</table>
		{if empty($METADATA.dfconditionals)}
			<div class="popupLinkListNoData">{$MOD.LBL_PM_NO_RULES}</div>
		{else}
			<table class="listTable" width="100%" border="0" cellspacing="1" cellpadding="5">
				<tr>
					<td class="colHeader small" width="80px">
						{$MOD.LBL_LIST_TOOLS}
					</td>
					<td class="colHeader small">
						{$MOD.LBL_PM_RULE}
					</td>
				</tr>
				{foreach key=RULEID item=RULE from=$METADATA.dfconditionals}
				<tr>
					<td class="listTableRow small">
						<a href="javascript:ProcessMakerScript.editDynaFormConditional('{$PROCESSID}','{$ID}','{$RULEID}');">
							<i class="vteicon" title="{$APP.LBL_EDIT}">create</i>
						</a>
						<a href="javascript:ProcessMakerScript.deleteDynaFormConditional('{$PROCESSID}','{$ID}','{$RULEID}');">
							<i class="vteicon" title="{$APP.LBL_DELETE}">clear</i>
						</a>
					</td>
					<td class="listTableRow small">{$RULE.title}</td>
				</tr>
				{/foreach}
			</table>
		{/if}
		<br>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td colspan="2" class="big">
					{$MOD.LBL_PM_ADVANCED_PERMISSIONS}
				</td>
			</tr>
			<tr>
				<td class="dvtCellLabel" align="right" width="20%"><span>{$MOD.LBL_ENTITY}</span>&nbsp;&nbsp;</td>
				<td>
					<div class="dvtCellInfo">
						<select id="record_involved" class="detailedViewTextBox">
							{foreach key=k item=i from=$ADV_RECORD_INVOLVED}
								<option value="{$k}" {$i.1}>{$i.0}</option>
							{/foreach}
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td class="dvtCellLabel" align="right" width="20%"><span>{$MOD.LBL_PM_RESOURCE}</span>&nbsp;&nbsp;</td>
				<td>
					{include file="EditViewUI.tpl" NOLABEL=true DIVCLASS="dvtCellInfo"
						uitype=$ADV_ASSIGNEDTO[0][0]
						fldlabel=$ADV_ASSIGNEDTO[0][0]
						fldlabel_sel=$ADV_ASSIGNEDTO[1][1]
						fldlabel_combo=$ADV_ASSIGNEDTO[1][2]
						fldname=$ADV_ASSIGNEDTO[2][0]
						fldvalue=$ADV_ASSIGNEDTO[3][0]
						secondvalue=$ADV_ASSIGNEDTO[3][1]
						thirdvalue=$ADV_ASSIGNEDTO[3][2]
						readonly=$ADV_ASSIGNEDTO[4]
						typeofdata=$ADV_ASSIGNEDTO[5]
						isadmin=$ADV_ASSIGNEDTO[6]
						keyfldid=$ADV_ASSIGNEDTO[7]
						keymandatory=false
						fifthvalue=$ADV_OTHER_ASSIGNED_TO
					}
				</td>
			</tr>
			<tr>
				<td class="dvtCellLabel" align="right" width="20%"><span>{$MOD.LBL_PERMISSIONS}</span>&nbsp;&nbsp;</td>
				<td>
					<div class="dvtCellInfo">
						<select id="permission" class="detailedViewTextBox">
							<option value="rw">{'Read/Write'|getTranslatedString:'Settings'}</option>
							<option value="ro">{'Read Only '|getTranslatedString:'Settings'}</option>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="button" class="crmbutton save small" value="{$MOD.LBL_ADV_ADD_RULE_BUTTON}" onclick="ProcessMakerScript.addAdvancedPermission('{$PROCESSID}','{$ID}')"/>
				</td>
			</tr>
		</table>
		{if !empty($METADATA.advanced_permissions)}
			<table class="listTable" width="100%" border="0" cellspacing="1" cellpadding="5">
				<tr>
					<td class="colHeader small" width="80px">
						{$MOD.LBL_LIST_TOOLS}
					</td>
					<td class="colHeader small" width="30%">
						{$MOD.LBL_ENTITY}
					</td>
					<td class="colHeader small" width="40%">
						{$MOD.LBL_PM_RESOURCE}
					</td>
					<td class="colHeader small">
						{$MOD.LBL_PERMISSIONS}
					</td>
				</tr>
				{foreach key=RULEID item=RULE from=$ADV_PERMISSIONS_LIST}
				<tr>
					<td class="listTableRow small">
						<a href="javascript:ProcessMakerScript.deleteAdvancedPermission('{$PROCESSID}','{$ID}','{$RULEID}');">
							<i class="vteicon" title="{$APP.LBL_DELETE}">clear</i>
						</a>
					</td>
					<td class="listTableRow small">{$RULE.record_involved_display}</td>
					<td class="listTableRow small">{$RULE.resource_display}</td>
					<td class="listTableRow small">{$RULE.permission_display}</td>
				</tr>
				{/foreach}
			</table>
		{/if}
	</form>
</div>