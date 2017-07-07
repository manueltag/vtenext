{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}

{* crmv@68357 *}

{assign var=VALUES value=$ICAL.values}
<div class="div_ical" id="div_ical_{$ICALID}">
	<div class="div_ical_fields">
	<table width="100%">
		<tr>
			<td colspan="2" class="ical_header">
				{if $ICAL.method eq 'REQUEST'}
					{'INVITATION'|getTranslatedString:'Calendar'}
				{elseif $ICAL.method eq 'REPLY'}
					{'ANSWER_TO_INVITATION'|getTranslatedString:'Calendar'}
				{elseif $ICAL.method eq 'PUBLISH'}
					{$APP.Event}
				{else}
				{* unsupported method *}
				{/if}
			</td>
		</tr>
		
		<tr>
			<td class="ical_cell_title" width="20%">{$APP.LBL_TITLE}:</td>
			<td class="ical_cell_value">{$VALUES.subject}</td>
		</tr>
		
		<tr>
			<td class="ical_cell_title">{$APP.LBL_WHEN}:</td>
			<td class="ical_cell_value">
				{if $VALUES.when_formatted}
					{$VALUES.when_formatted}
				{else}
					{$VALUES.date_start} {$VALUES.time_start} - {$VALUES.due_date} {$VALUES.time_end}
				{/if}
			</td>
		</tr>
		
		{if $VALUES.location}
		<tr>
			<td class="ical_cell_title">{$APP.LBL_WHERE}:</td>
			<td class="ical_cell_value">{$VALUES.location}</td>
		</tr>
		{/if}
		
		{if $VALUES.organizer}
		<tr>
			<td class="ical_cell_title">{$APP.LBL_ORGANIZER}:</td>
			<td class="ical_cell_value">
				<a href="javascript:;" onclick="OpenCompose('{$ID}', 'reply')">{if $VALUES.organizer.cname}{$VALUES.organizer.cname}{else}{$VALUES.organizer.email}{/if}</a>
			</td>
		</tr>
		{/if}
		
		{if $VALUES.invitees && count($VALUES.invitees) > 0}
		<tr>
			<td class="ical_cell_title">{'LBL_INVITEES'|getTranslatedString:'Calendar'}:</td>
			<td class="ical_cell_value">
			{foreach item=INVITEE from=$VALUES.invitees}
				<span class="ical_invitee addrBubble">
				{if $INVITEE.record.module eq 'Users'}
					<img alt="" title="{$APP.LBL_USER}" src="modules/Messages/src/img/avatar_lead.png" width="16px" valign="top" />
					<span title="{$APP.LBL_USER}">{$INVITEE.record.entityname}</span>
				{elseif $INVITEE.record.module eq 'Contacts'}
					<img alt="" title="{$APP.Contact}" src="modules/Messages/src/img/avatar_contact.png" width="16px" valign="top" />
					<a title="{$APP.Contact}" href="index.php?module=Contacts&action=DetailView&record={$INVITEE.record.crmid}">{$INVITEE.record.entityname}</a>
				{else}
					{* no record found *}
					<a href="mailto:{$INVITEE.email}">{if $INVITEE.cname}{$INVITEE.cname}{else}{$INVITEE.email}{/if}</a>
				{/if}
				{if $INVITEE.partecipation eq 2}
					<img alt="" title="{'LBL_INVITATION_YES'|getTranslatedString:'Calendar'}" src="modules/Calendar/wdCalendar/css/images/calendar/meetingyes.png" valign="middle" />
				{elseif $INVITEE.partecipation eq 1}
					<img alt="" title="{'LBL_INVITATION_NO'|getTranslatedString:'Calendar'}" src="modules/Calendar/wdCalendar/css/images/calendar/meetingno.png" valign="middle" />
				{/if}
				</span>
			{/foreach}
			</td>
		</tr>
		{/if}
		
		{if $VALUES.description || $VALUES.description_html}
		<tr>
			<td class="ical_cell_title">{$APP.LBL_DESCRIPTION}:</td>
			<td class="ical_cell_value">{if $VALUES.description_html}{$VALUES.description_html}{else}{$VALUES.description}{/if}</td>
		</tr>
		{/if}
		
	</table>
	</div>
	<br>
	<div class="div_ical_buttons">
		{if $ICAL.method eq 'REQUEST'}
		<div style="padding:4px 4px 8px 4px;">{'LBL_INVITATION_QUESTION'|getTranslatedString:'ModNotifications'}?</div>
		<input type="hidden" id="ical_{$ICALID}_activityid" value="{$ICAL.activityid}" />
		<table class="table_segmented_buttons" cellspacing="0" cellpadding="0">
			<tr>
				<td id="ical_{$ICALID}_button_yes" class="td_segmented_button td_segmented_button_first {if $ICAL.partecipation eq 2}green{/if}" onclick="Messages_iCal.replyYes('{$ID}', '{$ICALID}');">{$APP.LBL_YES}</td>
				<td id="ical_{$ICALID}_button_no" class="td_segmented_button td_segmented_button_last {if $ICAL.partecipation eq 1}red{/if}" onclick="Messages_iCal.replyNo('{$ID}', '{$ICALID}');">{$APP.LBL_NO}</td>
			</tr>
		</table>
		{/if}
		{* crmv@81126 *}
		{if !$ICAL.activityid || $ICAL.partecipation eq 0}
		&nbsp;
		<table class="table_segmented_buttons" cellspacing="0" cellpadding="0">
			<tr>
				<td id="ical_{$ICALID}_button_preview" class="td_segmented_button td_segmented_button_first td_segmented_button_last" onclick="Messages_iCal.preview('{$ID}', '{$ICALID}', '{$ICAL.activityid}');" >{$APP.LBL_SHOW_PREVIEW}</td>
			</tr>
		</table>
		{/if}
		{* crmv@81126e *}
	</div>
</div>
<br>