{* crmv@82770 *}

{* display the box with the chart *}

{if $HOME_STUFFID > 0}
{include file="modules/Charts/HomeStuffHeader.tpl"}
{/if}

<div class="small" style="padding:10px;" id="div_chart_{$CHART_ID}">
	<table cellspacing="0" cellpadding="0">

		{if $CHART_SHOWBORDER}
		<tr><td class="dvtSelectedCell" style="text-align:center">
			<a class="small" href="index.php?module=Charts&amp;action=DetailView&amp;record={$CHART_ID}">{$CHART_TITLE}</a> {* crmv@30976 *}
		</td></tr>
		{/if}

		{if $CHART_SHOWBORDER}
		<tr><td class="dvtContentSpace" align="right">
		{else}
		<tr><td align="right">
		{/if}

			<div id="chart_bccont_{$CHART_ID}" class="chart-breadcrumbs hidden"></div>
			<div class="chart-container">
				<canvas id="chart_img_{$CHART_ID}" width="{$CHART_DATA.canvas_width}" height="{$CHART_DATA.canvas_height}">Canvas element is not supported. Please update your browser.</canvas>
				<div class="hidden chart-legend legend-right" id="chart_legend_{$CHART_ID}"></div>
			</div>
			{if $CHART_SHOWDATE && $CHART_LASTUPDATE > 0}
				<span style="text-align:right;"><a style="color: gray; text-decoration: none; " href="javascript:;" title="{$CHART_LASTUPDATE_DISPLAY}">{$MOD.LBL_UPDATED_TO} {$CHART_LASTUPDATE_RELATIVE}</a></span>
			{/if}
		</td></tr>

	</table>

</div>

<script type="text/javascript">
	(function() {ldelim}
		// initialize the chart
		var chartData = {$CHART_DATA|@json_encode};
		VTECharts.generateCanvasChart('{$CHART_ID}', chartData, {ldelim}initialize: true{rdelim});
	{rdelim})();
</script>
