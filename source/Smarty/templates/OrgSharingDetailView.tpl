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

{* crmv@119414 *}
{assign var=overrides value=$THEME_CONFIG.tpl_overrides}
{if !empty($overrides[$smarty.template])}
	{include file=$overrides[$smarty.template]}
	{php}return;{/php}
{/if}
{* crmv@119414e *}

{* crmv@104853 *}

<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
{literal}
<style>
DIV.fixedLay{
	border:3px solid #CCCCCC;
	background-color:#FFFFFF;
	width:500px;
	position:fixed;
	left:250px;
	top:98px;
	display:block;
}
</style>
{/literal}
{literal}
<!--[if lte IE 6]>
<STYLE type=text/css>
DIV.fixedLay {
	POSITION: absolute;
}
</STYLE>
<![endif]-->

{/literal}

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@30683 -->
<tbody><tr>
        <td valign="top"></td>
        <td class="showPanelBg" style="padding: 5px;" valign="top" width="100%"> <!-- crmv@30683 -->


	<div align=center>
			{include file="SetMenu.tpl"}
			{include file='Buttons_List.tpl'} {* crmv@30683 *} 
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{'shareaccess.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" width="48" height="48" border=0 title="{$MOD.LBL_USERS}"></td>
					<td class=heading2 valign=bottom><b> {$MOD.LBL_SETTINGS} > {$MOD.LBL_SHARING_ACCESS} </b></td> <!-- crmv@30683 -->
					<td rowspan=2 class="small" align=right>&nbsp;</td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_SHARING_ACCESS_DESCRIPTION}</td>
				</tr>
				</table>

				<br>
				<div class='helpmessagebox' style='margin-bottom: 4px;'>
					<b style='color: red;'>{$APP.NOTE}</b> {$MOD.LBL_SHARING_ACCESS_HELPNOTE}
				</div>				
			  	<!-- GLOBAL ACCESS MODULE -->
		  		<div id="globaldiv">
				<form action="index.php" method="post" name="new" id="orgSharingform" onsubmit="VtigerJS_DialogBox.block();">
				<input type="hidden" name="module" value="Users">
				<input type="hidden" name="action" value="OrgSharingEditView">
				<input type="hidden" name="parenttab" value="Settings">
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<tr>
					<td class="big"><strong>1. {$CMOD.LBL_GLOBAL_ACCESS_PRIVILEGES}</strong></td>
					<td class="small" align=right>
						<input class="crmButton small cancel" title="{$CMOD.LBL_RECALCULATE_BUTTON}"  type="button" name="recalculate" value="{$CMOD.LBL_RECALCULATE_BUTTON}" onclick="return freezeBackground();">	
	&nbsp;<input class="crmButton small edit" type="submit" name="Edit" value="{$CMOD.LBL_CHANGE} {$CMOD.LBL_PRIVILEGES}" ></td>
					</td>
				</tr>
				</table>
				<table cellspacing="0" cellpadding="5" class="table" width="100%">
				{foreach item=module from=$DEFAULT_SHARING}	
				  {assign var="MODULELABEL" value=$module.0|@getTranslatedString:$module.0}
                  <tr>
                    <td width="20%" class="" nowrap>{$MODULELABEL}</td>
                    <td width="30%" class="" nowrap>
					{if $module.1 neq 'Private' && $module.1 neq 'Hide Details'}
						<i class="vteicon md-xsm md-text nohover checkok" >star</i>
					{else}
						<i class="vteicon md-xsm md-text nohover" >star</i>
					{/if}
						{$CMOD[$module.1]}
		    		</td>
                    <td width="50%" class="" nowrap>{$module.2}</td>
                  </tr>
		  		{/foreach}
		</form>	
              </table>
		</div>	
		  <!-- END OF GLOBAL -->
				<br><br>
		  <!-- Custom Access Module Display Table -->
		  <div id="customdiv">
			
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<tr>
					<td class="big"><strong>2. {$CMOD.LBL_CUSTOM_ACCESS_PRIVILEGES}</strong></td>
					<td class="small" align=right>&nbsp;</td>
				</tr>
				</table>
				<!-- Start of Module Display -->
				{foreach  key=modulename item=details from=$MODSHARING}
					{assign var="MODULELABEL" value=$modulename|@getTranslatedString:$modulename}
				{assign var="mod_display" value=$MODULELABEL}
				{if $mod_display eq $APP.Accounts}
					{assign var="xx" value=$APP.Contacts}
					{assign var="mod_display" value=$mod_display|cat:" & $xx"}
				{/if}
				{if $details.0 neq ''}
				<table width="100%" border="0" cellpadding="5" cellspacing="0" class="listTableTopButtons">
                  		<tr>
		                    <td  style="padding-left:5px;" class="big"><i class="vteicon nohover md-text">arrow_forward</i>&nbsp; <b>{$mod_display}</b>&nbsp; </td>
                		    <td align="right">
					<input class="crmButton small save" type="button" name="Create" value="{$CMOD.LBL_ADD_PRIVILEGES_BUTTON}" onClick="callEditDiv(this,'{$modulename}','create','')">
				    </td>
                  		</tr>
			  	</table>
				<table width="100%" class="table">
					<thead>
                 		<tr>
                   			<th width="7%" class="" nowrap>{$CMOD.LBL_RULE_NO}</th>
                         	<th width="20%" class="" nowrap>{$mod_display} {$CMOD.LBL_OF}</th>
                         	<th width="25%" class="" nowrap>{$CMOD.LBL_CAN_BE_ACCESSED}</th>
                         	<th width="40%" class="" nowrap>{$CMOD.LBL_PRIVILEGES}</th>
                         	<th width="8%" class="" nowrap>{$APP.Tools}</th>
                       	</tr>
                    </thead>
					<tr>
			  {foreach key=sno item=elements from=$details}
                          <td class="">{$sno+1}</td>
                          <td class="">{$elements.1}</td>
                          <td class="">{$elements.2}</td>
                          <td class="">{$elements.3}</td>
                          <td class="">
				<a href="javascript:void(0);" onClick="callEditDiv(this,'{$modulename}','edit','{$elements.0}')"><i class="vteicon" title='edit'>create</i></a> <a href='javascript:confirmdelete("index.php?module=Users&action=DeleteSharingRule&shareid={$elements.0}")'><i class="vteicon" title='del'>delete</i></a></td>
                        </tr>

                     {/foreach} 
                    </table>
	<!-- End of Module Display -->
	<!-- Start FOR NO DATA -->

			<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
			<tr><td>&nbsp;</td></tr>
			</table>
		    {else}
                    <table width="100%" cellpadding="0" cellspacing="0" class="table"><tr><td>
		      <table width="100%" border="0" cellpadding="5" cellspacing="0" class="listTableTopButtons">
                      <tr>
                        <td  style="padding-left:5px;" class="big"><i class="vteicon nohover md-text">arrow_forward</i>&nbsp; <b>{$mod_display}</b>&nbsp; </td>
                        <td align="right">
				<input class="crmButton small save" type="button" name="Create" value="{$APP.LBL_ADD_ITEM} {$CMOD.LBL_PRIVILEGES}" onClick="callEditDiv(this,'{$modulename}','create','')">
			</td>
                      </tr>
			<table width="100%" cellpadding="5" cellspacing="0">
			<tr>
			<td colspan="2"  style="padding:20px ;" align="center" class="small">
			   {$CMOD.LBL_CUSTOM_ACCESS_MESG} 
			   <a href="javascript:void(0);" onClick="callEditDiv(this,'{$modulename}','create','')">{$CMOD.LNK_CLICK_HERE}</a>
			   {$CMOD.LBL_CREATE_RULE_MESG}
			</td>
			</tr>
		    </table>
		    </table>	
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
			<tr><td>&nbsp;</td></tr>
			</table>
		    {/if}
		    {/foreach}			
		   </td></tr></table>
				<br>
		   </div>	
				<!-- Edit Button -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% >
				<tr><td class="small" ><div align=right><a href="#top">{$MOD.LBL_SCROLL}</a></div></td></tr>				</table>
		 </td>
                  </tr>
				
                </table>
			
			
			
			</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
		
	</div>
