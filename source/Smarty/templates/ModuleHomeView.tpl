{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@83340 crmv@98431 crmv@102334 crmv@104259 crmv@105193 *}

{* crmv@119414 *}
{assign var=overrides value=$THEME_CONFIG.tpl_overrides}
{if !empty($overrides[$smarty.template])}
	{include file=$overrides[$smarty.template]}
	{php}return;{/php}
{/if}
{* crmv@119414e *}

<script language="JavaScript" type="text/javascript" src="{"modules/`$MODULE`/`$MODULE`.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/dtlviewajax.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/ListView.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/SimpleListView.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/ModuleHome.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"modules/Charts/Charts.js"|resourcever}"></script>

{if $MODHOMEVIEWTYPE neq 'ListView'}
	<div id="modhome_loader" style="display:none;">
		{include file="LoadingIndicator.tpl"}
	</div>
	{include file='Buttons_List.tpl'}
{/if}

{if $CAN_ADD_HOME_BLOCKS}
	{assign var="FLOAT_TITLE" value=$APP.LBL_ADD_WIDGET}
	{assign var="FLOAT_WIDTH" value="400px"}
	{capture assign="FLOAT_CONTENT"}
	<input type="hidden" id="newblock_modhomeid" />
	<table width="100%" cellspacing="5" cellpadding="2" border="0">
		<tr>
			<td align="right" width="50%">
				<span>{$APP.LBL_CHOOSE_MODHOME_BLOCK_TYPE}</span>
			</td>
			<td align="left" width="50%">
				<select id="newblock_select" onchange="ModuleHome.loadNewBlockConfig()">
					<option value="">--{$APP.Select}--</option>
				{foreach item=block from=$HOME_BLOCK_TYPES}
					<option value="{$block.type}">{$block.label}</option>
				{/foreach}
				</select>
			</td>
		</tr>
	</table>
	<hr>
	<div id="newblock_config_div">
	</div>
	{/capture}
	{include file="FloatingDiv.tpl" FLOAT_ID="ChooseNewBlock" FLOAT_BUTTONS=""}
{/if}

{assign var="FLOAT_TITLE" value=$APP.NewModuleHomeView}
{assign var="FLOAT_WIDTH" value="400px"}
{capture assign="FLOAT_BUTTONS"}
{/capture}
{capture assign="FLOAT_CONTENT"}
<table border="0" cellspacing="2" cellpadding="3" width="100%" align="center">
	<tr>
		<td class="dvtCellLabel" align="right">
			{$APP.Name}
		</td>
		<td class="dvtCellInfo">
			<input type="text" name="homeviewname" id="homeviewname" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'">
		</td>
	</tr>
	<tr>
		<td colspan="2" align="right">
			<button type="button" class="crmbutton save" onclick="ModuleHome.createView()">{$APP.LBL_CREATE}</button>
		</td>
	</tr>
</table>
{/capture}
{include file="FloatingDiv.tpl" FLOAT_ID="ModHomeAddView"}

{capture assign="FLOAT_CONTENT"}
<table border="0" cellspacing="2" cellpadding="3" width="100%" align="center">
	<tr>
		<td class="dvtCellLabel" align="right">
			{$APP.Name}
		</td>
		<td class="dvtCellInfo">
			<input type="text" name="homeviewname3" id="homeviewname3" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'">
		</td>
	</tr>
	<tr>
		<td class="dvtCellLabel" align="right">
			{$APP.LBL_DEFAULT_FILTER}
		</td>
		<td class="dvtCellInfo">
			<select class="detailedViewTextBox" id="homecvid"></select>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="right">
			<button type="button" class="crmbutton save" onclick="ModuleHome.createListView()">{$APP.LBL_CREATE}</button>
		</td>
	</tr>
</table>
{/capture}
{include file="FloatingDiv.tpl" FLOAT_ID="ModHomeAddListView"}

