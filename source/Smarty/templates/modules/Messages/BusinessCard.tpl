{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}

{if $BUSINESS_CARD neq ''}
	<table cellspacing="0" cellpadding="2" width="100%" class="small">
		{if $BUSINESS_CARD.module eq 'Users'}
			{if $BUSINESS_CARD.title neq ''}
				<tr>
					<td>
						<img src="modules/Messages/src/img/ico_role.png" title="{'Role'|getTranslatedString:'Users'}" style="vertical-align:middle;">
					</td>
					<td>
						<b>{$BUSINESS_CARD.title}</b>
					</td>
				</tr>
			{/if}
		{elseif $BUSINESS_CARD.module eq 'Contacts'}
			{if $BUSINESS_CARD.accountname neq ''}
				<tr>
					<td colspan="2">
						<b><a href="javascript:;" onClick="preView('Accounts','{$BUSINESS_CARD.accountid}');">{$BUSINESS_CARD.accountname}</a></b>
					</td>
				</tr>
			{/if}
		{elseif $BUSINESS_CARD.module eq 'Accounts'}
			{if $BUSINESS_CARD.bill_city neq ''}
				<tr>
					<td colspan="2">
						{$BUSINESS_CARD.bill_city}
					</td>
				</tr>
			{/if}
		{elseif $BUSINESS_CARD.module eq 'Leads'}
			{if $BUSINESS_CARD.company neq ''}
				<tr>
					<td colspan="2">
						{$BUSINESS_CARD.company}
					</td>
				</tr>
			{/if}
			{if $BUSINESS_CARD.leadsource neq ''}
				<tr>
					<td>
						<img src="modules/Messages/src/img/ico_leadsource.png" title="{'Lead Source'|getTranslatedString:'Leads'}" style="vertical-align:middle;">
					</td>
					<td>
						{$BUSINESS_CARD.leadsource}
					</td>
				</tr>
			{/if}
		{elseif $BUSINESS_CARD.module eq 'Vendors'}
			{if $BUSINESS_CARD.website neq ''}
				<tr>
					<td colspan="2">
						<a href="http://{$BUSINESS_CARD.website}" target="_blank">{$BUSINESS_CARD.website}</a>
					</td>
				</tr>
			{/if}
		{/if}
		{if !empty($BUSINESS_CARD.phone)}
			<tr valign="top">
				<td width="15">
					<img src="modules/Messages/src/img/ico_phone.png" title="{'Phone'|getTranslatedString}" style="vertical-align:middle;">
				</td>
				<td>
					{foreach item=phone from=$BUSINESS_CARD.phone}
						{foreach item=value from=$phone.value}
							{if ''|@get_use_asterisk eq 'true'}
								<a href='javascript:;' title="{$phone.label}" onclick='startCall("{$value}", "{$BUSINESS_CARD.id}")'>{$value}</a>
							{else}
								<span title="{$phone.label}">{$value}</span>
							{/if}
							<br />
						{/foreach}
					{/foreach}
				</td>
			</tr>
		{/if}
	</table>
{/if}