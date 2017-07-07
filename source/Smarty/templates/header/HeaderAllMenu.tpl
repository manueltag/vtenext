{* crmv@126984 *}
<table class="table">
	<tr>
		<td width="50%" align="center" class="dvtUnSelectedCell" onclick="AllMenuObj.toggleMenu('allmenu_btn_areas','AllMenuArea','allmenu_btn_modules','OtherModuleList_sub')">
			<a href="javascript:;" id="allmenu_btn_areas">
				Area
			</a>
		</td>
		<td width="50%" align="center" class="dvtUnSelectedCell" onclick="AllMenuObj.toggleMenu('allmenu_btn_modules','OtherModuleList_sub','allmenu_btn_areas','AllMenuArea')">
			<a href="javascript:;" id="allmenu_btn_modules">
				Modules
			</a>
		</td>
	</tr>
</table>
<div id="AllMenuArea" style="display:none">
	{include file='modules/Area/Menu.tpl' UNIFIED_SEARCH_AREAS_CLASS=" "}
</div>
<div id="OtherModuleList_sub" style="display:none">
	{include file="header/HeaderAllModules.tpl"}
</div>

<script type="text/javascript" src="include/js/AllMenu.js"></script>
<script type="text/javascript">
	AllMenuObj.initialize();
</script>