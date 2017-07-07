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

<script src="modules/com_vtiger_workflow/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/fieldvalidator.js" type="text/javascript" charset="utf-8"></script>
<script src="{"modules/Settings/ProcessMaker/resources/ConditionTaskScript.js"|resourcever}" type="text/javascript"></script>
<script src="include/js/GroupConditions.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function(){ldelim}
	ConditionTaskScript.init('{$PROCESSID}','{$ELEMENTID}');
{rdelim});
</script>

<div style="padding:5px;">
	<form class="form-config-shape" shape-id="{$ELEMENTID}">
		<div id="conditions" style="display:none;">{$CONDITIONS}</div>
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
		<!-- Workflow Conditions -->
		<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr height="40">
				<td class="big" nowrap="nowrap"></td>
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