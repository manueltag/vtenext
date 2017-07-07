{********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************}

<script language="JavaScript" type="text/javascript" src="modules/PriceBooks/PriceBooks.js"></script>
{literal}
<script>
	function editProductListPrice(id,pbid,price,module) {
        $("status").style.display="inline";
        new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
            method: 'post',
            postBody: 'action='+module+'Ajax&file=EditListPrice&return_action=DetailView&return_module=PriceBooks&module='+module+'&record='+id+'&pricebook_id='+pbid+'&listprice='+price,
            onComplete: function(response) {
            	$("status").style.display="none";
				$("editlistprice").innerHTML= response.responseText;
			}
		});
	}

	function gotoUpdateListPrice(id,pbid,proid,module) {
		$("status").style.display="inline";
		$("roleLay").style.display = "none";
        var listprice = $("list_price").value;

		new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module='+module+'&action='+module+'Ajax&file=UpdateListPrice&ajax=true&return_action=CallRelatedList&return_module=PriceBooks&record='+id+'&pricebook_id='+pbid+'&product_id='+proid+'&list_price='+listprice,
			onComplete: function(response) {
				$("status").style.display="none";
				reloadTurboLift('PriceBooks', id, module);	//crmv@55227 crmv@55265
			}
		});
	}
{/literal}

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

<form name="SendMail" onsubmit="VtigerJS_DialogBox.block();"><div id="sendmail_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
<form name="SendFax" onsubmit="VtigerJS_DialogBox.block();"><div id="sendfax_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
<form name="SendSms" onsubmit="VtigerJS_DialogBox.block();"><div id="sendsms_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>

{include file='Buttons_List1.tpl'}
{include file='Buttons_List_Detail.tpl'}

<div id="editlistprice" style="position:absolute;width:300px;"></div>
		
<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
<tr>
	<td valign="top" width=100%>
		<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
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
							{if $MODULE eq 'Calendar'}
								<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action={$action}&module={$MODULE}&record={$ID}&activity_mode={$ACTIVITY_MODE}&parenttab={$CATEGORY}">{$SINGLE_MOD} {$APP.LBL_INFORMATION}</a></td>
							{else}
								<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action={$action}&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$SINGLE_MOD} {$APP.LBL_INFORMATION}</a></td>
							{/if}
							{* crmv@22700 *}
							{php}if (isModuleInstalled('Newsletter')) {{/php}
								{if $MODULE eq 'Campaigns'}
									<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
									<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action=Statistics&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{'LBL_STATISTICS'|@getTranslatedString:'Newsletter'}</a></td>
								{/if}
							{php}}{/php}
							{* crmv@22700e *}
							{* <!-- ds@8 project tool --> crmv@21249 *}
            		        {if $MODULE eq 'Projects'}
			              		<td class="dvtTabCacheBottom" style="width:10px">&nbsp;</td>
			              		<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action=BasicView&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}&mode_view=Basic">{$MOD.LBL_BASIC_INFORMATION}</a></td>
			              		<td class="dvtTabCacheBottom" style="width:10px">&nbsp;</td>
			              		<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action=BasicView&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}&mode_view=Project">{$MOD.LBL_PROJECT_INFORMATION}</a></td>
			          		{/if}
                    		{* <!-- ds@8e --> crmv@21249e *}
							<td class="dvtTabCache" style="width:10px">&nbsp;</td>
							<td class="dvtSelectedCell" align=center nowrap>{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</td>
							<td class="dvtTabCache" style="width:100%">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td valign="top">                
					<table border=0 cellspacing=0 cellpadding=0 width=100%>
						<tr>
							<td align=left valign="top">
								{include file='RelatedListsHidden.tpl'}
								<div id="RLContents">
									{include file='RelatedListContents.tpl'}
								</div>
								</form>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>