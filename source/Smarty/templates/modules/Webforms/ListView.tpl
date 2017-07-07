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
<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
{include file='SetMenu.tpl'}
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

{*<!-- Contents -->*}
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>

	<tr>
		<td class="showPanelBg" valign="top" width=100% style="padding:10px;">&nbsp;
			<div id="orgLay1" class="crmvDiv" style="display: none;position: absolute;top: 25%;left: 30%;height:400px;width:50%;z-index:100; ">
				<table cellspacing="0" cellpadding="5" border="0" width="100%">
					<tr height="34">
						<td class="level3Bg" align="left" style="padding:5px;">
							<b><p id="webform_popup_header" style="display:inline;"></p></b>
						</td>						
					</tr>
				</table>
				<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%" >
					<tr>
						<td class="small">
							<table cellpadding="5" border="0" align="center" width="100%"  celspacing="0">
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
			<div>
				
			<table border="0" cellspacing="1" cellpadding="3" width="100%" class="lvt small">
				<!-- Table Headers -->
				<tr>
					<td class="lvtCol">{'LBL_ACTION'|@getTranslatedString:$MODULE}</td>
					<td class="lvtCol">{'LBL_WEBFORM_NAME'|@getTranslatedString:$MODULE}</td>
					<td class="lvtCol">{'LBL_DESCRIPTION'|@getTranslatedString:$MODULE}</td>
					<td class="lvtCol">{'LBL_MODULE'|@getTranslatedString:$MODULE}</a></td>
					<td class="lvtCol">{'LBL_PUBLICID'|@getTranslatedString:$MODULE}</td>
					<td class="lvtCol">{'LBL_RETURNURL'|@getTranslatedString:$MODULE}</td>
					<td class="lvtCol" width="2%">{'LBL_STATUS'|@getTranslatedString:$MODULE}</td>
				</tr>
				<!-- Table Contents -->
				{if empty($WEBFORMS)}
				<tr>
					<td align="center" colspan="9" style="background-color:#efefef;height:340px">
						<div style="border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 45%; position: relative; z-index: 10000000;">
							<table cellspacing="0" cellpadding="5" border="0" width="98%">
								<tr>
									<td width="25%" rowspan="2">
										<img height="60" width="61" src="themes/images/empty.jpg">
									</td>
									<td nowrap="nowrap" width="75%" style="border-bottom: 1px solid rgb(204, 204, 204);">
										<span class="genHeaderSmall">{'LBL_NO_WEBFORM'|@getTranslatedString:$MODULE}
										</span>
									</td>
								</tr>
								<tr>
									<td nowrap="nowrap" align="left" class="small">{$APP.LBL_EMPTY_LIST_YOU_CAN_CREATE_RECORD_NOW}<br>
										&nbsp;&nbsp;- <b><a href="index.php?module=Webforms&action=WebformsEditView&parenttab=Settings">{'LBL_CREATE_WEBFORM'|@getTranslatedString:$MODULE}</a></b><br>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				{/if}
				{foreach item=webform from=$WEBFORMS name=pname}
				<form name="form{$webform->getId()}" action="" method="post">
					<input type="hidden" name="id" value="{$webform->getId()}"></input>
				</form>
				<tr bgcolor="white" onmouseover="this.className='lvtColDataHover'" onmouseout="this.className='lvtColData'" id="row_99" class="lvtColData">
					<td onmouseover="vtlib_listview.trigger('cell.onmouseover', $(this))" onmouseout="vtlib_listview.trigger('cell.onmouseout', $(this))"><a href="index.php?module=Webforms&amp;action=WebformsEditView&amp;id={$webform->getId()}&amp;parenttab=Settings&amp;operation=edit"><img src="{'small_edit.png'|@vtiger_imageurl:$THEME}" border="0" /></a>&nbsp;<a onclick="Webforms.deleteForm('form{$webform->getId()}',{$webform->getId()})" style="cursor:pointer;"><img src="{'small_delete.png'|@vtiger_imageurl:$THEME}" border="0" /></a>&nbsp;<a onclick='javascript:document.getElementById("webform_popup_header").innerHTML="{$webform->getName()}";Webforms.getHTMLSource({$webform->getId()});' style="cursor:pointer;"><img src="modules/Webforms/img/Webform_small.png" width="20" height="20" border="0" alt="{'LBL_SOURCE'|@getTranslatedString:$MODULE}" /></a></td>
					<td onmouseover="vtlib_listview.trigger('cell.onmouseover', $(this))" onmouseout="vtlib_listview.trigger('cell.onmouseout', $(this))"><a href="index.php?module=Webforms&amp;action=WebformsDetailView&amp;id={$webform->getId()}&amp;parenttab=Settings&amp;operation=detail" id="{$webform->getId()}">{$webform->getName()}</a></td>
					<td onmouseover="vtlib_listview.trigger('cell.onmouseover', $(this))" onmouseout="vtlib_listview.trigger('cell.onmouseout', $(this))">{$webform->getDescription()}</td>
					<td onmouseover="vtlib_listview.trigger('cell.onmouseover', $(this))" onmouseout="vtlib_listview.trigger('cell.onmouseout', $(this))">{$webform->getTargetModule()}</td>
					<td onmouseover="vtlib_listview.trigger('cell.onmouseover', $(this))" onmouseout="vtlib_listview.trigger('cell.onmouseout', $(this))">{$webform->getPublicId()}</td>
					<td onmouseover="vtlib_listview.trigger('cell.onmouseover', $(this))" onmouseout="vtlib_listview.trigger('cell.onmouseout', $(this))">{$webform->getReturnUrl()}</td>
					<td onmouseover="vtlib_listview.trigger('cell.onmouseover', $(this))" onmouseout="vtlib_listview.trigger('cell.onmouseout', $(this))" align="center"> {if $webform->getEnabled() eq 1}<img src="themes/images/prvPrfSelectedTick.gif">{else}<img src="themes/images/no.gif">{/if}</td>
				</tr>
				{/foreach}
			</table>
			</div>
		</td>
	</tr>
</table>