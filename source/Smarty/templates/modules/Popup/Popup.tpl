{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}

{* crmv@42752 crmv@43050 crmv@43864 crmv@82831 crmv@98810 *}

{include file="SmallHeader.tpl"}
{include file="modules/SDK/src/Reference/Autocomplete.tpl"}

<link href="themes/{$THEME}/popup.css" rel="stylesheet" type="text/css" />

<link href="include/js/jquery_plugins/mCustomScrollbar/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
<script src="include/js/jquery_plugins/mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
<script language="javascript" type="text/javascript" src="include/js/jquery_plugins/slimscroll/jquery.slimscroll.min.js"></script>
<link href="include/js/jquery_plugins/mCustomScrollbar/VTE.mCustomScrollbar.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="{"include/js/ListView.js"|resourcever}"></script>
<script type="text/javascript" src="{"include/js/dtlviewajax.js"|resourcever}"></script>
<script type="text/javascript" src="{"include/js/vtlib.js"|resourcever}"></script>
<script type="text/javascript" src="{"modules/SDK/src/Notifications/NotificationsCommon.js"|resourcever}"></script>
<script type="text/javascript" src="{"modules/Popup/Popup.js"|resourcever}"></script>

{* populate global JS variables *}
<script type="text/javascript">setGlobalVars('{$JS_GLOBAL_VARS|replace:"'":"\'"}');</script> {* crmv@70731 *}

{* crmv@73108 *}
{if $HEADERSCRIPTS}
	{foreach item=HEADERSCRIPT from=$HEADERSCRIPTS}
		<script type="text/javascript" src="{$HEADERSCRIPT->linkurl}"></script>
	{/foreach}
{/if}
{* crmv@73108 e *}

{if count($EXTRA_JS) > 0}
	{foreach item=JSPATH from=$EXTRA_JS}
	<script type="text/javascript" src="{$JSPATH}"></script>
	{/foreach}
{/if}

<form id="extraInputs" name="extraInputs">
{foreach key=name item=value from=$EXTRA_INPUTS}
	{if strpos($value, '"') !== false}
		<input type="hidden" id="{$name}" name="{$name}" value='{$value}' />
	{else}
		<input type="hidden" id="{$name}" name="{$name}" value="{$value}" />
	{/if}
{/foreach}
</form>

{* popup status *}
<div id="status" name="status" style="display:none;position:fixed;right:2px;top:45px;z-index:100">
	{include file="LoadingIndicator.tpl"}
</div>

<table id="linkMsgMainTab" border="0" height="100%">
	<tr>
		{* crmv@98866 *}
		<td id="linkMsgLeftPane" class="nopadding">
		{* modules list *}
		<div id="linkMsgModCont" height="100%" style="overflow-y:hidden">
			<table id="linkMsgModTab">
				{foreach item=mod from=$LINK_MODULES}
					<tr>
						{assign var="module" value=$mod.module}
						{assign var="module_lower" value=$module|strtolower}
						{assign var="trans_module" value=$module|getTranslatedString:$module}
						{assign var="first_letter" value=$trans_module|substr:0:1|strtoupper}
						
						<td class="linkMsgModTd" id="linkMsgMod_{$module}" onclick="LPOP.clickLinkModule('{$module}', '{$mod.action}', '{$mod.relation_id}')">
							<div class="vcenter text-left" style="width:15%">
								<i class="material-icons icon-module icon-{$module_lower}" data-first-letter="{$first_letter}"></i>
							</div>
							<span class="vcenter">{$trans_module}</span>
						</td>
					</tr> {* crmv@56603 *}
				{/foreach}
			</table>
		</div>
		{* crmv@98866 end *}

		</td>
		<td id="linkMsgRightPane">

			<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
			<tr><td id="linkMsgRightPaneTop">

				{* placeholder *}
				<div id="linkMsgDescrCont">{'LBL_SELECT_A_MODULE'|getTranslatedString}</div>

				{* list *}
				<div id="linkMsgListCont" style="display:none"></div>

				{* details *}
				<div id="linkMsgDetailCont" style="display:none"></div>

				{* edit *}
				<div id="linkMsgEditCont" style="display:none"></div>

			</td></tr>

			{if count($ATTACHMENTS) > 0}
			<tr><td id="linkMsgRightPaneBottom">

				<div id="linkMsgAttachCont" style="display:none">

{* attachments *}
<div id="popupAttachDiv">
	<div id="popupMsgAttachTitle">
		<input id="popupMsgAttachMainCheck" type="checkbox" name="" value="" checked="" onchange="messagesChangeAttach()" /> {$MOD.LBL_INCLUDE_ATTACH}
	</div>
	<div id="popupMsgAttachList">
		<table border="0" cellspacing="0" cellpadding="1">
		{foreach item=ATT from=$ATTACHMENTS}
			{assign var=inputName value='msgattach_'|cat:$ATT.contentid}
			<tr>
				<td>&nbsp;<input class="popupMsgAttachCheck" type="checkbox" name="{$inputName}" id="{$inputName}" value="" checked="" onchange="messagesChangeSingleAtt(this)" /></td>
				<td>{$ATT.name}</td>
			</tr>
		{/foreach}
		</table>
	</div>
</div>

				</div>

			</td></tr>
			{/if}

			</table>

		</td>

	</tr>
</table>
<script type="text/javascript">
{literal}

// crmv@103862
// slightly delay the initialization
jQuery(document).ready(function() {
	setTimeout(function() {
		
		jQuery('#linkMsgModCont').slimScroll({
			wheelStep: 10,
			height: jQuery('body').height()+20+'px',
			width: '100%'
		});

		var msgRightTopHeight = jQuery('#linkMsgRightPaneTop').height();
		var msgRightBottomHeight = jQuery('#linkMsgRightPaneBottom').height();

		// scroll for attachments
		jQuery('#popupMsgAttachList').slimScroll({
			wheelStep: 10,
			height: (msgRightBottomHeight-8)+'px',
			width: '100%'
		});

		(function(){
			var show_module = jQuery('#show_module').val();
			if (show_module) {
				jQuery('#linkMsgMod_'+show_module).click();
			}
		})();
		
	}, 100);
	
});
// crmv@103862e

{/literal}
</script>

</body>
</html>