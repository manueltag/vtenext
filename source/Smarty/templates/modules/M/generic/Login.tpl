{include file="modules/M/generic/Header.tpl"}

<body>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr class="toolbar">
	<td>
		<h1 class='page_title'>VTE CRM</h1>
	</td>
</tr>

<tr>
	<td>	
		<form method="post" action="index.php?action=Login">
		
		<table width=100% cellpadding=5 cellspacing=0 border=0 class="panel_login">
		<tr>
			<td width="50%"></td>
			<td colspan="2">
				{if $_ERR}<p class='error'>{$_ERR}</p>
				{else}<p>Effettua il Login per continuare...</p>{/if}
			</td>
			<td width="50%"></td>
		</tr>
		<tr>
			<td width="50%"></td>
			<td align="right" nowrap>{$MOD.LBL_LIST_USER_NAME}</td>
			<td><input type="text" name="username" value=""/></td>
			<td width="50%"></td>
		</tr>
		<tr>
			<td width="50%"></td>
			<td align="right">{$MOD.LBL_PASSWORD}</td>
			<td><input type="password" name="password" value=""/></td>
			<td width="50%"></td>
		</tr>
		
		<tr bgcolor="#f5f5f5">
			<td width="50%"></td>
			<td align="right">{$MOD.LBL_THEME}</td>
			<td align="left"><select class="small" name='login_theme'>
			{foreach item=_SKINVALUE key=_SKINKEY from=$_ALLSKINS}
				<option value="{$_SKINKEY}" {if $_SKINKEY eq $_SELSKIN}selected=true{/if}>{$_SKINVALUE}</option>
			{/foreach}
			</select></td>
			<td width="50%"></td>
		</tr>
		
		<tr>
			<td colspan="4" align="center">
				<input type="submit" value="Login" class="button">
			</td>
		</tr>
		</table>

		</form>
	</td>
</tr>
</table>

</body>

{include file="modules/M/generic/Footer.tpl"}