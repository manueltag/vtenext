{********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ********************************************************************************}
{* crmv@101506 *}

<script>
function loadCvList(type,id)
{ldelim}
        $("status").style.display="inline";
		if(type === 'Leads')
		{ldelim}
			if($("Leads_cv_list").value != 'None'){ldelim}
				new Ajax.Request(
	                        'index.php',
	                        {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
	                                method: 'post',
	                                postBody: 'module=Campaigns&action=CampaignsAjax&file=LoadList&ajax=true&return_action=DetailView&return_id='+id+'&list_type='+type+'&cvid='+$("Leads_cv_list").value,
	                                onComplete: function(response) {ldelim}
	                                        $("status").style.display="none";
	                                        $("RLContents").innerHTML= response.responseText;
	                                {rdelim}
	                        {rdelim}
	                	);
			{rdelim}
		{rdelim}

		if(type === 'Contacts')
		{ldelim}
			if($("Contacts_cv_list").value != 'None'){ldelim}		
				new Ajax.Request(
	                        'index.php',
	                        {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
	                                method: 'post',
	                                postBody: 'module=Campaigns&action=CampaignsAjax&file=LoadList&ajax=true&return_action=DetailView&return_id='+id+'&list_type='+type+'&cvid='+$("Contacts_cv_list").value,
	                                onComplete: function(response) {ldelim}
	                                        $("status").style.display="none";
	                                        $("RLContents").innerHTML= response.responseText;
	                                {rdelim}
	                        {rdelim}
	                	);
			{rdelim}
		{rdelim}
		
		if(type === 'Accounts')
		{ldelim}
			if($("Accounts_cv_list").value != 'None'){ldelim}
				new Ajax.Request(
	                        'index.php',
	                        {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
	                                method: 'post',
	                                postBody: 'module=Campaigns&action=CampaignsAjax&file=LoadList&ajax=true&return_action=DetailView&return_id='+id+'&list_type='+type+'&cvid='+$("Accounts_cv_list").value,
	                                onComplete: function(response) {ldelim}
	                                        $("status").style.display="none";
	                                        $("RLContents").innerHTML= response.responseText;
	                                {rdelim}
	                        {rdelim}
	                	);
			{rdelim}
		{rdelim}
{rdelim}
</script>

{* crmv@22700 *}
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td>
		{include file='Buttons_List1.tpl'}
	</td>
</tr>
</table>
<!-- Contents -->
<div id="editlistprice" style="position:absolute;width:300px;"></div>
<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
<tr>
	<td valign=top></td>
	<td class="showPanelBg" valign=top width=100% style="padding:0px">
		<!-- PUBLIC CONTENTS STARTS-->
		<div class="small" style="padding:0px">
			<table align="center" border="0" cellpadding="4" cellspacing="0" width="100%" class="level3Bg" id="Buttons_List_4">
			<tr>
				<td width="100%" style="padding:5px">
			  		{* Module Record numbering, used MOD_SEQ_ID instead of ID *}
			  		{assign var="USE_ID_VALUE" value=$MOD_SEQ_ID}
			  		{if $USE_ID_VALUE eq ''} {assign var="USE_ID_VALUE" value=$ID} {/if}
			 		<span class="dvHeaderText">
						<span class="recordTitle1">{$SINGLE_MOD|@getTranslatedString:$MODULE}</span>
						{if $SHOW_RECORD_NUMBER eq true}
							[ {$USE_ID_VALUE} ]
						{/if}
						{$NAME}&nbsp;<span style="font-weight:normal;">{$UPDATEINFO}</span>
					</span>
			 	</td>
			 	<td style="padding:5px 10px 5px 5px;" align="right" nowrap>
			 		<span id="vtbusy_info" style="display:none;" valign="bottom">{include file="LoadingIndicator.tpl"}</span>
					{if !$NEWSLETTER_STATISTICS}{'Filter by Newsletter'|getTranslatedString:'Newsletter'}:&nbsp;{/if}{$STATISTICS_SELECT}&nbsp;
					<a href="javascript:;"><i class="vteicon md-link md-text" name="jumpBtnIdTop" onclick="filter_statistics_newsletter({$CAMPAIGNID},getObj('statistics_newsletter'));" title="{$APP.Refresh}">refresh</i></a>
			 	</td>
			</table>
			<div id="vte_menu_white_1"></div>
			<script>
				//jQuery('#vte_menu_white_1').height(jQuery('#Buttons_List_4').height());
				//jQuery('#status').css('top',jQuery('#vte_menu_white').height()+jQuery('#vte_menu_white_1').height());
			</script>
			<!-- Account details tabs -->
			<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
{* crmv@22700e *}
			<tr>
				<td style="padding-top:3px;">
					<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
						<tr>
							{if $OP_MODE eq 'edit_view'}
								{assign var="action" value="EditView"}
							{else}
								{assign var="action" value="DetailView"}
							{/if}
							<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
							<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action={$action}&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$APP.LBL_INFORMATION}</a></td>
							{* crmv@22700 *}
							<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
							<td class="dvtSelectedCell" align=center nowrap>{'LBL_STATISTICS'|@getTranslatedString:'Newsletter'}</td>
							{* crmv@22700e *}
							<td class="dvtTabCache" style="width:10px">&nbsp;</td>
							{* crmv@22700 *}
							{if $SinglePane_View eq 'false' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
							<td class="dvtUnSelectedCell" onmouseout="fnHideDrop('More_Information_Modules_List');" onmouseover="fnDropDown(this,'More_Information_Modules_List',-8);" align="center" nowrap>{* crmv@22259 *}
								<a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a>
								<div onmouseover="fnShowDrop('More_Information_Modules_List')" onmouseout="fnHideDrop('More_Information_Modules_List')"
											 id="More_Information_Modules_List" class="drop_mnu" style="left: 502px; top: 76px; display: none;">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
									{foreach key=_RELATION_ID item=_RELATED_MODULE from=$IS_REL_LIST}
										<tr><td><a class="drop_down" href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}&selected_header={$_RELATED_MODULE}&relation_id={$_RELATION_ID}">{$_RELATED_MODULE|@getTranslatedString:$_RELATED_MODULE}</a></td></tr>
									{/foreach}
									</table>
								</div>
							</td>
							{/if}
							{* crmv@22700e *}
							<td class="dvtTabCache" style="width:100%">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td valign=top align=left >
					<table border=0 cellspacing=0 cellpadding=0 width=100%>
						<tr>
							<td>
								{include file='RelatedListsHidden.tpl' MODULE="Campaigns"}
								<div align="center">
									<img id="StatisticChar" src="" class="img-responsive" />
									<script type="text/javascript">
										jQuery("#StatisticChar").attr("src", "cache/charts/StatisticsChart.png?"+(new Date()).getTime()); // crmv@38600
									</script>
								</div>
								<div id="RLContents">
									{include file='RelatedListContents.tpl' MODULE="Campaigns"}
								</div>
								</form>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			</table>
		</div>
	<!-- PUBLIC CONTENTS STOPS-->
	</td>
{* crmv@22700 *}
</tr>
</table>
</td>
</tr></table>
{* crmv@22700e *}

<form name="SendMail" onsubmit="VtigerJS_DialogBox.block();"><div id="sendmail_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
<form name="SendFax" onsubmit="VtigerJS_DialogBox.block();"><div id="sendfax_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
<form name="SendSms" onsubmit="VtigerJS_DialogBox.block();"><div id="sendsms_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>

{* crmv@101503 *}
<div id="ModTarget" class="crmvDiv" style="display: none; position: fixed; left: 494px; top: 42px; visibility: visible; z-index: 1000000007;">
	<table width="100%" cellspacing="0" cellpadding="5" border="0">
		<tr height="34" style="cursor:move;">
			<td id="ModTarget_Handle" class="level2Bg" style="padding:5px">
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td>
							<b>{$APP.LBL_CREATE} {$APP.Targets}</b>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<div id="ModTarget_div" style="padding: 4px; width: 550px; height: 100px; overflow: auto;">
		<table width="100%">
			<tr>
				<td>
					<div>
						<span class='dvtCellLabel'> {$APP.Name} {$APP.Targets} </span>
					</div>
					<div class='dvtCellInfo'>
						<input id='targetname' class='detailedViewTextBox' type='text' value='' name='targetname'>
						<input id='title' class='detailedViewTextBox' type='hidden' value='' name='title'>
						<input id='campaignid' class='detailedViewTextBox' type='hidden' value='' name='campaignid'>
					</div>
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td align="right"><input class="crmButton small save" type="button" style="min-width: 70px" value="Salva" name="button" onclick="saveTarget();" accesskey="S" title="Salva [Alt+S]"></td></tr>
		</table>
	</div>
	<div class="closebutton" onclick="fninvsh('ModTarget');"></div>
</div>
{literal}
<script>
	var Handle = document.getElementById("ModTarget_Handle");
	var Root   = document.getElementById("ModTarget");
	Drag.init(Handle, Root);

	jQuery("#statistics_newsletter").click(function(){
   		fninvsh('ModTarget');
	});
</script>
{/literal}
{* crmv@101503e *}

<script>
function OpenWindow(url)
{ldelim}
	openPopUp('xAttachFile',this,url,'attachfileWin',380,375,'menubar=no,toolbar=no,location=no,status=no,resizable=no');	
{rdelim}
</script>