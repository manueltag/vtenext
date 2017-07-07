{*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************}

{* crmv@104568 *}
 
{* crmv@43864 *}
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
{* crmv@43864 *}

<span id="crmspanid" style="display:none;position:absolute;" onmouseover="show('crmspanid');">
   <a class="edit" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>

<div id="lstRecordLayout" class="layerPopup crmvDiv" style="display:none;width:320px;height:300px;z-index:21;position:fixed;"></div>	{* crmv@18592 *}

<table width="100%" cellpadding="0" cellspacing="0" border="0"> {* crmv@25128 *}
<tr>
	<td>

<!-- Contents -->
{* crmv@18592 *}
<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
<tr>
	<table border=0 cellspacing=0 cellpadding=0 width="100%" class="small" style="padding:10px;border-bottom:1px solid #ddf;">
	<tr>
		<td colspan="2" align="left" class="listMessageFrom">{$NAME}</td>
	</tr>
	<tr>
		<td align="left" style="color: gray;">{$UPDATEINFO}</td>
		<td align="right">
			{if $HIDE_BUTTON_LIST neq '1'} {* crmv@42752 *}
			<a href="javascript:;" onClick="preView('{$MODULE}',{$ID});">{$RETURN_MODULE|getSingleModuleName}</a>&nbsp;-
			<a href="index.php?module={$MODULE}&action=DetailView&record={$ID}">{$APP.LBL_SHOW_DETAILS}</a>
			{/if}
		</td>
	</tr>
	</table>
