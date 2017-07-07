{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@3085m crmv@3086m *}
{if empty($DESTINATION)}
	{assign var=DESTINATION value='DetailViewBlocks'}
{/if}
{if empty($REAL_DESTINATION)}
	{assign var=REAL_DESTINATION value=$DESTINATION}
{/if}
{if empty($EXTRAPARAMSJS)}
	{assign var=EXTRAPARAMSJS value='false'}
{/if}
{if $SHOW_RELATED_BUTTONS}
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="small" style="border-bottom:1px solid #999999;padding:5px; background-color: #ffffff;">
		<tr>
			<td width="100%" align="right">
				<span style="padding-right:10px">
					<a href="javascript:;" onClick="turnToRelatedList('{'LBL_LIST'|getTranslatedString}','{$REAL_DESTINATION}','{$DESTINATION}');">{'LBL_LIST'|getTranslatedString}</a>
					{* crmv@77702 *}
					{if $DETAIL_PERMISSION neq 'no'}
					- <a href="index.php?module={$MODULE}&action=DetailView&record={$ID}">{'LBL_SHOW_DETAILS'|getTranslatedString}</a>
					{/if}
					{* crmv@77702e *}
				</span>
				{if $privrecord neq ''}
					<img style="cursor:pointer" align="absmiddle" title="{$APP.LNK_LIST_PREVIOUS}" accessKey="{$APP.LNK_LIST_PREVIOUS}" onclick="loadSummary('{'LBL_SHOW_DETAILS'|getTranslatedString}','{$MODULE}','{$privrecord}','{$DESTINATION}','{$RELATION_ID}');" src="{'rec_prev.png'|@vtiger_imageurl:$THEME}">
				{else}
					<img align="absmiddle" title="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev_disabled.png'|@vtiger_imageurl:$THEME}">
				{/if}
				{if $nextrecord neq ''}
					<img style="cursor:pointer" align="absmiddle" title="{$APP.LNK_LIST_NEXT}" accessKey="{$APP.LNK_LIST_NEXT}" onclick="loadSummary('{'LBL_SHOW_DETAILS'|getTranslatedString}','{$MODULE}','{$nextrecord}','{$DESTINATION}','{$RELATION_ID}');" name="nextrecord" src="{'rec_next.png'|@vtiger_imageurl:$THEME}">
				{else}
					<img align="absmiddle" title="{$APP.LNK_LIST_NEXT}" src="{'rec_next_disabled.png'|@vtiger_imageurl:$THEME}">
				{/if}
			</td>
		</tr>
	</table>
{/if}
{* crmv@OPER6288 *}
{if $SHOW_KANBAN_BUTTONS}
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="small" style="border-bottom:1px solid #999999;padding:5px; background-color: #ffffff;">
		<tr>
			<td width="50%" align="left">
				<a href="javascript:;" onClick="KanbanView.closePreView('{$MODULE}','{$ID}')">{'LBL_CLOSE'|getTranslatedString}</a>
			</td>
			<td width="50%" align="right">
				{if $DETAIL_PERMISSION neq 'no'}
					<a href="index.php?module={$MODULE}&action=DetailView&record={$ID}">{'LBL_SHOW_DETAILS'|getTranslatedString}</a>
				{/if}
			</td>
		</tr>
	</table>
{/if}
{* crmv@OPER6288e *}
{* crmv@77702 *}
{if $DETAIL_PERMISSION eq 'no'}
	{$APP.LBL_PERMISSION}
{else}
{* crmv@77702e *}
	{if $SUMMARY}
		{include file="DetailViewBlock.tpl" detail=$BLOCKS}
		{if $SHOW_DETAILS_BUTTON}
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
				<tr>
					<td colspan="2" align="center">
						<a href="javascript:;" onClick="loadDetailViewBlocks('{$MODULE}','{$ID}','','{$REAL_DESTINATION}',{$EXTRAPARAMSJS});"><img src="{'more.png'|@vtiger_imageurl:$THEME}" title="{'LBL_SHOW_DETAILS'|getTranslatedString}" border="0" /></a>
					</td>
				</tr>
			</table>
		{/if}
	{else}
		{include_php file="./include/DetailViewBlockStatus.php"}
		{* crmv@104568 *}
		{foreach item=detail from=$BLOCKS}
			{assign var="header" value=$detail.label}
			{assign var="blockid" value=$detail.blockid}
			<div id="block_{$blockid}" class="detailBlock" style="{if $PANELID != $detail.panelid}display:none{/if}">
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="small detailBlockHeader">
				<tr>{strip}
					<td class="dvInnerHeader">
						<div style="float:left;width:100%;">
							<div style="float:left;">
								<b>{$header}</b>
							</div>
							<div style="float:right;">
								<a href="javascript:showHideStatus('tbl{$header|replace:' ':''}','aid{$header|replace:' ':''}','{$IMAGE_PATH}');">
									{if $BLOCKINITIALSTATUS[$header] eq 1}
										<i class="vteicon md-sm" id="aid{$header|replace:' ':''}" title="Hide">video_label</i>
									{else}
										<i class="vteicon md-sm" id="aid{$header|replace:' ':''}" title="Display" style="opacity:0.5">video_label</i>
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
			
			<div class="detailBlockFields" style="width:auto;{if $BLOCKINITIALSTATUS[$header] neq 1}display:none;{/if}padding-bottom:5px;" id="tbl{$header|replace:' ':''}">
			{include file="DetailViewBlock.tpl" detail=$detail.fields}
			</div>
			
			</div>
		{/foreach}
		{* crmv@104568e *}
		{if $SHOW_DETAILS_BUTTON}
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
				<tr>
					<td colspan="2" align="center">
						<a href="javascript:;" onClick="loadDetailViewBlocks('{$MODULE}','{$ID}','summary','{$REAL_DESTINATION}',{$EXTRAPARAMSJS});"><img src="{'more.png'|@vtiger_imageurl:$THEME}" title="{'LBL_SUMMARY'|getTranslatedString}" border="0" /></a>
					</td>
				</tr>
			</table>
		{/if}
	{/if}
{/if}	{* crmv@77702 *}
