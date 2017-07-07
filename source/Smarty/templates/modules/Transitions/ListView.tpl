<div id="createcf" style="display:block;position:absolute;width:500px;"></div>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody><tr>
        <td valign="top"></td>
        <td class="showPanelBg" style="padding: 5px;" valign="top" width="100%">

<form action="index.php" method="post" name="EditView" id="form">

<input type='hidden' name='module' value='Transitions'>
<input type='hidden' name='action' value='Transitions/EditView'>
<input type='hidden' name='return_action' value='index'>
<input type='hidden' name='return_module' value='Transitions'>
<input type='hidden' name='return_mode' value='StateTransitions'>

<input type='hidden' name='parenttab' value='Settings'>


	<div align=center>
			{include file='SetMenu.tpl'}
			{include file='Buttons_List.tpl'} {* crmv@30683 *}
			<!-- DISPLAY -->
			{if $TMODE neq 'edit'}
			<b><font color=red>{$DUPLICATE_ERROR} </font></b>
			{/if}


				<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tbody><tr>
					<td rowspan="2" valign="top" width="50"><img src="{$IMAGE_PATH}Transitions.gif" alt="{$TMOD.LBL_ST_MANAGER}" title="{$TMOD.LBL_ST_MANAGER}" border="0" height="48" width="48"></td>
					<td class="heading2" valign="bottom"><b> {$SMOD.LBL_SETTINGS} &gt; {$TMOD.LBL_ST_MANAGER}</b></td> <!-- crmv@30683 -->
				</tr>

				<tr>
					<td class="small" valign="top">{$TMOD.LBL_ST_MANAGER_DESCRIPTION}</td>
				</tr>
				</tbody>
				</table>

				<br>

				<table border=0 cellspacing=0 cellpadding=10 width=100% class="listTableTopButtons">
				
				<tr>
					<td>
					
						<b>{$APP.LBL_MODULE}:</b>
					</td>
					<td>	
	                    <select onChange="module_selection_change();" name="module_name" id="moduleName" style="width: 200px;">
	                    	<option value="-1" selected>{$APP.LBL_NONE}</option>
	                        {foreach from=$modules_list key=module_name item=module_name_show name=modules}
	                        	{assign var="module_name_show" value=$module_name|@getTranslatedString:$module_name}	<!-- crmv@16886 -->
	                            <option value="{$module_name}"{if $ST_PIECE_DATA.ModuleName eq $module_name} selected{/if}>{$module_name_show}</option>
	                        {/foreach}
	                    </select>
	                </td>

	            </tr>
				<tr id="field_line" style="visibility:collapse;">
					<td>
	                	<b>{$TMOD.LBL_CURR_ST_FIELD}:</b>
	                </td>
	                <td  id="field_select" >	
	             	</td>
	                <td colspan="2">
	                	<div id="make_field_transition" style="visibility:collapse;">
	                    	<input type="button" value="{$TMOD.LBL_MAKE_TRANSITION}" onclick="makefieldTransition();" class="crmButton delete small">
	                    </div>
	                	<div id="unmake_field_transition" style="visibility:collapse;">
	                    	<input type="button" value="{$TMOD.LBL_UNMAKE_TRANSITION}" onclick="unmakefieldTransition();" class="crmButton delete small">
	                    </div>
	             	</td>
	            </tr>
				<tr id="roles_line" style="visibility:collapse;">
					<td>
	                	<b>{$APP.LBL_ROLE}:</b>
	                </td>
	                <td>	
	                	{$ROLE_CHECK_PICKLIST}
	             	</td>
	                <td>
	                	<b>{$TMOD.COPY_FROM} {$APP.LBL_ROLE}:</b>
	                </td>
	                <td>	
	                	{$COPY_ROLE_CHECK_PICKLIST}
	                    &nbsp;
	                    <input type="button" value="{$TMOD.LBL_COPY}" onclick="sttCopy();" class="crmButton delete small">
	                </td>	             	
	            </tr>
	            <tr id="copy_roles_line" > 	       

	                
				</tr>
				
				<tr>
				<td colspan="5">
					<div id="st_table_content">
						{include file="modules/Transitions/ListViewContents.tpl"}
					</div>
				</td>
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
</form>
</table>
<br>
{literal}
<script>

