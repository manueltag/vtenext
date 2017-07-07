{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@119414 crmv@125351 *}

{if !empty($QCMODULE)}
<table id="quickCreated" class="table table-hover">
	{foreach from=$QCMODULE item=detail name=qcmodule}
		{assign var="moduleName" value=$detail.1}
		{assign var="moduleNameLower" value=$moduleName|strtolower}
		{assign var="moduleFirstLetter" value=$moduleName|substr:0:1|strtoupper}
		{*
		{assign var="entityType" value="SINGLE_"|cat:$moduleType|getTranslatedString:$moduleName}
		{if empty($entityType) || $entityType eq "SINGLE_"|cat:$moduleType}
			{assign var="entityType" value=$moduleType|getTranslatedString:$moduleName}
		{/if}
		*}
		{if $smarty.foreach.qcmodule.iteration eq 1}
			<tr>
				<td colspan="2" class="fastPanelTitle" style="border-top:0px none">
					<h4>{'LBL_QUICK_CREATE'|getTranslatedString}</h4>
				</td>
			</tr>
		{/if}
		{*
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
		*}
		
		{if $count is div by 2}
			{assign var="count_tmp" value=1}
			<tr class="fastList1LevelIcon">
		{/if}
			<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="25%" class="fastListIcon">
						<div class="smallCircle">
							<i class="vteicon icon-module icon-{$moduleNameLower} nohover" data-first-letter="{$moduleFirstLetter}"></i>
						</div>
					</td>
					<td width="75%" class="fastListText">
						<a href="javascript:;" onclick="NewQCreate('{$detail.1}');">{$detail.0}</a>
					</td>
				</tr>
				</table>
			</td>
		{if $count_tmp is div by 2}
			</tr>
		{/if}
		{assign var="count" value=$count+1}
		{assign var="count_tmp" value=1}
	{/foreach}
</table>
{else}
	<div class="fastEmptyMask">
		<div class="fastEmptyMaskInner">
			<div class="smallCircle fastMaskIcon">
				<i class="vteicon nohover">list</i>
			</div>
			<span class="fastMaskText">
				{"LBL_NO_QUICKCREATED"|getTranslatedString}
			</span>
		</div>
	</div>
{/if}