</td>
        <td valign="top"></td>
   </tr>
</tbody>
</table>

{assign var="FLOAT_TITLE" value=""}
{assign var="FLOAT_WIDTH" value="500px"}
{assign var="FLOAT_CONTENT" value=""}
{include file="FloatingDiv.tpl" FLOAT_ID="tempdiv" FLOAT_MAX_WIDTH="500px"}

<!-- For Disabling Window -->
<div id="confId"  class="veil_new small" style="display:none;">
<table class="options small" border="0" cellpadding="18" cellspacing="0">
	<tr>
		<td align="center" nowrap style="font-size:15px;">
			<b>{$CMOD.LBL_RECALC_MSG}</b>
		</td>
		<td align="center">
			<input type="button" class="crmbutton edit small" value="{$CMOD.LBL_YES}" onclick="return disableStyle('confId');">
			<input type="button" class="crmbutton edit small" value="&nbsp;{$CMOD.LBL_NO}&nbsp;" onclick="showSelect();$('confId').style.display='none';document.body.removeChild($('freeze'));">
		</td>
	</tr>
</table>
</div>

{literal}
<script type="text/javascript">
function callEditDiv(obj, modulename, mode, id) {
	$("status").style.display = "inline";
	
	jQuery.ajax({
		url: 'index.php?module=Settings&action=SettingsAjax&orgajax=true&mode='+mode+'&sharing_module='+modulename+'&shareid='+id,
		method: 'POST',
		dataType: 'html',
		success: function(response) {
			$("status").style.display = "none";
			jQuery("#tempdiv_div").html(response);
			
			showFloatingDiv('tempdiv', null, {
				modal: true, 
				center: true, 
				removeOnMaskClick: true
			});
				
			if (mode == 'edit') {
				setTimeout("", 10000);
				var related = $('rel_module_lists').value;
				fnwriteRules(modulename,related);
			}
		}
	});
}
</script>
{/literal}

