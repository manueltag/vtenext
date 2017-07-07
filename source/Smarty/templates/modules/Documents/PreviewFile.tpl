{* crmv@62414 *}
{if $FILE_STATUS eq 1}
	{if $FILE_SUPPORTED}
		<table cellpadding="0" cellspacing="0" border="0" style="width:100%;">
			<tr>
				<td align="center" class="rightMailMergeContent">
					<input type="button" class="crmbutton small edit" onclick="{$JS_ACTION}('{$RECORD}');" value="{$MOD.DOC_PREVIEW_BUTTON}" />
				</td>
			</tr>
		</table>
	{else}
		{$MOD.DOC_NOT_SUPP}
	{/if}
{else}
	{$MOD.DOC_NOT_ACTIVE}
{/if}