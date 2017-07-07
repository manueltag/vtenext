{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

<div id="ActivityRemindercallback-container">
	<div id="ActivityRemindercallback-fixed-header">
		<table border="0" cellpadding="2" cellspacing="0">
			<tr>
				<td style="text-indent:10px;"><b>{$APP.LBL_APPOINTMENT_REMINDER}</b></td>
			</tr>
		</table>
	</div>
	<div id="ActivityRemindercallback-content"></div>
	<div id="ActivityRemindercallback-fixed-footer">
		<table border="0" cellpadding="2" cellspacing="0">
			<tr>
				<td align="left" style="padding-left:6px">
					<input class="crmbutton small edit" type="button" value="{$APP.LBL_SNOOZE_ALL}" onclick="ActivityReminderPostponeAll();" />
				</td>
				<td align="right" style="padding-right:6px">
					<input class="crmbutton small edit" type="button" value="{$APP.LBL_DISMISS_ALL}" onclick="ActivityReminderCloseAll();" />
				</td>
			</tr>
		</table>
	</div>
</div>
