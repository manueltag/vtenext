{* crmv@38592 *}
{assign var=fields value=$FOCUS.column_fields}

<div id="mailHeader" style="width:100%;padding:4px;background-color:#E6ECF1">

<table width="100%" cellpadding="0" cellspacing="0" align="center">
	<tr><td>
		<table width="100%" cellpadding="2" cellspacing="0" border="0" align="center">
			<tr>
				<td align="right" valign="top" width="20%"><b>{"Subject"|getTranslatedString:'Messages'}:&nbsp;&nbsp;</b></td>
				<td align="left" valign="top">{$TEMPLATEINFO.subject}</td>
			</tr>
			<tr>
				<td align="right" valign="top" width="20%"><b>{"From"|getTranslatedString:'Messages'}:&nbsp;&nbsp;</b></td>
				<td align="left" valign="top">{$NEWSLETTERINFO.from_address}</td>
			</tr>
			{if $TO_ADDRESS neq ''}
			<tr class="mailHeader">
				<td align="right" valign="top" width="20%"><b>{"To"|getTranslatedString:'Messages'}:&nbsp;&nbsp;</b></td>
				<td align="left" valign="top">{if $TO_NAME neq ''}"{$TO_NAME}" {/if}&lt;{$TO_ADDRESS}&gt;</td>
			</tr>
			{/if}

	</table>
	</td>
	</tr>
</table>
</div>

<div id="messageBodyId" style="padding:10px">
{$TEMPLATEINFO.body}
</div>
