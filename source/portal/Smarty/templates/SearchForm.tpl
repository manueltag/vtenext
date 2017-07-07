{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@81291 *}



<div id="tabSrch" style="display:none; margin-top:50px;" class="container-fluid">
	<form action="index.php" method="post" name="search">
		<input type="hidden" name="module">
		<input type="hidden" name="action">
		<input type="hidden" name="fun">
		<div class="col-md-12" align="right"><a href="javascript:fnDown('tabSrch');" class="hdr">{'LBL_CLOSE'|getTranslatedString}</a></div>
		<div id="_search_formelements_" class="col-md-12">
			
			<div class="col-xs-12 col-md-4 input-search">
				{'TICKETID'|getTranslatedString}
				<input name="search_ticketid" type="text" class="inputTxt form-control" value="">

				{'TICKET_TITLE'|getTranslatedString}<br>
				<input name="search_title" type="text" class="inputTxt form-control" value="">
			</div>
			<div class="col-xs-12 col-md-4 input-search">
				{'TICKET_STATUS'|getTranslatedString}<br>
				{$SEARCH_TICKETSTATUS}

				{'TICKET_PRIORITY'|getTranslatedString}<br>
				{$SEARCH_TICKETPRIORITY}
			</div>
			<div class="col-xs-12 col-md-4 input-search">
				{'TICKET_CATEGORY'|getTranslatedString}<br>
				{$SEARCH_TICKETCATEGORY}

				{'TICKET_MATCH'|getTranslatedString}<br>
				<select name="search_match" class="form-control">
					<option value="all">{'LBL_ALL'|getTranslatedString}</option>
					<option value="any">{'LBL_ANY'|getTranslatedString}</option>
				</select>
			</div>
			<div align="center" class="col-md-12">
				<img style="display:none;" id="tabSrch_progress" src="images/status.gif" border="0" align="right" />&nbsp;
				<input name="Search" type="submit" value="{'LBL_SEARCH'|getTranslatedString}" class="btn btn-primary crmbutton small cancel" onclick="fnDown('tabSrch');this.form.module.value='HelpDesk';this.form.action.value='index';this.form.fun.value='search'">
			</div>
		</div>
	</form>
</div>