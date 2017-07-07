{include file="modules/M/generic/Header.tpl"}

<body>

<div id="__homebox__">

	<table width=100% cellpadding=0 cellspacing=0 border=0>
	<tr class="toolbar">
		<!-- crmv@manuele -->
		{if $_SELSKIN eq 'iPhone.css'}
		<td align="left"><a class="backButton" href="index.php?action=Logout" target="_self">{$APP.LBL_LOGOUT}</a></td>
		{else}
		<td align="left"><a class="link" href="index.php?action=Logout" target="_self"><img src="resources/images/iconza/yellow/logout_32x32.png" border="0"></a></td>
		{/if}
		<!-- crmv@manuele-e -->
		<td width="100%"><h1 class='page_title'>{$TITLE}</h1></td>
		<!-- crmv@manuele -->
		{if $_SELSKIN eq 'iPhone.css'}
		<td align="right"><a class="toolButton" href="javascript:void(0);" onclick="$fnT('__homebox__','__settingsbox__');" target="_self">{$APP.Settings}</a></td>
		{else}
		<td align="right"><a class="link" href="javascript:void(0);" onclick="$fnT('__homebox__','__settingsbox__');" target="_self"><img src="resources/images/iconza/yellow/user_32x32.png" border="0"></a></td>
		{/if}
		<!-- crmv@manuele-e -->
	</tr>
	<tr>
		<td colspan="3">	
		
			<table width=100% cellpadding=0 cellspacing=0 border=0 class="table_list">
				{foreach item=_MODULE from=$_MODULES}
				<tr>
				<td width="100%">			
					<a href="index.php?action=List&module={$_MODULE->name}" target="_blank">{$_MODULE->label}</a>				
				</td>
				<!-- crmv@manuele -->
				<td>
					<a href="index.php?action=List&module={$_MODULE->name}" target="_blank" class="link_rhook"><img src="{php}if (Mobile_COre_Skin::selected() == 'iPhone.css') echo 'resources/iui/listArrow.png'; else echo 'resources/images/iconza/royalblue/right_arrow_16x16.png';{/php}" border="0"></a>
				</td>
				<!-- crmv@manuele-e -->
				
				</tr>
				{/foreach}
			</table>
		
		</td>
	</tr>
	</table>

</div>


<div id="__settingsbox__" style='display:none;'>
	<table width=100% cellpadding=0 cellspacing=0 border=0>
	<tr class="toolbar">
		<!-- crmv@manuele -->
		{if $_SELSKIN eq 'iPhone.css'}
		<td align="right" style="padding-right: 5px;"><a class="backButton" href="javascript:void(0);" onclick="$fnT('__settingsbox__','__homebox__');">{$APP.Home}</a></td>
		{else}
		<td>&nbsp;</td>
		{/if}
		<!-- crmv@manuele-e -->
		<td width="100%">
			<h1 class='page_title'>
			<!-- crmv@manuele -->
			<!-- My Settings -->
			{$APP.Settings}
			<!-- crmv@manuele-e -->
			</h1>
		</td>
		<!-- crmv@manuele -->
		{if $_SELSKIN eq 'iPhone.css'}
		<td>&nbsp;</td>
		{else}
		<td align="right" style="padding-right: 5px;"><a class="link" href="javascript:void(0);" onclick="$fnT('__settingsbox__','__homebox__');"><img src="resources/images/iconza/yellow/delete_32x32.png" border="0"></a></td>
		{/if}
		<!-- crmv@manuele-e -->
	</tr>
	
	<tr>
		<td colspan=3>
		
			<form action="index.php" method="GET">
				<table width=100% cellpadding=5 cellspacing=0 border=0>
				<tr>
					<td width="20px" nowrap="nowrap">
					<!-- crmv@manuele -->
					<!-- Select Skin -->
					{$MOD.LBL_THEME}
					<!-- crmv@manuele-e -->
					</td>
					
					<td>
					<select name="skin" onchange="this.form.submit();">
						{foreach item=_SKINVALUE key=_SKINKEY from=$_ALLSKINS}
							<option value="{$_SKINKEY}" {if $_SKINKEY eq $_SELSKIN}selected=true{/if}>{$_SKINVALUE}</option>
						{/foreach}
					</select>
					
					<input type="hidden" name="action" value="Settings"/>
					<input type="hidden" name="mode" value="reset_skin"/>
					</td>
				</tr>
				</table>
			</form>
		
		</td>
		
	</tr>
	</table>
</div>

</body>

{include file="modules/M/generic/Footer.tpl"}