<script>
function fnwriteRules(module,related)
{ldelim}
		var modulelists = new Array();
		modulelists = related.split('###');
		var relatedstring ='';
		var relatedtag;
		var relatedselect;
		var modulename;
		for(i=0;i < modulelists.length-1;i++)
		{ldelim}
			modulename = modulelists[i]+"_accessopt";
			relatedtag = document.getElementById(modulename);
			relatedselect = relatedtag.options[relatedtag.selectedIndex].text;
			relatedstring += modulelists[i]+':'+relatedselect+' ';
		{rdelim}	
		var tagName = document.getElementById(module+"_share");
		var tagName2 = document.getElementById(module+"_access");
		var tagName3 = document.getElementById('share_memberType');
		var soucre =  document.getElementById("rules");
		var soucre1 =  document.getElementById("relrules");
		var select1 = tagName.options[tagName.selectedIndex].text;
		var select2 = tagName2.options[tagName2.selectedIndex].text;
		var select3 = tagName3.options[tagName3.selectedIndex].text;

		if(module == '{$APP.Accounts}')
		{ldelim}
			module = '{$APP.Accounts} & {$APP.Contacts}';	
		{rdelim}

		soucre.innerHTML = module +" {$APP.LBL_LIST_OF} <b>\"" + select1 + "\"</b> {$CMOD.LBL_CAN_BE_ACCESSED} <b>\"" +select2 + "\"</b> {$CMOD.LBL_IN_PERMISSION} "+select3;
		soucre1.innerHTML = "<b>{$CMOD.LBL_RELATED_MODULE_RIGHTS}</b> " + relatedstring;
{rdelim}


		function confirmdelete(url)
		{ldelim}
			if(confirm("{$APP.ARE_YOU_SURE}"))
			{ldelim}
				document.location.href=url;
			{rdelim}
		{rdelim}
	
	function disableStyle(id)
	{ldelim}
			VtigerJS_DialogBox.progress();
			$('orgSharingform').action.value = 'RecalculateSharingRules';
			$('orgSharingform').submit();
 			$(id).style.display = 'none';
	{rdelim}

	function freezeBackground()
	{ldelim}
		// crmv@97217
	    document.getElementById('confId').style.display = 'block';
	    hideSelect();
	    // crmv@97217e
	{rdelim}

</script>
