<!--
	/*********************************************************************************
	 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
	 * ("License"); You may not use this file except in compliance with the License
	 * The Original Code is:  vtiger CRM Open Source
	 * The Initial Developer of the Original Code is crmvillage.biz.
	 * Portions created by crmvillage.biz are Copyright (C) crmvillage.biz.
	 * All Rights Reserved.
	 *
	 ********************************************************************************/
-->
<div id="createcf" style="display:block;position:absolute;width:500px;"></div>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@30683 -->
<tbody><tr>
        <td valign="top"></td>
        <td class="showPanelBg" style="padding: 5px;" valign="top" width="100%"> <!-- crmv@30683 -->

<form action="index.php" method="post" name="EditView" id="form">

<input type='hidden' name='module' value='Conditionals'>
<input type='hidden' name='action' value='EditView'>
<input type='hidden' name='return_action' value='index'>
<input type='hidden' name='return_module' value='Conditionals'> 



<input type='hidden' name='parenttab' value='Settings'>
	<div align=center>
			{include file='SetMenu.tpl'}
			{include file='Buttons_List.tpl'} {* crmv@30683 *}
			<!-- DISPLAY -->
			{if $MODE neq 'edit'}
			<b><font color=red>{$DUPLICATE_ERROR} </font></b>
			{/if}

				<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tbody><tr>
					<td rowspan="2" valign="top" width="50"><img src="{$IMAGE_PATH}workflow.gif" alt="{$MOD.LBL_COND_MANAGER}" title="{$MOD.LBL_COND_MANAGER}" border="0" height="48" width="48"></td>
					<td class="heading2" valign="bottom"><b> {$MOD.LBL_SETTINGS} &gt; {$MOD.LBL_COND_MANAGER}</b></td> <!-- crmv@30683 -->
				</tr>

				<tr>
					<td class="small" valign="top">{$MOD.LBL_COND_MANAGER_DESCRIPTION}</td>
				</tr>
				</tbody>
				</table>

				<br>

				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
					<div id="ListViewContents">
						{include file="modules/Conditionals/ListViewContents.tpl"}
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
function getListViewEntries_js(module,url)
{
        $("status").style.display="inline";
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Conditionals&action=ConditionalsAjax&file=ListView&ajax=true&'+url,
                        onComplete: function(response) {
                                $("status").style.display="none";
                                $("ListViewContents").innerHTML= response.responseText;
                        }
                }
        );
}
</script>
{/literal}
