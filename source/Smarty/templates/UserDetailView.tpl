{********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************}
{* crmv@128159 *}

<script language="JavaScript" type="text/javascript" src="{"include/js/menu.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/dtlviewajax.js"|resourcever}"></script>
<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<span id="crmspanid" style="display:none;position:absolute;" onmouseover="show('crmspanid');">
   <a class="edit" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>

{include file='Buttons_List1.tpl'}	{* crmv@20054 *}
{include file='Buttons_List_Detail.tpl'}	{* crmv@20054 *}

<!-- Shadow table -->
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
    <td class="showPanelBg" style="padding: 0x;" valign="top" width="100%"> <!-- crmv@30683 -->

    <div align=center>
		{if $CATEGORY eq 'Settings'}
			{include file='SetMenu.tpl'}
		{/if}
				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="padTab" align="left">
						<form name="DetailView" method="POST" action="index.php" ENCTYPE="multipart/form-data" id="form" style="margin:0px" onsubmit="VtigerJS_DialogBox.block();">
							<input type="hidden" name="module" value="Users" style="margin:0px">
							<input type="hidden" name="record" id="userid" value="{$ID}" style="margin:0px">
							<input type="hidden" name="isDuplicate" value=false style="margin:0px">
							<input type="hidden" name="action" style="margin:0px">
							<input type="hidden" name="changepassword" style="margin:0px">
							{if $CATEGORY neq 'Settings'}
								<input type="hidden" name="modechk" value="prefview" style="margin:0px">
							{/if}
							<input type="hidden" name="old_password" style="margin:0px">
							<input type="hidden" name="new_password" style="margin:0px">
							<input type="hidden" name="return_module" value="Users" style="margin:0px">
							<input type="hidden" name="return_action" value="ListView"  style="margin:0px">
							<input type="hidden" name="return_id" style="margin:0px">
							<input type="hidden" name="forumDisplay" style="margin:0px">
							<input type="hidden" name="hour_format" id="hour_format" value="{$HOUR_FORMAT}" style="margin:0px">
							{if $CATEGORY eq 'Settings'}
							<input type="hidden" name="parenttab" value="{$PARENTTAB}" style="margin:0px">
							{/if}
							<input type="hidden" id="hdtxt_IsAdmin" value="{php}global $current_user; (is_admin($current_user))?$v='1':$v='0'; echo $v;{/php}">	{* crmv@47567 *}	
							<table width="100%" border="0" cellpadding="0" cellspacing="0" >
							<tr>
								<td colspan=2>
									<!-- Heading and Icons -->
									<table width="100%" cellpadding="5" cellspacing="0" border="0" class="settingsSelUITopLine">
									<tr>
										<td width=50 rowspan="2"><img src="{$IMAGE_PATH}ico-users.gif" align="absmiddle"></td>	
										<td>
											{if $CATEGORY eq 'Settings'}
											<span class="heading2">
											<b>{$MOD.LBL_SETTINGS}  &gt; <a href="index.php?module=Administration&action=index&parenttab=Settings"> {$MOD.LBL_USERS} </a>&gt;"{$USERNAME}" </b></span> <!-- crmv@30683 -->
											{else}
											<span class="heading2">	
											<b>{$APP.LBL_MY_PREFERENCES}</b>
											</span>
											{/if}
											<span id="vtbusy_info" style="display:none;" valign="bottom">{include file="LoadingIndicator.tpl"}</span>					
										</td>
										
									</tr>
									<tr>
										<td>{$UMOD.LBL_USERDETAIL_INFO} "{$USERNAME}"</td>
									</tr>
									</table>
								</td>
							</tr>
							{if $ERROR_MSG neq ''}
							<tr>
								<td colspan="2" align=left>
									{$ERROR_MSG}
								</td>
							</tr>
							{/if}								
							<tr>
								<td colspan="2" align=left>
								<!-- User detail blocks -->
								<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
								<td align="left" valign="top">
									{* crmv@104568 *}
									{foreach name=blockforeach item=detail from=$BLOCKS}
									{assign var=header value=$detail.label}
									<br>
									<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
									<tr>
										{strip}
										 <td class="big">	
										<strong>{$smarty.foreach.blockforeach.iteration}. {$header}</strong>
										 </td>
										 <td class="small" align="right">&nbsp;</td>	
										{/strip}
									</tr>
									</table>
									{include file="DetailViewBlock.tpl" detail=$detail.fields}
									{/foreach}
									{* crmv@104568e *}
