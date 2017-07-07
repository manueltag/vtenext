{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}

{* crmv@2043m crmv@56233 *}

{if $sdk_mode eq 'detail'}
	{include file="FieldHeader.tpl" uitype=$keyid mandatory=$keymandatory label=$label}
	<div class="dvtCellInfoOff">
		<input type="hidden" name="{$keyfldname}" value="{$keyval}">
		{$keyoptions}
	</div>
{elseif $sdk_mode eq 'edit'}
	{include file="DisplayFieldsHidden.tpl" uitype=1}
{/if}