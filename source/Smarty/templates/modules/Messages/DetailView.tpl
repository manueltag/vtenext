{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
 
{* crmv@59094 *}

<script type="text/javascript">
var gVTModule = '{$smarty.request.module|@vtlib_purify}';
var messageMode = '{$MESSAGE_MODE}';
</script>

<div id="lstRecordLayout" class="layerPopup crmvDiv" style="display:none;width:320px;height:300px;z-index:21;position:fixed;"></div>	{*<!-- crmv@18592 -->*}

<form action="index.php" method="post" name="DetailView" id="form">
{include file='DetailViewHidden.tpl'}

{if $HEADER_BLOCK neq ''}
	{include file="modules/Messages/DetailViewHeader.tpl"}
{/if}

{* crmv@68357 *}
{if $ICALS && count($ICALS) > 0}
	{include file="modules/Messages/IcalMainHeader.tpl"}
	{foreach item=ICAL key=ICALID from=$ICALS}
		{include file="modules/Messages/IcalDisplay.tpl"}
	{/foreach}
{else}

{* hide body if it's an invitation/reply *}
{if $DESCRIPTION neq '' || $BUTTON_DOWNLOAD_DESCRIPTION}
	<div style="padding:5px">
	<table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
		<tr style="height:25px">
			<td width="100%">
				{if $DESCRIPTION neq ''}
					<div id="DetailViewContentDescr" style="word-wrap: break-word;">{$DESCRIPTION}</div>
				{elseif $BUTTON_DOWNLOAD_DESCRIPTION}
					<div align="center" style="height:50px;"><a href="javascript:;" onClick="fetchBody({$ID});"><br />{'LBL_DOWNLOAD_BODY_MESSAGE'|getTranslatedString:'Messages'}</a></div>
				{/if}
			</td>
		</tr>
		{if !empty($INLINE_ATTACHMENTS)}
			{foreach key=contentid item=attach from=$INLINE_ATTACHMENTS}
				<tr style="height:25px;">
					<td width="100%" align="center" style="padding-top:5px;">
						<img border="0" src="index.php?module=Messages&action=MessagesAjax&file=Download&record={$ID}&contentid={$contentid}&mode=inline" style="max-width:400px">	{* crmv@65648 crmv@80250 *}
					</td>
				</tr>
			{/foreach}
		{/if}
	</table>
	</div>
{/if}

{/if} {* ical end *}
{* crmv@68357e *}

{if !empty($ATTACHMENTS)}
	<div style="padding: 5px; border-bottom:1px solid #E0E0E0;">
	<table width="100%" cellspacing="0" cellpadding="0">
	<tr height="30px" valign="top">
		<td colspan="2" style="color: gray;">
			{assign var="ATTACH_COUNT" value=$ATTACHMENTS|@count}
			{if $ATTACH_COUNT eq 1}
				{assign var="ATTACH_LABEL" value=$MOD.LBL_ATTACHMENT}
			{elseif $ATTACH_COUNT gt 1}
				{assign var="ATTACH_LABEL" value=$MOD.LBL_ATTACHMENTS}
			{/if}
			<img src="modules/Messages/src/img/flag_attach.png" style="vertical-align:middle;" border="0" />&nbsp;{$ATTACH_COUNT} {$ATTACH_LABEL}
			{* crmv@62340 *}
			{if $ATTACH_COUNT gt 1} 
				<a href="index.php?module=Messages&action=MessagesAjax&file=DownloadAttachments&record={$ID}" ><i class="vteicon md-text" title="{$MOD.LBL_DOWNLOAD_ALL}" style="vertical-align:middle;">file_download</i> {$MOD.LBL_DOWNLOAD_ALL}</a>
			{/if}
			{* crmv@62340e *}
		</td>
	</tr>
	{foreach item=attach from=$ATTACHMENTS}
		<tr class="lvtColData" onMouseOver="this.className='lvtColDataHover'" onMouseOut="this.className='lvtColData'">
			<td nowrap style="padding-right:10px;">
				{if $attach.action_download}
					<a href="{$attach.link}" {if $attach.target neq ''}target="{$attach.target}{/if}"><i class="vteicon md-text" title="{$MOD.LBL_DOWNLOAD}">file_download</i></a>
				{/if}
				{if $attach.action_save}
					{if empty($attach.document)}
						<a href="javascript:;" onClick="saveDocument({$ID},'{$attach.contentid}','','','yes');"><i class="vteicon md-text" title="{$MOD.LBL_SAVE_IN_DOCUMENTS_ACTION}">folder</i></a>
					{else}
						<a href="javascript:;" onClick="preView('Documents','{$attach.document}');"><i class="vteicon md-text" title="{$MOD.LBL_DOCUMENT_ALREADY_CREATED}">folder_special</i></a> {* crmv@62414 *}
					{/if}
				{/if}
				{if $attach.action_link}
					<a href="javascript:;" onClick="saveDocumentAndLink({$ID},'{$attach.contentid}');"><i class="vteicon md-text" title="{$MOD.LBL_SAVE_AND_LINK_ACTION}">open_in_new</i></a>
				{/if}
				{* crmv@62414 *}
				{if $attach.action_view}
					<a href="javascript:;" onClick="{$attach.action_view_JSfunction}({$ID},'{$attach.contentid}');"><i class="vteicon md-text" title="{$MOD[$attach.action_view_label]}">remove_red_eye</i></a>
				{/if}
				{* crmv@62414e *}
			</td>
			<td width="100%">
				<b>{$attach.name}</b>
			</td>
		</tr>
	{/foreach}
	</table>
	</div>
{/if}

{* vtlib Customization: Embed DetailViewWidget block:// type if any *}
{if $CUSTOM_LINKS && !empty($CUSTOM_LINKS.DETAILVIEWWIDGET)}
{foreach item=CUSTOM_LINK_DETAILVIEWWIDGET from=$CUSTOM_LINKS.DETAILVIEWWIDGET}
	{if preg_match("/^block:\/\/.*/", $CUSTOM_LINK_DETAILVIEWWIDGET->linkurl)}
	<!-- crmv@18485 -->
	{php}
		$widgetLinkInfo_tmp = $this->_tpl_vars['CUSTOM_LINK_DETAILVIEWWIDGET'];
		if (preg_match("/^block:\/\/(.*)/", $widgetLinkInfo_tmp->linkurl, $matches)) {
			list($widgetControllerClass_tmp, $widgetControllerClassFile_tmp) = explode(':', $matches[1]);
			if (vtlib_isModuleActive($widgetControllerClass_tmp)) {
	{/php}
	<!-- crmv@18485e -->
	<table width="100%" cellspacing="0" cellpadding="0" class="DetailViewWidget"><tr><td>
		<tr>
			<td style="padding:5px;" >
			{php}
				echo vtlib_process_widget($this->_tpl_vars['CUSTOM_LINK_DETAILVIEWWIDGET'], $this->_tpl_vars);
			{/php}
			</td>
		</tr>
	</table>
	<!-- crmv@18485 -->
	{php}}}{/php}
	<!-- crmv@18485e -->
	{/if}
{/foreach}
{/if}
{* END *}

</form>

<form name="SendMail" onsubmit="VtigerJS_DialogBox.block();"><div id="sendmail_cont" style="z-index:100001;position:absolute;"></div></form>
<form name="SendFax" onsubmit="VtigerJS_DialogBox.block();"><div id="sendfax_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
<!-- crmv@16703 -->
<form name="SendSms" id="SendSms" onsubmit="VtigerJS_DialogBox.block();" method="POST" action="index.php"><div id="sendsms_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
<!-- crmv@16703e -->

<div id="Button_List_Detail_Container" style="display:none;">
	{include file='modules/Messages/DetailViewButtons.tpl'}
</div>
<script language="javascript">
jQuery('#Button_List_Detail').html(jQuery('#Button_List_Detail_Container').html());
</script>