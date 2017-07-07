{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@92272 crmv@96450 crmv@112297 *}

{include file='CachedValues.tpl'}	{* crmv@26316 *}

<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/parallelexecuter.js" type="text/javascript" charset="utf-8"></script>

{literal}
<style type="text/css">
	/* crmv@112299 */
	.populateField, .populateFieldGroup {
		font-size:13px;
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

<table border="0" cellpadding="2" cellspacing="0" width="100%" class="small" style="padding-top:5px">
	<tr>
		<td align="right" width=15% nowrap="nowrap">
			{include file="FieldHeader.tpl" mandatory=true label='From'|@getTranslatedString:'Messages'}
		</td>
		<td>
			<div class="dvtCellInfo" style="float:left">
				<input type="text" name="sender" value="{$METADATA.sender}" id="save_sender" class="detailedViewTextBox" style='width: 350px;'>
			</div>
			<div class="dvtCellInfo" style="margin-left:5px; float:left">
				<span id="task-emailfields_sender-busyicon"><b>{'LBL_LOADING'|getTranslatedString:'com_vtiger_workflow'}</b>{include file="LoadingIndicator.tpl"}</span>
				<select id="task-emailfields_sender" class="detailedViewTextBox notdropdown populateField" style="display: none;"><option value=''>{'LBL_SELECT_OPTION_DOTDOTDOT'|getTranslatedString:'com_vtiger_workflow'}</option></select>
			</div>
			{* crmv@106857 *}
			{assign var="target_mode" value="overwrite_input"}
			{assign var="target" value="jQuery(jQuery('#save_sender').get())"}
			{assign var="dropdownid" value="task-emailfields_sender"}
			{assign var="fldname" value="sender"}
			<div class="tablefields_options" id="tablefields_options_{$fldname}" style="float:left; display:none;">
				<select class="populateField" onchange="ActionEmailScript.changeTableFieldOpt('{$target_mode}',{$target},'{$fldname}','{$dropdownid}',this)">
					{include file="Settings/ProcessMaker/actions/TablefieldsOptions.tpl"}
				</select>
			</div>
			<input type="text" id="tablefields_seq_{$fldname}" size="2" style="padding-left:5px; float:left; display:none;">
			<i id="tablefields_seq_btn_{$fldname}" class="vteicon md-link" style="float:left; display:none;" onclick="ActionEmailScript.insertTableFieldValue('{$target_mode}',{$target},'{$fldname}','{$dropdownid}','seq')">input</i>
			{* crmv@106857e *}
		</td>
	</tr>
	<tr>
		<td align="right" width=15% nowrap="nowrap">
			{include file="FieldHeader.tpl" mandatory=true label='To'|@getTranslatedString:'Messages'}
		</td>
		<td>
			<div class="dvtCellInfo" style="float:left">
				<input type="text" name="recepient" value="{$METADATA.recepient}" id="save_recepient" class="detailedViewTextBox" style='width: 350px;'>
			</div>
			<div class="dvtCellInfo" style="margin-left:5px; float:left">
				<span id="task-emailfields-busyicon"><b>{'LBL_LOADING'|getTranslatedString:'com_vtiger_workflow'}</b>{include file="LoadingIndicator.tpl"}</span>
				<select id="task-emailfields" class="detailedViewTextBox notdropdown populateField" style="display: none;">
					<option value=''>{'LBL_SELECT_OPTION_DOTDOTDOT'|getTranslatedString:'com_vtiger_workflow'}</option>
					{if !empty($SDK_CUSTOM_FUNCTIONS)}
						<optgroup label="{$MOD.LBL_PM_SDK_CUSTOM_FUNCTIONS}">
							{foreach key=k item=i from=$SDK_CUSTOM_FUNCTIONS}
								<option value="{$k}">{$i}</option>
							{/foreach}
						</optgroup>
					{/if}
				</select>
			</div>
			{* crmv@106857 *}
			{assign var="target_mode" value="append_input_comma"}
			{assign var="target" value="jQuery(jQuery('#save_recepient').get())"}
			{assign var="dropdownid" value="task-emailfields"}
			{assign var="fldname" value="recepient"}
			<div class="tablefields_options" id="tablefields_options_{$fldname}" style="float:left; display:none;">
				<select class="populateField" onchange="ActionEmailScript.changeTableFieldOpt('{$target_mode}',{$target},'{$fldname}','{$dropdownid}',this)">
					{include file="Settings/ProcessMaker/actions/TablefieldsOptions.tpl"}
				</select>
			</div>
			<input type="text" id="tablefields_seq_{$fldname}" size="2" style="padding-left:5px; float:left; display:none;">
			<i id="tablefields_seq_btn_{$fldname}" class="vteicon md-link" style="float:left; display:none;" onclick="ActionEmailScript.insertTableFieldValue('{$target_mode}',{$target},'{$fldname}','{$dropdownid}','seq')">input</i>
			{* crmv@106857e *}
		</td>
	</tr>
	<tr>
		<td align="right" width=15% nowrap="nowrap">
			{include file="FieldHeader.tpl" label='Cc'|@getTranslatedString:'Messages'}
		</td>
		<td>
			<div class="dvtCellInfo" style="float:left">
				<input type="text" name="emailcc" value="{$METADATA.emailcc}" id="save_emailcc" class="detailedViewTextBox" style='width: 350px;'>
			</div>
			<div class="dvtCellInfo" style="margin-left:5px; float:left">
				<span id="task-emailfieldscc-busyicon"><b>{'LBL_LOADING'|getTranslatedString:'com_vtiger_workflow'}</b>{include file="LoadingIndicator.tpl"}</span>
				<select id="task-emailfieldscc" class="detailedViewTextBox notdropdown populateField" style="display: none;"><option value=''>{'LBL_SELECT_OPTION_DOTDOTDOT'|getTranslatedString:'com_vtiger_workflow'}</option></select>
			</div>
			{* crmv@106857 *}
			{assign var="target_mode" value="append_input_comma"}
			{assign var="target" value="jQuery(jQuery('#save_emailcc').get())"}
			{assign var="dropdownid" value="task-emailfieldscc"}
			{assign var="fldname" value="emailcc"}
			<div class="tablefields_options" id="tablefields_options_{$fldname}" style="float:left; display:none;">
				<select class="populateField" onchange="ActionEmailScript.changeTableFieldOpt('{$target_mode}',{$target},'{$fldname}','{$dropdownid}',this)">
					{include file="Settings/ProcessMaker/actions/TablefieldsOptions.tpl"}
				</select>
			</div>
			<input type="text" id="tablefields_seq_{$fldname}" size="2" style="padding-left:5px; float:left; display:none;">
			<i id="tablefields_seq_btn_{$fldname}" class="vteicon md-link" style="float:left; display:none;" onclick="ActionEmailScript.insertTableFieldValue('{$target_mode}',{$target},'{$fldname}','{$dropdownid}','seq')">input</i>
			{* crmv@106857e *}
		</td>
	</tr>
	<tr>
		<td align="right" width=15% nowrap="nowrap">
			{include file="FieldHeader.tpl" label='Bcc'|@getTranslatedString:'Messages'}
		</td>
		<td>
			<div class="dvtCellInfo" style="float:left">
				<input type="text" name="emailbcc" value="{$METADATA.emailbcc}" id="save_emailbcc" class="detailedViewTextBox" style='width: 350px;'>
			</div>
			<div class="dvtCellInfo" style="margin-left:5px; float:left">
				<span id="task-emailfieldsbcc-busyicon"><b>{'LBL_LOADING'|getTranslatedString:'com_vtiger_workflow'}</b>{include file="LoadingIndicator.tpl"}</span>
				<select id="task-emailfieldsbcc" class="detailedViewTextBox notdropdown populateField" style="display: none;"><option value=''>{'LBL_SELECT_OPTION_DOTDOTDOT'|getTranslatedString:'com_vtiger_workflow'}</option></select>
			</div>
			{* crmv@106857 *}
			{assign var="target_mode" value="append_input_comma"}
			{assign var="target" value="jQuery(jQuery('#save_emailbcc').get())"}
			{assign var="dropdownid" value="task-emailfieldsbcc"}
			{assign var="fldname" value="emailbcc"}
			<div class="tablefields_options" id="tablefields_options_{$fldname}" style="float:left; display:none;">
				<select class="populateField" onchange="ActionEmailScript.changeTableFieldOpt('{$target_mode}',{$target},'{$fldname}','{$dropdownid}',this)">
					{include file="Settings/ProcessMaker/actions/TablefieldsOptions.tpl"}
				</select>
			</div>
			<input type="text" id="tablefields_seq_{$fldname}" size="2" style="padding-left:5px; float:left; display:none;">
			<i id="tablefields_seq_btn_{$fldname}" class="vteicon md-link" style="float:left; display:none;" onclick="ActionEmailScript.insertTableFieldValue('{$target_mode}',{$target},'{$fldname}','{$dropdownid}','seq')">input</i>
			{* crmv@106857e *}
		</td>
	</tr>
	<tr>
		<td align="right" width=15% nowrap="nowrap">
			{include file="FieldHeader.tpl" mandatory=true label='Subject'|@getTranslatedString:'Messages'}
		</td>
		<td>
			<div class="dvtCellInfo" style="float:left">
				<input type="text" name="subject" value="{$METADATA.subject}" id="save_subject" class="detailedViewTextBox" style='width: 350px;'>
			</div>
			<div class="dvtCellInfo" style="margin-left:5px; float:left">
				<span id="task-subjectfields-busyicon"><b>{'LBL_LOADING'|getTranslatedString:'com_vtiger_workflow'}</b>{include file="LoadingIndicator.tpl"}</span>
				<select class="detailedViewTextBox notdropdown populateFieldGroup" style="display:none"></select>
				<select id="task-subjectfields" class="detailedViewTextBox notdropdown populateField" style="display: none;">
					<option value=''>{'LBL_PM_SELECT_OPTION_FIELD'|getTranslatedString:'Settings'}</option>
					<option value="back">{'LBL_PM_FIELD_GO_BACK'|getTranslatedString:'Settings'}</option> {* crmv@112299 *}
				</select>
			</div>
			{* crmv@106857 *}
			{assign var="target_mode" value="append_input_space"}
			{assign var="target" value="jQuery(jQuery('#save_subject').get())"}
			{assign var="dropdownid" value="task-subjectfields"}
			{assign var="fldname" value="subject"}
			<div class="tablefields_options" id="tablefields_options_{$fldname}" style="float:left; display:none;">
				<select class="populateField" onchange="ActionEmailScript.changeTableFieldOpt('{$target_mode}',{$target},'{$fldname}','{$dropdownid}',this)">
					{include file="Settings/ProcessMaker/actions/TablefieldsOptions.tpl"}
				</select>
			</div>
			<input type="text" id="tablefields_seq_{$fldname}" size="2" style="padding-left:5px; float:left; display:none;">
			<i id="tablefields_seq_btn_{$fldname}" class="vteicon md-link" style="float:left; display:none;" onclick="ActionEmailScript.insertTableFieldValue('{$target_mode}',{$target},'{$fldname}','{$dropdownid}','seq')">input</i>
			{* crmv@106857e *}
		</td>
	</tr>
</table>

<div style="padding: 5px;">
	<table border="0" cellpadding="5" cellspacing="0" width="100%" class="small">
		<tr>
			<td><b>{'Body'|@getTranslatedString:'Messages'}</b></td>
		</tr>
	</table>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="small">
		<tr>
			<td>
				<div class="dvtCellInfo">
					<span id="task-fieldnames-busyicon"><b>{'LBL_LOADING'|getTranslatedString:'com_vtiger_workflow'}</b>{include file="LoadingIndicator.tpl"}</span>
					<select class="detailedViewTextBox notdropdown populateFieldGroup" style="display:none"></select>
					<select id='task-fieldnames' class="detailedViewTextBox notdropdown populateField" style="display: none;">
						<option value=''>{'LBL_PM_SELECT_OPTION_FIELD'|getTranslatedString:'Settings'}</option>
						<option value="back">{'LBL_PM_FIELD_GO_BACK'|getTranslatedString:'Settings'}</option> {* crmv@112299 *}
					</select>
				</div>
				{* crmv@106857 *}
				{assign var="target_mode" value="append_textarea"}
				{assign var="target" value="CKEDITOR.instances.save_content"}
				{assign var="dropdownid" value="task-fieldnames"}
				{assign var="fldname" value="content"}
				<div class="tablefields_options" id="tablefields_options_{$fldname}" style="float:left; display:none;">
					<select class="populateField" onchange="ActionEmailScript.changeTableFieldOpt('{$target_mode}',{$target},'{$fldname}','{$dropdownid}',this)">
						{include file="Settings/ProcessMaker/actions/TablefieldsOptions.tpl"}
					</select>
				</div>
				<input type="text" id="tablefields_seq_{$fldname}" size="2" style="padding-left:5px; float:left; display:none;">
				<i id="tablefields_seq_btn_{$fldname}" class="vteicon md-link" style="float:left; display:none;" onclick="ActionEmailScript.insertTableFieldValue('{$target_mode}',{$target},'{$fldname}','{$dropdownid}','seq')">input</i>
				{* crmv@106857e *}
			</td>
			<td>
				<div class="dvtCellInfo" style="margin-left:5px;">
					<select class="detailedViewTextBox notdropdown populateField" id="task_timefields">
						<option value="">{'LBL_SELECT_OPTION_DOTDOTDOT'|getTranslatedString:'com_vtiger_workflow'}</option>
						{foreach key=META_LABEL item=META_VALUE from=$META_VARIABLES}
							<option value="${$META_VALUE}">{$META_LABEL|@getTranslatedString:$MODULE_NAME}</option>
						{/foreach}
					</select>
				</div>
			</td>
		</tr>
	</table>
</div>	

<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>

<div style="padding-top:5px">
	<textarea style="width:90%;height:200px;" name="content" rows="55" cols="40" id="save_content" class="detailedViewTextBox"> {$METADATA.content} </textarea>
</div>

<script type="text/javascript" defer="1">
var current_language_arr = "{php} echo $_SESSION['authenticated_user_language']; {/php}".split("_");
var curr_lang = current_language_arr[0];
{literal}
CKEDITOR.replace('save_content', {
	filebrowserBrowseUrl: 'include/ckeditor/filemanager/index.html',
	language : curr_lang
});	
{/literal}
</script>
<script type="text/javascript">
ActionEmailScript.loadForm('{$ID}','{$ELEMENTID}','{$ACTIONTYPE}','{$ACTIONID}','{$INVOLVED_RECORDS}','{$OTHER_OPTIONS}','{$ELEMENTS_ACTORS}');	{* crmv@106857 *}
</script>