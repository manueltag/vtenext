<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->
<script language="javascript" type="text/javascript" src="modules/Home/Homestuff.js"></script>

{if $ALLOW_CHARTS eq 'yes'}
	<script language="javascript" type="text/javascript" src="modules/Charts/Charts.js"></script> {* crmv@30014 *}
{/if}

<input id="homeLayout" type="hidden" value="{$LAYOUT}">
<!--Home Page Entries  -->

{include file="Home/HomeButtons.tpl"}
<div id="vtbusy_homeinfo" style="display:none;">
	{include file="LoadingIndicator.tpl"}
</div>

<!-- Main Contents Start Here -->
<table width="97%" class="small showPanelBg" cellpadding="0" cellspacing="0" border="0" align="center" valign="top">
<tr>
	<td width="100%" align="center" valign="top" height="350">
		<div id="MainMatrix" class="topMarginHomepage" style="padding:0px;width:100%"> {* crmv@30014 *}
			{foreach item=tablestuff from=$HOMEFRAME name="homeframe"}
				<!-- create divs for each widget - the contents will be loaded dynamically from javascript -->
				{include file="Home/MainHomeBlock.tpl"}
				<script>
					<!-- load contents for the widget-->
					{if $tablestuff.Stufftype eq 'Default' && $tablestuff.Stufftitle eq 'Home Page Dashboard'|@getTranslatedString:'Home'}
						fetch_homeDB({$tablestuff.Stuffid});
					{elseif $tablestuff.Stufftype eq 'DashBoard'}
						loadStuff({$tablestuff.Stuffid},'{$tablestuff.Stufftype}');
					{/if}
				</script>
			{/foreach}
		</div>
	</td>
</tr>
</table>

<!-- Main Contents Ends Here -->
<!-- crmv@fix homegate IE -->
<script>
var Vt_homePageWidgetInfoList = [
{foreach item=tablestuff key=index from=$HOMEFRAME_RESTRICTED name="homeframe"}
	{ldelim}
		'widgetId':{$tablestuff.Stuffid},
		'widgetType':'{$tablestuff.Stufftype}'
	{rdelim}
	{if $index+1 < $HOMEFRAME_RESTRICTED|@count},{/if}
{/foreach}
	];
loadAllWidgets(Vt_homePageWidgetInfoList, {$widgetBlockSize});
{literal}
initHomePage();
<!-- crmv@fix homegate IE  end -->

/**
 * this function is used to display the add window for different dashboard widgets
 */
function fnAddWindow(obj,CurrObj,offsetTop){//crmv@23264
	var tagName = document.getElementById(CurrObj);
	var left_Side = findPosX(obj);
	var top_Side = findPosY(obj);
	tagName.style.left= left_Side + 2 + 'px';
	//crmv@23264
	if (typeof offsetTop == 'undefined') {
		var offsetTop = 0;
	}
	top_Side = top_Side + 22 + offsetTop;
	tagName.style.top= top_Side + 'px';
	//crmv@23264e
	tagName.style.display = 'block';
	document.getElementById("addmodule").href="javascript:chooseType('Module');fnRemoveWindow();setFilter($('selmodule_id'))";
	document.getElementById("addURL").href="javascript:chooseType('URL');fnRemoveWindow();show('addWidgetsDiv');placeAtCenter($('addWidgetsDiv'));";
{/literal}
{if $ALLOW_RSS eq "yes"}
	document.getElementById("addrss").href="javascript:chooseType('RSS');fnRemoveWindow();show('addWidgetsDiv');placeAtCenter($('addWidgetsDiv'));";
{/if}
{if $ALLOW_DASH eq "yes"}
	document.getElementById("adddash").href="javascript:chooseType('DashBoard');fnRemoveWindow()";
{/if}
{* crmv@30014 *}
{if $ALLOW_CHARTS eq "yes"}
	document.getElementById("addchart").href="javascript:chooseType('Charts');fnRemoveWindow()";
{/if}
{* crmv@30014e *}
{literal}
}
{/literal}
</script>