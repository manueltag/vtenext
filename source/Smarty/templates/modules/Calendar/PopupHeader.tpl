{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@98866 *}

<table border="0" cellspacing="0" cellpadding="5" width="100%">
	<tr>
		<td class="level3Bg" style="border:0px none">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td align="left" valign="center" style="font-size:14px" class="nopadding">
						<div id="headerTabCont">
							<ul id="header-tab" data-content="#header-tab-content" class="nav nav-tabs nopadding">
							    <li class="active">
							    	<a data-toggle="tab" href="#event-tab">{$MOD.LBL_ADD_EVENT}</a>
						    	</li>
							    <li>
							    	<a data-toggle="tab" href="#todo-tab">{$MOD.LBL_ADD_TODO}</a>
						    	</li>
					 	 	</ul>
				 	 	</div>
				 	 	<div id="headerTitleCont">
				 	 		<div style="text-indent:15px">
					 	 		<span class="dvHeaderText">
									<span class="recordTitle1"></span>
									<span class="recordTitle2"></span>
								</span>
							</div>
				 	 	</div>
					</td>
					<td align="right">
						<input id="btnDetail" class="crmbutton small edit" type="button" value="{$APP.LBL_SHOW_DETAILS}"></input>
						<input id="btnEdit" class="crmbutton small edit" type="button" value="{$MOD.LBL_EDIT}"></input>
						<input id="btnCloseActivity" class="crmbutton small save" type="button" value="{$MOD.LBL_LIST_CLOSE} {$APP.Activity}"></input>
						<input id="btnSave" alt="{$APP.LBL_SAVE_BUTTON_TITLE}" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey='S' type="submit" name="eventsave" class="crmbutton small save" value="{$MOD.LBL_SAVE}">
						<input id="btnCancel" alt="{$APP.LBL_CANCEL_BUTTON_TITLE}" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" type="button" class="crmbutton small cancel" name="eventcancel" value="{$MOD.LBL_DEL}">
					</td>
				</tr>
			</table>
	  	</td>
	</tr>
</table>

<script type="text/javascript">
	var deleteString = "{$MOD.LBL_DEL}";
	var cancelString = "{$APP.LBL_CANCEL_BUTTON_LABEL}";
</script>
