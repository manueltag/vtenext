{*<!--
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
-->*}
{include file='Buttons_List1.tpl'}	
<script type="text/javascript" src="modules/{$MODULE}/language/{$LANGUAGE}.lang.js"></script>
<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<script type="text/javascript">
	{if $WEBFORM->hasId()}
		var mode="edit";
	{else}
		var mode="save";
	{/if}
</script>
{include file='Buttons_List_Edit.tpl'}
{include file='SetMenu.tpl'} <!-- crmv@30683  --> 
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
<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
	<tr>
		<td class="showPanelBg" valign="top" width="100%">
			<div class="small">
				<table class="margintop" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
				<td>
					<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
					   <tr>
						<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
						<td class="dvtSelectedCell" align=center nowrap>{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</td>
						<td class="dvtTabCache" style="width:10px">&nbsp;</td>
						<td class="dvtTabCache" style="width:100%">&nbsp;</td>
					   </tr>
					</table>
				</td>
			   </tr>
				<tr>
					<td align="left" valign="top">

					<!-- Basic Information Tab Opened -->
					<table border=0 cellspacing=0 cellpadding=3 width=100% class="dvtContentSpace">
						<tr>
							<td align="left">
							<!-- content cache -->
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td id="autocom"></td>
									</tr>
									<tr>
										<td style="padding:5px;padding-top:15px;">
										<!-- General details -->
										<form name="webform_edit" id="webform_edit" action="index.php?module=Webforms&action=Save" method="post">
											{if $WEBFORM->hasId()}
											<input type="hidden" name="id" value={$WEBFORM->getId()}></input>
											{/if}
											<table   class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
												<!--Block Head-->
												<tr>
													<td colspan={if $WEBFORM->hasId()}"3"{else}"4"{/if} class="detailedViewHeader" style="border-right: none;">
														<b>{'LBL_MODULE_INFORMATION'|@getTranslatedString:$MODULE}</b>
													</td>
													{if $WEBFORM->hasId()}
													<td  colspan="1" class="detailedViewHeader" align="right" style="border-left: none;">
														{'LBL_ENABLE'|@getTranslatedString:$MODULE}
														{if $WEBFORM->getEnabled() eq 1}
															<input type="checkbox" name="enabled" id="enabled" checked="checked"></input>
														{else}
															<input type="checkbox" name="enabled" id="enabled" ></input>
														{/if}
													</td>
													{/if}
												</tr>
												<!-- Cell information  -->
												<tr style="height:25px">
													<td class="dvtCellLabel" align="right" width="10%" nowrap="nowrap">
														<font color="red">*</font>{'LBL_WEBFORM_NAME'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" width="40%">
														<input type="text" class="detailedViewTextBox" id="name"  name="name" value="{$WEBFORM->getName()}" {if $WEBFORM->hasId()}readonly="readonly"{/if}>
													</td>
													<td class="dvtCellLabel" align="right" width="10%" nowrap="nowrap">
														<font color="red">*</font>{'LBL_MODULE'|@getTranslatedString:$MODULE} :
													</td>
													<td class="dvtCellInfo" align="left" width="40%">
														{if $WEBFORM->hasId()}
															{$WEBFORM->getTargetModule()}
															<input type="hidden" value="{$WEBFORM->getTargetModule()}" name="targetmodule" id="targetmodule"></input>
														{else}
															<select id="targetmodule" name="targetmodule" onchange='javascript:Webforms.fetchFieldsView(this.value);' class="detailedViewTextBox">
																<option value="">--module--</option>
																 {foreach item=module from=$WEBFORMMODULES name=moduleloop}
																	<option value="{$module}">{$module}</option>
																{/foreach}
															</select>
														{/if}
													</td>
												</tr>
												<tr style="height:10px"><td colspan="4"></td></tr>
												<tr style="height:25px">
													<td class="dvtCellLabel" align="right" >
														<font color="red">*</font>{'LBL_ASSIGNED_TO'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														<select id="ownerid" name="ownerid" class="detailedViewTextBox">
															<option value="">--{'LBL_SELECT_USER'|@getTranslatedString:$MODULE}--</option>
																{foreach key=userid item=username name=assigned_user from=$USERS}
																<option value="{$userid}"
																	{if $WEBFORMID && $userid eq $WEBFORM->getOwnerId()} selected {/if}>
																	{$username}
																</option>
															{/foreach}
														</select>
													</td>
													<td class="dvtCellLabel" align="right" >
														{'LBL_RETURNURL'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" nowrap>
														http:// <input type="text" class="detailedViewTextBox" id="returnurl"  name="returnurl" value="{$WEBFORM->getReturnUrl()}">
													</td>
												</tr>
												{if $WEBFORM->hasId()}
												<tr style="height:10px"><td colspan="4"></td></tr>
												<tr style="height:25px;">
													<td class="dvtCellLabel" align="right" >
														{'LBL_PUBLICID'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														{$WEBFORM->getPublicId()}
													</td>
													<td class="dvtCellLabel" align="right" >
														{'LBL_POSTURL'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" align="left" >
														{$ACTIONPATH}
													</td>
												</tr>
												{/if}
												<tr style="height:10px"><td colspan="4"></td></tr>
												<tr>
													<td class="dvtCellLabel" align="right" colspan="1">
														{'LBL_DESCRIPTION'|@getTranslatedString:$MODULE}
													</td>
													<td class="dvtCellInfo" colspan="3">
														<textarea rows="8" cols="90" name="description" id="description" tabindex="" class="detailedViewTextBox" >{if $WEBFORM->hasId()}{$WEBFORM->getDescription()}{/if}</textarea>
													</td>
												</tr>
												<!--Cell Information end-->
												<tr style="height:25px">
													<td>&nbsp;</td>
												</tr>
												<!--Block Head-->
												<tr>
													<td colspan="3" class="detailedViewHeader" style="border-right: none;">
														<b>{'LBL_FIELD_INFORMATION'|@getTranslatedString:$MODULE}</b>
													</td>
													<td  colspan="1" class="detailedViewHeader" align="right" style="border-left: none;">
													</td>
												</tr>
	<!-- Cell information for fields -->
												<tr >
													<td colspan="4"  >
														<div id="Webforms_FieldsView"></div>
														{if $WEBFORM->hasId()}{include file="modules/Webforms/FieldsView.tpl"}{/if}
													</td>
												</tr>
	<!--Cell Information end-->
												<tr style="height:25px">
													<td>&nbsp;</td>
												</tr>
											</table>
										</form>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
		<!-- Basic Information Tab Closed -->
			</td>
		</tr>
	</table>
	</form></div>
	</td>
	<td align="right" valign="top"></td>
</tr>
</table>