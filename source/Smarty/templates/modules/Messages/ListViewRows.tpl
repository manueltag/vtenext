{if $smarty.request.appendlist eq 'yes'}
	@#@#@#{$NAVIGATION}@#@#@#
{/if}
{foreach item=entity key=entity_id from=$LISTENTITY}
	<!-- crmv@7230 -->
	{assign var=color value=$entity.clv_color}
	<tr id="row_{$entity_id}"> {* class="lvtColDataHover" *}
	 <!-- DS-ED VlMe 27.3.2008 -->
	 {* <!-- KoKr bugfix add (check_object) idlist for csv export --> *}
	<td width="20px" style="vertical-align:top; border-bottom:1px solid #e0e0e0;">
		<table border="0" cellspacing="0" cellpadding="0" width=100%>
		<tr height="20px">
			<td>
			{if $entity.thread eq 0}
				<input style="display:none" type="checkbox" name="selected_id" id="{$entity_id}" value="{$entity_id}" onClick="update_selected_ids(this.checked,'{$entity_id}',this.form,true);"
				{if count($SELECTED_IDS_ARRAY) > 0}
					{if $ALL_IDS eq 1 && !in_array($entity_id,$SELECTED_IDS_ARRAY)}
						checked
					{else}
						{if ($ALL_IDS neq 1 and $SELECTED_IDS neq "" and in_array($entity_id,$SELECTED_IDS_ARRAY))}
							checked
						{/if}
					{/if}
				{else}
					{if $ALL_IDS eq 1}
						checked
					{/if}
				{/if}
				>
			{/if}
			</td>
		</tr>
		<tr valign="middle">
			<td>
				{assign var="style_flag" value="style='display:none;'"}
				{assign var="unseenThreadFlag" value=false}
				{if $entity.thread neq ''}
					{assign var="unseenThreadFlag" value=$FOCUS->checkThreadFlag('unseen',$entity_id,$entity.thread)}
				{/if}
				{if $entity.seen eq 'no' || $unseenThreadFlag eq true}
					{assign var="style_flag" value=""}
				{/if}
				<div id="flag_{$entity_id}_unseen" {$style_flag}><img title="{'Seen'|@getTranslatedString:$MODULE}" src="modules/Messages/src/img/flag_unseen.png" /></div>
				
				{assign var="style_flag" value="style='display:none;'"}
				{assign var="flaggedThreadFlag" value=false}
				{if $entity.thread neq ''}
					{assign var="flaggedThreadFlag" value=$FOCUS->checkThreadFlag('flagged',$entity_id,$entity.thread)}
				{/if}
				{if $entity.flagged eq 'yes' || $flaggedThreadFlag eq true}
					{assign var="style_flag" value=""}
				{/if}
				<div id="flag_{$entity_id}_flagged" {$style_flag}><img title="{'Flagged'|@getTranslatedString:$MODULE}" src="modules/Messages/src/img/flag_flagged.png" /></div>
										
				{assign var="style_flag_answered_forwarded" value="style='display:none;'"}
				{assign var="style_flag_answered" value="style='display:none;'"}
				{assign var="style_flag_forwarded" value="style='display:none;'"}
				{if $entity.answered eq 'yes' && $entity.forwarded eq 'yes'}
					{assign var="style_flag_answered_forwarded" value=""}
				{elseif $entity.answered eq 'yes'}
					{assign var="style_flag_answered" value=""}
				{elseif $entity.forwarded eq 'yes'}
					{assign var="style_flag_forwarded" value=""}
				{/if}
				<div id="flag_{$entity_id}_answered_forwarded" {$style_flag_answered_forwarded}><img title="{'Answered'|@getTranslatedString:$MODULE}, {'Forwarded'|@getTranslatedString:$MODULE}" src="modules/Messages/src/img/flag_answ_fwd.png" /></div>
				<div id="flag_{$entity_id}_answered" {$style_flag_answered}><img title="{'Answered'|@getTranslatedString:$MODULE}" src="modules/Messages/src/img/flag_answered.png" /></div>
				<div id="flag_{$entity_id}_forwarded" {$style_flag_forwarded}><img title="{'Forwarded'|@getTranslatedString:$MODULE}" src="modules/Messages/src/img/flag_forwarded.png" /></div>
				
				{* crmv@59094 *}
				{assign var="style_flag_attachments" value="style='display:none;'"}
				{if $FOCUS->haveAttachments($entity_id)}
					{assign var="style_flag_attachments" value=""}
				{/if}
				<div id="flag_{$entity_id}_attachments" {$style_flag_attachments}><img title="{'LBL_FLAG_ATTACHMENTS'|@getTranslatedString:$MODULE}" src="modules/Messages/src/img/flag_attach.png" /></div>
				{* crmv@59094e *}
				
				{assign var="style_flag" value="style='display:none;'"}
				{if $FOCUS->haveRelations($entity_id,'','',$entity.thread)}
					{assign var="style_flag" value=""}
				{/if}
				<div id="flag_{$entity_id}_relations" {$style_flag}><img title="{'LBL_FLAG_LINK'|@getTranslatedString:$MODULE}" src="modules/Messages/src/img/flag_link.png" /></div>
				
				{assign var="style_flag" value="style='display:none;'"}
				{if $FOCUS->haveRelations($entity_id,'ModComments','',$entity.thread)}
					{assign var="style_flag" value=""}
				{/if}
				<div id="flag_{$entity_id}_talks" {$style_flag}><img title="{'LBL_MODCOMMENTS_COMMUNICATIONS'|@getTranslatedString:'ModComments'}" src="modules/Messages/src/img/mod_comments.png" /></div>
			</td>
		</tr>
		</table>
	</td>
	 <!-- DS-END -->
	 {assign var=thread_count value=$FOCUS->getChildren($entity.thread,'',true)}
	<td bgcolor="{$color}" class="listMessageRow" {if $entity.thread eq 0}onClick="selectRecord({$entity_id});"{else}onClick="selectThread({$entity_id},{$entity.thread},'{$thread_count}','{'Messages'|getTranslatedString:'Messages'|strtolower}');"{/if}>
		<table border="0" cellspacing="0" cellpadding="0" width=100%>
		<tr>
			<td align="right" style="font-size:11px; color: gray;" colspan="2">{$entity.mdate}</td>
		</tr>
		<tr>
			<td class="listMessageFrom" colspan="2">
				{if $CURRENT_FOLDER eq $SPECIAL_FOLDERS.Sent or $CURRENT_FOLDER eq $SPECIAL_FOLDERS.Drafts}
					{assign var="mto" value=$entity.mto|strip_tags}
					{assign var="mto_n" value=$entity.mto_n|strip_tags}
					{assign var="mto_f" value=$entity.mto_f|strip_tags}
					{$FOCUS->getAddressName($mto,$mto_n,$mto_f,true)}
				{else}
					{assign var="mfrom" value=$entity.mfrom|strip_tags}
					{assign var="mfrom_n" value=$entity.mfrom_n|strip_tags}
					{assign var="mfrom_f" value=$entity.mfrom_f|strip_tags}
					{$FOCUS->getAddressName($mfrom,$mfrom_n,$mfrom_f,true)}
				{/if}
			</td>
		</tr>
		<tr>
			{if $entity.thread eq 0}
				<td class="listMessageSubject" colspan="2">{$entity.subject}</td>
			{else}
				<td class="listMessageSubject" width="100%">{$entity.subject}</td>
				<td class="listMessageSubject" align="right" nowrap>
					{include file="BubbleNotification.tpl" COUNT=$thread_count}
				</td>
			{/if}
		</tr>
		{* crmv@93095 *}
		{if !empty($entity.cleaned_body)}
			<tr>
				<td class="style_Gray" colspan="2" style="word-wrap:break-word;">
					<div class="descriptionPreview" style="width:275px;overflow:auto;">
						{$entity.cleaned_body}
					</div>
				</td>
			</tr>
		{/if}
		{* crmv@93095e *}
		</table>
	</td>
	</tr>
	{* <tr style="height:1px;" bgcolor="black"><td colspan="2"></td></tr> *}
	<!-- crmv@7230e -->
{foreachelse}
	{* crmv@87055 *}
	{if $smarty.request.start gt 1 and $SEARCHING eq false}
		{* se sto scrollando e sto caricando il successivo slot di messaggi non ha senso mostrare il messaggio di lista vuota *}
	{else}
		{if $SEARCHING eq true and ($smarty.request.start lt ($FOCUS->search_intervals|@count-1) || $MESSAGES_RESULTS_PREV_SEARCH gt 0)}
			{* mostro il messaggio solo all'ultimo giro di ricerca se non ho avuto risultati *}
		{else}
			<tr><td colspan="2" style="height:340px" align="center" colspan="{$smarty.foreach.listviewforeach.iteration+1}">
				<div style="width: 100%; position: relative;">	<!-- crmv@18592 -->
					<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<tr>
		                	<td align="center">{$MSG_EMPTY_LIST}</td>	{* crmv@48159 *}
						</tr>
					</table>
				</div>
			</td></tr>
		{/if}
	{/if}
	{* crmv@87055e *}
{/foreach}