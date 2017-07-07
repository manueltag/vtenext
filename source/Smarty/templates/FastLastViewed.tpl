{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@119414 *}

{if !empty($HISTORY)}
<table id="lastViewed" class="table table-hover">
	{foreach from=$HISTORY item=histo name="histo"}
		{assign var="crmid" value=$histo.crmid}
		{assign var="moduleType" value=$histo.module_type}
		{assign var="itemSummary" value=$histo.item_summary}
		{assign var="moduleName" value=$histo.module_name}
		{assign var="moduleNameLower" value=$moduleName|strtolower}
		{assign var="moduleFirstLetter" value=$moduleName|substr:0:1|strtoupper}
		
		{assign var="entityType" value="SINGLE_"|cat:$moduleType|getTranslatedString:$moduleName}
		{if empty($entityType) || $entityType eq "SINGLE_"|cat:$moduleType}
			{assign var="entityType" value=$moduleType|getTranslatedString:$moduleName}
		{/if}
		
		{if $smarty.foreach.histo.iteration eq 1}
			<tr>
				<td colspan="3" class="fastPanelTitle" style="border-top:0px none">
					<h4>{"LBL_LAST_VIEWED"|getTranslatedString}</h4>
				</td>
			</tr>
		{/if}
		
		<tr class="fastList1LevelIcon">
			<td width="10%" class="fastListIcon">
				<div class="smallCircle">
					<i class="vteicon icon-module icon-{$moduleNameLower} nohover" data-first-letter="{$moduleFirstLetter}"></i>
				</div>
			</td>
			<td width="75%" class="fastListText">
				<a href="index.php?module={$moduleName}&action=DetailView&record={$crmid}">
					{$itemSummary}
				</a>
			</td>
			<td width="15%" class="fastListModule">
				<span>{$moduleType}</span>
			</td>
		</tr>
	{/foreach}
</table>
{else}
	<div class="fastEmptyMask">
		<div class="fastEmptyMaskInner">
			<div class="smallCircle fastMaskIcon">
				<i class="vteicon nohover">list</i>
			</div>
			<span class="fastMaskText">
				{"LBL_NO_LASTVIEWED"|getTranslatedString}
			</span>
		</div>
	</div>
{/if}
