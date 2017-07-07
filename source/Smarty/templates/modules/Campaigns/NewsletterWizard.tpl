{* crmv@43611 *}

{include file="SmallHeader.tpl"}

<script language="JavaScript" type="text/javascript" src="include/js/vtlib.js"></script>
<script language="JavaScript" type="text/javascript" src="modules/Campaigns/Campaigns.js"></script>
<script language="JavaScript" type="text/javascript" src="modules/Newsletter/Newsletter.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/{php} echo $_SESSION['authenticated_user_language'];{/php}.lang.js?{php} echo $_SESSION['vtiger_version'];{/php}"></script>
{include file='CachedValues.tpl'}	{* crmv@26316 crmv@55961 *}

<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$CALENDAR_LANG}.js"></script>

<script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>

<style type="text/css">
{literal}
	html, body {
		height: 94%; /* leave space for top panel */
	}
	#nlWizMainTab {
		z-index: -10;
		width: 100%;
	}
	#nlWizLeftPane {
		width:20%;
		min-width:200px;
		border-right: 1px solid #e0e0e0;
		vertical-align: top;
		padding: 0px;
	}
	#nlWizRightPane {
		min-width:400px;
		vertical-align: top;
		padding:8px;
	}
	#nlWizStepTable {
		width: 100%;
	}
	.nlWizStepCell {
	    margin: 2px;
	    font-weight: normal;
	    cursor: pointer;
	    line-height: 3rem;
	    padding: 1rem 1rem;
	    background-color: #fff;
	    border-bottom: 1px solid #d2d2d2;
	}
	.nlWizStepCellSelected {
		background-color: trasparent;
		font-weight: bold;
	}
	/*.nlWizStepCell:hover {
		background-color: #e0e0e0;
	}*/
	.nlWizTargetList {
		border-bottom:1px solid #e0e0e0;
		padding-bottom:4px;
		margin-bottom:10px;
		height: 300px;
		overflow: scroll;
	}
	.addrBubble {
		overflow: visible;
		position: static;
		background-color: #EDF4FD;
		border: 1px solid #DADADA;
	    color: black;
	    display: inline-block;
	    margin-bottom: 2px;
	    margin-left: 3px;
	    padding: 5px 3px;
	    border-radius: 0px;
	}
	.ImgBubbleDelete {
		display:inline-block;
		cursor:pointer;
	    height: 12px !important;
	    overflow: hidden;
	    width: 12px !important;
	}
	#nlw_templatePreviewCont {
		min-height:200px;
		overflow-y:auto;
		width:100%;
		padding:3px;
		border:1px solid #d0d0d0;
		margin:3px;
	}
	.circleIndicator {
		display:inline-block;
		font-size: 14px;
		color: #FFFFFF;
		text-align: center;
		border-radius: 50%;
		width: 30px;
		height: 30px;
		line-height: 30px;
		cursor: default;
		margin-right: 16px;
		background-color: #CDCDCD;
	}
	.circleIndicator.circleEnabled {
		background-color: #2C80C8;
	}
	.mailClientWriteEmailHeader {
		font-size: 16px;
		line-height: 24px;
		color: rgb(43, 87, 124);
		text-indent: 0px;
		height:59px;
	}
	.menuSeparation, .level3Bg {
    	border-bottom: 0px none;
	}
	.vte_menu_white {
		height:60px;
	}
{/literal}
</style>

<input type="hidden" name="newsletterid" id="newsletterid" value="{$NEWSLETTERID}" />
<input type="hidden" name="campaignid" id="campaignid" value="{$CAMPAIGNID}" />

{* popup status *}
<div id="status" name="status" style="display:none;position:fixed;right:2px;top:45px;z-index:100">
	{include file="LoadingIndicator.tpl"}
</div>


