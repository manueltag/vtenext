
<div class='hide_tab' id="editRowmodrss_{$HOME_STUFFID}" style="position:relative; top:0px;left:0px;">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="small" valign="top">
	<tr>
{if $HOME_STUFFTYPE eq "Module" || $HOME_STUFFTYPE eq "RSS" || $HOME_STUFFTYPE eq "Default"}	
		<td align="left" class="homePageMatrixHdr" style="height:30px; width:250px;" nowrap width="40%">
			{$MOD.LBL_HOME_SHOW}&nbsp;
			<select id="maxentries_{$HOME_STUFFID}" name="maxid" class="small" style="width:40px;">
	{section name=iter start=1 loop=13 step=1}
				<option value="{$smarty.section.iter.index}" {if $HOME_STUFF.Maxentries==$smarty.section.iter.index} selected {/if}>
					{$smarty.section.iter.index}
				</option>
	{/section}
			</select>&nbsp;{$MOD.LBL_HOME_ITEMS}
		</td>
		<td align="right" class="homePageMatrixHdr" nowrap style="height:28px;" width=60%>
			<input type="button" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " name="save" class="crmbutton small save" onclick="saveEntries('maxentries_{$HOME_STUFFID}')">
			<input type="button" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " name="cancel" class="crmbutton small cancel" onclick="cancelEntries('editRowmodrss_{$HOME_STUFFID}')">
		</td>		
{elseif $HOME_STUFFTYPE eq "DashBoard"}
		<td  valign="top" align='center' class="homePageMatrixHdr" style="height:28px;" width=60%>
			<input type="radio" id="dashradio_0" name="dashradio_{$HOME_STUFFID}" value="horizontalbarchart" {if $DASHDETAILS.$HOME_STUFFID.Chart eq 'horizontalbarchart'}checked{/if}>Horizontal
			<input type="radio" id="dashradio_1" name="dashradio_{$HOME_STUFFID}" value="verticalbarchart"{if $DASHDETAILS.$HOME_STUFFID.Chart eq 'verticalbarchart'}checked{/if}>Vertical
			<input type="radio" id="dashradio_2" name="dashradio_{$HOME_STUFFID}" value="piechart" {if $DASHDETAILS.$HOME_STUFFID.Chart eq 'piechart'}checked{/if}>Pie
		</td>
		</tr>
		<tr>
			<td  valign="top" align="center" class="homePageMatrixHdr" nowrap style="height:28px;" width="40%">
			<input type="button" name="save" title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " class="crmbutton small save" onclick="saveEditDash({$HOME_STUFFID})">
			<input type="button" name="cancel" title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " class="crmbutton small cancel" onclick="cancelEntries('editRowmodrss_{$HOME_STUFFID}')">
			</td>
		</tr>	
{elseif $HOME_STUFFTYPE eq "URL"}
		<td  valign="top" align='center' class="homePageMatrixHdr" style="height:28px;" width=60%>
			<input type="text" id="url_{$HOME_STUFFID}" name="url" value="{$URL}">
		</td>
		</tr>
		<tr>
			<td  valign="top" align="center" class="homePageMatrixHdr" nowrap style="height:28px;" width="40%">
			<input type="button" name="save" value="Save" class="crmbutton small save" onclick="saveEditurl('url_{$HOME_STUFFID}')">
			<input type="button" name="cancel" value="Cancel" class="crmbutton small cancel" onclick="cancelEntries('editRowmodrss_{$HOME_STUFFID}')">
			</td>
		</tr>		
{/if}
	</tr>
	</table>
</div>

