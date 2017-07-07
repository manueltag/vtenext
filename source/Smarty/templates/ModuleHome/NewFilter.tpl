{* crmv@83340 *}

{if count($QFILTERS) > 0}
	<table width="100%" cellspacing="5" cellpadding="2" border="0">
		<tr>
			<td align="right" width="50%">
				{$APP.LBL_FILTER}
			</td>
			<td align="left" width="50%">
			<select name="select_filter" id="select_filter">
				{$QFILTERS}
			</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right" colspan="2">
				<button type="button" class="crmbutton save" onclick="ModuleHome.addBlock('{$MODHOMEID}', 'Filter')">{$APP.LBL_SAVE_LABEL}</button>
			</td>
		</tr>
	</table>
{else}
	<center>
		<p>{$APP.LBL_NO_AVAILABLE_FILTERS}</p>
	</center>
{/if}