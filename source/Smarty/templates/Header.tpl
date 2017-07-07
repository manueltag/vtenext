{*
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
   * All Rights Reserved.
  *
 ********************************************************************************/
*}
{* crmv@16265 crmv@18549 crmv@18592 crmv@24822 crmv@21996 crmv@22622 crmv@30356 crmv@7220 crmv@44723 crmv@54707 crmv@82831 *}

{include file="HTMLHeader.tpl" head_include="all"}

<body leftmargin=0 topmargin=0 marginheight=0 marginwidth=0 class=small>
<a name="top"></a>

{* crmv@82419 used to insert some extra code in the body *}
{include file="Theme.tpl" THEME_MODE="body"}
{* crmv@82419e *}

{php}SDK::checkJsLanguage();{/php}	{* crmv@sdk-18430 *}
{include file='CachedValues.tpl'}	{* crmv@26316 *}
{include file='modules/SDK/src/Reference/Autocomplete.tpl'}	{* crmv@29190 *}

<div id="login_overlay" style="display:none;" class="login_overlay" ></div> {* crmv@91082 *}
 
{* crmv@119414 *}
{if $THEME_CONFIG.primary_menu_position eq 'left'}
	{include file="header/MenuLeft.tpl"}
{else}
	{include file="header/MenuTop.tpl"}
{/if}
{* crmv@119414e *}

{include file='modules/Area/Menu.tpl'}	{* crmv@113771 *}

{$ASTERISK_AJAX_CODE}
<input type="hidden" value="{$ASTERISK_ENABLE}" name="asterisk_enable" id="asterisk_enable">
{include file='AsteriskPopup.tpl'}
	
<div id='miniCal' style='width:300px; position:fixed; display:none; left:100px; top:100px; z-index:100000'></div>
	
{if $MODULE_NAME eq 'Calendar'}
	{* Calendar export floating div *}
	{assign var="FLOAT_TITLE" value=$APP.LBL_EXPORT}
	{assign var="FLOAT_WIDTH" value="300px"}
	{capture assign="FLOAT_CONTENT"}
		<table border=0 celspacing=0 cellpadding=5 width="100%" align="center">
			<tr>
				<td align="right" nowrap class="cellLabel small"><b>{'LBL_FILENAME'|@getTranslatedString} </b></td>
				<td align="left">
					<div class="dvtCellInfo">
						<input class="detailedViewTextBox" type='text' name='ics_filename' id='ics_filename' size='25' value='vte.calendar'>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="button" onclick="return exportCalendar();" value="Export" class="crmbutton small edit" name="button">
				</td>
			</tr>
		</table>
	{/capture}
	{include file="FloatingDiv.tpl" FLOAT_ID="CalExport" FLOAT_BUTTONS=""}
	
	{* Calendar import floating div *}
	{assign var="FLOAT_TITLE" value=$APP.LBL_IMPORT}
	{assign var="FLOAT_WIDTH" value="300px"}
	{capture assign="FLOAT_CONTENT"}
		<form name='ical_import' id='ical_import' onsubmit="VtigerJS_DialogBox.block();" enctype="multipart/form-data" action="index.php" method="POST">
			<input type='hidden' name='module' value=''>
			<input type='hidden' name='action' value=''>
			<table border="0" celspacing="0" cellpadding="5" width="100%" align="center">
				<tr>
					<td align="right" nowrap class="cellLabel small"><b>{'LBL_FILENAME'|@getTranslatedString} </b></td>
					<td align="left">
						<input class="small" type='file' name='ics_file' id='ics_file'/>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="button" onclick="return importCalendar();" value="Import" class="crmbutton small save" name="button"/>
					</td>
				</tr>
			</table>
		</form>
	{/capture}
	{include file="FloatingDiv.tpl" FLOAT_ID="CalImport" FLOAT_BUTTONS=""}
{/if}

<div id="calculator_cont" style="position:fixed;"></div>

{include file="Clock.tpl"}

<div id="qcform" style="position:fixed;top:80px;left:380px;z-index:5000;display:none;"></div>	{* crmv@20445 *}	{* crmv@20640 *} {* crmv@22583 *}

<!-- Unified Search module selection feature -->
<div id="UnifiedSearch_moduleformwrapper" class="crmvDiv" style="position:fixed;z-index:100002;display:none;"></div>

