{*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 *********************************************************************************}
 
{* crmv@104568 *}

{if $smarty.request.load_header eq 'yes'}
	<div relation_id="{$RELATIONID}" style="padding:5px;" id="container_{$MODULE}_{$HEADER|replace:' ':''}" data-relationid="{$RELATIONID}" {if $FIXED}data-isfixed="1"{/if}>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small lvt">	{* crmv@26896 *} {* crmv@62415 *}
		<tr>
			<td class="dvInnerHeader">
				<div style="float:left;">
					{assign var="related_module" value=$RELATED_MODULE}
					{assign var="related_module_lower" value=$related_module|strtolower}
					{assign var="trans_related_module" value=$RELATED_MODULE|@getTranslatedString:$RELATED_MODULE}
					{assign var="first_letter" value=$trans_related_module|substr:0:1|strtoupper}
				
					<div class="vcenter" style="margin-right:5px">
						<i class="vteicon icon-module icon-{$related_module_lower}" data-first-letter="{$first_letter}"></i>				
					</div>
					
					{* crmv@64792 *}
					{if empty($RELATED_MODULE)}
						<div class="vcenter"><b>{$HEADER|@getTranslatedString:$RELATED_MODULE}</b></div>
					{else}
						<div class="vcenter"><b>{$RELATED_MODULE|@getTranslatedString:$RELATED_MODULE}</b></div>
					{/if}
					{* crmv@64792e *}
					
					<span class="vcenter" id="cnt_{$MODULE}_{$HEADER|replace:' ':''}"></span> {* crmv@25809 *}
					- <span class="vcenter" id="dtl_{$MODULE}_{$HEADER|replace:' ':''}" style="font-weight:normal">{'LBL_LIST'|@getTranslatedString}</span>	{* crmv@3086m *}
					&nbsp;{include file="LoadingIndicator.tpl" LIID="indicator_"|cat:$MODULE|cat:"_"|cat:$HEADER|replace:' ':'' LIEXTRASTYLE="display:none;"}
				</div>
				<div style="float:right;">
					{if !$FIXED}
					<i class="vteicon2 fa-thumb-tack md-link" id="pin_{$MODULE}_{$HEADER|replace:' ':''}" style="display:none;" onClick="pinRelated('{$MODULE}_{$HEADER|replace:' ':''}','{$MODULE}','{$RELATED_MODULE}');"></i>
					<i class="vteicon2 fa-thumb-tack md-link" id="unPin_{$MODULE}_{$HEADER|replace:' ':''}" style="{if $PIN eq true}display:block;{else}display:none;{/if}opacity:0.5" onClick="unPinRelated('{$MODULE}_{$HEADER|replace:' ':''}','{$MODULE}','{$RELATED_MODULE}');"></i>
					<i class="vteicon md-link valign-bottom" id="hideDynamic_{$MODULE}_{$HEADER|replace:' ':''}" style="display:none" onClick="hideDynamicRelatedList(jQuery('#tl_{$MODULE}_{$HEADER|replace:' ':''}'));">clear</i>
					{/if}
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div relation_id="{$RELATIONID}" id="tbl_{$MODULE}_{$HEADER|replace:' ':''}"> {* crmv@62415 *}
{/if}

<table border=0 cellspacing=0 cellpadding=0 width=100% class="small" 
	style="border-bottom:1px solid #d2d2d2;padding:5px; background-color: #ffffff;">
	<tr>
		<td width="40%" align="left">
			{$RELATEDLISTDATA.navigation.0}
			{* crmv@22700 *}
			{php}if (isModuleInstalled('Newsletter')) { {/php}
				{assign var="CUSTOM_MODULE" value="Targets"}
			{php}} else {{/php}
				{assign var="CUSTOM_MODULE" value="Campaigns"}
			{php}}{/php}
			{if $MODULE eq $CUSTOM_MODULE && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 
				'Leads' || $RELATED_MODULE eq 'Accounts') && $RELATEDLISTDATA.entries|@count > 0}
				<br>{$APP.LBL_SELECT_BUTTON_LABEL}: <a href="javascript:void(0);"
					onclick="clear_checked_all('{$RELATED_MODULE}');">{$APP.LBL_NONE_NO_LINE}</a>
			{/if}
		</td>
		<td width="20%" align="center" nowrap>{$RELATEDLISTDATA.navigation.1} </td>
		<td width="40%" align="right">
			{$RELATEDLISTDATA.CUSTOM_BUTTON}
			{* crmv@22700 *}
			{if $HEADER eq 'Contacts' && $MODULE neq $CUSTOM_MODULE && $MODULE neq 'Accounts' && $MODULE neq 'Potentials' && $MODULE neq 'Products' && $MODULE neq 'Vendors' && $MODULE neq 'Fairs'}	{* crmv@2285m *}
				{if $MODULE eq 'Calendar'}
					<input alt="{$APP.LBL_SELECT_CONTACT_BUTTON_LABEL}" title="{$APP.LBL_SELECT_CONTACT_BUTTON_LABEL}" accessKey="" class="crmbutton small edit" value="{$APP.LBL_SELECT_BUTTON_LABEL} {$APP.Contacts}" LANGUAGE=javascript onclick='openPopup("index.php?module=Contacts&return_module={$MODULE}&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid={$ID}{$search_string}","test","width=640,height=602,resizable=0,scrollbars=0");' type="button"  name="button"></td>{*crmv@21048m*}
				{/if}
			{elseif $HEADER eq 'Users' && $MODULE eq 'Calendar'}
				<input title="Change" accessKey="" tabindex="2" type="button" class="crmbutton small edit" value="{$APP.LBL_SELECT_USER_BUTTON_LABEL}" name="button" LANGUAGE=javascript onclick='openPopup("index.php?module=Users&return_module=Calendar&return_action={$return_modname}&activity_mode=Events&action=Popup&popuptype=detailview&form=EditView&form_submit=true&select=enable&return_id={$ID}&recordid={$ID}","test","width=640,height=525,resizable=0,scrollbars=0")';>{* crmv@21048m *}
            {/if}
		</td>
	</tr>
