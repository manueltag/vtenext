{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/ *}
<script language="JavaScript" type="text/javascript" src="{"include/js/customview.js"|resourcever}"></script>
<script language="javascript">
function getCustomFieldList(customField)
{ldelim}
	var modulename = customField.options[customField.options.selectedIndex].value;
	var modulelabel = customField.options[customField.options.selectedIndex].text;
	$('module_info').innerHTML = '{$MOD.LBL_CUSTOM_FILED_IN} "'+modulelabel+'" {$APP.LBL_MODULE}';
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=CustomFieldList&fld_module='+modulename+'&parenttab=Settings&ajax=true',
			onComplete: function(response) {ldelim}
				$("cfList").innerHTML=response.responseText;
			{rdelim}
		{rdelim}
	);	
{rdelim}

{literal}
function deleteCustomField(id, fld_module, colName, uitype)
{
	{/literal}
        if(confirm("{$APP.ARE_YOU_SURE}"))
        {literal}
        {
                document.form.action="index.php?module=Settings&action=DeleteCustomField&fld_module="+fld_module+"&fld_id="+id+"&colName="+colName+"&uitype="+uitype
                document.form.submit()
        }
}

function getCreateCustomFieldForm(customField,id,tabid,ui)
{
    var modulename = customField;
    //To handle Events and Todo's separately while adding Custom fields
    var activitytype = '';
    var activityobj = document.getElementsByName('activitytype');
    if (activityobj != null) {
    	for(var i=0; i<activityobj.length; i++) {
    		if (activityobj[i].checked == true)
    			activitytype = activityobj[i].value;
    	}
    }
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Settings&action=SettingsAjax&file=CreateCustomField&fld_module='+customField+'&parenttab=Settings&ajax=true&fieldid='+id+'&tabid='+tabid+'&uitype='+ui+'&activity_type='+activitytype,
			onComplete: function(response) {
				$("createcf").innerHTML=response.responseText;
				gselected_fieldtype = '';
			}
		}
	);

}
function makeFieldSelected(oField,fieldid,blockid)
{
	if(gselected_fieldtype != '')
	{
		$(gselected_fieldtype).className = 'customMnu';
	}
	oField.className = 'customMnuSelected';	
	gselected_fieldtype = oField.id;	
	selFieldType(fieldid,'','',blockid)
	document.getElementById('selectedfieldtype_'+blockid).value = fieldid;
}
function CustomFieldMapping()
{
        document.form.action="index.php?module=Settings&action=LeadCustomFieldMapping";
        document.form.submit();
}
var gselected_fieldtype = '';
{/literal}
</script>
<div id="createcf" style="display:block;position:absolute;width:500px;"></div>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@30683 -->
<tbody><tr>
        <td valign="top"></td>
        <td class="showPanelBg" style="padding: 5px;" valign="top" width="100%"> <!-- crmv@30683 -->


	<div align=center>
			{include file='SetMenu.tpl'}
			{include file='Buttons_List.tpl'} {* crmv@30683 *}
			<!-- DISPLAY -->
			{if $MODE neq 'edit'}
			<b><font color=red>{$DUPLICATE_ERROR} </font></b>
			{/if}
			
				<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tbody><tr>
					<td rowspan="2" valign="top" width="50"><img src="{$IMAGE_PATH}custom.gif" alt="{$MOD.LBL_USERS}" title="{$MOD.LBL_USERS}" border="0" height="48" width="48"></td>
					<td class="heading2" valign="bottom"><b>{$MOD.LBL_SETTINGS} &gt; {$MOD.LBL_CUSTOM_FIELD_SETTINGS}</b></td> <!-- crmv@30683 -->
				</tr>

				<tr>
					<td class="small" valign="top">{$MOD.LBL_CREATE_AND_MANAGE_USER_DEFINED_FIELDS}</td>
				</tr>
				</tbody></table>
				
				<br>
				<table border="0" cellpadding="10" cellspacing="0" width="100%">
				<tbody><tr>
				<td>

				<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tbody><tr>
					<td class="big" nowrap><strong><span id="module_info">{$MOD.LBL_CUSTOM_FILED_IN} "{$APP.$MODULE}" {$APP.LBL_MODULE}</span></strong> </td>
					<td class="small" align="right">
					{$MOD.LBL_SELECT_CF_TEXT}
		                	<select name="pick_module" class="importBox" onChange="getCustomFieldList(this)">
							{foreach key=modulelabel item=module from=$MODULES}
								<!-- vtlib customization: Use translation only if available -->
								{assign var="modulelabel" value=$module|@getTranslatedString:$module}	<!-- crmv@16886 -->
								{if $MODULE eq $module}
									<option value="{$module}" selected>{$modulelabel}</option>
								{else}
									<option value="{$module}">{$modulelabel}</option>
								{/if}
							{/foreach}
			                </select>
					</td>
					</tr>
				</tbody>
				</table>
				<div id="cfList">
                                {include file="CustomFieldEntries.tpl"}
                </div>	
			<table border="0" cellpadding="5" cellspacing="0" width="100%">
			<tr>

		  	<td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
		<!-- End of Display -->
		
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
<br>