</tr>
<tr>
	<td class="showPanelBg" valign=top width=100% style="padding:5px;">
		{* crmv@104568 *}
		{if !empty($DETAILTABS) && count($DETAILTABS) > 1}
		<table border=0 cellspacing=0 cellpadding=3 width=100% class="small" id="DetailViewTabs">
			<tr>
				{* crmv@45699 *}
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
					<td class="{$_class}" align="center" onClick="{$_tab.onclick}" nowrap="" data-panelid="{$_tab.panelid}"><a href="{$_href}">{$_tab.label}</a></td>
				{/foreach}
				<td class="dvtTabCache" align="right" style="width:100%"></td>
				{* crmv@45699e *}
			</tr>
		</table>
		{/if}
		{* crmv@104568e *}
		
		<!-- PUBLIC CONTENTS STARTS-->
		<div class="small" style="padding:0px" >
		<!-- Account details tabs -->
		<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
		<tr>
			<td valign=top align=left >
				<table border=0 cellspacing=0 cellpadding=0 width=100% style="border-bottom:0;">
				<tr>

					<td align=left valign="top"> {* crmv@20260 *}
					<!-- content cache -->


				<table border=0 cellspacing=0 cellpadding=0 width=100%>
                <tr>
					<td>

							 <!-- NOTE: We should avoid form-inside-form condition, which could happen when
								Singlepane view is enabled. -->
							 <form action="index.php" method="post" name="DetailView" id="form">
							{include file='DetailViewHidden.tpl'}

							{include_php file="./include/DetailViewBlockStatus.php"}

							{* crmv@104568 *}
							{foreach item=detail from=$BLOCKS}
							{assign var="header" value=$detail.label}
							{assign var="blockid" value=$detail.blockid}
							<div id="block_{$blockid}" class="detailBlock" style="{if $PANELID != $detail.panelid}display:none{/if}">
							<!-- Detailed View Code starts here-->
							<table border=0 cellspacing=0 cellpadding=0 width=100% class="small">

							<tr>{strip}
								<td class="dvInnerHeader">
									<div style="float:left;font-weight:bold;width:100%;">
										<div style="float:left;">
											<b>{$header}</b>
										</div>
										<div style="float:right;">
											<a href="javascript:showHideStatus('tbl{$header|replace:' ':''}','aid{$header|replace:' ':''}','{$IMAGE_PATH}');">
												{if $BLOCKINITIALSTATUS[$header] eq 1}
													<img id="aid{$header|replace:' ':''}" src="{'windowMinMax.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;" alt="Hide" title="Hide"/>
												{else}
													<img id="aid{$header|replace:' ':''}" src="{'windowMinMax-off.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;" alt="Display" title="Display"/>
												{/if}
											</a>
										</div>
										<div style="float:right;">
											{if $header eq $MOD.LBL_ADDRESS_INFORMATION && ($MODULE eq 'Accounts' || $MODULE eq 'Contacts' || $MODULE eq 'Leads') }
												{if $MODULE eq 'Leads'}
													<input name="mapbutton" value="{$APP.LBL_LOCATE_MAP}" class="crmbutton small create" type="button" onClick="searchMapLocation( 'Main' )" title="{$APP.LBL_LOCATE_MAP}">
												{else}
													<input name="mapbutton" value="{$APP.LBL_LOCATE_MAP}" class="crmbutton small create" type="button" onClick="fnvshobj(this,'locateMap');" onMouseOut="fninvsh('locateMap');" title="{$APP.LBL_LOCATE_MAP}">
												{/if}
											{/if}
										</div>
									</div>
								</td>{/strip}
							</tr>
							</table>
							{if $BLOCKINITIALSTATUS[$header] eq 1}
								<div style="width:auto;display:block;" id="tbl{$header|replace:' ':''}" >
							{else}
								<div style="width:auto;display:none;" id="tbl{$header|replace:' ':''}" >
							{/if}
								{include file="DetailViewBlock.tpl" detail=$detail.fields}
							</div>
							</div>
							{/foreach}
							{*-- End of Blocks--*}
							{* crmv@104568e *}
					</td>
				</tr>
	<tr>
		<td>
			
			
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
					<tr>
						<td style="padding:5px;" >
						{php}
							echo vtlib_process_widget($this->_tpl_vars['CUSTOM_LINK_DETAILVIEWWIDGET'], $this->_tpl_vars);
						{/php}
						</td>
					</tr>
				<!-- crmv@18485 -->
				{php}}}{/php}
				<!-- crmv@18485e -->
				{/if}
			{/foreach}
			{/if}
			{* END *}

		</td>
	</tr>
	<!-- Inventory - Product Details informations -->
	<tr>
		<td >
			{$ASSOCIATED_PRODUCTS}
		</td>
		</tr>
		</td>
	</tr>

			</form>

		</table>

		</td>
		</tr>
		</table>

		</div>
		<!-- PUBLIC CONTENTS STOPS-->
	</td>
</tr>
</table>

<!-- added for validation -->
<script language="javascript">
  var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
  var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
  var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
  var fielduitype = new Array({$VALIDATION_DATA_FIELDUITYPE}); // crmv@83877
  var fieldwstype = new Array({$VALIDATION_DATA_FIELDWSTYPE}); //crmv@112297
</script>
</td>

</tr></table>

<form name="SendMail" onsubmit="VtigerJS_DialogBox.block();"><div id="sendmail_cont" style="z-index:100001;position:absolute;"></div></form>
<form name="SendFax" onsubmit="VtigerJS_DialogBox.block();"><div id="sendfax_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
<!-- crmv@16703 -->
<form name="SendSms" id="SendSms" onsubmit="VtigerJS_DialogBox.block();" method="POST" action="index.php"><div id="sendsms_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
<!-- crmv@16703e -->

<script language="javascript">
//showHideStatus('tblModCommentsDetailViewBlockCommentWidget','aidModCommentsDetailViewBlockCommentWidget','{$IMAGE_PATH}');
</script>

{* crmv@104568 *}
<script type="text/javascript">
	{if $PANEL_BLOCKS}
	var panelBlocks = {$PANEL_BLOCKS};
	{else}
	var panelBlocks = {ldelim}{rdelim};
	{/if}
</script>
{* crmv@104568e *}