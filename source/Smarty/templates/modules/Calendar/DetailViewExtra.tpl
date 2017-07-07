{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}

{* crmv@98866 *}
{* crmv@101312 *}

{if $ACTIVITYDATA.activitytype neq 'Task'}
<table border=0 cellspacing=0 cellpadding=0 width=100% align=center id="calendarExtraTable"> {* crmv@107341 *}
		<tr>
			<td>
				<table border=0 cellspacing=0 cellpadding=3 width=100%>
					<tr>
						<td class="dvtTabCache" style="width:10px">&nbsp;</td>
						<td id="cellTabRelatedto" class="dvtSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','on');ghide('addEventAlarmUI');ghide('addEventInviteUI');dispLayer('addEventRelatedtoUI');ghide('addEventRepeatUI');">{$MOD.LBL_LIST_RELATED_TO}</a></td>
						<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
						<td id="cellTabInvite" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','on');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','off');ghide('addEventAlarmUI');dispLayer('addEventInviteUI');ghide('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$MOD.LBL_INVITE}</a></td>
						<td class="dvtTabCache" style="width:10px">&nbsp;</td>
						{if $LABEL.reminder_time neq ''}
						<td id="cellTabAlarm" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','on');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','off');dispLayer('addEventAlarmUI');ghide('addEventInviteUI');ghide('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$MOD.LBL_REMINDER}</a></td>
						{/if}
						<td class="dvtTabCache" style="width:10px">&nbsp;</td>
						{if $LABEL.recurringtype neq ''}
						<td id="cellTabRepeat" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','on');switchClass('cellTabRelatedto','off');ghide('addEventAlarmUI');ghide('addEventInviteUI');dispLayer('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$MOD.LBL_REPEAT}</a></td>
						{/if}
						<td class="dvtTabCache" style="width:100%">&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td width=100% valign=top align=left class="dvtContentSpace" style="padding:10px;height:120px">
				<!-- Invite UI -->
				<div id="addEventInviteUI" style="display:none;width:100%">
					{include file="modules/Calendar/EventInviteUIReadOnly.tpl" disableStyle=true}
				</div>
				<!-- Reminder UI -->
				<div id="addEventAlarmUI" style="display:none;width:100%">
					{include file="modules/Calendar/EventAlarmUIReadOnly.tpl" disableStyle=true}
				</div>
				<!-- Repeat UI -->
				<div id="addEventRepeatUI" style="display:none;width:100%">
					{include file="modules/Calendar/EventRepeatUIReadOnly.tpl" disableStyle=true}
				</div>
				<!-- Relatedto UI -->
				<div id="addEventRelatedtoUI" style="display:block;width:100%">
					{include file="modules/Calendar/EventRelatedToUIReadOnly.tpl" disableStyle=true}
				</div>
			</td>
		</tr>
	</table>
{else}
	{if $LABEL.parent_id neq '' || $LABEL.contact_id neq ''}
		<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" id="calendarExtraTable"> {* crmv@107341 *}
			<tr>
				<td>
					<table border="0" cellpadding="3" cellspacing="0" width="100%">
					<tr>
						<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
						{if ($LABEL.parent_id neq '') || ($LABEL.contact_id neq '') }
							<td id="cellTabRelatedto" class="dvtSelectedCell" align=center nowrap><a href="javascript:doNothing()">{$MOD.LBL_RELATEDTO}</a></td>
						{/if}
						<td class="dvtTabCache" style="width: 100%;">&nbsp;</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width=100% valign=top align=left class="dvtContentSpace" style="padding:10px;height:120px">
					<div id="addTaskRelatedtoUI" style="display:{$vision};width:100%">
						{include file="modules/Calendar/TodoRelatedToUIReadOnly.tpl" disableStyle=true}
					</div>
				</td>
			</tr>
		</table>
	{/if}
{/if}