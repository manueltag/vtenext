<!--

/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

-->

{if $MODE eq 'edit'}
EDIT
{else}
CREATE
{/if}


<!-- module header -->

<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$CALENDAR_LANG}.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<!-- overriding the pre-defined #company to avoid clash with vtiger_field in the view -->
{literal}
<style type='text/css'>
#company {
	height: auto;
	width: 90%;
}
</style>

<style type='text/css'>
  .dvtCellInfo {
    background-image: url('themes/images/back.gif');
    background-repeat: repeat-x;
  }
</style>

{/literal}

<script type="text/javascript">
var gVTModule = '{$smarty.request.module|@vtlib_purify}';
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

{include file='Buttons_List1.tpl'}	
<!-- Contents -->
{*<!-- crmv@18592 -->*}
<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
   <tr>
	<td valign=top></td>

	<td class="showPanelBg" valign=top width=100%>
		<!-- PUBLIC CONTENTS STARTS-->
		{include file='EditViewHidden.tpl'}
		{include file='Buttons_List_Edit.tpl'}
		<div class="small">
			<!-- Account details tabs -->
			<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
			   <tr>
				<td>
					<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
					   <tr>
						<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
						<td class="dvtSelectedCell" align=center nowrap>{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</td>
						<td class="dvtTabCache" style="width:10px">&nbsp;</td>
						<td class="dvtTabCache" style="width:100%">&nbsp;</td>
					   </tr>
					</table>
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
									<!-- General details -->
									<table border="0" cellspacing="0" cellpadding="5" width=100% class="small">

									   <!-- included to handle the edit fields based on ui types -->
									   {foreach key=header item=data from=$BLOCKS}



							<!-- This is added to display the existing comments -->
							{if $header eq $MOD.LBL_COMMENTS || $header eq $MOD.LBL_COMMENT_INFORMATION}
							   <tr><td>&nbsp;</td></tr>
							   <tr>
								<td colspan=4 class="dvInnerHeader">
							        	<b>{$MOD.LBL_COMMENT_INFORMATION}</b>
								</td>
							   </tr>
							   <tr>
								<td colspan=4>{$COMMENT_BLOCK}</td>
							   </tr>
							   <tr><td>&nbsp;</td></tr>
							{/if}



									      <tr>
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

										<!-- Handle the ui types display -->
										{include file="DisplayFields.tpl"}
										<tr style="height:25px"><td>&nbsp;</td></tr>
									   {/foreach}


									   <!-- Added to display the Product Details in Inventory-->
									   {if $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Quotes' || $MODULE eq 'Invoice'}
							   		   <tr>
										<td colspan=4>
											{include file="Inventory/ProductDetailsEditView.tpl"}
										</td>
							   		   </tr>
									   {/if}
{*<!-- crmv@18592e -->*}
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


{if ($MODULE eq 'Emails' || 'Documents' || 'Timecards') and ($FCKEDITOR_DISPLAY eq 'true')}
<!--crmv@10621-->
	<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
	<script>
		var current_language_arr = "{php} echo $_SESSION['authenticated_user_language']; {/php}".split("_");
		var curr_lang = current_language_arr[0];
        {if $MODULE eq 'Timecards'}
			{literal}
			CKEDITOR.replace('description', {
				filebrowserBrowseUrl: 'include/ckeditor/filemanager/index.html',
				toolbar : 'Basic',	//crmv@31210
				language : curr_lang
			});	
			{/literal}	
        {else}					
			{literal}
			CKEDITOR.replace('notecontent', {
				filebrowserBrowseUrl: 'include/ckeditor/filemanager/index.html',
				toolbar : 'Basic',	//crmv@31210
				language : curr_lang
			});	
			{/literal}
		{/if}	
	</script>
<!--crmv@10621 e-->		
{/if}

{if $MODULE eq 'Accounts'}
<script>
	ScrollEffect.limit = 201;
	ScrollEffect.closelimit= 200;
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

<p style="color:blue;font-weight:700">
 EditViewLeads.tpl - END
</p>

{include file="modules/Processes/InitEditViewConditionals.tpl"} {* crmv@112297 *}