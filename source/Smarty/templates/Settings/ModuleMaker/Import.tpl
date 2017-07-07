{* crmv@64542 *}

<div>
	<p>{$MOD.LBL_MMAKER_IMPORT_INTRO}</p>
</div>

<br>

<form id="module_maker_form" method="POST" action="index.php?module=Settings&amp;action=ModuleMaker&amp;mode=import&amp;module_maker_step=2&amp;parentTab=Settings" enctype="multipart/form-data">

<br><br>
<table border="0" width="100%">
	<tr>
		<td colspan="2" align="center">
			<input type="file" id="mmaker_import_file" name="mmaker_import_file" />
			<br>
			<br>
		</td>
	</tr>
	<tr>
		<td align="left">
			<input type="button" class="crmbutton cancel" value="&lt; {$APP.LBL_BACK}" title="{$APP.LBL_BACK}" onclick="ModuleMaker.gotoList()" />
		</td>
		<td align="right">
			<input type="submit" class="crmbutton save" value="{$APP.LBL_IMPORT}" title="{$APP.LBL_IMPORT}" onclick="" />
		</td>
</table>

</form>