{* crmv@62394 *}

{* ---- buttons ---- *}
{if $TRACKER_DATA.enable_buttons eq true}
	<div class="track-label" style="padding-right:2px"><span class="small">{$APP.LBL_TRACK_MANAGER}:</span></div>
	{include file="modules/SDK/src/CalendarTracking/TrackingSmallButtons.tpl"}
{else}
	<span class="small">
	{include file="modules/SDK/src/CalendarTracking/TrackingSmallButtons.tpl"}
	</span>
{/if}
&nbsp;&nbsp;

{* ---- popup ---- *}
{include file="modules/SDK/src/CalendarTracking/PopupTracking.tpl"}