function role_selection_change() {
	var obj = getObj('st_table_content');
	obj.style.display = 'none';
	getStTable(true);
}
function module_selection_change() {
	var obj = getObj('st_table_content');
	obj.style.display = 'none';
	var module_name_obj = getObj('module_name');
	var role_check_obj = getObj('role_check');
	var field_check_obj = getObj('status_field');
	if(module_name_obj.value == "-1") { 
		hide_all();
	}
	else {
		getObj("field_line").style.visibility = "visible";
		getStField(module_name_obj.value,"");
	}	
}
function hide_all(){
	getObj("field_line").style.visibility= "collapse";
	getObj("roles_line").style.visibility= "collapse";
}
function status_field_selection_change(){
	var status_field_obj = getObj('status_field');
	var module_name_obj = getObj('module_name');
	getStField(module_name_obj.value,status_field_obj.value);
//	var url = "&module_name="+module_name_obj.value+"&field="+status_field_obj.value;
//    $("status").style.display="inline";
//    new Ajax.Request(
//            'index.php',
//            {queue: {position: 'end', scope: 'command'},
//                    method: 'post',
//                    postBody: 'module=Transitions&action=TransitionsAjax&file=SaveField&ajax=true&'+url,
//                    onComplete: function(response) {
//                            $("status").style.display="none";
//                    }
//            }
//    );
//	if(status_field_obj.value == "-1") {
//		getObj("roles_line").style.visibility= "collapse";
//		$("st_table_content").style.display = "none";
//	}
//	else {
//		getObj("roles_line").style.visibility = "visible";
//		getStTable(true);
//	}	
}
function getStField(module,field)
{
	var url = "&module_name="+module+"&field="+field;
    $("status").style.display="inline";
    new Ajax.Request(
            'index.php',
            {queue: {position: 'end', scope: 'command'},
                    method: 'post',
                    postBody: 'module=Transitions&action=TransitionsAjax&file=LoadField&ajax=true'+url,
                    onComplete: function(response) {
                            $("status").style.display="none";
//                          alert(response.responseText);return;                            
                            result = eval('(' + response.responseText + ')');
//                            alert(response.responseText);return;
                            $("field_select").innerHTML= result['picklist_fields'];
                    		$("unmake_field_transition").style.visibility= "collapse";
                    		$("make_field_transition").style.visibility= "collapse";
                            var status_field_obj = getObj('status_field');
                        	if(status_field_obj.value == "-1") {
                        		getObj("roles_line").style.visibility= "collapse";
                        		$("st_table_content").style.display = "none";
                        	}
                        	else {
                        		if (result['is_managed'] ){
                        			getObj("roles_line").style.visibility = "visible";
                        			$("unmake_field_transition").style.visibility = "visible";
                        			getStTable(false);
                        		}
                        		else {
                            		getObj("roles_line").style.visibility= "collapse";
                            		$("st_table_content").style.display = "none";
                            		$("unmake_field_transition").style.visibility= "collapse";
                        			$("make_field_transition").style.visibility = "visible";
                        		}                         		
                        	}   
                       	                        
                    }
            }
    );
}
function dotransition(module,field)
{
	var url = "&module_name="+module+"&field="+field;
    $("status").style.display="inline";
    new Ajax.Request(
            'index.php',
            {queue: {position: 'end', scope: 'command'},
                    method: 'post',
                    postBody: 'module=Transitions&action=TransitionsAjax&file=doTransition&ajax=true'+url,
                    onComplete: function(response) {
                            $("status").style.display="none";
                            result = eval('(' + response.responseText + ')');
                            if (result['success']){
                            	var status_field_obj = getObj('status_field');
                            	if(status_field_obj.value == "-1") {
                            		getObj("roles_line").style.visibility= "collapse";
                            		$("st_table_content").style.display = "none";
                            		$("unmake_field_transition").style.visibility= "collapse";
                            		$("make_field_transition").style.visibility= "collapse"; 
                            	}
                            	else {
                                	$("make_field_transition").style.visibility= "collapse";
                                	$("unmake_field_transition").style.visibility = "visible";
                                	getObj("roles_line").style.visibility = "visible";
                                	getStTable(true);
                                }		  
                            } 
                            else {
								alert(result['msg']);
                            }           
                    }
            }
    );
}
function makefieldTransition(){
	var status_field_obj = getObj('status_field');
	var module_name_obj = getObj('module_name');
	dotransition(module_name_obj.value,status_field_obj.value);	
}
function unmakefieldTransition(){
	var status_field_obj = getObj('status_field');
	status_field_obj.value = "-1";
	var module_name_obj = getObj('module_name');
	dotransition(module_name_obj.value,status_field_obj.value);	
		
}
function getStTable(alert_flag)
{
	var module_name_obj = getObj('module_name');
	var field_name_obj = getObj('status_field');
	var role_check_obj = getObj('role_check');
	if(role_check_obj.value == "-1") {
		if (alert_flag) 
			alert(alert_arr.LBL_STATUS_PLEASE_SELECT_A_ROLE);
		return;
	}
	$("st_table_content").style.display = "none";
	var url = "&module_name="+module_name_obj.value+"&roleid="+role_check_obj.value+"&field="+field_name_obj.value;
        $("status").style.display="inline";
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Transitions&action=TransitionsAjax&file=ListView&ajax=true&'+url,
                        onComplete: function(response) {
                                $("status").style.display="none";
//                                alert(response.responseText);
                                $("st_table_content").innerHTML= response.responseText;
                                $("st_table_content").style.display = "inline";
                                var width = eval(jQuery('.settingsSelectedUI').width());
                                jQuery('#rule_table').width(width-17);
                                //jQuery('#rule_table').scrollableFixedHeaderTable(100,100,1);
                                //crmv@16604
                                jQuery(".dvtCellLabel").attr('class', '').css("font-weight","bold").css("white-space","nowrap");
                                jQuery(".dvtCellInfo").attr('class', '');
                                //crmv@16604e
                        }
                }
        );
}


