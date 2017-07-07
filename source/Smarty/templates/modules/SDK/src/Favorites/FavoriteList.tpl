{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@119414 *}

{if !empty($FAV_LIST)}
	<table id="favoriteList" class="table table-hover">
		{foreach from=$FAV_LIST item=fav name="fav"}
			{assign var="module" value=$fav.module}
			{assign var="crmid" value=$fav.crmid}
			{assign var="name" value=$fav.name}
			{assign var="moduleNameLower" value=$module|strtolower}
			{assign var="moduleFirstLetter" value=$module|substr:0:1|strtoupper}
			
			{assign var="entityType" value="SINGLE_"|cat:$module|getTranslatedString:$module}
			{if empty($entityType) || $entityType eq "SINGLE_"|cat:$module}
				{assign var="entityType" value=$module|getTranslatedString:$module}
			{/if}
			
			{if $smarty.foreach.fav.iteration eq 1}
				<tr>
					<td colspan="3" class="fastPanelTitle" style="border-top:0px none">
						<h4>{"LBL_FAVORITES"|getTranslatedString}</h4>
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
					<a href="index.php?module={$module}&action=DetailView&record={$crmid}">{$name}</a>
				</td>
				<td width="15%" class="fastListModule">
					<span>{$entityType}</span>
				</td>
			</tr>
		{/foreach}
	</table>
{else}
	<div class="fastEmptyMask">
		<div class="fastEmptyMaskInner">
			<div class="smallCircle fastMaskIcon">
				<i class="vteicon nohover">star</i>
			</div>
			<span class="fastMaskText">
				{"LBL_NO_FAVORITES"|getTranslatedString}
			</span>
		</div>
	</div>
{/if}
