{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@106856 *}
{include file="Settings/ProcessMaker/Metadata/Header.tpl"}

<script src="{"modules/Settings/ProcessMaker/resources/ActionTaskScript.js"|resourcever}" type="text/javascript"></script>
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

<div id="editForm" style="padding:5px;">
	<form name="EditView">
		<input type="hidden" name="conditions_count" value="{$RULES|@count}" />
		<table border="0" width="100%">
		{foreach key=KEY item=RULE from=$RULES}
			<tr valign="top">
				<td width="1%" nowrap>
					<a href="javascript:;" onClick="ActionTaskScript.editAdvancedFieldAssignment('{$PROCESSID}','{$ELEMENTID}','{$ACTIONID}','{$FIELDNAME}','{$FORM_MODULE}','{$KEY}')"><i class="vteicon" title="{$APP.LBL_EDIT}">create</i></a>
					<a href="javascript:;" onClick="ActionTaskScript.deleteAdvancedFieldAssignment('{$PROCESSID}','{$ELEMENTID}','{$ACTIONID}','{$FIELDNAME}','{$FORM_MODULE}','{$KEY}')"><i class="vteicon" title="{$APP.LBL_DELETE}">clear</i></a>
				</td>
				<td>
					<div class="dvtCellLabel">{$RULE.conditions_translate}</div>
					{include file="EditViewUI.tpl" NOLABEL=true DIVCLASS="dvtCellInfo"
						uitype=$RULE.uitype
						value=$RULE.value
						assigntype=$RULE.assigntype
						fldvalue=$RULE.users_combo
						secondvalue=$RULE.groups_combo
						fldname=$FIELDNAME|cat:$KEY
						fldgroupname="assigned_group_id"|cat:$KEY
						fldothername="other_assigned_user_id"|cat:$KEY
						assigntypename="assigntype"|cat:$KEY
						assign_user_div="assign_user"|cat:$KEY
						assign_team_div="assign_team"|cat:$KEY
						assign_other_div="assign_other"|cat:$KEY
					}
				</td>
			</tr>
		{/foreach}
		</table>
		<div style="height:5px;"></div>
		<div style="float:right">
			<input type="button" onclick="ActionTaskScript.openAdvancedFieldAssignmentCondition('{$PROCESSID}','{$ELEMENTID}','{$ACTIONID}','{$FIELDNAME}','{$FORM_MODULE}')" class="crmbutton small create" value="{$MOD.LBL_ADD_RULE}" title="{$MOD.LBL_SAVE_LABEL}">
		</div>
	</form>
</div>

<script type="text/javascript">
	{foreach key=KEY item=RULE from=$RULES}
		jQuery('.editoptions[fieldname="sdk_params_assigned_user_id{$KEY}"]').html(parent.jQuery('.editoptions[fieldname="sdk_params_assigned_user_id"]').html());
	
		jQuery('#{"other_assigned_user_id"|cat:$KEY}').append(parent.jQuery('#task-smownerfieldnames').html());
		jQuery('#{"other_assigned_user_id"|cat:$KEY} option[value="advanced_field_assignment"]').remove();
		{if $RULE.assigntype eq 'O'}
			jQuery('#{"other_assigned_user_id"|cat:$KEY}').val('{$RULE.value}');
		{/if}
		jQuery('#{"other_assigned_user_id"|cat:$KEY}').change(function(){ldelim}
			ActionTaskScript.showSdkParamsInput(this,'{"assigned_user_id"|cat:$KEY}');	//crmv@113527
		{rdelim});
		ActionTaskScript.showSdkParamsInput(jQuery('#{"other_assigned_user_id"|cat:$KEY}'),'{"assigned_user_id"|cat:$KEY}');	//crmv@113527
	{/foreach}
	filterPopulateField();
</script>