{if $HOME_STUFFTYPE eq "Module"}
	<input type=hidden id=more_{$HOME_STUFFID} value="{$HOME_STUFF.ModuleName}"/>
	<input type=hidden id=cvid_{$HOME_STUFFID} value="{$HOME_STUFF.cvid}">
	<table border=0 cellspacing=0 cellpadding=2 width=100% class="table">
	{assign var='cvid' value=$HOME_STUFF.cvid}
	{assign var='modulename' value=$HOME_STUFF.ModuleName}
	<tr>	   
		<td width=5%>
			&nbsp;
		</td>
		{foreach item=header from=$HOME_STUFF.Header}
		<td align="left">
			<b>{$header}</b>
		</td>
		{/foreach}
	</tr>
	{if $HOME_STUFF.Entries|@count > 0}
		{foreach item=row key=crmid from=$HOME_STUFF.Entries}
 	<tr>
		<td>
			<a href="index.php?module={$HOME_STUFF.ModuleName}&amp;action=DetailView&amp;record={$crmid}">
				<i class="vteicon md-sm" title="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}">open_in_new</i>
			</a>
		</td>
		{assign var=color value=$row.clv_color}
		{foreach key=index item=element from=$row}
			{if ($index neq 'clv_color' and $index neq 'clv_status') or $index eq '0'} {* crmv@108022 *}
				<td bgcolor="{$color}" align="left">
					{$element}
				</td>
			{/if}
		{/foreach}
	</tr>
		{/foreach}
	{else}
		<div class="componentName">{$APP.LBL_NO_DATA}</div>
	{/if}
	</table>

{elseif $HOME_STUFFTYPE eq "Default"}
	<input type=hidden id=more_{$HOME_STUFFID} value="{$HOME_STUFF.Details.ModuleName}"/>
	<table border=0 cellspacing=0 cellpadding=2 width=100% class="table">
	<tr>	   
	{foreach item=header from=$HOME_STUFF.Details.Header name="header"}
		{if $smarty.foreach.header.iteration eq 1}
			<td width=5%>&nbsp;</td>
		{/if}
		<td align="left"><b>{$header}</b></td>
	{/foreach}
	</tr>
	{if $HOME_STUFF.Details.Entries|@count > 0}
		{foreach item=row key=crmid from=$HOME_STUFF.Details.Entries}
	<tr>
		<td>
			{if $HOME_STUFF.Details.Title.1 eq "My Sites"}
			<i class="vteicon md-sm" title="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}">open_in_new</i>
			{elseif $HOME_STUFF.Details.Title.1 neq "Key Metrics" && $HOME_STUFF.Details.Title.1 neq "My Group Allocation"}
			<i class="vteicon md-sm" title="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}">open_in_new</i>
			{elseif $HOME_STUFF.Details.Title.1 eq "Key Metrics"}
			<i class="vteicon md-sm" title="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}">open_in_new</i>
			{elseif $HOME_STUFF.Details.Title.1 eq "My Group Allocation"}
			<i class="vteicon md-sm" title="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}">open_in_new</i>
			{/if}
		</td>
		{foreach item=element from=$row}
		<td align="left"> {$element}</td>
		{/foreach}
	</tr>
		{/foreach}		
	{else}
		<div class="componentName">{$APP.LBL_NO_DATA}</div>
	{/if}
	</table>
	
{elseif $HOME_STUFFTYPE eq "RSS"}
	<input type=hidden id=more_{$HOME_STUFFID} value="{$HOME_STUFF.Entries.More}"/>
	<table border=0 cellspacing=0 cellpadding=2 width=100%>
		{foreach item="details" from=$HOME_STUFF.Entries.Details}
			<tr>
				<td align="left">
					<a href="{$details.1}" target="_blank">
						{$details.0|truncate:50}...
					</a>
				</td>
			</tr>
		{/foreach}
	</table>
	
{elseif $HOME_STUFFTYPE eq "DashBoard"}
	<input type=hidden id=more_{$HOME_STUFFID} value="{$DASHDETAILS[$HOME_STUFFID].DashType}"/>
	<table border=0 cellspacing=0 cellpadding=5 width=100%>
		<tr>
			<td align="left">{$HOME_STUFF}</td>
		</tr>
	</table>		

{/if}
{if $HOME_STUFF.Details|@is_array == 'true'}
<input id='search_qry_{$HOME_STUFFID}' name='search_qry_{$HOME_STUFFID}' type='hidden' value='{$HOME_STUFF.Details.search_qry}' />
{/if}