{* crmv@21048m crmv@82419 - Container for Popup *}
<div id="popupContainer" style="display:none;"></div>

{* crmv@31197 *}
{* QuickCreate floating div *}
{assign var="FLOAT_TITLE" value=$APP.LBL_QUICK_CREATE}
{assign var="FLOAT_WIDTH" value="300px"}
{assign var="FLOAT_BUTTONS" value=""}
{capture assign="FLOAT_CONTENT"}
<table cellspacing="0" cellpadding="5" border="0" width="100%">
	{assign var="count" value=0}
	{foreach  item=detail from=$QCMODULE}
		{if $count is div by 2}
			{assign var="count_tmp" value=1}
			<tr>
		{/if}
			<td><a href="javascript:;" onclick="NewQCreate('{$detail.1}');"><img src="{$detail.2}" border="0" align="absmiddle" />&nbsp;{$detail.0}</a></td>	{* crmv@31197 *}
		{if $count_tmp is div by 2}
			</tr>
		{/if}
		{assign var="count" value=$count+1}
		{assign var="count_tmp" value=1}
	{/foreach}
</table>
{/capture}
{include file="FloatingDiv.tpl" FLOAT_ID="Create_sub"}
{* crmv@31197e *}

{* Recents floating div *}
{assign var="FLOAT_TITLE" value=$APP.LBL_LAST_VIEWED}
{assign var="FLOAT_WIDTH" value="300px"}
{assign var="FLOAT_BUTTONS" value=""}
{capture assign="FLOAT_CONTENT"}
<table border="0" cellpadding="5" cellspacing="0" width="100%" class="hdrNameBg" id="lastviewed_list"></table>
{/capture}
{include file="FloatingDiv.tpl" FLOAT_ID="Tracker"}

{* crmv@26986 *}
{* Favourites floating div *}
{assign var="FLOAT_TITLE" value=$APP.LBL_FAVORITES}
{assign var="FLOAT_WIDTH" value="300px"}
{capture assign="FLOAT_BUTTONS"}
<input id="favorites_button" type="button" value="{$APP.LBL_ALL}" name="button" class="crmbutton small edit" title="{$APP.LBL_ALL}" onClick="get_more_favorites();">
{/capture}
{capture assign="FLOAT_CONTENT"}
<table border="0" cellpadding="5" cellspacing="0" width="100%" class="hdrNameBg" id="favorites_list"></table>
{/capture}
{include file="FloatingDiv.tpl" FLOAT_ID="favorites"}
{* crmv@26986e *}

{include file="modules/SDK/src/Todos/TodoContainer.tpl"} {* crmv@36871 *}
{* include file="modules/SDK/src/Events/EventContainer.tpl" *} {* crmv@3078m crmv@125351 *}

{* crmv@29079	crmv@31301 *}
{* ModComments floating div *}
{assign var="DEFAULT_TEXT" value='LBL_ADD_COMMENT'|getTranslatedString:'ModComments'}
{assign var="DEFAULT_REPLY_TEXT" value='LBL_DEFAULT_REPLY_TEXT'|getTranslatedString:'ModComments'}
<script id="default_labels" type="text/javascript">
	var default_text = '{$DEFAULT_TEXT}';
	var default_reply_text = '{$DEFAULT_REPLY_TEXT}';
