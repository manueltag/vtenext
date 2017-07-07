{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@43194 *}
{* crmv@104853 *}
{if $UNSEEN_IDS|is_array && $COMMENTMODEL->id()|in_array:$UNSEEN_IDS}
	{assign var="UNSEEN_CLASS" value=" ModCommUnseen"}
{else}
	{assign var="UNSEEN_CLASS" value=""}
{/if}
<table id="tbl{$UIKEY}_{$COMMENTMODEL->id()}" class="notificationItem" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign="top" class="dataImg" style="padding:10px">
			<img src="{$COMMENTMODEL->authorPhoto()}" alt="{$COMMENTMODEL->author()}" title="{$COMMENTMODEL->author()}" class="userAvatar" />
			<div style="margin-top:10px">
				<i class="seenIcon vteicon md-sm" width="20px" style="cursor:pointer;{if $UNSEEN_CLASS neq ''}display:none{/if}" title="{'LBL_SEEN_ACTION'|getTranslatedString:'Messages'}" onclick="ModNotificationsCommon.markAsUnread('{$COMMENTMODEL->id()}', '{$UIKEY}')">panorama_fish_eye</i>
				<i class="unseenIcon vteicon md-sm" width="20px" style="cursor:pointer;{if $UNSEEN_CLASS eq ''}display:none{/if}" title="{'LBL_UNSEEN_ACTION'|getTranslatedString:'Messages'}" onclick="ModNotificationsCommon.markAsRead('{$COMMENTMODEL->id()}', '{$UIKEY}')">lens</i>
			</div>
		</td>
		<td valign="middle" class="dataContent{$UNSEEN_CLASS}" style="word-wrap:break-word">
			<div class="dataId">{$COMMENTMODEL->id()}</div>	{* crmv@30850 *}
			<div class="dataField{$UNSEEN_CLASS}">
				{assign var="AUTHOR" value=$COMMENTMODEL->author()}
				{if $AUTHOR neq ''}<b>{$AUTHOR}</b>&nbsp;{/if}{$COMMENTMODEL->content()}
			</div>
			<div class="dataLabel{$UNSEEN_CLASS}" style="padding-top:5px">
				<a href="javascript:;" title="{$COMMENTMODEL->timestamp()}" style="color:gray;text-decoration:none;">{$COMMENTMODEL->timestampAgo()}</a>
			</div>
		</td>
	</tr>
</table>