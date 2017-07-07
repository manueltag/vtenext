{*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************}

<script type="text/javascript" src="include/js/reflection.js"></script>
<script type="text/javascript" src="include/scriptaculous/scriptaculous.js"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/dtlviewajax.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"modules/Popup/Popup.js"|resourcever}"></script> {* crmv@43864 *}

{* crmv@104568 *}
<link href="include/js/jquery_plugins/mCustomScrollbar/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="include/js/jquery_plugins/mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
<link href="include/js/jquery_plugins/mCustomScrollbar/VTE.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
{* crmv@104568e *}

<script type="text/javascript">
var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
var fielduitype = new Array({$VALIDATION_DATA_FIELDUITYPE}); // crmv@83877
var fieldwstype = new Array({$VALIDATION_DATA_FIELDWSTYPE}); //crmv@112297
</script>

<span id="crmspanid" style="display:none;position:absolute;" onmouseover="show('crmspanid');">
   <a class="edit" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>

{if $MODULE eq 'Leads'}
	<div id="convertleaddiv" style="display:block;position:absolute;left:225px;top:150px;"></div>
{/if}

<div id="lstRecordLayout" class="layerPopup crmvDiv" style="display:none;width:320px;height:300px;z-index:21;position:fixed;"></div>	{*<!-- crmv@18592 -->*}

{if $MODULE eq 'Accounts' || $MODULE eq 'Contacts' || $MODULE eq 'Leads'}
	{if $MODULE eq 'Accounts'}
        {assign var=address1 value='$MOD.LBL_BILLING_ADDRESS'}
        {assign var=address2 value='$MOD.LBL_SHIPPING_ADDRESS'}
	{/if}
	{if $MODULE eq 'Contacts'}
        {assign var=address1 value='$MOD.LBL_PRIMARY_ADDRESS'}
        {assign var=address2 value='$MOD.LBL_ALTERNATE_ADDRESS'}
	{/if}
	<div id="locateMap" onMouseOut="fninvsh('locateMap')" onMouseOver="fnvshNrm('locateMap')">
        <table bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" width="100%">
            <tr>
				<td nowrap>
					{if $MODULE eq 'Accounts'}
						<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Main' );" class="calMnu">{$MOD.LBL_BILLING_ADDRESS}</a>
						<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Other' );" class="calMnu">{$MOD.LBL_SHIPPING_ADDRESS}</a>
                   	{/if}
					{if $MODULE eq 'Contacts'}
						<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Main' );" class="calMnu">{$MOD.LBL_PRIMARY_ADDRESS}</a>
						<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Other' );" class="calMnu">{$MOD.LBL_ALTERNATE_ADDRESS}</a>
                   {/if}
				</td>
            </tr>
        </table>
	</div>
{/if}

{if $MODULE eq 'Products'}
	{* crmv@100492 - not needed here, they are included in DetailViewUtils.php *}
	{*
	<script language="JavaScript" type="text/javascript" src="modules/Products/Productsslide.js"></script>
	<script language="JavaScript" type="text/javascript">Carousel();</script -->
	*}
	{* crmv@100492e *}
{/if}

<form name="SendMail" onsubmit="VtigerJS_DialogBox.block();"><div id="sendmail_cont" style="z-index:100001;position:absolute;"></div></form>
<form name="SendFax" onsubmit="VtigerJS_DialogBox.block();"><div id="sendfax_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
<!-- crmv@16703 -->
<form name="SendSms" id="SendSms" onsubmit="VtigerJS_DialogBox.block();" method="POST" action="index.php"><div id="sendsms_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
<!-- crmv@16703e -->

{include file='Buttons_List1.tpl'}
{include file='Buttons_List_Detail.tpl'}

