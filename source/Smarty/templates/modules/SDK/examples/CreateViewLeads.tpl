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

<!-- module header -->

<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$CALENDAR_LANG}.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>

{literal}
<style type='text/css'>
  .dvtCellInfo {
    background-color: red;
  }
</style>
{/literal}

<script type="text/javascript">
var gVTModule = '{$smarty.request.module}';
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

	<td class="showPanelBg" valign="top" width=100%>
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

					{if $ADVBLOCKS neq ''}	
						<td width=75 style="width:15%" align="center" nowrap class="dvtSelectedCell" id="bi" onclick="fnLoadValues('bi','mi','basicTab','moreTab','normal','{$MODULE}')"><b>{$APP.LBL_BASIC} {$APP.LBL_INFORMATION}</b></td>
                    				<td class="dvtUnSelectedCell" style="width: 100px;" align="center" nowrap id="mi" onclick="fnLoadValues('mi','bi','moreTab','basicTab','normal','{$MODULE}')"><b>{$APP.LBL_MORE} {$APP.LBL_INFORMATION} </b></td>
                   				<td class="dvtTabCache" style="width:65%" nowrap>&nbsp;</td>
					{else}
						<td class="dvtSelectedCell" align=center nowrap>{$APP.LBL_BASIC} {$APP.LBL_INFORMATION}</td>
	                                        <td class="dvtTabCache" style="width:65%">&nbsp;</td>
					{/if}
{*<!-- crmv@18592e -->*}
				   <tr>
				</table>
			</td>
		   </tr>
		   <tr>
			<td valign=top align=left >

			    <!-- Basic Information Tab Opened -->
			    <div id="basicTab">

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

								   {foreach key=header item=data from=$BASBLOCKS}
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

								   <!-- Here we should include the uitype handlings-->
								   {include file="DisplayFields.tpl"}							
								   <tr style="height:25px"><td>&nbsp;</td></tr>
								   {/foreach}

								</table>
							</td>
						   </tr>
						</table>
					</td>
				   </tr>
				</table>
					
			    </div>
			    <!-- Basic Information Tab Closed -->

			    <!-- More Information Tab Opened -->
			    <div id="moreTab">
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

								   {foreach key=header item=data from=$ADVBLOCKS}
								   <tr>
						         		<td colspan=4 class="detailedViewHeader">
                                                	        		<b>{$header}</b>
                                                         		</td>
                                                         	   </tr>

								   <!-- Here we should include the uitype handlings-->
                                                        	   {include file="DisplayFields.tpl"}

							 	   <tr style="height:25px"><td>&nbsp;</td></tr>
								   {/foreach}
{*<!-- crmv@18592e -->*}
								   </tr>
								</table>
							</td>
						   </tr>
						</table>
					</td>
				   </tr>
				</table>
			    </div>

			</td>
		   </tr>
		</table>
	     </div>
	</td>
	<td align=right valign=top></td>
   </tr>
</table>
</form>
<!--crmv@16312-->
{if ($MODULE eq 'Emails' || $MODULE eq 'Documents' || $MODULE eq 'Timecards') and ($FCKEDITOR_DISPLAY eq 'true')}
<!--crmv@16312 end-->
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