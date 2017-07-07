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
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>

<script type="text/javascript" src="include/js/reflection.js"></script>
<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/dtlviewajax.js"|resourcever}"></script>
<span id="crmspanid" style="display:none;position:absolute;" onmouseover="show('crmspanid');">
   <a class="edit" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>

<div id="convertleaddiv" style="display:block;position:absolute;left:225px;top:150px;"></div>
<script>
var gVTModule = '{$smarty.request.module|@vtlib_purify}';
{literal}
function callConvertLeadDiv(id)
{
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Leads&action=LeadsAjax&file=ConvertLead&record='+id,
                        onComplete: function(response) {
                                $("convertleaddiv").innerHTML=response.responseText;
				eval($("conv_leadcal").innerHTML);
                        }
                }
        );
}
<!-- End Of Code modified by SAKTI on 10th Apr, 2008 -->

<!-- Start of code added by SAKTI on 16th Jun, 2008 -->
function setCoOrdinate(elemId){
	oBtnObj = document.getElementById(elemId);
	var tagName = document.getElementById('lstRecordLayout');
	leftpos  = 0;
	toppos = 0;
	aTag = oBtnObj;
	do{					  
	  leftpos  += aTag.offsetLeft;
	  toppos += aTag.offsetTop;
	} while(aTag = aTag.offsetParent);
	
	tagName.style.top= toppos + 20 + 'px';
	tagName.style.left= leftpos - 276 + 'px';
}

function getListOfRecords(obj, sModule, iId,sParentTab)
{
		new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Users&action=getListOfRecords&ajax=true&CurModule='+sModule+'&CurRecordId='+iId+'&CurParentTab='+sParentTab,
			onComplete: function(response) {
				sResponse = response.responseText;
				if (sModule == 'Accounts')
					HideHierarch();
				$("lstRecordLayout").innerHTML = sResponse;
				Lay = 'lstRecordLayout';	
				var tagName = document.getElementById(Lay);
				var leftSide = findPosX(obj);
				var topSide = findPosY(obj);
				var maxW = tagName.style.width;
				var widthM = maxW.substring(0,maxW.length-2);
				var getVal = parseInt(leftSide) + parseInt(widthM);
				if(getVal  > document.body.clientWidth ){
					leftSide = parseInt(leftSide) - parseInt(widthM);
					tagName.style.left = leftSide + 230 + 'px';
					tagName.style.top = topSide + 20 + 'px';
				}else{
					tagName.style.left = leftSide + 230 + 'px';
				}
				setCoOrdinate(obj.id);
				
				tagName.style.display = 'block';
				tagName.style.visibility = "visible";
			}
		}
	);
}
{/literal}
function tagvalidate()
{ldelim}
	if(trim(document.getElementById('txtbox_tagfields').value) != '')
		SaveTag('txtbox_tagfields','{$ID}','{$MODULE}');	
	else
	{ldelim}
		alert("{$APP.PLEASE_ENTER_TAG}");
		return false;
	{rdelim}
{rdelim}
function DeleteTag(id,recordid)
{ldelim}
	$("vtbusy_info").style.display="inline";
	Effect.Fade('tag_'+id);
	new Ajax.Request(
		'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                        method: 'post',
                        postBody: "file=TagCloud&module={$MODULE}&action={$MODULE}Ajax&ajxaction=DELETETAG&recordid="+recordid+"&tagid=" +id,
                        onComplete: function(response) {ldelim}
						getTagCloud();
						$("vtbusy_info").style.display="none";
                        {rdelim}
                {rdelim}
        );
{rdelim}

//Added to send a file, in Documents module, as an attachment in an email
function sendfile_email()
{ldelim}
	filename = $('dldfilename').value;
	OpenCompose(filename,'Documents');
{rdelim}

</script>

<div id="lstRecordLayout" class="layerPopup crmvDiv" style="display:none;width:320px;height:300px;z-index:21;position:fixed;"></div>	{*<!-- crmv@18592 -->*}

{if $MODULE eq 'Accounts' || $MODULE eq 'Contacts' || $MODULE eq 'Leads'}
        {if $MODULE eq 'Accounts'}
                {assign var=address1 value='$MOD.LBL_BILLING_ADDRESS'}
                {assign var=address2 value='$MOD.LBL_SHIPPING_ADDRESS'}
        {/if}
        {if $MODULE eq 'Contacts'}
                {assign var=address1 value='$MOD.LBL_PRIMARY_ADDRESS'}
                {assign var=address2 value='$MOD.LBL_ALTERNATE_ADDRESS'}
        {/if}
        <div id="locateMap" onMouseOut="fninvsh('locateMap')" onMouseOver="fnvshNrm('locateMap')">
                <table bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" width="100%">
                        <tr>
							<td nowrap>
								{if $MODULE eq 'Accounts'}
									<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Main' );" class="calMnu">{$MOD.LBL_BILLING_ADDRESS}</a>
									<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Other' );" class="calMnu">{$MOD.LBL_SHIPPING_ADDRESS}</a>
                               	{/if}
								{if $MODULE eq 'Contacts'}
									<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Main' );" class="calMnu">{$MOD.LBL_PRIMARY_ADDRESS}</a>
									<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Other' );" class="calMnu">{$MOD.LBL_ALTERNATE_ADDRESS}</a>
                               {/if}
							</td>
                        </tr>
                </table>
        </div>
{/if}


