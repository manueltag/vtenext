
<style type="text/css">
{literal}
	.level2Bg {
		color: #2b577c;
		background-color: #ebf2f8;
		border-bottom: 1px;
		border-color: gray;
		padding: 0px 20px 0px 20px;
		font-size: 16px;
	}
	
	.licence {
		height: 350px;
		width: 100%;
	}
{/literal}
</style>

{* crmv@22106 *}
<script language="JavaScript">
{literal}
jQuery(window).load(function() {
	loadedPopup();
});
{/literal}
</script>
{* crmv@22106e *}

<div id="popupContainer" style="display:none;"></div>

<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr height='40px'>
		{if $CAN_UPDATE}
			<td class='level2Bg'>
				{"LICENSE_ID"|getTranslatedString:'Morphsuit'}: {$MORPHSUIT_NO}
			</td>
			<td align='right' class='level2Bg'>
				<font size='2'><a href="javascript:top.window.location='index.php?module=Morphsuit&amp;action=MorphsuitAjax&amp;file=UpdateMorphsuit';">Update your license</a></font>
			</td>
		{else}
			<td class="level2Bg" colspan="2">License</td>
		{/if}
	</tr>
	<tr>
		<td align="center" colspan="2"><font size='2'>
			{if $FREE_VERSION}
				This software is a collective work consisting of the following major Open Source components:<br> 
				Apache software, MySQL server, PHP, SugarCRM, vTigerCRM, ADOdb, Smarty, PHPMailer, phpSysinfo, MagpieRSS and others, each licensed under a separate Open Source License.
				CrmVillage.biz is not affiliated with nor endorsed by any of the above providers.
				<br>If you are intended to use this software you also must subscribe the <a href='{$LICENSE_FILE}' class='copy' target='_blank'>license</a>
			{else}
				<iframe class="licence" frameborder="0" src="{$LICENSE_FILE}" scrolling="auto"></iframe>
			{/if}
		</font></td>
	</tr>
	</table>
</body>
</html>