<table id="nlWizMainTab" border="0" height="100%">
	<tr>
		<td id="nlWizLeftPane">
			<div>
				<table id="nlWizStepTable">
					<tr><td class="nlWizStepCell nlWizStepCellSelected"><span class="circleIndicator circleEnabled">1</span>{$MOD.ChooseRecipients}</td></tr>
					<tr><td class="nlWizStepCell"><span class="circleIndicator">2</span>Template</td></tr>
					<tr><td class="nlWizStepCell"><span class="circleIndicator">3</span>{$MOD.NewsletterData}</td></tr>
					<tr><td class="nlWizStepCell"><span class="circleIndicator">4</span>Test</td></tr>
					<tr><td class="nlWizStepCell"><span class="circleIndicator">5</span>{$MOD.ScheduleNewsletter}</td></tr>
				</table>
				<br><br><br>
				<table class="table" border="0" width="100%">
					<tr><td colspan="2"><b>{$MOD.NewsletterProgress}</b></td></tr>
					<tr><td>{$MOD.Recipients}:</td><td align="right"><span id="nlw_selTargetsCount"></span></td></tr>
					<tr><td>Template:</td><td align="right"><span id="nlw_selTemplate"></span></td></tr>
					<tr><td>{$MOD.TestEmail}:</td><td align="right"><span id="nlw_testEmailOk"></span></td></tr>
					<tr><td>{$MOD.NewsletterStatus}:</td><td align="right"><span id="nlw_newsletterSaved"></span></td></tr>
				</table>
			</div>
		</td>
		<td id="nlWizRightPane">

			<table id="nlwTopButtons" border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td align="left"><input type="button" class="crmbutton cancel" onclick="nlwGotoPrevStep()" id="nlw_backButton" style="display:none" value="&lt; {$APP.LBL_BACK}"></td>
					<td align="right"><input type="button" class="crmbutton save" onclick="nlwGotoNextStep()" id="nlw_nextButton" value="{$APP.LBL_FORWARD} &gt;"></td>
				</tr>
			</table>

			<div id="nlWizStep1" style="">
				{$MOD.WhichRecipientsToAdd}
				
				<div class="dvtCellInfo" style="display:inline-block;width:200px">
					<select class="detailedViewTextBox" id="nlw_targetTypeSel" onchange="nlwChangeTargetSel()">
						<option value="">{$APP.LBL_SELECT}</option>
						{foreach key=TMOD item=TMODINFO from=$TARGET_MODS}
						<option value="{$TMOD}">{$TMOD|getTranslatedString:$TMOD}</option>
						{/foreach}
					</select>
				</div>

				<div class="divider"></div>

				{foreach key=TMOD item=TMODINFO from=$TARGET_MODS}
					{if $TMOD eq 'Targets'}
						{assign var=LISTIDTARGETS value=$TMODINFO.listid}
					{/if}
					<div class="nlWizTargetList" id="nlw_targetList_{$TMOD}" style="display:none">
					{$TMODINFO.list}
					</div>
				{/foreach}

				<div id="nlw_targetsBoxCont">
					<p><b>{$MOD.SelectedRecipients}</b></p>
					<div id="nlWizTargetsBox"></div>
					{if $SEL_TARGETS neq '' && count($SEL_TARGETS) > 0}
					<script type="text/javascript">
						{foreach item=TGT from=$SEL_TARGETS}
							nlwRecordSelect('{$LISTIDTARGETS}', 'Targets', '{$TGT.crmid}', '{$TGT.entityname}');
						{/foreach}
					</script>
					{/if}
				</div>
			</div>


			<div id="nlWizStep2" style="display:none;">
				<div id="nlw_templateDetails">
				<p>{$MOD.NowChooseATemplate}:</p>
				<div style="height:190px">
					{$TPLLIST}
					<input type="hidden" id="nlw_templateid" value="" />
				</div>

				<div id="nlw_templatePreviewHeader" style="display:none">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td align="left"><b>{$APP.LBL_PREVIEW}</b></td>
						{if $CAN_EDIT_TEMPLATES}
						<td align="right"><input type="button" class="crmbutton edit" id="nlw_temlateEditButton" value="{$APP.LBL_EDIT_BUTTON}" style="display:none" onclick="nlwTemplateEdit(200, jQuery('#nlw_templateid').val())"></td>	{* crmv@59091 *}
						{/if}
					</tr>
				</table>
				</div>

				<div id="nlw_templatePreviewCont" style="display:none;">
					<table border="0" cellspacing="1" cellpadding="1" width="100%" height="100%">
						<tr>
							<td width="100" valign="top" height="16" style="border-bottom:1px solid #e0e0e0"><i>{$APP.LBL_SUBJECT}:</i></td>
							<td valign="top" height="16" style="border-bottom:1px solid #e0e0e0"><div id="nlw_templatePreviewSubject"></div></td>
						</tr>
						<tr><td valign="top"></td><td><div id="nlw_templatePreviewBody" style=""></div></td></tr>
					</table>
				</div>
				</div>

				{if $CAN_EDIT_TEMPLATES}
				<div id="nlw_templateEditCont" style="display:none">
					<input type="hidden" id="nlw_templateEditId" value="" />

					<table border="0" cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td align="left"><input type="button" class="crmbutton cancel" onclick="nlwCancelEditTemplate()" id="nlw_cancelEditTemplate" value="&lt; {$APP.LBL_CANCEL_BUTTON_LABEL}"></td>
							<td align="right"><div id="nlw_templateEditlIndicator" style="width:50px;display:none;">{include file="LoadingIndicator.tpl"}</div></td>
							<td align="right" width="60"><input type="button" class="crmbutton save" onclick="nlwSaveTemplate()" id="nlw_saveTemplate" value="{$APP.LBL_SAVE_LABEL}"></td>
						</tr>
					</table>
					<br>

					<table border="0" width="100%" style="margin-bottom:5px">
						{*crmv@104558*}
						<tr style="display:none;"><td>{'LBL_NAME'|getTranslatedString:'Settings'}:</td><td><div class="dvtCellInfoM"><input type="text" class="detailedViewTextBox" id="nlw_template_name" name="nlw_template_name" value=""></div></td></tr>
						<tr style="display:none;"><td>{$APP.LBL_DESCRIPTION}:</td><td><div class="dvtCellInfo"><input type="text" class="detailedViewTextBox" id="nlw_template_description" value=""></div></td></tr>
						<tr style="display:none;"><td>{$APP.LBL_SUBJECT}:</td><td><div class="dvtCellInfoM"><input type="text" class="detailedViewTextBox" id="nlw_template_subject" value=""></div></td></tr>
						{*crmv@104558e*}
						<tr><td width="20%">{$MOD.InsertVariable}:</td><td>

							<table border="0" width="100%">
							<tr style="display:none;"> {*crmv@104558*}
								<td>{'LBL_STEP'|getTranslatedString:'Settings'}1</td>
								<td>{'LBL_STEP'|getTranslatedString:'Settings'}2</td>
								<td>{'LBL_STEP'|getTranslatedString:'Settings'}3</td>
							</tr>
							<tr>
								<td>
									<div class="dvtCellInfo">
										<select class="detailedViewTextBox" id="entityType" onchange="modifyMergeFieldSelect(this, document.getElementById('mergeFieldSelect'));">
											<option value="None" selected="">{$APP.LBL_NONE}</option>
											{foreach key=module item=arr from=$TPLVARIABLES}
												<option value="{$module}">{$module|@getTranslatedString:$module}</option>
				                    		{/foreach}
										</select>
									</div>
								</td>
								<td>
									<div class="dvtCellInfo">
										<select class="detailedViewTextBox" id="mergeFieldSelect" onchange="jQuery('#mergeFieldValue').val(jQuery(this).val());">
											<option value="" selected>{$APP.LBL_NONE}</option>
										</select>
									</div>
								</td>
								<td>
									<div class="dvtCellInfo">
										<input type="text" class="detailedViewTextBox" id="mergeFieldValue" name="variable" value="" />
									</div>
								</td>
								<td>
									<input class="crmbutton create" type="button" onclick="InsertIntoTemplate('mergeFieldValue');" value="{'LBL_INSERT_INTO_TEMPLATE'|getTranslatedString}">
								</td>
							</tr>
							</table>

						</td></tr>

					</table>
					<div class="cellInfo">
						<textarea name="nlw_template_body" style="width:90%;height:315px" class=small tabindex="5"></textarea>
					</div>
				</div>

				<script type="text/javascript" defer="1">
					var curr_lang = '{$CALENDAR_LANG}';
					CKEDITOR.replace('nlw_template_body', {ldelim}
						filebrowserBrowseUrl: 'include/ckeditor/filemanager/index.html',
						//toolbar : 'Basic',
						language : curr_lang,
						{* crmv@56235 *}
						{literal}
						on: {
							'instanceReady': function (evt) { 
								//crmv@104558
								//evt.editor.resize('100%', jQuery('#nlWizRightPane').height() - 305);
								evt.editor.resize('100%', jQuery('#nlWizRightPane').height());
								//crmv@104558e
							}
						}
						{/literal}
						{* crmv@56235e *}
					{rdelim});

					// template variables initialization
					allTplOptions = {$TPLVARIABLES|@json_encode};
					allTplOptions['None'] = [['{$APP.LBL_NONE}', 'None']];
				</script>
				{/if}
			</div>


			<div id="nlWizStep3" style="display:none">
				<div class="spacer-20"></div>
				<p>{$MOD.InsertNewsletterData}:</p>
				<form name="nlw_RecordFields" id="nlw_RecordFields" onsubmit="return false;">
				<table class="table borderless" style="width:80%">
					{foreach item=FLD from=$NLFIELDS}
					<tr>
						<td>{if $FLD.mandatory}<font color="red">*</font>{/if}{$FLD.label}</td>
						<td>
							{if $FLD.uitype eq '19'}
							<div class="cellInfo">
								<textarea class="detailedViewTextBox {if $FLD.mandatory}mandatoryField{/if} vertical" name="{$FLD.name}">{$FLD.value}</textarea>
							</div>
							{else}
							<div class="cellInfo">
								<input type="text" class="detailedViewTextBox {if $FLD.mandatory}mandatoryField{/if}" name="{$FLD.name}" value="{$FLD.value}" />
							</div>
							{/if}
						</td>
					</tr>
					{/foreach}
					{*crmv@104558*}
					<tr>
						<td><font color="red">*</font>{'LBL_SUBJECT'|getTranslatedString:'Settings'} {'Newsletter'|getTranslatedString:'Newsletter'}</td>
						<td>
							<div class="cellInfo">
								<input type="text" class="detailedViewTextBox mandatoryField" name="subject" id="subject" value="" />
							</div>
						</td>
					</tr>
					{*crmv@104558e*}
				</table>
				</form>
			</div>

			<div id="nlWizStep4" style="display:none">
				<p>{$MOD.SendTestEmailTo}:</p>
				<table border="0" id="nlw_testEmailTable">
					<tr>
						<td>
							<div class="dvtCellInfo"><input type="text" class="detailedViewTextBox" name="nlw_testEmailAddress" id="nlw_testEmailAddress" value="{$TESTEMAILADDRESS}" size="40" />
						</td>
						<td>
							<input type="button" class="crmbutton save" id="nlw_sendTestEmailButton" value="{$APP.LBL_SEND}" onclick="nlwSendTestEmail()" />
							<input type="button" class="crmbutton save" id="nlw_resendTestEmailButton" value="{$APP.LBL_RESEND}" onclick="nlwSendTestEmail()" style="display:none"/>
						</td>
					</tr>
				</table>
				<div id="nlw_testEmailIndicator" style="display:none;width:400px;text-align:center">{include file="LoadingIndicator.tpl"}</div>
				<div id="nlw_testEmailStatus" style="display:none"></div>
				<br><br>
				<p>{$MOD.YouCanSeeNewsletterPreview}</p>
				<input type="button" class="crmbutton edit" id="nlw_previewButton" value="{$MOD.LBL_PREVIEW_NEWSLETTER}" onclick="nlwShowPreview()" />
				<div id="nlw_previewIndicator" style="display:none;width:200px;text-align:center">{include file="LoadingIndicator.tpl"}</div>
			</div>


			<div id="nlWizStep5" style="display:none">
				<p>{$MOD.OkWhenDoWeScheduleIt}</p>
				<div id="nlw_newsletterTimes">
				<table border="0" width="400">
					<tr>
						<td width="20"><input type="radio" name="nlw_radioSend" id="nlw_radioSendNow" checked="" onclick="jQuery('#nlw_sendTimesRow').hide();jQuery('#nlw_saveNowButton').show();jQuery('#nlw_saveLaterButton').hide();"></td>
						<td><label for="nlw_radioSendNow">{$APP.LBL_NOW}</label></td>
					</tr>
					<tr>
						<td><input type="radio" name="nlw_radioSend" id="nlw_radioSendLater" onclick="jQuery('#nlw_sendTimesRow').show();jQuery('#nlw_saveNowButton').hide();jQuery('#nlw_saveLaterButton').show();"></td>
						<td><label for="nlw_radioSendLater">{$MOD.AnotherTime}</label></td>
					</tr>
					<tr id="nlw_sendTimesRow" style="display:none"><td></td><td>
						<table border="0" width="100%">
							{assign var=dateAndTime value="Date & Time"}
							<tr><td colspan="2">{$APP.$dateAndTime}:</td></tr>
							<tr>
								<td height="22">
									<div class="dvtCellInfo">
										<input type="text" class="detailedViewTextBox" id="nlw_sendDate" value="" />
										<div class="dvtCellInfoImgRx">
											<img src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" id="jscal_trigger_nlw_sendDate" valign="top">
										</div>
									</div>
								</td>
								<td>
									<div class="dvtCellInfo">
										<input type="text" class="detailedViewTextBox" id="nlw_sendTime" value="" style="width:100px"/>
									</div>
								</td>
							</tr>
						</table>
						<script type="text/javascript">
							Calendar.setup ({ldelim}
								inputField : "nlw_sendDate", ifFormat : "%Y-%m-%d", showsTime : false, button : "jscal_trigger_nlw_sendDate", singleClick : true, step : 1
							{rdelim})
						</script>

					</td></tr>
				</table>
				<br>
				<input type="button" class="crmbutton save" id="nlw_saveNowButton" value="{$MOD.SaveAndSend}" onclick="nlwSaveAll()"/>
				<input type="button" class="crmbutton save" id="nlw_saveLaterButton" value="{$MOD.SaveAndSchedule}" onclick="nlwSaveAll()" style="display:none" />
				</div>
				<div id="nlw_newsletterIndicator" style="display:none;width:400px;text-align:center">{include file="LoadingIndicator.tpl"}</div>
				<div id="nlw_newsletterStatus" style="display:none"></div>
				<div id="nlw_closeButtonDiv" style="display:none">
					<br><br>
					<input type="button" class="crmbutton close" value="{$APP.LBL_CLOSE}" onclick="closePopup()" />
				</div>
			</div>


		</td>
	</tr>
</table>