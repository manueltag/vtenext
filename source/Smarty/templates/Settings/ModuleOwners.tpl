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
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/menu.js"|resourcever}"></script>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@30683 -->
<tbody><tr>
        <td valign="top"></td>
        <td class="showPanelBg" style="padding: 5px;" valign="top" width="100%"> <!-- crmv@30683 -->
	<div align=center>
	
			{include file='SetMenu.tpl'}
			{include file='Buttons_List.tpl'} {* crmv@30683 *}   
			<!-- DISPLAY -->
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
			<tr>
					<td width=50 rowspan=2 valign=top><img src="{$IMAGE_PATH}assign.gif" alt="{$MOD.LBL_USERS}" width="48" height="48" border=0 title="{$MOD.LBL_USERS}"></td>
				<td class="heading2" valign="bottom" ><b> {$MOD.LBL_SETTINGS} > {$MOD.LBL_MODULE_OWNERS} </b></td> <!-- crmv@30683 -->
			</tr>
			<tr>
				<td valign=top class="small">{$MOD.LBL_MODULE_OWNERS_DESCRIPTION}</td>
			</tr>
			</table>
			<br>
			<table border=0 cellspacing=0 cellpadding=10 width=100% >
			<tr>
			<td>

					<div id="module_list_owner">	
						{include file='Settings/ModuleOwnersContents.tpl'}
					</div>
	
	<table border=0 cellspacing=0 cellpadding=5 width=100% >
	<tr><td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
	</table>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</form>
	</table>
		
	</div>

</td>
        <td valign="top"></td>
   </tr>
</tbody>
</table>

	
{literal}
<script>
function assignmodulefn(mode)
{
	$("status").style.display="inline";
	var urlstring ='';
	for(i = 0;i < document.support_owners.elements.length;i++)
	{
		if(document.support_owners.elements[i].name != 'button')
		urlstring +='&'+document.support_owners.elements[i].name+'='+document.support_owners.elements[i].value;
	}
	new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: urlstring+'&list_module_mode='+mode+'&file_mode=ajax',
                        onComplete: function(response) {
                                $("status").style.display="none";
				$("module_list_owner").innerHTML=response.responseText;
                        }
                }
        );
}
</script>
{/literal}