function sttSetAll(boolset) {
	var table = document.getElementById("rule_table"); 
	var checks = table.getElementsByTagName("input"); 
	for (var i = 0; i < checks.length; i++) {
		if(checks[i].id.indexOf("st_ruleid_")>-1) {
			if(boolset) checks[i].checked = true;
			else checks[i].checked = false;
		}
	}
}

function sttUpdate() {
	var ruleid_sequence = "";
	var table = document.getElementById("rule_table"); 
	var checks = table.getElementsByTagName("input"); 
	for (var i = 0; i < checks.length; i++) {
		if(checks[i].id.indexOf("st_ruleid_")>-1) {
			if(checks[i].checked) 
				ruleid_sequence += "&"+checks[i].id+"=1"
			else ruleid_sequence += "&"+checks[i].id+"=0"
		}
	}
	var role_check_obj = getObj('role_check');
	var module_name_obj = getObj('module_name');
	var status_field_obj = getObj('status_field')
	var source_module = module_name_obj.value;
	var source_roleid = role_check_obj.value
	var status_field = status_field_obj.value
	var status_field_value = getObj(status_field).value
	
    $("status").style.display="inline";
    new Ajax.Request(
            'index.php',
            {queue: {position: 'end', scope: 'command'},
                    method: 'post',
                    postBody: 'module=Transitions&action=TransitionsAjax&file=Update&ajax=true&'+ruleid_sequence+'&source_module='+source_module+'&source_roleid='+source_roleid+'&status_field='+status_field+'&status_field_value='+status_field_value,
                    onComplete: function(response) {
                    		
                            $("status").style.display="none";
                    }
            }
    );
}

function sttCopy() {

	if(!confirm(alert_arr.ARE_YOU_SURE)) return;

	var role_check_obj = getObj('role_check');
	var module_name_obj = getObj('module_name');
	var src_role_check_obj = getObj('src_role_check');

	if(module_name_obj.value == "-1") { 
		alert(alert_arr.LBL_STATUS_PLEASE_SELECT_A_MODULE);
		return;
	}
	
	if(role_check_obj.value == "-1") {
		alert(alert_arr.LBL_STATUS_PLEASE_SELECT_A_ROLE);
		return;
	}

	if(src_role_check_obj.value == "-1") {
		alert(alert_arr.LBL_STATUS_PLEASE_SELECT_A_ROLE);
		return;
	}

var source_module = module_name_obj.value;
var source_roleid = src_role_check_obj.value
var destination_roleid = role_check_obj.value;

    $("status").style.display="inline";
	new Ajax.Request(
            'index.php',
            {queue: {position: 'end', scope: 'command'},
                    method: 'post',
                    postBody: 'module=Transitions&action=TransitionsAjax&file=Update&ajax=true&subaction=copy&source_module='+source_module+'&source_roleid='+source_roleid+'&destination_roleid='+destination_roleid,
                    onComplete: function(response) {
                            $("status").style.display="none";
                            
                            getStTable(false);
                    }
            }
    );
}

</script>
{/literal}
