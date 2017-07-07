{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
 
{* crmv@3085m crmv@3086m crmv@105588 *}

<div {if !empty($CARDID)}id="{$CARDID}"{/if} style="padding-top:5px;{if !empty($DISPLAY)}display:{$DISPLAY};{/if}" {if !empty($CARDONCLICK)}onClick="{$CARDONCLICK}"{/if}>
	<table cellspacing="0" cellpadding="0" width="100%" class="previewEntity" {if empty($CARDONCLICK)}style="cursor:auto;"{/if}>
		<tr>
			<td class="cardLabel" align="left">
				{if !empty($PREFIX)}
					<span class="gray vcenter">{$PREFIX}</span>
				{/if}
				<span class="vcenter">{$CARDNAME}</span>
			</td>
			<td class="cardIcon" align="right">
				{* crmv@98866 *}
				{assign var="moduleLower" value=$CARDMODULE|strtolower}
				{assign var="firstLetter" value=$CARDMODULE_LBL|substr:0:1|strtoupper}
				<div class="vcenter">{$CARDMODULE_LBL}</div>
				<div class="vcenter">
					<i class="vteicon icon-module icon-{$moduleLower}" data-first-letter="{$firstLetter}"></i>
				</div>
				{* crmv@98866e *}
			</td>
		</tr>
		{if !empty($CARDDETAILS)}
			<tr>
				<td colspan="2" class="cardContent">
					<table class="table borderless">
						{foreach key=fieldname item=detail from=$CARDDETAILS}
							<tr>
								<td>
									<span class="fieldLabel">{$detail.label}</span>
									&nbsp;
									<span class="fieldValue">{$detail.value}</span>
								</td>
							</tr>
						{/foreach}
					</table>
				</td>
			</tr>
		{/if}
	</table>
</div>
