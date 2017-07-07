{literal}
<script type="text/javascript">
   
//@todo -- put javascript code here 

</script>
{/literal}

<style type="text/css">
.TaskInput {ldelim}width: 240px; {rdelim}
.TaskTextArea {ldelim}width: 460px; height: 240px;{rdelim}
</style>



<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
        <br>

        <div align=center>
        {if $PARENTTAB eq 'Settings'}
                {include file='SetMenu.tpl'}
        {/if}

                <form name="EditView" method="POST" action="index.php" ENCTYPE="multipart/form-data">
                <input type="hidden" name="module" value="Workflow">
                <input type="hidden" name="record" value="{$ID}">
				<input type="hidden" name="sequence" value="{$SEQUENCE}">
                <input type="hidden" name="mode" value="{$MODE}">
                <input type='hidden' name='parenttab' value='{$PARENTTAB}'>
                <input type="hidden" name="activity_mode" value="{$ACTIVITYMODE}">
                <input type="hidden" name="action">
                <input type="hidden" name="return_module" value="{$RETURN_MODULE}">
                <input type="hidden" name="return_id" value="{$RETURN_ID}">
                <input type="hidden" name="return_action" value="{$RETURN_ACTION}">
                <input type="hidden" name="tz" value="Europe/Berlin">
                <input type="hidden" name="holidays" value="de,en_uk,fr,it,us,">
                <input type="hidden" name="workdays" value="0,1,2,3,4,5,6,">
                <input type="hidden" name="namedays" value="">
                <input type="hidden" name="weekstart" value="1">
                <input type="hidden" name="hour_format" value="{$HOUR_FORMAT}">
                <input type="hidden" name="start_hour" value="{$START_HOUR}">

        <table width="100%"  border="0" cellspacing="0" cellpadding="0" class="settingsSelUITopLine">
        <tr><td align="left">
                <table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
                <tr>
                        <td rowspan="2" style="width: 50px;"><img src="{$IMAGE_PATH}Transitions.gif" align="absmiddle"></td>
                        <td class="heading2">
                                <span aclass="lvtHeaderText">
                                {if $PARENTTAB neq ''}
                                <b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> &gt; <a href="index.php?module=Workflow&action=index&mode=FieldPermissionsOnFieldValue&parenttab=Settings">{$MOD.LBL_FPOFV_MANAGER}</a> &gt;
                                        {if $MODE eq 'edit'}
                                                {$APP.LBL_EDITING} &quot;{$FPOFV_PIECE_DATA.WorkflowName}&quot;
                                        {else}
                                                {if $DUPLICATE neq 'true'}
                                                {$UMOD.LBL_CREATE_NEW_USER}
                                                {else}
                                                {$APP.LBL_DUPLICATING} &quot;{$USERNAME}&quot;
                                                {/if}
                                        {/if}
                                        </b></span>
                                {else}
                                <span class="lvtHeaderText">
                                <b>{$APP.LBL_MY_PREFERENCES}</b>
                                </span>
                                {/if}
                        </td>
                        <td rowspan="2" nowrap>&nbsp;
                        </td>
                 </tr>
                <tr>
                        {if $MODE eq 'edit'}
                                <td><b class="small">{$UMOD.LBL_EDIT_VIEW} "{$FPOFV_PIECE_DATA.WorkflowName}"</b>
                        {else}
                                {if $DUPLICATE neq 'true'}
                                <td><b class="small">{$UMOD.LBL_CREATE_NEW_USER}</b>
                                {/if}
                        {/if}
                        </td>
                </tr>
                </table>
        </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
                <td nowrap align="right">
                                <input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small crmbutton save"  name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " onclick="this.form.action.value='FpofvSave'; return verify_data(EditView)" style="width: 70px;" type="button" />
                                <input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small crmbutton cancel" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " onclick="window.history.back()" style="width: 70px;" type="button" />

                </td>
        </tr>
        <tr><td class="padTab" align="left">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">

                <tr><td colspan="2">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="99%">
                        <tr>
                            <td align="left" valign="top">
                                     <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr><td align="left">

                                <br>
                <div class="workflow">
                        <div class="wf0" style="padding:0px 10px 20px 10px">
                                <b>{$UMOD.LBL_FPOFV_RULE_NAME}</b>
                                <br><br>
                                <input type="text" name="workflow_name" value="{$FPOFV_PIECE_DATA.WorkflowName}"  style="width: 200px;">
                        </div>

                        <div class="wf1" >
                                <b>{$UMOD.LBL_FPOFV_MODULE_NAME}</b>
                                <div style="padding:10px 10px 20px 10px;">
                                <select onChange="changeFields(this); setChangeFieldValues(this); selectCorrespondingValueEnterField(null, document.getElementById('wf2s' + (this.selectedIndex + 1)).length, 'wf41fc' + (this.selectedIndex + 1), 'i', 'box');" name="module_name" id="moduleName" style="width: 200px;">
                                        {foreach from=$modules_list item=module_name name=modules}
                                        <option value="{$module_name.0}"{if $FPOFV_PIECE_DATA.ModuleName eq $module_name.0} selected{/if}>{$APP[$module_name.1]}</option>
                                        {/foreach}
                                </select>
                                </div>
                        </div>

                        <b>{$UMOD.LBL_FPOFV_FIELD_NAME}</b>
                        <div style="padding:10px 10px 20px 10px;"> 
                        {foreach from=$modules_list item=module_name name=fields}
                        <div id="wf2{$smarty.foreach.fields.iteration}" style="display:{if $FPOFV_PIECE_DATA.ModuleName eq "" || $FPOFV_PIECE_DATA.ModuleField eq ""}{if $smarty.foreach.fields.first}inline{else}none{/if}{else}{if $FPOFV_PIECE_DATA.ModuleName eq $module_name.0}inline{else}none{/if}{/if};">
                                <select id="wf2s{$smarty.foreach.fields.iteration}" {if $FPOFV_PIECE_DATA.ModuleName eq "" || $FPOFV_PIECE_DATA.ModuleField eq ""}{if $smarty.foreach.fields.first}name="field"{/if}{else}{if $FPOFV_PIECE_DATA.ModuleName eq $module_name.0}name="field"{/if}{/if}  style="width: 200px;">
                                        {foreach from=$modules_fields[$module_name.0] item=field_name name=fieldc}
                                        <option value="{$fields_columnnames[$module_name.0][$smarty.foreach.fieldc.index]}" {if $FPOFV_PIECE_DATA.ModuleName eq $module_name.0}{if $FPOFV_PIECE_DATA.ModuleField eq $fields_columnnames[$module_name.0][$smarty.foreach.fieldc.index]} selected{/if}{/if}>{if $UMOD[$field_name] ne ''}{$UMOD[$field_name]}{else}{if $APP[$field_name] ne ''}{$APP[$field_name]}{else}{$field_name}{/if}{/if}</option>
                                        {/foreach}
                                </select>
                        </div>
                        {/foreach}
                        </div>


                        <div class="wf3">
                        	<table><tr>
                        		<td>
                                <b>{$UMOD.LBL_FPOFV_CRITERIA_NAME}</b>
		                        <div style="padding:10px 10px 20px 10px;">
                                <select name="criteria_id" style="margin-right: 20px;">
                                        <option value="0"{if $FPOFV_PIECE_DATA.CriteriaID eq "0"} selected{/if}>{$UMOD.LBL_CRITERIA_VALUE_LESS_EQUAL}</option>
                                        <option value="1"{if $FPOFV_PIECE_DATA.CriteriaID eq "1"} selected{/if}>{$UMOD.LBL_CRITERIA_VALUE_LESS_THAN}</option>
                                        <option value="2"{if $FPOFV_PIECE_DATA.CriteriaID eq "2"} selected{/if}>{$UMOD.LBL_CRITERIA_VALUE_MORE_EQUAL}</option>
                                        <option value="3"{if $FPOFV_PIECE_DATA.CriteriaID eq "3"} selected{/if}>{$UMOD.LBL_CRITERIA_VALUE_MORE_THAN}</option>
                                        <option value="4"{if $FPOFV_PIECE_DATA.CriteriaID eq "4"} selected{/if}>{$UMOD.LBL_CRITERIA_VALUE_EQUAL}</option>
                                        <option value="5"{if $FPOFV_PIECE_DATA.CriteriaID eq "5"} selected{/if}>{$UMOD.LBL_CRITERIA_VALUE_NOT_EQUAL}</option>
                                        <option value="6"{if $FPOFV_PIECE_DATA.CriteriaID eq "6"} selected{/if}>{$UMOD.LBL_CRITERIA_VALUE_INCLUDES}</option>
                                </select>
                                </td><td>
                                {$UMOD.LBL_CRITERIA_VALUE_NAME}
                                <input type="text" value="{$FPOFV_PIECE_DATA.FieldValue}" name="field_value" style="margin-left: 3px;">
                                </div>
                                
                                </td><td>
                                {$UMOD.LBL_USER_ROLE_GRP_LABEL}&nbsp;{$ROLE_GRP_CHECK_PICKLIST}
                                </td>
                            </tr></table>    
                                
                        </div>

                        <div class="wf4">
                                <b>{$UMOD.LBL_FPOFV_ACTION_NAME}</b>
		                        <div style="padding:10px 10px 10px 10px;">

								<input type="hidden" name="task" id="taskbox" value="FieldChange" >
       
                                </div>
                        </div>
						<div style="margin-bottom: 20px"> 
	                   
	                        {foreach from=$modules_list item=module_name name=fields}

	                        <div id="wf41b{$smarty.foreach.fields.iteration}" style="padding:0px 0px 0px 10px; display:none">
	                                <table border="0" cellpadding="0" cellspacing="0">
	                                <tr>
	                                <td style="padding: 0px 0px 10px 0;">
	                                
	                                	{if $MODE eq 'create'}
			                                <select MULTIPLE id="wf41f{$smarty.foreach.fields.iteration}"  style="width: 200px;">
		                                        {foreach from=$modules_fields[$module_name.0] item=field_name name=fieldi}
		                                    		<option value="{$fields_columnnames[$module_name.0][$smarty.foreach.fieldi.index]}" {if $fields_columnnames[$module_name.0][$smarty.foreach.fieldi.index] eq $FPOFV_PIECE_DATA.TaskField}selected{/if}>{if $UMOD[$field_name] ne ''}{$UMOD[$field_name]}{else}{if $APP[$field_name] ne ''}{$APP[$field_name]}{else}{$field_name}{/if}{/if}</option>
			                                    {/foreach}
			                                </select>
	                                	
	                                	{else}
			                                <select id="wf41f{$smarty.foreach.fields.iteration}"  style="width: 200px;">
		                                        {foreach from=$modules_fields[$module_name.0] item=field_name name=fieldi}
		                                    		<option value="{$fields_columnnames[$module_name.0][$smarty.foreach.fieldi.index]}" {if $fields_columnnames[$module_name.0][$smarty.foreach.fieldi.index] eq $FPOFV_PIECE_DATA.TaskField}selected{/if}>{if $UMOD[$field_name] ne ''}{$UMOD[$field_name]}{else}{if $APP[$field_name] ne ''}{$APP[$field_name]}{else}{$field_name}{/if}{/if}</option>
			                                    {/foreach}
			                                </select>
	                                		
	                                	{/if}
							            </td>
	                                </tr>
	                                
	                                </table>

	                        </div>
	                        {/foreach}

							</div>
						</div>
						
                        <b>{$UMOD.LBL_FPOFV_EXTENDED_PARAMETERS}</b>
                        <div style="padding:10px 10px 20px 10px;">
							<input type="checkbox" name="FpovReadPermission" onchange="toggle_permissions();" value="1" id="FpovReadPermission" {if $FPOFV_PIECE_DATA.FpovReadPermission eq "1"}checked{/if}><label for="FpovReadPermission">&nbsp;{$UMOD.LBL_FPOFV_READ_PERMISSION}</label>&nbsp;
							<input type="checkbox" name="FpovWritePermission" onchange="toggle_permissions();" value="1" id="FpovWritePermission" {if $FPOFV_PIECE_DATA.FpovWritePermission eq "1"}checked{/if}><label for="FpovWritePermission">&nbsp;{$UMOD.LBL_FPOFV_WRITE_PERMISSION}</label>&nbsp;
							<input type="checkbox" name="FpovMandatoryPermission" onchange="toggle_permissions();" value="1" id="FpovMandatoryPermission" {if $FPOFV_PIECE_DATA.FpovMandatoryPermission eq "1"}checked{/if}><label for="FpovMandatoryPermission">&nbsp;{$UMOD.LBL_FPOFV_MANDATORY_PERMISSION}</label>
                        </div>
                </div>

                                <br>
                                <tr><td colspan=4>&nbsp;</td></tr>

                                                <tr>
                                                               <td colspan=4 align="right">
                                                        <input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small crmbutton save"  name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  "  onclick="this.form.action.value='FpofvSave'; return verify_data(EditView)" style="width: 70px;" type="button" />
                                                        <input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small crmbutton cancel" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " onclick="window.history.back()" style="width: 70px;" type="button" />
                                                        </td>
                                                </tr>
                                            </table>
                                         </td></tr>
                                        </table>
                                     </td></tr>
                                   </table>
                                 <br>
                                  </td></tr>
                                <tr><td class="small"><div align="right"><a href="#top">{$MOD.LBL_SCROLL}</a></div></td></tr>
                                </table>
                        </td>
                        </tr>
                        </table>
                        </form>
</td>
</tr>
</table>
</td></tr></table>
<br>
{$JAVASCRIPT}
