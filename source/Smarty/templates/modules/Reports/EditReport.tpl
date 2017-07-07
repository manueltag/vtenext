{* crmv@98764 crmv@98866 *}
{assign var="BROWSER_TITLE" value=$MOD.TITLE_VTIGERCRM_CREATE_REPORT}
{include file="HTMLHeader.tpl" head_include="jquery,jquery_plugins,jquery_ui,fancybox,prototype,charts"}
	
<body style="width:100%">

{include file="Theme.tpl" THEME_MODE="body"}

{include file='CachedValues.tpl'}	{* crmv@26316 *}

<link href="themes/{$THEME}/editreport.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="modules/Reports/Reports.js"></script>
<script language="JavaScript" type="text/javascript" src="modules/Reports/EditReport.js"></script>
<script language="JavaScript" type="text/javascript" src="modules/Charts/Charts.js"></script>


{* popup status *}
<div id="editreport_busy" name="editreport_busy" style="display:none;position:fixed;right:200px;top:10px;z-index:100">
	{include file="LoadingIndicator.tpl"}
</div>

{* header *}
<table id="reportHeaderTab" class="mailClientWriteEmailHeader level2Bg menuSeparation" width="100%" border="0" cellspacing="0" cellpadding="5" > {* crmv@21048m *}
	<tr>
		<td class="moduleName" width="80%">{if $REPORTID}{$MOD.LBL_CUSTOMIZE_REPORT}{else}{$MOD.LBL_CREATE_REPORT}{/if}</td>
		<td width=30% nowrap class="componentName" align="right">{$MOD.LBL_CUSTOM_REPORTS}</td>
	</tr>
</table>

{* content *}
<table id="reportMainTab" border="0" height="100%" width="100%">
	<tr>
		<td id="leftPane" width="250" valign="top">
			<div>
				<table id="reportStepTable" width="100%">
					<tr><td id="step1label" class="reportStepCell reportStepCellSelected" style="padding-left:10px;" {if $REPORTID}onclick="EditReport.gotoStep(1)"{/if}>1. {$MOD.LBL_REPORT_DETAILS}</td></tr>
					<tr><td id="step2label" class="reportStepCell" style="padding-left:10px;" {if $REPORTID}onclick="EditReport.gotoStep(2)"{/if}>2. {$MOD.LBL_REPORT_TYPE}</td></tr>
					<tr><td class="reportStepCell" style="padding-left:10px" {if $REPORTID}onclick="EditReport.gotoStep(3)"{/if}>3. {$MOD.LBL_TEMPORAL_FILTER}</td></tr>
					<tr><td class="reportStepCell" style="padding-left:10px" {if $REPORTID}onclick="EditReport.gotoStep(4)"{/if}>4. {$MOD.LBL_ADVANCED_FILTER}</td></tr>
					<tr><td class="reportStepCell" style="padding-left:10px" {if $REPORTID}onclick="EditReport.gotoStep(5)"{/if}>5. {$MOD.LBL_SELECT_COLUMNS}</td></tr>
					<tr><td class="reportStepCell" style="padding-left:10px" {if $REPORTID}onclick="EditReport.gotoStep(6)"{/if}>6. {$MOD.LBL_CALCULATIONS}</td></tr>
					<tr><td class="reportStepCell" style="padding-left:10px" {if $REPORTID}onclick="EditReport.gotoStep(7)"{/if}>7. {$MOD.LBL_SHARING}</td></tr>
					{if $CAN_CREATE_CHARTS && !$REPORTID}
					<tr><td class="reportStepCell" style="padding-left:10px">8. {"Charts"|getTranslatedString:'Charts'}</td></tr>
					{/if}
				</table>
			</div>
		</td>
		
		<td id="rightPane" valign="top">

			<table id="reportTopButtons" border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td align="left"><input type="button" class="crmbutton cancel" onclick="EditReport.gotoPrevStep()" id="backButton" style="display:none" value="&lt; {$APP.LBL_BACK}"></td>
					<td align="right">
						<input type="button" class="crmbutton save" onclick="EditReport.gotoNextStep()" id="nextButton" value="{$APP.LBL_FORWARD} &gt;">
						<input type="button" class="crmbutton save" onclick="EditReport.saveReport()" id="saveButton" style="display:none" value="{$APP.LBL_SAVE_LABEL}">
					</td>
				</tr>
			</table>
			
			<br>
			
			<form id="NewReport" name="NewReport" onsubmit="return false">
			
			<input type="hidden" name="reportid" id="reportid" value="{$REPORTID}" />
			<input type="hidden" name="duplicate" id="duplicate" value="{$DUPLICATE}" />
			
			<div id="reportStep1" style="">
				{include file="modules/Reports/EditStepInfo.tpl"}
			</div>

			<div id="reportStep2" style="display:none;">
				{include file="modules/Reports/EditStepType.tpl"}
			</div>
			
			<div id="reportStep3" style="display:none">
				{include file="modules/Reports/EditStepStdFilters.tpl"}
			</div>

			<div id="reportStep4" style="display:none">
				{include file="modules/Reports/EditStepAdvFilters.tpl"}
			</div>

			<div id="reportStep5" style="display:none">
				{include file="modules/Reports/EditStepFields.tpl"}
			</div>

			<div id="reportStep6" style="display:none">
				{include file="modules/Reports/EditStepTotals.tpl"}
			</div>
			
			<div id="reportStep7" style="display:none">
				{include file="modules/Reports/EditStepSharing.tpl"}
			</div>
			
			{if $CAN_CREATE_CHARTS && !$REPORTID}
			<div id="reportStep8" style="display:none">
				{include file="modules/Reports/EditStepCharts.tpl"}
			</div>
			{/if}

			</form>
			
		</td>
	</tr>
</table>

<script type="text/javascript">
	{if $PRELOAD_JS}
	(function() {ldelim}
		var preload_js = {$PRELOAD_JS};
		EditReport.preloadCache(preload_js);
	{rdelim})();
	{/if}
	{if $FIELD_FUNCTIONS_JS}
	var ReportFieldFormulas = {$FIELD_FUNCTIONS_JS};
	{/if}
	
	// initialize the first step
	EditReport.initializeStep(1);
</script>


</body>
</html>