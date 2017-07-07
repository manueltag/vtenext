{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}

<input type="hidden" id="{$UIKEY}_unseen_ids" value="{','|implode:$UNSEEN_IDS}" />
<input type="hidden" id="{$UIKEY}_max_number_of_news" value="{$CRITERIA}" />
{assign var="INDICATOR" value="$('indicator"|cat:$UIKEY|cat:"')"}

<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>
		<div id="contentwrap_{$UIKEY}" style="width: 100%;">
			{foreach item=COMMENTMODEL from=$COMMENTS}
				{include file="modules/ModNotifications/widgets/DetailViewBlockCommentItem.tpl" COMMENTMODEL=$COMMENTMODEL}
			{foreachelse}
				<div class="fastEmptyMask">
					<div class="fastEmptyMaskInner">
						<div class="bigCircle fastMaskIcon">
							<i class="vteicon nohover">language</i>
						</div>
						<span class="fastMaskText">
							{"LBL_NO_NOTIFICATIONS"|getTranslatedString}
						</span>
					</div>
				</div>
			{/foreach}
		</div>
	</td>
</tr>
{if $TOTAL > $CRITERIA}
	<tr>
		<td class="ModCommAnswerBox" align="center" style="padding: 3px 0px;">
			(1-{$CRITERIA} {'LBL_OF'|getTranslatedString:'Settings'} {$TOTAL})&nbsp;<a href="javascript:ModNotificationsCommon.loadModNotifications({$CRITERIA}+eval(ModNotificationsCommon.default_number_of_news),'{$smarty.request.target_frame}','{$smarty.request.indicator}');">{'LBL_SHOW_OTHER_NOTIFICATIONS'|getTranslatedString:'ModNotifications'}</a>
		</td>
	</tr>
{/if}
</table>