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
{literal}
<script type="text/javascript">
function getModuleEntityNoInfo(form) {
	var module = form.selmodule.value;

	$("status").style.display="inline";
	new Ajax.Request(
    	'index.php',
        {queue: {position: 'end', scope: 'command'},
        	method: 'post',
            postBody: 'module=Settings&action=SettingsAjax&file=CustomModEntityNo&ajax=true&selmodule=' + encodeURIComponent(module),
            onComplete: function(response) {
				$("status").style.display="none";

				var restext = response.responseText;
				$('customentity_infodiv').innerHTML = restext;
            }
        }
    );
}
function updateModEntityNoSetting(button, form) {
	var module = form.selmodule.value;
	var recprefix = form.recprefix.value;
    var recnumber = form.recnumber.value;
	var mode = 'UPDATESETTINGS';

	if(recnumber == '') {
		alert("Start sequence cannot be empty!");
		return;
	}

	if(recnumber.match(/[^0-9]+/) != null) {
		alert("Start sequence should be numeric.");
		return;
	}

	$("status").style.display="inline";
	button.disabled = true;

	new Ajax.Request(
    	'index.php',
        {queue: {position: 'end', scope: 'command'},
        	method: 'post',
            postBody: 'module=Settings&action=SettingsAjax&file=CustomModEntityNo&ajax=true' + 
					'&selmodule=' + encodeURIComponent(module) +
					'&recprefix=' + encodeURIComponent(recprefix) +
                    '&recnumber=' + encodeURIComponent(recnumber) +
					'&mode=' + encodeURIComponent(mode),

            onComplete: function(response) {
				$("status").style.display="none";

				var restext = response.responseText;
				$('customentity_infodiv').innerHTML = restext;
            }
        }
    );
}
function updateModEntityExisting(button, form) {
	var module = form.selmodule.value;
	var recprefix = form.recprefix.value;
    var recnumber = form.recnumber.value;
	var mode = 'UPDATEBULKEXISTING';

	if(recnumber == '') {
		alert("Start sequence cannot be empty!");
		return;
	}

	if(recnumber.match(/[^0-9]+/) != null) {
		alert("Start sequence should be numeric.");
		return;
	}

	VtigerJS_DialogBox.progress();
	button.disabled = true;

	new Ajax.Request(
    	'index.php',
        {queue: {position: 'end', scope: 'command'},
        	method: 'post',
            postBody: 'module=Settings&action=SettingsAjax&file=CustomModEntityNo&ajax=true' + 
					'&selmodule=' + encodeURIComponent(module) +
					'&mode=' + encodeURIComponent(mode),

            onComplete: function(response) {
				VtigerJS_DialogBox.hideprogress();

				var restext = response.responseText;
				$('customentity_infodiv').innerHTML = restext;
            }
        }
    );
}
</script>
{/literal}

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@30683 -->
<tbody>
<tr>
	<td valign="top"></td>
    <td class="showPanelBg" style="padding: 5px;" valign="top" width="100%"> <!-- crmv@30683 -->

	<div align=center>
		{include file='SetMenu.tpl'}
		{include file='Buttons_List.tpl'} {* crmv@30683 *} 
		<!-- DISPLAY -->
		<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
		<tr>
			<td width=50 rowspan=2 valign=top><img src="{'settingsInvNumber.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_CUSTOMIZE_MODENT_NUMBER}" width="48" height="48" border=0 title="{$MOD.LBL_CUSTOMIZE_MODENT_NUMBER}"></td>
			<td class=heading2 valign=bottom><b> {$MOD.LBL_SETTINGS} > {$MOD.LBL_CUSTOMIZE_MODENT_NUMBER}</b></td> <!-- crmv@30683 -->
		</tr>
		<tr>
			<td valign=top class="small">{$MOD.LBL_CUSTOMIZE_MODENT_NUMBER_DESCRIPTION}</td>
		</tr>
		</table>				
		<br>
		{if $EMPTY eq 'true'}
		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src="{'denied.gif'|@vtiger_imageurl:$THEME}"></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
			<span class='genHeaderSmall'>{$APP.LBL_NO_MODULES_TO_SELECT}</span></td>
		</tr>
		</tbody>
		</table>
		{else}
		<form method="POST" action="javascript:;" onsubmit="VtigerJS_DialogBox.block();">
		<table border="0" cellpadding="10" cellspacing="0" width="100%">
		<tr>
			<td>
				<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td class="small" align="right">
					{$MOD.LBL_SELECT_CF_TEXT}
		            <select name="selmodule" class="small" onChange="getModuleEntityNoInfo(this.form)">
						{foreach key=sel_value item=label from=$MODULES}
		                {if $SELMODULE eq $sel_value}
                	    	{assign var = "selected_val" value="selected"}
		                {else}
                        	{assign var = "selected_val" value=""}
                        {/if}
	                    <option value="{$sel_value}" {$selected_val}>{$label}</option>
        		        {/foreach}
			        </select>
					</td>
				</tr>
				</table>

				<div id='customentity_infodiv' class="listRow">
					{include file='Settings/CustomModEntityNoInfo.tpl'}				
				</div>

			<table border="0" cellpadding="5" cellspacing="0" width="100%">
			<tr>
				<td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</form>
		{/if}
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

