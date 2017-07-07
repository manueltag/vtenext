{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}

{* crmv@42752 crmv@43050 crmv@43864 crmv@43942 crmv@54707 *}

{include file="SmallHeader.tpl"}
{include file="modules/SDK/src/Reference/Autocomplete.tpl"}

<link href="include/js/jquery_plugins/mCustomScrollbar/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
<script src="include/js/jquery_plugins/mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
<script language="javascript" type="text/javascript" src="include/js/jquery_plugins/slimscroll/jquery.slimscroll.min.js"></script>
<link href="include/js/jquery_plugins/mCustomScrollbar/VTE.mCustomScrollbar.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="{"include/js/ListView.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"modules/Area/Area.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/dtlviewajax.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"modules/SDK/src/Notifications/NotificationsCommon.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="include/js/{php} echo $_SESSION['authenticated_user_language'];{/php}.lang.js?{php} echo $_SESSION['vtiger_version'];{/php}"></script>
<script language="JavaScript" type="text/javascript" src="{"modules/Popup/Popup.js"|resourcever}"></script>

{if count($EXTRA_JS) > 0}
	{foreach item=JSPATH from=$EXTRA_JS}
	<script type="text/javascript" src="{$JSPATH}"></script>
	{/foreach}
{/if}
<style type="text/css">
	{literal}
	html, body {
		height: 94%; /* leave space for top panel */
	}
	#linkMsgMainTab {
		width:100%;
		/*height: 100%;
		position: absolute;*/
		z-index: -10;
	}
	#linkMsgLeftPane {
		width:20%;
		min-width:200px;
		border-right: 2px solid #e0e0e0;
		vertical-align: top;
	}
	#linkMsgRightPane {
		min-width:400px;
		vertical-align: top;
		padding:8px;
	}
	#linkMsgRightPaneTop {
		vertical-align: top;
		padding-bottom: 2px;
	}
	#linkMsgRightPaneBottom {
		height:15%;
		max-height:200px;
		min-height:80px;
		padding-top: 2px;
	}
	#linkMsgModTab {
		width:100%;
	}
	.linkMsgModTd {
		margin:2px;
		padding:10px;
		font-weight: 700;
		background-color: #f0f0f0;
		cursor: pointer;
	}
	.linkMsgModTd:hover {
		background-color: #e0e0e0;
	}
	.linkMsgModTdSelected {
		background-color: #e0e0e0;
	}
	.popupLinkTitleRow {
	}
	.popupLinkListTitleRow {
	}
	.popupLinkListTitleCell {
		padding: 4px;
		font-weight: 700;
		border-bottom: 1px solid #b0b0b0;
		text-align: left;
	}
	.popupLinkListDataRow {
		cursor: pointer;
		border-bottom: 1px solid #a0a0a0;
		text-align: left;
		height: 24px;
	}
	.popupLinkListDataRow0 {
		background-color: #f0f0f0;
	}
	.popupLinkListDataRow1 {
	}
	.popupLinkListDataRow:hover, .popupLinkListDataRow.hovered {
		background-color: #e0e0e0;
	}
	.popupLinkListDataExtraRow {
		padding: 6px;
	}
	.popupLinkListDataExtraCell {
		color:#606060;
		border-bottom:1px solid #d0d0d0;
		padding-bottom: 6px !important;
	}
	.popupLinkListDataCell {
		padding: 2px;
	}
	.popupLinkTitle {
		font-weight: 700;
		padding: 4px;
	}
	.popupLinkList {
		overflow-y: auto;
		overflow-x: hidden;
	}
	.popupLinkListLoading {
	}
	.popupLinkListNoData {
		width: 90%;
		text-align: center;
		padding: 10px;
		font-style: italic;
	}
	.navigationBtn {
		cursor: pointer;
	}
	#popupAttachDiv {
		background-color:  #f0f0f0;
		border-top: 2px solid  #e0e0e0;
		width: 100%
	}
	#popupMsgAttachTitle {
		font-weight: 700;
	}
	{/literal}
</style>

<form id="extraInputs" name="extraInputs">
{foreach key=name item=value from=$EXTRA_INPUTS}
<input type="hidden" id="{$name}" name="{$name}" value="{$value}" />
{/foreach}
</form>

{* popup status *}
<div id="status" name="status" style="display:none;position:fixed;right:2px;top:45px;z-index:100">
	{include file="LoadingIndicator.tpl"}
</div>

<table id="linkMsgMainTab" border="0" height="100%">
	<tr>
		<td id="linkMsgLeftPane">
		{* modules list *}
		<div id="linkMsgModCont" height="100%" style="overflow-y:hidden">
			<table id="linkMsgModTab">
				<tr><td align="right">
					<input type="button" onClick="LPOP.clickLinkModule('', 'CreateArea')" value="{'LBL_CREATE_AREA'|getTranslatedString}" class="crmbutton small save">
				</td></tr>
				{foreach item=mod from=$LINK_MODULES}
					<tr><td class="linkMsgModTd" id="linkMsgMod_{$mod.module}" onclick="LPOP.clickLinkModule('{$mod.module}', '{$mod.action}')">{$mod.label}</td></tr>
				{/foreach}
			</table>
			{if $IS_ADMIN eq '1'}
				<div style="position:absolute;bottom:0;width:100%;" align="center">
					<input type="button" onClick="LPOP.clickLinkModule('', 'AreaTools')" value="{'LBL_AREA_TOOLS'|getTranslatedString}" class="crmbutton small edit">
				</div>
			{/if}
		</div>

		</td>
		<td id="linkMsgRightPane">

			<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
			<tr><td id="linkMsgRightPaneTop">

				{* placeholder *}
				<div id="linkMsgDescrCont"></div>

				{* list *}
				<div id="linkMsgListCont" style="display:none"></div>

				{* details *}
				<div id="linkMsgDetailCont" style="display:none"></div>

				{* edit *}
				<div id="linkMsgEditCont" style="display:none"></div>

			</td></tr>
			</table>

		</td>

	</tr>
</table>

<script type="text/javascript">
{literal}
// slightly delay the initialization
jQuery(document).ready(function(){
	setTimeout(function() {
	
		jQuery('#linkMsgModCont').slimScroll({
			wheelStep: 10,
			height: jQuery('body').height()+'px',
			width: '100%'
		});
		var msgRightTopHeight = jQuery('#linkMsgRightPaneTop').height();
		var msgRightBottomHeight = jQuery('#linkMsgRightPaneBottom').height();
		(function(){
			var show_module = jQuery('#show_module').val();
			if (show_module) {
				jQuery('#linkMsgMod_'+show_module).click();
			}
		})();
	
	}, 200);
});
{/literal}
</script>

</body>
</html>