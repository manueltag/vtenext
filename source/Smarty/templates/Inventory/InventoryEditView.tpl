{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
*}

<!-- module header -->

{* crmv@119414 *}
{assign var=overrides value=$THEME_CONFIG.tpl_overrides}
{if !empty($overrides[$smarty.template])}
	{include file=$overrides[$smarty.template]}
	{php}return;{/php}
{/if}
{* crmv@119414e *}

<script type="text/javascript" src="{"include/js/Inventory.js"|resourcever}"></script>
<script type="text/javascript" src="modules/Services/Services.js"></script>
{if $MODULE eq 'Products' || $MODULE eq 'Services'}
	<script language="JavaScript" type="text/javascript" src="modules/{$MODULE}/multifile.js"></script>
{/if}
<script type="text/javascript">
function sensex_info()
{ldelim}
        var Ticker = $('tickersymbol').value;
        if(Ticker!='')
        {ldelim}
                $("vtbusy_info").style.display="inline";
                new Ajax.Request(
                      'index.php',
                      {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                                method: 'post',
                                postBody: 'module={$MODULE}&action=Tickerdetail&tickersymbol='+Ticker,
                                onComplete: function(response) {ldelim}
                                        $('autocom').innerHTML = response.responseText;
                                        $('autocom').style.display="block";
                                        $("vtbusy_info").style.display="none";
                                {rdelim}
                        {rdelim}
                );
        {rdelim}
{rdelim}
</script>
{if $HIDE_BUTTON_LIST neq '1'}
	{include file='Buttons_List1.tpl'} {* crmv@42752 *}
{/if}
<!-- Contents -->
{*<!-- crmv@18592 -->*}
<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
   <tr>
	<td valign=top></td>

	<td class="showPanelBg" valign=top width=100%>
		<!-- PUBLIC CONTENTS STARTS-->
		{include file='EditViewHidden.tpl'}
		{assign var=INVENTORY_VIEW value='true'}
		{if $HIDE_BUTTON_LIST neq '1'}
			{include file='Buttons_List_Edit.tpl'} {* crmv@42752 *}
		{/if}
		<div class="small">
			<!-- Account details tabs -->
			<table class="margintop" border=0 cellspacing=0 cellpadding=0 width=100% align=center> {* crmv@25128 *}
			   <tr>
				<td>
					{* crmv@104568e *}
					{if count($EDITTABS) > 1}
					<table border=0 cellspacing=0 cellpadding=3 width=100% class="small" id="EditViewTabs">
					   <tr>
						{if !empty($EDITTABS)}
							{foreach item=_tab from=$EDITTABS name="extraDetailForeach"}
								{if empty($_tab.href)}
									{assign var="_href" value="javascript:;"}
								{else}
									{assign var="_href" value=$_tab.href}
								{/if}
								{if $smarty.foreach.extraDetailForeach.iteration eq 1}
									{assign var="_class" value="dvtSelectedCell"}
								{else}
									{assign var="_class" value="dvtUnSelectedCell"}
								{/if}
								<td class="{$_class}" align="center" onClick="{$_tab.onclick}" nowrap="" data-panelid="{$_tab.panelid}"><a href="{$_href}">{$_tab.label}</a></td>
							{/foreach}
						{/if}
						<td class="dvtTabCache" align="right" style="width:100%"></td>
					   </tr>
					</table>
					{/if}
					{* crmv@104568e *}
				</td>
			   </tr>
			   <tr>
				<td valign=top align=left >
					<table border=0 cellspacing=0 cellpadding=3 width=100% class="dvtContentSpace">
					   <tr>

						<td align=left>
					
							<table border=0 cellspacing=0 cellpadding=0 width=100%>
							   <tr>
								<td id ="autocom"></td>
							   </tr>
							   <tr>
								<td style="padding:5px;padding-top:15px;">
									{* crmv@104568 *}
									{* Blocks *}
									{foreach item=data from=$BLOCKS}
										{assign var="header" value=$data.label}
										{assign var="blockid" value=$data.blockid}
										<div id="block_{$blockid}" class="editBlock">
										<table border="0" cellspacing="0" cellpadding="{if $OLD_STYLE eq true}2{else}5{/if}" width=100% class="small editBlockHeader">	{* crmv@57221 *}
											<tr>
											{* crmv@20176 *}
											{if $header == $MOD.LBL_ADDRESS_INFORMATION}
		                                    	{include file='AddressCopy.tpl'}
		                                    {else}
		                                    {* crmv@20176e *}
												<td colspan=4 class="detailedViewHeader">
													<b>{$header}</b>
											{/if}
												</td>
											</tr>
										</table>
										<table border="0" cellspacing="0" cellpadding="{if $OLD_STYLE eq true}2{else}5{/if}" width=100% class="small">	{* crmv@57221 *}
											{include file="DisplayFields.tpl" data=$data.fields}
											<tr style="height:25px"><td>&nbsp;</td></tr>
										</table>
										</div>
									{/foreach}

									{* Products block *}
									{if $MODULE|isInventoryModule}
										{include file="Inventory/ProductDetailsEditView.tpl"}
									{/if}
									{* crmv@104568e *}
								</td>
							   </tr>
							</table>
						</td>
						<!-- Inventory Actions - ends -->
					   </tr>
					</table>
				</td>
			   </tr>
			</table>
		</div>
	</td>
   </tr>
</table>
<input name='search_url' id="search_url" type='hidden' value='{$SEARCH}'>
</form>

<!-- This div is added to get the left and top values to show the tax details-->
<div id="tax_container" style="display:none; position:absolute; z-index:1px;"></div>

<script type="text/javascript">
	var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
	var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
	var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
	var fielduitype = new Array({$VALIDATION_DATA_FIELDUITYPE}); // crmv@83877
	var fieldwstype = new Array({$VALIDATION_DATA_FIELDWSTYPE}); //crmv@112297

	var product_labelarr = {ldelim}CLEAR_COMMENT:'{$APP.LBL_CLEAR_COMMENT}',
				DISCOUNT:'{$APP.LBL_DISCOUNT}',
				TOTAL_AFTER_DISCOUNT:'{$APP.LBL_TOTAL_AFTER_DISCOUNT}',
				TAX:'{$APP.LBL_TAX}',
				ZERO_DISCOUNT:'{$APP.LBL_ZERO_DISCOUNT}',
				PERCENT_OF_PRICE:'{$APP.LBL_OF_PRICE}',
				DIRECT_PRICE_REDUCTION:'{$APP.LBL_DIRECT_PRICE_REDUCTION}'{rdelim};

	var ProductImages=new Array();
	var count=0;
	function delRowEmt(imagename)
	{ldelim}
		ProductImages[count++]=imagename;
		multi_selector.current_element.disabled = false;
		multi_selector.count--;
	{rdelim}
	function displaydeleted()
	{ldelim}
		if(ProductImages.length > 0)
			document.EditView.del_file_list.value=ProductImages.join('###');
	{rdelim}
	
	{* crmv@104568 *}
	{if $PANEL_BLOCKS}
	var panelBlocks = {$PANEL_BLOCKS};
	{else}
	var panelBlocks = {ldelim}{rdelim};
	{/if}
	{* crmv@104568e *}
	
</script>
<!-- vtlib customization: Help information assocaited with the fields -->
{if $FIELDHELPINFO}
<script type='text/javascript'>
{literal}var fieldhelpinfo = {}; {/literal}
{foreach item=FIELDHELPVAL key=FIELDHELPKEY from=$FIELDHELPINFO}
	fieldhelpinfo["{$FIELDHELPKEY}"] = "{$FIELDHELPVAL}";
{/foreach}
</script>
{/if}
<!-- END -->

{include file="modules/Processes/InitEditViewConditionals.tpl"} {* crmv@112297 *}
