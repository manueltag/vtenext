{* crmv@97862 *}

<div class="stepTitle" style="width=100%">
	<span class="genHeaderGray">{$APP.Charts}</span><br>
	<span style="font-size:90%">{$MOD.LBL_ADD_CHART_TO_REPORT}</span><hr>
</div>

<div id="chartNotAvailable" style="padding:10px">
	</p><p>{$MOD.LBL_CHART_NEEDS_SUMMARY}
</div>

<div id="chartEditor" style="padding:10px">

	<div id="chartChooser" style="padding:5px">
		<input type="checkbox" id="chartCheckbox" onchange="EditReport.changeChartCheckbox()"/>
		<span>{$MOD.LBL_WANT_TO_CREATE_CHART}</span>
		<br>
	</div>

	<table id="chartFields" border=0 cellspacing=0 cellpadding=0 width="100%" class="small" style="display:none">
	<tr>
		<td>
		<!-- quick create UI starts -->
		<table border="0" cellspacing="0" cellpadding="5" width="100%" class="small" bgcolor="white" >

			<tr><td colspan="4">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td><span style="font-weight:bold" id="chartTypeLabel">&nbsp;{$APP.Type}&nbsp;</span></td>
					</tr><tr>
					<td>
						{assign var="imageBack" value="chart_button_bg.png"}
						{foreach key=ckey item=ctype from=$CHART_TYPES}
							{assign var="imageName" value="chart_$ckey.png"}
							<button id="button_ctype_{$ckey}" name="button_ctype_{$ckey}" type="button" onclick="chartSelectType(this)" style="background:none;border:none">
								<div style="background-image: url('{$imageBack|@vtiger_imageurl:$THEME}'); float:left;">
									<img src="{$imageName|@vtiger_imageurl:$THEME}" alt="{$ctype}" border="0" />
								</div>
							</button>
						{/foreach}
						<input id="chart_type" name="chart_type" type="hidden" value="" />
					</td>
				</tr></table>
			</td></tr>

			{assign var="fromlink_val" value="qcreate"}
			{assign var="data" value=$QUICKCREATE}
			{include file='DisplayFields.tpl'}
		</table>
		</td>

		<td align="center" valign="middle" width="340" height="280">
			<div id="chart_create_preview">{$MOD.LBL_CHART_PREVIEW}</div>
			<div id="chart_create_preview_wait" style="display:none">
				{include file="LoadingIndicator.tpl"}
			</div>
		</td>
	</tr>

	</table>
	
	{* attach a listener on every input element *}
	<script type="text/javascript">
		jQuery('#chartEditor :input').change(EditReport.generateChartPreview);
	</script>
</div>

