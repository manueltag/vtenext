{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/
*}

{* crmv@83878 *}

<table width="100%" cellspacing="0" cellpadding="5" border="0">
	<tr>
		<td class="heading2" width="10%">
			{'LBL_IMPORT_STEP_4'|@getTranslatedString:$MODULE}:
		</td>
		<td>
			<span class="big">{'LBL_IMPORT_STEP_4_DESCRIPTION'|@getTranslatedString:$MODULE}</span>
		</td>
		<td width="5%">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">
			{* crmv@92218 *}
			<div id="encodingsContainer" style="display:inline-block">
				{include file="modules/Import/Import_Encodings.tpl"}
			</div>
			<div id="savedMapsContainer" style="display:inline-block">
				{include file="modules/Import/Import_Saved_Maps.tpl"}
			</div>
			{* crmv@92218e *}
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" name="field_mapping" id="field_mapping" value="" />
			<input type="hidden" name="default_values" id="default_values" value="" />
			<input type="hidden" name="fields_formats" id="fields_formats" value="" />
			<table width="100%" cellspacing="0" cellpadding="5" class="listRow" border="0">
				<tr>
					{if $HAS_HEADER eq true}
					<td class="big tableHeading" width="20%"><b>{'LBL_FILE_COLUMN_HEADER'|@getTranslatedString:$MODULE}</b></td>
					{/if}
					<td class="big tableHeading" width="20%"><b>{'LBL_ROW_1'|@getTranslatedString:$MODULE}</b></td>
					<td class="big tableHeading" width="20%"><b>{'LBL_CRM_FIELDS'|@getTranslatedString:$MODULE}</b></td>
					<td class="big tableHeading" width="20%"><b>{'LBL_DEFAULT_VALUE'|@getTranslatedString:$MODULE}</b></td>
					<td class="big tableHeading" width="20%"><b>{'LBL_IMPORT_FORMAT'|@getTranslatedString:$MODULE}</b></td>
				</tr>
				{foreach key=_HEADER_NAME item=_FIELD_VALUE from=$ROW_1_DATA name="headerIterator"}
				{assign var="_COUNTER" value=$smarty.foreach.headerIterator.iteration}
				<tr class="fieldIdentifier" id="fieldIdentifier{$_COUNTER}">
					{if $HAS_HEADER eq true}
					<td class="cellLabel">
						<span name="header_name">{$_HEADER_NAME}</span>
					</td>
					{/if}
					<td class="cellLabel">
						<span class="importValueContainer">{$_FIELD_VALUE|@textlength_check}</span>
					</td>
					<td class="cellLabel">
						<input type="hidden" name="row_counter" value="{$_COUNTER}" />
						<select name="mapped_fields" class="txtBox" style="width: 100%" onchange="ImportJs.loadDefaultValueWidget('fieldIdentifier{$_COUNTER}')">
							<option value="">{'LBL_NONE'|@getTranslatedString:$FOR_MODULE}</option>
							{foreach key=_FIELD_NAME item=_FIELD_INFO from=$AVAILABLE_FIELDS}
							{assign var="_TRANSLATED_FIELD_LABEL" value=$_FIELD_INFO->getFieldLabelKey()|@getTranslatedString:$FOR_MODULE}
							{* crmv@83878 *}
							{if $LANGUAGE != '' && $LANGUAGE != 'en_us'}
								{assign var="_TRANSLATED_FIELD_LABEL2" value=$_FIELD_INFO->getFieldLabelKey()}
							{else}
								{assign var="_TRANSLATED_FIELD_LABEL2" value="-----"}
							{/if}
							<option value="{$_FIELD_NAME}" {if $_HEADER_NAME eq $_TRANSLATED_FIELD_LABEL || $_HEADER_NAME eq $_TRANSLATED_FIELD_LABEL2} selected {/if} >
								{$_TRANSLATED_FIELD_LABEL}
								{if $_FIELD_INFO->isMandatory($CURRENT_USER) eq 'true'}&nbsp; (*){/if}	{* crmv@49510 *}
							</option>
							{* crmv@83878e *}
							{/foreach}
						</select>
					</td>
					<td class="cellLabel" name="default_value_container">&nbsp;</td>
					<td class="cellLabel" name="format_container">&nbsp;</td>
				</tr>
				{/foreach}
			</table>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">
			<input type="checkbox" name="save_map" id="save_map" class="small" />
			<span class="small">{'LBL_SAVE_AS_CUSTOM_MAPPING'|@getTranslatedString:$MODULE}</span>&nbsp; : &nbsp;
			<input type="text" name="save_map_as" id="save_map_as" class="small" />
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
{include file="modules/Import/Import_Default_Values_Widget.tpl"}
{include file="modules/Import/Import_Formats_Widget.tpl"}
