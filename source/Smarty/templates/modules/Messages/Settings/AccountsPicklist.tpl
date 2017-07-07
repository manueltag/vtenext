{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
<script language="JavaScript" type="text/javascript" src="modules/Messages/Settings/Settings.js"></script>
{if !empty($ACCOUNTS)}
	{if $ACCOUNTS|@count eq 1}
		{assign var=style value="display:none;"}
	{/if}
	<span class="small" style="{$style}">{'LBL_ACCOUNTS'|getTranslatedString:'Messages'}</span>
	<select id="accountspicklist" name="accountspicklist" class="small" style="{$style}" {if !empty($JS_FUNCT)}onChange="{$JS_FUNCT}()"{/if}>
		{foreach item=ACCOUNT from=$ACCOUNTS}
			<option value="{$ACCOUNT.id}" {if $SEL_ACCOUNT eq $ACCOUNT.id}selected{/if}>{if !empty($ACCOUNT.description)}{$ACCOUNT.description}{else}{$ACCOUNT.server}{/if}</option>
		{/foreach}
	</select>
{/if}