<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
<tr>
	<td valign="top" width=100%>
		<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
			{if count($DETAILTABS) > 1 || ($MODULE eq 'Campaigns' && 'Newsletter'|isModuleInstalled) || ($SinglePane_View eq 'false' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0)}
				<tr>
					<td style="padding-top:3px;">
						<table border=0 cellspacing=0 cellpadding=3 width=100% style="background:white" class="small" id="DetailViewTabs">
							<tr>
								{* crmv@45699 crmv@104568 *}
								{if !empty($DETAILTABS)}
									{foreach item=_tab from=$DETAILTABS name="extraDetailForeach"}
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
										{* crmv@98866 *}
										<!-- <td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td> -->
										{* crmv@98866 end *}
										<td class="{$_class}" align="center" onClick="{$_tab.onclick}" nowrap="" data-panelid="{$_tab.panelid}"><a href="{$_href}">{$_tab.label}</a></td>
									{/foreach}
								{* crmv@45699e *}
								{else}
									{* crmv@98866 *}
									<!-- <td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td> -->
									{* crmv@98866 end *}
									<td class="dvtSelectedCell" align=center nowrap>{$APP.LBL_INFORMATION}</td>
									{* crmv@22700 *}
									{if $MODULE eq 'Campaigns' && 'Newsletter'|isModuleInstalled}
										{* crmv@98866 *}
										<!-- <td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td> -->
										{* crmv@98866 end *}
										<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action=Statistics&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{'LBL_STATISTICS'|@getTranslatedString:'Newsletter'}</a></td>
									{/if}
									{* crmv@22700e *}
								{/if}
								{* crmv@104568e *}
								{if $SinglePane_View eq 'false' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
									{* crmv@98866 *}
									<!-- <td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td> -->
									{* crmv@98866 end *}
									<td class="dvtUnSelectedCell" onmouseout="fnHideDrop('More_Information_Modules_List');" onmouseover="fnDropDown(this,'More_Information_Modules_List',-10);" align="center" nowrap>{* crmv@22259 *}{* crmv@22622 *}
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
								<td class="dvtTabCache" align="right" style="width:100%"></td>
							</tr>
						</table>
					</td>
				</tr>
			{/if}
			<tr>
				<td valign="top" style="padding-right:5px;">
					<table border=0 cellspacing=0 cellpadding=0 width=100%>
						<tr>
							{* MAIN COLUMN (fields and related) *}
							<td id="detailBlocksContainer" align=left valign="top">
								<div style="padding:5px">
									<form action="index.php" method="post" name="DetailView" id="form" autocomplete="off"> {* crmv@106308 *}
										{include file='DetailViewHidden.tpl'}
										<div id="DetailViewBlocks" style="background:white">
											{include file="DetailViewBlocks.tpl"}	{* crmv@57221 *}
										</div>
									</form>
									{* crmv@101312 *}
									{if $MODULE eq "Calendar"}
										{include file="modules/Calendar/DetailViewExtra.tpl"}
									{/if}
									{* crmv@101312e *}
									{* crmv@44323 *}
									<div id="DetailExtraBlock">
										{$EXTRADETAILBLOCK}
									</div>
									{* crmv@44323e *}
								</div>
							</td>
							<td id="detailWidgetsContainer" valign="top" style="padding-top:10px;">
								{* vtlib Customization: Embed DetailViewWidget block:// type if any *}
								{include file='DetailViewWidgets.tpl'}
								{* END *}
							</td>
							{* RIGHT COLUMN (buttons, widget, turbolift, ...) *}
							<td width="15%" valign="top" style="padding-top:5px;" id="turboLiftContainer"> {* crmv@43864 *}
								<div style="width:15%; position:fixed; top:55px; right:95px; display:none">
									{include file='Turbolift.tpl'}
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2" id="RelatedListsCont">
								<div id="editlistprice" style="position:absolute;width:300px;"></div> {* crmv@43864 *}
								{include file='RelatedListsHidden.tpl'}	{* crmv@54245 *}
								<div id="RelatedLists" {if empty($RELATEDLISTS)}style="display:none;"{/if}>
									{include file='RelatedListNew.tpl' PIN=true}
								</div>
								<div id="DynamicRelatedList" style="display:none;"></div>
								</form>	{* crmv@54245 close form opened in RelatedListsHidden.tpl *}
								{if !empty($ASSOCIATED_PRODUCTS)}
									<div style="padding:5px">
										{$ASSOCIATED_PRODUCTS}
									</div>
								{/if}
							</td>
						</tr>
						{*crmv@104558*}
						{if $MODULE eq 'Newsletter'}
						<tr>
							<td colspan="2" style="padding:10px">
								<div id="template_prev">
									<table class="small" width="100%" cellspacing="0" cellpadding="0" border="0">
										<tr>
											<td class="dvInnerHeader" width="98%">
												<div style="float:left;font-weight:bold;width:100%;">
													<div style="float:left;">
														<b>{$MOD.LBL_TEMPLATE_PREVIEW}</b>
													</div>
												</div>
											</td>
											<td class="dvInnerHeader" width="2%">
												<button class="crmbutton small edit" style="background-color: white;" name="Edit" onclick="openPopup('index.php?module=Newsletter&action=NewsletterAjax&file=widgets/TemplateEmailEdit&record={$ID}&mode=edit','TemplateEmailList','top=100,left=200,height=400,width=500,resizable=yes,scrollbars=yes,menubar=no,addressbar=no,status=yes','auto')" accesskey="E" title="Edit">{$APP.LBL_EDIT}</button>
											</td>
										</tr>
									</table>
									<div style="padding:15px">
										{include file='PreviewEmailTemplate.tpl'}
									</div>
								</div>
							</td>
						</tr>
						{/if}
						{*crmv@104558e*}
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>

{* crmv@95157 - metadata container *}
{if $MODULE eq 'Documents'}
<div id="metadataContainer" class="layerPopup crmvDiv" style="position:fixed;min-height:300px;min-width:500px;z-index:100;display:none">
	<table border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr style="cursor:move;" height="34">
			<td id="Meta_Handle" style="padding:5px" class="level3Bg">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="50%"><b>{$MOD.LBL_METADATA}</b></td>
					<td width="50%" align="right">&nbsp;
						<button id="metadataSaveButton" type="button" class="crmbutton save" onclick="saveMetadata('{$ID}')">{$APP.LBL_SAVE_LABEL}</button>
					</td>
				</tr>
				</table>
			</td>
		</tr>
	</table>
	<div class="crmvDivContent">
	</div>
	<div class="closebutton" onClick="fninvsh('metadataContainer');"></div>
</div>
<script type="text/javascript">
	{literal}
	(function() {
		var handle = document.getElementById("Meta_Handle");
		var root   = document.getElementById("metadataContainer");
		Drag.init(handle, root);
	})();
	{/literal}
</script>
{/if}
{* crmv@95157e *}

{* crmv@104568 *}
<script type="text/javascript">
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
</script>
{* crmv@104568e *}

{* crmv@93990 crmv@109851 *}
{if $RELATED_PROCESS neq false}
	<script type="text/javascript">
		jQuery(document).ready(function(){ldelim}
			DynaFormScript.popup({$RELATED_PROCESS});
		{rdelim});
	</script>
{/if}
{* crmv@93990e crmv@109851e *}
