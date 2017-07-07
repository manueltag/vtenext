{********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************}

{* crmv@104568 *}

{* crmv@119414 *}
{assign var=overrides value=$THEME_CONFIG.tpl_overrides}
{if !empty($overrides[$smarty.template])}
	{include file=$overrides[$smarty.template]}
	{php}return;{/php}
{/if}
{* crmv@119414e *}

{* overriding the pre-defined #company to avoid clash with vtiger_field in the view *}
<style type='text/css'>
{literal}
#company {
	height: auto;
	width: 90%;
}
{/literal}
</style>

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
{include file='Buttons_List1.tpl'} {* crmv@43864 *}
{/if}


{* crmv@18592 *}
<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
   <tr>
	<td valign=top></td>

	<td class="showPanelBg" valign="top" width="100%" style="padding:0px">
		<!-- PUBLIC CONTENTS STARTS-->
		{include file='EditViewHidden.tpl'}
		{if $HIDE_BUTTON_LIST neq '1'}
		{include file='Buttons_List_Edit.tpl'} {* crmv@43864 *}
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
							<!-- content cache -->

							<table border=0 cellspacing=0 cellpadding=0 width=100%>
							   <tr>
								<td id ="autocom"></td>
							   </tr>
							   <tr>
								<td style="padding:5px;padding-top:15px;">

									{* crmv@99316 crmv@104568 crmv@105937 *}
									{* Blocks *}
									{foreach item=data from=$BLOCKS}
										{assign var="header" value=$data.label}
										{assign var="blockid" value=$data.blockid}
										
										<div id="block_{$blockid}" class="editBlock" style="{if $PANELID != $data.panelid}display:none{/if}">
										<table border="0" cellspacing="0" cellpadding="{if $OLD_STYLE eq true}2{else}5{/if}" width=100% class="small editBlockHeader">	{* crmv@57221 *}
										
										{if isset($BLOCKVISIBILITY.$blockid) && $BLOCKVISIBILITY.$blockid eq 0}
											{* hide block *}
											{assign var="BLOCKDISPLAYSTATUS" value="display:none"}
										{else}
											{assign var="BLOCKDISPLAYSTATUS" value=""}
										{/if}
										{* crmv@99316e *}
										
										<tr class="blockrow_{$blockid}" style="{$BLOCKDISPLAYSTATUS}">
										{* crmv@20176 *}
										{if $header== $MOD.LBL_ADDRESS_INFORMATION}
											{include file='AddressCopy.tpl'}
										{* crmv@20176e *}
										{else}
											<td colspan=4 class="detailedViewHeader">
												<b>{$header}</b>
										{/if}
										</td>
										</tr>
										</table>
										<table border="0" cellspacing="0" cellpadding="{if $OLD_STYLE eq true}2{else}5{/if}" width=100% class="small">	{* crmv@57221 *}
											<tbody id="displayfields_{$blockid}" class="blockrow_{$blockid}" style="{$BLOCKDISPLAYSTATUS}">
												{include file="DisplayFields.tpl" data=$data.fields}
											</tbody>
											<tr class="blockrow_{$blockid}" style="height:25px; {$BLOCKDISPLAYSTATUS}"><td>&nbsp;</td></tr>
										</table>
										</div>
									{/foreach}

									{* Products block *}
									{if $MODULE|isInventoryModule}
										{include file="Inventory/ProductDetailsEditView.tpl"}
									{/if}
									{* crmv@99316e crmv@104568e crmv@105937e *}
									
									{* crmv@115268 vtlib Customization: Embed DetailViewWidget block:// type if any *}
									{include file='VtlibWidgets.tpl' WIDGETTYPE="EDITVIEWWIDGET"}
									{* END *}

									</table>
								</td>
							   </tr>
							</table>
						</td>
					   </tr>
					</table>
				</td>
			   </tr>
			</table>
		<div>
	</td>
	<td align=right valign=top></td>
   </tr>
</table>
<!--added to fix 4600-->
<input name='search_url' id="search_url" type='hidden' value='{$SEARCH}'>
</form>

{if $MODULE eq 'Accounts'}
<script type="text/javascript">
	{* crmv@97692 *}
	{literal}
	if (!window.ScrollEffect) {
		ScrollEffect = { };
		ScrollEffect.lengthcount=202;
		ScrollEffect.closelimit=0;
		ScrollEffect.limit=0;
	}
	ScrollEffect.limit = 201;
	ScrollEffect.closelimit= 200;
	{/literal}
	{* crmv@97692e *}
</script>
{/if}
<script type="text/javascript">
	var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
	var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
	var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
	var fielduitype = new Array({$VALIDATION_DATA_FIELDUITYPE}); // crmv@83877
	var fieldwstype = new Array({$VALIDATION_DATA_FIELDWSTYPE}); //crmv@112297

	var ProductImages=new Array();
	var count=0;

	function delRowEmt(imagename)
	{ldelim}
		ProductImages[count++]=imagename;
	{rdelim}

	function displaydeleted()
	{ldelim}
		var imagelists='';
		for(var x = 0; x < ProductImages.length; x++)
		{ldelim}
			imagelists+=ProductImages[x]+'###';
		{rdelim}

		if(imagelists != '')
			document.EditView.imagelist.value=imagelists
	{rdelim}
	
	{* crmv@104568 *}

	{if $PANEL_BLOCKS}
	var panelBlocks = {$PANEL_BLOCKS};
	{else}
	var panelBlocks = {ldelim}{rdelim};
	{/if}
	{if $PANELID > 0}
	var currentPanelId = {$PANELID};
	{else}
	var currentPanelId = 0;
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
