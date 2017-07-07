{* crmv@62394 crmv@125351 *}

{include file="HTMLHeader.tpl" head_include="all"}
<link rel="stylesheet" type="text/css" href="{$RELPATH}themes/{$THEME}/lateralmenu.css">

{if count($TRACKLIST) > 0}
<script type="text/javascript" src="modules/SDK/src/CalendarTracking/CalendarTracking.js"></script>
<table id="track_buttons_pop" class="table table-hover">
	{foreach key=id item=info name="tracklist" from=$TRACKLIST}
		{assign var="moduleNameLower" value=$info.module|strtolower}
		{assign var="moduleFirstLetter" value=$info.module|substr:0:1|strtoupper}
		
		{if $smarty.foreach.tracklist.iteration eq 1}
			<tr>
				<td colspan="3" class="fastPanelTitle" style="border-top:0px none">
					<h4>{"LBL_TRACK_MANAGER_TITLE"|getTranslatedString}</h4>
				</td>
			</tr>
		{/if}
		
		<tr class="fastList1LevelIcon">
			<td nowrap style="white-space:nowrap;letter-spacing:-8px">
				{if $info.enable eq true}
					<i class="vteicon md-link md-36" title="{$APP.LBL_PAUSE}" onClick="CalendarTracking.trackInCalendarList('{$id}','{$info.module}','pause');" >pause</i>
					<i class="vteicon md-link md-36" title="{$APP.LBL_FINISH}" onClick="CalendarTracking.trackInCalendarList('{$id}','{$info.module}','stop');" >stop</i>
					<i class="vteicon disabled md-36" title="{$APP.LBL_EJECT_TRACKING}">eject</i>
				{elseif $ACTIVE_TRACKED neq false && $ID neq $ACTIVE_TRACKED}
					<i class="vteicon disabled md-36" title="{$APP.LBL_START}">play_arrow</i>
					<i class="vteicon disabled md-36" title="{$APP.LBL_START}">stop</i>
					<i class="vteicon md-link md-36" title="{$APP.LBL_EJECT_TRACKING}" onClick="CalendarTracking.trackInCalendarList('{$id}','{$info.module}','eject');">eject</i>
				{else}
					<i class="vteicon md-link md-36" title="{$APP.LBL_START}" onClick="CalendarTracking.trackInCalendarList('{$id}','{$info.module}','start');" >play_arrow</i>
					<i class="vteicon disabled md-36" title="{$APP.LBL_START}">stop</i>
					<i class="vteicon md-link md-36" title="{$APP.LBL_EJECT_TRACKING}" onClick="CalendarTracking.trackInCalendarList('{$id}','{$info.module}','eject');">eject</i>
				{/if}
			</td>
			<td width="10%" class="fastListIcon">
				<div class="smallCircle">
					<i class="vteicon icon-module icon-{$moduleNameLower} nohover" data-first-letter="{$moduleFirstLetter}"></i>
				</div>
			</td>
			<td width="75%" class="fastListText">
				<a href="index.php?module={$info.module}&action=DetailView&record={$id}" target="_parent">
					{$info.name}
				</a>
			</td>
			<td width="15%" class="fastListModule">
				<span>{$info.entity_type}</span>
			</td>
		</tr>
	{/foreach}
</table>
<table border=0 cellspacing=1 cellpadding=3 width="100%" class="small" id="track_message_tbl" style="display:none;">
	<tr>
		<td align="center" class="dvtCellInfo">
			<textarea id="track_message" name="track_message" class="detailedViewTextBox" style="width:98%"></textarea>
		</td>
	</tr>
	<tr>
		<td align="right" id="track_message_tbl_buttons">
			<input type="hidden" name="track_message_id" id="track_message_id" value="" />
			<input type="hidden" name="track_message_module" id="track_message_module" value="" />
			<input type="hidden" name="track_message_type" id="track_message_type" value="" />
			{* crmv@79996 *}
			<span id="track_message_btns_helpdesk" style="display:none">
				<button type="button" title="{$APP.LBL_DO_TRACK}" name="button" onclick="CalendarTracking.changeTrackStateList(null, null, null, null, '');" class="crmbutton small save">{$APP.LBL_DO_TRACK}</button>&nbsp;
				<button type="button" title="{$APP.LBL_TRACK_AND_COMMENT}" name="button" onclick="CalendarTracking.changeTrackStateList();" class="crmbutton small save">{$APP.LBL_TRACK_AND_COMMENT}</button>
			</span>
			{* crmv@79996e *}
			<span id="track_message_btns_standard" style="display:none">
				<button type="button" title="{$APP.LBL_DO_TRACK}" name="button" onclick="CalendarTracking.changeTrackStateList();" class="crmbutton small save">{$APP.LBL_DO_TRACK}</button>&nbsp;
				{if $TICKETS_AVAILABLE}
					<button type="button" title="{$APP.LBL_DO_TRACK_AND}{"SINGLE_HelpDesk"|getTranslatedString}" name="button" onclick="CalendarTracking.changeTrackStateList(null, null, null, 'yes');" class="crmbutton small save">{$APP.LBL_DO_TRACK_AND}{"SINGLE_HelpDesk"|getTranslatedString}</button>
				{/if}
			</span>
			
			<button type="button" title="{'LBL_CANCEL_BUTTON_LABEL'|getTranslatedString}" name="button" onclick="location.reload();" class="crmbutton small cancel">{'LBL_CANCEL_BUTTON_LABEL'|getTranslatedString}</button>
		</td>
	</tr>
</table>
<div id="detailview_block_indicator" width="100%" align="center" style="display:none;">{include file="LoadingIndicator.tpl"}</div>
{else}
	<div class="fastEmptyMask">
		<div class="fastEmptyMaskInner">
			<div class="smallCircle fastMaskIcon">
				<i class="vteicon nohover">timer</i>
			</div>
			<span class="fastMaskText">
				{"LBL_TRACKING_NO_ENTRIES"|getTranslatedString}
			</span>
		</div>
	</div>
{/if}