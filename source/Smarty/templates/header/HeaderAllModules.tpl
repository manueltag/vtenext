{* crmv@126984 crmv@128159 *}
<table cellspacing="0" cellpadding="5" border="0" class="small" width="100%">
<tr>
	<td align="right">
		<div class="form-group moduleSearch">
			<input type="text" class="form-control searchBox" id="menu_search_text" placeholder="{$APP.LBL_SEARCH_MODULE}" onclick="AllMenuObj.clearMenuSearchText(this)" onblur="AllMenuObj.restoreMenuSearchDefaultText(this)" />
			<span class="cancelIcon">
				<i class="vteicon md-link md-sm" id="menu_search_icn_canc" style="display:none" title="Reset" onclick="AllMenuObj.cancelMenuSearchSearchText()">cancel</i>&nbsp;	
			</span>
			<span class="searchIcon">
				<i class="vteicon md-link" id="menu_search_icn_go" title="{$APP.LBL_FIND}" onclick="AllMenuObj.searchInMenu();">search</i>
			</span>
		</div>
	</td>
</tr>
</table>
<table cellspacing="0" cellpadding="0" border="0" class="small" width="100%">
{assign var="count" value=0}
{foreach item=info from=$OtherModuleList}
	{assign var="url" value="index.php?module="|cat:$info.name|cat:"&action=index"}
	{if $count eq 0}
		{assign var="div_open" value=true}
		<tr>
	{/if}
	{assign var="count" value=$count+1}
	<td width="200" style="padding:10px"><a href="{$url}" class="menu_entry" style="padding:5px;">{$info.translabel}</a></td>
	{if $count eq 3}
		</tr>
		{assign var="count" value=0}
		{assign var="div_open" value=false}
	{/if}
{/foreach}
{if $div_open eq true}
	</tr>
{/if}
</table>