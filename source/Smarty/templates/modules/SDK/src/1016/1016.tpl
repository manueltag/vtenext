{* /*+*************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/ *}

{* crmv@104567 *}

{literal}
<style>
	.signatureImg {
		height: 150px;
	}
</style>
{/literal}
 
{if $sdk_mode eq 'detail'}
	<table width="100%">
		<tr>
			{if $keyval eq $MOD.NO_SIGNATURE_IMAGE}
				<td>{$MOD.NO_SIGNATURE_IMAGE}</td>
			{else}
				{assign var=now value=$smarty.now}
				{assign var=unique_name value=$keyval|md5}
				
				<td align="center" valign="center"><img class="img-responsive signatureImg" src="{$keyval}?t={$now}" id="img_signature_{$unique_name}" /></td>
			{/if}
		</tr>
	</table>
{elseif $sdk_mode eq 'edit'}
	<table style="width:100%;">
		<tr>
			{if $fldvalue eq $MOD.NO_SIGNATURE_IMAGE}
				<td>{$MOD.NO_SIGNATURE_IMAGE}</td>
			{else}
				{assign var=now value=$smarty.now}
				{assign var=unique_name value=$fldvalue|md5}
				<td align="center" valign="center"><img class="img-responsive signatureImg" src="{$fldvalue}?t={$now}" id="img_signature_{$unique_name}" /></td>
			{/if}
		</tr>
	</table>
	<input type="hidden" name="{$fldname}" value="{$fldvalue}" />
{/if}