{assign var="FLOAT_TITLE" value=$APP.AddModuleHomeViewReport}
{assign var="FLOAT_WIDTH" value="600px"}
{capture assign="FLOAT_BUTTONS"}
{/capture}
{capture assign="FLOAT_CONTENT"}
<table border="0" cellspacing="2" cellpadding="3" width="100%" align="center">
	<tr>
		<td class="dvtCellLabel" align="right">
			{$APP.Name}
		</td>
		<td class="dvtCellInfo">
			<input type="text" name="homeviewname2" id="homeviewname2" class="detailedViewTextBox" onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'">
		</td>
	</tr>
	<tr>
		<td class="dvtCellLabel" align="right">
			Report
		</td>
		<td class="dvtCellInfo">
			<input type="text" name="chooserReportName" id="chooserReportName" class="detailedViewTextBoxOff" readonly="" style="width:100%">
			<input type="hidden" id="chooserReportId" value="" />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div id="reportChooserFolder" style="display:none;width:100%;height:350px;overflow-y:auto"></div>
			<div id="reportChooserList" style="display:none;width:100%;height:350px;overflow-y:auto;display:none"></div>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="right">
			<button type="button" class="crmbutton save" onclick="ModuleHome.createReportView()">{$APP.LBL_CREATE}</button>
		</td>
	</tr>
</table>

{/capture}
{include file="FloatingDiv.tpl" FLOAT_ID="ModHomeAddViewReport"}

<div id="Buttons_List_HomeMod" class="level4Bg">
	<table id="bl3" border=0 cellspacing=0 cellpadding=2 width=100% class="small">
		<tr height="34">
			<td align="left" valign="middle">
				{* if count($MODHOMEVIEWS) > 1}
				<div class="pull-left">
					Configurazione:
					&nbsp;&nbsp;&nbsp;&nbsp;
				</div>
				<div class="pull-left">
					<select class="" id="modhomeSelect" style="max-width:200px" onchange="ModuleHome.changeView(jQuery(this).val())">
						{foreach item=VIEW from=$MODHOMEVIEWS}
							<option value="{$VIEW.modhomeid}" {if $MODHOMEID == $VIEW.modhomeid}selected=""{/if}>{$VIEW.name}</option>
						{/foreach}
					</select>
				</div>
				{/if *}

				<div class="pull-left" style="position:relative">
					{* HORRIBLE HORRIBLE CODE!! Please use CSS and proper classes, not this shit!! *}
					<table border="0" cellspacing="0" cellpadding="3" width="100%" style="position:relative;top:3px;height:30px">
					<tr>
					{foreach item=VIEW from=$MODHOMEVIEWS}
						{if $MODHOMEID == $VIEW.modhomeid}
							{assign var="_class" value="dvtSelectedCell"}
							{assign var="VIEWNAME" value=$VIEW.name}
						{else}
							{assign var="_class" value="dvtUnSelectedCell"}
						{/if}
						<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
						<td class="{$_class}" style="padding-right:5px" align="center" id="tdViewTab_{$VIEW.modhomeid}" nowrap="">
							<a href="javascript:void(0)" onclick="ModuleHome.changeView('{$VIEW.modhomeid}')">{$VIEW.name}</a>
							{if ($CAN_DELETE_HOME_VIEWS || $CAN_ADD_HOME_BLOCKS) && $_class == 'dvtSelectedCell'}
							&nbsp;
							<span id="pencil_{$VIEW.modhomeid}" style="width:20px;height:18px;display:inline-block;vertical-align:text-top">
								<i class="vteicon valign-bottom md-sm md-link" style="display:none" onclick="fnDropDown(this,'editModHomeBlocks_{$VIEW.modhomeid}',-8);">settings</i>
							</span>
							<div class="drop_mnu" id="editModHomeBlocks_{$VIEW.modhomeid}" style="display:none;" onmouseover="fnShowDrop('editModHomeBlocks_{$VIEW.modhomeid}');" onmouseout="fnHideDrop('editModHomeBlocks_{$VIEW.modhomeid}');">
								<ul class="widgetDropDownList">
									{if $CAN_DELETE_HOME_VIEWS}
									<!-- sanitize the name -->
									{assign var="VIEWNAME_SAFE" value='"'|str_replace:"&quot;":$VIEWNAME}
									{assign var="VIEWNAME_SAFE" value="'"|str_replace:"\'":$VIEWNAME_SAFE}
									<li><a href="javascript:void(0);" onclick="ModuleHome.removeView('{$VIEW.modhomeid}', '{$VIEWNAME_SAFE}', true)" class='drop_down'>{$APP.LBL_REMOVE_MODHOME_VIEW}</a></li>
									{/if}
									{if $CAN_ADD_HOME_BLOCKS && !$VIEW.reportid && !$VIEW.cvid}
									<li><a class="drop_down" href="javascript:void(0);" onclick="ModuleHome.chooseNewBlock('{$VIEW.modhomeid}')">{$APP.LBL_ADD_WIDGET}</a></li>
									{/if}
								</ul>
							</div>
							{/if}
							
						</td>
					{/foreach}
					</tr>
					</table>
				</div>

				{if $CAN_ADD_HOME_VIEWS}
				<div class="pull-left" id="add_home_views" style="padding-top:8px;padding-left:8px;display:none" onmouseover="fnDropDown(this,'editModHomeViews',-8);" onmouseout="fnHideDrop('editModHomeViews');">
					<a>
						<i class="vteicon md-link" style="vertical-align:middle" data-toggle="tooltip" data-placement="top" title="{$APP.LBL_ADD_ITEM} tab">add</i>
						<span style="cursor:pointer">{"LBL_ADD_TAB"|getTranslatedString:"Settings"}</span>
					</a>
					<div class="drop_mnu" id="editModHomeViews" style="display:none;" onmouseover="fnShowDrop('editModHomeViews');" onmouseout="fnHideDrop('editModHomeViews');">
						<ul class="widgetDropDownList">
							<li><a href="javascript:void(0);" onclick="ModuleHome.addView()" class='drop_down'>{$APP.AddModuleHomeView}</a></li>
							<li><a href="javascript:void(0);" onclick="ModuleHome.addListView()" class='drop_down'>{$APP.AddModuleHomeListView}</a></li>
							<li><a href="javascript:void(0);" onclick="ModuleHome.addReportView()" class='drop_down'>{$APP.AddModuleHomeViewReport}</a></li>
						</ul>
					</div>
				</div>
				{/if}

			</td>
		</tr>
	</table>
