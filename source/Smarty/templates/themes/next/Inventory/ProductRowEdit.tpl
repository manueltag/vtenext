{* crmv@42024 - all file  - righe per prodotti *}
{foreach key=row_no item=data from=$ASSOCIATEDPRODUCTS name=outer1}
	{assign var="deleted" value="deleted"|cat:$row_no}
	{assign var="hdnProductId" value="hdnProductId"|cat:$row_no}
	{assign var="productName" value="productName"|cat:$row_no}
	{assign var="comment" value="comment"|cat:$row_no}
	{assign var="productDescription" value="productDescription"|cat:$row_no}
	{assign var="qtyInStock" value="qtyInStock"|cat:$row_no}
	{assign var="qty" value="qty"|cat:$row_no}
	{assign var="listPrice" value="listPrice"|cat:$row_no}
	{assign var="productTotal" value="productTotal"|cat:$row_no}
	{assign var="subproduct_ids" value="subproduct_ids"|cat:$row_no}
	{assign var="subprod_names" value="subprod_names"|cat:$row_no}
	{assign var="entityIdentifier" value="entityType"|cat:$row_no}
	{assign var="entityType" value=$data.$entityIdentifier}

	{assign var="discount_type" value="discount_type"|cat:$row_no}
	{assign var="discount_percent" value="discount_percent"|cat:$row_no}
	{assign var="checked_discount_percent" value="checked_discount_percent"|cat:$row_no}
	{assign var="style_discount_percent" value="style_discount_percent"|cat:$row_no}
	{assign var="discount_amount" value="discount_amount"|cat:$row_no}
	{assign var="checked_discount_amount" value="checked_discount_amount"|cat:$row_no}
	{assign var="style_discount_amount" value="style_discount_amount"|cat:$row_no}
	{assign var="checked_discount_zero" value="checked_discount_zero"|cat:$row_no}

	{assign var="discountTotal" value="discountTotal"|cat:$row_no}
	{assign var="totalAfterDiscount" value="totalAfterDiscount"|cat:$row_no}
	{assign var="taxTotal" value="taxTotal"|cat:$row_no}
	{assign var="netPrice" value="netPrice"|cat:$row_no}
	{assign var="netPriceInput" value="netPriceInput"|cat:$row_no} {* crmv@29686 *}

	{assign var="hdnProductcode" value="hdnProductcode"|cat:$row_no}	<!-- crmv@16267 -->
	{assign var="unit_cost" value="unit_cost"|cat:$row_no}	{* crmv@44323 *}
	{assign var="margin" value="margin"|cat:$row_no}	{* crmv@44323 *}

	<tr id="row{$row_no}" valign="top">

	<!-- column 1 - delete link - starts -->
	<td class="crmTableRow lineOnTop">
		{if $row_no neq 1}
			<i class="vteicon md-link" onclick="deleteRow('{$MODULE}',{$row_no},'{$IMAGE_PATH}')">delete</i>
		{/if}<br/><br/>
		{if $row_no neq 1}
			<a href="javascript:moveUpDown('UP','{$MODULE}',{$row_no})" title="Move Upward"><i class="vteicon">arrow_upward</i></a>
		{/if}
		{if not $smarty.foreach.outer1.last}
			<a href="javascript:moveUpDown('DOWN','{$MODULE}',{$row_no})" title="Move Downward"><i class="vteicon">arrow_downward</i></a>
		{/if}
		<input type="hidden" id="{$deleted}" name="{$deleted}" value="0">
	</td>

	<!-- column 2 - Product Name - starts -->
	<!-- crmv@16267 -->
	<td class="crmTableRow lineOnTop">
		<!-- Product Re-Ordering Feature Code Addition Starts -->
		<input type="hidden" name="hidtax_row_no{$row_no}" id="hidtax_row_no{$row_no}" value="{$tax_row_no}"/>
		<!-- Product Re-Ordering Feature Code Addition ends -->
		<table width="100%"  border="0" cellspacing="0" cellpadding="1">
		   <tr>
				<td class="" valign="top">
					{* crmv@29190 *}
					{assign var=fld_displayvalue value=$data.$productName}
					<div {if $fld_displayvalue|trim eq ''}class="dvtCellInfo"{else}class="dvtCellInfoOff"{/if} style="position:relative"> {* crmv@97216 *}
						{assign var=fld_style value='class="detailedViewTextBox" readonly'}
						{if $fld_displayvalue|trim eq ''}
							{assign var=fld_displayvalue value='LBL_SEARCH_STRING'|getTranslatedString}
							{assign var=fld_style value='class="detailedViewTextBox"'}
						{/if}
						<input type="text" id="{$productName}" name="{$productName}" value="{$fld_displayvalue|htmlentities}" {$fld_style} style="width: 80%;" />	{* crmv@55229 *}
						<input type="hidden" id="{$hdnProductId}" name="{$hdnProductId}" value="{$data.$hdnProductId}" />
						<input type="hidden" id="lineItemType{$row_no}" name="lineItemType{$row_no}" value="{$entityType}" />
						<input type="hidden" id="{$unit_cost}" name="{$unit_cost}" value="{$data.$unit_cost}" /> {* crmv@44323 *}
						<div class="dvtCellInfoImgRx">
							{if $entityType eq 'Services'}
								<img id="searchIcon{$row_no}" title="Services" src="themes/images/modulesimg/Services.png" style="cursor: pointer;" align="absmiddle" onclick="servicePickList(this,'{$MODULE}','{$row_no}')" width="23" />
							{else}
								<img id="searchIcon{$row_no}" title="Products" src="{'InventoryProducts.png'|@vtiger_imageurl:$THEME}" style="cursor: pointer;" align="absmiddle" onclick="productPickList(this,'{$MODULE}','{$row_no}')" width="23" />
							{/if}
						</div>
						<script type="text/javascript" id="script{$row_no}">
							initAutocompleteInventoryRow('{$entityType}','hdnProductId{$row_no}','productName{$row_no}',getObj('searchIcon{$row_no}'),'{$MODULE}',{$row_no},'yes');
						</script>
					</div>
					{* crmv@29190e *}
				</td>
			</tr>
			<tr>
				<td class="" id="viewproductcode">
					<div class="dvtCellInfoOff">
						<textarea id="{$hdnProductcode}" name="{$hdnProductcode}" class="detailedViewTextBox" style="height:23px;margin-top:2px;" readonly>{$data.$hdnProductcode}</textarea> {* crmv@112198 *}
					</div>
				</td>
			</tr>
		   	<tr>
				<td class="" id="viewdescription">
					<div class="dvtCellInfo">
						<textarea id="{$productDescription}" name="{$productDescription}" class="detailedViewTextBox" style="height:80px" placeholder="{'Description'|getTranslatedString:$MODULE}">{$data.$productDescription}</textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td class="">
					<input type="hidden" value="{$data.$subproduct_ids}" id="{$subproduct_ids}" name="{$subproduct_ids}" />
					<span id="{$subprod_names}" name="{$subprod_names}" style="font-style:italic">{$data.$subprod_names}</span>
				</td>
			</tr>
			<tr>
				<td class="" id="setComment">
					<div class="dvtCellInfo" style="position:relative"> {* crmv@97216 *}
						<textarea id="{$comment}" name="{$comment}" class="detailedViewTextBox" style="height:40px" placeholder="{'Comments'|getTranslatedString:$MODULE}">{$data.$comment}</textarea>
						<div class="dvtCellInfoImgRx">
							<img src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" onClick="{literal}${/literal}('{$comment}').value=''"; style="cursor:pointer;" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</td>
	<!-- crmv@16267e -->
	<!-- column 2 - Product Name - ends -->

	<!-- column 3 - Quantity in Stock - starts -->
	{if $MODULE neq 'PurchaseOrder'}	<!-- crmv@18498 -->
	   <td class="crmTableRow lineOnTop" valign="top">
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<div class="dvtCellInfo">
						<div id="{$qtyInStock}" style="padding:3px 0;">{$data.$qtyInStock|formatUserNumber}</div>
					</div>
				</td>
			</tr>
		</table>
	   </td>
	{/if}
	<!-- column 3 - Quantity in Stock - ends -->

	<!-- column 4 - Quantity - starts -->
	<td class="crmTableRow lineOnTop" valign="top">
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<div class="dvtCellInfo">
						<input id="{$qty}" name="{$qty}" type="text" class="detailedViewTextBox" onChange="settotalnoofrows(); calcTotal(); {if $MODULE eq 'Invoice' && $entityType neq 'Services'} stock_alert('{$row_no}');{/if} setDiscount(this,'{$row_no}');" value="{$data.$qty|formatUserNumber}"/><br><span id="stock_alert{$row_no}"></span> {* crmv@111712 *}
					</div>
				</td>
			</tr>
		</table>
	</td>
	<!-- column 4 - Quantity - ends -->

	<!-- column 5 - List Price with Discount, Total After Discount and Tax as table - starts -->
	<td class="crmTableRow lineOnTop" align="right" valign="top">
		<table width="100%" cellpadding="0" cellspacing="0">
		   <tr>
			<td align="right">
				<div class="dvtCellInfo" style="position:relative"> {* crmv@97216 *}
					<input id="{$listPrice}" name="{$listPrice}" value="{$data.$listPrice|formatUserNumber}" type="text" class="detailedViewTextBox" onChange="calcTotal(); setDiscount(this,'{$row_no}');callTaxCalc('{$row_no}');calcTotal(); "/> {* crmv@112748 *}
					<div class="dvtCellInfoImgRx">
						<img src="{'pricebook.gif'|@vtiger_imageurl:$THEME}" onclick="priceBookPickList(this,'{$row_no}')" align="absmiddle" style="cursor:pointer">
					</div>
				</div>
			</td>
		   </tr>
		   <tr>
			<td align="right" style="padding:5px;" nowrap>
				(-)&nbsp;<b><a href="javascript:void(0);" onClick="displayCoords(this,'discount_div{$row_no}','discount','{$row_no}')" >{$APP.LBL_DISCOUNT}</a> : </b>
				<div class="discountUI" id="discount_div{$row_no}">
					<input type="hidden" id="discount_type{$row_no}" name="discount_type{$row_no}" value="{$data.$discount_type}">
					<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
					   <tr>
						<td id="discount_div_title{$row_no}" class="level3Bg" colspan="2" nowrap align="left" ></td>
					   </tr>
					   {* crmv@2539m *}
					   <tr>
						<td align="left" class="lineOnTop"><input type="radio" name="discount{$row_no}" {$data.$checked_discount_zero} onclick="setDiscount(this,'{$row_no}'); callTaxCalc('{$row_no}');calcTotal();" id="discount_zero_{$row_no}">&nbsp;<label for="discount_zero_{$row_no}">{$APP.LBL_ZERO_DISCOUNT}</label></td>
						<td class="lineOnTop">&nbsp;</td>
					   </tr>
					   <tr>
						<td align="left"><input type="radio" name="discount{$row_no}" onclick="setDiscount(this,'{$row_no}'); callTaxCalc('{$row_no}'); calcTotal();" {$data.$checked_discount_percent} id="discount_percent_{$row_no}">&nbsp;<label for="discount_percent_{$row_no}">% {$APP.LBL_OF_PRICE}</label>&nbsp;<i class="vteicon md-link md-text md-sm" onclick="vtlib_field_help_show(this,'hdnDiscountPercent');">help</i></td>
						<td align="right"><input type="text" class="" size="8" id="discount_percentage{$row_no}" name="discount_percentage{$row_no}" value="{$data.$discount_percent}" {$data.$style_discount_percent} onChange="setDiscount(this,'{$row_no}'); callTaxCalc('{$row_no}'); calcTotal();">&nbsp;%</td>
					   </tr>
					   <tr>
						<td align="left" nowrap><input type="radio" name="discount{$row_no}" onclick="setDiscount(this,'{$row_no}'); callTaxCalc('{$row_no}'); calcTotal();" {$data.$checked_discount_amount} id="discount_amount_{$row_no}">&nbsp;<label for="discount_amount_{$row_no}">{$APP.LBL_DIRECT_PRICE_REDUCTION}</label></td>
						<td align="right"><input type="text" class="" size="8" id="discount_amount{$row_no}" name="discount_amount{$row_no}" value="{$data.$discount_amount|formatUserNumber}" {$data.$style_discount_amount} onChange="setDiscount(this,{$row_no}); callTaxCalc('{$row_no}'); calcTotal();">&nbsp;&nbsp;&nbsp;&nbsp;</td>
					   </tr>
					   {* crmv@2539me *}
					</table>
					<div class="closebutton" onClick="fnHidePopDiv('discount_div{$row_no}')"></div>
				</div>
			</td>
		   </tr>
		   <tr>
			<td align="right" style="padding:5px;" nowrap>
				<b>{$APP.LBL_TOTAL_AFTER_DISCOUNT} :</b>
			</td>
		   </tr>
		   <tr id="individual_tax_row{$row_no}" class="TaxShow">
			<td align="right" style="padding:5px;" nowrap>
				(+)&nbsp;<b><a href="javascript:void(0);" onClick="displayCoords(this,'tax_div{$row_no}','tax','{$row_no}')" >{$APP.LBL_TAX} </a> : </b>
				<div class="discountUI" id="tax_div{$row_no}">
					<!-- we will form the table with all taxes -->
					{include file="Inventory/ProductTaxDetail.tpl"}
				</div>
				<!-- This above div is added to display the tax informations -->
			</td>
		   </tr>
		   {* crmv@44323 *}
		   <tr>
		   	<td align="right" style="padding:5px;"><b>{$APP.LBL_MARGIN} :</b></td>
		   </tr>
		   {* crmv@44323e *}
		</table>
	</td>
	<!-- column 5 - List Price with Discount, Total After Discount and Tax as table - ends -->

	<!-- column 6 - Product Total - starts -->
	<td class="crmTableRow lineOnTop" align="right">
		<table width="100%" cellpadding="0" cellspacing="0">
		   <tr>
			<td align="right">
				<div class="dvtCellInfo">
					<div id="productTotal{$row_no}" style="padding:3px 0;">{$data.$productTotal|formatUserNumber}</div>
				</div>
			</td>
		   </tr>
		   <tr>
			<td align="right">
				<div class="dvtCellInfo">
					<div id="discountTotal{$row_no}" style="padding:3px 0;">{$data.$discountTotal|formatUserNumber}</div>
				</div>
			</td>
		   </tr>
		   <tr>
			<td align="right">
				<div class="dvtCellInfo">
					<div id="totalAfterDiscount{$row_no}" style="padding:3px 0;">{$data.$totalAfterDiscount|formatUserNumber}</div>
				</div>
			</td>
		   </tr>
		   <tr>
			<td align="right">
				<div class="dvtCellInfo">
					<div id="taxTotal{$row_no}" style="padding:3px 0;">{$data.$taxTotal|formatUserNumber}</div>
				</div>
			</td>
		   </tr>
		   <tr>
			<td align="right">
				<div class="dvtCellInfo">
					<div id="margin{$row_no}" style="padding:3px 0;">{if $data.$margin neq ''}{$data.$margin*100|round}%{else}{/if}</div>
				</div>
			</td> {* crmv@44323 *}
		   </tr>
		</table>
	</td>
	<!-- column 6 - Product Total - ends -->

	<!-- column 7 - Net Price - starts -->
	<td valign="bottom" class="crmTableRow lineOnTop" align="right">
		<table width="100%" cellpadding="0" cellspacing="0">
		   <tr>
			<td align="right">
				<div class="dvtCellInfo">
					<div id="netPrice{$row_no}"><b>{$data.$netPrice|formatUserNumber}</b></div>
					<input type="hidden" id="{$netPriceInput}" name="{$netPriceInput}" value="{$data.$netPrice}" /> {* crmv@29686 *}
				</div>
			</td>
		  </tr>
		</table>
	</td>
	<!-- column 7 - Net Price - ends -->

	</tr>
   <!-- Product Details First row - Ends -->
{/foreach}