<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
	<td>

		{include file='Buttons_List1.tpl'}
		
<!-- Contents -->
{*<!-- crmv@18592 -->*}
<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
<tr>
	<td valign=top></td>
	<td class="showPanelBg" valign=top width=100%>
		<!-- PUBLIC CONTENTS STARTS-->
		<div class="small" style="padding:0px" >
		{include file='Buttons_List_Detail.tpl'}
		<!-- Account details tabs -->
		<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
		<tr>
			<td>
				<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
				<tr>
					<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
					<td class="dvtSelectedCell" align=center nowrap>{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</td>
					{* crmv@22700 *}
					{php}if (isModuleInstalled('Newsletter')) { {/php}
						{if $MODULE eq 'Campaigns'}
							<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
							<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action=Statistics&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{'LBL_STATISTICS'|@getTranslatedString:'Newsletter'}</a></td>
						{/if}
					{php}}{/php}
					{* crmv@22700e *}
					<td class="dvtTabCache" style="width:10px">&nbsp;</td>
					{if $SinglePane_View eq 'false' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
					<td class="dvtUnSelectedCell" onmouseout="fnHideDrop('More_Information_Modules_List');" onmouseover="fnDropDown(this,'More_Information_Modules_List',-10);" align="center" nowrap>{* crmv@22259 *}{* crmv@22622 *}
						<a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a>
						<div onmouseover="fnShowDrop('More_Information_Modules_List')" onmouseout="fnHideDrop('More_Information_Modules_List')"
									 id="More_Information_Modules_List" class="drop_mnu" style="left: 502px; top: 76px; display: none;">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
							{foreach key=_RELATION_ID item=_RELATED_MODULE from=$IS_REL_LIST}
								<tr><td><a class="drop_down" href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}&selected_header={$_RELATED_MODULE}&relation_id={$_RELATION_ID}">{$_RELATED_MODULE|@getTranslatedString:$_RELATED_MODULE}</a></td></tr>
							{/foreach}
							</table>
						</div>
					</td>
					{/if}
					<td class="dvtTabCache" align="right" style="width:100%"></td>
{*<!-- crmv@18592e -->*}
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td valign=top align=left >                
				 <table border=0 cellspacing=0 cellpadding=3 width=100% class="dvtContentSpace" style="border-bottom:0;">
				<tr>

					<td align=left valign="top"> {* crmv@20260 *}
					<!-- content cache -->
										
					
				<table border=0 cellspacing=0 cellpadding=0 width=100%>
                <tr>
					<td style="padding:5px">
					<!-- Command Buttons -->
				  	<table border=0 cellspacing=0 cellpadding=0 width=100%>
							 <!-- NOTE: We should avoid form-inside-form condition, which could happen when
								Singlepane view is enabled. -->
							 <form action="index.php" method="post" name="DetailView" id="form" name="form1">
							{include file='DetailViewHidden.tpl'}
						
							  <!-- Start of File Include by SAKTI on 10th Apr, 2008 -->
							 {include_php file="./include/DetailViewBlockStatus.php"}
							 <!-- Start of File Include by SAKTI on 10th Apr, 2008 -->


{assign var=doextrablock value=0}

							{foreach key=header item=detail from=$BLOCKS}

{if $doextrablock neq 0 || $header neq 'Informazioni ospedale'} 


							<!-- Detailed View Code starts here-->
							<table border=0 cellspacing=0 cellpadding=0 width=100% class="small" name="block_{$header}">
							<tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                             <td align=right>
							{if $header eq $MOD.LBL_ADDRESS_INFORMATION && ($MODULE eq 'Accounts' || $MODULE eq 'Contacts' || $MODULE eq 'Leads') }
                             {if $MODULE eq 'Leads'}
                             <input name="mapbutton" value="{$APP.LBL_LOCATE_MAP}" class="crmbutton small create" type="button" onClick="searchMapLocation( 'Main' )" title="{$APP.LBL_LOCATE_MAP}">
                             {else}
                             <input name="mapbutton" value="{$APP.LBL_LOCATE_MAP}" class="crmbutton small create" type="button" onClick="fnvshobj(this,'locateMap');" onMouseOut="fninvsh('locateMap');" title="{$APP.LBL_LOCATE_MAP}">
							{/if}
                             {/if}
                             </td>
                             </tr>

							<!-- This is added to display the existing comments -->
							{if $header eq $MOD.LBL_COMMENTS || $header eq $MOD.LBL_COMMENT_INFORMATION}
							   <tr>
								<td colspan=4 class="dvInnerHeader">
						        	<b>{$MOD.LBL_COMMENT_INFORMATION}</b>
								</td>
							   </tr>
							   <tr>
							   			<td colspan=4>{$COMMENT_BLOCK}</td>
							   </tr>
							   <tr><td>&nbsp;</td></tr>
							{/if}


	{if $header neq 'Comments'}
 
						     <tr>{strip}
						     <td colspan=4 class="dvInnerHeader">
							
							<div style="float:left;font-weight:bold;"><div style="float:left;"><a href="javascript:showHideStatus('tbl{$header|replace:' ':''}','aid{$header|replace:' ':''}','{$IMAGE_PATH}');">
							{if $BLOCKINITIALSTATUS[$header] eq 1}
								<img id="aid{$header|replace:' ':''}" src="{'activate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;" alt="Hide" title="Hide"/>
							{else}
							<img id="aid{$header|replace:' ':''}" src="{'inactivate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;" alt="Display" title="Display"/>
							{/if}
								</a></div><b>&nbsp;
						        	{$header}
	  			     			</b></div>
						     </td>{/strip}
					             </tr>
{/if}
					</table>
{if $header neq 'Comments'}
	{if $BLOCKINITIALSTATUS[$header] eq 1}
	<div style="width:auto;display:block;" id="tbl{$header|replace:' ':''}" >
	{else}
	<div style="width:auto;display:none;" id="tbl{$header|replace:' ':''}" >
	{/if}
		<table border=0 cellspacing=0 cellpadding=0 width="100%" class="small">
	       {assign var="fieldcount" value=0}
	       {assign var="fieldstart" value=1}
	   		{assign var="tr_state" value=0}  							
	   		{foreach item=detail from=$detail}
			{foreach key=label item=data from=$detail}
			   {assign var=keyid value=$data.ui}
			   {assign var=keyval value=$data.value}
			   {assign var=keytblname value=$data.tablename}
			   {assign var=keyfldname value=$data.fldname}
			   {assign var=keyfldid value=$data.fldid}
			   {assign var=keyoptions value=$data.options}
			   {assign var=keysecid value=$data.secid}
			   {assign var=keyseclink value=$data.link}
			   {assign var=keycursymb value=$data.cursymb}
			   {assign var=keysalut value=$data.salut}
			   {assign var=keyaccess value=$data.notaccess}
			   {assign var=keycntimage value=$data.cntimage}
			   {assign var=keyadmin value=$data.isadmin}
			   {assign var=keyadmin value=$data.isadmin}
			   {assign var=keyreadonly value=$data.readonly}							   
			   {assign var=display_type value=$data.displaytype}

{* check type *}
{if $keyfldname eq 'industry'}
	{assign var=keyreadonly value=99}
	{if $keyval eq 'Hospitality'}
		{assign var=doextrablock value=1}
	{/if}
{/if}



			   	{if ($fieldcount eq 0 or $fieldstart eq 1) and $tr_state neq 1}	
			  		{if $fieldstart eq 1}
						{assign var="fieldstart" value=0}
					{/if}						
			   		<tr style="height:25px">
			   		{assign var="tr_state" value=1}
				{/if}	
					{if ($keyreadonly eq 99 or $EDIT_PERMISSION neq 'yes' or $display_type eq '2' or empty($DETAILVIEW_AJAX_EDIT) )}
						{if ($keyid eq 19 or $keyid eq 20) and $fieldcount neq 0}
							</tr>
							<tr style="height:25px">
							{assign var="tr_state" value=1}
							{assign var="fieldcount" value=0}
						{/if}						
		                {if $keycntimage ne ''}
							<td class="dvtCellLabel" align=right width=25%><input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>{$keycntimage}</td>
						{elseif $keyid eq '71' || $keyid eq '72'}<!-- Currency symbol -->
							<td class="dvtCellLabel" align=right width=25%>{$label}<input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input> ({$keycursymb})</td>
						{else}
							<td class="dvtCellLabel" align=right width=25%><input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>{$label}</td>
						{/if}					
						{assign var="fieldcount" value=$fieldcount+1}
						{include file="DetailViewFields.tpl"}
						<!-- crmv@16834 -->
						{if $keyid eq 19 or $keyid eq 20}
							{assign var="fieldcount" value=$fieldcount+1}
						{/if}
						<!-- crmv16834e -->
					{elseif $keyreadonly eq 100}	<!-- crmv@17935 -->
					{else}		
						{if ($keyid eq 19 or $keyid eq 20) and $fieldcount neq 0}
							</tr>
							<tr style="height:25px">
							{assign var="tr_state" value=1}
							{assign var="fieldcount" value=0}
						{/if}	
						{assign var="fieldcount" value=$fieldcount+1}
	                	{if $keycntimage ne ''}
							<td class="dvtCellLabel" align=right width=25%><input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>{$keycntimage}</td>
						{elseif $keyid eq '71' || $keyid eq '72'}<!-- Currency symbol -->
							<td class="dvtCellLabel" align=right width=25%>{$label}<input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input> ({$keycursymb})</td>
						{else}
							<td class="dvtCellLabel" align=right width=25%><input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>{$label}</td>
						{/if}		
										
						{include file="DetailViewUI.tpl"}
						{if $keyid eq 19 or $keyid eq 20}
							{assign var="fieldcount" value=$fieldcount+1}
						{/if}
					{/if}
			    {if $fieldcount eq 2}
					</tr>
					{assign var="fieldcount" value=0}	
					{assign var="tr_state" value=0}	
				{/if}
               {/foreach}
	   			{/foreach}	
	     </table>
	 </div>
{/if}
</td>
</tr>
	<tr>
		<td style="padding:5px">
		
{/if} {* extra block *}
			{/foreach}
{*-- End of Blocks--*}	
			{* vtlib Customization: Embed DetailViewWidget block:// type if any *}
			{if $CUSTOM_LINKS && !empty($CUSTOM_LINKS.DETAILVIEWWIDGET)}
			{foreach item=CUSTOM_LINK_DETAILVIEWWIDGET from=$CUSTOM_LINKS.DETAILVIEWWIDGET}
				{if preg_match("/^block:\/\/.*/", $CUSTOM_LINK_DETAILVIEWWIDGET->linkurl)}
				<!-- crmv@18485 -->
				{php}
					$widgetLinkInfo_tmp = $this->_tpl_vars['CUSTOM_LINK_DETAILVIEWWIDGET'];
					if (preg_match("/^block:\/\/(.*)/", $widgetLinkInfo_tmp->linkurl, $matches)) {
						list($widgetControllerClass_tmp, $widgetControllerClassFile_tmp) = explode(':', $matches[1]);
						if (vtlib_isModuleActive($widgetControllerClass_tmp)) {
				{/php}
				<!-- crmv@18485e -->
					<tr>
						<td style="padding:5px;" >
						{php}
							echo vtlib_process_widget($this->_tpl_vars['CUSTOM_LINK_DETAILVIEWWIDGET'], $this->_tpl_vars);
						{/php}
						</td>
					</tr>
				<!-- crmv@18485 -->				
				{php}}}{/php}
				<!-- crmv@18485e -->
				{/if}
			{/foreach}
			{/if}
			{* END *}                    		   
		</td>
</tr>
		<!-- Inventory - Product Details informations -->
		<tr>
			<td >
			{$ASSOCIATED_PRODUCTS}
			</td>
		</tr>
		</td>
		</tr>
			
			</form>	
			<!-- End the form related to detail view -->			

			{if $SinglePane_View eq 'true' && $IS_REL_LIST|@count > 0}
				{include file= 'RelatedListNew.tpl'}
			{/if}
		</table>
		
		</td>
		<td width=22% valign=top style="border-left:1px dashed #ffffff;padding:13px">
		<!-- vtc -->
		{if $MODULE eq 'Accounts'}
			<input title="{$MOD.HideHierarchy}" class="crmbutton small create" onclick="showHideHierarch('ShowHierarch',{$ID});" type="button" id="ShowHierarch" name="ShowHierarch" value="{$MOD.HideHierarchy}">&nbsp;
			<div id="accountHierarc" style="width: 100%; display: none; position: relative; z-index: 10;" class="layerPopup">
				<table border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr style="cursor:move;">
					<td colspan="2" class="mailClientBg small" id="Track_Handle"><strong>{$MOD.AccountsHierarchy}</strong></td>
					<td align="right" style="padding:5px;" class="mailClientBg small">
					<a href="javascript:;"><img src="{$IMAGE_PATH}close.gif" border="0"  onClick="showHideHierarch('ShowHierarch',{$ID});" hspace="5" align="absmiddle"></a>
					</td></tr>
				</table>
				<table border="0" cellpadding="5" cellspacing="0" width="100%" class="hdrNameBg">
				<tr><td>
					<div id="accountHierarcContent">
					</div>
				</td></tr>		
				</table>
			</div>
		{/if}
		<!-- vtc e -->				  
			<!-- right side relevant info -->
			<!-- Action links for Event & Todo START-by Minnie -->
			{if $MODULE eq 'Potentials' || $MODULE eq 'HelpDesk' || $MODULE eq 'Contacts' || $MODULE eq 'Accounts' || $MODULE eq 'Leads' || ($MODULE eq 'Documents' && ($ADMIN eq 'yes' || $FILE_STATUS eq '1') && $FILE_EXIST eq 'yes')}
  			<table width="100%" border="0" cellpadding="5" cellspacing="0">
								
				{if $MODULE eq 'HelpDesk'}
					{if $CONVERTASFAQ eq 'permitted'}
				<tr><td align="left" class="genHeaderSmall">{$APP.LBL_ACTIONS}</td></tr>				
				<tr>
					<td align="left" style="padding-left:10px;"> 
						<a class="webMnu" href="index.php?return_module={$MODULE}&return_action=DetailView&record={$ID}&return_id={$ID}&module={$MODULE}&action=ConvertAsFAQ"><img src="{'convert.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>
						<a class="webMnu" href="index.php?return_module={$MODULE}&return_action=DetailView&record={$ID}&return_id={$ID}&module={$MODULE}&action=ConvertAsFAQ">{$MOD.LBL_CONVERT_AS_FAQ_BUTTON_LABEL}</a>
					</td>
				</tr>
					{/if}
				{elseif $MODULE eq 'Potentials'}
						{if $CONVERTINVOICE eq 'permitted'}
				<tr><td align="left" class="genHeaderSmall">{$APP.LBL_ACTIONS}</td></tr>				
				<tr>
					<td align="left" style="padding-left:10px;"> 
						<a class="webMnu" href="index.php?return_module={$MODULE}&return_action=DetailView&return_id={$ID}&convertmode={$CONVERTMODE}&module=Invoice&action=EditView&account_id={$ACCOUNTID}"><img src="{'actionGenerateInvoice.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>
						<a class="webMnu" href="index.php?return_module={$MODULE}&return_action=DetailView&return_id={$ID}&convertmode={$CONVERTMODE}&module=Invoice&action=EditView&account_id={$ACCOUNTID}">{$APP.LBL_CREATE} {$APP.Invoice}</a>
					</td>
				</tr>
						{/if}
				{elseif $TODO_PERMISSION eq 'true' || $EVENT_PERMISSION eq 'true' || $CONTACT_PERMISSION eq 'true'|| $MODULE eq 'Contacts' || ($MODULE eq 'Documents')}                              
				<tr><td align="left" class="genHeaderSmall">{$APP.LBL_ACTIONS}</td></tr>
				{/if}
					{if $MODULE eq 'Contacts'}
						{assign var=subst value="contact_id"}
						{assign var=acc value="&account_id=$accountid"}
					{else}
						{assign var=subst value="parent_id"}
						{assign var=acc value=""}
					{/if}					
					{if $MODULE eq 'Leads' || $MODULE eq 'Contacts' || $MODULE eq 'Accounts' || $MODULE eq 'Vendors'}
						{if $SENDMAILBUTTON eq 'permitted'}						
							<tr>
								<td align="left" style="padding-left:10px;"> 
									<input type="hidden" name="pri_email" value="{$EMAIL1}"/>
									<input type="hidden" name="sec_email" value="{$EMAIL2}"/>
									{* crmv@18747 *}
									{*
										<a href="javascript:void(0);" class="webMnu" onclick="if(LTrim('{$EMAIL1}') !='' || LTrim('{$EMAIL2}') !=''){ldelim}fnvshobj(this,'sendmail_cont');sendmail('{$MODULE}',{$ID}){rdelim}else{ldelim}OpenCompose('','create'){rdelim}"><img src="{'sendmail.png'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>&nbsp;
										<a href="javascript:void(0);" class="webMnu" onclick="if(LTrim('{$EMAIL1}') !='' || LTrim('{$EMAIL2}') !=''){ldelim}fnvshobj(this,'sendmail_cont');sendmail('{$MODULE}',{$ID}){rdelim}else{ldelim}OpenCompose('','create'){rdelim}">{$APP.LBL_SENDMAIL_BUTTON_LABEL}</a>
									*}
									<a href="javascript:void(0);" class="webMnu" onclick="fnvshobj(this,'sendmail_cont');sendmail('{$MODULE}',{$ID})"><img src="{'sendmail.png'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>&nbsp;
									<a href="javascript:void(0);" class="webMnu" onclick="fnvshobj(this,'sendmail_cont');sendmail('{$MODULE}',{$ID})">{$APP.LBL_SENDMAIL_BUTTON_LABEL}</a>
									{* crmv@18747e *}
								</td>
							</tr>
						{/if}
						<!-- crmv@7216 -->							
						{if $SENDFAXBUTTON eq 'permitted'}
							<tr>
								<td align="left" style="padding-left:10px;"> 						
									<input type="hidden" name="fax" value="{$FAX}"/>
									{* crmv@18747 *}
									{*
										<a href="javascript:void(0);" class="webMnu" onclick="if(document.DetailView.fax.value !=''){ldelim}fnvshobj(this,'sendfax_cont');sendfax('{$MODULE}',{$ID}){rdelim}else{ldelim}OpenComposeFax('','create'){rdelim}"><img src="{'sendfax.png'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>&nbsp;
										<a href="javascript:void(0);" class="webMnu" onclick="if(document.DetailView.fax.value !=''){ldelim}fnvshobj(this,'sendfax_cont');sendfax('{$MODULE}',{$ID}){rdelim}else{ldelim}OpenComposeFax('','create'){rdelim}">{$APP.LBL_SEND_FAX_BUTTON}</a>
									*}
									<a href="javascript:void(0);" class="webMnu" onclick="fnvshobj(this,'sendfax_cont');sendfax('{$MODULE}',{$ID})"><img src="{'sendfax.png'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>&nbsp;
									<a href="javascript:void(0);" class="webMnu" onclick="fnvshobj(this,'sendfax_cont');sendfax('{$MODULE}',{$ID})">{$APP.LBL_SEND_FAX_BUTTON}</a>
									{* crmv@18747e *}
								</td>
							</tr>									
						{/if}	
						<!-- crmv@7216e -->	
						<!-- crmv@7217 -->	<!-- crmv@16703 -->
						{if $SENDSMSBUTTON eq 'permitted'}
							<tr>
								<td align="left" style="padding-left:10px;"> 						
									<input type="hidden" name="mobile" value="{$SMS}"/>
									<a href="javascript:void(0);" class="webMnu" onclick="if(document.DetailView.mobile.value !=''){ldelim}fnvshobj(this,'sendsms_cont');sendsms('{$MODULE}',{$ID}){rdelim}else{ldelim}sendsms('{$MODULE}',{$ID}){rdelim}"><img src="{'sendsms.png'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>&nbsp;
									<a href="javascript:void(0);" class="webMnu" onclick="if(document.DetailView.mobile.value !=''){ldelim}fnvshobj(this,'sendsms_cont');sendsms('{$MODULE}',{$ID}){rdelim}else{ldelim}sendsms('{$MODULE}',{$ID}){rdelim}">{$APP.LBL_SEND_SMS_BUTTON}</a>	
								</td>
							</tr>									
						{/if}	
						<!-- crmv@7217e -->	<!-- crmv@16703e -->
					{/if}
					
					{if $MODULE eq 'Contacts' || $EVENT_PERMISSION eq 'true'}	
					<tr>
						<td align="left" style="padding-left:10px;"> 
				        	<a href="index.php?module=Calendar&action=EditView&return_module={$MODULE}&return_action=DetailView&activity_mode=Events&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu"><img src="{'AddEvent.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>
							<a href="index.php?module=Calendar&action=EditView&return_module={$MODULE}&return_action=DetailView&activity_mode=Events&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu">{$APP.LBL_ADD_NEW} {$APP.Event}</a>
						</td>
					</tr>
					{/if}
		
					{if $TODO_PERMISSION eq 'true' && ($MODULE eq 'Accounts' || $MODULE eq 'Leads')}
					<tr>
						<td align="left" style="padding-left:10px;">
					        <a href="index.php?module=Calendar&action=EditView&return_module={$MODULE}&return_action=DetailView&activity_mode=Task&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu"><img src="{'AddToDo.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
							<a href="index.php?module=Calendar&action=EditView&return_module={$MODULE}&return_action=DetailView&activity_mode=Task&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu">{$APP.LBL_ADD_NEW} {$APP.Todo}</a>
						</td>
					</tr>
					{/if}
		
					{if $MODULE eq 'Contacts' && $CONTACT_PERMISSION eq 'true'}
					<tr>
						<td align="left" style="padding-left:10px;">
					        <a href="index.php?module=Calendar&action=EditView&return_module={$MODULE}&return_action=DetailView&activity_mode=Task&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu"><img src="{'AddToDo.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
							<a href="index.php?module=Calendar&action=EditView&return_module={$MODULE}&return_action=DetailView&activity_mode=Task&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu">{$APP.LBL_ADD_NEW} {$APP.Todo}</a>
						</td>
					</tr>
					{/if}							
					
					{if $MODULE eq 'Leads'}
						{if $CONVERTLEAD eq 'permitted'}
					<tr>
						<td align="left" style="padding-left:10px;">
							<a href="javascript:void(0);" class="webMnu" onclick="callConvertLeadDiv('{$ID}');"><img src="{'Leads.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>
							<a href="javascript:void(0);" class="webMnu" onclick="callConvertLeadDiv('{$ID}');">{$APP.LBL_CONVERT_BUTTON_LABEL}</a>
						</td>
					</tr>
						{/if}
					{/if}
					
					<!-- Start: Actions for Documents Module -->
					{if $MODULE eq 'Documents'}
                        <tr><td align="left" style="padding-left:10px;">			        
						{if $DLD_TYPE eq 'I' && $FILE_STATUS eq '1'}	
							<br><a href="index.php?module=uploads&action=downloadfile&fileid={$FILEID}&entityid={$NOTESID}"  onclick="javascript:dldCntIncrease({$NOTESID});" class="webMnu"><img src="{'fbDownload.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" title="{$APP.LNK_DOWNLOAD}" border="0"/></a>
		                    <a href="index.php?module=uploads&action=downloadfile&fileid={$FILEID}&entityid={$NOTESID}" onclick="javascript:dldCntIncrease({$NOTESID});">{$MOD.LBL_DOWNLOAD_FILE}</a>
						{elseif $DLD_TYPE eq 'E' && $FILE_STATUS eq '1'}
							<br><a target="_blank" href="{$DLD_PATH}" onclick="javascript:dldCntIncrease({$NOTESID});"><img src="{'fbDownload.gif'|@vtiger_imageurl:$THEME}"" align="absmiddle" title="{$APP.LNK_DOWNLOAD}" border="0"></a>
							<a target="_blank" href="{$DLD_PATH}" onclick="javascript:dldCntIncrease({$NOTESID});">{$MOD.LBL_DOWNLOAD_FILE}</a>
						{/if}
						</td></tr>
						{if $CHECK_INTEGRITY_PERMISSION eq 'yes'}
							<tr><td align="left" style="padding-left:10px;">	
							<br><a href="javascript:;" onClick="checkFileIntegrityDetailView({$NOTESID});"><img id="CheckIntegrity_img_id" src="{'yes.gif'|@vtiger_imageurl:$THEME}" alt="Check integrity of this file" title="Check integrity of this file" hspace="5" align="absmiddle" border="0"/></a>
		                    <a href="javascript:;" onClick="checkFileIntegrityDetailView({$NOTESID});">{$MOD.LBL_CHECK_INTEGRITY}</a>&nbsp;
		                    <input type="hidden" id="dldfilename" name="dldfilename" value="{$FILEID}-{$FILENAME}">
		                    <span id="vtbusy_integrity_info" style="display:none;">
								{include file="LoadingIndicator.tpl"}</span>
							<span id="integrity_result" style="display:none"></span>						
							</td></tr>
						{/if}
						<tr><td align="left" style="padding-left:10px;">			        
						{if $DLD_TYPE eq 'I'}	
							<!-- //crmv@16312 -->
							<input type="hidden" id="dldfilename" name="dldfilename" value="{$FILEID}-{$FILENAME}">
							<!-- //crmv@16312 end -->
							<br><a href="javascript: document.DetailView.return_module.value='Documents'; document.DetailView.return_action.value='DetailView'; document.DetailView.module.value='Documents'; document.DetailView.action.value='EmailFile'; document.DetailView.record.value={$NOTESID}; document.DetailView.return_id.value={$NOTESID}; sendfile_email();" class="webMnu"><img src="{'attachment.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
		                    <a href="javascript: document.DetailView.return_module.value='Documents'; document.DetailView.return_action.value='DetailView'; document.DetailView.module.value='Documents'; document.DetailView.action.value='EmailFile'; document.DetailView.record.value={$NOTESID}; document.DetailView.return_id.value={$NOTESID}; sendfile_email();">{$MOD.LBL_EMAIL_FILE}</a>                                      
						{/if}
						</td></tr>
						<tr><td>&nbsp;</td></tr>
					
						{/if}
					{/if}
					<!-- End: Actions for Documents Module -->	
	
                {* vtlib customization: Avoid line break if custom links are present *}
                {if !isset($CUSTOM_LINKS) || empty($CUSTOM_LINKS)}
                <br>
                {/if}
			
			{* vtlib customization: Custom links on the Detail view basic links *}
			{if $CUSTOM_LINKS && $CUSTOM_LINKS.DETAILVIEWBASIC}
				<table width="100%" border="0" cellpadding="5" cellspacing="0">
				{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEWBASIC}
				<tr>
					<td align="left" style="padding-left:10px;">
						{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
						{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
						{if $customlink_label eq ''}
							{assign var="customlink_label" value=$customlink_href}
						{else}
							{* Pickup the translated label provided by the module *}
							{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
						{/if}
						{if $CUSTOMLINK->linkicon}
						<a class="webMnu" href="{$customlink_href}"><img hspace=5 align="absmiddle" border=0 src="{$CUSTOMLINK->linkicon}"></a>
						{/if}
						<a class="webMnu" href="{$customlink_href}">{$customlink_label}</a>
					</td>
				</tr>
				{/foreach}
				</table>
			{/if}
			
			{* vtlib customization: Custom links on the Detail view *}
			{if $CUSTOM_LINKS && $CUSTOM_LINKS.DETAILVIEW}
				<br>
				{if !empty($CUSTOM_LINKS.DETAILVIEW)}					
					<table width="100%" border="0" cellpadding="5" cellspacing="0">
						<tr><td align="left" class="dvtUnSelectedCell dvtCellLabel">
							<a href="javascript:;" onmouseover="fnvshobj(this,'vtlib_customLinksLay');" onclick="fnvshobj(this,'vtlib_customLinksLay');"><b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b></a>
						</td></tr>
					</table>
					<br>
					<div class="drop_mnu" style="display: none; left: 193px; top: 106px; width: 155px; position:absolute;" id="vtlib_customLinksLay" 
						onmouseout="fninvsh('vtlib_customLinksLay')" onmouseover="fnvshNrm('vtlib_customLinksLay')">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr><td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;"><b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b></td></tr>
						<tr>
							<td>
								{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEW}
									{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
									{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
									{if $customlink_label eq ''}
										{assign var="customlink_label" value=$customlink_href}
									{else}
										{* Pickup the translated label provided by the module *}
										{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
									{/if}
									<a href="{$customlink_href}" class="drop_down">{$customlink_label}</a>
								{/foreach}
							</td>
						</tr>
						</table>
					</div>
				{/if}
			{/if}
		{* END *}
			<!-- Action links END -->
                
			{include file="modules/PDFMaker/InventoryPdfActions.tpl"}	<!-- crmv@17889 -->

			{if $TAG_CLOUD_DISPLAY eq 'true'}
			<!-- Tag cloud display -->
				<table border=0 cellspacing=0 cellpadding=0 width=100% class="tagCloud">
				<tr>
					<td class="tagCloudTopBg"><img src="{$IMAGE_PATH}tagCloudName.gif" border=0></td>
				</tr>
				<tr>
					<td><div id="tagdiv" style="display:visible;"><form method="POST" action="javascript:void(0);" onsubmit="return tagvalidate();"><input class="textbox"  type="text" id="txtbox_tagfields" name="textbox_First Name" value="" style="width:100px;margin-left:5px;"></input>&nbsp;&nbsp;<input name="button_tagfileds" type="submit" class="crmbutton small save" value="{$APP.LBL_TAG_IT}" /></form></div></td>
		        </tr>
				<tr>
					<td class="tagCloudDisplay" valign=top> <span id="tagfields">{$ALL_TAG}</span></td>
				</tr>
				</table>
				<!-- End Tag cloud display -->
			{/if}
			
			{if !empty($CUSTOM_LINKS.DETAILVIEWWIDGET)}
				{foreach key=CUSTOMLINK_NO item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEWWIDGET}
					{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
					{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
					{* Ignore block:// type custom links which are handled earlier *}
					{if !preg_match("/^block:\/\/.*/", $customlink_href)}
						{if $customlink_label eq ''}
							{assign var="customlink_label" value=$customlink_href}
						{else}
							{* Pickup the translated label provided by the module *}
							{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
						{/if}
						<br/>
						<table border=0 cellspacing=0 cellpadding=0 width=100% class="rightMailMerge">
			  				<tr>
								<td class="rightMailMergeHeader">
									<b>{$customlink_label}</b>
									{include file="LoadingIndicator.tpl" LIID="detailview_block_"|cat:$CUSTOMLINK_NO|cat:"_indicator" LIEXTRASTYLE="display:none;"}
								</td>
			  				</tr>
			  				<tr style="height:25px">
								<td class="rightMailMergeContent"><div id="detailview_block_{$CUSTOMLINK_NO}"></div></td>
			  				</tr>
			  				<script type="text/javascript">
			  					vtlib_loadDetailViewWidget("{$customlink_href}", "detailview_block_{$CUSTOMLINK_NO}", "detailview_block_{$CUSTOMLINK_NO}_indicator");
			  				</script>
						</table>
					{/if}
				{/foreach}
			{/if}
			
			</td>
		</tr>
		</table>
		
		</div>
		<!-- PUBLIC CONTENTS STOPS-->
	</td>
</tr>
	<tr>
		<td colpsan=2>			
			<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
				<tr>
					<td class="dvtTabCacheBottom" style="width:10px" nowrap>&nbsp;</td>
					
					<td class="dvtSelectedCellBottom" align=center nowrap>{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</td>	
					<td class="dvtTabCacheBottom" style="width:10px">&nbsp;</td>
					{if $SinglePane_View eq 'false' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
					<td class="dvtUnSelectedCell" onmouseout="fnHideDrop('More_Information_Modules_List_down');" onmouseover="fnDropUp(this,'More_Information_Modules_List_down');" align="center" nowrap>
						<a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a>
						<div onmouseover="fnShowDrop('More_Information_Modules_List_down')" onmouseout="fnHideDrop('More_Information_Modules_List_down')"
									 id="More_Information_Modules_List_down" class="drop_mnu" style="left: 502px; top: 76px; visibility: hidden, display: block">
							<table border="0" cellpadding="0" cellspacing="0" width="100%" id="More_Information_Modules_List_down_table">
							{foreach key=_RELATION_ID item=_RELATED_MODULE from=$IS_REL_LIST}
								<tr><td><a class="drop_down" href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}&selected_header={$_RELATED_MODULE}&relation_id={$_RELATION_ID}">{$_RELATED_MODULE|@getTranslatedString:$_RELATED_MODULE}</a></td></tr>
							{/foreach}
							</table>
						</div>
					</td>
					{/if}
					<td class="dvtTabCacheBottom" align="right" style="width:100%"></td>	{*<!-- crmv@18592 -->*}
				</tr>
			</table>
		</td>
	</tr>
</table>


{* ---- *}
{literal}
<script type="text/javascript">

  var healthblock = "Informazioni ospedale";

  function showExtraBlock() {
	  var bl = document.getElementsByName('block_'+healthblock);
	  if (bl) {
		  for (i=0; i<bl.length; ++i) {
		  	bl[i].style.display = '';
		  }
	  }

  }

  function hideExtraBlock() {
	  var bl = document.getElementsByName('block_'+healthblock);
	  if (bl) {
		  for (i=0; i<bl.length; ++i) {
		  	bl[i].style.display = 'none';
		  }
	  }
  }

  function onchangeIndustry() {
	var ind = document.getElementById('txtbox_Settore');
  	if (ind && ind.tagName.toUpperCase() == 'SELECT') {
 	  	sel = ind.selectedIndex;
  		val = ind.options.item(sel);
  		if (val && val.value == 'Hospitality') 
	  		showExtraBlock();
  		else
	  		hideExtraBlock();
  	}
  }

  //onchangeIndustry();
  //register onchange handler	
  var ind = document.getElementById('txtbox_Settore');
  if (ind && ind.tagName.toUpperCase() == 'SELECT') {
	  ind.onchange = onchangeIndustry;
	  onchangeIndustry();
  }

</script>
{/literal}



{if $MODULE eq 'Products'}
<script language="JavaScript" type="text/javascript" src="modules/Products/Productsslide.js"></script>
<script language="JavaScript" type="text/javascript">Carousel();</script>
{elseif $MODULE eq 'Accounts'}
<script language="JavaScript" type="text/javascript">show_hierach({$ID},1);</script>
{/if}

<script>
function getTagCloud()
{ldelim}
new Ajax.Request(
        'index.php',
        {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
        method: 'post',
        postBody: 'module={$MODULE}&action={$MODULE}Ajax&file=TagCloud&ajxaction=GETTAGCLOUD&recordid={$ID}',
        onComplete: function(response) {ldelim}
                                $("tagfields").innerHTML=response.responseText;
                                $("txtbox_tagfields").value ='';
                        {rdelim}
        {rdelim}
);
{rdelim}
{* crmv@26279 *}
{if $TAG_CLOUD_DISPLAY eq 'true'}
	getTagCloud();
{/if}
{* crmv@26279e *}
</script>
<!-- added for validation -->
<script language="javascript" type="text/javascript">
	var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
	var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
	var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
	var fielduitype = new Array({$VALIDATION_DATA_FIELDUITYPE}); // crmv@83877
	var fieldwstype = new Array({$VALIDATION_DATA_FIELDWSTYPE}); //crmv@112297
</script>
</td>

	<td align=right valign=top></td>
</tr></table>

<form name="SendMail" onsubmit="VtigerJS_DialogBox.block();"><div id="sendmail_cont" style="z-index:100001;position:absolute;"></div></form>
<form name="SendFax" onsubmit="VtigerJS_DialogBox.block();"><div id="sendfax_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
<!-- crmv@16703 -->
<form name="SendSms" id="SendSms" onsubmit="VtigerJS_DialogBox.block();" method="POST" action="index.php"><div id="sendsms_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
<!-- crmv@16703e -->