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
{include file='SetMenu.tpl'}
<!-- crmv@30683  -->
{* crmv@30683 *}
<table border=0 cellspacing=0 cellpadding=5 width=100%
	class="settingsSelUITopLine">
	<tr>
		<td width=50 rowspan=2 valign=top><img
			src="modules/Webforms/img/Webform.png" alt="{'Webforms'|@getTranslatedString:$MODULE}" width="48"
			height="48" border=0 title="{'Webforms'|@getTranslatedString:$MODULE}"></td>
		<td class=heading2 valign=bottom><b> {'LBL_SETTINGS'|@getTranslatedString:$MODULE} >
				{'Webforms'|@getTranslatedString:$MODULE} </b></td>
		<!-- crmv@30683 -->
	</tr>
	<tr>
		<td valign=top class="small">{'LBL_WEBFORMS_DESCRIPTION'|@getTranslatedString:$MODULE}</td>
	</tr>
</table>
{* crmv@30683e *}
<br>
<script>
var gVTModule = '{$smarty.request.module|@vtlib_purify}';
{literal}
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

//Added to send a file, in Documents module, as an attachment in an email
function sendfile_email()
{ldelim}
	filename = $('dldfilename').value;
	OpenCompose(filename,'Documents');
{rdelim}

</script>

<div id="lstRecordLayout" class="layerPopup" style="display:none;width:325px;height:300px;z-index:21;position:fixed;"></div>	{*<!-- crmv@18592 -->*}

