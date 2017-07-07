{* crmv@42024 *}
<tr>
   	{if $MODULE neq 'PurchaseOrder'}
		<td colspan="3" class="dvInnerHeader">
	{else}
		<td colspan="2" class="dvInnerHeader">
	{/if}
		<b>{$APP.LBL_ITEM_DETAILS}</b>
	</td>

	<td class="dvInnerHeader" align="center" colspan="2">
		<input type="hidden" value="{$INV_CURRENCY_ID}" id="prev_selected_currency_id" />
		<b>{$APP.LBL_CURRENCY}</b>&nbsp;&nbsp;
		<select class="small" id="inventory_currency" name="inventory_currency" onchange="updatePrices();">
		{foreach item=currency_details key=count from=$CURRENCIES_LIST}
			{if $currency_details.curid eq $INV_CURRENCY_ID}
				{assign var=currency_selected value="selected"}
			{else}
				{assign var=currency_selected value=""}
			{/if}
			<OPTION value="{$currency_details.curid}" {$currency_selected}>{$currency_details.currencylabel|@getTranslatedCurrencyString} ({$currency_details.currencysymbol})</OPTION>
		{/foreach}
		</select>
	</td>

	<td class="dvInnerHeader" align="center" colspan="2">
		<b>{$APP.LBL_TAX_MODE}</b>&nbsp;&nbsp;
		{if $TAXTYPE eq 'group'}	{* crmv@50153 *}
			{assign var="group_selected" value="selected"}
		{else}
			{assign var="individual_selected" value="selected"}
		{/if}
		<select class="small" id="taxtype" name="taxtype" onchange="decideTaxDiv(); calcTotal();">
			<OPTION value="individual" {$individual_selected}>{$APP.LBL_INDIVIDUAL}</OPTION>
			<OPTION value="group" {$group_selected}>{$APP.LBL_GROUP}</OPTION> {* crmv@42024 *} {* crmv@50153 *}
		</select>
	</td>
   </tr>

	<!-- Header for the Product Details -->
   <tr valign="top">
	<td width=5% valign="top" class="lvtCol" align="right"><b>{$APP.LBL_TOOLS}</b></td>
	<td width=40% class="lvtCol"><font color='red'>*</font><b>{$APP.LBL_ITEM_NAME}</b></td>
	{if $MODULE neq 'PurchaseOrder'}
		<td width=10% class="lvtCol"><b>{$APP.LBL_QTY_IN_STOCK}</b></td>
	{/if}
	<td width=10% class="lvtCol"><b>{$APP.LBL_QTY}</b></td>
	<td width=10% class="lvtCol" align="right"><b>{$APP.LBL_LIST_PRICE}</b></td>
	<td width=12% nowrap class="lvtCol" align="right"><b>{$APP.LBL_TOTAL}</b></td>
	<td width=13% valign="top" class="lvtCol" align="right"><b>{$APP.LBL_NET_PRICE}</b></td>
</tr>