<!--  crmv@7222+7221 -->	
		{if $IS_ADMIN eq 'true' and $ID neq 1}								
		  <!-- Custom Access Module Display Table -->
		  {assign var=crmv_count value=$smarty.foreach.blockforeach.iteration+1}
		  <br>	
				<table border=0 cellspacing=0 cellpadding=0 width=100% class="tableHeading">
				<tr>
					<td class="big"><strong> {$crmv_count}. {$MOD.LBL_CUSTOM_ACCESS_PRIVILEGES_USER}</strong></td>
					<td class="small" align="right"><i class="vteicon md-link" title="{$APP.LBL_EXPAND_COLLAPSE}" onClick="ShowHidefn('custom_access');">arrow_downward</i></td>
				</tr>
				</table>
				<!-- Start of Module Display -->
		  <div style="float: none; display: {$SHOW_SHARING}; padding-top: 10px;" id="custom_access">	{* crmv@22657 *}
				{foreach key=modulename item=details from=$MODSHARING}
				{assign var="mod_display" value=$modulename|getTranslatedString:$modulename}
				{if $mod_display eq $APP.Accounts}
					{assign var="xx" value=$APP.Contacts}
					{assign var="mod_display" value=$mod_display|cat:" & $xx"}
				{/if}
				{if $details.0 neq ''}
				<table width="100%" border="0" cellpadding="5" cellspacing="0" class="listTableTopButtons">
                  		<tr>
		                    <td  style="padding-left:5px;" class="big"><i class="vteicon md-text nohover">arrow_forward</i>&nbsp; <b>{$mod_display}</b>&nbsp; </td>
                		    <td align="right">
					<input class="crmButton small save" type="button" name="Create" value="{$MOD.LBL_ADD_PRIVILEGES_BUTTON}" onClick="callEditDiv(this,'{$modulename}','create','')">
				    </td>
                  		</tr>
			  	</table>
				<table width="100%" cellpadding="5" cellspacing="0" class="table">
							<thead>
                    		<tr>
                    		<th width="7%" class="" nowrap>{$MOD.LBL_RULE_NO}</th>
                          	<th width="20%" class="" nowrap>{$mod_display} {$MOD.LBL_OF}</th>
                          	<th width="25%" class="" nowrap>{$MOD.LBL_CAN_BE_ACCESSED}</th>
                          	<th width="40%" class="" nowrap>{$MOD.LBL_PRIVILEGES}</th>
                          	<th width="8%" class="" nowrap>{$APP.Tools}</th>
                        	</tr>
                        	</thead>
                        <tr>
			  {foreach key=sno item=elements from=$details}
                          <td class="">{$sno+1}</td>
                          <td class="">{$elements.1}</td>
                          <td class="">{$elements.2}</td>
                          <td class="">{$elements.3}</td>
                          <td align="" class="">
				<a href="javascript:void(0);" onClick="callEditDiv(this,'{$modulename}','edit','{$elements.0}')"><i class="vteicon" title='edit'>create</i></a>
				&nbsp;
				<a href='javascript:confirmdelete("index.php?module=Users&action=DeleteSharingRule&shareid={$elements.0}&return_module=Users&record={$ID}&recalculate=true")'><i class="vteicon" title='del'>delete</i></td>
                        </tr>

                     {/foreach} 
                    </table>
	<!-- End of Module Display -->
	<!-- Start FOR NO DATA -->
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="tableHeading">	{* crmv@20054 *}
			<tr><td>&nbsp;</td></tr>
			</table>
		    {else}
                    <table width="100%" cellpadding="0" cellspacing="0" class="table"><tr><td>
		      <table width="100%" border="0" cellpadding="0" cellspacing="0" class="listTableTopButtons">	{* crmv@20054 *}
                      <tr>
                        <td style="padding-left:5px;" class="big"><i class="vteicon nohover md-text">arrow_forward</i>&nbsp; <b>{$mod_display}</b>&nbsp; </td>
                        <td align="right">
				<input class="crmButton small save" type="button" name="Create" value="{$APP.LBL_ADD_ITEM} {$MOD.LBL_PRIVILEGES}" onClick="callEditDiv(this,'{$modulename}','create','')">
			</td>
                      </tr>
			<table width="100%" cellpadding="0" cellspacing="0">	{* crmv@20054 *}
			<tr>
			<td colspan="2"  style="padding:20px ;" align="center" class="small">
			   {$MOD.LBL_CUSTOM_ACCESS_MESG} 
			   <a href="javascript:void(0);" onClick="callEditDiv(this,'{$modulename}','create','')">{$MOD.LNK_CLICK_HERE}</a>
			   {$MOD.LBL_CREATE_RULE_MESG}
			</td>
			</tr>
		    </table>
		    </table>	
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="tableHeading">	{* crmv@20054 *}
			<tr><td>&nbsp;</td></tr>
			</table>
		    {/if}
		    {/foreach}			
		   </td></tr></table>
		   </div>	

		  <!-- Custom Access Module Display Table -->
		  {assign var=crmv_count value=$crmv_count+1}
		  <br>	
				<table border=0 cellspacing=0 cellpadding=0 width=100% class="tableHeading">	{* crmv@20054 *}
				<tr>
					<td class="big"><strong> {$crmv_count}. {$MOD.LBL_CUSTOM_ADV_ACCESS_PRIVILEGES}</strong></td>
					<td class="small" align="right"><i class="vteicon md-link" title="{$APP.LBL_EXPAND_COLLAPSE}" onClick="ShowHidefn('adv_custom_access');">arrow_downward</i></td>
				</tr>
				</table>
				<!-- Start of Module Display -->
		  <div style="float: none; display: {$SHOW_ADV_SHARING}; padding-top: 10px;" id="adv_custom_access"> {* crmv@22657 *}
				{foreach key=modulename item=details from=$ADVSHARING}
				{assign var="mod_display" value=$modulename|getTranslatedString:$modulename}
				{* //crmv@13979 *}
				{* if $mod_display eq $APP.Accounts}
					{assign var="xx" value=$APP.Contacts}
					{assign var="mod_display" value=$mod_display|cat:" & $xx"}
				{/if*}
				{* //crmv@13979 end *}
				{if $details.0 neq ''}
				<table width="100%" border="0" cellpadding="5" cellspacing="0" class="listTableTopButtons">
                  	<tr>
	                    <td  style="padding-left:5px;" class="big"><i class="vteicon md-text nohover">arrow_forward</i>&nbsp; <b>{$mod_display}</b>&nbsp; </td>
                	    <td align="right">
							<input class="crmButton small save" type="button" name="Create" value="{$MOD.LBL_ADV_ADD_RULE_BUTTON}" onClick="callAdvEditDiv(this,'{$modulename}','create','')">
					    </td>
                  	</tr>
			  	</table>
				<table width="100%" cellpadding="5" cellspacing="0" class="table">
							<thead>
                    		<tr>
                    		<th width="7%" class="" nowrap>{$MOD.LBL_RULE_NO}</th>
                          	<th width="20%" class="" nowrap>{$MOD.ADV_RULE_TITLE}</th>
                          	<th width="25%" class="" nowrap>{$MOD.ADV_RULE_DESC}</th>
                          	<th width="40%" class="" nowrap>{$MOD.LBL_PRIVILEGES}</th>
                          	<th width="8%" class="" nowrap>{$APP.Tools}</th>
                        	</tr>
                        	</thead>
                        <tr >
			  {foreach key=sno item=elements from=$details}
                          <td class="">{$sno+1}</td>
                          <td class="">{$elements.1}</td>
                          <td class="">{$elements.2}</td>
                          <td class="">{$elements.3}</td>
                          <td align="" class="">
							<a href="javascript:void(0);" onClick="callAdvEditDiv(this,'{$modulename}','edit','{$elements.0}')"><i class="vteicon" title='edit'>create</i></a>
							&nbsp;
							<a href='javascript:confirmdelete("index.php?module=Settings&action=DeleteAdvSharingRulePerm&shareid={$elements.0}&return_module=Users&record={$ID}&recalculate=true")'><i class="vteicon" title='del'>delete</i></a></td>
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
                        <td style="padding-left:5px;" class="big"><i class="vteicon nohover md-text">arrow_forward</i>&nbsp; <b>{$mod_display}</b>&nbsp; </td>
                        <td align="right">
				<input class="crmButton small save" type="button" name="Create" value="{$MOD.LBL_ADV_ADD_RULE_BUTTON}" onClick="callAdvEditDiv(this,'{$modulename}','create','')">
			</td>
                      </tr>
			<table width="100%" cellpadding="5" cellspacing="0">
			<tr>
			<td colspan="2"  style="padding:20px ;" align="center" class="small">
			   {$MOD.LBL_CUSTOM_ACCESS_MESG} 
			   <a href="javascript:void(0);" onClick="callAdvEditDiv(this,'{$modulename}','create','')">{$MOD.LNK_CLICK_HERE}</a>
			   {$MOD.LBL_CREATE_RULE_MESG}
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
<!--  crmv@7222+7221e -->	
			{else}
				{assign var=crmv_count value=$smarty.foreach.blockforeach.iteration}
			{/if}							
									<!-- Home page components -->
		  {assign var=crmv_count value=$crmv_count+1}									
									<table class="tableHeading" border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										 <td class="big">	
										<strong>{$crmv_count}. {$UMOD.LBL_HOME_PAGE_COMP}</strong>
										 </td>
										 <td class="small" align="right"><i class="vteicon md-link" title="{$APP.LBL_EXPAND_COLLAPSE}" onClick="ShowHidefn('home_comp');">arrow_downward</i></td>	
									</tr>
									</table>
									
									<div style="float: none; display: none;" id="home_comp">	
									<table class="table borderless" border="0" cellpadding="5" cellspacing="0" width="100%">
									{foreach item=homeitems key=values from=$HOMEORDER}
										<tr><td class="" align="right" width="30%">{if $UMOD.$values eq ''}{$values|@getTranslatedString:'Home'}{else}{$UMOD.$values|@getTranslatedString:'Home'}{/if}</td> {* crmv@3079m *}
											{if $homeitems neq ''}
												<td align="center" width="5%">
													<i class="vteicon md-sm checkok nohover" title="{$UMOD.LBL_SHOWN}" >check</i>
												</td>
												<td align="left">{$UMOD.LBL_SHOWN}</td> 		
												{else}	
												<td align="center" width="5%">
													<i class="vteicon md-sm checkko nohover" title="{$UMOD.LBL_HIDDEN}">clear</i>
												</td>
												<td align="left">{$UMOD.LBL_HIDDEN}</td> 		
											{/if}	
										</tr>			
									{/foreach}
									</table>	
									</div>
								
									<br>
									<!-- Tag Cloud Display -->
		  {assign var=crmv_count value=$crmv_count+1}									
									<table class="tableHeading" border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td class="big">
										<strong>{$crmv_count}. {$UMOD.LBL_TAGCLOUD_DISPLAY}</strong>
										</td>
										<td class="small" align="right"><i class="vteicon md-link" title="{$APP.LBL_EXPAND_COLLAPSE}" onClick="ShowHidefn('tagcloud_disp');">arrow_downward</i></td>
									</tr>
									</table>
									<div style="float: none; display: none;" id="tagcloud_disp">
									<table border="0" cellpadding="5" cellspacing="0" width="100%">
										<tr><td class="dvtCellLabel" align="right" width="30%">{$UMOD.LBL_TAG_CLOUD}</td>
											{if $TAGCLOUDVIEW eq 'true'}
												<td align="center" width="5%">
													<i class="vteicon md-sm checkok nohover" title="{$UMOD.LBL_SHOWN}" >check</i>
												</td>
												<td align="left">{$UMOD.LBL_SHOWN}</td>
											{else}
												<td align="center" width="5%">
													<i class="vteicon md-sm checkko nohover" title="{$UMOD.LBL_HIDDEN}">clear</i>
												</td>
												<td align="left">{$UMOD.LBL_HIDDEN}</td>
											{/if}
										</tr>
									</table>
                                    </div>
									<br>
									<!-- My Groups -->
		  {assign var=crmv_count value=$crmv_count+1}									
									<table class="tableHeading" border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td class="big">	
										<strong>{$crmv_count}. {$UMOD.LBL_MY_GROUPS}</strong>
										 </td>
										 <td class="small" align="right">
										{if $GROUP_COUNT > 0}
										<i class="vteicon md-link" title="{$APP.LBL_EXPAND_COLLAPSE}" onClick="fetchGroups_js({$ID});">arrow_downward</i>
										{else}
											&nbsp;
										{/if}
										</td>	
									</tr>
									</table>
									
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr><td align="left"><div id="user_group_cont" style="display:none;  padding-top: 10px;"></div></td></tr> {* crmv@22657 *}	
									</table>	
									<br>
									<!-- Login History -->
		  {assign var=crmv_count value=$crmv_count+1}									
									{if $IS_ADMIN eq 'true'}
									<table class="tableHeading" border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
										 <td class="big">	
										<strong>{$crmv_count}. {$UMOD.LBL_LOGIN_HISTORY}</strong>
										 </td>
										 <td class="small" align="right"><i class="vteicon md-link" title="{$APP.LBL_EXPAND_COLLAPSE}" onClick="fetchlogin_js({$ID});">arrow_downward</i></td>	
										</tr>
									</table>

									<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr><td align="left"><div id="login_history_cont" style="display:none;"></div></td></tr>	
									</table>	
									<br>	
									{/if}
									
									{* crmv@29617 *}
									{assign var=crmv_count value=$crmv_count+1}
									<table class="tableHeading" border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
										 <td class="big">	
										<strong>{$crmv_count}. {'LBL_NOTIFICATION_MODULE_SETTINGS'|getTranslatedString:'ModNotifications'}</strong>
										 </td>
										 <td class="small" align="right"><i class="vteicon md-link" title="{$APP.LBL_EXPAND_COLLAPSE}" onClick="ModNotificationsCommon.displayDetailNotificationModuleSettings({$ID});">arrow_downward</i></td>	
										</tr>
									</table>
									<table class="table borderless" border="0" cellpadding="5" cellspacing="0" width="100%" id="notification_module_settings" style="display: none;">
									{assign var="NOTIFICATION_MODULE_SETTINGS" value=$ID|getNotificationsModuleSettings}
									{foreach item=FLAGS key=MODULE_NAME from=$NOTIFICATION_MODULE_SETTINGS}
										<tr><td class="dvtCellLabel" align="right" width="30%">{$MODULE_NAME|@getTranslatedString:$MODULE_NAME}</td>
									    	<td align="center" width="5%">
									    		{if $FLAGS.create eq 1}
											    	<i class="vteicon md-sm checkok nohover">check</i>
											    {else}
											    	<i class="vteicon md-sm checkko nohover">clear</i>
											    {/if}
											</td>
											<td align="left" width="20%" nowrap>{'LBL_CREATE_NOTIFICATION'|getTranslatedString:'ModNotifications'}</td>
									    	<td align="center" width="5%">
									    		{if $FLAGS.edit eq 1}
											    	<i class="vteicon md-sm checkok nohover">check</i>
											    {else}
											    	<i class="vteicon md-sm checkko nohover">clear</i>
											    {/if}
									    	</td>
									    	<td align="left" nowrap>{'LBL_EDIT_NOTIFICATION'|getTranslatedString:'ModNotifications'}</td>
										</tr>
									{/foreach}	
									</table>	
									<br>
									{* crmv@29617e *}
									
								</td>
								</tr>
								</table>
								<!-- User detail blocks ends -->
								
								</td>
							</tr>
							<tr>
								<td colspan=2 class="small"><div align="right"><a href="#top">{$MOD.LBL_SCROLL}</a></div></td>
							</tr>
							</table>
							
						</form>
			
					</td>
				</tr>
				</table>

		
	</div>
	</td>
	
</tr>
</table>
			
			</td>
			</tr>
			</table>
			
			</td>
			</tr>
			</table>
			



<br>
{$JAVASCRIPT}

<div id="tempdiv" style="display:block;position:absolute;left:350px;top:200px;"></div>

{assign var="FLOAT_TITLE" value=""} 
{assign var="FLOAT_WIDTH" value="400px"} 
{assign var="FLOAT_CONTENT" value=""} 
{include file="FloatingDiv.tpl" FLOAT_ID="tempdiv2" FLOAT_MAX_WIDTH="400px"}

{assign var="FLOAT_TITLE" value=""} 
{assign var="FLOAT_WIDTH" value="500px"} 
{assign var="FLOAT_CONTENT" value=""} 
{include file="FloatingDiv.tpl" FLOAT_ID="tempdiv3" FLOAT_MAX_WIDTH="500px"}

<!-- added for validation -->
<script language="javascript">
  var gVTModule = '{$smarty.request.module}';
  var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
  var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
  var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
  var fielduitype = new Array({$VALIDATION_DATA_FIELDUITYPE}); // crmv@83877
  var fieldwstype = new Array({$VALIDATION_DATA_FIELDWSTYPE}); //crmv@112297
function ShowHidefn(divid)
{ldelim}
	if($(divid).style.display != 'none')
		Effect.Fade(divid);
	else
		Effect.Appear(divid);
{rdelim}
{literal}
function fetchlogin_js(id)
{
	if($('login_history_cont').style.display != 'none')
		Effect.Fade('login_history_cont');
	else
		fetchLoginHistory(id);

}
function fetchLoginHistory(id)
{
        $("status").style.display="inline";
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Users&action=UsersAjax&file=ShowHistory&ajax=true&record='+id,
                        onComplete: function(response) {
                                $("status").style.display="none";
                                $("login_history_cont").innerHTML= response.responseText;
				Effect.Appear('login_history_cont');
                        }
                }
        );

}
function fetchGroups_js(id)
{
	if($('user_group_cont').style.display != 'none')
		Effect.Fade('user_group_cont');
	else
		fetchUserGroups(id);
}
function fetchUserGroups(id)
{
        $("status").style.display="inline";
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Users&action=UsersAjax&file=UserGroups&ajax=true&record='+id,
                        onComplete: function(response) {
                                $("status").style.display="none";
                                $("user_group_cont").innerHTML= response.responseText;
				Effect.Appear('user_group_cont');
                        }
                }
        );
}

