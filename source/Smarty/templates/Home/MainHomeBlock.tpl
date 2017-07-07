
{* crmv@119414 *}
{assign var=overrides value=$THEME_CONFIG.tpl_overrides}
{if !empty($overrides[$smarty.template])}
	{include file=$overrides[$smarty.template]}
	{php}return;{/php}
{/if}
{* crmv@119414e *}

<!-- this file displays a widget div - the contents of the div are loaded later usnig javascript -->
{assign var="homepagedashboard_title" value='Home Page Dashboard'|@getTranslatedString:'Home'}
{assign var="keymetrics_title" value='Key Metrics'|@getTranslatedString:'Home'}
{* crmv@3079m *}
{if $tablestuff.Stufftype eq 'SDKIframe'}
	{assign var="stitle" value=$tablestuff.Stufftitle|getTranslatedString}
{else}
	{assign var="stitle" value=$tablestuff.Stufftitle}
{/if}
{* crmv@3079me *}
<script type="text/javascript">var vtdashboard_defaultDashbaordWidgetTitle = '{$homepagedashboard_title}';</script>
<div id="stuff_{$tablestuff.Stuffid}" class="{if $tablestuff.Stufftype eq 'URL'}MatrixLayerURL {else}MatrixLayer {if $tablestuff.Stufftitle eq $homepagedashboard_title}twoColumnWidget{/if}{/if}" style="float:left;overflow-x:hidden;{if $tablestuff.Stufftype eq 'Iframe' || $tablestuff.Stufftype eq 'SDKIframe'}overflow-y:hidden{else}overflow-y:auto;{/if};">	{* crmv@25314 crmv@25466 *}
	<table width="100%" cellpadding="0" cellspacing="0" class="small" style="padding-right:0px;padding-left:0px;padding-top:0px;">
		<tr id="headerrow_{$tablestuff.Stuffid}" class="dvInnerHeader headerrow">	{* crmv@61937 *}
			<td align="left" class="homePageMatrixHdr" style="height:30px;" nowrap width=60%><b>&nbsp;{$stitle}</b></td>
			<td align="right" class="homePageMatrixHdr" style="height:30px;" width=5%>
				<span id="refresh_{$tablestuff.Stuffid}" style="position:relative;">&nbsp;&nbsp;</span>
			</td>
			<td align="right" class="homePageMatrixHdr" style="height:30px;" width=35% nowrap>
{* crmv@31301 *}
{if $tablestuff.Stufftitle eq 'ModComments'|getTranslatedString:'ModComments'}
	{* crmv@82419 *}
	<div class="form-group basicSearch">
		<input id="modcomments_widg_search_text" class="form-control searchBox" type="text" value="{$APP.LBL_SEARCH_TITLE}{'ModComments'|getTranslatedString:'ModComments'}" onclick="clearTextModComments(this,'modcomments_widg_search')" onblur="restoreDefaultTextModComments(this, '{$APP.LBL_SEARCH_TITLE}{'ModComments'|getTranslatedString:'ModComments'}', 'modcomments_widg_search')" name="search_text" onkeypress="launchModCommentsSearch(event,'modcomments_widg_search');">
		<span class="cancelIcon">
			<i class="vteicon md-link md-sm" id="modcomments_widg_search_icn_canc" style="display:none" title="Reset" onclick="cancelSearchTextModComments('{$APP.LBL_SEARCH_TITLE}{'ModComments'|getTranslatedString:'ModComments'}','modcomments_widg_search','url_contents_{$tablestuff.Stuffid}','refresh_{$tablestuff.Stuffid}')">cancel</i>&nbsp;
		</span>
		<span class="searchIcon">
			<i id="modcomments_widg_search_icn_go" class="vteicon md-link" title="{$APP.LBL_FIND}" onclick="loadModCommentsNews(eval(jQuery('#url_contents_{$tablestuff.Stuffid}').contents().find('#max_number_of_news').val()),'url_contents_{$tablestuff.Stuffid}','refresh_{$tablestuff.Stuffid}',parent.jQuery('#modcomments_widg_search_text').val());" >search</i>
		</span>
	</div>
	{* crmv@82419e *}
{else}
{* crmv@31301e *}
	<!-- the edit button for widgets :: don't show for key metrics and dasboard widget -->
	{if ($tablestuff.Stufftype neq "Default" || $tablestuff.Stufftitle neq $keymetrics_title) && ($tablestuff.Stufftype neq "Default" || $tablestuff.Stufftitle neq $homepagedashboard_title) && ($tablestuff.Stufftype neq "Tag Cloud") && ($tablestuff.Stufftype neq "Iframe") && ($tablestuff.Stufftype neq "SDKIframe")}	{* crmv@25314 crmv@25466 *}
		<a id="editlink" style='cursor:pointer;' onclick="showEditrow({$tablestuff.Stuffid})">
			<i class="vteicon" title="{'LBL_EDIT'|@getTranslatedString}">tune</i>
		</a>
	{else}
		<i class="vteicon disabled" title="{'LBL_EDIT'|@getTranslatedString}">tune</i>
	{/if}
	<!-- code for edit button ends here -->
	
	<!-- code for refresh button -->
	{if $tablestuff.Stufftitle eq $homepagedashboard_title}
		<a style='cursor:pointer;' onclick="fetch_homeDB({$tablestuff.Stuffid});">
			<i class="vteicon" title="{'Refresh'|@getTranslatedString}">refresh</i>
		</a>
	{else}
		<a style='cursor:pointer;' onclick="loadStuff({$tablestuff.Stuffid},'{$tablestuff.Stufftype}');">
			<i class="vteicon" title="{'Refresh'|@getTranslatedString}">refresh</i>
		</a>
	{/if}
	<!-- code for refresh button ends here -->
	
	<!-- hide button :: show only for default widgets  -->
	{if $tablestuff.Stufftype eq "Default" || $tablestuff.Stufftype eq "Tag Cloud"}
		<a style='cursor:pointer;' onclick="HideDefault({$tablestuff.Stuffid})">
			<i class="vteicon" title="{'LBL_HIDE'|@getTranslatedString}">remove</i>
		</a>
	{else}
		<i class="vteicon disabled" title="{'LBL_HIDE'|@getTranslatedString}">remove</i>
	{/if}
	<!-- code for hide button ends here -->
	
	<!-- code for delete button :: dont show for default widgets -->
	{if $tablestuff.Stufftype neq "Default" && $tablestuff.Stufftype neq "Tag Cloud" && $tablestuff.Stufftype neq "Iframe" && ($tablestuff.Stufftype neq "SDKIframe")}	{* crmv@25314 crmv@25466 *}
		<a id="deletelink" style='cursor:pointer;' onclick="DelStuff({$tablestuff.Stuffid})">
			<i class="vteicon" title="{'LBL_HIDE'|@getTranslatedString}">clear</i>
		</a>
	{else}
		<i class="vteicon disabled" title="{'LBL_HIDE'|@getTranslatedString}">clear</i>
	{/if}
	<!-- code for delete button ends here -->
{/if}	{* crmv@31301 *}
			</td>
		</tr>
	</table>

	<div class="{if $tablestuff.Stufftype eq 'URL'}MatrixBorderURL{else}MatrixBorder{/if}" {if $tablestuff.Stufftitle eq 'MODCOMMENTS'|getTranslatedString:'Home'}style="height:650px;{if isMobile() eq true}overflow:scroll;-webkit-overflow-scrolling:touch;{/if}"{/if}> {* crmv@20052 *}	{* crmv@29079 *} {* crmv@30356 *}
		<table width="100%" cellpadding="0" cellspacing="0" class="small" style="padding-right:0px;padding-left:0px;padding-top:0px;">
	{if $tablestuff.Stufftype eq "Module"}
			<tr id="maincont_row_{$tablestuff.Stuffid}" class="show_tab winmarkModulesusr">
	{elseif $tablestuff.Stufftype eq "Default" && $tablestuff.Stufftitle neq $homepagedashboard_title}
			<tr id="maincont_row_{$tablestuff.Stuffid}" class="show_tab winmarkModulesdef">
	{elseif $tablestuff.Stufftype eq "RSS"}
			<tr id="maincont_row_{$tablestuff.Stuffid}" class="show_tab winmarkRSS">
	{elseif $tablestuff.Stufftype eq "DashBoard"}
			<tr id="maincont_row_{$tablestuff.Stuffid}" class="show_tab winmarkDashboardusr">
	{elseif $tablestuff.Stufftype eq "Default" && $tablestuff.Stufftitle eq $homepagedashboard_title}
			<tr id="maincont_row_{$tablestuff.Stuffid}" class="show_tab winmarkDashboarddef">
	{elseif $tablestuff.Stufftype eq "Tag Cloud"}
			<tr id="maincont_row_{$tablestuff.Stuffid}">
	{elseif $tablestuff.Stufftype eq "URL" || $tablestuff.Stufftype eq "Iframe" || $tablestuff.Stufftype eq "SDKIframe"}	{* crmv@25314 crmv@25466 *}
			<tr id="maincont_row_{$tablestuff.Stuffid}">
	{else}
			<tr id="maincont_row_{$tablestuff.Stuffid}" class="show_tab" align="center"> {* crmv@30014 *}
	{/if}
				<td colspan="2">
					<div id="stuffcont_{$tablestuff.Stuffid}" style="height:260px; overflow-y: auto; overflow-x:hidden;width:100%;height:100%;">
					</div>
				</td>
			</tr>
		</table>

		<table width="100%" cellpadding="0" cellspacing="5" class="small scrollLink">
		<tr>
		{if $tablestuff.Stufftype neq "URL" && $tablestuff.Stufftype neq "Charts"} {* crmv@30014 *}
			<td align="left">
				<a href="javascript:;" onclick="addScrollBar({$tablestuff.Stuffid});">
					{$MOD.LBL_SCROLL}
				</a>
			</td>
		{/if}
	{if $tablestuff.Stufftype eq "Module" || ($tablestuff.Stufftype eq "Default" &&  $tablestuff.Stufftitle neq "Key Metrics" && $tablestuff.Stufftitle neq $homepagedashboard_title && $tablestuff.Stufftitle neq "My Group Allocation" ) || $tablestuff.Stufftype eq "RSS" || $tablestuff.Stufftype eq "DashBoard"}
			<td align="right">
				<a href="#" id="a_{$tablestuff.Stuffid}">
					{$MOD.LBL_MORE}
				</a>
			</td>
	{/if}
		</tr>
		</table>
	</div>	{* crmv@20052e *}
</div>

<script language="javascript">
	<!-- position the div in the page -->
	{* crmv@30014 *}
	window.onresize = function(){ldelim}positionDivInAccord('stuff_{$tablestuff.Stuffid}','{$tablestuff.Stufftitle}','{$tablestuff.Stufftype}','{$tablestuff.Stuffsize}');{rdelim};
	positionDivInAccord('stuff_{$tablestuff.Stuffid}','{$tablestuff.Stufftitle}','{$tablestuff.Stufftype}','{$tablestuff.Stuffsize}');
	{* crmv@30014e *}
</script>
