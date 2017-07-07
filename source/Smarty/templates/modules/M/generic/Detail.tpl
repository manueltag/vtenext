{include file="modules/M/generic/Header.tpl"}

<link rel="stylesheet" type="text/css" media="all" href="../../jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="../../jscalendar/calendar.js"></script>
<script type="text/javascript" src="../../jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="../../jscalendar/calendar-setup.js"></script>

<script language="JavaScript" type="text/javascript" src="../../include/js/dtlviewajax.js"></script>

<body>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr class="toolbar">
	<td width="5%">
	{if $selected_skin eq 'iPhone.css'}
		<a class="backButton" href="javascript:history.back();">{$APP.LBL_BACK}</a>
	{else}
		<a class="link" href="javascript:history.back();"><img src="resources/images/iconza/royalblue/left_arrow_16x16.png" border="0"></a>
	{/if}
	</td>
	<td width="90%" align="left"><h1 class='page_title'>{$_MODULE->label}</h1></td>	
	<td width="5%" align="right" nowrap="nowrap">
		{if $_MODULE->name eq 'Accounts' || $_MODULE->name eq 'Contacts' || $_MODULE->name eq 'Leads' || $_MODULE->name eq 'Calendar' || $_MODULE->name eq 'Events'}
			{if $selected_skin eq 'iPhone.css'}
			 <a class="toolButton" href="?action=Edit&id={$ID}">{$APP.LBL_EDIT_BUTTON}</a>
			{else}	
			 <a class="link" href="?action=Edit&id={$ID}"><img src="resources/images/iconza/royalblue/edit_24x24.png" border="0"></a>
			{/if}
			<!--  a class="link" href="javascript:confirm('Delete Record');"><img src="resources/images/iconza/yellow/delete_24x24.png" border="0"></a -->
		{/if}		
	</td>
</tr>
<tr>
	<td colspan="3">	
	
		<table width=100% cellpadding=5 cellspacing=0 border=0 class="table_detail">
			{foreach item=_FIELDNAMES key=_BLOCKLABEL from=$_RECORD}
			
				<!-- se il blocco non contiene campi valorizzati non lo mostro -->
				{assign var=BLOCK_EMPTY value='yes'}
				{foreach item=_COLUMN key=_NUM from=$_FIELDNAMES}
					{foreach key=label item=data from=$_COLUMN}
						{assign var=keyval value=$data.value}
						{if $keyval ne ''}
							{assign var=BLOCK_EMPTY value='no'}
						{/if}
					{/foreach}
				{/foreach}
			
				{if $BLOCK_EMPTY eq 'no'}
					<tr>
						<td colspan=2 class="hdrlabel">{$_BLOCKLABEL}</td>
					</tr>
				{/if}
				
				{foreach item=_COLUMN key=_NUM from=$_FIELDNAMES}
					{foreach key=label item=data from=$_COLUMN}
					   {assign var=keyid value=$data.ui}
					   {assign var=keyval value=$data.value}
					   {assign var=keytblname value=$data.tablename}
					   {assign var=keyfldname value=$data.fldname}
					   {assign var=keyfldid value=$data.fldid}
					   {assign var=keyoptions value=$data.options}
					   {assign var=keysecid value=$data.secid}
					   {assign var=keyseclink value=$data.link}
					   {assign var=keycursymb value=$data.cursymb}
					   {assign var=keysalut value=$data.salut}
					   {assign var=keyaccess value=$data.notaccess}
					   {assign var=keycntimage value=$data.cntimage}
					   {assign var=keyadmin value=$data.isadmin}
					   {assign var=display_type value=$data.displaytype}
					   
						{if $label ne '' && $keyval ne ''}
						<tr>
							{if $keycntimage ne ''}
								<td class="label" align=right width=25%><input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>{$keycntimage}</td>
							{elseif $keyid eq '71' || $keyid eq '72'}<!-- Currency symbol -->
								<td class="label" align=right width=25%>{$label}<input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input> ({$keycursymb})</td>
							{else}
								<td class="label" align=right width=25%><input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>{$label}</td>
							{/if}
					   		{include file="DetailViewFields.tpl"}
					   	</tr>
						{/if}
					{/foreach}
				{/foreach}
			{/foreach}
		</table>
	</td>
</tr>
</table>

</body>

{include file="modules/M/generic/Footer.tpl"}