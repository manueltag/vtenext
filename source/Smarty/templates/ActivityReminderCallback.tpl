{*/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/*}

{* crmv@84320 *}
	{if $activitytype == 'Call'}
		{assign var="ACTIVITYIMG" value="call"}
	{elseif $activitytype == 'Meeting'}
		{assign var="ACTIVITYIMG" value="group"}
	{elseif $activitytype == 'Task'}
		{assign var="ACTIVITYIMG" value="event_note"}
	{/if}
{* crmv@84320e *}

<div id="{$popupid}" class="reminder-popup">
	<input type="hidden" name="{$popupid}_module" value="{$cbmodule}" />
	<input type="hidden" name="{$popupid}_record" value="{$cbrecord}" />
	<input type="hidden" name="{$popupid}_reminderid" value="{$cbreminderid}" />
	
	<table class="table-fixed" border="0" cellpadding="2" cellspacing="0">
		<tr>
			<td rowspan="2" align="center" style="width:72px">
				<i class="vteicon big_circle" style="color:#FFFFFF">{$ACTIVITYIMG}</i>
			</td>
			<td align="left" width="50%">
				<table width="100%" border="0" cellpadding="2" cellspacing="0">
					<tr>
						<td align="left">
							<b>{$activitytype|@getTranslatedString:$MODULE} - {$cbstatus|@getTranslatedString:$MODULE}</b>
						</td>
					</tr>
				</table>
			</td>
			<td rowspan="2" width="40%" style="padding-right:20px">
			    <table class="table-fixed" border="0" cellpadding="2" cellspacing="0">
			    	<tr>
			    		<td align="right" class="card-action">
			    			<input class="crmbutton small edit" type="button" value="{$APP.LBL_OPEN}" onclick="location.href='index.php?action=DetailView&module={$cbmodule}&record={$cbrecord}'" />
				    		<input class="crmbutton small edit" type="button" value="{$APP.LBL_DISMISS}" onclick="ActivityReminderCloseCallback('{$cbmodule}','{$cbrecord}','{$cbreminderid}');ActivityReminderCallbackReset(0, '{$popupid}');ActivityReminderRemovePopupDOM('{$popupid}');" />
			    		</td>
			    	</tr>
			    	<tr>
				    	<td align="right" class="wrap-content">
				    		{if !empty($cbcolor)}
				    			{assign var="colorprop" value="color:$cbcolor;"}
				    		{/if}
				    		<span style="{$colorprop}">{$APP.LBL_OVERDUE} {$PERIOD}.</span>
				    	</td>
			    	</tr>
			    </table>
			</td>
		</tr>
		<tr>
			<td align="left">
				<table class="table-fixed" border="0" cellpadding="2" cellspacing="0">
					<tr><td align="left" class="wrap-content"><b>{$cbsubject}</b></td></tr>
					<tr><td align="left" class="wrap-content">{$WHEN_STRING}</td></tr>
					<tr><td align="left" class="wrap-content">{$LOCATION_STRING}</td></tr>
				</table>
			</td>
		</tr>
	</table>
</div>
