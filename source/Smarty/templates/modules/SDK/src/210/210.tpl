{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@39110 crmv@56051 *}
{if $sdk_mode eq 'detail'}
	{include file="FieldHeader.tpl" uitype=$keyid mandatory=$keymandatory label=$label}
	<div class="dvtCellInfoOff">
		{$keyval}
	</div>
{elseif $sdk_mode eq 'edit'}
	{if $readonly eq 99}
		{include file="FieldHeader.tpl" uitype=$uitype mandatory=$keymandatory label=$fldlabel}
		<div class="{$DIVCLASS}">
			<textarea name="{$fldname}" tabindex="{$vt_tab}" class="detailedViewTextBox" style="display:none">{$fldvalue}</textarea>
			{$fldvalue}
		</div>
	{elseif $readonly eq 100}
		<textarea name="{$fldname}" tabindex="{$vt_tab}" class="detailedViewTextBox" style="display:none">{$fldvalue}</textarea>
	{else}
		{include file="FieldHeader.tpl" uitype=$uitype mandatory=$keymandatory label=$fldlabel massedit=$MASS_EDIT}
		<div class="{$DIVCLASS}">
			{if $MOBILE eq 'yes'}
				{assign var=cols value="25"}
			{else}
				{assign var=cols value="90"}
			{/if}
			<textarea class="detailedViewTextBox" tabindex="{$vt_tab}" onFocus="this.className='detailedViewTextBoxOn'" name="{$fldname}"  onBlur="this.className='detailedViewTextBox'" cols="{$cols}" rows="8">{$fldvalue}</textarea>
			{if $FCKEDITOR_DISPLAY eq 'true'}
				{* crmv@42752 *}
				<script type="text/javascript">
					/* this is to have it working inside popups */
					window.CKEDITOR_BASEPATH = 'include/ckeditor/';
				</script>
				{* crmv@42752e *}
				<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
				<script type="text/javascript">
					var current_language_arr = "{php} echo $_SESSION['authenticated_user_language']; {/php}".split("_");
					var curr_lang = current_language_arr[0];
					jQuery(document).ready(function() {ldelim}
						if (CKEDITOR.instances['{$fldname}']) CKEDITOR.instances['{$fldname}'].destroy(true);	//crmv@56883
						CKEDITOR.replace('{$fldname}', {ldelim}
							filebrowserBrowseUrl: 'include/ckeditor/filemanager/index.html',
							toolbar : 'Basic',
							language : curr_lang
						{rdelim});
					{rdelim});
				</script>
			{/if}
		</div>
	{/if}
{/if}