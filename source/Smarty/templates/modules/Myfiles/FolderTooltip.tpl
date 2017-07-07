{* crmv@30967 *}

<table class="small lview_foldertt_table" cellpadding="0" cellspacing="0">
	<tr><td class="dvtSelectedCell lview_foldertt_title" align="center">{$APP.LBL_FOLDER_CONTENT}</td></tr>
	{foreach item=doc from=$FOLDERDATA}
		<tr><td class="lview_foldertt_row">
			{$doc.title|truncate:30}
		</td></tr>
	{foreachelse}
		<tr><td class="lview_foldertt_row">{$MOD.LBL_NO_DOCUMENTS}</td></tr>
	{/foreach}

	{if count($FOLDERDATA) < $TOTALCOUNT}
		<tr><td class="lview_foldertt_row">...</td></tr>
	{/if}

	<tr><td class="lview_foldertt_lastrow"></td></tr>
</table>