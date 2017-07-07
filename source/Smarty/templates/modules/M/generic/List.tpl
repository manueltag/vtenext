{include file="modules/M/generic/Header.tpl"}

<script type="text/javascript" src="../../modules/{$MODULE}/{$MODULE}.js"></script>

<body>

{if $old_action == 'Popup'}
<input name="from_link" id="from_link" type="hidden" value="{$smarty.request.fromlink.value|@vtlib_purify}">
{/if}

<div id="__listview__" {if $_MODE eq 'search'}style='display:none;'{/if}>
	<table width=100% cellpadding=0 cellspacing=0 border=0>
	<tr class="toolbar">
		<!-- crmv@manuele -->
		{if $selected_skin eq 'iPhone.css'}
			{if $old_action == 'Popup'}
				<td><a class="backButton" href="javascript:window.close();">{$APP.LBL_CANCEL_BUTTON_LABEL}</a></td>
			{else}
				<td><a class="backButton" href="javascript:window.close();">{$APP.Home}</a></td>
			{/if}
		{else}
		<td><a class="link" href="javascript:window.close();"><img src="resources/images/iconza/royalblue/undo_32x32.png" border="0"></a></td>
		{/if}		
		<!-- crmv@manuele-e -->
		<td width="100%">
			<h1 class='page_title'>
			
			{if $_PAGER->hasPrevious()}
			<!-- crmv@manuele -->
				{if $old_action == 'Popup'}
					<a class="link" href="?module={$_MODULE->name}&action=List&old_action=Popup&popuptype={php}echo $_REQUEST['popuptype'];{/php}&form={php}echo $_REQUEST['form'];{/php}&form_submit={php}echo $_REQUEST['form_submit'];{/php}&fromlink={php}echo $_REQUEST['fromlink'];{/php}&html={php}echo $_REQUEST['html'];{/php}&page={$_PAGER->previous()}&q={$_SEARCH_Q}"><img src="{php}if (Mobile_COre_Skin::selected() == 'iPhone.css') echo 'resources/images/iphone/leftlistArrowSel.png'; else echo 'resources/images/iconza/yellow/left_arrow_24x24.png';{/php}" border="0"></a>
				{else}
					<a class="link" href="?action=List&module={$_MODULE->name}&page={$_PAGER->previous()}&q={$_SEARCH_Q}"><img src="{php}if (Mobile_COre_Skin::selected() == 'iPhone.css') echo 'resources/images/iphone/leftlistArrowSel.png'; else echo 'resources/images/iconza/yellow/left_arrow_24x24.png';{/php}" border="0"></a>
				{/if}
			<!-- crmv@manuele-e -->
			{else}
			<!-- crmv@manuele -->
				<a class="link" href="javascript:void(0);"><img src="{php}if (Mobile_COre_Skin::selected() == 'iPhone.css') echo 'resources/images/iphone/empty.png'; else echo 'resources/images/iconza/white/left_arrow_24x24.png';{/php}" border="0"></a>
			<!-- crmv@manuele-e -->
			{/if}
			
			{$_MODULE->label}
			
			{if $_PAGER->hasNext(count($_RECORDS))}
			<!-- crmv@manuele -->
				{if $old_action == 'Popup'}
					<a class="link" href="?module={$_MODULE->name}&action=List&old_action=Popup&popuptype={php}echo $_REQUEST['popuptype'];{/php}&form={php}echo $_REQUEST['form'];{/php}&form_submit={php}echo $_REQUEST['form_submit'];{/php}&fromlink={php}echo $_REQUEST['fromlink'];{/php}&html={php}echo $_REQUEST['html'];{/php}&page={$_PAGER->next()}&q={$_SEARCH_Q}"><img src="{php}if (Mobile_COre_Skin::selected() == 'iPhone.css') echo 'resources/iui/listArrowSel.png'; else echo 'resources/images/iconza/yellow/right_arrow_24x24.png';{/php}" border="0"></a>
				{else}
					<a class="link" href="?action=List&module={$_MODULE->name}&page={$_PAGER->next()}&q={$_SEARCH_Q}"><img src="{php}if (Mobile_COre_Skin::selected() == 'iPhone.css') echo 'resources/iui/listArrowSel.png'; else echo 'resources/images/iconza/yellow/right_arrow_24x24.png';{/php}" border="0"></a>
				{/if}
			<!-- crmv@manuele-e -->
			{else}
			<!-- crmv@manuele -->
				<a class="link" href="javascript:void(0);"><img src="{php}if (Mobile_COre_Skin::selected() == 'iPhone.css') echo 'resources/images/iphone/empty.png'; else echo 'resources/images/iconza/white/right_arrow_24x24.png';{/php}" border="0"></a>
			<!-- crmv@manuele-e -->
			{/if}
			
			</h1>
		</td>
		<!-- crmv@manuele -->
		<td align="right" style="padding-right: 5px;" nowrap>
			{if $old_action neq 'Popup' && ($_MODULE->name eq 'Accounts' || $_MODULE->name eq 'Contacts' || $_MODULE->name eq 'Leads' || $_MODULE->name eq 'Calendar' || $_MODULE->name eq 'Events')}
				<a class="toolButtonSmall2" href="?action=Edit&id={$_MODULE_ID}x"><img style="vertical-align:middle;align:center;" src="{php}if (Mobile_COre_Skin::selected() == 'iPhone.css') echo 'resources/images/iphone/create.png'; else echo 'resources/images/iconza/yellow/add_32x32.png';{/php}" border="0"></a>
			{/if}
			{if $selected_skin eq 'iPhone.css'}
				<a class="toolButtonSmall" href="javascript:void(0);" onclick="$fnT('__listview__', '__searchbox__'); $fnFocus('__searchbox__q_');" target="_self"><img style="vertical-align:middle;align:center;" src="resources/images/iphone/lens.png" border="0"></a>
			{else}
				<a class="link" href="javascript:void(0);" onclick="$fnT('__listview__', '__searchbox__'); $fnFocus('__searchbox__q_');" target="_self"><img style="vertical-align:middle;align:center;" src="resources/images/iconza/yellow/lens_32x32.png" border="0"></a>
			{/if}
		</td>
		<!-- crmv@manuele-e -->
	</tr>
	
	<tr>
		<td colspan="3">
			<table width=100% cellpadding=0 cellspacing=0 border=0 class="table_list">
			
				{foreach item=_RECORD from=$_RECORDS}

					{if $old_action eq 'Popup'}
						<tr>
						<td width="100%">{$_RECORD}</td>
						<td></td>
						</tr>
					{else}
						<tr>
						<td width="100%">
							<a href="?action=Detail&id={$_RECORD->id}" target="_self">{$_RECORD}</a>
						</td>
						<td>
						<!-- crmv@manuele -->
							<a href="?action=Detail&id={$_RECORD->id}" target="_self" class="link_rhook"><img src="{php}if (Mobile_COre_Skin::selected() == 'iPhone.css') echo 'resources/iui/listArrow.png'; else echo 'resources/images/iconza/royalblue/right_arrow_16x16.png';{/php}" border="0"></a>
						<!-- crmv@manuele-e -->								
						</td>
						</tr>
					{/if}
				
				{foreachelse}
				
				<tr class="info">
				<td width=25% align="right">
					<img src="resources/images/iconza/royalblue/info_24x24.png" border=0 />
				</td>
				<td width=100% align="left" valign="center">
					{if $_PAGER->hasPrevious()}
					<p>No more records found.</p>
					{else}
					<p>No records available.</p>
					{/if}
				</td>
				</tr>
				
				{/foreach}
			</table>
		
		</td>
	</tr>
	</table>
