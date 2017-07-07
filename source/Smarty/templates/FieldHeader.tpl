{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@82419 *}
{if $NOLABEL neq true}
	<div {if $OLD_STYLE eq true}style="float:left; padding-top:5px;"{/if}>	{* crmv@57221 *}
		{if $uitype eq 23 || $uitype eq 5 || $uitype eq 6}
			{assign var="labelfor" value="jscal_field_$fldname"}
		{else}
			{assign var="labelfor" value=$fldname}
		{/if}
		<label for="{$labelfor}" class="dvtCellLabel" {if $AJAXEDITTABLEPERM}ondblclick="{if !empty($AJAXONCLICKFUNCT)}{$AJAXONCLICKFUNCT}{else}hndMouseClick{/if}({$keyid},'{$label}','{$keyfldname}',jQuery('#fieldCont_{$keyfldid}').get(0));"{/if}>
			{if $massedit eq '1'}
				{* crmv@109685 *}
				<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" {if $mass_edit_check}checked{/if}>
				<label for="{$fldname}_mass_edit_check" class="dvtCellLabel">
				{* crmv@109685e *}
			{/if}
			{$label}
			{if !empty($keycursymb) && ($uitype eq '71' || $uitype eq '72')}
				({$keycursymb})
			{/if}
			{if $massedit eq '1'}
				</label>
			{/if}
			{* vtlib customization: Help information for the fields *}
			{if $FIELDHELPINFO && $FIELDHELPINFO.$fldname}
				<i class="vteicon md-sm valign-bottom" onclick="vtlib_field_help_show(this, '{$fldname}');">help</i>
			{/if}
			{* END *}
		</label>
		{* crmv@57221 *}
		{if $OLD_STYLE eq false}
			{include file="FieldButtons.tpl"}
		{/if}
		{* crmv@57221e *}
		<div id="editbutton_{$label}" style="float:right;"></div>
		{* crmv@92272 crmv@106857 *}
		{if $smarty.request.enable_editoptions eq 'yes'}
			{assign var="editoptionsfieldnames" value='|'|explode:$smarty.request.editoptionsfieldnames}
			<i id="tablefields_seq_btn_{$fldname}" class="vteicon md-link" style="float:right; display:none;" onclick="ActionUpdateScript.insertTableFieldValue(this,'{$fldname}','seq')">input</i>
			<input type="text" id="tablefields_seq_{$fldname}" size="2" style="padding-left:5px; float:right; display:none;">
			<div class="tablefields_options" id="tablefields_options_{$fldname}" style="float:right; display:none;">
				<select class="populateField" onchange="ActionUpdateScript.changeTableFieldOpt(this,'{$fldname}')">
					{include file="Settings/ProcessMaker/actions/TablefieldsOptions.tpl"}
				</select>
			</div>
			{if $fldname|in_array:$editoptionsfieldnames}
				<div class="editoptions" fieldname="{$fldname}" style="float:right;"></div>
			{/if}
		{/if}
		{* crmv@92272e crmv@106857e *}
	</div>
{/if}