</table>

<table border=0 cellspacing=1 cellpadding=3 width=100% style="background-color:#eaeaea;" class="small">
	{if $RELATEDLISTDATA.entries|@count > 0}
		<tr style="height:25px" bgcolor=white>
			{* crmv@22700 *}
	        {if $MODULE eq $CUSTOM_MODULE && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 'Leads' || $RELATED_MODULE eq 'Accounts')
				&& $RELATEDLISTDATA.entries|@count > 0}
			<td class="lvtCol">
				<input name ="{$RELATED_MODULE}_selectall" onclick="rel_toggleSelect(this.checked,'{$RELATED_MODULE}_selected_id','{$RELATED_MODULE}');"  type="checkbox">
			</td>
	        {/if}
			{foreach key=index item=_HEADER_FIELD from=$RELATEDLISTDATA.header}
			<td class="lvtCol">{$_HEADER_FIELD}</td>
			{/foreach}
		</tr>
	{/if}
	{foreach key=_RECORD_ID item=_RECORD from=$RELATEDLISTDATA.entries}
		{* crmv@80758 *}
		{if isset($_RECORD.clv_color)}
			{assign var=color value=$_RECORD.clv_color}
		{else}
			{assign var=color value=""}
		{/if}
		{* crmv@80758e *}
		<!-- crmv@17408 -->
		{assign var=header_rep value=$HEADER|replace:' ':''}
		{if $header_rep eq 'TicketHistory'}
			{assign var=color value=""}
		{/if}
		<!-- crmv@17408e -->
		<tr bgcolor=white>
			{* crmv@22700 *}
        	{if $MODULE eq $CUSTOM_MODULE && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 'Leads' || $RELATED_MODULE eq 'Accounts')}
			<td><input name="{$RELATED_MODULE}_selected_id" id="{$_RECORD_ID}" value="{$_RECORD_ID}" onclick="rel_check_object(this,'{$RELATED_MODULE}');" type="checkbox" {$RELATEDLISTDATA.checked.$_RECORD_ID}></td>	{*<!-- crmv@19139 -->*}
        	{/if}
			{foreach key=index item=_RECORD_DATA from=$_RECORD}
				 {* vtlib customization: Trigger events on listview cell *}
				 {if $index neq 'clv_color'  or $index eq '0'}
                 <td bgcolor="{$color}" onmouseover="vtlib_listview.trigger('cell.onmouseover', $(this))" onmouseout="vtlib_listview.trigger('cell.onmouseout', $(this))">{$_RECORD_DATA}</td>
                 {/if}
                 {* END *}
			{/foreach}
		</tr>
	{foreachelse}
		<tr style="height: 25px;" bgcolor="white"><td><i>{$APP.LBL_NONE_INCLUDED}</i></td></tr>
	{/foreach}
</table>

{if $smarty.request.load_header eq 'yes'}
				</div>
			</td>
		</tr>
	</table></div>
{/if}

{* crmv@26896 crmv@100492 *}
{if $PERFORMANCE_CONFIG.RELATED_LIST_COUNT eq true && $RELATEDLISTDATA.count != ''}
	<script type='text/javascript'>
	var target = "cnt_{$MODULE}_{$HEADER|replace:' ':''}";
	var count = {$RELATEDLISTDATA.count};
	jQuery('#'+target+'_tl').html("("+count+")");
	jQuery('#'+target).html("("+count+")");
	</script>
{/if}
{* crmv@26896e crmv@100492e *}

{if $MODULE eq $CUSTOM_MODULE && ($RELATED_MODULE eq 'Contacts' || $RELATED_MODULE eq 'Leads' || $RELATED_MODULE eq 'Accounts') && $RELATEDLISTDATA.entries|@count > 0 && $RESET_COOKIE eq 'true'}
	<script type='text/javascript'>set_cookie('{$RELATED_MODULE}_all', '');</script>
{/if}
