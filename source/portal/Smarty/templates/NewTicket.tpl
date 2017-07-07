{*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with
the License * The Original Code is: VTECRM * The Initial Developer of
the Original Code is VTECRM LTD. * Portions created by VTECRM LTD are
Copyright (C) VTECRM LTD. * All Rights Reserved.
***************************************************************************************}

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">{'LBL_NEW_TICKET'|getTranslatedString}</h1>
	</div>
</div>
<form name="Save" method="post" action="index.php" class="NewTicket" enctype="multipart/form-data">
	<input type="hidden" name="module" value="HelpDesk">
	<input type="hidden" name="action" value="index">
	<input type="hidden" name="fun" value="saveticket">
	<input type="hidden" name="projectid" value="{$PROJECTID}" />

	<!--crmv@57342-->
	<input type="hidden" name="customerfile_hidden"/>
	<!--crmv@57342e-->
	<div class="row">
		<div class="form-group">
			<label>
				<h4>{'TICKET_TITLE'|getTranslatedString}</h4>
			</label>
		</div>
		<input type="text" name="title" class="form-control"> 
<!--{* crmv@5946 *}
		<div class="form-group">
			<label>
				<h4>{'YOUR_POTENTIALS'|getTranslatedString}</h4>
			</label>
		</div>
		<select name="potential_change">
			{foreach from=$POTENTIAL_USER item=OPTION}
				{$OPTION}
			{/foreach}
		</select>
{* crmv@5946e *} -->
<!-- crmv@57342	 -->
	{if $PRIORITY != ''}    {* crmv@104022 *}
	<div class="row">
		<div class="form-group" style="margin-top:10px">
			<label>
				<h4>{'LBL_TICKET_PRIORITY'|getTranslatedString}</h4>
			</label>
		</div>
		<select name="priority" class="form-control">
			{foreach from=$PRIORITY key=KEY item=PRIO}
				<option value="{$PRIO}">{$PRIO|getTranslatedString}</option>
			{/foreach}
		</select>
	</div>
	{/if}	{* crmv@104022 *}
	{if $SEVERITY != ''} {* crmv@81291 *}
	<div class="row">
		<div class="form-group" style="margin-top:10px">
			<label>
				<h4>{'LBL_TICKET_SEVERITY'|getTranslatedString}</h4>
			</label>
		</div>
		<select name="severity" class="form-control">
			{foreach from=$SEVERITY key=KEY item=SER}
				<option value="{$SER}">{$SER|getTranslatedString}</option>
			{/foreach}
		</select>
	</div>
	{/if}{* crmv@81291e *}
	{if $CATEGORY != ''}    {* crmv@104022 *}
	<div class="row">
		<div class="form-group" style="margin-top:10px">
			<label>
				<h4>{'LBL_TICKET_CATEGORY'|getTranslatedString}</h4>
			</label>
		</div>		
		<select name="category" class="form-control">
			{foreach from=$CATEGORY key=KEY item=CAT}
				<option value="{$CAT}">{$CAT|getTranslatedString}</option>
			{/foreach}
		</select>
	</div>
	{/if}	{* crmv@104022 *}
 <!-- crmv@57342e -->
		<div class="form-group" style="margin-top:10px;">
			<label>
				<h4>{'LBL_DESCRIPTION'|getTranslatedString}</h4>
			</label>
		</div>
		<textarea name="description" class="form-control" rows="12" style="margin-bottom: 10px;"></textarea>

		<!--crmv@57342-->
		<div class="form-group" style="margin-top:10px;">
			<label>
				<h4>{'LBL_UPLOAD_PICTURE'|getTranslatedString}</h4>
			</label>
		</div>
		<input type="file" class="detailedViewTextBox form-control" name="customerfile" onchange="validateFilename(this);" />
		<!--crmv@57342e-->
	</div>
	
	<div class="row" style="margin-top: 10px">
		<button class="btn btn-success" accesskey="S" name="button" value="{'LBL_SEND'|getTranslatedString}" type="submit" onclick="return formvalidate(this.form)">{'LBL_SEND_REQUEST'|getTranslatedString}</button>
		<button class="btn btn-danger" accesskey="X" name="button" value="{'LBL_CANCEL'|getTranslatedString}" type="button" onclick="window.history.back()">{'LBL_CANCEL'|getTranslatedString}</button>
	</div>
</form>

<script>
{literal}
function formvalidate(form)
{
	if(trim(form.title.value) == '')
	{
		alert("Ticket Title is empty");
		return false;
	}
	return true;
}
function trim(s) 
{
	while (s.substring(0,1) == " ")
	{
		s = s.substring(1, s.length);
	}
	while (s.substring(s.length-1, s.length) == ' ')
	{
		s = s.substring(0,s.length-1);
	}

	return s;
}
{/literal}
</script>