function showAuditTrail()
{
	var userid =  document.getElementById('userid').value;
	openPopup('index.php?module=Settings&action=SettingsAjax&file=ShowAuditTrail&userid='+userid,'AuditTrail','','auto',false,false,'','nospinner');
}

function deleteUser(userid)
{
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'action=UsersAjax&file=UserDeleteStep1&return_action=ListView&return_module=Users&module=Users&parenttab=Settings&record='+userid,
			onComplete: function(response) {
				$("status").style.display="none";
				$("tempdiv").innerHTML= response.responseText;
			}
		}
	);
}
function transferUser(del_userid)
{
	$("status").style.display="inline";
	$("DeleteLay").style.display="none";
	var trans_userid=$('transfer_user_id').options[$('transfer_user_id').options.selectedIndex].value;
	window.document.location.href = 'index.php?module=Users&action=DeleteUser&ajax_delete=false&delete_user_id='+del_userid+'&transfer_user_id='+trans_userid;
}
{/literal}
</script>
<script>
function getListViewEntries_js(module,url)
{ldelim}
	$("status").style.display="inline";
        new Ajax.Request(
        	'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                	method: 'post',
                        postBody:"module="+module+"&action="+module+"Ajax&file=ShowHistory&record={$ID}&ajax=true&"+url,
			onComplete: function(response) {ldelim}
                        	$("status").style.display="none";
                                $("login_history_cont").innerHTML= response.responseText;
                  	{rdelim}
                {rdelim}
        );
{rdelim}
//crmv@7222
</script>

