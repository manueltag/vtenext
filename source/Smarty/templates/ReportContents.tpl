<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->

{* crmv@30967 - listview più standard *}
{* crmv@97237 *}
{* crmv@104853 *}

<div class="small" style="margin:4px">
	{foreach item=reportfolder from=$REPT_FLDR}

		<div style="margin:12px 0px">
			<b>{$reportfolder.name}</b>
			<i style="color:#C0C0C0">
				{if !empty($MOD[$reportfolder.description])}
					 - {$MOD[$reportfolder.description]}
				{elseif !empty($reportfolder.description)}
					 - {$reportfolder.description}
				{/if}
			</i>
		</div>

	<form id="report_form">
	<input id="folder_ids" name="folderId" type="hidden" value='{$FOLDE_IDS}'>

	{if count($reportfolder.details) > 0}
	
	<table class="table table-hover">
		
		{* table headers *}
		<thead>
			<tr>
				<th width="2%">
					<input type="checkbox" name="selectall{$reportfolder.id}" onclick='toggleSelect(this.checked,"selected_id{$reportfolder.id}")' />
				</th>
				<th width="100">{$APP.LBL_ACTION}</th>
				<th width="40%">{$MOD.LBL_REPORT_NAME}</th>
				<th>{$MOD.LBL_DESCRIPTION}</th>
			</tr>
		</thead>

		{* table content *}
		{foreach name=reportdtls item=reportdetails from=$reportfolder.details}

		<tr>

			{* <!-- KoKr bugfix add (check_object) idlist for csv export --> *}
			<td>

				{if $reportdetails.state neq 'SAVED' && $reportdetails.customizable eq '1' && $reportdetails.editable eq 'true'}
					<input id="check_report_{$reportdetails.reportid}" name="selected_id{$reportfolder.id}" value="{$reportdetails.reportid}" onclick='toggleSelectAll(this.name,"selectall{$reportfolder.id}")' type="checkbox" />
				{/if}

			</td>

			<td>
				{if $reportdetails.customizable eq '1' && $reportdetails.editable eq 'true'}
					<a href="javascript:;" onClick="Reports.editReport('{$reportdetails.reportid}');"><i class="vteicon" title="{$MOD.LBL_CUSTOMIZE_BUTTON}...">create</i></a>
				{/if}
				{if $reportdetails.state neq 'SAVED' && $reportdetails.editable eq 'true'}
					&nbsp;<a href="javascript:;" onclick="DeleteReport('{$reportdetails.reportid}');"><i class="vteicon" title="{$MOD.LBL_DELETE}...">delete</i></a>
				{/if}
			</td>

			<td>
				{if $reportdetails.url neq ''}
					<a href="{$reportdetails.url}">{$reportdetails.reportname|getTranslatedString:'Reports'}</a>
				{else}
					<a href="index.php?module=Reports&amp;action=SaveAndRun&amp;record={$reportdetails.reportid}&amp;folderid={$reportfolder.id}">{$reportdetails.reportname|getTranslatedString}</a>
				{/if}
				{if $reportdetails.sharingtype eq 'Shared'}
					<i class="vteicon md-sm md-text nohover">people</i>
				{/if}
			</td>

			<td>
				{$reportdetails.description|getTranslatedString:'Reports'}
			</td>

		</tr>

		{/foreach}

	</table>

	{else}
		<p style="margin:4px">{$APP.LBL_EMPTY_FOLDER}</p>
	{/if}

	</form>
	<br />

	{/foreach}
</div>