</div>

<div id="__searchbox__" {if $_MODE neq 'search'}style='display:none;'{/if}>
	<table width=100% cellpadding=0 cellspacing=0 border=0>
	<tr class="toolbar">
		<!-- crmv@manuele -->
		{if $selected_skin eq 'iPhone.css'}
		<td><a class="backButton" href="javascript:void(0);" onclick="$fnT('__searchbox__','__listview__');">{$_MODULE->label}</a></td>
		{elseif $old_action neq 'Popup'}
		<td><a class="link" href="?action=SearchConfig&module={$_MODULE->name}" target="_self"><img src="resources/images/iconza/yellow/wrench_32x32.png" border="0"></a></td>
		{else}
		<td></td>
		{/if}
		<!-- crmv@manuele-e -->
		<td width="100%">
			<h1 class='page_title'>
			{$APP.LBL_SEARCH} {$_MODULE->label}
			</h1>
		</td>
		<!-- crmv@manuele -->
		{if $selected_skin eq 'iPhone.css' && $old_action neq 'Popup'}
		<td><a class="toolButton" href="?action=SearchConfig&module={$_MODULE->name}" target="_self">{$APP.Settings}</a></td>
		{elseif $selected_skin neq 'iPhone.css'}
		<td align="right" style="padding-right: 5px;"><a class="link" href="javascript:void(0);" onclick="$fnT('__searchbox__','__listview__');"><img src="resources/images/iconza/yellow/zoom_out_32x32.png" border="0"></a></td>
		{else}
		<td></td>
		{/if}
		<!-- crmv@manuele-e -->
	</tr>
	
	<tr>
		<td colspan=3 align="center">
		
			<!-- denis - aggiunto nome form -->
			<form action="index.php" method="GET" name="searchform" onsubmit="if(this.q.value == '') return false;">
				{if $old_action eq 'Popup'}
					<input type="hidden" name="old_action" value="Popup"/>
					<input type="hidden" name="popuptype" value="{php}echo $_REQUEST['popuptype'];{/php}"/>
					<input type="hidden" name="form" value="{php}echo $_REQUEST['form'];{/php}"/>
					<input type="hidden" name="fromlink" value="{php}echo $_REQUEST['fromlink'];{/php}"/>
					<input type="hidden" name="html" value="{php}echo $_REQUEST['html'];{/php}"/>
				{/if}
				<input type="hidden" name="action" value="List" />
				<input type="hidden" name="module" value="{$_MODULE->name}" />
				<input id='__searchbox__q_' type="text" name="q" class="searchbox" value="{$_SEARCH_Q}"/>
				<!-- denis start -->
				{if $selected_skin eq 'iPhone.css'}
					<a href="javascript:void(0);"><img class="searchbox" src="resources/images/iphone/lens1.png" onclick="document.searchform.submit();" border=0 /></a>
				{else}
					<a href="javascript:void(0);"><img class="searchbox" src="resources/images/iconza/royalblue/lens_24x24.png" href="javascript:void(0);" onclick="document.searchform.submit();" border=0 /></a>
				{/if}
				<!-- denis end -->
			</form>
		
		</td>
		
	</tr>
	</table>
</div>

</body>

{include file="modules/M/generic/Footer.tpl"}