{literal}
<script type="text/javascript">

//crmv@104853
function callEditDiv(obj, modulename, mode, id) {
	var userid =  document.getElementById('userid').value;
	$("status").style.display = "inline";
	
	jQuery.ajax({
		url: 'index.php?module=Settings&action=SettingsAjax&orgajaxusr=true&mode='+mode+'&sharing_module='+modulename+'&shareid='+id+'&userid='+userid,
		method: 'POST',
		dataType: 'html',
		success: function(response) {
			$("status").style.display = "none";
			
			if (response.indexOf('FAILED') > -1) {
				var tmp = response.responseText.split('|##|');
				alert(tmp[1]);
			} else {
            	jQuery("#tempdiv2_div").html(response);
			
				showFloatingDiv('tempdiv2', null, {
					modal: true, 
					center: true, 
					removeOnMaskClick: true
				});
			
	            if (mode == 'edit') {
	                setTimeout("",10000);
	                var related = $('rel_module_lists').value;
	                fnwriteRules(modulename,related);
	            }
			}
		}
	});
}

function callAdvEditDiv(obj, modulename, mode, id) {
	var userid = document.getElementById('userid').value;
	$("status").style.display = "inline";
	
	jQuery.ajax({
		url: 'index.php?module=Settings&action=SettingsAjax&advprivilege=true&orgajaxadv=true&mode='+mode+'&sharing_module='+modulename+'&shareid='+id+'&userid='+userid,
		method: 'POST',
		dataType: 'html',
		success: function(response) {
			if (response == 'false') {
				alert(alert_arr.NO_RULES_FOUND);
				callAdvEditDivCreate(obj,modulename,mode,id);
			} else {
				$("status").style.display = "none";
            	jQuery("#tempdiv3_div").html(response);
            	showFloatingDiv('tempdiv3', null, {
					modal: true, 
					center: true, 
					removeOnMaskClick: true
				});
			}
		}
	});
}
//crmv@104853e

