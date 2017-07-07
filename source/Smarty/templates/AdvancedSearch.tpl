{********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************}

{* crmv@31245 crmv@10760 crmv@16312 crmv@3084m crmv@105588 *}

{* crmv@119414 *}
{assign var=overrides value=$THEME_CONFIG.tpl_overrides}
{if !empty($overrides[$smarty.template])}
	{include file=$overrides[$smarty.template]}
	{php}return;{/php}
{/if}
{* crmv@119414e *}

<div id="advSearch" class="menuSeparation" style="display:none;padding:10px;">
	<form name="advSearch" method="post" action="index.php" onSubmit="totalnoofrows();return callSearch('Advanced', '{$FOLDERID}');"> {* crmv@30967 *}
			{* header *}
			<table cellspacing=0 cellpadding="0" width=100% class="small" border="0">
	            <tr valign="middle">
					<td align="center">
						<span class="moduleName">{$APP.LNK_ADVANCED_SEARCH}</span>
						<i class="vteicon md-link pull-right" title="{'LBL_CLOSE'|getTranslatedString}" onclick="advancedSearchOpenClose();">clear</i>&nbsp;
					</td>
	            </tr>
	        </table>
	        
	        {* search fields *}
	        <table cellspacing="0" cellpadding="2" width="100%" align="center" class="small" border=0>
	            <tr>
	                <td align="center" class="small">
	                {* I had to remove the max-height:120px and overflow:auto in order to have the new picklists to show up... any idea on how to solve this? *}
	                <div id="fixed" style="width:95%" class="small">
	                    <table border=0 width=95%>
	                    <tr>
	                    <td align=left>
	                        <table width="100%"  border="0" cellpadding="5" cellspacing="0" id="adSrc" align="left">
	                        <tr>
	                            <td width="25%">
	                            	<div class="dvtCellInfo">
	                            		<select name="Fields0" id="Fields0" class="detailedViewTextBox" onchange="updatefOptions(this, 'Condition0')">{$FIELDNAMES}</select>
	                            	</div>
	                            </td>
	                            <td width="25%">
	                            	<div class="dvtCellInfo">
	                            		<select name="Condition0" id="Condition0" class="detailedViewTextBox">{$CRITERIA}</select>
	                            	</div>
	                            </td>
	                            <td width="40%">
	                            	<div class="dvtCellInfo">
	                            		<input type="text" name="Srch_value0" id="Srch_value0"  class="detailedViewTextBox">
	                            	</div>
	                            </td>
	                            <td width="10%"><div id="andFields0" name="and0" width="10%"><script>getcondition(false)</script></div></td>
	                        </tr>
	                        </table>
	                    </td>
	                    </tr>
	                </table>
	                </div>
	                </td>
	            </tr>
	        </table>
	        
	        {* action buttons *}
			<table cellspacing=0 cellpadding=5 width=100% class="small" align="center" border=0>
		        <tr>
					<td width="40%" nowrap>
						<input type="button" name="more" value=" {$APP.LBL_MORE} " onClick="fnAddSrch()" class="crmbutton small edit">
						<input type="button" name="button" value=" {$APP.LBL_FEWER_BUTTON} " onclick="delRow()" class="crmbutton small edit">
		            </td>
					<td width="20%" align="center">
						<input type="button" class="crmbutton small create" value="{$APP.LBL_SEARCH_NOW_BUTTON}" onClick="totalnoofrows();callSearch('Advanced', '{$FOLDERID}');"> {* crmv@30967 *}
						<input type="button" class="crmbutton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onClick="resetListSearch('Advanced','{$FOLDERID}');">
		            </td>
		            <td width="40%" align="left" style="padding-left:100px" nowrap>
						<div class="radio radio-primary">
							<label for="matchtype_all"><input id="matchtype_all" name="matchtype" type="radio" value="all" onclick="updatefOptionsAll(this.value);">{$APP.LBL_ADV_SEARCH_MSG_ALL}</label>
		            	</div>
		            	<div class="radio radio-primary">
							<label for="matchtype_any"><input id="matchtype_any" name="matchtype" type="radio" value="any" checked onclick="updatefOptionsAll(this.value);">{$APP.LBL_ADV_SEARCH_MSG_ANY}</label>
						</div>
		            </td>
		        </tr>
	    </table>
	    
	</form>
</div>