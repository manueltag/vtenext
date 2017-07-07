{* crmv@62394 *}
{* crmv@105588 *}

<div id="turbolift_tracker_cont" class="messagesTurboliftEntry btn">
	<div class="row no-gutter">
		<div class="col-sm-12">
			<div class="trackLabel col-sm-6 vcenter text-left">
				<span>{$APP.Tracking}</span>
			</div><!-- 
			 --><div class="trackButtons col-sm-6 vcenter text-right btn-group detail-view-topbar-group">
				{if $TRACKER_FOR_COMPOSE}
					{include file="modules/SDK/src/CalendarTracking/TrackingButtonsCompose.tpl"}
				{else}
					{include file="modules/SDK/src/CalendarTracking/TrackingSmallButtons.tpl" ID=$RECORD}
				{/if}
			</div>
			{include file="modules/SDK/src/CalendarTracking/PopupTracking.tpl" ID=0}
		</div>
	</div>
</div>