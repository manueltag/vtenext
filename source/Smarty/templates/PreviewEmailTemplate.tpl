{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@80155 *}
<table border=0 cellspacing=0 cellpadding=5 width=100% >
<tr>
	<td width=20% class="dvtCellLabel">{'LBL_NAME'|getTranslatedString:'Settings'}</td>
	<td width=80% class="small cellText">{$TEMPLATENAME}</td>
</tr>
<tr>
	<td valign=top class="dvtCellLabel">{'LBL_DESCRIPTION'|getTranslatedString:'Settings'}</td>
	<td class="cellText small" valign=top>{$DESCRIPTION}</td>
  </tr>
<tr>
	<td valign=top class="dvtCellLabel">{'LBL_FOLDER'|getTranslatedString:'Settings'}</td>
	<td class="cellText small" valign=top>{$FOLDERNAME}</td>
</tr>
{* crmv@22700 *}
<tr>
	<td width=20% class="dvtCellLabel">{'LBL_TYPE'|getTranslatedString}</td>
	<td width=80% class="small cellText">{$TEMPLATETYPE}</td>
</tr>
{* crmv@22700e *}
{if $TEMPLATETYPE eq 'Email'}
	<tr>
		<td width=20% class="dvtCellLabel">{'LBL_USE_SIGNATURE'|getTranslatedString:'Settings'}</td>
		<td width=80% class="small cellText">
			{if $USE_SIGNATURE eq 1}
			<i class="vteicon checkok nohover">check</i>
			{else}
			<i class="vteicon checkko nohover">clear</i>
			{/if}
		</td>
	</tr>
	<tr>
		<td width=20% class="dvtCellLabel">{'LBL_OVERWRITE_MESSAGE'|getTranslatedString:'Settings'}</td>
		<td width=80% class="small cellText">
			{if $OVERWRITE_MESSAGE eq 1}
			<i class="vteicon checkok nohover">check</i>
			{else}
			<i class="vteicon checkko nohover">clear</i>
			{/if}
		</td>
	</tr>
{/if}
{if $BU_MC_ENABLED}
	<tr>
		<td width=20% class="dvtCellLabel">Business Unit</td>
		<td width=80% class="small cellText">{$BU_MC}</td>
	</tr>
{/if}
<tr>
	<td colspan="2" valign=top class="cellText small">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="thickBorder">
			<tr>
				<td valign=top>
					<table width="100%"  border="0" cellspacing="0" cellpadding="5">
						<tr>
							<td colspan="2" valign="top" class="small" style="background-color:#cccccc"><strong>{'LBL_PREVIEW'|getTranslatedString}</strong></td>
						</tr>
						<tr>
							<td width="15%" valign="top" class="cellLabel small">{'LBL_SUBJECT'|getTranslatedString:'Settings'}</td>
							<td width="85%" class="cellText small">{$SUBJECT}</td>
						</tr>
						<tr>
							<td valign="top" class="cellLabel small">{'LBL_MESSAGE'|getTranslatedString:'Settings'}</td>
							<td class="cellText small">{$BODY}</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>