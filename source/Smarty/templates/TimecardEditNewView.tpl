{********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************}

<!-- module header -->

<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$CALENDAR_LANG}.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>

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

		{include file='Buttons_List1.tpl'}	

<!-- Contents -->
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
   <tr>
	<td valign=top></td>

	<td class="showPanelBg" valign=top width=100%>
		<!-- PUBLIC CONTENTS STARTS-->
		<div class="small" style="padding:20px">
		
			{if $OP_MODE eq 'edit_view'}   
				{if $MODULE eq 'HelpDesk' && $MODTAB eq 'TimeCard'}
				 <span class="lvtHeaderText"><font color="purple">[ {$ID} ] </font>{$NAME} -  {$APP.LBL_EDITING} {$MOD.LBL_TimeCards}</span> <br>
				{else}
				 <span class="lvtHeaderText"><font color="purple">[ {$ID} ] </font>{$NAME} - {$APP.LBL_EDITING} {$APP[$SINGLE_MOD]} {$APP.LBL_INFORMATION}</span> <br>
				{/if}
				{$UPDATEINFO}	 
			{/if}
			{if $OP_MODE eq 'create_view'}
				<span class="lvtHeaderText">{$APP.LBL_CREATING} {$APP[$SINGLE_MOD]}</span> <br>
			{/if}

			<hr noshade size=1>
			<br> 
		
			{include file='EditViewHidden.tpl'}

			<!-- Account details tabs -->
			<table border=0 cellspacing=0 cellpadding=0 width=95% align=center>
			   <tr>
				<td>
					<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
					   <tr>
						<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>

                       	{if $MODULE eq 'Documents' || $MODULE eq 'Faq'}
                        	<td class="dvtSelectedCell" align=center nowrap>{$APP[$SINGLE_MOD]} {$APP.LBL_INFORMATION}</td>
                            <td class="dvtTabCache" style="width:100%">&nbsp;</td>
						{else}
						    {if $MODTAB eq 'TimeCard'}
								<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action=DetailView&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$APP[$SINGLE_MOD]} {$APP.LBL_INFORMATION}</a></td>
								<td class="dvtTabCache" style="width:10px">&nbsp;</td>
							    <td class="dvtSelectedCell" align=center nowrap>{$MOD.LBL_TimeCards}</a></td>
								<td class="dvtTabCache" style="width:10px">&nbsp;</td>
							{else}
								<td class="dvtSelectedCell" align=center nowrap>{$APP[$SINGLE_MOD]} {$APP.LBL_INFORMATION}</td>
								<td class="dvtTabCache" style="width:10px">&nbsp;</td>
							    <td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action=CallTimeCardList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$MOD.LBL_TimeCards}</a></td>
								<td class="dvtTabCache" style="width:10px">&nbsp;</td>
							{/if}

   	                        {if $OP_MODE neq 'create_view' || $MODTAB eq 'TimeCard'}
            				  <td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}&mode={$OP_MODE}">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a></td>
							{/if}
						<td class="dvtTabCache" style="width:100%">&nbsp;</td>
						{/if}
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
								<td style="padding:10px">
									<!-- General details -->
									<table border="0" cellspacing="0" cellpadding="5" width=100% class="small">
									   <tr>
										<td  colspan=4 style="padding:5px">
											<div align="center">
												<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="this.form.action.value='TimeCardUpdate'; displaydeleted(); return formValidate()" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" >
												<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="window.location='{$TC_Cancel_URL}'" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
											</div>
										</td>
									   </tr>

									   <!-- included to handle the edit fields based on ui types -->
									   {foreach key=header item=data from=$BLOCKS}

									      <tr>
										{* crmv@20176 *}
										{if $header== 'Address Information'}
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

									   {/foreach}


									   <!-- Added to display the Product Details in Inventory-->
									   {if $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Quotes' || $MODULE eq 'Invoice'}
							   		   <tr>
										<td colspan=4>
											{include file="ProductDetailsEditView.tpl"}
										</td>
							   		   </tr>
									   {/if}

									   <tr>
										<td  colspan=4 style="padding:5px">
											<div align="center">
												{if $MODULE eq 'Emails'}
													<input title="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_TITLE}" accessKey="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_KEY}" class="crmbutton small create" onclick="window.open('index.php?module=Users&action=lookupemailtemplates&entityid={$ENTITY_ID}&entity={$ENTITY_TYPE}','emailtemplate','top=100,left=200,height=400,width=300,menubar=no,addressbar=no,status=yes')" type="button" name="button" value="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_LABEL}">
													<input title="{$MOD.LBL_SEND}" accessKey="{$MOD.LBL_SEND}" class="crmbutton small save" onclick="this.form.action.value='TimeCardUpdate';this.form.send_mail.value='true'; return formValidate()" type="submit" name="button" value="  {$MOD.LBL_SEND}  " >
												{/if}
   			                                	<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="this.form.action.value='TimeCardUpdate';  displaydeleted();return formValidate()" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" >
                                				<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="window.location='{$TC_Cancel_URL}'" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
											</div>
										</td>
									   </tr>
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
</form>


{if ($MODULE eq 'Emails' || 'Documents' || 'HelpDesk') and ($FCKEDITOR_DISPLAY eq 'true')}
<!--crmv@10621-->
<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
<script type="text/javascript" defer="1">
var current_language_arr = "{php} echo $_SESSION['authenticated_user_language']; {/php}".split("_");
var curr_lang = current_language_arr[0];
{literal}
CKEDITOR.replace('notecontent', {
	filebrowserBrowseUrl: 'include/ckeditor/filemanager/index.html',
	toolbar : 'Basic',	//crmv@31210
	language : curr_lang,	
});	
CKEDITOR.replace('description', {
	filebrowserBrowseUrl: 'include/ckeditor/filemanager/index.html',
	toolbar : 'Basic',	//crmv@31210
	language : curr_lang,	
});	
{/literal}	
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

{include file="modules/Processes/InitEditViewConditionals.tpl"} {* crmv@112297 *}