</div>
{* <script type="text/javascript">calculateButtonsList3();</script> *}


<input type="hidden" name="blockcolumns" id="blockcolumns" value="4" />

<div id="ModuleHomeMatrix" class="ModuleHomeMatrix">
	
	{foreach item=VIEW from=$MODHOMEVIEWS}
		{if $VIEW.modhomeid != $MODHOMEID}
			{php}continue{/php} {* please, update smarty to have the continue tag *}
		{/if}
		
		<input type="hidden" name="modhomeid" id="modhomeid" value="{$MODHOMEID}">
		
		{assign var="BLOCKIDS" value=$VIEW.blockids}
	
		{if count($VIEW.blocks) > 0}
		
			<div id="MainMatrix" class="topMarginHomepage" style="padding:0px;width:99%"> {* crmv@30014 crmv@97209 *}
			{foreach item=BLOCK from=$VIEW.blocks}
				{include file="ModuleHome/Block.tpl"}
			{/foreach}
			</div>
		
		{elseif $VIEW.reportid > 0}
			{assign var="REPORTID" value=$VIEW.reportid}
			{* WOW, HOW BAAD! VERY TRICKY, MUCH UGLY CODE! *}
			{php}
				global $mod_strings, $app_strings, $current_language, $currentModule;
				$oldCurrentMod = $currentModule;
				$oldModStrings = $mod_strings;
				$_REQUEST['record'] = $this->_tpl_vars['REPORTID'];
				$_REQUEST['tab'] = '';
				$_REQUEST['embedded'] = '1';
				$currentModule = 'Reports';
				$mod_strings = return_module_language($current_language, $currentModule);
				require('modules/Reports/SaveAndRun.php');
				$mod_strings = $oldModStrings;
				$currentModule = $oldCurrentMod;
			{/php}
			
		{elseif $VIEW.cvid > 0}
			{include file=$LISTVIEWTPL}

		{else}
			<div style="text-align:center;padding:20px">
			<p>{$LBL_NO_HOME_BLOCKS}</p>
			</div>
		{/if}
	
	{/foreach}
	
</div>

<script type="text/javascript">
	ModuleHome.initialize('MainMatrix');
	
	{if $EDITMODE}
	ModuleHome.enterEditMode();
	{/if}
	
	(function() {ldelim}
		var blocks = {$BLOCKIDS|@json_encode};
		ModuleHome.loadBlocks('{$MODHOMEID}', blocks);
	{rdelim})();
</script>
