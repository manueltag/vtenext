{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}

<!-- TicketList -->
<!-- <span class="lvtHeaderText"> -->

<h1 class="page-header">{'HelpDesk'|getTranslatedString}</h1>

<div class="row">
	<input class="btn btn-success" style="float:right; margin: 10px;" name="newticket" type="submit" value="{'NEW_TICKET'|getTranslatedString}" onClick="window.location.href='?module=HelpDesk&action=index&fun=newticket'">&nbsp;
	<input type="button" onclick="showSearchFormNow('tabSrch');" value="{'LBL_SEARCH'|getTranslatedString}" name="srch" class="crmbutton small cancel" style="float:right; margin: 10px;">

	{include file='SearchForm.tpl'}
<!--	<div id="tabSrch" style="display:none; margin-top:20px;">
		<form action="index.php" method="post" name="search">
			<input type="hidden" name="module">
			<input type="hidden" name="action">
			<input type="hidden" name="fun">
			<div id="_search_formelements_">
				{include file='SearchForm.tpl'}
			</div>
		</form>
	</div> -->
</div>
<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6">
			<!--<div class="col-lg-4 col-lg-offset-4 col-md-4 col-md-offset-2 col-sm-4 col-sm-offset-4" align="right"> -->
			<div class="col-md-4 col-md-offset-4 col-sm-4 col-sm-offset-4 col-xs-12 " align="right"> 
				{'SHOW'|getTranslatedString}
				<select class="form-control" name="list_type" onchange="getList(this, '{$MODULE}');">
 					<option value="mine" {$MINE_SELECTED}>{'MINE'|getTranslatedString}</option>
					<option value="all" {$ALL_SELECTED}>{'ALL'|getTranslatedString}</option>
				</select>
			</div>
			{* crmv@80441 *}
			<!-- <div class="col-md-4 col-sm-4 col--4" align="right"> -->
			<div class="col-md-4 col-sm-4 col-xs-12" align="right"> 
				{'TICKET_STATUS'|getTranslatedString}
				<select name="list_type" class="form-control" id="ticket_status_combo" onchange="getList(this, '{$MODULE}');">
					<option value=""></option>
 					<option value="Open" {$TICKETOPEN}>{'LBL_STATUS_TICKET_OPEN'|getTranslatedString}</option>
					<option value="Closed" {$TICKETCLOSE}>{'LBL_STATUS_TICKET_CLOSE'|getTranslatedString}</option>
				</select>
			</div>			
			{* crmv@80441 *}
		</div>
	</div>
</div>

{if !empty($ENTRIES)}
	{include file='ListViewFields.tpl'}
{else}
	{include file='ListViewEmpty.tpl'}
{/if}