<table class="margintop" width="100%" cellpadding="0" cellspacing="0" border="0"> {* crmv@25128 *}
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
		
		<form name="action_form" action="" method="post">
			<input type="hidden" name="id" value="{$WEBFORMMODEL->getId()}"></input>
		</form>
		<div id="orgLay1" class="crmvDiv" style="display:none; position:absolute; top:25%; left:30%; height:410px; width:50%; z-index:100;">
				<table cellspacing="0" cellpadding="5" border="0" width="100%">
					<tr>
						<td class="level3Bg" align="left" ><b>
							<img src="modules/Webforms/img/Webform_small.png">
							<p id="webform_popup_header" style="display:inline;">{$WEBFORMMODEL->getName()}</p></b>
						</td>						
					</tr>
				</table>
				<table cellspacing="0" cellpadding="0" border="0" align="center" width="95%" >
						<tr>
							<td class="small">
								<table cellpadding="5" border="0" bgcolor="white" align="center" width="100%"  celspacing="0">
									{*
									<tr>
										<td id="webform_source_description"></td>
									</tr>
									*}
									<tr>
										<td>
											<font color="green" >{'LBL_EMBED_MSG'|@getTranslatedString:$MODULE }</font>
										</td>
									</tr>
									<tr>
										<td rowspan="5">
											<textarea readonly="readonly" style="width:100%;height:320px;" rows="25" cols="25" id="webform_source" name="webform_source" value=""></textarea>
										</td>
									</tr>
								</table>
							</td>
						</tr>						
				</table>
				<div class="closebutton" onClick="$('orgLay1').style.display='none';"></div>
		</div>
		
		<!-- Account details tabs -->
		<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
		<tr>
			<td>
				<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
				<tr>
					<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
					<td class="dvtSelectedCell" align=center nowrap>{$APP.LBL_INFORMATION}</td>
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
							 <form action="index.php" method="post" name="DetailView" id="form">
							{include file='DetailViewHidden.tpl'}
						
							  <!-- Start of File Include by SAKTI on 10th Apr, 2008 -->
							 {include_php file="./include/DetailViewBlockStatus.php"}
							 <!-- Start of File Include by SAKTI on 10th Apr, 2008 -->

							<!-- Detailed View Code starts here-->
							<table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
							<tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                             </tr>

							<tr>
										<td id="autocom"></td>
									</tr>
									<tr>
										<td>
										<!-- General details -->
											<table   class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
												<!--Block Head-->
												<tr>
													<td colspan={if $WEBFORMMODEL->hasId()}"3"{else}"4"{/if} class="detailedViewHeader" style="border-right: none;">
														<b>{'LBL_MODULE_INFORMATION'|@getTranslatedString:$MODULE}</b>
													</td>
													<td colspan="1" class="detailedViewHeader" align="right" style="border-left: none;" nowrap>
														{'LBL_ENABLED'|@getTranslatedString:$MODULE}
														{if $WEBFORMMODEL->getEnabled() eq 1}
															<img src="themes/images/prvPrfSelectedTick.gif">
														{else}
															<img src="themes/images/no.gif">
														{/if}
													</td>

												</tr>
												<!-- Cell information  -->
												<tr style="height:25px">
													<td class="dvtCellLabel" align="right" width="10%">
														<font color="red">*</font>{'LBL_WEBFORM_NAME'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" width="40%">
														{$WEBFORMMODEL->getName()}
													</td>
													<td class="dvtCellLabel" align="right" width="10%">
														<font color="red">*</font>{'LBL_MODULE'|@getTranslatedString:$MODULE} :
													</td>
													<td class="dvtCellInfo" align="left" width="40%">
														{$WEBFORMMODEL->getTargetModule()}
													</td>
												</tr>
												<tr style="height:10px"><td colspan="4"></td></tr>
												<tr style="height:25px">
													<td class="dvtCellLabel" align="right" >
														<font color="red">*</font>{'LBL_ASSIGNED_TO'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														{$OWNER}
													</td>
													<td class="dvtCellLabel" align="right" >
														{'LBL_RETURNURL'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														http://{$WEBFORMMODEL->getReturnUrl()}
													</td>
												</tr>
												<tr style="height:10px"><td colspan="4"></td></tr>
												<tr style="height:25px;">
													<td class="dvtCellLabel" align="right" >
														{'LBL_PUBLICID'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														{$WEBFORMMODEL->getPublicId()}
													</td>
													<td class="dvtCellLabel" align="right" >
														{'LBL_POSTURL'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														{$ACTIONPATH}
													</td>
												</tr>
												<tr style="height:10px"><td colspan="4"></td></tr>
												<tr>
													<td class="dvtCellLabel" align="right" style="height:25px;">
														{'LBL_DESCRIPTION'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														{$WEBFORMMODEL->getDescription()
													</td>
												</tr>
												<!--Cell Information end-->
												<tr style="height:25px">
													<td>&nbsp;</td>
												</tr>
												<!--Block Head-->
												<tr>
													<td colspan="3" class="detailedViewHeader">
														<b>{'LBL_FIELD_INFORMATION'|@getTranslatedString:$MODULE}</b>
													</td>
												</tr>
												<tr><td>&nbsp;</td></tr>
	<!-- Cell information for fields -->
												<tr>
													<td class="detailedViewHeader" colspan="4">
														<b>{'LBL_FIELD_INFORMATION'|@getTranslatedString:$MODULE}</b>
													</td>
												</tr>
												<tr >
													<td colspan="4"  >
														<div id="Webforms_FieldsView"></div>
<!--Fields View-->
														<table id="field_table" class="small" border="0" cellpadding="5" cellspacing="1" width="100%">
															{* crmv@32257 *}
															<tr height="25px">
																<td class="lvtCol">{'LBL_FIELDLABEL'|@getTranslatedString:$MODULE}</td>
																<td class="lvtCol">{'LBL_DEFAULT_VALUE'|@getTranslatedString:$MODULE}</td>
																<td style="width:2%;" class="lvtCol">{'LBL_HIDDEN'|@getTranslatedString:$MODULE}</td>
																<td style="width:2%;" class="lvtCol">{'LBL_REQUIRED'|@getTranslatedString:$MODULE}</td>
																<td style="width:20%;" class="lvtCol">{'LBL_NEUTRALIZEDFIELD'|@getTranslatedString:$MODULE}</td>
															</tr>
															{* crmv@32257e *}
															{foreach item=field from=$WEBFORMMODEL->getFields() name=fieldloop}
															{assign var=fieldinfo value=$WEBFORM->getFieldInfo($WEBFORMMODEL->getTargetModule(), $field->getFieldName())}
															{if $WEBFORMMODEL->isActive($fieldinfo.name,$WEBFORMMODEL->getTargetModule())}
																<tr style="height:25px" id="field_row">
																	<td class="dvtCellLabel" align="left" colspan="1">
																	{if $fieldinfo.mandatory eq 1}
																		<font color="red">*</font>
																	{/if}
																		{$fieldinfo.label}
																	</td>
																	<td class="dvtCellInfo">
																		{assign var="defaultvalueArray" value=$WEBFORMMODEL->retrieveDefaultValue($WEBFORMMODEL->getId(),$fieldinfo.name)}
																		{if $fieldinfo.type.name eq 'boolean'}
																			{if $defaultvalueArray[0] eq 'off'}
																				no
																			{elseif $defaultvalueArray[0] eq 'on'}
																				yes
																			{/if}
																		{else}

																		{','|implode:$defaultvalueArray}
																		{/if}
																	</td>
																	{* crmv@32257 *}
																	<td align="center" colspan="1">
																		{if  $WEBFORMMODEL->isHidden($WEBFORMMODEL->getId(),$fieldinfo.name) eq true}
																			<img src="themes/images/prvPrfSelectedTick.gif">
																		{else}
																			<img src="themes/images/no.gif">
																		{/if}
																	</td>
																	{* crmv@32257e *}
																	<td align="center" colspan="1">
																		{if  $WEBFORMMODEL->isRequired($WEBFORMMODEL->getId(),$fieldinfo.name) eq true}
																			<img src="themes/images/prvPrfSelectedTick.gif">
																		{else}
																			<img src="themes/images/no.gif">
																		{/if}
																	</td>
																	<td class="dvtCellLabel" align="left" colspan="1">
																		{if $WEBFORMMODEL->isCustomField($fieldinfo.name) eq true}
																			label:{$fieldinfo.label}
																		{else}
																			{$fieldinfo.name}
																		{/if}
																	</td>
																</tr>
															{/if}
														{/foreach}
														</table>
<!--Fields view ends here-->
													</td>
												</tr>
	<!--Cell Information end-->
												<tr style="height:25px">
													<td>&nbsp;</td>
												</tr>
												
	     </table>
	 </div>
	</td>
	</tr>
			
			</form>	
			<!-- End the form related to detail view -->			

		</table>
		
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

<!-- added for validation -->
<script language="javascript">
  var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
  var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
  var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
  var fielduitype = new Array({$VALIDATION_DATA_FIELDUITYPE}); // crmv@83877
  var fieldwstype = new Array({$VALIDATION_DATA_FIELDWSTYPE}); //crmv@112297
</script>
</td>

<td align=right valign=top></td>
</tr></table>
