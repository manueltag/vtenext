{* crmv@28295 crmv@36871 crmv@82419 crmv@125351 *}
<link href="modules/SDK/src/Events/css/Events.css" rel="stylesheet" type="text/css" />

<div id="events">
	<table class="table">
		<tr>
			<td class="fastPanelTitle"><h4>{$APP.Events}</h4></td>
		</tr>
		<tr>
			<td align="right">
				{'LBL_EVENTS_FROM'|getTranslatedString:'Calendar'} <span id="Events_Range_Title_from"></span> {'LBL_EVENTS_TO'|getTranslatedString:'Calendar'} <span id="Events_Range_Title_to"></span>&nbsp;
				<input type="button" value="{$APP.LBL_CREATE}" name="button" class="crmbutton small create" title="{$APP.LBL_CREATE}" onClick="hideFloatingDiv('events');NewQCreate('Events');">
			</td>
		</tr>
		<tr>
			<td id="events_calendar_container" align="center">
				<div id="events_calendar"></div>
			</td>
		</tr>
		<tr>
			<td valign="top" id="events_list" width="100%"></td>
		</tr>
	</table>
</div>