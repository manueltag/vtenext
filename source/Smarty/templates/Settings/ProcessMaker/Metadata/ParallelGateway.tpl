{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@103534 *}
{include file="Settings/ProcessMaker/Metadata/Header.tpl"}
<div style="padding:5px;">
	<form class="form-config-shape" shape-id="{$ID}">
		<table border="0" cellpadding="0" cellspacing="5" width="100%">
			<tr><td>
				<div class="dvtCellLabel">{$MOD.LBL_PM_GATEWAY_END_PARALLEL}</div>
				<div class="dvtCellInfo">
					<select name="closing_gateway" class="detailedViewTextBox">
						<option value="" {if $METADATA.closing_gateway eq ''}selected{/if}>{$APP.LBL_NONE}</option>
						{foreach key=k item=v from=$GATEWAY_LIST}
							<option value="{$k}" {if $k eq $METADATA.closing_gateway}selected{/if}>{$v}</option>
						{/foreach}
					</select>
				</div>
			</td></tr>
		</table>
	</form>
</div>