</script>
{/literal}

<script type="text/javascript">
//crmv@7221
//crmv@101183
function callAdvEditDivCreate(obj,modulename,mode,id)
{ldelim}
		var userid = jQuery('#userid').val();
        jQuery("#status").show();
        jQuery.ajax({ldelim}
                       type: 'POST',
					   dataType: 'html',
                        url: 'index.php?module=Settings&action=SettingsAjax&orgajaxadv=true&mode='+mode+'&sharing_module='+modulename+'&shareid='+id+'&userid='+userid,
                        success: function(data) {ldelim}
	                                jQuery("#status").hide();
	                                //crmv@2281m
	                        		if(data.indexOf('FAILED') > -1) {ldelim}
	                        			var tmp = data.split('|##|');
										alert(tmp[1]);
									{rdelim} else {ldelim}
		                                jQuery("#tempdiv3").html(data);
										fnvshobj(obj,"tempdiv3");										
		                                if(mode == 'edit')
		                                {ldelim}
											setTimeout("",10000);
											var related = jQuery('#rel_module_lists').val();
											fnwriteRules(modulename,related);
		                                {rdelim}
									{rdelim}
									//crmv@2281me
                        {rdelim}
                {rdelim}
        );
{rdelim}
//crmv@101183e


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
//crmv@7222e
</script>