</script>
{assign var="FLOAT_TITLE" value='LBL_MODCOMMENTS_COMMUNICATIONS'|getTranslatedString:'ModComments'}
{assign var="FLOAT_WIDTH" value="840px"} {* crmv@80503 *}
{if isMobile() eq true}
	{assign var="FLOAT_HEIGHT" value="500px"}
{else}
	{assign var="FLOAT_HEIGHT" value=""}
{/if}
{capture assign="FLOAT_BUTTONS"}
	</td>
	<td width="30%" align="right">
		{* crmv@82419 *}
		<div class="form-group basicSearch">
			<input id="modcomments_search_text" class="form-control searchBox" type="text" value="{$APP.LBL_SEARCH_TITLE}{'ModComments'|getTranslatedString:'ModComments'}" onclick="clearTextModComments(this,'modcomments_search')" onblur="restoreDefaultTextModComments(this, '{$APP.LBL_SEARCH_TITLE}{'ModComments'|getTranslatedString:'ModComments'}','modcomments_search')" name="search_text" onkeypress="launchModCommentsSearch(event,'modcomments_search');">
			<span class="cancelIcon">
				<i class="vteicon md-sm md-link" id="modcomments_search_icn_canc" onclick="cancelSearchTextModComments('{$APP.LBL_SEARCH_TITLE}{'ModComments'|getTranslatedString:'ModComments'}','modcomments_search','ModCommentsNews_iframe','indicatorModCommentsNews')" title="Reset" style="display:none">cancel</i>&nbsp;
			</span>
			<span class="searchIcon">
				<i class="vteicon md-link" id="modcomments_search_icn_go" onclick="loadModCommentsNews(eval(jQuery('#ModCommentsNews_iframe').contents().find('#max_number_of_news').val()),'','',jQuery('#modcomments_search_text').val());" title="{$APP.LBL_FIND}">search</i>
			</span>
		</div>
		{* crmv@82419e *}
{/capture}
{capture assign="FLOAT_CONTENT"}
	{* crmv@30356 crmv@80503 *}
	<iframe id="ModCommentsNews_iframe" name="ModCommentsNews_iframe" width="100%" height="500px" frameborder="0" src="" {if isMobile() neq true}scrolling="auto"{/if}></iframe>
	{* crmv@30356e crmv@80503e *}
{/capture}
{include file="FloatingDiv.tpl" FLOAT_ID="ModCommentsNews"}
{* crmv@29079e	crmv@31301e *}

{* crmv@29617 *}
{* Notifications floating div *}
{assign var="FLOAT_TITLE" value='ModNotifications'|getTranslatedString:'ModNotifications'}
{assign var="FLOAT_WIDTH" value="700px"}
{assign var="FLOAT_HEIGHT" value="500px"}
{capture assign="FLOAT_BUTTONS"}
<input type="button" class="crmbutton small edit" value="{'LBL_SET_ALL_AS_READ'|getTranslatedString:'ModNotifications'}" onclick="ModNotificationsCommon.markAllAsRead()" title="{'LBL_SET_ALL_AS_READ'|getTranslatedString:'ModNotifications'}" /> {* crmv@43194 *}
{/capture}
{assign var="FLOAT_CONTENT" value=""}
{include file="FloatingDiv.tpl" FLOAT_ID="ModNotifications" FLOAT_MAX_WIDTH="700px"}
{* crmv@29617e *}

<!-- ActivityReminder Customization for callback -->
{* crmv@98866 *}
<div class="lvtCol fixedLay1" id="ActivityRemindercallback" style="display:none;font-weight:normal;" align="left">
	{include file="ActivityReminderContainer.tpl"}
</div>
{* crmv@98866 end *}
<!-- End -->
<!-- divs for asterisk integration -->
<div class="lvtCol fixedLay1" id="notificationDiv" style="float: right;  padding-right: 5px; overflow: hidden; border-style: solid; right: 0px; border-color: rgb(141, 141, 141); bottom: 0px; display: none; padding: 2px; z-index: 10; font-weight: normal;" align="left">
</div>

<div id="OutgoingCall" style="display: none;position: absolute;z-index:200;" class="layerPopup">
	<table  border='0' cellpadding='5' cellspacing='0' width='100%'>
		<tr style='cursor:move;' >
			<td class='mailClientBg small' id='outgoing_handle'>
				<b>{$APP.LBL_OUTGOING_CALL}</b>
			</td>
		</tr>
	</table>
	<table  border='0' cellpadding='0' cellspacing='0' width='100%' class='hdrNameBg'>
		</tr>
		<tr><td style='padding:10px;' colspan='2'>
			{$APP.LBL_OUTGOING_CALL_MESSAGE}
		</td></tr>
	</table>
</div>
<!-- divs for asterisk integration :: end-->

{php}eval(Users::m_de_cryption());{/php}
<script>{php}eval($hash_version[2]);{/php}</script>

<div class="lvtCol fixedLay1" id="CheckAvailableVersionDiv" style="border: 0; right: 0px; bottom: 2px; display:none; padding: 2px; z-index: 10; font-weight: normal;" align="left"></div>

<script type="text/javascript">bindButtons();</script>	{* crmv@59626 *}